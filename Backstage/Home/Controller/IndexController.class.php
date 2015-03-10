<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

	public function start(){
	       $this->display();
	}

            public function index(){
    	$Model = M('user');
    	$this->display();
            }

            public function initView(){
            	
            	$this->display();
            }
}