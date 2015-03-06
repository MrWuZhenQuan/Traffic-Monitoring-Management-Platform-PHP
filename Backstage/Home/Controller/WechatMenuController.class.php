<?php 
namespace Home\Controller;

use Think\Controller;
use Com\WechatAuth;
/**
 * Class WechatMenuController
 * @package Home\Controller
 * @package Com\WechatAuth
 * @author qax
 * @time 2014.11.17
 */
Class WechatMenuController extends CommonController {

	public function index(){
        $where['user_id'] = $_SESSION['USER_ID'];
        $wechat=M("t_wx_menu")->where($where)->find();
        //echo substr_count($wechat);
        $this->assign("wechat",$wechat);
		$this->display("Wechat/menu");
	}

	public function saveMenu(){
		$data = I('wechat');
		$data['content'] = htmlspecialchars_decode($data['content']);
		$Wechat = D('WechatMenu','Service');
		$url = U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index'));

		if($data['id'] !=""){
			$this->jump($Wechat->updateWechatMenu($data),$url);
		}else{
			$this->jump($Wechat->addWechatMenu($data),$url);
		}
	}

    //生成菜单，post到微信
    public function postMenu(){
        //初始化菜单数据
        if(!isset($_POST["wechat"]) || empty($_POST["wechat"])){
            $this->error('无效的操作！', U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
        }

        $result=$this->post($this->get_access_token(),$_POST['wechat'],true);

        $bool=json_decode($result,true);

        if(!is_null($bool)){
            if($bool['errcode']!=0){
                $this->error($bool['errmsg'], U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
            }
            else{
                $this->success("菜单生成成功", U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
            }
        }
        else{
            $this->error("菜单发送错误", U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
        }

    }

    public function deleteMenu(){

        $result=$this->post($this->get_access_token(),$_POST['wechat'],false);

        $bool=json_decode($result,true);
        if(!is_null($bool)){
            if($bool['errcode']!=0){
                $this->error("菜单撤销失败", U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
            }
            else{
                $this->success("菜单撤销成功", U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
            }
        }
        else{
            $this->error("菜单发送错误", U('WechatMenu/index',array('Controller' =>'WechatMenu','Method'=>'index')));
        }
    }

    public function get_access_token(){
        $where['user_id'] = $_SESSION['USER_ID'];
        $wechat=M("t_wx_users")->where($where)->find();

        $json=$this->http_request_json("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wechat['app_id']."&secret=".$wechat['app_secret']);

        $data=json_decode($json,true);

        if($data['access_token']){  
            return $data['access_token'];  
        }else{  
            return false;  
        }         
    }  
    
    public function http_request_json($url){    
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $result = curl_exec($ch);  
        curl_close($ch);  
        return $result;    
    }  
    
    public function post($token, $jsonData,$bool){//$bool true为生成，false为撤销
        //echo $token;

        if($bool){
            $MENU_URL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;
        }
        else{
            $MENU_URL = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$token;
        }
        
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $MENU_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        
        
        if (curl_errno($ch)) {
            echo 'Error'.curl_error($ch);
        }
        
        curl_close($ch) ;
        
        //echo $result;
        
        return $result;
    }

	// public function postMenu(){
	// 	$appid = 'wxec2a92c0d727325f';
	// 	$secret = '61ee458e33a406fa913041529d1eb5b1';
	// 	$wechatAuth = new WechatAuth($appid, $secret);
	// 	$accessToken = $wechatAuth->getAccessToken();

	// 	$newmenu =array(
	// 		array('type'=>'scancode_waitmsg','name'=>'扫码带提示','key'=>'rselfmenu_0_0'),
	// 		array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
	// 		array('name'=>'菜单',"sub_button"=>array(
	// 			array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
	// 			array('type'=>'view','name'=>'我要搜索','url'=>'http://www.baidu.com'),
	// 			array('type'=>'location_select','name'=>'发送位置','key'=>'rselfmenu_2_0'),
	// 			))
	// 		);

	// 	$menuReturn = $wechatAuth->menuCreate($newmenu);
	// }
}
 ?>