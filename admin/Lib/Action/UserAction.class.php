<?php
class UserAction extends BaseAction{
	public function index(){
        $mod=D("user"); 
        $pagesize=30;        
        import("ORG.Util.Page");
		if(!empty($_REQUEST['name'])){
			$name = $_REQUEST['name'];
			$this->assign('name',$name);
			$where['name'] = array('like',"%$name%");
		}
		if(!empty($_REQUEST['student_id'])){
			$keys = $_REQUEST['student_id'];
			$this->assign('student_id',$keys);
			$where['student_id'] = array('like',"%$keys%");
		}
		$count=$mod->where($where)->count();		
		$p = new Page($count,$pagesize);		
		$list=$mod->where($where)->order("add_time desc")->limit($p->firstRow.','.$p->listRows)->select();
		$page=$p->show();  
		$this->assign('list',$list);
		$this->assign('page',$page);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=User&a=add\', title:\'新增会员\', width:\'500\', height:\'350\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '新增会员');
        $this->assign('big_menu',$big_menu);

		$this->display();
	}
	function add(){
		if (isset($_POST['dosubmit'])) {
			//实例化
			$user_mod = D("user");
	    	$data = array();
			//检测是否重复
	    	$student_id = isset($_POST['student_id']) && trim($_POST['student_id']) ? trim($_POST['student_id']) : $this->error('请填写学号');
	    	$exist = $user_mod->where("student_id='".$student_id."'")->count();
			if($exist != 0){
				$this->error('该学号的会员已经存在');
			}
	    	$data = $user_mod->create();
	        $data['add_time'] = time();
	        $data['password'] = md5($data['password']);

	    	$uid = $user_mod->add($data);
						
	    	$this->success(L('operation_success'), '', '', 'add');

		} else {
			$this->display();
		}

	}
	function edit(){
		if (isset($_POST['dosubmit'])) {
			$mod = D('user');
			$book_mod = D('course_book');		
			$user_data = $mod->create();			
			$pass=trim($_REQUEST['pass']);
			
			if(!empty($pass)){
				$user_data['password']=md5($pass);
			}
			$result_info=$mod->where("id=". $user_data['id'])->save($user_data);
			if(false !== $result_info){
					//同步该用户的信息到预定表
					$user_course_book = $book_mod->where("uid=".$user_data['id'])->select();
					if ($user_course_book){
						foreach($user_course_book as $val){
						$val['uname'] = $user_data['name'];
						$val['ustudent_id'] = $user_data['student_id'];
						$val['uphone'] = $user_data['phone'];
						$book_mod->save($val);
						}
					}
				$this->success(L('operation_success'), '', '', 'edit');
			}else{				
				$this->success(L('operation_failure'));
			}
		} else {
			$mod = D('user');		
			if (isset($_GET['id'])) {
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error('请选择要编辑的会员');
			}
			$user = $mod->where('id='. $id)->find();		
			$this->assign('info', $user);
			$this->display();
		}
	}
	public function delete()
    {
		$user_mod = D('user');
		if(!isset($_POST['id']) || empty($_POST['id'])) {
            $this->error('请选择要删除的会员！');
		}	
		if( isset($_POST['id'])&&is_array($_POST['id']) ){			
			foreach( $_POST['id'] as $val ){
				$user_mod->delete($val);
			}			
		}else{
			$id = intval($_POST['id']);			
		    $user_mod->where('id='.$id)->delete();	
		}
		$this->success(L('operation_success'));
    }
}