<?php
$_PAGENAME = 'Feedback';
$_PAGE_DESC = 'Leave your feedback about our school';
$_CONTENT = '';

$_CONTENT .= '<div class="container mt-4">';
$_CONTENT .= '
    <a href="javascript:;" class="btn btn-custom-1" data-src="#form-feedback" data-fancybox="hello"><i class="fa fa-send"></i>&nbsp;&nbsp;Send feedback</a>
    <!--FEEDBACK
    <a href="#popupform" id="popupbutton" class="adminbtn">ЗАДАТЬ ВОПРОС</a>-->
        <!--<div class="comment">Оставьте Ваши данные и мы свяжемся с Вами</div>-->
    <script src=\'https://www.google.com/recaptcha/api.js\'></script>
    <form method="post" enctype="multipart/form-data" id="form-feedback" style="display: none; width: 100%; max-width: 660px;">
        <h4 class="text-center mb-4">Send feedback</h4>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" for="inputEmail">Email:</span>
                </div>
                <input type="text" name="email" id="inputEmail" class="form-control" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" for="inputFirstName">First name:</span>
                </div>
                <input type="text" name="firstname" id="inputFirstName" class="form-control" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" for="inputLastName">Last name:</span>
                </div>
                <input type="text" name="lastname" id="inputLastName" class="form-control" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" for="inputLastName">Country:</span>
                </div>
                <input type="text" name="country" id="inputCountry" class="form-control" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <label>Message:</label>
            <textarea rows="4" name="message" id="inputTxt" class="form-control" maxlength="1000"></textarea>
            <small class="counter">Message length: <span id="count_simvol"></span></small>
        </div>
        <div class="form-group">
            <small>Your photo:</small><br>
            <input type="file" class="form-control-file" name="img" id="img" accept="image/jpg, image/jpeg, image/png, image/gif, image/bmp">
            <small id="error_img" class="red"></small>
        </div>
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="'.RC_KEY.'"></div>
        </div>
        <div class="form-group">
            <input class="btn btn-custom-1" type="submit" value="Send feedback" /><span id="load" style="margin: 10px;font-size: 20px;display: inline;"></span>
            <small class="throw_error red mt-3"></small>
        </div>
    </form>

    <script>
        $(function() {
            $("#form-feedback").submit(function(e) {
                e.preventDefault();
                let err = false;
                
                function requiredInput(el) {
                    if ($(el).val() == ""){
                        $(el).addClass("is-invalid");
                        err = true;
                    }else{
                        $(el).removeClass("is-invalid");
                    }
                }
                requiredInput("#inputEmail");
                requiredInput("#inputFirstName");
                requiredInput("#inputLastName");
                requiredInput("#inputCountry");
                requiredInput("#inputTxt");
                
                if (err == false){
                    e.preventDefault();
                    var $that = $(this),
                    formData = new FormData($that.get(0)); // создаем новый экземпляр объекта и передаем ему нашу форму (*)
                    $("#load").fadeIn(200).html(\'<i class="fa fa-cog fa-spin"></i>\');
                    $.ajax({
                        type        : $that.attr("method"),
                        url         : "../ajaxscripts/ajax_feedback.php",
                        data        : formData,
                        dataType    : "json",
                        processData: false,
                        contentType: false,
                        success     : function(data){
                            if (!data.success){
                                if (data.errors.name){
                                    $(".throw_error").fadeIn(500).html(data.errors.name);
                                    $("#load").fadeOut(200).html("");
                                }
                            }else{
                                $("#form-feedback").fadeIn(500).html(data.posted);
                            }
                        }
                    });
                }
            });
        });
            
            $(document).ready(function(){
            let maxCount = 500;
            $("#count_simvol").html(maxCount);
            $("#inputTxt").keyup(function() {
                var revText = this.value.length;
                if (this.value.length > maxCount){this.value = this.value.substr(0, maxCount);}
                var cnt = (maxCount - revText);
                if(cnt <= 0){$("#count_simvol").html(\'0\');}
                else {$("#count_simvol").html(cnt);}
            });

            $("#img").change(function() {
                var input = $(this)[0];
                if (!input.files[0].type.match("image.*") ) {
                    $("#error_img").text(\'incorrect format image\');
                    $("#error_img").animate({height: "show"}, 200);
            }else{
                    $("#error_img").animate({height: "hide"}, 200);
                }});
        });
    </script>
';
$_CONTENT .= '<hr/><div class="feedback_list">';
$PAGINATION = funcPagination('feedback', '*', get_param('sheet', 0 ,'int'),25, 7, 'visible = 1','date_create DESC');


foreach ($PAGINATION['table'] as $row) {
    $row = (array) $row;
    $path_img = $CFG->dir_img.'/feedback/'.$row['id'].'/'.$row['id'].'.jpg';
    $img = is_file($path_img) ? SITE_URL.'/img/feedback/'.$row['id'].'/'.$row['id'].'.jpg' : '/img/static/userlogo.jpg';
    $message = strip_tags(html_entity_decode($row['message']));

    $_CONTENT .= '<div class="feedback_box mt-3">
                        <div class="img">
                            <img src="'.$img.'" class="img-fluid">
                        </div>
                        <div class="message">
                            <div><b>'.$row['firstname'].' '.$row['lastname'].'</b></div>
                            <div style="color: var(--primary-color)">'.$row['country'].'</div>
                            <!--<div class="info">'.date("d.m.Y", $row['date_create']).'</div>-->
                            <div>'.$message.'</div>
                        </div>
                    </div>';
}

$_CONTENT .= '<div class="container mt-4 mb-5">';
$_CONTENT .= $PAGINATION['pag'];
$_CONTENT .= '</div>';



$_CONTENT .= '</div>';
$_CONTENT .= '</div>';