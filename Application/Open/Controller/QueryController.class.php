<?php
 /**
  * class QueryAction 处理查询接口
  * @date 2014/01/23 
  * @author yanqizheng
  * @copyright SparkTech
  */
namespace Open\Controller;
use Think\Controller;
use Spark\Util\HttpClient;

class QueryController extends Controller {
	public function delivery(){
		$order_id = $_GET['oid'];
		$delivery_name = $_GET['company'];
		$url = 'http://huiwen.map.vitalinktech.com:8080/webquery.pl?profile=YBC0826&order_id='.$order_id;
		$resp = HttpClient::curlGet($url);
		echo $this->xml_to_json($resp);
	}
	
	private function xml_to_json($source) { 
		if(is_file($source)){ //传的是文件，还是xml的string的判断 
			$xml_array=simplexml_load_file($source); 
		}
		else{
			$xml_array=simplexml_load_string($source); 
		}
		$json = json_encode($xml_array,JSON_UNESCAPED_UNICODE); //php5，以及以上，如果是更早版本，请查看JSON.php 
		return $json; 
	}
	
	public function qrcode(){
		Vendor('Qrcode.qrlib');
		$data = $_GET['data'];
		if(empty($data)) $this->ajaxReturn(['status'=>400,'message'=>'missing data!']);
		$TMP_DIR = __ROOT__.ltrim(TEMP_PATH,'./').'QRcode/';
		if (!file_exists($TMP_DIR)) mkdir($TMP_DIR);
        
		$PNG_WEB_BASE_URL = C('site_url').'/'.ltrim(TEMP_PATH,'./').'QRcode/';
		$errorCorrectionLevel = 'L';
		if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    
		
		$matrixPointSize = 4;
		if(isset($_REQUEST['size'])){
			$matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
		}
		$filename = md5($data).'.png';
		
		if(!file_exists($TMP_DIR.$filename)) \QRcode::png($data, $TMP_DIR.$filename, $errorCorrectionLevel, $matrixPointSize, 2);
		$this->ajaxReturn(['status'=>0,'url'=>$PNG_WEB_BASE_URL.$filename,'message'=>'success']);
	}
}
?>