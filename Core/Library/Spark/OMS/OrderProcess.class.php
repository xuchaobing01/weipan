<?php
namespace Spark\Wechat;
class MessageHandler {
	public function __construct($token, $xml) {
		$this->token=$token;
		if(!empty($xml)) {
			foreach($xml as $key =>$value){
				$key=lcfirst($key);
				$this->$key=$value;
			}
		}
		$this->validate() || exit;
	}
	
	/**
	 * @method validate 验证公众号请求数是否超出限额
	 */
	public function validate() {
		$uid = M('Wxuser')->where(['token'=>$this->token])->find()['uid'];
		if(!$uid){
			return false;
		}
		$user = new \User\Model\UsersModel();
		$user->find($uid);
		return $user->checkRequestNum();
	}
	
	public function U($module,$params=[]){
		if(empty($params['token'])){
			$params['token']=$this->token;
		}
		if(empty($params['wechat_id'])){
			$params['wechat_id']=$this->fromUserName;
		}
		return rtrim(C('wap_domain'),'/') . U($module,$params);
	}
	
	public function handle() {
	
	}
	
	public function success() {
	
	}
	
	public function error() {
	
	}
	
	public function log($message) {
	
	}
	
	/**
	 * @method saveRequest 记录用户的最后一次消息
	 * @param string 消息内容
	 * @param string 消息类型
	 */
	function saveRequest($key, $msgtype = 'text') {
		$rdata              = array();
		$rdata['time']      = time();
		$rdata['token']     = $this->token;
		$rdata['keyword']   = $key;
		$rdata['msgtype']   = $msgtype;
		$rdata['uid']       = $this->fromUserName;
		$user_request_model = M('User_request');
		$user_request_row   = $user_request_model->where(array(
			'token' => $this->token,
			'msgtype' => $msgtype,
			'uid' => $rdata['uid']
		))->find();
		if (!$user_request_row) {
			$user_request_model->add($rdata);
		} 
		else {
			$rid['id'] = $user_request_row['id'];
			$user_request_model->where($rid)->save($rdata);
		}
	}
	
	/**
	 * @method record 记录消息请求
	 */
	public function record($type) {
		$log['year']  = date('Y');
		$log['month'] = date('m');
		$log['day']   = date('d');
		$log['token'] = $this->token;
		$model         = M('Requestdata');
		$check         = $model->field('id')->where($log)->find();
		if ($check == false) {
			$log['time'] = time();
			$log[$type] = 1;
			$model->add($log);
		}
		else {
			$model->where($log)->setInc($type);
		}
		//增加请求数
		//$uid = M('Wxuser')->where("token='$this->token'")->find()['uid'];
		M('Wxuser')->field('request_num')->where("token='{$this->token}'")->setInc('request_num');
	}
	
	public function getLastRequest($type='text'){
		$record   =  M('User_request')->where(array(
			'token' => $this->token,
			'msgtype' => $type,
			'uid' => $this->fromUserName
		))->find();
	}
	
	public function getLastText(){
		return $this->getLastRequest();
	}
	
	public function getLastLocation($expire=100){
		$location = $this->getLastRequest('location');
		if($location && (time()-intval($location["time"])<= $expire)) {
			return explode(',', $location['keyword']);
		}
	}
	
	public function text($text) {
		$this->record('textnum');
		return [$text, "text"];
	}
	
	public function news($news) {
		$this->record('imgnum');
		if(!is_array($news[0])){
			$news=[$news];
		}
		return [$news,'news'];
	}
	
	public function music($data){
		$this->record('musicnum');
		return [$data, "music"];
	}
	
	public function voice($data){
		$this->record('voicenum');
		return [$data, "voice"];
	}
}
?>