<?php
$news = $DB->get_records('news',['visible' => 1],'date_pub DESC, id DESC LIMIT 8');
?>
<div class="news mt-3">
    <?php
    $i = 0;
    //dpr($news);
    foreach($news as $row){
        $row = (array) $row;
        $path_img = $CFG->dir_upl.'/uploads/img/events/'.$row['id'].'/'.$row['id'].'_thumb.jpg';
        $new_img = is_file($path_img) ? SITE_URL.'/uploads/uploads/img/events/'.$row['id'].'/'.$row['id'].'_thumb.jpg' : '/img/static/new.png';
        $new_url = fGetURL('?pgs=news&alias='.$row['alias']);
        $desc = strip_tags(html_entity_decode($row['content'.$LANGSQL]));
        $desc = substr($desc, 0, 150); $desc = rtrim($desc, "!,.-"); $desc = substr($desc, 0, strrpos($desc, ' '));
        $type_new = $DB->get_record('news_types',['id'=>$row['type']],"name, name_en");
        $type_new = (array) $type_new;
        $event_day = '';
        $event_date = '';
        $event_time = '';
        if($row['dates']) {
            $date = $_SERVER['REQUEST_TIME'];
            $date_tomorrow = strtotime('+1 day', $date);
            if (date('d.m.Y', $date) == date('d.m.Y', $row['dates'])) {
                $event_color = 'event_orange';
                $event_day = '<div class="event_day '.$event_color.'">' . $LANGJSON['news']['today'][$LANGUAGE] . '</div>';
            }elseif (date('d.m.Y', $date_tomorrow) == date('d.m.Y', $row['dates'])) {
                $event_color = 'event_orange';
                $event_day = '<div class="event_day '.$event_color.'">' . $LANGJSON['news']['tomorrow'][$LANGUAGE] . '</div>';
            }else{
                $event_color = 'event_gray';
                $event_date = date('d.m.Y',$row['dates']);
                $event_time = date('H:i', $row['dates']);
                $event_day = '<div class="event_day '.$event_color.'">'.$event_date.' '.$event_time. '</div>';
            }
//                    $event_date = '<div class="event_date">'.date('d.m.Y',$row['dates']).'</div>';
//                    if(date('H:i',$row['dates']) != '00:00') {
//                        $event_time = '<div class="event_time">' . date('H:i', $row['dates']) . '</div>';
//                    }
        }
        ?>
        <div class="card">
            <?=$event_day?>
            <div class="card-img">
                <a href="<?=$new_url?>" style="display: inline-block">
                    <img src="<?=$new_img?>" class="img-fluid">
                </a>
            </div>
            <div class="card-body">
                <div class="text-muted"><?=$type_new['name'.$LANGSQL]?></div>
                <div><?=date("d.m.Y", $row['date_pub'])?></div>
                <div class="card-title"><a href="<?=$new_url?>"><?=$row['name'.$LANGSQL]?></a></div>
                <?php //echo $desc.'…'?>
                <a href="<?=$new_url?>" class="btn btn-light d-block"><?=$LANGJSON['news']['read_more'][$LANGUAGE]?></a>
            </div>
        </div>
    <?php } ?>
</div>
<div class="mt-3"><a href="<?=SITE_URL?>/news" class=""><?=$LANGJSON['news']['all_news'][$LANGUAGE]?> <i class="fa fa-angle-right" style="font-size: 15px;"></i></a></div>
