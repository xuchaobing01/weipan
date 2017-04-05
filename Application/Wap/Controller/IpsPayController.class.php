<?php
/**
 * @class WxpayController
 */
namespace Wap\Controller;
use Think\Controller;
use Think\Log;

class IpsPayController extends Controller
{

    private function getConfig()
    {
        $ipspay_config['Version'] = 'v1.0.0';
        //商戶号
        $ipspay_config['MerCode'] = '193289';
        //交易账户号
        $ipspay_config['Account'] = '1932890013';
        //商戶证书
        $ipspay_config['MerCert'] = 'QkYH5JEQPvdmQaMHS4mMlTHBuGVRUMDcuRchMzXLCts53rfd1xrtu4DAPXQm8RhwlbBMsnakzv5a8rlc3YWco19BRqDqHlpeFBAZ75lk6W0wmyu2xn6TrJJFeSYwPMQV';
        //请求地址
        $ipspay_config['PostUrl'] = 'https://thumbpay.e-years.com/psfp-webscan/services/scan?wsdl';
        return $ipspay_config;
    }

    public function home(){
//        $this->token = I('get.token', '', 'trim');
//        $this->wechat_id = I('get.wechat_id', '', 'trim');
        $this->out_trade_no = I('get.out_trade_no', '', 'trim');
        $this->display();
    }

    //支付发启页面
    public function index()
    {

        $out_trade_no = I('get.out_trade_no', '', 'trim');
//        $out_trade_no = $_POST["out_trade_no"];
        $gatewayType = I('get.gatewayType', '', 'trim');
        empty($out_trade_no) && exit("<h2>参数错误：订单号不能为空！</h2>");
        if($gatewayType == '12'){
            // 银联支付
            $url = '/wap/NewPay/index?out_trade_no='.$out_trade_no;
            @header("Location:$url");
        }
        else{
            /**************************请求参数**************************/

            $order = M('juejin_payorder')->where(array('ordersn' => $out_trade_no))->find();
            $pay_orderid = $out_trade_no . '_' . date("YmdHis");    //订单号

            vendor("IpsScanPay.IpsPayRequest");
            vendor("IpsScanPay.IpsPayVerify");
            $ipspay_config = $this->getConfig();

            //商户号
            $merCode = $ipspay_config['MerCode'];
            //商户账户号
            $merAccount = $ipspay_config['Account'];
            //商户名
            $merMerName = '';
            //商户订单号
            $merBillNo = $pay_orderid;
            //支付方式
            $gatewayType = $gatewayType;
            //订单日期
            $orderDate = date('Ymd');
            //订单金额
            $amount = $order['price'];
            //$amount = 0.02;
            //订单有效期
            $billEXP = '';
            //商品名称
            $goodsName = '港云外汇入金';
            //商户数据包
            $attach = '';
            //异步S2S返回
            $serverUrl = 'http://weipan.haohuajie.pw/wap/IpsPay/herfback';
            //加密方式
            $retEncodeType = '17';
            //币种
            $currencyType = '156';
            //语言
            $lang = 'GB';

            $MsgId = $order['id'];
            /************************************************************/

//构造要请求的参数数组
            $parameter = array(
                "MsgId" => $MsgId,
                "ReqDate" => date("YmdHis"),
                "MerCode" => $merCode,
                "MerName" => $merMerName,
                "Account" => $merAccount,
                "MerBillNo" => $merBillNo,
                "GatewayType" => $gatewayType,
                "Date" => $orderDate,
                "RetEncodeType" => $retEncodeType,
                "CurrencyType" => $currencyType,
                "Amount" => $amount,
                "BillEXP" => $billEXP,
                "GoodsName" => $goodsName,
                "ServerUrl" => $serverUrl,
                "Lang" => $lang,
                "Attach" => $attach
            );
//建立请求
            $ipspayRequest = new \IpsPayRequest($ipspay_config);
            $html_text = $ipspayRequest->buildRequest($parameter);

            $xmlResult = new \SimpleXMLElement($html_text);
            $strRspCode = $xmlResult->GateWayRsp->head->RspCode;
            if ($strRspCode == "000000") {
                //返回报文验签
                $ipspayVerify = new \IpsPayVerify($ipspay_config);
                $verify_result = $ipspayVerify->verifyReturn($html_text);
                // 验证成功
                if ($verify_result) {
                    $this->strQrCodeUrl = $xmlResult->GateWayRsp->body->QrCode;
                    $this->message = "交易成功";
                } else {
                    $this->message = "验签失败";
                }
            } else {
                $this->message = $xmlResult->GateWayRsp->head->RspMsg;
                $this->code = $strRspCode;
            }

            $this->display();
        }


    }

    public function qrcode(){
        Vendor("IpsScanPay.phpqrcode");
//        error_reporting(E_ERROR);
        $url = urldecode($_GET["data"]);
        \QRcode::png($url);
    }

    public function herfback()
    {
        vendor("IpsScanPay.IpsPayNotify");
        $ipspay_config = $this->getConfig();
        $ipspayNotify = new \IpsPayNotify($ipspay_config);
        $verify_result = $ipspayNotify->verifyReturn();

        if ($verify_result != '') { // 验证成功
            $orderid = $verify_result;
            $orderArray = explode('_', $orderid);
            $out_trade_no = $orderArray[0];
            $order = M('juejin_payorder')->where(array('ordersn' => $out_trade_no))->find();
            if ($order['status'] == 2) {
                $ret = "支付成功";
            } else {
                M('juejin_payorder')->where(array('ordersn' => $out_trade_no))->save(['status' => 2, 'paytime' => time()]);
                M('juejin_users')->where(['id' => $order['userid']])->setInc('money', $order['price']);

                M('juejin_users')->where(['id' => $order['userid']])->setInc('addmoney', $order['price']);

                // 超过100，送100
//                if($order["price"]>=100){
//                    M('juejin_users')->where(['id'=>$order['userid']])->setInc('money',$order['price']);
//                }

            }

            echo "ipscheckok";
        } else {
            echo "ipscheckfail";
        }
    }

}
?>