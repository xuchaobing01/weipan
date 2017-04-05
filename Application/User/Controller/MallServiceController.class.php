<?php
/**
 *@class MallService 商城售后服务
 */
namespace User\Controller;
class MallServiceController extends UserController{
	public function index(){
		$where = ['token'=>session('token')];
		($orderId = $_GET['orderId']) && ($where['orderId'] = ['like','%'.$orderId.'%']);
		($mobile = $_GET['mobile']) && ($where['mobile'] = ['like','%'.$mobile.'%']);
		(isset($_GET['status']) && $_GET['status'] != -1) && ($where['status']=$_GET['status']);
		if(!empty($_GET['daterange'])){
			$ranges = $this->_parseDateRange($_GET['daterange']);
			$where['create_time'][] = ['egt',strtotime($ranges[0])];
			$where['create_time'][] = ['lt',strtotime($ranges[1])+86400];
		}
		$count = M('mall_after_sale')->where($where)->count();
		$page = new \Spark\Util\Page($count,20);
		$list = M('mall_after_sale')->where($where)->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		$this->assign('page',$page->show());
		$this->assign('list',$list);
		$this->display();
	}
	
	private function _parseDateRange($date_str,$separator='~'){
		if(empty($date_str)) return false;
		$parse = explode($separator,$date_str);
		return array(trim($parse[0]),trim($parse[1]));
	}
	
	public function delete($id){
		!$id && $this->error('申诉不存在！');
		$check = M('mall_after_sale')->where(['token'=>session('token'),'id'=>$id])->find();
		!$check && $this->error('申诉不存在！');
		$ret = M('mall_after_sale')->delete($id);
		if($ret){
			M('item_order','weixin_')->where(['orderId'=>$check['orderId']])->setField('after_sale_id',0);
			$this->success('删除成功！');
		}
		else $this->error('删除失败！');
	}
	
	public function handle($id){
		!$id && $this->error('申诉不存在！');
		if(IS_POST){
			$check = M('mall_after_sale')->where(['token'=>session('token'),'id'=>$id])->find();
			!$check && $this->error('申诉不存在！');
			$data = ['status'=>1,'process_time'=>time()];
			$data['process_remark'] = $_POST['remark'];
			$ret = M('mall_after_sale')->where(['id'=>$id])->save($data);
			if($ret) $this->success('处理成功！');
			else $this->error('处理失败！');
		}
		else{
			$set  = M('mall_after_sale')->where(['token'=>session('token'),'id'=>$id])->find();
			$this->assign('set',$set);
			$this->display();
		}
	}
}
?>