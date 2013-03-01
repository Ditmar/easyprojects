<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Inicio extends CI_Controller {
    function Inicio() {
        parent::__construct();
        //$this->load->scaffolding('actividades_sistema');
    }
    function index() {
        $this->load->view("inicio");
    }
    function initpage(){
        $this->load->view("paginainicial");
    }
    function getMenu() {
        $this->load->model("Usuario_model");
        if($this->session->userdata("us_c")==TRUE) {
            $menu["menu"]=$this->menu->getMenu(true);
            //le pedimos al controlador los roles del usuario
            $menu["loginControl"]=$this->menu->getControlLogin(true,$this->Usuario_model->getRoles(),$this->Usuario_model->getUser());
        //$menu["registermenu"]=$this->menu->getSuperMenu($roles);
        }else {
            $menu["menu"]=$this->menu->getMenu(false);
            $menu["loginControl"]=$this->menu->getControlLogin(false,'','');
        }
        echo json_encode(array("menu"=>$menu["menu"]));
    }
}
?>
