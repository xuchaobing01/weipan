<?php
namespace Wap\Logic;
//微信支付发货通知与状态查询
class Wxpay{
	private $deliver_url = "https://api.weixin.qq.com/pay/delivernotify?access_token={access_token}";
	public function __construct($token){
		$this->token = $token;
	}
	
	/*
	 *发货通知
	 *@param $oid 订单号
	 */
	public function delivernotify($oid){
		if(empty($oid))return ['status'=>-1,'message'=>'订单号不能为空！'];
		$pay_record = M('wxpay_notify')->where(['out_trade_no'=>$oid])->find();
		if($pay_record == null)return ['status'=>-1,'message'=>'订单不存在！'];
		$appid = M('config')->where(['name'=>'WECHAT_APPID'])->find()['value'];
		$appsecret = M('config')->where(['name'=>'WECHAT_APPSECRET'])->find()['value'];
		Vendor("WxPay.WxPayHelper");
		$data['appid'] = $appid;
		$data['openid'] = $pay_record['openid'];
		$data['transid'] = $pay_record['transaction_id'];
		$data['out_trade_no'] = $pay_record['out_trade_no'];
		$data['deliver_timestamp'] = strval(time());
		$data['deliver_status'] = "1";
		$data['deliver_msg'] = "ok";
		$wxhelper = new \WxPayHelper();
		$sign = $wxhelper->sign($data);
		$data['app_signature'] = $sign;
		$data['sign_method'] = 'sha1';
		$access_token = S('access_token');
		if($access_token == null){
			$util = new \Spark\Wechat\WechatUtil($appid,$appsecret);
			$access_token = $util->_accessToken;
			S('access_token',$access_token,7200);
		}
		$url = str_replace('{access_token}',$access_token,$this->deliver_url);
		$json = json_encode($data);
		$ret = \Spark\Util\HttpClient::curlPost($url,$json);
		$ret = json_decode($ret);
		return ['status'=>$ret->errcode,'message'=>$ret->errmsg];
	}
	
	public function orderquery(){}
}
?>