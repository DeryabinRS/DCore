<div class="lk_user btn-group">
    <?php if($USER->id){?>
        <a href="<?=SITE_URL?>/users" class="btn btn-admin" data-toggle="tooltip" title="<?=$LANGJSON['frm_account']['name'][$LANGUAGE]?>"><i class="fa fa-user"></i></a>
        <?php
        if($USER->team) {
            echo '<a href="'.SITE_URL.'/teams/?id='.$USER->team.'" class="btn btn-admin" data-toggle="tooltip" title="'.$LANGJSON['teams']['team'][$LANGUAGE].'"><i class="fa fa-users"></i>';
            $TEAM_ADMIN = $DB->get_record('teams', ['admin' => $USER->id]);
            if ($TEAM_ADMIN) {
                $count_request = $DB->get_num_rows('SELECT id FROM '.$CFG->db['prefix'].'teams_requests WHERE team = ' . $TEAM_ADMIN->id);
                if ($count_request) {
                    echo  '<span>'.$count_request.'</span>';
                }
            }
            echo '</a>';
        }
        ?>
        <?php if($_SESSION['USER']['status']) echo '<a href="'.SITE_URL_ADM.'" class="btn btn-admin" data-toggle="tooltip" title="'.$LANGJSON['frm_user']['admin'][$LANGUAGE].'"><i class="fa fa-star fa-spin"></i></a>' ?>
        <a href="<?=SITE_URL?>/?auth=exit" class="btn btn-admin" data-toggle="tooltip" title="<?=$LANGJSON['frm_user']['exit'][$LANGUAGE]?>"><i class="fa fa-sign-out"></i></a>
    <?php }else{?>
        <a href="<?=SITE_URL?>/users/?usr=auth" class="btn btn-admin" data-toggle="tooltip" title="<?=$LANGJSON['frm_auth']['auth'][$LANGUAGE]?>"><i class="fa fa-sign-in"></i></a>
        <a href="<?=SITE_URL?>/users/?usr=reg" class="btn btn-admin" data-toggle="tooltip" title="<?=$LANGJSON['frm_reg']['reg'][$LANGUAGE]?>"><i class="fa fa-user-plus""></i></a>
    <?php } ?>
</div>