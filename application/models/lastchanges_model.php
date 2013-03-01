<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lastchanges_model
 *
 * @author Ditmar
 */
class lastchanges_model extends CI_Model {
    function lastchanges_model() {
        parent::__construct();
    }
    function insert($changes){
        $this->db->insert("lastchanges",$changes);
        return $this->db->insert_id();
    }
    function update($changes,$condicion){
        $this->db->query($this->db->update_string("lastchanges",$changes,$condicion));
    }
    function getLastChanges($start,$limit){
        $r=$this->db->query("select us.avatar,us.nick,la.* from lastchanges as la, usuario as us where us.id=la.idUs and idPro='".$this->session->userdata("us_idPro")."' order by id desc limit ".$start.",".$limit);
        //echo"select us.avatar,us.nick,la.* from lastchanges as la, usuario as us where us.id=la.idUs and idPro='".$this->session->userdata("us_idPro")."' order by id desc limit ".$start.",".$limit;
        return $r;
    }
    /*
     * Aqui consulto las notificaciones
     * del repositorio
     *
     */
    function getNodificacion($ids,$start,$limit){
        //echo"select * from notificacion where idPro='".$ids["idPro"]."' and idUs='".$ids["idUs"]."' and idEq='".$ids["idEq"]."' order by id desc limit ".$start.",".$limit."";
        if(isset($ids["filter"])&&$ids["filter"]!=""){
           $r=$this->db->query("select * from notificacion as no where idPro='".$ids["idPro"]."' and idUs='".$ids["idUs"]."' and idEq='".$ids["idEq"]."' and no.read=".$ids["filter"]." order by id desc limit ".$start.",".$limit."");
        }else{
            $r=$this->db->query("select * from notificacion as no where idPro='".$ids["idPro"]."' and idUs='".$ids["idUs"]."' and idEq='".$ids["idEq"]."' order by id desc limit ".$start.",".$limit."");
        }
        return $r;
    }
    function getNoCount($ids){
        $r=$this->db->query("select count(*) as c from notificacion where idPro='".$ids["idPro"]."' and idUs='".$ids["idUs"]."' and idEq='".$ids["idEq"]."'");
        return $r->row()->c;
    }
}
?>
