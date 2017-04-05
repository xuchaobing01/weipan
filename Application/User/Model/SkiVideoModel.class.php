<?php
namespace User\Model;
use Think\Model\RelationModel;
class SkiVideoModel extends RelationModel{	
	protected $_link  = array(
		'Cate' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'ski_video_cate',
			'foreign_key'   => 'cate_id',
			'mapping_name'  => 'cate',
			'as_fields' => 'cate_name:catename',
		)
	);
	
    function gettoken(){
		return session('token');
	}
	function getTime(){
		$date=$_POST['enddate'];
		if ($date){
			$dates=explode('-',$date);
			$time=mktime(23,59,59,$dates[1],$dates[2],$dates[0]);
		}else {
			$time=0;
		}
		return $time;
	}
}
?>