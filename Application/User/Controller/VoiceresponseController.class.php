<?php
/**
 *语音回复
**/
namespace User\Controller;
class VoiceresponseController extends UserController{
	public function index(){
		$where['uid']=session('uid');
		$res=M('Voiceresponse')->where($where)->select();
		$this->assign('info',$res);
		$this->display();
	}
	
	
	public function edit(){
		$id = I('id',0,'intval');
		if($id){
			$where['id'] = $id; 
			$where['uid']=session('uid');
			$res=D('Voiceresponse')->where($where)->find();
			$this->assign('info',$res);
			$this->assign('title','编辑音乐回复');
		}
		else{
			$this->assign('title','新建音乐回复');
		}
		$this->display();
	}
	
	public function del(){
		$where['id']=I('id',0,'intval');
		$where['uid']=session('uid');
		if(D('Voiceresponse')->where($where)->delete()){
			$this->success('操作成功',U('index'));
		}
		else{
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