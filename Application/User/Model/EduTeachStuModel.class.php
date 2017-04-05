<?php
namespace User\Model;
use Think\Model\RelationModel;
class EduTeachStuModel extends RelationModel{
	protected $_link  = array(
		'Classify' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'EduSchool',
			'foreign_key'   => 'school_id',
			'mapping_name'  => 'school',
			'as_fields' => 'name:school_name',
		)
	);
}