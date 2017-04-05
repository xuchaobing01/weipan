<?php
namespace User\Model;
use Think\Model;
class Selfform_productModel extends Model{
	protected $_validate = array(
		array('name','require','名称不能为空',1),
	);
	protected $_auto = array (
		array('create_time','time',1,'function'),
		array('update_time','time',3,'function')
	);
}

?>