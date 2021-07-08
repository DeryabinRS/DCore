<?php
$alias = !empty($rURL->arr_params[1]) ? $rURL->arr_params[1] : Null;
$_CONTENT = '';
if($alias) {
    $_CONTENT .= '<div class="content_news">';
    $new = $DB->get_record('news', ['alias' => $alias]);
    if ($new) {
        $new = (array)$new;
        $new_id = $new['id'];
        $type_new = $DB->get_record('news_types',['id'=>$new['type']],"name");
        $event_day = '';
        $event_date = '';
        $event_time = '';
        $event_date_content = Null;
        $dateS = $new['dates'];
        if($dateS){
            $date = $_SERVER['REQUEST_TIME'];
            $date_tomorrow = strtotime('+1 day',$date);
            if(date('d.m.Y',$date) == date('d.m.Y',$dateS)) $event_day = '<div class="content_event_day"><i class="fa fa-star fa-spin"></i> '.$LANGJSON['news']['today'][$LANGUAGE].'</div>';
            if(date('d.m.Y',$date_tomorrow) == date('d.m.Y',$dateS)) $event_day = '<div class="content_event_day"><i class="fa fa-star fa-spin"></i> '.$LANGJSON['news']['tomorrow'][$LANGUAGE].'</div>';
            $event_date = date('d.m.Y',$dateS);
            if(date('H:i',$dateS) != '00:00') {
                $event_time = date('H:i', $dateS);
            }
            $event_date_content = '<div class="mb-2">
                    <i class="fa fa-calendar-check-o"></i> '.$LANGJSON['news']['event_date'][$LANGUAGE].': ' . $event_date . ' '.$event_time.'
                    </div>';
        }
        $path_img = $CFG->dir_upl.'/uploads/img/events/'.$new_id.'/'.$new_id.'_thumb.jpg';
        $new_img = is_file($path_img) ? SITE_URL.'/uploads/uploads/img/events/'.$new_id.'/'.$new_id.'.jpg' : Null;
        if($new_img) {
            $content_img = '<img src="' . $new_img . '" class="content-img gallery">';
        }else{$content_img = Null;}

        $_PAGENAME = $new['name'.$LANGSQL];
        //dpr($new->visible);
        if ($new['visible'] == 0 and @$_SESSION['USER']['status'] == 0) {
            $new['content'] = '<div class="alert alert-danger">'.$LANGJSON['page']['access_off'][$LANGUAGE].'</div>';
        } else {
            $_CONTENT .= '<div class="mb-3"><a href="'.SITE_URL.'">'.$LANGJSON['page']['home'][$LANGUAGE].'</a> | <a href="'.SITE_URL.'/news">'.$LANGJSON['news']['title'][$LANGUAGE].'</a> | <a href="'.SITE_URL.'/news/'.$alias.'">'.$_PAGENAME.'</a></div>';
            $_CONTENT .= '<div class="mb-2"><i class="fa fa-calendar"></i> '.$LANGJSON['news']['published_date'][$LANGUAGE].': ' . date('d.m.Y', $new['date_pub']) . '</div>';
            $_CONTENT .= $event_date_content;
            //$_CONTENT .= $content_img;
            $_CONTENT .= htmlspecialchars_decode($new['content'.$LANGSQL]);
        }

    } else {
        $_CONTENT .= '<div class="alert alert-danger">'.$LANGJSON['page']['error_404'][$LANGUAGE].'</div>';
    }
    $_CONTENT .= '</div>';
}else{
    $_PAGENAME = $LANGJSON['news']['title'][$LANGUAGE];
    $PAGINATION = funcPagination('news', '*', get_param('sheet', 0 ,'int'),26, 7, 'visible = 1','date_pub DESC, id DESC');
    $_CONTENT .= '<div class="container mt-4 mb-5">';
    $_CONTENT .= '<div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <a href="'.SITE_URL.'">'.$LANGJSON['page']['home'][$LANGUAGE].'</a> | <a href="'.SITE_URL.'/news">'.$_PAGENAME.'</a>
                        </div>
                    </div>
                  </div>';

    $_CONTENT .= '<div class="row mt-4">';
    foreach ($PAGINATION['table'] as $row) {
        $_CONTENT .= '<div class="col-md-4">';
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
                $event_day = '<div class="event_day '.$event_color.'"><img src="'.SITE_URL.'/img/static/fire.png" style="width: 20px;display:inline-block;margin-bottom: 5px;"> '.$event_date.' '.$event_time. '</div>';
            }
        }

        $_CONTENT .= '<div class="news_list_box mt-3">
                        '.$event_day.'
                        <div class="news_list_box__img">
                            <a href="'.$new_url.'" style="display: inline-block"><img src="'.$new_img.'" class="img-fluid"></a>
                        </div>
                        <div class="news_list__desc">
                            <div class="news_list__type mt-3">'.$type_new['name'.$LANGSQL].'</div>
                            <!--<div class="news_list_info mb-2">'.date("d.m.Y", $row['date_pub']).'</div>-->
                            <div class="news_list__title mb-2"><a href="'.$new_url.'" style="display: inline-block">'.$row['name'.$LANGSQL].'</a></div>
                            <p class="mt-2"><a href="'.$new_url.'" class="btn btn-custom-1">'.$LANGJSON['news']['read_more'][$LANGUAGE].'</a></p>
                        </div>
                    </div>';
        $_CONTENT .= '</div>';
    }
    $_CONTENT .= '</div></div>';

    $_CONTENT .= '<div class="container mt-4 mb-5 content_news">';
    $_CONTENT .= $PAGINATION['pag'];
    $_CONTENT .= '</div>';


}

