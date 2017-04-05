<?php
namespace User\Controller;

class EduController extends UserController{
	public function school(){
		$branches=M('Edu_school')->where(array('token'=>session('token')))->order('taxis desc')->select();
		$this->assign('branches',$branches);
		$this->display();
	}
	
	public function school_edit(){
		$this->model=M('Edu_school');
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['token'] = session('token');
			if($id ==0){
				$id = $this->model->add($_POST);
				if($id){
					$this->success('添加成功！',U('Edu/school'));
				}
				else{
					$this->error('添加失败！',U('Edu/school'));
				}
			}
			else{
				$check = M('Edu_school')->where(['token'=>session('token'),'id'=>$id])->find();
				if($check ==null){
					$this->error('非法操作！',U('school'));
					exit;
				}
				if($this->model->create()){
					if($this->model->save()){
						$this->success('修改成功',U('Edu/school'));
					}
					else{
						$this->error('操作失败');
					}
				}
				else{
					$this->error($this->model->getError());
				}
			}
		}else{
			if($id != 0){
				$shop = M('Edu_school')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$shop);
			}
			$this->display();
		}
	}
	
	private function getSchools(){
		return M('Edu_school')->field('id,name')->where(array('token'=>session('token')))->order('taxis desc')->select();
	}
	
	public function school_delete(){
		$this->model=M('Edu_school');
		$where=array('token'=>$this->token,'id'=>intval($_GET['id']));
		$rt=$this->model->where($where)->delete();
		if($rt==true){
			$this->success('删除成功',U('Edu/school'));
		}else{
			$this->error('服务器繁忙,请稍后再试',U('Edu/school'));
		}
	}
	
	public function teach(){
		$model = D('EduTeachStu');
		$where = ['token'=>session('token'),'type'=>0];
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,10);
		$list = $model->where($where)->relation(true)->limit($page->firstRow.','.$page->listRows)->select();
		$schools = $this->getSchools();
		$this->assign('schools',$schools);
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function teach_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id ==0){
				$_POST['token'] = session('token');
				$_POST['type'] = 0;
				$id = M('edu_teach_stu')->add($_POST);
				if($id){
					$this->success('添加成功！',U('teach'));
				}
				else{
					$this->error('添加失败',U('teach'));
				}
			}
			else{
				$ret = M('edu_teach_stu')->save($_POST);
				if($ret){
					$this->success('保存成功！',U('teach'));
				}
				else{
					$this->error('保存失败！',U('teach'));
				}
			}
		}
		else{
			$schools = $this->getSchools();
			$this->assign('schools',$schools);
			if($id !=0){
				$set = M('edu_teach_stu')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function stu(){
		$model = D('EduTeachStu');
		$where = ['token'=>session('token'),'type'=>1];
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,10);
		$list = $model->where($where)->relation(true)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function stu_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id ==0){
				$_POST['token'] = session('token');
				$_POST['type'] = 1;
				$id = M('edu_teach_stu')->add($_POST);
				if($id){
					$this->success('添加成功！',U('stu'));
				}
				else{
					$this->error('添加失败',U('stu'));
				}
			}
			else{
				$ret = M('edu_teach_stu')->save($_POST);
				if($ret){
					$this->success('保存成功！',U('stu'));
				}
				else{
					$this->error('保存失败！',U('stu'));
				}
			}
		}
		else{
			$schools = $this->getSchools();
			$this->assign('schools',$schools);
			if($id !=0){
				$set = M('edu_teach_stu')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function st_delete(){
		$id = I('get.id',0,'intval');
		$ret = M('edu_teach_stu')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！');
		else $this->error('删除失败！');
	}
}
?>