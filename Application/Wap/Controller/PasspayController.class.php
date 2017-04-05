<?php
/**
 * @class WxpayController
 */
namespace Wap\Controller;
use Think\Controller;
use Think\Log;

require_once("Core/Library/Vendor/Passpay/shanpayfunction.php");

class PasspayController extends Controller {

	//获取支付配置
	private function getConfig(){
        $shan_config = [];
        //商户号（6位数字）
        $shan_config['user_seller'] = '454962';
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者PID，签约账号，由16位纯数字组成的字符串，请登录商户后台查看
        $shan_config['partner']		= '369771254370591';
        // MD5密钥，安全检验码，由数字和字母组成的32位字符串，请登录商户后台查看
        $shan_config['key']			= 'aDQBeV8q24UFESdp5GMbVGhHPa5CSWFA';
        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $shan_config['notify_url'] = "http://weixin.bajiejr.com.cn/Wap/Passpay/notify";
        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
        $shan_config['return_url'] = "http://weixin.bajiejr.com.cn/Wap/Passpay/return_url";
		return $shan_config;
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
        $shan_config = $this->getConfig();

        //支付场景模块

        $out_trade_no = I('get.out_trade_no','','trim');
        empty($out_trade_no) && exit("<h2>参数错误：订单号不能为空！</h2>");

        /**************************请求参数**************************/

        $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();

        //商户订单号
        $out_order_no = $out_trade_no;//商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = '会员充值';//必填
        //付款金额
        $total_fee = $order['price'];//必填 需为整数
//        $total_fee = 1;//测试
        //订单描述
        $body = '会员充值';
        //服务器异步通知页面路径
        $notify_url = $shan_config['notify_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $shan_config['return_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "partner" => $shan_config['partner'],
            "user_seller"  => $shan_config['user_seller'],
            "out_order_no"	=> $out_order_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url
        );

        //建立请求
        $html_text = buildRequestFormShan($parameter, $shan_config['key']);
        echo $html_text;
	}
	
	public function notify(){
        $shan_config = $this->getConfig();
        //计算得出通知验证结果
        $shanNotify = md5VerifyShan($_REQUEST['out_order_no'],$_REQUEST['total_fee'],$_REQUEST['trade_status'],$_REQUEST['sign'],$shan_config['key'],$shan_config['partner']);
        if($shanNotify) {//验证成功
            if($_REQUEST['trade_status']=='TRADE_SUCCESS'){
                /*
                加入您的入库及判断代码;
                判断返回金额与实金额是否想同;
                判断订单当前状态;
                完成以上才视为支付成功
                */
                //商户订单号
                $out_trade_no = $_REQUEST['out_order_no'];
                //云通付交易号
                $trade_no = $_REQUEST['trade_no'];
                //价格
                $price=$_REQUEST['total_fee'];

                //此处应该更新一下订单状态，商户自行增删操作
                Log::write("【支付成功】",'INFO');
                //更新订单状态
                $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();
                if($order['status'] == 2){
                    $ret = "支付成功";
                }
                else{
                    M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->save(['status'=>2,'paytime'=>time()]);
                    M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);

                M('juejin_users')->where(['id'=>$order['userid']])->setInc('addmoney',$order['price']);

                    $ret = "支付成功";
                }
                // var_dump($_REQUEST);
            }
            echo 'success';

        }else {
            //验证失败
            echo "fail";//请不要修改或删除
        }
	}

    public function return_url(){
	    $ret = "";
        $shan_config = $this->getConfig();
        //计算得出通知验证结果
        $shanNotify = md5VerifyShan($_REQUEST['out_order_no'],$_REQUEST['total_fee'],$_REQUEST['trade_status'],$_REQUEST['sign'],$shan_config['key'],$shan_config['partner']);
        if($shanNotify) {//验证成功
            if($_REQUEST['trade_status']=='TRADE_SUCCESS'){
                /*
                加入您的入库及判断代码;
                判断返回金额与实金额是否想同;
                判断订单当前状态;
                完成以上才视为支付成功
                */
                //商户订单号
                $out_trade_no = $_REQUEST['out_order_no'];
                //云通付交易号
                $trade_no = $_REQUEST['trade_no'];
                //价格
                $price=$_REQUEST['total_fee'];

                //此处应该更新一下订单状态，商户自行增删操作
                Log::write("【支付成功】",'INFO');
                //更新订单状态
                $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();
                if($order['status'] == 2){
                    $ret = "支付成功";
                }
                else{
                    M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->save(['status'=>2,'paytime'=>time()]);
                    M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);

                M('juejin_users')->where(['id'=>$order['userid']])->setInc('addmoney',$order['price']);

                $ret = "支付成功";
		        }

            }

        }else {
            //验证失败
            $ret = "验证失败";//请不要修改或删除
        }
        $this->ret = $ret;
        $this->display();
    }

}
?>