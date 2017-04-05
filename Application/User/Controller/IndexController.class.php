<?php
namespace User\Controller;

class IndexController extends UserController{
	public function index(){
		$wxuser = D('Wxuser');
		$uid = session('uid');
		$wx = $wxuser->where(['uid'=>$uid])->find();
		if($wx){
			session('wxid', $wx['id']);
			session('token', $wx['token']);
			session('wxname', $wx['wxname']);
		}
		else{
			$data['token'] = create_token();
			$data['token_sign'] = create_key();
			$data['uid'] = $uid;
			$id = M('wxuser')->add($data);
			if($id){
				session('token', $data['token']);
			}
		}

		


		$this->assign('wx',$wx);
		$this->display();
	}
	
	//公众帐号列表
	public function home(){
		$wxuser = D('Wxuser');
		$uid = session('uid');
		$wx = $wxuser->where(['uid'=>$uid])->find();
		$chart = $wxuser->getStatusChart();
		$user = M("juejin_user")->count();
		$this->assign('user',$user);
		$this->assign('wxaccount',$wx);
		$this->assign('chart',$chart);


		//总计
		$tong['user']=M('juejin_users')->count();
		$tong['paycount']=M('juejin_payorder')->where(['status'=>2])->sum('price');

		$tong['order']=M('juejin_order')->sum('amount');
		$tong['ordercount']=M('juejin_order')->count();


		//今日记录
		$time = strtotime(date('Y-m-d'));
		$tong['tuser']=M('juejin_users')->where(['create_time'=>array('gt',$time)])->count();
		$tong['tpaycount']=M('juejin_payorder')->where(['createtime'=>array('gt',$time),'status'=>2])->sum('price');


		$tong['torder']=M('juejin_order')->where(['createtime'=>array('gt',$time)])->sum('amount');
		$tong['tordercount']=M('juejin_order')->where(['createtime'=>array('gt',$time)])->count();



		$this->assign('tongji',$tong);

		$this->display();
	}
	
	public function edit(){
		if(IS_POST){
			$db   = D('Wxuser');
			if(!isset($_POST['is_certified'])){
				$_POST['is_certified'] = 0;
			}
			if(!isset($_POST['encript_mode'])){
				$_POST['encript_mode'] = 0;
			}
			if ($db->create() === false) {
				$this->error($db->getError());
			}
			if($db->is_certified ==0 && $db->wxtype == '订阅号'){
				$db->appid = '';
				$db->appsecret = '';
			}
			else {
				$db->appid = trim($db->appid);
				$db->appsecret = trim($db->appsecret);
			}
			$ret = $db->save();
			if($ret) $this->success('保存成功！');
			else $this->error('保存失败！');
		}
		else{
			$where['uid']=session('uid');
			$res=M('Wxuser')->where($where)->find();
			$this->assign('info',$res);
			$this->assign('title','编辑微信公众号');
			$this->display();
		}
	}
	
	public function editsms(){
		$id=I('get.id',0,'intval');
		$where['uid']=session('uid');
		$res=M('Wxuser')->where($where)->find($id);
		$this->assign('info',$res);
		$this->display();
	}

	public function editemail(){
		$id=I('get.id',0,'intval');
		$where['uid']=session('uid');
		$res=M('Wxuser')->where($where)->find($id);
		$this->assign('info',$res);
		$this->display();
	}
	
	public function del(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');
		if(D('Wxuser')->where($where)->delete()){
			M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->setDec('wechat_card_num');
			$this->success('操作成功',U('Index/home'));
		}
		else{
			$this->error('操作失败',U('Index/home'));
		}
	}
	
	//修改公众号
	public function upsave(){
		$this->all_save('Wxuser', 'home');
	}
	
	//创建公众号
	public function insert(){
		$grp=M('User_group')
		->field('wechat_card_limit')
		->where(array('id'=>session('gid')))
		->find();
		$users = M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->find();
		if($users['wechat_card_num']<$grp['wechat_card_limit']){
			$wxusr = D('Wxuser');
			if($wxusr->create()===false){
				$this->error($wxusr->getError());
			}
			else{
				$token = $wxusr->token;
				$id=$wxusr->add();
				if($id){
					//用户公众号数增1
					M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->setInc('wechat_card_num');
					//为公众号开通默认的功能
					$this->addfc($token);
					$this->success('操作成功',U('Index/home'));
				}
				else{
					$this->error('操作失败',U('Index/home'));
				}
			}
		}
		else{
			$this->error('您的等级所能创建的公众号数量已经到达上限，请购买后再创建',U('User/Index/home'));
		}
	}
	
	//功能
	public function autos(){
		$this->display();
	}
	
