<?php
namespace User\Controller;
use Think\Controller;
use Spark\Util\Page;
class ChaibaoController extends UserController{
	public function index(){
		$list=M('Lottery')->field('id,title,joinnum,pv,keyword,startdate,enddate,status')->where(array('token'=>session('token'),'type'=>5))->order('id desc')->select();
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>5))->count());
		$this->assign('list',$list);
		$this->display();	
	}
	
	public function sn(){
		$id = I('get.id');
		$data = M('Lottery')->where(array('token'=>session('token'),'id'=>$id,'type'=>5))->find();
		$where = array('lottery_id='.$id.' and status!=0');
		$count = M('Lottery_user')->where($where)->count();
		$page = new Page($count,20);
		$record = M('Lottery_user')->where($where)->order('time desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		$datacount = $data['fistnums'] + $data['secondnums'] + $data['thirdnums'];
		$this->assign('datacount',$datacount);//奖品数量
		$this->assign('recordcount',$count);//中奖数量
		$this->assign('record',$record);
		$this->assign('page',$page->show());
		$applyCount = M('Lottery_user')->where('lottery_id='.$id.' and status = 2')->count();
		$sendCount = M('Lottery_user')->where('lottery_id='.$id.' and status = 3')->count();
		$this->assign('applyCount',$applyCount);
		$this->assign('sendCount',$sendCount);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=D('lottery');
			$_POST['startdate'] = strtotime($_POST['startdate']);
			$_POST['enddate'] = strtotime($_POST['enddate']);
			$_POST['token'] = session('token');
			//处理幻灯片图片
			$imgs = $_POST['banners'];
			if(!empty($imgs) && is_array($imgs)){
				$_POST['reward6imgs'] = implode(';',$imgs);
			}
			$_POST['type'] = 5;
			if($data->create()!=false){
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['keyword'];
					M('Keyword')->add($data1);
					$user=M('Users')->where(array('id'=>session('uid')))->setInc('activity_num');
					$this->success('活动创建成功',U('Chaibao/index'));
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
	
	public function edit(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['id'] = I('get.id');
			$_POST['token']=session('token');
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token'],'type'=>5);
			$_POST['startdate'] = strtotime($_POST['startdate']);
			$_POST['enddate'] = strtotime($_POST['enddate']);
			//处理幻灯片图片
			$imgs = $_POST['banners'];
			if(!empty($imgs) && is_array($imgs)){
				$_POST['reward6imgs'] = implode(';',$imgs);
			}
			$check = $data->where($where)->find();
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
			$where = array('id'=>$id,'token'=>session('token'),'type'=>5);
			$data = M('Lottery');
			$lottery = $data->where($where)->find();
			if($lottery==false){
				$this->error('非法操作');
			}
			$imgs = $lottery['reward6imgs'];
			if(!empty($imgs)){
				$lottery['banners'] = explode(';',$imgs);
			}
			$this->assign('vo',$lottery);
			$this->display('add');
		}
	}
	
	public function del(){
		$id = I('get.id');
		$where = array('id'=>$id,'token'=>session('token'));
		$data = M('Lottery');
		$check = $data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back = $data->where($wehre)->delete();
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
		$where=array('id'=>$id,'status'=>2);
		$data['sendtime'] = time();
		$data['status'] = 3;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('成功发奖');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id = I('id');
		$where=array('id'=>$id,'status'=>3);
		$data['sendtime'] = '';
		$data['status'] = 2;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function tongji(){
		$lid = I('get.lid',0,'intval');
		$records = M('lottery_tongji')->where(['lid'=>$lid])->order('id desc')->select();
		$this->assign('records',$records);
		$this->display();
	}
}
?>