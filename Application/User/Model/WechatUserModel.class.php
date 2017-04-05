<?php
namespace User\Model;
use Think\Model;
class WechatUserModel extends Model{
	public $token;
	public $wxConfig;
	
	public function __construct($token){
		parent::__construct();
		$this->token = $token;
	}
	
	//读取所有粉丝信息
	public function cache(){
		$list = $this->where(['token'=>$this->token])->field('open_id')->select();
		$_list_cache = [];
		foreach($list as $user){
			$_list_cache[$user['open_id']] = $user;
		}
		
		return $this->_list_cache;
	}
	
	public function exists($openid){
		if(isset($this->_list_cache)){
			return isset($this->_list_cache[$openid]);
		}
		else return $this->where(['token'=>$this->token,'open_id'=>$openid])->find()==null?false:true;
	}
	
	/**
	 *@method get 获取微信用户信息
	 */
	public function get($wechat_id,$update=fasle){
		$u = $this->where(['open_id'=>$wechat_id])->find();
		if($this->wxConfig['wxtype']!='服务号'||$this->wxConfig['is_certified']!=1){
			return $u;
		}
		$savetime = time()-7*86400;
		if($u == null || $update){
			$util = new \Spark\Wechat\WechatUtil($this->wxConfig['appid'],$this->wxConfig['appsecret']);
			$user = $util->getUserInfo($wechat_id);
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
			if($data['subscribe']==1 ){
				$this->add($data);
			}
			return $data;
		}
		else if(empty($u['wechat_name']) || $u['update_time']<$savetime){
			$util = new \Spark\Wechat\WechatUtil($this->wxConfig['appid'],$this->wxConfig['appsecret']);
			$user = $util->getUserInfo($wechat_id);
			$data['token'] =$this->token;
			$data['wechat_name'] = $user->nickname;
			$data['sex'] = $user->sex;
			$data['language'] = $user->language;
			$data['country'] = $user->country;
			$data['province'] = $user->province;
			$data['headimgurl'] = substr($user->headimgurl,0,-1);
			$data['subscribe_time'] = $user->subscribe_time;
			$data['subscribe'] = $user->subscribe;
			$data['update_time'] = time();
			$this->where(['id'=>$u['id']])->save($data);
			return $data;
		}
		else return $u;
	}
}