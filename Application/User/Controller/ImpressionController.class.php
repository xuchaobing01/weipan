<?php
/**
 *@ class EstateController 印象墙模块控制器
 */
namespace User\Controller;
use Spark\Util\Page;
class ImpressionController extends UserController{
	public function _initialize(){
		parent::_initialize();
		$this->reply_info_model=M('reply_info');
		$this->token=session('token');
	}
	
	public function index(){
		$t_impress = M('impression'); 
        $impress = $t_impress->where(array('token'=>session('token')))->order('sort desc')->select();
        $this->assign('impress',$impress);
		$this->display();
	}
	
    public function impress(){
        $t_impress = M('impression'); 
        $impress = $t_impress->where(array('token'=>session('token')))->order('sort desc')->select();
        $this->assign('impress',$impress);
        $this->display();
    }
	
    public function add(){
        $t_impress = M('impression');
        if(IS_POST){
            $_POST['token'] = session('token');
            if($t_impress->add($_POST)){                     
				$this->success('添加成功',U('index'));
				exit;
			}
			else{
				$this->error('服务器繁忙,请稍候再试');exit;
			}
        }
		$this->assign('title','添加印象');
        $this->display('edit');
    }
	
	public function edit(){
        $t_impress = M('impression');
        $id = I("get.id");
        $where =  array('id'=>$id);
        $check = $t_impress->where($where)->find();
        if($check != null){
            $this->assign('impress',$check);
        }
        if(IS_POST){
            $wh = array('id'=>I('post.id')); 
			if($t_impress->where($wh)->save($_POST)){ 
				$this->success('修改成功',U('index'));
				exit;
			}else{
				$this->error('操作失败');exit;
			}
        }
		$this->assign('title','编辑印象');
        $this->display();
    }
	
    public function del(){
        $impress = M('impression');
        $id = I('get.id');
        $where = array('id'=>$id);
        $check = $impress->where($where)->find();
        if($check == null){
            $this->error('操作失败');
        }else{
            $isok = $impress->where($where)->delete();
            if($isok){
               $this->success('删除成功',U('index'));
			}
			else{
                $this->error('删除失败',U('index'));
			}
        }
    }
	
	public function config(){
		$infotype = 'Impression';
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$id = I('post.id','','intval');
			D('ReplyInfo')->set($infotype,$data);
			D('Keyword')->set($id,$infotype,['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$setting = D('ReplyInfo')->get($infotype);
			$keyword = D('Keyword')->get($setting['id'],$infotype);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display();
		}
	}
}
?>