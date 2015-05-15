<?php 
namespace Home\Controller;

use Think\Controller;
use Com\Wechat;
/**
 * Class WechatBindingController
 * @package Home\Controller
 * @author qax
 * @time 2014.11.17
 */
Class WechatBindingController extends Controller {
	
    /*测试数据函数*/
    public function test(){
        $userId = '{"ToUserName":"gh_8cfd2532906b","FromUserName":"oT02ms6qlkKu_-yC3AGASkJyDDUo","CreateTime":"1420357118","MsgType":"text","Content":"12","MsgId":"6100387370653431039"}';
        $arrayName = array('ToUserName' => "gh_8cfd2532906b", 
                            'FromUserName' => "oT02ms6qlkKu_-yC3AGASkJyDDUo",
                            'CreateTime' => '1420357118',
                            'MsgType' => 'text',
                            'Content' => '12');
        $Service = D('WechatBinding','Service');
        $key = '航展';
        // $msg = $Service->getContent($key);

        
        $Data = D('Index','Service'); //打开业务服务层
        $allDeviceData = json_decode($Data->getDiffentDeviceData(),true);//获取不同设备客流量信息
        p($allDeviceData["bar"]);
        p($allDeviceData["bar"][0]);
        foreach ($allDeviceData['bar'] as $key => $value) {
            p($value["device"]);
            $str .= $value["device"] ."quyukeliuliangwei:".$value["traffic"]."\n";
         }  

        p($str);die;
    }



public function index(){
        
        $userId = I('id');
        $token = $this->getToken($userId);
        // p($token);die;
        // $token = 'weixin';
        writeSyslog($token,'wechat',1);
        $Service = D('WechatBinding','Service');
        $wechat = new Wechat($token);
        $request =  $wechat->request();
        writeSyslog($request,'wechat',1);

        if($request && is_array($request)){

            switch ($request['MsgType']) {
                case Wechat::MSG_TYPE_TEXT:
                    // $content = $Service->getContent(htmlspecialchars_decode($request['Content']));
                        if(htmlspecialchars_decode($request['Content'] == "实时客流量")）{
                        $content = array("客流量统计表", "请点击打开并查看实时客流量统计图",
                                "http://szbk.chuzhou.cn/wdck/res/1/10/2011-10/08/A01/res01_attpic_brief.jpg", 
                                "http://tmp.nat123.net/Traffic-Monitoring-Management-Platform-PHP/index.php/Index/initView.html";
                        }
                    if(!is_null($content)&& !empty($content)){
                        switch (count($content)) {
                            case '0':
                                $wechat->replyNews($content);
                                break;
                            case '1':
                                $wechat->replyNews($content[0],$content[1]);
                                break;
                            case '2':
                                $wechat->replyNews($content[0],$content[1],$content[2]);
                                break;
                            case '3':
                                $wechat->replyNews($content[0],$content[1],$content[2],$content[3]);
                                break;
                            case '4':
                                $wechat->replyNews($content[0],$content[1],$content[2],$content[3],$content[4]);
                                break;
                            default:
                                $content[4] = array('一共'.count($content).'条记录，显示更多...','',"http://".$_SERVER['HTTP_HOST']."/okhaolvxing/index.php/ApiNewsRelease/getContent?id=");
                                $wechat->replyNews($content[0],$content[1],$content[2],$content[3],$content[4]);
                                break;
                        }
                    }else if(htmlspecialchars_decode($request['Content'] == "TrafficData")||htmlspecialchars_decode($request['Content'] == "客流量")){
                         $Data = D('Index','Service'); //打开业务服务层
                         $allDeviceData = json_decode($Data->getDiffentDeviceData(),true);//获取不同设备客流量信息  
                        foreach ($allDeviceData['bar'] as $key => $value) {
                            // p($value["device"]);
                            if ($value['traffic'] <=800){
                                $msg = "该地区实时客流量较少，状态为畅通"；
                            }else if（$value['traffic'] >800&&$value['traffic']<1600）{
                                $msg = "该地区实时客流量为中等，状态为相对畅通"；
                            }else{
                                $msg = "该地区实时客流量较多，状态为阻塞"；
                            }
                            $str .= $value["device"] ."区域客流量为:".$value["traffic"]."\n"。$msg."\n";
                         }  
                         
                         $wechat->replyText($str);                         
                    }else{
                        $wechat->replyText("您发送的内容，小编暂时还不知道，但是我会记下来，逐渐完善的，谢谢!");
                    }
                    break;
                case Wechat::MSG_TYPE_IMAGE:
                    $wechat->replyText("您发送的图片，小编暂时还不了解，但是我会记下来，逐渐完善的，谢谢!");
                    break;
                case Wechat::MSG_TYPE_VOICE:
                    $wechat->replyText("您发送的语音，小编暂时还能识别，但是我会记下来，逐渐完善的，谢谢!");
                    break;
                case Wechat::MSG_TYPE_MUSIC:
                    $wechat->replyText("您发送的音乐，小编暂时还听不懂，但是我会记下来，逐渐完善的，谢谢!");
                    break;
                case Wechat::MSG_TYPE_LOCATION:
                    $wechat->replyText("您发送的位置，小编暂时还找不到，但是我会记下来，逐渐完善的，谢谢!");
                    break;     
                case Wechat::MSG_TYPE_LINK:
                    $wechat->replyText("您发送的连接，小编暂时还不知道，但是我会记下来，逐渐完善的，谢谢!");
                    break;  
                case Wechat::MSG_TYPE_EVENT:
                    switch ($request['Event']) {
                        case Wechat::MSG_EVENT_SUBSCRIBE:
                            $msg = $Service->getSubscribeMessage($request['ToUserName']);
                            writeSyslog($userId,'wechat',1);
                            $wechat->replyText($msg);
                            break;
                        case Wechat::MSG_EVENT_SCAN:
                            # code...
                            break;
                        case Wechat::MSG_EVENT_LOCATION:
                            # code...
                            break;
                        case Wechat::MSG_EVENT_CLICK:
                            $wechat->replyText("这是菜单点击!");
                            break;
                        case Wechat::MSG_EVENT_MASSSENDJOBFINISH:
                            # code...
                            break; 
                        case Wechat::MSG_EVENT_UNSUBSCRIBE:
                            # code...
                            break; 
                        default:
                            # code...
                            break;
                    }
                    break;  
     
                default:
                    $wechat->replyText("Unknow msg type: ".$request['MsgType']);
                    break;
            }
        if(false === $Service->addMessagedata($request)){
            writeSyslog('插入失败数据:'.$request,'wechat',1);
        }
        }
		//$this->display('Wechat/index');
	}

    public function getToken(){

         if(!empty($_GET['id']) || isset($_GET['id'])){
            $where['user_id']=$_GET['id'];
            $wechat=M("wx_users")->where($where)->find();
            $token=$wechat['token'];
            
            return $token;
         }else{
             return 0;
         }
    }

    private function judgeRequest($data){

    }

    private function judegeEven($request){
        $Service = D('WechatBinding','Service');
        switch ($request['Event']) {
            case Wechat::MSG_EVENT_SUBSCRIBE:
                $msg = $Service->getSubscribeMessage($request['ToUserName']);
                writeSyslog($userId,'wechat',1);
                $wechat->replyText($msg);
                break;
            case Wechat::MSG_EVENT_SCAN:
                # code...
                break;
            case Wechat::MSG_EVENT_LOCATION:
                # code...
                break;
            case Wechat::MSG_EVENT_CLICK:
                $wechat->replyText("这是菜单点击!");
                break;
            case Wechat::MSG_EVENT_MASSSENDJOBFINISH:
                # code...
                break; 
            case Wechat::MSG_EVENT_UNSUBSCRIBE:
                # code...
                break; 
            default:
                # code...
                break;
        }
    }
    // //手动绑定后，微信会get数据过来。。被动响应banding

    // public function bangding(){

    //     if (isset($_GET["echostr"])) {
    //         $this->valid();
    //     }else{
    //         $this->responseMsg();
    //     }
    // }

    // public function valid()
    // {
    //     $echoStr = $_GET["echostr"];

    //     //valid signature , option
    //     if($this->checkSignature()){
    //         echo $echoStr;
    //         exit;
    //     }
    // }

    // private function checkSignature()//token绑定成功数据库
    // {
    //     $signature = $_GET["signature"];
    //     $timestamp = $_GET["timestamp"];
    //     $nonce = $_GET["nonce"];    

    //     $tmpArr = array($this->getToken(), $timestamp, $nonce);
    //     sort($tmpArr);
    //     $tmpStr = implode( $tmpArr );
    //     $tmpStr = sha1( $tmpStr );
        
    //     if( $tmpStr == $signature){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

    //   //回复信息
    // public function responseMsg()
    // {
    //     //get post data, May be due to the different environments
    //     $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

    //     //extract post data
    //     if (!empty($postStr)){

    //         $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    //         $RX_TYPE = trim($postObj->MsgType);

    //         switch($RX_TYPE)
    //         {
    //             case "text"://文字类型
    //                 $resultStr = $this->handleText($postObj);
    //                 break;
    //             case "event"://按钮和关注事件
    //                 $resultStr = $this->handleEvent($postObj);
    //                 break;
    //             default:
    //                 $resultStr = "Unknow msg type: ".$RX_TYPE;
    //                 break;
    //         }
    //         echo $resultStr;
    //     }else {
    //         echo "please input something";
    //         exit;
    //     }
    // }

    // //关键字处理回复
    // //修改
    // //时间2014/7/14
    // //修改人：吴振权
    // //主要是修改了回复消息的机制，完成文本回复和图文回复功能
    // public function handleText($postObj)
    // {
    //     $fromUsername = $postObj->FromUserName; //接收方账号原始id
    //     $toUsername = $postObj->ToUserName;     //开发者微信号
    //     $keyword = trim($postObj->Content);     //回复内容，长度不超过2048

    //     $where['store_id'] =$_GET['id'];
    //     if(is_null($keyword)||empty($keyword)){
    //         return false;
    //     }
            
    //     //$where['store_id'] = (int)$respond['store_id'];

    //     if(!empty($keyword))
    //     {
    //         // $respond=M("Wechat")->where($user)->field('store_id')->find();
            
    //         // $where = $respond['store_id'];
    //         //$where['keyword'] = array('like',$keyword.'%');模糊搜索
    //         $where['keyword'] = (string)$keyword;
    //         $Msg = M("htl_wechat_keyword")->where($where)->find();
            
    //         $middle['keyword_id'] = $Msg['id'];
    //         $Respond = M('htl_wechat_kw_res_middle')->where($middle)->find();
            
    //         $w['id'] =$Respond['respond_id']; 
    //         $content = M("htl_wechat_respond")->where($w)->find();

    //         if($content['ret_type']=="TEXT"){
    //             $content = $content['content'];
    //             $resultStr = $this->responseText($postObj, $content, $flag=0);

    //         }else if($content['ret_type']=="ARTICLE"){
    //             $link['respond_id'] = $Respond['respond_id'];
    //             //$subid['id']= $content['sub_id'];
    //             $Link = M("htl_wechat_link_chunks")->where($link)->select();
    //             $sub_count = $Link['htl_link_chunk_items_count'];
    //             $count = count($Link);
    //             if($sub_count==$count){
    //                 $count = $count + 1;
    //             }
    //             $contents = array();
    //             //添加了对link_chunk_item表为空值的判断
    //             $j=0;    
    //             for($i = 0;$i < $count ;$i++){

    //                 if(0 == $i){
    //                     $link_id['id'] = $content['sub_id'];
    //                 }else if($Link[$i]['link_id']!=$content['sub_id']){
    //                     $link_id['id'] = $Link[$i]['link_id'];
    //                 }else{
    //                     continue;
    //                 }
                        
    //                 $Item = M("htl_wechat_link_chunk_item")->where($link_id)->field('title,description,pic_url,href')->find();
    //                 if(!is_null($Item)){    
    //                     $contents[$j] = array("Title"=> $Item['title'], "Description"=>$Item['description'],
    //                      "PicUrl"=>"http://".$this->getHttpHost()."/hotel_php/Public/".$Item['pic_url'], "Url" =>$Item['href']);
    //                         $j++;
    //                     }
    //                 }
                

    //             $resultStr = $this->responseNews($postObj, $contents);
    //         }
    //     //$resultStr = $this->responseText($postObj,$respond['store_id'], $flag=0);
    //         return $resultStr;
    //     }else{
    //         echo "Input something...";
    //     }
    // }

    // //关注回复
    // public function handleEvent($object)
    // {

    //     $w['flag'] = "subscribe";
    //     $w['store_id'] =$_GET['id'];
    //     $contentStr = M("htl_wechat_respond")->where($w)->field('content')->find();
    //     $content = $contentStr['content'];


    //     switch ($object->Event)
    //     {
    //         case "subscribe":

    //             break;
    //         default :
    //             $content = "Unknow Event: ".$object->Event;
    //             break;
    //     }

    //     $resultStr = $this->responseText($object, $content);
    //     return $resultStr;
    // }

    // //文本消息回复
    // public function responseText($object, $content, $flag=0)
    // {
    //     $textTpl = "<xml>
    //                 <ToUserName><![CDATA[%s]]></ToUserName>
    //                 <FromUserName><![CDATA[%s]]></FromUserName>
    //                 <CreateTime>%s</CreateTime>
    //                 <MsgType><![CDATA[text]]></MsgType>
    //                 <Content><![CDATA[%s]]></Content>
    //                 <FuncFlag>%d</FuncFlag>
    //                 </xml>";
    //     $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
    //     return $resultStr;
    // }


    // //图文消息回复
    // private function responseNews($object, $newsArray)
    // {
    //     if(!is_array($newsArray)){
    //         return;
    //     }
    //     $itemTpl = "    
    //     <item>
    //     <Title><![CDATA[%s]]></Title>
    //     <Description><![CDATA[%s]]></Description>
    //     <PicUrl><![CDATA[%s]]></PicUrl>
    //     <Url><![CDATA[%s]]></Url>
    //      </item>";
    //     $item_str = "";
    //     foreach ($newsArray as $item){
    //         $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
    //     }
    //     $newsTpl = "<xml>
    //     <ToUserName><![CDATA[%s]]></ToUserName>
    //     <FromUserName><![CDATA[%s]]></FromUserName>
    //     <CreateTime>%s</CreateTime>
    //     <MsgType><![CDATA[news]]></MsgType>
    //     <Content><![CDATA[]]></Content>
    //     <ArticleCount>%s</ArticleCount>
    //     <Articles>$item_str</Articles>
    //     </xml>";
    //     $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
    //     return $result;
    // }


    /**
    * 获取域名
    * @return 
    */
    public function getHttpHost(){
        return $_SERVER['HTTP_HOST'];
    }
}
?>