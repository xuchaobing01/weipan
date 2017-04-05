<?php
namespace Shop\Controller;
class CartController extends BaseController {
	public function _initialize() {
        parent::_initialize();
		$this->cart = new \Shop\Logic\Cart();  
    }
	
	/**
	 * @ method check_buy_num 检查用户对某商品的购买数量
	 * @ return int 购买数量
	 */
	private function check_buy_num($item_id){
		$wechat_id = $this->wechat_id;
		$model = M('order_detail');
		$order_record = M('item_order')->field('orderId')->where(['token'=>$this->token,'wechat_id'=>$this->wechat_id,'status'=>['neq',5]])->select();
		
		if(empty($order_record)) return 0;
		else {
			foreach($order_record as $order){
				$order_id_list[] = $order['orderId'];
			}
			$list = M('order_detail')->field('sum(quantity) as buy_num')->where(['orderId'=>['in',$order_id_list],'itemId'=>$item_id])->find();
			$num = intval($list['buy_num']);
			return $num;
		}
	}
	
	/**
	 * @ method check_buy_num 检查某用户是否超出限购
	 * @ return boolean 没超出返回TRUE,否则返回FALSE
	 */
	private function check_buy_limit($item_id,$limit){
		return $this->check_buy_num($item_id) < $limit;
	}
	
	//获取商品信息
	private function _get_item($id){
		$item = M('item')->field('id,title,img,is_rush,rush_time,free,oprice,price,buy_limit,goods_stock')->where(array('id' => $id,'status' =>1,'is_rush'=>1))->find();
		return $item;
	}
	
	// 导入购物车类
    public function index(){
		//dump($this->cart->getItems());exit;
		$this->assign('item',$this->cart->getItems());
		$this->assign('sumPrice',$this->cart->getPrice());
		$this->display();
    }
	
	//抢购商品
    public function rush(){
		$goodId = I('post.goodId',0,'intval');//商品ID
    	$quantity = 1;//购买数量
    	$item = $this->_get_item($goodId);
		$buy_limit = intval($item['buy_limit']);
		$buy_num = -1;//已经购买的次数
		
		if($buy_limit>0){
			$buy_num = $this->check_buy_num($item['id']);
		}
		if($item['rush_time']>time()){
			$data=array('status'=>0,'msg'=>'活动尚未开始！');
		}
    	else if(!is_array($item)){
    		$data=array('status'=>0,'msg'=>'不存在该商品','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
    	}
		else if($buy_num>=$buy_limit){//检查商品限购
			$data=array('status'=>0,'msg'=>'您已经购买过该商品了','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
		}
		else if($buy_limit!=0 && ($buy_num+$quantity)>$buy_limit){
			$data=array('status'=>0,'msg'=>'购买数量超出限制！','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
		}
		elseif($item['goods_stock']<$quantity){
    		$data=array('status'=>0,'msg'=>'库存不足','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
    	}
		else {
			$this->cart->clear();//清除购物车里的其他商品
            $pr = $item['oprice'];
    		$result= $this->cart->addItem($item['id'],$item,$quantity);
    		$data = array('result'=>$result,'status'=>1,'count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice(),'msg'=>'抢购成功！！');
    	}
    	echo json_encode($data);
	}
	
	private function getSpec($specs,$specName){
		foreach($specs as $spec){
			if($spec->name == $specName){
				return $spec;
			}
		}
		return null;
	}
	
	//添加进购物车
    public function add_cart(){
    	$goodId = I('post.goodId',0,'intval');//商品ID
    	$quantity = I('post.quantity',0,'intval');//购买数量
		$specName = I('post.specName',0,'trim'); //规格名称
    	$item = M('item')->field('id,title,img,specs,oprice,free,price,buy_limit,goods_stock')->where('is_rush=0')->find($goodId);
		if($item['specs'] != ''){
			$item['specs'] = json_decode($item['specs']);
			$spec = $this->getSpec($item['specs'],$specName);
			$item['goods_stock'] = $spec->stock;
			$item['oprice'] = $spec->price;
		}
		$buy_limit = intval($item['buy_limit']);
		$buy_num = -1;//已经购买的次数
		if($buy_limit>0){
			$buy_num = $this->check_buy_num($item['id']);
		}
    	if(!is_array($item)){
    		$data=array('status'=>0,'msg'=>'不存在该商品','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
    	}
		else if($buy_num>=$buy_limit){//检查商品限购
			$data=array('status'=>0,'msg'=>'您已经购买过该商品了','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
		}
		else if($buy_limit!=0 && ($buy_num+$quantity)>$buy_limit){
			$data=array('status'=>0,'msg'=>'购买数量超出限制！','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
		}
		elseif($item['goods_stock']<$quantity){
    		$data=array('status'=>0,'msg'=>'没有足够的库存','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
    	}
		elseif($item['specs'] !=null && $specName == ''){
			$data=array('status'=>0,'msg'=>'请选择规格','count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice());
		}
		else {
            $pr = $item['oprice'];
            if(!isset($pr) || $pr == ""){
                $pr = $item['price'];
            }
    		$result= $this->cart->addItem($item['id'],$item,$quantity,$specName);
    		if($result==1){
    			$data = array('result'=>$result,'status'=>1,'count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice(),'msg'=>'该商品已经存在购物车');
    		}
			else{
				$data = array('result'=>$result,'status'=>1,'count'=>$this->cart->getCnt(),'sumPrice'=>$this->cart->getPrice(),'msg'=>'商品已成功添加到购物车');
    		}
    	}
    	echo json_encode($data);
    }
    
	//删除购物车商品
    public function remove_cart_item(){
    	$goodId= I('post.itemId',0,'intval');//商品ID
    	$this->cart->delItem($goodId);
    	$data=array('status'=>1);
    	echo json_encode($data);
    }
    
    public function change_quantity(){
    	$itemId= I('post.itemId',0,'intval');//商品ID
    	$quantity= I('post.quantity',0,'intval');//购买数量
    	$cartItem = $this->cart->getItem($itemId);
    	$item=M('item')->field('goods_stock')->find($itemId);
		if(($cartItem['buy_limit']>0)&&($quantity + $cartItem['buy_num']>$cartItem['buy_limit'])){
			$data=array('status'=>0,'msg'=>'超出购买限制！');
		}
    	elseif($item['goods_stock']<$quantity){
			$data=array('status'=>0,'msg'=>'该商品的库存不足');
    	}
		else {
			$this->cart->modNum($itemId,$quantity);
			$data=array('status'=>1,'item'=>$this->cart->getItem($itemId),'sumPrice'=>$this->cart->getPrice());
    	}
    	
    	echo json_encode($data);
    }
}