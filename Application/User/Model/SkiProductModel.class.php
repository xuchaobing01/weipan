<?php
namespace User\Model;
use Think\Model\RelationModel;
class SkiProductModel extends RelationModel{
    protected $_validate = array(
		array('name','require','名称不能为空',1),
		array('price','require','价格不能为空',1)
    );
	
    protected $_auto = array (
		array('token','gettoken',1,'callback'),
        array('time','time',1,'function'),
        array('storeid','1')
    );
	
	protected $_link  = array(
		'Cate' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'ski_cate',
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