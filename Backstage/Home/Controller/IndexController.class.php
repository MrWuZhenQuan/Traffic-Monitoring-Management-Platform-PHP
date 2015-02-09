<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

	public function start(){
		
		$this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

}