<?php
namespace User\Model;
use Think\Model\RelationModel;
class ArticleModel extends RelationModel{
	protected $_validate =array(
		array('title','require','标题不能为空',1),
	);
	
	protected $_auto = array (
		array('uid','getuser',self::MODEL_INSERT,'callback'),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_BOTH,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback')
	);
	
	function getuser(){
		return session('uid');
	}
	
	function gettoken(){
		return session('token');
	}
	
	protected $_link  = array(
		'Classify' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'Classify',
			'foreign_key'   => 'category',
			'mapping_name'  => 'cat',
			'as_fields' => 'name:catname',
		)
	);
}