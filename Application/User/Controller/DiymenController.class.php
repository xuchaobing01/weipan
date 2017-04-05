<?php
namespace User\Controller;
class DiymenController extends UserController{
	//自定义菜单配置
	public function index(){
		$data = M('Diymen_set')->where(array('token'=>session('token')))->find();
		$wx = M('wxuser')->field('appid,appsecret,is_certified,wxtype')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			$_POST['token'] = session('token');
			if($data==false){
				$this->all_insert('Diymen_set');
			}
			else{
				$_POST['id']=$data['id'];
				$this->all_save('Diymen_set');
			}
		}
		else{
			$this->assign('diymen',$data);
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort asc')->select();
			foreach($class as $key=>$vo){
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id']))->order('sort asc')->select();
				$class[$key]['class']=$c;
			}
			$this->assign('class',$class);
			$this->assign('wx',$wx);
			$this->display();
		}
	}
	
	public function  class_del(){
		$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>I('get.id')))->order('sort desc')->find();
		if($class==false){
			$back=M('Diymen_class')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete();
			if($back==true){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
		else{
			$this->error('请删除该分类下的子分类');
		}
	}
	
	/**
	 * @method class_edit 编辑或添加菜单
	 */
	public function  class_edit(){
		if(IS_POST){
			$_POST['token'] = session('token');
			$id = $this->auto_save('DiymenClass');
			$this->ajaxReturn(['status'=>0,'message'=>'success','record_id'=>$id]);
		}
		else{
			$id = I('get.id',0,'intval');
			if($id){
				$data=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$id))->find();
				if($data==false){
					$this->error('您所操作的数据对象不存在！');
					exit;
				}
				else{
					$this->assign('show',$data);
				}
			}
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();
			$this->assign('class',$class);
			$this->display();
		}
	}
	
	public function menu_remove(){
		$api=M('Diymen_set')->where(array('token'=>session('token')))->find();
		if($api['appid']==false||$api['appsecret']==false){
			$this->error('必须先填写【AppId】【 AppSecret】');
			exit;
		}
		$util = new \Spark\Wechat\WechatUtil($api['appid'],$api['appsecret']);
		$ret = $util->deleteMenu();
		if($ret->errcode==0){
			$this->success('菜单删除成功！',U('index'));
		}
		else{
			$this->error('菜单删除失败,errorCode:'.$this->errmsg,U('index'));
		}
	}
	
	public function  class_send(){
		$wx = M('wxuser')->field('appid,appsecret,is_certified,wxtype')->where(array('token'=>session('token')))->find();
		if($wx['wxtype']=='订阅号'&&$wx['is_certified']==0){
			$this->error('必须是服务号或认证过的服务号！');
		}
		else if(empty($wx['appid'])||empty($wx['appsecret'])){
			$this->error('必须先填写【AppId】【 AppSecret】');
		}
		$class = M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort asc')->select();
		if($class==null){
			$this->error('请先配置菜单！');
		}
		$menus = ['button'=>[]];
		foreach($class as $key=>$vo){
			//主菜单
			$menu = [];
			$menu['name'] = $vo['title'];
			$subClass = M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id']))->limit(5)->order('sort asc')->select();
			//子菜单
			if($subClass !=false){
				$menu['sub_button'] = [];
				foreach($subClass as $voo){
					$subMenu = ['name'=>$voo['title']];
					$voo['url']=str_replace(array('&amp;'),array('&'),$voo['url']);
					if($voo['url']){
						$subMenu['type']='view';
						$subMenu['url']=$voo['url'];
					}else{
						$subMenu['type']='click';
						$subMenu['key']=$voo['keyword'];
					}
					$menu['sub_button'][] = $subMenu;
				}
			}
			else{
				if ($vo['url']) {
					$menu['type']='view';
					$menu['url']=$vo['url'];
				}
				else{
					$menu['type']='click';
					$menu['key']=$vo['keyword'];
				}
			}
			$menus['button'][] = $menu;
		}
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
		$ret = $util->setMenu(json_encode($menus,JSON_UNESCAPED_UNICODE));
		if($ret->errcode!==0){
			$this->error('菜单创建失败！<br/>'.$ret->errcode.':'.$ret->errmsg);
		}
		else{
			$this->success('菜单创建成功！');
		}
	}
}
?>