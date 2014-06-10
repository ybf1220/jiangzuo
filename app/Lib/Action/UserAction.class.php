<?php
class UserAction extends UserBaseAction{
	public function login()	{
		$user_mod=M('user');
		if ($_POST) {
			$student_id = $_POST['student_id'] && trim($_POST['student_id']) ? trim($_POST['student_id']) : '';
			$password = $_POST['password'] && trim($_POST['password']) ? trim($_POST['password']) : '';
			if (!$student_id || !$password) {
				redirect(u('user/login'));
			}

			$user_info=$user_mod->where("student_id = '$student_id'")->find();

			//使用用户名、密码和状态的方式进行认证
			if(false == $user_info) {
				$this->error('帐号不存在！');
			}else {
				if($user_info['password'] != md5($password)) {
					$this->error('密码错误！');
				}
				//记住登录信息
				if($_POST['is_remember'] == 1){
				setcookie("student_id", $student_id, time()+3600*24*30);  
				setcookie("password", md5($password), time()+3600*24*30);  
				}
				//记录登录会话
				$_SESSION['user_info'] =$user_info;
				SESSION('user_info',$user_info);

				$this->success('登录成功！');
				exit;
			}
		}else{
		$this->assign('title', '用户登录');
		$this->display();
		}
	}
	//退出登录
	public function logout()
	{
		if(isset($_SESSION['user_info'])) {
			session('user_info',null);
			COOKIE('student_id',null);
			COOKIE('password',null);
			$this->success('退出登录成功！',u('Index/index'));
		}else {
			$this->success('已经退出登录！');
		}
	}

	//注册功能修改，采用学生邮箱注册
	public function email_register(){
		if (null){
		}

	}
	public function signup(){
		if ($_POST) {
			//实例化
			$user_mod = D("user");
	    	$data = array();
			//检测是否重复
	    	$student_id = isset($_POST['student_id']) && trim($_POST['student_id']) ? trim($_POST['student_id']) : $this->error('请填写学号');
	    	$exist = $user_mod->where("student_id='".$student_id."'")->count();
			if($exist != 0){
				 $this->assign('waitSecond','5');
				$this->error('该学号的会员已经存在,将跳转到找回密码界面！',u('user/findpass'));
			}
	    	$data = $user_mod->create();
	        $data['add_time'] = time();
	        $data['password'] = md5($data['password']);

	    	$uid = $user_mod->add($data);
			if($uid){
			$user_info=$user_mod->find($uid);
				//记录登录会话
				$_SESSION['user_info'] =$user_info;
				SESSION('user_info',$user_info);

			$this->success('注册成功',u('user/index'));
			}
		} else {
		    $this->assign('title', '用户注册');
			$this->display();
		}
	}
	public function index(){
		if ($_POST) {
			$user_mod = D("user");
			$user_data = $user_mod->create();			
			$result_info = $user_mod->where("id=". $user_data['id'])->save($user_data);
			if($result_info){
					$user_info=$user_mod->where("id =". $user_data['id'])->find();
					$_SESSION['user_info'] =$user_info;
					SESSION('user_info',$user_info);
					$this->success('修改成功',u('user/index'));
			}else {
				$this->error('资料没修改，请勿提交！',u('user/index'));
			}
		}else{
		    $this->assign('title', '账户设置');
			$this->display();
		}
	}
	public function password(){
		if ($_POST) {
			$user_mod = D("user");
			$id = $_POST['id'];
			$password = md5($_POST['password']);
			if(empty($_POST['password'])){
				$this->error('原密码不能为空',u('user/password'));
			}
			$result_info = $user_mod->where("id= $id and password ='$password'")->find();
			if(empty($result_info)){
				$this->error('原密码错误',u('user/password'));
			}
			$user_data['password'] = md5($_POST['newPassword']);
			if(empty($_POST['newPassword'])){
				$this->error('新密码不能为空',u('user/password'));
			}
			$result_info = $user_mod->where("id=". $id)->save($user_data);

			if($result_info){
			//修改密码后清空保存的cookie，避免下次登录出错
			COOKIE('student_id',null);
			COOKIE('password',null);
				$this->success('修改成功',u('user/password'));
			}else {
				$this->error('修改出错',u('user/password'));
			}
		}else{
		    $this->assign('title', '密码安全');
			$this->display();
		}
	}
	public function book(){
			//读取会话中的用户信息
			$user_info = $_SESSION['user_info'];
			if(!$user_info){
			$this->error('请先登录',u('user/login'));
			}
			//传递讲座参数ID，查询讲座信息
			$data['cid'] = trim($_GET['cid']);
			$course = D('course')->where("id =".$data['cid'])->find();
			//检查是否已经预约过
			$check_book = D('course_book')->where("cid =".$data['cid']." and uid =".$user_info['id'])->find();
			if($check_book){
				$this->error('已经预约了，请勿重复预约');
			}
			//检查是否已经满人
			if($course['people'] < $course['max_people']){
			$data['ctitle'] = $course['title'];
			$data['start_time'] = $course['start_time'];
			$data['uid'] = $user_info['id'];
			$data['uname'] = $user_info['name'];
			$data['address'] = $course['address'];
			$data['ustudent_id'] = $user_info['student_id'];
			$data['uphone'] = $user_info['phone'];
			$data['author'] = $course['author'];
			$data['add_time'] = time();
				if(D("course_book")->add($data)){
				//讲座中已报名人数增加1
				D('course')->where("id =".$data['cid'])->setInc('people',1);
				$this->success('预约成功');
				}else{
				$this->error('预约失败');
				}
			}else{
				$this->error('讲座人数已满，预约失败');
			}
	}
	public function booked(){
		$user_info = $_SESSION['user_info'];
		$uid = $user_info['id'];
		$list = d('course_book')->where("uid = $uid")->order("start_time desc")->select();
		$this->assign('list', $list);
		$this->display();
	}
	public function cancel(){
		if($_GET['id']){
			$id = trim($_GET['id']);
			$booked = D('course_book')->find($id);
				if($booked['start_time'] < time()){
				$this->error('讲座已经开始了！不能取消预约！');
				}
			if(D('course_book')->delete($id)){
			D('course')->where("id =".$booked['cid'])->setDec('people',1);
			$this->success("取消预约成功",U('user/booked'));
			}else{
			$this->error("取消预约出错！");
			}

		}else{
		$this->error("参数传递错误！");
		}
	}

