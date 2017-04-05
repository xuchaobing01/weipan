<?php
/**
 * @class WapController Wap模块控制器基类
 */
namespace Wap\Controller;
use Think\Controller;
class WapController extends Controller {
	protected $allowFromWeb = true;//是否允许非微信浏览器访问
	protected $strict_mode = true;//是否获取访问者openid
	protected $allow_anonymous = true;//是否允许匿名访问
	protected $token;
	protected $wechat_id;
    protected function _initialize() {
        
        //限制访问客户端
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(!$this->allowFromWeb && strpos($agent,"MicroMessenger")===FALSE) {
			exit('请使用微信浏览器访问！');
		}
		
        //从url中获取到token
        $this->token = 'anuxzf1435128586';
        //确定token,并且访问的公众号存在0
        if($this->token == ''){
            exit('非法访问! empty token');
        }
		
		//添加session前缀,确保区分用户同时访问多个商家
		session(['prefix'=>$this->token.'_']);
		//初始化缓存系统
		S(array('type'=>C('DATA_CACHE_TYPE'),'host'=>C('DATA_CACHE_HOST'),'port'=>C('DATA_CACHE_PORT'),'prefix'=>$this->token.'_','expire'=>C('DATA_CACHE_TIME')));
		
		$this->wxConfig = $this->getWxConfig();
		if($this->wxConfig == null){
			exit('非法访问, not found wxConfig!');
		}
		
		if(ACTION_NAME =='oauth2callback') return;
		$this->wechat_id =I('get.wechat_id','','trim');
		//$this->wechat_id='ookZtt4GUlquFv4WksRildCbLBEY';
		
		if(!is_valid_openid($this->wechat_id) && strpos($agent,"MicroMessenger")){//$this->strict_mode){//strpos($agent,"MicroMessenger")){
			if(is_valid_openid(session('wechat_id'))){
				$this->wechat_id = session('wechat_id');
			}
			else{
				$this->getOpenid();
			}
		}
		else{
			session('wechat_id',$this->wechat_id);
		}
		//判断访问者身份是否合法
		if(is_valid_openid($this->wechat_id)){
			$this->isValidOpenid = true;
			$open_id=$this->wechat_id;
			$phone=M('wechat_user')->where(array('open_id'=>$open_id))->getField('phone');
			$user=M('juejin_users')->where(['phone'=>$phone])->find();
			if($user){
				$_SESSION['user_id']=$user['id'];
				$_SESSION['user_phone']=$user['phone'];
			}
		}
		else $this->isValidOpenid = false;
		
		if(!$this->is_open_action()) {
			if ($_SESSION['user_id'] == '') {
//				$this->redirect('Wap/Login/login_show');
                $this->redirect('Wap/Login/regist');
				exit;
			}else{
				$status = M('juejin_users')->where(array('id'=>$_SESSION['user_id']))->getField('status');
				if($status==1){

					echo '<span style="font-size: 78px;display: block;text-align: center;top: 50%;position: relative;">该账号已冻结！！</span>';
					die;
				}
			}
		}
		
        $this->assign('token',$this->token);
        $this->assign('wechat_id',$this->wechat_id);
        $this->assign('wxset',$this->wxConfig);
		define('RES','/Public/'.MODULE_NAME);
		define('STATICS','/Public/Common');
    }
    /**
     *@method 判断是否是匿名访问操作
     */
    protected function is_open_action(){
    	return  in_array(CONTROLLER_NAME, ['Login','Seccode','Wap']) || in_array(ACTION_NAME,['insurance','zhuce','share','play', 'set_sim']) || (CONTROLLER_NAME == "Trading" && (in_array(ACTION_NAME,['index','getoption','lastKlineParameter','highStockChart','klineChart','get_now_orders', 'getUserCoupon'])));//  && in_array(ACTION_NAME,['login','reg','seccode']);
    }
	
	//通过OAuth授权获取openid
	public function getOpenid(){
		//认证后的服务号才能使用该接口
		if(isset($_GET['_from'])) session('ORIGIN_URL',$_GET['_from']);
		else session('ORIGIN_URL',urlencode(__SELF__));
		if($this->wxConfig['wxtype']=='服务号'&&$this->wxConfig['is_certified']==1){
			$util = new \Spark\Wechat\WechatUtil($this->wxConfig['appid'], $this->wxConfig['appsecret']);
			//$util->getOAuth2Code(C('wap_domain').'/Wap/Wap/oauth2callback.html?token='.$this->token);
			$util->getOAuth2Code(C('wap_domain').'/Wap/Wap/oauth2callback.html?token='.$this->token,'snsapi_userinfo','userinfo');//2016-1-23
		}
		else if(!$this->allow_anonymous){
			exit('<h2>无法获取您的身份信息！</h2>');
		}
	}
	
