<?php
namespace User\Controller;
class ScoreController extends UserController{
	public function index(){		
		$list=M('Score')->where(array('token'=>session('token'),'type'=>1))->select();
		$this->assign('list',$list);
		$this->display();
	}
	public function add(){
		if(IS_POST){
			$data=D('score');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$_POST['token']=session('token');
			if($data->create()!=false){	
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Score';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['keyword'];
					M('Keyword')->add($data1);	
					$this->success('活动创建成功',U('score/index'));
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
	public function edit(){
		if(IS_POST){
			$data=D('Score');
			$_POST['id']=I('get.id');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Score';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功',U('Score/index',array('token'=>session('token'))));
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
			$data=M('Score');
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
		$data=M('Score');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($where)->delete();
		if($back==true){
			//删除中奖记录
		//	M('Score_record')->where(array('lid'=>$id,'token'=>session('token')))->delete();
			//删除关键词
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Score'))->delete();
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function shop(){
		$id=(int)I('id');
		$where['token'] = session('token');
		$where['pid'] = $id;
		$list=M('Score_shop')->where($where)->order('id DESC')->select();
		$count = M('Score_shop')->where($where)->count();
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('pid',$id);
		$this->display();
	}
	public function addshop(){
		if(IS_POST){
			$data = M('Score_shop');
			$_POST['token']=session('token');
			if(!ceil($_POST['pid'])){
				$this->error('参数错误,请稍候再试');
			}
			if($data->create()!=false){
				if($id=$data->add()){
					$this->success('添加成功',U('Score/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));
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
			$this->assign('pid',$_GET['pid']);
			$this->display();
		}
	}
	public function editshop(){	 
		if(IS_POST){
			$data=M('Score_shop');
			$_POST['id'] = (int)I('get.id');
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Score/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));exit;
				}
				else{
					$this->success('修改成功',U('Score/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Score_shop');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('pid',$_GET['pid']);
			$this->assign('vo',$vo);
			$this->display('addshop');
		}
	}
	public function delshop(){
		$id = I('get.id');
		$vote = M('Score_shop');
		$find = array('id'=>$id);
		$result = $vote->where($find)->find();
		$pid = $result['pid'];
		if($result){
			$vote->where('id='.$result['id'])->delete();
			$this->success('删除成功',U('Score/shop',array('token'=>session('token'),'id'=>$pid)));
		}
		else{
			$this->error('非法操作！');
		}
	}
	public function pic(){
		if(IS_POST){
			$data=M('Score_pic');
			if($_POST['id']){
				if($data->where('id='.$_POST['id'])->save($_POST)){
					$this->success('修改成功',U('Score/index',array('token'=>session('token'))));
				}
			}else{
				$_POST['token']=session('token');
				if($data->create()!=false){
					if($id=$data->add()){
						$this->success('添加成功',U('Score/index',array('token'=>session('token'))));
					}
				}		
			}	
		}
		else{
			$where=array('token'=>session('token'));
			$data=M('Score_pic');
			$lottery=$data->where($where)->find();
			$this->assign('vo',$lottery);
			$this->display();
		}
	}	
}
?>