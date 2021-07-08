<?php

class DCore_DB{
    protected $mysql;
    protected $debug;
    protected $prefix;
    protected $DB_TIME = 0;
    function __construct($conf){
        $this->prefix = isset($conf['prefix']) ? $conf['prefix'] : '';
        $this->debug = isset($conf['debug']) && $conf['debug'] ? true : false;
        $port = isset($conf['port']) ? $conf['port'] : 21;
        if(!isset($conf['host'], $conf['user'], $conf['pass'], $conf['dbname'])){
            $this->error('Ivalid config parameters');
        }
        $this->mysql = @mysqli_connect($conf['host'],$conf['user'],$conf['pass'],$conf['dbname'],$port);

        if(!$this->mysql){
            $this->error(mysqli_connect_error());
        }
        $charset = isset($conf['charset']) ? $conf['charset'] : 'utf8';
        $this->query('SET NAMES '. $charset);
    }
    protected function error($msg = false, $sql = false){
        if(!$this->mysql || $this->mysql->errno || $msg){
            echo '<div style="background:#ffd4d4;border:1px dashed #000;padding:10px">';
            if($this->debug){
                if($msg) echo $msg.'<hr>';
                if($this->mysql && $this->mysql->errno) echo $this->mysql->error.'<hr>';
                if($sql) echo $sql;
            }else{
                echo 'ERROR DB!';
            }
            echo '</div>';
            die();
        }
        echo $msg;
    }
    protected function query($SQL = ''){
        $time = microtime(true);
        $result = @mysqli_query($this->mysql, $SQL);
        $this->DB_TIME += microtime(true) - $time;
        $this->error(false, $SQL);
        return $result;
    }
    //Возврат количества строк в таблице
    public function get_num_rows($SQL = ''){
        return mysqli_num_rows($this->query($SQL));
    }
    public function get_time(){
        return $this->DB_TIME;
    }
    public function fix_table_names($sql){
        return preg_replace('/\{([a-z][a-z0-9_]*)\}/', $this->prefix.'$1', $sql);
    }
    protected function normal_value($val){
        if(is_object($val)){
            $this->error('Invalid value is class: '.get_class($val));
        }
        if(is_array($val)){
            $this->error('Invalid value is array!');
        }
        if(is_null($val)){
            $val = 'NULL';
        }
        return '"'.$val.'"';
    }
    protected function get_where(array $select){
        if(empty($select)) return '';
        $return = [];
        foreach($select as $index => $val){
            if(is_numeric($index)){
                $this->error('Invalid params in DCore::get_where');
            }
            if(is_object($val)){
                $this->error('Invalid value is class'.get_class($val));
            }
            if(is_array($val)){
                $this->error('Invalid value is array');
            }
            if(is_null($val)){
                $return[]= '`'.$index.'` IS NULL';
            }else $return[]= '`'.$index.'` = "'.$val.'"';
        }
        return ' WHERE '.implode(' AND ', $return);
    }
    /**/
    public function insert_record($table, $data, $get_id = true){
        $data = (array)$data;
        $fields = [];
        $values = [];
        foreach($data as $index => $val){
            if(is_numeric($index)){
                $this->error('Invalid params in DCore::insert_record()');
            }
            $fields[] = '`'.$index.'`';
            $values[] = $this->normal_value($val);
        }
        $SQL = 'INSERT INTO '.$this->prefix.$table. ' ('.
            implode(', ', $fields).') VALUES ('.
            implode(', ', $values).')';
        $this->query($SQL);
        if($get_id) {
            $id = @$this->mysql->insert_id;
            if (!$id) $this->error('Unknow error in DCore::insert_record()');
            return $id;
        }
    }

    public function get_records_sql($sql='', $array = false){
        $return = [];
        $sql = $this->fix_table_names($sql);
        $result = $this->query($sql);
        while($row = $result->fetch_assoc()){
            $row = array_change_key_case($row, CASE_LOWER);
            $key = reset($row);
            if(isset($return[$key])){
                debuging('First field must be unicque key: '.$key);
            }
            $return[$key] = $array ? (array)$row: (object)$row;
        }
        return $return;
    }
    public function get_records($table, array $where = [], $sort = false,
                                $fields = '*', $limit = 0, $array = false){
        $where = $this->get_where($where);
        if($sort){
            $sort = ' ORDER BY '.$sort;
        }
        if($limit){
            $SQL = 'SELECT ' . $fields . ' FROM ' . $this->prefix . $table . $where . $sort. ' LIMIT '.$limit;
        }else {
            $SQL = 'SELECT ' . $fields . ' FROM ' . $this->prefix . $table . $where . $sort;
        }
        //dpr($SQL);
        return $this->get_records_sql($SQL, $array);
    }

    public function get_records_where_sql($table, $where = "", $fields = '*'){
        $SQL = 'SELECT ' . $fields . ' FROM ' . $this->prefix . $table . " WHERE " .$where;
        return $this->get_records_sql($SQL);
    }

    public function get_record_sql($sql=''){
        $records = $this->get_records_sql($sql);
        if(count($records)>1){
            $this->debuging('DCore_DB::get_records_sql() returned more that one line');
        }
        return reset($records);
    }
    public function get_record($table, array $where, $fields="*"){
        //$val = $DB->get_record('users',['login' => $login]);
        $records = $this->get_records($table, $where, false, $fields);
        if(count($records)>1){
            $this->debuging('DCore_DB::get_records_sql() returned more that one line');
        }
        return reset($records);
    }
    public function update_record($table, $data){
        $data = (array)$data;
        if(!isset($data['id'])){
            $this->error('DCore_DB::update_record() no key id');
        }
        $id = $data['id'];
        unset($data['id']);
        //Если после удаления id массив пустой то ошибка
        if(empty($data)) $this->error('DCore_DB::update_record() not data set');
        $set = [];
        foreach($data as $index => $val){
            $set[] = '`'.$index. '` = ' .$this->normal_value($val);
        }
        $SQL = 'UPDATE `'.$this->prefix.$table.'` SET '.implode(', ', $set).' WHERE `id`='.$id;
        $this->query($SQL);
        return true;
    }
    public function update_records($table, $data, array $where = []){
        $data = (array)$data;
        //Если после удаления id массив пустой то ошибка
        if(empty($data)) $this->error('DCore_DB::update_record() not data set');
        if(empty($where)) $this->error('DCore_DB::update_record() not data where');
        $set = [];
        foreach($data as $index => $val){
            $set[] = '`'.$index. '` = ' .$this->normal_value($val);
        }
        $where = $this->get_where($where);
        $SQL = 'UPDATE `'.$this->prefix.$table.'` SET '.implode(', ', $set).$where;
        //dpr($SQL);
        $this->query($SQL);
        return true;
    }
    public function delete_records($table, array $where = []){
        if(empty($where)){
            debuging('DCore_DB::need params for delete');
            return false;
        }
        $where = $this->get_where($where);
        $SQL = 'DELETE FROM '.$this->prefix.$table.$where;
        $this->query($SQL);
        return true;
    }
    public function delete_records_str($table, $where_str){
        $SQL = 'DELETE FROM '.$this->prefix.$table.' WHERE '.$where_str;
        $this->query($SQL);
        return true;
    }
    public function delete_records_all($table){
        $SQL = 'TURNCATE TABLE '.$this->prefix.$table;
        $this->query($SQL);
        return true;
    }
}