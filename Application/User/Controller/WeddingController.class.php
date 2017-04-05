<?php
namespace User\Controller;
use \Spark\Util\Page;
class WeddingController extends UserController{
	//喜帖配置
	
	public function index(){
		$Wedding=M('Wedding');
		$where['token']=session('token');
		$count=$Wedding->where($where)->count();
		$page=new Page($count,25);
		$list=$Wedding->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign('page',$page->show());
		$this->assign('wedding',$list);
		$this->display();
	}
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['time']=strtotime(I('post.time'));
			if(empty($id)){
				$_POST['token'] = session('token');
			}
			$ret = $this->auto_save('wedding',$_POST);
			if($ret['errcode']==0){
				$this->save_keyword($ret['id'],$_POST['keyword'],'Wedding');
				$this->success('操作成功！');
			}
			else $this->error($ret['errmsg']);
		}
		else{
			if($id){
				$Wedding=M('Wedding')->where(array('token'=>session('token'),'id'=>$id))->find();
				$this->assign('wedding',$Wedding);
			}
			$photo=M('Photo')->where(array('token'=>session('token')))->select();
			$this->assign('photo',$photo);
			$this->display();
		}
	}
	
	public function info(){
		$data=M('Wedding_info');
		$where['fid'] = I('id','intval');
		$where['type'] = I('get.type',1,'intval');
		$count=$data->where($where)->count();
		$page=new Page($count,25);
		$info=$data->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('wedding',$info);
		$this->assign('type',$where['type']);
		$this->display();
	}
	
	public function infodel(){
		$where['id']= I('get.id',0,'intval');
		$info=M('Wedding_info')->field('fid')->where($where)->find();
		$back=M('Wedding')->where(array('token'=>session('token'),'fid'=>$info['fid']))->find();
		if($back==false){$this->error('非法操作');exit;}
		if(D('Wedding_info')->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function del(){
		$where['id']= I('id',0,'intval');
		$where['token']=session('token');
		if(D('Wedding')->where($where)->delete()){
			$keyword_model=M('Keyword');
            $keyword_model->where(array('token'=>$this->token,'pid'=>I('id',0,'intval'),'module'=>'Wedding'))->delete();
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
}
?>