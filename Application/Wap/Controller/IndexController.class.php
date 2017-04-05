<?php
namespace Wap\Controller;
class IndexController extends WapController{
	private $tpl;	//微信公众帐号信息
	private $info;	//分类信息
	private $copyright;
	public  $company;
	public  $weixinUser;
	public  $homeInfo;
	protected $allowFromWeb = true;
	
	public function _initialize(){
		parent::_initialize();
		$where['token']=$this->token;
		$tpl=D('Wxuser')->where($where)->find();
		$this->weixinUser=$tpl;
		
		$info=M('Classify')->where(array('token'=>$this->token,'status'=>1,'pid'=>0))->order('sorts desc')->select();
		$info=$this->convertLinks($info);//加外链等信息
		$gid=D('Users')->field('gid')->find($tpl['uid']);
		$this->userGroup=M('User_group')->where(array('id'=>$gid['gid']))->find();
		$this->copyright=$this->userGroup['iscopyright'];
		
		$this->info=$info;
		$tpl['color_id']=intval($tpl['color_id']);
		$this->tpl=$tpl;
		$company_db=M('company');
		$this->company=$company_db->where(array('token'=>$this->token,'isbranch'=>0))->find();
		$this->assign('company',$this->company);
		$homeInfo=M('home')->where(array('token'=>$this->token))->find();
		$this->homeInfo=$homeInfo;
		$this->assign('iscopyright',$this->copyright);//是否允许自定义版权
		$this->assign('siteCopyright',C('copyright'));//站点版权信息
		
		$this->assign('homeInfo',$homeInfo);
		
		$nav = $this->getNav();
		$this->assign('nav',$nav);
		$menuStyle="Index:menu_style_".$this->homeInfo['menu_style'];
		$this->assign('menuStyle',$menuStyle);
	}
	
	public function classify(){
		$this->assign('info',$this->info);
		$this->display($this->tpl['tpltypename']);
	}
	
	public function index(){
		$where['token']=$this->token;
		$flash=M('Flash')->where($where)->order('sort desc')->select();
		$flash=$this->convertLinks($flash);
		$count=count($flash);
		//兼容多背景图
		$homeImgs = explode(';',$this->homeInfo['homeurl']);
		$this->homeInfo['homeImgs'] = $homeImgs;
		$this->assign('homeInfo',$this->homeInfo);
		
		$this->assign('flash',$flash);
		$this->assign('num',$count);
		$this->assign('info',$this->info);
		$this->assign('tpl',$this->tpl);
        $animation=M('animation')->where($where)->find();
        $this->assign("animation", $animation);
		$this->assign('shareConfig',$this->getWxShareConfig());
		$this->display('index_'.$this->tpl['tpltypeid']);
	}
	
	private function getNav(){
		$menus = M('home_menu')->where(['token'=>$this->token])->order('ordernum asc')->select();
		$menus = $this->convertLinks($menus);
		$nav=[];
		foreach($menus as $menu){
			if($menu['pid']==0){
				$nav[]=$menu;
			}
		}
		foreach($nav as $key => $value){
			foreach($menus as $menu){
				if($menu['pid']==$value['id']){
					$nav[$key]['sub'][]=$menu;
				}
			}
		}
		return $nav;
	}
	
	//文章列表
	public function lists(){
		$classid = I('get.classid',0,'intval');
		$where['token'] = $this->token;
		$where['category'] = $classid;
		$db = M('Article');
		//获取分页信息
		$page = I('get.p',1,'intval');
		
		$count=$db->where($where)->count();
		$cate = M('classify')->find($classid);
		//显示子分类
		$model = M('classify');
		$subcates = $model->where(['pid'=>$cate['id'],'status'=>1,'token'=>$this->token])->order('sorts desc')->select();
		$this->assign('subcates',$subcates);
		//父分类
		if($cate['pid']){
			$pcate = $model->where(['id'=>$cate['pid']])->field('id,name')->find();
			$this->assign('pcate',$pcate);
		}
		$this->assign('thisCate',$cate);
		$pageSize = 10;
		$pagecount=ceil($count/$pageSize);
		if($page > $pagecount){
			$page=$pagecount;
		}
		$p=($page-1) * $pageSize;
		
		$articles=$db->where($where)->order('sort DESC,id desc')->limit("{$p},".$pageSize)->select();
		//显示分类下的文章
		$this->assign('articles',$articles);
		$this->assign('page',$pagecount);
		$this->assign('p',$page);
		$this->assign('info',$this->info);
		$this->assign('tpl',$this->tpl);
		$this->assign('copyright',$this->copyright);
		$tpl_id = $this->tpl['tpllistid']?$this->tpl['tpllistid']:1;
		$this->display('category_'.$tpl_id);
	}
	
	/**
	 * @method content 显示图文详细页
	 */
	public function content(){
		$db=M('Img');
		$where['token']=$this->token;
		$contentid = I('get.id',0,'intval');
		$sid = $_GET['sid'];
		
		$where['id']=$contentid;
		$res=$db->where($where)->find();
		if($res['is_multi'] == 1&&isset($sid)){
			$more_news = unserialize($res['more_news']);
			if($more_news != false){
				$news = $more_news[$sid];
				$res['info'] = '';
				$res['pic'] = $news['cover'];
				$res['content'] = $news['detail'];
			}
		}
		$this->assign('info',$this->info);	//分类信息
		$this->assign('res',$res);			//内容详情;
		$this->assign('tpl',$this->tpl);				//微信帐号信息
		$this->assign('copyright',$this->copyright);	//版权是否显示
		$this->assign('shareConfig',$this->getWxShareConfig());
		$this->display('content_'.($this->tpl['tplcontentid']?$this->tpl['tplcontentid']:1));
	}
	
