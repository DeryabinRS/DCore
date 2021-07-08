<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once './lib/setup.php';

$_PAGENAME = null;
$_PAGE_DESC = '';
$_CONTENT = '';

$USER->answer();

$routed_file = $rURL->classname; // Получаем имя файла для подключения через require()
pr($_PAGENAME);

if($routed_file == 'main.php'){
    require_once './tpl/tpl.header.php';
    require('./pgs/'.$routed_file);
}else{
    require('./pgs/'.$routed_file);
    require_once './tpl/tpl.header.php';
    echo $TITLE_H1;
    echo $_CONTENT;
}

require_once './tpl/tpl.footer.php';
