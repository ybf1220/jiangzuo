<?php

class SettingAction extends BaseAction
{
	function index()
	{   
		$this->assign('set',$this->setting);
		$this->display($_REQUEST['type']);
	}
	function edit()
	{
		$setting_mod = M('setting');
		foreach(addslashes_set($_POST['site']) as $key=>$val ){
			$setting_mod->where("name='".$key."'")->save(array('data'=>trim($val)));
		}
		$this->success('修改成功',U('Setting/index'));
	}
	
	
}
?>