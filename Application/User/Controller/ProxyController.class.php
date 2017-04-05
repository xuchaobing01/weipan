<?php
namespace User\Controller;
use Think\Log;
class ProxyController extends BaseController{
	public function notify(){
		$token = '5576c0ef3f9ce';
		$key = 'slQFTTCOsNYWcvSk1rIJOGcha5lSBiT1oQaedjwt0a5';
		$appid = 'wxac6a1616cf08e7b0';
		Vendor('WxBizCrypt.WXBizMsgCrypt');
		$pc = new \WXBizMsgCrypt($token, $key, $appid);
		$rawXml = file_get_contents("php://input");
		$msg = '';
		$errCode = $pc->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $rawXml, $msg);
		if ($errCode == 0) {
			$rawXml = $msg;
		}
		Log::write($rawXml,'INFO');
		$xml = new \SimpleXMLElement($rawXml);
		$this->xml =new \stdClass();
		foreach ($xml as $key => $value) {
			$this->xml->$key = strval($value);
		}
		M('system_kv')->where(['key'=>'component_verify_ticket'])->setField('value',$this->xml->ComponentVerifyTicket);
		echo 'success';
	}
}