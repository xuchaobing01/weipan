<?php
/**
 *@ class JmallController 积分商城逻辑控制类
 *@ author yanqizheng
 *@ create 2014-03-28
 */
namespace User\Controller;
use Spark\Util\Page;
class JmallController extends UserController{
	public $token;
	public $product_model;
	public $product_cat_model;
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index(){		
		$product_model = D('jmall_item');
		$where=array('token'=>session('token'));
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['token'] = session('token'); 
            $map['name|intro|keyword'] = array('like',"%$key%"); 
            $list = $product_model->where($map)->relation(true)->select();
			
            $count      = $product_model->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }
		else{
        	$count      = $product_model->where($where)->count();
			
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
			
        	$list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);	
		$this->assign('list',$list);
		$this->assign('isProductPage',1);
		
		$this->display();
	}
	
	/**
	 *@method add 添加产品
	 */
	public function add(){
		if(IS_POST){
			$_POST['token'] = session('token');
			$_POST['time'] = time();
			$this->all_insert('jmall_item','index');
		}
		else{
			$this->display('set');
		}
	}
	
	/**
	 * 商品类别ajax select
	 *
	 */
	public function ajaxCatOptions(){
		$parentid=intval($_GET['parentid']);
		$data=M('Product_cat');
		$catWhere=array('parentid'=>$parentid,'token'=>session('token'));
		$cats=$data->where($catWhere)->select();
		$str='';
		if ($cats){
			foreach ($cats as $c){
				$str.='<option value="'.$c['id'].'">'.$c['name'].'</option>';
			}
		}
		$this->show($str);
	}
	
	/**
	 *@编辑商品
	 */
	public function set(){
        $id = I('get.id');
        $product_model=M('jmall_item');
        $product_cat_model=M('Product_cat');
		$checkdata = $product_model->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Jmall/add'));
        }
		if(IS_POST){
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$product_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($product_model->create()){
				if($product_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Jmall/index'));
				}
				else{
					$this->error('操作失败');
				}
			}
			else{
				$this->error($product_model->getError());
			}
		}
		else{
			$this->assign('set',$checkdata);
			$this->display();
		}
	}
	
	//商品类别下拉列表
	public function catoptions($cats,$selectedid){
		$str='';
		if ($cats){
			foreach ($cats as $c){
				$selected='';
				if ($c['id']==$selectedid){
					$selected=' selected';
				}
				$str.='<option value="'.$c['id'].'"'.$selected.'>'.$c['name'].'</option>';
			}
		}
		return $str;
	}
	
	/**
	 *@method del 删除商品
	 */
	public function del(){
		$product_model=M('jmall_item');
        $id = I('get.id',0,'intval');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_model->where($wehre)->delete();
            $this->success('操作成功',U('jmall/index'));
        }
	}
	
	public function orders(){
		$order_model=M('jmall_order');
		if (IS_POST){
			if ($_POST['token']!=session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$order_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
						$order_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}
					else {
						$order_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success('操作成功',U('Product/orders',array('token'=>session('token'),'dining'=>$this->isDining)));
		}
		else{
			$where=array('token'=>session('token'));
			$count      = $order_model->where($where)->count();
			$Page       = new Page($count,20);
			$show       = $Page->show();
			$orders=$order_model->where($where)->order('time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach($orders as $key => $value){
				$orders[$key]['item'] = M('jmall_item')->field('name,img')->find($value['item_id']);
			}
			$this->assign('orders',$orders);
			$this->assign('page',$show);
			$this->display();
		}
	}
	
	public function send(){
		$id = I('get.id',0,'intval');
		$ret = M('jmall_order')->where(['id'=>$id])->setField('status',1);
		if($ret)$this->success('发货成功！');
		else $this->error('发货失败！');
	}
	
	public function orderinfo(){
		$this->product_model=M('Product');
		$this->product_cat_model=M('Product_cat');
		$product_cart_model=M('product_cart');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		
		//检查权限
		if (strtolower($thisOrder['token'])!=strtolower(session('token'))){
			exit();
		}
		if (IS_POST){
			if (intval($_POST['sent'])){
				$_POST['handled']=1;
			}
			$product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('sent'=>intval($_POST['sent']),'logistics'=>$_POST['logistics'],'logisticsid'=>$_POST['logisticsid'],'handled'=>1));
			
			$this->success('修改成功',U('Product/orderInfo',array('token'=>session('token'),'id'=>$thisOrder['id'])));
		}
		else {
			//订餐信息
			$product_diningtable_model=M('product_diningtable');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}
			//
			$this->assign('thisOrder',$thisOrder);
			$carts=unserialize($thisOrder['info']);
			$totalFee=0;
			$totalCount=0;
			$products=array();
			$ids=array();
			foreach ($carts as $k=>$c){
				if (is_array($c)){
					$productid=$k;
					$price=$c['price'];
					$count=$c['count'];
					if (!in_array($productid,$ids)){
						array_push($ids,$productid);
					}
					$totalFee+=$price*$count;
					$totalCount+=$count;
				}
			}
			if (count($ids)){
				$list=$this->product_model->where(array('id'=>array('in',$ids)))->select();
			}
			if ($list){
				$i=0;
				foreach ($list as $p){
					$list[$i]['count']=$carts[$p['id']]['count'];
					$i++;
				}
			}
			$this->assign('products',$list);
			//
			$this->assign('totalFee',$totalFee);
			$this->display("orderInfo");
		}
	}
	
	public function deleteOrder(){
		$product_cart_model=M('jmall_order');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id']),'token'=>session('token')))->find();
		//检查权限
		$id=$thisOrder['id'];
		//删除订单和订单列表
		$product_cart_model->where(array('id'=>$id))->delete();
		$this->success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * @商城回复配置
	 */
	public function replyset(){
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$id = I('post.id','','intval');
			D('ReplyInfo')->set('JMall',$data);
			D('Keyword')->set($id,'JMall',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array('token'=>$this->token);
			$setting = D('ReplyInfo')->get('JMall');
			$keyword = D('Keyword')->get($setting['id'],'JMall');
			unset($setting['config']);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display('replyset');
		}
	}
}
?>