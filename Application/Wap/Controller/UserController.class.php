<?php
namespace Wap\Controller;
class UserController extends WapController{
	protected $capitalNaem = array('BTCCNY'=>'比特币','GBPUSD'=>'英镑/美元','USDJPY'=>'美元/日元','EURUSD'=>'欧元/美元','HJRMB'=>'黄金','BYRMB'=>'白银');//黄金-字母对比
	public  $userinfo;
	public function _initialize(){
		parent::_initialize();
		$this->userinfo = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		$this->assign('user',$this->userinfo);
	}

	public function ceshiY(){

						$shareinfo = M('juejin_users')->field('phone')->where(['id'=>27])->find();
						$shareuser = M('wechat_user')->field('open_id')->where(array('phone'=>$shareinfo['phone']))->find();
						if($shareuser['open_id']){
							$wx = M('wxuser')->where(['token'=>'anuxzf1435128586'])->field('appid,appsecret,is_certified')->find();
							$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
							$template_id='TOqICF8j63Wv0FnBYaounqJw-b8JDgA6znQu6WUQJhI';
							$url=rtrim(C('wap_domain'),'/') . U('Wap/User/my_friends');
							$topcolor="#000";
							$phone = $shareuser["phone"];
							$datas=array(
									'first'=>array('value'=>'恭喜您成功发展一名下级！','color'=>'#000'),
									'keyword1'=> array('value'=>substr($phone,0,3)."****".substr($phone,-4),'color'=>'#000'),
									'keyword2'=> array('value'=>date('Y-m-d H:i'),'color'=>'#000'),
									'remark'=> array('value'=>"点击查看详细情况",'color'=>'#000'),
							);
							$data1=json_encode($datas); //发送的消息模板数据
							$from_user=$shareuser['open_id'];
							$json = '{"touser":"' . $from_user . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data1 . '}';
							$ret = $util->sendTempMsg($template_id, $json);	
						}
						echo $shareuser['open_id'];
	}
	public function pay_water_show(){//充值，提现记录
		$page = I('page')?I('page'):1;
		$limit =( ($page-1)*15).',15';
		
		$paylist = M('juejin_fanliorder')->where(['touserid'=>$_SESSION['user_phone']])->limit($limit)->order('id desc')->select();
	
		if(empty($paylist)){
			$this->ajaxReturn(array());
		}
		$newarray = array();
		foreach ($paylist as $key=>$val){
			$newarray[$key]['cash_type']='充值返利';
			$newarray[$key]['amount']=$val['price'];
			$newarray[$key]['create_time']=date('m-d H:i:s',$val['createtime']);
			$user = M("juejin_users")->find($val['userid']);
	
			$newarray[$key]['auth_state']='<font color="green">'.substr($user['phone'],0,3).'***'.substr($user['phone'],-4).'</font>';
		}
		$this->ajaxReturn($newarray);
	}
	
	public function bank_info(){
		
		$this->display();
	}
	public function bank_info_save(){
		$save['username']=I('real_name');
		$save['id_number']=I('id_number');
		
		M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save($save);
		$this->ajaxReturn(['info'=>'银行信息修改成功！','status'=>1]);
	}
	
	
	public function set_sim(){
		
		$now_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;
		$change = ($now_sim==1)?0:1;		
		$_SESSION['is_sim']=$change;		
		$this->ajaxReturn(['msg'=>'切换成功！','status'=>1]);
		/* {
			"msg": "切换成功！",
			"status": 1
		} */
	}

