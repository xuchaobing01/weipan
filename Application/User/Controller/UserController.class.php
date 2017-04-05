<?php
namespace User\Controller;
class UserController extends BaseController{
	private $_defaultGroup = 2;
	protected function _initialize(){
		parent::_initialize();
		if(!$this->is_open_action()){
			if(CONTROLLER_NAME == "Channel" && $_SESSION['channel']==false){
				$this->redirect('User/Channel/login');
				exit;
			}else if(CONTROLLER_NAME != "Channel"  && session('uid') == false){
				$this->redirect('User/User/login');
				exit;
			}
			else{
				if(CONTROLLER_NAME == 'Index'){
					$umenu = $this->_get_display_menu();
					$this->assign('__MENU__',$umenu);//当前用户权限菜单
				}
			}
		}
	}
	
	/**
	 *@method check_access
	 *@desc 检查当前当前用户是否拥有链接访问权限
	 */
	protected function check_access($url){
		if(empty($url)||empty($_SESSION['account_id'])) return true;
		$rule = $this->get_menu_item($url);
		$rules = session('access_rule');
		if($rule == null || !$rules[$rule['id']]) return false;
		return true;
	}
	
	/**
	 *@method get_menu_item
	 *@desc 缓存菜单项
	 */
	protected function get_menu_item($url){
		if(S('MENU_'.$url)==false){
			$rule = M('menu')->where(['url'=>$url])->field('id')->find();
			S('MENU_'.$url,$rule,600);
			return $rule;
		}
		return S('MENU_'.$url);
	}
	
	public function clear_menus_cache(){
		if(session('role_id')){
			$cache_key = session('uid').session('role_id').'_MENUS_';
		}
		else $cache_key = session('uid').'_MENUS_';
		//dump(S($cache_key));
		S($cache_key,null);
		echo 'success';
	}
	
