<?php
 /**
  * 商品详细页
  */
namespace Shop\Controller;
class ItemController extends BaseController {
    public function index() {
        $id = intval($_GET['id']);
		$item = $this->_get_item($id);
		$favor = M('mall_favor','sp_')->where(['item_id'=>$id,'wechat_id'=>$this->wechat_id])->count();
		$item['favor'] = $favor;
		$this->assign('spec_list',$item['spec_list']);
		if($item['is_rush'] == 1) redirect(U('rush',['id'=>$item['id'],'token'=>$this->token]));
        $this->assign('item', $item);
		
        $sql="select distinct(wechat_id) as wechat_id from weixin_item_comment where item_id=$id";
        $wcs=M('item_comment')->query($sql);
        $sql="select t.id, t.info, t.add_time, t.info_reply, u.wechat_name, u.headimgurl, t.wechat_id from weixin_item_comment as t LEFT JOIN sp_wechat_user as u ON t.wechat_id=u.open_id where t.item_id=$id and t.status=1";
        $comments= M('item_comment')->query($sql);
		$this->assign('comments',$comments);
        $this->display();
    }
	
	public function _get_item_comment($id){
		if($this->cache->get('comment_'.$id) == false){
			$comments = M('item_comment')->where(['item_id'=>$id,'status'=>1])->limit(0,20)->order('add_time desc')->select();
			$this->cache->set('comment_'.$id,$comments,600);
			return $comments;
		}
		return $this->cache->get('comment_'.$id);
	}
	
	/**
     * 抢购
     */
    public function rush() {
        $id = $this->_get('id', 'intval');
        !$id && $this->_404();
		
		$item = $this->_get_item($id);
        $this->assign('item', $item);
		$sql="select distinct(wechat_id) as wechat_id from weixin_item_comment where item_id=$id";
        $wcs=M('item_comment')->query($sql);
        /*foreach($wcs as $key=>$val){
            $this->getwxuser($this->token, $val['wechat_id']);
        }*/
        $sql="select t.id, t.info, t.add_time, t.info_reply, u.wechat_name, u.headimgurl, t.wechat_id from weixin_item_comment as t LEFT JOIN
        sp_wechat_user as u ON t.wechat_id=u.open_id where t.item_id=$id and t.status=1 order by t.id desc";
        $comments= M('item_comment')->query($sql);
		$this->assign('comments',$comments);
        $this->display();
    }
	
	private function _get_item($id){
		if(S('item_'.$id) == false){
			$item_mod = M('item');
			$item = $item_mod->where(array('id' => $id, 'status' =>1,'token'=>$this->token))->find();
			$brand = M('brandlist')->field('name')->find($item['brand']);
			$item['brand'] = $brand['name'];
			//商品相册
			$item['img_list'] = M('item_img')->field('url')->where(array('item_id' => $id))->order('ordid')->select();
			//处理规格
			if(!empty($item['specs'])){
				$spec_list = json_decode($item['specs']);
				$item['spec_list'] = $spec_list;
				if(count($spec_list)>1){
					$minPrice = $spec_list[0]->price;
					$maxPrice = $spec_list[0]->price;
					foreach($spec_list as $spec){
						if($spec->price > $item['max_price']){
							$maxPrice = $spec->price;
						}
						if($spec->price < $item['min_price']){
							$minPrice = $spec->price;
						}
					}
					$item['price_range'] = $minPrice.'-'.$maxPrice;
				}
				else $item['price_range'] = $spec_list[0]->price;
			}
			S('item_'.$id,$item,60);
			return $item;
		}
		return S('item_'.$id);
	}
	
    /**
     * 点击去购买
     */
    public function go_rush() {
		$id = $this->_get('id', 'intval');
		if(!id ){
			echo json_encode(['status'=>1,'message'=>'商品不存在！']);exit;
		}
		$item = $this->_get_item($id);
		if(!$item ){
			echo json_encode(['status'=>1,'message'=>'商品不存在！']);exit;
		}
		if($item['is_rush'] == 0) {
			echo json_encode(['status'=>1,'message'=>'该商品没有参加抢购！']);exit;
		}
		if($item['rush_time'] > time()){
			echo json_encode(['status'=>1,'message'=>'抢购尚未开始！']);exit;
		}
    }

    /**
     * AJAX获取评论列表
     */
    public function comment_list() {
        $id = $this->_get('id', 'intval');
        !$id && $this->ajaxReturn(0, '商品不存在');
        $item_mod = M('item');
        $item = $item_mod->where(array('id' => $id, 'status' => '1'))->count('id');
        !$item && $this->ajaxReturn(0,'商品不存在');
        $item_comment_mod = M('item_comment');
        $pagesize = 8;
        $map = array('item_id' => $id);
        $count = $item_comment_mod->where($map)->count('id');
        $pager = $this->_pager($count, $pagesize);
        $pager->path = 'comment_list';
        $cmt_list = $item_comment_mod->where($map)->order('id DESC')->limit($pager->firstRow . ',' . $pager->listRows)->select();
        $this->assign('cmt_list', $cmt_list);
        $data = array();
        $data['list'] = $this->fetch('comment_list');
        $data['page'] = $pager->fshow();
        $this->ajaxReturn(1, '', $data);
    }
}