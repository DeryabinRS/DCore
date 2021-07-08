<?php
class DCore_Teams{
    public function get_team($id){
        global $DB;
        return $DB->get_record('teams', ['id' => $id]);
    }
    public function get_team_admin($id){
        global $DB;
        //$team = $this->get_team($id);
        //$admin_team = $DB->get_records_sql("SELECT t.id, t.name, u.lastname, u.firstname, u.email FROM dcore_teams as t, dcore_users as u WHERE t.admin = u.id AND t.id = ".$team->id);
        //dpr($admin_team);
    }
}