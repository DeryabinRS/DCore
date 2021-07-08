<?php
require_once '../lib/setup.php';
$USER->answer();
require_once 'header.php';
?>

<?php
if(!$USER->id){
?>
    <div style="background: #444;height: 100vh;margin-top: -60px">
<div class="container">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <div class="auth_box"><img src="<?=SITE_URL?>/img/static/logo.png" class="img-fluid mb-3">
            <?php require_once '../users/auth.php';?></div>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
    </div>
<?php
}elseif($USER->id && $_SESSION['USER']['status']){
    $_PAGE = $_GET['page'];
    if(!$_PAGE) $_PAGENAME = 'Главная';
    require_once 'menu.php';

    $title = isset($_PAGENAME) ? $CFG->title.' | '. $_PAGENAME : $CFG->title;
    $title_h1 = isset($_PAGENAME) ? '<div class="title_page">'.$_PAGENAME.'</div>' : '<div class="title_page">'.$CFG->title.'</div>';

    ?>
    <?=$title_h1?>
    <?php
    if(isset($_PAGE)){
        if(getAccess($MenuNavArray,$_PAGE,$_SESSION['USER']['status'])){
        $indx_file = $_PAGE.'/index.php';
        if(is_file($indx_file)){
            require_once ($indx_file);?>
            <script type="text/javascript">
                function confirmDelete() {
                    if (confirm("Вы подтверждаете удаление?")) {
                        return true;
                    } else {
                        return false;
                    }
                }
            </script>
        <?php }
        }else{
            echo '<div class="alert alert-danger">У вас нет доступа к данному разделу</div>';
        }
    }else{ ?>
        <div class="row">
            <div class="col-lg-4">
                <h5>Пользователи</h5>

                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Всего пользователей
                        <span class="badge badge-secondary badge-pill">
                            <?=count($DB->get_records('users',[],false,'id'))?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Активированных
                        <span class="badge badge-secondary badge-pill">
                            <?=count($DB->get_records('users',['activate' => 1],false,'id'))?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Заблокированных
                        <span class="badge badge-secondary badge-pill">
                            <?=count($DB->get_records('users',['block' => 1],false,'id'))?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Пользователей онлайн
                        <span class="badge badge-secondary badge-pill">
                            <?=count($DB->get_records_sql('SELECT id FROM '.$CFG->db['prefix'].'users WHERE time_online >= '.($_SERVER['REQUEST_TIME'] - 180)))?>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4">

            </div>
        </div>
    <?php }
    require_once 'footer.php';
}elseif ($USER->id && !$_SESSION['USER']['status']){?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">Нет прав доступа к данным</div>
                <a href="<?=SITE_URL?>" class="form-inline ml-auto mt-2 mt-md-0 btn btn-success"><i class="fa fa-sign-out"></i> Вернуться на сайт</a>
                <a href="?auth=exit" class="form-inline ml-auto mt-2 mt-md-0 btn btn-secondary"><i class="fa fa-sign-out"></i> Выход</a>
            </div>
        </div>
    </div>
<?php } ?>