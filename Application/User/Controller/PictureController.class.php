<?php
namespace User\Controller;
use Spark\Util\Page;
class PictureController extends UserController{
	public function index(){
        $per=50;
        $db=M('upload_attachment');
        $where=array('uid'=>session('uid'));
     
        $count=$db->where($where)->count();
        $page=new Page($count,20);
		$imgs=$db->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('imgs', $imgs);
		$this->display();
	}
	
	public function upload(){
		
	}
	
	public function delete(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');
		if(D('Article')->where($where)->delete()){
			$this->success('操作成功',U('index?cate='.$_GET['cate']));
		}
		else{
			$this->error('操作失败',U('index'.$_GET['cate']));
		}
	}
}
?>