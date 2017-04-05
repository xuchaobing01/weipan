<?php
namespace User\Model;
use Think\Model;
class WxuserModel extends Model{
	protected $_validate =array(
		array('wxname','require','公众号名称不能为空',1),
		array('wxid','require','公众号原始id不能为空',1),
		array('weixin','require','微信号不能为空',1),
		array('headerpic','require','头像地址不能为空',1),
		array('token','','token已经存在！',1,'unique',1),
		array('province','require','省份不能为空',1),
		array('city','require','市级不能为空',1),
		array('email','email','公众号邮箱格式不正确'),
		array('wxfans','number','微信粉丝格式不正确'),
	);
	
	protected $_auto = array (
		array('uid','getuser',self::MODEL_INSERT,'callback'),
		array('uname','getname',self::MODEL_INSERT,'callback'),
		array('tpltypeid','1',self::MODEL_INSERT),
		array('tpllistid','1',self::MODEL_INSERT),
		array('tplcontentid','1',self::MODEL_INSERT),
		array('createtime','time',self::MODEL_INSERT,'function'),
		array('updatetime','time',self::MODEL_BOTH,'function'),
		array('typeid','gettypeid',self::MODEL_BOTH,'callback'),
		array('token','createToken',self::MODEL_INSERT, 'callback')
	);
	
	public function chekWechatCardNums(){
		$data=M('User_group')->field('wechat_card_num')->where(array('id'=>session('gid')))->find();
		$users=M('Users')->field('wechat_card_num')->where(array('id'=>session('uid')))->find();
		if($users['wechat_card_num']<$data['wechat_card_num']){
			return 0;
		}
		else{
			return 1;
		}
	}
	
	public function getuser(){
		return session('uid');
	}
	
	public function getname(){
		return session('uname');
	}
	
	public function gettypeid(){
		return $_POST['type'];
	}
	
	public function gettypename(){
		$res=explode(',',$_POST['type']);
		return $res[1];
	}
	
	public function createToken(){
		$randLen=6;
		$chars='abcdefghijklmnopqrstuvwxyz';
		$len=strlen($chars);
		$randStr='';
		for ($i=0;$i<$randLen;$i++){
			$randStr.=$chars[rand(0,$len-1)];
		}
		$token=$randStr.time();
		return $token;
	}
	
	public function getStatusChart($token){
		if(empty($token)){
			$token=$this->token;
		}
		$wx = M('Wxuser')->where("token='$token'")->find();
		$chart = [];
		if($wx){
			$chart['name']=$wx['wxname'];
			$chart['text_num']=$wx['text_num'];
			$chart['img_num']=$wx['img_num'];
			$chart['voice_num']=$wx['voice_num'];
			$chart['request_num']=$wx['request_num'];
			//统计该公众号的全部请求数据
			$statistics=M('Requestdata')->field('sum(textnum) as textnum,sum(imgnum) as imgnum,sum(voicenum) as voicenum,sum(videonum) as videonum,sum(musicnum) as musicnum')->where("token='$token'")->find();
			
			$chart['total_request_num']=intval($statistics['textnum'])+intval($statistics['imgnum'])+intval($statistics['voicenum'])+intval($statistics['musicnum'])+intval($statistics['videonum']);
		}
		return $chart;
	}
	
	public static function clear(){
		$data['img_num']=0;
		$data['text_num']=0;
		$data['music_num']=0;
		$data['video_num']=0;
		$data['voice_num']=0;
		$data['request_num']=0;
		$data['activity_num']=0;
		$model = M('Wxuser');
		$model->where(['uid'=>session('uid')])->save($data);
	}
}
?>