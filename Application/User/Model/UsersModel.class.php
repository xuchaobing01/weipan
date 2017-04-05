<?php
namespace User\Model;
use Think\Model\RelationModel;
class UsersModel extends RelationModel{
	//自动验证
	protected $_validate=array(
		array('username','require','用户名称必须填写！',1,'',3),
		array('password','require','用户密码必须填写！',0,'',3),
		array('username','','用户名称已经存在！',1,'unique',1), // 新增修改时候验证username字段是否唯一
		array('email','email','邮箱格式不正确'),
		array('email','','邮箱已经存在！',1,'unique',1),
	);
	
	protected $_auto = array (
		array('password','md5',self::MODEL_BOTH,'function'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('create_ip','getip',self::MODEL_INSERT,'callback'),
		array('viptime','time',self::MODEL_BOTH,'function'),
		array('last_login_time','time',self::MODEL_BOTH,'function'),
		array('last_login_ip','getip',self::MODEL_BOTH,'callback'),
		array('status','getstatus',self::MODEL_BOTH,'callback'),
	);
	
	protected $_link  = array(
		'Group' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'UserGroup',
			'foreign_key'   => 'gid',
			'mapping_name'  => 'group',
			'as_fields' => 'name:gname',
		)
	);
	
	public function getip(){
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public function getstatus(){
		return 1;
	}
	
	public function checkRequestNum(){
		$group = M('User_group')->find($this->gid);
		if($group && intval($group['request_limit'])>intval($this->request_num)){
			return true;
		}
		return false;
	}
	
	public static function authenticate($uname,$passwd) {
		$model = new self();
		$ret = new \stdClass();
		if(strpos($uname,'@')!==false){
			list($uname,$master_uname) = explode('@',$uname);
			$m_user = $model->where(['username'=>$master_uname])->relation(true)->find();
			if($m_user != null){
				$sub_user = M('sub_users')->where(['username'=>$uname,'master_uid'=>$m_user['id']])->find();
				if($sub_user && ($passwd===$sub_user['password'])){
					if($sub_user['status']==0){
						$ret->statusCode = 402;
						$ret->message = '该帐号已经被禁用，请联系管理员！';
					}
					else{
						session('uname',$m_user['username']);
						session('uid',$m_user['id']);
						session('gid',$m_user['gid']);
						session('account_id',$sub_user['id']);
						session('role_id',$sub_user['role_id']);
						session('account_name',$sub_user['username']);
						session('viptime',$m_user['viptime']);
						session('gname',$m_user['gname']);
						$ret->statusCode = 0;
						$ret->message = '登录成功';
					}
				}
				else{
					$ret->statusCode = 400;
					$ret->message = '用户名或密码错误！';
				}
			}
			else{
				$ret->statusCode = 400;
				$ret->message = '用户名或密码错误！';
			}
		}
		else{
			$usr = $model->where(['username'=>$uname])->relation(true)->find();

			if($usr && ($passwd===$usr['password'])){
				if($usr['status']==0){
					$ret->statusCode = 400;
					$ret->message = '请联系在线客服，为你人工审核帐号！';
				}
				elseif($usr['viptime']<time()){
					$ret->statusCode = 400;
					$ret->message = '您的帐号已经到期，请充值后再使用！';
				}
				else{
					session('uid',$usr['id']);
					session('gid',$usr['gid']);
					session('uname',$usr['username']);
					session('viptime',$usr['viptime']);
					session('gname',$usr['gname']);
					session('last_login_time',$usr['last_login_time']);
					session('last_login_ip',$usr['last_login_ip']);
					//记录本次登录时间、IP
					$model = new self();
					$model->id = $usr['id'];
					$model->last_login_time=time();
					$model->last_login_ip=$model->getip();
					$model->save();
					$ret->statusCode = 0;
					$ret->message = '登录成功';
				}
			}
			else{
				$ret->statusCode = 400;
				$ret->message = '用户名或密码错误！';
			}
		}
		if($ret->statusCode == 0){
			$token = M('wxuser')->where(['uid'=>session('uid')])->field('token')->find()['token'];
			session('token',$token);
		}
		return $ret;
	}
}