<?php
namespace Wap\Logic;
class Member{
	public function __construct($token,$openid){
		$this->wechat_id = $openid;
		$this->token = $token;
	}
	
	//判断粉丝是否是会员
	public function check(){
		if(empty($this->wechat_id))return false;
		return M('Member_card_create')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->find() !== null;
	}
	
	/**
	 *@method join
	 *@desc 加入会员
	 */
	public function join($data){
		if($this->check()) return ['status'=>1,'message'=>'该会员已存在！']; //检查是否已经是会员
		$check = M('Member_info')->where(['token'=>$this->token,'tel'=>$data['tel']])->find();//判断手机号是否被使用
		if($check !=null){
			return ['status'=>2,'message'=>'该手机号已被使用！'];
		}
		//判断会员是否要审核
		$set = M('member_card_info')->where(['token'=>$this->token])->field('need_check')->find();
		$data['getcardtime'] = time();
		if($set['need_check'] == 0){
			$card = self::fetch_card($this->token);
			//如果商家还有会员卡，可以领
			if($card != false){
				//微信与会员卡绑定
				M('Member_card_create')->where(['id'=>$card['id']])->save(array('wechat_id'=>$data['wechat_id']));
				//记录会员信息
				self::save_member($data);
				//检查是否领卡送积分
				$exchange = M('Member_card_exchange')->field('startup_score')->where(array('token'=>$this->token))->find();
				if($exchange['startup_score']!=0){
					$this->alterScore($exchange['startup_score'],4,'领卡送积分');
				}
				return ['status'=>0,'message'=>'领卡成功！'];
			}
			else{
				//商家没有了会员卡
				return ['status'=>1,'message'=>'会员卡已被领完啦！'];
			}
		}
		else{
			$data['status'] = 0;//等待审核
			self::save_member($data);
			return ['status'=>0,'message'=>'申请已经提交，请耐心商家审核通过！'];
		}
	}
	
	
	/**
	 *@method save_member 
	 *@desc 保存会员信息
	 */
	public static function save_member($data){
		$model = M('Member_info');
		$wechat_id = $data['wechat_id'];
		$member = $model->field('id')->where(['token'=>$data['token'],'wechat_id'=>$data['wechat_id']])->find();
		if($member != null){
			$model->delete($member['id']);
		}
		return M('Member_info')->add($data);
	}
	
	/**
	 *@method fetch_card 
	 *@desc 获取一张空卡
	 *@return 成功时返回会员卡，失败时返回null
	 */
	public static function fetch_card($token,$cardId){
		if(empty($cardId)){
			$cardset = M('Member_card_set')->where(['token'=>$token,'base_points'=>0])->field('id')->find();
			if($cardset == null)return false;
			$cardId = $cardset['id'];
		}
		$card = M('Member_card_create')->field('id,number')->where(['token'=>$token,'wechat_id'=>'','card_id'=>$cardId])->find();
		if($card == null)return false;
		else return $card;
	}
	
	/**
	 *@method delete
	 *@desc 删除会员，并且删除会员的所有记录
	 */
	public function delete(){
		$condition = ['token'=>$this->token,'wechat_id'=>$this->wechat_id];
		$back = M('Member_info')->where($condition)->delete();
		if($back){
			//解除会员卡绑定
			M('member_card_create')->where($condition)->setField('wechat_id','');
			//删除签到信息
			M('member_card_sign')->where($condition)->delete();
			//删除消费信息
			M('member_expense')->where($condition)->delete();
			//删除积分记录
			M('member_score_record')->where($condition)->delete();
			//删除优惠券
			M('member_coupon_usage')->where($condition)->delete();
			//删除资金记录
			M('member_money_record')->where($condition)->delete();
			//删除充值记录
			M('member_recharge_record')->where($condition)->delete();
			return true;
		}
		else return false;
	}
	/**
	 *@method expense
	 *@desc 会员消费
	 *@param $info 消费金额
	 *@param $score 获得积分,如果不设置积分，则按照积分规则生成
	 *消费类型 0：手动添加，1：商城消费，2：餐饮消费
	 */
	public function expense($info,$score){
		if(is_array($info)){
			$data['expense'] = $info['money'];
			//$data['order_id'] = isset($info['order_id'])?$info['order_id']:"";
			$data['type'] = isset($info['type'])?$info['type']:0;
			$data['remark'] = isset($info['remark'])?$info['remark']:"";
			$data['time'] = isset($info['time'])?$info['time']:time();
		}
		else{
			$data['expense'] = floatval($info);
			$data['time'] = time();
			$data['type'] = 0;
		}
		if(isset($score)){
			$data['score'] = intval($score);
		}
		else{
			$set = M('Member_card_exchange')->where(array('token'=>$this->token))->find();
			if($set == null){
				$reward = 0;
			}
			else{
				$reward = $set['reward'];
			}
			$data['score'] = floor($data['expense']*$reward);
		}
		$data['token'] = $this->token;
		$data['wechat_id'] = $this->wechat_id;
		
		//增加消费积分记录
		$ret = M('member_expense')->add($data);
		//增加总积分记录
		$minfo = M('member_info');
		$minfo->where(['wechat_id'=>$this->wechat_id])->setInc('total_expense',$data['expense']);
		$minfo->where(['wechat_id'=>$this->wechat_id])->setInc('expense_score',$data['score']);
		$this->alterScore($data['score'],2,'消费积分');
		return $ret;
	}
	
