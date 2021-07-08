<?php
$IMG_WH_SIZE = 350;
//$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';


//$UPLOAD_FILE_DIR = 'img/users';
//$CFG->dir_f = $CFG->dir_upl.'/uploads/'.$UPLOAD_FILE_DIR;
//$CFG->dir_f_show = SITE_URL. '/uploads/uploads/'.$UPLOAD_FILE_DIR;

$UPLOAD_FILE_DIR = 'users';
$CFG->dir_f = $CFG->dir_img.'/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/img/'.$UPLOAD_FILE_DIR;

$UPLOAD_FILE_DIR_TEAM = 'teams';
//get images user,team
//$img_user_file = get_files_img('uploads/uploads/img/users/'.$USER->id, 'photo');
$img_user_file = get_files_img('img/'.$UPLOAD_FILE_DIR.'/'.$USER->id, 'photo');

//dpr($img_user_file);
$img_name = $USER->id;
//$get_files = get_files_tree($CFG->dir_f.'/'.$USER->id);
if(!empty($img_user_file[0])){
    $img_name = $img_user_file[0]['name'];
}else{
    $img_name = $USER->id.'_photo_'.$_SERVER['REQUEST_TIME'];
}
$IMG_SAMPLE = '../img/static/user.png';
$IMG_SAMPLE_TEAM = '../img/static/user.png';
$pg = $DB->get_record('users',['id' => $USER->id]);
?>
<div class="col-md-3">
    <div class="img_cont text-center">
        <form id="imgform1" method="post" enctype="multipart/from-data">
            <input id="image1" name="img" type="file" accept="image/*" title="Выбрать файл" style="display: none">
        </form>
        <?php
        if (!empty($img_user_file[0])){
            $img = $img_user_file[0]['dir_url'].'/'.$img_name.'.'.$img_user_file[0]['type'];?>
            <div class="btn-group btn-group-toggle frm_btn_box">
                <div class="btn btn-sm btn-danger" id="del_img1"><i class="fa fa-trash"></i></div>
                <div class="btn btn-sm btn-success" id="ch_img"><i class="fa fa-pencil"></i></div>
            </div>
            <img id="target1" src="<?=$img?>" class="image_preview1 img-fluid">
        <?php }else{ ?>
            <div class="btn-group btn-group-toggle frm_btn_box" style="display: none;">
                <div class="btn btn-sm btn-danger" id="del_img1"><i class="fa fa-trash"></i></div>
                <div class="btn btn-sm btn-success" id="ch_img"><i class="fa fa-pencil"></i></div>
            </div>
            <img id="target1" src="<?=$IMG_SAMPLE?>" class="image_preview1 img-fluid">
        <?php } ?>
    </div>

    <script>
        $('#target1').click(function () {
            $('#image1').click();
        });
        $('#ch_img').click(function () {
            $('#image1').click();
        });
        $('#image1').change(function() {
            if(getImage(this, 1)) {
                var file_data = $('#image1').prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                form_data.append('img_size', <?=$IMG_WH_SIZE?>);
                form_data.append('img_dir', '<?=$CFG->dir_f.'/'.$USER->id?>');
                form_data.append('fname', '<?=$USER->id.'_photo_'.$_SERVER['REQUEST_TIME'].'.'.$IMG_FILE_TYPE?>');
                uploadFile(form_data);
                del_img("<?=$img_name.'.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$USER->id?>");
                del_img("<?=$img_name.'_thumb.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$USER->id?>");
            }
        });
        function getMessageSlow(el, cls = "alert alert-primary", text = "", timeout = 1500) {
            $(el).attr({class:""}).addClass(cls).html(text).show();
            setTimeout(function(){$(el).hide('slow');}, timeout);
        }
        function uploadFile(fobj){
            //console.log(fobj);
            $.ajax({
                method: "POST",
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                url: '../ajaxscripts/ajax_upload_img.php',
                data: fobj,
                success: function(php_script_response){
                    getMessageSlow("#img_load_msg","alert alert-success","Файл загружен");
                }
            }).done(function(msg){
                console.log('done');
                $('.frm_btn_box').css({"display":"block"});
            }).fail(function(msg){
                console.log('fail');
            });
        }
        function getImage(sobj, nimg){
            var input = $(sobj)[0];
            if ( input.files && input.files[0] ) {
                if ( input.files[0].type.match('image.*') ) {
                    var reader = new FileReader();
                    if (nimg == 1) reader.onload = function(e) {
                        $('.image_preview1').attr('src', e.target.result);
                        $('.frm_btn_box').css({"display":"block"});
                    }
                    reader.readAsDataURL(input.files[0]);
                    $('.alert').animate({height: 'hide'}, 300);
                    return true;
                } else  { $('.alert').text('Не правильный формат изображения');
                    $('.alert').animate({height: 'show'}, 300);
                    return false;
                }
            } else console.log('not isset files data or files API not supordet');
        }
        $('#del_img1').click(function(){
            del_img("<?=$img_name.'.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$USER->id?>");
            del_img("<?=$img_name.'_thumb.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$USER->id?>");
            $('#' + "target1").attr({src: "<?=$IMG_SAMPLE?>"});
        });
        function del_img(fname, img_dir) {
            $.ajax({
                method: "POST",
                dataType: 'text',
                url: '../ajaxscripts/ajax_upload_img.php',
                data: {fname: fname, fdel: true,img_dir: img_dir},
                success: function(php_script_response){
                    getMessageSlow("#img_load_msg","alert alert-danger","Файл удален");
                    $('.frm_btn_box').css({"display":"none"});
                }
            });
        }
    </script>
