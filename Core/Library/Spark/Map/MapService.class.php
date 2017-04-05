<?php
/**
 * @class MapService 调用百度地图 web服务API
 * @desc 定位服务，导航，搜索
 */
namespace Spark\Map;
use Spark\Map\StaticMap;
class MapService {
	//公司静态地图
	public function staticCompanyMap(){
		//main company
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		$thisCompany=$company_model->where($where)->order('isbranch ASC')->find();
		//branches
		$where['isbranch']=1;
		$companies=$company_model->where($where)->order('taxis ASC')->select();
		$imgUrl = tstaticmap($thisCompany['latitude'], $thisCompany['longitude']);
		$titleStr=$thisCompany['name'].'地图';
		if ($companies){
			$titleStr='1.'.$titleStr;
		}
		
		$return[]=array($titleStr,"电话：".$thisCompany['tel']."\r\n地址：".$thisCompany['address']."\r\n点击查看详细",$imgUrl,WU('Wap/Company/index',['id'=>$thisCompany['id'],'token'=>$this->token],true));
		if ($companies){
			$i=2;
			$sep='';
			foreach ($companies as $thisCompany){
				$imgUrl = tstaticmap($thisCompany['latitude'],$thisCompany['longitude'],12,80,80);
				$return[]=array($i.'.'.$thisCompany['name'].'地图',"电话：".$thisCompany['tel']."\r\n地址：".$thisCompany['address']."\r\n点击查看详细",$imgUrl,WU('Wap/Company/index',['id'=>$thisCompany['id'],'token'=>$this->token],true));
				$i++;
			}
			//使用操作
			$imgUrl=$thisCompany['logourl'];
			$return[]=array('回复“最近的”查看哪一个离你最近，或者回复“开车去+编号”“步行去+编号”或“坐公交+编号”获取详细线路',"电话：".$thisCompany['tel']."\r\n地址：".$thisCompany['address']."\r\n点击查看详细",$imgUrl,WU('Wap/Company/index',[],true));
		}
		
		return $return;
	}
	
	public function walk($x,$y,$companyid=1){
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		$companies=$company_model->where($where)->order('isbranch ASC,taxis ASC')->select();
		$i=intval($companyid)-1;
		$thisCompany=$companies[$i];
		//
		$rt=json_decode(file_get_contents('http://api.map.baidu.com/direction/v1?mode=walking&origin='.$x.','.$y.'&destination='.$thisCompany['latitude'].','.$thisCompany['longitude'].'&region=&output=json&ak='.$this->apikey),1);
		if (is_array($rt)){
			$return=array();
			//
			$imgUrl='http://api.map.baidu.com/staticimage?center='.$thisCompany['longitude'].','.$thisCompany['latitude'].'&width=640&height=320&zoom=13&markers='.$thisCompany['longitude'].','.$thisCompany['latitude'];
			//长度
			$distance=$rt['result']['routes'][0]['distance'];
			if ($distance>1000){
				$distanceStr=(round($distance/1000,2)).'公里';
			}else {
				$distanceStr=$distance.'米';
			}
			//耗时
			$duration=$rt['result']['routes'][0]['duration']/60;
			if ($duration>60){
				$durationStr=intval($duration/100).'小时';
				if ($duration%60>0){
					$durationStr.=($duration%60).'分钟';
				}
			}else {
				$durationStr=intval($duration).'分钟';
			}
			//路书
			$stepStr="";
			$steps=$rt['result']['routes'][0]['steps'];
			if ($steps){
				$i=1;
				foreach ($steps as $s){
					$stepStr.="\r\n".$i.".".str_replace(array('<b>','</b>'),'',$s['instructions']);
					$i++;
				}
			}
			$return[]=array('步行到'.$thisCompany['name'].'行程有'.$distanceStr.',大概需要'.$durationStr,"具体方案：".$stepStr,$imgUrl,C('site_url').'/index.php?g=Wap&m=Company&a=walk&longitude='.$y.'&latitude='.$x.'&companyid='.$thisCompany['id'].'&token='.$this->token);
			return $return;
		}
		else {
			return '没有相应的路线';	
		}
	}
	
