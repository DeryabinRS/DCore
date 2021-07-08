<?php
if(isset($_POST['repass'])){
    $error = [];
    $email = isset($_POST['email']) ? get_param('email') : $error[] = 'Введите email';
    if(!checkEmail($email)){
        $error[] = 'Некорректный Email';
    }else{
        $usr = $DB->get_record('users',['email' => $email],('id, salt, email'));
        //dpr($usr);
        if(!$usr){$error[] = 'Пользователя с '.$email.' не существует';}
    }
    if(!$error){
        $topic = "Сообщение с сайта ". SITE_TITLE;
        $message = '<h2>'.$LANGJSON['frm_user']['repass_post_1'][$LANGUAGE].', <a href="'.SITE_URL.'/users/?usr=repass&id='.$usr->id.'&code='.$usr->salt.'">'.$LANGJSON['frm_user']['repass_post_2'][$LANGUAGE].'</a></h2>';
        //dpr($email);
        sendMail($email, $topic, $message);
        echo '<div class="alert alert-success">'.$LANGJSON['frm_user']['repass_mess'][$LANGUAGE].'</div>';
        exit;
    }else{foreach ($error as $err){echo '<div class="alert alert-danger">'.$err.'</div>';}}
}elseif (isset($_POST['setpass'])){
    $error = [];
    $id = get_param('id');
    $pass = get_param('pass');
    $pass_cnf = get_param('pass_cnf');
    if(empty($pass) || strlen($pass)<6 || strlen($pass)>32){$error[] ='Пароль должен содержать от 6 до 32 символов';}
    if($pass != $pass_cnf){$error[] ='Пароли не совпадают';}
    if(!preg_match("/^[a-zA-Z0-9]+$/",$pass)){$error[] = "Пароль может состоять только из букв английского алфавита и цифр";}
    if(!$error){
        $hpass = $USER->hashPass($pass);
        //dpr($hpass);
        $DB->update_record('users',['id' => $id, 'pass' => $hpass['pass'], 'salt' => $hpass['salt']]);
        echo '<div class="alert alert-success">'.$LANGJSON['frm_user']['repass_ok'][$LANGUAGE].'</div>';
        exit;
    }else{foreach ($error as $err){echo '<div class="alert alert-danger">'.$err.'</div>';}}
}
$get_code = isset($_GET['code'])? get_param('code'): 0;
$get_id = isset($_GET['id'])? get_param('id'): 0;
//dpr($get_code);
if($get_code && $get_id) { ?>
    <form method="POST" class="autform">
        <div class="form-group">
            <label for="pass"><?=$LANGJSON['frm_user']['new_pass'][$LANGUAGE]?></label>
            <input type="password" name="pass" id="pass" class="form-control" maxlength="50" placeholder="<?=$LANGJSON['frm_user']['pass'][$LANGUAGE]?>" required>
        </div>
        <div class="form-group">
            <label for="pass_cnf"><?=$LANGJSON['frm_user']['repeat_pass'][$LANGUAGE]?></label>
            <input type="password" name="pass_cnf" id="pass_cnf" class="form-control" maxlength="50" placeholder="<?=$LANGJSON['frm_user']['pass'][$LANGUAGE]?>" required>
        </div>
        <input type="submit" class="btn btn-success" name="setpass" value="<?=$LANGJSON['frm_user']['save'][$LANGUAGE]?>">
    </form>
<?php }else{ ?>
    <form method="POST" class="autform">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input type="text" name="email" class="form-control" maxlength="100" placeholder="Email" required>
            </div>
        </div>
        <input type="submit" class="btn btn-custom-2" name="repass" value="<?=$LANGJSON['frm_user']['repass'][$LANGUAGE]?>">
    </form>
<?php }

