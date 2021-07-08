<?php
$file_setup = "../lib/setup.php"; // можно любой файл, будь txt или htaccess

if(isset($_POST['chg'])){
    $error = [];
    $SITE_TITLE = get_param('SITE_TITLE');
    $REGISTRATION = isset($_POST['REGISTRATION']) ? 1 : 0;
    $SITE_MAIL = isset($_POST['SITE_MAIL']) ? get_param('SITE_MAIL') : $error[] = 'Введите основной адрес почты';
    //$SMTP_MAIL_HOST = isset($_POST['SMTP_MAIL_HOST']) ? get_param('SMTP_MAIL_HOST') : $error[] = 'Введите основной SMTP хост почты';
    //$SMTP_MAIL_PORT = isset($_POST['SMTP_MAIL_PORT']) ? get_param('SMTP_MAIL_PORT') : $error[] = 'Введите основной SMTP порт почты';
    //$SMTP_MAIL_USER = isset($_POST['SMTP_MAIL_USER']) ? get_param('SMTP_MAIL_USER') : $error[] = 'Введите основной SMTP пользователя почты';
    //$SMTP_MAIL_PASS = isset($_POST['SMTP_MAIL_PASS']) ? get_param('SMTP_MAIL_PASS') : $error[] = 'Введите основной SMTP пароль почты';
    $PHONE = isset($_POST['PHONE']) ? get_param('PHONE') : $error[] = 'Введите основной Телефон';
    if(!count($error)){
        $fileSettings = $_SERVER['DOCUMENT_ROOT'] . '/lib/setting.json';
        //dpr($fileSettings);
        if(file_exists($fileSettings)) {
            $settings = file_get_contents($fileSettings);
            $settings = json_decode($settings);
            $settings->SITE_TITLE = $SITE_TITLE;
            $settings->REGISTRATION = $REGISTRATION;
            $settings->SITE_MAIL = $SITE_MAIL;
            $settings->PHONE = $PHONE;
            //$settings->SMTP->SMTP_MAIL_HOST = $SMTP_MAIL_HOST;
            //$settings->SMTP->SMTP_MAIL_PORT = $SMTP_MAIL_PORT;
            //$settings->SMTP->SMTP_MAIL_USE = $SMTP_MAIL_USER;
            //$settings->SMTP->SMTP_MAIL_PASS = $SMTP_MAIL_PASS;
            $newJsonString = json_encode($settings);
            file_put_contents($fileSettings, $newJsonString);
        }else{
            die('Error setting file');
        }

        exit('<div class="alert alert-success">Настройки сохранены</div>');
    }else{
        foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
    }
}
//die($_SERVER['DOCUMENT_ROOT'].'/lib/setting.json');

?>
<form method="POST">
    <div class="form-group">
        <b>Название сайта</b>
        <input type="text" name="SITE_TITLE" class="form-control" maxlength="100" placeholder="Основной заголовок сайта" value="<?=SITE_TITLE?>" required>
    </div>
    <div class="form-group maxw300">
        <b>Регистрация на сайте</b>
        <label class="switch-sm">
            <input type="checkbox" class="success" name="REGISTRATION" <?php if(REGISTRATION)echo 'checked'?>>
            <span class="slider-sm round"></span>
        </label>
    </div><hr>
    <div class="form-group">
        <b>Основная почта сайта</b>
        <input type="text" name="SITE_MAIL" class="form-control" maxlength="100" placeholder="Email" value="<?=SITE_MAIL?>" required>
    </div><hr>
    <div class="form-group">
        <b>Телефон</b>
        <input type="text" name="PHONE" class="form-control" maxlength="100" placeholder="Телефон" value="<?=PHONE?>" required>
    </div><hr>
    <!--<b>Настройка SMTP</b><hr>
    <div class="form-group">
        SMTP_MAIL_HOST
        <input type="text" name="SMTP_MAIL_HOST" class="form-control" maxlength="100" placeholder="Хостинг почты" value="<?=SMTP_MAIL_HOST?>" required>
    </div>
    <div class="form-group">
        SMTP_MAIL_PORT
        <input type="text" name="SMTP_MAIL_PORT" class="form-control" maxlength="100" placeholder="Порт почты" value="<?=SMTP_MAIL_PORT?>" required>
    </div>
    <div class="form-group">
        SMTP_MAIL_USER
        <input type="text" name="SMTP_MAIL_USER" class="form-control" maxlength="100" placeholder="Пользователь почты" value="<?=SMTP_MAIL_USER?>" required>
    </div>
    <div class="form-group">
        SMTP_MAIL_PASS
        <input type="password" name="SMTP_MAIL_PASS" class="form-control" maxlength="100" placeholder="Пароль" value="<?=SMTP_MAIL_PASS?>" required>
    </div>-->
    <input type="submit" class="btn btn-success" name="chg" value="Сохранить настройки">
</form>
