<?php
namespace User\Controller;
use Spark\Util\Page;
class VotexController extends UserController{
	public $Mvote;
	public $Mitem;
	public $Mrecord;
	
	public function _initialize() {
		parent::_initialize();
		$this->Mvote = M('Votex','sp_','mysql://root:Pinoocle123456@121.40.230.126/weimarket');
		$this->Mitem = M('votex_item','sp_','mysql://root:Pinoocle123456@121.40.230.126/weimarket');
		$this->Mrecord = M('vote_record','sp_','mysql://root:Pinoocle123456@121.40.230.126/weimarket');
	}
	
    public function index(){
		$where['token'] = session('token');
        $list=$this->Mvote->where($where)->order('id DESC')->select();
        $count = $this->Mvote->where($where)->count();
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->display();
    }
	
	public function tongji(){
		$id = I('get.id',0,'intval');
		$list = $this->Mitem->where(['vote_id'=>$id,'token'=>session('token')])->order('vote_num desc,rank asc')->select();
		$rank = 1;
		$rankNum = $list[0]['vote_num'];
		foreach($list as $key => $item){
			if($rankNum != $item['vote_num']){
				$rank ++;
				$rankNum = $item['vote_num'];
			}
			$list[$key]['rank'] = $rank;
		}
		$this->assign('list',$list);
		$this->display();
	}

	
    public function del_record(){
        $id = I('get.id',0,'intval');
        $record_info = $this->Mrecord->where(array('token'=>$this->token,'id'=>$id))->find();
        if($this->Mrecord->where(array('id'=>$id))->delete()){
            $this->Mitem->where(array('id'=>array('in',"{$record_info['item_id']}")))->setDec('vcount',1);
            M('vote')->where(array('token'=>$this->token,'id'=>$record_info['vid']))->setDec('count',1);
 
            $this->success('删除成功',U('Vote/index',array('token'=>session('token'))));
        }
    }
	
