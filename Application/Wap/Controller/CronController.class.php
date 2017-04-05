<?php
namespace Wap\Controller;
class CronController extends Wap1Controller{
	public $BTCCNY_URl = 'https://www.btc123.com/api/getTicker?symbol=okcoinbtcusdfuture';
	public function setsecond(){

		$where['createtime']= array('lt',time()-480*60*16);
		M("juejin_minmsg")->where($where)->delete();
		M("juejin_msg")->where($where)->delete();

		$url = $this->BTCCNY_URl;
		$json_data = $this->postData($url);
		$date = json_decode($json_data);
		//echo $date->datas->ticker;
		$nowtime = time();
		$mintime = strtotime(date('Ymd H:i',$nowtime));
		if($date->isSuc){//调取成功
			$getArray = $date->datas->ticker;
			$add['createtime']= $nowtime;
			$add['gettime']=$getArray->date;
			$add['mintime']=$mintime;
			$add['buy']=$getArray->buy;
			$add['last']=$getArray->last;
			$add['type']='BTCCNY';
			($getArray->last > $add['buy'])?($add['high'] =$getArray->last ):($add['low']=$getArray->last);	
			empty($add['high'])?($add['high']=$add['buy']+rand(1,9)/100):($add['low']=$add['buy']-rand(1,9)/100);	
			M("juejin_msg")->add($add);

			$info = M("juejin_minmsg")->where(['createtime'=>$mintime,'type'=>'BTCCNY'])->count();
			//$cachekey = 'BTCCNY'.$mintime;
			if(!$info){
				//
				M("juejin_minmsg")->add(['createtime'=>$mintime,'type'=>'BTCCNY','last'=>$add['buy'],'buy'=>$add['buy'],'high'=>$add['high'],'low'=>$add['low']]);
				$maxScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>'BTCCNY'])->max('high');
				$minScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>'BTCCNY'])->min('low');
				$avgScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>'BTCCNY'])->avg('buy');
				M("juejin_minmsg")->where(['createtime'=>$mintime-60,'type'=>'BTCCNY'])->save(['high'=>$maxScore,'low'=>$minScore,'last'=>$avgScore]);
				//S($cachekey,1,360);
			}
		}
		$this->juhe();
        $this->juhe2();
	}
	public function juhe(){
			
		//************2.外汇汇率************
		$url = "http://web.juhe.cn:8080/finance/exchange/frate?key=daf2553d9d0a680145cff3ad0a28e43b";		
		$content = $this->postData($url);
		$result = json_decode($content,true);
		
		$nowtime = time();
		$mintime = strtotime(date('Ymd H:i',$nowtime));
		
		if($result){
			if($result['error_code']=='0'){
				$array[0]=$result['result'][0]['data4'];
				$array[1]=$result['result'][0]['data10'];
				$array[2]=$result['result'][0]['data3'];
	
				foreach ($array as $key=>$val){
					switch ($val['code']){
						case 'GBPUSD'://英镑美元
							$f_rand = rand(-5,5)/10000;
							$add_rand = rand(1,5)/10000;
							$buy_rand =rand(1,5)/10000;
							break;
						case 'USDJPY'://美元日元
							$f_rand = rand(-5,5)/1000;
							$add_rand = rand(1,5)/1000;
							$buy_rand =rand(1,5)/1000;
							break;
						case 'EURUSD'://欧元美元
							$f_rand = rand(-5,5)/10000;
							$add_rand = rand(1,5)/10000;
							$buy_rand =rand(1,5)/10000;
							break;
					}
					$add = array();
					$add['createtime']= $nowtime;
					$add['gettime']=$val['datatime'];
					$add['mintime']=$mintime;
					$add['buy']=$val['buyPic']+$buy_rand;
					$add['last']=$add['buy']+$f_rand; 
					$add['type']=$val['code'];
					
					($add['last'] > $add['buy'])?($add['high'] =$add['last'] ):($add['low']=$add['last']);
					empty($add['high'])?($add['high']=$add['buy']+$add_rand):($add['low']=$add['buy']-$add_rand);
					M("juejin_msg")->add($add);	
					$cachekey = $val['code'].$mintime;
					$info = M("juejin_minmsg")->where(['createtime'=>$mintime,'type'=>$add['type']])->count();
					if(!$info){
						//
						M("juejin_minmsg")->add(['createtime'=>$mintime,'type'=>$val['code'],'last'=>$add['buy'],'buy'=>$add['buy'],'high'=>$add['high'],'low'=>$add['low']]);
						$maxScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->max('high');
						$minScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->min('low');
						$avgScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->avg('last');
						M("juejin_minmsg")->where(['createtime'=>$mintime-60,'type'=>$val['code']])->save(['high'=>$maxScore,'low'=>$minScore,'last'=>$avgScore]);
					}
					
				}				
				echo 1;
		
			}else{
				echo $result['error_code'].":".$result['reason'];
			}
		}else{
			echo "请求失败";
		}
		
		
	}


    public function juhe2(){

        //************黄金白银************
        $url = "http://web.juhe.cn:8080/finance/gold/bankgold?key=402af678186c2751cc72b749b656919a";
        $content = $this->postData($url);
        $result = json_decode($content,true);

        $nowtime = time();
        $mintime = strtotime(date('Ymd H:i',$nowtime));

        if($result){
            if($result['error_code']=='0'){
                //$array[0]=$result['result'][0]['5'];
                //$array[1]=$result['result'][0]['6'];
                $array[0]=$result['result'][0]['6'];
                foreach ($array as $key=>$val){
                    switch ($val['variety']){
                        //case '人民币账户黄金'://人民币账户黄金
                        //    $f_rand = rand(-5,5)/1000;
                        //    $add_rand = rand(1,5)/1000;
                       //     $buy_rand =rand(1,5)/1000;
                       //     $val['code']="HJRMB";
                       //     break;
                        case '人民币账户白银'://人民币账户白银
                            $f_rand = rand(-5,5)/1000;
                            $add_rand = rand(1,5)/1000;
                            $buy_rand =rand(1,5)/1000;
                            $val['code']="BYRMB";
                            break;
                    }
                    $add = array();
                    $add['createtime']= $nowtime;
                    //$add['gettime']=$val['time'];
                    $add['gettime'] = strtotime(date('H:i',$val['time']));
                    $add['mintime']=$mintime;
                    $add['buy']=$val['buypri']+$buy_rand;
                    $add['last']=$add['buy']+$f_rand;
                    $add['type']=$val['code'];

                    ($add['last'] > $add['buy'])?($add['high'] =$add['last'] ):($add['low']=$add['last']);
                    empty($add['high'])?($add['high']=$add['buy']+$add_rand):($add['low']=$add['buy']-$add_rand);
                    M("juejin_msg")->add($add);
                    $cachekey = $val['code'].$mintime;
                    $info = M("juejin_minmsg")->where(['createtime'=>$mintime,'type'=>$add['type']])->count();
                    if(!$info){
                        //
                        M("juejin_minmsg")->add(['createtime'=>$mintime,'type'=>$val['code'],'last'=>$add['buy'],'buy'=>$add['buy'],'high'=>$add['high'],'low'=>$add['low']]);
                        $maxScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->max('high');
                        $minScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->min('low');
                        $avgScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->avg('last');
                        M("juejin_minmsg")->where(['createtime'=>$mintime-60,'type'=>$val['code']])->save(['high'=>$maxScore,'low'=>$minScore,'last'=>$avgScore]);
                    }

                    //黄金
                    $val['code']='HJRMB';
                    $add = array();
                    $add['createtime']= $nowtime;
                    //$add['gettime']=$val['time'];
                    $add['gettime'] = strtotime(date('H:i',$val['time']));
                    $add['mintime']=$mintime;
                    $add['buy']=$val['buypri']+$buy_rand + 253 + rand(1,5)/1000;
                    $add['last']=$add['buy']+$f_rand;
                    $add['type']=$val['code'];

                    ($add['last'] > $add['buy'])?($add['high'] =$add['last'] ):($add['low']=$add['last']);
                    empty($add['high'])?($add['high']=$add['buy']+$add_rand):($add['low']=$add['buy']-$add_rand);
                    M("juejin_msg")->add($add);
                    $cachekey = $val['code'].$mintime;
                    $info = M("juejin_minmsg")->where(['createtime'=>$mintime,'type'=>$add['type']])->count();
                    if(!$info){
                        //
                        M("juejin_minmsg")->add(['createtime'=>$mintime,'type'=>$val['code'],'last'=>$add['buy'],'buy'=>$add['buy'],'high'=>$add['high'],'low'=>$add['low']]);
                        $maxScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->max('high');
                        $minScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->min('low');
                        $avgScore = M("juejin_msg")->where(['mintime'=>$mintime-60,'type'=>$val['code']])->avg('last');
                        M("juejin_minmsg")->where(['createtime'=>$mintime-60,'type'=>$val['code']])->save(['high'=>$maxScore,'low'=>$minScore,'last'=>$avgScore]);
                    }

                }
                echo 1;

            }else{
                echo $result['error_code'].":".$result['reason'];
            }
        }else{
            echo "请求失败";
        }


    }
    public function get_newprofit()
    {
        $url = "http://f.fangwuedu.com/mobile.php/api/getnewprofit";
        $content = $this->postData($url);
        echo $content;
    }
    public function checkTable(){
        $Model = M();
        $result1 = $Model->execute("repair table sp_juejin_msg;");
        $result2 = $Model->execute("repair table sp_juejin_minmsg;");
        echo "请求结果".$result1."_".$result2;
    }
	public function test1(){
		$cachekey = 'BTCCNY1';
		if(!S($cachekey)){
			echo 'on';
			S($cachekey,222,360);
		}else{
			echo S($cachekey);
		}

	}
	public function get_history(){
		$list = M('juejin_order')->where(['type'=>0])->select();
		//echo M('juejin_order')->getlastsql();die;
		foreach ($list as $key => $value) {
			# code...
			
			if(($value['createtime']+$value['trade_time']+3) < time()){//关闭等待，系统判定输赢
				$id = $value['id'];
				
				$user = M('juejin_users')->where(['id'=>$value['userid']])->find();
				if($value['dircetion']==1){//涨价
					$rand= rand(-85,15)/100;
				}else{
					$rand= rand(-15,85)/100;//
				}
				$deal_value = $value['deal_value'] + $rand;

				$key = ($value['is_sim']==1)?'money':'coin';
				
				if( ($rand>0 && $value['dircetion']==1) || ($rand<0 && $value['dircetion']==0) ){
					$date['is_win']=1;
					$date['profit']=$value['get_amount'];
					M('juejin_users')->where(['id'=>$value['userid']])->setInc($key,$date['profit']);
					M('juejin_order')->where(['id'=>$value['id']])->save(['is_win'=>1,'type'=>1,'end_value'=>$deal_value]);
				}
				else{
					$date['is_win']=0;
					M('juejin_order')->where(['id'=>$value['id']])->save(['is_win'=>0,'type'=>1,'end_value'=>$deal_value]);
				}
				
			}
		}
		echo 1;
	}
	protected $capitalNaem = array('BTCCNY'=>'比特币','GBPUSD'=>'英镑/美元','USDJPY'=>'美元/日元','EURUSD'=>'欧元/美元','HJRMB'=>'黄金','BYRMB'=>'白银');//黄金-字母对比


	function postData($url){
		$ch = curl_init();
		$timeout = 300;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		//下面这句是绕过SSL验证，http://www.jb51.net/article/29282.htm，不然有些机器无法获得返回数据
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$handles = curl_exec($ch);
		curl_close($ch);
		return $handles;
	}
}
?>