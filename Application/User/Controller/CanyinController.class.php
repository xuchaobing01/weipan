<?php
namespace User\Controller;
use Spark\Util\Page;
class CanyinController extends UserController{
	public $token;
	
	public function _initialize() {
		parent::_initialize();
		$this->cat_model = M('Canyin_cat');
		$this->model = M('Canyin_food');//D('CanyinFood');
	}
	
	public function index(){
		$catid=intval($_GET['catid']);
		$catid=$catid==''?0:$catid;
		(I('name') && ($where['name'] = ['like','%'.I('name').'%']));
		$where['token']=session('token');
		if ($catid){
			$where['catid']=$catid;
		}
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
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
        	$list = $this->model->where($where)->order('sort asc, id desc')->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
		$this->assign('page',$show);	
		$this->assign('list',$list);
		$catsdata=M('Canyin_cat')->where(array('token'=>session('token')))->select();
        $cats=array();
        foreach($catsdata as $v){
            $cats[$v['id']]=$v['name'];
        }
        $this->assign('cats', $cats);

		$this->display();		
	}

    public function company_list(){
        $branches=M('canyin_company')->where(array('token'=>session('token')))->order('taxis ASC')->select();
        $this->assign('branches', $branches);
        $this->display();
    }

    public function company_edit(){
        $this->company_model=M('canyin_company');
        $token=session('token');
        $where=array('token'=>$token);
        $id = I('get.id',0,'intval');
        if(IS_POST){
            $_POST['token'] = $token;
            if($id ==0){
                $this->insertEx('canyin_company',U('Canyin/company_list'));
            }
            else{
                $check = M('canyin_company')->where(['token'=>$token, 'id'=>$id])->find();
                if($check ==null){
                    $this->error('非法操作！',U('canyin_company'));
                    exit;
                }
                if($this->company_model->create()){
                    if($this->company_model->save()){
                        $this->success('修改成功',U('Canyin/company_list'));
                    }
                    else{
                        $this->error('操作失败');
                    }
                }
                else{
                    $this->error($this->company_model->getError());
                }
            }
        }else{
            if($id != 0){
                $area=M('canyin_area')->select();
                $this->assign('area', $area);
                $shop = M('canyin_company')->where(['token'=>$token, 'id'=>$id])->find();
                $this->assign('set', $shop);
            }
            $this->display();
        }
    }

    public function company_delete(){
        $token=session('token');
        $where=array('token'=>$token,'id'=>intval($_GET['id']));
        $rt=M('canyin_company')->where($where)->delete();
        if($rt==true){
            $this->success( '删除成功', U('Company/index') );
        }else{
            $this->error( '服务器繁忙, 请稍后再试', U('Company/index') );
        }
    }

    public function index2(){
        $catid=intval($_GET['catid']);
        $catid=$catid==''?0:$catid;

        $where=array();
        if ($catid){
            $where['catid']=$catid;
        }
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
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
            $list = $this->model->where($where)->order('sort asc, id desc')->limit($Page->firstRow.','.$Page->listRows)
                ->relation(true)->select();
        }
        $this->assign('page',$show);
        $this->assign('list',$list);

