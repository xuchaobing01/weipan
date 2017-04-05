<?php
/**
 * @class TextHandler 处理文本消息
 * @author yanqizheng
 * @date 2014/05/14
 */
namespace Spark\Wechat;
class TextHandler extends MessageHandler {
	/**
	 * @method handle 处理文本消息
	 * @param object 请求消息
	 */
	public function handle() {
		$key       = $this->content;
		
		if(preg_match("/开车去|坐公交|步行去|最近的/",$key)) {
			return $this->guide($key);
		}
		
		$resp = $this->handleSysKeyword($key);
		if(!empty($resp)) {
			return $resp;
		}
		/*自定义关键词处理*/
		$resp = $this->handleCustomKeyword($key);
		
		if(!empty($resp)) {
			return $resp;
		}
		/*微服务处理*/
		$resp = $this->handleExService($key);
		if(!empty($resp)) {
			return $resp;
		}
		$set = M('kefu')->where(['token'=>$this->token])->find();
		/*智能客服处理*/
		$resp = $this->handleAutoService($key);
		if(!empty($resp)) {
			return $resp;
		}
		if($set['transfer_to_customer'] == 1){
			//转为人工客服消息
			return array(null,'transfer_customer_service');
		}
		/*默认回复处理*/
		return $this->defaultResponse();
	}
	
	/**
	 * @method guid 导航回复
	 */
	public function guide($key) {
		$this->saveRequest($key);
		$user_request_model = M('User_request');
		$location = $this->getLastLocation(60);//获取用户最近的位置
		if ($location) {
			return $this->LBSHandle($key, $location);
		}
		//如果无最近位置，则提醒用户输入位置
		return $this->text('请发送您所在的位置');
	}
	
	/**
	 * @method LBSHandle 处理有关位置的请求
	 */
	public function LBSHandle($keyword, $location) {
		$lh = new LocationHandler($this->token);
		$lh->fromUserName=$this->fromUserName;
		return $lh->LBSHandle($keyword, $location[1], $location[0]);
	}
	
	/**
	 * @method handleKeyword 处理系统关键词
	 */
	public function  handleSysKeyword($key) {
		if($key == '地图'){
			return $this->companyMap();
		}
		if(in_array($key, ['最近的','最近'])){
			return $this->guide($key);
		}
		if(in_array($key, ['help','帮助'])){
			return $this->help();
		}
	}
	
	/**
	 * @method keyword 回复商家配置的关键词
	 */
	public function handleCustomKeyword($key) {
		$data = $this->searchKeyword($key);
		if($data==null ||$data==false){
			$data = $this->searchKeyword($key,false);//完全匹配失败时采用模糊匹配
		}
		
		if ($data) {
			switch ($data['module']) {
				case 'Home':
					return $this->home();
					break;
				case 'Img':
					return $this->newsResponse($data['pid']);
					break;
				case 'Host':
					return $this->host($data['pid']);
					break;
				case 'Text':
					return $this->textResponse($data['pid']);
					break;
				case 'Product':
					return $this->product($data['pid']);
					break;
				case 'Selfform':
					return $this->selfform($data['pid']);
					break;
				case 'Panorama':
					return $this->panorama($data['pid']);
					break;
				case 'Dining':
					return $this->dining();
					break;
				case 'Lottery':
					return $this->lottery($data['pid']);
					break;
				case 'Voice':
					return $this->musicResponse($data['pid']);
				case 'Estate'://处理微房产模块
					return $this->microEstate($data['pid']);
				case 'WeiMall'://处理新版微商城模块
					return $this->microMall($data['pid']);
				case 'Membercard'://处理新版微商城模块
					return $this->member($data['pid']);
				case 'Impression'://处理微印象模块
					return $this->impression($data['pid']);
				case 'Message'://处理留言模块
					return $this->leaveMessage();
				case 'Photo'://处理微相册模块
					return $this->album();
				case 'Vote'://处理投票模块
					return $this->vote($data['pid']);
				case 'Wedding'://处理喜帖模块
					return $this->wedding($data['pid']);
			}
		}
	}
	
	/**
	 *@method handleAutoService 处理智能客服回复
	 */
	public function handleAutoService($keyword){
		$model = M('Kefu');
		$set = $model->where(['token'=>$this->token])->find();
		if($set !==null && $set['status']==1){
			$answer = M('Kefu_answer')->where("token = '{$this->token}' and question like '%{$keyword}%'")->find();
			if($answer !=null){
				return $this->text($set['kefu_name'].' '.date('H:i:s')."\n".$answer['answer']);
			}
		}
	}
	
