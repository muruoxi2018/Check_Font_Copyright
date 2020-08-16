<?php

/*
 * Copyright (C) www.muruoxi.com
 */

!defined('DEBUG') and define('DEBUG', 1);   // 0: 线上模式; 1: 调试模式;
define('APP_PATH', dirname(__FILE__) . '/');  // __DIR__
!defined('XIUNOPHP_PATH') and define('XIUNOPHP_PATH', APP_PATH . 'xiunophp/');

$conf = (@include APP_PATH . 'conf.php') or exit('<script>window.location="https://www.muruoxi.com/"</script>');

if (DEBUG > 0) {
    include XIUNOPHP_PATH . 'xiunophp.php';
} else {
    include XIUNOPHP_PATH . 'xiunophp.min.php';
}

// 转换为绝对路径，防止被包含时出错。
substr($conf['log_path'], 0, 2) == './' and $conf['log_path'] = APP_PATH . $conf['log_path'];
substr($conf['tmp_path'], 0, 2) == './' and $conf['tmp_path'] = APP_PATH . $conf['tmp_path'];
substr($conf['upload_path'], 0, 2) == './' and $conf['upload_path'] = APP_PATH . $conf['upload_path'];

$_SERVER['conf'] = $conf;

//测试数据库连接 / try to connect database
//db_connect() or exit($errstr);
// $_SERVER['db'] = $db = db_new($conf['db']);
// 此处可能报错
// $r = db_connect($db);
// print_r($r);

// 下面开始处理事物
// -1 意料外的错误
// 0 意料中的错误
// 1+ 各种识别中的状态

//设置返回类型为json
header('content-type:application/json;charset=utf-8');

$action = param('action');
switch ($action) {
    case 'uploadFont':
        uploadFont();
        break;
    case 'addFont':
        addFont();
        break;
    case 'getAllFontsList':
        getAllFontsList();
        break;
    case 'getFreeFontsList':
        getFreeFontsList();
        break;
    case 'getStatistics':
        getStatistics();
        break;
    case 'searchFonts':
        searchFonts();
        break;
    default:
        xn_message(-1, '未定义的操作');
}

/**
 * 入库一个字体，需要审核
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function addFont()
{
    $fonts = [];

    is_null(param('cn_name')) ? xn_message(0, '请输入字体的中文名') : $fonts['cn_name'] = param('cn_name');
    is_null(param('en_name')) ? xn_message(0, '请输入字体的英文名') : $fonts['en_name'] = param('en_name');
    is_null(param('is_vip')) ? xn_message(0, '请选择字体的类型') : $fonts['is_vip'] = param('is_vip');
    is_null(param('attachment')) ? xn_message(0, '请上传字体') : $fonts['attachment'] = str_replace($_SERVER['conf']['downurl'], '', param('attachment'));

    $result = db_find_one('fonts', array('en_name' => $fonts['en_name']));

    $result ? xn_message(0, '这个字体已经入库了，请不要重复提交') : db_insert('fonts', $fonts);

    xn_message(1, '提交成功');
}

/**
 * 拉取所有字体列表（只有审核过的会被拉取）
 * 至少要有一个审核通过的数据哦
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function getAllFontsList()
{
    $result = db_find('fonts', array('verify' => 1), null, 1, 10000, null, array('cn_name', 'en_name', 'is_vip', 'attachment'));

    foreach ($result as &$arr) {

        $arr['attachment'] = atod($arr['attachment']);
    }

    $result ? xn_message(1, array('fontslist' => $result)) : xn_message(-1, '数据库读取失败');
}

/**
 * 拉取统计数据，返回两个数据
 * count 入库的字体总数
 * verify 审核通过的字体总数
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function getStatistics()
{
    $result = [];
    $result['count'] = db_count('fonts');
    $result['verify'] = db_count('fonts', array('verify' => 1));

    xn_message(1, $result);
}

/**
 * 拉取免费字体列表
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function getFreeFontsList()
{
    $result = db_find('fonts', array('is_vip' => 0, 'verify' => 1));
    $result = arrlist_keep_keys($result, array('cn_name', 'en_name', 'is_vip', 'attachment'));
    xn_message(1, array('fontslist' => $result));
}

/**
 * 上传字体
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function uploadFont()
{

    $fileInfo = $_FILES['file']; //获取提交过来的文件
    $filePath = $_FILES['file']['tmp_name']; //获取文件临时目录
    $name = iconv('utf-8', 'gb2312', $fileInfo['name']); //避免中文乱码

    if (file_ext(file_name($name)) == 'zip') {
        global $time;
        $newpath = $_SERVER['conf']['upload_path'] . date('Y-n-j', $time) . '/';
        $newname = $newpath . xn_rand(8) . '.zip';
        xn_mkdir($newpath, null, true);
        move_uploaded_file($filePath, $newname);
        xn_message(1, array(
            'download' => str_replace($_SERVER['conf']['upload_path'], $_SERVER['conf']['downurl'], $newname)
        ));
    } else {
        xn_message(0, '文件类型错误');
    }
}

/**
 * 搜索字体
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function searchFonts()
{
    $font = param('font');
    if (!$font) {
        xn_message(0, '请输入字体的名称');
    }
    $en_result = db_find('fonts', array('en_name' => array('LIKE' => $font), 'verify' => 1));
    $cn_result = db_find('fonts', array('cn_name' => array('LIKE' => $font), 'verify' => 1));
    $result = $en_result + $cn_result;
    $result = arrlist_keep_keys($result, array('cn_name', 'en_name', 'is_vip', 'attachment'));
    foreach ($result as &$arr) {
        $arr['attachment'] = $arr['is_vip'] == 2 ? '#' : atod($arr['attachment']);
    }
    if (!count($result)) {
        xn_message(0, '尚未收录该字体信息');
    } else {
        xn_message(1, array('fonts' => $result));
    }
}

/**
 * 将本地附件的地址转为可下载的地址，网络地址不会变动
 * @param [string] $attachment
 * @return void
 * @Description
 * @Author MuRuoxi
 * @DateTime 2020-08-08
 */
function atod($attachment)
{
    $downurl = substr($attachment, 0, 8) == 'https://' || substr($attachment, 0, 7) == 'http://' ? $attachment : $_SERVER['conf']['downurl'] . $attachment;
    return $downurl;
}
