<?php
/**
 *公众号信息管理
 */
namespace User\Controller;

class WxaccountController extends UserController{
	public function index(){
		$wxuser = D('Wxuser');
		$wxaccount=$wxuser->find(session('wxid'));
		$chart = $wxuser->getStatusChart();
		$this->assign('wxaccount',$wxaccount);
		$this->assign('chart',$chart);
		$this->display();
	}
	
	public function chart(){
	
	}
}
?>