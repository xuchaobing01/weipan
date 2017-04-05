<?php
namespace Admin\Controller;
use User\Model;
use Spark\Util\Page;
class LinksController extends BaseController{
	public function index(){
		$db=D('Links');
		F('links',null);
		$links=M('Links')->select();
		F('links',$links);
		$count=$db->count();
		$page=new Page($count,25);
		$info=$db->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('info',$info);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function edit(){
		$where['id']= I('get.id',0,'intval');
		$db=D('Links');
		$info=$db->where($where)->find();
		$this->assign('info',$info);
		$this->display('add');
	}
	
	public function add(){
		$this->display();
	}
	
	public function insert(){
		$this->all_insert('Links');
	}
	
	public function upsave(){
		$this->all_save('Links');
	}
	
	public function del(){
		$id = I('get.id',0,'intval');
		if($db->delete($id)){
			$this->success('操作成功',U('index'));
		}else{
			$this->error('操作失败',U('index'));
		}
	}
	
}