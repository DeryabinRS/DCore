<?php
$action = get_param('action');
$IMG_WH_SIZE = 450;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'contacts';
$CFG->dir_f = $CFG->dir_img.'/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/img/'.$UPLOAD_FILE_DIR;
$IMG_TEMPLATE =  SITE_URL. '/img/static/staff.png';

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, '*', get_param('sheet', 0 ,'int'));
    ?>
    <div class="form-inline">
        <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
    </div>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th width="45"><i class="fa fa-image"></i></th>
            <th>Ф.И.О.</th>
            <th>Email</th>
            <th>Phone</th>
            <th width="40"><i class="fa fa-sort-amount-asc" data-toggle="tooltip" data-placement="top" title="Позиция в списке"></i></th>
            <th width="40"><i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="Видимость"></i></th>
            <th width="80"></th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){
            $img_dir = $CFG->dir_f.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE;
            $img = file_exists($img_dir) ? $CFG->dir_f_show.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE : $IMG_TEMPLATE;
            ?>
            <tr>
                <td><div class="gallery_box"><img src="<?=$img?>" class="gallery_box_img"></div></td>
                <td><?=$row->lastname.' '.$row->firstname.' '.$row->middlename?></td>
                <td><?=$row->email?></td>
                <td><?=$row->phone?></td>
                <td><?=$row->position?></td>
                <td><?php if($row->visible){echo '<i class="fa fa-eye"></i>';}else{echo '<i class="fa fa-eye-slash"></i>';}?></td>
                <td>
                    <div class="btn-group btn-group-toggle">
                        <a href="?page=<?=$_PAGE?>&action=chg&id=<?php echo $row->id;?>" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Редактировать"><i class="fa fa-pencil"></i></a>
                        <a href="?page=<?=$_PAGE?>&action=del&id=<?php echo $row->id;?>" onclick="return confirmDelete();" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Удалить"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?=$PAGINATION['pag']?>
    <?php
}elseif($action == 'add'){
    if(isset($_POST['add'])){
        $error = [];
        $firstname = isset($_POST['firstname']) ? get_param('firstname') : $error[] = 'Введите имя';
        $lastname = isset($_POST['lastname']) ? get_param('lastname') : $error[] = 'Введите фамилию';
        $middlename = get_param('middlename');
        $email = get_param('email');
        $phone = get_param('phone');
        $desc_en = get_param('desc_en');
        $visible = isset($_POST['visible']) ? 1 : 0;
        $files = !empty($_FILES['img']['tmp_name']) ? $_FILES['img'] : null;
        $pos = get_param('pos');
        $position = isset($_POST['position']) ? get_param('position') : 0;
        if(!count($error)){
            $frm = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'middlename' => $middlename,
                'email' => $email,
                'phone' => $phone,
                'desc_en' => $desc_en,
                'visible' => $visible,
                'pos' => $pos,
                'position' => $position,
                'date_create' => $_SERVER['REQUEST_TIME'],
            ];
            $id = $DB->insert_record($_PAGE, $frm);
            if ($files) {
                //dpr($_FILES['img']);
                if($id){
                    if(!is_dir($CFG->dir_f.'/'.$id)){
                        mkdir($CFG->dir_f.'/'.$id);
                    };
                }
                funSaveFile($_FILES['img'], $CFG->dir_f.'/'.$id, $id, $IMG_FILE_TYPE, $IMG_WH_SIZE,$IMG_WH_SIZE,false, 0);
            }
            echo '<div class="alert alert-success">Запись добавлена</div>';
            echo '<a href="'.SITE_URL_ADM.'/?page='.$_PAGE.'" class="btn btn-success">Вернуться к списку</a>';
            exit;
        }else{
            foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
        }
    }
    ?>
    <script src="<?=SITE_URL?>/js/bootstrap_file_input.js"></script>
    <script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
    <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
    <script>
        $(function(){
            $('#addevent').validate({
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
                    firstname{required: true},
                    lastname{required: true},
                    name_en:{required: true, maxlength:150,},
                    email:{required: true,email: true}
                },
            });


        });
    </script>
    <form method="POST" enctype="multipart/form-data" id="addevent">
        <div class="row">
            <div class="col-md-2 text-center">
                <div class="img_cont">
                    <b class="red">НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                    <input id="image" name="img" type="file" title="Выбрать файл" accept="image/*" style="display: none;">
                    <img src="../../img/static/img.png" id="target" class="image_preview img-fluid"/><br>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-group">
                    Фамилия<i class="red">*</i>
                    <input type="text" name="lastname" class="form-control maxw300" maxlength="100">
                </div>
                <div class="form-group">
                    Имя<i class="red">*</i>
                    <input type="text" name="firstname" class="form-control maxw300" maxlength="100">
                </div>
                <div class="form-group">
                    Отчество<i class="red">*</i>
                    <input type="text" name="middlename" class="form-control maxw300" maxlength="100">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" checked>
                    <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                </div>
                <hr/>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputP">Должность</label>
                        </div>
                        <input type="text" name="pos" id="inputP" class="form-control" maxlength="250">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputPhone">Телефон</label>
                        </div>
                        <input type="text" name="phone" id="inputPhone" class="form-control" maxlength="20">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputPhone">Email</label>
                        </div>
                        <input type="email" name="email" id="inputEmail" class="form-control" maxlength="100">
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputPos">Позиция в списке</label>
                        </div>
                        <input type="number" name="position" id="inputPos" class="form-control maxw200" maxlength="50" value="0">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Краткое описание</span>
                        </div>
                        <textarea class="form-control" name="desc_en" id="inputDesc" aria-label="Краткое описание"></textarea>
                    </div>
                    <small id="descHelp" class="form-text text-muted">Не более 300 символов</small>
                </div>

                <input type="submit" class="btn btn-success" name="add" value="Добавить запись">
            </div>
        </div>
    </form>


    <script>
        $('#image').change(function() {
            var input = $(this)[0];
            if ( input.files && input.files[0] ) {
                if ( input.files[0].type.match('image.*') ) {
                    var reader = new FileReader();
                    reader.onload = function(e) { $('.image_preview').attr('src', e.target.result); }
                    reader.readAsDataURL(input.files[0]);
                    $('.alert').animate({height: 'hide'}, 300);
                } else  { $('.alert').text('Не правильный формат изображения');
                    $('.alert').animate({height: 'show'}, 300); }
            } else console.log('not isset files data or files API not supordet');
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#target').click(function () {
                $('#image').click();
            });
        });
        $('#image').change(function() {
            var input = $(this)[0];
            if ( input.files && input.files[0] ) {
                if ( input.files[0].type.match('image.*') ) {
                    var reader = new FileReader();
                    reader.onload = function(e) { $('.image_preview').attr('src', e.target.result); }
                    reader.readAsDataURL(input.files[0]);
                    $('.alert').animate({height: 'hide'}, 300);
                }else{ $('.alert').text('Не правильный формат изображения');
                    $('.alert').animate({height: 'show'}, 300);
                }
            } else console.log('not isset files data or files API not supordet');
        });
    </script>
