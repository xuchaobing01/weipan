<?php
function wap_u($module,$params=[]){
	return rtrim(C('WAP_DOMAIN'),'/').U($module,$params);
}

function strtrunk($str,$len=10,$suffix='...'){
	if(mb_strlen($str,'utf-8')>$len){
		return mb_substr($str,0,$len,'utf-8').$suffix;
	}
	else{
		return $str;
	}
}
?>