<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of proyecto
 *
 * @author Ditmar
 */
class Proyecto extends CI_Controller {
    function Proyecto() {
        parent::__construct();
        $this->load->model("proyecto_model");
        $this->load->model("usuario_model");
        $this->load->model("roles_model");
        $this->load->model("equipo_model");
        $this->load->model("repositorio_model");
    }
    //metemos el id
    function admin($id) {

        $this->load->model("repositorio_model");


        if($this->proyecto_model->checkProject($id)) {
            $this->session->set_userdata("us_idPro",$id);

            $repositorio=$this->repositorio_model->getRepositorio("idPro",$id);
            foreach($repositorio->result() as $rep) {
                $this->session->set_userdata("us_idRep",$rep->id);
            }

            $this->load->view('proyecto/admin_proyecto');
        }
    }
    function getMenuProjects() {
        $information=$this->proyecto_model->getResumen($this->session->userdata("us_idPro"));
        //parseamos la informacion respecto del proyecto
        $pro=$information[0];
        $infocad="";
        foreach($pro->result() as $project) {
            $infocad.="<h1>".$project->nombre."</h1>";
            if($project->logo==null) {
                $infocad.="<h1>Logo:</h1>"."<img src='/logo/logopp.jpg' alt='".$project->nombre."'/>";
            }else{
                $infocad.="<h1>Logo:</h1>"."<img src='".$project->logo."' title='".$project->nombre."' alt='".$project->nombre."'/>";
            }
            $infocad.="<h1>Descripcion:</h1>".$project->descripcion;
        }
        $teams=$information[1];
        $numbers=$information[2];
        $infocad.="<h1>Equipos</h1> (".$numbers.")";
        $infocad.="<ul>";
        foreach($teams->result() as $te) {
            $infocad.="<li>".$te->nombre."</li>";
        }
        $infocad.="</ul>";
        $this->load->model("equipo_model");
        $users=$this->equipo_model->getUsuarios("completeObject");
        $liPro=$this->proyecto_model->getProyecto($this->session->userdata("us_idPro"));
        $lider=$this->usuario_model->getUserById($liPro->row()->idUs,"row");
        //echo$liPro->row()->id;
        $usercad="<ul>";

        $usercad.="<li><a href='javascript:onCheckUserScript(".$lider->id.")'><img src='/uploads/".$lider->avatar."' title='Lider del proyecto ".$lider->nombre." ".$lider->apellido."' class='thumb' ></img></a></li>";
        foreach($users->result() as $us) {
            if($us->avatar==null) {
                $avatar="/logo/avatar.jpg";
            }else {
                $avatar="/uploads/".$us->avatar;
            }
            $usercad.="<li><a href='javascript:onCheckUserScript(".$us->id.")'><img src='".$avatar."' id='".$us->nick."' title='".$us->nombre." ".$us->apellido."' alt='".$us->nombre." ".$us->apellido."' class='thumb'></img></a></li>";

        }

        $usercad.="</ul>";
        echo json_encode(array("menu"=>$this->menu->getMenuProjects($this->proyecto_model->getPoliticas($this->session->userdata("us_idPro")),$this->proyecto_model->getAvatar(),$infocad,$usercad)));
    }
    function misproyectos() {
        if($this->session->userdata("us_c")==TRUE) {
            $this->interface_menu["loginControl"]=$this->menu->getControlLogin(true,$this->Usuario_model->getRoles(),$this->Usuario_model->getUser());
        }else {
            $this->interface_menu["loginControl"]=$this->menu->getControlLogin(false,'','');
        }
        if($this->proyecto_model->checkAdminProyect()) {
        //$this->interface_menu["proyectos"]=$this->proyecto_model->getProyects();
        //consultamos los permisos para crear el menu

        //$this->load->view('proyecto/list_proyecto');
        }

    }
    /*function getMenuProject() {
        $data=$this->menu->getMenuProjects($this->proyecto_model->getPoliticas($this->session->userdata("us_idPro")),$this->Proyecto_model->getAvatar());
        echo json_ecode($data);
    }*/
    function getMyProjects() {
        if($this->session->userdata("us_id")!="") {
        //proyectos que administras
            if($this->proyecto_model->checkAdminProyect()) {
                $result=$this->proyecto_model->getMyHelperProjects();

                $resultado=array(
                    "success"=>true,
                    "rows"=>$result
                );
                echo json_encode($resultado);
            }
        }
    }
    function crearProject() {
        $proyecto=array(
            'nombre'=>$this->input->post('nombre'),
            'summary'=>$this->input->post('summary'),
            'descripcion'=>$this->input->post('descripcion'),
            'licencia'=>$this->input->post('licencia'),
            'framework'=>$this->input->post('framework'),
            'create_at'=>mdate("%y/%m/%d",time()),
            'idUs'=>$this->session->userdata('us_id')
        );
        $id=$this->proyecto_model->setProyecto($proyecto);
        // $this->session->set_userdata("us_idPro",$id);
        $equipo=array(
            'idPro'=>$id,
            'idUs'=>$this->session->userdata('us_id'),
            'nombre'=>'nexus team',
            'create_at'=>mdate("%y/%m/%d",time())
        );
        $idEq=$this->proyecto_model->setEquipo($equipo);
        $lider=array(
            'idEq'=>$idEq
        );

        $idLi=$this->proyecto_model->setLider($lider);
        $lip=array(
            'idLi'=>$idLi,
            'idUs'=>$this->session->userdata('us_id')
        );
        $rl=array(
            'idUs'=>$this->session->userdata('us_id'),
            'idEq'=>$idEq
        );
        $idLi=$this->proyecto_model->putLider($lip,$rl);
        $bool=$this->proyecto_model->setAllPoliticas($id);
        echo json_encode(array("success"=>true));
    }
    function crear() {
        $this->load->view('proyecto/crear_proyecto');

    }
    function basededatos() {
        $this->load->view($this->interface_menu,"proyecto/db_proyecto");
    }

