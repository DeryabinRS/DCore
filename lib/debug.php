<?php
defined('DCORE') || die();

function debugigng($value = ''){
    echo $value.'<br>';
}
function pr($v = '[NOT PR]', $var = false){
    echo '<pre>';
    if($var){
        var_dump($v);
    }else print_r($v);
    echo'</pre>';
}
function dpr($v = '[NOT_DPR]', $var = false){
    pr($v, $var);
    die('[[DIE_DPR]]');
}