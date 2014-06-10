<?php

class courseAction extends BaseAction{
	function course(){
		$mod = D('course');
		import("ORG.Util.Page");
		$count = $mod->count();
		$p = new Page($count,30);
		$list = $mod->limit($p->firstRow.','.$p->listRows)->order('add_time DESC')->select();
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('list',$list);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=course&a=course_add\', title:\'添加讲座\', width:\'500\', height:\'330\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加讲座');
        $this->assign('big_menu',$big_menu);
		$this->display();
	}

	function course_add()
	{
		if(isset($_POST['dosubmit'])){
			$mod = D('course');
	    	$data = array();
	    	$data = $mod->create();
		    $data['start_time'] = strtotime($data['start_time']);
		    $data['add_time'] = time();

	    	$mod->add($data);
	    	$this->success(L('operation_success'), '', '', 'add');

		}else{
			$this->display();
		}
	}

	function course_edit()
	{
		if(isset($_POST['dosubmit'])){
			$mod = D('course');
	    	$data = array();
	    	$id = isset($_POST['id']) && intval($_POST['id']) ? intval($_POST['id']) : $this->error('参数错误');
	    	$data = $mod->create();
			//是否有修改时间
			if(!empty($_POST['edit_time'])){
		    $data['start_time'] = strtotime($_POST['edit_time']);
			}

			$result = $mod->where("id=".$data['id'])->save($data);
			if(false !== $result){
				$this->success(L('operation_success'), '', '', 'edit');
			}else{
				$this->error(L('operation_failure'));
			}

		}else{
			$mod = D('course');

			if( isset($_GET['id']) ){
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error('请选择要编辑的时间段');
			}
			$info = $mod->where('id='.$id)->find();
			$this->assign('info', $info);
			$this->display();
		}
	}

	function course_delete()
    {
		$mod = D('course');
		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
            $this->error('请选择要删除的讲座！');
		}
		if( isset($_POST['id'])&&is_array($_POST['id']) ){
			$ids = implode(',',$_POST['id']);
			$mod->delete($ids);
		}else{
			$id = intval($_GET['id']);
		    $mod->where('id='.$id)->delete();
		}
		$this->success(L('operation_success'));
    }
	

}
?>