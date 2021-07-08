<?php
$img_width = BANNER_TOP_W;
$img_height = BANNER_TOP_H;
$UPLOAD_FILE_DIR = 'banners';
$CFG->dir_f = $CFG->dir_img.'/'.$UPLOAD_FILE_DIR;
if (isset($_POST['add'])){
    $err = [];
    if(empty($_POST['date1'])){$err[] = "Отсутствует дата начала показа";}
    if(empty($_POST['date2'])){$err[] = "Отсутствует дата окончания показа";}

    if (!empty($_FILES['img'])){
        if (is_uploaded_file($_FILES['img']['tmp_name'])){
            $img_tmp_name = $_FILES['img']['tmp_name'];
            //dpr(file_exists($img_tmp_name));
            $img_type = $_FILES['img']['type'];
            $img_size = $_FILES['img']['size'];
            $size = getimagesize($img_tmp_name);
            if ($size[0] != $img_width or $size[1] != $img_height){
                $err[] = 'Изображение должно быть '.$img_width.' х '.$img_height.' px';
            }
        }else{$err[] = "Отсутствует изображение";}
    }else{$err[] = "Отсутствует изображение";}
    # Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0){
        $vis = ($_POST['visible'] == 'Yes') ?  1 : 0;
        $img = $_SERVER['REQUEST_TIME'];
        $fields = [
            'img' => $img,
            'date1' => strtotime($_POST['date1']),
            'date2' => strtotime($_POST['date2']),
            'position' => $_POST['position'],
            'link' => $_POST['link'],
            'visible' => $vis
        ];
        $img_name = $img.'.jpg';
        if(!move_uploaded_file($img_tmp_name, '../img/banners/'.$img_name)) echo 'Ошибка загрузки изображения';
        $query = $DB->insert_record($_PAGE, $fields);
        //header('Refresh: 1; URL='.SITE_URL.'/'.PATH_ADMIN.'/?page=9');
        print '<div class="col-lg-12"><div class="alert alert-success">Запись добавлена</div></div>';
        //exit;
    }
    else{echo '<div class="col-lg-12">'; foreach($err AS $error){print '<div class="alert alert-danger">'.$error.'</div>';} echo '</div>';}
}
///////////////ОБНОВИТЬ ИНФОРМАЦИЮ/////////////////
if (isset($_POST['upd'])){
    $err = array();
    if(empty($_POST['id']))$err[] = "Системная ошибка. Обратитесь к администратору";
    if(empty($_POST['date1']))$err[] = "Отсутствует дата начала показа";
    # Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0){
        $vis = ($_POST['visible'] == 'Yes') ?  1 : 0;
        $fields = [
            'id' => $_POST['id'],
            'date1' => strtotime($_POST['date1']),
            'date2' => strtotime($_POST['date2']),
            'position' => $_POST['position'],
            'link' => $_POST['link'],
            'visible' => $vis,
        ];
        $query = $DB->update_record($_PAGE, $fields);
        print '<div class="col-lg-12"><div class="alert alert-success">Информация обновлена.</div></div>';
    }else{echo '<div class="col-lg-12">'; foreach($err AS $error){print '<div class="alert alert-danger">'.$error.'</div>';} echo '</div>';}
}
///////////////УДАЛИТЬ СТРОКУ/////////////////
if (isset($_POST['del'])){
    $err = array();
    if(empty($_POST['id']))$err[] = "Системная ошибка. Обратитесь к администратору";
    # Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0){
        $id = $_POST['id'];
        $img = $_POST['img'];
        if (file_exists($CFG->dir_f.'/'.$img)) unlink($CFG->dir_f.'/'.$img);
        $query = $DB->delete_records($_PAGE,['id' => $id]);
        print '<div class="col-lg-12"><div class="alert alert-danger">Запись удалена</div></div>';
    }else{echo '<div class="col-lg-12">'; foreach($err AS $error){print '<div class="alert alert-danger">'.$error.'</div>';} echo '</div>';}
}
//dpr(date('d.m.Y', $_SERVER['REQUEST_TIME']));
?>

