<?php
if(!$USER->id){
    //dpr($LANGJSON);
    ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="../inc/validator/jquery.validate.js"></script>
    <script src="<?=SITE_URL?>/js/jquery.mask.min.js"></script>
    <?php if($LANGUAGE == 'rus'){?>
    <script src="../inc/validator/messages_ru.js"></script>
    <?php } ?>
    <script>
        $(function(){
            $('.regform').validate({
                errorElement: "label",
                errorClass: "is-invalid",
                errorLabelClass: "invalid-feedback",
                validClass: "is-valid",
                highlight: function ( element, errorClass, validClass ) {
                    $(element).addClass(errorClass).removeClass(validClass);
                    //$(element.form).find('label[for='+element.name+']').addClass('invalid-feedback');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass(errorClass).addClass(validClass);
                    //$(element.form).find('label[for='+element.name+']').removeClass(errorLabelClass);
                },
                rules : {
                    email:{required: true, email: true, remote:{url: "../ajaxscripts/ajax_check_user.php", type : "post",}},
                    pass:{required: true, minlength: 8,},
                    pass_cnf: {required: true, equalTo: "#pass"},
                    //street:{required: true,},
                    fname:{required: true},
                    lname:{required: true}
                    //orgname:{required: true,},
                },
                messages:{
                    //pass_cnf:{equalTo: "Пароли не совпадают"},
                    email:{remote:"<?=$LANGJSON['frm_reg']['error_email_1'][$LANGUAGE]?>"}
                },
                submitHandler:
                    function(form) {
                        $('#btn_submit').prepend('<i class="fa fa-cog fa-spin"></i>');
                        $.post("../ajaxscripts/ajax_user.php", $(form).serialize(), function(data) {
                            var err = data.search("danger");
                            if(data.search("danger") == -1) {
                                $('.regform').remove();
                            }
                            $('#answers').html(data);
                            $('.fa-cog').remove();
                        });
                    }
            });
        });
    </script>
    <div id="answers"></div>
    <form method="POST" class="regform">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">@</div>
                </div>
                <input type="text" name="email" id="email" class="form-control" maxlength="50" autocomplete="off" placeholder="Email" required>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label><?=$LANGJSON['frm_reg']['pass'][$LANGUAGE]?></label>
                    <input type="password" name="pass" id="pass" class="form-control" autocomplete="off" maxlength="50" placeholder="<?=$LANGJSON['frm_reg']['pass'][$LANGUAGE]?>" required>
                </div>
                <div class="col-md-6">
                    <label><?=$LANGJSON['frm_reg']['repeat_pass'][$LANGUAGE]?></label>
                    <input type="password" name="pass_cnf" id="pass_cnf" class="form-control" autocomplete="off" maxlength="50" placeholder="<?=$LANGJSON['frm_reg']['repeat_pass'][$LANGUAGE]?>" required>
                </div>
            </div>
        </div>
        <div class="form-group" id="usr">
            <div class="row">
                <div class="col-md-4">
                    <label><?=$LANGJSON['frm_reg']['lastname'][$LANGUAGE]?></label>
                    <input type="text" name="lname" class="form-control" autocomplete="off" placeholder="<?=$LANGJSON['frm_reg']['lastname'][$LANGUAGE]?>">
                </div>
                <div class="col-md-4">
                    <label><?=$LANGJSON['frm_reg']['firstname'][$LANGUAGE]?></label>
                    <input type="text" name="fname" class="form-control" autocomplete="off" placeholder="<?=$LANGJSON['frm_reg']['firstname'][$LANGUAGE]?>">
                </div>
                <div class="col-md-4">
                    <label><?=$LANGJSON['frm_reg']['surname'][$LANGUAGE]?></label>
                    <input type="text" name="sname" class="form-control" autocomplete="off" placeholder="<?=$LANGJSON['frm_reg']['surname'][$LANGUAGE]?>">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="<?=RC_KEY?>"></div>
        </div>

        <!--<input type="submit" class="btn btn-success" value="Регистрация" id="btn_submit" name="registration">-->
        <button class="btn btn-success" id="btn_submit" name="registration"><?=$LANGJSON['frm_reg']['reg'][$LANGUAGE]?></button>
        <div style="border-top: 1px solid #ccc; margin-top: 10px;padding-top: 10px"><?=$LANGJSON['frm_reg']['personal_data_txt'][$LANGUAGE]?></div>
    </form>

    <script src="<?=SITE_URL?>/js/jquery.mask.min.js"></script>
<?php }else{
    echo 'Вы уже зарегестрированы';
} ?>