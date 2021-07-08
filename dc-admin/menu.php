<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
    <img src="<?=SITE_URL?>/img/static/logo.png" height="40px">
    <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarCollapse"
        aria-controls="navbarCollapse"
        aria-expanded="false"
        aria-label="Toggle navigation"
    >
        <span class="navbar-toggler-icon"></span>
    </button>
<?php
//$_PAGE = '';
function MenuNav($page = [], $text1 = '', $text2 = ''){
    if(in_array($_GET['page'], $page)){
        return $text1;
    }else{
        return $text2;
    }
}
function CreateMenu($Menu = []){
    global $_PAGE;
    global $_PAGENAME;
    $html = '';
    $i = 0;
    foreach ($Menu as $key => $m){
        if(is_array($m[2])){
            if(in_array($_SESSION['USER']['status'],$m[4])){
                $pages = [];
                foreach ($m[2] as $item){
                    $pages[] = $item[3];
                }
                $html .= '<li class="nav-item '. MenuNav($pages,'active').'">';
                $html .= '<a class="nav-link nav-link-collapse '.MenuNav($pages,'', 'collapsed').'" href="#" id="hasSubItems"
                        data-toggle="collapse" data-target="#collapseSubItems'.$i.'" aria-controls="collapseSubItems'.$i.'" aria-expanded="false"><i class="'.$m[0].'" aria-hidden="true"></i> '.$m[1].'</a>';
                $html .= '<ul class="nav-second-level collapse '.MenuNav($pages,'show').'" id="collapseSubItems'.$i.'" data-parent="#navAccordion">';
                $html .= CreateMenu($m[2]);
                $html .= '</ul>';
                $html .= '</li>';
                $i++;
            }
        }else{
            if(in_array($_SESSION['USER']['status'],$m[4])) {
                $page = $m[3] ? '/?page='.$m[3]: '';
                $html .= '<li class="nav-item ' . MenuNav([$m[3]], 'active') . '">';
                $html .= '<a class="nav-link" href="' . SITE_URL_ADM . $page . '"><i class="' . $m[0] . '"></i> ' . $m[1] . '</a>';
                $html .= '</li>';
                if($_PAGE == $m[3]){$_PAGENAME = $m[1];}
            }
        }
    }
    return $html;
}
function getAccess($arr = [], $page, $status){
    foreach ($arr as $m){
        if(is_array($m[2])){
            if(getAccess($m[2], $page, $status))return true;
        }else{
            //dpr($m[2]);
            if($m[3] == $page){
                if(in_array($status,$m[4])){return true;}
            }
        }
    }
}
/*Элементы $MenuNavArray
0 - иконка пункта меню
1 - Наименование
2 - Вложенное меню (массив)
3 - Ссылка
4 - Доступ для категорий пользователя
*/
$MenuNavArray = [
    ['fa fa-home','Главная', '', '', [1,2,3,4]],
    ['fa fa-bars','Меню сайта', '', 'menu',[1,2]],
    ['fa fa-clone','Страницы', '', 'pages',[1,2]],
    ['fa fa-newspaper-o','События', [
        ['fa fa-bars', 'Все события', '', 'news',[1,2,3,4]],
        ['fa fa-bars', 'Типы событий', '', 'news_types',[1,2,3]],
    ], '',[1,2,3,4]],
    ['fa fa-picture-o','Галерея', '', 'gallery',[1,2,3,4]],
    ['fa fa-picture-o','Баннеры', '', 'banners',[1,2,3]],
    ['fa fa-user','Пользователи', '', 'users',[1,2]],
    ['fa fa-handshake-o','Партнеры', '', 'partners',[1,2,3]],
    ['fa fa-sitemap','Менеджер файлов', '', 'filemanager',[1,2]],
    ['fa fa-cog','Настройки', '', 'settings',[1,2]],
];
?>
    <div class="collapse navbar-collapse" id="navbarCollapse">

        <ul class="navbar-nav mr-auto sidenav" id="navAccordion">
            <?=CreateMenu($MenuNavArray)?>
        </ul>
        <div class="form-inline ml-auto mt-2 mt-md-0">
            <a href="<?=SITE_URL?>" class="btn btn-success ml-2"><i class="fa fa-globe"></i> На сайт</a>
            <a href="?auth=exit" class="btn btn-secondary ml-2"><i class="fa fa-sign-out"></i> Выход</a>
        </div>

    </div>
</nav>

<main class="content-wrapper">
    <div class="container-fluid">