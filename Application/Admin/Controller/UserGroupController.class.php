<?php
namespace Admin\Controller;
use User\Model;
use Spark\Util\Page;

class UserGroupController extends BaseController{
	public function index(){
		$map = array();
		$UserDB = M('User_group');
		$list = $UserDB->where($map)->order('id ASC')->select();			
		$this->assign('list',$list);
		$this->assign('meta_title','版本管理');
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$data['name'] = trim($_POST['name']);
			$data['request_limit'] = trim($_POST['request_limit']);
			$data['member_card_limit'] = trim($_POST['member_card_limit']);
			$data['free_sms_count'] = trim($_POST['free_sms_count']);
			$data['activity_limit'] = trim($_POST['activity_limit']);
			$data['iscopyright'] = I('post.iscopyright',0,'intval');
			if($id){
				$data['id'] = $id;
				$ret = M('user_group')->save($data);
			}
			else{
				$ret = M('user_group')->add($data);
			}
			if($ret)$this->success('操作成功！');
			else $this->error('操作失败！');
		}
		else{
			if($id){
				$this->assign('meta_title','版本编辑');
				$info = M('User_group')->where(array('id'=>$id))->find();
				$this->assign('info',$info);
			}
			else{
				$this->assign('meta_title','添加版本');
			}
			$this->display();			
		}			
	}
	
	public function del(){
		$id=I('get.id',0,'intval');
		if($id==0)$this->error('非法操作');
		$check = M('users')->where('gid='.$id)->count();
		if($check>0)$this->error('无法删除，该版本有用户使用！');
		$info = M('User_group')->delete($id);
		if($info){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function access(){
		if(IS_POST){
			$id = I('get.id',0,'intval');
			$model = M('user_group_menu');
			$rules = $_POST['rule'];
			//先删除
			$model->where("group_id=".$id)->delete();
			//再添加
			foreach($rules as $menu){
				$model->add(array('menu_id'=>$menu,'group_id'=>$id));
			}
			$this->success('更新成功！');
		}
		else{
			$menus = $this->_get_menus();
			$id = I('get.id',0,'intval');
			$role = M('user_group')->where(['id'=>$id])->find();
			$rules = M('user_group_menu')->where("group_id=".$id)->field('menu_id')->select();
			if($rules != null){
				foreach($rules as $item){
					$rules[$item['menu_id']] = true;
				}
			}
			else $rules = array();
			$this->assign('meta_title','访问授权');
			$this->assign('role',$role);
			$this->assign('menus',$menus);
			$this->assign('rules',$rules);
			$this->display();
		}
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
}
?>