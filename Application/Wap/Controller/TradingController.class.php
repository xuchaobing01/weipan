<?php
namespace Wap\Controller;
class TradingController extends WapController{
	 public function _initialize(){

		parent::_initialize();	
	} 
	public function deal_zyq(){
		
		$amount = I('amount');
		$user =M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		if($user['xulimoney'] <$amount*0.1){
			$data['status']=0;
			$data['info']='增益券金额不足';
		}else{
			$data['status']=1;
			$data['info']='已勾选使用增益券';
			$_SESSION['user_zyj']=1;
		}

		$this->ajaxReturn($data);
	}
	

	public function index(){

		$is_week = in_array(date("w"), array(0,6))?1:0;
		$this->assign('is_week',$is_week);
		
		$user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();

		$this->assign('user',$user);
		
		$this->display();
	}

    public function index_new(){

        $this->display();
    }

    public function dice(){
        $user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
        if($user["dice_time"] != date('Ymd')){
            $user["dice_time"] = date('Ymd');
            $user["dice_cnt"] = 10;
            M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save($user);
        }
        $this->assign('user',$user);
        $this->display();
    }

	public function getoption(){
        $array[0]=array('capital_name'=>'黄金','capital_key'=>'HJRMB');
        $array[1]=array('capital_name'=>'白银','capital_key'=>'BYRMB');
//		$array[2]=array('capital_name'=>'比特币','capital_key'=>'BTCCNY');
        $array[2]=array('capital_name'=>'美元/日元','capital_key'=>'USDJPY');
		$array[3]=array('capital_name'=>'英镑/美元','capital_key'=>'GBPUSD');
		$array[4]=array('capital_name'=>'欧元/美元','capital_key'=>'EURUSD');

		exit(json_encode($array));	
	}
	public function getCapitalTypes(){
		$array[0] = array('id'=>1,'trade_type'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产','capital_type'=>0,
		'capital_key'=>'EURUSD','capital_name'=>'欧元/美元','capital_time'=>0,'flag'=>1,'sort'=>3);
		
		$array[1] = array('id'=>2,'trade_type'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产','capital_type'=>0,
		'capital_key'=>'USDJPY','capital_name'=>'美元/日元','capital_time'=>0,'flag'=>1,'sort'=>1);
		
		$array[2] = array('id'=>3,'trade_type'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产','capital_type'=>0,
		'capital_key'=>'GBPUSD','capital_name'=>'英镑/美元','capital_time'=>0,'flag'=>1,'sort'=>1);

        $array[3] = array('id'=>4,'trade_type'=>0,'trade_name'=>'标准','product_type'=>1,'product_key'=>'hj','product_name'=>'货币对','capital_type'=>0,
            'capital_key'=>'HJRMB','capital_name'=>'黄金','capital_time'=>0,'flag'=>1,'sort'=>3);
//        $array[4] = array('id'=>5,'trade_type'=>0,'trade_name'=>'标准','product_type'=>1,'product_key'=>'by','product_name'=>'货币对','capital_type'=>0,
//            'capital_key'=>'BYRMB','capital_name'=>'白银','capital_time'=>0,'flag'=>1,'sort'=>3);

		exit(json_encode($array));
		
	}
	public function get_newallorder_ajax(){
		$orderlist =  M('juejin_order')->order('id desc')->limit(100)->select();
		$pinlist=array();
		$time = time();
		$is_week = in_array(date("w"), array(0,6))?1:0;

		foreach ($orderlist as $key=>$val){
			$pinlist[$key]['id'] = $val['id'];
			$pinlist[$key]['user_id'] = $val['userid'];
			$pinlist[$key]['trade_amount'] = $val['amount'];
			$pinlist[$key]['trade_direction'] = $val['dircetion'];
			$nowtime = $time-$key*10+rand(1,12);
			$pinlist[$key]['shijian'] = date('H:i:s',$nowtime);
			if($is_week){
				$pinlist[$key]['capital_name'] ='比特币';
			}else{
				$pinlist[$key]['capital_name'] =$this->capitalNaem[$val['capital']];
			}
				
		}
		
		$list['list']=$pinlist;
		$list['live_status']=0;
	
		$list['renshu']=15632+M('juejin_order')->group('userid')->count();
		$list['status']=1;
		$list['trade_count']=46521+M('juejin_order')->count();
		
		exit(json_encode($list));
		
	
		$array[0]=array('id'=>'5','option_id'=>5,'user_id'=>53596,'proxy_id'=>0,'trade_amount'=>'200.00',
				'trade_direction'=>0,'trade_point'=>1264.14,'trade_time'=>1475740769,'finish_time'=>1475740829,
				'failed_profit'=>0.00,'expect_profit'=>370.00,'profit'=>null,'is_settle'=>0,'settle_time'=>null,
				'settle_point'=>null,'is_win'=>null,'option_key'=>'XAUUSD','trade_type'=>0,'pruduct_type'=>null,
				'bot_flag'=>0,'settle_num'=>'0000','settle_ip'=>"121.62.154.163",'minute'=>null,'is_sim'=>1,
				'is_fast'=>0,'is_control'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产',
				'capital_type'=>4,'capital_key'=>'XAUUSD','capital_name'=>'黄  金','capital_time'=>0,'flag'=>1,'sort'=>7,'shijian'=>'15:59:35');
	
		$array[1]=array('id'=>'5','option_id'=>5,'user_id'=>53596,'proxy_id'=>0,'trade_amount'=>'200.00',
				'trade_direction'=>1,'trade_point'=>1264.14,'trade_time'=>1475740769,'finish_time'=>1475740829,
				'failed_profit'=>0.00,'expect_profit'=>370.00,'profit'=>null,'is_settle'=>0,'settle_time'=>null,
				'settle_point'=>null,'is_win'=>null,'option_key'=>'XAUUSD','trade_type'=>0,'pruduct_type'=>null,
				'bot_flag'=>0,'settle_num'=>'0000','settle_ip'=>"121.62.154.163",'minute'=>null,'is_sim'=>1,
				'is_fast'=>0,'is_control'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产',
				'capital_type'=>4,'capital_key'=>'XAUUSD','capital_name'=>'黄  金','capital_time'=>0,'flag'=>1,'sort'=>7,'shijian'=>'15:59:34');
	
		$array[2]=array('id'=>'5','option_id'=>5,'user_id'=>53596,'proxy_id'=>0,'trade_amount'=>'200.00',
				'trade_direction'=>1,'trade_point'=>1264.14,'trade_time'=>1475740769,'finish_time'=>1475740829,
				'failed_profit'=>0.00,'expect_profit'=>370.00,'profit'=>null,'is_settle'=>0,'settle_time'=>null,
				'settle_point'=>null,'is_win'=>null,'option_key'=>'XAUUSD','trade_type'=>0,'pruduct_type'=>null,
				'bot_flag'=>0,'settle_num'=>'0000','settle_ip'=>"121.62.154.163",'minute'=>null,'is_sim'=>1,
				'is_fast'=>0,'is_control'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产',
				'capital_type'=>4,'capital_key'=>'XAUUSD','capital_name'=>'黄  金','capital_time'=>0,'flag'=>1,'sort'=>7,'shijian'=>'15:59:33');
	
		$array[3]=array('id'=>'5','option_id'=>5,'user_id'=>53596,'proxy_id'=>0,'trade_amount'=>'200.00',
				'trade_direction'=>1,'trade_point'=>1264.14,'trade_time'=>1475740769,'finish_time'=>1475740829,
				'failed_profit'=>0.00,'expect_profit'=>370.00,'profit'=>null,'is_settle'=>0,'settle_time'=>null,
				'settle_point'=>null,'is_win'=>null,'option_key'=>'XAUUSD','trade_type'=>0,'pruduct_type'=>null,
				'bot_flag'=>0,'settle_num'=>'0000','settle_ip'=>"121.62.154.163",'minute'=>null,'is_sim'=>1,
				'is_fast'=>0,'is_control'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产',
				'capital_type'=>4,'capital_key'=>'XAUUSD','capital_name'=>'黄  金','capital_time'=>0,'flag'=>1,'sort'=>7,'shijian'=>'15:59:32');
	
		$array[4]=array('id'=>'5','option_id'=>5,'user_id'=>53596,'proxy_id'=>0,'trade_amount'=>'200.00',
				'trade_direction'=>1,'trade_point'=>1264.14,'trade_time'=>1475740769,'finish_time'=>1475740829,
				'failed_profit'=>0.00,'expect_profit'=>370.00,'profit'=>null,'is_settle'=>0,'settle_time'=>null,
				'settle_point'=>null,'is_win'=>null,'option_key'=>'XAUUSD','trade_type'=>0,'pruduct_type'=>null,
				'bot_flag'=>0,'settle_num'=>'0000','settle_ip'=>"121.62.154.163",'minute'=>null,'is_sim'=>1,
				'is_fast'=>0,'is_control'=>0,'trade_name'=>'标准','product_type'=>0,'product_key'=>'hot','product_name'=>'热门资产',
				'capital_type'=>4,'capital_key'=>'XAUUSD','capital_name'=>'黄  金','capital_time'=>0,'flag'=>1,'sort'=>7,'shijian'=>'15:59:31');
	
	
	
		$list['list']=$array;
		$list['live_status']=0;
		$list['renshu']=371914;
		$list['status']=1;
		$list['trade_count']=2303491;
	
		exit(json_encode($list));
	
	
	}
	public function getSameUser(){
		
		
		exit(json_encode(array(false)));
	}
	public function deal_dq(){
		
		/* amount:5000
		trade_time:60
		capital:BTCCNY
		product:hot
		dircetion:1
		sec:60秒
		deal_value:
		yield:undefined
		expect_profit:9000
		faild_profit:undefined */
		
		$user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		if($_SESSION['user_id'] == false || !$user){
			$this->ajaxReturn(['error'=>'登录失效，请刷新登录','type'=>'error']);
		}
		$is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;
		$key = ($is_sim==1)?'money':'coin';
		
		if($user[$key]<I("amount")){
			$this->ajaxReturn(['error'=>'账户余额不足，请充值！','type'=>'error']);
		}

		//
		$data['userid']=$_SESSION['user_id'];
		$data['phone']=$_SESSION['user_phone'];
		$data['createtime']=time();
		$data['capital']=I("capital");
		$data['amount']=I("amount");
		$data['dircetion']=I("dircetion");
		$data['deal_value']=I("deal_value");
		$data['trade_time']=I('trade_time');

		// 检查券
        $user_couponId = I('couponId');
        $user_coupon_count = I('coupon_count');

        $where = [];
        $where["over_time"] = array('gt',time());
        $where["count"] = array('egt',$user_coupon_count);
        $where["id"] = $user_couponId;
        $user_coupon = M("juejin_user_coupon")->where($where)->find();
        if($user_coupon == null){
            $data['user_couponId']=0;
            $data['user_coupon_count']=0;
        }
        else{
            $data['user_couponId']=$user_couponId;
            $data['user_coupon_count']=$user_coupon_count;
        }

        $bili = $this->getBili($user_coupon, $user_coupon_count);
		switch (I('trade_time')){
			case 300:
				$data['get_amount']=I("amount")*(1+0.75*$bili);
				break;
			case 180:
				$data['get_amount']=I("amount")*(1+0.8*$bili);
				break;
			case 60:
				$data['get_amount']=I("amount")*(1+0.85*$bili);
				break;
		}
		
		$data['is_sim']=$is_sim;
		$data['user_zyj']=$_SESSION['user_zyj']?$_SESSION['user_zyj']:0;
		if($data['user_zyj']>0){
			$_SESSION['user_zyj']=0;
			M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setDec('xulimoney',I("amount")*0.1);
		}
	
		if(M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setDec($key,I("amount"))){
			$newuser = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
			$id = M('juejin_order')->add($data);

			// 减券的个数
            if($user_coupon != null){
                M('juejin_user_coupon')->where(['id'=>$user_couponId])->setDec('count',$user_coupon_count);
                M('juejin_user_coupon')->where(['id'=>$user_couponId])->setInc('used',$user_coupon_count);
            }
			$date['error']='';
			$date['type']='success';
			$date['amount']=I("amount");
			$date['direction']=I('dircetion');//1涨 0 亏
			$date['deal_value']=I("deal_value");
			$date['user_deal_time']=time();
			$date['time_dif']=0;
			$date['capital']=I("capital");
			$date['id']=$id;
			$date['usermoney']=$newuser['money'];
			$date['sim_money']=$newuser['coin'];
			$date['e_price']=null;
			$date['e_is']=null;
			$date['fate']=0.15;
			$this->ajaxReturn($date);
		}
		else{
			$this->ajaxReturn(['error'=>'订单创建失败，请联系客服！','type'=>'error']);	
		}
		
	}

