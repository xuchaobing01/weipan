<?php
/**
 *文本回复
**/
namespace User\Controller;
use Spark\Util\Page;
class TextController extends UserController{
	public function index(){
		$db=D('Text');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	public function edit(){
		if(IS_POST){
			$id=I('id',0,'intval');
			$_POST['text']=htmlspecialchars($_POST['text']);
			if($id){
				$this->all_save();
			}
			else{
				$this->all_insert();
			}
		}
		else{
			$where['id']=I('id',0,'intval');
			if($where['id']){
				$where['uid']=session('uid');
				$where['token']=session('token');
				$res=D('Text')->where($where)->find();
				$this->assign('info',$res);
			}
			$this->display();
		}
	}
	
	public function del(){
		$where['id']=I('id',0,'intval');
		$where['uid']=session('uid');
		if(D('Text')->where($where)->delete()){
			M('Keyword')->where(array('pid'=>I('id',0,'intval'),'token'=>session('token'),'module'=>'Text'))->delete();
			$this->success('操作成功',U('index'));
		}
		else{
			$this->error('操作失败',U('index'));
		}
	}
}
?>