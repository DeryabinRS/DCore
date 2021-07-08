<?php
require_once '../lib/setup.php';
$USER->answer();


if($_GET['usr'] == 'reg'){
    $_PAGENAME = $LANGJSON['frm_reg']['reg'][$LANGUAGE];
}elseif ($_GET['usr'] == 'act'){
    $_PAGENAME = $LANGJSON['frm_activate']['activate'][$LANGUAGE];
}elseif ($_GET['usr'] == 'auth'){
    $_PAGENAME = $LANGJSON['frm_auth']['auth'][$LANGUAGE];
}elseif ($_GET['usr'] == 'repass'){
    $_PAGENAME = $LANGJSON['frm_user']['repass'][$LANGUAGE];
}elseif ($_GET['usr'] == 'change_pass'){
    $_PAGENAME = $LANGJSON['frm_user']['change_pass'][$LANGUAGE];
}else{
    $_PAGENAME = $LANGJSON['frm_account']['name'][$LANGUAGE];
}
if($_GET['usr'] == 'reg'){
    require_once '../tpl/tpl.header.php';
    //echo $TITLE_H1;
    echo '<div class="container mt-4 mb-5 content"><div class="row">';
    echo '<div class="col-lg-3"></div><div class="col-lg-6">';
            if(REGISTRATION){require_once './reg.php';}else{echo '<h2 class="text-center">'.$_PAGENAME = $LANGJSON['frm_reg']['reg_close'][$LANGUAGE].'</h2>';}
    echo '</div><div class="col-lg-3"></div>';
    echo '</div></div>';
}elseif ($_GET['usr'] == 'act'){
    require_once '../tpl/tpl.header.php';
    //echo $TITLE_H1;
    echo '<div class="container mt-4 mb-5 content"><div class="row">';
    //Активация учетной записи
    $error_activation = $LANGJSON['frm_activate']['error'][$LANGUAGE];
    $usr_id = isset($_GET['id']) ? get_param('id') : NULL;
    $usr_code = isset($_GET['code']) ? get_param('code') : NULL;
    if($usr_id && $usr_code){
        $usr = $DB->get_record('users', ['id' => $usr_id, 'salt' => $usr_code]);
        if($usr && $usr->activate == 0){
            $act = $DB->update_record('users',['id' => $usr_id, 'activate' => 1]);
            if($act){echo '<div class="col-12"><div class="alert alert-success">'.$LANGJSON['frm_activate']['done'][$LANGUAGE].'</div></div>';}
        }else{
            echo '<div class="col-12"><div class="alert alert-danger">'.$LANGJSON['frm_activate']['done']['already'].'</div></div>';
        }
    }else{
        echo $error_activation;
    }
    echo '</div></div>';
}elseif ($_GET['usr'] == 'auth'){
    require_once '../tpl/tpl.header.php';
    if(!$USER->id) {
        //echo $TITLE_H1;
        echo '<div class="container mt-4 mb-5 content"><div class="row">';
        echo '<div class="col-lg-3"></div><div class="col-lg-6">';
        require_once './auth.php';
        echo '</div><div class="col-lg-3"></div>';
        echo '</div></div>';
    }else{
        //echo '<div class="col-12"><div class="alert alert-success">Вы уже вошли как '.$_SESSION['USER']['login'].'</div></div>';
        header('Location:'.SITE_URL);
    }
}elseif ($_GET['usr'] == 'repass'){
    require_once '../tpl/tpl.header.php';
    if(!$USER->id){
        //echo $TITLE_H1;
        echo '<div class="container mt-4 mb-5 content"><div class="row">';
        echo '<div class="col-lg-3"></div><div class="col-lg-6">';
        require_once './repass.php';
        echo '</div><div class="col-lg-3"></div>';
        echo '</div></div>';
    }else{
        header('Location:'.SITE_URL);
    }
}elseif ($_GET['usr'] == 'change_pass'){
    require_once '../tpl/tpl.header.php';
    if($USER->id){
        //echo $TITLE_H1;
        echo '<div class="container mt-4 mb-5 content"><div class="row">';
        echo '<div class="col-lg-3"></div><div class="col-lg-6">';
        require_once './change_pass.php';
        echo '</div><div class="col-lg-3"></div>';
        echo '</div></div>';
    }else{
        header('Location:'.SITE_URL);
    }
}else{
    if($USER->id){
        require_once '../tpl/tpl.header.php';
        //echo $TITLE_H1;
        echo '<div class="container mt-4 mb-5 content"><div class="row">';
        require_once './user.php';
        echo '</div></div>';
    }else{
        header('Location:' . SITE_URL . '/users/?usr=auth');
    }
}
require_once '../tpl/tpl.footer.php';