	/**
	 * @method defaultResponse 默认处理
	 */
	public function defaultResponse(){
		$other = M('Other')->where(array(
			'token' => $this->token
		))->find();
		if ($other == false) {
			return $this->text('回复帮助，可了解所有功能');
		}
		
		return $this->handleCustomKeyword($other['keyword']);
	}
	
	/**
	 * @method searchKeyword 查找关键字对应的回复
	 * @param keyword 用户发送的关键字
	 * @param is_strict 是否完全匹配关键字,默认为是
	 */
	public function searchKeyword($keyword, $is_strict=true){
		$model = M('Keyword');
		if($is_strict){
			$where['keyword'] = $keyword;
			$where['token'] = $this->token;
			$where['is_strict'] = 1;
		}
		else{
			$where['keyword'] = array('like', '%'.$keyword.'%');
			$where['token'] = $this->token;
			$where['is_strict'] = 0;
		}
		$data            = $model->where($where)->order('id desc')->find();
		return $data;
	}
	
	/**
	 * @method handleExService 处理扩展功能
	 */
	public function handleExService($key) {
		import('Spark.Wechat.Functions');
		$open      = M('Token_open')
		->where(array('token' => $this->token))
		->find();
		$this->service = $open['queryname'];
		
		$datafun   = explode(',', $this->service);
		$words      = explode(' ', $key);
		$Pin       = new \Spark\Util\PinYin();
		$string = $Pin->getCode($words[0]);
		if (in_array($string, $datafun) && function_exists($string)) {
			array_shift($words);
			eval('$return = ' . $string . '($words);');
		}
		
		if (!empty($return)) {
			if(is_array($return)){
				if($return[1]=="music"){
					return $this->music($return[0]);
				}
				else if($return[1]=="voice"){
					return $this->voice($return[0]);
				}
				else if($return[1]=="news"){
					return $this->news($return[0]);
				}
			}
			else{
				return $this->text($return);
			}
		}
	}
	
	/**
	 * @method microEstate 微房产回回复处理
	 */
	public function microEstate($id){
		$setting = M('Estate')->where(array(
			'id' => $id
		))->find();
		return $this->news(array(
			$setting['title'],
			'',
			$setting['cover'],
			$this->U('Wap/Estate/index')
		));
	}
	
