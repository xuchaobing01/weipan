<?php
namespace User\Controller;
class HomemenuController extends UserController{
	public $token;
	
	public function _initialize() {
		parent::_initialize();
		$this->token=session('token');
		$this->model=D('HomeMenu');
	}
	
	public function index(){
		$pid = I('get.pid',0,'intval');
		$menus = $this->model->where(['token'=>session('token'),'pid'=>$pid])->order('ordernum asc')->select();
		
		$this->assign('menus',$menus);
		$this->assign('pid',$pid);
		$this->display();
	}
	
	/**
	 * @method edit 添加或编辑导航菜单
	 */
	public function edit(){
		if(IS_POST){
			$id = $this->auto_save('HomeMenu');
			$this->ajaxReturn(['status'=>0,'message'=>'success','record_id'=>$id]);
		}
		else{
			$id = I('get.id',0,'intval');
			$pid = I('get.pid',0,'intval');
			if($id){
				$menu = $this->model->find($id);
				$this->assign('menu',$menu);
			}
			$this->assign('pid',$pid);
			$this->display();
		}
	}
	
	
	public function style(){
		if(IS_POST){
			$style=I('post.style',0,'intval');
			$ret = M('home')->where(['token'=>session('token')])->save(['menu_style'=>$style]);
			$this->ajaxReturn(['status'=>0,'message'=>'success']);
		}
		else{
			$home = M('home')->where(['token'=>session('token')])->find();
			$this->assign('style',$home['menu_style']);
			$this->display();
		}
	}
	
	public function del(){
		$id = I('get.id',0,'intval');
		$this->model->where("id=$id or pid=$id")->delete();
		$this->success('删除成功！',U('User/Homemenu/index'));
	}
	
	public function set(){
		$where=array('token'=>$this->token);
		$home=M('home')->where(array('token'=>session('token')))->find();
		if (!$home){
			$this->error('请先配置微网站信息',U('Home/set',array('token'=>session('token'))));
		}
		else {
			if(IS_POST){
				//保存版权信息和菜单颜色
				M('home')->where($where)->save(array('plugmenucolor'=>I('post.plugmenucolor'),'copyright'=>I('post.copyright')));
				$this->success('设置成功',U('Homemenu/set',array('token'=>$this->token)));
			}
			else {
				$homeInfo=M('home')->where($where)->find();
				if (!$homeInfo['plugmenucolor']){
					$homeInfo['plugmenucolor']='#ff0000';
				}
				$this->assign('homeInfo',$homeInfo);
				$this->display();
			}
		}
	}
}
?>