<?php
/**
 * @class WxpayController
 */
namespace Wap\Controller;

use Think\Controller;
use Think\Log;

define('BASE_PATH', "/www/wwwroot/weipan.haohuajie.pw/Core/Library/Vendor/IpsScanPay/logs/");
define('PATH_LOG_FILE', BASE_PATH . '/newPayDemo-' . date('Y-m-j') . '.log');


class NewPayController extends Controller
{
    private function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);

        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    function post($url, $param=array()){
        if(!is_array($param)){

            throw new Exception("参数必须为array");
        }

        $str = '<form id="Form1" name="Form1" method="post" style="display:none;" action="' . $url . '">';
        foreach ($param as $key => $val) {
            $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
        $str = $str . '<input type="submit" value="提交">';
        $str = $str . '</form>';
        $str = $str . '<script>';
        $str = $str . 'document.Form1.submit();';
        $str = $str . '</script>';

        echo $str;
    }


    private function getMerInfo()
    {
        $arrayMer = array(
            'memno' => '193289',
            'mername' => '港云外汇',
            'mercert' => 'QkYH5JEQPvdmQaMHS4mMlTHBuGVRUMDcuRchMzXLCts53rfd1xrtu4DAPXQm8RhwlbBMsnakzv5a8rlc3YWco19BRqDqHlpeFBAZ75lk6W0wmyu2xn6TrJJFeSYwPMQV',
            'acccode' => '1932890013'
        );
        return $arrayMer;
    }

    private function init()
    {
        $this->payWayurl = "http://newpay.ips.com.cn/psfp-entry/gateway/payment.html";
        $this->paytestWayurl = "http://bankbackuat.ips.com.cn/psfp-entry/gateway/payment.html";
        //定义日志路径

        //创建日志存储目录
        if (!is_dir(BASE_PATH)) mkdir(BASE_PATH);
        define('Mode', "1");//0#测试环境 1#生产环境
    }

    //支付发启页面
    public function index()
    {
        //支付场景模块
        $this->init();
        $out_trade_no = I('get.out_trade_no', '', 'trim');
//        $out_trade_no = $_POST["out_trade_no"];
        $gatewayType = I('get.gatewayType', '', 'trim');
        empty($out_trade_no) && exit("<h2>参数错误：订单号不能为空！</h2>");
        $order = M('juejin_payorder')->where(array('ordersn' => $out_trade_no))->find();
        $pay_orderid = $out_trade_no . '_' . date("YmdHis");    //订单号

        /**************************请求参数**************************/


        $meminfo = $this->getMerInfo();
        //获取输入参数

        $pVersion = 'v1.0.0';//版本号
        $pMerCode = $meminfo["memno"];//商户号
        $pMerName = $meminfo["mername"];//商户名
        $pMerCert = $meminfo["mercert"];//商户证书
        $pAccount = $meminfo["acccode"];//账户号
        $pMsgId = $order["id"];//消息编号
        $pReqDate = date("YmdHis");//商户请求时间

        $pMerBillNo = $pay_orderid;//商户订单号
        $pAmount = $order["price"];//订单金额
        //$pAmount = 0.02;//订单金额
        $pDate = date("Ymd");//订单日期
        $pCurrencyType = 'GB';//币种
        $pGatewayType = '01';//支付方式，借记卡
        $pLang = '156';//语言
        $pMerchanturl = 'http://pay.haojiehua.pw/p2p_url.php';//支付结果成功返回的商户URL
        $pFailUrl = "";//支付结果失败返回的商户URL
        $pAttach = '';//商户数据包
        $pOrderEncodeTyp = '5';//订单支付接口加密方式 默认为5#md5
        $pRetEncodeType = '17';//交易返回接口加密方式
        $pRetType = '3';//返回方式
        $pServerUrl = 'http://pay.haojiehua.pw/s2s_url.php';//Server to Server返回页面
        $pBillEXP = 1;//订单有效期(过期时间设置为1小时)
        $pGoodsName = '港云外汇入金';//商品名称
        $pIsCredit = 0;//直连选项
        $pBankCode = '';//银行号
        $pProductType = '1';//产品类型


        if ($pIsCredit == 0) {
            $pBankCode = "";
            $pProductType = '';
        }
        $data = array();
        $data["MerName"] = $pMerName;
        $data["MerCert"] = $pMerCert;
        $data["MerCode"] = $pMerCode;
        $data["Account"] = $pAccount;
        $data["MsgId"] = $pMsgId;
        $data["ReqDate"] = $pReqDate;

        $data["MerBillNo"] = $pMerBillNo;
        $data["Amount"] = $pAmount;
        $data["Date"] = $pDate;
        $data["CurrencyType"] = $pCurrencyType;
        $data["GatewayType"] = $pGatewayType;
        $data["Lang"] = $pLang;
        $data["Merchanturl"] = $pMerchanturl;
        $data["FailUrl"] = $pFailUrl;
        $data["Attach"] = $pAttach;
        $data["OrderEncodeType"] = $pOrderEncodeTyp;
        $data["RetEncodeType"] = $pRetEncodeType;
        $data["RetType"] = $pRetType;
        $data["ServerUrl"] = $pServerUrl;
        $data["BillEXP"] = $pBillEXP;
        $data["GoodsName"] = $pGoodsName;
        $data["IsCredit"] = $pIsCredit;
        $data["BankCode"] = $pBankCode;
        $data["ProductType"] = $pProductType;

        $url= 'http://pay.haojiehua.pw/submit_page.php';

        $this->post($url, $data);
    }

    public function s2s()
    {
        $paymentResult = $_POST["paymentResult"];//获取信息
        $postType= $_POST["postType"]; // 1:跳转url, 2: s2s
        file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S接收到的报文信息:".$paymentResult."\r\n",FILE_APPEND);
        $xml=simplexml_load_string($paymentResult,'SimpleXMLElement', LIBXML_NOCDATA);

        //读取相关xml中信息
        $ReferenceIDs = $xml->xpath("GateWayRsp/head/ReferenceID");//关联号
        //var_dump($ReferenceIDs);
        $ReferenceID = $ReferenceIDs[0];//关联号
        $RspCodes = $xml->xpath("GateWayRsp/head/RspCode");//响应编码
        $RspCode=$RspCodes[0];
        $RspMsgs = $xml->xpath("GateWayRsp/head/RspMsg"); //响应说明
        $RspMsg=$RspMsgs[0];
        $ReqDates = $xml->xpath("GateWayRsp/head/ReqDate"); // 接受时间
        $ReqDate=$ReqDates[0];
        $RspDates = $xml->xpath("GateWayRsp/head/RspDate");// 响应时间
        $RspDate=$RspDates[0];
        $Signatures = $xml->xpath("GateWayRsp/head/Signature"); //数字签名
        $Signature=$Signatures[0];
        $MerBillNos = $xml->xpath("GateWayRsp/body/MerBillNo"); // 商户订单号
        $MerBillNo=$MerBillNos[0];
        $CurrencyTypes = $xml->xpath("GateWayRsp/body/CurrencyType");//币种
        $CurrencyType=$CurrencyTypes[0];
        $Amounts = $xml->xpath("GateWayRsp/body/Amount"); //订单金额
        $Amount=$Amounts[0];
        $Dates = $xml->xpath("GateWayRsp/body/Date");    //订单日期
        $Date=$Dates[0];
        $Statuss = $xml->xpath("GateWayRsp/body/Status");  //交易状态
        $Status=$Statuss[0];
        $Msgs = $xml->xpath("GateWayRsp/body/Msg");    //发卡行返回信息
        $Msg=$Msgs[0];
        $Attachs = $xml->xpath("GateWayRsp/body/Attach");    //数据包
        $Attach=$Attachs[0];
        $IpsBillNos = $xml->xpath("GateWayRsp/body/IpsBillNo"); //IPS订单号
        $IpsBillNo=$IpsBillNos[0];
        $IpsTradeNos = $xml->xpath("GateWayRsp/body/IpsTradeNo"); //IPS交易流水号
        $IpsTradeNo=$IpsTradeNos[0];
        $RetEncodeTypes = $xml->xpath("GateWayRsp/body/RetEncodeType");    //交易返回方式
        $RetEncodeType=$RetEncodeTypes[0];
        $BankBillNos = $xml->xpath("GateWayRsp/body/BankBillNo"); //银行订单号
        $BankBillNo=$BankBillNos[0];
        $ResultTypes = $xml->xpath("GateWayRsp/body/ResultType"); //支付返回方式
        $ResultType=$ResultTypes[0];
        $IpsBillTimes = $xml->xpath("GateWayRsp/body/IpsBillTime"); //IPS处理时间
        $IpsBillTime=$IpsBillTimes[0];

        $resParam="关联号:"
            .$ReferenceID
            ."响应编码:"
            .$RspCode
            ."响应说明:"
            .$RspMsg
            ."接受时间:"
            .$ReqDate
            ."响应时间:"
            .$RspDate
            ."数字签名:"
            .$Signature
            ."商户订单号:"
            .$MerBillNo
            ."币种:"
            .$CurrencyType
            ."订单金额:"
            .$Amount
            ."订单日期:"
            .$Date
            ."交易状态:"
            .$Status
            ."发卡行返回信息:"
            .$Msg
            ."数据包:"
            .$Attach
            ."IPS订单号:"
            .$IpsBillNo
            ."交易返回方式:"
            .$RetEncodeType
            ."银行订单号:"
            .$BankBillNo
            ."支付返回方式:"
            .$ResultType
            ."IPS处理时间:"
            .$IpsBillTime;
        file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S新系统获取参数信息:'.$resParam."\r\n",FILE_APPEND);

//验签明文
//billno+【订单编号】+currencytype+【币种】+amount+【订单金额】+date+【订单日期】+succ+【成功标志】+ipsbillno+【IPS订单编号】+retencodetype +【交易返回签名方式】+【商户内部证书】

        $arrayMer=$this->getMerInfo();
        $pmercode = $arrayMer["memno"];
        $sbReq = "<body>"
            . "<MerBillNo>" . $MerBillNo . "</MerBillNo>"
            . "<CurrencyType>" . $CurrencyType . "</CurrencyType>"
            . "<Amount>" . $Amount . "</Amount>"
            . "<Date>" . $Date . "</Date>"
            . "<Status>" . $Status . "</Status>"
            . "<Msg><![CDATA[" . $Msg . "]]></Msg>"
            . "<Attach><![CDATA[" . $Attach . "]]></Attach>"
            . "<IpsBillNo>" . $IpsBillNo . "</IpsBillNo>"
            . "<IpsTradeNo>" . $IpsTradeNo . "</IpsTradeNo>"
            . "<RetEncodeType>" . $RetEncodeType . "</RetEncodeType>"
            . "<BankBillNo>" . $BankBillNo . "</BankBillNo>"
            . "<ResultType>" . $ResultType . "</ResultType>"
            . "<IpsBillTime>" . $IpsBillTime . "</IpsBillTime>"
            . "</body>";
        $sign=$sbReq.$pmercode.$arrayMer['mercert'];
        file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S验签明文:'.$sign."\r\n",FILE_APPEND);
        $md5sign=  md5($sign);
        file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S验签密文:'.$md5sign."\r\n",FILE_APPEND);

        //判断签名
        if(true)
        {
            file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S验签成功.\r\n",FILE_APPEND);
            if($RspCode=='000000')
            {
                file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S订单支付成功.\r\n",FILE_APPEND);
                //
                $orderid = $MerBillNo;
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
//                    if ($order["price"] >= 100) {
//                        M('juejin_users')->where(['id' => $order['userid']])->setInc('money', $order['price']);
//                    }
                }
                if($postType == 1){
                    $this->ret = "支付成功";
                    $this->display();
                }
            }

        }
        else
        {
            file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S验签失败.\r\n",FILE_APPEND);
            echo "订单签名错误";
            if($postType == 1){
                $this->ret = "订单签名错误";
                $this->display();
            }
        }

    }
}

?>