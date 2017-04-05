<?php
/**
 *分类管理
**/
namespace User\Controller;
use Spark\Util\Page;
class ClassifyController extends UserController{
	public function index(){
		$db=D('Classify');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('sorts desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		
		$this->display();
	}
	
	public function edit(){
		if(IS_POST){
			$db = D('Classify');
			if ($db->create() === false) {
				$this->error($db->getError());
			}
			if(empty($db->id)){
				$db->token = session('token');
				$db->create_time = time();
				$id = $db->add();
				if($id){
					$this->success('添加成功！',U('Classify/edit',['id'=>$id]));
				}
			}
			else{
				$db->save();
				$this->success('编辑成功！');
			}
		}
		else{
			$id=I('get.id',0,'intval');
			if($id){
				$info=M('Classify')->find($id);
				$this->assign('info',$info);
				$this->assign('title','编辑分类');
			}
			else{
				$this->assign('title','新建分类');
			}
			$cates = M('classify')->where(array('token'=>session('token'),'id'=>['neq',$id]))->order('sorts desc')->field('id,name')->select();
			$this->assign('cates',$cates);
			$this->display();
		}
	}
	
	public function del(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');
		if(D('Classify')->where($where)->delete()){
			$this->success('操作成功',U('index'));
		}else{
			$this->error('操作失败',U('index'));
		}
	}
}
?>