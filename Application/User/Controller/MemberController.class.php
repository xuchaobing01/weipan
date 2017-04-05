<?php
namespace User\Controller;
use Spark\Util\Page;
class MemberController extends UserController{
    //获取支付配置
    private function getConfig($token){
        $set = M('pay_config')->where(['token'=>$token])->field('wxpay_config')->find();
        $set['config'] = unserialize($set['wxpay_config']);
        return ['APPID'=>$set['config']['appid'],'MCHID'=>$set['config']['mchid'],'KEY'=>$set['config']['key']];
    }

	public function givemoney(){
		$channeid = I("id");
		$model = M('juejin_channel');
		$channel = $model->find($channeid);
		$userlist = M("juejin_users")->select();
        $childstr = '';
		if($channel){
			$user = M("juejin_users")->where(['phone'=>$channel['phone']])->find();
			$last = array();
			if($user){
				$last = $this->getChannelChild($userlist,$user['phone'],0);
				foreach ($last as $k=>$v){
					$childstr .=$v['id'].','; 
				}
				$wh['userid'] =['in',rtrim($childstr,',')];
				
				$win = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('get_amount');
				$win_monet = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('amount');
				$last = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>0]))->sum('amount');
				$getmoney = ($last- $win+$win_monet)*$channel['fandian']/100;
				M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1]))->save(['channelgive'=>1,'givetime'=>time()]);
				
				if($getmoney>0){
					$model->where(['id'=>$channeid])->setInc('allgive',$getmoney);
					
					$model->where(['id'=>$channeid])->save('givetime',time());
                    //打款到客户的账户余额中去
                    M("juejin_users")->where(['phone'=>$channel['phone']])->setInc('money',$getmoney);
				}
			}
			$this->success('打款成功');
		}else
			$this->success('无效代理身份');
	}
	public function channel(){
		$where = $this->_search();
		$model = M('juejin_channel');
		$count = $model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
        $childstr = '';
		$list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		$userlist = M("juejin_users")->select();
		foreach ($list as $key=>$val){
			$user = M("juejin_users")->where(['phone'=>$val['phone']])->find();
			$last = array();
			if($user){
				$last = $this->getChannelChild($userlist,$user['phone'],0);
				foreach ($last as $k=>$v){
					$childstr .=$v['id'].','; 
				}
				
				$wh['userid'] =['in',rtrim($childstr,',')];
				$list[$key]['pay'] = M('juejin_payorder')->where(array_merge($wh,['status'=>2]))->sum('price');
				 
				$list[$key]['win'] = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('get_amount');
				$list[$key]['win_amont'] = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>1]))->sum('amount');
				$list[$key]['las'] = M('juejin_order')->where(array_merge($wh,['channelgive'=>0,'is_sim'=>1,'is_win'=>0]))->sum('amount');
				 
			}
				
			$list[$key]['num']=$last;
			
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		 
		$this->display();	
	}
	function getChannelChild($arr,$pid,$num,$i=0){	//递归查询
		static $last_count = array();//数组
		if($i == 0) $last_count = array();//从0开始时清空数组，防止多次调用后出现重复
		foreach($arr as $key=> $v){
			if($v['shareid'] == $pid){
				$last_count[] =$v;
				$this->getChannelChild($arr,$v['phone'],$num+1,$i+1);
			}
		}
		return $last_count;
	
	}
	public function addchannel(){
		$Model=M('juejin_channel');
		if(IS_POST){
			$has=$Model->where(array('phone'=>$_POST['phone']))->find();
			if($has){
				$this->error('该代理商已存在',U('Member/addchannel'));
			}
			
			$data['phone']=$_POST['phone'];
			$data['username']=$_POST['username'];
			$data['fandian']=$_POST['fandian'];
			$data['address']=$_POST['address'];
			$data['idcard']=$_POST['idcard'];
			$data['company']=$_POST['company'];
			$data['create_time']=time();
			$data['password']=md5($_POST['password']);
			$user=$Model->add($data);
			if($user){
				$this->success('添加成功!',U('Member/channel'));
			}else{
				$this->error('操作失败',U('Member/channel'));
			}
		}else{
			$this->display();
		}
	}
	
	//删除会员
	public function channeldel(){
	
		$data['id'] = I('get.id');
		$member = M('juejin_channel')->where($data)->find();
		if($member == null) $this->error('代理不存在！');
		$status = I("status");
		$res = M('juejin_channel')->where($data)->delete();
		if($res){
			$this->success('操作成功！');
		}
		else{
			$this->error('操作失败！');
		}
	}
	
	//会员详细信息显示与编辑
	public function channeldetail(){
		if(IS_POST){
			$model = M('juejin_channel');	
			$data['username']=$_POST['username'];
			$data['fandian']=$_POST['fandian'];
			$data['address']=$_POST['address'];
			$data['idcard']=$_POST['idcard'];
			$data['company']=$_POST['company'];
			$data['last_login_time']=time();
			if($_POST['password']){
				$data['password']=md5($_POST['password']);
			}	
			$ret = $model->where(['id'=>$_POST['id']])->save($data);
			if($ret){
				$this->success('保存成功！');
			}
			else{
				$this->error('保存失败-无内容变动！');
			}
		}
		else{
			$mid = I('get.id',0,'intval');		
			$model = M('juejin_channel');
			$where['id'] = $mid;		
			$member = $model->where($where)->find();
			$this->assign('member',$member);	
			$this->display();
		}
	}
	public function index(){

		$where = $this->_search();
		$model = M('juejin_users');
        $start = I('starttime') ? strtotime(I('starttime')):  time() -3600*365*10 ;
        $end =  I('endtime') ?  strtotime(I('endtime')):time() +3600*365*10 ;
        $where["create_time"] = array(array('gt',$start),array('lt',$end));
		$count = $model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		
		$userlist = $model->select();
		$channellist = M('juejin_channel')->select();
		//print_r($userlist);
		foreach ($list as $key =>$val){	
			$list[$key]['num']=$this->getChildCount($userlist,$val['phone'],0);
			$fdata=$this->getChannelAndAgent($userlist,$val['shareid'],$channellist);
			$list[$key]['agent']=$fdata['agent'][0]['username'];
			$list[$key]['agent_id']=$fdata['agent'][0]['id'];
			$list[$key]['channel']=$fdata['channel']['username'];
			$list[$key]['channel_id']=$fdata['channel']['id'];
			//总充值金额
			$list[$key]['paysum']=M("juejin_payorder")->where(['userid'=>$val['id'],'status'=>2])->sum('price');
		}
//print_r($list);
		$this->assign('list',$list);
		$this->assign('page',$show);
     
		$this->display();
	}
	public function setting(){
		$model = M('juejin_set');
		if(IS_POST){
			$model->create();
			$ret = $model->save();
			if($ret){
				$this->success('保存成功！');
			}
			else{
				
				$this->error('保存失败！');
			}
		}
		else{
			$info = $model->find();
			$this->assign('set',$info);
			$this->display();
		}	
	}
	public function mychild(){
		$pid = I("pid");
		$lever = I("lever");
		$model = M('juejin_users');
		$userlist = $model->select();
		
		$userinfo = $model->find($pid);
		switch ($lever){
			case 0:
				$userinfo['title']='一级人数';
				break;
			case 1:
				$userinfo['title']='二级人数';
				break;
			case 2:
				$userinfo['title']='三级人数';
				break;
			
		}
		
		
		$list=$this->getChildVal($userlist,$userinfo["phone"],0,$lever);
		
		
		foreach ($list as $key =>$val){
			$list[$key]['num']=$this->getChildCount($userlist,$val['phone'],0);
		}
		
		$this->assign('userinfo',$userinfo);
		$this->assign('list',$list);
		$this->display();
		
	}
	function getChildCount($arr,$pid,$num,$i=0){	//递归查询
		static $last_count = array();//数组
		if($num<3){
			if($i == 0) $last_count = array();//从0开始时清空数组，防止多次调用后出现重复
			foreach($arr as $key=> $v){
				if($v['shareid'] == $pid){
					$last_count[$num]['count'] +=1;
					$this->getChildCount($arr,$v['phone'],$num+1,$i+1);
				}
			}		
		}
		return $last_count;	
		
	}

	function getChannelAndAgent($arr,$pid,$channelArr) {
		$fm = array();
		$num=0;

		while($pid>0) {
			foreach ($arr as $a) {
				if($a['phone'] == $pid) {
					$fm['agent'][] = $a;
					$pid = $a['shareid'];
					$num+=1;
					break;
				}
			}
			foreach ($channelArr as $c) {
				if($c['phone'] == $pid) {
					$fm['channel'] = $c;
					$pid=0;
					break;
				}
			}
		}
		return array_reverse($fm);
	}

	function getChildVal($arr,$pid,$num,$lever=0,$i=0){
		static $last_array = array();
		if($num<3){
			if($i == 0) $last_count = array();//从0开始时清空数组，防止多次调用后出现重复
			foreach($arr as $key=> $v){
				if($v['shareid'] == $pid){
					if($num == $lever){
						$last_array[$key] = $v;
						$last_array[$key]['daishu'] = $num;
					}
					$this->getChildVal($arr,$v['phone'],$num+1,$lever,$i+1);
				}
			}
		}
		return 	$last_array;
	}
	
	
	
	public function tongji(){
		
		$count = M('member_recharge_record')->where(['token'=>session('token')])->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = M('member_recharge_record')->where(['token'=>session('token')])->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
		$model = M('Member_card_view');
		foreach ($list as $key=>$val){
			$data = array();
			$data['uid']=session('uid');
			$data['wechat_id']=$val['wechat_id'];	
			$user = $model->where($data)->find();
			$list[$key]['truename']=$user['truename'];
			$list[$key]['type_name']=$user['type_name'];
			$list[$key]['tel']=$user['tel'];
			$list[$key]['number']=$user['number'];		
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	
	}
	private function _search(){
		(I('wechat_name') && ($search['wechat_name'] = ['like','%'.I('wechat_name').'%']));
		(I('number') && ($search['number'] = ['like','%'.I('number').'%']));
		(I('phone') && ($search['phone'] = ['like','%'.I('phone').'%']));
		return $search;
	}

	public function yongjin(){
		//$search = $this->_search();
		$where = array();
//		if($search){
//			$user = M('juejin_users')->where($search)->find();
//			$where['userid']=$user['id'];
//		}
		(I('wechat_name') && ($where['weixin'] = ['like','%'.I('wechat_name').'%']));
		(I('phone') && ($where['cardphone'] = ['like','%'.I('phone').'%']));

		$model = M('juejin_tiorder');
		$count = $model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		foreach ($list as $key=>$val){
			$user = M('juejin_users')->find($val['userid']);
			$list[$key]['wechat_name']=$user['wechat_name'];
			$list[$key]['phone']=$user['phone'];
			
		}
		//print_r($list);
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		 
		$this->display();

	}
	public function order(){

		$search = $this->_search();
		$where = ['is_sim'=>1];
		if($search){
			$user = M('juejin_users')->where($search)->find();
			$where['userid']=$user['id'];
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
	public function xuorder(){


		$search = $this->_search();
		$where = ['is_sim'=>0];
		if($search){
			$user = M('juejin_users')->where($search)->find();
			$where['userid']=$user['id'];
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
	
	
	public function phb(){
		$sql = "SELECT userid,SUM(amount) as amount FROM `sp_juejin_order` where is_sim=1 GROUP BY userid ORDER BY  SUM(amount) DESC";

		$list = M('juejin_order')->query($sql);
		foreach ($list as $key=>$val){
			$user = M('juejin_users')->find($val['userid']);
			//总充值金额
			$list[$key]['paysum']=M("juejin_payorder")->where(['userid'=>$val['userid'],'status'=>2])->sum('price');
			//总提现金额
			$list[$key]['sucsum']=M("juejin_tiorder")->where(['userid'=>$val['userid'],'status'=>2])->sum('price');
			//待确定提现
			$list[$key]['errsum']=M("juejin_tiorder")->where(['userid'=>$val['userid'],'status'=>0])->sum('price');
			
			if(!$user){unset($list[$key]);continue;}
			$list[$key]['wechat_name']=$user['wechat_name'];
			$list[$key]['phone']=$user['phone'];
			
		}	
		$this->assign('list',$list);
		$this->display();


	}
	public function pay(){
		$search = $this->_search();
		$where = array();
		if($search){
			$user = M('juejin_users')->where($search)->find();
			$where['userid']=$user['id'];
		}

		$model = M('juejin_payorder');
        $start = I('starttime') ? strtotime(I('starttime')):  time() -3600*365*10 ;
        $end =  I('endtime') ?  strtotime(I('endtime')):time() +3600*365*10 ;
        $where["createtime"] = array(array('gt',$start),array('lt',$end));
		$count = $model->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $model->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
		$userlist =  M('juejin_users')->select();
		$channellist = M('juejin_channel')->select();
		foreach ($list as $key=>$val){
			$user = M('juejin_users')->find($val['userid']);
			$list[$key]['wechat_name']=$user['wechat_name'];
			$list[$key]['phone']=$user['phone'];
			$fdata=$this->getChannelAndAgent($userlist,$user['shareid'],$channellist);
			$list[$key]['agent']=$fdata['agent'][0]['username'];
			$list[$key]['agent_id']=$fdata['agent'][0]['id'];
			$list[$key]['channel']=$fdata['channel']['username'];
			$list[$key]['channel_id']=$fdata['channel']['id'];
			
		}
		
		$this->assign('list',$list);
		$this->assign('page',$show);

		
		$this->display();
	}
	public function queren_yongjin(){
		$orderid = I("id");
		$info  = M('juejin_tiorder')->find($orderid);
		if($info){
			$res = M('juejin_tiorder')->where(['id'=>$orderid])->setField('status',2);
			if($res){
				$this->success('提现确认成功',U('Member/yongjin'));
			}else{
				$this->error('确认失败，请重试');
			}
		}else{
			$this->error('无效订单号，请确定');
		}
		
		
	}

    private function get_server_ip() {
        return "60.169.81.74";
        if (isset($_SERVER)) {
            if($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } else {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }
    private  function brandpay($info){
        $token = session('token');
        $xml = file_get_contents("php://input");
        //Log::write("[WXPAY]:xml=".$xml,'DEBUG');

        Vendor("WxPayV3.WxPayPubHelper");
        //使用通用通知接口
        $money = new \SendMoney_pub();
        $config = $this->getConfig($token);
        $money->APPID = $config["APPID"];
        $money->MCHID = $config["MCHID"];
        $money->KEY = $config["KEY"];
        $money->parameters["partner_trade_no"] = '100000'.$info["id"];
        $money->parameters["openid"] = $info["wechat_id"];
        $money->parameters["check_name"] = "OPTION_CHECK";
        $money->parameters["re_user_name"] = $info["weixin"];
        //$money->parameters["amount"] = 100;   //测试，1元
        $money->parameters["amount"] =  $info["price"] * 100;
        $money->parameters["desc"] = "用户提现";
        $money->parameters["spbill_create_ip"] = $this->get_server_ip();
        $result = $money->getResult();

        return $result;
    }

    public function tixian_yongjin(){
        $orderid = I("id");

        $info  = M('juejin_tiorder')->find($orderid);

        if($info){
            $result = $this->brandpay($info);
            $res = false;
            if($result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS"){
                // 支付成功，修改状态
                $res = M('juejin_tiorder')->where(['id'=>$orderid])->setField('status',2);
            }
            else if($result["result_code"] == "FAIL"){
                $this->error(serialize($result));
            }
            else{
                $this->display(serialize($result));
            }

            if($res){
                $this->success('提现确认成功',U('Member/yongjin'));
            }else{
                $this->error('确认失败，请重试');
            }
        }else{
            $this->error('无效订单号，请确定');
        }


    }

	public function back_yongjin(){
		$orderid = I("id");
		$info  = M('juejin_tiorder')->find($orderid);
		if($info){
			$res = M('juejin_tiorder')->where(['id'=>$orderid])->setField('status',1);
			
			M('juejin_users')->where(['id'=>$info['userid']])->setInc('addmoney',$info['addmoney']);
			if($info['is_over']==1){
				$money = $info['price'];
			}else{
				$money = $info['price']*2;
			}
			M('juejin_users')->where(['id'=>$info['userid']])->setInc('money',$money);
			M('juejin_users')->where(['id'=>$info['userid']])->setDec('dongjie',$info['price']);
			if($res){
				$this->success('提现退回成功',U('Member/yongjin'));
			}else{
				$this->error('操作失败，请重试');
			}
		}else{
			$this->error('无效订单号，请确定');
		}
		
		
	}
	
	public function add(){
		$sql=M('Member');
		$data['token']=session('token');
		$data['uid']=session('uid');
		$member=$sql->field('id')->where($data)->find();
		$pic['homepic']=I('post.homepic');
		if($member!=false){
			$back=$sql->where($data)->save($pic);
			if($back){
				$this->success('更新成功');
			}
			else{
				$this->error('服务器繁忙，请稍后再试1');
			}
		}else{
			$data['homepic']=$pic['homepic'];
			$back=$sql->add($data);
			if($back){
				$this->success('更新成功');
			}
			else{
				$this->error('服务器繁忙，请稍后再试');
			}
		}
	}
	
	//删除会员
	public function del(){
	
		$data['id'] = I('get.id');
		$member = M('juejin_users')->where($data)->find();
		if($member == null) $this->error('会员不存在！');
		$status = I("status");
		$res = M('juejin_users')->where($data)->save(['status'=>$status,'updatetime'=>time()]);
		if($res){
			$this->success('操作成功！');
		}
		else{
			$this->error('操作失败！');
		}
	}

	public function delUser(){
        $data['id'] = I('get.id');
        $member = M('juejin_users')->where($data)->find();
        if($member == null) $this->error('会员不存在！');

        // 删除订单
        M('juejin_order')->where(['phone'=>$member['phone']])->delete();
        // 删除微信账户
        M('wechat_user')->where(['phone'=>$member['phone']])->delete();
        M('juejin_payorder')->where(['userid'=>$member['id']])->delete();
        // 删除用户
        $res = M('juejin_users')->where($data)->delete();
        if($res){
            $this->success('操作成功！');
        }
        else{
            $this->error('操作失败！');
        }
    }

	//会员详细信息显示与编辑
	public function detail(){
		if(IS_POST){
			$model = M('juejin_users');
			$model->create();
			$ret = $model->save();
			if($ret){
				$this->success('保存成功！');
			}
			else{
				$this->error('保存失败！');
			}
		}
		else{
			$mid = I('get.id',0,'intval');
			
			$model = M('juejin_users');
		
		
			$where['id'] = $mid;
			
			
			$member = $model->where($where)->find();

			$this->assign('member',$member);
			
			$this->display();
		}
	}
	
	//更改会员卡类型
	public function change_card_level(){
		$card_type = I('post.card_type',0,'intval');
		$wechat_id = I('post.wechat_id','','trim');
		(!$wechat_id || !$card_type) && $this->error('目标级别会员卡不存在！');
		$cardType = M('member_card_set')->field('type_name')->where(['token'=>session('token'),'id'=>$card_type])->find();
		//查找新卡
		$card = M('member_card_create')->where(['card_id'=>$card_type,'token'=>session('token')])->order('id asc')->find();
		!$card && $this->error($cardType['type_name'].'已被领完！');
		//解绑旧卡
		M('member_card_create')->where(['wechat_id'=>$wechat_id,'token'=>session('token')])->setField('wechat_id','');
		//设置新卡
		$ret = M('member_card_create')->where(['number'=>$card['number'],'token'=>session('token')])->setField('wechat_id',$wechat_id);
		if($ret)$this->success('设置成功！');
		else $this->error('设置失败！');
	}
	
	//会员消费管理
	public function expense(){
		$mid = I('get.id',0,'intval');
		$member = M('member_info')->field('wechat_id')->find($mid);
		$wechat_id = $member['wechat_id'];
		if(IS_POST){
			//在会员信息里增加消费金额和积分
			$logic = new \Wap\Logic\Member(session('token'),$wechat_id);
			$data['money'] = $_POST['expense'];
			$data['time'] = strtotime($_POST['time']);
			$data['remark'] = $_POST['remark'];
			
			$ret = $logic->expense($data,$_POST['score']);
			if($ret){
				$this->success('添加成功！');
			}
			else{
				$this->error('添加失败！');
			}
		}
		else{
			$list = M('member_expense')->where(['token'=>session('token'),'wechat_id'=>$member['wechat_id']])->order('id desc')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
	
	//删除消费记录
	public function expense_del(){
		$id = I('get.id',0,'intval');
		$uid = I('get.uid',0,'intval');
		$info = M('member_expense')->find($id);
		if($info ==null){
			$this->error('非法操作!',U('Member/expense?id='.$uid));exit;
		}
		$ret = M('member_expense')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret){
			$where = ['token'=>session('token'),'wechat_id'=>$info['wechat_id']];
			M('member_info')->where($where)->setDec('total_expense',$info['expense']);
			M('member_info')->where($where)->setDec('expense_score',$info['score']);
			M('member_info')->where($where)->setDec('total_score',$info['score']);
			$this->success('删除成功!',U('Member/expense?id='.$uid));
		}
		else{
			$this->error('非法操作!',U('Member/expense?id='.$uid));
		}
	}
	
	//会员积分管理
	public function score(){
		$mid = I('get.id',0,'intval');
		$member = M('member_info')->field('wechat_id')->find($mid);
		$wechat_id = $member['wechat_id'];
		if(IS_POST){
			$score = I('post.score',0,'intval');
			$remark = I('post.remark','','trim');
			$logic = new \Wap\Logic\Member(session('token'),$member['wechat_id']);
			$ret = $logic->alterScore($score,4,$remark);
			if($ret){
				$this->success('添加成功！');
			}
			else{
				$this->error('添加失败！');
			}
		}
		else{
			$list = M('member_score_record')->where(['wechat_id'=>$member['wechat_id']])->order('id desc')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}
	
	public function score_del(){
		$id = I('get.id',0,'intval');
		$uid = I('get.uid',0,'intval');
		$info = M('member_score_record')->find($id);
		if($info ==null){
			$this->error('非法操作!',U('Member/score?id='.$uid));exit;
		}
		$ret = M('member_score_record')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret){
			$where = ['token'=>session('token'),'wechat_id'=>$info['wechat_id']];
			M('member_info')->where($where)->setDec('total_score',$info['score']);
			$this->success('删除成功!',U('Member/score?id='.$uid));
		}
		else{
			$this->error('非法操作!',U('Member/score?id='.$uid));
		}
	}
	
	//会员申请审核
	public function check(){
		$list = M('member_info')->where(['token'=>session('token'),'status'=>0])->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function check_request(){
		$id = I('get.id',0,'intval');
		$member = M('member_info')->field('wechat_id')->where(['token'=>session('token'),'id'=>$id])->find();
		$cardset = M('Member_card_set')->where(['token'=>session('token'),'base_points'=>0])->field('id')->find();
		$card = M('Member_card_create')->field('id,number')->where("token = '".session('token')."' and wechat_id = '' and card_id=".$cardset['id'])->find();
		$exchange = M('Member_card_exchange')->field('startup_score')->where(array('token'=>session('token')))->find();
		
		//如果商家还有会员卡，可以领
		if($card != null){
			//微信与会员卡绑定
			$card_up=M('Member_card_create')->where(array('id'=>$card['id']))->setField('wechat_id',$member['wechat_id']);
			//记录会员信息
			M('Member_info')->where(['token'=>session('token'),'wechat_id'=>$member['wechat_id']])->setField('status',1);
			if($exchange['startup_score']!=0){
				$logic = new \Wap\Logic\Member(session('token'),$member['wechat_id']);
				$logic->alterScore($exchange['startup_score'],4,'领卡送积分');
			}
			$this->success('审核成功！');
		}
		else{
			//商家没有了会员卡
			$this->error('会员卡已被领完啦！');
		}
	}
	public function adduser(){
		if(IS_POST){
			$has=M('juejin_users')->where(array('phone'=>$_POST['phone']))->find();
			if($has){
				$this->error('该用户已存在',U('Member/adduser'));
			}
			if($_POST['shareid']){
				$sharehas = M('juejin_users')->where(array('phone'=>$_POST['shareid']))->find();
				if(empty($sharehas)){
					$this->error('无填写的推荐人ID',U('Member/adduser'));
				}
			}	
			$data['phone']=$_POST['phone'];
			$data['username']=$_POST['username'];
			$data['shareid']=$_POST['shareid'];
			$data['xulimoney']=8888;
			$data['create_time']=time();
			$data['password']=md5($_POST['password']);
			$user=M('juejin_users')->add($data);
			if($user){
				$this->success('添加成功!',U('Member/index'));
			}else{
				$this->error('操作失败',U('Member/index'));
			}
		}else{
			$this->display();
		}
	}
	public function change(){

		
		$save['star']=I("star");	
		$user=M('juejin_users')->where(array('id'=>$_POST['orderid']))->limit(1)->save($save);
		$this->success('修改成功!',U('Member/index'));

	}

	public function changeChannel(){
		if(IS_POST){
			$save['shareid']=I("phone");
			$rs=M('juejin_users')->where(array('id'=>$_POST['id']))->limit(1)->save($save);
			if($rs){
				$this->success('修改成功!',U('Member/index'));
			}else{
				$this->error('修改失败!',U('Member/index'));
			}

		}else{
			$id=I("id");
			$channel_id=I("channel_id");
			$list = M('juejin_channel')->where(['status'=>'0'])->order('id desc')->select();
			$this->assign('list',$list);
			$this->assign('id',$id);
			$this->assign('channel_id',$channel_id);
			$this->display();
		}
	}



	public function money(){
		$mid = I('get.id',0,'intval');
		$member = M('juejin_users')->find($mid);
		if(IS_POST){
			$money = floatval(I('post.money'));
			$remark = I('post.remark','','trim');
			
			$data['userid'] = $mid;
			$data['wechat_id'] = $member['wechat_id'];
			$data['money'] = $money;
			$data['time'] = time();
			$data['remark'] = I('post.remark','线下充值','trim');
			$data['status'] = 1;
			$data['type'] = 1;	

			$ret =M('member_recharge_record')->add($data);
			if($ret){
				M('juejin_users')->where(['id'=>$mid])->setInc('money',$money);
				$this->success('充值成功！');
			}
			else{
				$this->error('充值失败！');
			}
		}
		else{
			$list = M('juejin_payorder')->where(['userid'=>$mid])->order('id desc')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}

	public function approve(){
        $model = M("juejin_content");
        $count = $model->where(["status"=>0])->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $model->where(["status"=>0])->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();
        $result = [];
        foreach($list as $li)
        {
            $li["imglist"] = explode(",", $li["serverId"]);
            $result[] = $li;
        }
        $this->assign('list',$result);
        $this->assign('page',$show);
        $this->display();
    }

    public function approveInfo(){
        $id = I("id");
        $info  = M('juejin_content')->find($id);
        if($info){
            $res = M('juejin_content')->where(['id'=>$id])->setField('status',1);
            if($res){
                $this->success('审核确认成功',U('Member/approved'));
            }else{
                $this->error('审核失败，请重试');
            }
        }else{
            $this->error('无效id，请确定');
        }
    }

    public function approved(){
        $model = M("juejin_content");
        $count = $model->where(["status"=>1])->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list = $model->where(["status"=>1])->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->select();

        $result = [];
        foreach($list as $li)
        {
            $li["imglist"] = explode(",", $li["serverId"]);
            $result[] = $li;
        }
        $this->assign('list',$result);
        $this->assign('page',$show);
        $this->display();
    }

    public function delcomment(){
        $id = I("id");
        $info  = M('juejin_content')->find($id);
        if($info){
            $res = M('juejin_content')->where(['id'=>$id])->delete();
            if($res){
                $this->success('删除成功',U('Member/approve'));
            }else{
                $this->error('删除失败，请重试');
            }
        }else{
            $this->error('无效id，请确定');
        }
    }

    public function adlist(){
        $list = M("juejin_ad")->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function delad(){
        $id = I("id");
        $info  = M('juejin_ad')->find($id);
        if($info){
            $res = M('juejin_ad')->where(['id'=>$id])->delete();
            if($res){
                $this->success('删除成功',U('Member/adlist'));
            }else{
                $this->error('删除失败，请重试');
            }
        }else{
            $this->error('无效id，请确定');
        }
    }

    public function addad(){
        if(IS_POST){
            $data['imgUrl']=$_POST['imgUrl'];
//            $data['url']=$_POST['url'];
            $data['sort']=$_POST['sort'];

            $ad=M('juejin_ad')->add($data);
            if($ad){
                $this->success('添加成功!',U('Member/adlist'));
            }else{
                $this->error('操作失败',U('Member/adlist'));
            }
        }else{
            $this->display();
        }
    }

    public function couponlist(){
        $list = M("juejin_coupon")->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function addcoupon(){
        if(IS_POST){
            if($_POST['id']){
                $id=$_POST['id'];
                $data = M("juejin_coupon")->find($id);
            }
            $data['name']=$_POST['name'];
            $data['type']=$_POST['type'];
            $data['overdue_time']=$_POST['overdue_time'];
            $data['amount']=$_POST['amount'];
            $data['use_area']=$_POST['use_area'];
            $data['satisfy_amount']=$_POST['satisfy_amount'];
            $data['remark']=$_POST['remark'];
            $data['register_present']=$_POST['register_present'];
            $data['recharge_present']=$_POST['recharge_present'];
            $data['create_time']=time();
            if($_POST['id']){
                $ad=M('juejin_coupon')->save($data);
            }
            else{
                $ad=M('juejin_coupon')->add($data);
            }

            if($ad){
                $this->success('添加成功!',U('Member/couponlist'));
            }else{
                $this->error('操作失败',U('Member/addcoupon'));
            }
        }else{
            if(I('id')){
                $id=I('id');
                $data = M("juejin_coupon")->find($id);
                $this->assign('data',$data);
            }
            $this->display();
        }
    }

    public function invitelist(){
        $where = $this->_search();
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
        $data['phone']='admin';
        $data['is_used']=0;
        $data['used_phone']='';
        $data['create_time']=time();
        $ad=M('juejin_invite_code')->add($data);

        if($ad){
            $this->success('添加成功!',U('Member/invitelist'));
        }else{
            $this->error('操作失败',U('Member/invitelist'));
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
}
?>