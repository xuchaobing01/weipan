<?php
/**
 *@class OrderControll 订单流程处理
 */
namespace Wf\Controller;
class OrderController extends \Think\Controller{
	public function index(){
		echo IS_CLI;
	}
	
	/**
	 * @method check_order_pay 订单付款过期处理
	 */
	public function check_order_pay(){
		$expire_time = time()- 3600;
		$ret = M('item_order','weixin_')->where(['status'=>1,'add_time'=>['elt',$expire_time]])->save(['status'=>5,'closemsg'=>'过期未付款，自动关闭']);
		echo json_encode(['status'=>0,'count'=>$ret,'timestamp'=>date('Y-m-d H:i:s')]);
	}
	
	/**
	 * @method check_order_pay 订单确认过期处理
	 */
	public function check_order_confirm(){
		$expire_time = time()- 7*24*3600;
		$orders = M('item_order','weixin_')->where(['status'=>3,'fahuo_time'=>['elt',$expire_time]])->select();
		$model = M('item_order','weixin_');
		foreach($orders as $order){
			$member = new \Wap\Logic\Member($order['token'],$order['wechat_id']);
			$member->expense($order['order_sumPrice']);
			$model->where(['token'=>$order['token'],'orderId'=>$order['orderId']])->setField('status',4);
		}
		echo json_encode(['status'=>0,'count'=>count($orders),'timestamp'=>date('Y-m-d H:i:s')]);
	}
	
	public function test(){
		echo json_encode(['status'=>0,'message'=>'it works!','timestamp'=>time()]);
	}
}
?>