	private function getBili($user_coupon, $user_coupon_count){
        // 检查券是否合法
        if($user_coupon["coupon_type"] == "Win"){
            // 必盈
            return 1;
        }
        $res = 1;
        for($i = 0; $i < $user_coupon_count; $i++){
            $res = $res * 1.1;
        }

        return $res;
    }



    private function getDiceArr($dir){
        $c1 = rand(1,6);
        $c2 = rand(1,6);
        $c3 = rand(1,6);
        if($dir == 0){

            while($c1 + $c2 + $c3 >= 11){
                $c1 = rand(1,6);
                $c2 = rand(1,6);
                $c3 = rand(1,6);
            }
            return array($c1,$c2,$c3);
        }
        else{
            while($c1 + $c2 + $c3 < 11){
                $c1 = rand(1,6);
                $c2 = rand(1,6);
                $c3 = rand(1,6);
            }
            return array($c1,$c2,$c3);
        }
    }

    public function deal_dice(){

        /* amount:5000
        trade_time:60
        capital:BTCCNY
        product:hot
        dircetion:1
        sec:60秒
        deal_value:
        yield:undefined
        expect_profit:9000
        faild_profit:undefined */

        $user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
        if($_SESSION['user_id'] == false || !$user){
            $this->ajaxReturn(['error'=>'登录失效，请刷新登录','type'=>'error']);
        }
        $is_sim = 1;
        $key = 'money';

        if($user[$key]<I("amount")){
            $this->ajaxReturn(['error'=>'账户余额不足，请充值！','type'=>'error']);
        }

        // 检查用户今天是否玩过10次
        if($user["dice_cnt"] < 1 && $user["dice_time"] == date("Ymd")){
            $this->ajaxReturn(['error'=>'今天已经玩够10次，请明天再来！','type'=>'error']);
        }

        //
        $data['userid']=$_SESSION['user_id'];
        $data['phone']=$_SESSION['user_phone'];
        $data['createtime']=time();
        $data['capital']=I("capital");
        $data['amount']=I("amount");
        $data['dircetion']=I("dircetion");
        $data['deal_value']=I("deal_value");
        $data['trade_time']=I('trade_time');
        $data['type'] = 1;
        $data['get_amount']=I("amount");

        $result = array("points"=>"", "result"=> 0);
        //判断是否输赢，然后决定是增加还是减少金额
        $isWin = false;
        //todo:
        $suc_rand = rand(1,99);
        if($suc_rand <= 50){
            // 输
            if(I("dircetion") == 0){
                // 客户选小，就返回大
                $arr = $this->getDiceArr(1);
                $result["points"] = join("-", $arr);
                $result["result"] = 1;
            }
            else{
                // 客户选大，就返回小
                $arr = $this->getDiceArr(0);
                $result["points"] = join("-", $arr);
                $result["result"] = 0;
            }
        }
        else{
            $isWin = true;
            // 赢
            if(I("dircetion") == 0){
                // 客户选小，就返回小
                $arr = $this->getDiceArr(0);
                $result["points"] = join("-", $arr);
                $result["result"] = 0;
            }
            else{
                // 客户选大，就返回大
                $arr = $this->getDiceArr(1);
                $result["points"] = join("-", $arr);
                $result["result"] = 1;
            }
        }

        $data['is_sim']=$is_sim;
        $data['user_zyj']= 0;

        $ok = false;
        if($isWin){
            $ok = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setInc($key,I("amount"));
        }
        else{
            $ok = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setDec($key,I("amount"));
        }
        if($ok){
            $newuser = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
            $newuser["dice_cnt"] = $newuser["dice_cnt"] - 1;
            $newuser["dice_time"] = date("Ymd");
            M('juejin_users')->where(['id'=>$_SESSION['user_id']])->save($newuser);

            $id = M('juejin_order')->add($data);
            $result["money"] = $newuser["money"];
            $result["dice_cnt"] = $newuser["dice_cnt"];
            $date['error']='';
            $date['type']='success';
            $date['data'] = $result;
            $this->ajaxReturn($date);
        }
        else{
            $this->ajaxReturn(['error'=>'订单创建失败，请联系客服！','type'=>'error']);
        }

    }



