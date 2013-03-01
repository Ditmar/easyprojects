<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Proyecto_model
 *
 * @author Ditmar
 */
class Proyecto_model extends CI_Model {
    function Proyecto_model() {
        parent::__construct();

    }
    function setProyecto($pro) {
        $this->db->insert("proyecto",$pro);
        return $this->db->insert_id();
    }
    function setEquipo($equipo) {
        $this->db->insert("equipo",$equipo);
        return $this->db->insert_id();
    }
    function checkAdminProyect() {
        $res=$this->db->query("select * from proyecto where idUs='".$this->session->userdata('us_id')."'");
        // echo"--"."select * from proyecto where idUs='".$this->session->userdata['us_id']."'";

        if($res->num_rows()>0)
            return true;
        $res=$this->db->query("select * from rel_us_pro where idUs='".$this->session->userdata("us_id")."'");
        if($res->num_rows()>0)
            return true;
        return false;
    }

    function setLider($li) {
        $this->db->insert("lider",$li);

        return $this->db->insert_id();
    }
    function putLider($li,$rl) {
        $this->db->insert("rel_li_us",$li);
        $this->db->insert("rel_equi_us",$rl);
    }
    /*
     * Aignamos los permisos
     */
    function setAllPoliticas($id) {
    //no hace falta consultar la base de datos ya que el ID siempre será el mismo
        $res=$this->db->query("select * from politica");
        $true=true;
        foreach($res->result() as $p) {
            $true=true;
            if($p->nombre=="Blog") {
                if($p->tipo=='1') {
                    $true=false;
                }
            }
            if($true) {
                $data=array(
                    "idUs"=>$this->session->userdata['us_id'],
                    "idPo"=>$p->id,
                    "idPro"=>$id
                );
                $k=$this->db->insert("rel_us_po",$data);
                if(!$k)
                    return false;
            }

        }
        return $p;
    }
    /*
     * Regresa todas las politicas del sistema
     */
    function getAllPoliticas() {
        $result=$this->db->query("select * from politica");
        if($result->num_rows()>0) {
            return $result;
        }
        return null;
    }
    /*
     * Politcias
     */
    function getAllUserPoliticas($id) {
        $result=$this->db->query("select po.* from rel_us_po as rel, politica as po where rel.idUs='".$id."' and rel.idPro='".$this->session->userdata("us_idPro")."' and rel.idPo=po.id");
        if($result->num_rows()>0) {
            return $result;
        }
        return null;
    }
    /*
     * Coloca las politicas básicas del sistema
     */
    function setBasicPoliticas($idPro) {
    //$this->db->query("select * from politica where id=73 or id=83 or id=79 or id=86 or id=87");
        $idPol=array(73,83,79,86,87);
        for($i=0;$i<5;$i++) {
            $data=array(
                "idUs"=>$this->session->userdata['us_id'],
                "idPo"=>$idPol[$i],
                "idPro"=>$idPro
            );
            $this->db->insert("rel_us_po",$data);
        }
        return true;
    }
    function setPolitica($table) {
        $this->db->insert("rel_us_po",$table);
        return true;
    }
    function removePolitica($row) {
        $res=$this->db->query("delete from rel_us_po where idUs='".$row["idUs"]."' and idPo='".$row["idPo"]."' and idPro='".$row["idPro"]."'");
        return $res;
    }

