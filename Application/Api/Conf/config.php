<?php
return array(
	//定义路由规则
	'URL_ROUTE_RULES' 		=> array(
		'proxy/:APPID'=>'Api/Weixin/proxy?APPID=:1',
		':token'        => 'Api/Weixin/index?token=:1',
	)
);
?>