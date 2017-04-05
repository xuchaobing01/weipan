<?php
namespace User\Model;
use Think\Model;
class PlateItemModel extends \Think\Model\RelationModel {
	protected $tableName = 'mall_plate_item';
	protected $_link = array(
		'Item' => array( 
			'mapping_type'  => self::BELONGS_TO, 
			'class_name'    => 'Item',
			'foreign_key'   => 'item_id',
			'mapping_name'  => 'cate',
			'as_fields' => 'title,price,oprice,img')
	);
}