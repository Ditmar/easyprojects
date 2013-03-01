<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of roles_model
 *
 * @author Ditmar
 * @descripcion clase destinada al control de roles con la base de datos aqui se verifican los permisos
 * según la tabla politica y rel_us_po
 */
class roles_model extends CI_Model {
    //put your code here
    function roles_model(){
        parent::__construct();
    }
    function checkPermit($type){
       $result= $this->db->query("select * from rel_us_po as rel where rel.idPo='".$type."' and rel.idUs='".$this->session->userdata("us_id")."' and rel.idPro='".$this->session->userdata("us_idPro")."'");
       if($result->num_rows()>0){
           return true;
       }
       return false;
    }
    function getError($id){
        $erros=array(
        "71"=>"No tienes el permiso para Administrar los usuario, nice try budy =D",
        "72"=>"No cuentas con el permiso para Administrar los contenidos, nice try budy =D",
        "73"=>"No tienes el permiso para administrar los blogs, nice try budy =D",
        "74"=>"No cuentas con el permiso para ver los blogs",
        "75"=>"No cuentas con el permiso para entrar a la creación de usuarios",
        "76"=>"No cuentas con el permiso para entrar a la creación de bases de datos",
        "77"=>"No cuentas con el permiso para entrar a a PHPMYAdmin",
        "78"=>"No cuentas con el permiso de entrar a ver los equipos",
        "79"=>"No cuentas con el permiso de ver los últimos cambios del proyecto",
        "80"=>"",
        "81"=>"",
        "82"=>"",
        "83"=>"",
        "84"=>"",
        "85"=>"",
        "86"=>"",
        "87"=>"",
        "88"=>"No tienes el permiso para admistrar las cuentas y equipos!",
        "89"=>"",
        "90"=>""
        );
        return $erros[$id];
    }
}
?>
