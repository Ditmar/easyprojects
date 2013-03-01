<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of registro_usuario
 *
 * @author Ditmar
 */
class usuario extends CI_Controller {
    var $interface_menu;
    function usuario() {
        parent::__construct();
        $this->load->model("Usuario_model");
        $this->load->model("tweet_model");
    }
    function index() {

    }
    function logea() {
        
        $r=$this->Usuario_model->checkAutentication($this->input->post("_nick"),$this->input->post("_password"));
        if($r) {
            if($r->code=="activo") {
                $data=array(
                    'us_id'=>$r->id,
                    'us_nick'=>$r->nick,
                    'us_email'=>$r->email,
                    'us_c'=>TRUE
                );
                $this->session->set_userdata($data);
                echo json_encode(array("success"=>true,"msg"=>"Bienvenido ".$r->nick,"id"=>$r->id,"avatar"=>$r->avatar));
            }else{
                echo json_encode(array("success"=>true,"msg"=>"comprobar"));
            }
        //echo json_encode(array("type"=>true,"form"=>$this->menu->getControlLogin(true,$this->Usuario_model->getRoles(),$this->Usuario_model->getUser())));
        }else {
            echo json_encode(array("success"=>false,"msg"=>"Es posible que el Usuario no este registrado"));
        }

    }
    function loadmenulogin() {
    //$roles=$this->Usuario_model->getRoles();
        $html=$this->menu->getMainMenu($this->Usuario_model->getRoles(),$this->Usuario_model->getUser());
        echo json_encode(array("roles"=>$html));
    }
    function checkCodeAutentication(){
        $code=$this->input->post("code");
        $r=$this->Usuario_model->checkCode($code);
        if($r->num_rows()>0){
            $usuario=array(
                "code"=>"activo"
            );
            $res=$this->Usuario_model->updateCode($usuario,"id='".$r->row()->id."'");
            echo json_encode(array("success"=>true));
        }else{
            echo json_encode(array("success"=>false,"msg"=>"Código Incorrecto"));
        }
    }
    function checksession() {
        if($this->session->userdata("us_id")!="") {
            $us=$this->Usuario_model->getUser();
            $info=array("result"=>true,"html"
                =>"Bienvenido ".$this->session->userdata("us_nick")." <a href='/index.php/usuario/logOut'>LogOut</a>","id"=>$this->session->userdata("us_id"),"avatar"=>$us->avatar
            );
            echo json_encode($info);
        }else {
            $info=array("result"=>false,"html"=>"<input type='button' id='loginss' value='Entrar'/>"
            );
            echo json_encode($info);
        }
    }
    
