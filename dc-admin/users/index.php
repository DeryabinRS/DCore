<?php
$action = get_param('action');

if(!$action) {?>
    <!--<div class="row">
        <div class="col-12">
            <i class="fa fa-envelope"></i> - Подтверждение Email<br>
            <i class="fa fa-times"></i> - Блокировка пользователя<br>
            <i class="fa fa-eye"></i> - Просмотр сведений
        </div>
    </div>-->
    <?php $PAGINATION = funcPagination($_PAGE, 'id, login, email, firstname, lastname, activate, status, block, time_reg, member', get_param('sheet', 0 ,'int'));
    $PAGINATION['pag']?>
    <div class="table-responsive-sm">
    <table class="table table-sm">
        <thead class="thead-light"><tr>
<!--            <th><i class="fa fa-user"></i></th>-->
            <th><i class="fa fa-calendar" data-toggle="tooltip" data-original-title="Дата регистрации"></i></th>
            <!--<th>Ф.И.О.</th>-->
            <th><i class="fa fa-at"></i></th>
            <th>Статус</th>
            <th><i data-toggle="tooltip" data-placement="bottom" data-original-title="Подтверждение Email" class="fa fa-envelope"></i></th>
            <th><i data-toggle="tooltip" data-placement="bottom" data-original-title="Блокировка пользователя" class="fa fa-times"></i></th>
            <th class="text-center" width="40"><i data-toggle="tooltip" data-placement="bottom" data-original-title="Просмотр профиля" class="fa fa-eye"></i></th>
        </tr></thead>
        <?php
        foreach ($PAGINATION['table'] as $row){ ?>
            <tr>
                <!--<td><?php if($row->member == 0){echo '<i class="fa fa-user"></i>';}else{echo '<i class="fa fa-building-o"></i>';} ?></td>-->
                <td><?=date('d.m.Y',$row->time_reg)?></td>
                <!--<td><?=$row->lastname.' '.$row->firstname.' '.$row->surname?></td>-->
                <td><?=$row->email?></td>
                <td><?php $users_status = $DB->get_record('user_status',['id' => $row->status]);echo $users_status->name;?></td>
                <td><?php if($row->activate){echo '<i class="fa fa-envelope-open-o green"></i>';}else{echo '<i class="fa fa-envelope-o red"></i>';}?></td>
                <td><?php if($row->block){echo '<i class="fa fa-times red"></i>';}else{echo '';}?></td>
                <td>
                    <!--<a href="?page=<?=$_PAGE?>&action=del&id=<?php echo $row->id;?>" onclick="return confirmDelete();" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Удалить"><i class="fa fa-trash"></i></a>-->
                    <a href="?page=<?=$_PAGE?>&action=upd&id=<?php echo $row->id;?>" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Просморт профиля"><i class="fa fa-eye"></i></a>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>
    <?=$PAGINATION['pag']?>
    <?php
}elseif($action == 'upd'){
    $id = get_param('id', 'int');
    if($id){
       if($_SESSION['USER']['status'] >= 1 and $_SESSION['USER']['status'] <= 2) {$access_element = 'enabled';}else{$access_element = 'disabled';}
        if(isset($_POST['upd'])){
            $block = isset($_POST['block']) ? 1 : 0;
            if($_SESSION['USER']['status'] == 1){$status = $_POST['status'];}
            if($_SESSION['USER']['status'] == 2){$status = $_POST['status'] >= 2 ? $_POST['status']: 2;}
            $frm = [
                'id' => $id,
                'block' => $block,
                'status' => $status
            ];
            $DB->update_record ($_PAGE, $frm);
            echo '<div class="alert alert-success">Изменения внесены</div>';
        }
        $pg = $DB->get_record($_PAGE,['id' => $id]); ?>

        <form method="POST">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        Фамилия
                        <input type="text" name="lastname" class="form-control" value="<?=$pg->lastname?>" disabled>
                    </div>
                    <div class="col">
                        Имя
                        <input type="text" name="firstname" class="form-control" value="<?=$pg->firstname?>" disabled>
                    </div>
                    <div class="col">
                        Отчество
                        <input type="text" name="surname" class="form-control" value="<?=$pg->surname?>" disabled>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        Телефон
                        <input type="text" name="phone" class="form-control" value="<?=$pg->phone?>" disabled>
                    </div>
                    <div class="col-md-4">
                        Email
                        <input type="text" name="desc" class="form-control" value="<?=$pg->email?>" disabled>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        Страна
                        <input type="text" name="country" class="form-control" value="<?=$pg->country?>" disabled>
                    </div>
                    <div class="col-md-4">
                        Город
                        <input type="text" name="city" class="form-control" value="<?=$pg->city?>" disabled>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        Место работы / Учебы
                        <input type="text" name="work_study" class="form-control" value="<?=$pg->work_study?>" disabled>
                    </div>
                    <div class="col-md-4">
                        Cпециализация
                        <input type="text" name="specialty" class="form-control" value="<?=$pg->specialty?>" disabled>
                    </div>
                </div>
            </div>
            <!--<div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        Права доступа
                        <select class="form-control"  name="status" <?=$access_element?>>
                            <?php
                            $users_status = $DB->get_records('user_status',[]);
                            foreach ($users_status as $us){
                                $selected = $pg->status == 0 || $pg->status == $us->id ? 'selected' : '';
                                echo '<option value="'.$us->id.'" '.$selected.'>';
                                echo $us->name;
                                echo '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>-->

            <div class="form-group">
            Активация аккаунта:
            <?php if($pg->activate){echo '<i class="fa fa-check-square-o green"></i>';}else{echo '<i class="fa fa-square-o"></i>';}?>
            </div>
            <div class="form-group">
                Дата регистрации пользователя:
                <?=date('d.m.Y H:i:s',$pg->time_reg)?>
            </div>
            <?php if ($pg->status != 1){ ?>
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="block" <?php if($pg->block) echo 'checked';?>>
                    <label class="form-check-label" for="exampleCheck1"><b class="red">Блокировка пользователя</b></label>
                </div>
            </div>
            <input type="submit" class="btn btn-success" name="upd" value="Сохранить">
            <?php } ?>
        </form>
    <?php }else{print '<div class="alert alert-danger">Ошибка записи.</div>';}
}