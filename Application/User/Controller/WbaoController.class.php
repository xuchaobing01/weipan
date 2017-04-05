<?php
namespace User\Controller;
use Spark\Util\Page;
class WbaoController extends UserController{
public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
	public function index(){
		$where['token'] = $this->token;
		$list=M('wbao')->where($where)->order('id DESC')->select();
		$count = M('wbao')->where($where)->count();
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=M('wbao');
			$_POST['starttime']=strtotime(I('post.startdate'));
			$_POST['endtime']=strtotime(I('post.enddate'));
			$_POST['createtime']=time();
			$_POST['token']=$this->token;
			if($_POST['endtime'] < $_POST['starttime']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				if($data->create()!=false){
					if($data->add()){
						$this->success('活动创建成功',U('wbao/index'));
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
	 * @method setinc 编辑活动
	 */
	public function edit(){
		if(IS_POST){
			$data=M('wbao');
			$_POST['id']=I('get.id');
			$_POST['token']=$this->token;
			$_POST['starttime']=strtotime(I('post.startdate'));
			$_POST['endtime']=strtotime(I('post.enddate'));
			$_POST['createtime']=time();
			if($_POST['endtime'] < $_POST['starttime']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
				$check=$data->where($where)->find();
				if($check==false)$this->error('非法操作');
				if($data->where($where)->save($_POST)){
					$this->success('修改成功',U('Wbao/index'));
				}
				else{
					$this->error('操作失败');
				}
			}
		}
		else{
			$id=I('get.id');
			$where=array('id'=>$id,'token'=>$this->token);
			$data=M('wbao');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$coupons=$data->where($where)->find();
			$this->assign('vo',$coupons);
			$this->display('add');
		}
	}
	public function del(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>$this->token);
		$data=M('wbao');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($where)->delete();
		if($back==true){
			$this->success('删除成功');
		}
		else{
			$this->error('操作失败');
		}
	
	}
	
	
	
	
	
	
	public function breglist(){
		$leave_model =M("wbaob");
		import("ORG.Util.Page"); // 导入分页类		
		$find=I('get.find',"");
		if($find!=""){
			if(preg_match("/1[3458]{1}\d{9}$/",$find)){
				$where['telphone']=$find;
			}else{
				$where['_string']="name like '%{$find}%'  ";
			}
		}		
		$where['token'] = session('token');
		
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	
	public function creglist(){
		$leave_model =M("wbaoc");
		import("ORG.Util.Page"); // 导入分页类
		
		$find=I('get.find',"");
		if($find!=""){
			if(preg_match("/1[3458]{1}\d{9}$/",$find)){
				$where['telphone']=$find;
			}else{
				$where['_string']="name like '%{$find}%'  ";
			}
		}
		$where['token'] = session('token');
		
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	public function user(){
		$leave_model =M("wbaou");
		import("ORG.Util.Page"); // 导入分页类
		
		$where['token'] = session('token');
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
		
	}
	public function bedit(){
		if(IS_POST){
			$data=M('wbaob');
			$_POST['id']=I('get.id');
			$_POST['token']=$this->token;
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$po['status']=$_POST['status'];
			$po['errormsg']=$_POST['errormsg'];
			$po['username']=$_POST['username'];
			$po['password']=$_POST['password'];
			if($data->where($where)->save($po)){
				$this->success('审核成功',U('Wbao/breglist'));
			}
			else{
				$this->error('操作失败');
			}
		}
		else{
			$id=I('get.id');
			$where=array('id'=>$id,'token'=>$this->token);
			$data=M('wbaob');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$list=$data->where($where)->find();
			$this->assign('vo',$list);
			$this->display();
		}	
	}
	public function cedit(){
		if(IS_POST){
			$data=M('wbaoc');
			$_POST['id']=I('get.id');
			$_POST['token']=$this->token;
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$po['status']=$_POST['status'];
			$po['errormsg']=$_POST['errormsg'];
			$po['username']=$_POST['username'];
			$po['password']=$_POST['password'];
			
			if($data->where($where)->save($po)){
				$this->success('审核成功',U('Wbao/creglist'));
			}
			else{
				$this->error('操作失败');
			}
		}
		else{
			$id=I('get.id');
			$where=array('id'=>$id,'token'=>$this->token);
			$data=M('wbaoc');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$list=$data->where($where)->find();
			$this->assign('vo',$list);
			$this->display();
		}
	}
	
	
	
}
?>