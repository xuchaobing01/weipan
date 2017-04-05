<?php
namespace User\Controller;
class PaycfgController extends UserController{
	public function index(){
		if(IS_POST){
			$data['wxpay_config'] = $this->get_config('WXPAY');
			$data['id'] = I('post.id',0,'intval');
			if($data['id']){
				$ret = M('pay_config')->save($data);
				if($ret) $this->success('修改成功！');
				else $this->error('修改失败！');
			}
			else{
				$data['token'] = session('token');
				$ret = M('pay_config')->add($data);
				if($ret) $this->success('添加成功！');
				else $this->error('添加失败！');
			}
		}
		else{
			$set = M('pay_config')->where(['token'=>session('token')])->field('id,wxpay_config as config')->find();
			if($set){
				$set['config'] = unserialize($set['config']);
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function alipay_cfg(){
		if(IS_POST){
			$data['alipay_config'] = $this->get_config('ALIPAY');
			$data['id'] = I('post.id',0,'intval');
			if($data['id']){
				$ret = M('pay_config')->save($data);
				if($ret) $this->success('修改成功！');
				else $this->error('修改失败！');
			}
			else{
				$data['token'] = session('token');
				$ret = M('pay_config')->add($data);
				if($ret) $this->success('添加成功！');
				else $this->error('添加失败！');
			}
		}
		else{
			$set = M('pay_config')->where(['token'=>session('token')])->field('id,alipay_config as config')->find();
			if($set){
				$set['config'] = unserialize($set['config']);
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	private function get_config($name='WXPAY'){
		if($name == 'ALIPAY'){
			$config['account'] = I('post.account','','trim');
			$config['pid'] = I('post.pid','','trim');
			$config['key'] = I('post.key','','trim');
		}
		else if($name== 'WXPAY'){
			$config['appid'] = I('post.appid','','trim');
			$config['key'] = I('post.key','','trim');
			$config['appsecret'] = I('post.appsecret','','trim');
			$config['mchid'] = I('post.mchid','','trim');
		}
		$config['status'] = I('post.status','','intval');;
		return serialize($config);
	}
	
	public function test(){
		$config = M('pay_config','sp_')->where(['token'=>session('token')])->find();
		dump($config);
		if(!empty($config['wxpay_config'])){
			$wxpay = unserialize($config['wxpay_config']);
			if($wxpay['status']==1){
				$paytype[] = ['value'=>2,'name'=>'微信支付'];
			}
		}
		if(!empty($config['alipay_config'])){
			$alipay = unserialize($config['alipay_config']);
			if($alipay['status']==1){
				$paytype[] = ['value'=>3,'name'=>'支付宝'];
			}
		}
		$paytype[] = ['value'=>1,'name'=>'货到付款'];
		dump($paytype);
	}
	
	public function genkey(){
		$this->ajaxReturn(['key'=>create_key(32)]);
	}
}
?>