	public function drive($x,$y,$companyindex=1){
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		$companies=$company_model->where($where)->order('isbranch ASC,taxis ASC')->select();
		$i=intval($companyindex)-1;
		$thisCompany=$companies[$i];
		//
		$rt=json_decode(file_get_contents('http://api.map.baidu.com/direction/v1?mode=driving&origin='.$x.','.$y.'&destination='.$thisCompany['latitude'].','.$thisCompany['longitude'].'&origin_region=&destination_region=&output=json&ak='.$this->apikey),1);
		if (is_array($rt)){
			$return=array();
			//
			$imgUrl='http://api.map.baidu.com/staticimage?center='.$thisCompany['longitude'].','.$thisCompany['latitude'].'&width=640&height=320&zoom=13&markers='.$thisCompany['longitude'].','.$thisCompany['latitude'];
			//长度
			$distance=$rt['result']['routes'][0]['distance'];
			if ($distance>1000){
				$distanceStr=(round($distance/1000,2)).'公里';
			}else {
				$distanceStr=$distance.'米';
			}
			//耗时
			$duration=$rt['result']['routes'][0]['duration']/60;
			if ($duration>60){
				$durationStr=intval($duration/100).'小时';
				if ($duration%60>0){
					$durationStr.=($duration%60).'分钟';
				}
			}else {
				$durationStr=intval($duration).'分钟';
			}
			//路书
			$stepStr="";
			$steps=$rt['result']['routes'][0]['steps'];
			if ($steps){
				$i=1;
				foreach ($steps as $s){
					$stepStr.="\r\n".$i.".".strip_tags($s['instructions']);
					$i++;
				}
			}
			$return[]=array('开车到'.$thisCompany['name'].'行程有'.$distanceStr.',大概需要'.$durationStr,"具体方案：".$stepStr,$imgUrl,C('site_url').'/index.php?g=Wap&m=Company&a=drive&longitude='.$y.'&latitude='.$x.'&companyid='.$thisCompany['id'].'&token='.$this->token);
			return $return;
		}
		else {
			return '没有相应的路线';	
		}
	}
	
	public function bus($x='',$y='',$companyindex=1){
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		$companies=$company_model->where($where)->order('isbranch ASC,taxis ASC')->select();
		$i=intval($companyindex)-1;
		$thisCompany=$companies[$i];
		
		//查出起点所在城市
		$ocityArr=json_decode(file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$this->apikey.'&location='.$x.','.$y.'&output=json&pois=0'),1);
		$ocityName=$ocityArr['result']['addressComponent']['city'];
		//查出终点所在城市
		$dcityArr=json_decode(file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak='.$this->apikey.'&location='.$thisCompany['latitude'].','.$thisCompany['longitude'].'&output=json&pois=0'),1);
		$dcityName=$dcityArr['result']['addressComponent']['city'];
		if ($dcityName!=$ocityName){
			return '起点和终点不在同一城市，不支持公共交通查询';
		}
		
		$url='http://api.map.baidu.com/direction/v1?mode=transit&type=2&origin='.$x.','.$y.'&destination='.$thisCompany['latitude'].','.$thisCompany['longitude'].'&region='.$ocityName.'&output=json&ak='.$this->apikey;
		$rt=json_decode(file_get_contents($url),1);
		
		if (is_array($rt)){
			$return=array();
			//
			$imgUrl='http://api.map.baidu.com/staticimage?center='.$thisCompany['longitude'].','.$thisCompany['latitude'].'&width=640&height=320&zoom=13&markers='.$thisCompany['longitude'].','.$thisCompany['latitude'];
			//路书
			$schemeStr="";
			$schemes=$rt['result']['routes'][0]['scheme'];
			
			if ($schemes){
				$i=1;
				foreach ($schemes as $s){
					$distance=$this->_getDistance($s['distance']);
					$duration=$this->_getTime($s['duration']);
					$stepStr='';
					if ($s['steps']){
						$sep="";
						foreach ($s['steps'] as $step){
							$stepStr.=$sep.strip_tags($step[0]['stepInstruction']);
							$sep="\r\n";
						}
					}
					$schemeStr.="\r\n".$distance."/".$duration.":\r\n".$stepStr;
					$i++;
				}
			}
			$return[]=array('坐公交到'.$thisCompany['name'].'行程有'.$distance.',大概需要'.$duration,"推荐线路：\r\n".$schemeStr,$imgUrl,C('site_url').'/index.php?g=Wap&m=Company&a=bus&longitude='.$y.'&latitude='.$x.'&companyid='.$thisCompany['id'].'&token='.$this->token);
			return $return;
		}
		else {
			return '没有相应的路线';
		}
	}
	
