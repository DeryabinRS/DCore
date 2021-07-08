<?php
$action = get_param('action');

if(!$action) {
    $PAGINATION = funcPagination($_PAGE, 'id, name, alias, visible', get_param('sheet', 0 ,'int'));
    //$query = $DB->get_records($_PAGE,[],true);
    //dpr($pages);
    ?>
    <div class="form-inline">
        <a href="<?=SITE_URL_ADM.'?page='.$_PAGE?>&action=add" class="btn btn-success"><i class="fa fa-plus"></i> Добавить запись</a>
    </div>
    <?=$PAGINATION['pag']?>
    <table class="table table-sm mt-2">
        <thead class="thead-light"><tr>
            <th>Наименование</th>
            <th>Алиас</th>
            <th><i class="fa fa-eye"></i></th>
            <th>Ред</th></tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){ ?>
            <tr>
                <td><?=$row->name?></td>
                <td><?=$row->alias?></td>
                <td><?php if($row->visible){echo '<i class="fa fa-eye"></i>';}else{echo '<i class="fa fa-eye-slash"></i>';}?></td>
                <td>
                    <div class="btn-group btn-group-toggle">
                    <a href="?page=<?=$_PAGE?>&action=del&id=<?php echo $row->id;?>" onclick="return confirmDelete();" class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Удалить"><i class="fa fa-trash"></i></a>
                    <a href="?page=<?=$_PAGE?>&action=chg&id=<?php echo $row->id;?>" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Редактировать"><i class="fa fa-pencil"></i></a>
                    <!--<a href="<?=fGetURL('?pgs='.$_PAGE.'&alias='.$row->alias)?>" target="_blank" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Посмотреть на сайте - <?php echo $row->name; ?>">
                        <i class="fa fa-reply"></i>
                    </a>-->
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
        //$type = isset($_POST['type']) ? get_param('type', 'int'): $error[] = 'Введите тип события';
        $desc = get_param('desc');
        $keywords= get_param('keywords');
        $content = $_POST['content'];
        $content_en = $_POST['content_en'];
        $visible = isset($_POST['visible']) ? 1 : 0;
        $period = isset($_POST['period']) ? 1 : 0;
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
                //'type' => $type,
                'desc' => $desc,
                'keywords' => $keywords,
                'content' => htmlspecialchars(get_param($content)),
                'content_en' => htmlspecialchars(get_param($content_en)),
                'visible' => $visible,
                'author' => $USER->id,
                'period' => $period,
                'date_create' => $_SERVER['REQUEST_TIME'],
            ];
            //dpr($frm);
            $DB->insert_record($_PAGE, $frm);
            echo '<div class="alert alert-success">Запись добавлена</div>';
        }else{
            foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
        }
    }
    ?>
    <form method="POST">
        <div class="form-group">
            Наименование<i class="red">*</i> (<= 50 символов)
            <input type="text" name="name" class="form-control" maxlength="50" placeholder="Наименование" required>
        </div>
        <div class="form-group">
            Наименование (Eng)<i class="red">*</i> (<= 50 символов)
            <input type="text" name="name_en" class="form-control" maxlength="50" placeholder="Наименование (Eng)" required>
        </div>
<!--        <div class="form-group">-->
<!--            Краткое описание (<= 250 символов)-->
<!--            <input type="text" name="desc" class="form-control" maxlength="250" placeholder="Краткое описание">-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            Метатеги (вводятся через запятую, <= 250 символов)-->
<!--            <input type="text" name="metateg" class="form-control" maxlength="250" placeholder="Метатеги">-->
<!--        </div>-->
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" checked>
            <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck2" name="period">
            <label class="form-check-label" for="exampleCheck2">Указывать временные рамки событий (актуально для мероприятий)</label>
        </div>
<!--        <div class="form-group">-->
<!--            Контент<i class="red">*</i>-->
<!--            <textarea id="TINYArea" class="form-control" rows="3" name="content"></textarea>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            Контент (Eng)<i class="red">*</i>-->
<!--            <textarea id="TINYArea2" class="form-control" rows="3" name="content_en"></textarea>-->
<!--        </div>-->
        <div class="form-group mt-3">
            <input type="submit" class="btn btn-success" name="add" value="Добавить запись">
        </div>
    </form>
