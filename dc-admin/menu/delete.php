<?php
require_once '../../lib/setup.php';

function recursiveDelete($id) {
    global $DB;
    $query = $DB->get_records('menu',['parent' => $id]);
    if (count($query) > 0) {
       foreach ($query as $qid){
           recursiveDelete($qid->id);
       }
    }
    $DB->delete_records('menu', ['id' => $id]);
}
recursiveDelete($_POST['id']);