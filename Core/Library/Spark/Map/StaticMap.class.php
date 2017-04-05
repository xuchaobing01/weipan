<?php
/**
 * @class 调用百度地图API，生成静态地图URL
 * @desc URL长度：2048,点标记的数量：50个
 */
namespace Spark\Map;
class StaticMap {
		public $width = "640";
		public $height = "320";
		public $zoom = 11;
		public $url= 'http://api.map.baidu.com/staticimage';
		public $lng = null;
		public $lat = null;
		public $address = "";
		
		public function __construct($lat, $lng) {
			if(isset($lng)){
				$this->lat=$lat;
				$this->lng=$lng;
			}
			else {
				$this->address=$lat;
			}
		}
		
		public function mapUrl(){
			$url=$this->url;
			if(!is_null($this->lng)&&!is_null($this->lat)){
				$url="$url?center={$this->lng},{$this->lat}&markers={$this->lng},{$this->lat}";
			}
			else {
				$url="$url?center={$this->address}&markers={$this->address}";
			}
			return "$url&width={$this->width}&height={$this->height}&zoom={$this->zoom}&markerStyles=l,1";
		}
	}
?>