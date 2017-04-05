<?php
 /**
 * class WeixinAction 处理微信接口的请求
 * @created on 2014/1/10
 * @last updated 2015/02/4 
 * @author yanqizheng
 */
namespace Api\Controller;
use Think\Controller;
use Spark\Wechat\Wechat;
use Api\Logic\MessageHandler;
use Api\Logic\TextHandler;
use Api\Logic\EventHandler;
use Api\Logic\LocationHandler;

class WeixinController extends Controller {
    private $token;
    private $data = array();
    private $isDebug = false;
	//微信接口处理
	public function index(){
		ob_clean();
        $this->token = I('get.token','','trim');
		$wxuser = M('wxuser')->where(['token'=>$this->token])->field('appid,token_sign,encript_mode,encript_key')->find();
        $weixin      = new Wechat($this, $wxuser['token_sign'],$wxuser['encript_mode'],$wxuser);
		
		//调试模式下关闭消息验证
		if($_SERVER['HTTP_HOST'] == '127.0.0.1'){
			$this->isDebug = true;
		}
		//验证签名
		if(!$this->isDebug){
			$weixin->auth($wxuser['token_sign']) || exit('ACCESS DENIED');
		}
		if (IS_GET) {
            exit($_GET['echostr']);
        }
		$weixin->dispatch();
    }
	
	//token:5576c0ef3f9ce
	//key:slQFTTCOsNYWcvSk1rIJOGcha5lSBiT1oQaedjwt0a5
	public function proxy(){
		$token = '5576c0ef3f9ce';
		$key = 'slQFTTCOsNYWcvSk1rIJOGcha5lSBiT1oQaedjwt0a5';
		$appid = 'wxac6a1616cf08e7b0';
		Vendor('WxBizCrypt.WXBizMsgCrypt');
		$pc = new \WXBizMsgCrypt($token, $key, $appid);
		$msg = '';
		$errCode = $pc->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $xml_txt, $msg);
		if ($errCode == 0) {
			$xml_txt = $msg;
		} else {
			exit;
		}
		echo $_GET['APPID'];
	}
	
	/**
	 * @method handleText 处理文本消息
	 * @param object 请求消息
	 */
	public function handleText($xml){
		$handler=new TextHandler($this->token,$xml);
		return $handler->handle();
	}
	
	/**
	 * @method handleImage 处理图像消息
	 * @param object 请求消息
	 */
	public function handleImage($xml){
		if($this->token == 'bvqtyw1437098034'){
			return ['感谢您对好车家双11活动大力支持，活动期间好车家客服会添加您为好友。2015年11月11日11时建立微信红包群，在11分准时开抢红包，敬请关注！', "text"];
		}else
			return ['NO RESPONSE', "text"];
	}
	
	/**
	 * @method handleText 处理语音消息
	 * @param object 请求消息
	 */
	public function handleVoice($xml){
	
	}
	
	/**
	 * @method handleText 处理视频消息
	 * @param object 请求消息
	 */
	public function handleVideo($xml){
		return ['NO RESPONSE', "text"];
	}
	
	/**
	 * @method handleLocation 处理位置消息
	 * @param object 请求消息
	 */
	public function handleLocation($xml){
		$handler=new LocationHandler($this->token,$xml);
		return $handler->handle();
	}
	
	/**
	 * @method handleLink 处理链接消息
	 * @param object 请求消息
	 */
	public function handleLink($xml){
		
	}
	
	/**
	 * @method handleText 处理事件消息
	 * @param object 请求消息
	 */
	public function handleEvent($xml) {
		$handler=new EventHandler($this->token,$xml);
		return $handler->handle();
	}
	
	
	public function handleApi($xml) {
		$like['keyword'] = array('like','%' . $xml->Content);
		$like['token']   = $this->token;
		$api             = M('api')->where($like)->order('id desc')->find();
		if ($api != false) {
			$vo['fromUsername'] = $xml->FromUserName;
			$vo['Content']      = $xml->Content;
			$vo['toUsername']   = $this->token;
			if ($api['type'] == 2) {
				$apidata = $this->api_notice_increment($api['url'], $vo);
				return array(
					$apidata,
					'text'
				);
			}
			else {
				$apidata = $this->api_notice_increment($api['url'], $file);
				echo $apidata;
				return false;
			}
		}
	}
	
    public function api_notice_increment($url, $data) {
        $ch     = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        } 
		else {
            return $tmpInfo;
        }
    }
	
	public function getWords($title, $num = 10) {
		Vendor('Pscws.Pscws4', '', '.class.php');
		$pscws = new PSCWS4();
		$pscws->set_dict(CONF_PATH . 'pscws/dict.utf8.xdb');
		$pscws->set_rule(CONF_PATH . 'pscws/rules.utf8.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($title);
		$words = $pscws->get_tops($num);
		$pscws->close();
		$tags = array();
		foreach ($words as $val) {
			$tags[] = $val['word'];
		}
		return implode(',', $tags);
	}
}
?>