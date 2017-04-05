<?php
/**
 *@ class ProductController 商城逻辑控制类
 *@ author yanqizheng
 *@ create 2014-03-28
 */
namespace User\Controller;
use Spark\Util\Page;
class SkiController extends UserController{
	public $product_model;
	public $product_cat_model;
	
	public function index(){
		$product_model = D('ski_product');
		$product_cat_model = M('ski_cate');
		$where=array('token'=>session('token'));
        if(IS_POST){
            $key = I('post.searchkey');
            if(empty($key)){
                $this->error("关键词不能为空");
            }
            $map['token'] = session('token'); 
            $map['name|intro|keyword'] = array('like',"%$key%"); 
            $list = $product_model->where($map)->relation(true)->select();
            $count      = $product_model->where($map)->count();       
            $Page       = new Page($count,20);
        	$show       = $Page->show();
        }
		else{
        	$count      = $product_model->where($where)->count();
        	$Page       = new Page($count,20);
        	$show       = $Page->show();
        	$list = $product_model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
        }
		$this->assign('page',$show);	
		$this->assign('list',$list);
		$this->display();
	}
	
	public function cate(){
		$data=M('ski_cate');
		$where=array('token'=>session('token'));
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
        $list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();		
	}
	
	public function cateadd(){
		if(IS_POST){
            $_POST['time'] = time();
			$_POST['token'] =session('token');
			$_POST['is_recommended'] = I('post.is_recommended',0,'intval');
			$this->insert('ski_cate','cate');
		}
		else{
			$this->display('cateset');
		}
	}
	
	public function catedel(){
        $id = I('get.id');
        if(IS_GET){                        
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Product_cat');
            $check=$data->where($where)->find();
            if($check==false)   $this->error('非法操作');
            $product_model=M('Product');
            $productsOfCat=$product_model->where(array('catid'=>$id))->select;
            if (count($productsOfCat)){
            	$this->error('本分类下有商品，请删除商品后再删除分类',U('Product/cats',array('token'=>session('token'))));
            }
            $back=$data->where($wehre)->delete();
            if($back==true){
            	$this->success('操作成功',U('Product/cats',array('parentid'=>$check['parentid'])));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Product/cats'));
            }
        }
	}
	
