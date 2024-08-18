<?php

define('DCORE', true);
//Основные настройки сайта
define('SITE_URL', "http://dcore.local");
define('SITE_URL_ADM', SITE_URL."/dc-admin");
define('SMTP_MAIL_HOST', "ssl://smtp.mail.ru");
define('SMTP_MAIL_PORT', "465");
define('SMTP_MAIL_USER', "test@test.ru");
define('SMTP_MAIL_PASS', "");
//Настройки Google капча v2 (flag)
define('RC_KEY',"");
define('RC_S_KEY',"");

ini_set('default_chatset','utf8mb4');
date_default_timezone_set('Etc/GMT-7');
unset($CFG);
$CFG = new stdClass();
$CFG->http_root = $_SERVER['DOCUMENT_ROOT'];

if(file_exists($CFG->http_root.'/lib/setting.json')){
    $settings = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/lib/setting.json');
    $settings = json_decode($settings, true);
    define('SITE_TITLE', $settings['SITE_TITLE']);
    define('SITE_DESCRIPTION', $settings['SITE_DESCRIPTION']);
    define('SITE_KEYWIRDS', $settings['SITE_KEYWIRDS']);
    define('SITE_MAIL', $settings['SITE_MAIL']);
    define('SITE_CORE', $settings['SITE_CORE']);
    define('BANNER_TOP_W', $settings['IMG_SIZE']['BANNER_TOP_W']);
    define('BANNER_TOP_H', $settings['IMG_SIZE']['BANNER_TOP_H']);
    define('EVENTS_IMG_SIZE', $settings['IMG_SIZE']['EVENTS_IMG_SIZE']);
    define('EVENTS_IMG_THUMB_SIZE', $settings['IMG_SIZE']['EVENTS_IMG_THUMB_SIZE']);
    define('REGISTRATION', $settings['REGISTRATION']);
    define('ADDRESS', $settings['ADDRESS']);
    define('PHONE', $settings['PHONE']);
    define('EMAIL_ORG', $settings['EMAIL_ORG']);
}else{
    die('Error setting file');
}
if(file_exists($CFG->http_root.'/assets/lang/language.json')){
    $LANGJSON = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/lang/language.json');
    $LANGJSON = json_decode($LANGJSON, true);
    //print_r($LANGJSON);
}else{
    die('Error language file');
}

$CFG->dir_lib  = $CFG->http_root.'/lib';
$CFG->dir_cls  = $CFG->http_root.'/cls';
$CFG->dir_upl  = $CFG->http_root.'/uploads';
$CFG->dir_com  = $CFG->http_root.'/components';
$CFG->dir_inc = $CFG->http_root.'/inc';
$CFG->dir_img = $CFG->http_root.'/img';
$CFG->dir_tpl = $CFG->http_root.'/tpl';
$CFG->db = [
    'host'   => 'MySQL-8.2',
    'user'   => 'root',
    'pass'   => '',
    'dbname' => 'dcore',
    'charset'=> 'utf8',
    'debug'  => true,
    'port'   => 0,
    'prefix' => 'dcore_',
];
require($CFG->dir_lib.'/debug.php');
require($CFG->dir_lib.'/lib.php');
//------------------------------------------------
/*if(!file_exists($CFG->datadir)){
    if(!@mkdir($CFG->datadir)){
        die('mkdir($CFG->datadir.\'/uploads\'))');
    }
}*/
//------------------------------------------------

require($CFG->dir_cls.'/db.class.php');
$DB = new DCore_DB($CFG->db);
require($CFG->dir_cls.'/user.class.php');
$USER = new DCore_User();
require($CFG->dir_cls.'/file.class.php');
require($CFG->dir_cls.'/router.class.php');
$rURL = new uSitemap();

//unset($_SESSION['lang']);
//LANGUAGE
require_once ($CFG->dir_com.'/Lang/Lang.php');