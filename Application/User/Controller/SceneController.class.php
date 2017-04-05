<?php
namespace User\Controller;
use Think\Controller;
class SceneController extends UserController{
	public function index(){
		if(I('content')){
			$where['phone']=array('gt',0);
			$list = M("wechat_user")->where($where)->select();
			$wx = M('wxuser')->where(['token'=>session('token')])->field('appid,appsecret,is_certified')->find();
			$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);

			foreach ($list as $key => $value) {
				# code...
				$fromUserName=$value['open_id'];
				$msg= I("content")." - "."<a href='".I("link_url")."'>".I("link_name")."</a>";
				$ret = $util->sendCustomMsg($fromUserName,$msg);

			}
			$this->success('发送信息成功',U('Scene/index'));
		}
		$this->display();
	}
	
	public function smssend(){
		if(IS_POST){
			$content = I("content");
			if(!$content){	
				$this->error("短信内容不可为空");
			}	
			if(I("status")==2){//指定发送者
				$mobile = I("phone");
			}else{	
				$userlist = M('juejin_users')->field('phone')->select();
				foreach ($userlist as $val){
					$mobile .= $val['phone'].',';
				}
				$mobile = rtrim($mobile,',');
			}
			$res = send_newsms($mobile, $content);
			if($res){
				$this->success('短信发送信息成功',U('Scene/smssend'));
			}else{
				$this->error('短信发送信息失败');
			}
			die;
		}
		
		$this->display();
	}
	
}
?>