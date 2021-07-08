<?php
require_once '../lib/img_resizer.php';

$fimg = $_POST['fname'];
$ing_dir = $_POST['img_dir'];
$img_size = $_POST['img_size'];
$img_size_w = isset($_POST['img_size_w'])?$_POST['img_size_w']:0;
$img_size_h = isset($_POST['img_size_h'])?$_POST['img_size_h']:0;
$img_thumb_size = $_POST['img_thumb_size'];
$fname_thumb = $_POST['fname_thumb'];

if(isset($_POST['fdel'])){
    if (file_exists($ing_dir.'/'.$fimg)) {
        unlink($ing_dir.'/'.$fimg);
        echo 'Файл удален: '.$fimg;
    }
    if (file_exists($ing_dir.'/'.$fname_thumb)) {
        unlink($ing_dir.'/'.$fname_thumb);
        echo 'Файл удален: '.$fname_thumb;
    }
}
if (isset($_FILES['file'])){
    $size = getimagesize($_FILES['file']['tmp_name']);
    if(!is_dir($ing_dir)){mkdir($ing_dir);}
    if (file_exists($ing_dir.'/'.$fimg)) unlink($ing_dir.'/'.$fimg);
    if (file_exists($ing_dir.'/'.$fname_thumb)) unlink($ing_dir.'/'.$fname_thumb);
    if(!move_uploaded_file($_FILES['file']['tmp_name'], $ing_dir.'/'.$fimg )) echo 'Ошибка загрузки изображения';
    if($img_size_w || $img_size_h){
        resize($ing_dir . '/' . $fimg, $ing_dir . '/' . $fimg, $img_size_w, $img_size_h);
    }else {
        crop($ing_dir . '/' . $fimg, $ing_dir . '/' . $fimg);
        resize($ing_dir . '/' . $fimg, $ing_dir . '/' . $fimg, $img_size, $img_size);
    }
    if($fname_thumb){
        crop($ing_dir . '/' . $fimg, $ing_dir . '/' . $fname_thumb);
        resize($ing_dir . '/' . $fname_thumb, $ing_dir . '/' . $fname_thumb, $img_thumb_size, $img_thumb_size);
    }
}