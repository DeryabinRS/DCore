<?php
function redirectURL($url = ''){
    header('Location: '. $url);
}
function get_param($name, $def = '', $type = 'str'){
    if ( isset($_POST[$name]) ) {
        $param = $_POST[$name];
    } elseif ( isset($_GET[$name]) ) {
        $param = $_GET[$name];
    } else return $def;

    if ( is_array($param) ) {
        get_param_array($name, $type);
    }
    return clean_param($param, $type);
}
function get_param_array($name, $type = 'str'){
    if(isset($_POST[$name])){
        $param = $_POST[$name];
    }elseif(isset($_GET[$name])){
        $param = $_GET[$name];
    }else return [];
    if(is_array($param)){
        return clean_param_array($param, $type);
    }else return clean_param($param, $type);
}

function clean_param($param, $type = 'str'){
    # code...
    switch ($type) {
        case 'int':
            return (int)$param;
            break;
        case 'float':
            return (float)$param;
            break;
        case 'date':
            return strtotime($param);
            dpr(123);
            break;
        case 'str':
        case 'string':
        default:
            return htmlspecialchars((string)$param);
            break;
    }
}

function clean_param_array(array $param = null, $type = 'str'){
    $param = (array)$param;
    //pr($param);
    foreach($param as $key => $val){
        if(is_array($val)){
            $param[$key] = clean_param_array($val, $type);
        }else{
            $param[$key] = clean_param($val, $type);
        }
    }
    return $param;
}
function DCore_scandir($dir=__DIR__, $type = 'all'){
    if(!is_string($dir)) return false;
    //dpr($dir);
    if(!is_dir($dir)) return false;
        $files = scandir($dir);
        array_shift($files);
        array_shift($files);
        if ($type != 'all') {
            $f = [];
            $d = [];
            foreach ($files as $name) {
                if (is_dir($dir . '/' . $name)) {
                    $d[] = $name;
                } else {
                    $f[] = $name;
                }
            }
            switch ($type) {
                case 'files':
                    return $f;
                    break;
                case 'dir':
                    return $d;
                    break;
                case 'mix':
                    return ['files' => $f, 'dir' => $d];
                    break;
                default:
                    return $files;
                    break;
            }
        }
        return $files;

}
//Дерево папок и файлов
function get_files_tree($dir=__DIR__, $level=0){
    if(!is_dir($dir)) return false;
    $files = DCore_scandir($dir, 'mix');
    $files['level'] = $level;
    foreach($files['dir'] as $index => $NameDir){
        $files['dir'][$NameDir] = get_files_tree($dir.'/'.$NameDir, $level + 1);
        unset($files['dir'][$index]);
    }
    return $files;
}
//Найти файл изображения в каталоге. Вернуть массив с названием файла, и его пути
function get_files_img($dir, $search_str, $type = 'jpg'){
    $img_arr = [];
    $path = $_SERVER['DOCUMENT_ROOT'].'/'.$dir;
    $path_url = SITE_URL.'/'.$dir;
    $get_files = get_files_tree($path);
    //dpr($get_files);
    if(!empty($get_files['files'])){
        foreach ($get_files['files'] as $key => $file){
            if(strpos($file,$search_str)){
                $img_arr[$key]['name'] = str_replace('.'.$type,'',$file);
                $img_arr[$key]['dir_path'] = $path;
                $img_arr[$key]['dir_url'] = $path_url;
                $img_arr[$key]['type'] = $type;
            }
        }
        return $img_arr;
    }else{
        return Null;
    }
}

//Рекурсивное удаление директорий
function recursiveRemoveDir($dir) {
    if(is_dir($dir)) {
        $includes = new FilesystemIterator($dir);
        foreach ($includes as $include) {
            if (is_dir($include) && !is_link($include)) {
                recursiveRemoveDir($include);
            } else {
                unlink($include);
            }
        }
        rmdir($dir);
        return true;
    }else{
        return false;
    }
}
function random_string($count = 15){
    $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $n = strlen($pool) - 1;
    $ret = '';
    for($i=0; $i < $count; $i++){
        $ret .= $pool[rand(0, $n)];
    }
    return $ret;
}
//# слова расположены строго по порядку, в размере 3-х
//$arWords = array('яблоко','яблока','яблок');
//$num = 3;
//echo 'Мне нужно '.$num.' '.declension_words($num,$arWords).'<br>';
//склонение слов в зависимости от числа
function declension_words($n,$words){
    return ($words[($n=($n=$n%100)>19?($n%10):$n)==1?0 : (($n>1&&$n<=4)?1:2)]);
}

//Проверка введенного Email
function checkEmail($str){
    return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}
//Отправка сообщений писем SMTP
function sendMail($email, $topic, $message, $pathfile = []){
    global $CFG;
    require_once $CFG->http_root.'/inc/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    // Настройки SMTP
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPDebug = 0;
    $mail->Host = SMTP_MAIL_HOST;
    $mail->Port = SMTP_MAIL_PORT;
    $mail->Username = SMTP_MAIL_USER;
    $mail->Password = SMTP_MAIL_PASS;
    // От кого
    $mail->setFrom(SMTP_MAIL_USER, SITE_URL);
    // Кому
    $mail->addAddress($email, 'Техническая поддержка '. SITE_TITLE);
    // Тема письма
    $mail->Subject = $topic;
    // Тело письма
    $body = $message;
    $mail->msgHTML($body);
    // Приложение
    if($pathfile){
        foreach ($pathfile as $df) {
            $mail->addAttachment($df);
        }
    }
    return $mail->send();
}

