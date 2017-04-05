<?php
namespace User\Controller;
use Think\Controller;

class BaseController extends Controller {
    protected function _initialize(){
		S(array('type'=>C('DATA_CACHE_TYPE'),'host'=>C('DATA_CACHE_HOST'),'port'=>C('DATA_CACHE_PORT'),'expire'=>C('DATA_CACHE_TIME')));
		define('RES','/Public/'.MODULE_NAME);
		define('STATICS','/Public/Common');
        $this->assign('action', ACTION_NAME);
    }
	
    protected function all_insert($name = '', $back = 'index'){
        $name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else {
            $id = $db->add();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Selfform',
                    'Panorama',
					'Home',
					'Photo',
					'Wedding'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']     = $id;
                    $data['module']  = $name;
                    $data['token']   = session('token');
                    $data['keyword'] = $_POST['keyword'];
					if($_POST['type']!=''){
						$data['is_strict']  = $_POST['type'];
					}
                    M('Keyword')->add($data);
                }
                $this->success('操作成功', U(CONTROLLER_NAME .'/'. $back));
            }
			else {
                $this->error('操作失败', U(CONTROLLER_NAME .'/'. $back));
            }
        }
    }
	
    protected function insert($name = '', $back ='index'){
		$name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else {
            $id = $db->add();
            if ($id == true) {
                $this->success('操作成功', U(CONTROLLER_NAME.'/'.$back));
            } else {
                $this->error('操作失败', U(CONTROLLER_NAME.'/'.$back));
            }
        }
    }
	
	protected function insertEx($name = '', $back){
        if(empty($back)){
			$back = U('index');
		}
		$name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else {
            $id = $db->add();
            if ($id == true) {
                $this->success('操作成功', $back);
            }
			else {
                $this->error('操作失败', $back);
            }
        }
    }
	
    protected function save($name = '', $back = 'index') {
        $name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else {
            $id = $db->save();
            if ($id == true) {
                $this->success('操作成功', U(CONTROLLER_NAME .'/'. $back));
            }
			else {
                $this->error('操作失败', U(CONTROLLER_NAME .'/'. $back));
            }
        }
    }
	
    protected function all_save($name = '', $back = 'index') {
        $name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else {
            $id = $db->save();
            if ($id !== false) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
                    'Product',
                    'Selfform',
					'Panorama',
					'Home',
					'Photo'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']    = $_POST['id'];
                    $data['module'] = $name;
                    $data['token']  = session('token');
                    $up['keyword']  = $_POST['keyword'];
					if($_POST['type']!=''){
						$up['is_strict']  = $_POST['type'];
					}
                    M('Keyword')->where($data)->save($up);
                }
				$this->success('操作成功', U(CONTROLLER_NAME .'/'. $back));
            }
			else {
                $this->error('操作失败', U(CONTROLLER_NAME .'/'. $back));
            }
        }
    }
	
	protected function update_all($name = '', $back = 'index') {
		$name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        }
		else{
			$db->save();
			$this->success('操作成功', U(CONTROLLER_NAME .'/'. $back));
		}
	}
	
    protected function all_del($id, $name = '', $back = '/index'){
        $name = $name ? $name : CONTROLLER_NAME;
        $db   = D($name);
        if ($db->delete($id)) {
            $this->ajaxReturn('操作成功', U(CONTROLLER_NAME .'/'. $back));
        } else {
            $this->ajaxReturn('操作失败', U(CONTROLLER_NAME .'/'. $back));
        }
    }
	
	/**
	 * @method auto_save 自动保存
	 * @desc 如果数据库记录不存在则add,否则save
	 */
	protected function auto_save($name,$data){
		$name = $name ? $name : MODULE_NAME;
        $db   = D($name);
		if(!isset($data)|| !is_array($data)){
			$data = $_POST;
		}
        if ($db->create($data) === false) {
            return ['errcode'=>500,'errmsg'=>$db->getError()];
        }
		if($db->id){
			$id = $db->save();
			if($id) return ['errcode'=>0,'id'=>$db->id,'action'=>'update'];
			else return ['errcode'=>500,'errmsg'=>$db->getError()];
		}
		else{
			$id = $db->add();
			if($id) return ['errcode'=>0,'id'=>$id,'action'=>'insert'];
			else return ['errcode'=>500,'errmsg'=>$db->getError()];
		}
	}
	
	protected function save_keyword($pid,$keyword,$module,$is_strict){
		$module = $module ? $module : MODULE_NAME;
		$where = array('pid'=>$pid,'module'=>$module,'token'=>session('token'));
		$check = M('keyword')->where($where)->count();
		if($check == 0){
			$where['keyword'] = $keyword;
			M('keyword')->add($where);
		}
		else {
			M('keyword')->where($where)->setField('keyword',$keyword);
		}
	}
	
	protected function ajax($code,$msg,$data = []){
		header('Content-type:text/json');
		$data['errcode'] = $code;
		$data['errmsg'] = $msg;
		echo json_encode($data);
		exit;
	}
}
?>