	public function get_now_orders(){
		/* $type =I('type');//XAUUSD
		$date['is_sim']=1;
		$date['status']=1;
		$date['list'][]= array('id'=>6377845,'expect_profit'=>3700.00,'finish_time'=>'10-13 10:32:28',
				'is_settle'=>'未结算','option_key'=>'黄  ','settle_time'=>'01-01 08:00:00','trade_amount'=>2000.00,
				'trade_direction'=>'买涨','trade_point'=>1258.67,'trade_time'=>'10-13 10:31:28','trade_type'=>1
		);
		$this->ajaxReturn($date); */

        $is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;
        $data['is_sim']=$is_sim;
        $data['status']=1;
        $page = I('page')?I('page'):1;
        $limit =( ($page-1)*15).',15';
        $now=time();
        $timewhere['createtime'] = array('gt',$now - 10*60);//10分钟内的订单
        $list = M('juejin_order')->where(['userid'=>$_SESSION['user_id'],'is_sim'=>$is_sim])->where($timewhere)->limit($limit)->order('id desc')->select();
        if(empty($list)){
            $this->ajaxReturn(['status'=>'no']);
        }
        $orderlist = array();
        foreach ($list as $key=>$val) {

            $orderlist[$key]['id'] = $val['id'];
            $orderlist[$key]['trade_amount'] = $val['amount'];
            $orderlist[$key]['expect_profit'] = $val['get_amount'];
            $orderlist[$key]['trade_direction'] = ($val['capital'] == 'DICE') ? $this->capitalNaem[$val['dircetion']] : (($val['dircetion'] == 1) ? '买涨' : '买跌');
            $orderlist[$key]['trade_point'] = number_format($val['deal_value'], 2, '.', ',');
            $orderlist[$key]['trade_time'] = date('m-d H:i:s', $val['createtime']);
            $orderlist[$key]['option_key'] = $this->capitalNaem[$val['capital']];
			 $orderlist[$key]['trade_type'] = 1;
            if ($val['createtime'] + $val['trade_time'] > $now) {
                $orderlist[$key]['is_settle'] = '未结算';
            } else {               
                $orderlist[$key]['is_settle'] = $val['is_win'] ? '<font color=red>盈利</font>' : '<font color=green>亏损</font>';//
            }
        }
        $data['list']=$orderlist;


        $this->ajaxReturn($data);
	}

