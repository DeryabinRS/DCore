<?php
$alias = $rURL->arr_params[1] ? $rURL->arr_params[1] : Null;
$_CONTENT = '';
if($alias) {
    $new = $DB->get_record('news', ['alias' => $alias]);
    if($new) {
        if ($alias) {
            if ($new) {
                $_PAGENAME = $new->name;
                //dpr($new->visible);
                if ($new->visible == 0 and $_SESSION['USER']['status'] == 0) {
                    $new->content = '<div class="alert alert-danger">Доступ к странице закрыт</div>';
                } else {
                    $_CONTENT .= '<div class="mb-2"><i class="fa fa-calendar"></i> ' . date('d.m.Y', $new->date_create) . '</div>';
                    $_CONTENT .= htmlspecialchars_decode($new->content);
                }
            } else {
                $_CONTENT .= '<div class="alert alert-danger">Страницы не существует</div>';
            }
        } else {
            $_CONTENT .= '<div class="alert alert-danger">Страницы не существует</div>';
        }
    }
}