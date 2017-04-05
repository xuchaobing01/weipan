<?php
namespace User\Controller;
use Spark\Util\Page;
class ArticleController extends UserController{
	public function index(){
		$db=D('Article');
		$where['token']=session('token');
		$cates = M('classify')->field('id,name')->where($where)->select();
		$this->assign('cates',$cates);
		$cate = I('get.cate',0,'intval');
		if($cate != 0){
			$where['category'] = $cate;
		}
        $find=I('get.find',"");
        if($find!=""){
            $where['_string']="title like '%{$find}%' or `summary` like '%{$find}%' or content like '%{$find}%'";
        }
		$count=$db->where($where)->count();
		$page=new Page($count,20);
		$articles=$db->where($where)->order('sort desc,id desc')->limit($page->firstRow.','.$page->listRows)->relation(true)->select();
		$this->assign('page',$page->show());
		
		$this->assign('articles', $articles);
		$this->display();
	}
	
	public function edit(){
		$id=I('get.id',0,'intval');
		if(IS_POST){
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
			if($id){
				$this->update_all('Article','index?cate='.$_GET['cate']);
			}
			else{
				$this->insert('Article','index?cate='.$_GET['cate']);
			}
		}
		else{
			if($id){
				$article=M('Article')->find($id);
				$this->assign('article',$article);
				$this->assign('title','编辑文章');
			}
			else{
				$this->assign('title','新建文章');
			}
			$cates = M('Classify')->field('id,name')->where(['token'=>session('token')])->order('sorts desc')->select();
			$this->assign('categories',$cates);
			$this->display();
		}
	}
	
	public function delete(){
		$where['id']=I('get.id',0,'intval');
		$where['uid']=session('uid');
		if(D('Article')->where($where)->delete()){
			$this->success('操作成功',U('index?cate='.$_GET['cate']));
		}
		else{
			$this->error('操作失败',U('index'.$_GET['cate']));
		}
	}
}
?>