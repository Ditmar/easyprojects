<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basedata
 *
 * @author Ditmar
 */
class BaseData extends CI_Controller {
//put your code here
    var $interface_menu;
    function BaseData() {
        parent::__construct();
        $this->load->model("basedata_model");
        $this->load->model("roles_model");
    }
    function index() {
        if($this->session->userdata("us_c")!=true) {
            redirect(base_url()."index.php/usuario/logea");
        }
    }
    function createuser(){
        $data=array(
            "nick"=>$this->session->userdata["us_idPro"]."_".$this->input->post("nombre"),
            "pass"=>$this->input->post("password"),
            "fecha"=>mdate("%y/%m/%d",time()),
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $r=$this->basedata_model->createUser($data);
        if($r){
            $usuarios=$this->basedata_model->getUsersDbProject(array("idPro"=>$this->session->userdata("us_idPro")));
            echo json_encode(array("success"=>true,"data"=>$usuarios));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>"Error no se creo el Usuario"));
        return;
    }
    function usuario() {
        $this->interface_menu["users"]=$this->basedata_model->getUsersDbProject(array("idPro"=>$this->session->userdata("us_idPro")));
        $this->load->view("basedata/usuario",$this->interface_menu);
    }
    function borrarUsuario() {
        $us=$this->input->post("users");
        $id=$this->input->post("ids");
        $users=split(",",$us);
        $ids=split(',',$id);
        for($i=0;$i<count($users);$i++){
            $r=$this->basedata_model->deleteUser($ids[$i],$users[$i]);
        }
        echo json_encode(array("result"=>true,"users"=>$users));
    //if(ech$this->input->post("id");)
        /*for($i=0;$i<$this->input->post("length");$i++) {
            if($this->input->post("id".$i)=="ON") {
            //$this->input->post("id_data_form".$i);
                $r=$this->basedata_model->deleteUser($this->input->post("id_data_form".$i),$this->input->post("nick_form".$i));
                if($r==FALSE) {
                    $this->interface_menu["ERROR"]="No se han podido eliminar los usuarios correctamente";
                }else {
                    $this->interface_menu["SUCCES"]="Se ha eliminado correctamen el usuario";

                }
            }
        }*/
        //$this->interface_menu["users"]=$this->basedata_model->getUsersDbProject(array("idPro"=>$this->session->userdata("us_idPro")));
        //$this->load->view("basedata/borrarusuario",$this->interface_menu);
    // if($this->input->post("id"));
    }
    function createdb() {
        $name=$this->session->userdata("us_idPro")."_".$this->input->post("namedb");
        $options=array(
            "fecha"=>mdate("%y/%m/%d",time()),
            "nombre"=>$name,
            "hora"=>mdate("%h:%i:%a",time()),
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $count=$this->basedata_model->getDataBases(array("nombre"=>$this->session->userdata("us_idPro")."_".$this->input->post("namedb"),"count"=>true));
        if($count>0) {
            $info = array(
                'success' => false,
                'msg' => 'La creación no se concreto con éxito',
                'errors' => array(
                'namedb' => $name.' Ya tienes una base de ese nombre'
                )
            );
            echo json_encode($info);
            return;
        }
        $result=$this->basedata_model->addBaseData($options);
        if($result) {
            echo json_encode(array("success"=>true,"data"=>$name,"msg"=>"La base de datos ".$name." se creo correctamente"));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>"Error"));
        return;
    }
    function db() {
        $this->interface_menu["basedata"]=$this->basedata_model->getDataBases(array("idPro"=>$this->session->userdata("us_idPro")));
        $this->interface_menu["users"]=$this->basedata_model->getUsersDbProject(array("idPro"=>$this->session->userdata("us_idPro")));
        $this->interface_menu["databases"]=$this->basedata_model->getBaseDataUser(array("idPro"=>$this->session->userdata("us_idPro")));
        $this->load->view("basedata/createbasedata",$this->interface_menu);
    }
    /*
     * Asignamos usuarios a la base de datos
     */
    function asignarDb() {
        $option=array(
            "idBase"=>$this->basedata_model->getDataBases(array("nombre"=>$this->input->post("databaseList")))->id,
            "idBase_user"=>$this->basedata_model->getUsersDbProject(array("nick"=>$this->input->post("userbaseList")))->id,
            "nameBase"=>$this->input->post("databaseList"),
            "userBaseName"=>$this->input->post("userbaseList"),
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $r=$this->basedata_model->addUserData($option);
        if(!$r) {
        //$this->interface_menu["succes"]="<p> Se asigno el susuario a la base de datos </p>";
            $info=$this->basedata_model->getBaseDataUser(array("idPro"=>$this->session->userdata("us_idPro")));
            echo json_encode(array("result"=>true,"info"=>$info));
            return;

        }
        echo json_encode(array("result"=>false,"msg"=>"Se produjo un error en la asignación del susuario"));
        return;
    }
    /*
     * Esta función se activa con ajax desde el cliente
     * llama al modelo eliminar base de datos y kita los privilegios de usuario hace un revoke
     */
    function eliminarDb() {
    //$array=array("response"=>$this->input->post("dbName")."--> ");
    //echo json_encode($array);
        $r=$this->basedata_model->deleteBaseData(array("id"=>$this->input->post("idBase"),"nombre"=>$this->input->post("dbName")));

        $dbList=$this->basedata_model->getDataBases(array("idPro"=>$this->session->userdata("us_idPro")));
        $datos=array();
        foreach($dbList as $db) {
            $datos[]=$db->nombre;
        }
        if($r) {

            echo json_encode(array("response"=>"se borro la información con exito","data"=>$datos));
        }
    }
    function revokeUser() {

        $r=$this->basedata_model->revokeUser(array("database"=>$this->input->post("dbName"),"user_database"=>$this->input->post("dbUser"),"idDataBase"=>$this->input->post("idBase"),"idDataBaseUser"=>$this->input->post("idUser")));
        if($r) {
            echo json_encode(array("response"=>"Usuario revocado con éxito"));
        }
    }
    function  phpmyadmin(){
        $this->load->model("route");
        $row=$this->route->getConfigs();
        $data["url"]=$row->row()->phpadmin_url;
        $this->load->view("basedata/phpmyadmin",$data);
    }
}   
?>
