<?php
namespace User\Controller;
use Think\Controller;
class ZadanController extends UserController{
	public function index(){
		$user = M('Users')->field('gid,activity_num')->where(array('id'=>session('uid')))->find();
		$group = M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activity_num',$user['activity_num']);
		
		$list=M('Lottery')->field('id,title,joinnum,pv,keyword,startdate,enddate,status')->where(array('token'=>session('token'),'type'=>4))->order('id desc')->select();
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>4))->count());
		$this->assign('list',$list);
		$this->display();	
	}
	
	public function sn(){
		$id = I('get.id');
		$data = M('Lottery')->where(array('token'=>session('token'),'id'=>$id,'type'=>4))->find();
		$record = M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->select();
		$recordcount = M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->count();
		$datacount = $data['fistnums']+$data['secondnums']+$data['thirdnums'];
		$this->assign('datacount',$datacount);//奖品数量
		$this->assign('recordcount',$recordcount);//中讲数量
		$this->assign('record',$record);
		$sendCount = M('Lottery_record')->where('lid='.$id.' and sendstatus=1 and sn!=""')->count();
		$this->assign('sendCount',$sendCount);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=D('lottery');
			$_POST['startdate'] = strtotime($_POST['startdate']);
			$_POST['enddate'] = strtotime($_POST['enddate']);
			$_POST['token'] = session('token');
			for($i=1;$i<=6;$i++){ //处理奖品图片
				$imgs = $_POST['reward'.$i.'imgs'];
				if(!empty($imgs) && is_array($imgs)){
					$_POST['reward'.$i.'imgs'] = implode(';',$imgs);
				}
			}
			if($data->create()!=false){
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['keyword'];
					M('Keyword')->add($data1);
					$user=M('Users')->where(array('id'=>session('uid')))->setInc('activity_num');
					$this->success('活动创建成功',U('Zadan/index'));
				}else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->display();
		}
	}
	
	public function setinc(){
		$id = I('get.id');
		$where = array('id'=>$id,'token'=>session('token'));
		$check = M('Lottery')->where($where)->find();
		if($check==false)$this->error('非法操作');
		/*$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		
		if($user['activitynum']>=$group['activitynum']){
			$this->error('您的免费活动创建数已经全部使用完,请充值后再使用',U('Home/Index/price'));
		}*/
		$data=M('Lottery')->where($where)->setInc('status');
		if($data!=false){
			$this->success('恭喜你,活动已经开始');
		}
		else{
			$this->error('服务器繁忙,请稍候再试');
		}
	}
	
	public function setdes(){
		$id = I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$data=M('Lottery')->where($where)->setDec('status');
		if($data!=false){
			$this->success('活动已经结束');
		}else{
			$this->error('服务器繁忙,请稍候再试');
		}
	}
	
	public function edit(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['id'] = I('get.id');
			$_POST['token']=session('token');
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token'],'type'=>4);
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			for($i=1;$i<=6;$i++){ //处理奖品图片
				$imgs = $_POST['reward'.$i.'imgs'];
				if(!empty($imgs) && is_array($imgs)){
					$_POST['reward'.$i.'imgs'] = implode(';',$imgs);
				}
			}
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){		
				if($id=$data->where($where)->save($_POST)!==false){
					$data1['pid']=$_POST['id'];
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}
		else{
			$id = I('get.id');
			$where=array('id'=>$id,'token'=>session('token'),'type'=>4);
			$data=M('Lottery');
			$lottery=$data->where($where)->find();
			if($lottery==false)$this->error('非法操作');
			for($i=1;$i<=6;$i++){ //处理奖品图片
				$imgs = $lottery['reward'.$i.'imgs'];
				if(!empty($imgs)){
					$lottery['reward'.$i.'imgs'] = explode(';',$imgs);
				}
			}
			
			$this->assign('vo',$lottery);
			$this->display('add');
		}
	}
	
	public function del(){
		$id = I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Lottery');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Lottery'))->delete();
			$this->success('删除成功');
		}
		else{
			$this->error('操作失败');
		}
	}
	
	public function sendprize(){
		$id = I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = time();
		$data['sendstatus'] = 1;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('成功发奖');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id = I('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = '';
		$data['sendstatus'] = 0;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}else{
			$this->error('操作失败');
		}
	}
}
?>