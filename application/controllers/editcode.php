<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class EditCode extends CI_Controller {
    var $mainpath="C:/wamp/www/blog/";
    function EditCode() {
        parent::__construct();
        $this->load->model("repositorio_model");
    }
    function editing() {
        $this->load->view("DashBoard/editcode/editcode");
    }
    function _openFile($file, $mode, $input) {
        if ($mode == "READ") {
            if (file_exists($file)) {
                $handle = fopen($file, "r");
                $output = fread($handle, filesize($file));
                fclose($handle);
                return $output; // output file text
            } else {
                return false; // failed.
            }
        } elseif ($mode == "WRITE") {
            $handle = fopen($file, "w+");
            if (!fwrite($handle, $input)) {
                return false; // failed.
            } else {
                return true; //success.
            }
        } elseif ($mode == "READ/WRITE") {
            if (file_exists($file) && isset($input)) {
                $handle = fopen($file,"r+");
                $read = fread($handle, filesize($file));
                $data = $read.$input;
                if (!fwrite($handle, $data)) {
                    return false; // failed.
                } else {
                    return true; // success.
                }
            } else {
                return false; // failed.
            }
        } else {
            return false; // failed.
        }
        /* $handle = fopen($file, "r");
         $output = fread($handle, filesize($file));
         fclose($handle);*/
        return $output; // output file text

    }
    function loadFile() {
        $ruta=$this->input->post("ruta");
        $file=$this->input->post("archivo");
        $rutaAbs=$this->input->post("rutaAbs");
        $texto=$this->_openFile($rutaAbs.$ruta,"READ","");

        $array=array("result"=>$texto,"file"=>$file);
        echo json_encode($array);
    }
    /*
     * Guarda el contenido del editor dentro del archivo
     * y actualiza el estado a update
     */
    function saveFile() {
        $datos=$this->input->post("data");
        $archivo=$this->input->post("file");
        $rutaAbs=$this->input->post("rutaAbs");

        if($this->_openFile($rutaAbs.$archivo,"WRITE",$datos)) {
            $array=array("result"=>true);
            //actualizamos en log File
            /*
             * en caso de que no éxista creamos el archivo
             *
             */
            $archi=$archivo;
            $_abs=$rutaAbs;
            $archi=explode("/",$archi);
            if(count($archivo)>0)
                $file=$archi[(count($archi)-1)];
            for($i=0;$i<count($archi)-1;$i++) {
                $_abs.=$archi[$i]."/";
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
                    "estado"=>"update",
                    "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
                );
                $this->repositorio_model->insertFilesRecovery($data);
            }else {
                $this->repositorio_model->updateFilesCopy($archivo,$rutaAbs,array("estado"=>"update","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));

            }

            echo json_encode($array);
            return;
        }
        $array=array("result"=>false);
        echo json_encode($array);
    }
    /*
     * Guarda en contenido del en un archivo y crea un log informatico de cambios
     */
    function saveFileLog() {
        $datos=$this->input->post("data");
        $archivo=$this->input->post("file");
        $rutaAbs=$this->input->post("rutaAbs");
        $log=$this->input->post("log");
        if($this->_openFile($rutaAbs.$archivo,"WRITE",$datos)) {
            $array=array("result"=>true);

            $archi=$archivo;
            $_abs=$rutaAbs;
            $archi=explode("/",$archi);
            if(count($archivo)>0)
                $file=$archi[(count($archi)-1)];
            for($i=0;$i<count($archi)-1;$i++) {
                $_abs.=$archi[$i]."/";
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
                    "estado"=>"update",
                    "updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")
                );
              $id=$this->repositorio_model->insertFilesRecovery($data);
            }else {
                $r=$this->repositorio_model->updateFilesCopy($archivo,$rutaAbs,array("estado"=>"update","updatefecha"=>mdate("%y/%m/%d",time())." ".date("H:i:s")));
                $id=$r[0]["id"];
            }
            $fill=array(
                "idFil"=>$id,
                "logText"=>$log,
                "fecha"=>mdate("%y/%m/%d",time()),
                "hupdate"=>date("H:i:s")
            );
            $id=$this->repositorio_model->insertLog($fill);

            echo json_encode($array);
            return;
        }
        $array=array("result"=>false);
        echo json_encode($array);

    }
    /*
     *  Upload file
     * subimos el directorio al la base de datos
     */
    function uploadFile($name,$path,$abs) {
        $path=str_replace("-","/",$path);
        if(copy($_FILES['Filedata']['tmp_name'],$abs.$path.$_FILES['Filedata']['name'])) {
            echo $abs.$path;
        }
        echo "Server: Error al subir el archivo";
    }

}
?>
