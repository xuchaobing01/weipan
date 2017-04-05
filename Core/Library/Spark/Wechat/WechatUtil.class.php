<?php
/**
 * @class WechatUtil 微信接口通用工具类
 * @author: yanqizheng
 * @date: 2014/01/25
 */
namespace Spark\Wechat;
use Spark\Util\HttpClient;
use Org\Net\Http;
class WechatUtil{
	public $_accessToken;
	private $_apiUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
	private $_fileUrl = 'http://file.api.weixin.qq.com/cgi-bin/media/get?';
	public function __construct($appId, $appSecret) {
		$this->_appid = $appId;
		$this->_appSecret = $appSecret;
		$this->_accessToken = $this->getAccessToken();
	}
	
	public function download($mediaId,$format){
		$url = $this->_fileUrl.'access_token='.$this->_accessToken.'&media_id='.$mediaId;
		Http::curlDownload($url,'/data/wxplatform/asset/voice/'.$mediaId.'.'.$format);
		return true;
	}

    public function downloadWXImg($mediaId){
        $url = $this->_fileUrl.'access_token='.$this->_accessToken.'&media_id='.$mediaId;
        //\Think\Log::write("url:".$url);
        $path = '/Uploads/wx/'.$mediaId.'.jpg';
        Http::curlDownload($url,__ROOT__.$path);
        return $path;
    }
	
	//从缓存中获取access_token
	public function getAccessToken(){	
		if(S($this->_appid.'_access_token') == false){
			$url = $this->_apiUrl.'&appid='.$this->_appid.'&secret='.$this->_appSecret;
			
			$resp = json_decode(HttpClient::curlGet($url));
			if($resp->errcode){
				return $resp->errmsg;
			}
			else{
				S($this->_appid.'_access_token',$resp->access_token,3600);
				return $resp->access_token;
			}
		}
		return S($this->_appid.'_access_token');
	}
	
	/**
	 *@setMenu 生成自定义菜单
	 */
	public function setMenu($menus){
		$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->_accessToken;
		$ret = json_decode(HttpClient::curlPost($url,$menus));
		return $ret;
	}
	
	/**
	 *@getMenu 获取已有自定义菜单
	 */
	public function getMenu(){
	
	}
	
	/**
	 *@deleteMenu 删除已有自定义菜单
	 *成功后返回 {"errcode":0,"errmsg":"ok"}
	 */
	public function deleteMenu(){
		HttpClient::curlGet('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->_accessToken);
		$ret = json_decode(ret);
		return $ret;
	}
	
	public function getOAuth2Code($redirect_uri,$scope='snsapi_base',$state='1'){
		$redirect_uri = urlencode($redirect_uri);//链接进行url编码
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->_appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
		header('Location:'.$url);
		exit;
	}
	
	public function getOAuth2AccessToken($code){
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->_appid}&secret={$this->_appSecret}&code={$code}&grant_type=authorization_code";
		$ret = HttpClient::curlGet($url);
		return json_decode($ret);
	}
	
	public function getUserInfo($openid){
		if(empty($openid)){
			return false;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->_accessToken}&openid={$openid}&lang=zh_CN";
		$ret = json_decode(HttpClient::curlGet($url));
		return $ret;
	}
	public function getNewUserInfo($openid,$access_token){//snsapi_userinfo状态获取信息
		if(empty($openid)){
			return false;
		}
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
		$ret = json_decode(HttpClient::curlGet($url));
		return $ret;
	}
	//发送文字消息
	public function sendCustomMsg($from_user,$msg){
		$post = '{"touser":"' . $from_user . '","msgtype":"text","text":{"content":"' . $msg . '"}}';
		$ret = json_decode(HttpClient::curlPost("https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$this->_accessToken}",$post));
		return $ret;
	}
	//发送模板消息
	public function sendTempMsg($template_id, $date)
	{
		$postarr = $date;
		$ret = json_decode(HttpClient::curlPost("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$this->_accessToken}",$postarr));
		return $ret;
	}
	
	//获取场景二维码图像
	public function getQrcodeUrl($sceneId){
		$ticket = $this->getQrcodeTicket($sceneId,1);
		return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
	}
	
	//获取场景二维码ticket
	public function getQrcodeTicket($sceneId,$type = 0){
		if($type==0){
			$data['expire_seconds'] = 1800;
			$data['action_name'] = 'QR_SCENE';
		}
		else{
			$data['action_name'] = 'QR_LIMIT_SCENE';
		}
		$data['action_info'] = ['scene'=>['scene_id'=>$sceneId]];
		
		$json = json_encode($data);
		$ret = json_decode(HttpClient::curlPost("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$this->_accessToken}",$json));
		if($ret->errcode!=0){
			return false;
		}
		else return $ret->ticket;
	}
	
	/**
	 * @method getUserList 获取粉丝列表
	 * 
	 */
	public function getUserList($next_openid=''){
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->_accessToken}&next_openid={$next_openid}";
		$ret = json_decode(HttpClient::curlGet($url));
		return $ret;
	}
	
	/**
	 * @method
	 */
	public function getUser($openid){
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->_accessToken}&openid={$openid}&lang=zh_CN";
		$ret = json_decode(HttpClient::curlGet($url));
		return $ret;
	}
	
	/**
	 *@method 发送模板消息
	 */
	public function pushMessage($tplId,$touser,$set,$values,$colors = []){
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$this->_accessToken}";
		$data = array();
		$data['template_id'] = $tplId;
		$data['touser'] = $touser;
		$data['topcolor'] = $set['color']?$set['color']:'#44B549';
		$data['url'] = $set['url'];
		foreach($values as $key => $value){
			$item['value'] = $value;
			$item['color'] = $colors[$key]?$colors[$key]:"#000000";
			$data['data'][$key] = $item;
		}
		$json = json_encode($data);
		$ret = json_decode(HttpClient::curlPost($url,$json));
		return $ret;
	}
	
	public function getJsApiTicket(){
		if(S($this->_appid.'_jsapi_ticket') == false){
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$this->_accessToken}&type=jsapi";
			$resp = json_decode(HttpClient::curlGet($url));
			if($resp->errcode){
				return false;
			}
			else{
				S($this->_appid.'_jsapi_ticket',$resp->ticket,3600);
				return $resp->ticket;
			}
		}
		return S($this->_appid.'_jsapi_ticket');
	}
}
?>