<?php
/**
 *@关注回复
 **/
namespace User\Controller;
class AreplyController extends UserController{
	public function index(){
		$areply=M('Areply')->where(array('token'=>session('token')))->find();
        $_POST['token'] = $_SESSION['token'];
		if(IS_POST){
			if($areply==false){				
				$this->all_insert('Areply','index');
			}else{
				$_POST['id']=$areply['id'];
				$this->all_save('Areply','index');				
			}
		}else{
			$this->assign('areply',$areply);
			$this->display();
		}
	}
	
	public function insert(){
		C('TOKEN_ON',false);
		$db=D('Areply');
		$ret = $db->set(I('post.keyword'));
		$this->success('设置成功',U('Areply/index'));
	}
}
?>