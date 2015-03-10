<?php

namespace Home\Controller;
use Think\Controller;
class ChartController extends CommonController {

    const DATABASE = 'traffic_data';

    public function index(){

        $data = M(self::DATABASE)->select();
        p($d = date('Y-m-d H:i:s',$data[0]['created_at']));
        $array = array();
        $currentHour = date('H');
        for ($i=0; $i < $currentHour ; $i++) { 
            $chartData = array('period' => $i.'', 'visits' => $data['visiters'],'device' => '');
            array_push($array,$chartData);
        }
        p($array);
        p($data);die;
        echo time();die;
        $array = array();
        for($i=1;$i<=24;$i++){
            
            $data = array('period' => $i."", 'visits' => $data['visiters'],'device' => '');
            array_push($array,$data);
        }
        
        $time = time();
        echo date('H:i:s');die;
        $this->ajaxReturn($data);
    	$this->display();
    }
}