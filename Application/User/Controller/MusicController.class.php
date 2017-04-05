<?php
namespace User\Controller;
class MusicController extends UserController{
	public function index(){
		$list = M('music')->where(['token'=>session('token')])->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$data['title'] = $_POST['title'];
			$data['src'] = $_POST['src'];
			if($id){
				$data['id'] = $id;
				$ret = M('music')->save($data);
				if($ret != false){
					$this->success('操作成功');					
				}
				else{
					$this->error('编辑失败！');
				}
			}
			else{
				$data['token'] = session('token');
				$data['create_time'] = time();
				$ret = M('music')->add($data);
				if($ret != false){
					$this->success('操作成功');					
				}
				else{
					$this->error('添加失败！');
				}
			}
		}
		else{
			if($id){
				$set = M('music')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();	
		}
	}
	
	public function delete(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('操作失败！');
		$ret = M('music')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！');
		else $this->error('操作失败！');
	}
}
?>