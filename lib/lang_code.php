<?php
//LANGUAGE
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
if($_SESSION['lang'] == 'rus'){
$frm_lang = '<div class="lang_frm">
    <form method="post"><input type="submit" class="lang_btn active" name="lang"  value="Rus"></form>
    <form method="post"><input type="submit" class="lang_btn" name="lang"  value="Eng"></form>
</div>';
}else {
$frm_lang = '<div class="lang_frm">
    <form method="post"><input type="submit" class="lang_btn" name="lang"  value="Rus"></form>
    <form method="post"><input type="submit" class="lang_btn active" name="lang"  value="Eng"></form>
</div>';
}