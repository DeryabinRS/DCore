<?php
$action = get_param('action');
$IMG_WH_SIZE = 250;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'calendar';
$CFG->dir_f = $CFG->dir_img.'/calendar/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/img/calendar/'.$UPLOAD_FILE_DIR;
$IMG_TEMPLATE =  SITE_URL. '/img/static/icon_course.png';

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, '*', get_param('sheet', 0 ,'int'),50,7,'', 'date_event DESC');
    ?>
    <div class="form-inline">
        <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
    </div>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th><i class="fa fa-image"></i></th>
            <th>Дата</th>
            <th>Наименование</th>
            <th>Тип</th>
            <th width="40"><i class="fa fa-eye"></i></th>
            <th width="80"></th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){
            $img_dir = $CFG->dir_f.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE;
            $img = file_exists($img_dir) ? $CFG->dir_f_show.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE : $IMG_TEMPLATE;

            $type_event = $DB->get_record('calendar_type_event',['id' => $row->type_event]);
            ?>
            <tr>
                <td><div class="gallery_box"><img src="<?=$img?>" class="gallery_box_img"></div></td>
                <td><?=date("Y-m-d",$row->date_event)?></td>
                <td><?=$row->name_en?></td>
                <td><?=$type_event->name_en?></td>
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
        //VALUES FOR DB
        $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование';
        $content_en = get_param('content_en');
        $date_event = isset($_POST['date_event']) ? strtotime(get_param('date_event')): $error[] = 'Введите дату события';
        $type_event = isset($_POST['type_event']) ? get_param('type_event'): $error[] = 'Выберите тип события';
        $visible = isset($_POST['visible']) ? 1 : 0;
        $files = !empty($_FILES['img']['tmp_name']) ? $_FILES['img'] : null;
        if(!count($error)){
            $frm = [
                'name_en' => $name_en,
                'content_en' => $content_en,
                'visible' => $visible,
                'type_event' => $type_event,
                'date_event' => $date_event,
                'date_create' => $_SERVER['REQUEST_TIME']
            ];
            $id = $DB->insert_record($_PAGE, $frm);
            if ($files) {
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
                    name:{required: true, minlength: 3,maxlength:50,},
                    content:{required: true,},
                },
            });
        });
    </script>
    <form method="POST" enctype="multipart/form-data" id="addevent">
        <div class="row">
            <!--<div class="col-md-2 text-center">
                <div class="img_cont">
                    <b class="red">НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                    <input id="image" name="img" type="file" title="Выбрать файл" accept="image/*" style="display: none;">
                    <img src="../../img/static/img.png" id="target" class="image_preview img-fluid"/><br>
                </div>
            </div>-->
            <div class="col-lg-10">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputPos">Наименование</label>
                        </div>
                        <input type="text" name="name_en" class="form-control" maxlength="150">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" checked>
                    <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                </div>
                <hr/>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputDate">Дата</label>
                        </div>
                        <input type="date" name="date_event" id="inputDate" class="form-control maxw200" maxlength="50" value="<?=date('Y-m-d')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="inputType">Тип</label>
                        </div>
                    <select name="type_event" id="inputType" class="form-control" style="max-width: 300px;" required>
                        <?php $types = $DB->get_records('calendar_type_event', [], false,'id, name_en, name_ch');
                        foreach ($types as $pt){
                            echo '<option value="'.$pt->id.'">'.$pt->name_en.'</option>';
                        }
                        ?>
                    </select>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="form-group">
            Контент
            <textarea id="TINYArea" class="form-control" rows="3" name="content_en"></textarea>
        </div>
        <input type="submit" class="btn btn-success" name="add" value="Добавить запись">
    </form>
    <script src="<?=SITE_URL?>/inc/tinymce/tinymce.min.js"></script>
    <script src="<?=SITE_URL?>/js/tinymce.js"></script>
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
        echo '<div class="alert alert-danger">Запись удалена.</div>';
        echo '<a href="'.SITE_URL_ADM.'/?page='.$_PAGE.'" class="btn btn-success">Вернуться к списку</a>';
    }else{print '<div class="alert alert-danger">Ошибка удаления записи.</div>';}
}elseif($action == 'chg'){
    $id = get_param('id', 'int');
    if($id){
        if(isset($_POST['chg'])){
            $error = [];
            $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование';
            $content_en = get_param('content_en');
            $date_event = isset($_POST['date_event']) ? strtotime(get_param('date_event')): $error[] = 'Введите дату события';
            $type_event = isset($_POST['type_event']) ? get_param('type_event'): $error[] = 'Выберите тип события';
            $visible = isset($_POST['visible']) ? 1 : 0;
            //dpr($frm);
            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'name_en' => $name_en,
                    'content_en' => $content_en,
                    'visible' => $visible,
                    'type_event' => $type_event,
                    'date_event' => $date_event
                ];

                //dpr($frm);
                $DB->update_record ($_PAGE, $frm);
                echo '<div class="alert alert-success">Изменения внесены</div>';
            }else{
                foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
            }
        }
        $pg = $DB->get_record($_PAGE,['id' => $id]); //dpr($pg);?>
        <form method="POST" enctype="multipart/form-data" id="addevent">
            <div class="row">
                <!--<div class="col-md-2 text-center">
                    <div class="img_cont">
                        <b class="red">НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                        <input id="image" name="img" type="file" title="Выбрать файл" accept="image/*" style="display: none;">
                        <img src="../../img/static/img.png" id="target" class="image_preview img-fluid"/><br>
                    </div>
                </div>-->
                <div class="col-lg-10">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputPos">Наименование</label>
                            </div>
                            <input type="text" name="name_en" class="form-control" maxlength="150" value="<?=$pg->name_en?>">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                        <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputDate">Дата</label>
                            </div>
                            <input type="date" name="date_event" id="inputDate" class="form-control maxw200" maxlength="50" value="<?=date('Y-m-d', $pg->date_event)?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputType">Тип</label>
                            </div>
                            <select name="type_event" id="inputType" class="form-control" style="max-width: 300px;" required>
                                <?php $types = $DB->get_records('calendar_type_event', [], false,'id, name_en, name_ch');
                                if($types){
                                    foreach ($types as $i => $sd){?>
                                        <option value="<?=$i?>" <?php if($pg->type_event == $sd->id) echo 'selected' ?>><?=$sd->name_en?></option>
                                    <?php } } ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="form-group">
                Контент
                <textarea id="TINYArea" class="form-control" rows="3" name="content_en"><?=$pg->content_en?></textarea>
            </div>
            <input type="submit" class="btn btn-success" name="chg" value="Добавить запись">
        </form>
        <script src="<?=SITE_URL?>/inc/tinymce/tinymce.min.js"></script>
        <script src="<?=SITE_URL?>/js/tinymce.js"></script>

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