<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Repositorio extends CI_Controller {
    var $interface_menu;
    var $absoulte="C:/tesis/projects";
    function repositorio() {
        parent::__construct();
        $this->load->model("Usuario_model");
        $this->load->model("Proyecto_model");
        $this->load->model("repositorio_model");
        $this->load->model("proyecto_model");
        $this->load->model("wall_model");
        $this->load->model("equipo_model");
        $this->load->model("route");
        $this->load->model("roles_model");
    }
    /*
     *
     * MODIFICAR ESTA RUTA CUANDO EL SISTEMA SE INSTALE
     */
    function createRepositorio() {
        $name=$this->input->post("_rep_n");
        /*
         * rutas rescatadas del servidor
         */

        $r=$this->route->getConfigs();
        $virtual=$r->row()->virtual_url;
        $absoulte=$r->row()->project_url;
        $data=array(
            "nombre"=>$name,
            "idPro"=>$this->session->userdata("us_idPro"),
            "ruta"=>$virtual.$name,
            "rutaabsoluta"=>$absoulte.$name,
            "create_at"=>mdate("%y/%m/%d",time()),
            "update_at"=>mdate("%y/%m/%d",time())
        );
        $id=$this->repositorio_model->insert($data);
        /*
         * Esta session Sirve para hacer la copia de archivos en el repositorio principal
         */
        if($this->session->userdata("us_Rep")=="") {
        //foreach($row->result() as $r) {
            $this->session->set_userdata("us_Rep",$id);
        //}
        }
        if(!is_dir($absoulte.$name."/trunk")) {
            mkdir($absoulte.$name, 0777);
            mkdir($absoulte.$name."/trunk", 0777);
            mkdir($absoulte.$name."/branches", 0777);
            mkdir($absoulte.$name."/tags", 0777);
        }
        $message="Tu repositorio Actual es modeltras en la dirección";
        $message.=" ".$virtual.$name." Si deseas hacer una reedición puedes entrar a está dirección ";
        $message.="Editar Repositorio ";
        $message.="Esta opción es poco recomendable por el riesgo que conlleva cuando el proyecto tiene avances importantes.</br>";
        $message.="Puede entrar al DashBoard de programación por ente enlacé DashBoard";
        $repositorio=$this->repositorio_model->getRepositorio("idPro",null);
        $res=$repositorio->row();
        echo json_encode(array("success"=>true,"message"=>$message,"newrep"=>$res->rutaabsoluta,"idsession"=>$this->session->userdata("us_Rep")));
    }
    function version() {
        if($this->session->userdata("us_c")==TRUE) {
            $this->interface_menu["loginControl"]=$this->menu->getControlLogin(true,$this->Usuario_model->getRoles(),$this->Usuario_model->getUser());
        }else {
            $this->interface_menu["loginControl"]=$this->menu->getControlLogin(false,'','');
        }
        $this->load->model("proyecto_model");
        $this->validation->set_message("min_length","El nombre no es valido porque es muy corto");
        $this->interface_menu["proyecto"]=$this->proyecto_model->getProyecto($this->session->userdata("us_idPro"));
        $this->interface_menu["repositorio"]=$this->repositorio_model->getRepositorio("idPro",null);

        $name=$this->input->post("_rep_n");
        /*
         * validación básica por seguridad
         */
        $reglas["_rep_n"]="trim|requiered|min_length[5]|max_length[20]|xss_clean";
        $this->validation->set_rules($reglas);
        $fields["_rep_n"]="nombre del repositorio";
        /*
         * Ruta absoluta de los archivos
         *
         */

        $this->validation->set_fields($fields);
        if($this->validation->run()==false) {
            $this->load->view("/repositorio/version",$this->interface_menu);
        }else {
            /*$data=array(
                "nombre"=>$name,
                "idPro"=>$this->session->userdata("us_idPro"),
                "ruta"=>"http://localhost:8181/projects/".$name,
                "rutaabsoluta"=>$absoulte.$name,
                "create_at"=>mdate("%y/%m/%d",time()),
                "update_at"=>mdate("%y/%m/%d",time())
            );*/
        //$row=$this->repositorio_model->insert($data);
            /*
             * creamos la session
             */
            /*if($this->session->userdata("us_idRep")=="") {
                foreach($row->result() as $r) {
                    $this->session->set_userdata("us_idRep",$r->id);
                }
            }*/

            /*
             * creamos el folder contenedor
             */
            /*if(!is_dir($absoulte.$name."/trunk")) {
                mkdir($absoulte.$name, 0777);
                mkdir($absoulte.$name."/trunk", 0777);
                mkdir($absoulte.$name."/branches", 0777);
                mkdir($absoulte.$name."/tags", 0777);
            }*/
        //redirect("/repositorio/version/");
        //$this->load->view("/repositorio/version",$this->interface_menu);
        }
    //$this->load->view("/repositorio/version",$this->interface_menu);
    }
    function checkname() {
        $cad=$this->input->post("cad");
        //verificamos el proyecto
        $ruta="/projects/".$cad."/";
        $obj=$this->repositorio_model->getRepositorio("nombre",$cad);
        if($obj->num_rows()>0) {
            echo json_encode(array("r"=>false,"result"=>"Tienes q cambiar el nombre, ya que existe un repositorio con ese nombre"));
            return false;
        }
        $direccion=base_url()."projects/".$cad."/";
        echo json_encode(array("r"=>true,"result"=>$cad."<br/>El nombre Es Correcto <br/> La ruta absoluta del proyecto es en  <a href='".$direccion."'>".$direccion."</a>"));
    }
    function createCopy() {
        $source=$this->input->post("source");
        $destino=$this->input->post("dest");
        $frameworks=$this->route->getConfigs();
        $frames=$frameworks->result_array();

        $us_rep=$this->repositorio_model->getRepositorio("idPro",$this->session->userdata("us_idPro"));
        foreach($us_rep->result() as $f) {
            $id=$f->id;
        }
        $r=$this->_smartCopy($frames[0][$source],$destino."/trunk",0755,0644,null,$id);
        echo json_encode(array("result"=>$r));
    }
    function _smartCopy($source, $dest, $folderPermission=0755,$filePermission=0644,$where=null,$idPak=null) {
    # source=file & dest=dir => copy file from source-dir to dest-dir
    # source=file & dest=file / not there yet => copy file from source-dir to dest and overwrite a file there, if present

    # source=dir & dest=dir => copy all content from source to dir
    # source=dir & dest not there yet => copy all content from source to a, yet to be created, dest-dir

        $result=false;

        if (is_file($source)) { # $source is file
            if(is_dir($dest)) { # $dest is folder
                if ($dest[strlen($dest)-1]!='/') # add '/' if necessary
                    $__dest=$dest."/";
                $__dest .= basename($source);
            }
            else { # $dest is (new) filename
                $__dest=$dest;
            }
            $ext=split(".",$__dest);
            $extension=$ext[count($ext)-1];
            $ruta=split("/",$__dest);
            $strruta="";
            for($i=0;$i<count($ruta)-1;$i++) {
                $strruta.=$ruta[$i]."/";
            }
            $name=$ruta[count($ruta)-1];
            if(!$where["files"]) {
            //echo"entra ".$this->session->userdata("us_Rep");

                /*$data=array(
                    "idPak"=>$idPak,
                    "nombre"=>$name,
                    "ruta"=>$strruta,
                    "descripcion"=>"default FrameWork",
                    "create_at"=>mdate("%y/%m/%d",time()),
                    "update_at"=>mdate("%y/%m/%d",time()),
                    "estado"=>"activo"

                );
                $this->repositorio_model->insertFiles($data);*/
            }elseif($where["files"]) {
                /*$data=array(
                    "idUs"=>$where["idUs"],
                    "idEq"=>$where["idEq"],
                    "nombre"=>$name,
                    "ruta"=>$strruta,
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>date("H:i:s"),
                    "estado"=>"activo"
                );
                $this->repositorio_model->insertFilesRecovery($data);*/
            }
            $result=copy($source, $__dest);
            chmod($__dest,$filePermission);
        }
        elseif(is_dir($source)) { # $source is dir
            if(!is_dir($dest)) { # dest-dir not there yet, create it
                @mkdir($dest,$folderPermission);
                chmod($dest,$folderPermission);
                $ruta=split("/",$dest);
                $strruta="";
                for($i=0;$i<count($ruta)-1;$i++) {
                    $strruta.=$ruta[$i]."/";
                }
                if(!$where["files"]) {
                //echo $ruta[(count($ruta)-1)]." <-carpetas <br/>";
                    $data=array(
                        "idPak"=>$idPak,
                        "nombre"=>$ruta[(count($ruta)-1)],
                        "ruta"=>$strruta,
                        "descripcion"=>"default FrameWork",
                        "create_at"=>mdate("%y/%m/%d",time()),
                        "update_at"=>mdate("%y/%m/%d",time()),
                        "estado"=>"activo"
                    );
                    $this->repositorio_model->insertFiles($data);
                }elseif($where["files"]) {
                    $data=array(
                        "idUs"=>$where["idUs"],
                        "idEq"=>$where["idEq"],
                        "nombre"=>$ruta[(count($ruta)-1)],
                        "ruta"=>$strruta,
                        "fecha"=>mdate("%y/%m/%d",time()),
                        "hora"=>date("H:i:s"),
                        "estado"=>"activo"
                    );
                    $this->repositorio_model->insertFilesRecovery($data);
                }
            }
            if ($source[strlen($source)-1]!='/') # add '/' if necessary
                $source=$source."/";
            if ($dest[strlen($dest)-1]!='/') # add '/' if necessary
                $dest=$dest."/";

            # find all elements in $source
            $result = true; # in case this dir is empty it would otherwise return false
            $dirHandle=opendir($source);
            while($file=readdir($dirHandle)) { # note that $file can also be a folder
                if($file!="." && $file!="..") { # filter starting elements and pass the rest to this function again
                #                echo "$source$file ||| $dest$file<br />\n";
                    if($where["files"])
                        $result=$this->_smartCopy($source.$file, $dest.$file, $folderPermission, $filePermission,$where,null);
                    else
                        $result=$this->_smartCopy($source.$file, $dest.$file, $folderPermission, $filePermission,null,$idPak);
                }
            }
            closedir($dirHandle);
        }
        else {
            $result=false;
        }
        return $result;
    }
    function workspace($id) {
        $view["id"]=$id;
        $this->load->view("repositorio/workspace",$view);
    }
    /*
     * Checkworkspace verifica el espacio de trabajo en base al equipo y el id del usuario
     */
    function checkworkspace() {
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        $repositorio=$this->repositorio_model->getWorkSpace($idUs,$idEq);
        echo json_encode(array("result"=>$repositorio->num_rows(),"data"=>$repositorio->result_array()));
    }
    function getWorkspace() {
        $repositorio=$this->repositorio_model->getWorkSpace($this->session->userdata("us_id"),$this->session->userdata("idEq"));
        echo json_encode(array("result"=>$repositorio->num_rows(),"data"=>$repositorio->result_array()));
    }
    /*
     * crea el espacio de trabajo
     */
    function createWorkSpace() {
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        $nick=$this->input->post("nick");
        $fecha=mdate("%y/%m/%d",time());

        $result=$this->repositorio_model->getRepositorio("idPro",$this->session->userdata("us_idPro"));
        //echo $idEq;
        $t=$this->equipo_model->getTeam($idEq);
        $team=explode(" ",$t->row()->nombre);
        $teamstring="";
        //ECHO "entra  a ".$t->row()->nombre;
        //return;
        for($i=0;$i<count($team);$i++) {
            $teamstring.=$team[$i];
        }
        if(count($team)==0)
            $teamstring=$t->row()->nombre;

        $data=$result->result_array();
        $rutaBase=$data[0]["rutaabsoluta"]."/trunk/";
        $path=$data[0]["ruta"]."/branches/".$teamstring."/".$nick."/";
        $rutanueva=$data[0]["rutaabsoluta"]."/branches/".$teamstring."/".$nick."/";
        if(!is_dir($data[0]["rutaabsoluta"]."/branches/".$teamstring."/")) {
            mkdir($data[0]["rutaabsoluta"]."/branches/".$teamstring."/", 0777);
        }
        if(!is_dir($rutanueva)) {
            mkdir($rutanueva, 0777);
        }
        //0755,$filePermission=0644
        $paremeters=array(
            "files"=>true,
            "idUs"=>$idUs,
            "idEq"=>$idEq
        );
        $information=array(
            "idUs"=>$idUs,
            "idEq"=>$idEq,
            "rutaabs"=>$rutanueva,
            "path"=>$path,
            "fecha"=>$fecha,
            "permit"=>"private"
        );

        $rr=$result=$this->repositorio_model->insertWorkSpace($information);
        $r=$this->_smartCopy($rutaBase,$rutanueva,0755,0644,$paremeters,null);
        if($r) {

            echo json_encode(array("result"=>$rr));
            return 1;
        }
        echo json_encode(array("result"=>false));
    }
    /*
     * Bajar Soruces
     * esta libreria fue creada por
     */
    function downloadZip() {
        $this->load->library('zip');
        $this->load->library('MY_Zip');
        $result=$this->repositorio_model->getWorkSpace($this->session->userdata("us_id"),$this->session->userdata("idEq"));
        $path = $result->row()->rutaabs;
        $folder_in_zip = "";
        $this->zip->get_files_from_folder($path, $folder_in_zip);
        $this->zip->download('mi_repositorio.zip');

    }
    /*Publish changes
     * a partir de aqui para bajo se adjuntan los métodos de control
     * para publicar
     * los cambios efectuados en la pared
     *
     */
    /*
     * llamamos a publicar cargamos la vista
     * situada en la URl decrita
     */
    function publishChanges () {
        $this->load->view("codeeditor/publish/public");
    }
    /*
     * Llamamos a los archivos que tienen el cambio en su estructura
     * esta llamada se hace en base
     * al Id del equipo
     * id del usuario
     * e cambios en el atributo estado de la base de datos
     *
     */
    function getChanges($parse) {
        $start = ($_POST['start']); //posición a iniciar
        $limit = ($_POST['limit']);
        if($parse!="s") {
            $data=explode("-",$parse);
            $resultado=$this->repositorio_model->getChanges($data[0],$data[1],$start,$limit);
            $result=array();
        }else {

            $resultado=$this->repositorio_model->getChanges($this->session->userdata("us_id"),$this->session->userdata("idEq"),$start,$limit);
            $result=array();
        }

        foreach($resultado->result() as $lista) {
            $result[]=array(
                "id"=>$lista->id,
                "idUs"=>$lista->idUs,
                "idEq"=>"<b>".$lista->idEq,
                "nombre"=>$lista->nombre,
                "ruta"=>$lista->ruta,
                "fecha"=>$lista->fecha,
                "hora"=>$lista->hora,
                "estado"=>$lista->estado,
                "updatefecha"=>$lista->updatefecha);
        }

        $data=array(
            "success"=>true,
            "totalCount"=>$this->repositorio_model->getChangesCount($this->session->userdata("us_id"),$this->session->userdata("idEq")),
            "rows"=>$result
        );
        echo json_encode($data);
    }
    function getLog() {
        $start = ($_POST['start']); //posición a iniciar
        $limit = ($_POST['limit']);
        $id=($_POST['id']);
        $resultado=$this->repositorio_model->getLog($id,$start,$limit);
        $result=array();
        foreach($resultado->result() as $lista) {
            $result[]=array(
                "id"=>$lista->id,
                "logText"=>$lista->logText,
                "fecha"=>$lista->fecha,
                "hora"=>$lista->hupdate
            );
        }
        $data=array(
            "success"=>true,
            "totalCount"=>$this->repositorio_model->getLogCount($id),
            "rows"=>$result
        );
        echo json_encode($data);
    }
    /*----------------------------------------------------------------
     * De aqui en adelante estan las funciones encargadas de hacer las
     * cpìas al repositorio
     * ---------------------------------------------------------------
     *
     * llamda al arbol de cambios del archivo
     *
     */
    function getUserDirectory($params) {
        $data=explode("-",$params);
        if($data[3]=="User") {

            $result=$this->repositorio_model->getWorkSpace($data[0],$data[1]);
            foreach($result->result() as $row) {
                $root=$row->rutaabs;

            }
            /*
             * sacamos los cambios del repositorio
             */
            $changes=$this->repositorio_model->getChanges($data[0],$data[1]);
            $changesArray=$changes->result_array();
        //echo json_encode($changesArray);
        }else if($data[3]=="System") {
                $result=$this->repositorio_model->getRepositorio('idPro',$this->session->userdata("idPro"));
                foreach($result->result() as $row) {
                    $root=$row->rutaabsoluta."/";

                }
                /*
                 * verificamos si queremos mostrar los archivos
                 * especificando su cambio
                 */

                if(isset($data[4])) {
                    if($data[4]=="viewchanges") {
                        $filesSystem=$this->repositorio_model->getChangesSystem();
                        $changesArray=$filesSystem->result_array();
                    }

                }
            }else if($data[3]=="session") {
                    $result=$this->repositorio_model->getWorkSpace($this->session->userdata("us_id"),$this->session->userdata("idEq"));
                    foreach($result->result() as $row) {
                        $root=$row->rutaabs;

                    }
                }

        $node = isset($_REQUEST['node'])?$_REQUEST['node']:""; //step 2
        if(strpos($node, '..') !== false) {
            die('Nice try buddy.');
        }
        $nodes = array();
        $d = dir($root.$node);      //step 1
        while($f = $d->read()) {      //step 2
            if($f == '.' || $f == '..' || substr($f, 0, 1) == '.') continue;
            if(is_dir($root.$node.'/'.$f)) {         //step 4
                array_push($nodes,array('text'=>$f, 'id'=>$node.'/'.$f));
            }else {
                if($data[3]=="User"||(ISSET($data[4])&&$data[4]=="viewchanges")) {
                //$changesArray
                    $_labelVar=false;
                    for($i=0;$i<count($changesArray);$i++) {
                        $url="";
                        //echo"".$node;
                        if($node==".") {
                            $url=$root.$f;

                        }
                        else {
                            if((ISSET($data[4])&&$data[4]=="viewchanges")) {
                                $aux=substr($node,1,strlen($node));
                                $url=$root.$aux."/".$f;
                            }else {
                                $aux=substr($node,2,strlen($node));
                                $url=$root.$aux."/".$f;
                            }
                        }
                        //echo $url."<br/>";
                        //echo $root.$aux."/".$f."  ".$changesArray[$i]["ruta"].$changesArray[$i]["nombre"]."<br/>";
                        //echo"->".$url." ".$changesArray[$i]["ruta"].$changesArray[$i]["nombre"]."<BR/>";
                        if($url==$changesArray[$i]["ruta"].$changesArray[$i]["nombre"]) {

                            $_labelVar=true;
                            array_push($nodes, array('text'=>$f, 'id'=>$node.'/'.$f, 'leaf'=>true,'iconCls'=>$this->_getChangedIcons($f)));
                            continue;
                        }
                    }
                    if(!$_labelVar) {
                        array_push($nodes, array('text'=>$f, 'id'=>$node.'/'.$f, 'leaf'=>true,'iconCls'=>$this->_getIcon($f)));
                    }
                }else {
                    array_push($nodes, array('text'=>$f, 'id'=>$node.'/'.$f, 'leaf'=>true,'iconCls'=>$this->_getIcon($f)));
                }

            }
        }
        $d->close();
        echo json_encode($nodes);
    }
    function _getChangedIcons($name) {
        if (preg_match("/\.png$/", $name) || preg_match("/\.jpg$/", $name) || preg_match("/\.gif$/", $name)) {
            return 'newjpg-icon';
        }else if(preg_match("/\.php$/", $name)) {
                return'newphp-icon';
            }else if(preg_match("/\.css$/", $name)) {
                    return 'newcss-icon';
                }else if(preg_match("/\.html$/", $name)) {
                        return 'newhtml-icon';
                    }else {
                        return 'newunknow-icon';
                    }
    }
    function _getIcon($name) {
        if (preg_match("/\.png$/", $name) || preg_match("/\.jpg$/", $name) || preg_match("/\.gif$/", $name)) {
            return 'jpg-icon';
        }else if (preg_match("/\.xls$/", $name) || preg_match("/\.xlsx$/", $name)) {
                return 'xls-icon';
            }else if (preg_match("/\.ppt$/", $name) || preg_match("/\.pptx$/", $name)) {
                    return 'ppt-icon';
                }else if (preg_match("/\.doc$/", $name) || preg_match("/\.docx$/", $name)) {
                        return 'doc-icon';
                    }else if(preg_match("/\.php$/", $name)) {
                            return'php-icon';
                        }else if(preg_match("/\.css$/", $name)) {
                                return 'css-icon';
                            }else if(preg_match("/\.html$/", $name)) {
                                    return 'html-icon';
                                }else {
                                    return 'unknow-icon';
                                }
    }

    /*
     * Funciones de copia
     * Copiamos un solo archivo defino de aqui adelante
     *se ejecuta cuando se copia un archivo fisicamente puede remplazar
     * a uno del repositorio además se crea el log del archivo e la base de datos
     *  a la base de datos
     *
     */

    function copyFile() {
        $ruta=$this->input->post("relative");
        $name=$this->input->post("name");
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        $rep=$this->repositorio_model->getRepositorio("idPro",null);
        $abs="";
        foreach($rep->result() as $r) {
            $abs=$r->rutaabsoluta;
            $idRep=$r->id;
        }
        $path=$abs."/trunk/".$ruta;
        //echo $path;
        $subruta=substr($path,0,(strlen($path)-strlen($name)));
        $workspace=$this->repositorio_model->checkExistArchivos($subruta,$name);
        if($workspace["success"]) {
            echo json_encode(array("success"=>false,"result"=>"desea remplazar el archivo ".$ruta,"path"=>$path));
            return;
        }else {
            $result=$this->repositorio_model->getWorkSpace($idUs,$idEq);
            $absrute=$result->row();
            $ruta=$absrute->rutaabs.$ruta;
            /*
             * hacemos insercion
             */
            $data=array(
                "idPak"=>$idRep,
                "nombre"=>$name,
                "ruta"=>$subruta,
                "descripcion"=>"default FrameWork",
                "create_at"=>mdate("%y/%m/%d",time()),
                "update_at"=>mdate("%y/%m/%d",time()),
                "estado"=>"new"
            );
            //echo $name." ".$ruta;
            $subroute=substr($ruta,0,(strlen($ruta)-strlen($name)));
            $infoFiles=$this->repositorio_model->updateFilesCopy($name,$subroute,array("estado"=>"activo","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
            $changes=$this->repositorio_model->getChanges($idUs,$idEq);
            $this->repositorio_model->insertFiles($data);

            $rr=$this->_remplaceFile($ruta,$path);
            $this->repositorio_model->insertRel_lo_ar(array("idFi"=>$infoFiles[0]["id"],"idAr"=>$workspace["row"]["id"],"fecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));

            if($rr) {
                if($changes->num_rows()>0) {
                    echo json_encode(array("success"=>true));
                }else {
                    echo json_encode(array("success"=>true,"result"=>"cambios terminados"));
                }

            }else {
                echo json_encode(array("success"=>false));
            }
        }

    }
    function remplace() {
        $ruta=$this->input->post("relative");
        $name=$this->input->post("name");
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        $path=$this->input->post("path");
        $result=$this->repositorio_model->getWorkSpace($idUs,$idEq);
        $absrute=$result->row();
        $ruta=$absrute->rutaabs.$ruta;
        $subroute=substr($ruta,0,(strlen($ruta)-strlen($name)));

        $pathsub=substr($path,0,(strlen($path)-strlen($name)));

        $workspace=$this->repositorio_model->checkExistArchivos($pathsub,$name);
        $infoFiles=$this->repositorio_model->updateFilesCopy($name,$subroute,array("estado"=>"activo","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        /*
         * hacemos un update a la base de datos
         */
        $subpath=substr($path,0,(strlen($path)-strlen($name)));
        $this->repositorio_model->updateArchivos($name,$subpath,array("estado"=>"update","update_at"=>mdate("%y/%m/%d",time())));
        $rr=$this->_remplaceFile($ruta,$path);
        $changes=$this->repositorio_model->getChanges($idUs,$idEq);
        $this->repositorio_model->insertRel_lo_ar(array("idFi"=>$infoFiles[0]["id"],"idAr"=>$workspace["row"]["id"],"fecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        if($rr) {

            if($changes->num_rows()>0) {
                echo json_encode(array("success"=>true));
            }else {
                /*
                 * hacemos una peticion entregando a todos los usuarios que cuentan con un respositorio
                 *con esta llamada
                 * contiene el id del usuario que cuenta con el repositorio
                 *
                 */
                $list=$this->repositorio_model->getRepositoriosModel($idUs);
                $version=$this->repositorio_model->getLastBranches();
                if($version->num_rows()==0) {
                    $__v="";
                }else {
                    $__v=$version->row()->version;
                }
                echo json_encode(array("success"=>true,"result"=>"cambios terminados","list"=>$list->result(),"version"=>$__v));
            }

        }else {
            echo json_encode(array("success"=>false));
        }
    }
    /*
     * remplza el archivo
     */
    function _remplaceFile($r1,$r2) {

        $file=fopen($r1,"r+");
        $data=fread($file,filesize($r1));
        $here=fopen($r2,"w+");
        if($here) {
            fwrite($here,$data);
            fclose($here);
            fclose($file);
            //echo json_encode(array("success"=>true,"data"=>".".$r2));
            return true;
        }
        return false;
    }
	function getFilesData(){
        $ruta=$this->input->post("ruta");
        $file=$this->input->post("nombre");
        $row=$this->repositorio_model->getFilesCopy($ruta,$file);
        if($row->num_rows()==0){
            echo json_encode(array("success"=>false));
            return;
        }
        echo json_encode(array("success"=>true,"id"=>$row->row()->id));

    }
    /*
     * Copiar archivo del Systema principal a tu repositorio
     */
    function copySystemFile() {
    //$route=$this->input->post("abspath");//c:...
        $name=$this->input->post("name");//solo el nombre y extensión
        $relative=$this->input->post("relative");//ruta relativa a partir del trunk trunk/...
        $absoluteUserPath=$this->input->post("userpath");// sin / al final


        $virtualPath=$absoluteUserPath.$relative.$name;

        if(is_file($virtualPath)) {
            echo json_encode(array("success"=>false,"message"=>"¿El archivo Existe, desea remmplazarlo?"));
            return;
        }else {
            /*
         * sacamos información del repositorio del usuario
         * workspace
         */
            $r=$this->repositorio_model->getRepositorio('idPro',$this->session->userdata("us_idPro"));
            $route=$r->row()->rutaabsoluta;
            $this->_remplaceFile($route."/trunk".$relative.$name,$virtualPath);
            //mdate("%y/%m/%d",time())." ".date("H:i:s")
            $newFile=array(
                "idUs"=>$this->session->userdata("us_id"),
                "idEq"=>$this->session->userdata("idEq"),
                "nombre"=>$name,
                "ruta"=>$absoluteUserPath.$relative,
                "fecha"=>mdate("%y/%m/%d"),
                "hora"=>date("H:i:s"),
                "estado"=>"new",
                "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
            );

            $this->repositorio_model->insertFilesRecovery($newFile);
            //$this->repositorio_model->updateFilesCopy($relative.$name,$absoluteUserPath,array("estado"=>"activo"));
            echo json_encode(array("success"=>true,"message"=>"¿La copia se realizo con éxito?"));
            return;

        }
    }
    function remplaceSystemFile() {
        $name=$this->input->post("name");//solo el nombre y extensión
        $relative=$this->input->post("relative");//ruta relativa a partir del trunk trunk/...
        $absoluteUserPath=$this->input->post("userpath");
        $virtualPath=$absoluteUserPath.$relative.$name;
        $r=$this->repositorio_model->getRepositorio('idPro',$this->session->userdata("us_idPro"));
        $route=$r->row()->rutaabsoluta;
        $this->_remplaceFile($route."/trunk".$relative.$name,$virtualPath);
        $this->repositorio_model->updateFilesCopy($relative.$name,$absoluteUserPath,array("estado"=>"activo"));

        echo json_encode(array("success"=>true,"message"=>"Cambio Relaizado con éxito"));
    }
    /*
     *
     * remplaca todos los archivos del repositorio
     * ejecutando esta acción
     */
    function remplaceAllFilesSystem() {
        $absoluteUserPath=$this->input->post("userpath");
        $r=$this->repositorio_model->getRepositorio('idPro',$this->session->userdata("us_idPro"));
        $abs=$r->row()->rutaabsoluta;
        $indice=strlen($abs)+6;
        /*
         * me regresa los archivos del sistema cambiados
         * atodos los que se le hicierón un deleted o un updated
         */
        $filesSystem=$this->repositorio_model->getChangesSystem();
        $folders=array();

        foreach($filesSystem->result() as $fsystem) {
            $subpath=substr($fsystem->ruta,$indice,strlen($fsystem->ruta));
            if($fsystem->estado=="deletedforever") {
                if(is_file($absoluteUserPath.$subpath.$fsystem->nombre)) {
                    try {
                        unlink($absoluteUserPath.$subpath.$fsystem->nombre);
                    }catch(Error $e ) {

                    }
                }
                if(is_dir($absoluteUserPath.$subpath.$fsystem->nombre)) {
                    $folders[]=$absoluteUserPath.$subpath.$fsystem->nombre;
                }
                $this->repositorio_model->updateFilesCopy($subpath.$fsystem->nombre,$absoluteUserPath,array("estado"=>"activo"));
            }else {
            //echo $absoluteUserPath.$subpath.$fsystem->nombre."-- <br/>";
                if(is_dir($fsystem->ruta.$fsystem->nombre)) {
                //echo"entra --->> ";
                    if(!is_dir($absoluteUserPath.$subpath.$fsystem->nombre))
                        mkdir($absoluteUserPath.$subpath.$fsystem->nombre,0777);
                    $newFile=array(
                        "idUs"=>$this->session->userdata("us_id"),
                        "idEq"=>$this->session->userdata("idEq"),
                        "nombre"=>$fsystem->nombre,
                        "ruta"=>$absoluteUserPath.$subpath,
                        "fecha"=>mdate("%y/%m/%d"),
                        "hora"=>date("H:i:s"),
                        "estado"=>"activo",
                        "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
                    );
                    $this->repositorio_model->insertFilesRecovery($newFile);
                }elseif(is_file($fsystem->ruta.$fsystem->nombre)) {
                    $this->_remplaceFile($fsystem->ruta.$fsystem->nombre,$absoluteUserPath.$subpath.$fsystem->nombre);
                    $newFile=array(
                        "idUs"=>$this->session->userdata("us_id"),
                        "idEq"=>$this->session->userdata("idEq"),
                        "nombre"=>$fsystem->nombre,
                        "ruta"=>$absoluteUserPath.$subpath,
                        "fecha"=>mdate("%y/%m/%d"),
                        "hora"=>date("H:i:s"),
                        "estado"=>"activo",
                        "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
                    );

                    $this->repositorio_model->insertFilesRecovery($newFile);
                }else {
                    $this->_remplaceFile($fsystem->ruta.$fsystem->nombre,$absoluteUserPath.$subpath.$fsystem->nombre);
                    $this->repositorio_model->updateFilesCopy($subpath.$fsystem->nombre,$absoluteUserPath,array("estado"=>"activo"));

                }

            }
        }
        for($i=(count($folders)-1);$i>=0;$i--){
            rmdir($folders[$i]);
        }
        echo json_encode(array("success"=>true,"message"=>"Se ha Actualizado su repositorio"));
    }

    /*
     * ------------------------------------------------------------
     * publica las notificaciones cierra el repositorio del usuario
     * que envio los cambios y publica la informacion a la pared
     * llena la carpeta de trunk con zip backups del sistema
     * antes copiaba en la carpeta branches los zip pero lo tiene q hacer en
     * tags y en branches se hace el desarrollo paralelo que tiene el trunk
     * -------------------------------------------------------------
     */
    function finishUpdate() {
         /*
          * Parametros
          */
        $infoIds=$this->input->post("ids");
        $infoidEq=$this->input->post("idEqs");
        $infoNick=$this->input->post("nicks");
        $msn=$this->input->post("msn");
        $version=$this->input->post("version");
        $idchanges=$this->input->post("idChanges");
        $idsArr=explode("-",$infoIds);
        $idEqsArr=explode("-",$infoidEq);
        $nicks=explode("-",$infoNick);


        $idUs=$this->input->post("id");
        $idEq=$this->input->post("idEq");
         /*
          * Mensaje
          *
          */
        if(strlen($this->input->post("ids"))>0) {
            if(count($idsArr)>0) {
                for($i=0;$i<count($idsArr);$i++) {
                    $data=array(
                        "message"=>"Este mensaje se auto genero <br/>Los cambios propuestos fueron aceptados, necesitas actualizar los nuevos archivos <br/>".$msn,
                        "fecha"=>mdate("%y/%m/%d",time())." ".mdate("%h:%i:%a",time()),
                        "idPro"=>$this->session->userdata("us_idPro"),
                        "idUs"=>$idsArr[$i],
                        "idEq"=>$idEqsArr[$i],
                        "tipo"=>"update",
                        "params"=>"{idUs:'".$idUs."',idEq:'".$idEq."'}",
                        "read"=>false
                    );
             /*
              * insertamos en notificacion
              */
                    $this->repositorio_model->insertNotificacion($data);
                }
            }else {

                $data=array(
                    "message"=>"Este mensaje se auto genero <br/>Los cambios propuestos fueron aceptados, necesitas actualizar los nuevos archivos",
                    "fecha"=>mdate("%y/%m/%d",time())." ".mdate("%h:%i:%a",time()),
                    "idPro"=>$this->session->userdata("us_idPro"),
                    "idUs"=>$infoIds,
                    "idEq"=>$infoidEq,
                    "tipo"=>"update",
                    "params"=>"{idUs:'".$idUs."',idEq:'".$idEq."'}",
                    "read"=>false
                );
                //echo"enra";
                $this->repositorio_model->insertNotificacion($data);
            }
        }

         /*
          * hacemos el update
          * d3entro del workspace para cerrarlo al publico
          */
         /*
          * cerramos el update
          */
        $this->repositorio_model->updateWorkSpace(array("permit"=>"private"),"idUs='".$idUs."' and idEq='".$idEq."'");
         /*
          * publicamos en la pared
          */
        $info=array(
            "fecha"=>mdate("%y/%m/%d",time()),
            "hora"=>mdate("%h:%i:%a",time()),
            "titulo"=>"Cambio Aceptado",
            "cuerpo"=>"El cambio se ha aceptado con éxito, se envio una notificación a los usuarios que tienen un repositorio para que hagan un Update a los archivos",
            "idUs"=>$this->session->userdata("us_id"),
            "idPro"=>$this->session->userdata("us_idPro")
        );

        $id=$this->wall_model->insertnew($info);
        /*
         * creamos Zip
         * t lo ponemos en los branches
         */
        /*
         * cargamos libreria zip
         */
        $this->load->library('zip');
        $this->load->library('MY_Zip');
        $rep=$this->repositorio_model->getRepositorio("idPro",null);
        $abs="";
        foreach($rep->result() as $r) {
            $abs=$r->rutaabsoluta;
            $local=$r->ruta;
        }
        $names="";
        $split=explode(" ",$version);
        for($i=0;$i<count($split);$i++){
            $names.=$split[$i]."_";
        }

        $this->zip->get_files_from_folder($abs."/trunk/","");
        $this->zip->archive($abs."/tags/repositorio".$names.".zip");
        $branche=array(
            "url"=>$local."/tags/repositorio".$names.".zip",
            "idPro"=>$this->session->userdata("us_idPro"),
            "fecha"=>mdate("%y-%m-%d",time())." ".mdate("%h-%i-%a",time()),
            "idUsReview"=>$idUs,
            "idUsChange"=>$this->session->userdata("us_id"),
            "version"=>$version
        );
        /*
         * En realidad es Brances y no tags
         *
         */
        $this->repositorio_model->insertBranches($branche);
        /*
         * actualizamos el review
         * lastchanges
         * review
         * approved
         *rejected
         */
        $this->repositorio_model->updatelastchanges(array("estado"=>"approved"),"id='".$idchanges."'");
        echo json_encode(array("success"=>true,"message"=>"Los cambios se realizarón con éxito"));
    }
    function  comprimir(){
        $this->load->library('zip');
        $this->load->library('MY_Zip');
        $rep=$this->repositorio_model->getRepositorio("idPro",null);
        $abs="";
        foreach($rep->result() as $r) {
            $abs=$r->rutaabsoluta;
            $local=$r->ruta;
        }
        $this->zip->get_files_from_folder($abs."/trunk/","");
        $this->zip->archive($abs."/tags/rep_.zip");

    }
    function setAllChanges() {
        $idUs=$this->input->post("idUs");
        $idEq=$this->input->post("idEq");
        $resultado=$this->repositorio_model->getChanges($idUs,$idEq);
        $workspace=$this->repositorio_model->getWorkSpace($idUs,$idEq);
        $abs=$this->repositorio_model->getRepositorio("idPro",$this->session->userdata("us_idPro"));
        $absolutepath=$abs->result_array();
        $workdata=$workspace->result_array();
        $carpetas=array();
        foreach($resultado->result() as $files) {
            if($files->estado=="deleted") {
                /*
                 * caso especial para los archivos
                 * que son borrados
                 */
                if(count(explode(".",$files->nombre))>0) {
                    $workbasepath=$workdata[0]["rutaabs"];
                    $complete=$files->ruta.$files->nombre;
                    $subbase=substr($complete,strlen($workbasepath),strlen($complete));
                    //echo $absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase;
                    if(is_file($absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase)) {
                        try {
                            unlink($absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase);
                        }catch(Exception  $error) {

                        }
                    }
                    if(is_dir($absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase)) {
                        $carpetas[]=$absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase;

                    }
                    $infoFiles=$this->repositorio_model->updateFilesCopy($files->nombre,$files->ruta,array("estado"=>"deletedforever","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));

                    $completepath=$absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase;
                    $subpath=substr($completepath,0,(strlen($completepath)-strlen($files->nombre)));


                    //getFilesSystem
                    /*$row=$this->repositorio_model->getFilesSystem($subpath,$files->nombre);
                    if($row->num_rows()==0){

                    }*/
                    $c=$this->repositorio_model->updateArchivos($files->nombre,$subpath,array("estado"=>"deletedforever","update_at"=>mdate("%y/%m/%d",time())));

                    if($c==0) {
                        $us_rep=$this->repositorio_model->getRepositorio("idPro",$this->session->userdata("us_idPro"));
                        foreach($us_rep->result() as $f) {
                            $id=$f->id;
                        }
                        $data=array(
                            "idPak"=>$id,
                            "nombre"=>$files->nombre,
                            "ruta"=>$subpath,
                            "descripcion"=>"default FrameWork",
                            "create_at"=>mdate("%y/%m/%d",time()),
                            "update_at"=>mdate("%y/%m/%d",time()),
                            "estado"=>"deletedforever"

                        );
                        $this->repositorio_model->insertFiles($data);
                    }
                }

            }else {
                $complete=$files->ruta.$files->nombre;
                $workbasepath=$workdata[0]["rutaabs"];
                $subbase=substr($complete,strlen($workbasepath),strlen($complete));
                $completepath=$absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase;
                $subpath=substr($completepath,0,(strlen($completepath)-strlen($files->nombre)));
                if(count(explode(".",$files->nombre))>1) {
                    $this->_remplaceFile($files->ruta.$files->nombre,$absolutepath[0]["rutaabsoluta"]."/trunk/".$subbase);
                }else {
                    if(!is_dir($completepath)) {
                        mkdir($completepath,0777);
                    }
                }
                //$rr++;
                $c=$this->repositorio_model->updateArchivos($files->nombre,$subpath,array("estado"=>$files->estado,"update_at"=>mdate("%y/%m/%d",time())));
                /*
                 * no se actulizo porque no existe
                 */
                if($c==0) {
                    /*
                     * Insertamos
                     */
                    $us_rep=$this->repositorio_model->getRepositorio("idPro",$this->session->userdata("us_idPro"));
                    foreach($us_rep->result() as $f) {
                        $id=$f->id;
                    }
                    $data=array(
                        "idPak"=>$id,
                        "nombre"=>$files->nombre,
                        "ruta"=>$subpath,
                        "descripcion"=>"default FrameWork",
                        "create_at"=>mdate("%y/%m/%d",time()),
                        "update_at"=>mdate("%y/%m/%d",time()),
                        "estado"=>"new"

                    );
                    $this->repositorio_model->insertFiles($data);
                }
                $infoFiles=$this->repositorio_model->updateFilesCopy($files->nombre,$files->ruta,array("estado"=>"activo","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
            }
            $work=$this->repositorio_model->checkExistArchivos($subpath,$files->nombre);
            $information=$work["row"];
            //echo "->>> ".$information[0]["id"];
            $this->repositorio_model->insertRel_lo_ar(array("idFi"=>$infoFiles[0]["id"],"idAr"=>$information[0]["id"],"fecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        }
        /*
         * Borramos las carpetas
         */
        for($i=(count($carpetas)-1);$i>=0;$i--) {
            rmdir($carpetas[$i]);
        }
        $list=$this->repositorio_model->getRepositoriosModel($idUs);
        $version=$this->repositorio_model->getLastBranches();
        $__v="";
        if($version->num_rows()==0) {
            $__v="";
        }else {
            $__v=$version->row()->version;
        }
        echo json_encode(array("success"=>true,"message"=>"Se hizo el cambio correctamente","list"=>$list->result(),"version"=>$__v));
    }
    /*
     * El rechazo público
     *
     */
    function rejected() {
        $message=$this->input->post("messaje");
        $id=$this->input->post("id");
        $idchanges=$this->input->post("idChanges");
        $user=$this->Usuario_model->getUserById($id,"array");
        $info=array(
            "fecha"=>mdate("%y/%m/%d",time()),
            "hora"=>mdate("%h:%i:%a",time()),
            "titulo"=>$this->session->userdata("us_nick")." ha rezado el Cambio propuesto por ".$user[0]["nick"],
            "cuerpo"=>$this->session->userdata("us_nick")." ha rechado el Cambio propuesto por ".$user[0]["nick"]." <br/><b>Razones</b><br/> ".$message,
            "idUs"=>$this->session->userdata("us_id"),
            "idPro"=>$this->session->userdata("us_idPro")
        );
        $id=$this->wall_model->insertnew($info);
        $this->repositorio_model->updatelastchanges(array("estado"=>"rejected"),"id='".$idchanges."'");
        echo json_encode(array("success"=>true));
    }
    /*
     * Repositorio Versions módulo de versiones del proyecto
     *
     */
    function Versions() {
        if($this->session->userdata("us_id")!="") {
            $this->load->view("repositorio/versionssystem");
        }

    }
    function getBranchesList(){

        $r=$this->repositorio_model->getBranchesList();

        echo json_encode(array("success"=>true,"rows"=>$r->result()));
    }
}
?>