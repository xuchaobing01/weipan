<?php
namespace User\Model;
use Think\Model;
class DiningCatModel extends Model{
	protected $_validate = array(
		array('name','require','名称不能为空',1)
	);
	protected $_auto = array (
	array('token','gettoken',1,'callback'),
		array('time','time',1,'function')
	);
	function gettoken(){
		return session('token');
	}
}
?>