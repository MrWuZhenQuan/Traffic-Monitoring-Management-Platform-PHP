<?php 
namespace Home\Controller;

use Think\Controller;
use Com\WechatAuth;

/**
 * Class WechatPushController
 * @package Home\Controller
 * @package Com\WechatAuth
 * @author qax
 * @time 2015.1.5
 */
 Class WechatPushController extends CommonController {
 	
    /* 消息类型常量 */
    const MSG_TYPE_THUMB    = 'thumb';
    const MSG_TYPE_IMAGE    = 'image';
    const MSG_TYPE_VOICE    = 'voice';
    const MSG_TYPE_VIDEO    = 'video';
	/* 数据库表 */
	const USERDATABASE = 't_wx_users';
 	private $user_id = '';
 	private $wechatAuth = '';
 	private $accessToken = '';

 	private function init(){
  		$this->user_id = 0;
 		$wechat=M(self::USERDATABASE)->where(array('site_id'=>$this->user_id))->find();
 		$appid = $wechat['app_id'];
		$secret = $wechat['app_secret'];
		$this->wechatAuth = new WechatAuth($appid, $secret);
		$this->accessToken = $this->wechatAuth->getAccessToken();
		return $this->accessToken;
 	}

 	public function index(){

 		$this->display('Wechat/index');
 	}


	/*注意事项
	上传的多媒体文件有格式和大小限制，如下：

	图片（image）: 1M，支持JPG格式
	语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
	视频（video）：10MB，支持MP4格式
	缩略图（thumb）：64KB，支持JPG格式
	媒体文件在后台保存时间为3天，即3天后media_id失效。*/
 	public function uploadMaterial(){
 		$init = $this->init();
 		if(isset($init['access_token'])){
			$Service = D('WechatPush','Service');
			$content = $Service->getContent($this->user_id);
			$uploadResult=$this->wechatAuth->mediaUpload($content['nphoto'],self::MSG_TYPE_IMAGE);
			$uploadResult['url'] = $this->wechatAuth->mediaGet($uploadResult['media_id']);
			$uploadResult['local_url'] = $content['nphoto'];
			//$uploadResult['user_id'] = $this->user_id;
			$url = U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index'));
			if(isset($uploadResult['media_id']) ||isset($uploadResult['url'])){
				$this->jump($result = $Service->addMaterial($uploadResult),$url);
			}else{
				return $this->error('调用接口失败!!<br>errcode: '.$uploadResult['errcode'].'<br>errmsg: '.$uploadResult['errmsg'], U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index')));
			}

		}else{
			return $this->error('获取ACESS_TOKEN失败！请检查网络！', U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index')));
		}
	}
 	

 	public function push(){
 		$init = $this->init();
 		$media_id = 'Jaf428F0EfiqUH0qFirhOKKFCQ6bhFiND2Lka8CezphjHxpteG4TqF_kTgJTDIHQ';
 		$pushResult = $this->wechatAuth->massSendGroup(WechatAuth::MSG_TYPE_ARTICLES,$media_id,0);
 		p($pushResult);
 	}

 	public function getGroups(){
 		$init = $this->init();
 		$group = $this->wechatAuth->groupsGet();
 		p($group);
 	}

 	/*最多10条*/
	public function uploadArticles(){
		$init = $this->init();
		if(isset($init['access_token'])){
			$news = array(array('thumb_media_id' => "3z52TO9nTNIYV-e2rk_2snDfn160ijM-3nqG0ZuN-6dNPy83RbEb1v2aG4h_dOKo", 
							'author' => "",
							'title' => "［生态休闲观光］兰花争奇斗艳，市民春节前可免费观赏！",
							'content_source_url' => "www.baidu.com",
							'content' => '<img src="http://34163234.ngrok.com/okhaolvxing/Uploads/ueditor/image/20150110/1420859985112992.jpg" title="1420859985112992.jpg" alt="Koala.jpg" width="230" height="155" style="width: 230px; height: 155px;"/>',
							'digest' => "这是描述这是描述这是描述这是描述这是描述这是描述",
							'show_cover_pic' => 1),
							array('thumb_media_id' => "3z52TO9nTNIYV-e2rk_2snDfn160ijM-3nqG0ZuN-6dNPy83RbEb1v2aG4h_dOKo", 
							'author' => "测试",
							'title' => "测试标题",
							'content_source_url' => "www.baidu.com",
							'content' => "这是测试这是测试这是测试这是测试这是测试",
							'digest' => "这是描述这是描述这是描述这是描述这是描述这是描述",
							'show_cover_pic' => 0));

			$Service = D('WechatPush','Service');
			$url = U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index'));
			$uploadResult = $this->wechatAuth->newsUpload($news);

			if(isset($uploadResult['media_id'])){
				$this->jump($result = $Service->addMaterial($uploadResult),$url);
			}else{
				return $this->error('调用接口失败!!<br>errcode: '.$uploadResult['errcode'].'<br>errmsg: '.$uploadResult['errmsg'], U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index')));
			}
			//$uploadResult['user_id'] = $this->user_id;
			
		}else{
			return $this->error('获取ACESS_TOKEN失败！请检查网络！', U('WechatPush/index',array('Controller' =>'WechatPush','Method'=>'index')));
		}

	}

 	public function pushResult(){

 	}



 	public function pushPreview(){
 		$init = $this->init();

 		$media_id = 'yuPldJTGWO3leJABiztzzOd9VRuvV3hja66VDOi-WUPE5rJ9Ymv37OH3oWY8D0oh';
 		$openid = "oMInejoLnAEngDtJ53ZdXUN_woc8";
 		$pushResult = $this->wechatAuth->massPreview(WechatAuth::MSG_TYPE_ARTICLES,$media_id,$openid);
 		p($pushResult);
 		/* Array
			(
			    [errcode] => 0
			    [errmsg] => preview success
			)*/
 	}

     /****************************页面跳转和显示开始****************************/

    public function push_text()
    {
        $this->display('/Wechat/push_text');
    }

    /****************************页面跳转和显示结束****************************/

 }
 ?>
}