	/**
	 * @method nearest 获取最离用户最近分店的位置信息
	 */
	public function nearest($x, $y){
		import('@.Map.StaticMap');
		$company_model=M('Company');
		$where=array('token'=>$this->token);
		
		$companies=$company_model->where($where)->order('isbranch ASC,taxis ASC')->select();
		$ldistance=0;
		$nearestCompany=array();
		$i=1;
		$index=0;
		$j=0;
		if ($companies){
			foreach ($companies as $c){
				$distance = $this->getDistance_map($x, $y, $c['latitude'], $c['longitude']);
				if ($ldistance==0){
					$nearestCompany=$c;
					$ldistance=$distance;
					$index=1;
				}
				else {
					if ($distance<$ldistance){
						$nearestCompany=$c;
						$ldistance=$distance;
						$index=$j+1;
					}
				}
				$j++;
			}
			$distanceStr=$this->_getDistance($ldistance);
			$smap = new StaticMap($nearestCompany['latitude'], $nearestCompany['longitude']);
			$smap->zoom=15;
			$imgUrl = $smap->mapUrl();
			
			$return[]=array('最近的是'.$nearestCompany['name'].'，大约'.$distanceStr,"回复'步行去".$index."','坐公交".$index."'或'开车去".$index."'获取详细路线图",$imgUrl,C('site_url').U("Wap/Company/map",['companyid'=>$nearestCompany['id'],'token'=>$this->token]));
			return $return;
		}
		else {
			return '还没配置公司位置信息呢，您稍等';
		}
		
	}
	
	function getDistance_map($lat_a, $lng_a, $lat_b, $lng_b) {
		//R是地球半径（米）
		$R = 6366000;
		$pk = doubleval(180 / 3.14169);    
		$a1 = doubleval($lat_a / $pk);
		$a2 = doubleval($lng_a / $pk);
		$b1 = doubleval($lat_b / $pk);
		$b2 = doubleval($lng_b / $pk);
		$t1 = doubleval(cos($a1) * cos($a2) * cos($b1) * cos($b2));
		$t2 = doubleval(cos($a1) * sin($a2) * cos($b1) * sin($b2));
		$t3 = doubleval(sin($a1) * sin($b1));
		$tt = doubleval(acos($t1 + $t2 + $t3));
		return round($R * $tt);
	}

	public function _getDistance($distance){
		if ($distance>1000){
			$distanceStr=(round($distance/1000,2)).'公里';
		}
		else {
			$distanceStr=$distance.'米';
		}
		return $distanceStr;
	}
	
	public function _getTime($duration){
		$duration=$duration/60;
		if ($duration>60){
			$durationStr=intval($duration/100).'小时';
			if ($duration%60>0){
				$durationStr.=($duration%60).'分钟';
			}
		}else {
			$durationStr=intval($duration).'分钟';
		}
		return $durationStr;
	}
	
	public function geodecoder($lat,$lng){
		$url = 'http://api.map.baidu.com/geocoder/v2/?ak='.$this->apikey.'&location='.$lat.','.$lng.'&output=json&pois=0';
		$ret = json_decode(file_get_contents($url));
		$options=[];
		
		$options['city']=$ret->result->addressComponent->city;
		$options['fullAddress']=$ret->result->formatted_address;
		$options['shortAddress']=$ret->result->addressComponent->district.$ret->result->addressComponent->street.$ret->result->addressComponent->street_number;
		return $options;
	}
}
?>