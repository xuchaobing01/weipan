<?php
namespace User\Model;
use Think\Model;
class VoiceresponseModel extends Model{
	protected $_validate =array(
		array('title','require','标题不能为空',1),
		array('keyword','require','关键词不能为空',1),
		array('musicurl','require','音乐连接不能为空',1),
		array('musicurl','url','音乐连接格式不正确',1),
		array('hqmusicurl','require','高品质音乐连接不能为空',1),
		array('hqmusicurl','url','高品质音乐连接格式不正确',1),
	);
	
	protected $_auto = array (
		array('uid','getuser',self::MODEL_INSERT,'callback'),
		array('uname','getname',self::MODEL_INSERT,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('uptatetime','time',self::MODEL_BOTH,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback'),
		array('click','0'),
	);
	
	public function getuser(){
		return session('uid');
	}
	
	public function getname(){
		return session('uname');
	}

	function gettoken(){
		return session('token');
	}
	
	protected function _after_insert($data, $options){
		M('Wxuser')->field('voice_num')->where("token='".$this->gettoken()."'")->setInc('voice_num');
	}
}