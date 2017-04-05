<?php
namespace Shop\Controller;
use Wap\Controller\WapController;
class BaseController extends WapController {
	public function _initialize(){
		parent::_initialize();
		
		if(S('MALL_CONFIG')==false){
			$config = M('mall_config','sp_')->where(['token'=>$this->token])->find();
			if(empty($config['theme_color']))$config['theme_color'] = "rgb(84, 176, 4)";
			S('MALL_CONFIG',$config,600);
		}
		else $config = S('MALL_CONFIG');
		$this->config = $config;
		$this->assign('config',$config);
	}
}