	public function get_history_ajax(){
		$id = I('id');
		$type=I('type');//1 flow_span_value
		$order = M('juejin_order')->find($id);
		$user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
		$deal_value =I("deal_value")?I("deal_value"):$order['deal_value'];
		$is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;

        // 网络延迟导致
		if($order["type"] == 1){
            $newuser = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
            $date['is_win']=$newuser['is_win'];;
            $date['money']=$newuser['money'];
            $date['sim_money']=$newuser['coin'];
            $date['profit']=$order['amount'];
            $date['sim_money']=$user['coin'];

            $date['is_sim']=$is_sim;
            $date['close']=$order["end_value"];

            $date['Klinetime']=$order['createtime']+60;
            $date['capital_name']=$this->capitalNaem[$order['capital']];
            $date['option_key']=$order['capital'];
            $date['expect_profit']=$order['get_amount'];
            $date['failed_profit']=$order['amount'];

            $date['finish_time']=$order['createtime']+60;
            $date['id']=$order['id'];
            $date['trade_amount']=$order['amount'];
            $date['trade_direction']=$order['dircetion'];

            $date['trade_point']=$order['deal_value'];//开始价格
            $date['trade_time']=$order['createtime'];
            $date['trade_type']='1';
            $date['user_id']=$_SESSION['user_id'];//6376396
            $date['settle_point']=$order["end_value"];


            $date['high']='1256.06';
            $date['is_settle']=1;
            $date['low']='1255.87';
            $date['open']='1255.89';
            $date['option_id']='5';
            $date['proxy_id']='0';
            $date['pruduct_type']=null;
            $date['status']='1';
            exit(json_encode($date));
        }
		
		$setting = M('juejin_set')->find(1);
	
		$end_value = 0;	
		if($setting['status']==1){
			$suc_rand = rand(1,99);
			switch ($order['capital']){
				case 'GBPUSD'://英镑美元
					$buy_rand =rand(1,5)/10000;
					break;
				case 'USDJPY'://美元日元
					$buy_rand =rand(1,5)/1000;
					break;
				case 'EURUSD'://欧元美元
					$buy_rand =rand(1,5)/10000;
					break;
				case 'BTCCNY':
					$buy_rand =rand(1,5)/100;
					break;
                case 'HJRMB'://黄金人民币
                    $buy_rand =rand(1,5)/1000;
                    break;
                case 'BYRMB'://白银人民币
                    $buy_rand =rand(1,5)/1000;
                    break;
			}
			if($is_sim==1){//实盘
				if($setting['suc_true']<$suc_rand){//失败
					if($order['dircetion']==1){//涨
						($deal_value<$order['deal_value'])?($end_value = $deal_value):($end_value = $order['deal_value'] -$buy_rand);
					}else{
						($deal_value<$order['deal_value'])?($end_value = $order['deal_value'] + $buy_rand):($end_value = $deal_value);
					}
				}else{//赢
					if($order['dircetion']==1){//涨
						($deal_value<$order['deal_value'])?($end_value = $order['deal_value'] + $buy_rand):($end_value = $deal_value);
					}else{
						($deal_value<$order['deal_value'])?($end_value = $deal_value):($end_value = $order['deal_value'] - $buy_rand);
					}
				}
			}else{
				if($setting['suc_xu']<$suc_rand){//shibai
					if($order['dircetion']==1){//涨
						($deal_value<$order['deal_value'])?($end_value = $deal_value):($end_value = $order['deal_value'] -$buy_rand);
					}else{
						($deal_value<=$order['deal_value'])?($end_value = $order['deal_value'] + $buy_rand):($end_value = $deal_value);
					}
				}else{//赢
					if($order['dircetion']==1){//涨
						($deal_value<=$order['deal_value'])?($end_value = $order['deal_value'] + $buy_rand):($end_value = $deal_value);
				
					}else{
						($deal_value<$order['deal_value'])?($end_value = $deal_value):($end_value = $order['deal_value'] - $buy_rand);
					}
				}
			}
		}else{
			$end_value = $deal_value;
		}
		$key = ($is_sim==1)?'money':'coin';
		$isUserWin = $this->isUseWinCoupon($order);
		if( (($end_value-$order['deal_value'])>0 && $order['dircetion']==1) || (($end_value-$order['deal_value'])<0 && $order['dircetion']==0) || $isUserWin){
			$date['is_win']=1;

			$date['profit']=$order['get_amount'];	
			M('juejin_users')->where(['id'=>$_SESSION['user_id']])->setInc($key,$date['profit']);
			M('juejin_order')->where(['id'=>$order['id']])->save(['is_win'=>1,'type'=>1,'end_value'=>$end_value]);
				
			$newuser = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
			$date['money']=$newuser['money'];
			$date['sim_money']=$newuser['coin'];
		}
		else{
			$date['is_win']=0;
			$date['profit']=$order['amount'];
			$date['money']=$user['money'];
			$date['sim_money']=$user['coin'];
			M('juejin_order')->where(['id'=>$order['id']])->save(['is_win'=>0,'type'=>1,'end_value'=>$end_value]);
		}
	
		$date['is_sim']=$is_sim;
		$date['close']=$end_value;
	
		$date['Klinetime']=$order['createtime']+60;
		$date['capital_name']=$this->capitalNaem[$order['capital']];
		$date['option_key']=$order['capital'];
		$date['expect_profit']=$order['get_amount'];
		$date['failed_profit']=$order['amount'];
	
		$date['finish_time']=$order['createtime']+60;
		$date['id']=$order['id'];
		$date['trade_amount']=$order['amount'];
		$date['trade_direction']=$order['dircetion'];
	
		$date['trade_point']=$order['deal_value'];//开始价格
		$date['trade_time']=$order['createtime'];
		$date['trade_type']='1';
		$date['user_id']=$_SESSION['user_id'];//6376396
		$date['settle_point']=$end_value;
	
		
		$date['high']='1256.06';
		$date['is_settle']=1;
		$date['low']='1255.87';
		$date['open']='1255.89';
		$date['option_id']='5';
		$date['proxy_id']='0';
		$date['pruduct_type']=null;
		$date['status']='1';
		exit(json_encode($date));
	}
	
