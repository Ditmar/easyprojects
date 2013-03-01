<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lastchanges
 *
 * @author Ditmar
 */
class lastchanges extends CI_Controller{
    function lastchanges(){
        parent::__construct();
        $this->load->model("lastchanges_model");
    }
    function getLastChanges(){
        $start=$_POST["start"];
        $limit=$_POST["limit"];
        $r=$this->lastchanges_model->getLastChanges($start,$limit);
        //$result=array();
        $respuesta=array(
            "success"=>true,
            "rows"=>$r->result_array()
        );
        echo json_encode($respuesta);
    }
    function checkchanges($data){
        $information["ids"]=$data;
        $this->load->view("proyecto/lastchanges/panelchanges",$information);
    }
    function getNotificaciones(){
        $start=$this->input->post("start");
        $limit=$this->input->post("limit");
        $filter=$this->input->post("filter");
        $ids=array(
            "idPro"=>$this->session->userdata("us_idPro"),
            "idUs"=>$this->session->userdata("us_id"),
            "idEq"=>$this->session->userdata("idEq"),
            "filter"=>$filter
        );
        $result=$this->lastchanges_model->getNodificacion($ids,$start,$limit);
        $count=$this->lastchanges_model->getNoCount($ids);
        $respuesta=array(
            "sucess"=>true,
            "rows"=>$result->result_array(),
            "totalCount"=>$count
        );
        echo json_encode($respuesta);
    }
}
?>
