<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class wall extends CI_Controller {
    
    function wall() {
        parent::__construct();
        $this->load->model("wall_model");
        $this->load->model("usuario_model");
        $this->load->model("repositorio_model");
        $this->load->model("lastchanges_model");
    }
    function layoutWall() {
        $this->load->view("proyecto/wall/mainlayout");
    }
        /*
         * Con seguridad
         */
    function submitwall() {
        if($this->session->userdata("us_id")!=null) {
            if($this->session->userdata("us_idPro")!=null) {
                $info=array(
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>mdate("%h:%i:%a",time()),
                    "titulo"=>$this->input->post("title"),
                    "cuerpo"=>$this->input->post("cuerpo"),
                    "idUs"=>$this->session->userdata("us_id"),
                    "idPro"=>$this->session->userdata("us_idPro")
                );

                $id=$this->wall_model->insertnew($info);
                $us=$this->usuario_model->getUser();
                if($us->avatar==null) {
                    $us->avatar="/logo/avatar.jpg";
                }
                $info["id"]=$id;
                $info["avatar"]=$us->avatar;
                $info["nick"]=$this->session->userdata("us_nick");
                echo json_encode(array("success"=>true,"data"=>$info));
                return;

            }

            echo json_encode(array("success"=>false,"data"=>false));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>false));
        return;

    }
    function submitwallPost($params) {
        $param=explode("-",$params);
        if($this->session->userdata("us_id")!=null) {
            if($this->session->userdata("us_idPro")!=null) {
                /*
                 * Verificamos Que Workspace tiene este usuario
                 */
                $repositorio=$this->repositorio_model->getWorkSpace($this->session->userdata("us_id"),$this->session->userdata("idEq"));
                if(!($repositorio->num_rows()>0)) {
                    echo json_encode(array("success"=>false,"data"=>"No puedes publicar información, no cumples el requisito"));
                    return;
                }
                foreach($repositorio->result() as $row) {
                    $url=$row->path;
                    $permit=$row->permit;
                }
                 /*
                  * indica que habilita el mostrado de los archivos
                  */
                $btn="";
                /*
                 * insertamos dentro de la tabla lastchnages
                 */
                $information=array(
                    "idEq"=>$this->session->userdata("idEq"),
                    "idUs"=>$this->session->userdata("us_id"),
                    "idPro"=>$this->session->userdata("us_idPro"),
                    "fecha"=>mdate("%y/%m/%d",time())." ".mdate("%h:%i:%a",time()),
                    "estado"=>"review"
                );
                $this->lastchanges_model->insert($information);
                if($param[1]=="publicFiles") {
                    $this->repositorio_model->updateWorkSpace(array("permit"=>"public"),"idUs='".$this->session->userdata("us_id")."' and idEq='".$this->session->userdata("idEq")."'");
                    $btn='<br/><b>Ver Archivos</b><input type="hidden" id="__idEq" value="'.$this->session->userdata("idEq").'"/>
                        <input type="hidden" id="__idUs" value="'.$this->session->userdata("us_id").'"/>
                         <input type="button" id="_fileExplorer" value="Ver Archivos"/>';
                }
                $info=array(
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>mdate("%h:%i:%a",time()),
                    "titulo"=>$this->input->post("title"),
                    "cuerpo"=>$this->input->post("cuerpo")."<br/><b>Puedes Probar la aplicacion desde este link</b><br/><a href='".$url."'>Test Aplication</a>".$btn,
                    "idUs"=>$this->session->userdata("us_id"),
                    "idPro"=>$this->session->userdata("us_idPro")
                );
                $id=$this->wall_model->insertnew($info);
                $us=$this->usuario_model->getUser();
                if($us->avatar==null) {
                    $us->avatar="/logo/avatar.jpg";
                }
                $info["id"]=$id;
                $info["avatar"]=$us->avatar;
                $info["nick"]=$this->session->userdata("us_nick");
                echo json_encode(array("success"=>true,"data"=>$info));
                return;

            }

            echo json_encode(array("success"=>false,"data"=>false));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>false));
        return;

    }
    function deleteComent(){
        $id=$this->input->post("id");
        $this->wall_model->deleteComent($id);
        echo json_encode(array("success"=>true));
    }
    function deleteNew(){
        $id=$this->input->post("id");
        /*
         * vemos si tienen comentarios
         */
       $data= $this->wall_model->getConewsComents($id);
       if($data->num_rows()>0){
           echo json_encode(array("success"=>false,"message"=>"no puedes Borrar una noticia que ya ha sido comentada"));
           return;
       }
    $this->wall_model->deleteNews($id);
        echo json_encode(array("success"=>true));

    }
   /*
     *
     *
         * con seguridad
         */
    function submitcoment() {
        if($this->session->userdata("us_id")!=null) {
            if($this->session->userdata("us_idPro")!=null) {
                $info=array(
                    "idUs"=>$this->session->userdata("us_id"),
                    "idPro"=>$this->session->userdata("us_idPro"),
                    "idNo"=>$this->input->post("idNoti"),
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>mdate("%h:%i:%a",time()),
                    "comentario"=>$this->input->post("coment")
                );
                $id=$this->wall_model->insertcoment($info);
                $us=$this->usuario_model->getUser();
                if($us->avatar==null) {
                    $us->avatar="/logo/avatar.jpg";
                }
                $info["id"]=$id;
                $info["avatar"]=$us->avatar;
                $info["nick"]=$this->session->userdata("us_nick");
                echo json_encode(array("success"=>true,"data"=>$info));
                return;
            }
            echo json_encode(array("success"=>false,"data"=>false));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>false));
        return;
    }
    function fillform() {
        $id=isset($_POST["id"])?$_POST["id"]:0;
        $action=isset($_POST["action"])?$_POST["action"]:"send";
        if($action=="fillform") {
            $coments=$this->wall_model->getComent($id);
            $info = array(
                'success'=>true,
                'data'=> $coments
            );
            echo json_encode($info);
        }else {
            $information=array(
                "id"=>$id,
                "updatedate"=>mdate("%y/%m/%d",time()),
                "updatehour"=>mdate("%h:%i:%a",time()),
                "comentario"=>$this->input->post("comentario")
            );

            $this->wall_model->updatecoment($information);
            echo json_encode(array("success"=>true,"data"=>$information));
        }

    }
    function fillnew() {
        $id=$_POST["id"];
        $action=isset($_POST["action"])?$_POST["action"]:"send";
        if($action=="fillnew") {
            $r=$this->wall_model->getnew($id);
            $info = array(
                'success'=>true,
                'data'=> $r->row()
            );
            echo json_encode($info);
        }else {
            $info=array(
                "id"=>$id,
                "titulo"=>$this->input->post("titulo"),
                "cuerpo"=>$this->input->post("cuerpo"),
                "idUs"=>$this->session->userdata("us_id"),
                "idPro"=>$this->session->userdata("us_idPro"),
                "updatedate"=>mdate("%y/%m/%d",time()),
                "updatetime"=>mdate("%h:%i:%a",time())
            );
            $this->wall_model->updatenew($info);
            echo json_encode(array("success"=>true,"data"=>$info));
        }
    }
}
?>
