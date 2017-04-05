<?php
namespace Shop\Controller;
class CateController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->assign('nav_curr', 'book');
    }
	
    public function index() {
		$cates = M('item_cate')->where(['token'=>$this->token,'pid'=>0])->order('ordid desc')->select();
		$this->assign('cates',$cates);
        $this->display();
    }
	
    /**
     * 按分类查看
     */
    public function cate() {
        $cid = I('get.cid', 0,'intval');
		$subcates = M('item_cate')->where(['token'=>$this->token,'pid'=>$cid])->order('ordid desc')->select();
		if($subcates == null){
			$cate_info = M('item_cate')->where(['token'=>$this->token,'id'=>$cid])->find();
			$items = M('item')->where(['token'=>$this->token,'cate_id'=>$cid])->select();
			$this->assign('cate_info',$cate_info);
			$this->assign('items',$items);
			$this->display('cate');
		}
		else{
			$this->assign('cates',$subcates);
			$this->display('index');
		}
		
    }
}