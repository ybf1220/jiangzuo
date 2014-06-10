<?php

class bookAction extends BaseAction{
	function book(){
        $mod=D('course_book'); 
        $pagesize=100;        
        import("ORG.Util.Page");
		if(!empty($_GET['ctitle'])){
			$ctitle = $_REQUEST['ctitle'];
			$this->assign('ctitle',$ctitle);
			$where['ctitle'] = array('like',"%$ctitle%");
		}
		if(!empty($_GET['ustudent_id'])){
			$ustudent_id = $_REQUEST['ustudent_id'];
			$this->assign('ustudent_id',$ustudent_id);
			$where['ustudent_id'] = array('like',"%$ustudent_id%");
		}
		if(!empty($_GET['uname'])){
			$uname = $_REQUEST['uname'];
			$this->assign('uname',$uname);
			$where['uname'] = array('like',"%$uname%");
		}
		$count=$mod->where($where)->count();		
		$p = new Page($count,$pagesize);		
		$list=$mod->where($where)->order("id desc")->limit($p->firstRow.','.$p->listRows)->select();
		$page=$p->show();  
		$this->assign('list',$list);
		$this->assign('page',$page);
		//输出现在时间用于模板判断是否能取消预约
		$this->assign('nowtime',time());
		
		$this->display();
	}
	public function cancle_book(){
		if(isset($_POST['dosubmit'])){
			$id = trim($_POST['id']);
			$booked = D('course_book')->find($id);
				if($booked['start_time'] < time()){
				$this->error('这个讲座已经开始啦，不能取消预约！');
				}
			if(D('course_book')->delete($id)){
				D('course')->where("id =".$booked['cid'])->setDec('people',1);
	    		$this->success("取消预约成功", '', '', 'add');
			}else{
			$this->error("取消预约出错！");
			}

		}else{
			if (isset($_GET['id'])) {
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error('请选择要取消的预约');
			}

		$info= D('course_book')->where("id = $id")->find();
		$this->assign('info', $info);
		$this->display();
		}
	}
}
?>