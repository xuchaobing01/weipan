<?php
namespace User\Controller;
use Spark\Util\Page;
class ReplyController extends UserController{
	public $token;
	public $needCheck;//设置是否需要审核
	public $differ;//区分是网友回复还是管理员回复
	
	public function _initialize(){
		parent::_initialize();
		$this->reply_info_model=M('reply_info');
		$this->token=session('token');
		//获取配置
		$thisInfoConfig = $this->reply_info_model->where(array('infotype'=>'message','token'=>$this->token))->find();
		
		$detailConfig=unserialize($thisInfoConfig['config']);
		$this->needCheck=intval($detailConfig['needcheck']);
		
		$this->differ=1;
		
		$this->assign('token',$this->token);
		$this->assign('needCheck',$this->needCheck);
	}
	
	public function config(){
		$infotype = 'Message';
		$thisInfo = $this->reply_info_model->where(array('infotype'=>$infotype,'token'=>$this->token))->find();
		if ($thisInfo&&$thisInfo['token']!=$this->token){
			exit();
		}
		if(IS_POST){
			$row['title'] = I('post.title');
			$row['info'] = I('post.info');
			$row['picurl'] = I('post.picurl');
			$row['token'] = $this->token;
			$row['infotype'] = $infotype;
			$row['config'] = serialize(array('needcheck'=>intval($_POST['needcheck']),'banner_url'=>$_POST['banner_url']));
			$id = I('post.id','','intval');
			D('ReplyInfo')->set($infotype,$row);
			D('Keyword')->set($id,$infotype,['keyword'=>$_POST['keyword']]);
			$this->success('保存成功！');
		}
		else{
			$setting = D('ReplyInfo')->get($infotype);
			$config=unserialize($setting['config']);//获取自定义配置
			$setting['needcheck']=$config['needcheck'];
			$setting['banner_url']=$config['banner_url'];
			$keyword = D('Keyword')->get($setting['id'],$infotype);
			$this->assign('set',$setting);
			$this->assign('keyword',$keyword);
			$this->display();
		}
	}
	