    /*
     * Permisos de la base de datos
     */
    function getPoliticas($id) {
        $res=$this->db->query("select * from rel_us_po,politica where id=idPo and idPro='".$id."' and idUs='".$this->session->userdata['us_id']."'");
        return $res->result();
    }
    /*
     * da el avatar del usuario
     */
    function getAvatar() {
        $res=$this->db->query("select avatar from usuario where id='".$this->session->userdata['us_id']."'");
        foreach($res->result() as $p) {
            return $p->avatar;
        }
    }
    /*
     * Resumen del proyecto
     */
    function getResumen($id) {
        $proyecto=$this->db->query("select proyecto.nombre, proyecto.descripcion,proyecto.licencia,proyecto.summary,proyecto.logo,usuario.nick,usuario.email from proyecto, usuario where proyecto.id='".$id."' and usuario.id='".$this->session->userdata['us_id']."'");
        //sacamos cuantos equipos hay más el nombre y lider del equipo
        $superArreglo=array();
        $equipo=$this->db->query("select * from equipo where idPro='".$id."' ");
        $superArreglo[]=$proyecto;
        $superArreglo[]=$equipo;
        $superArreglo[]=$equipo->num_rows();
        //$superArreglo[]=$equipo->num_rows();
        return $superArreglo;
    }
    function getNameProyecto() {
        $res=$this->db->query("select * from proyecto where id='".$this->session->userdata('us_idPro')."' ");
        $nom=$res->result_array();

        return $nom[0]["nombre"];

    }
    function getUserTeam($id) {
        $res=$this->db->query("select * from rel_equi_us where idEq='".$id."'");
        return $res;
    }
    /*function getProyect($id,$type){
        $res=$this->db->query("select * from proyecto where idUs='".$id."'");
        if($type=="array"){
            return $res->result_array();
        }else if(type=="objects"){
            return $res->result();
        }
        return false;
    }*/
    function getMyProjects() {
        $res=$this->db->query("select * from proyecto where idUs='".$this->session->userdata('us_id')."'");
        return $res->result();
    }
    function getMyHelperProjects() {
        //datos de tus proyectos
        $res=$this->db->query("select * from proyecto where idUs='".$this->session->userdata('us_id')."'");
        $projects=array();
        foreach($res->result() as $rows) {
            $projects[]=array(
                "id"=>$rows->id,
                "nombre"=>$rows->nombre,
                "descripcion"=>$rows->descripcion,
                "create"=>$rows->create_at,
                "licencia"=>$rows->licencia,
                "summary"=>$rows->summary,
                "logo"=>$rows->logo,
                "framework"=>$rows->framework
            );
        }
        //datos de los proyectos invitado
        $res2=$this->db->query("select pro.* from rel_us_pro as rel, proyecto as pro where rel.idUs='".$this->session->userdata("us_id")."' and rel.idPro=pro.id order by rel.fecha");
        foreach($res2->result() as $rows){
           $projects[]=array(
                "id"=>$rows->id,
                "nombre"=>$rows->nombre,
                "descripcion"=>$rows->descripcion,
                "create"=>$rows->create_at,
                "licencia"=>$rows->licencia,
                "summary"=>$rows->summary,
                "logo"=>$rows->logo,
                "framework"=>$rows->framework
            );
        }
        return $projects;
    }
    function getProyecto($id) {
        $res=$this->db->query("select * from proyecto where id='".$id."'");
        return $res;
    }
    function checkProject($id) {

        $pro=$this->db->query("select * from proyecto where id='".$id."' and idUs='".$this->session->userdata("us_id")."'");
        if(count($pro)==0) {
        //check relation

            $count=$this->db->query("select * from rel_us_pro where idPro='".$id."' and idUs='".$this->session->userdata("us_id")."'");
            if(count($count)==0) {
                return false;
            }
            return true;
        }

        return true;
    }
    /*
     * relación insersion de relacion entre usuario y proyecto
     */
    function insert_rel_us_pro($data) {
        $res=$this->db->insert("rel_us_pro",$data);
        if($res) {
            return true;
        }
        return false;
    }
    function get_rel_us_pro($type) {
    //return "select pro.* from rel_us_pro as rel,proyecto as pro where rel.idUs='".$this->session->userdata("us_id")."' and rel.idPro=pro.id order by fecha desc";
        $res=$this->db->query("select pro.* from rel_us_pro as rel,proyecto as pro where rel.idUs='".$this->session->userdata("us_id")."' and rel.idPro=pro.id order by fecha desc");
        if($type=="array") {
            return $res->result_array();
        }
        else {
            return $res->result();
        }
        return false;
    }
    /*
     * Dont use wall del proyecto especializado
     */
    function getProjectWall($idPro,$idUs,$limit,$limitComent) {
        $infoMaster=array();
        if(isset($idPro)&&!isset($idUs)) {
        // echo"select * from noticia where idPro='".$idPro."' limit ".$limit;
            $noticia_rows=$this->db->query("select us.apellido,us.nombre,us.nick,us.avatar,n.* from noticia as n, usuario as us where n.idPro='".$idPro."' and us.id=n.idUs order by n.id desc  limit ".$limit);
        }else if(isset($idPro)&&isset($idUs)) {
                $noticia_rows=$this->db->query("select us.apellido,us.nombre,us.nick,us.avatar,n.* from noticia where idPro='".$idPro."' and idUs='".$idUs."' and us.id=n.idUs order by n.id desc limit ".$limit);
            }
         /*
          * armamos la informacion en base a las noticias
          */

        foreach($noticia_rows->result() as $nt) {
        //echo"siu ";
        //echo $nt->idUs."=".$this->session->userdata("us_id")."|";
            if($nt->idUs==$this->session->userdata("us_id")) {
                $actionsNoti=array("Comentar","Me Gusta","Actualizar","Borrar");
            }else {
                $actionsNoti=array("Comentar","Me Gusta");
            }
            if($nt->avatar==null) {
                $nt->avatar="/logo/avatar.jpg";
            }else {
                $nt->avatar="/uploads/".$nt->avatar;
            }
            $comentario=$this->db->query("select co.*,us.nick,us.avatar from comentario as co,usuario as us where idNo='".$nt->id."' and us.id=co.idUs limit ".$limitComent);
            if($comentario->num_rows()>0) {
                $personalComent=array();
                foreach($comentario->result() as $coment) {
                //$coment->avatar="/uploads/".$coment->avatar;
                    if($this->session->userdata("us_id")==$coment->idUs) {
                        $actions=array("update","delete");
                    }else {
                        $actions=array();
                    }
                    $personalComent[]=array("com"=>$coment,"actions"=>$actions);
                }
                $infoMaster[]=array("noticia"=>$nt,"acciones"=>$actionsNoti,"comentario"=>$personalComent);
            }else {
                $infoMaster[]=array("noticia"=>$nt,"acciones"=>$actionsNoti,"comentario"=>"");
            }

        }
        return $infoMaster;
    }
    /*
     * updating System
     */
    function updateproject($data,$condicion){
        $this->db->query($this->db->update_string("proyecto",$data,$condicion));
    }
}
?>
