<?php
namespace Wap\Controller;
class LoginController extends WapController{

	public function login_show(){
		$this->display();
	}
	public function regist(){
        $setting = M('juejin_set')->find(1);
        // 检查是否开启邀请码
        $this->show = $setting["invite_status"] == 0 ? "display:none;" : "display:block;";
		$this->display();
	}
	public function login(){
		$phone = I("tel");
		$pas = I("usrPwd");
		
		$user = M('juejin_users')->where(['phone'=>$phone,'password'=>md5($pas)])->find();
	//echo M('juejin_users')->getlastsql();
		if($user){
			$_SESSION['user_id']=$user['id'];
			$_SESSION['user_phone']=$user['phone'];
			if(is_valid_openid($this->wechat_id)){
				$open_id=$this->wechat_id;
				$wechat=M('wechat_user')->where(array('open_id'=>$open_id))->setField('phone',$user['phone']);
				$wechat_user = $this->getWechatInfo();
				M('juejin_users')->where(['phone'=>$phone])->save(['headimgurl'=>$wechat_user['headimgurl'],'wechat_name'=>$wechat_user['wechat_name']]);
			}

			// 登陆暂时送券
            // 赠送1张必盈券和9张增益券
//            $this->send_coupon($phone);

			$this->ajaxReturn(['status'=>1,'message'=>'登录成功']);
		}else{
			$this->ajaxReturn(['status'=>0,'info'=>'登录失败，请确定是否注册']);
		}
		
	}
	public function img_send_sms_data(){
		$post = json_decode($_POST['data']);
		//$verify = new \Think\Verify();
		//$verify->reset = false;
		//$code =$post->verify_code;
		
	 	/* if(!$verify->check($code)){
			$this->ajaxReturn(['status'=>'0','Code'=>$code,'message'=>'验证码错误']);
		}  */
		
		$phone =$post->phone;	
		
		$hasuser = M("juejin_users")->where(['phone'=>$phone])->count();
		if($hasuser){
			$this->ajaxReturn(['status'=>0,'message'=>'该手机号码已经注册，请登录。。']);
		}
		$verify1 = rand(1000,9999);
		$content='短信验证码为:'.$verify1.',请勿将验证码提供给他人.';
		$ret = send_newsms($phone,$content);
		if($ret){
			$_SESSION['mobile_code']=$verify1;
			$this->ajaxReturn(['status'=>'success','Desc'=>'发送成功']);
		}else{
			$this->ajaxReturn(['status'=>0,'Code'=>'','message'=>'短信发送失败！']);
		}		
	}
	public function regist_user(){
		$phone = I("tel");
		$passwd = I("passwd");
		$SMSCode = I("SMSCode");
		$inviteCode = I("invite");
		if($SMSCode !=$_SESSION['mobile_code'] || $SMSCode==''){
			$this->ajaxReturn(['status'=>'0','Code'=>'','info'=>'短信验证码输入错误！']);
		}else{
            $setting = M('juejin_set')->find(1);
            // 检查是否开启邀请码
            if($setting["invite_status"] == 1){
                // 开启验证，检查邀请码是否正确
                $invite = M('juejin_invite_code')->where(['code'=>$inviteCode])->find();
                if($invite == null || $inviteCode["is_used"] == 1){
                    // 邀请码不存在或者已使用，报错
                    $this->ajaxReturn(['status'=>0,'info'=>'注册失败，请输入正确的邀请码']);
                }
                else{
                    M('juejin_invite_code')->where(['code'=>$inviteCode])->setField('is_used',1);
                    M('juejin_invite_code')->where(['code'=>$inviteCode])->setField('used_phone',$phone);
                }
            }

			//插入user 表
			$date['phone']=$phone;
			$date['password']=md5($passwd);
			$date['create_time']= time();
            $date['money']=0;
			$date['coin']=8888;
			$res = M("juejin_users")->add($date);
			if($res){
			    // 登陆成功
                $user = M('juejin_users')->where(['phone'=>$phone,'password'=>md5($passwd)])->find();
                $_SESSION['user_id']=$user['id'];
                $_SESSION['user_phone']=$user['phone'];
				if(is_valid_openid($this->wechat_id)){
					$open_id=$this->wechat_id;
					$wechat=M('wechat_user')->where(array('open_id'=>$open_id))->setField('phone',$user['phone']);
					$wechat_user = $this->getWechatInfo();
					M('juejin_users')->where(['phone'=>$phone])->save(['headimgurl'=>$wechat_user['headimgurl'],'wechat_name'=>$wechat_user['wechat_name']]);
					
					$wechat=M('wechat_user')->where(array('open_id'=>$this->wechat_id))->find();
					
					if($wechat['scene_id']){
                        $suser = M('juejin_users')->where(['id'=>$wechat['scene_id']])->find();
						M('juejin_users')->where(['phone'=>$phone])->save(['shareid'=>$suser['phone']]);
						//发展下级模板提醒
						$shareinfo = M('juejin_users')->field('phone')->where(['id'=>$wechat['scene_id']])->find();
						$shareuser = M('wechat_user')->field('open_id')->where(array('phone'=>$shareinfo['phone']))->find();
						if($shareuser['open_id']){
							$wx = M('wxuser')->where(['token'=>'anuxzf1435128586'])->field('appid,appsecret,is_certified')->find();
							$util = new \Spark\Wechat\WechatUtil($wx['appid'],$wx['appsecret']);
							$template_id='TOqICF8j63Wv0FnBYaounqJw-b8JDgA6znQu6WUQJhI';
							$url=rtrim(C('wap_domain'),'/') . U('Wap/User/my_friends');
							$topcolor="#000";
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
						
					}	
			
				}

				// 赠送1张必盈券和9张增益券
                $this->send_coupon($phone);

				
				$this->ajaxReturn(['status'=>'1','Code'=>'','info'=>'success']);
			}
			else
				$this->ajaxReturn(['status'=>0,'info'=>'注册失败，请刷新重试']);
		}
	}
	public function forget_passwd(){
		
		$this->display();
	}
	public function send_sms_data(){
		$post = json_decode($_POST['data']);
	
		$phone =$post->phone;
		$hasuser = M("juejin_users")->where(['phone'=>$phone])->count();
		if(!$hasuser){
			$this->ajaxReturn(['status'=>0,'message'=>'该手机号码未注册，请注册。。']);
		}
		$verify = rand(1000,9999);
		$content='短信验证码为:'.$verify.',请勿将验证码提供给他人.';
		$ret = send_newsms($phone,$content);
		if($ret){
			$_SESSION['mobile_code']=$verify;
			$this->ajaxReturn(['status'=>'success','Code'=>$verify,'Desc'=>'发送成功']);
		}else{
			$this->ajaxReturn(['status'=>'0','Code'=>'','message'=>'短信发送失败！']);
		}
	}
	
	public function forget_data(){
		/* SMSCode:5059
		tel:13655518604
		password:123456 */
		
		//修改密码 -
		$phone = I("tel");
		$passwd = I("password");
		$SMSCode = I("SMSCode");
		
		$hasuser = M("juejin_users")->where(['phone'=>$phone])->count();
		if($SMSCode !=$_SESSION['mobile_code'] || $SMSCode==''){
			$this->ajaxReturn(['status'=>2,'Code'=>'','message'=>'短信验证码有误，请重新获取']);
		}elseif(!$hasuser){
			$this->ajaxReturn(['status'=>3,'Code'=>'','message'=>'该号码未注册']);
		}
		
		$res =  M("juejin_users")->where(['phone'=>$phone])->save(['password'=>md5($passwd)]);
		if($res)
			$this->ajaxReturn(['status'=>1,'Code'=>'','message'=>'密码修改成功，请直接登录']);
		else 
			$this->ajaxReturn(['status'=>3,'Code'=>'','message'=>'修改失败，请重试']);
	}

	private function send_coupon($phone){
        $win_coupon = M('juejin_coupon')->where(['type'=>'Win'])->find();
        if($win_coupon != null && $win_coupon["register_present"] > 0){
            $win = [
                "couponId"=>$win_coupon["id"],
                "coupon_type"=>"Win",
                "phone"=>$phone,
                "count"=>$win_coupon["register_present"],
                "used"=>0,
                "add_time"=>time(),
                "over_time"=>time()+$win_coupon["overdue_time"]*3600*24
            ];
            M('juejin_user_coupon')->add($win);
        }

        $incr_coupon = M('juejin_coupon')->where(['type'=>'Incr'])->find();
        if($incr_coupon != null && $incr_coupon["register_present"] > 0){
            $incr = [
                "couponId"=>$incr_coupon["id"],
                "coupon_type"=>"Incr",
                "phone"=>$phone,
                "count"=>$incr_coupon["register_present"],
                "used"=>0,
                "add_time"=>time(),
                "over_time"=>time()+$incr_coupon["overdue_time"]*3600*24
            ];
            M('juejin_user_coupon')->add($incr);
        }
    }
}
?>