<?php
/**
 * Author: edvard
 * @time 2014.9.24 9:24
 */

include VENDOR_PATH . '/ImageCache/ImageCache.php';

/**
 * 文件上传
 * @param  string $save_path 保存路径
 * @return array
 * @param $oldPath 旧图片路径没有则输入null
 */
function upload($save_path, $oldPath, $size = -1, $rule = 'uniqid')
{
    // 设置存储路径
    $dirname = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/' . $save_path . '/';
    // 建立存储文件夹，如果不存在则建立
    if (!file_exists($dirname)) {
        mkdir($dirname, 0777, true);
    }
    $upload = new \Org\Util\UploadFile();
    // 设置附件上传类型
    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg', 'ipa', 'apk');
    // 文件大小
    $upload->maxSize = $size;
    //设置附件上传目录
    $upload->savePath = $dirname;
    // 建立存储文件夹，如果不存在则建立
    if (!file_exists($upload->savePath)) {
        mkdir($upload->savePath, 0777, true);
    }
    // 上传文件名唯一
    $upload->saveRule = $rule;
    //文件上传超时
    if (!$upload->upload()) {
        //捕获上传异常
        return array('status' => false, 'info' => $upload->getErrorMsg());
    }

    // 得到上传的文件路径
    $info = $upload->getUploadFileInfo();
    //删除旧图片
    if ($oldPath != null) {
        delete_old_photo($oldPath);
    }
    foreach ($info as $key => $item) {
        $info[$key]['path'] = $save_path . $item['savename'];

    }
    $savePath = '/Uploads/' . $save_path . '/' . $info[$key]['savename'];
    //return array('status' => true, 'info' => $info);
    return $savePath;
}


/**
 * 多文件上传,按照用户id分文件夹
 * @param  string $storeId 用户id
 * @param $size 3145728为3M
 * @param $address 图片保存的文件夹
 * @return array
 */
function uploadMultiImage($storeId, $size = -1, $address, $rule = 'uniqid')
{
    // 取得时间戳
    //$date = date('Ym', time());

    // 设置存储路径
//    $dirname = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/Uploads/' . $storeId . '/image/' . $address;
    writeSyslog( "test path : "  . $_SERVER['DOCUMENT_ROOT'] . __ROOT__);
    $dirname =  'Uploads/' . $storeId . '/image/' . $address;
    // 建立存储文件夹，如果不存在则建立
    if (!file_exists($dirname)) {
        mkdir($dirname, 0777, true);
    }
    // 实例化上传类对象
    $upload = new \Org\Util\UploadFile();
    // 文件大小
    $upload->maxSize = $size;
    // 上传文件名唯一
    $upload->saveRule = $rule;
    // 限制上传的类型
    $upload->allowExts = array('jpg', 'png', 'jpeg', 'bmp', 'gif');

    // 设置上传的路径
    $upload->savePath = 'Uploads/' . $storeId . '/image/' . $address . '/';

    // 上传图片并判断是否上传成功
    if (!$upload->upload()) {
        return -1;
    } else {
        // 设置缓存目录
        // $imageCache = new \ImageCache();
        // $imageCache->cached_image_directory = $upload->savePath;

        // 获得保存路径
        $info = $upload->getUploadFileInfo();
        foreach ($info as $key => $value) {
            $path = 'Uploads/' . $storeId . '/image/' . $address . '/' . $info[$key]['savename'];
            // $imageCache->cache($path);
            // $savePath[$key] = $imageCache->cached_filename;
            $savePath[$key] = $path;
        }

        return $savePath;
    }
}

/**
 * 裁剪图片，截取后的图片保存在原图片的当天路径下，名称为原原图片名称加上"thumb-", "small-", "midlle-"等 前缀
 * @param $imgPath 需要截取的图片的路径
 * @param $oldPath 旧图片路径没有则输入null
 * @return int|string 如果失败则返回-1，否则返回截取后的图片的路径
 */
