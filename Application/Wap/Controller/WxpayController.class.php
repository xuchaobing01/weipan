<?php
/**
 * @class WxpayController
 */
namespace Wap\Controller;
use Think\Controller;
use Think\Log;
class WXPAY{
	const MODULE_SHOP = 1001;
}

class WxpayController extends Controller {
	//获取支付配置
	private function getConfig($token){
		$set = M('pay_config')->where(['token'=>$token])->field('wxpay_config')->find();
		$set['config'] = unserialize($set['wxpay_config']);
		return ['APPID'=>$set['config']['appid'],'MCHID'=>$set['config']['mchid'],'KEY'=>$set['config']['key']];
	}
	
	//支付发启页面
	public function index(){
		//获取支付请求参数
		$this->token = I('get.token','','trim');
		empty($this->token) && exit("<h2>参数错误：token不能为空！</h2>");
		$this->wechat_id = I('get.wechat_id','','trim');
		if(!is_valid_openid($this->wechat_id)){
			exit("<h2>无法确定您的身份，支付失败！</h2>");
		}
		//支付场景模块
		$module = I('get.module',WXPAY::MODULE_SHOP,'intval');
		$out_trade_no = I('get.out_trade_no','','trim');
		empty($out_trade_no) && exit("<h2>参数错误：订单号不能为空！</h2>");
		$time_start = time();
		//支付成功后前端调用地址,默认为/wap/wxpay/success.html
		$callback = I('get.callback',C('WAP_DOMAIN').U('Wxpay/paySuccess'),'urldecode');
		//初始化微信支付
		Vendor("WxPayV3.WxPayPubHelper");
		$config = $this->getConfig($this->token);
		//使用jsapi接口
		$jsApi = new \JsApi_pub();
		$jsApi->KEY = $config['KEY'];
		//使用统一支付接口
		$unifiedOrder = new \UnifiedOrder_pub();
		$unifiedOrder->APPID = $config['APPID'];
		$unifiedOrder->MCHID = $config['MCHID'];
		$unifiedOrder->KEY = $config['KEY'];
		
		//获取订单详细信息
		$order = $this->getOrderDetail($this->token,$out_trade_no,$module);	
		if($order==null || empty($order['total_fee']) || empty($order['body'])){
			exit('<h2>支付失败:订单参数错误！</h2>');
		}
		//设置统一支付接口参数
		$unifiedOrder->setParameter("openid",$this->wechat_id);//商品描述
		$unifiedOrder->setParameter("body",$order['body']);//商品描述
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$unifiedOrder->setParameter("total_fee",floatval($order['total_fee'])*100);//总金额,以分为单位
		$unifiedOrder->setParameter("notify_url",C('WAP_DOMAIN').'/wap/wxpay/notify/token/'.$this->token.'.html');//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		$unifiedOrder->setParameter("attach","$module");//附加数据 
		$unifiedOrder->setParameter("time_start","$time_start");//交易起始时间
		//获取prepayid
		$unifiedOrder->getPrepayId();
		if($unifiedOrder->result['return_code']=='FAIL'){
			exit("<h2>准备支付失败！</h2><p style='color:red;'>".$unifiedOrder->result['return_msg'].'</p>');
		}
		$prepay_id = $unifiedOrder->result['prepay_id'];
		//=====使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		
		$jsApiParameters = $jsApi->getParameters($unifiedOrder->APPID);
		$this->assign("PayPackage",$jsApiParameters);
		$this->assign('callback',$callback);
		$this->assign('order',$order);
		$this->display('index');
	}
	
