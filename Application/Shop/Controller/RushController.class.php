<?php
class RushAction extends Action {
	public function index(){
		import('ORG.Cache');
		$token = $_GET['token'];
		dump($token);
		$cacheSet = array('host'=>C('DATA_CACHE_HOST'),'port'=>C('DATA_CACHE_PORT'),'prefix'=>$token);
		dump($cacheSet);
		$cache = new MCache($cacheSet);
		dump($cache->get('mall_config'));
    }
}