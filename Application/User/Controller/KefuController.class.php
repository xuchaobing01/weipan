<?php
namespace User\Controller;
class KefuController extends UserController{
	protected function _initialize(){
		$this->model = M('Kefu');
		parent::_initialize();
	}
	
	public function index(){
		
	}
	
	public function set(){
		if(IS_POST){
			$ret = $this->model->save($_POST);
			if($ret){
				$this->success('保存成功！');
			}
			else{
				$this->error('保存失败！');
			}
		}
		else{
			$set = $this->model->where(['token'=>session('token')])->find();
			if($set == null){
				$set = ['token'=>session('token'),'kefu_name'=>'客服小微','status'=>0];
				$id = $this->model->add($set);
				$set['id'] = $id;
			}
			$this->assign('set',$set);
			$this->display();
		}
	}
	
	public function answers(){
		$lists = M('Kefu_answer')->where(['token'=>session('token')])->select();
		$this->assign('lists',$lists);
		$this->display();
	}
	
	/**
	 *@method add 添加话术
	 */
	public function add(){
		$data['answer'] = I('post.answer','','trim');
		$data['question'] = I('post.question','','trim');
		$data['token'] = session('token');
		$data['time'] = time();
		$model = M('Kefu_answer');
		$id = $model->add($data);
		if($id){
			$this->ajaxReturn(['code'=>0,'message'=>'添加成功！']);
		}
		else{
			$this->ajaxReturn(['code'=>400,'message'=>'添加失败！']);
		}
	}
	
	public function edit(){
		$data['id'] = I('post.id','','intval');
		$data['answer'] = I('post.answer','','trim');
		$data['question'] = I('post.question','','trim');
		$check = M('Kefu_answer')->where(['token'=>session('token'),'id'=>$data['id']])->find();
		if($check!==null){
			M('Kefu_answer')->save($data);
			$this->ajaxReturn(['code'=>0,'message'=>'编辑成功！']);
		}
		else{
			$this->ajaxReturn(['code'=>500,'message'=>'非法操作！']);
		}
	}
	
	public function del(){
		$data['id']=I('id',0,'intval');
		$data['token']=session('token');
		$re=M('Kefu_answer')->where($data)->find();
		if($re==false){
			$this->error('非法操作');
		}
		else{
			$del=M('Kefu_answer')->where($data)->delete();
			if($del==false){
				$this->error('服务器繁忙',U('answers'));
			}else{
				$this->success('删除成功',U('answers'));
			}
		}
	}
}
?>