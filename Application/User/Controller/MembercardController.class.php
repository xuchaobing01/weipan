<?php
namespace User\Controller;
use Spark\Util\Page;
class MembercardController extends UserController{
	public function _initialize() {
		parent::_initialize();
		$this->token=session('token');
		$this->assign('token',$this->token);
		$this->wxuser_db=M("Wxuser");
		//获取所在组的开卡数量
		$thisWxUser=$this->wxuser_db->where(array('token'=>$this->token))->find();
		$thisUser=M("Users")->where(array('id'=>$thisWxUser['uid']))->find();
		$thisGroup=M("User_group")->where(array('id'=>$thisUser['gid']))->find();
		$this->wxuser_db->where(array('token'=>$this->token))->save(array('allcardnum'=>$thisGroup['member_card_limit']));
		$can_cr_num = $thisWxUser['allcardnum'] - $thisWxUser['yetcardnum'];
		if($can_cr_num > 0){
			$data['cardisok'] = 1;
			$this->wxuser_db->where(array('uid'=>session('uid'),'token'=>session('token')))->save($data);
		}
	}
	
	//会员卡配置
	public function index(){
		$data=M('Member_card_set')->where(array('token'=>$_SESSION['token']))->find();
		if(IS_POST){
			$_POST['token']=$_SESSION['token'];			
			if($data==false){				
				$this->all_insert('Member_card_set');
			}
			else{
				$_POST['id']=$data['id'];
				$this->all_save('Member_card_set');
			}
		}
		$this->assign('card',$data);
		$this->display();
	}
	
	public function privilege(){
		$data=M('Member_card_vip')->where(array('token'=>$_SESSION['token'],'card_id'=>I('get.cid')))->order('id desc')->select();
		$this->assign('data_vip',$data);
		$this->display();
	}
	
	public function privilege_add(){
		if(IS_POST){
			if($_POST['type']=='0'){
				$_POST['startdate'] = strtotime($_POST['startdate']);
				$_POST['enddate'] = strtotime($_POST['enddate']);
			}
			$_POST['token'] = session('token');
			$_POST['card_id'] = I('get.cid',0,'intval');
			$this->all_insert('Member_card_vip','privilege?cid='.$_POST['card_id']);
		}
		else{
			$this->display('privilege_edit');
		}
	}
	
	public function privilege_edit(){
		if(IS_POST){
			if($_POST['type']=='0'){
				$_POST['startdate']=strtotime($_POST['startdate']);
				$_POST['enddate']=strtotime($_POST['enddate']);
			}
			$_POST['id'] = I('get.id');
			
			$this->all_save('Member_card_vip','privilege?cid='.$_GET['cid']);
		}
		else{
			$data=M('Member_card_vip')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			if($data!=false){
				$this->assign('vip',$data);
				$this->display();
			}
			else{
				$this->error('非法操作');
			}
		}
	}
	
	public function privilege_del(){
		$data=M('Member_card_vip')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete();
		if($data==false){
			$this->error('服务器繁忙请稍后再试');
		}
		else{
			$this->success('操作成功',U('Membercard/privilege',array('cid'=>$_GET['cid'])));
		}
	}
	
	//会员卡列表
	public function create(){
		$data=M('Member_card_create');
		$where = ['token'=>session('token'),'card_id'=>I('get.cid',0,'intval')];
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//计算领取张数
		$count    =M('member_card_create')->where($where)->count();
		$usecount =M('member_card_create')->where('token="'.$_SESSION['token'] .'" and wechat_id !="" and card_id='.I('get.cid',0,'intval'))->count();
		$this->assign("usecount",$usecount);
		$this->assign("count",$count);
		$this->assign("ucount",$count - $usecount);
		$this->assign('page',$show);
		$this->assign('data_vip',$list);
		$this->display();
	}
	
	/**
	 *@method getuserinfo 获取会员信息
	 */
	public function getuserinfo(){
		$wechat_id = I("get.id");

		$uinfo = M('Userinfo')->where(array('wechat_id'=>$wechat_id ,'token'=>$_SESSION['token']))->order('id DESC')->find();
		$this->assign('list',$uinfo);
	
		$this->display();	
	}