	public function index(){
		$leave_model =M("leave");
		import("ORG.Util.Page"); // 导入分页类
		$where = array('token'=>$this->token);
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($res as $key=>$val){
			$reply_model = M("reply");
			$where = array("message_id"=>$val['id']);
			$res[$key]['count'] = $reply_model->where($where)->count();//全部回复数量
			$where = array("message_id"=>$val['id'],"checked"=>0);
			$res[$key]['chkcount'] = $reply_model->where($where)->count();//新回复数量
		}
		//var_dump($res);exit;
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	
	public function reply(){
		$reply_model =M("reply");
		$id= I('get.msgid');
		$leave_model =M("leave");
		$message = $leave_model->where(array('id'=>$id))->getField('message');
		$this->assign("message_id",$id);
		$this->assign('message',$message);
		$where = array("message_id"=>$id);
		import('ORG.Util.Page');// 导入分页类
		$count      = $reply_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res= $reply_model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		if($res){
			$this->assign("res",$res);
		}else{
			$this->assign("res",0);
		}
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	
	public function checkMany(){//多项审核
		$leave_model = M("leave");
		$id = $_GET['chk_value'];
		$id = explode(",",$id);
		$result =array();
		foreach($id as $val){
			$res = $leave_model->where(array("id"=>intval($val)))->setField("checked",$this->needCheck);
			if($res){
				$result = 1;
			}else{
				$result = 0;
			}
		}
		if(in_array("0",$result)){
			echo "审核失败";
		}else{
			echo "审核成功";
		}
	}
	
	public function checkOne(){//单项审核
		$leave_model = M("leave");
		$id = $_GET['chk_value'];
		
		$checked = $leave_model->where(array("id"=>intval($id)))->getField("checked");
		
		if($checked == 1){
			//$this->success("已审核",U('User/Reply/index'));
		}
		else{
			$res = $leave_model->where(array("id"=>$id))->setField("checked",1);
			if($res){
				$this->success("审核成功",U('User/Reply/index'));
			}
			else{
				$this->error("审核失败",U('User/Reply/index'));
			}
		}
	}
	
	public function del(){//多项删除
		$leave_model = M("leave");
		$id = $_GET['chk_value'];
		$res = $leave_model->delete($id);
		if($res){
			echo "删除成功";
			exit;
		}else{
			echo "删除失败";
			exit;
		}
	}
	public function deled(){//单项删除
		$leave_model = M("leave");
		$id = $_GET['chk_value'];
		$res = $leave_model->delete($id);
		if($res){
			$this->success("删除成功",U('User/Reply/index',array('wecha_id'=>$this->wecha_id,'token'=>$this->token)));
		}else{
			$this->success("删除失败",U('User/Reply/index',array('wecha_id'=>$this->wecha_id,'token'=>$this->token)));
		}
	}
	
	/**
	 * @管理员回复
	 */
	public function addreply(){
		$reply_model = M("reply");
		$content = I('post.content');
		$data['wechat_id']='';
		$data['checked'] = 1;
		$data['differ'] = 1;
		$data['message_id']=I('get.message_id');
		$data['reply'] =$content;
		$data['time']=time();
		$res = $reply_model->add($data);
		if($res){
			$this->success("回复成功") ;
		}
		else{
			$this->error("回复失败") ;
		}
	}
	
	public function add(){//获得回复数据
		$reply_model = M("reply");
		$chk_value =I('get.chk_value');
		$checked =I('get.checked');
		$this->assign("chk_value",$chk_value);
		$this->assign("checked",$checked);
		$this->display();
	}
	
	public function insert(){//添加回复 插入数据库
		$reply_model = M("reply");
		$content = I('post.content');

		$checked =I('post.checked');
		$message_id =I('post.chk');
		$id = explode(",",$message_id);
		$result = array();
		foreach($id as $val){
			$data['wecha_id']=$this->wecha_id;
			$data['differ'] =$this->differ;
			$data['checked'] =$checked;
			$data['message_id']=$val;
			$data['reply'] =$content;
			$data['time']=time();
			$res = $reply_model->add($data);
			if($res){
				$result[]= 1;
			}
			else{
				$result[]= 0;
			}
		}
		if(in_array("0",$result)){
			$this->error("回复失败",U('User/Reply/index',array('token'=>$this->token)));
		}
		else{
			$this->success("回复成功",U('User/Reply/index',array('token'=>$this->token)));
		}
	}
	
	public function replyChk(){//回复内容多项审核
		$reply_model = M("reply");
		$id = I('get.chk_value');
		$id = explode(",",$id);
		$result =array();
		foreach($id as $val){
			$res = $reply_model->where(array("id"=>intval($val)))->setField("checked",1);
			if($res){
				$result = 1;
			}
			else{
				$result = 0;
			}
		}
		if(in_array("0",$result)){
			echo "审核失败";
		}
		else{
			echo "审核成功";
		}
	}
	
	public function replyChked(){//回复内容单项审核
		$reply_model = M("reply");
		$msgid = I('get.msgid');
		$id = I('get.id');
		$checked = $reply_model->where(array("id"=>intval($id)))->getField("checked");
		if($checked == 1){
			$this->success("已审核",U('User/Reply/index',array('msgid'=>$msgid)));
		}
		else{
			$res = $reply_model->where(array("id"=>$id))->setField("checked",1);
			if($res){
				$this->success("审核成功",U('User/Reply/reply',array('msgid'=>$msgid)));
			}
			else{
				$this->error("审核失败",U('User/Reply/reply',array('msgid'=>$msgid)));
			}
		}
	}
	
	public function replyDel(){//回复内容多项删除
		$reply_model = M("reply");
		$id = I('get.chk_value');
		$res = $reply_model->delete($id);
		if($res){
			echo "删除成功";
			exit;
		}else{
			echo "删除失败";
			exit;
		}
	}
	public function replyDeled(){//回复内容单项删除
		$reply_model = M("reply");
		$id = I('get.id');
		$res = $reply_model->delete($id);
		if($res){
			$this->success("删除成功");
		}else{
			$this->success("删除失败");
		}
	}
}
?>