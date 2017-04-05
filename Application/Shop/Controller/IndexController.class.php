<?php
namespace Shop\Controller;
class IndexController extends BaseController {
    public function index() {
		if(isset($_GET['showloadimg'])){
			$this->assign('showLoadImg',true);
		}
		else{
			$this->assign('showLoadImg',false);
		}
    	/*****幻灯片广告***/
        $this->assign('ad',$this->_get_flash_ads());
		
        /*****加载页广告***/
        $this->assign('loadAds',$this->_get_load_ads());
		
		/*****产品广告***/
        $this->assign('productAds',$this->_get_product_ads());
		
		$shop_html = M('mall_code','sp_')->where(['token'=>$this->token])->find();

		if($shop_html == null) $shop_html = ['plate_code'=>'<h2>NO CONFIG</h2>'];

        $this->assign('plate_html',$shop_html['plate_code']);
		$set = $this->get_shop_set();
        $this->assign('set',$this->get_shop_set());
		if($this->config['index_tpl']=='1'){
			$tpl = 'index';
		}
		else $tpl = 'index-'.$this->config['index_tpl'];
        $this->display($tpl);
    }
	
	private function _get_load_ads(){
		if(S('LOAD_ADS') == false){
			$loadAds= M('ad')->field('url,content,extval,extimg,desc')->where("board_id=15 and status=1 and token='".$this->token."'")->order('ordid asc')->find();
			S('LOAD_ADS',$loadAds,600);
			return $loadAds;
		}
		return S('LOAD_ADS');
	}
	
	private function _get_flash_ads(){
		if(S('ADS') == false){
			$ads= M('ad')->field('url,content,desc')->where("board_id=1 and status=1 and token='".$this->token."'")->order('ordid asc')->select();
			S('ADS',$ads,600);
			return $ads;
		}
		return S('ADS');
	}
	
	private function _get_product_ads(){
		if(S('PRODUCT_ADS') == false){
			$productAds= M('ad')->field('url,content,extval,desc')->where("board_id=16 and status=1 and token='".$this->token."'")->order('ordid asc')->find();
			S('PRODUCT_ADS',$productAds,600);
			return $productAds;
		}
		return S('PRODUCT_ADS');
	}
	
	private function get_shop_set(){
		$set = D('ReplyInfo')->where(['token'=>$this->token,'infotype'=>'WeiMall'])->field('id,title,picurl,info')->find();
		return $set;
	}
	
    public function getItem($where = array()){
    	$where_init = array('status'=>'1', 'token' => $this->token);
        $where =array_merge($where_init, $where);
    	return $item=M('item')->where($where)->order('ordid asc,id desc')->select();
    }
	
	public function search(){
		$keyword = I('get.keyword','','trim');
		$list = $this->getItem(['title'=>['like','%'.$keyword.'%']]);
		$this->assign('list',$list);
		$this->display();
	}
}