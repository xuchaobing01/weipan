<?php
namespace Admin\Controller;
class SystemController extends BaseController{
	protected $_moduleName ='Site';
	public function index(){
		$where['display']=1;
		$where['status']=1;
		$order['sort']='asc';
		$nav=M('node')->where($where)->order($order)->select();		
		$this->assign('nav',$nav);
		$this->display();
	}
	
	protected function subnav(){
		$pnode = M('Node')->where(['level'=>1,'name'=>'Site'])->find();
		$where['level'] = 2;
		$where['pid'] = $pnode['id'];
		$where['status'] = 1;
		$where['display'] = array('gt',0);
		$order['sort'] = 'asc';
		$nav=M('Node')->where($where)->order($order)->select();
		$this->assign('subnav',$nav);
	}
	
	public function menu(){
		if(empty($_GET['pid'])){
			$where['display']=2;
			$where['status']=1;
			$where['pid']=2;
			$where['level']=2;
			$order['sort']='asc';
			$nav=M('node')->where($where)->order($order)->select();
			$this->assign('nav',$nav);
		}
		$this->display();
	}
	
	public function main(){
		$this->display();
	}
}