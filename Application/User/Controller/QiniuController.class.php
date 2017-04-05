<?php
namespace User\Controller;
use Think\Controller;
class QiniuController extends Controller{
	public function _initialize() {
		//读取keys.conf里的配置
		$this->bucket = C('QINIU_BUCKET');
		$this->domain = C('QINIU_DOMAIN');
		$this->accessKey = C('QINIU_ACCESS_KEY');
		$this->secretKey = C('QINIU_SECRET_KEY');
		$this->upload_domain = C('UPLOAD_DOMAIN');
		Vendor('Qiniu.io');
		Vendor('Qiniu.rs');
	}
	
	public function ueditor(){
		header("Content-Type: text/html; charset=utf-8");
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(__ROOT__."Public/Common/ueditor/php/config.json")), true);
		$action = $_GET['action'];

		switch ($action) {
			case 'config':
				$result =  json_encode($CONFIG);
				break;
				/* 上传图片 */
			case 'uploadimage':
				/* 上传涂鸦 */
			case 'uploadscrawl':
				/* 上传视频 */
			case 'uploadvideo':
				/* 上传文件 */
			case 'uploadfile':
					$result = $this->uploadUE();
					break;
				/* 列出图片 */
			case 'listimage':
					$result =  $this->listImage();
					break;
				/* 列出文件 */
			case 'listfile':
					$result = include("action_list.php");
					break;

				/* 抓取远程文件 */
			case 'catchimage':
					$result = include("action_crawler.php");
					break;

			default:
					$result = json_encode(array(
						'state'=> '请求地址出错'
					));
					break;
		}

		/* 输出结果 */
		if (isset($_GET["callback"])) {
			if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
			}
			else {
				echo json_encode(array(
					'state'=> 'callback参数不合法'
				));
			}
		}
		else {
			echo $result;
		}
	}
	
	public function listImage(){
	
	}
	
	
	//检查上传
	public function check($config){
		if (!empty($_FILES['upfile']['error'])) {
			switch($_FILES['upfile']['error']){
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			return $error;
		}
		return null;
	}
	
	public function uploadUE(){
		$error = $this->check();
		if($error != null) return json_encode(array("state" => $error));
		//服务器上临时文件名
		$tmp_name = $_FILES['upfile']['tmp_name'];

		/*增加新上传方式*/
		$_FILES['file'] = $_FILES['upfile'];
		unset($_FILES['upfile']);
		$url = $this->newUpload($_FILES);
		echo  json_encode(array('state' => 'SUCCESS','url' => $url));
		exit;
		/*end*/

		list($ret, $err) = $this->putFile2Qiniu($tmp_name);
		if ($err !== null) {
			return json_encode(array('state' =>$err->Err));
		}
		else {
			$key=$ret['key'];
			$url = Qiniu_RS_MakeBaseUrl($this->domain, $key);
			$this->record($key, $file_size, $url, $hash);
			return json_encode(array('state' =>'SUCCESS', 'url' => $url));
		}
	}
	
	private function genUploadKey(){
		list($usec, $sec) = explode(" ", microtime());
		$key = $sec.$usec;
		return session("uid").'/'.base64_encode($key);
	}
	
	function alert($msg) {
		header('Content-type: text/json; charset=UTF-8');
		echo json_encode(array('error' => 1, 'message' => $msg),JSON_UNESCAPED_UNICODE);
		exit;
	}
	
	/**
	 * @处理来自kindeditor编辑器的上传
	 */
	public function kindEditorUpload(){
		if (!empty($_FILES['imgFile']['error'])) {
			switch($_FILES['imgFile']['error']){
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			$this->alert($error);
		}

		//原文件名
		$file_name = $_FILES['imgFile']['name'];
		//服务器上临时文件名
		$tmp_name = $_FILES['imgFile']['tmp_name'];
		//文件大小
		$file_size = $_FILES['imgFile']['size'];
		//检查文件名
		if (!$file_name) {
			$this->alert("请选择文件。");
		}
		/*增加新上传方式*/
		$_FILES['file'] = $_FILES['imgFile'];
		unset($_FILES['imgFile']);
		$url = $this->newUpload($_FILES);
		echo  json_encode(array('error' => 0, 'key'=>'key','url' => $url));
		exit;

		/*end*/

		list($ret, $err) = $this->putFile2Qiniu($tmp_name);
		
		if ($err !== null) {
			$this->alert($err);
		}
		else {
			$key=$ret['key'];
			//$url = Qiniu_RS_MakeBaseUrl($this->domain, $key);
			$url ="http://{$this->domain}/$key";
			$this->record($key, $file_size, $url, $hash);
			echo json_encode(array('error' => 0, 'url' => $url));
		}
	}
	
	public function upload(){
		if (!empty($_FILES['file']['error'])) {
			switch($_FILES['file']['error']){
				case '1':
					$error = '超过php.ini允许的大小。';
					break;
				case '2':
					$error = '超过表单允许的大小。';
					break;
				case '3':
					$error = '图片只有部分被上传。';
					break;
				case '4':
					$error = '请选择图片。';
					break;
				case '6':
					$error = '找不到临时目录。';
					break;
				case '7':
					$error = '写文件到硬盘出错。';
					break;
				case '8':
					$error = 'File upload stopped by extension。';
					break;
				case '999':
				default:
					$error = '未知错误。';
			}
			$this->alert($error);
		}

		//原文件名
		$file_name = $_FILES['file']['name'];
		//服务器上临时文件名
		$tmp_name = $_FILES['file']['tmp_name'];
		//文件大小
		$file_size = $_FILES['file']['size'];
		//检查文件名
		if (!$file_name) {
			$this->alert("请选择文件。");
		}
		
		/*增加新上传方式*/
		$url = $this->newUpload($_FILES);
		echo  json_encode(array('error' => 0, 'key'=>'key','url' => $url));
		exit;

		/*end*/


		list($ret, $err) = $this->putFile2Qiniu($tmp_name);
		
		if ($err !== null) {
			$this->alert($err);
		}
		else {
			$key = $ret['key'];
			$hash= $ret['hash'];
			$url ="http://{$this->domain}/$key";
			$this->record($key, $file_size, $url, $hash);
			echo json_encode(array('error' => 0, 'key'=>$key,'url' => $url));
		}
	}
	public function newUpload(){
		$path="Uploads/".session("uid")."/"; //上传路径
		$tp = array("image/gif","image/pjpeg","image/jpeg",'image/png');
		//检查上传文件是否在允许上传的类型
		if(!in_array($_FILES["file"]["type"],$tp))
		{
			$this->alert("格式不对。");
		}//END IF
		$filetype = $_FILES['file']['type'];
		if(in_array($filetype, array('image/jpeg','image/jpg','image/pjpeg'))){
			$type = '.jpg';
		}
		if($filetype == 'image/gif'){
			$type = '.gif';
		}
		if($filetype == 'image/png'){
			$type = '.png';
		}
			
		if($_FILES["file"]["name"])
		{
			$today=date("YmdHis"); //获取时间并赋值给变量
			$file2 = $path.$today.$type; //图片的完整路径
			$img = $today.$type; //图片名称
			$flag=1;
		}//END IF
		
		if(!file_exists($path))
		{
			//检查是否有该文件夹，如果没有就创建，并给予最高权限
			mkdir("$path", 0700);
		}
		if($flag) {
			$result=move_uploaded_file($_FILES["file"]["tmp_name"],$file2); //临时文件
		}
		return $this->upload_domain.$path.$img;
		//return  json_encode(array('error' => 0, 'key'=>'key','url' => 'http://w.pinnoocle.com/'.$path.$img));
		
	}
	/**
	 * @method makeCallbackPolicy 生成回调上传策略
	 */
	private function makeCallbackPolicy(){
		$options = [];
		$options["key"] = $this->genUploadKey();
		Qiniu_SetKeys($this->accessKey, $this->secretKey);
		//构造上传策略
		$putPolicy = new \Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->CallbackUrl = C('site_url').U('Qiniu/callback');
		$putPolicy->CallbackBody = 'key=$(key)&name=$(fname)&hash=$(etag)&size=$(fsize)&uid='.session('uid');
		$options["token"] = $putPolicy->Token(null);
		$options['PHPSESSID'] = session_id();
		return $options;
	}
	
	/**
	 * @method makeRedirectPolicy 生成303重定向上传策略
	 */
	private function makeRedirectPolicy(){
		$options = [];
		$options["key"] = $this->genUploadKey();
		Qiniu_SetKeys($this->accessKey, $this->secretKey);
		//构造上传策略
		$putPolicy = new \Qiniu_RS_PutPolicy($this->bucket);
		$putPolicy->ReturnUrl = 'http://'.$_SERVER['SERVER_NAME'].'/'.U("Qiniu/uploadRedirect");
		$putPolicy->ReturnBody = '{"key":$(key),"name": $(fname),"hash": $(etag),"size": $(fsize)}';
		$options["token"] = $putPolicy->Token(null);
		$options['PHPSESSID'] = session_id();
		return $options;
	}
	
	/**
	 * @method putFile2Qiniu 从服务端上传文件到七牛服务器
	 */
	private function putFile2Qiniu($file_name){
		$key = $this->genUploadKey();
		Qiniu_SetKeys($this->accessKey, $this->secretKey);
		$putPolicy = new \Qiniu_RS_PutPolicy($this->bucket);
		$token = $putPolicy->Token(null);
		$putExtra = new \Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		return Qiniu_PutFile($token, $key, $file_name, $putExtra);
	}
	
	/**
	 * @method uploadPolicy 获取上传凭证
	 */
	public function uploadPolicy(){
		$type=I('get.type',0,'intval');
		if($type==0){
			$options=$this->makeCallbackPolicy();//获取回调凭证
		}
		elseif($type==1){
			$options=$this->makeRedirectPolicy();//获取重定向凭证
		}
		$this->ajaxReturn($options);
	}
	
	/**
	 * @method callback 用于处理七牛服务器回调业务
	 */
	public function callback(){
		header("Content-type: application/json; charset=utf-8");
		$content = file_get_contents('php://input');
		$key = $_POST['key'];
		$hash = $_POST['hash'];
		$size = $_POST['size'];
		\Think\Log::record('qiniu_callback:'.$content,'INFO');
		$url = Qiniu_RS_MakeBaseUrl($this->domain, $key);
		if(empty($_POST['uid'])){
			echo json_encode(['status'=>'403','message'=>'missing parameter!']);
			exit;
		}
		
		$this->record($key, $size, $url, $hash, $_POST['uid']);
		echo json_encode(["url"=>$url,'content'=>$content]);
		exit;
	}
	
	/**
	 * @method uploadRedirect 303重定向方法
	 */
	public function uploadRedirect(){
		$returnBody = json_decode(base64_decode(I("get.upload_ret")));
		$key = $returnBody->key;
		//上传资源的URL地址
		$assetUrl = Qiniu_RS_MakeBaseUrl($this->domain, $key);
		$this->assign('assetUrl',$assetUrl);
		$this->record($returnBody->key, $returnBody->size, $assetUrl, $returnBody->hash);
		$this->ajaxReturn($assetUrl,'success',0);
	}
	
	/**
	 * @method record 记录上传信息
	 */
	public function record($key, $size, $url, $hash='', $uid){
		$data['key']=$key;
		$data['etag']=$hash;
		$data['size']=$size;
		$data['time']=time();
		
		if(empty($uid)){
			$uid=$_SESSION['uid'];
		}
		$data['uid']=$uid;
		$data['bucket']=$this->bucket;
		$data['url']=$url;
		M('Upload_attachment')->data($data)->add();
	}
	
	/**
	 *C('accessKey')取得 AccessKey
	 *C('secretKey')取得 SecretKey  
	 *callback.php 为回调地址的Path部分  
	 *file_get_contents('php://input')获取RequestBody,其值形如:  
	 *name=sunflower.jpg&hash=Fn6qeQi4VDLQ347NiRm-RlQx_4O2\
	 *&location=Shanghai&price=1500.00&uid=123
	*/
	function IsQiniuCallback(){
		$authstr = $_SERVER['HTTP_AUTHORIZATION'];
		if(strpos($authstr,"QBox ")!=0){
			return false;
		}
		$auth = explode(":",$substr($authstr,5));
		if(sizeof($auth)!=2||$auth[0] != $this->accessKey){
			return false;
		}
		$data = "/callback.php\n".file_get_contents('php://input');
		return URLSafeBase64Encode(hash_hmac('sha1',$data,C("secretKey"), true)) == $auth[1];
	}
}
?>