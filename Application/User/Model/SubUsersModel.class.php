<?php
namespace User\Model;
use Think\Model\RelationModel;
class SubUsersModel extends RelationModel{
	protected $_link  = array(
		'link' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'users_role',
			'foreign_key'   => 'role_id',
			'mapping_name'  => 'role',
			'as_fields' => 'name:role_name',
		)
	);
}