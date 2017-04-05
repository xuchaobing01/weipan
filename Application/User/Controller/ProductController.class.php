<?php
/**
 *@ class ProductController 商城逻辑控制类
 *@ author yanqizheng
 *@ create 2014-03-28
 */
namespace User\Controller;
use Spark\Util\Page;
class ProductController extends UserController{
	public $token;
	public $product_model;
	public $product_cat_model;
	public $isDining;
	public function _initialize() {
		parent::_initialize();
		$this->isDining=0;
		$this->assign('isDining',$this->isDining);
	}
	
	public function index(){		
		$catid=intval($_GET['catid']);
		$catid=$catid==''?0:$catid;
		$product_model = D('Product');
		$product_cat_model=M('Product_cat');
		$where=array('token'=>session('token'));
		if ($catid){
			$where['catid']=$catid;
		}
		$where['groupon']=array('neq',1);
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
			
        	$list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
        }
		$this->assign('page',$show);	
		$this->assign('list',$list);
		$this->assign('isProductPage',1);
		
		$this->display();
	}
	
	public function cats(){
		$parentid=intval($_GET['parentid']);
		$parentid=$parentid==''?0:$parentid;
		$data=M('Product_cat');
		$where=array('parentid'=>$parentid,'token'=>session('token'));
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['token'] = session('token'); 
            $map['name|des'] = array('like',"%$key%"); 
            $list = $data->where($map)->select(); 
            $count      = $data->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }else{
        	$count      = $data->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        }
		$this->assign('page',$show);
		$this->assign('list',$list);
		if ($parentid){
			$parentCat = $data->where(array('id'=>$parentid))->find();
		}
		$this->assign('parentCat',$parentCat);
		$this->assign('parentid',$parentid);
		$this->display();		
	}
	
	public function catadd(){
		if(IS_POST){
            $_POST['time'] = time();
			$_POST['token'] =session('token');
			if ($this->isDining){
				$this->insert('ProductCat','cats?dining=1&parentid='.I('post.parentid'));
			}
			else {
				$this->insert('ProductCat','cats?parentid='.I('post.parentid'));
			}
		}
		else{
			$parentid=intval($_GET['parentid']);
			$parentid=$parentid==''?0:$parentid;
			$this->assign('parentid',$parentid);
			$this->display('catSet');
		}
	}
	
	public function catdel(){
        $id = I('get.id');
        if(IS_GET){                          
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Product');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Product/cats',array('token'=>session('token'))));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	$this->success('操作成功',U('Product/cats',array('parentid'=>$check['parentid'])));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Product/cats'));
            }
        }
	}
	
	public function catSet(){
        $id = I('get.id');
		$checkdata = M('Product_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Product/catAdd'));
        }
		if(IS_POST){
            $data=D('ProductCat');
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->save()){
					$this->success('修改成功',U('Product/cats',array('parentid'=>I('post.parentid'))));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display("catSet");
		}
	}
	
	/**
	 *@method add 添加产品
	 */
	public function add(){
		if(IS_POST){
			$this->all_insert('Product','index');
		}
		else{
			$data=M('Product_cat');
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			
			$cats=$data->where($catWhere)->select();
			if (!$cats){
				$this->error("请先添加分类",U('Product/catAdd',array('token'=>session('token'),'dining'=>$this->isDining)));
				exit();
			}
			$this->assign('cats',$cats);
			$catsOptions=$this->catOptions($cats,0);
			$this->assign('catsOptions',$catsOptions);
			//
			$this->assign('isProductPage',1);
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
	
	public function set(){
        $id = I('get.id'); 
        $product_model=M('Product');
        $product_cat_model=M('Product_cat');
		$checkdata = $product_model->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Product/add'));
        }
		if(IS_POST){
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$product_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($product_model->create()){
				if($product_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Product/index'));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($product_model->getError());
			}
		}
		else{
			//分类
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			if ($this->isDining){
				$catWhere['dining']=1;
			}
			$cats=$product_cat_model->where($catWhere)->select();
			$this->assign('cats',$cats);
			
			$thisCat=$product_cat_model->where(array('id'=>$checkdata['catid']))->find();
			$childCats=$product_cat_model->where(array('parentid'=>$thisCat['parentid']))->select();
			$this->assign('thisCat',$thisCat);
			$this->assign('parentCatid',$thisCat['parentid']);
			$this->assign('childCats',$childCats);
			$this->assign('isUpdate',1);
			$catsOptions=$this->catOptions($cats,$checkdata['catid']);
			$childCatsOptions=$this->catOptions($childCats,$thisCat['id']);
			$this->assign('catsOptions',$catsOptions);
			$this->assign('childCatsOptions',$childCatsOptions);
			//
			$this->assign('set',$checkdata);
			$this->assign('isProductPage',1);
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
		$product_model=M('Product');
        $id = I('get.id',0,'intval');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_model->where($wehre)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success('操作成功',U('Product/index'));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Product/index'));
            }
        }
	}
	
	public function orders(){
		$product_cart_model=M('product_cart');
		if (IS_POST){
			if ($_POST['token']!=session('token')){
				exit();
			}
			for ($i=0;$i<40;$i++){
				if (isset($_POST['id_'.$i])){
					$thiCartInfo=$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->find();
					if ($thiCartInfo['handled']){
					$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>0));
					}else {
						$product_cart_model->where(array('id'=>intval($_POST['id_'.$i])))->save(array('handled'=>1));
					}
				}
			}
			$this->success('操作成功',U('Product/orders',array('token'=>session('token'),'dining'=>$this->isDining)));
		}
		else{
			$where=array('token'=>session('token'));
			if ($this->isDining){
				$where['dining']=1;
			}
			else {
				$where['dining']=0;
			}
			$where['groupon']=array('neq',1);
			if(IS_POST){
				$key = I('post.searchkey');
				if(empty($key)){
					$this->error("关键词不能为空");
				}

				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->limit($Page->firstRow.','.$Page->listRows)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
			}else {
				if (isset($_GET['handled'])){
					$where['handled']=intval($_GET['handled']);
				}
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
				$orders=$product_cart_model->where($where)->order('time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}
			$unHandledCount=$product_cart_model->where(array('token'=>session('token'),'handled'=>0))->count();
			$this->assign('unhandledCount',$unHandledCount);
			$this->assign('orders',$orders);

			$this->assign('page',$show);
			$this->display();
		}
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
		$product_model=M('product');
		$product_cart_model=M('product_cart');
		$product_cart_list_model=M('product_cart_list');
		$thisOrder=$product_cart_model->where(array('id'=>intval($_GET['id'])))->find();
		//检查权限
		$id=$thisOrder['id'];
		if ($thisOrder['token']!=session('token')){
			exit();
		}
		//删除订单和订单列表
		$product_cart_model->where(array('id'=>$id))->delete();
		$product_cart_list_model->where(array('cartid'=>$id))->delete();
		//商品销量做相应的减少
		$carts=unserialize($thisOrder['info']);
		foreach ($carts as $k=>$c){
			if (is_array($c)){
				$productid=$k;
				$price=$c['price'];
				$count=$c['count'];
				$product_model->where(array('id'=>$k))->setDec('salecount',$c['count']);
			}
		}
		$this->success('操作成功',$_SERVER['HTTP_REFERER']);
	}
	
	
	/**
	 * @商城回复配置
	 */
	public function replyset(){
		if (IS_POST){
			$_id = I('post.id','','intval');
			if($_id){
				M('product_keyword')->where('id='.$_id)->save($_POST);
			}else{
				$_POST['token']=session('token');
				M('product_keyword')->add($_POST);
			}
			$this->success('保存成功！');
		}
		else{	
			$where=array('token'=>session('token'));
			$setting = M('product_keyword')->where($where)->find();	
			$this->assign('set',$setting);
			$this->display('replyset');
		}
	}
	public function adv(){		
		$data=M('Product_adv');
		$where=array('token'=>session('token'));   
        $count      = $data->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
 
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();		
	}
	public function advadd(){
		if(IS_POST){
			$_POST['time'] = time();
			$_POST['token'] =session('token');
			M('Product_adv')->add($_POST);
			$this->success('操作成功',U('Product/adv'));
		}
		else{
			$this->display();
		}
	}
	
	public function advdel(){
		$id = I('get.id');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('product_adv');
			$check=$data->where($where)->find();
			if($check==false)   $this->error('非法操作');
			$back=$data->where($where)->delete();
			if($back==true){
				$this->success('操作成功',U('Product/adv'));
			}else{
				$this->error('服务器繁忙,请稍后再试',U('Product/adv'));
			}
		}
	}
	
	public function advSet(){
		$id = I('get.id');
		$checkdata = M('Product_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
			$this->error("没有相应记录.您现在可以添加.",U('Product/catAdd'));
		}
		if(IS_POST){
			$data=D('ProductCat');
			$where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->save()){
					$this->success('修改成功',U('Product/cats',array('parentid'=>I('post.parentid'))));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display("catSet");
		}
	}
}
?>