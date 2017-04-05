<?php
/**
 *@ class GuessController 微闯关控制类
 */
namespace User\Controller;
use Spark\Util\Page;
class PassController extends UserController{
	public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
	public function index(){
		$data=M('Pass');
		$where=array('token'=>session('token'));		
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();	
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function addpass(){
		if(IS_POST){			
			$data = M('Pass');
			$_POST['token']=session('token');
			//$_POST['name'] = I("post.name");
		//	$_POST['des'] = strip_tags(I("post.info"));
			$_POST['createtime']=time();
			$_POST['title'] = I("post.title");
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['des'] = preg_replace($pattern,"",$_POST['content']);
		
					
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");			
			$_POST['musicurl'] = I("post.musicurl");
			$_POST['ppicurl'] = I("post.ppicurl");			
		
			if($data->create()!=false){
				if($data->add()){
					$this->success('添加成功',U('Pass/index',array('token'=>session('token'))));
				}
				else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$vo['title']='全世界只有3个人玩到了40关';
			$vo['des']='<p style="white-space: normal;">将手放在屏幕,使竿变长</p><p style="white-space: normal;">据说智商超过130的人,</p><p style="white-space: normal;">才能玩到第40关,</p><p style="white-space: normal;">成绩发到公众号：皮诺客科技 来比拼</p>';
			$this->assign('vo',$vo);
			$this->display();
		}	
	}
	public function edit(){
		if(IS_POST){
			$data=M('Pass');
			$_POST['id'] = (int)I('get.id');
			$_POST['des'] = strip_tags(I("post.info"));
				$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['des'] = preg_replace($pattern,"",$_POST['content']);
			$_POST['createtime']=time();
			$_POST['starttime'] = strtotime($_POST['startdate']);
			$_POST['endtime'] = strtotime($_POST['enddate']);
			//$_POST['content'] = I("content");
				
			$_POST['title'] = I("post.title");
			$_POST['banner'] = I("post.picurl");
			$_POST['banner2'] = I("post.banner2");
				
			$_POST['rename1'] = I("rename1");
			$_POST['rename2'] = I("rename2");
			//$_POST['rename3'] = I("rename3");
			//$_POST['rename4'] = I("rename4");
				
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");
				
			$_POST['musicurl'] = I("post.musicurl");
			$_POST['ppicurl'] = I("post.ppicurl");		
				
			if($_POST['endtime']<$_POST['starttime']){
				$this->error('结束时间不能小于开始时间!');
			}
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
				
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Pass/index',array('token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Pass/index',array('token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('pass');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('vo',$vo);
			$this->display('addpass');
		}
	}
	public function del(){//单项删除
		$id = I('get.id');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('pass');
			$check=$data->where($where)->find();
			if($check==false)   $this->error('非法操作');
			$r=$data->where($where)->delete();
			if($r==true){
				$this->success('操作成功',U('Guess/index'));
			}
			else{
				$this->error('服务器繁忙,请稍后再试',U('Guess/index'));
			}
		}
	}

	public function pt(){
		$data=M('pt');
		$where=array('token'=>session('token'));		
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();	
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function addpt(){
		if(IS_POST){			
			$data = M('pt');
			$_POST['token']=session('token');
			$_POST['createtime']=time();
			$_POST['title'] = I("post.title");
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['des'] = preg_replace($pattern,"",$_POST['content']);		
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");			
			$_POST['musicurl'] = I("post.musicurl");
			$_POST['ppicurl'] = I("post.ppicurl");
			$_POST['statdate'] = strtotime(I('post.statdate'));
    		$_POST['enddate'] = strtotime(I('post.enddate'));			
		
			if($data->create()!=false){
				if($data->add()){
					$this->success('添加成功',U('Pass/pt',array('token'=>session('token'))));
				}
				else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$this->display();
		}	
	}
	public function editpt(){
		if(IS_POST){
			$data=M('pt');
			$_POST['id'] = (int)I('get.id');
			$_POST['des'] = strip_tags(I("post.info"));
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['des'] = preg_replace($pattern,"",$_POST['content']);
			$_POST['createtime']=time();	
			$_POST['title'] = I("post.title");		
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");
			$_POST['statdate'] = strtotime(I('post.statdate'));
    		$_POST['enddate'] = strtotime(I('post.enddate'));
				
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
				
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Pass/pt',array('token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Pass/pt',array('token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('pt');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('vo',$vo);
			$this->display('addpt');
		}
	}
	public function delpt(){//单项删除
		$id = I('get.id');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('pt');
			$check=$data->where($where)->find();
			if($check==false)   $this->error('非法操作');
			$r=$data->where($where)->delete();
			if($r==true){
				$this->success('操作成功',U('Pass/pt'));
			}
			else{
				$this->error('服务器繁忙,请稍后再试',U('Pass/pt'));
			}
		}
	}
	
	
	
}
?>