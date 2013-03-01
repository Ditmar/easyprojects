<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class invitacion_model extends CI_Model{
    /*var $title="";
    var $message="";
    var $idPro="";
    var $idUs="";
    var $acepted="";
    var $fecha="";*/
    function invitacion_model(){
        parent::__construct();
    }
    /*
     * llena la tabla invitacion
     */
    function fillInvitacion($info){
        $r=$this->db->insert("invitacion",$info);
        if($r){
            $invitacion=$this->getLastInvitacion();

            return  $invitacion;
        }
        return false;
    }
    /*
     * lena la tabla rel_users_invite
     */
    function fill_rel_users_invite($info){
        $r=$this->db->insert("rel_users_invite",$info);
        if($r){
            return $this->getLast_rel_users_invite();
        }
        return false;
    }
    /*
     * t regresa la ultima entrada de la tabla rel_users_invite
     */
    function getLast_rel_users_invite(){
        $data=$this->db->query("select * from rel_users_invite order by idIn desc limit 1");
        return $data->result_array();

    }
    /*
     * te regresa la ultima entrada en invitacio
     */
    function getLastInvitacion(){
        $data=$this->db->query("select * from invitacion order by id desc  limit 1");
        return $data->result_array();
    }
    /*
     *
     */
    function fillRel($data){
        $res=$this->db->insert("rel_users_invite",$data);
        if($res){
            return true;
        }
        return false;
        
    }
    function update_data($informacion,$id){
       $res=$this->db->query($this->db->update_string("invitacion",$informacion,"id=".$id.""));
       if($res){
           return $this->getLastInvitacion();;
       }
       return false;
    }
    /*
     *regresa las invitaciones según el id
     * viendo la session
     */
    function getMyInvitations($indexin,$indexof,$type,$filter){
        if($filter=="all"){
             $sql="select inv.*,us2.avatar from invitacion as inv,rel_users_invite as rel,usuario as us,usuario as us2 where rel.idUs='".$this->session->userdata("us_id")."' and rel.idIn=inv.id and us.id=rel.idUs and us2.id=inv.idRe order by id desc limit ".$indexin.",".$indexof."";
        }
        if($filter=="new"){
             $sql="select inv.*,us2.avatar from invitacion as inv,rel_users_invite as rel,usuario as us,usuario as us2 where rel.idUs='".$this->session->userdata("us_id")."' and rel.idIn=inv.id and us.id=rel.idUs and us2.id=inv.idRe and acepted='nocontest' order by id desc limit ".$indexin.",".$indexof."";
        }
        if($filter=="refuse"){
           $sql="select inv.*,us2.avatar from invitacion as inv,rel_users_invite as rel,usuario as us,usuario as us2 where rel.idUs='".$this->session->userdata("us_id")."' and rel.idIn=inv.id and us.id=rel.idUs and us2.id=inv.idRe and acepted='refuse' order by id desc limit ".$indexin.",".$indexof."";
        }

        $data=$this->db->query($sql);
        if($data){
            if($type=="array"){
                return $data->result_array();
            }
            return $data->result();
        }
        return false;
    }
    function getInvitation($id,$type){
        $data=$this->db->query("select * from invitacion where id='".$id."'");
        if($type=="array"){
            return $data->result_array();
        }
        return $data->result();
    }
    function getInvTotal(){
        $data=$this->db->query("select inv.* from invitacion as inv,rel_users_invite as rel,usuario as us where rel.idUs='".$this->session->userdata("us_id")."' and rel.idIn=inv.id and us.id=rel.idUs");
    
        return count($data->result_array());
    }
    function getIds_rel_users_invite($id){
        $result=$this->db->query("select * from rel_users_invite as rel where rel.idIn='".$id."' and rel.idUs='".$this->session->userdata("us_id")."'");
        return $result->num_rows();
    }
    /*
     * Invitacion del proyecto
     */
    function getProInvites($start,$limit){
        //echo "select inv.*,us.avatar as avatar1,us1.avatar as avatar2,us.nick as nick1,us1.nick as nick2 from invitacion as inv,usuario as us, usuario as us1 where inv.idPro='".$this->session->userdata('us_idPro')."' and inv.idUs=us.id and inv.idRe=us1.id order by id desc limit ".$start.",".$limit."";
        $r=$this->db->query("select inv.*,us.avatar as avatar1,us1.avatar as avatar2,us.nick as nick1,us1.nick as nick2 from invitacion as inv,usuario as us, usuario as us1 where inv.idPro='".$this->session->userdata('us_idPro')."' and inv.idUs=us.id and inv.idRe=us1.id order by id desc limit ".$start.",".$limit."");
    
        return $r;
    }
    function getCount(){
        $r=$this->db->query("select count(*) as tam from invitacion as inv,usuario as us, usuario as us1 where inv.idPro='".$this->session->userdata('us_idPro')."' and inv.idUs=us.id and inv.idRe=us1.id");

        return $r->row()->tam;
    }
}
?>
