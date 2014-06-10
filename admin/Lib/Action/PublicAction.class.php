<?php

class PublicAction extends BaseAction
{
	// 菜单页面
	public function menu(){
		//显示菜单项
		$id	=	intval($_REQUEST['tag'])==0?6:intval($_REQUEST['tag']);
		$menu  = array();
		$role_id = D('admin')->where('id='.$_SESSION['admin_info']['id'])->getField('role_id');
		$node_ids_res = D("access")->where("role_id=".$role_id)->field("node_id")->select();
		
		$node_ids = array();
		foreach ($node_ids_res as $row) {
			array_push($node_ids,$row['node_id']);
		}
		//读取数据库模块列表生成菜单项
		$node    =   M("node");
		$where = "auth_type<>2 AND status=1 AND is_show=0 AND group_id=".$id;
		$list	=$node->where($where)->field('id,action,action_name,module,module_name,data')->order('sort DESC')->select();
		foreach($list as $key=>$action) {
			$data_arg = array();
			if ($action['data']){
				$data_arr = explode('&', $action['data']);
				foreach ($data_arr as $data_one) {
					$data_one_arr = explode('=', $data_one);
					$data_arg[$data_one_arr[0]] = $data_one_arr[1];
				}
			}
			$action['url'] = U($action['module'].'/'.$action['action'], $data_arg);
			if ($action['action']) {
				$menu[$action['module']]['navs'][] = $action;
			}
			$menu[$action['module']]['name']	= $action['module_name'];
			$menu[$action['module']]['id']	= $action['id'];
		}
		$this->assign('menu',$menu);
		$this->display('left');
	}
	/**	 
	 * 后台主页
	 */
	public function main()
	{
        $disk_space = @disk_free_space(".")/pow(1024,2);
        $this->assign('set',$this->setting);
		$this->display();
	}
	public function login()
	{
		$admin_mod=M('admin');
		if ($_POST) {
			$username = $_POST['username'] && trim($_POST['username']) ? trim($_POST['username']) : '';
			$password = $_POST['password'] && trim($_POST['password']) ? trim($_POST['password']) : '';
			if (!$username || !$password) {
				redirect(u('Public/login'));
			}
			//生成认证条件
			$map  = array();
			$map['user_name']	= $username;
			$map["status"]	=	array('eq',1);			
			$admin_info=$admin_mod->where($map)->find();
			//使用用户名、密码和状态的方式进行认证
			if(!$admin_info) {
				$this->error('帐号不存在或已禁用！');
			}else {
				if($admin_info['password'] != md5($password)) {
					$this->error('密码错误！');
				}

				$_SESSION['admin_info'] =$admin_info;
				$this->success('登录成功！',u('Index/index'));
				exit;
			}
		}
		$this->assign('set',$this->setting);
		$this->display();
	}
	//退出登录
	public function logout()
	{
		if(isset($_SESSION['admin_info'])) {
			unset($_SESSION['admin_info']);			
			$this->success('退出登录成功！',u('Public/login'));
		}else {
			$this->error('已经退出登录！');
		}
	}
 	//验证码
    public function verify(){
    	import("ORG.Util.Image");
        Image::buildImageVerify(4,1,'gif','50','24');
    }
/*
	 * 清除缓存
	 * */
    function clearCache()
    {
    	import("ORG.Io.Dir");
    	$dir = new Dir;

    	if (is_dir(CACHE_PATH) )
		{
			$dir->del(CACHE_PATH);
		}
		if (is_dir(TEMP_PATH) )
		{
			$dir->del(TEMP_PATH);
		}
		if (is_dir(LOG_PATH) )
		{
			$dir->del(LOG_PATH);
		}
		if (is_dir(DATA_PATH.'_fields/') )
		{
			$dir->del(DATA_PATH.'_fields/');
		}

		if (is_dir("./index/Runtime/Cache/") )
		{
			$dir->del("./index/Runtime/Cache/");
		}

		if (is_dir("./index/Runtime/Temp/") )
		{
			$dir->del("./index/Runtime/Temp/");
		}

		if (is_dir("./index/Runtime/Logs/") )
		{
			$dir->del("./index/Runtime/Logs/");
		}

		if (is_dir("./index/Runtime/Data/_fields/") )
		{
			$dir->del("./index/Runtime/Data/_fields/");
		}
		$this->display('index');
    }
}
?>