<?php
namespace User\Controller;
use Spark\Util\Page;
class MallController extends UserController{
	public function set(){
		if(IS_POST){
			$data['shop_title'] = I('post.shop_title','','trim');
			$data['price_rename'] = I('post.price_rename','','trim');
			$data['wxprice_rename'] = I('post.wxprice_rename','','trim');
			$data['order_tip'] = I('post.order_tip','','trim');
			$data['theme_color'] = I('post.theme_color','','trim');
			$data['custom_plate'] = I('post.custom_plate',0,'intval');
			$data['use_coupon'] = I('post.use_coupon',0,'intval');
			$data['use_agent_pay'] = I('post.use_agent_pay',0,'intval');
			$data['after_sale_tip'] = I('post.after_sale_tip','','htmlspecialchars');
			$ret = M('mall_config')->where(['token'=>session('token')])->save($data);
			if($ret) $this->success('更新成功！');
			else $this->error('更新失败！');
		}
		else{
			$set = M('mall_config')->where(['token'=>session('token')])->find();
			if($set == null) $set = $this->_init_set();
			$this->assign('set',$set);
			$this->display();
		}
	}
	
	/**
	 * @method transport_set 物流设置
	 * 
	 */
	public function transport_set(){
		if(IS_POST){
			$data['transport_fee'] = I('post.transport_fee',0,'intval');
			$data['transport_free_line'] = I('post.transport_free_line',0,'intval');
			$data['transport_free_base_num'] = I('post.transport_free_base_num',0,'intval');
			$ret = M('mall_config')->where(['token'=>session('token')])->save($data);
			if($ret) $this->success('更新成功！');
			else $this->error('更新失败！');
		}
		else{
			$set = M('mall_config')->field('transport_fee,transport_free_line,transport_free_base_num')->where(['token'=>session('token')])->find();
			if($set == null) $set = $this->_init_set();
			$this->assign('set',$set);
			$this->display();
		}
	}
	
	private function _init_set(){
		$data['use_coupon'] = 0;
		$data['use_agent_pay'] = 0;
		$data['transport_fee'] = 0;
		$data['transport_free_line'] = 0;
		$data['transport_free_base_num'] = 0;
		$data['token'] = session('token');
		M('mall_config')->data($data)->add();
		return $data;
	}
	
