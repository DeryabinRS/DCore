<?php
$_PAGENAME = $LANGJSON['partners']['title'][$LANGUAGE];
$feedback = '
<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="../inc/validator/jquery.validate.js"></script>';
if($LANGUAGE == 'rus') {
    $feedback .= '<script src="../inc/validator/messages_ru.js"></script>';
}
$feedback .= '
    <script>
        $(function(){
            $("#partners").validate({
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
                    name:{required: true,},
                    email:{required: true,email: true},
                    phone: {required: true},
                    message:{required:true}
                },
                submitHandler:
                    function(form) {
                        $("#btn_submit").prepend("<i class=\"fa fa-cog fa-spin\"></i>");
                        $.post("../lib/feedback.php", $(form).serialize(), function(data) {
                            var err = data.search("danger");
                            if(data.search("danger") == -1) {
                                $("#partners").remove();
                            }
                            $("#answers").html(data);
                            $(".fa-cog").remove();
                        });
                    }
            });
        });
    </script>

<!--FEEDBACK
<a href="#popupform" id="popupbutton" class="adminbtn">ЗАДАТЬ ВОПРОС</a>-->
<div id="answers"></div>

    <!--<div class="comment">Оставьте Ваши данные и мы свяжемся с Вами</div>-->
    <form method="post" id="partners">
    <h4>'.$LANGJSON['partners']['frm_title'][$LANGUAGE].'</h4>
        <div class="form-group">
            <label>'.$LANGJSON['partners']['frm_name'][$LANGUAGE].'</label>
            <input type="text" name="name" id="name" class="form-control" maxlength="50"> 
            <div id="bthrow_error_name"></div>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text"name="email" class="form-control" maxlength="100">
        </div>
        <div class="form-group">
            <label>'.$LANGJSON['partners']['frm_phone'][$LANGUAGE].'</label>
            <input type="text" name="phone" class="form-control" maxlength="20">
        </div>
        <div class="form-group">
            <label>'.$LANGJSON['partners']['frm_message'][$LANGUAGE].'</label>
            <textarea rows="4" name="message" class="form-control" maxlength="2000"></textarea>
        </div>
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="'.RC_KEY.'"></div>
        </div>
        <div class="form-group">
            <button class="btn btn-custom-2" id="btn_submit">'.$LANGJSON['partners']['btn_send'][$LANGUAGE].'</button>
        </div>
        <span class="under-form">'.$LANGJSON['partners']['frm_bottom'][$LANGUAGE].'</span>
    </form>
';

$contacts = '';

$_CONTENT = '<div class="container mt-4 mb-5 content">';
$_CONTENT .= '<div class="row">';
$_CONTENT .= '<div class="col-lg-3">';
$_CONTENT .= $contacts;
$_CONTENT .= '</div>';
$_CONTENT .= '<div class="col-lg-6">';
$_CONTENT .= $feedback;
$_CONTENT .= '</div>';
$_CONTENT .= '<div class="col-lg-3"></div>';
$_CONTENT .= '</div></div>';