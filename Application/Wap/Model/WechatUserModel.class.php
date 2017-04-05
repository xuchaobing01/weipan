<?php
namespace Wap\Model;
use Think\Model;
class WechatUserModel extends Model{
	protected $_auto = array(
		['create_time','time',1,'function'],
		['update_time','time',3,'function']
	);
	
	public static function get($openId){
		return M('Wechat_user')->where(['open_id'=>$openId])->find();
	}
	
	public static function set($openId,$info){
		$model = M('Wechat_user');
		$usr = M('Wechat_user')->where(['open_id'=>$openId])->find();
		$info['open_id'] = $openId;
		$info['update_time'] = time();
		if($usr == null || $usr == false){
			$info['create_time'] = time();
			$model->create($info);
			$model->add();
		}
		else{
			$model->create($info);
			$ret = $model->where(['open_id'=>$openId])->save();
		}
	}
}
?>