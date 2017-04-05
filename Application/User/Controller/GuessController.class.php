<?php
/**
 *@ class GuessController 微猜题控制类
 */
namespace User\Controller;
use Spark\Util\Page;
class GuessController extends UserController{
	public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
	public function index(){
		$data=M('Guess');
		$where=array('token'=>session('token'));		
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();	
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function addguess(){
		if(IS_POST){			
			$data = M('Guess');
			$_POST['token']=session('token');
			//$_POST['name'] = I("post.name");
			$_POST['des'] = strip_tags(I("post.info"));
			$_POST['createtime']=time();
			$_POST['starttime'] = strtotime(I('post.startdate'));
			$_POST['endtime'] = strtotime(I('post.enddate'));
			//$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			//$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
				
			$_POST['title'] = I("post.title");
			$_POST['banner'] = I("post.picurl");
			$_POST['banner2'] = I("post.banner2");	
			$_POST['rename1'] = I("rename1");
			$_POST['rename2'] = I("rename2");
					
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");			
			$_POST['musicurl'] = I("post.musicurl");
			$_POST['ppicurl'] = I("post.ppicurl");			
			if($_POST['endtime']<$_POST['starttime']){
				$this->error('结束时间不能小于开始时间!');
			}
			if($data->create()!=false){
				if($data->add()){
					$this->success('添加成功',U('Guess/index',array('token'=>session('token'))));
				}
				else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$this->display();
		}	
	}
	public function edit(){
		if(IS_POST){
			$data=M('Guess');
			$_POST['id'] = (int)I('get.id');
			$_POST['des'] = strip_tags(I("post.info"));
			$_POST['createtime']=time();
			$_POST['starttime'] = strtotime($_POST['startdate']);
			$_POST['endtime'] = strtotime($_POST['enddate']);
			//$_POST['content'] = I("content");
				
			$_POST['title'] = I("post.title");
			$_POST['banner'] = I("post.picurl");
			$_POST['banner2'] = I("post.banner2");
				
			$_POST['rename1'] = I("rename1");
			$_POST['rename2'] = I("rename2");
			//$_POST['rename3'] = I("rename3");
			//$_POST['rename4'] = I("rename4");
				
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");
				
			$_POST['musicurl'] = I("post.musicurl");
			$_POST['ppicurl'] = I("post.ppicurl");		
				
			if($_POST['endtime']<$_POST['starttime']){
				$this->error('结束时间不能小于开始时间!');
			}
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
				
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Guess/index',array('token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Guess/index',array('token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Guess');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('vo',$vo);
			$this->display('addguess');
		}
	}
	public function del(){//单项删除
		$id = I('get.id');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Guess');
			$check=$data->where($where)->find();
			if($check==false)   $this->error('非法操作');
			$r=$data->where($where)->delete();
			if($r==true){
				$this->success('操作成功',U('Guess/index'));
			}
			else{
				$this->error('服务器繁忙,请稍后再试',U('Guess/index'));
			}
		}
	}
	public function message(){
		$leave_model =M("guess_topic");
		import("ORG.Util.Page"); // 导入分页类
		$cid= $_GET['id'];
		$where = array('token'=>$this->token,'cid'=>$cid);
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

		$this->assign('cid',$cid);
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	
	public function addmessage(){
		if(IS_POST){
			$data =M("guess");
			$message=$_POST['content'];
			$id= $_POST['id'];
			$msgarr = array();	
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));	
			$msgarr['title']=$_POST['title'];
			$msgarr['token']=$this->token;
			$msgarr['createtime'] =time();
			$msgarr['cid']=$id;	
			$msgarr['name1'] =$_POST['name1'];
			$msgarr['name2'] =$_POST['name2'];
			$msgarr['name3'] =$_POST['name3'];
			$msgarr['rename'] =$_POST['rename'];
			$msgarr['banner'] =$_POST['banner'];
			$msgarr['startdate'] =strtotime($_POST['startdate']);
			$res = M('guess_topic')->add($msgarr);
			if($res){
				$this->success('题目添加成功',U('Guess/message',array('id'=>$id)));
			}else{
				$this->success('题目添加失败',U('Guess/message',array('id'=>$id)));
			}
		}else{
			$id=$_GET['id'];
			$this->assign('id',$id);			
			$endtime=time()+10*24*3600;
			$this->assign('endtime',$endtime);	
			$this->display();
		}
	}
	public function editmessage(){
		if(IS_POST){
			$data=M('Guess');
			$msgarr['title']=$_POST['title'];	
			$msgarr['name1'] =$_POST['name1'];
			$msgarr['name2'] =$_POST['name2'];
			$msgarr['name3'] =$_POST['name3'];
			$msgarr['rename'] =$_POST['rename'];
			$msgarr['banner'] =$_POST['banner'];
			$msgarr['startdate'] =strtotime($_POST['startdate']);	
			$where=array('id'=>$_POST['pid'],'token'=>session('token'));
			if( M('guess_topic')->create()){
				if( M('guess_topic')->where($where)->save($msgarr)){
					$this->success('修改成功!',U('Guess/message',array('id'=>$_POST['id'],'token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Guess/message',array('id'=>$_POST['id'],'token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('guess_topic');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('vo',$vo);
			$id=$_GET['cid'];
			$this->assign('id',$id);
			$this->assign('endtime',$vo['startdate']);
			$this->display('addmessage');
		}
	}
	public function delmessage(){//单项删除
		$leave_model = M("guess_topic");
		$id = $_GET['id'];
		$cid = $_GET['cid'];
		$where=array('id'=>$id,'token'=>session('token'));
		$res=$leave_model->where($where)->delete();
		if($res){
			$this->success("删除成功",U('User/Guess/message',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}else{
			$this->success("删除失败",U('User/Guess/message',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}
	}
	
	public function coupons(){
		$leave_model =M("guess_coupons");
		import("ORG.Util.Page"); // 导入分页类
		$cid= $_GET['id'];
		$where = array('token'=>$this->token,'lid'=>$cid);
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('cid',$cid);
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	public function editcoupons(){
		$cid = I('get.cid',0,'intval');
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$data['coupons'] = $_POST['title'];
			$data['cid'] = $_POST['cid'];
			if(!$data['coupons']){
				$this->error('编辑失败！');
			}
			if($id){
				$data['id'] = $id;
				$ret = M('guess_coupons')->save($data);
				if($ret != false){
					$this->success('操作成功');
				}
				else{
					$this->error('编辑失败！');
				}
			}
			else{
				$data['token'] = session('token');
				$data['createtime'] = time();
				$ret = M('guess_coupons')->add($data);
				if($ret != false){
					$this->success('操作成功');
				}
				else{
					$this->error('添加失败！');
				}
			}
		}
		else{
			if($id){
				$set = M('guess_coupons')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->assign('cid',$cid);
			$this->display();
		}
	}
	public  function delall(){
		$allid = rtrim($_GET['allid'],',');
		$where['id'] = array('in',$allid);
		$res=M("guess_coupons")->where($where)->delete();
		exit(json_encode('1'));
		
	}
	public function coupons_import(){
		$check = $this->checkUploadFile();
		if($check!=null){
			$this->ajaxReturn(['errcode'=>500,'errmsg'=>$check]);
			exit;
		}
		$filePath = $_FILES['file']['tmp_name'];;
		$data = $this->readExcel($filePath);
		if($data==false){
			$this->ajaxReturn(['errcode'=>500,'errmsg'=>'读取excel数据失败！']);
			exit;
		}
		//清除之前的数据
		$token = session('token');
		$model = M('guess_coupons');
		//$model->where(['token'=>$token])->delete();
	
		foreach($data as $row){
			$data = array();
			$data['coupons'] = $row[0];
			$data['lid'] = $_GET['cid'];
			$data['token'] = $token;	
			$model->add($data);
		}
		$this->ajaxReturn(['errcode'=>0,'total'=>count($data)]);
	}
	
	public function delcoupons(){//单项删除
		$leave_model = M("guess_coupons");
		$id = $_GET['id'];
		$cid = $_GET['cid'];
		$where=array('id'=>$id,'token'=>session('token'));
		$res=$leave_model->where($where)->delete();
		if($res){
			$this->success("删除成功",U('User/Guess/coupons',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}else{
			$this->success("删除失败",U('User/Guess/coupons',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}
	}
	
	public function tongji(){//统计
		
		list($date_start,$date_end) = $this->_date_range();
		$data = $this->_init_data($date_start,$date_end);
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end + 86400];
		$where['pid']=$_GET['id'];
		$list = M('Guess_tongji')->where($where)->order('id desc')->select();
		$this->assign('list',$list);
		foreach($list as $item){
			$key = $item['month'].$item['day'];
			$data['follow'][$key] = intval($item['viewnum']);
			$data['unfollow'][$key] = intval($item['guessnum']);
			
		}		
		$followNum = json_encode_array($data['follow']);
		$unfollowNum = json_encode_array($data['unfollow']);
		
		$this->assign('followNum',$followNum);
		$this->assign('unfollowNum',$unfollowNum);
		
		$this->assign('name',json_encode_array($data['name']));
		$this->assign('id',$where['pid']);
		$this->display();
		
	}
	public function export(){
		Vendor('PHPExcel.PHPExcel');
		$data = array();
		$title = ['日期','参与人数'];
		$data[] = $title;
		list($date_start,$date_end) = $this->_date_range();
		$where = ['token'=>session('token')];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end];
		$where['pid']=$_GET['id'];
		$list = M('power_tongji')->where($where)->field('time,num')->order('id desc')->select();
		foreach($list as $item){
			$row = array();
			$row[] = date('Y-m-d',$item['time']);
			$row[] = $item['num'];
			$data[] = $row;
		}
		createExcel('',$data);
	}
	private function _date_range(){
		$daterange = I('daterange');
		if(empty($daterange)) $daterange = 7;
		if(strpos($daterange,'~') > 0){
			$parse = explode('~',$daterange);
			$date_start = strtotime($parse[0]);
			$date_end = strtotime($parse[1]);
		}
		else{
			$today = date('Y-m-d');
			$date_end = strtotime($today) - 86400; //从昨天算起
			$date_start = $date_end - (intval($daterange)-1)*86400;
		}
		$this->assign('range',$daterange);
		return [$date_start,$date_end];
	}
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['name'][$key] = date('m-d',$tmp);
			$data['text'][$key] = 0;
			$data['img'][$key] = 0;
			$data['menu'][$key] = 0;
			$data['follow'][$key] = 0;
			$data['unfollow'][$key] = 0;
			$data['truefollow'][$key] = 0;
		}
		return $data;
	}
	
	
	
	
	
	
	
	
	
	private function readExcel($filePath){
		Vendor('PHPExcel.PHPExcel');
		$PHPExcel = new \PHPExcel();
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new \PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				return false;
			}
		}
		$data = array();
		$PHPExcel = $PHPReader->load($filePath);
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $PHPExcel->getSheet(0);
		/**取得最大的列号*/
		$allColumn = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
		/**从第二行开始输出，因为excel表中第一行为列名*/
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
			/**从第A列开始输出*/
			if($currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue()=='')continue;
			$row = array();
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();
				//echo iconv('gb2312','utf-8',$val)."\t";
				if($currentColumn == 'C'){
					$row[] = $this->getExcelDate($val);
				}
				else $row[] = $val;
			}
			$data[] = $row;
		}
		return $data;
	}
	
	public function getExcelDate($val){
		if(is_numeric($val)){
			//$jd = gregoriantojd(1, 1, 1970);
			$jd = 2440588;
			$gregorian = jdtogregorian($jd+intval($val)-25569);/**显示格式为 “月/日/年” */
			$tmp = explode('/',$gregorian);
			return $tmp[2].'/'.$tmp[0].'/'.$tmp[1];
		}
		else return $val;
	}
	private function checkUploadFile(){
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
			return $error;
		}
		return null;
	}
}
?>