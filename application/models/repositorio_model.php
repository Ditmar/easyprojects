<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class repositorio_model extends CI_Model{
    function repositorio_model(){
        parent::__construct();
    }
    function getRepositorio($type,$id){
        if(!$id){
            $result=$this->db->query("select * from repositorio where ".$type."='".$this->session->userdata("us_idPro")."'");
            return $result;
        }
        $result=$this->db->query("select * from repositorio where ".$type."='".$id."'");
        return $result;
    }
    function getLastInserted(){
        $rep=$this->db->query("select * from repositorio order by id desc limit 1");
        return $rep;
    }
    function insert($data){
        $this->db->insert("repositorio",$data);
        //$result=$this->getLastInserted();
        return $this->db->insert_id();
    }
    function insertFiles($data){
        $this->db->insert("archivos",$data);
        return true;
    }
    /*
     * update files
     */
    function updateArchivos($nombre,$ruta,$estado){
        $condicion="nombre='".$nombre."' and ruta='".$ruta."'";
        //echo $this->db->update_string("archivos",$estado,$condicion);
        $this->db->query($this->db->update_string("archivos",$estado,$condicion));
        $r=$this->db->query("select * from archivos where ".$condicion);
        return $r->num_rows();
    }
    /*
     *regresa los archivos del Systema
     */
    function getFilesSystem($abs,$nombre){
        $result=$this->db->query("select * from archivos where ruta='".$abs."' and nombre='".$nombre."'");

        return $result;
    }
    function checkExistArchivos($value,$nombre){
        $result=$this->db->query("select * from archivos where ruta='".$value."' and nombre='".$nombre."'");
        //echo "select * from archivos where ruta='".$value."' and nombre='".$nombre."'";
        if($result->num_rows()>0){
            //echo"entraaa ";
            $workspace=array(
                "success"=>true,
                "row"=>$result->result_array()
            );
            return $workspace;
        }
        echo "select * from archivos where ruta='".$value."' and nombre='".$nombre."'";
        $workspace=array(
                "success"=>false,
                "row"=>""
            );
        return $workspace;
    }
    function getArchivos($type,$value){
        $result=$this->db->query("select * from archivos where ".$type."='".$value."'");

        return $result;
    }
    /*
     * Update sobre los workspaces
     */
    function updateWorkSpace($fields,$condicion){
        $this->db->query($this->db->update_string("workspace",$fields,$condicion));
        return true;
    }
    /*
     * funciones con la tabla del workspace
     *recupera un workspace
     */
    function getWorkSpace($idUs,$idEq){
        //echo"select * from workspace where idUs='".$idUs."' and idEq='".$idEq."'";
        $rep=$this->db->query("select * from workspace where idUs='".$idUs."' and idEq='".$idEq."'");
        return $rep;
    }
    /*
     * Hcw insersiones en la tabla Files_copy
     * de los archivos en base a todos los parametros concernientes en un archivo
     */
    function insertFilesRecovery($data){
        $this->db->insert("files_copy",$data);
        return $this->db->insert_id();
    }
    /*
     * Inserta dentro del la tabla workspace
     */
    function insertWorkSpace($data){
        $this->db->insert("workspace",$data);
        return true;
    }
    /*
     * hace un update dentro de la tabla Files Copy
     * especificamente en el atributo estado según el parámetro estado
     * el parametro ruta nos da el id de la ruta fisica del archivo
     * y el nombre
     * parametros
     * updateFilesCopy('rutarelativa + nombre','raíz de la ruta absoluta','que actualizamos');
     */
    function updateFilesCopy($file,$ruta,$estado){
       $archivo=explode("/",$file);
       if(count($archivo)>0)
        $file=$archivo[(count($archivo)-1)];
       for($i=0;$i<count($archivo)-1;$i++){
           $ruta.=$archivo[$i]."/";
       }
       $condicion="nombre='".$file."' and ruta='".$ruta."'";
       $res=$this->db->query("select id from files_copy where nombre='".$file."' and ruta='".$ruta."'");
       $this->db->query($this->db->update_string("files_copy",$estado,$condicion));
       //echo$this->db->update_string("files_copy",$estado,$condicion);
       return $res->result_array();
    }
    function updateFilesUser(){
        
    }
    /*
     * Insercion dentro del log_file
     * inserta el log en la tabla log file
     * que es directamente relacionada con Files_copy
     * parametros informacion de cambios efectuados
     */
    function insertLog($data){
        $this->db->insert("log_file",$data);
        return $this->db->insert_id();

    }
    /*
     * esta funcion nos permite obtener el log_file
     * el que queramos
     * $param=array("ruta"=>"c:/..","nombre"=>"nombre.php")
     * 
     */
    function getAttrLogFile($param){
        $result=$this->db->query("select lf.".$atributo." from files_copy as fc, log_file as lf where idUs='10'  and idEq='41' and lf.idFil=fc.id and fc.ruta='".$param["ruta"]."' and fc.nombre='".$param["nombre"]."'");
        return $result;
    }
    /*
     * de aqui adelante estan las consultas
     * pertenecientes a la publicación
     *
     */
    function getChangesSystem(){
        $r=$this->db->query("select ar.* from repositorio as rep, archivos as ar where ar.idPak=rep.id and rep.idPro='".$this->session->userdata("us_idPro")."' and estado!='activo'");
        return $r;
    }
    /*
     * Te regresa un archivo en base a su ruta y nombre
     *
     */
    function getFilesCopy($ruta,$nombre){
        //echo "select * from files_copy where ruta='".$ruta."' and nombre='".$nombre."'";
        $r=$this->db->query("select * from files_copy where ruta='".$ruta."' and nombre='".$nombre."'");
        return $r;
    }


    /*
     * t da los archivos que sufrierón cambios en su estructura
     * de la tabla files_copy
     */
    function getChanges($idUs,$idEq){
        $result=$this->db->query("select * from files_copy where estado!='activo' and estado!='deletedforever' and idUs='".$idUs."' and idEq='".$idEq."'");
        //echo"select * from files_copy where estado!='activo' and idUs='".$idUs."' and idEq='".$idEq."' limit ".$start.",".$limit;
        return $result;
    }
    function getChangesCount($idUs,$idEq){
        $result=$this->db->query("select count(*) as tam from files_copy where estado!='activo' and idUs='".$idUs."' and idEq='".$idEq."'");
        return $result->row()->tam;
    }
    function getLog($id,$start,$limit){
        $result=$this->db->query("select * from log_file where idFil='".$id."' order by id desc limit ".$start.",".$limit."");
        return $result;
    }
    function getLogCount($id){
        //echo $id;
        $result=$this->db->query("select count(*) as tam from log_file where idFil='".$id."'");
        return  $result->row()->tam;
    }
    /*
     * Repositorios
     * este método es especial
     * regresa todos los repositorios del proyecto que se este manipulando, más el nick y avatar de la persona que los usa
     * el parametro $idUs indica que repositorio no quiere q se cargue, con el fin de enviar invitaciones a todos menos quien propuso el cambio
     */
    function getRepositoriosModel($idUs){
        $result=$this->db->query("select eq.nombre,us.nick,us.avatar,wo.path,us.id as idUs,eq.id as idEq from equipo as eq,usuario as us,workspace as wo where eq.idPro='".$this->session->userdata("us_idPro")."' and wo.idEq=eq.id  and us.id=wo.idUs and us.id!='".$idUs."'");
    
        return $result;
    }
    /*
     * Insert Notificacion
     */
    function insertNotificacion($data){
        $this->db->insert("notificacion",$data);
        return true;
    }

    /*
     * insertamos dentro de branchessss tabla y carpeta del repositorio
     *
     */
    function insertBranches($data){
        $this->db->insert("branches",$data);
        return true;
    }
    function insertRel_lo_ar($data){
        /*
         * verificacion
         *
         */
        $info=$this->db->query("select * from rel_lo_ar where idFi='".$data["idFi"]."' and idAr='".$data["idAr"]."'");
        if($info->num_rows()==0){
            $this->db->insert("rel_lo_ar",$data);
        }

        return true;
    }
    /*
     * Ultimos cambios
     */
    function updatelastchanges($data,$condicion){
        $this->db->query($this->db->update_string("lastchanges",$data,$condicion));
        return true;
    }
    function deleteworkspace($idUs,$idEq){
        //echo"delete from workspace where idUs='".$idUs."' and idEq='".$idEq."'";
        $this->db->query("delete from workspace where idUs='".$idUs."' and idEq='".$idEq."'");
    }
    /*
     * borrado de items de la tabla fiels_copy
     */
    function deleteFiles($idUs,$idEq){
        try{
            //echo "delete from files_copy where idUs='".$idUs."' and idEq='".$idEq."'";
            $this->db->query("delete from files_copy where idUs='".$idUs."' and idEq='".$idEq."'");
        }catch(Error $e){

        }
        
    }
    /*
     * pedimos los brances al del repositorio para descargar
     */
    function getBranchesList(){
        $r=$this->db->query("select bran.*,us1.avatar as avatar1,us2.avatar as avatar2,us1.nick as nick1, us2.nick as nick2 from branches as bran, usuario as us1, usuario as us2 where bran.idPro='".$this->session->userdata("us_idPro")."' and us1.id=bran.idUsReview and us2.id=bran.idUsChange");

        return $r;
    }
    
    /*
     * regresa el ultimo brances
     */
    function getLastBranches(){
        $r=$this->db->query("select * from branches where idPro='".$this->session->userdata("us_idPro")."' order by id desc limit 1");

        return $r;
    }
}
?>
