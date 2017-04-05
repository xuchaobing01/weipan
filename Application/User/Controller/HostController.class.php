<?php
namespace User\Controller;
use Spark\Util\Page;
class HostController extends UserController{		
	public function index(){
		$data=M('Host');
		$count      = $data->where(array('token'=>$_SESSION['token']))->count();
		$Page       = new Page($count,12);
		$show       = $Page->show();
        $list = $data->where(array('token'=>$_SESSION['token']))->limit($Page->firstRow.','.$Page->listRows)->select(); 
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['token'] = session('token'); 
            $map['keyword|title|tel2|tel'] = array('like',"%$key%"); 
            $list = M('Host')->where($map)->select();
        }
		$this->assign('page',$show);		
		$this->assign('list',$list);
		$this->display();		
	}
	 
	public function set(){
        $id = I('get.id'); 
		$checkdata = M('Host')->where(array('token'=>$_SESSION['token'],'id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有商家记录.您现在可以添加.",U('Host/add',array('token'=>session('token'))));
        }
		if(IS_POST){ 
            $_POST['id']        = I('post.id');
            $_POST['token']     = session('token');
            $_POST['keyword']   = I('post.keyword');            
            $data=D('Host');
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Host';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功',U('Host/index',array('token'=>session('token'))));
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
			$this->assign('set',$checkdata);
			$this->display();
		}
	}
    
	public function add(){ 
        if(IS_POST){   
            $this->all_insert('Host'); 
        }else{
			$this->display('set');
		}
	}

	public function index_del(){
        $id = I('get.id');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Host');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');

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

    public function lists(){
        $data=M('Host_list_add');
        $hid = I('get.id');
        $count      = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->count();
        $Page       = new Page($count,12);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);        
        $this->assign('li',$li);
        $this->display();
    }

    public function listAdd(){
        if(IS_POST){
            $data['hid']      = I('get.id'); 
            if(empty($data['hid'] )){
                $this->error('链接失效!');
                exit;
            }
            $data['type']    = I('post.type');            
            $data['typeinfo']= I('post.typeinfo');
            $data['price']   = I('post.price');
            $data['yhprice'] = I('post.yhprice');
            $data['name']    = I('post.name');
            $data['picurl']  = I('post.picurl');
            $data['url']     = I('post.url');
            $data['info']    = I('post.info');
            $data['token']   = session('token');
            if(empty($data['type']) || 
                empty($data['typeinfo'])||
                empty($data['price'])|| 
                empty($data['yhprice'])|| 
                empty($data['info']) 
                ) {
                $this->error("不能为空.");exit;
            }
            M('Host_list_add')->data($data)->add();
            $this->success('操作成功',U('index'));
        }
		else{
			$this->display('listAdd');
		}
    }

     public function listEdit(){
		$id = I('get.id');
		$token = session('token');
		$list_add = M('Host_list_add')->where(array('id'=>$id,'token'=>$token))->find();
		if(IS_POST){
			$data['type']    = I('post.type');
			$data['typeinfo']= I('post.typeinfo');
			$data['price']   = I('post.price');
			$data['yhprice'] = I('post.yhprice');
			$data['name']    = I('post.name');
			$data['picurl']  = I('post.picurl');
			$data['url']     = I('post.url');
			$data['info']    = I('post.info');                  
			if(empty($data['type']) || 
				empty($data['typeinfo'])||
				empty($data['price'])|| 
				empty($data['yhprice'])|| 
				empty($data['info']) 
				) {
					$this->error("不能为空.");exit;
			}
			$where = array('id'=>$id,'token'=>session('token'));                 
			M('Host_list_add')->where($where)->save($data);
			$this->success('操作成功',U('Host/index'));
		}
		else{
			$this->assign('list',$list_add);
			$this->display('listAdd');
		}
    }
	
	public function list_del(){
		$id = I('get.id');
        $token = session('token');
		$data = M('Host_list_add')->where(array('id'=>$id,'token'=>$token))->delete();
		if($data==false){
			$this->error('删除失败');
		}
		else{
			$this->success('操作成功');
		}
	}

    public function admin(){
        $hid = I('get.id');        
        $data=M('Host_order');
        $count      = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->count();
        $ok_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>1,'hid'=>$hid))->count();
        $lost_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>2,'hid'=>$hid))->count();
        $no_count      = $data->where(array('token'=>$_SESSION['token'],'order_status'=>3,'hid'=>$hid))->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $li = $data->where(array('token'=>$_SESSION['token'],'hid'=>$hid))->limit($Page->firstRow.','.$Page->listRows)->select(); 
        $this->assign('count',$count);
        $this->assign('ok_count',$ok_count);
        $this->assign('no_count',$no_count);
        $this->assign('lost_count',$lost_count);
        $this->assign('page',$show);        
        $this->assign('li',$li);
        if(IS_POST){
           $da['check_in']     = strtotime($this->_post('check_in'));
           $da['order_status'] = $this->_post('status');
           $id = $this->_post('id');
           $hid = $this->_post('hid');
           $token = session('token');
           M('Host_order')->where(array('id'=>$id,'token'=>$token))->save($da);
           $this->success('操作成功',U('Host/admin',array('token'=>session('token'),'id'=>$hid)));
        }
		else{
			$this->display();
		}
    }
}
?>