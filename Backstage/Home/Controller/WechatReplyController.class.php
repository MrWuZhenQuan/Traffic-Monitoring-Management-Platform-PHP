<?php 
namespace Home\Controller;

use Think\Controller;

/**
 * Class WechatReplyController
 * @package Home\Controller
 * @author qax
 * @time 2014.11.17
 */
Class WechatReplyController extends CommonController {
	
	public function index(){

        if(!empty($_SESSION['USER_ID'])){
            $user_id = $_SESSION['USER_ID'];
            $wechat=M('wx_users')->getByUserId($user_id);

            if(is_null($wechat) || empty($wechat)){

    		    $wechat['url']="http://".$this->getHttpHost()."/Traffic-Monitoring-Management-Platform-PHP/index.php/WechatBinding?id=".$_SESSION['USER_ID'];
    		    $wechat['token']=randomkeys(8);
    		    $wechat['user_id']=$user_id;
            }

            $this->assign("wechat",$wechat);
        }
		$this->display('Wechat/index');
	}

	public function follow_text_reply(){
        if(!empty($_SESSION['USER_ID'])){

            $wechat=M('wx_respond')->getByUserId($_SESSION['USER_ID']);
            // if(is_null($wechat) || empty($wechat)){
            //     $wechat['user_id'] = $_SESSION['USER_ID'];
            // }
        }
        $this->assign('wechat',$wechat);
		$this->display('Wechat/follow_text_reply');
	}

	public function text_reply(){

		$this->display('Wechat/text_reply');
	}

	public function link_reply(){

		$this->display('Wechat/link_reply');
	}

    public function add_link_reply(){
        $this->display('Wechat/add_link');
    }

    public function add_follow_text_reply(){
        $data = I('wechat');
        $url = U('WechatReply/follow_text_reply',array('Controller' =>'WechatReply','Method'=>'follow_texreply'));
        $wechat=M('wx_respond')->getByUserId($_SESSION['USER_ID']);
        $Wechat =D('WechatReply','Service');

        if(is_null($wechat) || empty($wechat)){
            //$_SESSION['token']=$wechat['token'];
            $data['retype'] = 'TEXT';
            $data['even_key'] = 'subscribe';
            $data['remark'] = "关注回复";
            $data['created_at'] = time();
            $data['user_id'] = $_SESSION['USER_ID'];
            $this->jump($Wechat->addFollowText($data),$url);
        }else{
            $data['updated_at'] = time();
            $this->jump($Wechat->updateFollowText($data),$url);
        }
        
    }

	public function add_wechat_user(){
		$wechat = I('wechat');

        $wechat['user_id'] = $_SESSION['USER_ID'];
        if(is_null($wechat['user_id']))
            $wechat['user_id'] = 0;
        $data=M('wx_users')->getBySiteId($wechat['user_id']);
        $url = U('WechatReply/index',array('Controller' =>'WechatReply','Method'=>'index'));
        if(is_null($data) || empty($data)){
            $Wechat =D('WechatReply','Service');
    		//$_SESSION['token']=$wechat['token'];
    		$wechat['created_at'] = time();

            $this->jump($Wechat->addWechatUser($wechat),$url);
        }else{
            return $this->error('公众账号信息已存在！', U('WechatReply/index',array('Controller' =>'WechatReply','Method'=>'index')));
        }
	}

    public function addLinkReply(){
        $data = I('link');
        $data = htmlspecialchars_decode($data);
        p($data);
    }

	/**
    * 获取域名
    * @return 
    */
    public function getHttpHost(){
        return $_SERVER['HTTP_HOST'];
    }
}
?>