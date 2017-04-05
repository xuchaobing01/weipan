<?php
/**
 * @class WxpayController
 */
namespace Wap\Controller;
use Think\Controller;
use Think\Log;

class CaihongController extends Controller {

	//支付发启页面
	public function index(){
        $this->token = I('get.token','','trim');
        empty($this->token) && exit("<h2>参数错误：token不能为空！</h2>");
        $this->wechat_id = I('get.wechat_id','','trim');
        if(!is_valid_openid($this->wechat_id)){
            exit("<h2>无法确定您的身份，支付失败！</h2>");
        }

        //支付场景模块

        $out_trade_no = I('get.out_trade_no','','trim');
        empty($out_trade_no) && exit("<h2>参数错误：订单号不能为空！</h2>");

        /**************************请求参数**************************/

        $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();

        $apiurl = "http://pay.0n2.com/bank/";
        $parter = "1728";
        $key = "3af4364ee9794ced995a661069426ea6";
        $orderid = $out_trade_no;
        $banktype = 1007;
        $attach = '微信充值';
        $value =$order["price"];
//        $value =0.02;
        $callbackurl = "http://weipan.haohuajie.pw/wap/caihong/callback";
        $hrefbackurl = "http://weipan.haohuajie.pw/wap/caihong/herfback";
        $signSource = sprintf("parter=%s&type=%s&value=%s&orderid=%s&callbackurl=%s%s", $parter, $banktype, $value, $orderid, $callbackurl, $key);
        $sign = md5($signSource);
        $postUrl = $apiurl. "?type=".$banktype;
        $postUrl.="&parter=".$parter;
        $postUrl.="&value=".$value;
        $postUrl.="&orderid=".$orderid;
        $postUrl.="&callbackurl=".$callbackurl;
        $postUrl.="&hrefbackurl=".$hrefbackurl;
        $postUrl.="&attach=".$attach;
        $postUrl.="&sign=".$sign;
        header("Content-type: text/html; charset=gb2312");
        header ("location:$postUrl");

        echo '';
	}
	
	public function callback(){

        $orderid = $_REQUEST['orderid'];
        $opstate = $_REQUEST['opstate'];

        if($opstate == 0){
            $out_trade_no = $orderid;
            $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();
            if($order['status'] == 2){
                $ret = "支付成功";
            }
            else{
                M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->save(['status'=>2,'paytime'=>time()]);
                M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);

                M('juejin_users')->where(['id'=>$order['userid']])->setInc('addmoney',$order['price']);

                // 超过100，送100
                if($order["price"]>=100){
                    //M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);
                }

                $ret = "支付成功";
            }
            $this->ret=$ret;
        }
        else{
            $this->ret='支付失败';
        }
        echo  $this->ret;
	}

    public function herfback(){

        $orderid = $_REQUEST['orderid'];
        $opstate = $_REQUEST['opstate'];

        if($opstate == 0){
            $out_trade_no = $orderid;
            $order = M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->find();
            if($order['status'] == 2){
                $ret = "支付成功";
            }
            else{
                M('juejin_payorder')->where(array('ordersn'=>$out_trade_no))->save(['status'=>2,'paytime'=>time()]);
                M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);

                M('juejin_users')->where(['id'=>$order['userid']])->setInc('addmoney',$order['price']);

                // 超过100，送100
                if($order["price"]>=100){
                    M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);
                }

                $ret = "支付成功";
            }
            $this->ret=$ret;
        }
        else{
            $this->ret='支付失败';
        }
        $this->display();
    }

}
?>