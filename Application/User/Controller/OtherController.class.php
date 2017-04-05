<?php
/**
 *@默认回复设置
 */
namespace User\Controller;
class OtherController extends UserController{
	public function index(){
		$other=M('Other')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			$_POST['token'] = session('token');
			if($other==false){				
				$this->all_insert('Other','index');
			}
			else{
				$_POST['id']=$other['id'];
				$this->all_save('Other','index');
			}
		}
		else{
			$this->assign('other',$other);
			$this->display();
		}
	}
}



?>