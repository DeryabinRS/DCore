<?php
$action = get_param('action');
$IMG_WH_SIZE = 250;
$IMG_THUMB_SIZE = EVENTS_IMG_THUMB_SIZE;
$IMG_FILE_TYPE = 'jpg';
$UPLOAD_FILE_DIR = 'feedback';
$CFG->dir_f = $CFG->dir_img.'/'.$UPLOAD_FILE_DIR;
$CFG->dir_f_show = SITE_URL. '/img/'.$UPLOAD_FILE_DIR;
$IMG_TEMPLATE =  SITE_URL. '/img/static/icon_course.png';

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, '*', get_param('sheet', 0 ,'int'));
    ?>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th><i class="fa fa-calendar"></i></th>
            <th width="45"><i class="fa fa-image"></i></th>
            <th>email</th>
            <th><i class="fa fa-user"></i> name</th>
            <th width="40"><i class="fa fa-eye"></i></th>
            <th width="80"></th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){
            $img_dir = $CFG->dir_f.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE;
            $img = file_exists($img_dir) ? $CFG->dir_f_show.'/'.$row->id.'/'.$row->id.'.'.$IMG_FILE_TYPE : $IMG_TEMPLATE;
            ?>
            <tr>
                <td><?=date("d-m-Y",$row->date_create)?></td>
                <td><div class="gallery_box"><img src="<?=$img?>" class="gallery_box_img"></div></td>
                <td><?=$row->email?></td>
                <td><?=$row->firstname. ' ' .$row->lastname?></td>
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
}elseif($action == 'del'){
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
            $email = isset($_POST['email']) ? get_param('email'): $error[] = 'Введите email';
            $date_create = isset($_POST['date_create']) ? get_param('date_create', '', 'date'): $error[] = 'Введите дату';
            $firstname = isset($_POST['firstname']) ? get_param('firstname'): $error[] = 'Введите имя';
            $lastname = isset($_POST['lastname']) ? get_param('lastname'): $error[] = 'Введите фамилию';
            $country = isset($_POST['country']) ? get_param('country'): $error[] = 'Введите страну';

            $message = get_param('message');
            $visible = isset($_POST['visible']) ? 1 : 0;

            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'email' => $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'country' => $country,
                    'date_create' => $date_create,
                    'visible' => $visible,
                    'message' => $message,

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
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Email</span>
                                </div>
                                <input type="text" class="form-control maxw300" name="email" value="<?=$pg->email?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Дата</span>
                                </div>
                                <input type="date" class="form-control maxw200" name="date_create" value="<?=date("Y-m-d",$pg->date_create)?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Имя</span>
                                </div>
                                <input type="text" class="form-control maxw300" name="firstname" value="<?=$pg->firstname?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Фамилия</span>
                                </div>
                                <input type="text" class="form-control maxw300" name="lastname" value="<?=$pg->lastname?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Страна</span>
                                </div>
                                <input type="text" class="form-control maxw300" name="country" value="<?=$pg->country?>" maxlength="100" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                        <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
                    </div>

                    <hr/>

                    <div class="form-group">
                        Сообщение
                        <textarea id="TINYArea" class="form-control" rows="3" name="message"><?=htmlspecialchars_decode($pg->message)?></textarea>
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