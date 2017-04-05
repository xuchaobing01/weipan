<?php
namespace User\Controller;
use Spark\Util\Page;
class VoteController extends UserController{
    public function index(){
		$where['token'] = session('token');
        $list=M('Vote')->where($where)->order('id DESC')->select();
        $count = M('Vote')->where($where)->count();
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->display();
    }

    public function totals(){
        $token      = session('token');
        $id         = I('get.id');
        $t_vote     = M('Vote');
        $t_record   = M('Vote_record');
        $where      = array('id'=>$id,'token'=>session('token'));
        $vote       = $t_vote->where($where)->find();
        if(empty($vote)){
            exit('非法操作');
        }
        $vote_item = M('Vote_item')->where('vid='. $vote['id'])->select();
        $vcount = $t_record->where(array('vid'=>$id))->count();
        $this->assign('count',$vcount);
		
        $xml='<chart borderThickness="0" caption="'.$vote['title'].'" baseFontColor="666666" baseFont="宋体" baseFontSize="14" bgColor="FFFFFF" bgAlpha="0" showBorder="0" bgAngle="360" pieYScale="90"  pieSliceDepth="5" smartLineColor="666666">';
        
        foreach ($vote_item as $k=>$value) {
            $xml.='<set label="'.$value['item'].'" value="'.$value['vcount'].'"/>';
        }            
        $xml.='</chart>';

        $Page     = new Page($vcount,15);

        $record = $t_record->where(array('vid'=>$id))->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach($record as $key=>$value){
            $record[$key]['wxname']     = M('userinfo')->where(array('wechat_id'=>$value['wechat_id']))->getField('wechaname');
            $record[$key]['itemname']   = $this->_getItemName($value['item_id']);
        }

        $this->assign('page',$record);
        $this->assign('page',$Page->show());
        $this->assign('record',$record);
        $this->assign('xml',$xml);
        $this->assign('vote_item', $vote_item);
        $this->assign('vote',$vote);
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


    public function _getItemName($item_id){
        $id     = explode(',', $item_id);
        $name   = '';
        foreach ($id as $key => $value) {
            $name .= M('Vote_item')->where('id='. $value)->getField('item').',';
        }

        return rtrim($name,',');
    }

    public function add(){
		$this->assign('type',I('get.type'));
        if(IS_POST){
            $adds = $_REQUEST['add'];
            if(empty($adds) || empty($_REQUEST['add']['item'][0]) && empty($_REQUEST['add']['startpicurl'][0])){
                $this->error('投票选项你还没有填写');
                exit;
            }
            foreach ($adds as $ke => $value) {
                 foreach ($value as $k => $v) {
                    if($v != "")
                     $item_add[$k][$ke]=$v;
                 }
            }
            $data=D('Vote');
            $_POST['token']=session('token');
            $_POST['type'] = I('get.type');
            $_POST['statdate'] = strtotime(I('post.statdate'));
            $_POST['enddate'] = strtotime(I('post.enddate'));
            $_POST['cknums'] = I('post.cknums');
            $_POST['display'] = intval(I("post.display"));
            $_POST['info'] = strip_tags(I("post.info"));
            $_POST['picurl'] = I("post.picurl");
            $_POST['title'] = I("post.title");
            $_POST['keyword'] = I('post.keyword');

            if($_POST['enddate']<$_POST['statdate']){
                $this->error('结束时间不能小于开始时间!');
            }

            //$isset_keyword = $data->where(array('keyword' => $_POST['keyword'],'token'=>$_POST['token']))->field('keyword')->find();
            //if($isset_keyword != NULL){
             //   $this->error('关键词已经存在！');
              //  exit;
           // }
            $t_item = M('Vote_item');

            if($data->create()!=false){
                if($id=$data->add()){
                    foreach ($item_add as $k => $v) {
                      if($v['item'] != ''){
                        $data2['vid'] = $id;
                        $data2['item']=$v['item'];
                        $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                        $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                        if($_POST['type'] == 'img' || I('get.type') == 'scene'){
                            $data2['startpicurl']=empty($v['startpicurl']) ? "#" : $v['startpicurl'];
                            $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                        }
                        $t_item->add($data2);
                      }

                    }
                    $data1['pid']=$id;
                    $data1['module']='Vote';
                    $data1['token']=session('token');
                    $data1['keyword']=$_POST['keyword'];
                    M('keyword')->add($data1);
                    $this->success('添加成功',U('Vote/index',array('token'=>session('token'))));
                }else{
                    $this->error('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error($data->getError());
            }
        }else{
            $this->display();
        }
    }

    public function del(){
        $type = I('get.type');
        $id = I('get.id');
        $vote = M('Vote');
        $find = array('id'=>$id,'type'=>$type);
        $result = $vote->where($find)->find();
         if($result){
            $vote->where('id='.$result['id'])->delete();
            M('Vote_item')->where('vid='.$result['id'])->delete();
            M('Vote_record')->where('vid='.$result['id'])->delete();
            $where = array('pid'=>$result['id'],'module'=>'Vote','token'=>session('token'));
            M('Keyword')->where($where)->delete();
            $this->success('删除成功',U('Vote/index',array('token'=>session('token'))));
        }
		else{
            $this->error('非法操作！');
        }
    }

    public function setinc(){
        $id = I('get.id');
        $where = array('id'=>$id,'token'=>session('token'));
        $check = M('Vote')->where($where)->find();
        if($check==NULL)$this->error('非法操作');
        if ($check['status']==0){
            $data=M('Vote')->where($where)->save(array('status'=>1));
            $tip='恭喜你,活动已经开始';
        }else {
            $data=M('Vote')->where($where)->save(array('status'=>0));
            $tip='设置成功,活动已经结束';
        }
        if($data!=NULL){
            $this->success($tip);
        }else{
            $this->error('设置失败');
        }

    }
    public function setdes(){
        $id = I('get.id');
        $where = array('id'=>$id,'token'=>session('token'));
        $check = M('Vote')->where($where)->find();
        if($check==NULL)$this->error('非法操作');
        $data=M('Vote')->where($where)->setDec('status');
        if($data!=NULL){
            $this->success('活动已经结束');
        }else{
            $this->error('服务器繁忙,请稍候再试');
        }
    }

    public function edit(){
        $this->assign('type',I('get.type'));
        if(IS_POST){
            $data=M('Vote');
            $_POST['id']= (int)I('get.id');
            $_POST['type'] = I('get.type');
            $_POST['statdate']=strtotime(I('statdate'));
            $_POST['enddate']=strtotime(I('enddate'));
            $_POST['cknums'] = (int)I('cknums');
            $_POST['display'] = I("display");
            $_POST['info'] = strip_tags(I("info"));
            $_POST['picurl'] = I("picurl");
            $_POST['title'] = I("title");
             if($_POST['enddate']<$_POST['statdate']){
                $this->error('结束时间不能小于开始时间!');
                exit;
            }
            $where=array('id'=>$_POST['id'],'token'=>session('token'));
            $check=$data->where($where)->find();
            if($check==NULL) exit($this->error('非法操作'));
            if(empty($_REQUEST['add'])){
                $this->error('投票选项必须填写');
            }
            $t_item = M('Vote_item');
            $datas = $_REQUEST['add'];
            //$datas = array_filter($datas);
             foreach ($datas as $ke => $value) {
                 foreach ($value as $k => $v) {
                    if( $v != ""){
                        $item_add[$k][$ke]=$v;
                    }
                 }
            }

            $isnull =  $t_item->where('vid='.$_POST['id'])->find();

            foreach ($item_add as $k => $v) {
                $a++;
                if($v['item'] !=""){
                    $i_id['id']=$v['id'];
                    if($i_id['id'] != ''){
                        $data2['item']=$v['item'];
                        $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                        $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                        if(I('get.type') == 'img' || I('get.type') == 'scene'){
                            $data2['startpicurl']=$v['startpicurl'];
                            $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                        }
                      $t_item->where(array('id'=>$i_id['id'],'vid'=>$_POST['id']))->save($data2);

                    }else{

                            $data2['vid'] = $_POST['id'];
                            $data2['item']=$v['item'];
                            $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                            $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                            if($_POST['type'] == 'img'){
                                $data2['startpicurl']=empty($v['startpicurl']) ? "#" : $v['startpicurl'];
                                $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                            }
                            $t_item->add($data2);

                    }
                }

            }

            if($data->create()){

                if($data->where($where)->save($_POST)){
                    $data1['pid']=$_POST['id'];
                    $data1['module']='Vote';
                    $data1['token']=session('token');

                    $da['keyword']=trim($_POST['keyword']);
                    $ok = M('keyword')->where($data1)->save($da);
                    $this->success('修改成功!',U('Vote/index',array('token'=>session('token'))));exit;
                }else{
                    //$this->error('没有做任何修改！');exit;
                    $this->success('修改成功',U('Vote/index',array('token'=>session('token'))));exit;
                }
            }else{
                $this->error($data->getError());
            }


        }else{
            $id=(int)I('id');
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Vote');
            $check=$data->where($where)->find();
            if($check==NULL)$this->error('非法操作');
            $vo=$data->where($where)->find();
            $items = M('Vote_item')->where('vid='.$id)->order('rank DESC')->select();
            $this->assign('items',$items);

            $this->assign('vo',$vo);

            $this->display('add');
        }
    }

    public function del_tab(){
         $da['tid']      = strval($this->_post('id'));
         M('Vote_item')->where(array('id'=>$da['tid']))->delete();
         //$arr=array('errno'=>0,'tid'=>$da['tid']);
         //echo json_encode($arr);
         exit;
    }


}



?>