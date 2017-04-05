<?php
namespace User\Model;
use Think\Model;
class ReplyInfoModel extends Model{
	protected $_validate = array(
		array('title','require','标题不能为空',1)
	);
	
	protected $_auto = array (
		array('token','gettoken',1,'callback')
	);
	
	function gettoken(){
		return session('token');
	}
	
	/**
	 * @method get 获取模块回复设置
	 */
	public function get($type){
		$this->model = M('Reply_info');
		$data = ['token'=>session(token),'infotype'=>$type];
		$setting = $this->model->where($data)->find();
		if($setting == null){
			$id = $this->model->add($data);
			return M('Reply_info')->find($id);
		}
		return $setting;
	}
	
	public function set($type,$data){
		M('Reply_info')->where(array('infotype'=>$type,'token'=>session('token')))->save($data);
	}
}

?>