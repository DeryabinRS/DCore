<?php
$alias = $rURL->arr_params[1] ? $rURL->arr_params[1] : Null;
$_CONTENT = '';
if($alias) {
    $new = $DB->get_record('news', ['alias' => $alias]);
    $type_new = $DB->get_record('news_types',['id'=>$new->type],"name");
    $event_day = '';
    $event_date = '';
    $event_time = '';
    if($new->dates){
        $date = $_SERVER['REQUEST_TIME'];
        $date_tomorrow = strtotime('+1 day',$date);
        if(date('d.m.Y',$date) == date('d.m.Y',$new->dates)) $event_day = '<div class="content_event_day"><i class="fa fa-star fa-spin"></i> СЕГОДНЯ</div>';
        if(date('d.m.Y',$date_tomorrow) == date('d.m.Y',$new->dates)) $event_day = '<div class="content_event_day"><i class="fa fa-star fa-spin"></i> ЗАВТРА</div>';
        $event_date = date('d.m.Y',$new->dates);
        if(date('H:i',$new->dates) != '00:00') {
            $event_time = date('H:i', $new->dates);
        }
        $event_date_content = '<div class="mb-2"><i class="fa fa-calendar-check-o"></i> Дата мероприятия: ' . $event_date . ' '.$event_time.'</div>';
    }
    $path_img = $CFG->dir_upl.'/uploads/img/events/'.$new->id.'/'.$new->id.'_thumb.jpg';
    $new_img = is_file($path_img) ? SITE_URL.'/uploads/uploads/img/events/'.$new->id.'/'.$new->id.'.jpg' : Null;
    if($new_img) {
        $content_img = '<img src="' . $new_img . '" class="content-img gallery">';
    }else{$content_img = Null;}

    $_CONTENT .= '<div class="container mt-4 mb-5 content">';
    $_CONTENT .= '<div class="row">';
    $_CONTENT .= '<div class="col-lg-12">';
    if ($alias) {
        if ($new) {
            $_PAGENAME = $new->name;
            //dpr($new->visible);
            if ($new->visible == 0 and $_SESSION['USER']['status'] == 0) {
                $new->content = '<div class="alert alert-danger">Доступ к странице закрыт</div>';
            } else {
                $_CONTENT .= '<div class="mb-2"><i class="fa fa-calendar"></i> Опубликовано: ' . date('d.m.Y', $new->date_create) . '</div>';
                $_CONTENT .= $event_date_content;
                $_CONTENT .= $content_img;
                $_CONTENT .= htmlspecialchars_decode($new->content);
            }
        } else {
            $_CONTENT .= '<div class="alert alert-danger">Страницы не существует</div>';
        }
    } else {
        $_CONTENT .= '<div class="alert alert-danger">Страницы не существует</div>';
    }
    $_CONTENT .= '</div></div></div>';
}

