<?php
namespace User\Controller;
use Spark\Util\Page;
class BargainController extends UserController{
    public function index(){
		$where['token'] = session('token');
        $list=M('Bargain')->where($where)->order('id DESC')->select();
        $count = M('Bargain')->where($where)->count();
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->display();
    }
    public function add(){
    	if(IS_POST){
    		$data = M('Bargain');
    		$_POST['token']=session('token');
    		$_POST['statdate'] = strtotime(I('post.statdate'));
    		$_POST['enddate'] = strtotime(I('post.enddate'));
    		$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
    		$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
    		
    		if($_POST['enddate']<$_POST['statdate']){
    			$this->error('结束时间不能小于开始时间!');
    		}
    		if($data->create()!=false){
    			if($id=$data->add()){
    				$this->success('添加成功',U('Bargain/index',array('token'=>session('token'))));
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
    public function del(){
    	$id = I('get.id');
    	$vote = M('Bargain');
    	$find = array('id'=>$id);
    	$result = $vote->where($find)->find();
    	if($result){
    		$vote->where('id='.$result['id'])->delete();
    		M('Votex_item')->where('vote_id='.$result['id'])->delete();
    		M('Votex_record')->where('vote_id='.$result['id'])->delete();
    		$this->success('删除成功',U('Votex/index',array('token'=>session('token'))));
    	}
    	else{
    		$this->error('非法操作！');
    	}
    }
    
    public function edit(){
    	if(IS_POST){
    		$data=M('Bargain');
    		$_POST['id'] = (int)I('get.id');
    		$_POST['statdate']=strtotime(I('statdate'));
    		$_POST['enddate']=strtotime(I('enddate'));
    		$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
    		$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
    		if($_POST['enddate']<$_POST['statdate']){
    			$this->error('结束时间不能小于开始时间!');
    		}
    		$where=array('id'=>$_POST['id'],'token'=>session('token'));
    		$check=$data->where($where)->find();
    		if($check==NULL) exit($this->error('非法操作'));
    		if($data->create()){
    			if($data->where($where)->save($_POST)){
    				$this->success('修改成功!',U('Bargain/index',array('token'=>session('token'))));exit;
    			}
    			else{
    				$this->success('修改成功',U('Bargain/index',array('token'=>session('token'))));exit;
    			}
    		}
    		else{
    			$this->error($data->getError());
    		}
    	}else{
    		$id=(int)I('id');
    		$where=array('id'=>$id,'token'=>session('token'));
    		$data=M('Bargain');
    		$check=$data->where($where)->find();
    		if($check==NULL)$this->error('非法操作');
    		$vo=$data->where($where)->find();
    		$this->assign('vo',$vo);
    		$this->display('add');
    	}
    }
    public function shop(){
    	$id=(int)I('id');
    	$where['token'] = session('token');
    	$where['pid'] = $id;
    	$list=M('Bargain_shop')->where($where)->order('id DESC')->select();
    	$count = M('Bargain_shop')->where($where)->count();
    	$this->assign('count',$count);
    	$this->assign('list',$list);
    	$this->assign('pid',$id);
    	$this->display();  	
    }
    public function addshop(){
    	if(IS_POST){
    		$data = M('Bargain_shop');
    		$_POST['token']=session('token');
    		if(!ceil($_POST['pid'])){
    			$this->error('参数错误,请稍候再试');
    		}
    		if($data->create()!=false){
    			if($id=$data->add()){
    				$this->success('添加成功',U('Bargain/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));
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
    		$this->assign('pid',$_GET['pid']);
    		$this->display();
    	}
    }
    public function editshop(){
    	
    	if(IS_POST){
    		$data=M('Bargain_shop');
    		$_POST['id'] = (int)I('get.id');
    		if($_POST['enddate']<$_POST['statdate']){
    			$this->error('结束时间不能小于开始时间!');
    		}
    		$where=array('id'=>$_POST['id'],'token'=>session('token'));
    		$check=$data->where($where)->find();
    		if($check==NULL) exit($this->error('非法操作'));
    		if($data->create()){
    			if($data->where($where)->save($_POST)){
    				$this->success('修改成功!',U('Bargain/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));exit;
    			}
    			else{
    				$this->success('修改成功',U('Bargain/shop',array('token'=>session('token'),'id'=>$_POST['pid'])));exit;
    			}
    		}
    		else{
    			$this->error($data->getError());
    		}
    	}else{
    		$id=(int)I('id');
    		$where=array('id'=>$id,'token'=>session('token'));
    		$data=M('Bargain_shop');
    		$check=$data->where($where)->find();
    		if($check==NULL)$this->error('非法操作');
    		$vo=$data->where($where)->find();
    		$this->assign('pid',$_GET['pid']);
    		$this->assign('vo',$vo);
    		$this->display('addshop');
    	}	
    }
    public function delshop(){
    	$id = I('get.id');
    	$vote = M('Bargain_shop');
    	$find = array('id'=>$id);
    	$result = $vote->where($find)->find();
    	$pid = $result['pid'];
    	if($result){
    		$vote->where('id='.$result['id'])->delete();
    		$this->success('删除成功',U('Bargain/shop',array('token'=>session('token'),'id'=>$pid)));
    	}
    	else{
    		$this->error('非法操作！');
    	}	
    }
    
    
    
	
	public function tongji(){//统计		
		list($date_start,$date_end) = $this->_date_range();
		$data = $this->_init_data($date_start,$date_end);
		$pid = $_GET['id'];
		$where = ['token'=>$this->token,'pid'=>$pid];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end + 86400];
		//$where['pid']=$_GET['id'];
		$list = M('bargain_tongji')->where($where)->order('id desc')->select();
		$this->assign('list',$list);
		foreach($list as $item){
			$key = $item['month'].$item['day'];
			$data['viewnum'][$key] = intval($item['viewnum']);
			$data['anum'][$key] = intval($item['anum']);
			$data['dnum'][$key] = intval($item['dnum']);		
		}		
		$viewnum = json_encode_array($data['viewnum']);
		$ordernum = json_encode_array($data['anum']);
		$paynum = json_encode_array($data['dnum']);

		
		$this->assign('viewnum',$viewnum);
		$this->assign('anum',$ordernum);
		$this->assign('dnum',$paynum);	
		$this->assign('name',json_encode_array($data['name']));	
		$this->assign('id',$pid);
		$this->display();
		
	}
	
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['name'][$key] = date('m-d',$tmp);
			$data['viewnum'][$key] = 0;
			$data['anum'][$key] = 0;
			$data['dnum'][$key] = 0;
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

	
    public function del_record(){
        $id = I('get.id',0,'intval');
        $record_info = M('Bargain_win')->where(array('token'=>$this->token,'id'=>$id))->find();
        if(M('Bargain_win')->where(array('id'=>$id))->delete()){   
            $this->success('删除成功',U('Bargain/index',array('token'=>session('token'))));
        }
    }
	
	public function items(){
		$vid = I('get.vote_id',0,'intval');
		$model =  M('bargain_item');
		$count = $model->where(['token'=>session('token'),'pid'=>$vid])->count();
		$Page = new Page($count,20);
		$list = $model->where(['token'=>session('token'),'pid'=>$vid])->order('istop asc,id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
		
		/* $votex=M('Votex')->where(['token'=>session('token'),'id'=>$vid])->find();
		$this->assign('votex',$votex); */
		$this->display();
	}
	 
	
	public function record(){
        $vid = I('get.vote_id',0,'intval');
        $count = M('Bargain_win')->where(['pid'=>$vid])->count();
        $page = new Page($count,20);
       // $records = M('Bargain_win')->where(['pid'=>$vid])->limit($page->firstRow.','.$page->listRows)->select();

        $sql ="SELECT w.*,s.shop_title FROM `sp_bargain_win` AS w LEFT JOIN `sp_bargain_item` AS i ON w.`wechat_id`=i.`wechat_id` LEFT JOIN  `sp_bargain_shop` AS s ON i.`shopid`=s.id WHERE w.token= '".
                session('token') ."' limit ".$page->firstRow.",".$page->listRows;
        $records = M('Bargain_win')->query($sql);

        $this->assign('list',$records);
        $this->assign('page',$page->show());
        $this->display();
    }
}
?>