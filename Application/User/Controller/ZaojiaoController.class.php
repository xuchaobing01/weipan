<?php
namespace User\Controller;
use Spark\Util\Page;
class ZaojiaoController extends UserController{
	public function index(){
		
	}
	
	public function member(){
		$list = M('zaojiao_member')->where(['token'=>session('token')])->select();
		foreach($list as $i => $item){
			$list[$i]['baby_age'] = $this->calcu_age($item['baby_birthday']);
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	private function calcu_age($birthday){
		$time = time() - strtotime($birthday);
		$month = intval($time/(30*86400));
		$year = intval($month/12);
		$month = $month%12;
		$str = $year?$year.'岁':'';
		$str .= $month?$month.'月':'';
		return $str;
	}
	
	private function calcu_age2($birthday){
		$time = time() - intval($birthday);
		$month = intval($time/(30*86400));
		$year = intval($month/12);
		$month = $month%12;
		$str = $year?$year.'岁':'';
		$str .= $month?$month.'月':'';
		return $str;
	}
	
	public function delete(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('非法操作！');
		$ret = M('zaojiao_member')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret)$this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	public function zj_class(){
		$list = M('zaojiao_class')->where(['token'=>session('token')])->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function zj_class_edit(){
		$id = I('id',0,'intval');
		if(IS_POST){
			if($id){
				$_POST['end_time'] = strtotime($_POST['end_time']);
				$_POST['detail'] = htmlspecialchars($_POST['detail']);
				$ret = M('zaojiao_class')->save($_POST);
				if($ret) $this->success('保存成功！');
				else $this->error('保存失败！');
			}
			else{
				$_POST['token'] = session('token');
				$_POST['end_time'] = strtotime($_POST['end_time']);
				$_POST['create_time'] = time();
				$_POST['detail'] = htmlspecialchars($_POST['detail']);
				$ret = M('zaojiao_class')->add($_POST);
				if($ret) $this->success('添加成功！');
				else $this->error('添加失败！');
			}
		}
		else{
			if($id){
				$set = M('zaojiao_class')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function zj_class_del(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('非法操作！');
		$ret = M('zaojiao_class')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret)$this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	public function zj_activity(){
		$list = M('zaojiao_activity')->where(['token'=>session('token')])->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function zj_activity_edit(){
		$id = I('id',0,'intval');
		if(IS_POST){
			if($id){
				$_POST['end_time'] = strtotime($_POST['end_time']);
				$_POST['detail'] = htmlspecialchars($_POST['detail']);
				$_POST['start_time'] = strtotime($_POST['start_time']);
				$_POST['sign_stop_time'] = strtotime($_POST['sign_stop_time']);
				$ret = M('zaojiao_activity')->save($_POST);
				if($ret) $this->success('保存成功！');
				else $this->error('保存失败！');
			}
			else{
				$_POST['token'] = session('token');
				$_POST['end_time'] = strtotime($_POST['end_time']);
				$_POST['start_time'] = strtotime($_POST['start_time']);
				$_POST['sign_stop_time'] = strtotime($_POST['sign_stop_time']);
				$_POST['create_time'] = time();
				$_POST['detail'] = htmlspecialchars($_POST['detail']);
				$ret = M('zaojiao_activity')->add($_POST);
				if($ret) $this->success('添加成功！');
				else $this->error('添加失败！');
			}
		}
		else{
			if($id){
				$set = M('zaojiao_activity')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function zj_activity_del(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('非法操作！');
		$ret = M('zaojiao_activity')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret)$this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	public function zj_class_form(){
		$list = M('zaojiao_form')->where(['token'=>session('token'),'type'=>0])->select();
		foreach($list as $i => $item){
			$list[$i]['baby_age'] = $this->calcu_age2($item['baby_birthday']);
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	public function zj_activity_form(){
		$list = M('zaojiao_form')->where(['token'=>session('token'),'type'=>1])->select();
		foreach($list as $i => $item){
			$list[$i]['baby_age'] = $this->calcu_age2($item['baby_birthday']);
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	public function zj_form_del(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('非法操作！');
		$ret = M('zaojiao_form')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret)$this->success('删除成功！');
		else $this->error('删除失败！');
	}
}
?>