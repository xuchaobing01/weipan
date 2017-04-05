<?php
namespace User\Controller;
class AlipaycfgController extends UserController{
	public $alipay_config_db;
	public function _initialize() {
		parent::_initialize();
		$this->alipay_config_db=M('Alipay_config');
		if (!$this->token){
			exit();
		}
	}
	public function index(){
		$config = $this->alipay_config_db->where(array('token'=>$this->token))->find();
		if(IS_POST){
			$row['pid']=I('pid');
			$row['key']=I('key');
			$row['name']=I('name');
			$row['token']=I('token');
			$row['open']=I('open');
			if ($config){
				$where=array('token'=>$this->token);
				$this->alipay_config_db->where($where)->save($row);
			}else {
				$this->alipay_config_db->add($row);
			}
			$this->success('设置成功',U('index',$where));
		}else{
			$this->assign('config',$config);
			$this->display();
		}
	}
}
?>