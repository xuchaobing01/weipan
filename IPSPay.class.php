<?php
class IPSPay
{
    protected $cfg = array(

        'Version'         => '', //可选 版本号 v1.0.0
        'MerCode'         => '193289', //必填 商户号
        'MerName'         => '', //可选 商户名
        'Account'         => '1932890013', //必填 账号号 交易账户号
        'MsgId'           => '', //可选 消息编号 消息唯一标示，交易必输，查询可选
        'ReqDate'         => '', //必填 商户请求时间 yyyyMMddHHmmss
        'Signature'       => '', //必填 数字签名 md5 body.商户号.商户证书

        'MerBillNo'       => '', //商户订单号 30位一下字母及数字
        'Amount'          => '', //订单金额 保留两位小数 12.00
        'Date'            => '', //订单日期 yyyyMMdd
        'CurrencyType'    => '156', //币种 156#人名币
        'GatewayType'     => '01', //支付方式  01#借记卡;02#行用卡;03#IPS账户支付 默认01
        'Lang'            => 'GB', //语音 中文 GB
        'Merchanturl'     => 'http://weipan.haojiehua.pw/Wap/User/private_person.html', //支付结果成功返回商户URL
        'FailUrl'         => 'http://weipan.haojiehua.pw/Wap/User/private_person.html', //支付结果失败返回的商户URL
        'Attach'          => '', //商户数据包
        'OrderEncodeType' => '5', //订单支付接口加密方式 md5 值：5
        'RetEncodeType'   => '17', //交易返回接口加密方式 17#md5 16#md5withrsa
        'RetType'         => '1', //返回方式 1#S2S Server to Server
        'ServerUrl'       => 'http://weipan.haojiehua.pw/Wap/User/private_person.html', //异步S2S返回 当RetType#1时，本字段有效
        'BillEXP'         => '', //订单有效期 以小时计算 必须是整数
        'GoodsName'       => '', //商品名称
        'IsCredit'        => '', //直连选项 1#直连必填
        'BankCode'        => '', //银行号 直连必填
        'ProductType'     => '', //产品类型 1#个人网银 2#企业网银 直连必填
    );

    protected $MerKey = 'QkYH5JEQPvdmQaMHS4mMlTHBuGVRUMDcuRchMzXLCts53rfd1xrtu4DAPXQm8RhwlbBMsnakzv5a8rlc3YWco19BRqDqHlpeFBAZ75lk6W0wmyu2xn6TrJJFeSYwPMQV'; //商户交易密钥

    public function __construct($merbillno, $amount, $date, $goodsname)
    {
        $this->cfg['ReqDate']   = date('YmdHis');
        $this->cfg['MerBillNo'] = $merbillno;
        $this->cfg['Amount']    = $amount;
        $this->cfg['Date']      = $date;
        $this->cfg['GoodsName'] = $goodsname;
    }

    public function form()
    {
        $dataStr = '<Ips><GateWayReq>';
        $dataStr .= $this->head() . $this->body();
        $dataStr .= '</GateWayReq></Ips>';
        //https://newpay.ips.com.cn/psfp-entry/gateway/payment.do
        //https://mobilegw.ips.com.cn/psfp-mgw/paymenth5.do
        $str = '<form action="https://mobilegw.ips.com.cn/psfp-mgw/paymenth5.do" method="post">
		<input name="pGateWayReq" type="text" value="' . $dataStr . '" />
		<input type="submit">
		</form>';

        return $str;
    }

    public function crypt()
    {

        $str = $this->body() . $this->cfg['MerCode'] . $this->MerKey;

        $this->cfg['Signature'] = md5($str);

    }

    public function head()
    {
        $this->crypt(); //生成签名

        $head = '<head>';
        $head .= '<Version>%s</Version>';
        $head .= '<MerCode>%s</MerCode>';
        $head .= '<MerName>%s</MerName>';
        $head .= '<Account>%s</Account>';
        $head .= '<MsgId>%s</MsgId>';
        $head .= '<ReqDate>%s</ReqDate>';
        $head .= '<Signature>%s</Signature>';
        $head .= '</head>';

        return sprintf($head, $this->cfg['Version'], $this->cfg['MerCode'], $this->cfg['MerName'], $this->cfg['Account'], $this->cfg['MsgId'], $this->cfg['ReqDate'], $this->cfg['Signature']);
    }

    public function body()
    {
        $body = '<body>';
        $body .= '<MerBillNo>%s</MerBillNo>';
        $body .= '<Amount>%s</Amount>';
        $body .= '<Date>%s</Date>';
        $body .= '<CurrencyType>%s</CurrencyType>';
        $body .= '<GatewayType>%s</GatewayType>';
        $body .= '<Lang>%s</Lang>';
        $body .= '<Merchanturl>%s</Merchanturl>';
        $body .= '<FailUrl>%s</FailUrl>';
        $body .= '<Attach>%s</Attach>';
        $body .= '<OrderEncodeType>%s</OrderEncodeType>';
        $body .= '<RetEncodeType>%s</RetEncodeType>';
        $body .= '<RetType>%s</RetType>';
        $body .= '<ServerUrl>%s</ServerUrl>';
        $body .= '<BillEXP>%s</BillEXP>';
        $body .= '<GoodsName>%s</GoodsName>';
        $body .= '<IsCredit>%s</IsCredit>';
        $body .= '<BankCode>%s</BankCode>';
        $body .= '<ProductType>%s</ProductType>';
        $body .= '</body>';

        return sprintf($body, $this->cfg['MerBillNo'], $this->cfg['Amount'], $this->cfg['Date'], $this->cfg['CurrencyType'], $this->cfg['GatewayType'], $this->cfg['Lang'], $this->cfg['Merchanturl'], $this->cfg['FailUrl'], $this->cfg['Attach'], $this->cfg['OrderEncodeType'], $this->cfg['RetEncodeType'], $this->cfg['RetType'], $this->cfg['ServerUrl'], $this->cfg['BillEXP'], $this->cfg['GoodsName'], $this->cfg['IsCredit'], $this->cfg['BankCode'], $this->cfg['ProductType']
        );

    }
}
