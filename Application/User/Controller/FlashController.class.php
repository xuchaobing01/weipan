<?php
/**
 *首页幻灯片回复
**/
namespace User\Controller;
use Spark\Util\Page;

class FlashController extends UserController{
	public function index(){
		$db=D('Flash');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if($id){
			$where['id']= $id;
			$where['uid']=session('uid');
			$res=D('Flash')->where($where)->find();
			$this->assign('info',$res);
			$this->assign('title', '编辑幻灯片');
		}
		else{
			$this->assign('title', '新建幻灯片');
		}
		$this->display();
	}
	
	public function del(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');
		if(D('Flash')->where($where)->delete()){
			$this->success('操作成功',U('index'));
		}else{
			$this->error('操作失败',U('index'));
		}
	}
	
	public function insert(){
		$this->all_insert();
	}

	public function upsave(){
		$this->all_save();
	}

}
?>