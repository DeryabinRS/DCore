<?php
if(isset($_POST['change_pass'])){
    if($USER->id){
        $error = [];
        $id = $USER->id;
        $pass = get_param('pass');
        $pass_cnf = get_param('pass_cnf');
        if(empty($pass) || strlen($pass)<8 || strlen($pass)>32){$error[] ='Пароль должен содержать от 8 до 32 символов';}
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
}
?>
<form method="POST" class="change_pass">
    <div class="form-group">
        <label for="pass"><?=$LANGJSON['frm_user']['new_pass'][$LANGUAGE]?></label>
        <input type="password" name="pass" id="pass" class="form-control" maxlength="50" placeholder="<?=$LANGJSON['frm_user']['pass'][$LANGUAGE]?>" required>
    </div>
    <div class="form-group">
        <label for="pass_cnf"><?=$LANGJSON['frm_user']['repeat_pass'][$LANGUAGE]?></label>
        <input type="password" name="pass_cnf" id="pass_cnf" class="form-control" maxlength="50" placeholder="<?=$LANGJSON['frm_user']['pass'][$LANGUAGE]?>" required>
    </div>
    <input type="submit" class="btn btn-success" name="change_pass" value="<?=$LANGJSON['frm_user']['save'][$LANGUAGE]?>">
</form>

<script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
<script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
<?php if($LANGUAGE == 'rus'){?>
    <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
<?php } ?>
<script>
    $(function(){
        $('.change_pass').validate({
            errorElement: "label",
            errorClass: "is-invalid",
            errorLabelClass: "invalid-feedback",
            validClass: "is-valid",
            highlight: function ( element, errorClass, validClass ) {
                $(element).addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass).addClass(validClass);
            },
            rules : {
                pass:{required: true, minlength: 8,},
                pass_cnf: {required: true, equalTo: "#pass"},
            },
        });
    });
</script>