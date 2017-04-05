<?php
namespace Admin\Controller;
use User\Model;
use Spark\Util\Page;
class UsersController extends BaseController{
	public function index(){
		$db=D('Users');
		$group=M('User_group')->field('id,name')->order('id desc')->select();
		$agents_list=M('agent')->field('id,company')->order('id desc')->select();
		$where = array();
		if(isset($_GET['aid'])){
			$where['agent_id']= $_GET['aid'];
		}
		$count= $db->where($where)->count();
		$Page= new Page($count,25);
		$show= $Page->show();
		$list = $db->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($agents_list as $key=>$val){
			$agents[$val['id']] = $val['company'];
		}
		foreach($list as $key=>$item){
			if($item['agent_id']==0){
				$list[$key]['agent_name'] = '总部';
			}
			else $list[$key]['agent_name'] = $agents[$item['agent_id']];
		}
		foreach($group as $key=>$val){
			$g[$val['id']]=$val['name'];
		}
		unset($group);
		$this->assign('info',$list);
		$this->assign('page',$show);
		$this->assign('agents',$agents_list);
		$this->assign('group',$g);
		$this->assign('meta_title','<i class="fa fa-users"></i>&nbsp;客户列表');
		$this->display();
	}
	
	// 添加用户
    public function add(){
        $UserDB = M("Users");
        if(IS_POST) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
                $this->error('密码必须填写！');
            }
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }
			$_POST['password'] = md5($_POST['password']);
            //根据表单提交的POST数据创建数据对象
            $_POST['viptime']=strtotime($_POST['viptime']);
            if($UserDB->create()){
                $user_id = $UserDB->add();
                if($user_id){
					$this->success('添加成功！',U('Admin/Users/index'));                    
                }
				else{
                    $this->error('添加失败!');
                }
            }
			else{
                $this->error($UserDB->getError());
            }
        }
		else{
			$agents_list=M('agent')->field('id,company')->order('id desc')->select();
            $role = M('User_group')->field('id,name')->where('status = 1')->select();
            $this->assign('role',$role);
			$info['viptime']=time()+10*24*3600;
			$this->assign('info',$info);
			$this->assign('agents',$agents_list);
			$this->assign('meta_title','<i class="fa fa-user"></i>&nbsp;添加客户');
            $this->display();
        }
    }
	
	public function search(){
		$name=I('post.name');
		$type=I('post.type');
		switch($type){
			case 1:
			$data['username']=['like','%'.$name.'%'];
			break;
			case 2:
			$data['id']=['like','%'.$name.'%'];
			break;
			case 3:
			$data['email']=['like','%'.$name.'%'];
		}
		if(isset($_GET['aid'])){
			$data['agent_id']= $_GET['aid'];
		}
		$group=M('User_group')->field('id,name')->order('id desc')->select();
		$agents_list=M('agent')->field('id,company')->order('id desc')->select();
		$list=M('Users')->where($data)->select();
		foreach($agents_list as $key=>$val){
			$agents[$val['id']] = $val['company'];
		}
		foreach($list as $key=>$item){
			if($item['agent_id']==0){
				$list[$key]['agent_name'] = '总部';
			}
			else $list[$key]['agent_name'] = $agents[$item['agent_id']];
		}
		foreach($group as $key=>$val){
			$g[$val['id']]=$val['name'];
		}
		unset($group);
		$this->assign('info',$list);
		$this->assign('group',$g);
		$this->assign('agents',$agents_list);
		$this->display('index');
	}
	
    // 编辑用户
    public function edit(){
        $UserDB = D("Users");
        if(IS_POST) {
            $password = I('post.password','','trim');
            $repassword = I('post.repassword','','trim');
			$users=M('Users')->field('gid')->find($_POST['id']);
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }
            if($password==false){
				unset($_POST['password']);
				unset($_POST['repassword']);
			}
			else{
				$_POST['password']=md5($password);
			}
            //根据表单提交的POST数据创建数据对象
            $_POST['viptime']=strtotime($_POST['viptime']);
			if($UserDB->save($_POST)){
				//用户组变更
				if($_POST['gid']!=$users['gid']){
					
				}
				$this->success('编辑成功！',U('Admin/Users/index'));
			}
			else{
				$this->error('编辑失败!');
			}
        }
		else{
            $id = I('get.id',0,'intval');
            if(!$id)$this->error('参数错误!');
            $role = M('User_group')->field('id,name')->select();
            $info = $UserDB->find($id);
            $this->assign('role',$role);
            $this->assign('info',$info);
			$agents_list=M('agent')->field('id,company')->order('id desc')->select();
			$this->assign('agents',$agents_list);
			$this->assign('meta_title','<i class="fa fa-user"></i>&nbsp;编辑客户');
            $this->display('edit');
        }
    }
	
	public function addfc(){
		$token_open=M('Token_open');
		$open['uid']=session('uid');
		$open['token']=$_POST['token'];
		$gid=session('gid');
		$fun=M('Function')->field('funname,gid,isserve')->where('`gid` <= '.$gid)->select();
		foreach($fun as $key=>$vo){
			$queryname.=$vo['funname'].',';
		}
		$open['queryname']=rtrim($queryname,',');
		$token_open->data($open)->add();
	}
	
	//删除用户
    public function del(){
        $id = I('get.id',0,'intval');
        if(!$id)$this->error('参数错误!');
        $UserDB = D('Users');
        if($UserDB->delete($id)){
			$where['uid']=$id;
			M('wxuser')->where($where)->delete();
			M('token_open')->where($where)->delete();
			M('text')->where($where)->delete();
			M('img')->where($where)->delete();
			M('member')->where($where)->delete();
			M('indent')->where($where)->delete();
			M('areply')->where($where)->delete();
			$this->assign("jumpUrl");
			$this->success('删除成功！');            
        }else{
            $this->error('删除失败!');
        }
    }
	
	public function export2excel(){
		Vendor('PHPExcel.PHPExcel');
		$data = array();
		$title = array('用户名','手机号','QQ','邮箱','注册时间','最后登录时间','到期时间');
		$data[] = $title;
		$where['cellphone'] = ['like','1%'];
		$rows = M('users')->field('username,cellphone,qq,email,create_time,last_login_time,viptime')->where($where)->order('id desc')->select();
		foreach($rows as $row){
			$r = array();
			$r[] = $row['username'];
			$r[] = ' '.$row['cellphone'];
			$r[] = $row['qq'];
			$r[] = $row['email'];
			$r[] = date('Y/m/d',$row['create_time']);
			$r[] = date('Y/m/d',$row['last_login_time']);
			$r[] = date('Y/m/d',$row['viptime']);
			$data[] = $r;
		}
		//dump($data);exit;
		createExcel('',$data);
	} 
}