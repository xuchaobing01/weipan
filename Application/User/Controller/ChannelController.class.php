<?php
namespace User\Controller;
use Spark\Util\Page;
class ChannelController extends UserController{


    public function index(){

        $user=M('juejin_channel')->where(array('id'=>$_SESSION['channel']['id']))->find();


        $alluser = M('juejin_users')->field('phone,id,shareid')->where(['status'=>0])->select();

        $userinfo=M('juejin_users')->where(array('phone'=>$_SESSION['channel']['phone']))->find();
        $childkey = $this->getChildkey($alluser,$userinfo['phone']);
        $childstr ='';
        foreach ($childkey as $val){
            $childstr .= $val.',';
        }

        $where['userid'] =['in',rtrim($childstr,',')];
        $user['pay'] = M('juejin_payorder')->where(array_merge($where,['status'=>2]))->sum('price');


        $user['win'] = M('juejin_order')->where(array_merge($where,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('get_amount');
        $user['win_amont'] = M('juejin_order')->where(array_merge($where,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('amount');
        $user['las'] = M('juejin_order')->where(array_merge($where,['channelgive'=>0,'is_sim'=>1,'is_win'=>0]))->sum('amount');

        $user['count'] = count($childkey);
        $this->assign('user',$user);
        $this->display();
    }

    public function login() {
        if(IS_POST){
            $username = I('post.username', '');
            $password = md5(I('post.password', ''));
            $check = M('juejin_channel')->where(array('username'=>$username, 'password'=>$password))->find();
            if(!$check){
                $this->ajaxReturn(['statusCode'=>-1]);
            }
            else{

                unset($check['password']);
                $_SESSION['channel']=$check;
                $this->ajaxReturn(['statusCode'=>0]);
            }
        } else {
            $this->display('login');
        }
    }

    // 用户登出
    public function logout() {
        if(session('shop')) {
            unset($_SESSION['shop']);
            redirect(U('User/channel/login'));
        }else {
            $this->error('已经登出！',U('User/channel/login'));
        }
    }
    public function change_pass(){
        if(IS_POST){
            $oldpassword=md5($_POST['oldpassword']);
            $password=md5($_POST['password']);
            $repassword=md5($_POST['repassword']);
            $shops=M('juejin_channel')->where(array('id'=>$_SESSION['channel']['id']))->find();
            if($oldpassword != $shops['password']){
                $this->error('原始密码不正确！');
            }elseif($password!=$repassword){
                $this->error('两次密码不一致！');
            }else{
                $data=$_POST;
                $data['password']=md5($_POST['password']);
                $result=M('juejin_channel')->where(array('id'=>$_SESSION['channel']['id']))->save($data);
                if($result){
                    $this->success('修改密码成功！',U("channel/home"),1);
                }else{
                    $this->error('操作失败！');
                }
            }
        }else{
            $this->display();
        }
    }
    function getChildVal($arr,$pid,$num=0,$search){
        $data = array();
        if($num<11){
            foreach($arr as $key=> $v){
                if(empty($search)){
                    if($v['shareid'] == $pid){
                        $data[$v['phone']] = $v;
                        $data[$v['phone']]['daishu'] = $num;
                        $data = array_merge($data,self::getChildVal($arr,$v['phone'],$num+1,$search));
                    }
                }else{
                    if(($v['shareid'] == $pid)&& ($v['phone']== $search['phone'] || $v['wechat_name']==$search['wechat_name'])){
                        $data[$v['phone']] = $v;
                        $data[$v['phone']]['daishu'] = $num;
                        $data = array_merge($data,self::getChildVal($arr,$v['phone'],$num+1,$search));
                    }
                }

            }
        }
        return $data;
    }
    public function custom(){
        $search = $this->_search();
        $start = I('starttime') ? strtotime(I('starttime')):  time() -3600*365*10 ;
        $end =  I('endtime') ?  strtotime(I('endtime')):time() +3600*365*10 ;
        $where["create_time"] = array(array('gt',$start),array('lt',$end));
        $where['status'] = 0;
        $alluser = M('juejin_users')->where($where)->select();//field('id,phone,shareid')->
        $user=M('juejin_channel')->where(array('phone'=>$_SESSION['channel']['phone']))->find();

        $childlist = $this->getChildVal($alluser,$user['phone'],0,$search);

        $count = count($childlist);
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = array_slice($childlist,$Page->firstRow,$Page->listRows);

        foreach ($list as $key =>$val){
            //  $list[$key]['num']=$this->getChildCount($userlist,$val['id'],0);

            //总充值金额
            $list[$key]['paysum']=M("juejin_payorder")->where(['userid'=>$val['id'],'status'=>2])->sum('price');
        }

        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    public function adduser(){
        if(IS_POST){
            $user=M('juejin_channel')->where(array('phone'=>$_SESSION['channel']['phone']))->find();
            $has=M('juejin_users')->where(array('phone'=>$_POST['phone']))->find();
            if($has){
                $this->error('该用户已存在',U('channel/adduser'));
            }
            if($_POST['shareid']){
                $sharehas = M('juejin_users')->where(array('phone'=>$_POST['shareid']))->find();
                if(empty($sharehas)){
                    $this->error('无填写的推荐人ID',U('channel/adduser'));
                }
            }
            $data['phone']=$_POST['phone'];
            $data['username']=$_POST['username'];
            $data['shareid']=$user["phone"];
            $data['coin']=8888;
            $data['create_time']=time();
            $data['password']=md5($_POST['password']);
            $user=M('juejin_users')->add($data);
            if($user){
                $this->success('添加成功!',U('channel/custom'));
            }else{
                $this->error('操作失败',U('channel/custom'));
            }
        }else{
            $this->display();
        }
    }

    function getChildkey($arr,$pid, $num=0){
        $data = array();
        if($num<11){
            foreach($arr as $key=> $v){
                if($v['shareid'] == $pid){
                    $data[] = $v['id'];
                    $data = array_merge($data,self::getChildkey($arr,$v['phone']));
                }
            }
        }
        return $data;
    }

    public function order(){

        $alluser = M('juejin_users')->field('phone,id,shareid')->where(['status'=>0])->select();

        $user=M('juejin_users')->where(array('phone'=>$_SESSION['channel']['phone']))->find();
        $childkey = $this->getChildkey($alluser,$user['phone']);
        $childstr ='';
        foreach ($childkey as $val){
            $childstr .= $val.',';
        }
        $where = ['is_sim'=>1];

        $where['userid'] =['in',rtrim($childstr,',')];
        $search = $this->_search();
        if($search){
            $user = M('juejin_users')->where($search)->find();
            if(in_array($user['id'],$childkey)){
                $where['userid']=$user['id'];
            }
        }
        $model = M('juejin_order');
        $start = I('starttime') ? strtotime(I('starttime')):  time() -3600*365*10 ;
        $end =  I('endtime') ?  strtotime(I('endtime')):time() +3600*365*10 ;
        $where["createtime"] = array(array('gt',$start),array('lt',$end));
        $count = $model->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();

        foreach ($list as $key=>$val){
            $user = M('juejin_users')->find($val['userid']);
            $list[$key]['wechat_name']=$user['wechat_name'];
            $list[$key]['phone']=$user['phone'];

        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    private function _search(){
        (I('wechat_name') && ($search['wechat_name'] = I('wechat_name')));
        (I('username') && ($search['username'] = I('username')));
        (I('phone') && ($search['phone'] = I('phone')));
        return $search;
    }
    public function xuorder(){

        $alluser = M('juejin_users')->field('phone,id,shareid')->where(['status'=>0])->select();
        $user=M('juejin_users')->where(array('phone'=>$_SESSION['channel']['phone']))->find();
        $childkey = $this->getChildkey($alluser,$user['phone']);

        $childstr ='';
        foreach ($childkey as $val){
            $childstr .= $val.',';
        }
        $where = ['is_sim'=>0];
        $where['userid'] =['in',rtrim($childstr,',')];
        $search = $this->_search();
        if($search){
            $user = M('juejin_users')->where($search)->find();
            if(in_array($user['id'],$childkey)){
                $where['userid']=$user['id'];
            }
        }

        $model = M('juejin_order');
        $start = I('starttime') ? strtotime(I('starttime')):  time() -3600*365*10 ;
        $end =  I('endtime') ?  strtotime(I('endtime')):time() +3600*365*10 ;
        $where["createtime"] = array(array('gt',$start),array('lt',$end));
        $count = $model->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        foreach ($list as $key=>$val){
            $user = M('juejin_users')->find($val['userid']);
            $list[$key]['wechat_name']=$user['wechat_name'];
            $list[$key]['phone']=$user['phone'];
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }

    public function invitelist(){
        $where['phone'] = $_SESSION['channel']['phone'];
        $model = M('juejin_invite_code');
        $count = $model->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }

    public function addinvite(){
        $data = [];
        $data['code']=$this->get_code();
        $data['phone']=$_SESSION['channel']['phone'];
        $data['is_used']=0;
        $data['used_phone']='';
        $data['create_time']=time();
        $ad=M('juejin_invite_code')->add($data);

        if($ad){
            $this->success('添加成功!',U('Channel/invitelist'));
        }else{
            $this->error('操作失败',U('Channel/invitelist'));
        }
    }

    private function get_random($len=3){
        //range 是将10到99列成一个数组
        $numbers = range (10,99);
        //shuffle 将数组顺序随即打乱
        shuffle ($numbers);
        //取值起始位置随机
        $start = mt_rand(1,10);
        //取从指定定位置开始的若干数
        $result = array_slice($numbers,$start,$len);
        $random = "";
        for ($i=0;$i<$len;$i++){
            $random = $random.$result[$i];
        }
        return $random;
    }

    private function get_code(){
        $code = $this->get_random(4);

        $ic = M('juejin_invite_code')->where(['code'=>$code])->find();
        while ($ic != null){
            $code = $this->get_random(4);
            $ic = M('juejin_invite_code')->where(['code'=>$code])->find();
        }
        return $code;
    }


   function changeAgent(){
		if(IS_POST){
			$save['shareid']=I("phone");
			$rs=M('juejin_users')->where(array('id'=>$_POST['id']))->limit(1)->save($save);
			if($rs){
				$this->success('修改成功!',U('Channel/custom'));
			}else{
				$this->error('修改失败!',U('Channel/custom'));
			}

		}else{
			$id=I("id");
            $agent_id=I("agent_id");
			$list = M('juejin_users')->where('shareid='.$_SESSION['channel']['phone'] .' and id!='.$id)->order('id desc')->select();
			$this->assign('list',$list);
			$this->assign('id',$id);
			$this->assign('agent_id',$agent_id);
			$this->display();
		}
    }
}