	//OAuth授权回调方法
	public function oauth2callback(){
		if(isset($_GET['code'])){
			$util = new \Spark\Wechat\WechatUtil($this->wxConfig['appid'],$this->wxConfig['appsecret']);
			$ret = $util->getOAuth2AccessToken($_GET['code']);
			if($ret&&$ret->openid){
				session('wechat_id',$ret->openid);
				if ($_GET['state']  == 'userinfo') {//2016-1-23
					$user = $util->getNewUserInfo($ret->openid,$ret->access_token);
					if($user&&$user->openid){
						$u = M('wechat_user')->where(['open_id'=>$user->openid])->find();
						if($u==null || empty($u['wechat_name'])){
							$data['token'] =$this->token;
							$data['open_id'] = $user->openid;
							$data['wechat_name'] = $user->nickname;
							$data['sex'] = $user->sex;
							$data['language'] = $user->language;
							$data['country'] = $user->country;
							$data['province'] = $user->province;
							$data['headimgurl'] = substr($user->headimgurl,0,-1);
							$data['update_time'] = time();
							if($u==null){
								M('wechat_user')->add($data);
							}
							else{
								M('wechat_user')->where(['id'=>$u['id']])->save($data);
							} 	
						}
					}			
				}
				header('location:'.urldecode(session('ORIGIN_URL')).'#wechat_redirect');
				exit('正在跳转...');
			}
			else{
				$this->assign('message','获取用户信息失败！ERR:'.json_encode($ret));
				$this->assign('detail','<code>'.__SELF__.'</code><br/>');
				$this->display('Public:error');
			}
		}
		else {
			$this->assign('message','获取oauth 授权code失败！');
			$this->display('Public:error');
		}
	}
	
	//获取公众号设置信息
	protected function getWxConfig(){
		if(S('WXCONFIG')==false){
			$config = M('wxuser','sp_')->where(['token'=>$this->token])->find();
			S('WXCONFIG',$config);
			return $config;
		}
		else return S('WXCONFIG');
	}
	
	//获取微信用户信息，默认为当前用户
	protected function getWechatInfo($openid,$update=false){
		if(empty($openid)){
			$openid = $this->wechat_id;
		}
		$u = new \User\Model\WechatUserModel('wechat_user','sp_');
		$u->token = $this->token;
		$u->wxConfig = $this->wxConfig;
		return $u->get($openid,$update);
	}
	
	public function reset(){
		session('[destroy]');
	}
	
	public function openid(){
		echo $this->wechat_id;
	}
	
	//给当前用户发送模板消息
	protected function sendTplMessage($tpl_id,$url,$data){
		$set['url'] = $url;
		$util = new \Spark\Wechat\WechatUtil($this->wxConfig['appid'],$this->wxConfig['appsecret']);
		return $util->pushMessage($tpl_id,$this->wechat_id,$set,$data);
	}
	
	//获取会员信息
	protected function getMemberInfo($openid){
		if(empty($openid)){
			$openid = $this->wechat_id;
		}
		return M('member_info')->where(['token'=>$this->token,'wechat_id'=>$openid])->find();
	}
	
	//获取分享配置
	protected function getWxShareConfig(){
		$config = M('wxuser')->where(['token'=>$this->token])->find();
		$util = new \Spark\Wechat\WechatUtil($config['appid'],$config['appsecret']);
		$data['jsapi_ticket'] = $util->getJsApiTicket();
		$data['timestamp'] = time();
		$data['noncestr'] = create_key();
		$data['url'] = C('WAP_DOMAIN').$_SERVER['REQUEST_URI'];
		$plain = 'jsapi_ticket='.$data['jsapi_ticket'].'&noncestr='.$data['noncestr'].'&timestamp='.$data['timestamp'].'&url='.$data['url'];
		$signature = sha1($plain);
		return ['appid'=>$config['appid'],'signature'=>$signature,'timestamp'=>$data['timestamp'],'noncestr'=>$data['noncestr']];
	}
	
	protected function isFocused(){
		if($this->wechat_id != ''){
			$check = M('wechat_user')->where(['open_id'=>$this->wechat_id])->count();
			if(!$check){		
				$date =$this-> getWechatInfo($this->wechat_id);
				$check = $date['subscribe'];
			}
			return $check != 0;
		}
		else return false;
	}
}
?>