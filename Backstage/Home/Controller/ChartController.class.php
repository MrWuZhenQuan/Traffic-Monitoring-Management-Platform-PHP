<?php
namespace Home\Controller;
use Think\Controller;
class ChartController extends Controller {

    const DATABASE = 'traffic_data';

    public function index(){
    	$data = M(self::DATABASE)->select();

        // var_dump($data);
        echo time();die;
        $array = array();
        for($i=1;$i<=24;$i++){
            
            $data = array('period' => $i.'点', 'visits' => $data['visiters'],'device' => '');
            array_push($array,$data);
        }
        
        $time = time();
        echo date('H:i:s');die;
        $this->ajaxReturn($data);
    	$this->display();
    }
}