    function logOut() {
        $this->session->sess_destroy();
        redirect("");
    //$this->load->view("inicio");
    }
    function registro() {
        $this->load->view("registro_usuarios");
    }
    function submitRegistro() {
        /*
         * agregamos el code generado
         */
        $usuario=array(
            'nombre'=>$this->input->post("nombres"),
            'apellido'=>$this->input->post("apellidos"),
            'nick'=>$this->input->post("nick"),
            'pass'=>md5($this->input->post("repassword")),
            'icq'=>$this->input->post('icq'),
            'msn'=>$this->input->post('msn'),
            'gmail'=>$this->input->post('gmail'),
            'web'=>$this->input->post('web'),
            'facebook'=>$this->input->post('facebook'),
            'pais'=>$this->input->post('pais'),
            'create_at'=>mdate("%y/%m/%d",time()),
            'email'=>$this->input->post('email'),
            'activo'=>'0',
            'code'=>$this->encrypt->encode($this->input->post("nick"))
        );
            /*
             * Verificamos el nick y el email
             */

        $this->load->model("Usuario_model");
        //if()
        if($this->input->post("password")==$this->input->post("repassword")) {
            if($this->Usuario_model->checkNick($usuario["nick"])) {
                if($this->Usuario_model->checkEmail($usuario["email"])) {
                    $_id=$this->Usuario_model->setData($usuario);
                    $this->Usuario_model->setActividad($_id);
                /*
                 * creamos los permisos de las actividades
                 */
                    //este método es solo para la DB porqueno soporta trigers al insertar tonces ahí se genera el error
                    //$this->Usuario_model->setActividad($_id);
                /*
                 * creamos relaciones de sus tweets
                 */
                    $this->tweet_model->followSetTweet($_id,$_id);
                    $cuerpo="Hola <b>".$usuario["nick"]."</b><br/> ya casi completaste el registro, ahora al momento de logearte por primera vez el sistema te pedira el códgo de seguridad enviando descrito abajo";
                    $cuerpo.="<br/><b>Code:</b> ".$usuario["code"]."<br/>mensaje generado automáticamente";

                    mail($usuario["email"],"easyprojects Registro",$cuerpo);
                    if($_id>0) {
                        $resultado=array("success"=>true,"msg"=>"La información se guardo correctamente en la base de datos Se le envio un mail a su correo electrónico");
                    }else {
                        $resultado=array("success"=>false,"msg"=>"Error en el almacenamiento");
                    }
                    echo json_encode($resultado);
                }else {

                    echo json_encode(array("success"=>false,"msg"=>"Se Dio un Error al momento de Guardar la información","errors"=>array("email"=>"el email ya esta registrado en la Base de Datos")));
                }
            }else {
                echo json_encode(array("success"=>false,"msg"=>"Se Dio un Error al momento de Guardar la información","errors"=>array("nick"=>"el usuario ya existe en la Base de Datos")));


            }
        }else {
            echo json_encode(array("success"=>false,"msg"=>"Se Dio un Error al momento de Guardar la información","errors"=>array("password"=>"Los Passwords no coinciden")));

        }
    }
    function email_check($str) {
        
        $this->load->model("Usuario_model");
        if($str=="")
            return false;
        if($this->session->userdata("us_email")==$str)
            return true;
        if(!$this->Usuario_model->checkEmail($str)) {
            $this->validation->set_message("email_check","El email %s ya esta en uso lo sentimos tendra que cambiarlo");

            return false;
        }else {
            return true;
        }
    }
    function nick_check($str) {
        $this->load->model("Usuario_model");
        if($str=="")
            return false;
        if($this->session->userdata("us_nick")==$str)
            return true;
        if(!$this->Usuario_model->checkNick($str)) {
            $this->validation->set_message("nick_check","El nick %s ya esta en uso lo sentimos tendra que cambiarlo");
            return false;
        }else {
            return true;
        }
    }
    /*
     * Perfil
     */
    function perfil() {
        if($this->session->userdata("us_c")==false) {
            redirect("");
            return;
        }
        $this->load->view("perfil/perfil");
    }
    function perfilInfo() {
        if($this->session->userdata("us_c")==false) {
            redirect("");
            return;
        }
        $user=$this->Usuario_model->getUser();
        echo json_encode(array("success"=>true,"data"=>$user));
    }
    function _data() {
        $fields["nombres"]="nombre ";
        $fields["apellidos"]="apellidos ";
        $fields["nick"]="nick ";
        $fields["pais"]="pais";
        $fields["email"]="email";
        $fields["password1"]="password1";
        $fields["password2"]="password2";
        $fields["icq"]="icq";
        $fields["msn"]="msn";
        $fields["gmail"]="gmail";
        $fields["web"]="web";
        $fields["facebook"]="facebook";
        $this->validation->set_fields($fields);
    }
    function perfil_validator() {

        $usuario=array(
            'nombre'=>$this->input->post("nombre"),
            'apellido'=>$this->input->post("apellido"),
            'nick'=>$this->input->post("nick"),
            'icq'=>$this->input->post('icq'),
            'msn'=>$this->input->post('msn'),
            'gmail'=>$this->input->post('gmail'),
            'web'=>$this->input->post('web'),
            'facebook'=>$this->input->post('facebook'),
            'pais'=>$this->input->post('pais'),
            'update_at'=>mdate("%d/%m/%y",time()),
            'email'=>$this->input->post('email')
        );


        //$this->Usuario_model->checkNick($usuario["nick"])
        if($this->session->userdata("us_nick")!=$this->input->post("nick")) {
            if(!$this->Usuario_model->checkNick($usuario["nick"])) {
                echo json_encode(array("success"=>false,"msg"=>"Se Dio un Error al momento de Guardar la información","errors"=>array("nick"=>"El nombre de usuario ya esta registrado en la base de datos")));
                return;
            }
        }
        if($this->session->userdata("us_email")!=$this->input->post("email")) {
            if(!$this->Usuario_model->checkEmail($usuario["email"])) {
                echo json_encode(array("success"=>false,"msg"=>"se dio un error al momento de actualizar los datos","errors"=>array("email"=>"Este email le pertenece a otro usuario")));
                return;
            }
        }
        $res=$this->Usuario_model->upDateUser($usuario);
        if($res) {
            echo json_encode(array("success"=>true,"msg"=>"Los datos se actualziarón correctamente"));
        }
    }
    function popupwindows() {
        $users=$this->Usuario_model->getUser();
        $resultados["user"]=$users;
        $resultados["error"]="";

        $this->load->view("perfil/windowpopup",$resultados);
    }
    /*
     * Upload avatar
     */
    function Upload() {
        $this->interface_menu["title"]="Actualiza los datos de tu perfil";
        $this->_data();
        $this->interface_menu["user"]=$this->Usuario_model->getUser();
        $config['upload_path'] ='./uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '300';
        $config['max_width'] = '2048';
        $config['max_height'] = '1468';
        $this->load->library('upload', $config);
        $this->interface_menu["error"]="";
        if ( ! $this->upload->do_upload()) {
            $this->interface_menu["error"] = $this->upload->display_errors();

            $this->load->view('perfil/windowpopup',$this->interface_menu);

        }
        else {
            $this->interface_menu["data"] =$this->upload->data();
            $url=$this->Usuario_model->updateAvatar($this->interface_menu["data"]["file_name"]);
            $this->interface_menu["url"]=$url;
            //echo base_url()."uploads/".$url;
            sleep(1);
            $this->_resize("./uploads/".$url,100,"./uploads/".$url);

            $this->load->view('perfil/windowpopup', $this->interface_menu);
        }
    }
    function _toksplit($string, $tokens) {

        $val = array();
        $v = strtok($string, $tokens);
        do $val[] = $v;
        while($v = strtok($tokens));
        return $val;
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

    function sendinvite() {

    }
    function dashboard() {
        $this->load->view("/usuario/dashboard",$this->interface_menu);
    }

}
?>