	public function notify(){
		$this->token = I('get.token','','trim');
		$xml = file_get_contents("php://input");
		Log::write("[WXPAY]:xml=".$xml,'DEBUG');
		
		Vendor("WxPayV3.WxPayPubHelper");
		//使用通用通知接口
		$notify = new \Notify_pub();
		$config = $this->getConfig($this->token);
		$notify->KEY = $config['KEY'];
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}
		else if($this->checkNotify($this->token,$notify->data)){
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				Log::write("【通信出错】",'INFO');
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				Log::write("【业务出错】",'INFO');
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
				Log::write("【支付成功】",'INFO');
				//更新订单状态
				$this->updateOrderState($notify->data,$this->token);
				
			}
			
		}
		$returnXml = $notify->returnXml();
		Log::Write('returnXml='.$returnXml,'DEBUG');
		echo $returnXml;
	}
	
	/**
	 *@method getOrderDetail
	 *@desc 通过订单号获取支付详情
	 *@param string token
	 *@param string orderId
	 *@param int module 
	 */
	protected function getOrderDetail($token,$orderId,$module){
		$ret = array();
		switch($module){
			case WXPAY::MODULE_SHOP:
				$order = M('juejin_payorder')->where(array('ordersn'=>$orderId))->find();
				$ret['total_fee'] = $order['price'];//0.1;
				//if($_SESSION['user_id']==27){$ret['total_fee'] = 0.1;}//0.1;}
				$ret['body'] = '会员充值';
				return $ret;	
				
		}
		return null;
	}
	
	/**@desc 更新订单状态
	 * @param array data: out_trade_no,openid
	 */
	protected function updateOrderState($data,$token){
		$module = intval($data['attach']);
		if($module == WXPAY::MODULE_SHOP){
			//支付业务
			$order = M('juejin_payorder')->where(array('ordersn'=>$data['out_trade_no'],'wechat_id'=>$data['openid']))->find();
            if($order['status'] == 2){
                // 不做任何处理
            }
            else{
                M('juejin_payorder')->where(array('ordersn'=>$data['out_trade_no'],'wechat_id'=>$data['openid']))->save(['status'=>2,'paytime'=>time()]);
                M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);

                M('juejin_users')->where(['id'=>$order['userid']])->setInc('addmoney',$order['price']);

                if($order['price']>=100){
                    M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);
                }
                /*$user = M('juejin_users')->where(['id'=>$order['userid']])->find();*/

                // 发券，首次充值，
                $orderCount = M('juejin_payorder')->where(array('userid'=>$order['userid']))->count();
                if($orderCount == 1){
                    // 首次充值
                    $count = floor($order['price'] / 10);
                    $incr_coupon = M('juejin_coupon')->where(['type'=>'Incr'])->find();
                    if($incr_coupon != null && $incr_coupon["recharge_present"] > 0) {
                        $incr = [
                            "couponId" => $incr_coupon["id"],
                            "coupon_type" => "Incr",
                            "phone" => $order["phone"],
                            "count" => floor($order['price'] * $incr_coupon["recharge_present"]),
                            "used" => 0,
                            "add_time" => time(),
                            "over_time" => time() + $incr_coupon["overdue_time"] * 3600 * 24
                        ];
                        M('juejin_user_coupon')->add($incr);
                    }
                }
            }


			/* if($user['shareid']){//上级返利
				$fanli = $order['price']*0.1;
				M('juejin_users')->where(['id'=>$user['shareid']])->setInc('money',$fanli);
				//添加返利记录表
				M('juejin_fanliorder')->add(['userid'=>$order['userid'],'wechat_id'=>$data['openid'],'touserid'=>$user['shareid'],
				'price'=>$fanli,'status'=>2,'createtime'=>time()]);
				
			} */
			
		}
		
	}
	
	//保存通知记录,如果已经存在则返回false
	public function checkNotify($token,$notify){
		$data['transaction_id'] = $notify['transaction_id'];
		$data['out_trade_no'] = $notify['out_trade_no'];
		$data['openid'] = $notify['openid'];
		$data['time_end'] = $notify['time_end'];
		$data['total_fee'] = floatval($notify['total_fee'])/100;
		$data['bank_type'] = $notify['bank_type'];
		$data['module'] = $notify['attach'];
		$data['trade_type'] = $notify['trade_type'];
		$data['is_subscribe'] = $notify['is_subscribe'];
		$data['token'] = $token;
		$check = M('wxpay_notify')->where(['transaction_id'=>$notify['transaction_id']])->count();
		if($check==0){
			M('wxpay_notify')->add($data);
		}
		return true;
	}
	//原生支付通知接口
	public function native(){
	
	}
	
	//支付成功跳转页面
	public function paySuccess(){
		$this->assign('order_id',$_GET['oid']);
		$this->display('paysuccess');
	}
	
	//支付失败跳转页面
	public function payError(){
		
	}
	
	//用户维权接口
	public function rights(){
		$xml = get_http_body();
		$content = xml2array($xml);
		$data['openid'] = $content['OpenId'];
		$data['msg_type'] = $content['MsgType'];
		$data['feed_back_id'] = $content['FeedBackId'];
		$data['trans_id'] = $content['TransId'];
		$data['reason'] = $content['Reason'];
		$data['solution'] = $content['Solution'];
		$data['extinfo'] = $content['ExtInfo'];
		$data['time'] = $content['TimeStamp'];
		
		M('customer_rights')->add($data);
	}
	
	//系统告警
	public function alarm(){
		$xml = get_http_body();
		$alarm = xml2array($xml);
		$data['error_type'] = $alarm['ErrorType'];
		$data['desc'] = $alarm['Description'];
		$data['content'] = $alarm['AlarmContent'];
		$data['time'] = $alarm['TimeStamp'];
		
		M('alarm')->add($data);
		echo 'success';
	}
	
	//微信发货通知
	public function fahuo(){
		$oid = I('get.oid','','trim');
		$pay = new \Wap\Logic\Wxpay($this->token);
		$this->ajaxReturn($pay->delivernotify($oid));
	}
	/*
	<xml>
		<OpenId><![CDATA[o0pk9uIVnlY-fJkzFKEbQ6LJ4cFc]]></OpenId>
		<AppId><![CDATA[wxc04ce1d87dcd13cd]]></AppId>
		<TimeStamp>1401206434</TimeStamp>
		<MsgType><![CDATA[request]]></MsgType>
		<FeedBackId>13275936403980775178</FeedBackId>
		<TransId><![CDATA[1218614901201405273313470595]]></TransId>
		<Reason><![CDATA[没有收到货品]]></Reason>
		<Solution><![CDATA[退款，并不退货]]></Solution>
		<ExtInfo><![CDATA[Test 13456780012]]></ExtInfo>
		<AppSignature><![CDATA[1f4a626f59f9ae8007158b0a9510e88db56fa80b]]></AppSignature>
		<SignMethod><![CDATA[sha1]]></SignMethod>
	</xml>
	<xml>
		<OpenId><![CDATA[111222]]></OpenId>
		<AppId><![CDATA[wwwwb4f85f3a797777]]></AppId>
		<TimeStamp>1369743511</TimeStamp>
		<MsgType><![CDATA[confirm/reject]]></MsgType>
		<FeedBackId><![CDATA[5883726847655944563]]></FeedBackId>
		<Reason><![CDATA[商品质量有问题]]></Reason>
		<AppSignature><![CDATA[bafe07f060f22dcda0bfdb4b5ff756f973aecffa]]></AppSignature>
		<SignMethod><![CDATA[sha1]]></SignMethod>
	</xml>
	*/
	/*告警POST
	<xml>
		<AppId><![CDATA[wxf8b4f85f3a794e77]]></AppId>
		<ErrorType>1001</ErrorType>
		<Description><![CDATA[错识描述]]></Description>
		<AlarmContent><![CDATA[错误详情]]></AlarmContent>
		<TimeStamp>1393860740</TimeStamp>
		<AppSignature><![CDATA[f8164781a303f4d5a944a2dfc68411a8c7e4fbea]]></AppSignature>
		<SignMethod><![CDATA[sha1]]></SignMethod>
	</xml>
	*/
}
?>