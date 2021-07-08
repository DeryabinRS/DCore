<?php
require_once('../lib/setup.php');
$dir = $_POST['dir'];
$dir_show = $_POST['dir_show'];
$get_files = get_files_tree($dir);
$res = '';
if(!empty($get_files['files'])){
    foreach ($get_files['files'] as $file){
        $res .= '<div class="gallery_show_img"><img src="'.$dir_show.'/'.$file.'"></div>';
    }
}
echo $res;