	//创建会员卡
	public function create_add(){
		$_POST['token'] = $_SESSION['token'];
		$card=M("Wxuser")->where(array('uid'=>session('uid'),'token'=>$_SESSION['token']))->find();
		if(IS_POST){
			$start = I('post.start',0,'intval');
			$end = I('post.end',0,'intval');
			$cid = I('get.cid',0,'intval');
			$title = I('post.title','','trim');
			if($end > 65535 || $start < 0){
				$this->error('卡号起始号最小为0，结束号最大为65535');
				return;
			}
			
			$num = $end - $start+1;
			if($num <=0 ){
				$this->error('开始卡号必须小于结束卡号！');
				return;
			}
			
			$group_cread_num=M('User_group')->field('member_card_limit')->where(array('id'=>session('gid')))->find();
			if(($num>$group_cread_num['member_card_limit'])){
				$this->error('你当前的等级只允许创建'.$group_cread_num['member_card_limit'].'张卡');
				exit;
			}
			
			$markcard = M("Wxuser")->where(array('uid'=>session('uid'),'token'=>session('token')))->find();
			$can_cr_num = $group_cread_num['member_card_limit'] - $markcard['yetcardnum']; //还剩下几张
			
			if( $num > $can_cr_num ){
				$this->error("您本月开卡数量只剩下 " . $can_cr_num ." 张！");
				exit;
			}
			$len = strlen($_POST['end']);
			for($i=1;$i<=$num;$i++){
				$data['number'] = $title.sprintf("%0{$len}d",$start);
				$start++;
				$data['token'] = session('token');
				$data['card_id'] = $cid;
				M('member_card_create')->data($data)->add();
			}
  			
  			$back = M('Wxuser')->where(array('uid'=>session('uid'),'token'=>session('token')))->setInc('yetcardnum',$num);
  			M('Wxuser')->where(array('uid'=>session('uid'),'token'=>session('token')))->setInc('totalcardnum',$num);
  			$markcard = M("Wxuser")->where(array('uid'=>session('uid'),'token'=>session('token')))->find();
  			$can_cr_num = $markcard['allcardnum'] - $markcard['yetcardnum'];
  			if($can_cr_num <= 0){
  				$data['cardisok'] = 2;
  				M('Wxuser')->where(array('uid'=>session('uid'),'token'=>session('token')))->save($data);	
  			}
			if($back!=false){
				$this->success('恭喜您共开了'.$num.'张会员卡',U('Membercard/create',array('cid'=>$cid)));
			}
			else{
				$this->error('服务器繁忙请稍后再试');
			}
		}
		else{
			$markcard = M("Wxuser")->where(array('uid'=>session('uid'),'token'=>session('token')))->find();
			$can_cr_num = $markcard['allcardnum'] - $markcard['yetcardnum'];
			$this->assign('count',$markcard['allcardnum']);
			$this->assign('cancrnum',$can_cr_num);
			$this->display();
		}
	}
	
	//会员优惠卷
	public function coupon(){
		$data=M('Member_card_coupon')->where(array('token'=>$_SESSION['token'],'card_id'=>I('get.cid')))->order('id desc')->select();
		$this->assign('data_vip',$data);
		$this->display();	
	}
	
	/**
	 * @method coupon_edit 优惠券编辑
	 */
	public function coupon_edit(){
		if(IS_POST){
			$_POST['expire_date']=strtotime($_POST['expire_date']);
		
			$this->all_save('Member_card_coupon','coupon?cid='.$_GET['cid']);
		}
		else{
			$data=M('Member_card_coupon')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			if($data!=false){
				$this->assign('vip',$data);
				$this->display();
			}
			else{
				$this->error('非法操作');
			}
		}
	}
	
	/**
	 * @method coupon_add 添加优惠券
	 */
	public function coupon_add(){
		if(IS_POST){
			$_POST['expire_date']=strtotime($_POST['expire_date']);
			$_POST['create_time']=time();
			$_POST['token'] = session('token');
			$_POST['card_id'] = I('get.cid',0,'intval');
			$this->all_insert('Member_card_coupon','coupon?cid='.$_POST['card_id']);
		}
		else{
			$this->display('coupon_edit');	
		}
	}
	
