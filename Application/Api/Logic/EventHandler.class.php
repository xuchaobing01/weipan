<?php
/**
 * @class Eventhandler 处理事件消息
 * @desc: 订阅事件，取消订阅，菜单点击
 * @author: yanqizheng
 * @update: 2015/01/26
 */
namespace Api\Logic;
class EventHandler extends MessageHandler {
	public function handle() {
		if('subscribe' == $this->event) {
			$this->record('follownum');
			M('wxuser')->field('wxfans')->where("token='{$this->token}'")->setInc('wxfans');
			return $this->handleSubscribe($this->fromUserName);
		}
		else if('unsubscribe' == $this->event) {
			$this->record('unfollownum');
			$this->vshopUnsub();
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
			$data['subscribe'] = 1;
			$data['update_time'] = time();
			M('wechat_user')->where(['open_id'=>$this->fromUserName])->save($data);
		}
		//微人脉二维码提醒-微人脉场景值从10000开始
		if($data['scene_id']>10000){
			$ret = $this->vshopScene($data['scene_id']);
			return $ret;
		}
		if($data){ //根据关键词回复相应的内容
			$keyword = $data['keyword'];
			$handler = new TextHandler($this->token);
			$handler->fromUserName = $this->fromUserName;
			
			$ret = $handler->handleCustomKeyword($keyword);
			return $ret;
		}
	}
	public function vshopUnsub(){
		$user = M('wechat_user')->where(['open_id'=>$this->fromUserName])->find();
		if($user['scene_id']>10000){
			M('bj_qmxk_member','vshop_')->where(['from_user'=>$this->fromUserName])->save(array('subscribe'=>0,'subscribe_time'=>time()));
		}
	}
	public function vshopScene($scene_id){
		$realid=$scene_id-10000; //为上级  mid
		$list = M('bj_qmxk_member','vshop_')->find($realid);	
		$text = '';
		if($list){
			// vshop_bj_qmxk_member  vshop_fans
			$myfans = M('fans','vshop_')->where(array('weid'=>$list['weid'],'from_user'=>$this->fromUserName))->find();
			$mylist = M('bj_qmxk_member','vshop_')->where(array('weid'=>$list['weid'],'from_user'=>$this->fromUserName))->find();
			$theone =M('bj_qmxk_rules','vshop_')->where(array('weid'=>$list['weid'],'enwph'=>1))->find();
			if(empty($myfans['id'])){
				$fans = array('weid' => $list['weid'], 'from_user' => $this->fromUserName, 'follow' => 1, 'createtime' => time());
				M('fans','vshop_')->add($fans);
			}		
			if(empty($mylist['id'])){		
				if ($theone['promotertimes'] == 1) {
					$data = array('weid' => $list['weid'], 'from_user' => $this->fromUserName, 'commission' => 0, 'createtime' => time(), 'flagtime' => time(), 'shareid' => $realid, 'status' => 1, 'flag' => 1);
				} else {
					$data = array('weid' => $list['weid'], 'from_user' => $this->fromUserName,  'commission' => 0, 'createtime' => time(), 'flagtime' => time(), 'shareid' => $realid, 'status' => 1, 'flag' => 0);
				}
				M('bj_qmxk_member','vshop_')->add($data);
				$count = M('bj_qmxk_member','vshop_')->where(array('weid'=>$list['weid']))->count();
				$sharetitle='恭喜您由【'.$list['realname'].'】邀请成为'.$theone['wrmtitle'].'的';	
			}else{	
				$map['id']  = array('lt',$mylist['id']);
				$map['weid']=$list['weid'];
				$count = M('bj_qmxk_member','vshop_')->where($map)->count();				
				if($mylist['shareid']){
					$list = M('bj_qmxk_member','vshop_')->find($mylist['shareid']);
					$sharetitle='恭喜您由【'.$list['realname'].'】邀请成为'.$theone['wrmtitle'].'的';
				}else{
					$sharetitle='恭喜您成为'.$theone['wrmtitle'].'的';
				}	
			}
			if($list['weid']==830){
				
			}else{
				$text = $sharetitle.'第【'.$count.'】位梦想家，
					现在购买'.$theone['wrmtitle'].'VIP会员，即可上传自己的微信二维码，开启被动加人。结识更多的新朋友！
							-------<a href="'.$theone['wrmurl'].'">点击这里</a> 购买VIP会员，获得更多特权体验。
				--或者<a href="http://w.weimarket.cn/vshop/mobile.php?act=module&name=bj_qmxk&do=wrm&weid='.$list['weid'].'">点击进入【'.$theone['wrmtitle'].'】</a>';
			}	
			
		}	
		//str_replace('{wechat_id}', $this->fromUserName, $info['text'])
		return [$text, "text"];
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