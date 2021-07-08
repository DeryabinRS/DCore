<?php
if(get_param('lang') == 'Rus')$_POST['lang'] = 'rus';
if(get_param('lang') == 'Eng')$_POST['lang'] = 'eng';
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = isset($_POST['lang']) ? get_param('lang') : 'rus';
}else{
    if(isset($_POST['lang'])) {
        if ($_SESSION['lang'] != $_POST['lang']) {
            $_SESSION['lang'] = get_param('lang');
            header("Location: ".$_SERVER["REQUEST_URI"]);
            exit;
        }
    }
}

$LANGUAGE = isset($_SESSION['lang']) ? $_SESSION['lang']: 'rus' ;
$LANGSQL = $_SESSION['lang'] == 'eng' ? '_en': '' ;
//Кнопка иностранной версии
$btn_lang = $LANGUAGE == 'rus' ? 'eng':'rus';
$frm_lang  = '<div class="lang_frm mb-2">';
$frm_lang .= '<form method="post">';
$frm_lang .= '<input type="submit" class="btn btn-light btn-sm mr-1';
if ($_SESSION['lang'] == 'rus'){$frm_lang .= ' active';}
$frm_lang .= '" name="lang"  value="Rus">';
$frm_lang .= '<input type="submit" class="btn btn-light btn-sm ';
if ($_SESSION['lang'] == 'eng'){$frm_lang .= ' active';}
$frm_lang .= '" name="lang"  value="Eng">';
$frm_lang .= '</form>';
$frm_lang .= '</div>';
