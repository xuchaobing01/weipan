<?php
namespace User\Controller;
class HelpController extends UserController{
	//链接选择
	public function link(){
		$token = session('token');
		//系统链接
		$sysModules=[];
		$sysModules[]=array('name'=>'微官网','url'=>$this->U('Wap/Index/index'));
		$sysModules[]=array('name'=>'微相册','url'=>$this->U('Wap/photo/index'));
        $sysModules[]=array('name'=>'微餐饮','url'=>$this->U('Wap/Dining/index'));
        $sysModules[]=array('name'=>'微餐饮行业版','url'=>$this->U('Wap/Canyin/index'));
		$sysModules[]=array('name'=>'地图','url'=>$this->U('Wap/Company/map'));
		$sysModules[]=array('name'=>'微会员','url'=>$this->U('Wap/Card/index'));
		$sysModules[]=array('name'=>'积分商城','url'=>$this->U('Wap/Jmall/index'));
		$sysModules[]=array('name'=>'微留言','url'=>$this->U('Wap/Reply/index'));
        $sysModules[]=array('name'=>'微商城','url'=>rtrim(C('wap_domain'),'/')."/mall/index.php?token=".$token);
        $sysModules[]=array('name'=>'微商城(新版)','url'=>$this->U('Shop/Index/index'));
        $sysModules[]=array('name'=>'微旅游','url'=>$this->U('Wap/lvyou/index'));
        $sysModules[]=array('name'=>'微房产','url'=>$this->U('Wap/Estate/index'));
        $sysModules[]=array('name'=>'微酒店','url'=>$this->U('Wap/Hotel/index'));
		$sysModules[]=array('name'=>'一键拨号','url'=>'tel:18800000000');
		$sysModules[]=array('name'=>'发送短信','url'=>'sms:18800000000');
		$lotteries=M('Lottery')->field('id,type,title')->where("token='{$token}'")->select();
		foreach($lotteries as $lottery){
			$param=['token'=>$token,'id'=>$lottery['id']];
			if($lottery['type']==1){
				$sysModules[]=array('name'=>"幸运转盘({$lottery['title']})",'url'=>$this->U('Wap/lottery/index',$param));
			}
			else if($lottery['type']==2){
				$sysModules[]=array('name'=>"刮刮卡({$lottery['title']})",'url'=>$this->U('Wap/guajiang/index',$param));
			}
			else if($lottery['type']==3){
				$sysModules[]=array('name'=>"优惠券({$lottery['title']})",'url'=>$this->U('Wap/coupon/index',$param));
			}
			else if($lottery['type']==4){
				$sysModules[]=array('name'=>"砸金蛋({$lottery['title']})",'url'=>$this->U('Wap/Zadan/index',$param));
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
		//游戏链接
		$gameModules = [];
		$gameModules[] = array('name'=>'2048','url'=>C('wap_domain').'/Public/game/2048/index.html');
		$gameModules[] = array('name'=>'看你有多色','url'=>C('wap_domain').'/Public/game/se/index.html');
		$gameModules[] = array('name'=>'密室逃脱','url'=>C('wap_domain').'/Public/game/mstt/index.html');
		$gameModules[] = array('name'=>'一个都不能死','url'=>C('wap_domain').'/Public/game/bunengsi/index.html');
		$gameModules[] = array('name'=>'堆木头','url'=>C('wap_domain').'/Public/game/mutou/index.html');
		$gameModules[] = array('name'=>'点灯','url'=>C('wap_domain').'/Public/game/diandeng/index.html');
		$this->assign('sysModules', $sysModules);
		$this->assign('catModules', $catModules);
		$this->assign('artModules', $artModules);
		$this->assign('gameModules', $gameModules);
		$this->display();
	}
	
	private function U($module,$params=[]){
		$this->token = session('token');
		if(empty($params['token'])){
			$params['token']=$this->token;
		}
		return rtrim(C('wap_domain'),'/') . U($module,$params);
	}
	
	public function qrcode(){
		$data = trim($_GET['url']);
		echo json_encode(['errcode'=>0,'qrcode'=>qrcode($data)]);
	}
}
?>