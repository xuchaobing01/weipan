<?php
/**
 *@class AnalysisController 页面访问量统计
 */
namespace Wf\Controller;
class AnalysisController extends \Think\Controller{
	public function index(){
		echo IS_CLI;
	}
	
	/**
	 * @method mall_analysis 商城页面访问PV统计
	 */
	public function mall_analysis(){
		$dir = C('PAGE_LOG_DIR');
		if(!isset($_GET['date'])){
			$yestoday = date('Y-m-d',time() - 24*3600);
			$time = strtotime($yestoday);
		}
		else $time = strtotime($_GET['date']);
		$log_file_name = $dir.'/'.date('Y-m-d',$time).'.log';
		if(is_file($log_file_name)){
			G('begin');
			$file = fopen($log_file_name,'r');
			$data = array();
			$count = 0;
			$map = array();
			while(!feof($file)){
				$line = fgets($file);
				$log = json_decode($line);
				$count ++;
				if(!$log->token) continue;
				if(!isset($data[$log->token])) {
					$data[$log->token] = array();
					$data[$log->token]['map'] = array();
					$data[$log->token]['detail'] = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];//记录每个小时的访问人数
					$data[$log->token]['pv'] = 0;//总访问量
					$data[$log->token]['uv'] = 0;//访问人数
				}
				$data[$log->token]['pv'] ++;
				if(!isset($data[$log->token]['map'][$log->wechat_id])){
					$data[$log->token]['map'][$log->wechat_id] = true;
					$data[$log->token]['uv'] ++;
					$h = date('G',strtotime($log->timestamp));
					$data[$log->token]['detail'][$h] ++;
				}
			}
			$this->_save_statistics($time,$data,'mall');
			G('end');
			fclose($file);
			echo json_encode(['status'=>0,'total_count'=>$count,'time used'=>G('begin','end').'s','memory used'=>G('begin','end','m').'kb','message'=>'complete']);
		}
		else {
			echo json_encode(['status'=>500,'message'=>'log file not found!']);
		}
	}
	
	private function _save_statistics($time,$data,$module){
		$y = date('Y',$time);
		$m = date('m',$time);
		$d = date('d',$time);
		$model = M('page_statistics');
		foreach($data as $key => $item){
			$row = array();
			$row['token'] = $key;
			$row['module'] = $module;
			$row['y'] = $y;
			$row['m'] = $m;
			$row['d'] = $d;
			$row['time'] = $time;
			$row['count'] = $item['pv'];
			$row['pv'] = $item['pv'];
			$row['uv'] = $item['uv'];
			$row['detail'] = serialize($item['detail']);
			$model->add($row);
		}
	}
	
	public function test(){
		$model = M('page_statistics');
		$rows = $model->field('id,y,m,d')->select();
		foreach($rows as $row){
			$model->where(['id'=>$row['id']])->setField('time',strtotime($row['y'].'-'.$row['m'].'-'.$row['d']));
		}
		echo count($rows);
	}
	
	public function make_up(){
	
	}
}
?>