	/**
	 *@method _get_menu 缓存当前用户的菜单
	 */
	private function _get_menus(){
		if(session('role_id')){
			$cache_key = session('uid').session('role_id').'_MENUS_';
		}
		else $cache_key = session('uid').'_MENUS_';
		
		if(S($cache_key)==false){
			$account_id = session('account_id');
			if($account_id){
				if(session('access_rule')){
					$rules = session('access_rule');
				}
				else{
					$role = M('users_role')->where(['id'=>session('role_id')])->find();
					$rules_s = explode(',',$role['rules']);
					$rules = array();
					foreach($rules_s as $rule){
						$rules[$rule] = true;
					}
					session('access_rule',$rules);
				}
			}
			$menus = M('v_user_group_menu')->where(['hide'=>0,'pid'=>0,'group_id'=>session('gid')])->field('id,title,url,tip')->order('sort asc')->select();
			//获取一级菜单
			foreach($menus as $key => $item){
				if($account_id && !$rules[$item['id']]){
					unset($menus[$key]);
					continue;
				}
				$groups = M('menu')->where("pid = {$item['id']}")->distinct(true)->order('sort asc,id asc')->field("`group`")->select();
				if($groups){
					$groups = array_column($groups, 'group');
				}
				else{
					$groups =   array();
				}
				
				// 按照分组生成子菜单树
				foreach ($groups as $g) {
					$map = array('group'=>$g);
					$map['pid'] =   $item['id'];
					$map['hide']    =   0;
					$map['group_id']    =   session('gid');
					if(!C('DEVELOP_MODE')){ // 是否开发者模式
						$map['is_dev']  =   0;
					}
					$menuList = M('v_user_group_menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc,id asc')->select();
					foreach($menuList as $i=>$menu){
						if($account_id && !$rules[$menu['id']]){
							unset($menuList[$i]);
						}
					}
					if(count($menuList)!=0) $menus[$key]['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
				}
			}
			S($cache_key,$menus,600);
			return $menus;
		}
		return S($cache_key);
	}
	
	/**
	 *@method _get_display_menu 获取显示菜单
	 */
	private function _get_display_menu(){
		$all_menus = $this->_get_menus();
		//dump($all_menus[4]);exit;
		$umenu['root'] = array();
		foreach($all_menus as $menu){
			$root = array();
			$root['id'] = $menu['id'];
			$root['title'] = $menu['title'];
			$root['pid'] = $menu['pid'];
			$root['url'] = $menu['url'];
			$root['tip'] = $menu['tip'];
			$umenu['root'][$menu['id']] = $root;
		}
		
		$current_url = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		//$current_url = 'User/Other/index';
		foreach($all_menus as $root){
			if(strpos($root['url'],$current_url)!== FALSE){
				$umenu['root'][$root['id']]['active'] = true;
				$umenu['current'] = $root;
			}
			foreach($root['child'] as $sub_menu_group){
				foreach($sub_menu_group as $menu){
					if(strpos($menu['url'],$current_url)!== FALSE){
						$umenu['root'][$root['id']]['active'] = true;
						$umenu['current'] = $root;
					}
				}
			}
		}
		return $umenu;
	}
	
	/**
	 *@method 判断是否是匿名访问操作
	 */
	protected function is_open_action(){
		return in_array(CONTROLLER_NAME, ['User','Channel']) && in_array(ACTION_NAME,['login','reg','verifyuser','logout']);
	}
	
	/**
	 *@method login 用户登录
	 */
	public function login(){
		if(IS_POST){
			$uname = I('post.username','','trim');
			$pwd = I('post.password','','trim,md5');
			$ret = \User\Model\UsersModel::authenticate($uname,$pwd);
			if($ret->statusCode == 0){
				//每个月第一次登陆公众号计数器清零
				$now = time();
				$month = date('m', $now);
				$m = date('m', intval(session('last_login_time')));
				if($month!=$m && $m!=0){
					\User\Model\WxuserModel::clear();
				}
				$this->ajaxReturn(['message'=>'登录成功','statusCode'=>0]);
			}
			else{
				$this->ajaxReturn(['message'=>$ret->message,'statusCode'=>400]);
			}
		}
		else{
			$this->display();
		}
	}
	
	// 用户登出
    public function logout() {
        if(session('uid')) {
			S(session('uid').session('role_id').'_MENUS_',null);
			session(null);
			session_destroy();
			unset($_SESSION);
            redirect(U('User/User/login'));
        }else {
            $this->error('已经登出！',U('User/User/login'));
        }
    }
	
	public function verifyuser(){
		/* RECEIVE VALUE */
		$validateValue=$_REQUEST['fieldValue'];
		$validateId=$_REQUEST['fieldId'];
		
		/* RETURN VALUE */
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;
		//验证用户名不能重复
		if(is_null(M('Users')->where("username='$validateValue'")->find())){		// validate
			$arrayToJs[1] = true;			// RETURN TRUE
			echo json_encode($arrayToJs);	// RETURN ARRAY WITH success
		}
		else {
			$arrayToJs[1] = false;
			echo json_encode($arrayToJs);	// RETURN ARRAY WITH ERROR
		}
	}
	
	/**
	 * checkreg 用户注册
	 * 新增用户添加到体验用户组
	 */
	public function reg() {
		if(IS_POST){
			$db = new \User\Model\UsersModel();
			$group = M('User_group')->find($this->_defaultGroup);
			if($db->create()) {
				$email = $_POST['email'];
				$cellphone = $_POST['cellphone'];
				$qq = $_POST['qq'];
				$db->gid = $this->_defaultGroup;
				$id = $db->add();
				if($id) {
					$viptime=time() + 1*30*24*3600;//注册后可试用1月
					$db->where(array('id'=>$id))->save(array('viptime'=>$viptime,'status'=>1));
					session('uid', $id);
					session('gid', $this->_defaultGroup);
					session('uname', $_POST['username']);
					session('activity_num', 0);
					session('viptime', $viptime);
					session('gname', $group['name']);
					
					/* 邮件通知
					if(!empty($email)){
						$this->registerNotify($email);
					}*/
					// 通知管理员
					$this->notifyAdmin($cellphone,$qq);
					$this->success('注册成功',U('Index/index'));
				}
				else {
					$this->error('注册失败',U('User/reg'));
				}
			}
			else {
				$this->error($db->getError(),U('User/reg'));
			}
		}
		else{
			$this->display();
		}
	}
	
	//用户注册成功后邮件通知
	private function registerNotify($email){
		//给用户发送注册通知
		$subject =  C('reg_mail_subject');
		$content = str_replace('{username}',session('uname'),C('reg_mail_content'));
		$this->email($email, $subject, $content);
	}
	
	private function notifyAdmin($cellphone, $qq, $email){
		if(empty($email)){
			$email = C('EMAIL_NOTICE_ACCOUNT');
		}
		$subject =  '新用户注册通知';
		$uname = session('uname');
		$content = "<ul><li>用户名：{$uname}</li><li>手机号：{$cellphone}</li><li>QQ：{$qq}</li></ul>";
		email($email, $subject, $content);
	}
	
	public function checkpwd(){
		$where['username'] = $_POST('username');
		$where['email']= $_POST('email');
		$db=D('Users');
		$list=$db->where($where)->find();
		
		if($list==false) { 
			$this->error('邮箱和帐号不正确',U('Index/regpwd'));
		}
		
		$smtpserver = C('email_server'); 
		$port = C('email_port');
		$smtpuser = C('email_user');
		$smtppwd = C('email_pwd');
		$mailtype = "TXT";
		$sender = C('email_user');
		$smtp = new Smtp($smtpserver,$port,true,$smtpuser,$smtppwd,$sender); 
		$to = $list['email']; 
		$subject = C('pwd_email_title');
		$code = C('site_url').U('Index/resetpwd',array('uid'=>$list['id'],'code'=>md5($list['id'].$list['password'].$list['email']),'resettime'=>time()));
		$fetchcontent = C('pwd_email_content');
		$fetchcontent = str_replace('{username}',$where['username'],$fetchcontent);
		$fetchcontent = str_replace('{time}',date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),$fetchcontent);
		$fetchcontent = str_replace('{code}',$code,$fetchcontent);
		$body=$fetchcontent;
		//$body = iconv('UTF-8','gb2312',$fetchcontent);
		$send=$smtp->sendmail($to,$sender,$subject,$body,$mailtype);
		$this->success('请访问你的邮箱 '.$list['email'].' 验证邮箱后登录!<br/>');
	}
	
	public function resetpwd(){
		$where['id']=$this->_post('uid','intval');
		$where['password']=$this->_post('password','md5');
		if(M('Users')->save($where)){
			$this->success('修改成功，请登录！',U('Index/login'));
		}else{
			$this->error('密码修改失败！',U('Index/index'));
		}
	}
}