	/**
	 * @method article 显示文章内容
	 */
	public function article(){
		$db = M('Article');
		$where['token']=$this->token;
		$where['id']=$_GET['id'];
		$article = $db->where($where)->find();
		$this->assign('res',$article);		//内容详情;
		$this->assign('tpl',$this->tpl);				//微信帐号信息
		$this->assign('copyright',$this->copyright);	//版权是否显示
		if($article['tplid']!=0){
			$tpl = $article['tplid'];
		}
		else $tpl = $this->tpl['tplcontentid']?$this->tpl['tplcontentid']:1;
		$this->assign('shareConfig',$this->getWxShareConfig());
		$this->display('content_'.$tpl);
	}
	
	public function flash(){
		$where['token']=$this->token;
		$flash=M('Flash')->where($where)->select();
		$count=count($flash);
		$this->assign('flash',$flash);
		$this->assign('info',$this->info);
		$this->assign('num',$count);
		$this->display('ty_index');
	}
	
	/**
	 * 获取链接
	 *
	 * @param unknown_type $url
	 * @return unknown
	 */
	public function getLink($url){
		$urlArr=explode(' ',$url);
		$urlInfoCount=count($urlArr);
		if ($urlInfoCount>1){
			$itemid=intval($urlArr[1]);
		}
		//会员卡 刮刮卡 团购 商城 大转盘 优惠券 订餐 商家订单 表单
		if (strpos($url,'刮刮卡')!==false){
			$link = $this->U('Wap/Guajiang/index',['id'=>$itemid]);
		}
		elseif (strpos($url,'大转盘')!==false){
			$link = $this->U('Wap/Lottery/index',['id'=>$itemid]);
		}
		elseif (strpos($url,'优惠券')!==false){
			$link = $this->U('Wap/Coupon/index',['id'=>$itemid]);
		}
		elseif (strpos($url,'商家订单')!==false){
			$link = $this->U('Wap/Host/index',['hid'=>$itemid]);
		}
		elseif (strpos($url,'万能表单')!==false){
			if ($itemid){
				$link = $this->U('Wap/Selfform/index',['id'=>$itemid]);
			}
		}
		elseif (strpos($url,'相册')!==false){
			$link= $this->U('Wap/Photo/index',['id'=>$itemid]);
		}elseif (strpos($url,'全景')!==false){
			$link = $this->U('Wap/Panorama/index');
			if ($itemid){
				$link = $this->U('Wap/Panorama/item',['id'=>$itemid]);
			}
		}
		elseif (strpos($url,'会员卡')!==false){
			$link = $this->U('Wap/Card/vip');
		}
		elseif (strpos($url,'商城')!==false){
			$link = $this->U('Wap/Product/index');
		}
		elseif (strpos($url,'订餐')!==false){
			$link = $this->U('Wap/Dining/index');
		}
		elseif (strpos($url,'团购')!==false){
			$link = $this->U('Wap/Groupon/grouponIndex');
		}
		elseif (strpos($url,'首页')!==false){
			$link = $this->U('Wap/Index/index');
		}
		else{
			$url = urldecode($url);
			$link=str_replace('{wechat_id}',$this->wechat_id,$url);
		}
		return $link;
	}
	
	private function U($route,$param=[]){
		if(!isset($param['token'])){
			$param['token']=$this->token;
		}
		if(!isset($param['wechat_id'])){
			$param['wechat_id']=$this->wechat_id;
		}
		return U($route,$param);
	}
	
	public function convertLinks($arr){
		$i=0;
		foreach ($arr as $a){
			if ($a['url']){
				$arr[$i]['url']=$this->getLink($a['url']);
			}
			$i++;
		}
		return $arr;
	}
	
	public function s(){
		$this->assign('shareConfig',$this->getWxShareConfig());
		$this->display();
	}
	
	public function gongyi(){
		$date = $this->getDate();
		$this->assign('date',$date);
		$where['token']=$this->token;
		$flash=M('Flash')->where($where)->order('sort desc')->select();
		$flash=$this->convertLinks($flash);
		$count=count($flash);
		$this->assign('flash',$flash);
		$this->assign('num',$count);
		$this->assign('info',$this->info);
		$this->assign('tpl',$this->tpl);
		$animation=M('animation')->where($where)->find();
		$this->assign("animation", $animation);
		$this->assign('shareConfig',$this->getWxShareConfig());
		$this->display('index_'.$this->tpl['tpltypeid']);	
	}
	private function getDate(){
		$where['token']=$this->token;
		$date['zcount']= M('wechat_user')->where($where)->count();//总关注人数
		$where1['open_id']=$this->wechat_id;
		$pdate = M('wechat_user')->where( array_merge($where,$where1))->field('id')->find();
		$pid = $pdate['id'];
		$date['pcount']= M('wechat_user')->where(array_merge($where,array('id'=>array('elt',$pid))))->count();//排名
		return $date;
	}
}