	public function lastKlineParameter(){
		$capitalType = I('capitalType');
		$where['type']= $capitalType;
		
		switch ($capitalType){
			case 'BTCCNY':
				$add_rand = rand(-5,5)/100;
				break;
			case 'GBPUSD'://英镑美元
				$add_rand = rand(-5,5)/10000;
				
				break;
			case 'USDJPY'://美元日元
				$add_rand = rand(-5,5)/1000;
				
				break;
			case 'EURUSD'://欧元美元
				$add_rand = rand(-5,5)/10000;
				
				break;
            case 'HJRMB'://黄金人民币
                $add_rand = rand(-5,5)/1000;

                break;
            case 'BYRMB'://白银人民币
                $add_rand = rand(-5,5)/1000;

                break;
		}

		$info = M("juejin_msg")->where($where)->order('id desc')->find();
		$time = strtotime(date('Ymd H:i'));
		$info['buy'] = $info['buy']+$add_rand;

		// kline
        $obj = ["symbol"=>$capitalType,
            "TypeName"=>$capitalType,
            "ctm"=>date('Y-m-d H:i:s'),
            "bid"=>$info['last'],
            "ask"=>$info['buy'],
            "Low"=>$info['low'],
            "High"=>$info['high']
        ];

		$data=[$time,$info['buy'],$capitalType,I('diff'),0,$obj];
//        "symbol":"SB",
//                        "TypeName":"SB",
//                        "ctm":"2017-02-09T17:03:12",
//                        "bid":2972.7,
//                        "ask":2972.7,
//                        "Low":2938.44,
//                        "High":2976.93

		$this->ajaxReturn($data);		
	}