	/**
	 * @method microEstate 微商城回复处理
	 */
	public function microMall($id){
		$setting = M('reply_info')
		->where(array('infotype' => 'WeiMall','token' => $this->token))
		->find();
		if($setting){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					rtrim(C('wap_domain'),'/').'/mall/index.php?token='.$this->token.'&wechat_id='.$this->fromUserName.'&showloadimg=1'
				)
			);
		}
	}
	
	/**
	 * @method order 预约和表单回复
	 */
	public function selfform($id){
		$form = M('Selfform')->where(array(
			'id' => $id
		))->find();
		return $this->news(array(
			$form['name'],
			strip_tags(htmlspecialchars_decode($form['intro'])),
			$form['logourl'],
			$this->U('Wap/Selfform/index',['id'=>$id])
		));
	}
	
	/**
	 * @method dining 订餐回复
	 */
	public function dining(){
		$pro = M('reply_info')
		->where(array('infotype' => 'Dining','token' => $this->token))
		->find();
		return $this->news(
			array(
				$pro['title'],
				strip_tags(htmlspecialchars_decode($pro['info'])),
				$pro['picurl'],
				$this->U('Wap/Dining/index')
			)
		);
	}
	
	/**
	 * @method leaveMessage 留言
	 */
	public function leaveMessage(){
		$setting = M('reply_info')
		->where(array('infotype' => 'Message','token' => $this->token))
		->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Reply/index')
				)
			);
		}
	}
	
	public function vote($id){
		$setting = M('vote')
		->where(array('id' => $id,'token' => $this->token))
		->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Vote/index',['id'=>$id])
				)
			);
		}
	}
	
	/**
	 *@method wedding 微喜帖
	 */
	public function wedding($id){
		$setting = M('Wedding')
		->where(array('id' => $id,'token' => $this->token))
		->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['word'])),
					$setting['coverurl'],
					$this->U('Wap/Wedding/index',['id'=>$id])
				)
			);
		}
	}
	/**
	 * @method shopMall 商城回复
	 */
	public function shopMall(){
		$setting = M('reply_info')->where(array(
			'infotype' => 'Mall',
			'token' => $this->token
		))->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Product/index')
				)
			);
		}
	}
	
	/**
	 * @method impression 微印象回复
	 */
	public function impression(){
		$setting = M('reply_info')->where(array(
			'infotype' => 'Impression',
			'token' => $this->token
		))->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Impression/index')
				)
			);
		}
	}
	
	/**
	 * @method groupPurchase 团购回复
	 */
	public function groupPurchase(){
		$setting = M('reply_info')
		->where(array('infotype' => 'Groupon','token' => $this->token)) 
		->find();
		if($setting != null){
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Groupon/grouponIndex')
				)
			);
		}
	}
	
	/**
	 * @method panorama 全景回复
	 */
	public function panorama($id){
		$setting = M('reply_info')->where(array(
			'infotype' => 'panorama',
			'token' => $this->token
		))->find();
		if ($setting) {
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Panorama/index')
				)
			);
		}
	}
	
	/**
	 *@method member 会员卡回复
	 */
	public function member() {
		/*先判断用户有没有领取会员卡*/
		$card     = M('member_card_create')->where(array(
			'token' => $this->token,
			'wechat_id' => $this->fromUserName
		))->find();
		
		/*获取会员卡回复配置*/
		$setting = M('reply_info')->where(array(
			'infotype' => 'Membercard',
			'token' => $this->token
		))->find();
		$config = unserialize($setting['config']);
		if ($setting) {
			$config = unserialize($setting['config']);
			if ($card == false) {
				return $this->news(
					array(
						$setting['title'],
						strip_tags(htmlspecialchars_decode($setting['info'])),
						$setting['picurl'],
						$this->U('Wap/Card/get_card')
					)
				);
			}
			else {
				return $this->news(
					array(
						$config['member_title'],
						strip_tags(htmlspecialchars_decode($config['member_info'])),
						$config['member_picurl'],
						$this->U('Wap/Card/vip')
					)
				);
			}
		}
	}
	
	
	//回复首页
	function home() {
		$home = M('Home')->where(array(
			'token' => $this->token
		))->find();
		
		if ($home == false) {
			return $this->text('商家未做首页配置，请稍后再试！');
		}
		else {
			$imgurl = $home['picurl'];
			if ($home['apiurl'] == false) {
				$url = $this->U('/Wap/Index/index');
			}
			else {
				$url = $this->filterUrl($home['apiurl']);
			}
			
			return $this->news(
				array(
					$home['title'],
					$home['info'],
					$imgurl,
					$url
				)
			);
		}
	}
	
	public function textResponse($id){
		$info = M('text')->order('id desc')->find($id);
		//$this->toUserName;
		$info['text'] = htmlspecialchars_decode($info['text']);
		//处理WIFI关键词，获取上网验证
		if($info['keyword']=='WIFI'){
			if($this->token == 'omkpxq1397715993'){//山水WIFI链接处理
				$handle = new \Spark\Custom\ShanShui();
				$link = $handle->getWifiLink($this->fromUserName);
			}
			else{
				//一般WIFI认证链接构造
				$link = 'http://192.168.255.215?key='.$this->fromUserName;
			}
			$info['text'] = str_replace('{$LINK$}', $link, $info['text']);
		}
		return $this->text(
			str_replace('{wechat_id}', $this->fromUserName, $info['text'])
		);
	}

	public function newsResponse($id){
		$img   = M('Img');
		$news     = $img->field('id,text,pic,url,title,is_multi,more_news')->where(['id'=>$id])->find();
		if ($news['url'] != false) { //图文带有链接
			$news['url'] = $this->getFuncLink(htmlspecialchars_decode($news['url']));
		}
		else { //图文不带链接跳转到详细页
			$news['url'] = $this->U('Wap/Index/content',['id' => $news['id']]);
		}
		$return[] = array(
			$news['title'],
			$news['text'],
			$news['pic'],
			$news['url']
		);
		if($news['is_multi'] == 1 ){//多图文
			$more_news = unserialize($news['more_news']);
			if($more_news!=false && is_array($more_news)){
				foreach($more_news as $key => $value){
					if ($value['url'] != false) { //图文带有链接
						$value['url'] = $this->getFuncLink(urldecode($value['url']));
					}
					else { //图文不带链接跳转到详细页
						$value['url'] = $this->U('Wap/Index/content',['id' => $news['id'],'sid'=>$key]);
					}
					$return[] = array(
						$value['title'],
						'',
						$value['cover'],
						$value['url']
					);
				}
			}
			
		}
		return $this->news($return);
	}
	
	public function musicResponse($id){
		$info = M($data['module'])->order('id desc')->find($id);
		return array(
			array(
				$info['title'],
				$info['keyword'],
				$info['musicurl'],
				$info['hqmusicurl']
			),
			'music'
		);
	}
	
	public function voiceResponse($id){
		$info = M($data['module'])->order('id desc')->find($id);
		return array(
			array(
				$info['title'],
				$info['keyword'],
				$info['musicurl'],
				$info['hqmusicurl']
			),
			'music'
		);
	}
	
	public function videoResponse($key){
	
	}
	
	public function linkResponse($key){
	
	}
	
	/**
	 *@ method host 回复商家信息
	 */
	public function host($id) {
		$host = M('Host')->find($id);
		return $this->news(
			array(
				$host['name'],
				$host['info'],
				$host['ppicurl'],
				$this->U('Wap/Host/index',['hid'=>$id])
			)
		);
	}
	
	/**
	 *@ method host 回复产品信息
	 */
	public function product($like) {
		$infos = M('Product')->limit(9)->order('id desc')->where($like)->select();
		if ($infos) {
			$return = array();
			foreach ($infos as $info) {
				$return[] = array(
					$info['name'],
					strip_tags(htmlspecialchars_decode($info['intro'])),
					$info['logourl'],
					$this->U('Wap/Product/product',['id'=>$id])
				);
			}
			return $this->news($return);
		}
	}
	
	
	public function lottery($id) {
		$info = M('Lottery')->find($id);
		if ($info == false || $info['status'] == 3) {
			return $this->text('活动可能已经结束或者被删除了');
		}
		switch ($info['type']) {
			case 1:
				$model = 'Lottery';
				break;
			case 2:
				$model = 'Guajiang';
				break;
			case 3:
				$model = 'Coupon';
			case 4:
				$model = 'Zadan';
		}
		$id   = $info['id'];
		$type = $info['type'];
		if ($info['status'] == 1) {
			$picurl = $info['startpicurl'];
			$title  = $info['title'];
			$id     = $info['id'];
			$info   = $info['info'];
		}
		else {
			$picurl = $info['endpicurl'];
			$title  = $info['endtite'];
			$info   = $info['endinfo'];
		}
		$url = $this->U('Wap/' . $model . '/index',['type' => $type,'id' => $id,]);
		return $this->news(
			array(
				$title,
				$info,
				$picurl,
				$url
			)
		);
	}
	
	public function getWords($title, $num = 10) {
		vendor('Pscws.Pscws4', '', '.class.php');
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
	
	/**
	 * @链接过滤
	 */
	function filterUrl($url){
		return str_replace('{wechat_id}', $this->fromUserName, $url);
	}
	
	function getFuncLink($u) {
		if(strpos($u,'{wechat_id}')!==false){
			$url = str_replace('{wechat_id}', $this->fromUserName, $u);
		}
		else{
			if(strpos($u,'?')===false)$url=$u.'?wechat_id='.$this->fromUserName;
			else $url=$u.'&wechat_id='.$this->fromUserName;
		}
		return $url;
	}
	
	/**
	 * @method album 回复相册信息
	 */
	function album(){
		$setting = M('reply_info')->where(array(
			'infotype' => 'Photo',
			'token' => $this->token
		))->find();
		if ($setting) {
			return $this->news(
				array(
					$setting['title'],
					strip_tags(htmlspecialchars_decode($setting['info'])),
					$setting['picurl'],
					$this->U('Wap/Photo/index')
				)
			);
		}
		else{
			$photo           = M('Photo')->where(array(
				'token' => $this->token,
				'status' => 1
			))->find();
			$data['title']   = $photo['title'];
			$data['content'] = $photo['info'];
			$data['url']     = $this->U('Wap/Photo/index');
			$data['picurl']  = $photo['picurl'];
			return $this->news(
				array(
					$data['title'],
					$data['content'],
					$data['picurl'],
					$data['url']
				)
			);
		}
	}
	
	//回复商家地图
	function companyMap(){
		$map = new \Spark\Map\MapService();
		$map->token = $this->token;
		$map->apikey = C('baidu_map_api');
		return $this->news($map->staticCompanyMap());
	}
	
	public function help() {
		$data = M('Areply')->where(array(
			'token' => $this->token
		))->find();
		return $this->text(
			preg_replace("/(\015\012)|(\015)|(\012)/", "\n", $data['content'])
		);
	}
}
?>