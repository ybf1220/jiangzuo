<?php
class IndexAction extends BaseAction {
    public function index(){
		$mod = D('course');
		import("ORG.Util.Page");
		//不显示还没开始的讲座
		$map['start_time']  = array('gt',time());
		$count = $mod->where($map)->count();
		$p = new Page($count,5);
		$list = $mod->where($map)->limit($p->firstRow.','.$p->listRows)->order('add_time desc')->select();

		$page = $p->show();
			
			//获取用户信息，检查该用户是不是已经预约了该讲座
			if($_SESSION['user_info'])
			$uid = $_SESSION['user_info']['id'];
			$had_book = D('course_book')->where("uid = ".$uid)->select();
			foreach($had_book as $k){
			$check_book[$k['cid']] = 1;
			}
			$this->assign('check_book', $check_book);
	
		$this->assign('page',$page);
		$this->assign('list',$list);
		$this->assign('title', '最新讲座信息');
		$this->display();
    }
    public function history(){
		$mod = D('course');
		import("ORG.Util.Page");
		//只显示没开始的讲座
		$map['start_time']  = array('elt',time());
		$count = $mod->where($map)->count();
		$p = new Page($count,5);
		$list = $mod->where($map)->limit($p->firstRow.','.$p->listRows)->order('start_time desc')->select();
		$page = $p->show();
						
		$this->assign('page',$page);
		$this->assign('list',$list);
		$this->assign('title', '已结束讲座信息');
		$this->display();
    }
}