<?php
namespace Shop\Controller;
class OrderController extends BaseController {
	public function _initialize(){
		parent::_initialize();
        $this->cart = new \Shop\Logic\Cart();
	}
	
	/**
	 *@method back 申请退换货
	 */
	public function back(){
		if(IS_POST){
			$orderId = $_POST['orderId'];
			$order = M('item_order')->where(['orderId'=>$orderId])->find();
			if($order == null) {
				echo json_encode(['status'=>2,'message'=>'订单不存在！']);
				exit;
			}
			$data['token'] = $this->token;
			$data['wechat_id'] = $this->wechat_id;
			$data['orderId'] = $orderId;
			$data['create_time'] = time();
			$data['type'] = $_POST['type'];
			$data['message'] = $_POST['message'];
			$data['img'] = $_POST['img'];
			$data['linkman'] = $_POST['linkman'];
			$data['mobile'] = $_POST['mobile'];
			$data['status'] = 0;
			$id = M('mall_after_sale','sp_')->add($data);
			if($id){
				M('item_order')->where(['orderId'=>$orderId])->setField('after_sale_id',$id);
				echo json_encode(['status'=>0,'message'=>'申请成功！','url'=>U('checkOrder?orderId='.$orderId.'&token='.$this->token)]);
			}
			else{
				echo json_encode(['status'=>1,'message'=>'申请失败！']);
			}
		}
		else{
			$orderId=$_GET['orderId'];
			!$orderId && $this->_404();
			$order = M('item_order')->field('address_name,mobile')->where(['orderId'=>$orderId])->find();
			$apply = M('mall_after_sale','sp_')->where(['token'=>$this->token,'orderId'=>$orderId])->find();
			$this->assign('apply',$apply);
			$this->assign('set',$this->get_mall_set());
			$this->assign('orderId',$orderId);
			$this->assign('order',$order);
			$this->display();
		}
	}
	
	
	/**
	 *@method cancelOrder 取消订单
	 */
	public function cancelOrder(){
		$orderId=$_GET['orderId'];
		!$orderId && $this->_404();
		$this->assign('orderId',$orderId);
		$this->display();
	}
	
	/**
	 *@method confirmOrder 确认收货
	 */
	public function confirmOrder(){
		$orderId=$_GET['orderId'];
		$status=$_GET['status'];
	    !$orderId && $this->_404();
	    $item_order=M('item_order');
	    $item=M('item');
	    $item_orders= $item_order->where(['orderId'=>$orderId,"token"=>$this->token,"wechat_id"=>$this->wechat_id,"status"=>3])->find();
	    if(!is_array($item_orders))
	    {
	     	$this->error('该订单不存在!');
	    }
	    $data['status']=4;//收到货
	    $data['complete_time']=time();
	    if($item_order->where(['orderId'=>$orderId,"token"=>$this->token,"wechat_id"=>$this->wechat_id])->save($data))
	    {
			//添加会员消费积分
			$this->expense($item_orders['order_sumPrice']);
	     	$this->redirect('User/index',['token'=>$this->token,'status'=>$status]);
	    }
		else {
	     	$this->error('确定收货失败');
	    }
	}
	
