<?php
namespace Wap\Controller;
class UserController extends WapController{
	protected $capitalNaem = array('BTCCNY'=>'比特币','GBPUSD'=>'英镑/美元','USDJPY'=>'美元/日元','EURUSD'=>'欧元/美元');//黄金-字母对比
	public  $userinfo;
	public function _initialize(){
		parent::_initialize();
		$this->userinfo = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		$this->assign('user',$this->userinfo);
	}
	
	
	
	public function set_sim(){
		
		$now_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:1;
		$change = ($now_sim==1)?0:1;		
		$_SESSION['is_sim']=$change;		
		$this->ajaxReturn(['msg'=>'切换成功！','status'=>1]);
		/* {
			"msg": "切换成功！",
			"status": 1
		} */
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
		$filename = md5($scene['scene_id']).'.png';
		ImagePng($QR, $TMP_DIR.$filename);
		$PNG_WEB_BASE_URL = C('site_url').'/Uploads/Crm/'.$filename;

		$this->assign('imgurl',$PNG_WEB_BASE_URL);
		
		
		//我的分销数
		
		$share['num']=M('juejin_users')->where(['shareid'=>$_SESSION['user_id']])->count();
		$share['money']=M('juejin_fanliorder')->where(['touserid'=>$_SESSION['user_id']])->sum('price');
		
	$this->assign('share',$share);
		$this->display();
	}
	public function my_friends(){
		
		$sharelist=M('juejin_users')->where(['shareid'=>$_SESSION['user_id']])->select();
		$this->assign('sharelist',$sharelist);
		$this->display();
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
		
		redirect(U('User/login_show'));
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
		$this->ajaxReturn(['status'=>0,'info'=>'密码修改成功']);
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
		
		

		$total['count']=M('juejin_order')->where($where)->count();
		$total['sum']=M('juejin_order')->where(array_merge($where))->sum('get_amount');
		
		
		$this->assign('today',$today);
		$this->assign('total',$total);
		$this->display();
	}
	public function history_refresh(){
		
		
	
		$is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:1;
		
		$data['is_sim']=$is_sim;
		$data['status']=1;
		
		$page = I('page')?I('page'):1;
		$limit =( ($page-1)*15).',15';
		$list = M('juejin_order')->where(['userid'=>$_SESSION['user_id'],'is_sim'=>$is_sim])->limit($limit)->order('id desc')->select();
		
		if(empty($list)){
			$this->ajaxReturn(['status'=>'no']);
		}
		
		$orderlist = array();
		foreach ($list as $key=>$val){
			$orderlist[$key]['id']=$val['id'];
			$orderlist[$key]['trade_amount']=$val['amount'];
			$orderlist[$key]['expect_profit']=$val['get_amount'];
			$orderlist[$key]['trade_direction']=($val['dircetion']==1)?'买涨':'买跌';
			$orderlist[$key]['trade_point']=$val['deal_value'];
			$orderlist[$key]['option_key']=$this->capitalNaem[$val['capital']];
			$orderlist[$key]['profit']=$val['is_win']?'<font color=red>+'.$val['get_amount'].'</font>':'<font color=green>-'.$val['amount'].'</font>';//
			$orderlist[$key]['is_win']=$val['is_win']?'<font color=red>盈利</font>':'<font color=green>亏损</font>';//
			$orderlist[$key]['trade_time']=date('m-d H:i:s',$val['createtime']);	
			
		}
		$data['list']=$orderlist;
		$this->ajaxReturn($data);
	}
	public function insurance(){
		
		$this->display();
	}
	public function bank_water(){
		$date['paycount'] = M('juejin_payorder')->where(['userid'=>$_SESSION['user_id'],'status'=>2])->sum('price');
		
		
		$this->assign('date',$date);
		$this->display();
	}
	public function bank_water_show(){//充值，提现记录
		

		$page = I('page')?I('page'):1;
		$limit =( ($page-1)*15).',15';
		
		if(I('type')==1){//提现记录juejin_fanliorder
			$paylist = array();//M('juejin_payorder')->where(['userid'=>$_SESSION['user_id']])->limit($limit)->order('id desc')->select();		
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
			$url = '/wap/wxpay/index.html?module=1001&token='.$this->token.'&wechat_id='.$this->wechat_id.'&callback='.$callback.'&out_trade_no='.$data['ordersn'];
			@header("Location:$url");
			
		}else{
			exit( json_encode(array('errMsg'=>'参数错误！')) );
		}		
	}
}
?>