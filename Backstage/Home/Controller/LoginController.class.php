<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){

        $this->display();
    }


    /**
     * 登录业务，如果登录成功，便在页面上进行跳转
     */
    public function doLogin()
    {	
        $username = $_POST['username'];
        $password = $_POST['password'];
        if(!empty($username) && !empty($password)){
            $loginService = D('Login', 'Service');
            //登录服务
            $result = $loginService->verify($username, $password,NULL);
            //写入日志服务
            //$loginService->loginLog();
            
            //判断是否登录，如果登录后。检测是否是手机
			if( session(C('LOGINNAME'))){
				$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
				$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
				if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
				{
				  $result['ismobile']=1;
				}
			}
            $this->ajaxReturn($result);
        }else{
            $result['content'] = "用户名/密码不能为空";
            $result['ismobile']=0;
            $this->ajaxReturn($result);
        }
    }

    /**
     * 退出登录
     * @modify qax 2014.10.15 10:26
     */
    public function exitLogin()
    {
        $loginService = D('Login', 'Service');
        $result = $loginService->exitLogin();
        // var_dump($result);die;
        if($result['status'] === false){
            $this->error($result['data']['error'], U('Login/index'));
        }else{
            $this->success($result['data']['success'], U('Login/index'));
        }
    }   
}