<?php
namespace User\Controller;
use Spark\Util\Page;
class HotelController extends UserController{
	public function set(){
		if(IS_POST){
            $_POST['token']     = session('token');
            $_POST['keyword']   = I('post.keyword');         
            $_POST['use_wxpay']   = I('post.use_wxpay',0,'intval');         
            $_POST['use_offline_pay']   = I('post.use_offline_pay',0,'intval');         
            $_POST['use_tpl_msg']   = I('post.use_tpl_msg',0,'intval');         
            $_POST['use_sms_note']   = I('post.use_sms_note',0,'intval');         
            $_POST['tpl_msg_id']   = I('post.tpl_msg_id');         
            $data=M('Hotel');
            $where=array('token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false){
				$_POST['token'] = session('token');
				$id = $data->add($_POST);
				if($id){
					$data1['pid']=$id;
					$data1['module']='Hotel';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->add($da);
					$this->success('修改成功',U('Hotel/set'));
				}
				else{
					$this->error('操作失败');
				}
			}
			else if($data->create()){
				if($data->where($where)->save($_POST)){
					$data1['pid']=$check['id'];
					$data1['module']='Hotel';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功',U('Hotel/set'));
				}
				else{
					$this->error('操作失败');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$checkdata = M('Hotel')->where(array('token'=>session('token')))->find();
			$this->assign('set',$checkdata);
			$this->display();
		}
	}

	public function index_del(){
        $id = I('get.id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Host');
            $check=$data->where($where)->find();
            if($check==false)$this->error('非法操作');
            $back=$data->where($wehre)->delete();
            if($back==true){
                M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Host'))->delete();
                $this->success('操作成功',U('Host/index',array('token'=>session('token'))));
            }
			else{
                $this->error('服务器繁忙,请稍后再试',U('Host/index',array('token'=>session('token'))));
            }
        }        
	}

    public function house(){
        $data=M('Hotel_house');
        $hid = I('get.id');
        $count      = $data->where(array('token'=>session('token')))->count();
        $Page       = new Page($count,12);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);        
        $this->assign('li',$li);
        $this->display();
    }
	
    public function houseadd(){
        if(IS_POST){
            $data['name']    = I('post.name');            
            $data['desc']= I('post.desc');
            $data['price']   = I('post.price');
            $data['sale_price'] = I('post.sale_price');
			$data['dining'] = I('post.dining');
			$data['has_wifi'] = I('post.has_wifi');
			$data['has_bathtub'] = I('post.has_bathtub');
			$data['has_window'] = I('post.has_window');
			$data['bed'] = I('post.bed');
			$data['floor'] = I('post.floor');
            $data['image_1']  = I('post.image_1');
            $data['image_2']  = I('post.image_2');
            $data['image_3']  = I('post.image_3');
            $data['image_4']  = I('post.image_4');
            $data['token']   = session('token');
            M('Hotel_house')->data($data)->add();
            $this->success('操作成功',U('house'));
        }
		else{
			$this->display('house-edit');
		}
    }
	
    public function housedit(){
		$id = I('get.id');
		$token = session('token');
		if(IS_POST){
			$data['name']    = I('post.name');
            $data['desc']= I('post.desc');
            $data['price']   = I('post.price');
            $data['sale_price'] = I('post.sale_price');
			$data['dining'] = I('post.dining');
			$data['has_wifi'] = I('post.has_wifi');
			$data['has_bathtub'] = I('post.has_bathtub');
			$data['has_window'] = I('post.has_window');
			$data['bed'] = I('post.bed');
			$data['floor'] = I('post.floor');
			$data['image_1']  = I('post.image_1');
            $data['image_2']  = I('post.image_2');
            $data['image_3']  = I('post.image_3');
            $data['image_4']  = I('post.image_4');
			$where = array('id'=>$id,'token'=>session('token'));                 
			M('Hotel_house')->where($where)->save($data);
			$this->success('操作成功',U('house'));
		}
		else{
			$house = M('Hotel_house')->where(array('id'=>$id,'token'=>$token))->find();
			$this->assign('list',$house);
			$this->display('house-edit');
		}
    }
	
	public function house_del(){
		$id = I('get.id');
        $token = session('token');
		$data = M('Hotel_house')->where(array('id'=>$id,'token'=>$token))->delete();
		if($data==false){
			$this->error('删除失败');
		}
		else{
			$this->success('操作成功');
		}
	}

    public function order(){
        $data = M('Hotel_order_v');
        $count      = $data->where(array('token'=>$_SESSION['token']))->count();
        $ok_count      = $data->where(array('token'=>$_SESSION['token'],'status'=>1))->count();
        $lost_count      = $data->where(array('token'=>$_SESSION['token'],'status'=>2))->count();
        $no_count      = $data->where(array('token'=>$_SESSION['token'],'status'=>3))->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('count',$count);
        $this->assign('ok_count',$ok_count);
        $this->assign('no_count',$no_count);
        $this->assign('lost_count',$lost_count);
        $this->assign('page',$show);        
        $this->assign('li',$li);
        if(IS_POST){
           $da['status'] = 2;
           $id = I('post.id');
           $token = session('token');
           M('Hotel_order_v')->where(array('id'=>$id,'token'=>$token))->save($da);
           $this->success('操作成功',U('order',array('token'=>session('token'))));
        }
		else{
			$this->display();
		}
    }
	
	public function pay(){
		$id = I('get.id');
		$check = M('hotel_order')->where(['token'=>session('token'),'id'=>$id])->find();
		if($check==null){
			$this->error('操作失败！');
		}
		M('hotel_order')->where(['token'=>session('token'),'id'=>$id])->setField(['status'=>2,'pay_time'=>time()]);
		$this->success('操作成功！');
	}
	
	public function cancel(){
		$id = I('get.id');
		$check = M('hotel_order')->where(['token'=>session('token'),'id'=>$id])->find();
		if($check==null){
			$this->error('操作失败！');
		}
		M('hotel_order')->where(['token'=>session('token'),'id'=>$id])->setField(['status'=>0]);
		$this->success('操作成功！');
	}
	
	public function sms(){
		$mobile = '18919693267';
		$content = '@1@=闫其政,@2@=18919693267,@3@=标准间,@4@=350';
		$ret = send_sms(session('token'),$mobile,$content,C('SMS_TPL_HOTEL_NOTIFY'));
		dump($ret);
	}
}
?>