<?php
namespace User\Controller;
use Spark\Util\Page;
class DiaoyanController extends UserController{
	public $diaoyan_model;
	public $diaoyan_timu;
	
	public function _initialize() {
		$this->token = session('token');
		parent::_initialize();
		$this->diaoyan_model=M('diaoyan');
		$this->diaoyan_timu=M('diaoyan_timu');

		$this->token=session('token');
		$this->assign('token',$this->token);
		$this->assign('module','Yuyue');
	}
	
	//预约列表
	public function index(){
		$where = array('token'=> $this->token);
		//分页
		$count      = $this->diaoyan_model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		
		$data = $this->diaoyan_model->where($where)->select();
		
		$this->assign('page',$show);
		$this->assign('data',$data);
		$this->display();
	}
	
	//添加调研
	public function add(){ 
		$_POST['token'] = $this->token;
		if(IS_POST){
			if($id = $this->diaoyan_model->add($_POST)){
				$keyword_model=M('Keyword');
				$key = array(
					'keyword'=>$_POST['keyword'],
					'pid'=>$id,
					'token'=>$this->token,
					'module'=> 'Diaoyan'
				);
				$keyword_model->add($key);
				$this->success('调研信息添加成功！',U('Diaoyan/index'));
			}
			else{
				$this->error('调研信息添加失败！');
			}
		}else{
			$set=array();
			$set['time']=time()+10*24*3600;
			$this->assign('set',$set);
			$this->display('set');
		}
	}
	
	//修改和添加预约
	public function set(){
        $id = intval(I('get.id')); 
		$checkdata = $this->diaoyan_model->where(array('id'=>$id))->find();
		if(empty($checkdata)||$checkdata['token']!=$this->token){
            $this->error("没有相应记录.您现在可以添加.",U('Diaoyan/add'));
        }
		if(IS_POST){
            $where=array('id'=>I('post.id'),'token'=>$this->token);
			$check=$this->diaoyan_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($this->diaoyan_model->create()){
				if($this->diaoyan_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Diaoyan/index',array('token'=>$this->token)));
					$keyword_model=M('Keyword');
					$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->save(array('keyword'=>$_POST['keyword']));
				}
				else{
					$this->error('操作失败');
				}
			}else{
				$this->error($this->diaoyan_model->getError());
			}
		}
		else{
			$this->assign('set',$checkdata);
			$this->display();	
		
		}
	}
	
	//删除调研
	public function del(){
		$id = I('get.id',0,'intval');
        if(IS_GET){
            $where=array('id'=>$id,'token'=>$this->token);
            $check=$this->diaoyan_model->where($where)->find();
            if($check==false)   $this->error('非法操作');
			
            $back=$this->diaoyan_model->where($where)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Diaoyan'))->delete();
				$timu = M('Diaoyan_timu');
				$timu->where(array('pid'=> $id))->delete();
                $this->success('操作成功',U('Diaoyan/index',array('token'=>$this->token)));
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Diaoyan/index',array('token'=>$this->token)));
            }
        }        
	}
	
	//订单列表显示
	public function timu(){
		$id = I('get.id',0,'intval');
		$data = $this->diaoyan_timu->where(array('pid'=> $id))->select();
		//分页
		$count = $this->diaoyan_timu->where(array('pid'=> $id))->count();	
		$Page = new Page($count,20);
		$show = $Page->show();
		
		$this->assign('id',$id);
		$this->assign('page',$show);
		$this->assign('data', $data);
		$this->display();
	}
	
	//添加调研
	public function addTimu(){
		$pid = I('get.pid',0,'intval');
		if(IS_POST){
			if($this->diaoyan_timu->add($_POST)){
				$this->success('题目添加成功！',U('Diaoyan/timu',array('id'=> $pid)));
			}
			else{
				$this->error('题目添加失败！');
			}
		}
		else{
			$this->assign('pid', $pid);
			$this->display('setTimu');
		}
	}
	
	//修改和添加题目
	public function setTimu(){
        $pid = I('get.pid',0,'intval'); 
		$checkdata = $this->diaoyan_timu->where(array('tid'=>$pid))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.请先添加.",U('Diaoyan/index'));
        }
		if(IS_POST){
            $where=array('tid'=>$pid);
			$check=$this->diaoyan_timu->where($where)->find();
			if($check==false){
				$this->error('非法操作!');
			}
			if($this->diaoyan_timu->create()){
				if($this->diaoyan_timu->where($where)->save($_POST)){
					$pid = $_GET['pid'];
					$this->success('修改成功',U('Diaoyan/timu',array('token'=>$this->token, 'id'=>$pid)));
				}
				else{
					$this->error('操作失败');
				}
			}
			else{
				$this->error($this->diaoyan_timu->getError());
			}
		}
		else{
			$this->assign('pid', $pid);
			$this->assign('set',$checkdata);
			$this->display('setTimu');
		}
	}
	
	//删除题目
	public function delTimu(){
        $tid = I('get.id',0,'intval');
        $pid = I('get.pid',0,'intval');
        if(IS_GET){
            $where=array('tid'=>$tid,'token'=>$this->token);
            $check=$this->diaoyan_timu->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $back=$this->diaoyan_timu->where($where)->delete();
            if($back==true){
                $this->success('操作成功',U('Diaoyan/timu',array('id'=>$pid,'token'=>$this->token)));
            }else{
                $this->error('服务器繁忙,请稍后再试');
            }
        }
	}
	
	//统计
	public function survey(){
		$id = I('get.id');
		$data = $this->diaoyan_model->find($id);
		
		$user = M('diaoyan_user');
		$count = $user->where(array('diaoyan_id'=>$id))->count();
		
		$timu = $this->diaoyan_timu->where(array('pid'=> $id))->select();
		
		$sur = array();
		foreach($timu as $v){
			$res =0;	
			$res += $v['perca'];
			$res += $v['percb'];
			$res += $v['percc'];
			$res += $v['percd'];
			$res += $v['perce'];
			$sur[] = $res;
		}
		
		$this->assign('timu', $timu);
		$this->assign('count', $count);
		$this->assign('diaoyan', $data);
		$this->display();
	}
}
?>