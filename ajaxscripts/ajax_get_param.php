<?php
require_once ("../lib/setup.php");
$tab = isset($_POST['tab'])? get_param('tab'): null;
$params = isset($_POST['params'])? $_POST['params'] : null;
//$params = string('val'=>$val)
if($tab && !empty($params)){
    $query = $DB->get_record($tab, $params);
    echo json_encode($query);
}
