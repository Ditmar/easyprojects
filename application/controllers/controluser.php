<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class controluser extends CI_Controller{
    function controluser(){
        parent::__construct();
        $this->load->model("usuario_model");
        $this->load->model("proyecto_model");
        $this->load->model("equipo_model");
        $this->load->model("repositorio_model");
    }
    function checkSession(){
        //return "hola";
        return $this->session->userdata("us_id");
    }
    function autentication($nick,$name){
        $rows=$this->usuario_model->checkAutentication($nick,$pass);
        if($row==1){
            return true;
        }
        return false;
    }
    function getParticipeProjects(){
        $r=$this->proyecto_model->get_rel_us_pro("array");
        return $r;
    }
    function getMyProjects(){
        $r=$this->proyecto_model->getMyProjects();
        return $r;
    }
    /*
     * MEda los espacios de trabajo de mi sistema
     */
    function getWorkSpace(){
        $idEq=$this->input->post("idEq");
        $r=$this->repositorio_model->getWorkSpace($this->session->userdata("us_id"),$idEq);
        if($r->num_rows()>0){
            /*
             * Creamos la session del equipo
             */
            $this->session->set_userdata("idEq",$idEq);
            echo json_encode(array("result"=>true,"info"=>$r->row()));
        }else{
            echo json_encode(array("result"=>false,"info"=>$r->row()));
        }
    }
    function getTeam(){
        $idPro=$this->session->userdata("us_idPro");
        $type=array("idPro","idUs");
        $data=array($idPro,$this->session->userdata("us_id"));
       $r=$this->equipo_model->getTeamRelation($this->session->userdata("us_id"),$idPro);

       //$r=$this->equipo_model->getEquipo($type,$data,null);
       $ids=array();
       //echo $this->session->userdata("us_idPro")." ".$this->session->userdata("us_id");
       
       foreach($r->result() as $rr){
            $repo[]=$rr->nombre;
            $ids[$rr->nombre]=$rr->id;
        }
        echo json_encode(array("result"=>true,"data"=>$repo,"ids"=>$ids));
        //return $r->result();
    }
    function getTeamRelation($idPro){
      $r=$this->equipo_model->getTeamRelation($this->session->userdata("us_id"),$idPro);
       return $r->result();
    }
    function checkRepository(){
        $datos=array(
            "prueba"=>"HOLA",
            "prueba2"=>"Hola 2"
        );
        return $datos;
    }
}
?>
