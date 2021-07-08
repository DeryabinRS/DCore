<?php
require_once ("../lib/setup.php");
$errors = [];
$form_data = [];

if ($_POST['email'] == ""){
    $errors['name'] = 'Enter email';
}else{
    if(!checkEmail($_POST['email'])){$errors['name'] = 'Enter correct email';}
}
if ($_POST['firstname'] == ""){ $errors['name'] = 'Enter first name';}
if ($_POST['lastname'] == ""){ $errors['name'] = 'Enter last name';}
if ($_POST['country'] == ""){ $errors['name'] = 'Enter country';}
if ($_POST['message'] == ""){ $errors['name'] = 'Enter message';}
$recaptcha = $_POST['g-recaptcha-response'];

if(empty($recaptcha)){$errors['name'] = 'Enter captcha';}
if (!empty($errors)){
    $form_data['success'] = false;
    $form_data['errors']  = $errors;
}else{
    $message = "<h2>Сообщение с сайта: ".$_SERVER['HTTP_HOST']."</h2>";

    $frm = [
        'firstname' => get_param('firstname'),
        'lastname' => get_param('lastname'),
        'country' => get_param('country'),
        'email' => get_param('email'),
        'message' => get_param('message'),
        'date_create' => $_SERVER['REQUEST_TIME']
    ];

    foreach ($frm as $key => $item) {
        $message .= $key.': '.$item.'<br>';
    }
    $id = $DB->insert_record('feedback', $frm);
    if($id) {
        $IMG_FILE_TYPE = 'jpg';
        $IMG_WH_SIZE = 250;
        $doc_dir = $CFG->dir_img . '/feedback/'.$id;
        if(!file_exists($doc_dir)){
            mkdir($doc_dir);
        }
        $doc_arr = [];
        $doc_arr[] = funSaveFile($_FILES['img'], $doc_dir , $id, $IMG_FILE_TYPE, $IMG_WH_SIZE, $IMG_WH_SIZE, false, 0);
    }
    $topic = "Сообщение с сайта ". SITE_TITLE;
    if (sendMail(SITE_MAIL, $topic, $message, $doc_arr)){
        $form_data['success'] = true;
        $form_data['posted'] = '<div class="alert alert-success">Your message has been accepted and will be processed shortly.</div>';
    }else{
        $errors['name'] = 'Ошибка отправки письма';
    }

}
echo json_encode($form_data);