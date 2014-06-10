<?php

class BaseAction extends Action {
	public function _initialize() {
		$user_info =$_SESSION['user_info'];
		
		$this->assign('user_info', $user_info);

	}
	


}

?>