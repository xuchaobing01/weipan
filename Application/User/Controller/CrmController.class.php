<?php
/**
 *客户管理
**/
namespace User\Controller;
use Spark\Util\Page;
class CrmController extends UserController{
	public function index(){
		
	}
	
	/**
	 * @method qrcodeScene 场景二维码管理
	 **/
	public function qrcode_scene(){
		$list = M('qrcode_scene')->where(['token'=>session('token')])->select();
		foreach($list as $key=>$val){
			$list[$key]['count']=M('wechat_user')->where(array('scene_id'=>$val['scene_id'],'token'=>session('token')))->count();	
		}
		
		
		$this->assign('list',$list);
		$this->display();
	}
	
	public function qrcode_scene_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id){
				$_POST['id']=$id;
				$ret = M('qrcode_scene')->save($_POST);
				if($ret) $this->success('编辑成功！');
				else $this->error('编辑失败！');
			}
			else{
				$data['token']=session('token');
				$data['scene_name']=I('post.scene_name');
				$data['scene_desc']=I('post.scene_desc');
				$check = M('qrcode_scene')->where(['token'=>session('token')])->field('max(scene_id) as max_scene_id')->find();
				if($check == null) $data['scene_id'] = 1;
				else $data['scene_id'] = intval($check['max_scene_id'])+1;
				$ret = M('qrcode_scene')->add($data);
				if($ret) $this->success('添加成功！');
				else $this->error('添加失败！');
			}
		}
		else{
			if($id){
				$set = M('qrcode_scene')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function qrcode_scene_delete($id){
		$ret = M('qrcode_scene')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	public function qrcode_scene_img($id){
		$wx = M('wxuser')->where(['token'=>session('token')])->field('appid,appsecret,is_certified')->find();
		$scene = M('qrcode_scene')->where(['token'=>session('token'),'id'=>$id])->find();
		if($scene == null) exit('场景二维码不存在！');
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
		//dump($scene);exit;
		header('location:'.$util->getQrcodeUrl(intval($scene['scene_id'])));
	}
	
	/*
	 *@method user 获取关注者列表
	 */
	public function user(){
		$model = M('wechat_user');
		$where = ['token'=>session('token')];
		if(!empty($_GET['scene_id'])){
			$where['scene_id'] = $_GET['scene_id'];
		}
		$count=$model->where($where)->count();
		$page=new \Spark\Util\Page($count,20);
		$list = $model->where($where)->field('id,wechat_name,headimgurl,country,province,city,sex,subscribe_time')->limit($page->firstRow.','.$page->listRows)->select();
		
		$scene_list = M('qrcode_scene')->where(['token'=>session('token')])->select();
		$this->assign('scene_list',$scene_list);
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	/*
	 *@method user_sync 从公众平台同步粉丝
	 */
	public function user_sync(){
		$wx = M('wxuser')->where(['token'=>session('token')])->field('appid,appsecret,is_certified')->find();
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
		$list = $util->getUserList();
		
		if($list->count==0) $this->ajaxReturn(['status'=>100,'message'=>'暂时无粉丝！']);
		$token = session('token');
		$model = new \User\Model\WechatUserModel($token);
		$old_list = $model->cache(); //原来的数据
		$list_synced = [];
		foreach($list->data->openid as $openid){ //如果数据库不存在该粉丝，则保存
			$user = $util->getUser($openid);
			
			$data['wechat_name'] = $user->nickname;
			$data['sex'] = $user->sex;
			$data['language'] = $user->language;
			$data['country'] = $user->country;
			$data['province'] = $user->province;
			$data['headimgurl'] = substr($user->headimgurl,0,-1);
			$data['subscribe_time'] = $user->subscribe_time;
			$data['subscribe'] = $user->subscribe;
			$data['update_time'] = time();
			
			if(!$model->exists($openid)){ //要增加的
				$data['open_id'] = $openid;
				$data['token'] = $token;
				$data['scene_id'] = 0;
				$model->add($data);
				$list_synced[$openid] = 1;
			}
			else{ //更新的
				$model->where(['open_id'=>$openid])->save($data);
				$list_synced[$openid] = 2;
			}
			unset($old_list[$openid]);
		}
		//删除取消关注的粉丝
		if(count($old_list)>0){ 
			$key_to_removed = [];
			foreach($old_list as $key => $value){
				$key_to_removed[] = $key;
			}
			$model->where(['open_id'=>['in',$key_to_removed]])->delete();
		}
		$this->ajaxReturn(['status'=>0,'message'=>'数据同步成功！','count'=>$list->count]);
	}
	public function user_sync1(){
		$wx = M('wxuser')->where(['token'=>session('token')])->field('appid,appsecret,is_certified')->find();
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);

		$map['scene_id']  = array('gt',0);
		$map['token']  = session('token');
		
		$list = M('wechat_user')->where($map)->select();
		if(empty($list))$this->ajaxReturn(['status'=>100,'message'=>'暂时无粉丝！']);		
		foreach ($list as $val){
			$openid =  $val['open_id'];
			$user = $util->getUser($openid);			
			$data['wechat_name'] = $user->nickname;
			$data['sex'] = $user->sex;
			$data['language'] = $user->language;
			$data['country'] = $user->country;
			$data['province'] = $user->province;
			$data['headimgurl'] = substr($user->headimgurl,0,-1);
			$data['subscribe_time'] = $user->subscribe_time;
			$data['subscribe'] = $user->subscribe;
			$data['update_time'] = time();
			M('wechat_user')->where(['open_id'=>$openid])->save($data);
			
		}
		$this->ajaxReturn(['status'=>0,'message'=>'数据同步成功！','count'=>count($list)]);
		
	}
	
	public function test(){
		$wx = M('wxuser')->where(['token'=>session('token')])->field('appid,appsecret,is_certified')->find();
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
		dump($util->getQrcodeUrl(100));
	}
}
?>