<?php
$TITLE_PAGE = isset($_PAGENAME) ? SITE_TITLE.' - '. $_PAGENAME : SITE_TITLE;
$TITLE_H1 = isset($_PAGENAME) ? '<h1 class="title_page">'.$_PAGENAME.'</h1>' : '<h1 class="title_page bg1">'.SITE_TITLE.'</h1>';
?>
<!DOCTYPE html>
<html>
<head lang="ru">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?=SITE_DESCRIPTION?>"/>
    <meta name="keywords" content="<?=SITE_KEYWIRDS?>"/>
    <link rel="icon" href="<?=SITE_URL?>/favicon.ico">
    <title><?=$TITLE_PAGE?></title>

    <meta property="og:title" content="<?=$TITLE_PAGE?>">
    <meta property="og:site_name" content="<?=SITE_TITLE?>">
    <meta property="og:url" content="<?=SITE_URL?>">
    <meta property="og:description" content="<?=SITE_DESCRIPTION?>">
    <meta property="og:image" content="<?=SITE_URL?>/img/static/banner.jpg">

    <link href="<?=SITE_URL?>/inc/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" >
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/animate.css/animate.css">
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/owlcarousel/owl.carousel.css">
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/owlcarousel/owl.theme.default.css">
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/aos/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=SITE_URL?>/assets/css/style.css" type="text/css" rel="stylesheet">

    <script src="<?=SITE_URL?>/js/jquery.js"></script>
    <script src="<?=SITE_URL?>/inc/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=SITE_URL?>/inc/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?=SITE_URL?>/inc/aos/aos.js"></script>
    <link rel="stylesheet" href="<?=SITE_URL?>/inc/fancybox/jquery.fancybox.css" />
    <script src="<?=SITE_URL?>/inc/fancybox/jquery.fancybox.js"></script>
    <script src="<?=SITE_URL?>/inc/owlcarousel/owl.carousel.js"></script>
</head>
<div class="page">
<header class="mb-4 pb-3">
    <div class="mt-3">
        <a href="<?=SITE_URL?>"><img src="<?=SITE_URL.'/img/static/logo.png'?>" class="img-fluid"></a>
    </div>
    <div class="mt-md-3 mt-2">
        <?php require($CFG->dir_com.'/SocialBtns/SocialBtns.php');?>
    </div>
    <div class="mt-md-3 mt-2 text-right">
        <?=$frm_lang;?>
    </div>
</header>
<div class="main">
<?php $TITLE_H1 ?>