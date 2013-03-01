<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message
 *
 * @author Ditmar
 */
class message extends CI_Controller {
    function message(){
        parent::__construct();
        $this->load->model("msn_model");
        $this->load->model("usuario_model");
    }
    function getMessages(){
        $start=Isset($_POST["start"])?$_POST["start"]:"0";
        $limit=Isset($_POST["limit"])?$_POST["limit"]:"20";
        $r=$this->msn_model->getMessages($start,$limit);
        $respuesta=array(
            "success"=>true,
            "rows"=>$r->result_array(),
            "totalCount"=>$this->msn_model->countMessages()
        );
        echo json_encode($respuesta);
    }
    function updateReaded(){
        $id=$this->input->post("id");
        $info=array(
            "readed"=>true
        );
        $this->msn_model->update($info,"id='".$id."'");
        echo json_encode(array("success"=>true));
    }
    function delete(){
        $id=$this->input->post("id");
        $this->msn_model->deleteMessage($id);
        echo json_encode(array("success"=>true));
    }
    function insert(){

        $subject=$this->input->post("subject");
        $sendNick=$this->input->post("sendNick");
        $msn=$this->input->post("mensaje");
        $user=$this->usuario_model->getUserByNick($sendNick);



        $mensaje=array(
            "idUs"=>$user->row()->id,
            "idRem"=>$this->session->userdata("us_id"),
            "mensaje"=>$msn,
            "create_at"=>mdate("%y/%m/%d"),
            "subject"=>$subject,
            "readed"=>false
            
            
        );
        $this->msn_model->sendMessage($mensaje);
        echo json_encode(array("success"=>true,"msg"=>"El Mensaje se envio correctamente"));
    }
    //put your code here
}
?>
