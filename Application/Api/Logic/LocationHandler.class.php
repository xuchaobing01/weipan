<?php
/**
 * @class LocationHandler 处理事件消息
 * @author: yanqizheng
 * @date: 2014/01/25
 */
namespace Api\Logic;
class LocationHandler extends MessageHandler {
	public function handle() {
		
		M('wechat_user')->where(array('open_id' => $this->fromUserName))->save(array('latitude'=>$this->location_X,'longitude'=>$this->location_Y));
		
		$this->saveRequest($this->location_X . ',' .$this->location_Y , 'location');
		$last_request   =  M('User_request')->where(array(
			'token' => $this->token,
			'msgtype' => 'text',
			'uid' => $this->fromUserName
		))->find();
		if(!$this->keywordExpired($last_request)) {
			return $this->LBSHandle($last_request['keyword'], $this->location_X, $this->location_Y);
		}
		else {
			return $this->nearestCompany($this->location_X, $this->location_Y);
		}
	}
	
	/**
	 * @method keywordExpired 判断上一次关键请求是否超时
	 * @return boolean 超时返回true,否则返回false
	 */
	function keywordExpired($request) {
		if($request && (time()-intval($request["time"])>5*60)){
			return true;
		}
		return false;
	}
	
	/**
	 * @method LBSHandle 位置处理,只提供公司的LBS服务
	 */
	function LBSHandle($keyword, $x, $y) {
		/*if (preg_match("/附近|最近|周边/", $keyword)) {
			return $this->placeSearch(preg_replace("/附近|最近|周边/", '', $keyword));
		}*/
		if(preg_match("/开车去|坐公交|步行去/",$keyword)){ 
			return $this->gotoCompany($keyword);
		}
		else {
			return $this->nearestCompany($x, $y);
		}
	}
	
	//导航到公司
	public function gotoCompany($keyword) {
		import("Home.Action.MapAction");
		$mapAction = new MapAction();
		$mapAction->token=$this->token;
		if($keyword)
		$companyId = str_replace('/开车去|坐公交|步行去/', '', $keyword);
		if (!$companyId) {
			$companyId = 1;
		}
		if ((strpos($keyword, '步行去') !== FALSE)) {
			return $this->news($mapAction->walk($x, $y, $companyid));
		}
		if ((strpos($keyword, '开车去') !== FALSE)) {
			return $this->news($mapAction->drive($x, $y, $companyid));
		}
		if ((strpos($keyword, '坐公交') !== FALSE)) {
			return $this->news($mapAction->bus($x, $y, $companyid));
		}
	}
	
	public function nearestCompany($x, $y){
		import("Home.Action.MapAction");
		$mapAction = new MapAction();
		$mapAction->token=$this->token;
		
		return $this->news($mapAction->nearest($x, $y));
	}
	
	//位置搜索
	public function placeSearch($key){
		$radius  = 2000;
		$result     = file_get_contents($_SERVER["HTTP_HOST"] . '/asset/map.php?keyword=' . urlencode($key) . '&x=' . $x . '&y=' . $y);
		$result   = json_decode($result);
		$map     = array();
		foreach ($result as $key => $vo) {
			$map[] = array(
				$vo->title,
				$key,
				rtrim(C('site_url'), '/') . '/tpl/static/images/home.jpg',
				$vo->url
			);
		}
		return $this->news($map);
	}
}
?>