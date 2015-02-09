<?php
namespace Home\Controller;

use Think\Controller;

/**
 * Class MsgTemplateController
 * @author sun
 * @time 2014.10.06 20:13
 */
class WechatMaterialController extends CommonController
{


    /****************************页面跳转和显示开始****************************/

    public function material_img_text_all()
    {
        $this->display('/Wechat/material_img_text_all');
    }

    public function material_img_all()
    {
        $this->display('/Wechat/material_img_all');
    }


    public function material_img_text_add()
    {
        $this->display('/Wechat/material_img_text_add');
    }

    public function material_img_add()
    {
        $this->display('/Wechat/material_img_add');
    }



    /****************************页面跳转和显示结束****************************/




}