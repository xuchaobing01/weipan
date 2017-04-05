<?php
/**
 *首页幻灯片回复
**/
namespace User\Controller;
use Spark\Util\Page;

class AnimationController extends UserController{
	public function index(){
		$db=D('Animation');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count, 25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	
	public function edit(){

        $where['token']=session('token');
        $res=D('Animation')->where($where)->find();
        $this->assign('info',$res);
        $this->assign('title', '开机动画');

		$this->display("edit");
	}
	
	public function del(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');

		if(D('Animation')->where($where)->delete()){
			$this->success('操作成功',U('index'));
		}else{
			$this->error('操作失败',U('index'));
		}
	}
	
	public function insert(){
        $_POST['token']=session('token');
		if(!M('Animation')->where(array('token'=>session('token')))->find()){
            M('Animation')->add($_POST);
        }else{
            $this->upsave();
        }
        $this->edit();
	}

	public function upsave(){
        $_POST['token']=session('token');
        M('Animation')->where(array('token'=>session('token')))->save($_POST);
        $this->edit();
	}

}
?>