	public function items(){
		$vid = I('get.vote_id',0,'intval');
		$model =  $this->Mitem;
		$count = $model->where(['token'=>session('token'),'vote_id'=>$vid])->count();
		$Page = new Page($count,20);
		$list = $model->where(['token'=>session('token'),'vote_id'=>$vid])->order('istop asc,rank asc,id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
		
		$votex=$this->Mvote->where(['token'=>session('token'),'id'=>$vid])->find();
		$this->assign('votex',$votex);
		$this->display();
	}
	
    public function add(){
        if(IS_POST){
            $data = $this->Mvote;
            $_POST['token']=session('token');
            $_POST['start_time'] = strtotime(I('post.statdate'));
            $_POST['end_time'] = strtotime(I('post.enddate'));
            $_POST['detail'] = strip_tags(I("post.info"));
            $_POST['banner'] = I("post.picurl");
            $_POST['banner2'] = I("post.banner2");
            $_POST['title'] = I("post.title");
			$_POST['rename1'] = I("rename1");
            $_POST['rename2'] = I("rename2");
            $_POST['rename3'] = I("rename3");
            $_POST['share_title'] = I("share_title");
            $_POST['share_detail'] = I("share_detail");
            $_POST['share_img'] = I("share_img");
            $_POST['online_sign'] = I("post.online_sign");
			
            if($_POST['end_time']<$_POST['start_time']){
                $this->error('结束时间不能小于开始时间!');
            }
			if($_POST['online_sign'] != 0){
				$_POST['sign_end_time'] = strtotime(I('post.sign_end_time'));
				if($_POST['sign_end_time'] > $_POST['start_time']){
					//$this->error('报名结束时间必须在投票开始之前!');
				}
			}
            if($data->create()!=false){
                if($id=$data->add()){
                    $this->success('添加成功',U('Votex/index',array('token'=>session('token'))));
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
	
	//虚拟投票
	private function fake_vote($id,$num){
		$model = $this->Mitem;
		$count = $model->where(['token'=>session('token'),'vote_id'=>$id])->count();
		if($count > 0){
			$model->where(['vote_id'=>$id])->setInc('vote_num',$num);
			$total = $count*$num;
			$this->Mvote->where(['id'=>$id])->setInc('view',$total);
			$this->Mvote->where(['id'=>$id])->setInc('vote',$total);
		}
	}
	
	//添加投票选项
	public function add_item(){
		$voteId = I('get.vote_id',0,'intval');
		if(IS_POST){
            $data = $this->Mitem;
            $_POST['token']=session('token');
            $_POST['intro'] = strip_tags(I("post.intro"));
            $_POST['img'] = I("post.img");
            $_POST['title'] = I("post.title");
            $_POST['rank'] = I("post.rank");
			$_POST['vote_id'] = $voteId;
			$_POST['serial_id'] = $this->get_serial_id($voteId);
            if($data->create()!=false){
                if($id=$data->add()){
                    $this->success('添加成功',U('Votex/items',array('vote_id'=>$_GET['vote_id'])));
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
	
	private function get_serial_id($vid){
		$check = $this->Mitem->field('max(serial_id) as serial')->where(['token'=>session('token'),'vote_id'=>$vid])->find();
		if($check['serial']==null){
			return  1;
		}
		else return intval($check['serial'])+1;
	}
	
	public function item_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
            $data = $this->Mitem;
            $_POST['intro'] = strip_tags(I("post.intro"));
            $_POST['img'] = I("post.img");
            $_POST['title'] = I("post.title");
            $_POST['rank'] = I("post.rank");
			$_POST['id'] = $id;
            $addnum = ceil(I("post.addnum"));
            unset($_POST['addnum']);
            if($addnum>0){
                $this->fake_signvote($id,$_GET['vid'],$addnum);
                $this->success('票数添加成功',U('Votex/items',array('token'=>session('token'),'vote_id'=>$_GET['vid'])));
            }elseif($data->create()!=false){
                if($id=$data->save()){
                    $this->success('编辑成功',U('Votex/items',array('token'=>session('token'),'vote_id'=>$_GET['vid'])));
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
			$item = $this->Mitem->where(['token'=>session('token'),'id'=>$id])->find();
			$this->assign('vo',$item);
            $this->display('add_item');
        }
	}
    //单个添加投票
    private function fake_signvote($id,$vote_id,$num){
        $model = $this->Mitem;
        if($num > 0){
            $model->where(['id'=>$id])->setInc('vote_num',$num);
            $this->Mvote->where(['id'=>$vote_id])->setInc('view',$num);
            $this->Mvote->where(['id'=>$vote_id])->setInc('vote',$num);
        }
    }
	
	
	//删除投票选项
	public function item_del(){
        $id = I('get.id');
        $vote = $this->Mitem;
        $find = array('id'=>$id);
        $result = $vote->where($find)->find();
        if($result){
            $vote->where('id='.$result['id'])->delete();
            $this->success('删除成功',U('Votex/items',array('token'=>session('token'),'vote_id'=>$_GET['vid'])));
        }
		else{
            $this->error('非法操作！');
        }
    }
	
    public function del(){
        $id = I('get.id');
        $vote = $this->Mvote;
        $find = array('id'=>$id);
        $result = $vote->where($find)->find();
        if($result){
            $vote->where('id='.$result['id'])->delete();
            $this->Mitem->where('vote_id='.$result['id'])->delete();
            $this->Mrecord->where('vote_id='.$result['id'])->delete();
            $this->success('删除成功',U('Votex/index',array('token'=>session('token'))));
        }
		else{
            $this->error('非法操作！');
        }
    }

    public function edit(){
        if(IS_POST){
            $data=$this->Mvote;
            $_POST['id'] = (int)I('get.id');
            $_POST['start_time']=strtotime(I('statdate'));
            $_POST['end_time']=strtotime(I('enddate'));
            $_POST['detail'] = strip_tags(I("info"));
            $_POST['banner'] = I("picurl");
            $_POST['banner2'] = I("post.banner2");
            $_POST['title'] = I("post.title");
			$_POST['rename1'] = I("rename1");
            $_POST['rename2'] = I("rename2");
            $_POST['rename3'] = I("rename3");
            $_POST['share_title'] = I("share_title");
            $_POST['share_detail'] = I("share_detail");
            $_POST['share_img'] = I("share_img");
			$_POST['online_sign'] = I("post.online_sign");

            $_POST['ontelphone'] = I("post.ontelphone");
            $_POST['telname'] = I("post.telname");
            $_POST['msg1'] = I("post.msg1");
            $_POST['reg_sign'] = I("post.reg_sign");
			
			if($_POST['online_sign'] != 0){
				$_POST['sign_end_time'] = strtotime(I('post.sign_end_time'));
				if($_POST['sign_end_time'] > $_POST['start_time']){
					//$this->error('报名结束时间必须在投票开始之前!');
				}
			}
            if($_POST['end_time']<$_POST['start_time']){
                $this->error('结束时间不能小于开始时间!');
            }
            $where=array('id'=>$_POST['id'],'token'=>session('token'));
            $check=$data->where($where)->find();
            if($check==NULL) exit($this->error('非法操作'));
			//增加票数
			$num = I('increment',0,'intval');
			if($num > 0){
				$this->fake_vote($_POST['id'],$num);
			}
            if($data->create()){
                if($data->where($where)->save($_POST)){
                    $this->success('修改成功!',U('Votex/index',array('token'=>session('token'))));exit;
                }
				else{
                    $this->success('修改成功',U('Votex/index',array('token'=>session('token'))));exit;
                }
            }
			else{
                $this->error($data->getError());
            }
        }else{
            $id=(int)I('id');
            $where=array('id'=>$id,'token'=>session('token'));
            $data=$this->Mvote;
            $check=$data->where($where)->find();
            if($check==NULL)$this->error('非法操作');
            $vo=$data->where($where)->find();
            $this->assign('vo',$vo);
            $this->display('add');
        }
    }
    public function changetop(){//置顶
    	$leave_model = $this->Mitem;
    	$id = $_GET['chk_value'];
    	$vote_id= $_GET['cid'];
    	$istop = $leave_model->where(array("id"=>intval($id)))->getField("istop");
    	
    	if($istop == $_GET['istop']){
    		$this->success('重复操作',U('Votex/items',array('token'=>session('token'),'vote_id'=>$vote_id)));
    	}
    	else{
    		$res = $leave_model->where(array("id"=>$id))->setField("istop",$_GET['istop']);
    		if($res){
    			if($_GET['istop']==1)
    				$this->success("审核成功",U('Votex/items',array('token'=>session('token'),'vote_id'=>$vote_id)));
    				
    			else
    				$this->success("取消审核成功",U('Votex/items',array('token'=>session('token'),'vote_id'=>$vote_id)));
    		}
    		else{
    			$this->error("操作失败",U('Votex/items',array('token'=>session('token'),'vote_id'=>$vote_id)));
    		}
    	}
    
    }
      public  function verifyAll(){
        $allid = rtrim($_GET['allid'],',');
        $where['id'] = array('in',$allid);
        $this->Mitem->where($where)->setField("istop",1);
        exit(json_encode('1'));
    
    }
	
	public function record(){
		$vid = I('get.vote_id',0,'intval');
		$count = $this->Mrecord->where(['vote_id'=>$vid])->count();
		$page = new Page($count,20);
		$records = $this->Mrecord->where(['vote_id'=>$vid])->field('distinct wechat_id')->limit($page->firstRow.','.$page->listRows)->select();
		$wxUser = new \User\Model\WechatUserModel($this->token);
		$model = $this->Mrecord;
		$wxConfig = M('wxuser')->where(['token'=>session('token')])->find();
		$wxUser->wxConfig = $wxConfig;
		foreach($records as $key => $item){
			$detail = $wxUser->get($item['wechat_id']);
			$records[$key]['wechat_name'] = $detail['wechat_name'];
			$records[$key]['vote_num'] = $model->where(['wechat_id'=>$item['wechat_id'],'vote_id'=>$vid])->field('sum(1) as vote_num')->find()['vote_num'];
		}
		$this->assign('list',$records);
		$this->assign('page',$page->show());
		$this->display();
	}
}
?>