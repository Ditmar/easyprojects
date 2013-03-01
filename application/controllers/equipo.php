<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class equipo extends CI_Controller {
    var $interface_menu;
    function equipo() {
        parent::__construct();
        $this->load->model("Usuario_model");
        //$this->load->model("Proyecto_model");
        $this->load->model("Equipo_model");
        $this->load->model("repositorio_model");
        
    }
    function equipos() {

        $this->load->view("equipos/equipos");
    }
    function adminequipo() {

        $this->load->view("equipos/adminequipo",$this->interface_menu);
    }
    /*
     * Métodos controladores de adminequipo
     */
    function getUsuarios() {
     
    //rescatamos los parámetros iniciales
        $start = @$_REQUEST["start"];
        $limit = @$_REQUEST["limit"];
        $ususarios=$this->Equipo_model->getUsuarios("completeObject");
        
        $rows=array();
        foreach($ususarios->result() as $user) {
            $rows[]=array(
                "id"=>$user->id,
                "nick"=>$user->nick,
                "infpersonal"=>"<b>Nombres</b><br/>".$user->nombre."<br/><b>Apellidos</b><br/>".$user->apellido,
                "email"=>$user->email,
                "contacto"=>$user->icq."<br/>".$user->msn."<br/>".$user->gmail."<br/>".$user->web."<br/>".$user->facebook,
                "fecha"=>$user->fecha,
                "avatar"=>"/uploads/".$user->avatar
            );
        }
        /*
         * pedimos al lider
         * $lider->row()
         */
        $lider=$this->Usuario_model->getUserByProject();
        $rows[]=array(
            "id"=>$lider->row()->id,
            "nick"=>$lider->row()->nick,
            "infpersonal"=>"<b>Lider y creadora de este <br/> proyecto</b> <b>Nombres</b><br/>".$lider->row()->nombre."<br/><b>Apellidos</b><br/>".$lider->row()->apellido,
            "email"=>$lider->row()->email,
            "contacto"=>$lider->row()->icq."<br/>".$lider->row()->msn."<br/>".$lider->row()->gmail."<br/>".$lider->row()->web."<br/>".$lider->row()->facebook,
            "fecha"=>$lider->row()->fecha,
            "avatar"=>"/uploads/".$lider->row()->avatar
        );
        
        $result=array(
            "totalCount"=>$ususarios->num_rows(),
            "users"=>$rows
        );
        echo json_encode($result);
    }
    function decode() {
        echo json_encode(array("result"=>$this->encrypt->decode($this->input->post("id"))));
    }

    function getEquipos($r=null) {

        $start = @$_REQUEST["start"];
        $limit = @$_REQUEST["limit"];
        //$start=0;
        //$limit=10;
        $equipos=$this->Equipo_model->getEquipo(null,null,array("start"=>$start,"limit"=>$limit));
        $rows=array();

        foreach($equipos->result() as $team) {
            $user=$this->Equipo_model->getRelacion($team->id);
            $listUser="";
            if(!isset($r)) {

                foreach($user->result() as $us) {
                    $listUser.=$us->id."-".$us->nick."|token|";
                }
            }else {
                foreach($user->result() as $us) {
                    $listUser.="<img src='/uploads/".$us->avatar."' title='".$us->nick."' class='thumb'/><br/><b>".$us->nick."</b></br>";
                }
            }
            $rows[]=array(
                "id"=>$team->id,
                "nombre"=>$team->nombre,
                "create_at"=>$team->create_at,
                "users"=>$listUser,
                "actions"=>"/images/team/user_add.png,/images/team/note_delete.png,/images/team/time_add.png,/images/team/package_add.png"
            );
        }
        $result=array(
            "success"=>true,
            "totalCount"=>$equipos->num_rows(),
            "teams"=>$rows
        );
        echo json_encode($result);
    }
    function createTeam() {
        $nombre=$this->input->post("nombre");
        $equipo=array(
            "idPro"=>$this->session->userdata("us_idPro"),
            "idUs"=>$this->session->userdata("us_id"),
            "nombre"=>$nombre,
            "create_at"=>mdate("%y/%m/%d",time())
        );
        $id=$this->Equipo_model->setEquipo($equipo);
        if($id>0) {
            echo json_encode(array("result"=>"Equipo ".$nombre.", se creo correctamente"));
        }else {
            echo  json_encode(array("result"=>"Error al crear el equipo ".$nombre));
        }


    }
    function addUser() {
        $idUs=$this->input->post("idUs");
        $idTeam=$this->input->post("idTeam");

        $r=$this->Equipo_model->setRelation(array("idUs"=>$idUs,"idEq"=>$idTeam));
        if($r) {
            echo  json_encode(array("result"=>"Relacion Satisfactoria!"));
        }else {
            echo  json_encode(array("result"=>"Error en la relación"));
        }
    }
    function getUs($id) {
        $users=$this->Equipo_model->getRelacion($id);
        /*
         * creamosl a lista en el arreglo
         */
        $row=array();
        foreach($users->result() as $us) {
            $row[]=array(
                "id"=>$us->id,
                "nombre"=>$us->nombre,
                "nick"=>$us->nick,
                "avatar"=>"/uploads/".$us->avatar
            );
        }
        
        echo json_encode(array("users"=>$row,"totalcount"=>$users->num_rows()));
    }
    /*
     * sirve para verificar si el usuario tiene un workspace
     * se usa para el borrado
     */
    function getWorkSpace() {
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");

        $r=$this->repositorio_model->getWorkSpace($idUs,$idEq);
        if($r->num_rows()==1) {
            echo json_encode(array("success"=>true,"message"=>"este usuario tiene un repositorio ".$r->row()->path));
        }elseif($r->num_rows()==0) {

            $this->repositorio_model->deleteworkspace($idUs,$idEq);
            $this->Equipo_model->delete($idUs,$idEq);

            echo json_encode(array("success"=>false,"message"=>"no cuenta con un repositorio "));
        }
    }

    function deleteWorkSpace() {
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        /*
         * hacemos el borrado físico
         */

        if(isset($idEq)&&$idEq!="") {
            $r=$this->repositorio_model->getWorkSpace($idUs,$idEq);

            $this->_deleteDirectory($r->row()->rutaabs);

            $this->repositorio_model->deleteFiles($idUs,$idEq);
            $this->repositorio_model->deleteworkspace($idUs,$idEq);
            //$r=$this->repositorio_model->deleteFiles($idUs,$idEq);



            $this->Equipo_model->delete($idUs,$idEq);
            echo json_encode(array("success"=>true,"message"=>""));
            return;
        }

        echo json_encode(array("success"=>false,"message"=>$idUs."  ".$idEq));
        return;

    }
    function _deleteDirectory($dir) {

        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) {
        //$this->repositorio_model->updateFilesCopy($ruta,$abs,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
            return unlink($dir);
        };
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->_deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->_deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        //$this->repositorio_model->updateFilesCopy($ruta,$abs,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        return rmdir($dir);
    }
    function removeTeam() {
        $idEq=$this->input->post("idEq");
        /*
         * vemos la relación
         */
        $rows=$this->Equipo_model->getRelacion($idEq);
        if($rows->num_rows()==0) {
            $this->Equipo_model->removeTeam($idEq);

            echo json_encode(array("success"=>true));
            return;
        }
        echo json_encode(array("success"=>false));
        return;

    }
}
?>
