<?php
$alias = $rURL->arr_params[1] ? $rURL->arr_params[1] : Null;
$_CONTENT = '';
if($alias) {
    $query = $DB->get_record('pages', ['alias' => $alias]);
    if($query) {
        $new = (array)$query;
        $_PAGENAME = $new['name' . $LANGSQL];
        $_CONTENT .= '<div><a href="' . SITE_URL . '">' . $LANGJSON['page']['home'][$LANGUAGE] . '</a> | <a href="' . SITE_URL . '/pages">' . $_PAGENAME . '</a></div>';
        $_CONTENT .= '<div>';
        if ($alias) {
            if ($new) {
                //dpr($new->visible);
                if ($new['visible'] == 0 and @$_SESSION['USER']['status'] == 0) {
                    $_CONTENT .= '<div class="alert alert-danger">' . $LANGJSON['page']['access_off'][$LANGUAGE] . '</div>';
                } else {
                    //$_CONTENT .= '<div class="mb-2"><i class="fa fa-calendar"></i> ' . date('d.m.Y', $new->date_create) . '</div>';
                    $_CONTENT .= htmlspecialchars_decode($new['content' . $LANGSQL]);
                }
            } else {
                $_CONTENT .= '<div class="alert alert-danger">' . $LANGJSON['page']['error_404'][$LANGUAGE] . '</div>';
            }
        } else {
            $_CONTENT .= '<div class="alert alert-danger">' . $LANGJSON['page']['error_404'][$LANGUAGE] . '</div>';
        }
        $_CONTENT .= '</div>';
    }
}