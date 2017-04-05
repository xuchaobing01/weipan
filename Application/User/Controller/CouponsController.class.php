<?php
/**
 *@ class CouponsController 大众优惠券
 */
namespace User\Controller;
use Spark\Util\Page;
class CouponsController extends UserController{
	public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
	public function index(){
		$where['token'] = $this->token;
		$list=M('Coupons')->where($where)->order('id DESC')->select();
		$count = M('Coupons')->where($where)->count();
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=M('Coupons');
			$_POST['starttime']=strtotime(I('post.startdate'));
			$_POST['endtime']=strtotime(I('post.enddate'));
			$_POST['createtime']=time();
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
			$_POST['token']=$this->token;
			if($_POST['endtime'] < $_POST['starttime']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				if($data->create()!=false){
					if($data->add()){
						$this->success('活动创建成功',U('Coupons/index'));
					}
					else{
						$this->error('服务器繁忙,请稍候再试');
					}
				}
				else{
					$this->error($data->getError());
				}
			}
		}
		else{
			$this->display();
		}	
	}
	/**
	 * @method setinc 编辑活动
	 */
	public function edit(){
		if(IS_POST){
			$data=M('Coupons');
			$_POST['id']=I('get.id');
			$_POST['token']=$this->token;
			$_POST['starttime']=strtotime(I('post.startdate'));
			$_POST['endtime']=strtotime(I('post.enddate'));
			$_POST['createtime']=time();
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
			
			if($_POST['endtime'] < $_POST['starttime']){
				$this->error('结束时间不能小于开始时间');
			}
			else{
				$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
				$check=$data->where($where)->find();
				if($check==false)$this->error('非法操作');
				if($data->where($where)->save($_POST)){
					$this->success('修改成功',U('Coupons/index'));
				}
				else{
					$this->error('操作失败');
				}
			}
		}
		else{
			$id=I('get.id');
			$where=array('id'=>$id,'token'=>$this->token);
			$data=M('Coupons');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$coupons=$data->where($where)->find();
			$this->assign('vo',$coupons);
			$this->display('add');
		}
	}
	public function del(){
		$id=I('get.id');
		$where=array('id'=>$id,'token'=>$this->token);
		$data=M('Coupons');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($where)->delete();
		if($back==true){
			//删除中奖记录
			//M('Coupons_record')->where(array('lid'=>$id,'token'=>session('token')))->delete();
			//删除关键词
			$this->success('删除成功');
		}
		else{
			$this->error('操作失败');
		}
	
	}
	public function prize_log(){
		$id = I('get.id',0,'intval');
		$model = M('coupons_item');
		$where = ['lid'=>$id,'token'=>session('token')];
		$count = $model->where($where)->count();
		$page = new Page($count,20);	
		$show       = $page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $model->where($where)->order('isuser desc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	public function yhq(){
		$data =M("coupons_item");
		import("ORG.Util.Page"); // 导入分页类
		$cid= $_GET['id'];
		$where = array('token'=>$this->token,'lid'=>$cid);
		$count      = $data->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $data->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();	
		echo 
		$this->assign('cid',$cid);
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
		
		
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
		$model = M('coupons_item');
		//$model->where(['token'=>$token])->delete();
		foreach($data as $row){
			$data = array();
			$data['title'] = $row[0];
			$data['lid'] = $_GET['cid'];
			$data['token'] = $token;
			$data['ticket'] = $row[1];
			$model->add($data);
		}
		$this->ajaxReturn(['errcode'=>0,'total'=>count($data)]);
	}
	public function message(){		
		$leave_model =M("coupons_record");
		import("ORG.Util.Page"); // 导入分页类
		$lid= $_GET['id'];
		$where = array('lid'=>$lid);
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('lid',$lid);
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
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