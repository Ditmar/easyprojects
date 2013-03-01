<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of friends_model
 *
 * @author Ditmar
 */
class friends_model extends CI_Model{
    //put your code here
    function friends_model(){
        parent::__construct();
    }
    function getUsuarios($params=null){
        if(($params["start"]!="")&&($params["limit"]!="")){
            $result=$this->db->query("select fr.fecha_confirm,us.* from friends as fr, usuario as us where us.id=fr.idFr and fr.idUs='".$this->session->userdata("us_id")."' limit ".$params["start"].",".$params["limit"]."");

        }else{
            $result=$this->db->query("select fr.fecha_confirm,us.* from friends as fr, usuario as us where us.id=fr.idFr and fr.idUs='".$this->session->userdata("us_id")."'");

        }
        return $result;
    }

    function delete($id){
        $this->db->query("delete from friends where id='".$id."'");
        return true;
    }
    function updatecontest($type,$updates,$condicion){
        $this->db->query($this->db->update_string("friends",$updates,$condicion));
    
        return true;
    }
    function insert($friends){
        $this->db->insert("friends",$friends);
        return $this->db->insert_id();
    }

    /*function search($keys){

    }*/
}
?>