    /*
     * nombre
     */

    function invite() {
        $this->interface_menu["nombrePro"]=$this->proyecto_model->getNameProyecto();
        $this->load->view("proyecto/invite",$this->interface_menu);
    }
    /*
     *
     * Controlador de busquedas
     * funciona con jquery
     */
    function buscarUsuario() {
        $keyquest=$this->input->post('keyquest');
        $fields=array("nombre","nick","apellido");
        $list=$this->usuario_model->searchUser("usuario",$fields,$keyquest);
        $resultado=array();
        $descripcion=array();
        foreach($list->result() as $row) {
            $item=array($row->nick,$row->nombre,$row->apellido,$row->email,"<b>Msn</b> ".$row->msn."</br><b>Gmail</b> ".$row->gmail."<br/><b>Web</b>".$row->web);
            $resultado[]=$item;
        }
        $data=array("result"=>$resultado);
        echo json_encode($data);
    }
    /*
     * Proyecto perfil
     */
    function loadPerfil() {

        $data=$this->proyecto_model->getProyecto($this->session->userdata("us_idPro"));
        echo json_encode(array("success"=>true,"data"=>$data->row()));
    }
    function updateLogo() {
        /*$allowedExtensions = array("jpg","jpeg","gif","png");
        foreach ($_FILES as $file) {
            if ($file['tmp_name'] > '') {
                if (!in_array(end(explode(".",
                strtolower($file['name']))),
                $allowedExtensions)) {
                    echo json_encode(array("success"=>false,"message"=>"es un tipo inválido de archivo"));
                    return;
                }
            }
        }*/
        //$url=$_FILES['userfile']['name'];
        //echo $url;
        if(copy($_FILES['userfile']['tmp_name'],"./logo/".$_FILES['userfile']['name'])) {
            $proyecto=array(
                "logo"=>"/logo/".$_FILES['userfile']['name']
            );
            $this->_resize("./logo/".$_FILES['userfile']['name'],200,"./logo/".$_FILES['userfile']['name']);

             $this->proyecto_model->updateproject($proyecto,"id='".$this->session->userdata("us_idPro")."'");
             echo json_encode(array("success"=>true,"data"=>"Archivo Subído con éxito"));
             return;
        }
        echo json_encode(array("success"=>false,"data"=>"Error al Subir el archivo"));
        return;

    }
    function _resize($img, $thumb_width, $newfilename) {
        $max_width=$thumb_width;

        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        list($width_orig, $height_orig, $image_type) = getimagesize($img);

        switch ($image_type) {
            case 1: $im = imagecreatefromgif($img); break;
            case 2: $im = imagecreatefromjpeg($img);  break;
            case 3: $im = imagecreatefrompng($img); break;
            default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
        }

    /*** calculate the aspect ratio ***/
        $aspect_ratio = (float) $height_orig / $width_orig;

    /*** calulate the thumbnail width based on the height ***/
        $thumb_height = round($thumb_width * $aspect_ratio);


        while($thumb_height>$max_width) {
            $thumb_width-=10;
            $thumb_height = round($thumb_width * $aspect_ratio);
        }

        $newImg = imagecreatetruecolor($thumb_width, $thumb_height);

    /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($image_type == 1) OR ($image_type==3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg,true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

        //Generate the file, and rename it to $newfilename
        switch ($image_type) {
            case 1: imagegif($newImg,$newfilename); break;
            case 2: imagejpeg($newImg,$newfilename);  break;
            case 3: imagepng($newImg,$newfilename); break;
            default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
        }

        return $newfilename;
    }
    function updatePerfil() {
        $proyecto=array(
            'nombre'=>$this->input->post('nombre'),
            'summary'=>$this->input->post('summary'),
            'descripcion'=>$this->input->post('descripcion'),
            'descripcion'=>$this->input->post('descripcion'),
            'licencia'=>$this->input->post('licencia'),
            'framework'=>$this->input->post('framework'),
            'create_at'=>mdate("%y/%m/%d",time()),
            'update_at'=>mdate("%y/%m/%d",time()),
            'idUs'=>$this->session->userdata('us_id')
        );
        $this->proyecto_model->updateproject($proyecto,"id='".$this->session->userdata("us_idPro")."'");

        echo json_encode(array("success"=>true,"msg"=>"Se actualizo con éxito"));
    }

    function perfil() {
        if(!$this->roles_model->checkPermit(83)) {
            $this->load->view("error",$this->roles_model->getError(83));
            return;
        }
        $data=$this->proyecto_model->getResumen($this->session->userdata("us_idPro"));
        $this->interface_menu["data_proyecto"]=$data[0]->result();
        $this->interface_menu["data_equipo"]=$data[1]->result();
        $this->load->view("proyecto/perfil",$this->interface_menu);
    }
    /*
     * actualizamos la invitacion
     */
    function acepto() {
        $this->load->view("usuario/acepto");
    }
    function checkCode() {
        $id=$this->input->post("id");
        $idPro=$this->input->post("idPro");
        $this->load->model("invitacion_model");
        if($this->session->userdata("id_in")==$id) {
        //hacemos el update de la invitación
            $this->invitacion_model->update_data(array("acepted"=>"acepted"),$id);
            //creamos la relacion entre el usurio y el proyecto
            //insertamos dentro del rel_us_pro
            $res=$this->proyecto_model->insert_rel_us_pro(array("idPro"=>$idPro,"idUs"=>$this->session->userdata("us_id"),"fecha"=>mdate("%y/%m/%d",time())));
            $this->proyecto_model->setBasicPoliticas($idPro);
            $proyecto=$this->proyecto_model->getProyecto($idPro);
            $pro=$proyecto->result_array();
            echo json_encode(array("result"=>"Gracias por aceptar la invitacion <br/> Se te asignarón los permisos básicos al sistema, puedes entrar por este enlacé <a href='/index.php/Proyecto/admin/".$idPro."'>".$pro[0]["nombre"]."</a> <br/> el Lider del proyecto te asignara más permisos si los necesitas"));
        //echo json_encode(array("result"=>"El Lider del proyecto te asignara más permisos si los necesitas"));
        }else {
            echo json_encode(array("result"=>"Usted no tiene permiso para ver la información"));
        }
    //echo json_encode(array("result"=>"Usted no tiene permiso para ver la información ".$id));
    }
    function sendinvite() {
        /*
         * Incluir seguridad
         *
         */

        $this->load->model("usuario_model");
        $this->load->model("invitacion_model");
        $title=$this->input->post("_title");
        $nameProyect=$this->input->post("_name");
        $subject=$this->input->post("_subject");
        $idUser=$this->usuario_model->getIdUser($subject,"array");
        $message=$this->input->post("_message");

        $fillInvitacion=array(
            "title"=>$title,
            "message"=>$message,
            "idPro"=>$this->session->userdata("us_idPro"),
            "idRe"=>$this->session->userdata("us_id"),
            "idUs"=>$idUser[0]["id"],
            "fecha"=>mdate("%y/%m/%d",time()),
            "readed"=>false
        );
        //echo json_encode(array("title"=>$fillInvitacion["idUs"]));
        $result=$this->invitacion_model->fillInvitacion($fillInvitacion);
        //echo json_encode(array("title"=>$result));
        /*
         * Crearemos la página resultante de la invitacion
         */
        if($result) {
            $data=array(
                "message"=>"<br/>Usted ha sido invitado a formar parte del proyecto ".$nameProyect."  <br/>
                copie el código de seguridad Se adjunto el siguiente mensaje del usuario ".$subject."</b><br/>".$message
            );
            /*
             * creamos la relacion
             */
            $rel=$this->invitacion_model->fill_rel_users_invite(array("idIn"=>$result[0]["id"],"idUs"=>$idUser[0]["id"]));
            $info=$this->invitacion_model->update_data($data,$result[0]["id"]);
            echo json_encode(array("title"=>$info[0]["title"],"msn"=>"<br/> Se ha enviado el mensaje a ".$subject." espere la confirmación del mismo "));
            return null;
        }
        echo json_encode(array("title"=>"Error"));
        return null;
    }
    /*
     * Roles de Usuario Sobre el proyecto
     */
    function viewRoles($id) {
        /*
         * Incluir seguridad
         *
         */

        $parameters=explode("-", $id);
        $user["id"]=$parameters[0];
        $user["idEq"]=$parameters[1];
        $this->load->model("usuario_model");
        $user["user"]=$this->usuario_model->getUserById($id,"array");

        $this->load->view("proyecto/roles",$user);

    }
    /*
     * regresa politicas
     */
    function _buscarDuplicados($arreglo,$key,$item) {
        for($i=0;$i<count($arreglo);$i++) {
            if($arreglo[$i][$key]==$item) {
                return true;
            }
        }
        return false;

    }
    function getPolitica($id) {
        /*
         * Incluir seguridad
         *
         */
        $start = @$_REQUEST["start"];
        $limit = @$_REQUEST["limit"];
        $results=$this->proyecto_model->getAllPoliticas();
        $userresults=$this->proyecto_model->getAllUserPoliticas($id);
        $rules=array();
        $aux_data=$results->result_array();

        for($i=0;$i<count($aux_data);$i++) {
            foreach($userresults->result() as $user) {
                if($aux_data[$i]["id"]==$user->id) {
                    $aux_data[$i]["id"]=-1;
                }
            }
        }
        for($i=0;$i<count($aux_data);$i++) {
            if($aux_data[$i]["id"]>0) {
                $rules[]=array(
                    "id"=>$aux_data[$i]["id"],
                    "nombre"=>$aux_data[$i]["nombre"],
                    "tipo"=>$aux_data[$i]["tipo"],
                    'url'=>$aux_data[$i]["url"]
                );
            }
        }
        $finalResult=array(
            "totalcount"=>$results->num_rows(),
            "rules"=>$rules

        );
        echo json_encode($finalResult);
    }
    function getUserPolitica($id) {
        /*
         * Incluir seguridad
         *
         */
        $start = @$_REQUEST["start"];
        $limit = @$_REQUEST["limit"];
        $results=$this->proyecto_model->getAllUserPoliticas($id);
        $rules=array();
        foreach($results->result() as $r) {
            $rules[]=array(
                'id'=>$r->id,
                'nombre'=>$r->nombre,
                'tipo'=>$r->tipo,
                'url'=>$r->url
            );
        }
        $finalResult=array(
            "totalcount"=>$results->num_rows(),
            "rules"=>$rules

        );
        echo json_encode($finalResult);
    }
    function setRol() {
        /*
         * incluir seguridad
         */
        $idRol=$this->input->post("idRol");
        $idUs=$this->input->post("idUs");
        $row=array(
            "idUs"=>$idUs,
            "idPo"=>$idRol,
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $result=$this->proyecto_model->setPolitica($row);
        //echo json_encode(array("result"=>$this->session->userdata("us_idPro")));
        echo json_encode(array("result"=>$result));
    }
    function deleteRol() {
        /*
         * agregar seguridad
         */
        $idRol=$this->input->post("idRol");
        $idUs=$this->input->post("idUs");
        $row=array(
            "idUs"=>$idUs,
            "idPo"=>$idRol,
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $result=$this->proyecto_model->removePolitica($row);
        echo json_encode(array("result"=>$result));
    }
    /*
     * toda la información del componente
     * Wall
     * Probablemente el más importante componente de información
     */
    function getallwall() {
        $resultado=$this->proyecto_model->getProjectWall($this->session->userdata("us_idPro"),null,10,10);
        echo json_encode(array("success"=>true,"data"=>$resultado));
    }
    /*
     *
     * Cargamos el editor de codigo funcion
     * Especializada para Extjs
     *
     */
    function GoEditing() {

        $this->load->view("codeeditor/codeeditor");
    }
    /*
     * cargamos los ultimos cambios
     *
     *
     */
    function lastchanges() {
        $this->load->view("proyecto/lastchanges");
    }
    /*
     * Administración de los usuarios
     * del sistema
     * que son parte del proyecto
     *
     */
    function loadadminuser($id){
        $user=$this->usuario_model->getUserById($id,"row");

        $info["user"]=$user;
        $team=$this->equipo_model->getTeamRelation($user->id,$this->session->userdata("us_idPro"));
        $info["teams"]=$team;
        foreach($team->result() as $t){
            $info["workspaces"][]=$this->repositorio_model->getWorkSpace($id,$t->id);
        }
        $this->load->view("proyecto/usuario/adminus",$info);
    }
    /*
     * MOSTRAMOS LAS INVITACIONES ENVIADAS
     */
    function loadInvitaciones(){
        $start=isset($_POST["start"])?$_POST["start"]:0;
        $limit=isset($_POST["limit"])?$_POST["limit"]:20;
        $this->load->model("invitacion_model");
        $r=$this->invitacion_model->getProInvites($start,$limit);

        $data=array(
            "success"=>true,
            "rows"=>$r->result(),
            "totalCount"=>$this->invitacion_model->getCount()
        );
        echo json_encode($data);
    }
    /*
     * rechazar invitqaciones
     */
    function rechazo(){
        $id=$this->input->post("id");
        $infor=array(
            "acepted"=>"refuse"
        );
        $this->invitacion_model->update_data($infor,$id);
        echo json_encode(array("result"=>true,"message"=>"Se ha rechazado la invitacion a formar parte del proyecto"));
    }
}
?>