<?php }elseif($action == 'del'){
    $id = get_param('id', 'int');
    if($id){
        $DB->delete_records($_PAGE,['id' => $id]);
        if (file_exists($CFG->dir_f.'/'.$id.'/'.$id.'.'.$IMG_FILE_TYPE)) {
            unlink($CFG->dir_f.'/'.$id.'/'.$id.'.'.$IMG_FILE_TYPE);
        }
        if (file_exists($CFG->dir_f.'/'.$id.'/'.$id.'_thumb.'.$IMG_FILE_TYPE)) {
            unlink($CFG->dir_f.'/'.$id.'/'.$id.'_thumb.'.$IMG_FILE_TYPE);
        }
        if(is_dir($CFG->dir_f . '/' . $id)) {
            rmdir($CFG->dir_f . '/' . $id);
        }
        echo '<div class="alert alert-danger">Запись удалена.</div>';
        echo '<a href="'.SITE_URL_ADM.'/?page='.$_PAGE.'" class="btn btn-success">Вернуться к списку</a>';
    }else{print '<div class="alert alert-danger">Ошибка удаления записи.</div>';}
}elseif($action == 'chg'){
    $id = get_param('id', 'int');
    if($id){
        if(isset($_POST['chg'])){
            $error = [];

            $firstname = isset($_POST['firstname']) ? get_param('firstname') : $error[] = 'Введите имя';
            $lastname = isset($_POST['lastname']) ? get_param('lastname') : $error[] = 'Введите фамилию';
            $middlename = get_param('middlename');
            $email = get_param('email');
            $phone = get_param('phone');
            $desc_en = get_param('desc_en');
            $visible = isset($_POST['visible']) ? 1 : 0;
            $files = !empty($_FILES['img']['tmp_name']) ? $_FILES['img'] : null;
            $pos = get_param('pos');
            $position = isset($_POST['position']) ? get_param('position') : 0;
            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'middlename' => $middlename,
                    'email' => $email,
                    'phone' => $phone,
                    'desc_en' => $desc_en,
                    'visible' => $visible,
                    'pos' => $pos,
                    'position' => $position,
                    'date_create' => $_SERVER['REQUEST_TIME'],
                ];
                //dpr($frm);
                $DB->update_record ($_PAGE, $frm);
                echo '<div class="alert alert-success">Изменения внесены</div>';
            }else{
                foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
            }
        }
        $pg = $DB->get_record($_PAGE,['id' => $id]); //dpr($pg);?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-2 img_panel">
                    <div class="img_cont text-center">
                        <b>НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                        <form id="imgform1" method="post" enctype="multipart/from-data">
                            <input id="image1" name="img" type="file" accept="image/*" title="Выбрать файл" style="display: none">
                        </form>
                        <?php
                        if (file_exists($CFG->dir_f.'/'.$pg->id.'/'.$pg->id.'.'.$IMG_FILE_TYPE)){
                            $img = $CFG->dir_f_show.'/'.$pg->id.'/'.$pg->id.'.'.$IMG_FILE_TYPE;?>
                            <img id="target1" src="<?=$img?>" class="image_preview1 img-fluid">
                            <div class="btn btn-sm btn-danger del_img" id="del_img1" style="width: 100%;margin-top: 5px;"><i class="fa fa-times"></i> Удалить</div>
                        <?php }else{$img = '../img/static/img.png';?>
                            <img id="target1" src="<?=$img?>" class="image_preview1 img-fluid">
                            <div class="btn btn-sm btn-danger del_img" id="del_img1" style="width: 100%;margin-top: 5px;display: none;"><i class="fa fa-times"></i> Удалить</div>
                        <?php } ?>
                    </div>
                    <div id="img_load_msg" style="display:none;"></div>
                </div>
                <div class="col-lg-10">
                    <div class="form-group">
                        Фамилия<i class="red">*</i>
                        <input type="text" name="lastname" class="form-control maxw300" maxlength="100" value="<?=$pg->lastname?>">
                    </div>
                    <div class="form-group">
                        Имя<i class="red">*</i>
                        <input type="text" name="firstname" class="form-control maxw300" maxlength="100" value="<?=$pg->firstname?>">
                    </div>
                    <div class="form-group">
                        Отчество<i class="red">*</i>
                        <input type="text" name="middlename" class="form-control maxw300" maxlength="100" value="<?=$pg->middlename?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                        <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputP">Должность</label>
                            </div>
                            <input type="text" name="pos" id="inputP" class="form-control" maxlength="250" value="<?=$pg->pos?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputPhone">Телефон</label>
                            </div>
                            <input type="text" name="phone" id="inputPhone" class="form-control" maxlength="20" value="<?=$pg->phone?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputPhone">Email</label>
                            </div>
                            <input type="email" name="email" id="inputEmail" class="form-control" maxlength="100" value="<?=$pg->email?>">
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputPos">Позиция в списке</label>
                            </div>
                            <input type="number" name="position" id="inputPos" class="form-control maxw200" maxlength="50" value="<?=$pg->position?>" >
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Краткое описание</span>
                            </div>
                            <textarea class="form-control" name="desc_en" id="inputDesc" aria-label="Краткое описание"><?=$pg->desc_en?></textarea>
                        </div>
                        <small id="descHelp" class="form-text text-muted">Не более 300 символов</small>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-success" name="chg" value="Сохранить">
                    </div>
                </div>
            </div>
        </form>
        <script>
            $('#target1').click(function () {
                $('#image1').click();
            });
            $('#image1').change(function() {
                var input = $(this)[0];
                if(getImage(this, 1)) {
                    var file_data = $('#image1').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    form_data.append('img_size', <?=$IMG_WH_SIZE?>);
                    //form_data.append('img_thumb_size',<?=$IMG_THUMB_SIZE?>);
                    form_data.append('img_dir', '<?=$CFG->dir_f.'/'.$pg->id?>');
                    form_data.append('fname', '<?=$pg->id.'.'.$IMG_FILE_TYPE?>');
                    //form_data.append('fname_thumb', '<?=$pg->id.'_thumb.'.$IMG_FILE_TYPE?>');
                    uploadFile(form_data);
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
                            $('#del_img1').css({"display":"block"});
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
                del_img("<?=$pg->id.'.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$pg->id?>");
                del_img("<?=$pg->id.'_thumb.'.$IMG_FILE_TYPE?>", "<?=$CFG->dir_f.'/'.$pg->id?>");
            });
            function del_img(fname, img_dir) {
                $.ajax({
                    method: "POST",
                    dataType: 'text',
                    url: '../ajaxscripts/ajax_upload_img.php',
                    data: {fname: fname, fdel: true,img_dir: img_dir},
                    success: function(php_script_response){
                        $('#' + "target1").attr({src: "../img/static/img.png"});
                        getMessageSlow("#img_load_msg","alert alert-danger","Файл удален");
                        $('#del_img1').css({"display":"none"});
                    }
                });
            }
        </script>

    <?php }else{print '<div class="alert alert-danger">Ошибка записи.</div>';}
}