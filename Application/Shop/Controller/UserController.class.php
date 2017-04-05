<?php
namespace Shop\Controller;
class UserController extends BaseController {
    public function addaddress(){
    	if(IS_POST){
			$user_address=M('user_address');
			$consignee= I('post.consignee','','trim');
			$sheng= I('post.sheng','', 'trim');
			$shi= I('post.shi','', 'trim');
			$qu= I('post.qu','', 'trim');
			$address = I('post.address', '','trim');
			$phone_mob = I('post.phone_mob','','trim');
			$data['token'] = $this->token;
			$data['wechat_id'] = $this->wechat_id;
			$data['consignee'] = $consignee;
			$data['sheng'] = $sheng;
			$data['shi'] = $shi;
			$data['qu'] = $qu;
			$data['address'] = $address;
			$data['mobile'] = $phone_mob;
			if($user_address->data($data)->add()!==false){
				$this->ajaxReturn(['errcode'=>0,'errmsg'=>'添加成功！']);
			}
			else $this->ajaxReturn(['errcode'=>500,'errmsg'=>'添加失败！']);
    	}
        $addr=M('item_order_address')->where(array('token'=>$this->token))->select();
        $this->assign('myaddress', $addr);
    	$this->display();
    }

    /**
    * 订单信息
    */
    public function index() {
		$item_order=M('item_order');
		$order_count = array();
		$order_count[] = $item_order->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>1])->count();//待付款
		$order_count[] = $item_order->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>2])->count();//待发货
		$order_count[] = $item_order->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>3])->count();//待收货
		$where = ['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>4];
		$where['_string'] = 'comment_id is null';
		$order_count[] = M('item_comment_v','sp_')->where($where)->count();//已完成
		$order_count[] = $item_order->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>5])->count();//已关闭
		$addr_count = M('user_address')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->count();
		$this->assign('order_count',$order_count);
		$this->assign('addr_count',$addr_count);
        $this->display();
    }
	
	public function order_list(){
		$item_order=M('item_order');
		$order_detail=M('order_detail');
		if(!isset($_GET['status'])){
			$status=1;
		}
		else {
			$status=$_GET['status'];
		}
        $item_orders= $item_order->order('id desc')->where('status='.$status." and wechat_id='".$this->wechat_id."'  and token='".$this->token."'")->select();
        foreach ($item_orders as  $key=>$val){
			$order_details= $order_detail->where("orderId='".$val['orderId']."'")->select();
			$order_id = $val['orderId'];
			foreach ($order_details as $val){
				$items= array('title'=>$val['title'],'img'=>$val['img'],'oprice'=>$val['oprice'],'price'=>$val['price'],'quantity'=>$val['quantity'],'itemId'=>$val['itemId'],'spec'=>$val['spec']);
				$item_orders[$key]['items'][]=$items;
				$check = M('item_comment')->where(['order_id'=>$order_id,'item_id'=>$val['itemId']])->count();
				$item_orders[$key]['has_comment'] = $check;
			}
			$check_apply = M('mall_after_sale','sp_')->where(['token'=>$this->token,'orderId'=>$order_id])->find();
			if($check_apply !=null) $item_orders[$key]['has_apply'] = true;
			else $item_orders[$key]['has_apply']=false;
		}
		$this->assign('item_orders',$item_orders);
		$this->assign('status',$status);
        $this->display();
	}
	
    
    public function edit_address(){
		$user_address_mod = M('user_address');
        $id = I('get.id', 0,'intval');
		if (IS_POST) {
            $consignee = I('post.consignee', '','trim');
            $address = I('post.address', '','trim');
			$mobile = I('post.phone_mob', '','trim');
			$sheng = I('post.sheng', '','trim');
			$shi = I('post.shi', '' ,'trim');
			$qu = I('post.qu', '','trim');
            $result = $user_address_mod->where(array('id'=>$id, 'wechat_id'=>$this->wechat_id))->save(array(
				'consignee' => $consignee,
				'address' => $address,
				'mobile' => $mobile,
				 'sheng' => $sheng,
				  'shi' => $shi,
				   'qu' => $qu
			));
			if ($result) {
				$msg = array('errcode'=>0, 'errmsg'=>'编辑成功！');
			} else {
				$msg = array('errcode'=>500, 'errmsg'=>'编辑失败！');
			}
			$this->ajaxReturn($msg);
        }
        $info = $user_address_mod->find($id);
		$this->assign('info', $info);
        $addr=M('item_order_address')->where(array('token'=>$this->token))->select();
        $this->assign('myaddress', $addr);
    	$this->display();
    }
    
    /**
     * 收货地址
     */
    public function address() {
        $user_address_mod = M('user_address');
        $id = I('id',0,'intval');
        $type = I('type', 'edit', 'trim');
        if ($id) {
            if ($type == 'del') {
                $user_address_mod->where(array('id'=>$id, 'wechat_id'=>session('wechat_id')))->delete();
                $msg = array('status'=>1, 'info'=>'删除失败！');
                $this->assign('msg', $msg);
            } else {
                $info = $user_address_mod->find($id);
                $this->assign('info', $info);
            }
        }
        $address_list = $user_address_mod->where(array('token'=>$this->token, 'wechat_id'=>$this->wechat_id))->order('id desc')->select();
        $this->assign('address_list', $address_list);
        $this->display();
    }
	
	/**
	 *收藏商品，已收藏的删除收藏
	 */
	public function collect(){
		$itemId = I('item_id',0,'intval');
		$model = M('mall_favor','sp_');
		if($itemId){
			$check = $model->where(['wechat_id'=>$this->wechat_id,'item_id'=>$itemId])->find();
			if($check == null){
				$model->add(['wechat_id'=>$this->wechat_id,'item_id'=>$itemId,'token'=>$this->token]);
				$this->ajaxReturn(['errcode'=>1,'errmsg'=>'收藏成功！']);
			}
			else{
				$model->delete($check['id']);
				$this->ajaxReturn(['errcode'=>2,'errmsg'=>'删除收藏成功！']);
			}
		}
		$this->ajaxReturn(['errcode'=>0,'errmsg'=>'操作失败！']);
	}
	
	public function collect_list(){
		$model = M('v_mall_favor','sp_');
		$list = $model->where(['wechat_id'=>$this->wechat_id])->order('id desc')->select();
		$this->assign('collects',$list);
		$this->display();
	}
}