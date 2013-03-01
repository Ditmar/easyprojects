<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of msn_model
 *
 * @author Ditmar
 */
class msn_model extends CI_Model
{
    function msn_model(){
        parent::__construct();
    }
    function getMessages($start,$limit){
        $result=$this->db->query("select me.*,us.avatar,us.nick,us.nombre,us.apellido from mensaje as me, usuario as us where me.idUs='".$this->session->userdata("us_id")."' and me.idRem=us.id order by me.id desc limit ".$start.",".$limit."");
    
        return $result;
    }
    function countMessages(){
        $result=$this->db->query("select count(*) as total from mensaje as me, usuario as us where me.idUs='".$this->session->userdata("us_id")."'");

        return $result->row()->total;
    }
    function sendMessage($info){
        $this->db->insert("mensaje",$info);
        return true;
    }
    function update($info,$condicion){
        $this->db->query($this->db->update_string("mensaje",$info,$condicion));
        return true;
    }
    function deleteMessage($id){
        $this->db->query("delete from mensaje where id='".$id."'");
        return true;
    }
    //put your code here
}
?>
