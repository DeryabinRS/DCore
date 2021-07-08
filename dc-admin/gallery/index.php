<?php
$action = get_param('action');
$IMG_WH_SIZE = EVENTS_IMG_SIZE;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'gallery';
$CFG->dir_f = $CFG->dir_upl.'/uploads/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/uploads/uploads/'.$UPLOAD_FILE_DIR;
if(!is_dir($CFG->dir_f))mkdir($CFG->dir_f);
if(!$action) { ?>
    <div class="gallery mt-2">
        <?php
        $PAGINATION = funcPagination($_PAGE, 'id, name, date_create, visible', get_param('sheet', 0 ,'int'));
        //$query = $DB->get_records($_PAGE,[],true);
        //dpr($pages);
        ?>
        <div class="form-inline">
            <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
        </div>
        <?=$PAGINATION['pag']?>
        <table class="table table-sm mt-2">
            <thead class="thead-light"><tr>
                <th width="80"></th>
                <th width="100">Дата</th>
                <th>Наименование</th>
                <th width="40"><i class="fa fa-eye"></i></th>
                <th width="80"></th></tr></thead>
            <?php
            foreach ($PAGINATION['table'] as $row){
                $files = get_files_tree($CFG->dir_f.'/'.$row->id);
                //dpr($files['files'][0]);
                ?>
                <tr>
                    <td><div class="gallery_box"><img src="<?=$CFG->dir_f_show.'/'.$row->id.'/'.$files['files'][0]?>" class="gallery_box_img"></div></td>
                    <td><?=date('d.m.Y',$row->date_create)?></td>
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

    </div>
<?php }elseif($action == 'add'){
    if(isset($_POST['add'])){
        $error = [];
        $date = isset($_POST['date']) ? strtotime(get_param('date')): $error[] = 'Установите дату';
        $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
        $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование (Eng)';
        if(empty($_FILES)){$error[] = 'Выберите файлы';}
        //if(is_dir($dir)){$error[] = 'Такой альбом уже существует';}
        if(empty($error)){
            $frm = [
                'name' => $name,
                'name_en' => $name_en,
                'date' => $date,
                'author' => $USER->id,
                'date_create' => $_SERVER['REQUEST_TIME'],
            ];
            $id = $DB->insert_record($_PAGE, $frm);
            if($id){
                $dir = $CFG->dir_f.'/'.$id;
                mkdir($dir);
                $i = 0;
                $files = funArrFilesForMultiload('files');
                foreach ($files as $f){
                    if($i < 10)$f_name = '00'.$i;
                    elseif ($i >= 10 and $i < 100) $f_name = '0'.$i;
                    elseif ($i >= 100 and $i < 1000) $f_name = $i;
                    funSaveFile($f,$dir,$f_name,$IMG_FILE_TYPE,$IMG_WH_SIZE);
                    $i++;
                    if($i >= 1000) break;
                }
                echo '<div class="alert alert-success">Альбом создан</div>';
                echo '<a href="'.SITE_URL_ADM.'/?page='.$_PAGE.'" class="btn btn-success">Вернуться к списку</a>';
                exit;
            }
        }else{
            foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
        }
    }
    ?>
    <script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
    <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
    <script>
        $(function(){
            $('#frm').validate({
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
                    name_en:{required: true, minlength: 3,maxlength:50,},
                    date:{required: true, date: true},
                },
            });
        });
    </script>
    <form id="frm" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <input type="date" class="form-control" name="date" placeholder="дд.мм.гггг" required style="width: 200px">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="name" placeholder="Наименование" required style="width: 300px">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="name_en" placeholder="Наименование (Eng)" required style="width: 300px">
        </div>
        <div class="form-group">
            <input type="file" name="files[]" accept="image/*" multiple required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-success" name="add" value="Создать альбом">
        </div>
    </form>
<?php }elseif($action == 'del'){
    $id = get_param('id', 'int');
    if($id){
        $DB->delete_records($_PAGE,['id' => $id]);
        if (is_dir($CFG->dir_f.'/'.$id)) {
            recursiveRemoveDir($CFG->dir_f.'/'.$id);
            echo '<div class="alert alert-danger">Запись удалена.</div>';
        }else{
            echo '<div class="alert alert-danger">Ошибка. Папка отсутствует</div>';
        }
        echo '<a href="'.SITE_URL_ADM.'/?page='.$_PAGE.'" class="btn btn-success">Вернуться к списку</a>';
    }else{print '<div class="alert alert-danger">Ошибка удаления записи.</div>';}
}elseif($action == 'chg'){
    $id = get_param('id', 'int');
    //dpr($id);
    if($id) {
        if (isset($_POST['chg'])) {
            $error = [];
            $date = isset($_POST['date']) ? strtotime(get_param('date')): $error[] = 'Установите дату';
            $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
            $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование (Eng)';
            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'name' => $name,
                    'name_en' => $name_en,
                    'date' => $date,
                ];
                //dpr($frm);
                $DB->update_record ($_PAGE, $frm);
                echo '<div class="alert alert-success">Изменения внесены</div>';
            }else{
                foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
            }
        }
        $pg = $DB->get_record($_PAGE,['id' => $id]);
        //dpr($pg->date);
        ?>
        <script src="<?=SITE_URL?>/inc/validator/jquery.validate.js"></script>
        <script src="<?=SITE_URL?>/inc/validator/messages_ru.js"></script>
        <script>
            $(function(){
                $('#frm').validate({
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
                        name_en:{required: true, minlength: 3,maxlength:50,},
                        date:{required: true, date: true},
                    },
                });
            });
        </script>
        <form id="frm" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="date" class="form-control" name="date" placeholder="дд.мм.гггг" value="<?=date('Y-m-d',$pg->date)?>" required style="width: 200px">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Наименование" value="<?=$pg->name?>" required style="width: 300px">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="name_en" placeholder="Наименование (Eng)" value="<?=$pg->name_en?>" required style="width: 300px">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" name="chg" value="Сохранить">
            </div>
        </form>

        <form id="fileupload" action="upload.php" method="POST" enctype="multipart/form-data">

        </form>
        <div id="get_img_files"></div>
        <script>
            $.post('../../ajaxscripts/ajax_get_files_img.php',
                {
                    dir: "<?=$CFG->dir_f.'/'.$id?>",
                    dir_show: "<?=$CFG->dir_f_show.'/'.$id?>"
                }, function(data){
                $('#get_img_files').html(data);
            });
        </script>
        <div class="mt-3">
            <div><b>Папка альбома:</b> <b class="red">gallery/<?=$id?></b></div>
            <a href="<?=SITE_URL_ADM.'/?page=filemanager'?>" class="btn btn-secondary">Редактировать фото</a>
        </div>
    <?php }else{print '<div class="alert alert-danger">Ошибка записи.</div>';}
}