	public function addfc($token){
		$token_open=M('Token_open');
		$gid=session('gid');
		$open['uid']=session('uid');
		$open['token']=$token;
		
		$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$gid)->select();
		foreach($fun as $key=>$vo){
			$queryname.=$vo['funname'].',';
		}
		$open['queryname']=rtrim($queryname,',');
		$token_open->data($open)->add();
	}
	
	
	public function useredit(){
		$usr=D('Users')->find(session('uid'));
		$usr['gname']=M('user_group')->find(session('gid'))['name'];
		$this->assign('user',$usr);
		$this->display();
	}
	
	public function usersave(){
		$data['id']=session('uid');
		$data['username']=I('post.username');
		$data['cellphone']=I('post.cellphone');
		$data['email']=I('post.email');
		if(M('Users')->save($data)){
			$this->success('修改成功！',U('Index/useredit'));
		}
		else{
			$this->error('修改失败！',U('Index/useredit'));
		}
	}
	
	//修改密码
	public function changepwd(){
		$orignPwd=I('post.oldpassword');
		$newPwd=I('post.newpassword');
		if($newPwd!=false&&$orignPwd!=false){
			if(session('account_id')){
				$uid = session('account_id');
				$model = M('sub_users');
			}
			else {
				$uid = session("uid");
				$model = M('users');
			}
			$usr = $model->find($uid);
			if($usr['password'] == md5($orignPwd)){
				$data['password'] = md5($newPwd);
				$data['id'] = $uid;
				if($model->save($data)){
					$this->success('密码修改成功！',U('Index/passedit'));
				}
				else{
					$this->error('密码修改失败！',U('Index/passedit'));
				}
			}
			else{
				$this->error('原密码错误！',U('Index/passedit'));
			}
		}
		else{
			$this->error('密码不能为空!',U('Index/passedit'));
		}
	}
	
	public function asset(){
		$tpl = I('get.tpl',0,'intval');
		if($tpl==2){
			$this->display('asset_back');
		}
		else $this->display();
	}


    public function imgs(){
        $per=50;
        $p=I('get.page', 0);
        $imgs=M('upload_attachment')->where(array('uid'=>session('uid')))->order('id desc')
            ->limit($p*$per, $per)->select();
        $this->ajaxReturn($imgs);
    }

    public function imgs_delete(){
        $id=I('get.id',0,'intval');
        $ret=M('upload_attachment')->where(array('id'=>$id))->delete();
        echo '{"ret":"'.$ret.'"}';
    }

    private function U($module,$params=[]){
		$this->token = session('token');
		if(empty($params['token'])){
			$params['token']=$this->token;
		}
		if(empty($params['wechat_id'])){
			$params['wechat_id']='{wechat_id}';
		}
		return rtrim(C('wap_domain'),'/') . U($module,$params);
	}
	
	/**
	 * @method 显示模块链接
	 */
	public function link(){
		$token = session('token');
		//系统链接
		$sysModules=[];
		$sysModules[]=array('name'=>'微官网','url'=>$this->U('Wap/Index/index'));
		$sysModules[]=array('name'=>'微相册','url'=>$this->U('Wap/photo/index'));
		$sysModules[]=array('name'=>'微商城','url'=>$this->U('wap/Product/products',['catid'=>0]));
        $sysModules[]=array('name'=>'微餐饮','url'=>$this->U('Wap/Dining/index'));
        $sysModules[]=array('name'=>'微餐饮行业版','url'=>$this->U('Wap/Canyin/company_list',array('token'=>$token)));
		$sysModules[]=array('name'=>'地图','url'=>$this->U('Wap/Company/map'));
		$sysModules[]=array('name'=>'微会员','url'=>$this->U('Wap/Card/index'));
		$sysModules[]=array('name'=>'微留言','url'=>$this->U('Wap/Reply/index'));
        $sysModules[]=array('name'=>'微商城行业版','url'=>rtrim(C('wap_domain'),'/')."/mall/index.php?token=".$token."&wechat_id={wechat_id}");
        $sysModules[]=array('name'=>'微旅游','url'=>$this->U('Wap/lvyou/index'));
        $sysModules[]=array('name'=>'微房产','url'=>$this->U('Wap/Estate/index'));
		
		$sysModules[]=array('name'=>'一键拨号','url'=>'tel:18800000000');
		$sysModules[]=array('name'=>'发送短信','url'=>'sms:18800000000');
		$lotteries=M('Lottery')->field('id,type,title')->where("token='{$token}'")->select();
		foreach($lotteries as $lottery){
			$param=['wechat_id'=>'{wechat_id}','token'=>$token,'id'=>$lottery['id']];
			if($lottery['type']==1){
				$sysModules[]=array('name'=>"幸运转盘({$lottery['title']})",'url'=>$this->U('Wap/lottery/index',$param));
			}
			else if($lottery['type']==2){
				$sysModules[]=array('name'=>"刮刮卡({$lottery['title']})",'url'=>$this->U('Wap/guajiang/index',$param));
			}
			else if($lottery['type']==3){
				$sysModules[]=array('name'=>"优惠券({$lottery['title']})",'url'=>$this->U('Wap/coupon/index',$param));
			}
		}
		//预约
		$orders=M('Selfform')->field('id,name')->where("token='{$token}'")->select();
		foreach($orders as $order){
			$sysModules[]=array('name'=>"微预约({$order['name']})",'url'=>$this->U('Wap/selfform/index',['id'=>$order['id']]));
		}
		
		//分类链接
		$catModules=[];
		$cats = M('Classify')->where("token='{$token}'")->select();
		foreach($cats as $cat){
			$catModules[]=array('name'=>$cat['name'],'url'=>$this->U('Wap/index/lists',['classid'=>$cat['id'],'token'=>$token]));
		}
		
		//文章链接
		$artModules=[];
		$articles = M('article')->where("token='{$token}'")->select();
		foreach($articles as $article){
			$artModules[]=array('name'=>$article['title'],'url'=>$this->U('Wap/index/article',['id'=>$article['id'],'token'=>$token]));
		}
		
		$this->assign('sysModules', $sysModules);
		$this->assign('catModules', $catModules);
		$this->assign('artModules', $artModules);
		$this->display();
	}
}
?>