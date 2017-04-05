<?php
namespace User\Controller;
class LotteryController extends UserController{
	public function index(){
		$user=M('Users')->field('gid,activity_num')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activity_num',$user['activity_num']);
		$list=M('Lottery')->where(array('token'=>session('token'),'type'=>1))->select();
		
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>1))->count());
		$this->assign('list',$list);
		$this->display();
	}
	
	public function sn(){
		$id = I('get.id');
		$data = M('Lottery')->where(array('token'=>session('token'),'id'=>$id))->find();
		
		(I('sn') && ($where['sncode'] = ['like','%'.I('sn').'%']));
		$where['lottery_id']=$id;
		$where['status']=array('neq',0);
		
		$record = M('Lottery_user')->where($where)->order('id desc')->select();
		echo 
		$recordcount = count($record);
		$sendCount = 0;
		foreach($record as $r){
			if($r['status'] == 2){
				$sendCount ++ ;
			}
		}
		$datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums']+$data['fournums']+$data['fivenums']+$data['sixnums'];
		$this->assign('datacount',$datacount);
		$this->assign('recordcount',$recordcount);
		$this->assign('sendCount',$sendCount);
		$this->assign('record',$record);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$_POST['need_share'] = I('post.need_share',0,'intval');
			if($data->create()!=false){	
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['keyword'];
					M('Keyword')->add($data1);
					$user=M('Users')->where(array('id'=>session('uid')))->setInc('activity_num');
					$this->success('活动创建成功',U('Lottery/index'));
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
	
	//开启活动
	public function setinc(){
		if(session('gid')==1){
			$this->error('vip0无法开启活动,请充值后再使用',U('Home/Index/price'));
		}
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$user=M('Users')->field('gid,activity_num')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();		
		if($user['activity_num']>=$group['activity_limit']){
			$this->error('您的免费活动创建数已经全部使用完,请充值后再使用',U('Home/Index/price'));
		}
		if ($check['status']==0){
			$data=M('Lottery')->where($where)->save(array('status'=>1));
			$tip='恭喜你,活动已经开始';
		}
		else {
			$data=M('Lottery')->where($where)->save(array('status'=>0));
			$tip='设置成功,活动已经结束';
		}
		
		if($data!=false){
			$this->success($tip);
		}else{
			$this->error('设置失败');
		}
	}
	
	public function setdes(){
		$id=I('get.id');
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
			$_POST['id']=I('get.id');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$_POST['need_share'] = I('post.need_share',0,'intval');
			if(empty($_POST['fist']) || empty($_POST['fistnums'])){
				$this->error('必须设置一等奖奖品和数量');
			}
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功',U('Lottery/index',array('token'=>session('token'))));
				}
				else{
					$this->error('操作失败');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$id=I('get.id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Lottery');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$lottery=$data->where($where)->find();		
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
			//删除中奖记录
			M('lottery_record')->where(array('lid'=>$id,'token'=>session('token')))->delete();
			//删除关键词
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Lottery'))->delete();
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendprize(){
		$id = I('get.id');
		$where=array('id'=>$id);
		$data['sendtime'] = time();
		$data['status'] = 2;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('成功发奖');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id = I('get.id');
		$where=array('id'=>$id);
		$data['sendtime'] = '';
		$data['status'] = 1;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}else{
			$this->error('操作失败');
		}
	}
}
?>