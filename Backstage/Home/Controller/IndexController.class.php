<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

// const DATABASE = 'traffic_data';
// const DEVICEDATABASE = 'device';
	public function start(){
	    $this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

    public function initView(){

        $Service = D('Index','Service');
        $data = $Service->getDiffentPeriodData();
        $deviceName = $Service->getDeviceName();
        $allDeviceData = $Service->getDiffentDeviceData();
        // p($deviceName);
        $this->assign('data',$data);
        $this->assign('device',$deviceName);
        $this->assign('allDeviceData',$allDeviceData);

        $this->display();
    }

    public function updateTestTime()
    {
        for ($i=1; $i <=3 ; $i++) { 
            $data['id'] = $i;
            $data['created_at'] = date('Y-m-d H:i:s');
            M('traffic_data')->save($data);
        }
    }
}