function funcRusToLat($st)
{
    $st = mb_strtolower($st, "utf-8");
    $st = str_replace([
        '?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '[', ']', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=', '|', '"', '\'', '_',
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к',
        'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',
        'ъ', 'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я'
    ], [
        '-', '-', '.', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-',
        'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k',
        'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h',
        'j', 'i', 'e', '-', 'zh', 'ts', 'ch', 'sh', 'shch',
        '', 'yu', 'ya'
    ], $st);

    $st = preg_replace("/[^a-z0-9-.]/", "", $st);
    $st = trim($st, '-');
    //dpr($st);
    $prev_st = '';
    /*do {
        $prev_st = $st;
        $st = preg_replace("/-[a-z0-9]-/", "-", $st);
    } while ($st != $prev_st);

    $st = preg_replace("/-{2,}/", "-", $st);*/
    //dpr($st);
    return $st;
}
//Возвращеает массив записей постранично
function funcPagination($PAGE, $FIELDS = '*', $SHEET, $lim = 25, $count_show_sheets = 7, $WHERE = '', $ORDER = 'id DESC'){
    global $DB;
    global $CFG;
    $PAGINATION = '';
    $sheet = $SHEET ? $SHEET : 1;
    $res = $DB->get_records($PAGE,[]);
    $total = count($res);
    $art = ($sheet * $lim) - $lim;
    $str_pag = ceil($total / $lim);
    if($WHERE != '')$WHERE = 'WHERE '.$WHERE;
    $table = $DB->get_records_sql('SELECT '.$FIELDS.' FROM '.$CFG->db['prefix'].$PAGE.' '.$WHERE.' ORDER BY '.$ORDER.' LIMIT '.$art.', '.$lim);
    if ($str_pag > 1) { // Всё это только если количество страниц больше 1
        $left = $sheet - 1;
        $right = $str_pag - $sheet;
        if ($left < floor($count_show_sheets / 2)) $start = 1;
        else $start = $sheet - floor($count_show_sheets / 2);
        $end = $start + $count_show_sheets - 1;
        if ($end > $str_pag) {
            $start -= ($end - $str_pag);
            $end = $str_pag;
            if ($start < 1) $start = 1;
        }
        $PAGINATION ='<div class="pagination">';
        if ($sheet != 1){
            $PAGINATION .='<a href="?page='.$PAGE.'" title="Первая страница">&lt;&lt;&lt;</a>';
            $PAGINATION .='<a href="';
            if ($sheet == 2) { $PAGINATION .='?page='.$PAGE;} else { $PAGINATION .='?page='.$PAGE.'&sheet='.($sheet - 1);}
            $PAGINATION .='" title="Предыдущая страница">&lt;</a>';
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $sheet) {
                $PAGINATION .='<span>'.$i.'</span>';
            }else{
                $PAGINATION .='<a href="';
                if ($i == 1) {
                    $PAGINATION .='?page='.$PAGE;
                }else{
                    $PAGINATION .='?page='.$PAGE.'&sheet='.$i;
                }
                $PAGINATION .='">'.$i.'</a>';
            }
        }
        if ($sheet != $str_pag) {
            $PAGINATION .='<a href="?page='.$PAGE.'&sheet='.($sheet + 1).'" title="Следующая страница">&gt;</a>';
            $PAGINATION .='<a href="?page='.$PAGE.'&sheet='. $str_pag.'" title="Последняя страница">&gt;&gt;&gt;</a>';
        }
        $PAGINATION .='</div>';
    }
    return ['pag' => $PAGINATION, 'table' => $table];
}
function fGetURL($url){
    $url = parse_url($url);
    parse_str($url['query'], $url);
    $url = implode('/', $url);
    return SITE_URL.'/'.$url;
}
function funArrFilesForMultiload($input_name){
    $files = array();
    $diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
    if ($diff == 0) {
        $files = array($_FILES[$input_name]);
    } else {
        foreach($_FILES[$input_name] as $k => $l) {
            foreach($l as $i => $v) {
                $files[$i][$k] = $v;
            }
        }
    }
    return $files;
}
function funSaveFile($file , $dirsave, $fileSaveName, $typeF = 'jpg', $f_size_w = 0, $f_size_h = 0, $thumb = false, $thumb_size = 0){
    global $CFG;
    if(is_array($file['tmp_name'])){

    }
    if(file_exists($file['tmp_name'])) {
        $fileName = $fileSaveName . '.' . $typeF;
        $fileName_thumb = $fileSaveName . '_thumb.' . $typeF;
        if (@is_array(getimagesize($file['tmp_name']))){
            require_once ($CFG->dir_lib.'/img_resizer.php');
            if($f_size_w == $f_size_h) {
                crop($file['tmp_name'], $dirsave . '/' . $fileName);
                resize($dirsave . '/' . $fileName, $dirsave . '/' . $fileName, $f_size_w, 0);
            }else {
                resize($file['tmp_name'], $dirsave . '/' . $fileName, $f_size_w, 0);
            }
            //dpr(1);
            if($thumb && $thumb_size){
                crop($file['tmp_name'], $dirsave . '/' . $fileName_thumb);
                resize($dirsave . '/' . $fileName_thumb, $dirsave . '/' . $fileName_thumb, $thumb_size, $thumb_size);
            }
        }else{
            move_uploaded_file($file['tmp_name'], $dirsave.'/'.$fileName);
        }
        return $dirsave.'/'.$fileName;
    }
    return false;
}