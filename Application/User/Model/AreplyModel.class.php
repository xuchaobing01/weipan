<?php
namespace User\Model;
use Think\Model;
class AreplyModel extends Model{
	protected $_validate =array(
		array('keyword','require','关键字不能为空！'), 
	);
	
	protected $_auto = array (
		array('uid','getuser',self::MODEL_INSERT,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback'),
		array('updatetime','time',self::MODEL_BOTH,'function'),
	);
	
	function getuser(){
		return session('uid');
	}
	
	function getname(){
		return session('uname');
	}
	
	function gettoken(){
		return session('token');
	}
}