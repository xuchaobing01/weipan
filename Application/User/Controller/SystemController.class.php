<?php
namespace User\Controller;

class SystemController extends UserController{
	public function index(){
		$this->display();
	}
	
	/**
	 *@method role 角色管理
	 */
	public function role(){
		$list = M('users_role')->where(['token'=>session('token')])->select();
		$this->assign('list',$list);
		
		$this->display();
	}
	
	public function role_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$data['name'] = $_POST['name'];
			$data['status'] = $_POST['status'];
			$data['remark'] = $_POST['remark'];
			if(empty($id)){
				$data['token'] = session('token');
				$ret = M('users_role')->add($data);
				if($ret) $this->success('添加成功！',U('role'));
				else $this->error('添加失败！');
			}
			else{
				$ret = M('users_role')->where(['token'=>session('token'),'id'=>$id])->save($data);
				if($ret) $this->success('编辑成功！',U('role'));
				else $this->error('编辑失败！');
			}
		}
		else{
			if(!empty($id)){
				$role = M('users_role')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('role',$role);
			}
			$this->display();
		}
	}
	
	/**
	 *@method role_delete
	 *@desc 删除角色
	 */
	public function role_delete(){
		$id = I('get.id',0,'intval');
		$role = M('users_role')->where(['token'=>session('token'),'id'=>$id])->find();
		!$role && $this->error('角色不存在！');
		$check = M('sub_users')->where(['master_uid'=>session('uid'),'role_id'=>$id])->count();
		if($check !=0) $this->error('请先删除该角色里的用户！');
		$ret = M('users_role')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！',U('role'));
		else $this->error('删除失败！');
	}
	
	public function role_access(){
		if(IS_POST){
			$id = I('get.id',0,'intval');
			$rules = implode(',',$_POST['rule']);
			$ret = M('users_role')->where(['token'=>session('token'),'id'=>$id])->setField('rules',$rules);
			if($ret) $this->success('更新成功！');
			else $this->error('更新失败！');
		}
		else{
			$menus = $this->_get_menus();
			$id = I('get.id',0,'intval');
			$role = M('users_role')->where(['token'=>session('token'),'id'=>$id])->find();
			
			$rules = array();
			if($role['rules'] != ''){
				$rules_s = explode(',',$role['rules']);
				
				foreach($rules_s as $item){
					$rules[$item] = true;
				}
			}
			$this->assign('role',$role);
			$this->assign('menus',$menus);
			$this->assign('rules',$rules);
			$this->display();
		}
	}
	
	
	/**
	 *@method account 子账号管理
	 */
	public function account(){
		$model = new \User\Model\SubUsersModel();
		$list = $model->where(['master_uid'=>session('uid')])->relation('role')->select();
		$this->assign('list',$list);
		
		$this->display();
	}
	
	public function account_edit(){
		$id = I('get.id',0,'intval');
		$uid = session('uid');
		if(IS_POST){
			$data['username'] = $_POST['username'];
			if($_POST['passwd'] != ''){
				$data['password'] = md5($_POST['passwd']);
			}
			$data['status'] = $_POST['status'];
			$data['remark'] = $_POST['remark'];
			$data['role_id'] = $_POST['role_id'];
			
			if(empty($id)){
				$data['master_uid'] = $uid;
				$data['create_time'] = time();
				if(empty($data['password'])){
					$data['password'] = md5('123456');
				}
				$ret = M('sub_users')->add($data);
				if($ret) $this->success('添加成功！',U('account'));
				else $this->error('添加失败！');
			}
			else{
				$ret = M('sub_users')->where(['master_uid'=>$uid,'id'=>$id])->save($data);
				if($ret) $this->success('编辑成功！',U('account'));
				else $this->error('编辑失败！');
			}
		}
		else{
			if(!empty($id)){
				$account = M('sub_users')->where(['master_uid'=>$uid,'id'=>$id])->find();
				$this->assign('account',$account);
			}
			$roles = M('users_role')->field('id,name')->where(['token'=>session('token'),'status'=>1])->select();
			if($roles == null){
				$this->error('请先添加角色！',U('role_edit'));
			}
			$this->assign('roles',$roles);
			$this->display();
		}
	}
	
	public function account_delete(){
		$id = I('get.id',0,'intval');
		$ret = M('sub_users')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！',U('account'));
		else $this->error('删除失败！');
	}
	
	private function _get_menus(){
		$menus = M('menu')->where(['hide'=>0,'pid'=>0])->field('id,title,url,tip')->order('sort asc')->select();//获取一级菜单
		$model = M('Menu');
		foreach($menus as $key => $item){
			$map['pid'] =   $item['id'];
			$map['hide']    =   0;
			if(!C('DEVELOP_MODE')){ // 是否开发者模式
				$map['is_dev']  =   0;
			}
			$menuList = M('Menu')->where($map)->field('id,pid,title,url,tip,group')->order('`group` asc,sort asc,id asc')->select();
			foreach($menuList as $i => $menu){
				$actions = $model->where(['pid'=>$menu['id'],'hide'=>0])->field('id,pid,title,url,tip')->order('sort asc,id asc')->select();
				$menuList[$i]['actions'] = $actions;
			}
			$menus[$key]['child'] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
		}
		return $menus;
	}
	
	public function verifyuser(){
		/* RECEIVE VALUE */
		$validateValue=$_REQUEST['fieldValue'];
		$validateId=$_REQUEST['fieldId'];
		
		/* RETURN VALUE */
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;
		//验证用户名不能重复
		if((M('sub_users')->where(['username'=>$validateValue,'master_uid'=>session('uid')])->find()) == null){
			$arrayToJs[1] = true;			// RETURN TRUE
			echo json_encode($arrayToJs);	// RETURN ARRAY WITH success
		}
		else {
			$arrayToJs[1] = false;
			echo json_encode($arrayToJs);	// RETURN ARRAY WITH ERROR
		}
	}
}
?>