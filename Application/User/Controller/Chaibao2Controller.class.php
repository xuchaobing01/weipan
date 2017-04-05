<?php
namespace User\Controller;
use Think\Controller;
use Spark\Util\Page;
class Chaibao2Controller extends UserController{
	public function index(){
		$list=M('Lottery')->field('id,title,joinnum,pv,keyword,startdate,enddate,status')->where(array('token'=>session('token'),'type'=>5))->order('id desc')->select();
		$this->assign('count',M('Lottery')->where(array('token'=>session('token'),'type'=>5))->count());
		$this->assign('list',$list);
		$this->display();	
	}
	
	public function sn(){
		$id = I('get.id');
		$data = M('Lottery')->where(array('token'=>session('token'),'id'=>$id,'type'=>5))->find();
		$where = array('lottery_id='.$id.' and status!=0');
		$count = M('Lottery_user')->where($where)->count();
		$page = new Page($count,20);
		$record = M('Lottery_user')->where($where)->order('time desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		$datacount = $data['fistnums'] + $data['secondnums'] + $data['thirdnums'];
		$this->assign('datacount',$datacount);//奖品数量
		$this->assign('recordcount',$count);//中奖数量
		$this->assign('record',$record);
		$this->assign('page',$page->show());
		$applyCount = M('Lottery_user')->where('lottery_id='.$id.' and status = 2')->count();
		$sendCount = M('Lottery_user')->where('lottery_id='.$id.' and status = 3')->count();
		$this->assign('applyCount',$applyCount);
		$this->assign('sendCount',$sendCount);
		$this->display();
	}
	
	public function add(){
		if(IS_POST){
			$data=D('lottery');
			$_POST['startdate'] = strtotime($_POST['startdate']);
			$_POST['enddate'] = strtotime($_POST['enddate']);
			$_POST['token'] = session('token');
			//处理幻灯片图片
			$imgs = $_POST['banners'];
			if(!empty($imgs) && is_array($imgs)){
				$_POST['reward6imgs'] = implode(';',$imgs);
			}
			$_POST['type'] = 5;
			if($data->create()!=false){
				if($id=$data->add()){
					$data1['pid']=$id;
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$data1['keyword']=$_POST['keyword'];
					M('Keyword')->add($data1);
					$user=M('Users')->where(array('id'=>session('uid')))->setInc('activity_num');
					$this->success('活动创建成功',U('Chaibao/index'));
				}else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->display();
		}
	}
	
	public function edit(){
		if(IS_POST){
			$data=D('Lottery');
			$_POST['id'] = I('get.id');
			$_POST['token']=session('token');
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token'],'type'=>5);
			$_POST['startdate'] = strtotime($_POST['startdate']);
			$_POST['enddate'] = strtotime($_POST['enddate']);
			//处理幻灯片图片
			$imgs = $_POST['banners'];
			if(!empty($imgs) && is_array($imgs)){
				$_POST['reward6imgs'] = implode(';',$imgs);
			}
			$check = $data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($id=$data->where($where)->save($_POST)!==false){
					$data1['pid']=$_POST['id'];
					$data1['module']='Lottery';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}
		else{
			$id = I('get.id');
			$where = array('id'=>$id,'token'=>session('token'),'type'=>5);
			$data = M('Lottery');
			$lottery = $data->where($where)->find();
			if($lottery==false){
				$this->error('非法操作');
			}
			$imgs = $lottery['reward6imgs'];
			if(!empty($imgs)){
				$lottery['banners'] = explode(';',$imgs);
			}
			$this->assign('vo',$lottery);
			$this->display('add');
		}
	}
	
	public function del(){
		$id = I('get.id');
		$where = array('id'=>$id,'token'=>session('token'));
		$data = M('Lottery');
		$check = $data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back = $data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Lottery'))->delete();
			$this->success('删除成功');
		}
		else{
			$this->error('操作失败');
		}
	}
	
	public function sendprize(){
		$id = I('get.id');
		$where=array('id'=>$id,'status'=>2);
		$data['sendtime'] = time();
		$data['status'] = 3;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('成功发奖');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function sendnull(){
		$id = I('id');
		$where=array('id'=>$id,'status'=>3);
		$data['sendtime'] = '';
		$data['status'] = 2;
		$back = M('Lottery_user')->where($where)->save($data);
		if($back==true){
			$this->success('已经取消');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function tongji(){
		$lid = I('get.lid',0,'intval');
		$records = M('lottery_tongji')->where(['lid'=>$lid])->order('id desc')->select();
		$this->assign('records',$records);
		$this->display();
	}
	private function _search(){
		$search['token'] = session('token');
		(I('truename') && ($search['truename'] = ['like','%'.I('truename').'%']));
		(I('number') && ($search['number'] = ['like','%'.I('number').'%']));
		(I('mobile') && ($search['tel'] = ['like','%'.I('mobile').'%']));
		return $search;
	}
	
	public function coupons(){		
		$leave_model =M("guess_coupons");
		$cid= $_GET['id'];
		import("ORG.Util.Page"); // 导入分页类
		(I('number') && ($where['coupons'] = ['like','%'.I('number').'%']));
		$where['lid']=$cid;
		$where['token']=$this->token;
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
	public function edit_coupons(){
		$id=I('id');
		$cid=I('cid');
		M("guess_coupons")->where('id='.$id)->save(array('isuser'=>0));
		$this->success('修改成功',U('coupons',array('id'=>$cid)));
	}
}
?>