	public function plate(){
		$list = M('mall_plate')->where(['token'=>session('token')])->order('sort desc,id asc')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function plate_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['status'] = I('post.status',0,'intval');
			if($id){
				$_POST['id'] = $id;
				$ret = M('mall_plate')->save($_POST);
				if($ret) $this->success('保存成功！');
				else $this->error('保存失败！');
			}
			else{
				$_POST['token'] = session('token');
				$_POST['display_limit'] = 0;
				$ret = M('mall_plate')->add($_POST);
				if($ret) $this->success('保存成功！');
				else $this->error('保存失败！');
			}
		}
		else{
			if($id){
				$set = M('mall_plate')->find($id);
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function plate_del(){
		$id = I('get.id',0,'intval');
		!$id && $this->error('非法操作！');
		$ret = M('mall_plate')->delete($id);
		if($ret)$this->success('删除成功！');
		else $this->error('删除失败！');
	}

	/**
      @method plate_item
      @desc 栏目商品管理
	*/
	public function plate_item(){
		$plate_id = I('id',0,'intval');
		$items = D('PlateItem')->where(['token'=>session('token'),'plate_id'=>$plate_id])->order('id desc')->relation(true)->select();
		$this->assign('items',$items);
		$this->display();
	}
	
	/**
	 *@method 栏目商品编辑
	 */
	public function plate_item_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$plate_id = I('get.plate_id',0,'intval');
			$ids = $_POST['ids'];
			$custom = I('post.custom',0,'intval');
			if($custom == '1'){
				$data['token'] = session('token');
				$data['plate_id'] = $plate_id;
				$data['item_title'] = trim($_POST['title']);
				$data['item_subtitle'] = trim($_POST['subtitle']);
				$data['item_link'] = trim($_POST['link']);
				$data['item_img'] = trim($_POST['img']);
				$data['is_custom'] = 1;
				if($id){
					$data['id'] = $id;
					M('mall_plate_item')->save($data);
					$this->ajax(0,'编辑成功！');
				}
				else{
					M('mall_plate_item')->add($data);
				}
			}
			else{
				foreach($ids as $id){
					$row = array();
					$row['token'] = session('token');
					$row['plate_id'] = $plate_id;
					$row['is_custom'] = 0;
					$row['item_id'] = $id;
					M('mall_plate_item')->add($row);
				}
			}
			$this->ajax(0,'添加成功！');
		}
		else{
			if($id){
				$set = M('mall_plate_item')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			else{
				$model = M('item','weixin_');
				$where = ['token'=>session('token'),'status'=>1];
				$count= $model->where($where)->count();
				$page=new Page($count,10);
				$items = $model->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
				$this->assign('items',$items);
				$this->assign('page',$page->show());
			}
			$this->display();
		}
	}
	
	public function plate_item_del(){
		$plate_id = I('get.plate_id',0,'intval');
		$id = I('get.id',0,'intval');
		$ret = M('mall_plate_item')->where(['token'=>session('token'),'plate_id'=>$plate_id,'id'=>$id])->delete();
		if($ret){
			$this->ajaxReturn(['errcode'=>0,'errmsg'=>'删除成功！']);
		}
		else $this->ajaxReturn(['errcode'=>1,'errmsg'=>'删除失败！']);
	}
	
	//生成栏目代码
	public function plate_update(){
		$plates = M('mall_plate')->where(['token'=>session('token')])->order('sort desc,id desc')->select();
		foreach($plates as $key => $plate){
			$plates[$key]['items'] = D('PlateItem')->where(['token'=>session('token'),'plate_id'=>$plate['id']])->order('id desc')->relation(true)->select();
		}
		$this->assign('token',session('token'));
		$this->assign('plates',$plates);
		$config = M('mall_config')->field('custom_plate')->where(['token'=>session('token')])->find();
		if($config&&$config['custom_plate'])$tpl = 'plate_tpl_'.$config['custom_plate'];
		else $tpl = 'plate_tpl_1';
		$content = $this->fetch($tpl);
		$check = M('mall_code')->where(['token'=>session('token')])->count();
		if($check ==0){
			M('mall_code')->add(['token'=>session('token'),'plate_code'=>$content]);
		}
		else M('mall_code')->where(['token'=>session('token')])->save(['plate_code'=>$content]);
		$this->ajax(0,'栏目更新成功！');
	}
	
	//商品规格管理
	public function spec(){
		$specs = M('v_mall_spec')->where(['token'=>session('token')])->select();
		$this->assign("list",$specs);
		$this->display();
	}
	
	public function spec_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$data['name'] = I('post.name','','trim');
			$data['cate_id'] = I('post.cate_id',0,'intval');
			if($id){
				$data['id'] = $id;
				$ret = M('mall_spec')->save($data);
				if($ret){
					$this->ajaxReturn(['errcode'=>0,'errmsg'=>'保存成功！']);
				}
				else $this->ajaxReturn(['errcode'=>1,'errmsg'=>'保存失败！']);
			}
			else{
				$data['token'] = session('token');
				$ret = M('mall_spec')->add($data);
				if($ret){
					$this->ajaxReturn(['errcode'=>0,'errmsg'=>'添加成功！']);
				}
				else $this->ajaxReturn(['errcode'=>1,'errmsg'=>'添加失败！']);
			}
		}
		else{
			$cates = M('item_cate','weixin_')->field('id,name')->where(['token'=>session('token')])->select();
			$this->assign('cates',$cates);
			if($id){
				$spec = M('mall_spec')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('spec',$spec);
			}
			$this->display();
		}
	}
	
	public function spec_del(){
		$id = I('get.id',0,'intval');
		$ret = M('mall_spec')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret){
			$this->ajaxReturn(['errcode'=>0,'errmsg'=>'删除成功！']);
		}
		else $this->ajaxReturn(['errcode'=>1,'errmsg'=>'删除失败！']);
	}
	
	//商品管理
	
	public function item_add(){
		$cates = M('item_cate','weixin_')->field('id,name')->where(['token'=>session('token')])->select();
		$this->assign('cates',$cates);
		$this->display('item_add');
	}
	
	//商品编辑
	public function item_set(){
		$id = I('get.id',0,'intval');
		$model = D('item');
		if(IS_POST){
			$data['cate_id'] = intval($_POST['cate_id']);
			$data['title'] = trim($_POST['title']);
			$data['intro'] = trim($_POST['intro']);
			$data['info'] = trim($_POST['info']);
			$data['img'] = trim($_POST['img']);
			$data['price'] = floatval($_POST['price']);
			$data['oprice'] = floatval($_POST['oprice']);
			$data['goods_stock'] = intval($_POST['goods_stock']);
			$data['buy_num'] = intval($_POST['buy_num']);
			$data['ordid'] = intval($_POST['ordid']);
			$data['status'] = I('post.status',0,'intval');
			$specs = array();
			$sku_name_arr = $_POST['sku_name'];
			$sku_price_arr = $_POST['sku_price'];
			$sku_stock_arr = $_POST['sku_stock'];
			foreach($sku_name_arr as $key => $sku_name){
				$specs[] = ['name'=>$sku_name,'price'=>$sku_price_arr[$key],'stock'=>$sku_stock_arr[$key]];
			}
			$item_images = $_POST['item_image'];
			if(is_array($item_images)){
				$data['album'] = implode(';',$item_images);
			}
			else $data['album'] = '';
			
			$data['specs'] = $specs;
			$data['id'] = $id;
			if($model->create($data)){ 
				if($id){
					$ret = $model->update();
					if($ret)$this->ajax(0,'保存成功！');
					else $this->ajax(500,'保存失败！');
				}
				else{
					$ret = $model->publish();
					if($ret)$this->ajax(0,'添加成功！');
					else $this->ajax(500,'添加失败！');
				}
			}
			else $this->ajax(500,$model->getError());
		}
		else{
			if($id){
				$model = new \User\Model\ItemModel();
				$item = $model->where(['token'=>session('token'),'id'=>$id])->relation(true)->find();
				if(!empty($item['album'])){
					$album = explode(';',$item['album']);
					$this->assign('album',$album);
				}
				if(!empty($item['specs'])){
					$spec_list = json_decode($item['specs']);
					$this->assign('spec_list',$spec_list);
				}
				$this->assign('set',$item);
				$cate = array('id'=>$item['cate_id'],'name'=>$item['cate_name']);
			}
			else{
				$cate = M('item_cate','weixin_')->field('id,name')->where(['token'=>session('token'),'id'=>$_GET['cate_id']])->find();
			}
			//获取分类规格
			$spec = M('mall_spec')->where(['token'=>session('token'),'cate_id'=>$cate['id']])->find();
			$this->assign('spec',$spec);
			$this->assign('cate',$cate);
			$this->display();
		}
	}
	
	/**
	 *@desc 商品列表
	 */
	public function item(){
		$title = I('get.title','','trim');
		$model = new \User\Model\ItemModel();
		
		$where = ['token'=>session('token')];
		if($title != ''){
			$where['title'] = ['like','%'.$title.'%'];
		}
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,20);
		
		$list = $model->where($where)->order('ordid desc,add_time desc')->relation(true)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('title',$title);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function item_del(){
		$id = I('get.id',0,'intval');
		$model = new \User\Model\ItemModel();
		$ret = $model->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret)$this->ajax(0,'删除成功！');
		else $this->ajax(500,'删除失败！');
	}
	
	public function reply_set(){
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$id = I('post.id',0,'intval');
			$set = D('ReplyInfo')->where(['token'=>session('token'),'infotype'=>'WeiMall'])->field('id')->find();
			D('ReplyInfo')->set('WeiMall',$data);
			D('Keyword')->set($set['id'],'WeiMall',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array('token'=>$this->token);
			$setting = D('ReplyInfo')->get('WeiMall');
			$keyword = D('Keyword')->get($setting['id'],'WeiMall');
			unset($setting['config']);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display();
		}
	}
}
?>