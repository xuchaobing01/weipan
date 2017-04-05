<?php
/**
 *@ class PowerController 微助力控制类
 */
namespace User\Controller;
use Spark\Util\Page;
class SunController extends UserController{
	public $token;
	public $wechat_id;	
	public function _initialize(){
		parent::_initialize();	
		$this->token=session('token');	
		$this->assign('token',$this->token);
	}
		
	public function set(){//首页微信扫描配置
		$data=M('Power_activity');
		$where=array('token'=>session('token'),'type'=>1);
		
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
		
		
	}
	public function  activity(){//助力活动列表
		$data=M('Power_activity');
		$where=array('token'=>session('token'));
		$count      = $data->where($where)->count();
		$Page       = new Page($count,20);
		$show       = $Page->show();
		$list = $data->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	public function addactivity (){
		if(IS_POST){	
			$data = M('Power_activity');
			$_POST['token']=session('token');
			$_POST['type']=1;
			$_POST['name'] = I("post.name");
			$_POST['des'] = strip_tags(I("post.info"));
			$_POST['createtime']=time();
			$_POST['starttime'] = strtotime(I('post.startdate'));
			$_POST['endtime'] = strtotime(I('post.enddate'));
			$pattern = "/<(\/?)(script|style|html|body|title|marquee|link|meta|\?|\%)([^>]*?)>/isU";
			$_POST['content'] = preg_replace($pattern,"",$_POST['content']);
					
			$_POST['title'] = I("post.title");			
			$_POST['banner'] = I("post.picurl");
			$_POST['banner2'] = I("post.banner2");
			
			$_POST['rename1'] = I("rename1");
			$_POST['rename2'] = I("rename2");
			$_POST['rename3'] = I("rename3");
			$_POST['rename4'] = I("rename4");
			
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");
			
			$_POST['musicurl'] = I("post.musicurl");			
			$_POST['ppicurl'] = I("post.ppicurl");	
			$_POST['online_sign'] = I("online_sign");

			if($_POST['endtime']<$_POST['starttime']){
				$this->error('结束时间不能小于开始时间!');
			}			
			if($data->create()!=false){
				if($id=$data->add()){
					$this->success('添加成功',U('Sun/set',array('token'=>session('token'))));
				}
				else{
					$this->error('服务器繁忙,请稍候再试');
				}
			}
			else{
				$this->error($data->getError());
			}
		}
		else{
			$this->display();
		}				
	}
	public function edit(){
		if(IS_POST){			
			$data=M('Power_activity');
			$_POST['id'] = (int)I('get.id');
			$_POST['name'] = I("post.name");
			$_POST['des'] = strip_tags(I("post.info"));
			$_POST['createtime']=time();
	
			$_POST['starttime'] = strtotime($_POST['startdate']);
			$_POST['endtime'] = strtotime($_POST['enddate']);
			$_POST['content'] = I("content");
					
			$_POST['title'] = I("post.title");			
			$_POST['banner'] = I("post.picurl");
			$_POST['banner2'] = I("post.banner2");
			
			$_POST['rename1'] = I("rename1");
			$_POST['rename2'] = I("rename2");
			$_POST['rename3'] = I("rename3");
			$_POST['rename4'] = I("rename4");
			
			$_POST['share_title'] = I("share_title");
			$_POST['share_detail'] = I("share_detail");
			$_POST['share_img'] = I("share_img");
			
			$_POST['musicurl'] = I("post.musicurl");			
			$_POST['ppicurl'] = I("post.ppicurl");	
			$_POST['online_sign'] = I("online_sign");
			$_POST['cert_sign'] = I("post.cert_sign");
			
			if($_POST['endtime']<$_POST['starttime']){
				$this->error('结束时间不能小于开始时间!');
			}
			$where=array('id'=>$_POST['id'],'token'=>session('token'));
			$check=$data->where($where)->find();
			if($check==NULL) exit($this->error('非法操作'));
			
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$this->success('修改成功!',U('Sun/set',array('token'=>session('token'))));exit;
				}
				else{
					$this->success('修改成功',U('Sun/set',array('token'=>session('token'))));exit;
				}
			}
			else{
				$this->error($data->getError());
			}
		}else{
			$id=(int)I('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('power_activity');
			$check=$data->where($where)->find();
			if($check==NULL)$this->error('非法操作');
			$vo=$data->where($where)->find();
			$this->assign('vo',$vo);
			$this->display('addactivity');
		}
	}
	public function activitydel(){
		$id = I('get.id');
		if(IS_GET){
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('power_activity');
			$check=$data->where($where)->find();
			if($check==false)   $this->error('非法操作');
			$r=$data->where($where)->delete();
			if($r==true){
				$this->success('操作成功',U('Sun/set'));	
			}
			else{
				$this->error('服务器繁忙,请稍后再试',U('Sun/activity'));
			}
		}	
	}
	public function del(){//单项删除
		$leave_model = M("power_img");
		$id = $_GET['id'];
		$res = $leave_model->delete($id);
		if($res){
			$this->success("删除成功",U('User/Sun/activityimg',array('wecha_id'=>$this->wecha_id,'token'=>$this->token)));
		}else{
			$this->success("删除失败",U('User/Sun/activityimg',array('wecha_id'=>$this->wecha_id,'token'=>$this->token)));
		}
	}
	
	
	public function message(){
		$leave_model =M("power_message");
		import("ORG.Util.Page"); // 导入分页类
		$cid= $_GET['id'];
		$where = array('token'=>$this->token);
		$where = array('pid'=>0,'cid'=>$cid);
		$count      = $leave_model->where($where)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$res = $leave_model->where($where)->order('istop DESC,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($res as $key=>$val){
			$reply_model = M("power_message");
			$where = array("pid"=>$val['id']);
			$res[$key]['count'] = $reply_model->where($where)->count();//全部回复数量
			$where = array("pid"=>$val['id'],"checked"=>0);
			$res[$key]['chkcount'] = $reply_model->where($where)->count();//新回复数量
		}
		$this->assign('cid',$cid);
		$this->assign('res',$res);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
	}
	public function addmessage(){
		if(IS_POST){
			$leave_model =M("power_message");
			$message=$_POST['content'];
			$id= $_POST['id'];
			$check=M('power_activity')->where(array('token'=>$this->token,'id'=>$id))->field('online_sign')->find()['online_sign'];
			$msgarr = array();
			$msgarr['checked'] = 1;
			$msgarr['message'] = $message;
			$msgarr['wechat_id'] = '';
			$msgarr['token']=$this->token;
			$msgarr['time'] =time();
	
			$msgarr['cid']=$id;
			$msgarr['pid']= isset($_POST['pid'])?$_POST['pid']:0;
				
			$msgarr['name'] =$_POST['name'];
			$msgarr['pic'] =$_POST['ppicurl'];
			//根据token 来确定同一用户60秒以后才能留言
			$lasttime = $leave_model->where(array("token"=>$this->token))->getField("max(time)");//获得准备数据 是否与数据库中数据留言是同一人
			$timeres = time() - $lasttime;
			$res = $leave_model->add($msgarr);
	
			if($res){
				$msgarr['id']=$res;
				$this->success('留言成功',U('Sun/message',array('id'=>$id)));
			}else{
	
				$this->success('留言失败',U('Sun/message',array('id'=>$id)));
			}
		}else{
			$id=$_GET['id'];
			$this->assign('id',$id);
			$this->display();
		}
	
	}
	public function tongji(){//统计
		
		list($date_start,$date_end) = $this->_date_range();
		$data = $this->_init_data($date_start,$date_end);
		$where = ['token'=>$this->token];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end + 86400];
		$where['pid']=$_GET['id'];
		$list = M('power_tongji')->where($where)->order('id desc')->select();
		$this->assign('list',$list);
		foreach($list as $item){
			$key = $item['month'].$item['day'];
			$data['follow'][$key] = intval($item['num']);
			
		}		
		$followNum = json_encode_array($data['follow']);
		$this->assign('followNum',$followNum);
		$this->assign('name',json_encode_array($data['name']));
		$this->assign('id',$where['pid']);
		$this->display();
		
	}
	public function export(){
		Vendor('PHPExcel.PHPExcel');
		$data = array();
		$title = ['日期','参与人数'];
		$data[] = $title;
		list($date_start,$date_end) = $this->_date_range();
		$where = ['token'=>session('token')];
		$where['time'][] = ['egt',$date_start];
		$where['time'][] = ['elt',$date_end];
		$where['pid']=$_GET['id'];
		$list = M('power_tongji')->where($where)->field('time,num')->order('id desc')->select();
		foreach($list as $item){
			$row = array();
			$row[] = date('Y-m-d',$item['time']);
			$row[] = $item['num'];
			$data[] = $row;
		}
		createExcel('',$data);
	}
	private function _date_range(){
		$daterange = I('daterange');
		if(empty($daterange)) $daterange = 7;
		if(strpos($daterange,'~') > 0){
			$parse = explode('~',$daterange);
			$date_start = strtotime($parse[0]);
			$date_end = strtotime($parse[1]);
		}
		else{
			$today = date('Y-m-d');
			$date_end = strtotime($today) - 86400; //从昨天算起
			$date_start = $date_end - (intval($daterange)-1)*86400;
		}
		$this->assign('range',$daterange);
		return [$date_start,$date_end];
	}
	private function _init_data($start,$end){
		$data = array();
		for($tmp = $start;$tmp<=$end;$tmp+=86400){
			$key = date('nj',$tmp);
			$data['name'][$key] = date('m-d',$tmp);
			$data['text'][$key] = 0;
			$data['img'][$key] = 0;
			$data['menu'][$key] = 0;
			$data['follow'][$key] = 0;
			$data['unfollow'][$key] = 0;
			$data['truefollow'][$key] = 0;
		}
		return $data;
	}
	public function changetop(){//置顶
		$leave_model = M("power_message");
		$id = $_GET['chk_value'];
		$cid= $_GET['cid'];
		$istop = $leave_model->where(array("id"=>intval($id)))->getField("istop");
	
		if($istop == $_GET['istop']){
			$this->error("重复操作",U('User/Sun/message',array('id'=>$cid)));
			//$this->success("已审核",U('User/Reply/index'));
		}
		else{
			$res = $leave_model->where(array("id"=>$id))->setField("istop",$_GET['istop']);
			if($res){
				if($_GET['istop']==1)
					$this->success("取消置顶成功",U('User/Sun/message',array('id'=>$cid)));
				else 
					$this->success("置顶成功",U('User/Sun/message',array('id'=>$cid)));
			}
			else{
				$this->error("置顶失败",U('User/Sun/message',array('id'=>$cid)));
			}
		}
		
	}
	public function replyedit(){
		$model =M("power_message");
		if(IS_POST){
			$id= $_POST['id'];
			$cid= $_POST['cid'];
			$message =$_POST['content'];
			$res = $model ->where(array("id"=>$id))->setField("message",$message);
			if($res){
				$this->success("编辑成功",U('User/Sun/message',array('id'=>$cid)));
			}else{
				$this->success("编辑失败",U('User/Sun/message',array('id'=>$cid)));
			}
		}else{
			
			$id= I('get.chk_value');
			$message = $model->where(array('id'=>$id))->find();
			$this->assign('list',$message);
			$this->display();	
		}
		
		
	}
	
	
	public function reply(){
		$reply_model =M("power_message");
		$id= I('get.msgid');
		$leave_model =M("power_message");
		$message = $leave_model->where(array('id'=>$id))->getField('message');
		$this->assign("message_id",$id);
		$this->assign('message',$message);
		$this->assign('cid',$_GET['cid']);
		$where = array("pid"=>$id);
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
	public function checkOne(){//单项审核
		$leave_model = M("power_message");
		$id = $_GET['chk_value'];
		$cid= $_GET['cid'];
		$checked = $leave_model->where(array("id"=>intval($id)))->getField("checked");
	
		if($checked == 1){
			//$this->success("已审核",U('User/Reply/index'));
		}
		else{
			$res = $leave_model->where(array("id"=>$id))->setField("checked",1);
			if($res){
				$this->success("审核成功",U('User/Sun/message',array('id'=>$cid)));
			}
			else{
				$this->error("审核失败",U('User/Sun/message',array('id'=>$cid)));
			}
		}
	}
	public function deled(){//单项删除
		$cid= $_GET['cid'];
		$leave_model = M("power_message");
		$id = $_GET['chk_value'];
		$res = $leave_model->delete($id);
		if($res){
			$this->success("删除成功",U('User/Sun/message',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}else{
			$this->success("删除失败",U('User/Sun/message',array('wecha_id'=>$this->wecha_id,'token'=>$this->token,'id'=>$cid)));
		}
	}
	/**
	 * @管理员回复
	 */
	public function addreply(){
		$reply_model = M("power_message");
		$content = I('post.content');
		$data['name'] = I('post.name');
		$data['wechat_id']='';
		$data['checked'] = 1;
		$data['differ'] = 1;
		$data['pid']=I('get.message_id');
		$data['message'] =$content;
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
		$reply_model = M("power_message");
		$content = I('post.content');
	
		$checked =I('post.checked');
		$message_id =I('post.chk');
		$id = explode(",",$message_id);
		$result = array();
		foreach($id as $val){
			$data['wecha_id']=$this->wecha_id;
			$data['differ'] =$this->differ;
			$data['checked'] =$checked;
			$data['pid']=$val;
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
			$this->error("回复失败",U('User/Sun/message',array('token'=>$this->token)));
		}
		else{
			$this->success("回复成功",U('User/Sun/message',array('token'=>$this->token)));
		}
	}
	public function replyChked(){//回复内容单项审核
		$reply_model = M("power_message");
		$msgid = I('get.msgid');
		$id = I('get.id');
		$checked = $reply_model->where(array("id"=>intval($id)))->getField("checked");
		if($checked == 1){
			$this->success("已审核",U('User/Sun/message',array('msgid'=>$msgid)));
		}
		else{
			$res = $reply_model->where(array("id"=>$id))->setField("checked",1);
			if($res){
				$this->success("审核成功",U('User/Sun/reply',array('msgid'=>$msgid)));
			}
			else{
				$this->error("审核失败",U('User/Sun/reply',array('msgid'=>$msgid)));
			}
		}
	}
	public function replyDeled(){//回复内容单项删除
		$reply_model = M("power_message");
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