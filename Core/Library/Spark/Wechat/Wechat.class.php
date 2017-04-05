<?php
/**
 * class Wechat 微信消息处理类
 * @date 2014/01/23 
 * @author yanqizheng
 * @copyright SparkTech
 * @update 2015-02-05 增加消息体加密功能
 */
namespace Spark\Wechat;
use Think\Log;
class Wechat{
    private $data = array();
	public $handler = null;
	private $xml ;
	private $log = false;
	private $isEncrypted = false;
	private $token = '';
	/**
	 * constructor 
	 * @param string token 公众号验证token
	 */
    public function __construct($handler, $token ,$encript_mode,$set){
		$this->handler=$handler;
		$this->token = $token;
		if (IS_POST) {
            $xml_txt = file_get_contents("php://input");
			if($this->log){
				Log::write($xml_txt, Log::INFO);
			}
			if($encript_mode == '1' && $_GET['msg_signature']){
				Vendor('WxBizCrypt.WXBizMsgCrypt');
				$pc = new \WXBizMsgCrypt($token, $set['encript_key'], $set['appid']);
				$msg = '';
				$errCode = $pc->decryptMsg($_GET['msg_signature'], $_GET['timestamp'], $_GET['nonce'], $xml_txt, $msg);
				if ($errCode == 0) {
					$xml_txt = $msg;
					$this->isEncrypted = true;
					$this->wxset = $set; 
				} else {
					exit;
				}
			}
			$xml = new \SimpleXMLElement($xml_txt);
			$xml || exit;
			$this->xml =new \stdClass();
			foreach ($xml as $key => $value) {
				$this->xml->$key = strval($value);
			}
        }
    }
	
	/**
	 * @method dispatch 消息调度
	 */
	public function dispatch() {
		if(!is_null($this->handler)){
			if(method_exists($this->handler,"dispatch")){
				$resp = $this->dispatch($this->xml);
			}
			else { //默认根据消息类型调用相应函数
				switch ($this->xml->MsgType){
					case 'text':
						$resp = $this->handler->handleText($this->xml);
						break;
					case 'image':
						$resp = $this->handler->handleImage($this->xml);
						break;
					case 'voice':
						$resp = $this->handler->handleVoice($this->xml);
						break;
					case 'video':
						$resp = $this->handler->handleVideo($this->xml);
						break;
					case 'location':
						$resp = $this->handler->handleLocation($this->xml);
						break;
					case 'link':
						$resp = $this->handler->handleLink($this->xml);
						break;
					case 'event':
						$resp = $this->handler->handleEvent($this->xml);
						break;
					default:
						$resp = $this->handler->handleDefault($this->xml);
				}
			}
			if(!empty($resp)){
				$this->response($resp[0], $resp[1]);
			}
			else if($this->debug){
				$this->response('no response!');
			}
		}
	}
	
	/**
	 * method request 获取用户请求数据
	 * @return array 用户请求数据
	 */
    public function request(){
        return $this->data;
    }
	
	/**
	 * @method response 回复用户请求
	 * @param mix 回复内容
	 * @param string 回复类型，默认为文本类型
	 * @param int 回复标志
	 */
    public function response($content, $type = 'text') {
        $this->data = array(
            'ToUserName' => $this->xml->FromUserName,
            'FromUserName' => $this->xml->ToUserName,
            'CreateTime' => NOW_TIME,
            'MsgType' => $type
        );
		if(method_exists($this,$type)){
			$this->$type($content);
		}
        $xml                    = new \SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
		if($this->isEncrypted){
			$timestamp = $_GET['timestamp'];
			$nonce = $_GET['nonce'];
			$pc = new \WXBizMsgCrypt($this->token, $this->wxset['encript_key'], $this->wxset['appid']);
			$encryptMsg = '';
			$errCode = $pc->encryptMsg($xml->asXML(), $timestamp, $nonce, $encryptMsg);
			$xml_tree = new \DOMDocument();
			$xml_tree->loadXML($encryptMsg);
			$array_e = $xml_tree->getElementsByTagName('Encrypt');
			$array_s = $xml_tree->getElementsByTagName('MsgSignature');
			$encrypt = $array_e->item(0)->nodeValue;
			$msg_sign = $array_s->item(0)->nodeValue;
			
			//$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
			$format = '<xml><Encrypt><![CDATA[%s]]></Encrypt><MsgSignature><![CDATA[%s]]></MsgSignature><TimeStamp>'.$timestamp.'</TimeStamp><Nonce><![CDATA['.$nonce.']]></Nonce></xml>';
			$resp_xml = sprintf($format, $encrypt,$msg_sign);
			exit($resp_xml);
		}
        else exit($xml->asXML());
    }
	
	//转换成多客服模式
	public function transferCS(){
		$this->data = array(
            'ToUserName' => $this->xml->FromUserName,
            'FromUserName' => $this->xml->ToUserName,
            'CreateTime' => NOW_TIME,
            'MsgType' => 'transfer_customer_service'
        );
        $xml                    = new \SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $this->data);
	}
	
	/**
	 * @method text 回复文本消息
	 * @param string 文本内容
	 */
    private function text($content)
    {
        $this->data['Content'] = $content;
    }
	
	/**
	 * @method music 回复音乐消息
	 * @param array 音乐资源信息
	 */
    private function music($music) {
		$m['Title'] = $music[0];
		$m['Description'] = $music[1];
		$m['MusicUrl'] = $music[2];
		$m['HQMusicUrl'] = $music[3];
       
		$this->data['Music'] = $m;
    }
	
    private function news($news) {
        $articles = array();
        foreach ($news as $key => $value) {
            list($articles[$key]['Title'], $articles[$key]['Description'], $articles[$key]['PicUrl'], $articles[$key]['Url']) = $value;
            if ($key >= 9) {
                break;
            }
        }
        $this->data['ArticleCount'] = count($articles);
        $this->data['Articles']     = $articles;
    }
	
	/**
	 * @method data2xml 将回复的消息转换成xml
	 * @param object 用以保存结果的xml对象
	 * @param array 消息内容
	 * @param string 
	 */
    private function data2xml($xml, $data, $item = 'item'){
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            }
			else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                }
				else {
                    $child = $xml->addChild($key);
                    $node  = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }
	
	/**
	 * @method auth 公众号接口验证
	 * @param string 公众号token
	 * @return boolean 验证通过返回true,否则返回false
	 */
    public function auth($token) {
        $data = array(
            $_GET['timestamp'],
            $_GET['nonce'],
            $token
        );
        $sign = $_GET['signature'];
        sort($data, SORT_STRING);
        $signature = sha1(implode($data));
        return $signature === $sign;
    }

}
?>