	public function fanli(){

		$sql = "SELECT p.userid,SUM(p.price) AS pay,u.shareid FROM `sp_juejin_payorder` AS p LEFT JOIN `sp_juejin_users` AS u ON p.userid=u.id WHERE p.status=2 AND p.pgive=0 AND u.shareid>0  GROUP BY p.userid ORDER BY  SUM(p.price) DESC";
		$user = M('juejin_order')->query($sql);//$user为满足充值未结算记录	
		foreach ($user as $val){
			$count = M('juejin_order')->where(['userid'=>$val['userid'],'is_sim'=>1])->sum('amount');
			if($count>$val['pay'] * 2){//满足返利
				//三级返利
				$setting = M('juejin_set')->find(1);
				
				$one_money =number_format($setting['first']*$val['pay']/100,2,'.',',');
				$two_money = number_format($setting['second']*$val['pay']/100,2,'.',',');
				$three_money = number_format($setting['three']*$val['pay']/100,2,'.',',');


				$oneshare = M('juejin_users')->where(['phone'=>$val['shareid']])->find();

				M('juejin_payorder')->where(['userid'=>$val['userid']])->save(['pgive'=>1]);

				
				$wx = M('wxuser')->where(['token'=>'anuxzf1435128586'])->field('appid,appsecret,is_certified')->find();
				$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);

				$template_id='iXus6syaDcTuaxMgD-G_i8p8ZBQxIY3uzNKQ6Idrauk';
				$url=rtrim(C('wap_domain'),'/') . U('Wap/User/private_person');
				$topcolor="#000";

				if($oneshare && $one_money){
					M('juejin_users')->where(['phone'=>$val['shareid']])->setInc('money',$one_money);
					M('juejin_fanliorder')->add(['userid'=>$val['userid'],'touserid'=>$val['shareid'],'price'=>$one_money,'status'=>2,'createtime'=>time()]);
					$wechat = M("wechat_user")->where(['phone'=>$oneshare['phone']])->field('open_id')->find();
					if($wechat){
						$datas=array(
								'first'=>array('value'=>'恭喜您获得一笔佣金已充入您的余额帐户！','color'=>'#000'),
								'order'=> array('value'=>'充值返现','color'=>'#000'),
								'money'=> array('value'=>$one_money,'color'=>'#000'),
								'remark'=> array('value'=>"点击查看现金账户",'color'=>'#000'),
						);
						$data1=json_encode($datas); //发送的消息模板数据	
						$from_user=$wechat['open_id'];
						$json = '{"touser":"' . $from_user . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data1 . '}';
						$ret = $util->sendTempMsg($template_id, $json);

						//$msg = "恭喜您获得返利充值佣金".$one_money.',已到你的余额账号，请查看。';
						//$ret = $util->sendCustomMsg($wechat['open_id'],$msg);
					}
					
				}

				if($oneshare['shareid']){
					$twohare = M('juejin_users')->where(['phone'=>$oneshare['shareid']])->find();
					if($twohare && $two_money){
						M('juejin_users')->where(['phone'=>$oneshare['shareid']])->setInc('money',$two_money);
						M('juejin_fanliorder')->add(['userid'=>$val['userid'],'touserid'=>$oneshare['shareid'],'price'=>$two_money,'status'=>2,'createtime'=>time()]);
						
						$wechat = M("wechat_user")->where(['phone'=>$twohare['phone']])->field('open_id')->find();
						if($wechat){
							$datas=array(
								'first'=>array('value'=>'恭喜您获得一笔佣金已充入您的余额帐户！','color'=>'#000'),
								'order'=> array('value'=>'充值返现','color'=>'#000'),
								'money'=> array('value'=>$two_money,'color'=>'#000'),
								'remark'=> array('value'=>"点击查看现金账户",'color'=>'#000'),
							);
							$data1=json_encode($datas); //发送的消息模板数据	
							$from_user=$wechat['open_id'];
							$json = '{"touser":"' . $from_user . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data1 . '}';
							$ret = $util->sendTempMsg($template_id, $json);
							//$msg = "恭喜您获得返利充值佣金".$two_money.',已到你的余额账号，请查看。';
							//$ret = $util->sendCustomMsg($wechat['open_id'],$msg);
						}
					}
				}

				if($twohare['shareid']){
					$threeshare = M('juejin_users')->where(['phone'=>$twohare['shareid']])->find();
					if($threeshare && $three_money){
						M('juejin_users')->where(['phone'=>$twohare['shareid']])->setInc('money',$three_money);
						M('juejin_fanliorder')->add(['userid'=>$val['userid'],'touserid'=>$twohare['shareid'],'price'=>$three_money,'status'=>2,'createtime'=>time()]);
					
						$wechat = M("wechat_user")->where(['phone'=>$threeshare['phone']])->field('open_id')->find();
						if($wechat){
							$datas=array(
								'first'=>array('value'=>'恭喜您获得一笔佣金已充入您的余额帐户！','color'=>'#000'),
								'order'=> array('value'=>'充值返现','color'=>'#000'),
								'money'=> array('value'=>$three_money,'color'=>'#000'),
								'remark'=> array('value'=>"点击查看现金账户",'color'=>'#000'),
							);
							$data1=json_encode($datas); //发送的消息模板数据	
							$from_user=$wechat['open_id'];
							$json = '{"touser":"' . $from_user . '","template_id":"' . $template_id . '","url":"' . $url . '","topcolor":"' . $topcolor . '","data":' . $data1 . '}';
							$ret = $util->sendTempMsg($template_id, $json);

							//$msg = "恭喜您获得返利充值佣金".$three_money.',已到你的余额账号，请查看。';
							//$ret = $util->sendCustomMsg($wechat['open_id'],$msg);
						}
					}
				}	
				
			}		
		}
		//echo  1;
        $this->success('操作成功');
	}

	public function private_person(){
		
		$this->display();
	}
	public function recharge(){//充值

		$this->display();
	}
	public function recharge_no(){
		
		$this->display();
	}
	public function cashOut(){
		$usercash=M('juejin_tiorder')->where(['userid'=>$_SESSION['user_id'],'status'=>'2'])->find();

		if($usercash){
			$usercash['weixin'] = mb_strlen($usercash['weixin'],'utf-8')>2?mb_substr($usercash['weixin'],0,mb_strlen($usercash['weixin'],'utf-8')-1,'utf-8')."*":$usercash['weixin'];
			$usercash['idcard']=strlen($usercash['idcard'])==15?substr_replace($usercash['idcard'],"**********",2,10):(strlen($usercash['idcard'])==18?substr_replace($usercash['idcard'],"**********",4,10):$usercash['idcard']);
			$usercash['cardphone']=strlen($usercash['cardphone'])>7?substr_replace($usercash['cardphone'],"****",3,4):$usercash['cardphone'];
			$usercash['cardnum']=strlen($usercash['cardnum'])>11?substr_replace($usercash['cardnum'],"****",7,4):$usercash['cardnum'];
		}

		$this->assign('usercash',$usercash);
		$this->display();
	}
	public function invite(){
		//二维码

		$wx = M('wxuser')->where(['token'=>$this->token])->field('appid,appsecret,is_certified')->find();
		$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
		$url = $util->getQrcodeUrl(intval($_SESSION['user_id']));
		
		
		$TMP_DIR = __ROOT__.'/Uploads/Crm/';
		$bg = C('site_url').'/Uploads/Crm/create_qrcode_img.jpg';
		
		$logo  = imagecreatefromstring(file_get_contents($url));
		if ($logo !== FALSE) {
			$QR = imagecreatefromstring(file_get_contents($bg));
			
			$QR_width = imagesx($QR);//二维码图片宽度
			$QR_height = imagesy($QR);//二维码图片高度
			$logo_width = imagesx($logo);//logo图片宽度
			$logo_height = imagesy($logo);//logo图片高度
			$logo_qr_width = $QR_width / 5;
			$scale = $logo_width/$logo_qr_width;
			$logo_qr_height = $logo_height/$scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			//重新组合图片并调整大小
			//imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
			//$logo_qr_height, $logo_width, $logo_height);
			imagecopyresampled($QR, $logo, 160, 360, 0, 0, 190,
			190, $logo_width, $logo_height);
		}
//		$filename = md5($scene['scene_id']).'.png';
        $filename = md5($_SESSION['user_id']).'.png';
		ImagePng($QR, $TMP_DIR.$filename);
		$PNG_WEB_BASE_URL = C('site_url').'/Uploads/Crm/'.$filename;

		$this->assign('imgurl',$PNG_WEB_BASE_URL);
		

		//我的分销数
		
		$share['num']=M('juejin_users')->where(['shareid'=>$_SESSION['user_phone']])->count();
		$share['money']=number_format(M('juejin_fanliorder')->where(['touserid'=>$_SESSION['user_phone']])->sum('price'),2,'.',',');
		
	$this->assign('share',$share);
		$this->display();
	}
	public function my_friends(){
		
		$model = M('juejin_users');
		$userlist = $model->select();
		
		$list['num']=$this->getChildCount($userlist,$_SESSION['user_phone'],0);
		//一级	
		$list1=$this->getChildVal($userlist,$_SESSION['user_phone'],0,0);
		//2级
		$list2=$this->getChildVal($userlist,$_SESSION['user_phone'],0,1);
		//3级
		$list3=$this->getChildVal($userlist,$_SESSION['user_phone'],0,2);
		foreach ($list1 as $key=>$val){
			$list1[$key]['tmoney'] = M('juejin_fanliorder')->where(['userid'=>$val['id'],'touserid'=>$_SESSION['user_phone']])->sum('price');
		
		}
		foreach ($list2 as $key=>$val){
			$list2[$key]['tmoney'] = M('juejin_fanliorder')->where(['userid'=>$val['id'],'touserid'=>$_SESSION['user_phone']])->sum('price');
		
		}
		foreach ($list3 as $key=>$val){
			$list3[$key]['tmoney'] = M('juejin_fanliorder')->where(['userid'=>$val['id'],'touserid'=>$_SESSION['user_phone']])->sum('price');
		
		}	
			
		$this->assign('list1',$list1);
		$this->assign('list2',$list2);
		$this->assign('list3',$list3);
		$this->assign('list',$list);
		$this->display();
	}
	function getChildCount($arr,$pid,$num){
	
		$data = array();
		if($num<3){
			foreach($arr as $key=> $v){
				if($v['shareid'] == $pid){
					$data[$num]['count'] +=1;
					$data = array_merge($data,self::getChildCount($arr,$v['phone'],$num+1));
				}
			}
		}
		return $data;
	
	}
	
	function getChildVal($arr,$pid,$num,$lever=0){
		$data = array();
		if($num<3){
			foreach($arr as $key=> $v){
				if($v['shareid'] == $pid){
					if($num ==$lever){
						$data[$key] = $v;
						$data[$key]['daishu'] = $num;
					}
						
					$data = array_merge($data,self::getChildVal($arr,$v['phone'],$num+1,$lever));
				}
			}
		}
	
		return $data;
	}
	public function person(){
		
		$this->display();
	}
	public function setting(){
		
		$this->display();
	}
	public function login_out(){
		
		session(null);
		session_destroy();
		unset($_SESSION);
		if(is_valid_openid($this->wechat_id)){
				$open_id=$this->wechat_id;
				M('wechat_user')->where(array('open_id'=>$open_id))->setField('phone','');
		}

		
		redirect(U('Login/login_show'));
	}
	public function change_password(){
		
		$this->display();
	}
	public function save_password(){
		
		$user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		if($user['password'] !=md5(I('old_password'))){
			$this->ajaxReturn(['status'=>0,'info'=>'原始密码错误，请重新输入']);
		}
		M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save(['password'=>md5(I('new_password'))]);
		$this->ajaxReturn(['status'=>1,'info'=>'密码修改成功']);
	}
	public function history(){
		
		$is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:1;
		$start = strtotime(date('Y-m-d',time()));
		$end = strtotime(date('Y-m-d',time()))+86400;
		
		$where['is_sim']=$is_sim;
		$where['userid']=$_SESSION['user_id'];
		//$where['createtime'] =array('between',$start.','.$end);

		$today['count']=M('juejin_order')->where(array_merge($where,array('createtime'=>array('between',$start.','.$end))))->count();
		$today['sum']=M('juejin_order')->where(array_merge($where,array('is_win'=>1,'createtime'=>array('between',$start.','.$end))))->sum('get_amount');
		$today['lastsum']=M('juejin_order')->where(array_merge($where,array('is_win'=>0,'createtime'=>array('between',$start.','.$end))))->sum('amount');
		
		

		$total['count']=M('juejin_order')->where($where)->count();
		$total['sum']=M('juejin_order')->where(array_merge($where))->sum('amount');
		
		
		$this->assign('today',$today);
		$this->assign('total',$total);
		$this->display();
	}
	public function history_refresh()
    {


        $is_sim = $_SESSION['is_sim'] ? $_SESSION['is_sim'] : 1;

        $data['is_sim'] = $is_sim;
        $data['status'] = 1;

        $page = I('page') ? I('page') : 1;
        $limit = (($page - 1) * 15) . ',15';
        $list = M('juejin_order')->where(['userid' => $_SESSION['user_id'], 'is_sim' => $is_sim])->limit($limit)->order('id desc')->select();

        if (empty($list)) {
            $this->ajaxReturn(['status' => 'no']);
        }
        $now = time();
        $orderlist = array();
        foreach ($list as $key => $val) {
            if ($val['createtime'] + $val['trade_time'] <= $now) {
                $orderlist[$key]['id'] = $val['id'];
                $orderlist[$key]['trade_amount'] = $val['amount'];
                $orderlist[$key]['expect_profit'] = $val['get_amount'];
                $orderlist[$key]['trade_direction'] = ($val['dircetion'] == 1) ? '买涨' : '买跌';
                $orderlist[$key]['trade_point'] = $val['deal_value'];
                $orderlist[$key]['option_key'] = $this->capitalNaem[$val['capital']];
                $orderlist[$key]['profit'] = $val['is_win'] ? '<font color=red>+' . $val['get_amount'] . '</font>' : '<font color=green>-' . $val['amount'] . '</font>';//
                $orderlist[$key]['is_win'] = $val['is_win'] ? '<font color=red>盈利</font>' : '<font color=green>亏损</font>';//
                $orderlist[$key]['trade_time'] = date('m-d H:i:s', $val['createtime'] + $val['trade_time']);
            }
        }
        $data['list'] = $orderlist;
        $this->ajaxReturn($data);
    }
	public function insurance(){
		
		$this->display();
	}
	public function bank_water(){
		$date['paycount'] = M('juejin_payorder')->where(['userid'=>$_SESSION['user_id'],'status'=>2])->sum('price');
		$date['ticount'] = M('juejin_tiorder')->where(['userid'=>$_SESSION['user_id'],'status'=>2])->sum('price');
		
		
		$this->assign('date',$date);
		$this->display();
	}
	public function bank_water_show(){//充值，提现记录
		

		$page = I('page')?I('page'):1;
		$limit =( ($page-1)*15).',15';
		
		if(I('type')==1){//提现记录juejin_fanliorder
			$paylist = M('juejin_tiorder')->where(['userid'=>$_SESSION['user_id']])->limit($limit)->order('id desc')->select();
			foreach ($paylist as $key=>$val){
				$newarray[$key]['cash_type']='会员提现';
				$newarray[$key]['amount']=$val['price'];
				$newarray[$key]['create_time']=date('m-d H:i:s',$val['createtime']);
				$newarray[$key]['auth_state']=($val['status']==2)?'<font color="red">已审核</font>':'<font color="green">未审核</font>';	
			}
		
	
			$this->ajaxReturn($newarray);

		}else{
			$paylist = M('juejin_payorder')->where(['userid'=>$_SESSION['user_id']])->limit($limit)->order('id desc')->select();
		}
		
		if(empty($paylist)){
			$this->ajaxReturn(array());
		}
		$newarray = array();
		foreach ($paylist as $key=>$val){
			$newarray[$key]['cash_type']='会员充值';
			$newarray[$key]['amount']=$val['price'];
			$newarray[$key]['create_time']=date('m-d H:i:s',$val['createtime']);
			$newarray[$key]['auth_state']=($val['status']==2)?'<font color="red">已支付</font>':'<font color="green">未支付</font>';	
		}
		
	
		$this->ajaxReturn($newarray);
	}
	public function weixinpay(){
		$price = I('total_fee');
		
		$data['userid']  =  $_SESSION['user_id'];
		$data['wechat_id']  =  $this->wechat_id;
		$data['ordersn'] = 'K'.substr(time(),4,10).rand(10,99);
		$data['price']       =$price;
		$data['createtime']      = time();
		$data['status'] = 0;
		
		if($order = M('juejin_payorder')->add($data)){	
			$callback = urlencode(C('WAP_DOMAIN').U('Wap/User/private_person'));
			$url = '/wap/IpsPay/home?module=1001&token='.$this->token.'&wechat_id='.$this->wechat_id.'&callback='.$callback.'&out_trade_no='.$data['ordersn'];
			@header("Location:$url");
			
		}else{
			exit( json_encode(array('errMsg'=>'参数错误！')) );
		}		
	}
	public function img_send_sms_text(){
		
		$phone =$_SESSION['user_phone'];	
		$hasuser = M("juejin_users")->where(['phone'=>$phone])->count();
		if($hasuser){
			$paycode = rand(1000,9999);
			$content='短信验证码为:'.$paycode.',请勿将验证码提供给他人.';
			$ret = send_newsms($phone,$content);
			if($ret){
				$_SESSION['pay_code']=$paycode;
				$this->ajaxReturn(['status'=>1,'message'=>'验证码发送成功！']);
			}else{
				$this->ajaxReturn(['status'=>0,'Code'=>'','message'=>'短信发送失败！']);
			}		
			
		}
		$this->ajaxReturn(['status'=>0,'message'=>'请退出重试。']);
		
	}
	public function cash_out(){
		$amount  = I('amount');
		$code = I('SMSCode');
		$message =array();

		$user = $this->userinfo;
//		if(empty($code) || $_SESSION['pay_code'] !=$code){
//			$message['status']=0;
//			$message['success']='操作失败';
//			$message['message']='验证码不能为空';
//
//		}else
			if( $amount<=0 || $user['money']<$amount ){//addmoney 充值记录
			$message['status']=0;
			$message['success']='操作失败';
			$message['message']='填写金额无效，请重新确定';

		}
//		elseif($amount % 100 != 0 ){//addmoney 充值记录
//            $message['status']=0;
//            $message['success']='操作失败';
//            $message['message']='提现金额必须为100的倍数，请重新确定';
//
//        }
        else{

		    if($amount <= 300){
                $lastmoney = $user['money']-$amount -3 ;
            }
            else{
                $lastmoney = $user['money']-$amount*1.01;
            }
            if($lastmoney < 0){
                $message['status']=0;
                $message['success']='操作失败';
                $message['message']='余额不足，提现失败';
            }
            else{
                //$lastmoney = $user['money']-$amount;
                M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save(['money'=>$lastmoney]);
                $data['is_over']  =  1;
                $data['addmoney']  =  $user['addmoney'];
            }
        }
//        elseif($amount>=$user['addmoney']*10){
//			$lastmoney = $user['money']-$amount;
//			M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save(['money'=>$lastmoney]);
//			$data['is_over']  =  1;
//			$data['addmoney']  =  $user['addmoney'];
//
//		}
//		elseif($amount>$user['money']/2){
//			$message['status']=0;
//			$message['success']='操作失败';
//			$message['message']='输入金额大于可提现金额';
//		}
//		else{
//			$moneylast = $user['money']-$amount*2;
//			M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save(['money'=>$moneylast]);
//			M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setInc('dongjie',$amount);
//			$data['is_over']  =  0;
//			$data['addmoney']  =  $user['addmoney'];
//		}

		if(empty($message)){
			$usercash=M('juejin_tiorder')->where(['userid'=>$_SESSION['user_id'],'status'=>'2'])->find();

			if($usercash){
				$weixin=$usercash['weixin'];
				$card=$usercash['card'];
				$idcard=$usercash['idcard'];
				$cardphone=$usercash['cardphone'];
				$cardnum=$usercash['cardnum'];

			}else{
				$weixin=I('weixin');
				$card=I('card');
				$idcard=I('idcard');
				$cardphone=I('cardphone');
				$cardnum=I('cardnum');
			}


			$price = I('total_fee');
			$data['userid']  =  $_SESSION['user_id'];
			$data['wechat_id']  =  $this->wechat_id;
			$data['ordersn'] = 'K'.substr(time(),4,10).rand(10,99);
			$data['price']       =$amount;
			$data['createtime']      = time();
			$data['status'] = 0;
			$data['weixin'] = $weixin;
			$data['card'] = $card;
			$data['idcard'] = $idcard;
			$data['cardphone'] = $cardphone;
			$data['cardnum'] = $cardnum;
			//$data['zhifubao'] = I('zhifubao');
			
			$order = M('juejin_tiorder')->add($data);
			$message['success']='操作成功';
			$message['status']=1;
			$message['message']='提现成功，请等待后台审核';

		}
		$this->assign('message',$message);
		$this->display();
		
	}


}
?>