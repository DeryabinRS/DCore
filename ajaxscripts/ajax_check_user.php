<?php include_once("../lib/setup.php");

if(isset($_POST['email'])) {
    $email = $_POST['email'];
    $query = count($DB->get_records('users',['email' => $email],false,'id'));
    if($query > 0){
        echo "";
    }else{
        echo "true";
    }
}