</div>
<div class="col-md-9">
    <script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
    <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
    <?php if($LANGUAGE == 'rus'){?>
        <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
    <?php } ?>
    <script>
        $(function(){
            $('#user_form').validate({
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
                    lastname:{required: true},
                    firstname:{required: true}
                    //surname:{required: true},
                },
                submitHandler:
                    function(form) {
                        $('#btn_submit').prepend('<i class="fa fa-cog fa-spin"></i>');
                        $.post("../ajaxscripts/ajax_user.php", $(form).serialize(), function(data) {
                            var err = data.search("danger");
                            if(data.search("danger") == -1) {
                                $('.regform').remove();
                            }
                            $('#answers').hide();
                            $('#answers').html(data);
                            $('#answers').show(300);
                            $('.fa-cog').remove();
                            setTimeout(function(){
                                $('#answers').hide(300);
                                $('#answers').html('');
                            }, 3000);
                        });
                    }
            });
        });
    </script>

    <div id="answers"></div>
    <form method="POST" id="user_form">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-6">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value="<?=$pg->email?>" disabled>
                </div>
                <div class="col-sm-6">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['phone'][$LANGUAGE]?></label>
                    <input type="text" name="phone" class="form-control" value="<?=$pg->phone?>" data-mask="0-000-000-00-00" placeholder="0-000-000-00-00">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['lastname'][$LANGUAGE]?><b class="red">*</b></label>
                    <input type="text" name="lastname" class="form-control" value="<?=$pg->lastname?>" placeholder="<?=$_PAGENAME = $LANGJSON['frm_account']['lastname'][$LANGUAGE]?>" maxlength="30">
                </div>
                <div class="col-sm-4">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['firstname'][$LANGUAGE]?><b class="red">*</b></label>
                    <input type="text" name="firstname" class="form-control" value="<?=$pg->firstname?>" placeholder="<?=$_PAGENAME = $LANGJSON['frm_account']['firstname'][$LANGUAGE]?>" maxlength="30">
                </div>
                <div class="col-sm-4">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['middlename'][$LANGUAGE]?></label>
                    <input type="text" name="surname" class="form-control" value="<?=$pg->surname?>" placeholder="<?=$_PAGENAME = $LANGJSON['frm_account']['middlename'][$LANGUAGE]?>" maxlength="30">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['birthday'][$LANGUAGE]?></label>
                    <input type="date" name="birthday" class="form-control" value="<?php if($pg->birthday) echo date('Y-m-d',$pg->birthday);?>" placeholder="dd.mm.YYYY" maxlength="10" style="max-width: 200px;">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-6">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['country'][$LANGUAGE]?></label>
                    <select class="form-control" id="country" name="country">
                        <option value=""></option>
                    <?php
                    require_once ($CFG->dir_lib.'/country_array.php');
                    foreach ($countryList as $key => $val){
                        $select = '';
                        if($pg->country == $key){$select = 'selected';}
                        echo '<option value="'.$key.'" '.$select.'>'.$val.'</option>';
                    }
                    ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['city'][$LANGUAGE]?></label>
                    <input type="text" name="city" class="form-control" value="<?=$pg->city?>" maxlength="50">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-6">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['work_study'][$LANGUAGE]?></label>
                    <input type="text" name="work_study" class="form-control" value="<?=$pg->work_study?>" maxlength="50">
                </div>
                <div class="col-sm-6">
                    <label><?=$_PAGENAME = $LANGJSON['frm_account']['specialty'][$LANGUAGE]?></label>
                    <input type="text" name="specialty" class="form-control" value="<?=$pg->specialty?>" maxlength="500">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label><?=$_PAGENAME = $LANGJSON['frm_account']['about'][$LANGUAGE]?></label>
            <textarea name="about" class="form-control" maxlength="50"><?=$pg->about?></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-success mt-2" id="btn_submit" name="update" id="btn_submit"><?=$_PAGENAME = $LANGJSON['frm_account']['btn_save'][$LANGUAGE]?></button>
            <a href="<?=SITE_URL?>/users?usr=change_pass" class="btn btn-primary mt-2">Изменить пароль</a>
            <!--<input type="submit" class="btn btn-custom-2 mt-3" name="update" value="<?=$_PAGENAME = $LANGJSON['frm_account']['btn_save'][$LANGUAGE]?>">-->
        </div>
    </form>
    <script src="<?=SITE_URL?>/js/jquery.mask.min.js"></script>
</div>