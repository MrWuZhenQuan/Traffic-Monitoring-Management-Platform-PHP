<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {

	public function user_manage(){
	     $data = M('user')->select();
	     $this->assign('data',$data);	

	     $this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

}
