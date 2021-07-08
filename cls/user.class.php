<?php
class DCore_User{
    var $id = 0;
    var $error = ['auth' => [], 'reg' => []];
    protected $life_cookie = 86400;
    protected $set_field = ['login', 'email', 'firstname', 'lastname', 'surname', 'phone', 'member', 'team'];
    protected $get_field = ['id', 'login', 'email', 'firstname', 'lastname', 'time_online', 'time_reg', 'status', 'surname', 'phone', 'block', 'member', 'ip', 'team'];

    function __construct(){
        //dpr($_COOKIE);
        session_start();
        isset($_SESSION) || die('DCore_user::DCore_user() ERROR!');
        //$this->checkCookie();
        if(isset($_SESSION['USER']['id'])){
            $this->init();
            $this->online();
        }
    }
    protected function init(){
        foreach($this->get_field as $field){
            $this->$field = &$_SESSION['USER'][$field];
        }
    }
    public function checkCookie(){
        if(!empty($_COOKIE['userid'])){
            global $DB;
            $user = $DB->get_record('users', ['id' => $_COOKIE['userid']]);
            if($user){
                $this->auth($user);
                $this->online();
            }
        }
    }
    //Вводит текущее время в БД
    protected function online(){
        global $DB;
        $DB->update_record('users', ['id' => $this->id, 'time_online' => $_SERVER['REQUEST_TIME']]);
    }
    public function set_field($name, $value = ''){
        if($this->id && in_array($name, $this->set_field)){
            global $DB;
            $user = ['id' =>  $this->id, $name => $value];
            $_SESSION['USER'][$name] = $value;
            //$this->$name = $value;
            return $DB->update_record('users', $user);
        }
        return false;
    }
    public function hashPass($pass){
        $salt = random_string(15);
        return $this->hashWithPass($pass, $salt);
    }
    public function hashWithPass($pass, $salt){
        $sha1 = sha1($salt.$pass);
        for($i = 0; $i <1000; $i++){
            $sha1 = sha1($sha1.($i % 2 == 0 ? $pass : $salt));
        }
        return(['salt' => $salt, 'pass' => $sha1]);
        //return $salt.$sha1;
    }
    public function checkPass($pass, $hash, $salt){
        //$salt = substr($hash, 10, 15);
        $hash_ar = $this->hashWithPass($pass, $salt);
        //dpr($hash_ar);
        //return $this->hashWithPass($pass, $salt) == $salt.$hash;
        return $hash_ar['salt'].$hash_ar['pass'] == $salt.$hash;
    }
    public function answer(){
        global $LANGUAGE;
        global $LANGJSON;
        if(!$this->id){
            $email = strtolower(get_param('email'));
            $pass = get_param('pass');
            if($email && $pass){
                global $DB;
                $user = $DB->get_record('users',['email' => $email]);
                //dpr($user->activate);
                if(@!$user->block) {
                    if ($user && $user->activate) {
                        if ($this->checkPass($pass, $user->pass, $user->salt)) {
                            $DB->update_record('users', ['id' => $user->id, 'ip' => $_SERVER['REMOTE_ADDR']]);
                            $this->auth($user);
                        } else {
                            $this->error['auth'][] = $LANGJSON['frm_auth']['error_pass_0'][$LANGUAGE];
                        }
                    } else {
                        $this->error['auth'][] = $LANGJSON['frm_auth']['error_usr_0'][$LANGUAGE];
                    }
                }else{$this->error['auth'][] = $LANGJSON['frm_auth']['error_usr_1'][$LANGUAGE];}
            }else{
                $this->error['auth'][] = @$LANGJSON['frm_auth']['error_auth_0'][$LANGUAGE];
            }
        }elseif(get_param('auth') == 'exit'){
            //dpr('exit');
            $this->delAuth();
        }
    }
    public function answerReg(){
        global $DB;
        global $LANGUAGE;
        global $LANGJSON;
        if(!$this->id){
            $pass = get_param('pass');
            $pass_cnf = get_param('pass_cnf');
            $email = strtolower(get_param('email'));
            $fname = get_param('fname');
            $lname = get_param('lname');
            $sname = get_param('sname');
            $phone = get_param('phone');

            //if(!preg_match("/^[a-zA-Z0-9]+$/",$login)){$this->error['reg'][] = "Логин может состоять только из букв английского алфавита и цифр";}
            //if(empty($login) || strlen($login)<6 || strlen($login)>32){$this->error['reg'][] ='Имя пользователя должно содержать от 6 до 32 символов';}
            if(empty($pass) || strlen($pass) < 8 || strlen($pass) > 32){$this->error['reg'][] = $LANGJSON['frm_reg']['error_email_1'][$LANGUAGE];}
            if($pass != $pass_cnf){$this->error['reg'][] = $LANGJSON['frm_reg']['error_pass_0'][$LANGUAGE];}
            if(!preg_match("/^[a-zA-Z0-9]+$/",$pass)){$this->error['reg'][] = $LANGJSON['frm_reg']['error_email_2'][$LANGUAGE];}
            if(empty($email)){$this->error['reg'][] = $LANGJSON['frm_reg']['error_email_0'][$LANGUAGE];
            }else{
                if(!checkEmail($email)){
                    $this->error['reg'][]=$LANGJSON['frm_reg']['error_email_2'][$LANGUAGE];
                }else{
                    $user = $DB->get_record('users', ['email' => $email]);
                    if ($user) {
                        $this->error['reg'][] = $LANGJSON['frm_reg']['error_email_1'][$LANGUAGE];
                    }
                }
            }
            if(!count($this->error['reg'])){
                $usr_id = NULL;
                $hpass = $this->hashPass($pass);
                $insert = [
                    'pass' => $hpass['pass'],
                    'salt' => $hpass['salt'],
                    'email' => $email,
                    'firstname' => $fname,
                    'lastname' => $lname,
                    'surname' => $sname,
                    'status' => 0,
                    'phone' => $phone,
                    'time_reg' => $_SERVER['REQUEST_TIME'],
                ];

                $usr_id = $DB->insert_record('users',$insert);
                if($usr_id) {
                    $topic = "Сообщение с сайта " . SITE_TITLE;
                    $link_activate = SITE_URL . '/users/?usr=act&id=' . $usr_id . '&code=' . $hpass['salt'];
                    $message = '<h2>' . $LANGJSON['frm_reg']['to_activate'][$LANGUAGE] . ' <a href="' . $link_activate . '">' . $LANGJSON['frm_reg']['follow_link'][$LANGUAGE] . '</a></h2><br> ' . $link_activate;
                    $sendmail = sendMail($email, $topic, $message);
                    $topic_admin = 'Регистрация нового пользователя на сайте ' . SITE_URL;
                    $message_admin = 'Email: ' . $email . '<br>ФИО: ' . $lname . ' ' . $fname . ' ' . $sname . '<br>Телефон: ' . $phone;
                    $sendmail_admin = sendMail(SITE_MAIL, $topic_admin, $message_admin);
                    echo '<div class="alert alert-success">' . $LANGJSON['frm_reg']['post_reg_link'][$LANGUAGE] . '</div>';
                }else{
                    echo '<div class="alert alert-danger">Error Registration</div>';
                }
            }
        }
    }

    protected function auth($user){
        $_SESSION['USER'] = [];
        foreach($this->get_field as $field){
            $this->$field = $user->$field;
            $_SESSION['USER'][$field] = &$this->$field;
        }
        //$this->saveCookie();
        //header('Location:'.SITE_URL);
        //echo '<div class="alert alert-success">Успешная авторизация</div>';
    }

    public function delAuth(){
        session_destroy();
        unset($_SESSION['USER']);
        foreach($this->get_field as $field){
            $this->$field = null;
        }
        //setcookie('userid', '', $_SERVER['REQUEST_TIME'] - 1);
        header('Location:'.SITE_URL);
    }

    protected function saveCookie(){
        setcookie('userid', session_id(), $_SERVER['REQUEST_TIME'] + $this->life_cookie);
    }
    //check request
    public function checkRequest(){
        global $DB;
        if($this->id) {
            return $DB->get_record('teams_requests', ['usr' => $this->id]);
        }
    }
}