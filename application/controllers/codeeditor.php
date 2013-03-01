<?php
/*
 * CLASS CODE EDITOR
 * BY: DITMAR DAVID CASTRO ANGULO
 * FUNCIONES DE EDICION Y GESTION DE ARVHIVOS
 * COMUNICACION CON FLEX VIA AMF
 *
 */
class codeeditor extends CI_Controller {
    var $path="";
    var $idTree=0;
    var $mainpath="C:/wamp/www/blog/";
    /*
     * Templates to class in php
     */
    var $templateForPhp="<?php\n ?>";
    function codeeditor() {
        parent::__construct();
        $this->load->helper("directory");
        $this->load->model("repositorio_model");
    }
    /*
     * Cambiar nombre
     */
    function reNameFile() {
        $absoluteRute=$this->input->post("absoluterute");
        $file=$this->input->post("path");
        $name=$this->input->post("name");
        $padre=$this->input->post("fatherPath");
        /*
         * hacemos revisiones a los archivos para crear los logs
         * primer verificamos sui el archivo éxiste fisicamente en el sistema
         */
        $ruta=$absoluteRute;
        $archivo=explode("/",$file);
        if(count($archivo)>0)
            $file=$archivo[(count($archivo)-1)];
        for($i=0;$i<count($archivo)-1;$i++) {
            $ruta.=$archivo[$i]."/";
        }
        if(is_file($ruta.$file)) {
            $row=$this->repositorio_model->getFilesCopy($ruta,$file);
            if($row->num_rows()==0) {
                $data=array(
                    "idUs"=>$this->session->userdata("us_id"),
                    "idEq"=>$this->session->userdata("idEq"),
                    "nombre"=>$name,
                    "ruta"=>$ruta,
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>date("H:i:s"),
                    "estado"=>"newname"
                );
                $this->repositorio_model->insertFilesRecovery($data);
            }else {

                $this->repositorio_model->updateFilesCopy($this->input->post("path"),$this->input->post("absoluterute"),array("estado"=>"newname","nombre"=>$name,"updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));

            }
            rename($absoluteRute.$this->input->post("path"),$absoluteRute.$padre.$name);
            echo json_encode(array("success"=>true,"data"=>".".$padre));
        }

    }
    /*
     *Copia solo un archivo
     *
     */
    function copyTo() {
        $name=$this->input->post("name");
        $absoluteRute=$this->input->post("absolute");
        $fileInit=$this->input->post("source").$name;
        $fileDestiny=$this->input->post("dest");
        $ruta=$fileDestiny;
        $file=fopen($absoluteRute.$fileInit,"r+");
        $data=fread($file,filesize($absoluteRute.$fileInit));
        $datos=array(
            "idUs"=>$this->session->userdata("us_id"),
            "idEq"=>$this->session->userdata("idEq"),
            "nombre"=>$name,
            "ruta"=>$absoluteRute.$fileDestiny,
            "fecha"=>mdate("%y/%m/%d",time()),
            "hora"=>date("H:i:s"),
            "estado"=>"new"
        );
        $this->repositorio_model->insertFilesRecovery($datos);
        if(is_dir($absoluteRute.$fileDestiny)) {
            $here=fopen($absoluteRute.$fileDestiny.$name,"w+");
            if($here) {
                fwrite($here,$data);
                fclose($here);
                fclose($file);
                echo json_encode(array("success"=>true,"data"=>".".$fileDestiny));
                return;
            }
            echo json_encode(array("success"=>false,"data"=>"Error no se pusdo copiar el archivo"));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>"Error no se pusdo copiar el archivo"));
        return;
    }
    /*
     * NOMBRE: SAVEFILE
     * DESCRIPCION: Guarda el contenido en un archivo
     * parametros: absoluteRute: "ruta completa del archivo"
     *             code: "informacion que guarda dentro del archivo"
     * regresa falso o verdadero
     */
    function saveFile($absoluteRute,$container) {
        $file=fopen($this->mainpath.$absoluteRute,"w+");
        if($file) {
            fwrite($file,$container);
            return true;
        }
        return false;
    }
    /*
     * Borra un directorio
     * 
     */
    function _deleteDirectory($dir,$abs,$ruta) {

        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) {

            $_abs=$abs;
            $archivo=explode("/",$ruta);
            if(count($archivo)>0)
                $file=$archivo[(count($archivo)-1)];
            for($i=0;$i<count($archivo)-1;$i++) {
                $_abs.=$archivo[$i]."/";
            }
            //if(is_file($_abs.$file)) {
            $rows=$this->repositorio_model->getFilesCopy($_abs,$file);
            if($rows->num_rows()==0) {
                $data=array(
                    "idUs"=>$this->session->userdata("us_id"),
                    "idEq"=>$this->session->userdata("idEq"),
                    "nombre"=>$file,
                    "ruta"=>$_abs,
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>date("H:i:s"),
                    "estado"=>"deleted",

                "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
                );
                $this->repositorio_model->insertFilesRecovery($data);
            //}
            }else {
                $this->repositorio_model->updateFilesCopy($ruta,$abs,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
            }

            return unlink($dir);
        };
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->_deleteDirectory($dir . "/" . $item,$abs,$ruta."/".$item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->_deleteDirectory($dir . "/" . $item,$abs,$ruta."/".$item)) return false;
            };
        }

