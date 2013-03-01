<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Error
 *
 * @author Ditmar
 */
class Error extends CI_Controller {
    function Error(){
        parent::__construct();
        $this->load->model("roles_model");
    }
    function messages($tipeError){
       // $this->interface_data["error"]="hola";
        $datos=$this->errorslibrary->getErrors();
        $this->interface_data["error"]=$datos[$tipeError];
        $this->load->view("errors/Messages",$this->interface_data);
    }
    function redirect($id){
        $m["errormsn"]=$this->roles_model->getError($id);
        $this->load->view("error",$m);
    }
}
?>
