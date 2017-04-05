<?php
namespace Common\Model;
use Think\Model;
class WechatUserModel extends Model{
	/**
	 *@method get 获取微信用户信息
	 */
	public $token;
	public $wxConfig;
	public function get($wechat_id){
		$u = $this->where(['open_id'=>$wechat_id])->find();
		if($u == null){
			if($this->wxConfig['wxtype']=='服务号'&&$this->wxConfig['is_certified']==1){
				Vendor('Spark.Wechat.WechatUtil');
				$util = new WechatUtil($this->wxConfig['appid'],$this->wxConfig['appsecret']);
				$user = $util->getUser($wechat_id);
				$data['token'] =$this->token;
				$data['open_id'] = $wechat_id;
				$data['wechat_name'] = $user->nickname;
				$data['sex'] = $user->sex;
				$data['language'] = $user->language;
				$data['country'] = $user->country;
				$data['province'] = $user->province;
				$data['headimgurl'] = substr($user->headimgurl,0,-1);
				$data['subscribe_time'] = $user->subscribe_time;
				$data['subscribe'] = $user->subscribe;
				$data['update_time'] = time();
				$this->add($data);
				return $data;
			}
			else return array();
		}
		return $u;
	}
}
?>
