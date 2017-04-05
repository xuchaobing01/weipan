<?php
/**
 * 通用模板管理
 * */
namespace User\Controller;
class TmplsController extends UserController {
    public function index() {
        $db = D('Wxuser');
        $where['token'] = session('token');
        $where['uid'] = session('uid');
        $info = $db->where($where)->find();
		$set = M('Home')->field('theme_color')->where(['token'=>session('token')])->find();
        $this->assign('info', $info);
		$this->assign('set',$set);
        $this->display();
    }

    public function add() {
        $db = M('Wxuser');
		$data['tpltypeid']=I('style',0,'intval');
        $where['token'] = session('token');
        $db->where($where)->save($data);
        
        $result=M('Home')->where(array('token'=>session('token')))->save(array('advancetpl'=>0));
		$this->ajaxReturn($result,"修改模板成功！",1);
    }

    public function lists() {
        $db = M('Wxuser');
		$data['tpllistid'] = I('style',0,'intval');
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }

    public function content() {
        $db = M('Wxuser');
		$data['tplcontentid'] = I('style',0,'intval');
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }
    
    public function background() {
        $data['color_id'] = I('style',0,'intval');
        $db = M('Wxuser');
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }

    public function color($v) {
        $data['theme_color'] = $v;
        $db = M('Home');
        $where['token'] = session('token');
        $db->where($where)->save($data);
    }

    public function upsave() {
	
    }

}

?>