<?php
	function create_token(){
		$randLen = 6;
		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$len = strlen($chars);
		$randStr = '';
		for ($i=0;$i<$randLen;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$token = $randStr.time();
		return $token;
	}
	
	/**
	 *@function json_encode_array
	 *@desc 把关联或索引数组转化为json数组
	 */
	function json_encode_array($data,$key){
		$ret = array();
		foreach($data as $item){
			$ret[] = $item;
		}
		return json_encode($ret);
	}
	
	function cache_wxsetting(){
		
	}
?>