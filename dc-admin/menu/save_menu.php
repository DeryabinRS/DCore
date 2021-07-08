<?php
require_once '../../lib/setup.php';
if($_POST['id'] != ''){
	//$db->exec("update tbl_menu set label = '".$_POST['name']."', link  = '".$_POST['link']."' where id = '".$_POST['id']."' ");
	$DB->update_record('menu', ['id' => $_POST['id'], 'link'  => $_POST['link'] , 'name' => $_POST['name'], 'name_en' => $_POST['name_en'],  'icon' => $_POST['icon']]);
	$arr['type']  = 'edit';
	$arr['name'] = $_POST['name'];
	$arr['name_en'] = $_POST['name_en'];
	$arr['link']  = $_POST['link'];
    $arr['icon']  = $_POST['icon'];
	$arr['id']    = $_POST['id'];
} else {
	//$db->exec("insert into tbl_menu (label,link) values ('".$_POST['name']."', '".$_POST['link']."')");
	$id = $DB->insert_record('menu',['name' => $_POST['name'], 'name_en' => $_POST['name_en'], 'link' => $_POST['link'], 'icon' => $_POST['icon']]);
	$arr['menu'] = '<li class="dd-item dd3-item" data-id="'.$id.'" >
	                    <div class="dd-handle dd3-handle"><i class="'.$_POST['icon'].'"></i></div>
	                    <div class="dd3-content"><span id="name_show'.$id.'">'.$_POST['name'].'</span>
	                        <span class="span-right"><span id="link_show'.$id.'">'.$_POST['link'].'</span> &nbsp;&nbsp; 
	                        	<a class="edit-button" id="'.$id.'" name="'.$_POST['name'].'" name_en="'.$_POST['name_en'].'" link="'.$_POST['link'].'" icon="'.$_POST['icon'].'"><i class="fa fa-pencil"></i></a>
                           		<a class="del-button" id="'.$id.'"><i class="fa fa-trash"></i></a>
	                        </span> 
	                    </div>';
	$arr['type'] = 'add';
}
print json_encode($arr);