<?php
/**
 * @class Eventhandler 处理事件消息
 * @desc: 订阅事件，取消订阅，菜单点击
 * @author: yanqizheng
 * @update: 2015/01/26
 */
namespace Spark\Wechat;
class EventHandler extends MessageHandler {
	public function handle() {
		if('subscribe' == $this->event) {
			$this->record('follownum');
			M('wxuser')->field('wxfans')->where("token='{$this->token}'")->setInc('wxfans');
			return $this->handleSubscribe($this->fromUserName);
		}
		else if('unsubscribe' == $this->event) {
			$this->record('unfollownum');
			M('wxuser')->field('wxfans')->where("token='{$this->token}'")->setDec('wxfans');
			M('wechat_user')->where(['open_id'=>$this->fromUserName])->delete();
		}
		else if('scan' == $this->event) { //扫描带参数二维码事件
			
		}
		else if('CLICK' == $this->event){ //自定义菜单事件
			$this->record('menunum');
			return $this->handleMenuClick($this->eventKey);
		}
		else if('VIEW' == $this->event){ //自定义菜单事件
			$this->record('menunum');
			return '';
		}
		else if('LOCATION' == $this->event){ //上报地理位置事件
		
		}
	}
	
	/**
	 * @method handleText 处理用户订阅事件
	 * @param string 订阅用户微信ID
	 */
	public function handleSubscribe() {
		$data = M('Areply')
		->field('keyword')
		->where(array('token' => $this->token ))
		->find();
		
		$model = M('wechat_user');
		$check = $model->where(['token'=>$this->token,'open_id'=>$this->fromUserName])->find();
		if($this->eventKey){ //带有扫描场景值时保存信息
			$data['scene_id']=substr($this->eventKey,8,strlen($this->eventKey)-8);
		}
		if($check == null){
			$data['open_id']=$this->fromUserName;
			$data['token']=$this->token;
			$data['update_time'] = time();
			M('wechat_user')->add($data);
		}
		else{
			$data['update_time'] = time();
			M('wechat_user')->where(['open_id'=>$this->fromUserName])->save($data);
		}
		if($data){ //根据关键词回复相应的内容
			$keyword = $data['keyword'];
			$handler = new TextHandler($this->token);
			$handler->fromUserName = $this->fromUserName;
			
			$ret = $handler->handleCustomKeyword($keyword);
			return $ret;
		}
	}
	
	/**
	 * @method handleMenuClick 处理菜单点击事件
	 * @param string 菜单ID
	 */
	public function handleMenuClick($eventKey){
		$th = new TextHandler($this->token);
		$th->fromUserName=$this->fromUserName;
		
		$resp = $th->handleSysKeyword($eventKey);
		if(!empty($resp)) {
			return $resp;
		}
		
		$resp = $th->handleCustomKeyword($eventKey);
		
		if(!empty($resp)) {
			return $resp;
		}
		/*智能客服处理*/
		$resp = $th->handleAutoService($key);
		if(!empty($resp)) {
			return $resp;
		}
		return $this->text("菜单正在完善中，请稍后再试！");
	}
}
?>