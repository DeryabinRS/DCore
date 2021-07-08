<?php
$action = get_param('action');
$IMG_WH_SIZE = EVENTS_IMG_SIZE;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'img/events';
$CFG->dir_f = $CFG->dir_upl.'/uploads/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/uploads/uploads/'.$UPLOAD_FILE_DIR;

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, 'id, name, alias, visible, date_create, type', get_param('sheet', 0 ,'int'));
    //$query = $DB->get_records($_PAGE,[],true);
    //dpr($pages);
    ?>
    <div class="form-inline">
        <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
    </div>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th width="100"><i class="fa fa-calendar" data-toggle="tooltip" data-original-title="Дата публикации"></th>
            <th>Наименование</th>
            <th>Тип</th>
            <th width="40"><i class="fa fa-eye"></i></th>
            <th width="80"></th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){
            $type_new = $DB->get_record('news_types',['id'=>$row->type],"name");
            ?>
            <tr>
                <td><?=date('d.m.Y',$row->date_create)?></td>
                <td><?=$row->name?></td>
                <td><?=$type_new->name?></td>
                <td><?php if($row->visible){echo '<i class="fa fa-eye"></i>';}else{echo '<i class="fa fa-eye-slash"></i>';}?></td>
                <td>
                    <div class="btn-group btn-group-toggle">
                        <a href="?page=<?=$_PAGE?>&action=chg&id=<?php echo $row->id;?>" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Редактировать"><i class="fa fa-pencil"></i></a>
                        <a href="<?=fGetURL('?pgs='.$_PAGE.'&alias='.$row->alias)?>" target="_blank" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Посмотреть на сайте - <?php echo $row->name; ?>">
                            <i class="fa fa-reply"></i>
                        </a>
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
        $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
        $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование (Eng)';
        //$alias = isset($_POST['alias']) ? get_param('alias'): $error[] = 'Введите алиас';
        $type = isset($_POST['type']) ? get_param('type', 'int'): $error[] = 'Введите тип события';
        $desc = get_param('desc');

        $dateS = isset($_POST['dateS']) ? get_param('dateS'): Null;
        $timeS = isset($_POST['timeS']) ? get_param('timeS'): Null;
        if($dateS)$dateS = strtotime($dateS.' '.$timeS);
        $datePo = isset($_POST['datePo']) ? get_param('datePo'): Null;
        $timePo = isset($_POST['timePo']) ? get_param('timePo'): Null;
        if($datePo)$datePo = strtotime($datePo.' '.$timePo);
        //dpr($datePo);
        if($datePo && $dateS > $datePo) $error[] = 'Неверно установлена дата';

        $date_pub = isset($_POST['date_pub']) ? strtotime(get_param('date_pub')): Null;

        $keywords= get_param('keywords');
        $content = get_param('content');
        $content_en = get_param('content_en');
        $visible = isset($_POST['visible']) ? 1 : 0;
        $alias = funcRusToLat($name);
        $alias_db = $DB->get_record_sql("SELECT alias FROM ".$CFG->db['prefix'].$_PAGE." WHERE alias = \"$alias\"");
        $i = 1;
        while ($alias_db){
            $al = $alias.'-'.$i;
            $alias_db = $DB->get_record_sql("SELECT alias FROM ".$CFG->db['prefix'].$_PAGE." WHERE alias = \"$al\"");
            if(!$alias_db)$alias = $al;
            $i++;
        }
        if(!count($error)){
            $frm = [
                'name' => $name,
                'name_en' => $name_en,
                'alias' => $alias,
                'type' => $type,
                'desc' => $desc,
                'keywords' => $keywords,
                'content' => $content,
                'content_en' => $content_en,
                'visible' => $visible,
                'author' => $USER->id,
                'dateS' => $dateS,
                'datePo' => $datePo,
                'date_pub' => $date_pub,
                'date_create' => $_SERVER['REQUEST_TIME'],
            ];
            $id = $DB->insert_record($_PAGE, $frm);
            if (!empty($_FILES['img'])) {
                if($id){
                    //$dirsave = $dirsave.'/'.$fileSaveName;
                    if(!is_dir($CFG->dir_f.'/'.$id)){
                        mkdir($CFG->dir_f.'/'.$id);
                    };
                }
                funSaveFile($_FILES['img'], $CFG->dir_f.'/'.$id, $id, $IMG_FILE_TYPE, $IMG_WH_SIZE,0,true, $IMG_THUMB_SIZE);
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
                    name:{required: true, minlength: 3,maxlength:100,},
                    type:{required: true,},
                    dateS:{required: true, date: true},
                    //timeS:{time: true},
                    datePo:{date: true},
                    //timePo:{time: true},
                    content:{required: true,},
                },
            });
        });
    </script>
    <form method="POST" enctype="multipart/form-data" id="addevent">
        <div class="row">
            <div class="col-md-2 text-center">
                <div class="img_cont">
                    <b>НАЖМИТЕ НА ИЗОБРАЖЕНИЕ, ЧТОБЫ ЗАГРУЗИТЬ ФАЙЛ</b>
                    <input id="image" name="img" type="file" title="Выбрать файл" accept="image/*" style="display: none;">
                    <img src="../../img/static/img.png" id="target" class="image_preview img-fluid"/><br>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-group">
                    Наименование<i class="red">*</i> (<= 100 символов)
                    <input type="text" name="name" class="form-control" maxlength="100" placeholder="Наименование">
                </div>
                <div class="form-group">
                    Наименование (Eng)<i class="red">*</i> (<= 100 символов)
                    <input type="text" name="name_en" class="form-control" maxlength="100" placeholder="Наименование (Eng)">
                </div>
                <div class="form-group">Тип<i class="red">*</i>
                    <select name="type" id="type" class="form-control" style="max-width: 300px;" required>
                        <?php $page_types = $DB->get_records($_PAGE.'_types', [], false,'id, name');
                        foreach ($page_types as $pt){
                            echo '<option value="'.$pt->id.'">'.$pt->name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <script>
                    $('#type').change(function(){
                        $.post("../ajaxscripts/ajax_get_param.php",
                            {
                                "tab":"news_types",
                                "params":{
                                    "id": this.value,
                                    "period": 1,
                                }
                            },
                            function (data) {
                                obj = JSON.parse(data);
                                //console.log(obj.id);
                                if(obj.id){
                                    $('#new_param').html('<div class="row">' +
                                        '<div class="col-md-6 form-group">Дата начала <b class="red">*</b><input type="date" name="dateS" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ" required></div>' +
                                        '<div class="col-md-6 form-group">Время начала<input type="time" name="timeS" id="timeS" class="form-control" maxlength="5" required></div>' +
                                        '</div>' +
                                        '<div class="row">' +
                                        '<div class="col-md-6 form-group">Дата окончания<input type="date" name="datePo" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ"></div>' +
                                        '<div class="col-md-6 form-group">Время окончания<input type="time" name="timePo" id="timePo" class="form-control" maxlength="5"></div>' +
                                        '</div><hr>');
                                }else{
                                    $('#new_param').html('');
                                }
                            });
                    });
                </script>
                <div id="new_param"></div>
                <!--                <div class="form-group">-->
                <!--                    Краткое описание (<= 250 символов)-->
                <!--                    <input type="text" name="desc" class="form-control" maxlength="250" placeholder="Краткое описание">-->
                <!--                </div>-->
                <!--                <div class="form-group">-->
                <!--                    Метатеги (вводятся через запятую, <= 250 символов)-->
                <!--                    <input type="text" name="keywords" class="form-control" maxlength="250" placeholder="Метатеги">-->
                <!--                </div>-->

                <div class="form-group" style="max-width: 300px;">
                    Дата публикации:<input type="date" name="date_pub" value="<?=date('Y-m-d', $_SERVER['REQUEST_TIME'])?>" class="datepicker-here form-control" maxlength="10" placeholder="ДД.MM.ГГГГ" required>
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
            $alias = isset($_POST['alias']) ? get_param('alias'): $error[] = 'Введите алиас';
            $type = isset($_POST['type']) ? get_param('type', 'int'): $error[] = 'Введите тип события';
            $desc = get_param('desc');
            $keywords = get_param('keywords');
            $content = get_param('content');
            $content_en = get_param('content_en');
            $gallery = get_param('gallery');

            $date_pub = isset($_POST['date_pub']) ? strtotime(get_param('date_pub')): Null;

            $dateS = isset($_POST['dateS']) ? get_param('dateS'): Null;
            $timeS = isset($_POST['timeS']) ? get_param('timeS'): Null;
            if($dateS)$dateS = strtotime($dateS.' '.$timeS);
            $datePo = isset($_POST['datePo']) ? get_param('datePo'): Null;
            $timePo = isset($_POST['timePo']) ? get_param('timePo'): Null;
            if($datePo)$datePo = strtotime($datePo.' '.$timePo);

            //dpr($content );
            $visible = isset($_POST['visible']) ? 1 : 0;
            $alias_db = $DB->get_record_sql("SELECT alias FROM ".$CFG->db['prefix'].$_PAGE." WHERE alias = \"$alias\" AND NOT  id = $id");
            //dpr($alias_db);
            if($alias_db) $error[] = 'Такой алиас уже существует';
            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'name' => $name,
                    'name_en' => $name_en,
                    'alias' => funcRusToLat($alias),
                    'type' => $type,
                    'desc' => $desc,
                    'keywords' => $keywords,
                    'content' => $content,
                    'content_en' => $content_en,
                    'album' => $gallery,
                    'dateS' => $dateS,
                    'datePo' => $datePo,
                    'date_pub' => $date_pub,
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
                        Наименование<i class="red">*</i> (<= 100 символов)
                        <input type="text" name="name" class="form-control" maxlength="100" value="<?=$pg->name?>" placeholder="Наименование" required>
                    </div>
                    <div class="form-group">
                        Наименование (Eng)<i class="red">*</i> (<= 100 символов)
                        <input type="text" name="name_en" class="form-control" maxlength="100" value="<?=$pg->name_en?>" placeholder="Наименование (Eng)" required>
                    </div>
                    <div class="form-group">
                        Алиас<i class="red">*</i> (<= 100 латинских символов)
                        <input type="text" name="alias" class="form-control" maxlength="250" value="<?=$pg->alias?>" placeholder="Нужен для отображения в адресной строке">
                    </div>
                    <div class="form-group">Тип<i class="red">*</i>
                        <select name="type" id="type" class="form-control" style="max-width: 300px;">
                            <?php $page_types = $DB->get_records($_PAGE.'_types', [], false,'id, name, period');
                            foreach ($page_types as $pt){
                                $selected = '';
                                if($pt->id == $pg->type){
                                    $selected = 'selected';
                                    $period = $pt->period;
                                }
                                echo '<option value="'.$pt->id.'" '.$selected.'>'.$pt->name.'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <script>
                        $('#type').change(function(){
                            $.post("../ajaxscripts/ajax_get_param.php",
                                {
                                    "tab":"news_types",
                                    "params":{
                                        "id": this.value,
                                        "period": 1,
                                    }
                                },
                                function (data) {
                                    obj = JSON.parse(data);
                                    //console.log(obj.id);
                                    if(obj.id){
                                        $('#new_param').html('<div class="row">' +
                                            '<div class="col-md-6 form-group">Дата начала <b class="red">*</b><input type="date" name="dateS" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ" required></div>' +
                                            '<div class="col-md-6 form-group">Время начала<input type="time" name="timeS" id="timeS" class="form-control" maxlength="5" required></div>' +
                                            '</div>' +
                                            '<div class="row">' +
                                            '<div class="col-md-6 form-group">Дата окончания<input type="date" name="datePo" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ"></div>' +
                                            '<div class="col-md-6 form-group">Время окончания<input type="time" name="timePo" id="timePo" class="form-control" maxlength="5"></div>' +
                                            '</div><hr>');
                                    }else{
                                        $('#new_param').html('');
                                    }
                                });
                        });
                    </script>
                    <div id="new_param">
                        <?php if($period){
                            $dateS = $pg->dates ? date("Y-m-d",$pg->dates): Null;
                            $timeS = $pg->dates ? date("H:i",$pg->dates): Null;
                            $datePo = $pg->datepo  ? date("Y-m-d",$pg->datepo): Null;
                            $timePo = $pg->datepo  ? date("H:i",$pg->datepo): Null;
                            ?>
                            <div class="row">
                                <div class="col-md-6 form-group">Дата начала <b class="red">*</b><input type="date" name="dateS" value="<?=$dateS?>" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ" required></div>
                                <div class="col-md-6 form-group">Время начала<input type="time" name="timeS" value="<?=$timeS?>" id="timeS" class="form-control" maxlength="5" required></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">Дата окончания<input type="date" name="datePo" value="<?=$datePo?>" class="datepicker-here form-control" maxlength="20" placeholder="ДД.MM.ГГГГ"></div>
                                <div class="col-md-6 form-group">Время окончания<input type="time" name="timePo" value="<?=$timePo?>" id="timePo" class="form-control" maxlength="5"></div>
                            </div><hr>
                        <?php } ?>
                    </div>
                    <!--                    <div class="form-group">-->
                    <!--                        Краткое описание (<= 250 символов)-->
                    <!--                        <input type="text" name="desc" class="form-control" maxlength="250" value="<?=$pg->desc?>"  placeholder="Краткое описание">-->
                    <!--                    </div>-->
                    <!--                    <div class="form-group">-->
                    <!--                        Метатеги (вводятся через запятую, <= 250 символов)-->
                    <!--                        <input type="text" name="keywords" class="form-control" maxlength="250" value="<?=$pg->keywords?>" placeholder="Метатеги">-->
                    <!--                    </div>-->
                    <div class="form-group" style="max-width: 300px;">
                        Дата публикации:<input type="date" name="date_pub" value="<?=date('Y-m-d', $pg->date_pub)?>" class="datepicker-here form-control" maxlength="10" placeholder="ДД.MM.ГГГГ" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                        <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                    </div>
                    <div class="form-group mt-3">Прикрепить галерею
                        <select name="gallery" id="gallery" class="form-control" style="max-width: 300px;">
                            <option value="0"></option>
                            <?php $gallery = $DB->get_records('gallery',[]);
                            foreach($gallery as $alb){
                                $selected = '';
                                if($alb->id == $pg->album) $selected = 'selected';
                                ?>
                                <option value="<?=$alb->id?>" <?=$selected?>><?=$alb->name?></option>
                            <?php } ?>
                        </select>
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
                    form_data.append('img_size_w', <?=$IMG_WH_SIZE?>);
                    form_data.append('img_thumb_size',<?=$IMG_THUMB_SIZE?>);
                    form_data.append('img_dir', '<?=$CFG->dir_f.'/'.$pg->id?>');
                    form_data.append('fname', '<?=$pg->id.'.'.$IMG_FILE_TYPE?>');
                    form_data.append('fname_thumb', '<?=$pg->id.'_thumb.'.$IMG_FILE_TYPE?>');
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