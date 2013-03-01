<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Equipo_model extends CI_Model{
    function equipo_model(){
        parent::__construct();
    }

    /*
     * Métodos de insercion
     */
    function setEquipo($equipo) {
        $this->db->insert("equipo",$equipo);
        return $this->db->insert_id();
    }


    /*
     * Método get usuarios
     * Regresa a los miembros del proyecto
     * excluye el lider
     */
    function getUsuarios($type){
        $result=$this->db->query("select us.*,rel.fecha from usuario as us,rel_us_pro as rel where us.id=rel.idUs and rel.idPro='".$this->session->userdata("us_idPro")."'");
        if($type=="array"){
            return $result->result_array();
        }elseif($type=="completeObject"){
            return $result;

        }
        return $result->result();
    }
    function getTeam($id){
        //echo "select * from equipo where id='".$id."'";
        $r=$this->db->query("select * from equipo where id='".$id."'");

        return $r;
    }
    /*
     * Formas de uso
     * en bolas
     */
    function getEquipo($type,$id,$limits){
        if(!isset($type)&&!isset($id)){
            if(isset($limits)){
                $result=$this->db->query("select * from equipo where idPro='".$this->session->userdata("us_idPro")."' limit ".$limits["start"].",".$limits["limit"]."");
            }else{
              
                $result=$this->db->query("select * from equipo where idPro='".$this->session->userdata("us_idPro")."'");
            }
        }else{
            if(count($type)>0&&count($id)>0&&count($id)==count($type)){
                $consulta="";
                for($i=0;$i<count($type);$i++){
                    $consulta.=$type[$i]."='".$id[$i]."'";
                    if($i<(count($type)-1)){
                        $consulta.=" and ";
                    }
                }
                //$result="select * from equipo where ".$consulta;
                //echo "select * from equipo where ".$consulta;
                $result=$this->db->query("select * from equipo where ".$consulta);
            }else{
                if(isset($limits)){
                    $result=$this->db->query("select * from equipo where ".$type."='".$id."' limit ".$limits["start"].",".$limits["limit"]." ");
                }else{
                    $result=$this->db->query("select * from equipo where ".$type."='".$id."'");
                }
            }

        }
        return $result;
    }
    /*
     * te regresa los equipos
     * a los que pertenece un usuario
     */
    function getTeamRelation($idUs,$idPro){
       //$r="select team.* from equipo as team, rel_equi_us as r , usuario as us where us.id='".$idUs."' and team.idPro='".$idPro."' and team.idUs=r.idUs and team.id=r.idEq";
        $r=$this->db->query("select team.* from equipo as team, rel_equi_us as r , usuario as us where us.id='".$idUs."' and team.idPro='".$idPro."' and us.id=r.idUs and team.id=r.idEq");
        //echo "select team.* from equipo as team, rel_equi_us as r , usuario as us where us.id='".$idUs."' and team.idPro='".$idPro."' and team.idUs=r.idUs and team.id=r.idEq";
        return $r;
    }
    function getRelacion($id){
        $users= $this->db->query("select us.nombre,us.id,us.nick,us.avatar from usuario as us, rel_equi_us as rel where rel.idEq='".$id."' and us.id=rel.idUs");
        return $users;
    }
    function setRelation($obj){
        $this->db->insert("rel_equi_us",$obj);
        return true;
    }
    function delete($idUs,$idEq){
        $this->db->query("delete from rel_equi_us where idUs='".$idUs."' and idEq='".$idEq."' ");
    }
    /*
     * Borrado del equipo
     */
    function removeTeam($idEq){
        $this->db->query("delete from equipo where id='".$idEq."'");
        return true;
    }
}
?>
