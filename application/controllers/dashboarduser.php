<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class  dashboarduser extends CI_Controller {
    function dashboarduser() {
        parent::__construct();
        $this->load->model("invitacion_model");
        $this->load->model("tweet_model");
    }
    function renderInvite() {

        $this->load->view("dashboarduser/invitaciones/invite");
    }
    function renderMessages() {
        $this->load->view("dashboarduser/messages/messages");
    }
    function perfil() {
        $this->load->view("perfil/loadinfo");
    }
    function myfriends() {
        $this->load->view("dashboarduser/friends/friends");
    }
    function mytwitter() {
        $this->load->view("dashboarduser/tweets/tweets");
    }
    function myprojects() {
        $this->load->view("dashboarduser/projects/projects");
    }
    /*
     * Entraga las peticiones
     * de las invitaciones al usuario en su bandeja
     */
    function bandejaInvitaciones($param) {
        $start = @$_REQUEST["start"];
        $limit = @$_REQUEST["limit"];


        $data=$this->invitacion_model->getMyInvitations($start,$limit,"",$param);

        $total=$this->invitacion_model->getInvTotal();
        $rows=array();
        foreach($data as $d) {
            $rows[]=array("readed"=>$d->readed,"title"=>$d->id." ".$d->title."3mo3D".$d->acepted,"fecha"=>$d->fecha,"avatar"=>"/uploads/".$d->avatar);

        }
        $result=array(
            "totalCount"=>$total,
            "bandeja"=>$rows
        );
        echo json_encode($result);
    }
    /*
     * cargar la invitacion
     */
    function loadInvite() {
        $id=$this->input->post("id");
        $invitacion=$this->invitacion_model->getInvitation($id,"");
        /*
         * además de llenar la informacion del mensaje crearemos el 
         * formulario de respuesta dinámicamente
         */
        //generaremos el formulario

        foreach($invitacion as $invite) {
            $result=array(
                "title"=>$invite->title,
                "message"=>$invite->message."<br/>"
            );
            if(!$invite->readed) {
            //hacemos el update para marcar como leido
                $this->invitacion_model->update_data(array("readed"=>true),$id);
            }
        }
        echo json_encode($result);

    }
    function renderMessage($id) {
    //consultamos la base de datos
    //hacemos esto por seguridad
        $data["invitacion"]="";
        //$tam=$this->invitacion_model->getIds_rel_users_invite($id);
        if($this->invitacion_model->getIds_rel_users_invite($id)==1) {
            $this->session->set_userdata("id_in",$id);
            $data=array();
            $data["invitacion"]=$this->invitacion_model->getInvitation($id,"");
            //ponemos el mensaje como leido
            $this->invitacion_model->update_data(array("readed"=>true),$id);
            $data["message"]="";

        }else {
            $data["message"]="Usted no cuenta con el permiso para visualizar esta información";
        }
        $this->load->view("dashboarduser/invitaciones/mails",$data);

    }
    function bandejaDeEntrada() {

    }
    function insertTweet() {
        $message=$this->input->post("message");
        $tweet=array("message"=>$message,"fecha"=>mdate("%Y/%m/%d %h:%i:%a",time()));
        $id=$this->tweet_model->insertTweets($tweet);
        echo json_encode(array("result"=>$message));
    }
    function bandejaMyTweets() {
        $start = ($_POST['start']); //posición a iniciar
        $limit = ($_POST['limit']);
        $resultado=$this->tweet_model->getTweetsById($this->session->userdata("us_id"),$start,$limit);
        $result=array();
        foreach($resultado->result() as $lista) {
            $result[]=array("idUs"=>$lista->idUs,"avatar"=>$lista->avatar,"id"=>$lista->id,"message"=>"<b>".$lista->nick."</b> escribio el  ".$lista->fecha."<br/> ".$lista->message);
        }
        $data=array(
            "success"=>true,
            "totalCount"=>$this->tweet_model->getCount($this->session->userdata("us_id")),
            "rows"=>$result
        );
        echo json_encode($data);
    }
    
}
?>