<!--    <script src="--><?//=SITE_URL?><!--/inc/tinymce/tinymce.min.js"></script>-->
<!--    <script src="--><?//=SITE_URL?><!--/js/tinymce.js"></script>-->
<!--    <script>-->
<!--        tinymce.init({-->
<!--            selector:'#TINYArea2',-->
<!--            language: 'ru',-->
<!--            forced_root_block : '',-->
<!--            height: 250,-->
<!--            plugins: [-->
<!--                'advlist autolink lists link image charmap print preview hr anchor pagebreak',-->
<!--                'searchreplace wordcount visualblocks visualchars code fullscreen',-->
<!--                'insertdatetime media nonbreaking save table contextmenu directionality',-->
<!--                'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager'-->
<!--            ],-->
<!--            toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' +-->
<!--                '| link unlink image print preview media | forecolor backcolor emoticons | removeformat responsivefilemanager',-->
<!--            image_advtab: true,-->
<!--            relative_urls : false,-->
<!--            templates: [-->
<!--                { title: 'Test template 1', content: 'Test 1' },-->
<!--                { title: 'Test template 2', content: 'Test 2' }-->
<!--            ],-->
<!--            image_advtab: true ,-->
<!---->
<!--            external_filemanager_path:"/inc/filemanager/",-->
<!--            filemanager_title:"Responsive Filemanager" ,-->
<!--            external_plugins: { "filemanager" : "/inc/filemanager/plugin.min.js"}-->
<!--        });-->
<!--    </script>-->
<?php }elseif($action == 'del'){
    $id = get_param('id', 'int');
    if($id){
        $DB->delete_records($_PAGE,['id' => $id]);
        print '<div class="alert alert-danger">Запись удалена.</div>';
    }else{print '<div class="alert alert-danger">Ошибка удаления записи.</div>';}
}elseif($action == 'chg'){
    $id = get_param('id', 'int');
    if($id){
        if(isset($_POST['chg'])){
            $error = [];
            $name = isset($_POST['name']) ? get_param('name'): $error[] = 'Введите наименование';
            $name_en = isset($_POST['name_en']) ? get_param('name_en'): $error[] = 'Введите наименование (Eng)';
            $alias = isset($_POST['alias']) ? get_param('alias'): $error[] = 'Введите алиас';
            $desc = get_param('desc');
            $keywords = get_param('keywords');
            $content = htmlspecialchars(get_param('content'));
            $content_en = htmlspecialchars(get_param('content_en'));
            //dpr($content );
            $visible = isset($_POST['visible']) ? 1 : 0;
            $period = isset($_POST['period']) ? 1 : 0;
            $alias_db = $DB->get_record_sql("SELECT alias FROM ".$CFG->db['prefix'].$_PAGE." WHERE alias = \"$alias\" AND NOT  id = $id");
            //dpr($alias_db);
            if($alias_db) $error[] = 'Такой алиас уже существует';
            if(!count($error)){
                $frm = [
                    'id' => $id,
                    'name' => $name,
                    'name_en' => $name_en,
                    'alias' => funcRusToLat($alias),
                    'desc' => $desc,
                    'keywords' => $keywords,
                    'content' => $content,
                    'content_en' => $content_en,
                    'visible' => $visible,
                    'period' => $period,
                ];
                //dpr($frm);
                $DB->update_record ($_PAGE, $frm);
                echo '<div class="alert alert-success">Изменения внесены</div>';
            }else{
                foreach($error AS $err){print '<div class="alert alert-danger">'.$err.'</div>';}
            }
        }
        $pg = $DB->get_record($_PAGE,['id' => $id]); ?>
        <form method="POST">
            <div class="form-group">
                Наименование<i class="red">*</i> (<= 50 символов)
                <input type="text" name="name" class="form-control" maxlength="50" value="<?=$pg->name?>" placeholder="Наименование" required>
            </div>
            <div class="form-group">
                Наименование (Eng)<i class="red">*</i> (<= 50 символов)
                <input type="text" name="name_en" class="form-control" maxlength="50" value="<?=$pg->name_en?>" placeholder="Наименование (Eng)" required>
            </div>
            <div class="form-group">
                Алиас<i class="red">*</i> (<= 100 латинских символов)
                <input type="text" name="alias" class="form-control" maxlength="250" value="<?=$pg->alias?>" placeholder="Нужен для отображения в адресной строке">
            </div>
<!--            <div class="form-group">-->
<!--                Краткое описание (<= 250 символов)-->
<!--                <input type="text" name="desc" class="form-control" maxlength="250" value="<?//=$pg->desc?>"  placeholder="Краткое описание">-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                Метатеги (вводятся через запятую, <= 250 символов)-->
<!--                <input type="text" name="keywords" class="form-control" maxlength="250" value="<?//=$pg->keywords?>" placeholder="Метатеги">-->
<!--            </div>-->
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="visible" <?php if($pg->visible) echo 'checked';?>>
                <label class="form-check-label" for="exampleCheck1">Видимость для всех пользователей</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck2" name="period" <?php if($pg->period) echo 'checked';?>>
                <label class="form-check-label" for="exampleCheck2">Указывать временные рамки событий (актуально для мероприятий)</label>
            </div>
<!--            <div class="form-group">-->
<!--                Контент-->
<!--                <textarea id="TINYArea" class="form-control" rows="3" name="content"><?//=$pg->content?></textarea>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                Контент (Eng)-->
<!--                <textarea id="TINYArea2" class="form-control" rows="3" name="content_en"><?//=$pg->content_en?></textarea>-->
<!--            </div>-->
            <div class="form-group mt-3">
                <input type="submit" class="btn btn-success" name="chg" value="Сохранить">
            </div>
        </form>
<!--        <script src="--><?php //echo SITE_URL; ?><!--/inc/tinymce/tinymce.min.js"></script>-->
<!--        <script src="--><?php //echo SITE_URL; ?><!--/js/tinymce.js"></script>-->
<!--        <script>-->
<!--            tinymce.init({-->
<!--                selector:'#TINYArea2',-->
<!--                language: 'ru',-->
<!--                forced_root_block : '',-->
<!--                height: 250,-->
<!--                plugins: [-->
<!--                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',-->
<!--                    'searchreplace wordcount visualblocks visualchars code fullscreen',-->
<!--                    'insertdatetime media nonbreaking save table contextmenu directionality',-->
<!--                    'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager'-->
<!--                ],-->
<!--                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' +-->
<!--                    '| link unlink image print preview media | forecolor backcolor emoticons | removeformat responsivefilemanager',-->
<!--                image_advtab: true,-->
<!--                relative_urls : false,-->
<!--                templates: [-->
<!--                    { title: 'Test template 1', content: 'Test 1' },-->
<!--                    { title: 'Test template 2', content: 'Test 2' }-->
<!--                ],-->
<!--                image_advtab: true ,-->
<!---->
<!--                external_filemanager_path:"/inc/filemanager/",-->
<!--                filemanager_title:"Responsive Filemanager" ,-->
<!--                external_plugins: { "filemanager" : "/inc/filemanager/plugin.min.js"}-->
<!--            });-->
<!--        </script>-->
    <?php }else{print '<div class="alert alert-danger">Ошибка записи.</div>';}
}

