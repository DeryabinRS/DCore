<?php
if(isset($_POST['authorization'])){
    foreach ($USER->error['auth'] as $err){
        echo '<div class="alert alert-danger">'.$err.'</div>';
    }
}?>

<script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
<?php if($LANGUAGE == 'rus'){?>
<script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
<?php } ?>
<script>
    $(function(){
        $('.autform').validate({
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
                email:{required: true, email: true,},
                pass:{required: true, minlength: 8,},
            },
            submitHandler:
                function(form) {
                    $('#btn_submit').prepend('<i class="fa fa-cog fa-spin"></i>');
                    $.post("../ajaxscripts/ajax_user.php", $(form).serialize(), function(data) {
                        $('#answers').html(data);
                        //location.reload();
                        if(data.search("danger") == -1) {
                            $('.autform').remove();
                            location.replace("<?=SITE_URL_ADM?>");
                        }
                        $("#btn_submit").find('.fa-cog').remove();
                        //console.log(window.location.href);
                        //location.replace("<?=SITE_URL?>");
                    });
                }
        });
    });
</script>
<div id="answers"></div>
<form method="POST" class="autform">
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">@</div>
            </div>
            <input type="text" name="email" class="form-control" maxlength="50" placeholder="Email" autocomplete="off" required>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <input type="password" name="pass" class="form-control" maxlength="50" autocomplete="off" placeholder="<?=$LANGJSON['frm_auth']['pass'][$LANGUAGE]?>" required>
        </div>
    </div>
    <a href="<?=SITE_URL?>/users/?usr=repass" class="auth_repass_link"> <?=$LANGJSON['frm_user']['get_pass'][$LANGUAGE]?></a>
    <div class="mt-2">
        <button class="btn btn-success" id="btn_submit" name="authorization"><?=$LANGJSON['frm_auth']['login'][$LANGUAGE]?></button>
    <!--<a href="<?=SITE_URL?>" class="btn btn-primary"> На сайт</a>-->
        <a href="<?=SITE_URL?>/users/?usr=reg" class="btn btn-primary ml-2"> <?=$LANGJSON['frm_reg']['reg'][$LANGUAGE]?></a>
    </div>
</form>
