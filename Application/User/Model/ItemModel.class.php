<?php
namespace User\Model;
use Think\Model;
class ItemModel extends \Think\Model\RelationModel {
	protected $tablePrefix = 'weixin_';
	//自动验证
	protected $_validate=array(
		array('title','require','商品名称必须！',1,'',3),
		array('cate_id','require','商品分类必须！',1,'',3), 
		array('img','require','商品图片必须！',1,'',3),
	);
	
	//发布商品
	public function publish(){
		$this->data['token'] = session('token');
		$this->data['add_time'] = time();
		$this->data['specs'] = json_encode($this->data['specs']);
		return $this->add();
	}
	
	//更新商品
	public function update(){
		$this->data['specs'] = json_encode($this->data['specs']);
		return $this->save();
	}
	
	protected $_link = array(
		'Cate' => array( 
			'mapping_type'  => self::BELONGS_TO, 
			'class_name'    => 'item_cate',
			'foreign_key'   => 'cate_id',
			'mapping_name'  => 'cate',
			'as_fields' => 'name:cate_name')
	);
}