	public function coupon_del(){
		$id = I('get.id',0,'intval');
		$data=M('Member_card_coupon')->where(array('token'=>session('token'),'id'=>$id))->delete();
		if($data==false){
			$this->error('删除失败！');
		}
		else{
			//关联删除会员里的优惠券
			M('member_coupon_usage')->where(['coupon_id'=>$id])->delete();
			$this->success('操作成功',U('Membercard/coupon',array('cid'=>$_GET['cid'])));
		}
	}
	
	public function coupon_publish(){
		$token = session('token');
		$coupon_id = I('get.id',0,'intval');
		$coupon = M('member_card_coupon')->where(['token'=>$token,'status'=>0,'id'=>$coupon_id])->find();
		if($coupon == null) $this->error('非法操作！');
		$model = M('member_card_create');
		$members = $model->where(['token'=>$token,'card_id'=>$coupon['card_id'],'wechat_id'=>['neq','']])->select();
		if($coupon['exchange_points']>0) $status =0;
		else $status =1;
		foreach($members as $i => $member){
			$data['token'] = $token;
			$data['wechat_id'] = $member['wechat_id'];
			$data['card_id'] = $member['card_id'];
			$data['coupon_id'] = $coupon_id;
			$data['status'] = $status;
			$data['sncode'] = $this->create_sncode($coupon_id,$i);
			M('member_coupon_usage')->add($data);
		}
		M('member_card_coupon')->where(['token'=>$token,'id'=>$coupon_id])->setField('status',1);
		$this->success('优惠券发布成功！');
	}
	
	//生成优惠券SN 码
	public function create_sncode($prefix=0,$i=''){
		return $prefix.time().$i.rand(10,90);
	}
	
	//管理优惠券SN码
	public function coupon_sncode($id){
		$list = M('member_coupon_usage')->where(['coupon_id'=>$id,'token'=>session('token')])->select();
		$this->assign('list',$list);
		$this->display();
	}
	//会员礼卷
	public function integral(){
		$data=M('Member_card_integral')->where(array('token'=>$_SESSION['token'],'card_id'=>I('get.cid')))->order('id desc')->select();
		$this->assign('data_vip',$data);
		$this->display();
	}
	
	
	public function integral_edit(){
		if(IS_POST){
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$this->all_save('Member_card_integral','integral?cid='.$_GET['cid']);
		}
		else{
			$data=M('Member_card_integral')->where(array('token'=>session('token'),'id'=>I('get.id')))->find();
			if($data!=false){
				$this->assign('vip',$data);
				$this->display();
			}
			else{
				$this->error('非法操作');
			}
		}
	}
	
	public function integral_add(){
		if(IS_POST){
			$_POST['token'] = session('token');
			$_POST['card_id'] = I('get.cid',0,'intval');
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$this->all_insert('Member_card_integral','integral?cid='.$_POST['card_id']);
		}
		else{
			$this->display('integral_edit');
		}
	}
	
	public function integral_del(){
		$data=M('Member_card_integral')->where(array('token'=>session('token'),'id'=>I('get.id')))->delete();
		if($data==false){
			$this->error('服务器繁忙请稍后再试');
		}
		else{
			$this->success('操作成功',U('Membercard/integral',array('cid'=>$_GET['cid'])));
		}
	}
	
