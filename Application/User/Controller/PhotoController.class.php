<?php
namespace User\Controller;
use Spark\Util\Page;
class PhotoController extends UserController{
	public function index(){
		//相册列表
		$data=M('Photo');
		$count      = $data->where(array('token'=>session('token')))->count();
		$Page       = new Page($count,12);
		$show       = $Page->show();
		$list = $data->where(array('token'=>session('token')))->order('sort asc, id desc ')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('photo',$list);
		$this->display();
	}
	
	public function edit(){
		$data=D('Photo');
		if(IS_POST){
			$_POST['token'] = session('token');
			$id = I('post.id',0,'intval');
			if($id){
				$this->all_save('Photo');
			}
			else{
				$this->all_insert('Photo','index');
			}
		}
		else{
			$id = I('get.id',0,'intval');
			if($id){
				$photo=$data->where(array('token'=>session('token'),'id'=>$id))->find();
				$this->assign('photo',$photo);
			}
			$this->display();
		}
	}
	
	public function list_edit(){
		$check=M('Photo_list')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>I('post.id')))->find();
		if($check==false){
			$this->error('照片不存在');
		}
		if(IS_POST){
			$this->all_save('Photo_list','list_add?id='.$check['pid']);		
		}
		else{
			$this->error('非法操作');
		}
	}
	
	public function list_del(){
		$check=M('Photo_list')->field('id,pid')->where(array('token'=>$_SESSION['token'],'id'=>I('get.id')))->find();
		if($check==false){$this->error('服务器繁忙');}
		if(empty($_POST['edit'])){
			if(M('Photo_list')->where(array('id'=>$check['id']))->delete()){
				M('Photo')->where(array('id'=>$check['pid']))->setDec('num');
				$this->success('操作成功');
			}
			else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	}
	
	public function list_add(){
		$checkdata=M('Photo')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
		if($checkdata==false){
			$this->error('相册不存在');
		}
		if(IS_POST){
			$_POST['token'] = session('token');
			M('Photo')->where(array('token'=>session('token'),'id'=>I('post.pid')))->setInc('num');
			$this->insertEx('Photo_list',U('list_add',array('id'=>I('get.id'))));		
		}
		else{
			$data=M('Photo_list');
			$count      = $data->where(array('token'=>$_SESSION['token'],'pid'=>I('get.pid')))->count();
			
			$list = $data->where(array('token'=>$_SESSION['token'],'pid'=>I('get.id')))->order('sort desc')->select();	
		
			$this->assign('photo',$list);
			$this->display();
		}
	}
	
	public function add(){
        $_POST['token'] = $_SESSION['token'];
		if(IS_POST){
			$this->all_insert('Photo','index');
		}
		else{
			$this->display();
		}
	}
	
	public function del(){
		$check=M('Photo')->field('id')->where(array('token'=>$_SESSION['token'],'id'=>I('get.id')))->find();
		if($check==false){
			$this->error('服务器繁忙');
		}
		if(empty($_POST['edit'])){
			if(M('Photo')->where(array('id'=>$check['id']))->delete()){
				M('Photo_list')->where(array('pid'=>$check['id']))->delete();
				$this->success('操作成功');
			}
			else{
				$this->error('服务器繁忙,请稍后再试');
			}
		}
	}
	
	/**
	 * @相册回复配置
	 */
	public function replySet(){
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$id = I('post.id','','intval');
			D('ReplyInfo')->set('Photo',$data);
			D('Keyword')->set($id,'Photo',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array('token'=>$this->token);
			$setting = D('ReplyInfo')->get('Photo');
			$keyword = D('Keyword')->get($setting['id'],'Photo');
			unset($setting['config']);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display('replyset');
		}
	}
}
?>