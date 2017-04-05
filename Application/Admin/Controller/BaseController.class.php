<?php
/**
 * @class Admin模块控制器基类
 */
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;
class BaseController extends Controller{
	protected $pid;
	protected function _initialize(){
		define('RES','/Public/'.MODULE_NAME);
		define('STATICS','/Public/Common');
        $this->assign('action', ACTION_NAME);
		if(!isset($_SESSION['username'])){
			$this->error('非法操作',U('Admin/Admin/index'));
		}
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
		$this->show_nav();
	}
	
	private function getAllNode(){
		$where['level'] = 1;
		$where['pid'] = 1;
		$where['status'] = 1;
		$where['display'] = array('gt',0);
		$order['sort'] = 'asc';
		$nav=M('Node')->where($where)->order($order)->select();
	}
	
	private function show_nav(){
		$where['level'] = 1;
		$where['pid'] = 1;
		$where['status'] = 1;
		$where['display'] = array('gt',0);
		$order['sort'] = 'asc';
		$nav=M('Node')->where($where)->order($order)->select();
		$this->assign('nav',$nav);
	}
}
?>