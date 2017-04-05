<?php
namespace User\Model;
use Think\Model;
class LotteryModel extends Model{
	protected $_validate =array(
		
	);
	
	protected $_auto = array (
		array('token','gettoken',self::MODEL_INSERT,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
	);
	
	public function gettoken(){
		return session('token');
	}
}