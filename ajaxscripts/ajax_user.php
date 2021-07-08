<?php include_once("../lib/setup.php");
if(isset($_POST['registration'])) {
    $recaptcha=$_POST['g-recaptcha-response'];
    if(!RC_KEY || RC_KEY == ''){
        $registration = $USER->answerReg();
        foreach ($USER->error['reg'] AS $err) {
            echo '<div class="alert alert-danger">' . $err . '</div>';
        }
    }else{
        if (!empty($recaptcha)) {
// массив для переменных, которые будут переданы с запросом
            $paramsArray = array(
                'secret' => RC_S_KEY,
                'response' => $_POST['g-recaptcha-response'],
            );
            // преобразуем массив в URL-кодированную строку
            $vars = http_build_query($paramsArray);
// создаем параметры контекста
            $options = array(
                'http' => array(
                    'method' => 'POST',  // метод передачи данных
                    'header' => 'Content-type: application/x-www-form-urlencoded',  // заголовок
                    'content' => $vars,  // переменные
                )
            );
            $context = stream_context_create($options);  // создаём контекст потока
            $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context); //отправляем запрос
            //dpr(json_decode($result, true)); // вывод результата*/
            $registration = $USER->answerReg();
            foreach ($USER->error['reg'] AS $err) {
                echo '<div class="alert alert-danger">' . $err . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">' . $LANGJSON['frm_reg']['captcha_not'][$LANGUAGE] . '</div>';
        }
    }
}
if(isset($_POST['authorization'])) {
    $authorization = $USER->answer();
    foreach ($USER->error['auth'] as $err){
        echo '<div class="alert alert-danger">'.$err.'</div>';
    }
}
if(isset($_POST['update'])){
    $frm = [
        'id' => $USER->id,
        'phone' => get_param('phone'),
        'lastname' => get_param('lastname'),
        'firstname' => get_param('firstname'),
        'surname' => get_param('surname'),
        'birthday' => strtotime(get_param('birthday')),
        'country' => get_param('country'),
        'city' => get_param('city'),
        'work_study' => get_param('work_study'),
        'specialty' => get_param('specialty'),
        'about' => get_param('about'),
    ];
    //dpr($frm);
    $DB->update_record ('users', $frm);
    echo '<div class="alert alert-success">'.$LANGJSON['frm_account']['update_frm'][$LANGUAGE].'</div>';
}