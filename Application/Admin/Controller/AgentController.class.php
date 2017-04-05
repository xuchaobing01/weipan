<?php
namespace Admin\Controller;
use User\Model;
use Spark\Util\Page;
class AgentController extends BaseController{
	public function index(){
		$list = M('agent')->order('id desc')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id){
				$_POST['id'] = $id;
				$ret = M('agent')->save($_POST);
			}
			else{
				$_POST['create_time'] = time();
				$ret = M('agent')->add($_POST);
			}
			if($ret)$this->success('操作成功！');
			else $this->error('操作失败！');
		}
		else{
			if($id){
				$agent = M('agent')->find($id);
				$this->assign('info',$agent);
			}
			$this->assign('levels',C('AGENT_LEVELS'));
			$this->display();
		}
	}
}