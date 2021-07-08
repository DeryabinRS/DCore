<?php
$_PAGENAME = 'Contacts';

$IMG_DIR_SC = $CFG->dir_img.'/contacts';
$IMG_SHOW_SC = SITE_URL. '/img/contacts';
$IMG_TEMPLATE_SC =  SITE_URL. '/img/static/usr.jpg';

$contacts = '';

$_CONTENT = '<div class="container mt-4 mb-5 content">';
$_CONTENT .= '<div class="row">';
$request = $DB->get_records('contacts',['visible' => 1],'position ASC', '*',0,true);
//dpr($request);
foreach ($request as $row){
    $path_img = $IMG_DIR_SC.'/'.$row['id'].'/'.$row['id'].'.jpg';
    $img = is_file($path_img) ? $IMG_SHOW_SC.'/'.$row['id'].'/'.$row['id'].'.jpg' : '/img/static/new.png';

    $_CONTENT .= '<div class="col-lg-4 mt-3">
                    <div class="contact_box">
                        <div class="img text-center"><img src="'.$img.'" class="img-fluid"></div>
                        <div class="name text-center mt-2">'.$row['firstname'].' '.$row['middlename'].' '.$row['lastname'].'</div>
                        <div class="pos text-center">'.$row['pos'].'</div>
                        <div class="desc text-center">'.$row['desc_en'].'</div>
                        <div class="phone text-center mt-3"><i class="fa fa-phone"></i> '.$row['phone'].'</div>
                        <div class="email text-center mt-2">'.$row['email'].'</div>
                    </div>
                </div>';
}
$_CONTENT .= '</div></div>';