	public function findpass(){
		$user_mod=M('user');
		if ($_POST) {
			$student_id = $_POST['student_id'] && trim($_POST['student_id']) ? trim($_POST['student_id']) : '';; 
			$user_info = $user_mod->where("student_id = '$student_id'")->find();
		    $email_addr = $_POST['student_id']."@stu.zjut.edu.cn";
		    if(!$user_info) {
				$this->error('帐号不存在！');
				}else {
    			//改良的随机数生成算法
    			$tmp_code = md5(mt_rand(0,9999999));
				$length = 18;
				$random_data = substr($tmp_code,0,$length);

 				$to = $email_addr;
 				$name = $email_addr;
 				$subject = '讲座系统密码找回';
 				$body="敬爱的用户您好,请根据以下链接重置你的讲座系统密码.<br/><br/>   
 				http://".$_SERVER['HTTP_HOST']."/jiangzuo/index.php?m=user&a=resetpassword&id=".$student_id."&verifycode=".$random_data;
 				//将verifycode 插入到数据库，
 				$data['verifycode']= $random_data;
 				$user_info = $user_mod->where("student_id = '$student_id'")->data($data)->save();
 				//调用common.php 函数
 			    $send_email =  jiangzuo_send_mail($to, $name, $subject , $body , $attachment = null);
 			    	if ($send_email == 1){
 			    	$this->success("发送到邮箱  ".$to."成功！",U('user/login'));
 			    	}
    			}
			}else{
			$this->display();		
		}	
	}

	public function resetpassword(){
		$student_id=$_GET['id'];
		//echo "$student_id";
		$verifycode=$_GET['verifycode'];
		//echo $verifycode;
		$user_mod = M('user');
		$user_result=$user_mod->where("verifycode = '$verifycode'")->find();
		$this->assign("verifycode",$verifycode); 
		if($_GET){
			if ($user_result && !empty($verifycode)){
				$this->display();
			}else if(!$user_result || empty($verifycode)) {
				$this->error("链接已经失效或者参数不全！请重新发送邮件获取！",U('user/findpass'));
			}
		}

	}

	public function doresetpassword(){
		$password = md5($_POST['password']);
		$verifycode = $_POST['verifycode'];
		$user_mod = M('user');
		$user_result=$user_mod->where("verifycode = '$verifycode'")->find();
		if($user_result){
			$user_info=$user_mod->where("verifycode = '$verifycode'")->setField('password',$password);
				$this->success("重置密码成功！请使用新密密码登录！",U('user/login'));
				$user_info=$user_mod->where("verifycode = '$verifycode'")->setField('verifycode',"");
			}else {
			$this->error("校验码不存在，重新邮件获取",U('user/findpass'));
		}

	}

}
?>