<?php
/**
 *@class MallAnalysis 商城数据分析统计
 */
namespace User\Controller;
class MallAnalysisController extends UserController{
	public function page(){
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
		$data = $this->_init_data($date_start,$date_end);
		$where = ['token'=>session('token'),'module'=>'mall'];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end];
		$list = M('page_statistics')->field('y,m,d,pv,uv,level,module')->where($where)->order('id desc')->select();
		
		foreach($list as $row){
			$data['PV'][$row['m'].$row['d']] = $row['pv'];
			$data['UV'][$row['m'].$row['d']] = $row['uv'];
		}
		
		$xAxis = array();
		foreach($data['title'] as $item){
			$xAxis[] = $item;
		}
		$series = array();
		foreach($data['PV'] as $item){
			$series['PV'][] = $item;
		}
		foreach($data['UV'] as $item){
			$series['UV'][] = $item;
		}
		$this->assign('range',$daterange);
		$this->assign('xAxis',$xAxis);
		$this->assign('series',$series);
		$this->display();
	}
	
	public function time_range(){
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
		$where = ['token'=>session('token'),'module'=>'mall'];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end];
		$list = M('page_statistics')->field('y,m,d,count,detail,level,module')->where($where)->order('id desc')->select();
		$data = [['name'=>'0-2','value'=>0],['name'=>'3-5','value'=>0],['name'=>'6-8','value'=>0],['name'=>'9-11','value'=>0],['name'=>'12-14','value'=>0],['name'=>'15-17','value'=>0],['name'=>'18-20','value'=>0],['name'=>'21-23','value'=>0]];
		
		foreach($list as $row){
			$row['detail'] = unserialize($row['detail']);
			$data[0]['value'] += ($row['detail'][0]+$row['detail'][1]+$row['detail'][2]);
			$data[1]['value'] += ($row['detail'][3]+$row['detail'][4]+$row['detail'][5]);
			$data[2]['value'] += ($row['detail'][6]+$row['detail'][7]+$row['detail'][8]);
			$data[3]['value'] += ($row['detail'][9]+$row['detail'][10]+$row['detail'][11]);
			$data[4]['value'] += ($row['detail'][12]+$row['detail'][13]+$row['detail'][14]);
			$data[5]['value'] += ($row['detail'][15]+$row['detail'][16]+$row['detail'][17]);
			$data[6]['value'] += ($row['detail'][18]+$row['detail'][19]+$row['detail'][20]);
			$data[7]['value'] += ($row['detail'][21]+$row['detail'][22]+$row['detail'][23]);
		}
		$title = ['0-2','3-5','6-8','9-11','12-14','15-17','18-20','21-23'];
		$this->assign('range',$daterange);
		$this->assign('data',$data);
		$this->assign('title',$title);
		$this->display();
	}
	
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['title'][$key] = date('m-d',$tmp);
			$data['PV'][$key] = 0;
			$data['UV'][$key] = 0;
		}
		return $data;
	}
	
	private function days_in_month($month, $year) { 
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
	} 
}
?>