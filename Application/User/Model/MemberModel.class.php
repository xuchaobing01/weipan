<?php
/**
 *@class MemberModel 会员用户模型类
 */
namespace User\Model;
use Think\Model\RelationModel;
class MemberInfoModel extends RelationModel{	
	protected $_link  = array(
		'Card' => array(
			'mapping_type'  => self::HAS_ONE,
			'class_name'    => 'Member_card_create',
			'foreign_key'   => 'wechat_id',
			'mapping_name'  => 'card',
			'mapping_fields' => 'number,card_id',
		)
	);
}
?>