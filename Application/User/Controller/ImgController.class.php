<?php
/**
 *图文回复
**/
namespace User\Controller;
use Spark\Util\Page;
class ImgController extends UserController{
	public function index(){
		$db=D('Img');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	public function edit(){
		$id = I('get.id',0,'intval');
		if($id){
			$where['id']= $id;
			$where['token']=session('token');
			$res=M('Img')->where($where)->find();
			$this->assign('info',$res);
			$this->assign('title','编辑图文回复');
		}
		else{
			$this->assign('title','新建图文回复');
		}
		$this->display();
	}
	
	public function del(){
		$id = I('get.id',0,'intval');
		$where['id'] = $id;
		$where['token']=session('token');
		if(D('Img')->where($where)->delete()){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Img'))->delete();
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
	
	public function insert(){
		$pat = "/<(\/?)(script|i?frame|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
		$_POST['info'] = preg_replace($pat,"",$_POST['info']);
		$this->all_insert();
	}
	
	public function upsave(){
		$this->all_save();
	}
	
	public function multi_img(){
		$list = M('Img')->where(['token'=>session('token'),'is_multi'=>1])->order('id desc')->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function multi_img_edit(){
		$id = I('get.id',0,'intval');
		if(IS_POST){
			$pat = "/<(\/?)(script|i?frame|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$data['keyword'] = $_POST['keyword'];
			$data['title'] = $_POST['title'];
			$data['pic'] = $_POST['pic'];
			$data['url'] = $_POST['url'];
			$data['info'] = preg_replace($pat,"",$_POST['info']);
			$titles = $_POST['titles'];
			$covers = $_POST['covers'];
			$urls = $_POST['urls'];
			$details = $_POST['details'];
			$more_news = array();
			foreach($titles as $key =>$value){
				$more_news[] = array('title'=>$titles[$key],'cover'=>$covers[$key],'url'=>$urls[$key],'detail'=>preg_replace($pat,"",$details[$key]));
			}
			
			$data['more_news'] = serialize($more_news);
			
			$model = D('Img');
			if(empty($id)){
				$data['is_multi'] = 1;
				$model->create($data);
				$id = $model->add();
				if($id){
					$this->success('添加成功！',U('multi_img'));
				}
				else $this->error('添加失败！');
			}
			else{
				$data['id'] = $id;
				$model->create($data);
				$ret = $model->save();
				if($ret){
					$this->success('更新成功！',U('multi_img'));
				}
				else $this->error('更新失败！');
			}
		}
		else{
			if(empty($id)){
				$info = array('title'=>'title','pic'=>'/Public/Wap/images/common/noneimg.jpg');
				$smallNews = array(['title'=>'title','cover'=>'/Public/Wap/images/common/noneimg.jpg','url'=>'','detail'=>'']);
			}
			else{
				$info = M('Img')->where(['id'=>$id,'token'=>session('token')])->find();
				$smallNews = unserialize($info['more_news']);
			}
			$this->assign('info',$info);
			$this->assign('smallNews',$smallNews);
			$this->display();
		}
	}
}
?>