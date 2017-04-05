<?php
namespace User\Controller;
use Spark\Util\Page;
class RedpacketController extends UserController{
    public function index(){
		$where['token'] = session('token');
		$model = M('redpacket');
        $list = $model->where($where)->order('id DESC')->select();
		foreach($list as $key => $item){
			$tongji= M('redpacket_record')->where(['redpacket_id'=>$item['id'],'status'=>1])->field('count(1) as totalPerson,sum(packet_value) as totalMoney')->find();
			$list[$key]['totalPerson'] = $tongji['totalPerson'];
			$list[$key]['totalMoney'] = $tongji['totalMoney'];
		}
        $this->assign('items',$list);
        $this->display();
    }
	
	public function prize_log(){
		$id = I('get.id',0,'intval');
		$model = M('redpacket_record');
		$where = ['redpacket_id'=>$id,'token'=>session('token')];
		$count = $model->where($where)->count();
		$page = new Page($count,20);
		$list = $model->where($where)->order('status desc,id desc')->select();
		$wxUser = new \User\Model\WechatUserModel(session('token'));
		
		$wxConfig = M('wxuser')->where(['token'=>session('token')])->find();
		$wxUser->wxConfig = $wxConfig;
		foreach($list as $key => $item){
			$detail = $wxUser->get($item['wechat_id']);
			$list[$key]['wechat_name'] = $detail['wechat_name'];
		}
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	private function get_serial_id($vid){
		$check = M('votex_item')->field('max(serial_id) as serial')->where(['token'=>session('token'),'vote_id'=>$vid])->find();
		if($check['serial']==null){
			return  1;
		}
		else return intval($check['serial'])+1;
	}
	
	public function set(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
            $model = M('Redpacket');
            $data['title'] = I("post.title");
            $data['info'] = I("post.info");
            $data['desc'] = I("post.desc");
            $data['detail'] = I("post.detail");
            $data['item_unit'] = I("post.item_unit");
            $data['item_sum'] = I("post.item_sum");
            $data['item_num'] = I("post.item_num");
            $data['item_max'] = I("post.item_max");
            $data['balance'] = I("post.item_sum");
            $data['imgs'] = I("post.imgs");
            $data['share_img'] = I("post.share_img");
            $data['packet_type'] = I("post.packet_type");
            $data['start_time'] = I("post.start_time",'','strtotime');
            $data['end_time'] = I("post.end_time",'','strtotime');
			$data['id'] = $id;
            if($model->create($data)!=false){
				if($id){
					if($id=$model->save()){
						$this->success('编辑成功',U('Redpacket/index'));
					}
					else{
						$this->error('服务器繁忙,请稍候再试');
					}
				}
				else{
					$model->token = session('token');
					$model->create_time = time();
					if($id=$model->add()){
						$this->success('添加成功',U('Redpacket/index'));
					}
					else{
						$this->error('服务器繁忙,请稍候再试');
					}
				}
            }
			else{
                $this->error($data->getError());
            }
        }
		else{
			if($id){
				$item = M('redpacket')->where(['token'=>session('token'),'id'=>$id])->find();
				$item['start_time'] = date('Y-m-d',$item['start_time']);
				$item['end_time'] = date('Y-m-d',$item['end_time']);
				$this->assign('set',$item);
			}
            $this->display();
        }
	}
	
	
	//删除红包
	public function del(){
        $id = I('get.id');
        $model = M('Redpacket');
        $find = array('id'=>$id,'token'=>session('token'));
        $result = $model->where($find)->find();
        if($result){
            $model->where('id='.$result['id'])->delete();
			M('redpacket_record')->where(['redpacket_id'=>$id])->delete();
            $this->success('删除成功',U('Redpacket/index'));
        }
		else{
            $this->error('非法操作！');
        }
    }
	
	public function record(){
		$id = I('get.id',0,'intval');
		$where = ['token'=>session('token'),'redpacket_id'=>$id];
		$count = M('redpacket_record')->where($where)->count();
		$page = new Page(20,$count);
		$records = M('redpacket_record')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$records);
		$this->assign('page',$page->show());
		$this->display();
	}
}
?>