        $this->display();
    }

	public function cats(){
		$parentid = intval($_GET['pid']);
		$parentid = $parentid==''?0:$parentid;
        $token=session('token');
		$data=M('Canyin_cat');
		$where=array('parentid'=>$parentid, 'token'=>$token);
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['name|des'] = array('like',"%$key%");
            $map['token']=$token;
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
            $_POST['token']=session('token');
            $_POST['time'] = time();
			$this->insert('Canyin_cat','cats?parentid='.I('post.parentid',0,'intval'));
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
            $where=array('id'=>$id);
            $data=M('Canyin_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Canyin_food');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Canyin/cats'));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	if (!$this->isCanyin){
					$this->success('操作成功',U('Canyin/cats',array('parentid'=>$check['parentid'])));
            	}
				else {
            		$this->success('操作成功',U('Canyin/cats',array('parentid'=>$check['parentid'])));
            	}
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Canyin/cats'));
            }
        }
	}
	
	public function catSet(){
        $id = I('get.id',0,'intval');
        $token=session('token');
		$checkdata = M('Canyin_cat')->where(array('token'=>$token, 'id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('Canyin/catAdd'));
        }
		if(IS_POST){
            $data=D('Canyin_cat');
			$_POST['time'] = time();
            $_POST['token']=$token;
            $where=array('id'=>I('post.id'));
			$check=$data->where($where)->find();
			if($check==false){
				$this->error('非法操作');
			}
			if($data->create()){
				if($data->save()){
					$this->success('修改成功',U('Canyin/cats',array('parentid'=>I('post.parentid'))));
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

    public function tplt_set(){
        $token=session('token');
        $checkdata = M('Canyin_tplt')->where(array('token'=>$token))->find();

        if(IS_POST){
            $data=D('Canyin_tplt');
            $_POST['token']=$token;
            $where=array('token'=>$token);
            $check=$data->where($where)->find();
            if($check==false){
               $data->add($_POST);
            }else{
                $data->where($where)->save($_POST);
            }
            $this->success('修改成功',U('Canyin/tplt_set',array('parentid'=>I('post.parentid'))));
        }else{
            $this->assign('parentid',$checkdata['parentid']);
            $this->assign('set',$checkdata);
            $this->display('');
        }
    }
	/**
	 * 商品类别ajax select
	 *
	 */
	public function ajaxCatOptions(){
		$parentid=intval($_GET['parentid']);
		$data=M('Canyin_cat');
		$catWhere=array('parentid'=>$parentid);
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
			$model = M('Canyin_food');
            $_POST['time'] = time();
            $_POST['token']=session('token');
			if($model->create()){
				$ret = $model->add();

                $company=M('company')->where(['token'=>session('token')])->select();
                $id=$ret;
                foreach($company as $i => $v){
                    $a=array('company_id'=>$v['id'], 'dining_food_id'=>$id);
                    if(!M('Canyin_stock')->where($a)->find()){
                        M('Canyin_stock')->add(array('company_id'=>$v['id'], 'dining_food_id'=>$id, 'stock'=>$_POST['stock']));
                    }
                    else{
                        M('Canyin_stock')->where($a)->setField('stock', $_POST['stock']);
                    }
                }
                // 更新版块
                $sections=$_POST['sections'];
                foreach($sections as $i=>$v){
                    $a=array('dining_food_id'=>$id, 'Canyin_section_id'=>$v);
                    if(!M('Canyin_food_section_relation')->where($a)->find()){
                        M('Canyin_food_section_relation')->where($a)->add($a);
                    }
                }

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
            $section=M('Canyin_section')->select();
            $this->assign('section', $section);

			$data=M('Canyin_cat');
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			$cats=$data->where($catWhere)->select();
			if (!$cats){
				$this->error("请先添加分类",U('Canyin/catAdd'));
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
            $this->error("没有相应记录.您现在可以添加.",U('Canyin/add'));
        }
		if(IS_POST){
            $id = I('post.id',0,'intval');
            $token=session('token');
			$_POST['id'] = $id;
			$_POST['time'] = time();
            $_POST['token']=$token;
            $where=array('id'=>$_POST['id'], 'token'=>$token);
			$check=$this->model->where($where)->find();
			if($check==false){
				$this->error('非法操作');
			}
            // 更新库存
            $company=M('canyin_company')->where(array('token'=>$token))->select();
            foreach($company as $i => $v){
                $a=array('company_id'=>$v['id'], 'dining_food_id'=>$id);
                if(!M('Canyin_stock')->where($a)->find()){
                    $s_data=array('company_id'=>$v['id'], 'dining_food_id'=>$id, 'stock'=>$_POST['stock']);
                    M('Canyin_stock')->add($s_data);

                }
                else{
                    $ret=M('Canyin_stock')->where($a)->setField('stock', $_POST['stock']);

                }
            }
            // 更新版块
            $sections=$_POST['sections'];
            foreach($sections as $i=>$v){
                $a=array('dining_food_id'=>$id, 'dining_section_id'=>$v);
                if(!M('Canyin_food_section_relation')->where($a)->find()){
                    M('Canyin_food_section_relation')->where($a)->add($a);
                }
            }
			if($this->model->create()){
                $this->model->save();
                $this->success('修改成功',U('Canyin/index'));

			}else{
				$this->error($this->model->getError());
			}
		}
		else{
			//分类
			$catWhere=array('parentid'=>0,'token'=>session('token'));
			$cats=$this->cat_model->where($catWhere)->select();
			$this->assign('cats',$cats);

            // 版块
            $section=M('Canyin_section')->select();
            $this->assign('section', $section);
            $sections=M('Canyin_food_section_relation')->where(array('dining_food_id'=>$id))->select();
            $this->assign('sections', $sections);
			
			$thisCat = $this->cat_model->where(array('id'=>$checkdata['catid']))->find();
			$childCats = $this->cat_model->where(array('parentid'=>$thisCat['parentid']))->select();
			$this->assign('thisCat',$thisCat);
			$this->assign('parentCatid', $thisCat['parentid']);
			$this->assign('childCats', $childCats);
			$this->assign('isUpdate',1);
			$catsOptions=$this->catOptions($cats, $checkdata['catid']);
			$childCatsOptions=$this->catOptions($childCats, $thisCat['id']);
			$this->assign('catsOptions', $catsOptions);
			$this->assign('childCatsOptions', $childCatsOptions);
			$this->assign('set',$checkdata);
			$this->display();
		}
	}

    // 修改分店库存
    public function setStock(){
        if(IS_POST){
            M('Canyin_stock')->where(array('dining_food_id'=>$_GET['id'], 'company_id'=>$_SESSION['company']['id']))
                ->setField('stock', $_POST['stock']);
        }
        $s=M('Canyin_stock')->where(array('dining_food_id'=>$_GET['id'], 'company_id'=>$_SESSION['company']['id']))->find();
        $this->assign('stock', $s['stock']);
        $this->display();
    }

    public function section_relation_delete(){
        if(IS_POST){
            $ret=M('Canyin_food_section_relation')->
                where(array('dining_food_id'=>$_POST['dining_food_id'], 'dining_section_id'=>$_POST['secId']))
                ->delete();
            echo $ret;
        }
    }

    public function stockSet(){
        $id = I('get.id');
        $token=session('token');
        if(IS_POST){
            $cids=$_POST['cids'];
            $stocks=$_POST['stocks'];
            foreach($cids as $i=>$v){
                M('Canyin_stock')->where(array('dining_food_id'=>$id, 'company_id'=>$v))->setField('stock', $stocks[$i]);
            }
            $this->success('保存成功！');
        }else{
            $sql="select s.company_id, c.name, s.stock from sp_canyin_stock as s, sp_canyin_company as c where s.dining_food_id='".
                $id."' and s.company_id = c.id and c.token='$token'";
            $stock=M('Canyin_stock')->query($sql);
            $this->assign('stock', $stock);
            $food=M('canyin_food')->where(array('id'=>$id))->find();
            $this->assign('food', $food);
        }

        $this->display('stockSet');
    }
	
	/**
	 *@method del 删除菜品
	 */
	public function del(){
		$product_model=M('canyin_food');
        $id = I('get.id',0,'intval');
        if(IS_GET){                              
            $where=array('id'=>$id);
            $check=$product_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_model->delete();
            if($back==true){
                $this->success('操作成功',U('Canyin/index'));
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Canyin/index'));
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
	 *@method orders 显示订单   主账号
	 */
	public function orders(){
        if(session('uid') == false){
            //$this->redirect('User/User/login');
            echo '<script>location.href="/User/User/login.html";</script>';
            exit;
        }
		$product_cart_model=M('Canyin_cart');
        $token=session('token');
		//处理订单
		if (IS_POST){
			if(count($_POST['id'])>0){
				$ids = implode(',',$_POST['id']);
				$ret = M('Canyin_order')->where("id in ({$ids})")->setField('state',$_POST['state']);
				$this->success('操作成功',U('Canyin/orders'));
			}
			else{
				$this->error('请选择要处理的订单！',U('Canyin/orders'));
			}
		}
		else{
			$order_model = M('Canyin_order');
			$where=array('token'=>$token);
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
			$orders=$order_model->where($where)->order('state asc, create_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			
			$unHandledCount = $order_model->where(array('token'=>$token,'state'=>1))->count();
			$this->assign('unhandledCount',$unHandledCount);
			$this->assign('orders',$orders);

            $shops=M('canyin_company')->where(array('token'=>$token))->select();
            $a=array();
            foreach($shops as $v){
                $a[$v['id']]=$v['name'];
            }
            $this->assign('shops',$a);
            $this->assign('orderCount',$count);

			$this->assign('page',$show);
			$this->display('orders');
		}
	}


    /**
     *@method orders 显示订单
     */
    public function orders2(){
        $product_cart_model=M('Canyin_cart');
        //处理订单
        if (IS_POST){
            if(count($_POST['id'])>0){
                $ids = implode(',',$_POST['id']);
                $ret = M('Canyin_order')->where("id in ({$ids})")->setField('state',$_POST['state']);
                $this->success('操作成功');
            }
            else{
                $this->error('请选择要处理的订单！');
            }
        }
        else{
            $order_model = M('Canyin_order');
            $where=array();
            $state = I('state',"");
            $keyword = I('keyword', '', 'trim');
            $date = I('date', 0, 'strtotime');
            $where['shopId']=$_SESSION['company']['id'];
            if ($state != ""){
                $where['state']=$state;
            }
            if($keyword != ''){
                if( $where['_string'] && $where['_string']!=""){
                    $where['_string'] .= " and ";
                }
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
            $sort='state asc, create_time DESC';
            if(I('sort',"", 'trim')!=""){
                $sort=I('sort',"", 'trim');
            }
            $orders=$order_model->where($where)->order($sort)->limit($Page->firstRow.','.$Page->listRows)->select();

            $unHandledCount = $order_model->where(array('state'=>1,'shopId'=>$_SESSION['company']['id']))->count();
            $this->assign('unhandledCount',$unHandledCount);
            $this->assign('orders',$orders);

            $shops=M('company')->select();
            $a=array();
            foreach($shops as $v){
                $a[$v['id']]=$v['name'];
            }
            $this->assign('shops',$a);
            $this->assign('orderCount',$count);
            $this->assign('shopId', $_SESSION['company']['id']);
            $this->assign('companyName', $_SESSION['company']['name']);
            $this->assign('page',$show);
            $this->display('orders2');
        }
    }


    // 获取订单数
    public function getOrderCount(){
        if ($_GET['handled'] && $_GET['handled'] == "0"){
            $_POST['_string']=" (state = '0' or state='1') ";
        }
        $count      = M('Canyin_order')->where($_POST)->count();
        echo '{"orderCount":"'.$count.'"}';
    }


    public function orderInfo(){
		$this->product_model=M('Canyin_food');
		$this->product_cat_model=M('Canyin_cat');
		$order_model=M('CanyinOrder');
		$thisOrder=$order_model->where(array('id'=>intval($_GET['id'])))->find();

		if (IS_POST){
            if(count($_POST['id'])>0){
                $where=array();
                $where['id'] = $_POST['id'];
                $where['token']=session('token');
                $ret = M('Canyin_order')->where($where)->setField('state',$_POST['state']);
                $this->success('操作成功');
            }
            else{
                $this->error('请选择要处理的订单！');
            }
			$this->success('修改成功',U('Canyin/orderInfo',array('id'=>$thisOrder['id'])));
		}
		else {
			//订餐信息
			$product_Canyintable_model=M('Canyin_table');
			if ($thisOrder['tableid']) {
				$thisTable=$product_Canyintable_model->where(array('id'=>$thisOrder['tableid']))->find();
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
			$this->display('orderInfo');
		}
	}
	
	public function deleteOrder(){
		$product_order=M('Canyin_order');
		$id = I('get.id',0,'intval');
		$thisOrder=$product_order->find($id);
		//检查权限

        $product_order->where('id='.$id)->delete();
        $this->success('操作成功',$_SERVER['HTTP_REFERER']);
	}

    // 分店退回订单
    public function backOrder(){
        if($_SESSION['uid']){
            if(IS_POST){
                $ret=M('Canyin_order')->where(array('id'=>I('get.id',0,'intval')))
                    ->setField('shopId', $_POST['shopId']);
                M('Canyin_order')->where(array('id'=>I('get.id',0,'intval')))
                    ->setField('state', 1);
            }
            $this->assign('company', M('company')->select());
            $this->display();

        }
        else{
            if(count($_POST['id'])>0){
                $ids = implode(',',$_POST['id']);
                $ret = M('Canyin_order')->where("id in ({$ids})")->setField('state', 0);
                $this->success('操作成功');
            }
            else{
                $this->error('请选择要处理的订单！');
            }

        }
    }
	
	//桌台管理
	public function tables(){
		$product_Canyintable_model=M('Canyin_table');
		if (IS_POST){
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
			$this->success('操作成功',U('Canyin/orders',array()));
		}
		else{
			$where=array();
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
				$count      = $product_Canyintable_model->where($where)->count();
				$Page       = new Page($count,20);
				$show       = $Page->show();
				$tables=$product_Canyintable_model->where($where)->order('taxis ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
			}
			$this->assign('tables',$tables);
			$this->assign('page',$show);
			$this->display();
		}
	}
	
	public function tableAdd(){ 
		if(IS_POST){
			$this->insert('Canyin_table','tables');
		}
		else{
			$this->display('tableset');
		}
	}
	
	public function tableSet(){
		$product_Canyintable_model=M('Canyin_table');
        $id = I('get.id');
		$checkdata = $product_Canyintable_model->where(array('id'=>$id))->find();
		if(IS_POST){ 
            $where=array('id'=>I('post.id'));
			$check=$product_Canyintable_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($product_Canyintable_model->create()){
				if($product_Canyintable_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Canyin/tables'));
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
            $where=array('id'=>$id);
            $product_Canyintable_model=M('Canyin_table');
            $check=$product_Canyintable_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_Canyintable_model->where($wehre)->delete();
            if($back==true){
            	$this->success('操作成功',U('Canyin/tables'));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Canyin/tables'));
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
			D('ReplyInfo')->set('Canyin',$data);
			D('Keyword')->set($id,'Canyin',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array();
			$setting = D('ReplyInfo')->get('Canyin');
			$keyword = D('Keyword')->get($setting['id'],'Canyin');
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
        $wx=D('wxuser')->where(array())->field("id, need_sms, sms_count, receiver")->select();
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

    public function slideImg(){
        $list=M('Canyin_slideimg')->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function imgSet(){
        $id=I('get.id','');
        if(IS_POST){
            M('Canyin_slideimg')->where(array('id'=>$id))->save($_POST);
            $this->success('保存成功！');
            return;
        }
        $img=M('Canyin_slideimg')->where(array('id'=>$id))->find();
        $this->assign('set', $img);
        $this->display('imgSet');
    }

    // 抢购管理
    public function rushBuy(){
        if(IS_POST){
            $rush=M('Canyin_rushbuy')->find();
            if(!$rush){
                M('Canyin_rushbuy')->add($_POST);
            }else{
                M('Canyin_rushbuy')->where(array('id'=>$rush['id']))->save($_POST);
            }
        }
        $rush=M('Canyin_rushbuy')->find();
        $this->assign('set', $rush);
        $foodList=M("Canyin_food")->select();
        $this->assign('foodList', $foodList);
        $this->display('rushBuy');
    }
    // 团购管理
    public function groupBuy(){
        if(IS_POST){
            $rush=M('Canyin_groupbuy')->find();
            if(!$rush){
                M('Canyin_groupbuy')->add($_POST);
            }else{
                M('Canyin_groupbuy')->where(array('id'=>$rush['id']))->save($_POST);
            }
        }
        $rush=M('Canyin_groupbuy')->find();
        $this->assign('set', $rush);
        $foodList=M("Canyin_food")->select();
        $this->assign('foodList', $foodList);
        $this->display('groupBuy');
    }

    // 会员
    public function members(){
        $users=M('Canyin_user')->order('id desc')->select();
        $this->assign('users', $users);
        $this->display('members');
    }


    /**
     *@method export 预约订单导出到excel
     */
    public function export(){
        ob_clean();
        Vendor('PHPExcel.PHPExcel');
        $data = array();
        $id = I('get.id',0,'intval');
        $where=array('id'=>$id);
        $title=['赤橙黄绿青蓝紫','谁持彩练当空舞'];
        $data[] = $title;
        createExcel('',$data);
    }

}

?>