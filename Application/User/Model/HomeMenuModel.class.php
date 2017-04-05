<?php
namespace User\Model;
use Think\Model;
class HomeMenuModel extends Model{
	protected $fields = array(
		'id', 'pid', 'name', 'token', 'ordernum','picurl','url','status','_pk' => 'id', '_autoinc' => true
	);
	
	protected $_validate =array(
		array('name','require','²Ëµ¥Ãû³Æ±ØÐë£¡')
	);
	
	protected $_auto = array (
		array('token','get_token',1,'callback')
	);
	
	function get_token(){
		return session('token');
	}
	

}