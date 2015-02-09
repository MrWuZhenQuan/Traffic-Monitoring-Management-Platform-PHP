<?php 
namespace Home\Service;

/**
 * Class WechatBindingService
 * @author qax
 * Created_time: 12月30日
 */
class WechatBindingService extends CommonService {

	/* 数据库表 */
    const RETDATABASE = 't_wx_respond';
	const USERDATABASE = 't_wx_users';
	const MSGDATABASE = 't_wx_message';
	const CONTENTDATABASE = 't_news_release';
	/*查询条件*/
	private $condition = 'nrtype';

	public function getContent($key){
		$Model = M(self::CONTENTDATABASE);

		$content = $Model->where(array($this->condition=> array('like','%'.$key.'%')))->select();

		$arr = array();
		foreach ($content as $key => $value) {
			$shortcontent = sysSubStr(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", strip_tags($value['nrcontent'])), "48");
			$data = array($content[$key]['nrtitle'], $shortcontent,
						"http://".$_SERVER['HTTP_HOST']."/okhaolvxing/index.php/".$content[$key]['nphoto'], 
    					"http://".$_SERVER['HTTP_HOST']."/okhaolvxing/index.php/ApiNewsRelease/getContent?id=".$content[$key]['id']);
			array_push($arr, $data);
		}
		
		return $arr;
	}

	public function getSubscribeMessage($id)
	{
		$data = self::getUserId($id);
		$data['even_key'] = 'subscribe';
		$msg = M(self::RETDATABASE)->where($data)->field('content')->find();
		return $msg['content'];
	}

	private function getUserId($id){
		return M(self::USERDATABASE)->where(array('wechat_id' => $id))->field('user_id')->find();
	}

	public function addMessagedata($data){

		$msg = array('request_type' => $data['MsgType'],  
					 'from_user_name' => $data['FromUserName'], 
					 'to_user_name' => $data['ToUserName'],
					 'created_at' => $data['CreateTime'],
					 'status' => 1);

		if($data['MsgType'] == 'event'){
			$msg['request_content'] = $data['Event'];
			$msg['event_key'] = htmlspecialchars_decode($data['EventKey']);
		}else{
			$msg['request_content'] = $data['Content'];
		}
		return $this->add($msg, self::MSGDATABASE);
	}

	// public function add($data,$db)
	// {
	// 	p($data);die;
	// }
}
 ?>