<?php
namespace User\Controller;
class CouponController extends UserController{
	public function index(){
		$user=M('Users')->field('gid,activity_num')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activity_num',$user['activity_num']);
		$list=M('Lottery')->where(array('token'=>session('token'),'type'=>3))->select();
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>3))->count());
		$this->assign('list',$list);
		$this->display();
	}
	
	public function sn(){
		$id=I('get.id',0,'intval');
		$data=M('Lottery')->where(array('token'=>session('token'),'id'=>$id,'type'=>3))->find();
		$record=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->select();
		$recordcount=M('Lottery_record')->where('token="'.session('token').'" and lid='.$id.' and sn!=""')->count();
		$datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums'];
		$this->assign('datacount',$datacount);
		$this->assign('recordcount',$recordcount);
		$this->assign('record',$record);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['startdate']=strtotime(I('post.startdate'));
			$_POST['enddate']=strtotime(I('post.enddate'));
			$_POST['type']=3;
			if($_POST['enddate'] < $_POST['startdate']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				if($data->create()!=false){
					if($id=$data->add()){
						$data1['pid']=$id;
						$data1['module']='Lottery';
						$data1['token']=session('token');
						$data1['keyword']=I('post.keyword');
						M('Keyword')->add($data1);
						$user=M('Users')->where(array('id'=>session('uid')))->setInc('activity_num');
						$this->success('活动创建成功',U('Coupon/index'));
					}
					else{
						$this->error('服务器繁忙,请稍候再试');
					}
				}
				else{
					$this->error($data->getError());
				}
			}
		}
		else{
			$this->display();
		}
	}
	
	/**
	 * @method setinc 开启活动
	 */
	public function setinc(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$user=M('Users')->field('gid,activity_num')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		
		if($user['activity_num']>=$group['activity_limit']){
			$this->error('您的免费活动创建数已经全部使用完,请充值后再使用');
		}
		$data=M('Lottery')->where($where)->setInc('status');
		if($data!=false){
			$this->success('恭喜你,活动已经开始');
		}
		else{
			$this->error('服务器繁忙,请稍候再试');
		}
	}
	
	/**
	 * @method setinc 结束活动
	 */
	public function setdes(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Lottery')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$data=M('Lottery')->where($where)->setDec('status');
		if($data!=false){
			$this->success('活动已经结束');
		}
		else{
			$this->error('服务器繁忙,请稍候再试');
		}
	
	}
	
	/**
	 * @method setinc 编辑活动
	 */
	public function edit(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['id']=I('get.id');
			$_POST['token']=session('token');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			if($_POST['enddate'] < $_POST['startdate']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				$where=array('id'=>$_POST['id'],'token'=>$_POST['token'],'type'=>3);
				$check=$data->where($where)->find();
				if($check==false)$this->error('非法操作');
					if($data->where($where)->save($_POST)){
						$data1['pid']=$_POST['id'];
						$data1['module']='Lottery';
						$data1['token']=session('token');
						$da['keyword']=$_POST['keyword'];
						M('Keyword')->where($data1)->save($da);
						$this->success('修改成功',U('Coupon/index',array('token'=>session('token'))));
					}
					else{
						$this->error('操作失败');
					}
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
		$id=I('get.id');
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
		}
		else{
			$this->error('操作失败');
		}
	
	}
	
	public function sendprize(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = time();
		$data['sendstatus'] = 1;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('成功发优惠券');
		}
		else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data['sendtime'] = '';
		$data['sendstatus'] = 0;
		$back = M('Lottery_record')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}
		else{
			$this->error('操作失败');
		}
	}
}
?>