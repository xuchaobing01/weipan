<?php
namespace User\Controller;
class PanoramaController extends UserController{
	public $panorama_model;
	public function _initialize() {
		parent::_initialize();
		$this->token = session('token');
		$this->panorama_model=M('Panorama');
	}
	
	public function index(){
		if (IS_POST){
			$data['title']=$_POST['title'];
			$data['picurl']=$_POST['picurl'];
			$data['info']=$_POST['info'];
			$id = I('post.id','','intval');
			D('ReplyInfo')->set('Panorama',$data);
			
			D('Keyword')->set($id,'Panorama',['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$where=array('token'=>$this->token);
			$list=$this->panorama_model->where($where)->order('taxis ASC')->select();
			$setting = D('ReplyInfo')->get('Panorama');
			
			$keyword = D('Keyword')->get($setting['id'],'Panorama');
			$this->assign('list',$list);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display();
		}
	}
	
	public function add(){
		if(IS_POST){
			$this->all_insert('Panorama','index');
		}
		else{
			$set=array();
			$baseUrl=RES.'/images/panorama/';
			$set['frontpic']=$baseUrl.'0.jpg';
			$set['rightpic']=$baseUrl.'1.jpg';
			$set['backpic']=$baseUrl.'2.jpg';
			$set['leftpic']=$baseUrl.'3.jpg';
			$set['toppic']=$baseUrl.'4.jpg';
			$set['bottompic']=$baseUrl.'5.jpg';
			$this->assign('set',$set);
			
			$this->display('set');
		}
	}
	
	public function set(){
        $id = intval(I('get.id'));
		$checkdata = $this->panorama_model->where(array('id'=>$id))->find();
		if(empty($checkdata)||$checkdata['token']!=$this->token){
            $this->error("没有相应记录.您现在可以添加.",U('Panorama/add'));
        }
		if(IS_POST){
            $where=array('id'=>I('post.id'),'token'=>$this->token);
			$check=$this->panorama_model->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($this->panorama_model->create()){
				if($this->panorama_model->where($where)->save($_POST)){
					$this->success('修改成功',U('Panorama/index',array('token'=>$this->token)));
					$keyword_model=M('Keyword');
					$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Panorama'))->save(array('keyword'=>$_POST['keyword']));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($this->panorama_model->getError());
			}
		}else{
			$this->assign('set',$checkdata);
			$this->display();
		}
	}
	
	public function delete(){
        $id = intval(I('get.id'));
        if(IS_GET){                      
            $where=array('id'=>$id,'token'=>$this->token);
            $check=$this->panorama_model->where($where)->find();
            if($check==false){$this->error('非法操作');}
            $back=$this->panorama_model->where($wehre)->delete();
            if($back==true){
            	$keyword_model=M('Keyword');
            	$keyword_model->where(array('token'=>$this->token,'pid'=>$id,'module'=>'Panorama'))->delete();
                $this->success('操作成功',U('Panorama/index',array('token'=>$this->token)));
            }else{
                $this->error('服务器繁忙,请稍后再试',U('Panorama/index',array('token'=>$this->token)));
            }
        }   
	}
}
?>