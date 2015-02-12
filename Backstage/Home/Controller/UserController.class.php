<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {

	public function user_manage(){
		
		$this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

}