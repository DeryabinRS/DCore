<?php
class DC_files{

    function __constructor(){}

    static public function create_dir($name){
        global $CFG;
        $level1 = substr($name, 0, 2);
        $level2 = substr($name, 2, 2);
        $out = $CFG->datadir.'/'.$level1;
        if(!file_exists($out)){
            mkdir($out);
        }
        $out .= '/'.$level2;
        if(!file_exists($out)){
            mkdir($out);
        }
        return $out.= '/'.$name;
    }
    static public function uploaded($nameForm, $component, $space = 'test'){
        if(!empty($_FILES[$nameForm])){
            global $CFG, $DB, $USER;
            $dir = $CFG-datadir;
            $ret = ['done' => [], 'fail' => []];
            $f = $_FILES[$nameForm];
            if(is_array($f['name'])){
                foreach($f['name'] as $index => $filename){
                    $sha1_name = sha1_file( $f['tmp_name'][$index] );
                    $upload_file = DC_files::create_dir($sha1_name);
                    if(move_uploaded_file( $f['tmp_name'][$index], $upload_file)){
                        $get = $DB->get_record('files',[
                            'component' => $component,
                            'space' =>$space,
                            'filename' => $filename,
                        ]);
                        if($get){
                            $get->filehash =  $sha1_name;
                            $get->userid =  $USER->id;
                            $get->type = $f['type'][$index];
                            $get->size = $f['size'][$index];
                            $get->date = $_SERVER['REQUEST_TIME'];
                            $DB->update_record('files', $get);
                            //TODO delete file
                        }else{
                            $insert = [
                                'filehash'  => $sha1_name,
                                'component' => $component,
                                'space'     => $space,
                                'filename'  => $filename,
                                'userid'    => $USER->id,
                                'type'      => $f['type'][$index],
                                'size'      => $f['size'][$index],
                                'date'      => $_SERVER['REQUEST_TIME']
                            ];
                            $DB->insert_record('files', $insert);
                        }

                        $ret['done'][] = $filename[$index];
                    }else{
                        $ret['fail'][] = $filename[$index];
                    }
                }
            }else{
                dpr($f);
            }
            return $ret;
        }
        return false;
    }
}