	public function cateset(){
        $id = I('get.id');
		$checkdata = M('ski_cate')->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('cateadd'));
        }
		if(IS_POST){
            $data=D('ski_cate');
			$_POST['is_recommended'] = I('post.is_recommended',0,'intval');
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				$data->time = time();
				if($data->save()){
					$this->success('修改成功',U('cate'));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$this->assign('set',$checkdata);
			$this->display("cateset");
		}
	}
	
	/**
	 *@method add 添加产品
	 */
	public function add(){
		if(IS_POST){
			$_POST['token'] = session('token');
			$_POST['time'] = time();
			$specs = $_POST['spec'];
			if(empty($specs)) $specs = array();
			$_POST['specs'] = serialize($specs);
			M('ski_product')->add($_POST);
			$this->success('添加成功！',U('index'));
		}
		else{
			$cates=M('ski_cate')->where(array('token'=>session('token')))->select();
			$this->assign('cates',$cates);
			$this->display('set');
		}
	}
	
	public function set(){
        $id = I('get.id'); 
        $product_model=M('ski_product');
        $product_cat_model=M('ski_cate');
		$checkdata = $product_model->where(array('id'=>$id))->find();
		if(empty($checkdata)){
            $this->error("没有相应记录.您现在可以添加.",U('add'));
        }
		if(IS_POST){
            $where=array('id'=>I('post.id'),'token'=>session('token'));
			$check=$product_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			$_POST['time'] = time();
			$specs = $_POST['spec'];
			if(empty($specs)) $specs = array();
			$_POST['specs'] = serialize($specs);
			if($product_model->create()){
				if($product_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Ski/index'));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($product_model->getError());
			}
		}
		else{
			$cates=M('ski_cate')->where(array('token'=>session('token')))->select();
			$this->assign('cates',$cates);
			$this->assign('set',$checkdata);
			$this->assign('specs',unserialize($checkdata['specs']));
			$this->display();	
		
		}
	}
	
	
	/**
	 *@method del 删除商品
	 */
	public function del(){
		$product_model=M('Product');
        $id = I('get.id',0,'intval');
        if(IS_GET){                              
            $where=array('id'=>$id,'token'=>session('token'));
            $check=$product_model->where($where)->find();
            if($check==false){
				$this->error('非法操作');
			}
            $back=$product_model->where($wehre)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>session('token'),'pid'=>$id,'module'=>'Product'))->delete();
                $this->success('操作成功',U('Product/index'));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Product/index'));
            }
        }
	}
	
	public function orders(){
		$orders = M('ski_order')->where(['token'=>session('token'),'order_type'=>0])->order('id desc')->select();
		$this->assign('orders',$orders);
		$this->display();
	}
	
	public function order_book(){
		$orders = M('ski_order')->where(['token'=>session('token'),'order_type'=>1])->select();
		$this->assign('orders',$orders);
		$this->display();
	}
	
	public function order_handle(){
		$id = I('get.id',0,'intval');
		$ret = M('ski_order')->where(['id'=>$id,'token'=>session('token')])->setField('is_handled',1);
		if($ret)$this->success('订单处理成功！');
		else $this->error('订单处理失败！');
	}
	
	public function order_delete(){
		$id = I('get.id',0,'intval');
		$ret = M('ski_order')->where(['id'=>$id,'token'=>session('token')])->delete();
		if($ret)$this->success('订单删除成功！');
		else $this->error('订单删除失败！');
	}
	
	public function comments(){
		$comments = M('ski_comment')->where(['token'=>session('token')])->order('time desc')->select();
		$this->assign('comments',$comments);
		$this->display();
	}
	
	public function comment_del(){
		$id = I('get.id',0,'intval');
		$ret = M('ski_comment')->where(['id'=>$id,'token'=>session('token')])->delete();
		if($ret)$this->success('评论删除成功！');
		else $this->error('评论删除失败！');
	}
	
	public function student(){
		$model = M('ski_student');
		$where = ['token'=>session('token')];
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,10);
		$list = $model->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function teacher(){
		$model = M('ski_teacher');
		$where = ['token'=>session('token')];
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,10);
		$list = $model->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function student_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['certificate_time'] = strtotime($_POST['certificate_time']);
			if($id ==0){
				$_POST['token'] = session('token');
				$id = M('ski_student')->add($_POST);
				if($id){
					$this->success('添加成功！',U('student'));
				}
				else{
					$this->error('添加失败',U('student'));
				}
			}
			else{
				$ret = M('ski_student')->save($_POST);
				if($ret){
					$this->success('保存成功！',U('student'));
				}
				else{
					$this->error('保存失败！',U('student'));
				}
			}
		}
		else{
			if($id !=0){
				$set = M('ski_student')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	public function coach(){
		$model = M('ski_coach');
		$where = ['token'=>session('token')];
		$count = $model->where($where)->count();
		$page = new \Spark\Util\Page($count,10);
		$list = $model->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$page->show());
		$this->display();
	}
	
	public function coach_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$_POST['certificate_time'] = strtotime($_POST['certificate_time']);
			$_POST['fields'] = implode(';',$_POST['fields']);
			if($id ==0){
				$_POST['token'] = session('token');
				$id = M('ski_coach')->add($_POST);
				if($id){
					$this->success('添加成功！',U('coach'));
				}
				else{
					$this->error('添加失败',U('coach'));
				}
			}
			else{
				$ret = M('ski_coach')->save($_POST);
				if($ret){
					$this->success('保存成功！',U('coach'));
				}
				else{
					$this->error('保存失败！',U('coach'));
				}
			}
		}
		else{
			if($id !=0){
				$set = M('ski_coach')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$fields = $this->getFields($set);
			$this->assign('fields',$fields);
			$this->display();
		}
	}
	
	public function getFields($set){
		$lists = M('ski_field')->where(['token'=>session('token')])->field('name')->select();
		if(!empty($set)){
			$field_selected = explode(';',$set);
			foreach($lists as $key => $value){
				if(array_search($value['name'],$field_selcted)!==FALSE) $lists[$key]['checked'] = true;
			}
		}
		return $lists;
	}
	
	public function st_delete(){
		$id = I('get.id',0,'intval');
		$ret = M('ski_student')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	public function coach_delete(){
		$id = I('get.id',0,'intval');
		$ret = M('ski_coach')->where(['token'=>session('token'),'id'=>$id])->delete();
		if($ret) $this->success('删除成功！');
		else $this->error('删除失败！');
	}
	
	/** 产品幻灯片 **/
	public function flash(){
		$db=M('ski_flash');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	
	public function flash_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id){
				$ret = M('ski_flash')->save($_POST);
			}
			else{
				$_POST['token'] = session('token');
				$ret = M('ski_flash')->add($_POST);
			}
			if($ret)$this->success('操作成功！',U('flash'));
			else $this->error('操作失败！',U('flash'));
		}
		else{
			if($id){
				$where['id']= $id;
				$res=M('ski_flash')->where($where)->find();
				$this->assign('info',$res);
				$this->assign('title', '编辑幻灯片');
			}
			else{
				$this->assign('title', '新建幻灯片');
			}
			$this->display();
		}
	}
	
	public function flash_del(){
		$where['id']=I('get.id',0,'intval');
		if(M('ski_flash')->where($where)->delete()){
			$this->success('操作成功',U('flash'));
		}
		else{
			$this->error('操作失败',U('flash'));
		}
	}
	
	/** 雪场管理 **/
	public function ski_field(){
		$db = M('ski_field');
		$where['token'] = session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function field_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			if($id){
				$ret = M('ski_field')->save($_POST);
			}
			else{
				$_POST['token'] = session('token');
				$ret = M('ski_field')->add($_POST);
			}
			if($ret)$this->success('操作成功！',U('ski_field'));
			else $this->error('操作失败！',U('ski_field'));
		}
		else{
			if($id){
				$where['id']= $id;
				$res=M('ski_field')->where($where)->find();
				$this->assign('info',$res);
				$this->assign('title', '编辑幻灯片');
			}
			else{
				$this->assign('title', '新建幻灯片');
			}
			$this->display();
		}
	}
	
	public function field_del(){
		$where['id']=I('get.id',0,'intval');
		if(M('ski_field')->where($where)->delete()){
			$this->success('操作成功',U('ski_field'));
		}
		else{
			$this->error('操作失败',U('ski_field'));
		}
	}
	
	/*视频分类*/
	public function video_cate(){
		$list = M('ski_video_cate')->where(['token'=>session('token')])->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function video_cate_del(){
        $id = I('get.id');
        if(IS_GET){                        
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('ski_video_cate');
            $check=$data->where($where)->find();
            if($check==false)$this->error('非法操作');
            $back=$data->where($where)->delete();
            if($back==true){
            	$this->success('操作成功！',U('video_cate'));
            }else{
                $this->error('操作失败！',U('video_cate'));
            }
        }
	}
	
	public function video_cate_set(){
        $id = I('get.id');
		if(IS_POST){
            $data=M('ski_video_cate');
			$_POST['time'] = time();
            if($id){
				$ret = $data->save($_POST);
			}
			else{
				$_POST['token'] = session('token');
				$ret = $data->add($_POST);
			}
			if($ret)$this->success('操作成功！',U('video_cate'));
			else $this->error($data->getError(),U('video_cate'));
		}
		else{
			if($id){
				$set = M('ski_video_cate')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			$this->display();
		}
	}
	
	/**视频 **/
	public function video(){
		$list = D('ski_video')->where(['token'=>session('token')])->relation(true)->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function video_set(){
		$id = I('get.id');
		if(IS_POST){
            $data=M('ski_video');
			$_POST['time'] = time();
            if($id){
				$ret = $data->save($_POST);
			}
			else{
				$_POST['token'] = session('token');
				$ret = $data->add($_POST);
			}
			if($ret)$this->success('操作成功！',U('video'));
			else $this->error($data->getError(),U('video'));
		}
		else{
			$cate = M('ski_video_cate')->where(['token'=>session('token')])->field('id,cate_name as name')->select();
			$this->assign('cates',$cate);
			if($id){
				$set = M('ski_video')->where(['token'=>session('token'),'id'=>$id])->find();
				$this->assign('set',$set);
			}
			
			$this->display();
		}
	}
	
	public function video_del(){
		$where['id'] = I('get.id',0,'intval');
		if(M('ski_video')->where($where)->delete()){
			$this->success('操作成功',U('video'));
		}
		else{
			$this->error('操作失败',U('video'));
		}
	}
	
	public function student_import(){
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
		$model = M('ski_student');
		$model->where(['token'=>$token])->delete();
		foreach($data as $row){
			$student = array();
			$student['token'] = $token;
			$student['name'] = $row[0];
			$student['level'] = $row[1];
			$student['certificate_time'] = strtotime($row[2]);
			$student['card_number'] = $row[3];
			$student['mobile'] = $row[4];
			$model->add($student);
		}
		$this->ajaxReturn(['errcode'=>0,'total'=>count($data)]);
	}
	
	public function teacher_import(){
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
		$model = M('ski_teacher');
		$model->where(['token'=>$token])->delete();
		foreach($data as $row){
			$student = array();
			$student['token'] = $token;
			$student['name'] = $row[0];
			$student['level'] = $row[1];
			$student['certificate_time'] = strtotime($row[2]);
			$student['card_number'] = $row[3];
			$student['mobile'] = $row[4];
			$model->add($student);
		}
		$this->ajaxReturn(['errcode'=>0,'total'=>count($data)]);
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