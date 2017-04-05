<?php
namespace User\Model;
use Think\Model;
class KeywordModel extends Model{
	protected $_validate =array(
		
	);
	
	protected $_auto = array (
		
	);
	
	public function get($pid,$module){
		$this->model = M('Keyword');
		$data=['token'=>session('token'),'module'=>$module,'pid'=>$pid];
		$setting = $this->model->where($data)->find();
		if($setting == null){
			$id = $this->model->add($data);
			$data['id'] = $id;
			return $data;
		}
		return $setting;
	}
	
	public function set($pid,$module,$data){
		$db = M('Keyword');
		$ret = $db->where(array('pid'=>$pid,'module'=>$module,'token'=>session('token')))->save($data);
	}
}