function reduceImage($imgPath, $oldPath)
{
    $img_size_array = require "./Common/Conf/img_size_config.php";
    $lastCharPosition = strripos($imgPath, '/');
    $preImgPath = substr($imgPath, 0, $lastCharPosition + 1);
    $imageName = substr($imgPath, $lastCharPosition + 1, strlen($imgPath));

    $image = new \Think\Image();
    // $imageCache = new \ImageCache();
    // $imageCache->cached_image_directory = dirname($imageName);
    $image->open($imgPath);

    //生成图像原尺寸
    $result = $image->thumb(null, null);
    $newImg = $preImgPath . $imageName;
    $result->save($newImg);
    // $imageCache->cache($newImg);

    $image->open($imgPath);
    //生成图像为720*720的缩略图
    $result = $image->thumb($img_size_array['large_size'], $img_size_array['large_size']);
    $newImg_large = $preImgPath . 'large-' . $imageName;
    $result->save($newImg_large);
    // $imageCache->cache($newImg_large);

    $image->open($imgPath);
    //生成图像为500*500的缩略图
    $result = $image->thumb($img_size_array['middle_size'], $img_size_array['middle_size']);
    $newImg_middle = $preImgPath . 'middle-' . $imageName;
    $result->save($newImg_middle);
    // $imageCache->cache($newImg_middle);

    $image->open($imgPath);
    //生成图像为280*280的缩略图
    $result = $image->thumb($img_size_array['cover_size'], $img_size_array['cover_size']);
    $newImg_cover = $preImgPath . 'cover-' . $imageName;
    $result->save($newImg_cover);
    // $imageCache->cache($newImg_cover);

    $image->open($imgPath);
    //生成图像为200*200的缩略图
    $result = $image->thumb($img_size_array['small_size'], $img_size_array['small_size']);
    $newImg_small = $preImgPath . 'small-' . $imageName;
    $result->save($newImg_small);
    // $imageCache->cache($newImg_small);

    $image->open($imgPath);
    //生成图像的100*100的缩略图
    $result = $image->thumb($img_size_array['thumb_size'], $img_size_array['thumb_size']);
    $newImg_thumb = $preImgPath . 'thumb-' . $imageName;
    $result->save($newImg_thumb);
    // $imageCache->cache($newImg_thumb);

    /*//居中裁剪
    $result_list = $image->thumb($width_square, $height_square ,\Think\Image::IMAGE_THUMB_CENTER);
    if ($result === false) {
        return -1;
    }
    //根据传进来的参数进行裁剪

    $newImg_square = $preImgPath . 'Square-' . $imageName;

    $result_list->save($newImg_square);*/

    //删除中间图片
    //unlink($imgPath);
    writeSyslog($oldPath);
    if ($oldPath != null) {
        delete_old_photo($oldPath);
    }

    if ($result === false) {
        return -1;
    } else {
        return $newImg;
    }
}

/**
 * 修改图片后删除之前的图片
 */
function delete_old_photo($oldPath)
{
    $lastCharPosition = strripos($oldPath, '/');
    $preImgPath = substr($oldPath, 0, $lastCharPosition + 1);
    $imageName = substr($oldPath, $lastCharPosition + 1, strlen($oldPath));
    $OldImg_thumb = $preImgPath . 'thumb-' . $imageName;
    $OldImg_small = $preImgPath . 'small-' . $imageName;
    $OldImg_cover = $preImgPath . 'cover-' . $imageName;
    $OldImg_middle = $preImgPath . 'middle-' . $imageName;
    $OldImg_large = $preImgPath . 'large-' . $imageName;
    unlink($oldPath);
    unlink($OldImg_thumb);
    unlink($OldImg_small);
    unlink($OldImg_cover);
    unlink($OldImg_middle);
    unlink($OldImg_large);
}

/**
 * 取得最新插入数据的ID
 * @param  $model 一个model
 * @return id
 */
function getFirstId($model)
{
    $id = $model->order('id desc')->limit(1)->select();; //获取表中最后一条数据的id
    return $id[0]["id"];
}

/**
 * 获得随机数
 * @param  $length 随机长度
 * @return string
 */
function randomkeys($length)
{
    $returnStr = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $returnStr .= $pattern{mt_rand(0, 61)}; //生成php随机数
    }
    return $returnStr;
}

/**
 * 调试用，输出变量
 * @param $array
 */
function p($array)
{
    dump($array, 1, '<pre>', 0);
}

/**
 * 发送一个http请求
 * @param $url
 * @param $param
 * @param string $httpMethod
 * @return bool|mixed
 */
function makeRequest($url, $param, $httpMethod = 'GET')
{
    $oCurl = curl_init();
    if ($httpMethod == 'GET') {
        curl_setopt($oCurl, CURLOPT_URL, $url . "?" . http_build_query($param));
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    } else {
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($param));
    }
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return FALSE;
    }
}

/**
 * 中英文混合字符串截取
 * @param $string
 * @param $length
 * @param bool $append
 * @return array|string
 */
function sysSubStr($string, $length, $append = false)
{
    if (strlen($string) <= $length) {
        return $string;
    } else {
        $i = 0;
        while ($i < $length) {
            $stringTMP = substr($string, $i, 1);
            if (ord($stringTMP) >= 224) {
                $stringTMP = substr($string, $i, 3);
                $i = $i + 3;
            } elseif (ord($stringTMP) >= 192) {
                $stringTMP = substr($string, $i, 2);
                $i = $i + 2;
            } else {
                $i = $i + 1;
            }
            $stringLast[] = $stringTMP;
        }
        $stringLast = implode("", $stringLast);
        if ($append) {
            $stringLast .= "...";
        }
        return $stringLast;
    }
}