	//会员详情
	public function info(){
		$data=M('Member_card_info')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			$_POST['need_check'] = I('post.need_check',0,'intval');
			if($data==false){
				$_POST['token']=session('token');	
				$this->all_insert('Member_card_info','info');
			}
			else{
				$_POST['id']=$data['id'];
				$this->all_save('Member_card_info','info');
			}
		}
		else{
			$this->assign('info',$data);
			$contact=M('Member_card_contact')->where(array('token'=>$_SESSION['token']))->order('sort desc')->select();
			$this->assign('contact',$contact);
			$this->display();
		}
	}
	
	public function contact(){
		if(IS_POST){
			$this->all_insert('Member_card_contact','info');
		}
		else{
			$this->error('非法操作');	
		}
	}
	
	public function contact_edit(){
		if(IS_POST){			
			$this->all_save('Member_card_contact','info');
		}
		else{
			$this->error('非法操作');			
		}
	}

	/**
	 *@method exchange 积分设置 设置会员卡积分策略及会员卡级别
	 */
	public function exchange(){
		$data=M('Member_card_exchange')->where(array('token'=>$_SESSION['token'],'card_id'=>I('get.cid',0,'intval')))->find();
		$cid = I('get.cid',0,'intval');
		if(IS_POST){
			$_POST['create_time'] = time();
			if($data==false){
				$_POST['token'] = $_SESSION['token'];
				$_POST['card_id'] = $cid;
				$this->all_insert('Member_card_exchange','exchange?cid='.$cid);
			}
			else{
				$_POST['id'] = $data['id'];
				$this->all_save('Member_card_exchange','exchange?cid='.$cid);
			}
		}
		else{
			$this->assign('exchange',$data);
			$this->display();
		}
	}
	
	/**
	 *@method replySet 会员卡回复设置
	 */
	public function replySet(){
		exit();
		if (IS_POST){
			$data['title'] = $_POST['title'];
			$data['picurl'] = $_POST['picurl'];
			$data['info'] = $_POST['info'];
			$config['member_title'] = $_POST['member_title'];
			$config['member_picurl'] = $_POST['member_picurl'];
			$config['member_info'] = $_POST['member_info'];
			$data['config'] = serialize($config);
			$set = M('reply_info')->where(['token'=>session('token'),'infotype'=>'Membercard'])->field('id')->find();
			D('ReplyInfo')->set('Membercard',$data);
			D('Keyword')->set($set['id'],'Membercard',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$setting = D('ReplyInfo')->get('Membercard');
			$config = unserialize($setting['config']);
			if($config){
				foreach($config as $key => $value){
					$setting[$key]=$value;
				}
			}
			if(empty($setting['picurl'])){
				$setting['picurl'] = rtrim(C('site_url'),'/').STATICS.'/images/member.jpg';
			}
			if(empty($setting['member_picurl'])){
				$setting['member_picurl'] = rtrim(C('site_url'),'/').STATICS.'/images/vip.jpg';
			}
			
			$keyword = D('Keyword')->get($setting['id'],'Membercard');
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display('replyset');
		}
	}
	
	/**
	 *@method card 会员卡管理页面
	 */
	public function card(){
		$cards = M('Member_card_set')->where(array('token'=>session('token')))->select();
		foreach($cards as $key => $card){
			$total_card_num = M('Member_card_create')->where(array('card_id'=>$card['id']))->count();
			$cards[$key]['member_num'] = M('Member_card_create')->where(array('card_id'=>$card['id'],'wechat_id'=>['neq','']))->count();
			$cards[$key]['free_card_num'] = $total_card_num - $cards[$key]['member_num'];
		}
		$this->assign('cards',$cards);
		$this->display();
	}
	
	/**
	 *@method card_edit 会员卡编辑页面
	 */
	public function card_edit(){
		if(IS_POST){
			$_POST['token']=$_SESSION['token'];
			$id = I('post.id',0,'intval');
			$model = M('Member_card_set');
			$model->create();
			if($id == false){
				$model->create_time=time();
				$id = $model->add();
			}
			else{
				$model->save();
			}
			$this->success('保存成功!',U('card_edit?id='.$id));
		}
		else{
			$id = I('get.id',0,'intval');
			if($id){
				$card=M('Member_card_set')->where(array('token'=>$_SESSION['token'],'id'=>$id))->find();
				$this->assign('card',$card);
			}
			$this->display();
		}
	}
	
	public function card_del(){
		$id = I('get.id',0,'intval');
		if($id){
			$ret = M('Member_card_set')->where(['token'=>session('token'),'id'=>$id])->delete();
			if($ret){
				M('member_card_create')->where(['token'=>session('token'),'card_id'=>$id])->delete();//删除该类型下的所有会员卡
				$this->success('删除成功！',U('card'));
			}
			else{
				$this->error('删除失败！',U('card'));
			}
		}
		else{
			$this->error('非法操作！',U('card'));
		}
	}
}
?>