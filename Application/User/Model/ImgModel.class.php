<?php
namespace User\Model;
use Think\Model;
class ImgModel extends Model{
	protected $_validate =array(
		array('title','require','标题不能为空',1)
	);
	
	protected $_auto = array (
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_BOTH,'function'),
		array('token','gettoken',self::MODEL_INSERT,'callback'),
		array('click','0'),
	);
	
	function gettoken(){
		return session('token');
	}
	
	protected function _after_insert($data, $options){
		M('Keyword')->add(['pid'=>$data['id'],'token'=>session('token'),'module'=>'Img','keyword'=>$data['keyword']]);
	}
	
	protected function _after_update($data, $options){
		M('Keyword')->where(['pid'=>$data['id'],'token'=>session('token'),'module'=>'Img'])->save(['keyword'=>$data['keyword']]);
	}
}