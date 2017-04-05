<?php
namespace User\Controller;
class RequerydataController extends UserController{
	public function index(){
		list($date_start,$date_end) = $this->_date_range();
		$data = $this->_init_data($date_start,$date_end);
		$where = ['token'=>session('token')];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end + 86400];
		
		$list = M('Requestdata')->where($where)->order('id desc')->select();
		$this->assign('list',$list);
		foreach($list as $item){
			$key = $item['month'].$item['day'];
			$data['text'][$key] = intval($item['textnum']);
			$data['img'][$key] = intval($item['imgnum']);
			$data['menu'][$key] = intval($item['menunum']);
			$data['follow'][$key] = intval($item['follownum']);
			$data['unfollow'][$key] = intval($item['unfollownum']);
			$data['truefollow'][$key] = $data['follow'][$key] - $data['unfollow'][$key];
		}
		$texNum = json_encode_array($data['text']);
		$imgNum = json_encode_array($data['img']);
		$menuNum = json_encode_array($data['menu']);
		
		$followNum = json_encode_array($data['follow']);
		$unfollowNum = json_encode_array($data['unfollow']);
		$truefollowNum = json_encode_array($data['truefollow']);
		
		$this->assign('textNum',$texNum);
		$this->assign('imgNum',$imgNum);
		$this->assign('menuNum',$menuNum);
		$this->assign('followNum',$followNum);
		$this->assign('unfollowNum',$unfollowNum);
		$this->assign('truefollowNum',$truefollowNum);
		$this->assign('name',json_encode_array($data['name']));
		$this->display();
	}
	
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['name'][$key] = date('m-d',$tmp);
			$data['text'][$key] = 0;
			$data['img'][$key] = 0;
			$data['menu'][$key] = 0;
			$data['follow'][$key] = 0;
			$data['unfollow'][$key] = 0;
			$data['truefollow'][$key] = 0;
		}
		return $data;
	}
	
	private function _date_range(){
		$daterange = I('daterange');
		if(empty($daterange)) $daterange = 7;
		if(strpos($daterange,'~') > 0){
			$parse = explode('~',$daterange);
			$date_start = strtotime($parse[0]);
			$date_end = strtotime($parse[1]);
		}
		else{
			$today = date('Y-m-d');
			$date_end = strtotime($today) - 86400; //从昨天算起
			$date_start = $date_end - (intval($daterange)-1)*86400;
		}
		$this->assign('range',$daterange);
		return [$date_start,$date_end];
	}
	
	public function export(){
		Vendor('PHPExcel.PHPExcel');
		$data = array();
		$title = ['日期','文本消息数','图片消息数','语音消息数','菜单点击数','关注人数','取消关注数','总消息数'];
		$data[] = $title;
		list($date_start,$date_end) = $this->_date_range();
		$where = ['token'=>session('token')];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end];
		$list = M('Requestdata')->where($where)->field('textnum,imgnum,voicenum,menunum,follownum,unfollownum,time')->order('id desc')->select();
		foreach($list as $item){
			$row = array();
			$row[] = date('Y-m-d',$item['time']);
			$row[] = $item['textnum'];
			$row[] = $item['imgnum'];
			$row[] = $item['voicenum'];
			$row[] = $item['menunum'];
			$row[] = $item['follownum'];
			$row[] = $item['unfollownum'];
			$row[] = $item['textnum']+$item['imgnum']+$item['videonum']+$item['voicenum'];
			$data[] = $row;
		}
		createExcel('',$data);
	}
}
?>