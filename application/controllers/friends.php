<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of friends
 *
 * @author Ditmar
 */
class friends extends CI_Controller {
    function friends() {
        parent::__construct();
        $this->load->model("friends_model");
        $this->load->model("tweet_model");
        $this->load->model("usuario_model");
        $this->load->model("msn_model");

    }
    /*
     * Se optiene  una lista de amigos
     *
     */

    function getFriends() {
        $start=$this->input->post("start");
        $limit=$this->input->post("limit");
        $params["start"]=$start;
        $params["limit"]=$limit;
        $friends=$this->friends_model->getUsuarios($params);
        $listFr=array();
        foreach($friends->result() as $fr) {
            $listFr[]=array("id"=>$fr->id,"title"=>$fr->nick,"avatar"=>$fr->avatar,"fecha_arg"=>$fr->fecha_confirm,"nombre"=>$fr->nombre,"apellido"=>$fr->apellido);
        }
        $result=array(
            "success"=>true,
            "data"=>$listFr
        );
        echo json_encode($result);
    }


    function removeFriends() {
        $id=$this->input->post("idFr");
        $this->friends_model->delete($id);
    }
    /*
     * Se confirma a los amigos
     *
     */
    function confirmFriend() {
        $type=$this->input->post("contest");
        $id=$this->input->post("idFr");
        if(isset($type)) {
            $updates=array(
                "confirm"=>$type,
                "fecha_confirm"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
            );
            $r=$this->friends_model->updatecontest($type,$updates,"idFr='".$id."' and idUs='".$this->session->userdata("us_id")."'");
            if($type=="refused") {

                echo json_encode(array("success"=>true,"Messaje "=>"Se ha rechazado al amigo"));
                return;
            }
            if($type=="accepted") {
                echo json_encode(array("success"=>true,"Messaje "=>"Se ha agregado el contacto"));
                return;
            }
        }
    }
    /*
     * Se agrega a los amigos
     */
    function setFriends() {
        $follow=$this->input->post("follow");
        //$nick=$this->input->post("nick");
        $friends=array(
            "idFr"=>$this->input->post("idFr"),
            "idUs"=>$this->session->userdata("us_id"),
            "fecha"=>mdate("%y/%m/%d",time()),
            "confirm"=>"nocontest"
        );

        $this->friends_model->insert($friends);
        /*
         * agreamos una relación al teet
         */
        if(isset($follow)&&$follow==true) {
            $id=$this->tweet_model->followTweetsUser($this->input->post("idFr"));
        }
        /*
         * Enviamos mensaje informando que 
         */
        if($this->input->post("response")!="response") {
            $mensaje=array(
                "idUs"=>$this->input->post("idFr"),
                "idRem"=>$this->session->userdata("us_id"),
                "mensaje"=>$this->session->userdata("us_nick")." Ha pedido ser tú amigo, ¿deseas agregarlo como amigo? <input type='hidden' id='___type' value='addfriend' /><input type='hidden' id='parameter_messages_panel' value='".$this->session->userdata("us_id")."'><div class='_panel_messages_mails'><input type='button' id='acepted_btn_panel' value='Aceptar'/>   <input type='button' id='refushed_btn_panel' value='rechazar'/></div>",
                "create_at"=>mdate("%y/%m/%d"),
                "subject"=>$this->session->userdata("us_nick")." te a agregado a sus amigos",
                "readed"=>false


            );
        }elseif($this->input->post("response")=="response"){
            $mensaje=array(
                "idUs"=>$this->input->post("idFr"),
                "idRem"=>$this->session->userdata("us_id"),
                "mensaje"=>$this->session->userdata("us_nick")." Te ha aceptado como amigo =)",
                "create_at"=>mdate("%y/%m/%d"),
                "subject"=>$this->session->userdata("us_nick")." te a agregado a sus amigos",
                "readed"=>false


            );
        }
        $this->msn_model->sendMessage($mensaje);
        echo json_encode(array("success"=>true,"messaje"=>"<b>".$this->input->post("nickFr")."</b> Se ha agredo a tus amigos espera su confirmacion"));
    }
    /*
     * buscar amigo
     */
    function searchFriend() {
        $cad=$this->input->post("stringquest");
        $result=$this->usuario_model->searchUser("usuario",array("nick","nombre","apellido","email"),$cad);
        /*
         * sacamos los amigos
         */
        
        $row_s=array(
            "success"=>true,
            "rows"=>$result->result()
        );
        echo json_encode($row_s);
    }


}
?>
