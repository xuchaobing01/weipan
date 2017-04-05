<?php
namespace User\Controller;
class FunctionController extends UserController{
	function index(){
		$wxid=I('get.id');
		$uid = session('uid');
		$wx = M('Wxuser')->where(['id'=>$wxid,'uid'=>$uid])->find();
		if($wx){
			session('wxid', $wxid);
			session('token', $wx['token']);
			session('wxname', $wx['wxname']);
			$this->display();
		}
		else{
			$this->error('非法操作！',U('Index/index'));
		}
	}
	
	function home(){
		$id=session("wxid");
		$token=session('token');
		$gid=session('gid');
		$token_open=M('Token_open');
		$toback=$token_open->field('id,queryname')->where(array('token'=>session('token'),'uid'=>session('uid')))->find();
		
		$open['uid']=session('uid');
		$open['token']=session('token');
		
		$check=explode(',',$toback['queryname']);
		$this->assign('check',$check);
		
		$fun=M('Function')->where("status=1 and gid <=$gid")->select();
		
		$this->assign('fun',$fun);
		
		$this->assign('group',$group);
		$this->assign('token',session('token'));
		$this->display();
	}
}
?>