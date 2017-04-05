<?php
	/**
	 * @function 实用功能函数
	 */
	function getIckdExpressInfo($companyEn, $number){
		$appid = "103223";
		$secret = "5d91adb21d97d109130d4671369910f4";
		$url= "http://api.ickd.cn/?id=".$appid."&secret=".$secret."&com=".$companyEn."&nu=".$number."&type=json&encode=utf8";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);

		if(curl_errno($ch))
		{ echo 'CURL ERROR Code: '.curl_errno($ch).', reason: '.curl_error($ch);}
		curl_close($ch);
		
		$AllInfo = json_decode($output, true);

		if($AllInfo['message'] != "" ){
			return $AllInfo['message'];
		}else{
			$result = "";
			foreach ($AllInfo["data"] as $singleStep)
			{
				$result .= $singleStep["time"]." ".$singleStep["context"]."\n";
			}
			return trim($result);
		}
	}
	
	function kuaidi($param){
		$expresses = array(
			"AAE"=>"aae","安捷"=>"anjie","安信达"=>"anxinda","ARAMEX"=>"aramex","巴伦支"=>"balunzhi","宝通达"=>"baotongda","成都奔腾"=>"benteng","CCES"=>"cces","长通"=>"changtong","程光"=>"chengguang","城际"=>"chengji","城市100"=>"chengshi100","传喜"=>"chuanxi","传志"=>"chuanzhi","出口易"=>"chukouyi","CITYLINK"=>"citylink","东方"=>"coe","城市之星"=>"cszx","大田"=>"datian","大洋"=>"dayang","德邦"=>"debang","德创"=>"dechuang","DHL"=>"dhl","店通"=>"diantong","递达"=>"dida","叮咚"=>"dingdong","递四方"=>"disifang","DPEX"=>"dpex","D速"=>"dsu","百福东方"=>"ees","EMS"=>"ems","凡宇"=>"fanyu","FARDAR"=>"fardar","FEDEX"=>"fedex","FEDEX国内"=>"fedexcn","飞邦"=>"feibang","飞豹"=>"feibao","原飞航"=>"feihang","飞狐"=>"feihu","飞特"=>"feite","飞远"=>"feiyuan","丰达"=>"fengda","飞康达"=>"fkd","广东邮政"=>"gdyz","国内小包"=>"gnxb","共速达"=>"gongsuda","国通"=>"guotong","山东海红"=>"haihong","海盟"=>"haimeng","昊盛"=>"haosheng","河北建华"=>"hebeijianhua","恒路"=>"henglu","华诚"=>"huacheng","华翰"=>"huahan","华企"=>"huaqi","华夏龙"=>"huaxialong","天地华宇"=>"huayu","汇强"=>"huiqiang","汇通"=>"huitong","海外环球"=>"hwhq","佳吉快运"=>"jiaji","佳怡"=>"jiayi","加运美"=>"jiayunmei","金大"=>"jinda","京广"=>"jingguang","晋越"=>"jinyue","急先达"=>"jixianda","嘉里大通"=>"jldt","康力"=>"kangli","顺鑫"=>"kcs","快捷"=>"kuaijie","宽容"=>"kuanrong","跨越"=>"kuayue","乐捷递"=>"lejiedi","联昊通"=>"lianhaotong","立即送"=>"lijisong","龙邦"=>"longbang","民邦"=>"minbang","明亮"=>"mingliang","闽盛"=>"minsheng","尼尔"=>"nell","港中能达"=>"nengda","OCS"=>"ocs","平安达"=>"pinganda","邮政"=>"pingyou","品速心达"=>"pinsu","全晨"=>"quanchen","全峰"=>"quanfeng","全际通"=>"quanjitong","全日通"=>"quanritong","全一"=>"quanyi","保时达"=>"rpx","如风达"=>"rufeng","赛澳递"=>"saiaodi","三态"=>"santai","伟邦"=>"scs","圣安"=>"shengan","盛丰"=>"shengfeng","盛辉"=>"shenghui","申通"=>"shentong","顺丰"=>"shunfeng","穗佳"=>"suijia","速尔"=>"sure","天天"=>"tiantian","TNT"=>"tnt","通成"=>"tongcheng","通和天下"=>"tonghe","UPS"=>"ups","USPS"=>"usps","万博"=>"wanbo","万家"=>"wanjia","微特派"=>"weitepai","祥龙运通"=>"xianglong","新邦"=>"xinbang","信丰"=>"xinfeng","希优特"=>"xiyoute","源安达"=>"yad","亚风"=>"yafeng","一邦"=>"yibang","银捷"=>"yinjie","音素"=>"yinsu","亿顺航"=>"yishunhang","优速"=>"yousu","一统飞鸿"=>"ytfh","远成"=>"yuancheng","圆通"=>"yuantong","元智捷诚"=>"yuanzhi","越丰"=>"yuefeng","誉美捷"=>"yumeijie","韵达"=>"yunda","运通中港"=>"yuntong","宇鑫"=>"yuxin","源伟丰"=>"ywfex","宅急送"=>"zhaijisong","郑州建华"=>"zhengzhoujianhua","芝麻开门"=>"zhima","中天万运"=>"zhongtian","中通"=>"zhongtong","忠信达"=>"zhongxinda","中邮"=>"zhongyou"
		);
        $appid = "103223";
        $secret = "5d91adb21d97d109130d4671369910f4";
        $url= "http://api.ickd.cn/?id=".$appid."&secret=".$secret."&com=".$expresses[$param[0]]."&nu=".$param[1]."&type=json&encode=utf8";
        Vendor('HttpClient');
        $contentStr=\Spark\Util\HttpClient::curlGet($url);
        $data=json_decode($contentStr);
        if($data->errCode != '0'){
           return $data->message;
        }
        $str="";
        foreach($data->data as $k=>$v){
            $str.=$v->time." ".$v->context."; \n";
        }
        return $str;

	}
	
	/*END OF KUAIDI*/
	
	//朗读
	function langdu($data){
		$data   = implode('', $data);
		$mp3url = 'http://www.apiwx.com/aaa.php?w=' . urlencode($data);
		return array(
			array(
				$data,
				'点听收听',
				$mp3url,
				$mp3url
			),
			'music'
		);
	}
	
	//健康查询
	function jiankang($data){
		if (empty($data)){
			return '主人，' . $this->my . "提醒您\n正确的查询方式是:\n健康+身高,+体重\n例如：健康170,65";
		}
		$height  = $data[1] / 100;
		$weight  = $data[2];
		$Broca   = ($height * 100 - 80) * 0.7;
		$kaluli  = 66 + 13.7 * $weight + 5 * $height * 100 - 6.8 * 25;
		$chao    = $weight - $Broca;
		$zhibiao = $chao * 0.1;
		$res     = round($weight / ($height * $height), 1);
		if ($res < 18.5) {
			$info = '您的体形属于骨感型，需要增加体重' . $chao . '公斤哦!';
			$pic  = 1;
		}
		elseif ($res < 24) {
			$info = '您的体形属于圆滑型的身材，需要减少体重' . $chao . '公斤哦!';
		}
		elseif ($res > 24) {
			$info = '您的体形属于肥胖型，需要减少体重' . $chao . '公斤哦!';
		}
		elseif ($res > 28) {
			$info = '您的体形属于严重肥胖，请加强锻炼，或者使用我们推荐的减肥方案进行减肥';
		}
		return $info;
	}
	
	/**
	 * @function yinle 音乐点播
	 * param[0] 歌曲名称
	 * param[1] 歌手名称
	 */
	function yinle($param){
		if (empty($param) || $param[0]== ""){
			$content = "你还没告诉我音乐名称呢？";
		}
		else{
			if (!empty($param[1])){
				$url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$param[0]."$$".$param[1]."$$$$";
			}else{
				$url = "http://box.zhangmen.baidu.com/x?op=12&count=1&title=".$param[0]."$$";
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			
			$content = "检索失败";
			@$menus = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
			if ($menus->count > 0 && isset($menus->url[0]) && isset($menus->durl[0])){
				$url_prefix = substr($menus->url[0]->encode,0,strripos($menus->url[0]->encode,'/') + 1);
				$url_suffix = substr($menus->url[0]->decode,0,strripos($menus->url[0]->decode,'&'));
				$durl_prefix = substr($menus->durl[0]->encode,0,strripos($menus->durl[0]->encode,'/') + 1);
				$durl_suffix = substr($menus->durl[0]->decode,0,strripos($menus->durl[0]->decode,'&'));
				$content = array($param[0],
								$param[1]?$param[1]:"百度音乐掌门人提供",
								$url_prefix.$url_suffix,
								$durl_prefix.$durl_suffix);
			}
		}
		return [$content,'music'];
	}
	
	function tianqi($data){
		if(!isset($data[0])){
			$city="合肥";
		}
		else{
			$city=$data[0];
		}
		$apiUrl="http://api.map.baidu.com/telematics/v3/weather?output=json&ak=eXXe9yiLQspb3AEkKYc4PBFD&location=".$city;
		
		$json = json_decode(file_get_contents($apiUrl));
		if($json->error==0){
			$weather=$json->results[0]->weather_data;
			return array([
				[
					$weather[0]->date." ".$weather[0]->weather.",".$weather[0]->wind.",".$weather[0]->temperature,
					"",
					$weather[0]->dayPictureUrl
				],
				[
					$weather[1]->date." ".$weather[1]->weather.",".$weather[1]->wind.",".$weather[1]->temperature,
					"",
					$weather[1]->dayPictureUrl
				]
			],"news");
		}
		else{
			return "天气查询方法:天气+城市,如天气 北京";
		}
	}

	function shouji($n){
		$name = implode('', $n);
		$str  = $this->myapi.urlencode($name);
		$json = json_decode(file_get_contents($str));
		$reply = urldecode($json->content);
		$reply   = str_replace('{br}', "\n", $reply);
		return $reply;
	}

	function shenfenzheng($n) {
		$n = implode('', $n);
		if (count($n) > 1) {
			$this->error_msg($n);
			return false;
		}
		$str1     = file_get_contents('http://www.youdao.com/smartresult-xml/search.s?jsFlag=true&type=id&q=' . $n);
		$array    = explode(':', $str1);
		$array[2] = rtrim($array[4], ",'gender'");
		$str      = trim($array[3], ",'birthday'");
		if ($str !== iconv('UTF-8', 'UTF-8', iconv('UTF-8', 'UTF-8', $str)))
			$str = iconv('GBK', 'UTF-8', $str);
		$str = '【身份证】 ' . $n . "\n" . '【地址】' . $str . "\n 【该身份证主人的生日】" . str_replace("'", '', $array[2]);
		return $str;
	}

	function gongjiao($data){
		$data = array_merge($data);
		if (count($data) != 2) {
			return "格式不正确";
			return false;
		}
        $url="http://openapi.aibang.com/bus/lines?app_key=6ab1930b7608c5fef196df94bacdd8bf&city=".$data[0]."&q=".$data[1];
        Vendor('HttpClient');
        $content=\Spark\Util\HttpClient::curlGet($url);
        $arr=xml_to_array($content);
        //print_r($arr);
        $str="";
        if(count($arr['root']['lines'])<=0){
            $str="没有查询到相关信息";
        }
        else{
            $a=$arr['root']['lines']['line'][0];
            $str=$a['name']."，途径站点：".$a['stats'];
            return $str;
        }

		/* http://www.aibang.com/api/mykeys
		 * KEY:6ab1930b7608c5fef196df94bacdd8bf
            私钥:489015bb20fd4a91
            类型:非商业用途
            申请时间:2015-01-15 18:25:06
            访问限额:1000次/日
		 * */

	}
    function xml_to_array( $xml )
    {
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches))
        {
            $count = count($matches[0]);
            $arr = array();
            for($i = 0; $i < $count; $i++)
            {
                $key= $matches[1][$i];
                $val = xml_to_array( $matches[2][$i] );  // 递归
                if(array_key_exists($key, $arr))
                {
                    if(is_array($arr[$key]))
                    {
                        if(!array_key_exists(0,$arr[$key]))
                        {
                            $arr[$key] = array($arr[$key]);
                        }
                    }else{
                        $arr[$key] = array($arr[$key]);
                    }
                    $arr[$key][] = $val;
                }else{
                    $arr[$key] = $val;
                }
            }
            return $arr;
        }else{
            return $xml;
        }
    }

	function huoche($data, $time = ''){
		$data    = array_merge($data);
		if (count($data) != 2) {
			$this->error_msg($data[0] . '至' . $data[1]);
			return false;
		}

        $station = include(dirname(__FILE__).'/train_station_name.php');
        $startstaion = $station[$data[0]];
        $endstation = $station[$data[1]];
        $date=date('Y-m-d', time());
        $url ="https://kyfw.12306.cn/otn/lcxxcx/query?purpose_codes=ADULT&queryDate=$date&from_station=$startstaion&to_station=$endstation";
        Vendor('HttpClient');
        $data = \Spark\Util\HttpClient::curlGet($url);
        $data=json_decode($data);
        $result="";
        if($data->status!=1){
            $result="error";
        }else{
            $rows=$data->data->datas;
            foreach($rows as $k=>$v){
                $result.= $v->station_train_code.",".$v->from_station_name."到".$v->to_station_name.",".$v->start_time."开; ";
            }
        }

        return $result;
	}

	function fanyi($param){
		$keyword = $param[0];
		if ($keyword == ""){
			return "请带上要翻译的内容哦";
		}
		
		$apihost = "http://fanyi.youdao.com/";
		$apimethod = "openapi.do?";
		$apiparams = array('keyfrom'=>"weimarket", 'key'=>"1503735639", 'type'=>"data", 'doctype'=>"json", 'version'=>"1.1", 'q'=>$keyword);
		$apicallurl = $apihost.$apimethod.http_build_query($apiparams);
		echo $apicallurl;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apicallurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		if(curl_errno($ch)){ 
			echo 'CURL ERROR Code: '.curl_errno($ch).', reason: '.curl_error($ch);
		}
		curl_close($ch);

		$youdao = json_decode($output, true);
		
		$result = "";
		switch ($youdao['errorCode']){
			case 0:
				if (isset($youdao['basic'])){
					$result .= $youdao['basic']['phonetic']."\n";
					foreach ($youdao['basic']['explains'] as $value) {
						$result .= $value."\n";
					}
				}else{
					$result .= $youdao['translation'][0];
				}
				break;
			default:
				$result = "系统错误：错误代码：".$errorcode;
				break;
		}
		return trim($result);
	}

	function caipiao($name){
		$name = array_merge($name);
		$url  = "http://api2.sinaapp.com/search/lottery/?appkey=0020130430&appsecert=fa6095e113cd28fd&reqtype=text&keyword=" . $name[0];
		$json = Http::fsockopenDownload($url);
		if ($json == false) {
			$json = file_get_contents($url);
		}
		$json = json_decode($json, true);
		$str  = $json['text']['content'];
		return $str;
	}

	function gupiao($name){
		$name = array_merge($name);
        $url="http://in.finance.yahoo.com/d/quotes.csv?s=000001.SZ&f=sl1d1t1c1ohgv&e";
        $qr=file_get_contents($url);echo $qr;
        //var_dump($qr);

		return '<img src="'.$url.'">';
	}
	
	function getmp3($data){
		$obj            = new getYu();
		$ContentString  = $obj->getGoogleTTS($data);
		$randfilestring = 'mp3/' . time() . '_' . sprintf('%02d', rand(0, 999)) . ".mp3";
		file_put_contents($randfilestring, $ContentString);
		return rtrim(C('site_url'), '/') . $randfilestring;
	}

	function baike($name){
		$name = implode('', $name);
		$name_gbk         = iconv('utf-8', 'gbk', $name);
		$encode           = urlencode($name_gbk);
		$url              = 'http://baike.baidu.com/list-php/dispose/searchword.php?word=' . $encode . '&pic=1';
		$get_contents     = httpGetRequest_baike($url);
		$get_contents_gbk = iconv('gbk', 'utf-8', $get_contents);
		preg_match("/URL=(\S+)'>/s", $get_contents_gbk, $out);
		$real_link     = 'http://baike.baidu.com' . $out[1];
		$get_contents2 = httpGetRequest_baike($real_link);
		preg_match('#"Description"\scontent="(.+?)"\s\/\>#is', $get_contents2, $matchresult);
		if (isset($matchresult[1]) && $matchresult[1] != "") {
			return htmlspecialchars_decode($matchresult[1]);
		}
		else {
			return "抱歉，没有找到与“" . $name . "”相关的百科结果。";
		}
	}

	function httpGetRequest_baike($url){
		$headers = array(
			"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language: en-us,en;q=0.5",
			"Referer: http://www.baidu.com/"
		);
		$ch      = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$output = curl_exec($ch);
		curl_close($ch);
		if ($output === FALSE) {
			return "cURL Error: " . curl_error($ch);
		}
		return $output;
	}
?>