	/**
	 *@method expense 增加消费积分
	 *
	 */
	private function expense($info,$score){
		C('DB_PREFIX','sp_');
		//检查是否领取会员卡
		if(M('Member_card_create')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id])->find() == null) return 0;
		if(is_array($info)){
			$data['expense'] = $info['money'];
			$data['order_id'] = isset($info['order_id'])?$info['order_id']:"";
			$data['type'] = isset($info['type'])?$info['type']:0;
			$data['desc'] = isset($info['desc'])?$info['desc']:"";
		}
		else{
			$data['expense'] = floatval($info);
			$data['type'] = 0;
		}
		if(isset($score)){
			$data['score'] = intval($score);
		}
		else{
			$set = M('Member_card_exchange')->where(array('token'=>$this->token))->find();
			if($set == null){
				$reward = 0;
			}
			else{
				$reward = $set['reward'];
			}
			$data['score'] = floor($data['expense']*$reward);
		}
		$data['token'] = $this->token;
		$data['wechat_id'] = $this->wechat_id;
		$data['time'] = time();
		//增加消费积分记录
		$ret = M('member_expense')->add($data);
		//增加总积分记录
		$minfo = M('member_info');
		$minfo->where(['wechat_id'=>$this->wechat_id])->setInc('total_expense',$data['expense']);
		$minfo->where(['wechat_id'=>$this->wechat_id])->setInc('expense_score',$data['score']);
		$minfo->where(['wechat_id'=>$this->wechat_id])->setInc('total_score',$data['score']);
		$row = array();
		$row['token'] = $this->token;
		$row['wechat_id'] = $this->wechat_id;
		$row['score'] = $data['score'];
		$row['type'] = 2;
		$row['remark'] = '消费积分';
		$row['time'] = time();
		M('Member_score_record')->add($row);
		C('DB_PREFIX','weixin_');
		return $ret;
	}
	
	/**
	 *@method closeOrder 用户关闭订单
	 */
	public function closeOrder()
	{
		$orderId=$_POST['orderId'];
		$cancel_reason=$_POST['cancel_reason'];
		!$orderId && $this->_404();
		$item_order=M('item_order');
		$item=M('item');
		$order_detail=M('order_detail');
		$order=$item_order->where(['orderId'=>$orderId,'token'=>$this->token,'wechat_id'=>session('wechat_id')])->find();
		if(!is_array($order))
		{
			$this->error('该订单不存在');
		}
		else {
			$data['status']=5;
			$data['closemsg']=$cancel_reason;
			if($item_order->where(['orderId'=>$orderId])->save($data))//设置为关闭
			{
				$order_details=$order_detail->where(['orderId'=>$orderId])->select();
				foreach ($order_details as $val)
				{
					$item->where('id='.$val['itemId'])->setInc('goods_stock',$val['quantity']);
				}
				$this->redirect('User/index?token='.$this->token);
			}
			else{
				$this->error('关闭订单失败!');
			}
		}
	}
	
	/**
	 *@method 查看订单
	 */
	public  function checkOrder(){
		$orderId=$_GET['orderId'];
		!$orderId && $this->_404();
		$status=$_GET['status'];
		$item_order=M('item_order');
		
		$order=$item_order->where(['orderId'=>$orderId,'token'=>$this->token,'wechat_id'=>$this->wechat_id])->find();
		if(!is_array($order)){
			$this->error('该订单不存在');
		}
		else{
			$order_detail=M('order_detail');
			$order_details= $order_detail->where(["orderId"=>$order['orderId']])->select();
			$item_detail=array();
			foreach ($order_details as $val){
				$items= array('itemId'=>$val['itemId'],'title'=>$val['title'],'img'=>$val['img'],'oprice'=>$val['oprice'],'price'=>$val['price'],'quantity'=>$val['quantity'],'spec'=>$val['spec']);
				$item_detail[]=$items;
			}
		}
	    $this->assign('item_detail',$item_detail);
		$this->assign('order',$order);
		$this->display();
	}
	
	/**
	 *@method jiesuan 用户结算页面
	 */
	public function jiesuan(){
		if(count(session('cart'))>0){
			$user_address_mod = M('user_address');
            $address_list = $user_address_mod->where(array('token'=>$this->token,'wechat_id'=>$this->wechat_id))->select();
            $this->assign('address_list', $address_list);
            $items=M('item');
			
			$freesum = $this->_get_transport_fee();
			$goodsPrice= $this->cart->getPrice();
			$sumPrice= $this->cart->getPrice() + $freesum;
			//支付方式
			$this->assign('paytype',$this->get_pay_type());
			//商城设置
			$set = $this->get_mall_set();
			$this->assign('set',$set);
			$this->assign('items',$this->cart->getItems());
			//获取运费
			
			$this->assign('freesum',sprintf('%.2f',$freesum));
			$this->assign('sumPrice',sprintf('%.2f',$sumPrice));
			$this->assign('goodsPrice',sprintf('%.2f',$goodsPrice));
			if($set['use_coupon'] == 1){
				$this->assign('coupons',$this->get_coupon($sumPrice));
			}
            $addr=M('item_order_address')->where(array('token'=>$this->token))->select();
            $this->assign('myaddress', $addr);
			
			$this->display();
		}
		else
		{
			$this->redirect('Shopcart/index',['token'=>$this->token]);
		}
	}
	
	private function get_mall_set(){
		if(S('mall_config')==false){
			$set = M('mall_config','sp_')->where(['token'=>$this->token])->find();
			$set['transport_fee'] = floatval($set['transport_fee']);
			$set['transport_free_line'] = floatval($set['transport_free_line']);
			$set['transport_free_base_num'] = intval($set['transport_free_base_num']);
			$set['after_sale_tip'] = $set['after_sale_tip'];
			
			S('mall_config',$set,600);
			return $set;
		}
		return S('mall_config');
	}
	
	private function get_pay_type(){
		if(S('pay_type')==false){
			$config = M('pay_config','sp_')->where(['token'=>$this->token])->find();
			if(!empty($config['wxpay_config'])){
				$config = unserialize($config['wxpay_config']);
				if($config['status']==1){
					$paytype[] = ['value'=>2,'name'=>'微信支付'];
				}
			}
			if(!empty($config['alipay_config'])){
				$config = unserialize($config['alipay_config']);
				if($config['status']==1){
					$paytype[] = ['value'=>3,'name'=>'支付宝'];
				}
			}
			$paytype[] = ['value'=>1,'name'=>'货到付款'];
			S('pay_type',$paytype,600);//缓存支付配置
			return $paytype;
		}
		return S('pay_type');
	}
	
	/**
	 *@method 获取当前用户可用的优惠券
	 */
	public function get_coupon($orderPrice = 0){
		C('DB_PREFIX','sp_');
		$model = M('member_coupon_view');
		$time = time()-24*3600;
		$coupons = $model->field('id,status,title,expire_date,discount_money,order_base_price,sncode')->where("token = '{$this->token}' and wechat_id='{$this->wechat_id}' and expire_date > {$time} and status = 1 and order_base_price <= {$orderPrice}")->select();
		C('DB_PREFIX','weixin_');
		return $coupons;
	}
	
	/**
	 *@ method use_coupon 使用优惠券
	 */
	private function use_coupon($couponId){
		M('member_coupon_usage','sp_')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'id'=>$couponId])->setField('status',2);//状态设置为2，表示已经使用
	}
	
	private function check_coupon($couponId,$orderPrice){
		C('DB_PREFIX','sp_');
		$time = time()-24*3600;
		$coupon = M('member_coupon_view')->field('id,status,title,expire_date,discount_money,order_base_price,sncode')->where("token = '{$this->token}' and wechat_id='{$this->wechat_id}' and expire_date > {$time} and status = 1 and order_base_price <= {$orderPrice} and id = {$couponId}")->find();
		C('DB_PREFIX','weixin_');
		return $coupon;
	}
	
	/**
	 * @method check_goods_stock
	 * @desc 检查商品实时库存
	 * @param itemId 商品编号
	 * @return integer 商品库存
	 */
	private function check_goods_stock($itemId){
		return M('item')->field('goods_stock')->find($itemId);
	}
	
	/**
	 *@method _get_transport_fee 
	 *@desc 获取当前订单的运费
	 */
	private function _get_transport_fee(){
		$orderPrice = $this->cart->getPrice();
		$orderNum = $this->cart->getNum();
		$set = $this->get_mall_set();
		if(($orderPrice >= $set['transport_free_line']) && ($orderNum >= $set['transport_free_base_num'])) return 0;
		else{
			$items = $this->cart->getItems();
			$freesum = 0;
			foreach($items as $item){
				if($item['free']== 2){
					$freesum = $set['transport_fee'];
					break;
				}
			}
			return $freesum;
		}
	}
	
	/**
	 *@method _gen_orderid 生成订单号
	 *@desc 订单号=时间戳+毫秒数+随机数
	 */
	private function _gen_orderid(){
		list($usec, $sec) = explode(" ", microtime());
		$orderid = date("ymdHis",$sec);
		$orderid .= substr($usec,2,4);
		$orderid .= rand(10,99);
		return $orderid;
	}
	
	/**
	 *@method 用户提交订单
	 */
	public function pay(){
		if(IS_POST&&count(session('cart'))>0)
		{
			$user_address=M('user_address');
			$item_order=M('item_order');
			$order_detail=M('order_detail');
			$item_goods=M('item');
			
			$address_options = I('post.addr_id',0,'intval');//地址 0：刚填的地址 大于0历史的地址
			$postscript = I('post.postscript','','trim');//卖家留言
			$pay_type = I('post.pay_type',0,'intval');//支付方式，1:货到付款，2：微支付，3：支付宝
			$coupon_id = I('post.coupon_id',0,'intval');
			if(!empty($postscript)){
				$data['note']=$postscript;
			}
			$data['freetype'] = 2;
			$data['goods_sumPrice'] = $this->cart->getPrice();//商品总额
			$data['freeprice'] = $this->_get_transport_fee();//获取运费
			$data['order_sumPrice'] = $data['goods_sumPrice'] + $data['freeprice'];
			
			//处理优惠券
			if($coupon_id !=0){
				$coupon = $this->check_coupon($coupon_id,$data['order_sumPrice']);
				if($coupon == null) {
					$this->ajaxReturn(1,'所选优惠券不可用！');
					exit;
				}
				else{
					$data['sncode'] = $coupon['sncode'];
					$data['discount'] = $coupon['discount_money'];
					$data['order_sumPrice'] = $data['order_sumPrice'] - $data['discount'];
					if($data['order_sumPrice'] < 0)$data['order_sumPrice'] = 0; //订单金额至少为0
				}
			}
			
			//生成订单号
			$dingdanhao = $this->_gen_orderid();
			$data['orderId'] = $dingdanhao;//订单号
			$data['add_time'] = time();//添加时间
			
			//订单标识
			$data['token'] = $this->token;
			$data['wechat_id'] = $this->wechat_id;
			//支付方式
			$data['supportmetho'] = $pay_type;
            $data['deliver_day'] = I('post.deliver_day','','trim');
			$data['client_ip'] =  $_SERVER['REMOTE_ADDR'];//IP地址
			if($address_options==0){
                $consignee = I('post.consignee','','trim');//真实姓名
                $sheng = I('post.sheng','','trim');//省
                $shi = I('post.shi','','trim');//市
                $qu = I('post.qu','','trim');//区
                $address = I('post.address','','trim');//详细地址
                $mobile = I('post.mobile','','trim');//电话号码
                $save_address = 1;//是否保存地址
				
                $data['address_name'] = $consignee;//收货人姓名
                $data['mobile'] = $mobile;//电话号码
                $data['address'] = $sheng.$shi.$qu.$address;//地址
              
				if($save_address)//保存地址
				{
					$add_address['token'] = $this->token;
					$add_address['wechat_id'] = $this->wechat_id;
					$add_address['consignee']=$consignee;
					$add_address['address']=$address;
					$add_address['mobile']=$mobile;
					$add_address['sheng']=$sheng;
					$add_address['shi']=$shi;
					$add_address['qu']=$qu;
					$user_address->data($add_address)->add();
				}
			}
			else{
				$address = $user_address->where(["token"=>$this->token,"wechat_id"=>$this->wechat_id])->find($address_options);//取到地址
				$data['address_name'] = $address['consignee'];//收货人姓名
				$data['mobile'] = $address['mobile'];//电话号码
				$data['address'] = $address['sheng'].$address['shi'].$address['qu'].$address['address'];//地址
			}
			//支持高并发操作，先减少商品成功后再增加订单
			$order_details = array();
			$order_details = array();
			foreach ($this->cart->cart as $item ){
				if($item['spec'] == ''){
					$row_num = $item_goods->where(['id'=>$item['id'],'goods_stock'=>['egt',$item['num']]])->setDec('goods_stock',$item['num']);
				}
				else{//增加对规格的处理
					$specs = json_decode($item_goods->where(['id'=>$item['id']])->field('specs')->find()['specs']);
					foreach($specs as $key => $spec){
						if($spec->name == $item['spec']){
							$specs[$key]->stock = intval($spec->stock) - $item['num'];
						}
					}
					$specs_json = json_encode($specs);
					$row_num = $item_goods->where(['id'=>$item['id']])->setField('specs',$specs_json);
				}
				if($row_num != 0){
					$orderItem = array();
					$orderItem['orderId']=$dingdanhao;
					$orderItem['itemId'] = $item['id'];//商品ID
					$orderItem['title'] = $item['title'];//商品名称
					$orderItem['img'] = $item['img'];//商品图片
					$orderItem['price'] = $item['price'];//商品价格 
					$orderItem['oprice'] = $item['oprice'];//商品优惠价格 
					$orderItem['quantity'] = $item['num'];//购买数量
					$orderItem['spec'] = $item['spec'];//规格
					$orderItem['item_detail_id'] = $order_detail->data($orderItem)->add();
					$order_details[] = $orderItem;
				}
				else{ //库存不足，退出订单
					$this->_cancel_order_detail($order_details);
					$this->ajaxReturn(1,'下单失败，商品库存不足！');
					exit;
				}
			}
			//下单
			$orderid=$item_order->data($data)->add();
			if(!$orderid) {
				$this->_cancel_order_detail($order_details);
				$this->ajaxReturn(1,'生成订单失败！');
				exit;
			}
			//下单成功后处理优惠券
			if($coupon_id !=0){
				$this->use_coupon($coupon_id);
			}
			$this->cart->clear();//清空购物车
			$this->assign('orderid',$orderid);//订单ID
			$this->assign('dingdanhao',$dingdanhao);//订单号
			$this->assign('order_sumPrice',$data['order_sumPrice']);	
			echo $this->jump2Pay($data,$order_details);
		}
		else if(isset($_GET['orderId'])){
			$item_order=M('item_order');
			$orderId=$_GET['orderId'];//订单号
			$orders=$item_order->where(" token='".$this->token."' and wechat_id='".$this->wechat_id."' and orderId='".$orderId."'")->find();
			if(!is_array($orders)) $this->_404();
			$order_details = M('order_detail')->where(['token'=>$this->token,'orderId'=>$orderid])->select();
			$ret = json_decode($this->jump2Pay($orders,$order_details));
			redirect($ret->url);
		}
		else{
			$this->redirect('User/index?token='.$this->token);
		}
	}
	
	/**
	 *@method increase
	 */
	private function increase_sale_count($details){
		$model = M('item');
		foreach($details as $detail){
			$model->where(['id'=>$detail['itemId']])->setInc('buy_num',$detail['quantity']);//增加销售量
		}
	}
	
	/**
	 *@method _cancel_order_detail 下单失败时删除订单商品记录
	 */
	private function _cancel_order_detail($order_details){
		foreach($order_details as $detail){
			M('order_detail')->delete($detail['item_detail_id']);
		}
	}
	
	/**
	 * @method jump2Pay 根据支付方式跳转到相应页面，0为未选择
	 */
	private function jump2Pay($order,$details){
		if($order['supportmetho'] == 1){ //货到付款
			$data['status'] = 2;//订单设置为已付款
			$data['support_time'] = time();
			if(M('item_order')->where(["token"=>$this->token,"wechat_id"=>$this->wechat_id,"orderId"=>$order['orderId']])->save($data)){
				$this->increase_sale_count($details);
				return json_encode(['status'=>0,'message'=>'操作成功！','url'=>U('User/index',['token'=>$this->token,'status'=>2])]);
			}
			else
			{
				return json_encode(['status'=>1,'message'=>'操作失败！']);
			}
		}
		else if($order['supportmetho'] == 2){//微信支付
			return json_encode(['status'=>0,'message'=>'操作成功！','url'=>'/wap/wxpay/index.html?token='.$this->token.'&out_trade_no='.$order['orderId'].'&wechat_id='.$this->wechat_id.'&callback='.urlencode('/mall/index.php?m=User&a=order_list&status=2&token='.$this->token)]);
		}
		else if($order['supportmetho'] == 3){//支付宝支付
			return json_encode(['status'=>0,'message'=>'操作成功！','url'=>'/index.php?m=wap&c=alipay&a=pay&token='.$this->token.'&out_trade_no='.$order['orderId'].'&wechat_id='.$this->wechat_id]);
		}
		else if($order['supportmetho'] == 0){ //代付订单，跳转到代付页面
			return json_encode(['status'=>0,'message'=>'操作成功！','url'=>'/index.php?m=wap&c=mall&a=create_agent_payment&token='.$this->token.'&orderid='.$order['orderId'].'&wechat_id='.$this->wechat_id]);
		}
	}
	
	public function end(){
		if(IS_POST)
		{
			$payment_id=$_POST['payment_id'];
			$orderid=$_POST['orderid'];
			$dingdanhao=$_POST['dingdanhao'];
			$item_order=M('item_order')->where("token='".$this->token."' and wechat_id='".$this->wechat_id."' and orderId=".$dingdanhao)->find();
			!$item_order && $this->_404();
			if($payment_id==2)//货到付款
			{
				$data['status']=2;
				$data['supportmetho']=2;
				$data['support_time']=time();
				if(M('item_order')->where("token='".$this->token."' and wechat_id='".$this->wechat_id."' and orderId=".$dingdanhao)->data($data)->save())
				{
					$this->redirect('User/index?token='.$this->token);
				}
				else 
				{
					$this->error('操作失败!');
				}
			}
			elseif ($payment_id==1) //支付宝
			{
				$data['supportmetho']=1;
				if(M('item_order')->where("token='".$this->token."' and wechat_id='".$this->wechat_id."' and orderId=".$dingdanhao)->data($data)->save())
				{
					$alipay=M('alipay')->find();
					echo "<script>location.href='wapapli/alipayapi.php?WIDseller_email=".$alipay['alipayname']."&WIDout_trade_no=".$dingdanhao."&WIDsubject=".$dingdanhao."&WIDtotal_fee=".$item_order['order_sumPrice']."'</script>";
				}
				else {
					$this->error('操作失败!');
				}
			}
			else 
			{
				$this->error('操作失败!');
			}
		}
	}
	
	/**
	 *@method getFree 获取邮费
	 */
	public function getFree($type=2)
	{
		import('Think.ORG.Cart');
		$cart=new Cart();	
		$money=0;
		$items=M('item');
		
		$method=array(1=>'pingyou',2=>'kuaidi',3=>'ems');
		foreach ($cart->cart as $item)
		{
			$free= $items->field('free,pingyou,kuaidi,ems')->where('free=2')->find($item['id']);
			if(is_array($free))
			{
				$money+=$free[$method[$type]];
			}
		}
		return $money;
	}
	
	/**
	 *@method comment 用户评论
	 */
	public function comment(){
		if(IS_POST){
			$data['token'] = $this->token;
			$data['wechat_id'] = $this->wechat_id;
			$data['order_id'] = $_POST['order_id'];
			$data['info'] = $_POST['content'];
			$data['item_id'] = $_POST['item_id'];
			$item = M('item')->field('title')->find($data['item_id']);
			$order = M('item_order')->field('address_name,mobile')->where(['orderId'=>$data['order_id']])->find();
			
			$data['tel'] = $order['mobile'];
			$data['uname'] = $order['address_name'];
			$data['item_name'] = $item['title'];
            $ret=M('item_comment')->where($data)->find();
            $data['add_time'] = time();
			if(!$ret && M('item_comment')->add($data)){
				echo json_encode(['status'=>0,'message'=>'评论成功！','url'=>__SELF__]);
			}
			else{
				echo json_encode(['status'=>1,'message'=>'评论失败！']);
			}
		}
		else{
			$item_id = $_GET['item_id'];
			$order_id = $_GET['order_id'];
			$item = M('Item')->field('id,title,intro,img,oprice')->find($item_id);
			if($item != null){
				$total_count = M('item_comment')->where(['item_id'=>$item_id])->count();
				$this->assign('total_count',$total_count);
				if($total_count>0){
					$comments = M('item_comment')->where(['item_id'=>$item_id,'wechat_id'=>$this->wechat_id])->order('add_time desc')->select();
					$this->assign('comments',$comments);
				}
				
				foreach($comments as $comment){
					if($comment['order_id'] == $order_id){
						$this->assign('deny_comment',true);
						break;
					}
				}
			}
			$this->assign('item',$item);
			$this->display();
		}
	}
	
	public function checkDelivery(){
		$oid = $_GET['oid'];
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/Open/Query/delivery?oid='.$oid;
		$result = json_decode(file_get_contents($url));
		$this->assign('oid',$oid);
		$this->assign('info',$result->order);
		$this->assign('logs',$result->order->logs->log);
		$this->display();
	}
	
	public function test(){
		$time_start = time();
		$comments = M('item_comment')->field('id,item_id,order_id,wechat_id')->select();
		foreach($comments as $comment){
			$item = M('item')->field('title')->find($comment['item_id']);
			$order = M('item_order')->field('address_name,mobile')->where(['orderId'=>$comment['order_id']])->find();
			M('item_comment')->where(['id'=>$comment['id']])->save(['tel'=>$order['mobile'],'uname'=>$order['address_name'],'item_name'=>$item['title']]);
		}
		$time_end = time();
		echo 'process '.count($comments).',time used '.($time_end-$time_start).'s';
	}
}