    public function highStockChart(){
        $capitalType = I('type');
        $range = I("range");
        if($range == "1M")
        {
            $range = "M1";
        }
        $count = I("count");
        $diff = substr($range, 1) * 60;
        $where['type']= $capitalType;
        $decimalPoint = 2;

        $where['createtime']= array('gt',time()-($count*$diff));

        $list = M("juejin_minmsg")->where($where)->order('id asc')->select();
        switch ($capitalType){
            case 'BTCCNY':
                $decimalPoint = 2;
                $print = "%.2f";
                break;
            case 'GBPUSD'://英镑美元
                $decimalPoint = 4;
                $print = "%.4f";
                break;
            case 'USDJPY'://美元日元
                $decimalPoint = 3;
                $print = "%.3f";
                break;
            case 'EURUSD'://欧元美元
                $decimalPoint = 4;
                $print = "%.4f";
                break;
            case 'HJRMB'://黄金人民币
                $decimalPoint = 3;
                $print = "%.3f";
                break;
            case 'BYRMB'://白银人民币
                $decimalPoint = 3;
                $print = "%.3f";
                break;
        }
        $i=1;
        $d = substr($range, 1);
        foreach ($list as $key=>$val){
            $second = date('i',$val['createtime']);
            if($second % $d == 0){
                $idx = $i-1;
                $date[$idx][]=date('Y-m-d H:i:s',$val['createtime']);
                $date[$idx][]=sprintf($print, $val['buy']);
                $date[$idx][]=sprintf($print, $val['last']); //$val['last'];//$val['low'];
                $date[$idx][]=sprintf($print, $val['high']);// $val['high'];
                $date[$idx][]=sprintf($print, $val['low']); //$val['low'];
                $date[$idx][]=$i++;

                $time[$idx]= date('i:s',$val['createtime']);
                $price[$idx]=floatval(sprintf($print, $val['last']));
            }
        }
        $data['range'] = $range;
        $data['decimalPlace'] = $decimalPoint;
        $data['quotesArr']=$date;
        $data['type']=$capitalType;
        $data['timeArr']=$time;
        $data['priceArr']=$price;
        $data['msg']='ok';
        $this->ajaxReturn($data);
    }
	
