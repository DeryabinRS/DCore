<?php
$query = $DB->get_records_sql("select * from dcore_menu order by sort asc");
$ref   = [];
$items = [];

foreach($query as $data){
    $data = (array)$data;
    //dpr($data['name'.$LANGSQL]);
    $thisRef = &$ref[$data['id']];
    $thisRef['icon'] = $data['icon'];
    $thisRef['parent'] = $data['parent'];
    $thisRef['name'] = $data['name'.$LANGSQL];
    $thisRef['link'] = $data['link'];
    $thisRef['id'] = $data['id'];

    if($data['parent'] == 0) {
        $items[$data['id']] = &$thisRef;
    } else {
        @$ref[$data['parent']]['child'][$data['id']] = &$thisRef;
    }
}

function get_menu($items, $class = null) {
    $html = '<ul>';
    foreach($items as $key=>$value) {
        $classArr = [];
        $classArr[] = array_key_exists('child',$value) ? 'dropdown' : '';
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $url = $url[0];
        $classArr[] = $url == SITE_URL.'/'.$value['link'] ? 'active': '';
        $html.= '<li class="'.implode(' ', $classArr).'"><a href="'.SITE_URL.'/'.$value['link'].'"><i class="'.$value['icon'].'"></i> <span>'.$value['name'].'</a></span>';
        if(array_key_exists('child',$value)) {
            $html .= get_menu($value['child'], '');
        }
        $html .= "</li>";
    }
    $html .= "</ul>";
    return $html;
}

echo '<div class="sidebar">';
$_MENU = '
<div id="cssmenu">
'.get_menu($items).'
</div>
';

echo $_MENU . '</div>';