        $_abs=$abs;
        $archivo=explode("/",$ruta);
        if(count($archivo)>0)
            $file=$archivo[(count($archivo)-1)];
        for($i=0;$i<count($archivo)-1;$i++) {
            $_abs.=$archivo[$i]."/";
        }
        //if(is_file($_abs.$file)) {
        $rows=$this->repositorio_model->getFilesCopy($_abs,$file);
        if($rows->num_rows()==0) {
            $data=array(
                "idUs"=>$this->session->userdata("us_id"),
                "idEq"=>$this->session->userdata("idEq"),
                "nombre"=>$file,
                "ruta"=>$_abs,
                "fecha"=>mdate("%y/%m/%d",time()),
                "hora"=>date("H:i:s"),
                "estado"=>"deleted",
                "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
            );
            $this->repositorio_model->insertFilesRecovery($data);
        //}
        }else {
            $this->repositorio_model->updateFilesCopy($ruta,$abs,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        }
        //$this->repositorio_model->updateFilesCopy($ruta,$abs,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        return rmdir($dir);
    }
    /*
     * Nombre: Delete Folder
     * DESCRIOCIÖN Borra un directorio completo
     */
    function deleteFolder() {
        $absoluteRute=$this->input->post("absoluterute");
        $ruta=$this->input->post("ruta");
        //$nombre=$this->input->post("nombre");
        if(is_dir($absoluteRute.$ruta)) {
            if($this->_deleteDirectory($absoluteRute.$ruta,$absoluteRute,$ruta)) {

                echo json_encode(array("success"=>true,"data"=>".".$ruta));

                return;
            }
        }

        echo json_encode(array("success"=>false,"data"=>"No se pudo eliminar el directorio"));
        return;
    }
    /*
     * NOMBRE: DELETEFILE
     * DESCRIPCIÓN: Borra un archivo del servidor, o directorio
     * PARÁMETROS: Ruta: " ruta relativa del directorio"
     *             archivo:"nombre del archivo"
     * REGRESA: "El directorio modificado"
     */
    function deleteFile() {
        $absoluteRute=$this->input->post("absoluterute");
        $ruta=$this->input->post("ruta");

        $_abs=$absoluteRute;
        $archivo=explode("/",$ruta);
        if(count($archivo)>0)
            $file=$archivo[(count($archivo)-1)];
        for($i=0;$i<count($archivo)-1;$i++) {
            $_abs.=$archivo[$i]."/";
        }

        $rows=$this->repositorio_model->getFilesCopy($_abs,$file);
        if($rows->num_rows()==0) {
            $data=array(
                "idUs"=>$this->session->userdata("us_id"),
                "idEq"=>$this->session->userdata("idEq"),
                "nombre"=>$file,
                "ruta"=>$_abs,
                "fecha"=>mdate("%y/%m/%d",time()),
                "hora"=>date("H:i:s"),
                "estado"=>"deleted",
                "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
            );
            $this->repositorio_model->insertFilesRecovery($data);
        //}
        }else {
            $this->repositorio_model->updateFilesCopy($ruta,$absoluteRute,array("estado"=>"deleted","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
        }

        unlink($absoluteRute.$ruta);
        echo json_encode(array("success"=>true,"data"=>".".$ruta));
    }
    /*
     * NOMBRE CREATEFILE
     *  DESCRIPCIÓN: Crea un archivo nuevo en el servidor
     *  PARÁMETROS: Ruta: " ruta relativa del directorio" nombre: "nombre del archivo "
     *  REGRESA: "El directorio modificado"
     *
     */
    function createFile() {
        $absolutePath=$this->input->post("absolutepath");
        $ruta=$this->input->post("name_path");
        $nombre=$this->input->post("name_folder");
        if(!is_dir($absolutePath.$ruta.$nombre)) {
            $file=fopen($absolutePath.$ruta.$nombre,"w+");
            if($file) {
                $data=array(
                    "idUs"=>$this->session->userdata("us_id"),
                    "idEq"=>$this->session->userdata("idEq"),
                    "nombre"=>$nombre,
                    "ruta"=>$absolutePath.$ruta,
                    "fecha"=>mdate("%y/%m/%d",time()),
                    "hora"=>date("H:i:s"),
                    "estado"=>"new"
                );
                $this->repositorio_model->insertFilesRecovery($data);
                fwrite($file,$this->templateForPhp);
                fclose($file);
                echo json_encode(array("success"=>true,"data"=>".".$ruta));
                return;
            }
        }
        echo json_encode(array("success"=>false,"data"=>$nombre));
        return;
    }
    /*
     * NOMBRE CREATEDIRECTORY
     *  DESCRIPCIÓN: Crea una carpeta en el sevidor
     *  PARÁMETROS: Ruta: " ruta relativa del directorio" nombre: "nombre de la carpeta "
     *  OBSERVACION: crea un archivo de tipo oculto dentro de la carpeta para que sea correctamente visualizada
     *  cuando la información del directorio es importada desde flex en el componente tree
     *  REGRESA: "El directorio modificado"
     */
    function createDirectory() {
        /*
         * rescatamos los nuevos valores
         */
        $nombre=$this->input->post("name_folder");
        $ruta=$this->input->post("name_path");
        $absolute=$this->input->post("absolutepath");

        if(!is_dir($absolute.$ruta.$nombre)) {
            mkdir($absolute.$ruta.$nombre, 0777);
            $data=array(
                "idUs"=>$this->session->userdata("us_id"),
                "idEq"=>$this->session->userdata("idEq"),
                "nombre"=>$nombre,
                "ruta"=>$absolute.$ruta,
                "fecha"=>mdate("%y/%m/%d",time()),
                "hora"=>date("H:i:s"),
                "estado"=>"new"
            );
            $this->repositorio_model->insertFilesRecovery($data);
            /*if(!$file) {
                echo json_encode(array("success"=>false,"data"=>"Se ha creado la carpeta"));
                return;
            }*/
        }else {
            echo json_encode(array("success"=>false,"data"=>"La carpeta ya existe"));
            return;

        }
        echo json_encode(array("success"=>true,"data"=>".".$ruta));
        return;

    }
    /*
     * Envia informacion del editor
     */
    function sendDataEditor($url) {
        $datos["url"]=$url;
        $this->load->view("DashBoard/editcode/editcode",$datos);
        return $url;
    }
    function uploadview($cad) {
        $this->load->view("codeeditor/uploadFile/upload");
    }
    function uploadFile() {
    //$path=str_replace("-","/",$path);
        $path=$this->input->post("path");
        $abs=$this->input->post("absolutepath");

        if(copy($_FILES['userfile']['tmp_name'],$abs.$path.$_FILES['userfile']['name'])) {
            $data=array(
                "idUs"=>$this->session->userdata("us_id"),
                "idEq"=>$this->session->userdata("idEq"),
                "nombre"=>$_FILES['userfile']['name'],
                "ruta"=>$abs.$path,
                "fecha"=>mdate("%y/%m/%d",time()),
                "hora"=>date("H:i:s"),
                "estado"=>"new"
            );
            $this->repositorio_model->insertFilesRecovery($data);
            echo json_encode(array("success"=>true,"data"=>".".$path));
            return;
        }
        echo json_encode(array("success"=>false,"data"=>"Error al Subir el archivo"));
        return;
    }
    /*
     * Copia un directorio completo
     */
    function copyDirectory() {
        $absoluteRute=$this->input->post("absolute");
        $source=$this->input->post("source");
        $dest=$this->input->post("dest");
        $ruta=$dest;
        $name=$this->input->post("name");

        $source=$absoluteRute.$source;
        $dest=$absoluteRute.$dest;
        //creamos el directorio raíz
        @mkdir($dest.$name,0777);
        if(is_dir($dest.$name)) {
            if($this->_smartCopy($source,$dest.$name,0755,0644)) {
                echo json_encode(array("success"=>true,"data"=>".".$ruta));

                return;
            }
        }
        echo json_encode(array("success"=>false,"data"=>"Error.. no se ha completado la acción con éxito"));
        return;
    }
/*
 *
 * Directorio del proyecto
 * 
 */
    function getDirectory($param) {

        $root=str_replace("-","/",$param);
        //$root = $this->input->post("root");   //step 1
        $node = isset($_REQUEST['node'])?$_REQUEST['node']:""; //step 2
        if(strpos($node, '..') !== false) {
            die('Nice try buddy.');
        }
        
        $nodes = array();
        $d = dir($root."/".$node);      //step 1
        while($f = $d->read()) {      //step 2
            if($f == '.' || $f == '..' || substr($f, 0, 1) == '.') continue;    //step 3

            if(is_dir($root.'/'.$node.'/'.$f)) {         //step 4
                array_push($nodes,array('text'=>$f, 'id'=>$node.'/'.$f));
            }else {
                array_push($nodes, array('text'=>$f, 'id'=>$node.'/'.$f, 'leaf'=>true,'iconCls'=>$this->_getIcon($f)));
            }
        }
        $d->close();
        echo json_encode($nodes);
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
     * Nombre smartCopy
     * descripcion copia el contenido de directorios enteros a otros directorios
     */
    function _smartCopy($source, $dest, $folderPermission=0755,$filePermission=0644) {
    # source=file & dest=dir => copy file from source-dir to dest-dir
    # source=file & dest=file / not there yet => copy file from source-dir to dest and overwrite a file there, if present

    # source=dir & dest=dir => copy all content from source to dir
    # source=dir & dest not there yet => copy all content from source to a, yet to be created, dest-dir
        $files=explode("/",$dest);
        $f=$files[count($files)-1];
        $n=strlen($f);
        $ruta=substr($dest,0,strlen($dest)-$n);
        $data=array(
            "idUs"=>$this->session->userdata("us_id"),
            "idEq"=>$this->session->userdata("idEq"),
            "nombre"=>$f,
            "ruta"=>$ruta,
            "fecha"=>mdate("%y/%m/%d",time()),
            "hora"=>date("H:i:s"),
            "estado"=>"new"
        );
        $this->repositorio_model->insertFilesRecovery($data);
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
            $result=copy($source, $__dest);
            chmod($__dest,$filePermission);
        }
        elseif(is_dir($source)) { # $source is dir
            if(!is_dir($dest)) { # dest-dir not there yet, create it
                @mkdir($dest,$folderPermission);
                chmod($dest,$folderPermission);
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
                    $result=$this->_smartCopy($source.$file, $dest.$file, $folderPermission, $filePermission);
                }
            }
            closedir($dirHandle);
        }
        else {
            $result=false;
        }
        return $result;
    }
}

?>