	public function klineChart(){
		$capitalType = I('capitalType');
        $diff=I('diff');
		$where['type']= $capitalType;
		$where['createtime']= array('gt',time()-30*$diff);

		$list = M("juejin_minmsg")->where($where)->order('id asc')->select();	
		switch ($capitalType){
			case 'BTCCNY':
				$decimalPoint = 2;
				$print = "%.2f";
				break;
			case 'GBPUSD'://英镑美元
				$decimalPoint = 4;
				$print = "%.4f";
				break;
			case 'USDJPY'://美元日元
				$decimalPoint = 3;
				$print = "%.3f";
				break;
			case 'EURUSD'://欧元美元
				$decimalPoint = 4;
				$print = "%.4f";
				break;
            case 'HJRMB'://黄金人民币
                $decimalPoint = 3;
                $print = "%.3f";
                break;
            case 'BYRMB'://白银人民币
                $decimalPoint = 3;
                $print = "%.3f";
                break;
		}
		foreach ($list as $key=>$val){
			$date[$key][]=$val['createtime'];
			$date[$key][]=sprintf($print, $val['buy']);
			$date[$key][]=sprintf($print, $val['last']); //$val['last'];//$val['low'];
			$date[$key][]=sprintf($print, $val['high']);// $val['high'];
			$date[$key][]=sprintf($print, $val['low']); //$val['low'];
			$date[$key][]=0;
		}

		$data['data']=$date;
		$data['decimalPoint']=$decimalPoint;
		$data['capitalType']=$capitalType;
		$data['diff']=$diff;
		$this->ajaxReturn($data);
	}

    public function jueshengquan(){
        $user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
        $ads = M('juejin_ad')->order('sort')->select();
        $this->assign('user',$user);
        $this->assign('ads', $ads);
        $this->display();
    }

    public function timeline(){
        $user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
        $config = $this->getWxShareConfig();
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }

    public function post_content(){
        $uid = I('uid');
        $user = M('juejin_users')->where(['id'=>$_SESSION['user_id']])->find();
        $date['uid'] = $uid;
        $arr = json_decode(htmlspecialchars_decode(I("serverId")));
        $config = $this->getWxConfig();
        $util =  new \Spark\Wechat\WechatUtil($config['appid'],$config['appsecret']);
        $serverIds = [];
        foreach ($arr as $id){
            \Think\Log::write("serverId:".$id);
            $path = $util->downloadWXImg($id);
            array_push($serverIds, $path);
        }
        $date["uname"] = $user["wechat_name"];
        $date["headImgUrl"] = $user["headimgurl"];
        $date['serverId'] = join(",", $serverIds);
        $date['Content'] = I("content");
        $date['likes'] = 0;
        $date['status'] = 0;
        $date['create_time']= time();
        $res = M("juejin_content")->add($date);
        if($res){
            $this->ajaxReturn(['status'=>'1','Code'=>'','info'=>'success']);
        }
        $this->ajaxReturn(['status'=>'0','Code'=>'','info'=>'发表失败']);
    }

    public function get_js_list(){
        $page = I("page");
        $start = 10 * ($page - 1);
        $result = M("juejin_content")->where(['status'=>'1'])->order('id DESC')->limit($start.',10')->select();
        $list = [];
        foreach ($result as $res)
        {
            $res["imglist"] = explode(",", $res["serverId"]);
            $count = count($res["imglist"]);
            $width = 100;
            if($count == 1){
                $width = 100;
            }
            else if($count == 2 || $count == 4){
                $width = 96 / 2;
            }
            else if ($count == 3 || $count == 5 || $count == 6){
                $width = 96 / 3;
            }

            $res["width"] = $width;
            $list[] = $res;
        }
        $this->assign("result", $list);
        $this->display();
    }

