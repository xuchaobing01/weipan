<?php
namespace User\Controller;
use Spark\Util\Page;
class WxcController extends UserController{
    public function index(){
        $where['token'] = session('token');
        $list=M('wxc_active')->where($where)->order('id DESC')->select();
        $count = M('wxc_active')->where($where)->count();
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->display();
    }
    
    public function tongji(){
        $id = I('get.id',0,'intval');
        $list = M('votex_item')->where(['vote_id'=>$id,'token'=>session('token')])->order('vote_num desc,rank asc')->select();
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
        $record_info = M('vote_record')->where(array('token'=>$this->token,'id'=>$id))->find();
        if(M('vote_record')->where(array('id'=>$id))->delete()){
            M('vote_item')->where(array('id'=>array('in',"{$record_info['item_id']}")))->setDec('vcount',1);
            M('vote')->where(array('token'=>$this->token,'id'=>$record_info['vid']))->setDec('count',1);
 
            $this->success('删除成功',U('Vote/index',array('token'=>session('token'))));
        }
    }
    
    public function items(){
        $vid = I('get.vote_id',0,'intval');
        $model =  M('votex_item');
        $count = $model->where(['token'=>session('token'),'vote_id'=>$vid])->count();
        $Page = new Page($count,20);
        $list = $model->where(['token'=>session('token'),'vote_id'=>$vid])->order('istop asc,rank asc,id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        
        $votex=M('Votex')->where(['token'=>session('token'),'id'=>$vid])->find();
        $this->assign('votex',$votex);
        $this->display();
    }
    
    public function add(){
        if(IS_POST){
            $data = M('wxc_active');
            $_POST['token']=session('token');
            $_POST['start_time'] = strtotime(I('post.statdate'));
            $_POST['end_time'] = strtotime(I('post.enddate'));
            $_POST['banner'] = I("post.picurl");
            $_POST['title'] = I("post.title");
        
            if($_POST['end_time']<$_POST['start_time']){
                $this->error('结束时间不能小于开始时间!');
            }
           
            if($data->create()!=false){
                if($id=$data->add()){

                    $this->success('添加成功',U('Wxc/index',array('token'=>session('token'))));
                }
                else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }
            else{
                echo 
                $this->error($data->getError());
            }
        }
        else{
            $this->display();
        }
    }
    
    //虚拟投票
    private function fake_vote($id,$num){
        $model = M('votex_item');
        $count = $model->where(['token'=>session('token'),'vote_id'=>$id])->count();
        if($count > 0){
            $model->where(['vote_id'=>$id])->setInc('vote_num',$num);
            $total = $count*$num;
            M('votex')->where(['id'=>$id])->setInc('view',$total);
            M('votex')->where(['id'=>$id])->setInc('vote',$total);
        }
    }
    
    //添加投票选项
    public function add_item(){
        $voteId = I('get.vote_id',0,'intval');
        if(IS_POST){
            $data = M('Votex_item');
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
        $check = M('votex_item')->field('max(serial_id) as serial')->where(['token'=>session('token'),'vote_id'=>$vid])->find();
        if($check['serial']==null){
            return  1;
        }
        else return intval($check['serial'])+1;
    }
    
    public function item_edit(){
        $id = I('get.id',0,'intval');
        if(IS_POST){
            $data = M('Votex_item');
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
            $item = M('votex_item')->where(['token'=>session('token'),'id'=>$id])->find();
            $this->assign('vo',$item);
            $this->display('add_item');
        }
    }
    //单个添加投票
    private function fake_signvote($id,$vote_id,$num){
        $model = M('votex_item');
        if($num > 0){
            $model->where(['id'=>$id])->setInc('vote_num',$num);
            M('votex')->where(['id'=>$vote_id])->setInc('view',$num);
            M('votex')->where(['id'=>$vote_id])->setInc('vote',$num);
        }
    }
    
    
    //删除投票选项
    public function item_del(){
        $id = I('get.id');
        $vote = M('Votex_item');
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
        $vote = M('wxc_active');
        $find = array('id'=>$id);
        $result = $vote->where($find)->find();
        if($result){
            $vote->where('id='.$result['id'])->delete();
            $this->success('删除成功',U('Wxc/index',array('token'=>session('token'))));
        }
        else{
            $this->error('非法操作！');
        }
    }

    public function edit(){
        if(IS_POST){
            $data=M('wxc_active');
            $_POST['id'] = (int)I('get.id');
            $_POST['start_time']=strtotime(I('statdate'));
            $_POST['end_time']=strtotime(I('enddate'));
          
            $_POST['banner'] = I("picurl");
          
            $_POST['title'] = I("post.title");
        
            
    
            if($_POST['end_time']<$_POST['start_time']){
                $this->error('结束时间不能小于开始时间!');
            }
            $where=array('id'=>$_POST['id'],'token'=>session('token'));
            $check=$data->where($where)->find();
            if($check==NULL) exit($this->error('非法操作'));
            //增加票数
            if($data->create()){
                if($data->where($where)->save($_POST)){
                    $this->success('修改成功!',U('Wxc/index',array('token'=>session('token'))));exit;
                }
                else{
                    $this->success('修改成功',U('Wxc/index',array('token'=>session('token'))));exit;
                }
            }
            else{
                $this->error($data->getError());
            }
        }else{
            $id=(int)I('id');
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('wxc_active');
            $check=$data->where($where)->find();
            if($check==NULL)$this->error('非法操作');
            $vo=$data->where($where)->find();
            $this->assign('vo',$vo);
            $this->display('add');
        }
    }
    public function changetop(){//置顶
        $leave_model = M("Votex_item");
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
        M("Votex_item")->where($where)->setField("istop",1);
        exit(json_encode('1'));
    
    }
    
    public function record(){
        $vid = I('get.vote_id',0,'intval');
        $count = M('votex_record')->where(['vote_id'=>$vid])->count();
        $page = new Page($count,20);
        $records = M('votex_record')->where(['vote_id'=>$vid])->field('distinct wechat_id')->limit($page->firstRow.','.$page->listRows)->select();
        $wxUser = new \User\Model\WechatUserModel($this->token);
        $model = M('votex_record');
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