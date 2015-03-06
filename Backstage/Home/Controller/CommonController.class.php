<?php
namespace Home\Controller;

use Think\Controller;

/**
  * CommonController
  * @author qax
  */ 

class CommonController extends Controller {

    /**
     * 全部Controller执行之前会执行的方法
     * 1、抛出菜单数据
     * 2、添加登陆验证
     * 
     */
    public function _initialize()
    {
        //检测是否登录
        //$this->loginOrNot();

        //如果为超级管理员，则输出全部菜单
        // if (session(C('SUPERORNOT')) == true)
        //     $this->throwMenu();
        // else
        //     $this->getMenuByUserId();

        // $this->getCurrMenu();
        //扔出当前登录用户的名称
        // $this->assign('LoginUser', session(C('LOGINNAME')));
    }

    /**
     * 判断是否有用户登录，没有则跳去登录界面
     */
    protected function loginOrNot()
    {
        if (session(C('LOGINNAME')) == null) {
            $this->redirect('Login/index');
        }else{
            //保证同一个账户不能同时登录
            $loginname = session(C('LOGINNAME'));
            $clientSid = cookie('PHPSESSID');
            $userModel = M('t_sys_user');
            $loginUser = $userModel->where(array('Loginname' => $loginname))->find();
            $dbSid = $loginUser['SessionID'];
            if($clientSid != $dbSid)
                $this->redirect('Login/index');
        }
    }

    /**
     * 在执行数据之前会调用的方法，以便抛出导航栏数据
     */
    // public function _before_index(){
    // $this->getCurrMenu();
    //}

    /**
     * 获得当前菜单
     */
    public function getCurrMenu()
    {
        $navigation = array();
        $menuId = $_GET['nav_id'] ? $_GET['nav_id'] : session('menuId');
        //保存menuId值到session中去
        session('menuId', $menuId);

        $menuModel = M('T_sys_menu');
        $item = $menuModel->where(array('ID' => $menuId))->find();
        if ($item['Parent_id'] == -1) {
            $navigation[0] = $item['Name'];
            $navigation[1] = '';
            $navigation_id[0] = $item['ID'];
            $navigation_id[1] = '-1';
        } else {
            $parentId = $item['Parent_id'];
            $parentItem = $menuModel->where(array('ID' => $parentId))->find();
            $navigation[0] = $parentItem['Name'];
            $navigation[1] = $item['Name'];
            $navigation_id[0] = $item['ID'];
            $navigation_id[1] = $parentItem['ID'];
        }

        //把当前页码保存到session中
        $p = $_GET['p'];
        session(C('PAGE'), $p);
        //保存结束

        /**
         * 抛出导航栏数据
         */
        $this->assign('navigation', $navigation);
        //用于控制-当前活动菜单 样式
        $this->assign('navigation_id', $navigation_id);
    }

    /**
     * 抛出菜单栏数据
     */
    protected function throwMenu()
    {
        /**
         * 抛出菜单栏数据
         */
        $menusService = D('menu', 'Service');
        $result = $menusService->getMenus(false);

        /**
         * 根据用户的id动态生成菜单栏数据
         */

        $this->assign('slideMenus', $result['data']);
    }

    /**
     * 根据登录id取菜单
     */
    protected function getMenuByUserId()
    {
        /**
         * 抛出菜单栏数据
         */
        $menusService = D('menu', 'Service');
        $result = $menusService->getMenuByUserId();

        /**
         * 根据用户的id动态生成菜单栏数据
         */
        $this->assign('slideMenus', $result);
    }

    /**
     * 后台管理
     * --左边菜单列表
     */
    protected function get_manage_menus()
    {
        $menusService = D('menu', 'Service');
        $result = $menusService->getMenus(false);
        $this->assign('slideMenus', $result['data']);
    }

    /**
     * 传入结果字符串和跳转的url
     * @param $result
     * @param $url
     */
    protected function jump($result, $url)
    {
        if (false == $result['status']) {
            $this->error($result['data']['error'], $url);
        } else {
            $this->success($result['data']['success'], $url);
        }
    }

    /**
     * 跳转至index界面
     * 当跳转到当前模块下的index.html界面时调用该方法
     */
    protected function jumpIndex($result)
    {
        if (false == $result['status']) {
            $this->error($result['data']['error'], $this->indexUrl());
        } else {
            $this->success($result['data']['success'], $this->indexUrl());
        }
    }

    /**
     * 动态拼接跳转到index.html的url
     * @return string
     */
    protected function indexUrl()
    {
        return U(CONTROLLER_NAME . '/index', array('id' => session('menuId'), 'p' => session('PAGE')));
    }


    /**
     * 扔出当前返回到index页面的url
     */
    protected function throwIndexUrl()
    {
        $this->assign('indexUrl', $this->indexUrl());
    }

    /**
     * 扔出共同的分页及列表数据
     * @param $data
     */
    protected function throwCommonData($data)
    {
        $this->assign('list', $data['list']);
        $this->assign('page', $data['page']);
        $this->assign('js', $data['js']);
    }

    /**
     * @param $msg 内容
     * @param $filename 文件名
     */
    function writeLog($msg, $filename = "syslog.txt")
    {
        $filename = 'log/' . date('Ymd') . '_' . $filename;
        file_put_contents($filename, "Time " . date('Y-m-d H:i:s') . $msg . "\r\n", FILE_APPEND);
    }
}
 ?>