    public function  set_likes(){
        $mid = I("aid");
        $status = I("status");
        if($status == 1){
            //增加1
            M("juejin_content")->where(["id"=>$mid])->setInc("likes", 1);
        }
        else{
            M("juejin_content")->where(["id"=>$mid])->setDec("likes", 1);
        }
    }

    public function coupon(){
        $this->display();
    }

    public function coupon_detail(){
        $this->display();
    }

    public function getmycoupon(){
        $type = I("type");
        $pageIndex = I("pageindex");
        $pageSize = I("pagesize");
        $where = [];
        $where["phone"] = $_SESSION['user_phone'];
        if($type == 1){
            //未使用
            $where["over_time"] = array('gt',time());
            $where["count"] = array('gt',0);
        }
        else if($type == 2){
            // 已使用
            $where["used"] = array('gt',0);
        }
        else{
            // 已过期
            $where["over_time"] = array('lt',time());
        }
        $count = M("juejin_user_coupon")->where($where)->count();
        $start = ($pageIndex-1)*$pageSize;
        $list = M("juejin_user_coupon")->where($where)->limit($start.','.$pageSize)->select();
        $win_coupon = M('juejin_coupon')->where(['type'=>'Win'])->find();
        $incr_coupon = M('juejin_coupon')->where(['type'=>'Incr'])->find();
        $res = [];
        foreach ($list as $item){
            if($item["coupon_type"]=="Win"){
                $item["coupon"] = $win_coupon;
            }
            else{
                $item["coupon"] = $incr_coupon;
            }
            $item["add_time"] = date('Y-m-d H:i', $item["add_time"]);
            $item["over_time"] = date('Y-m-d H:i', $item["over_time"]);
            $res[] = $item;
        }
        $data["List"] = $res;
        $data["total"] = $count;
        $this->ajaxReturn($data);
    }

    public function getUserCoupon(){
        $is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;
        if($is_sim == 0){
            $this->ajaxReturn([]);
        }
        else{
            $type_name = I("type_name");
            $money = I("money");
            $where = [];
            $where["phone"] = $_SESSION['user_phone'];
            $where["over_time"] = array('gt',time());
            $where["count"] = array('gt',0);
            $list = M("juejin_user_coupon")->where($where)->select();
            $win_coupon = M('juejin_coupon')->where(['type'=>'Win'])->find();
            $incr_coupon = M('juejin_coupon')->where(['type'=>'Incr'])->find();
            $res = [];
            foreach ($list as $item){
                if($item["coupon_type"]=="Win"){
                    $item["coupon"] = $win_coupon;
                }
                else{
                    $item["coupon"] = $incr_coupon;
                }
                if($item["coupon"]["use_area"] != $type_name && $item["coupon"]["use_area"] != "ALL"){
                    continue;
                }
                if($item["coupon"]["satisfy_amount"] > $money){
                    continue;
                }
                $item["add_time"] = date('Y-m-d H:i', $item["add_time"]);
                $item["over_time"] = date('Y-m-d H:i', $item["over_time"]);
                $res[] = $item;
            }
            $this->ajaxReturn($res);
        }
    }

    private function isUseWinCoupon($order){
        $is_sim = $_SESSION['is_sim']?$_SESSION['is_sim']:0;
        if($is_sim == 0){
            return false;
        }
        $coupon = M("juejin_user_coupon")->where(["id"=>$order["user_couponId"]])->find();
        if($coupon != null && $coupon["coupon_type"] == "Win"){
            return true;
        }
        return false;
    }

    protected function getWxShareConfig(){
        $config = $this->wxConfig;
        $util = new \Spark\Wechat\WechatUtil($config['appid'],$config['appsecret']);
        $data['jsapi_ticket'] = $util->getJsApiTicket();
        $data['timestamp'] = time();
        $data['noncestr'] = create_key();
        $data['url'] = C('WAP_DOMAIN').$_SERVER['REQUEST_URI'];
        $plain = 'jsapi_ticket='.$data['jsapi_ticket'].'&noncestr='.$data['noncestr'].'&timestamp='.$data['timestamp'].'&url='.$data['url'];
        $signature = sha1($plain);
        return ['appid'=>$config['appid'],'signature'=>$signature,'timestamp'=>$data['timestamp'],'noncestr'=>$data['noncestr']];
    }
	
	protected $capitalNaem = array('BTCCNY'=>'比特币','GBPUSD'=>'英镑/美元','USDJPY'=>'美元/日元','EURUSD'=>'欧元/美元','HJRMB'=>'黄金','BYRMB'=>'白银', 'DICE'=>'玩色子');//黄金-字母对比
    protected $diceNaem = array('0'=>'买小','1'=>'买大');//黄金-字母对比

}
?>