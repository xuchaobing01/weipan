<?php
namespace User\Model;
use Think\Model;
class User_groupModel extends Model{
	protected $_validate=array(
		array('name','require','用户组名称必须填写！',1,'',3),
		array('text_num','require','自定义文本数必须填写！',1,'',3),
		array('img_num','require','自定义图文数必须填写！',1,'',3),
		array('voice_num','require','自定义语音数必须填写！',1,'',3),
		array('request_limit','require','用户请求数数必须填写！',1,'',3),
		array('activity_limit','require','活动创建数称必须填写！',1,'',3),
		array('price','require','价格必须填写！',1,'',3),
		array('name','','用户组名称已经存在！',1,'unique',3), 
	);
	
	// 获取单个用户信息
	public function getGroup($where = '',$field = '*') {
		return $this->field($field)->where($where)->find();
	}
	
	//所有的用户组
	public function getAllGroup($where = '',$field = '*') {
		return $this->field($field)->where($where)->select();
	}
}
?>