<?php
$action = get_param('action');
$IMG_WH_SIZE = 250;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'img/partners';
$CFG->dir_f = $CFG->dir_upl.'/uploads/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/uploads/uploads/'.$UPLOAD_FILE_DIR;

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, '*', get_param('sheet', 0 ,'int'));
    ?>
    <div class="form-inline">
        <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
    </div>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th><i class="fa fa-image"></i></th>
            <th>Наименование</th>
            <th width="40"><i class="fa fa-eye"></i></th>
            <th width="80"></th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){
            $files = get_files_tree($CFG->dir_f.'/'.$row->id);
            ?>
            <tr>
                <td><div class="gallery_box"><img src="<?=$CFG->dir_f_show.'/'.$row->id.'/'.$files['files'][0]?>" class="gallery_box_img"></div></td>
                <td><?=$row->name?></td>
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
        $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
        $name_en = get_param('name_en');
        $content = get_param('content');
        $content_en = get_param('content_en');
        $visible = isset($_POST['visible']) ? 1 : 0;
        $link = get_param('link');
        if (empty($_FILES['img']['size'])) {$error[] = 'Загрузите логотип партнера';}

        if(!count($error)){
            $frm = [
                'name' => $name,
                'name_en' => $name_en,
                'content' => $content,
                'content_en' => $content_en,
                'visible' => $visible,
                'link' => $link,
            ];
            $id = $DB->insert_record($_PAGE, $frm);
            if (!empty($_FILES['img'])) {
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
            <div class="col-md-2 text-center">
                <div class="img_cont">
                    <b class="red">НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                    <input id="image" name="img" type="file" title="Выбрать файл" accept="image/*" style="display: none;">
                    <img src="../../img/static/img.png" id="target" class="image_preview img-fluid"/><br>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-group">
                    Наименование<i class="red">*</i> (<= 50 символов)
                    <input type="text" name="name" class="form-control" maxlength="50" placeholder="Наименование">
                </div>
                <div class="form-group">
                    Наименование (Eng)<i class="red">*</i> (<= 50 символов)
                    <input type="text" name="name_en" class="form-control" maxlength="50" placeholder="Наименование (Eng)">
                </div>
                <div class="form-group">
                    Ссылка (http://)
                    <input type="text" name="link" class="form-control" maxlength="255" placeholder="Ссылка">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" checked>
                    <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            Контент
            <textarea id="TINYArea" class="form-control" rows="3" name="content"></textarea>
        </div>
        <div class="form-group">
            Контент (Eng)
            <textarea id="TINYArea2" class="form-control" rows="3" name="content_en"></textarea>
        </div>
        <input type="submit" class="btn btn-success" name="add" value="Добавить запись">
    </form>
    <script src="<?=SITE_URL?>/inc/tinymce/tinymce.min.js"></script>
    <script src="<?=SITE_URL?>/js/tinymce.js"></script>
    <script>
        tinymce.init({
            selector:'#TINYArea2',
            language: 'ru',
            forced_root_block : '',
            height: 250,
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager'
            ],
            toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' +
                '| link unlink image print preview media | forecolor backcolor emoticons | removeformat responsivefilemanager',
            image_advtab: true,
            relative_urls : false,
            templates: [
                { title: 'Test template 1', content: 'Test 1' },
                { title: 'Test template 2', content: 'Test 2' }
            ],
            image_advtab: true ,

            external_filemanager_path:"/inc/filemanager/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "/inc/filemanager/plugin.min.js"}
        });
    </script>
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
            $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
            $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование (Eng)';
            $content = get_param('content');
            $content_en = get_param('content_en');
            $link = get_param('link');
            $visible = isset($_POST['visible']) ? 1 : 0;

            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'name' => $name,
                    'name_en' => $name_en,
                    'content' => $content,
                    'content_en' => $content_en,
                    'link' => $link,
                    'visible' => $visible,
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
                        Наименование<i class="red">*</i> (<= 50 символов)
                        <input type="text" name="name" class="form-control" maxlength="50" value="<?=$pg->name?>" placeholder="Наименование" required>
                    </div>
                    <div class="form-group">
                        Наименование (Eng)<i class="red">*</i> (<= 50 символов)
                        <input type="text" name="name_en" class="form-control" maxlength="50" value="<?=$pg->name_en?>" placeholder="Наименование (Eng)" required>
                    </div>
                    <div class="form-group">
                        Ссылка (http://)
                        <input type="text" name="link" class="form-control" maxlength="250" value="<?=$pg->link?>" placeholder="Ссылка http://">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                        <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    Контент
                    <textarea id="TINYArea" class="form-control" rows="3" name="content"><?=htmlspecialchars_decode($pg->content)?></textarea>
                </div>
                <div class="col-lg-12">
                    Контент (Eng)
                    <textarea id="TINYArea2" class="form-control" rows="3" name="content_en"><?=htmlspecialchars_decode($pg->content_en)?></textarea>
                </div>
                <div class="col-lg-12">
                    <input type="submit" class="btn btn-success" name="chg" value="Сохранить">
                </div>
            </div>
        </form>
        <script src="<?php echo SITE_URL; ?>/inc/tinymce/tinymce.min.js"></script>
        <script src="<?php echo SITE_URL; ?>/js/tinymce.js"></script>
        <script>
            tinymce.init({
                selector:'#TINYArea2',
                language: 'ru',
                forced_root_block : '',
                height: 250,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' +
                    '| link unlink image print preview media | forecolor backcolor emoticons | removeformat responsivefilemanager',
                image_advtab: true,
                relative_urls : false,
                templates: [
                    { title: 'Test template 1', content: 'Test 1' },
                    { title: 'Test template 2', content: 'Test 2' }
                ],
                image_advtab: true ,

                external_filemanager_path:"/inc/filemanager/",
                filemanager_title:"Responsive Filemanager" ,
                external_plugins: { "filemanager" : "/inc/filemanager/plugin.min.js"}
            });
        </script>
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