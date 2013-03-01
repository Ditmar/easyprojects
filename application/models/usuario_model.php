<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usuario
 *
 * @author Ditmar
 */
class Usuario_model extends CI_Model{
    function Usuario_model(){
        parent::__construct();
    }
    function getRoles(){
        //checamos la session
        $res=$this->db->query("select * from rel_us_actividad,actividades_sistema where actividades_sistema.id=rel_us_actividad.idActi and idUs='".$this->session->userdata["us_id"]."'");
        $roles=$res->result_array();
        return $roles;
    }
    /*
     * Envia un Objeto con los datos del usuario
     * el objeto que envia se crea dinámicamente identico al de la tabla que hace referencia
     */
    function getUser(){
        $res=$this->db->query("select * from usuario where id='".$this->session->userdata["us_id"]."'");
        return $res->row();
    }
    /*
     * SE AUTENTICA LOS DATOS CON LA BASE DE DATOS
     */
    function checkAutentication($nick,$pass){
        $res=$this->db->query("select * from usuario where nick='".$nick."' and pass='".md5($pass)."'");
        if($res->num_rows()==1){
            /*
             * verificamos si tien el code registrado
             */
            
            return $res->row();
        }
        return false;
    }
    /*
     * SE VERIFICA SI EXISTE UN NICK SIMILAR
     */
    function checkNick($nick){
        $res=$this->db->query("select nick from usuario where nick='".$nick."'");
        if($res->num_rows()==1){
            return false;
        }
        return true;
    }
    /*
     * SE VERIFICA SI EXISTE UN EMAIL IGUAL
     *
     */
    function checkEmail($email){
        $res=$this->db->query("select email from usuario where email='".$email."'");
        //echo"hola".$res->num_rows();
        if($res->num_rows()>0){
            return false;
        }
        return true;
    }
    /*
     * SE AGREGA EL REGISTRO DEL NUEVO USUARIO
     */
    function checkCode($code){
        $r=$this->db->query("select * from usuario where code='".$code."'");
        return $r;
    }
    function setData($usuario){
        $this->db->insert("usuario",$usuario);
        return $this->db->insert_id();
    }
    /*
     * Update a los datos
     */
    function updateCode($data,$condicion){
        $this->db->query($this->db->update_string("usuario",$data,$condicion));
        return true;
    }
    function upDateUser($usuario){
       $condicion="id='".$this->session->userdata["us_id"]."'";
       $res=$this->db->query($this->db->update_string("usuario",$usuario,$condicion));
       return $res;
       //echo"--->> ".$res;

    }
    function setActividad($id){
        $this->db->query("insert into rel_us_actividad values('".$id."',1),('".$id."',2);");
        return true;
    }
    /*
     *
     * Actualiza avatar
     */
    function updateAvatar($url){
        $data=array(
            'avatar'=>$url
        );
        $condicion="id='".$this->session->userdata["us_id"]."'";
       $this->db->query($this->db->update_string("usuario",$data,$condicion));
       echo $this->db->update_string("usuario",$data,$condicion);
       return $url;
    }
    /*
     * Buscar usuario
     * modelo de consultas
     * para obtener resultados
     */
    function searchUser($table,$fields,$parametro){
        $sql="select * from ".$table." where";
       for($i=0;$i<count($fields);$i++){
            $sql.=" ".$fields[$i]." like '%".$parametro."%' ";
            if($i<count($fields)-1)
                $sql.="or";
        }
        $result=$this->db->query($sql);
        return $result;
    }
    /*
     * dame el id
     */
    function getIdUser($nick,$tipe){
        $user=$this->db->query("select * from usuario where nick='".$nick."'");
        if($tipe=="array"){
            return $user->result_array();
        }else if($tipe=="object"){
            return $user->result();
        }
    }
    function getUserByIdAndType($id,$type){
       $user=$this->db->query("select ".$type." from usuario where id='".$id."'");
       
       return $user->row();
    }
    function getUserById($id,$tipe){
        $user=$this->db->query("select * from usuario where id='".$id."'");
        if($tipe=="array"){
            return $user->result_array();
        }elseif("row"){
            return $user->row();
        }
        return $user->result();
    }
    function getUserByProject(){
        $user=$this->db->query("select us.*,pro.create_at as fecha from usuario as us, proyecto as pro where pro.id='".$this->session->userdata("us_idPro")."' and pro.idUs=us.id");
        return $user;
    }
    function getUserByNick($nick){
        $user=$this->db->query("select * from usuario where nick='".$nick."'");
        return $user;
    }
    function setPrimaryActivity(){
        
    }
}
?>
