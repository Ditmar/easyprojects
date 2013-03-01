<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class wall_model extends CI_Model{
    function wall_model(){
        parent::__construct();
    }
    function insertnew($data){
        $this->db->insert("noticia",$data);
        return $this->db->insert_id();
    }
    /*
     * update
     */
    function updatenew($data){
        $condicion="id='".$data["id"]."'";
        $res=$this->db->query($this->db->update_string("noticia",$data,$condicion));
        return $res;
    }
    function getnew($id){
        $r=$this->db->query("select * from noticia where id='".$id."'");
        return $r;
    }
    /*
     * update
     */

    function updatecoment($data){
        $condicion="id='".$data["id"]."'";
        $res=$this->db->query($this->db->update_string("comentario",$data,$condicion));
        return $res;
    }
    function insertcoment($data){
        $this->db->insert("comentario",$data);
        return $this->db->insert_id();
    }
    function getConewsComents($id){
        $result=$this->db->query("select * from comentario where idNo='".$id."'");
        return $result;
    }
    function deleteNews($id){
        $this->db->query("delete from noticia where id='".$id."'");
        return true;
    }
    function deleteComent($id){
        $this->db->query("delete from comentario where id='".$id."'");
        return true;
    }
    function getComent($id){
        $result=$this->db->query("select * from comentario where id='".$id."'");
        return $result->row();
    }
}
?>
