<?php
namespace User\Controller;
use Spark\Util\Page;
class DiningController extends UserController{
	public $token;
	
	public function _initialize() {
		parent::_initialize();
		$this->token = session('token');
		$this->cat_model = M('Dining_cat');
		$this->model = D('DiningFood');
	}
	
	public function index(){		
		$catid=intval($_GET['catid']);
		$catid=$catid==''?0:$catid;
		
		$where=array('token'=>session('token'));
		if ($catid){
			$where['catid']=$catid;
		}
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['token'] = session('token');
            $map['name|intro|keyword'] = array('like',"%$key%"); 
            $list = $this->model->where($map)->select(); 
            $count      = $this->model->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }
		else{
        	$count      = $this->model->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $this->model->where($where)->order('sort asc, id desc')->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
        }
		$this->assign('page',$show);	
		$this->assign('list',$list);
		
		$this->display();		
	}

	public function cats(){
		$parentid = intval($_GET['pid']);
		$parentid = $parentid==''?0:$parentid;
		$data=M('Dining_cat');
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
        }
		else{
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
            $_POST['token'] =session('token');
			$this->insert('Dining_cat','cats?parentid='.I('post.parentid',0,'intval'));
		}
		else{
			$parentid=intval($_GET['parentid']);
			$parentid=$parentid==''?0:$parentid;
			$this->assign('parentid',$parentid);
			$this->display('catset');
		}
	}
	
	public function catdel(){
        $id = I('get.id');
        if(IS_GET){
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Dining_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Dining_food');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Dining/cats'));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	if (!$this->isDining){
					$this->success('操作成功',U('Dining/cats',array('parentid'=>$check['parentid'])));
            	}
				else {
            		$this->success('操作成功',U('Dining/cats',array('parentid'=>$check['parentid'])));
            	}
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Dining/cats'));
            }
        }
	}
	
	public function catSet(){
        $id = I('get.id',0,'intval');
		$checkdata = M('Dining_cat')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Dining/catAdd'));
        }
		if(IS_POST){
            $data=D('Dining_cat');
			$_POST['time'] = time();
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false){
				$this->error('非法操作');
			}
			if($data->create()){
				if($data->save()){
					$this->success('修改成功',U('Dining/cats',array('parentid'=>I('post.parentid'))));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{ 
			$this->assign('parentid',$checkdata['parentid']);
			$this->assign('set',$checkdata);
			$this->display('catset');
		}
	}
	
	/**
	 * 商品类别ajax select
	 *
	 */
	public function ajaxCatOptions(){
		$parentid=intval($_GET['parentid']);
		$data=M('Dining_cat');
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
	 *@method add 添加菜品
	 */
	public function add(){ 
		if(IS_POST){
			$_POST['token'] = session('token');
			$model = D('DiningFood');
			if($model->create()){
				$ret = $model->add();
				if($ret == false){
					$this->error('添加失败：'.$model->getError(),U('index'));
				}
				else{
					$this->success('添加成功！',U('index'));
				}
			}
			else{
				$this->error('添加失败！');
			}
		}
		else{
			$data=M('Dining_cat');
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			$cats=$data->where($catWhere)->select();
			if (!$cats){
				$this->error("请先添加分类",U('Dining/catAdd'));
				exit();
			}
			$this->assign('cats',$cats);
			$catsOptions=$this->catOptions($cats,0);
			$this->assign('catsOptions',$catsOptions);
			$this->display('set');
		}
	}
	
	public function set(){
        $id = I('get.id');
		$checkdata = $this->model->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Dining/add'));
        }
		if(IS_POST){
			$_POST['id'] = I('post.id',0,'intval');
			$_POST['time'] = time();
			$_POST['token']=session('token');
            $where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$this->model->where($where)->find();
			if($check==false){
				$this->error('非法操作');
			}
			if($this->model->create()){
				if($this->model->save()){
					$this->success('修改成功',U('Dining/index'));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($this->model->getError());
			}
		}
		else{
			//分类
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			$cats=$this->cat_model->where($catWhere)->select();
			$this->assign('cats',$cats);
			
			$thisCat = $this->cat_model->where(array('id'=>$checkdata['catid']))->find();
			$childCats = $this->cat_model->where(array('parentid'=>$thisCat['parentid']))->select();
			$this->assign('thisCat',$thisCat);
			$this->assign('parentCatid',$thisCat['parentid']);
			$this->assign('childCats',$childCats);
			$this->assign('isUpdate',1);
			$catsOptions=$this->catOptions($cats,$checkdata['catid']);
			$childCatsOptions=$this->catOptions($childCats,$thisCat['id']);
			$this->assign('catsOptions',$catsOptions);
			$this->assign('childCatsOptions',$childCatsOptions);
			$this->assign('set',$checkdata);
			$this->display();
		}
	}
	
	/**
	 *@method del 删除菜品
	 */
	public function del(){
		$product_model=M('DiningFood');
        $id = I('get.id',0,'intval');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_model->delete();
            if($back==true){
                $this->success('操作成功',U('Dining/index'));
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Dining/index'));
            }
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
	 *@method orders 显示订单
	 */
	public function orders(){
		$product_cart_model=M('Dining_cart');
		//处理订单
		if (IS_POST){
			$token = session('token');
			if(count($_POST['id'])>0){
				$ids = implode(',',$_POST['id']);
				$ret = M('dining_order')->where("id in ({$ids}) and token = '{$token}'")->setField('is_handled',1);
				$this->success('操作成功',U('Dining/orders'));
			}
			else{
				$this->error('请选择要处理的订单！',U('Dining/orders'));
			}
		}
		else{
			$order_model = M('Dining_order');
			$where=array('token'=>session('token'));
			$handled = I('handled',2,'intval');
			$keyword = I('keyword','','trim');
			$date = I('date',0,'strtotime');
			if ($handled != 2){
				$where['is_handled'] = $handled;
			}
			if($keyword != ''){
				$where['_string'] = "linkman like '%$keyword%' or cellphone like '%$keyword%'";
			}
			if($date != 0){
				$date1 = $date;
				$date2 = $date+86400;
				$where['_string'] = "create_time > $date1 AND create_time < $date2";
			}
			
			$count      = $order_model->where($where)->count();
			$Page       = new Page($count,20);
			$show       = $Page->show();
			$orders=$order_model->where($where)->order('create_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			
			$unHandledCount = $order_model->where(array('token'=>session('token'),'is_handled'=>0))->count();
			$this->assign('unhandledCount',$unHandledCount);
			$this->assign('orders',$orders);

			$this->assign('page',$show);
			$this->display('orders');
		}
	}
	
	public function orderinfo(){
		$this->product_model=M('Dining_food');
		$this->product_cat_model=M('Dining_cat');
		$order_model=M('DiningOrder');
		$thisOrder=$order_model->where(array('id'=>intval($_GET['id'])))->find();
		
		//检查权限
		if (strtolower($thisOrder['token'])!=strtolower(session('token'))){
			exit();
		}
		if (IS_POST){
			if (intval($_POST['sent'])){
				$_POST['handled']=1;
			}
			$product_cart_model->where(array('id'=>$thisOrder['id']))->save(array('sent'=>intval($_POST['sent']),'logistics'=>$_POST['logistics'],'logisticsid'=>$_POST['logisticsid'],'handled'=>1));
			
			$this->success('修改成功',U('Dining/orderInfo',array('token'=>session('token'),'id'=>$thisOrder['id'])));
		}
		else {
			//订餐信息
			$product_diningtable_model=M('Dining_table');
			if ($thisOrder['tableid']) {
				$thisTable=$product_diningtable_model->where(array('id'=>$thisOrder['tableid']))->find();
				$thisOrder['tableName']=$thisTable['name'];
			}
			//
			$this->assign('thisOrder',$thisOrder);
			$carts=unserialize($thisOrder['detail']);
			$totalFee=0;
			$totalCount=0;
			$products=array();
			$ids=array();
			foreach ($carts as $k=>$c){
				if (is_array($c)){
					$dishesId = $c['id'];
					$price=$c['price'];
					$count=$c['count'];
					$carts[$dishesId] = $c;
					if (!in_array($dishesId,$ids)){
						array_push($ids,$dishesId);
					}
				}
			}
			if (count($ids)){
				$list=$this->product_model->where(array('id'=>array('in',$ids)))->field('id,name,logourl,price')->select();
			}
			if ($list){
				$i=0;
				foreach ($list as $p){
					$list[$i]['count']=$carts[$p['id']]['count'];
					$i++;
				}
			}
			$this->assign('dishesList',$list);
			$this->display();
		}
	}
	
	public function deleteOrder(){
		$product_order=M('Dining_order');
		$id = I('get.id',0,'intval');
		$thisOrder=$product_order->find($id);
		//检查权限
		
		if ($thisOrder['token']!=session('token')){
			$this->error('非法操作！',U('orders'));
		}
		else{
			$product_order->where('id='.$id)->delete();
			$this->success('操作成功',$_SERVER['HTTP_REFERER']);
		}
	}
	
	//桌台管理
	public function tables(){
		$product_diningtable_model=M('Dining_table');
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
			$this->success('操作成功',U('Dining/orders',array('token'=>session('token'))));
		}
		else{
			$where=array('token'=>session('token'));
			if(IS_POST){
				$key = I('post.searchkey');
				if(empty($key)){
					$this->error("关键词不能为空");
				}
				$where['truename|address'] = array('like',"%$key%");
				$orders = $product_cart_model->where($where)->select();
				$count      = $product_cart_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
			}
			else {
				$count      = $product_diningtable_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
				$tables=$product_diningtable_model->where($where)->order('taxis ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}
			$this->assign('tables',$tables);
			$this->assign('page',$show);
			$this->display();
		}
	}
	
	public function tableAdd(){ 
		if(IS_POST){
			$this->insert('Dining_table','tables');
		}
		else{
			$this->display('tableset');
		}
	}
	
	public function tableSet(){
		$product_diningtable_model=M('Dining_table');
        $id = I('get.id');
		$checkdata = $product_diningtable_model->where(array('id'=>$id))->find();
		if(IS_POST){ 
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$product_diningtable_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($product_diningtable_model->create()){
				if($product_diningtable_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Dining/tables'));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->assign('set',$checkdata);
			$this->display();
		}
	}
	
	public function tableDel(){
        $id = I('get.id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $product_diningtable_model=M('Dining_table');
            $check=$product_diningtable_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_diningtable_model->where($wehre)->delete();
            if($back==true){
            	$this->success('操作成功',U('Dining/tables'));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Dining/tables'));
            }
        }        
	}
	
	/**
	 * @微餐饮回复配置
	 */
	public function replySet(){
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$config['yuding'] = $_POST['yuding'];
			$config['waimai'] = $_POST['waimai'];
			$data['config'] = serialize($config);
			$id = I('post.id','','intval');
			D('ReplyInfo')->set('Dining',$data);
			D('Keyword')->set($id,'Dining',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array('token'=>$this->token);
			$setting = D('ReplyInfo')->get('Dining');
			$keyword = D('Keyword')->get($setting['id'],'Dining');
			$config = unserialize($setting['config']);
			if($config === false){
				$setting['yuding'] = 1;
				$setting['waimai'] = 1;
			}
			else{
				$setting['yuding'] = $config['yuding'];
				$setting['waimai'] = $config['waimai'];
			}
			unset($setting['config']);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display('replyset');
		}
	}


    /**
     * @订单短信提醒
     */
    public function sms(){
        $wx=D('wxuser')->where(array('token'=>$this->token))->field("id, need_sms, sms_count, receiver")->select();
        if($wx && count($wx)>0){
            $this->assign("sms", $wx[0]);
        }
        $this->display('sms');
    }

    public function sms_need(){
        $ret = D('wxuser')->where(array('id'=>$_POST['id']))->save(array('need_sms'=>$_POST['need_sms']));
        if($ret){
            echo "1";
        }else{
            echo "0";
        }
    }

    public  function sms_save(){
        $ret = D('wxuser')->where(array('id'=>$_POST['id']))->save(array('receiver'=>$_POST['receiver']));
        if($ret){
            echo "1";
        }else{
            echo "0";
        }
    }
}

?>