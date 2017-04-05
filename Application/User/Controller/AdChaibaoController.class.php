<?php
namespace User\Controller;
class AdChaibaoController extends UserController{
	public function index(){
		$list=M('adchaibao')->where(array('token'=>session('token')))->select();
		$this->assign('count',M('adchaibao')->where(array('token'=>session('token')))->count());
		$this->assign('list',$list);
		$this->display();
	}
	
	public function sn(){
		$id = I('get.id');
		$data = M('adchaibao')->where(array('token'=>session('token'),'id'=>$id))->find();
		$record = M('adchaibao_user')->where('lottery_id ='.$id)->order('time desc')->select();
		$recordcount = M('adchaibao_user')->where('lottery_id ='.$id.' and status != 2')->order('time desc')->count();;
		$sendCount = 0;		
		$datacount=$data['fistnums']+$data['secondnums']+$data['thirdnums'];
		$this->assign('datacount',$datacount);
		$this->assign('recordcount',$recordcount);
		$this->assign('record',$record);
		$this->display();
	}
	public function addsn(){
		if(IS_POST){
			$data=M('adchaibao_user');
			$_POST['time']=time();
			if($data->create()!=false){
				if($id=$data->add()){
					$this->success('添加成功',U('AdChaibao/sn',array('id'=>$_POST['lottery_id'])));
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
			$this->assign('id',$_GET['id']);
			$this->display();
		}
	}
	
	public function add(){
		if(IS_POST){
			$data=M('adchaibao');
			$_POST['token']=session('token');
			if($data->create()!=false){	
				if($id=$data->add()){
					$this->success('活动创建成功',U('AdChaibao/index'));
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
			$data=M('adchaibao');	
			$where=array('token'=>session('token'),'id'=>$_POST['id']);
			$check=$data->where($where)->find();
			if($check==false){
				$_POST['token']=session('token');
				$data->add($_POST);
				$this->success('添加成功',U('AdChaibao/index',array('token'=>session('token'))));
			}elseif($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功',U('AdChaibao/index',array('token'=>session('token'))));
				}
				else{
					//echo $data->getlastsql();
					$this->error('操作失败');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{	
			$id = I('get.id');		
			$where=array('token'=>session('token'),'id'=>$id);
			$data=M('adchaibao');
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
	

}
?>