/**
 * 发送提醒短信(默认使用首易的接口)
 * @param $msg
 * @param $phoneNums
 *              多个号码时用";"号分开
 * @return interger
 *              若大于0则为成功的数量, 其余为发送失败
 */

function sendMessage($msg, $phoneNums)
{
    $msgSendService = D("MsgSend", "Service");
    $numberArray = explode(';', $phoneNums);
    $result = $msgSendService->sendMessage($msg, $numberArray, "");
    return $result;
}

/**
 * @param $msg 内容
 * @param $filename 文件名
 * 注:日志将会写入根目录下的 log文件夹内
 */
function writeSyslog($msg, $filename = "syslog",$format = 0)
{
    if($format){
        $msg = json_encode($msg);
    }
//    $filename = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/log/' . date('Ymd') . '_' . $filename .'.txt';
    $filename = 'log/' . date('Ymd') . '_' . $filename .'.txt';
    // 建立存储文件夹，如果不存在则建立
    if (!file_exists($filename)) {
        mkdir('log', 0777, true);
    }
    file_put_contents($filename, "Time " . date('Y-m-d H:i:s') ."\r\n" . CONTROLLER_NAME." ".$msg . "\r\n", FILE_APPEND);
}


/**
 * 对二维数组进行排序
 * @param $arr 数组
 * @param $keys 数组要排序的字段
 * @param string $type 排序的类型 desc为默认
 * @return array
 */
function array_sort($arr, $keys, $type = 'desc')
{
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}


/**
 * 根据图片链接返回合适的图片尺寸
 * @param $imgPath 图片地址
 * @param string $imgNamePredix 图片前缀
 * @return mixed
 */
function getApiReducedImgPath($imgPath, $imgNamePredix="")
{
    if(!empty($imgNamePredix)){
        $lastIndex = strrpos($imgPath,"/");
        $newImgPath = substr_replace($imgPath, $imgNamePredix, $lastIndex + 1, 0);
        //如果文件存在且可用
        if(is_file($newImgPath)){
            return $newImgPath;
        }
    }

    return $imgPath;
}

function scanfDir($dir) {
    $ds = DIRECTORY_SEPARATOR;
    $data = array();

    if(is_dir($dir)) {
        if($handle = opendir($dir)) {
            while(($file = readdir($handle)) !== false) {
                if($file != '.' && $file != '..' && $file != '.DS_Store') {
                    $tmpFile = $file;
                    if(is_dir("{$dir}/{$file}")) {
                        $data[$tmpFile] = scanfDir("{$dir}{$ds}{$file}");
                    } else {
                        $data[] = $tmpFile;
                    }
                }
            }

            closedir($handle);
            return $data;
        }
    }

    return false;
}

function scanfRenameDir($dir) {
    $ds = DIRECTORY_SEPARATOR;
    $data = array();

    if(is_dir($dir)) {
        if($handle = opendir($dir)) {
            while(($file = readdir($handle)) !== false) {
                if($file != '.' && $file != '..' && $file != '.DS_Store') {
                    $tmpFile = iconv("GB2312//IGNORE", "UTF-8", $file);
                    if(is_dir("{$dir}/{$file}")) {
                        $data[$tmpFile] = scanfDir("{$dir}{$ds}{$file}");
                    } else {
                        $data[] = $tmpFile;
                    }

                    rename("{$dir}/{$file}", "{$dir}/{$tmpFile}");
                }
            }

            closedir($handle);
            return $data;
        }
    }

    return false;
}

function datetime($format = 'Y-m-d H:i:s') {
    return date($format, time());
}

function getStoreTypeName($typeId) {
    // 数据从t_store_type中取出
    switch ($typeId) {
        case 4:
            return '旅行社';

        case 5:
            return '酒店';

        case 6:
            return '景点';

        case 8:
            return '电影';

        case 9:
            return '餐饮';

        case 10:
            return '运动';

        case 11:
            return '购物';

        case 12:
            return '娱乐';
    }

    return '综合行业';
}

function getStoreTypeId($typeName) {
    $typeName = trim($typeName);
    switch ($typeName) {
        case '旅行社':
            return 4;

        case '酒店':
            return 5;

        case '景点':
            return 6;

        case '电影':
            return 8;

        case '餐饮':
            return 9;

        case '运动':
            return 10;

        case '购物':
            return 11;

        case '娱乐':
            return 12;
    }

    return 7;
}