<script src="<?=SITE_URL?>/js/bootstrap_file_input.js"></script>
<script type="text/javascript">
    jQuery(function($){
//Преобразовать выбор файла в кнопку
        $('input[type=file]').bootstrapFileInput();
        $('.file-inputs').bootstrapFileInput();
    });
</script>

<form enctype="multipart/form-data" method="post">
    <div class="row" style="border: 1px solid #ccc; padding: 15px 0; margin: 0">
        <div class="col-lg-8">
            <div><img src="<?=SITE_URL?>/img/banners/0.jpg " id="target" class="image_preview img-fluid" style="max-height: 250px"></div>
            <div>
            <b style="font-size: 17px;font-family: serif;">Загрузить баннер (<i class="red"><?=$img_width?> х <?=$img_height?></i> пикселей!)<i class="red">*</i></b><br>
                <input id="image" class="btn btn-success" name="img" type="file" title="Выбрать файл"></div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                Дата начала показа:<i class="red">*</i>
                <input name="date1" class="form-control" style="width:180px" type="date" value="<?=date('Y-m-d', $_SERVER['REQUEST_TIME'])?>" placeholder="Дата нач.">
            </div>
            <div class="form-group">
                Дата окончания показа (включ.):<i class="red">*</i>
                <input name="date2" class="form-control" style="width:180px" type="date" value="<?=date('Y-m-d', $_SERVER['REQUEST_TIME'])?>" placeholder="Дата оконч.(включ.)">
            </div>
            <div class="form-group">
                Позиция показа:
                <input name="position" class="form-control" style="width:50px" type="text" value="1" maxlength="2" placeholder="№">
            </div>
            <div class="form-group">
                Ссылка:
                <input name="link" class="form-control" type="text" placeholder="Ссылка (url)">
            </div>
            <div class="form-group">
                <input name="visible" type="checkbox" value="Yes" checked> - Показать на сайте
                <button class="btn btn-success" name="add" type="submit" style="float:right;">Добавить баннер</button>
            </div>
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

<table class="admintab">
    <thead>
    <tr>
        <td>№</td>
        <td>Баннер</td>
        <td>Дата от</td>
        <td>Дата до</td>
        <td><i class="fa fa-sort"></i></td>
        <td>Ссылка</td>
        <td><i class="fa fa-eye"></i></td>
        <td class="hidden-print"><i class="fa fa-refresh"></i></td>
    </tr>
    </thead>
    <?php
    $query = $DB->get_records($_PAGE,[]);
    $i = 1;
    foreach($query as $row){ ?>
        <tr><form action="" method="post">
                <td>
                    <?=$i?><input name="id" style="display:none;" value="<?=$row->id?>" class="form-control">
                    <input name="img" style="display:none;" value="<?=$row->img?>.jpg" class="form-control">
                </td>
                <td width="200"><img src="<?=SITE_URL?>/img/banners/<?=$row->img?>.jpg" class="img-fluid" alt=""></td>
                <td width="115"><input type="date" name="date1" maxlength="10" placeholder="ГГГГ-ММ-ДД" value="<?=date('Y-m-d',$row->date1)?>" class="form-control"></td>
                <td width="115"><input type="date" name="date2" maxlength="10" placeholder="ГГГГ-ММ-ДД" value="<?=date('Y-m-d',$row->date2)?>" class="form-control"></td>
                <td width="60"><input type="text" name="position" maxlength="2" placeholder="№" value="<?=$row->position?>" class="form-control"></td>
                <td width="350"><input type="text" name="link" maxlength="100" placeholder="Ссылка (url)" value="<?=$row->link?>" class="form-control"></td>
                <td class="hidden-print"><input name="visible" type="checkbox" value="Yes" <?php if($row->visible) echo 'checked';?>></td>
                <td class="hidden-print">
                    <div class="btn-group">
                        <button class="btn btn-success" name="upd" type="submit"><i class="fa fa-refresh"></i></button>
                        <button class="btn btn-danger" name="del" type="submit" onclick="return confirmDelete();"><i class="fa fa-trash"></i></button>
                    </div>
                </td>
            </form>
        </tr>
    <?php $i++; } ?>
</table>