	/**
	 *@method expense
	 *@desc 会员签到
	 */
	public function sign(){
		$cardsign = M('member_card_sign');
		$set_exchange = M('Member_card_exchange')->field('everyday,continuation')->where(array('token'=>$this->token))->find();//获取签到规则
		$whereinfo =  array('token'=>$this->token,'wechat_id'=>$this->wechat_id);
		$userinfo = M('Member_info')->field('total_score,sign_score,continuous')->where($whereinfo)->find(); //获取会员信息
		//连续签到检查
		$sign = $cardsign->field('sign_time')->where($whereinfo)->order('id desc')->find();
		if($sign == null || (time() - intval($sign['sign_time'])) > 86400) $is_continue_check = false;
		else $is_continue_check = true;
		
		$data['sign_time']  = time();
		$data['token']      = $this->token;
		$data['wechat_id']   = $this->wechat_id;
		//检查 计数器是否等于 6 天，够了就增加 预设好的积分
		if($is_continue_check && $userinfo['continuous'] == 6){
			$data['score']    =  $set_exchange['everyday'] + $set_exchange['continuation'];//积分=每日签到积分+连续签到奖励积分
			$da['continuous']  = 0; //清空计数器
		}
		else{
			//添加本次签到积分
			$data['score']    = $set_exchange['everyday'];
			
			//连续签到计数器设置
			if ($is_continue_check) {
				$da['continuous'] = $userinfo['continuous'] + 1;//是连续签到，签到计数器加1
			}
			else $da['continuous']  =  1;
		}
		$cardsign->add($data);//添加签到记录
		$da['sign_score']  = $userinfo['sign_score'] + $data['score'];
		$this->alterScore($data['score'],1,'签到积分');
		M('Member_info')->where($whereinfo)->save($da);
		return $data['score'];
	}
	
	/**
	 *@method alterScore 用户积分，增加记录
	 *@param socre int 积分 ，为负数时表示减少积分
	 *@param type 积分变更类型,1:签到积分,2:消费积分,3:使用积分,4:其他
	 *@param remark 变更说明
	 */
	public function alterScore($score,$type = 1,$remark = ''){
		$model = M('Member_info');
		$row = array();
		$row['token'] = $this->token;
		$row['wechat_id'] = $this->wechat_id;
		$row['score'] = $score;
		$row['type'] = $type;
		$row['remark'] = $remark;
		$row['time'] = time();
		M('Member_score_record')->add($row);
		$ret = $model->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->setInc('total_score',$score);
		return $ret;
	}
	
	public function get(){
		return M('member_info')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->find();
	}
	
	/**
	 *@desc 会员充值
	 *@param float money 充值金额
	 *@param string remark 备注
	 *@return int status 状态
	 */
	public function recharge($money,$remark = '会员充值'){
		$data['token'] = $this->token;
		$data['wechat_id'] = $this->wechat_id;
		$data['delta_money'] = floatval($money);
		$data['time'] = time();
		$data['remark'] = $remark;
		//变更记录
		M('member_money_record')->add($data);
		$ret = M('member_info')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->setInc('money',floatval($money));
		return $ret;
	}
	//实体卡充值
	public function addTruecard(){
		
	}
}
?>