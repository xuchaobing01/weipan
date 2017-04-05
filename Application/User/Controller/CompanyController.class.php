<?php
namespace User\Controller;
class CompanyController extends UserController{
	public $token;
	public $isBranch;
	public $company_model;
	public function _initialize() {
		parent::_initialize();
		$this->token = session('token');
		$this->assign('token',$this->token);
		$this->company_model=M('Company');
	}
	
	public function edit(){
		$where=array('token'=>$this->token);
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['token'] = $this->token;
			if($id ==0){
				$this->insertEx('Company',U('Company/index'));
			}
			else{
				$check = M('Company')->where(['token'=>$this->token,'id'=>$id])->find();
				if($check ==null){
					$this->error('非法操作！',U('index'));
					exit;
				}
				if($this->company_model->create()){
					if($this->company_model->save()){
						$this->success('修改成功',U('Company/index'));
					}
					else{
						$this->error('操作失败');
					}
				}
				else{
					$this->error($this->company_model->getError());
				}
			}
		}else{
			if($id != 0){
				$shop = M('Company')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$shop);
			}
			$this->display();
		}
	}
	
	public function index(){
		$branches=$this->company_model->where(array('token'=>$this->token))->order('taxis ASC')->select();
		$this->assign('branches',$branches);
		$this->display();
	}
	
	public function delete(){
		$where=array('token'=>$this->token,'id'=>intval($_GET['id']));
		$rt=$this->company_model->where($where)->delete();
		if($rt==true){
			$this->success('删除成功',U('Company/index'));
		}else{
			$this->error('服务器繁忙,请稍后再试',U('Company/index'));
		}
	}
}
?>