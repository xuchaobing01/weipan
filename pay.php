<?php
$dd  = md5('<body><MerBillNo>001</MerBillNo><Amount>5.00</Amount><Date>20170401</Date><CurrencyType>156</CurrencyType><GatewayType>01</GatewayType><Lang>GB</Lang><Merchanturl>http://weipan.haojiehua.pw/Wap/User/private_person.html</Merchanturl><FailUrl></FailUrl><Attach></Attach><OrderEncodeType>5</OrderEncodeType><RetEncodeType>17</RetEncodeType><RetType>1</RetType><ServerUrl>1</ServerUrl><BillEXP></BillEXP><GoodsName>office</GoodsName><IsCredit></IsCredit><BankCode></BankCode><ProductType></ProductType></body>' . '193289' . 'QkYH5JEQPvdmQaMHS4mMlTHBuGVRUMDcuRchMzXLCts53rfd1xrtu4DAPXQm8RhwlbBMsnakzv5a8rlc3YWco19BRqDqHlpeFBAZ75lk6W0wmyu2xn6TrJJFeSYwPMQV');
$str = '<Ips><GateWayReq><head><Version></Version><MerCode>193289</MerCode><MerName></MerName><Account>1932890013</Account><MsgId></MsgId><ReqDate>20170401094200</ReqDate><Signature>' . $dd . '</Signature></head><body><MerBillNo>001</MerBillNo><Amount>5.00</Amount><Date>20170401</Date><CurrencyType>156</CurrencyType><GatewayType>01</GatewayType><Lang>GB</Lang><Merchanturl>http://weipan.haojiehua.pw/Wap/User/private_person.html</Merchanturl><FailUrl></FailUrl><Attach></Attach><OrderEncodeType>5</OrderEncodeType><RetEncodeType>17</RetEncodeType><RetType>1</RetType><ServerUrl>1</ServerUrl><BillEXP></BillEXP><GoodsName>office</GoodsName><IsCredit></IsCredit><BankCode></BankCode><ProductType></ProductType></body></GateWayReq></Ips>';
?>


<form action="https://newpay.ips.com.cn/psfp-entry/gateway/payment.do" method="post">
	<input name="pGateWayReq" type="text" value="<?php echo $str ?>" />
	<input type="submit">
</form>
