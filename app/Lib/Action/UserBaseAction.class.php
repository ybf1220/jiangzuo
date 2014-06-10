<?php
class UserBaseAction extends Action {
	public function _initialize() {
		// 用户权限检查
		$this->checkAuthority();
		//需要登陆
		$user_info =$_SESSION['user_info'];
		
		$this->assign('user_info', $user_info);
	}
	public function checkAuthority()
	{
		if(in_array(ACTION_NAME, array('findpass','dofindpass','resetpassword','doresetpassword'))) {
			return true;
		}
		if ((!isset($_SESSION['user_info']) || !$_SESSION['user_info']) && !in_array(ACTION_NAME, array('login','signup'))) {
			if(isset($_COOKIE['student_id']) && isset($_COOKIE['password'])){
			$student_id = $_COOKIE['student_id'];
			$password = $_COOKIE['password'];
			$user_info=D('user')->where("student_id = '$student_id' and password = '$password'")->find();
			$_SESSION['user_info'] =$user_info;
			SESSION('user_info',$user_info);
			return true;
			}
			$this->redirect('user/login');
		}
		if ((isset($_SESSION['user_info']) || $_SESSION['user_info']) && in_array(ACTION_NAME, array('login','signup'))) {
			$this->redirect('user/index');
		}
		if ((isset($_SESSION['user_info']) || $_SESSION['user_info'])) {
			return true;
		}
		if(in_array(ACTION_NAME, array('login','signup'))) {
			if(isset($_COOKIE['student_id']) && isset($_COOKIE['password'])){
			$student_id = $_COOKIE['student_id'];
			$password = $_COOKIE['password'];
			$user_info=D('user')->where("student_id = $student_id and password = '$password'")->find();
			$_SESSION['user_info'] =$user_info;
			SESSION('user_info',$user_info);
			$this->redirect('user/index');
			}
			return true;
		}
		if ($rel==0) {
			$this->error(L('_VALID_ACCESS_'));
		}
	}	

}

?>