<?php
/**
 *@ class KtvController 阿波罗
 */
namespace User\Controller;
use Spark\Util\Page;
class KtvController extends UserController{
	public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
	public function index(){
		$list = M('ktv_setting')->where(array('token'=>$this->token))->find();
		if(IS_POST){
			$data=M('ktv_setting');	
			$_POST['createtime']=time();
			$_POST['token']=$this->token;
			if($list){
				$data->where(array('token'=>$this->token))->save($_POST);
				$this->success('活动修改成功',U('Ktv/index'));
			}else{
				if($data->create()!=false){
					if($data->add()){
						$this->success('活动创建成功',U('Ktv/index'));
					}
					else{
						$this->error('服务器繁忙,请稍候再试');
					}
				}
				else{
					$this->error($data->getError());
				}
			}
		}else{	
			$this->assign('vo',$list);
			$this->display();
		}	
	}
	public function add(){
		if(IS_POST){
			$data=M('ktv_home');
			$_POST['time']=time();
			$_POST['token']=$this->token;
			if($data->create()!=false){
				if($data->add()){
					$this->success('添加包间成功',U('Ktv/home'));
				}
				else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$this->display();
		}
	}
	public function edit(){
		if(IS_POST){
			$data=M('ktv_home');
			$_POST['id'] = (int)I('get.id');
		
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
				
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Ktv/home',array('token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Ktv/home',array('token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('ktv_home');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('set',$vo);
			$this->display('add');
		}
	}
	/**
	 *@method del 删除商品
	 */
	public function del(){
		$product_model=M('ktv_home');
		$id = I('get.id',0,'intval');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$check=$product_model->where($where)->find();
			if($check==false){
				$this->error('非法操作');
			}
			$back=$product_model->where($where)->delete();
			if($back==true){
				$this->success('操作成功',U('Ktv/home'));
			}else{
				$this->error('服务器繁忙,请稍后再试',U('Ktv/home'));
			}
		}
	}
	
	public function home(){
	
		$product_model = D('ktv_home');
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
	public function saveOrder(){
		$id=I('get.id');
		$list = M('ktv_order')->where(array('token'=>$this->token,'id'=>$id,'status'=>1))->setInc('status');
		$this->success('操作成功',U('Ktv/order'));
		
	}
	public function order(){
		$order = M('ktv_order');
		$where=array('token'=>session('token'));
		if(IS_POST){
			$key = I('post.searchkey');
			if(empty($key)){
				$this->error("订单号不能为空");
			}
			$map['token'] = session('token');
			$map['orderid'] = array('like',"%$key%");
			$list = $order->where($map)->select();
			$count      = $order->where($map)->count();
			$Page       = new Page($count,20);
			$show       = $Page->show();
		}
		else{
			if (isset($_GET['status'])){
						$where['status']=intval($_GET['status']);
			}
			if(isset($_GET['orderid'])){
				$where['orderid']=intval($_GET['orderid']);
			}
			$count      = $order->where($where)->count();
			$Page       = new Page($count,20);
			$show       = $Page->show();
			$list = $order->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			$pay['nopay'] =  $order->where(array('token'=>session('token'),'status'=>0))->count();
			$pay['pay'] =  $order->where(array('token'=>session('token'),'status'=>1))->count();
			$pay['ypay'] =  $order->where(array('token'=>session('token'),'status'=>2))->count();
		}
		$this->assign('pay',$pay);
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->assign('isProductPage',1);		
		$this->display();
	}
	
	public function member(){
		$where = $this->_search();
		$model = M('Member_card_view');
		$data['uid']=session('uid');
		$count = $model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('getcardtime desc')->select();
		$this->assign('list',$list);
		$this->assign('page',$show);	
		$this->display();
	}
	private function _search(){
		$search['token'] = session('token');
		(I('truename') && ($search['truename'] = ['like','%'.I('truename').'%']));
		(I('number') && ($search['number'] = ['like','%'.I('number').'%']));
		(I('mobile') && ($search['tel'] = ['like','%'.I('mobile').'%']));
		return $search;
	}
	public function tongji(){//统计		
		list($date_start,$date_end) = $this->_date_range();
		$data = $this->_init_data($date_start,$date_end);
		$where = ['token'=>$this->token];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end + 86400];
		//$where['pid']=$_GET['id'];
		$list = M('ktv_tongji')->where($where)->order('id desc')->select();
		$this->assign('list',$list);
		foreach($list as $item){
			$key = $item['month'].$item['day'];
			$data['view'][$key] = intval($item['viewnum']);
			$data['order'][$key] = intval($item['ordernum']);
			$data['pay'][$key] = intval($item['paynum']);
			$data['get'][$key] = intval($item['getnum']);
			
		}		
		$viewnum = json_encode_array($data['view']);
		$ordernum = json_encode_array($data['order']);
		$paynum = json_encode_array($data['pay']);
		$getnum = json_encode_array($data['get']);
		
		$this->assign('viewnum',$viewnum);
		$this->assign('ordernum',$ordernum);
		$this->assign('paynum',$paynum);	
		$this->assign('getnum',$getnum);
		$this->assign('name',json_encode_array($data['name']));
		$this->display();
		
	}
	
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['name'][$key] = date('m-d',$tmp);
			$data['view'][$key] = 0;
			$data['order'][$key] = 0;
			$data['pay'][$key] = 0;
			$data['get'][$key] = 0;
		}
		return $data;
	}
	
	private function _date_range(){
		$daterange = I('daterange');
		if(empty($daterange)) $daterange = 7;
		if(strpos($daterange,'~') > 0){
			$parse = explode('~',$daterange);
			$date_start = strtotime($parse[0]);
			$date_end = strtotime($parse[1]);
		}
		else{
			$today = date('Y-m-d');
			$date_end = strtotime($today) - 86400; //从昨天算起
			$date_start = $date_end - (intval($daterange)-1)*86400;
		}
		$this->assign('range',$daterange);
		return [$date_start,$date_end];
	}
	
	
}
?>