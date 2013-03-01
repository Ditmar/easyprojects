<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basedata_model
 *
 * @author Ditmar
 */
class Basedata_model extends CI_Model {
//put your code here
    function Basedata_model() {
        parent::__construct();
    }

    //utils methods
    function _requiered($requiere,$data) {
        foreach($requiere as $fields) {
            if(!isset($data[$fields]))return false;
        }
        return true;
    }
    /*
     * getUsersDbProject metodo que regresa los usuarios que estan en la tabla 'base_user'
     * Option Values
     * ---------------
     * id
     * fecha
     * nick
     * pass
     * idPro
     * limit offSet
     * orderBy orderDirection
     * @param array oprtions
     * Returned Objects
     * nick
     * idPro
     * fecha
     * @result array collection or simple request
     */

    function getUsersDbProject($options=array()) {
    //Qualificaction
        if(isset($options["id"])) {
            $this->db->where("id",$options["id"]);
        }
        if(isset($options["fecha"])) {
            $this->db->where("fecha",$options["fecha"]);
        }
        if(isset($options["nick"])) {
            $this->db->where("nick",$options["nick"]);
        }
        if(isset($options["pass"])) {
            $this->db->where("pass",$options["pass"]);
        }
        if(isset($options["idPro"])) {
            $this->db->where("idPro",$options["idPro"]);
        }
        //limiy/offset
        if(isset($options["limit"])&&isset($options["offSet"])) {
            $this->db->limit($options["limit"],$options["offSet"]);
        }elseif(isset($options["limit"])) {
            $this->db->limit($options["limit"]);
        }
        //orderBy/orderDirection
        if(isset($options["orderBy"])&&isset($options["orderDirection"])) {
            $this->db->order_by($options["orderBy"],$options["orderDirection"]);
        }
        $query=$this->db->get("base_user");
        if(isset($options["id"])||isset($options["nick"]))
            return $query->row(0);

        return $query->result();
        /*$res=$this->db->query("select * from base_user where idPro='".$this->session->userdata("us_idPro")."'");
        return $res->result();*/
    }
    function createUser($data) {
        $resnom=$this->db->query("select * from base_user where nick='".$data["nick"]."'");
        if($resnom->num_rows()==0) {

            $res=$this->db->query("create user ".$data["nick"]." identified by '".$data["pass"]."'");
            if($res==1) {
                $r=$this->db->insert("base_user",$data);
                if($r==1)
                    return true;
                return false;
            }
        }
        //echo "El usuario ya existe ";
        return false;
    }


    function deleteUser($id,$nombre) {
    //el borrado se hace de la sigueinte forma
    //primero borramos el usuario creado en la Base de datos
    //segundo borramos el usuario listado en la base de datos
    //tercero enviamos la confiormación correcta
        $res= $this->db->query("drop user ".$nombre);
        if($res==1) {
            $r=$this->db->query("delete from base_user where id='".$id."'");
            if($r) {
                return true;
            }
            return false;
        }
    }
    /*
     * addBaseData metodo crea  un registro de el nombre de bases de datos en la tabla database_nexus
     * y crea una consulta con query de create database 
     *
     * Options: Values
     *----------------
     * fecha
     * hora
     * nombre
     * idPro
     *@param array $options
     * @result int insert_id()
     */
    function addBaseData($options=array()) {
    //valores requeridos
        if(!$this->_requiered(
        array("fecha","hora","nombre","idPro"),$options
        ))return false;
        $res=$this->db->query("create database ".$options["nombre"].";");
        if($res) {
            $this->db->insert("database_nexus",$options);
            return $this->db->insert_id();
        }
        return false;
    }
    /*
     * addUserData motodo que crea un registro de ids dentro de la tabla 'rel_data_user' que relaciona usuario y base de datos
     * y asigna lo privilegios con la cuenta de usuario ROOT 
     * option: Values
     * --------------
     * idBase
     * idBase_user
     * nameBase
     * userBaseName
     * idPro
     * @param array $options
     * @result true or false
     */
    function addUserData($options) {
        if(!$this->_requiered(
        array("idBase","idBase_user","nameBase","userBaseName"),$options
        ))return false;
        $re=$this->db->query("grant all on ".$options["nameBase"].".* to ".$options["userBaseName"].";");
        if($re) {
            $this->db->insert("rel_data_user",array("idBase"=>$options["idBase"],"idBase_user"=>$options["idBase_user"],"idPro"=>$options["idPro"]));
            return $this->db->insert_id();
        }
        return false;
    }
    /*
     * getDataBases es un método que me da las bases de datos creadas en la tabla "database_nexus"
     * option:value
     * -------------
     *
     * id
     * fecha
     * hora
     * nombre
     * idPro
     * limit offSet
     * orderBy orderDirection
     *
     * returned object(array of)
     * fecha
     * hora
     * nombre
     * idPro
     * @param array $options
     * @return object(array of)
     */

    function getDataBases($options=array()) {

        if(isset($options["id"])) {
            $this->db->where('id',$options["id"]);
        }
        if(isset($options["fecha"])) {
            $this->db->where('fecha',$options["fecha"]);
        }
        if(isset($options["hora"])) {
            $this->db->where('hora',$options["hora"]);
        }
        if(isset($options["nombre"])) {
            $this->db->where('nombre',$options["nombre"]);
        }
        if(isset($options["idPro"])) {
            $this->db->where('idPro',$options["idPro"]);
        }
        //limit/offSet
        if(isset($options["limit"])&&isset($options["offSet"])) {
            $this->db->limit($options["limit"],$options["offSet"]);
        }
        if(isset($options["limit"])) {
            $this->db->limit($options["limit"]);
        }
        //orderBy / orderDirection
        if(isset($options["orderBy"])&&$options["orderDirection"]) {
            $this->db->order_by($option["orderBy"],$option["orderDirection"]);
        }
        $query=$this->db->get("database_nexus");
        if(isset($options["id"])||isset($options["nombre"])) {
            if(isset($options["count"])){
                return $query->num_rows();
            }
            return $query->row(0);
        }
        return $query->result();
    }
    /*
     * deleteBaseData method: Metodo que borra las bases de datos desde la raíz
     * borra el registro grabado en la base de datos database_nexus
     * option :value
     * --------------------------
     * id
     * nombre
     *@param: array $options
     * @return true or false
     */

    function deleteBaseData($options=array()) {
        if(!$this->_requiered(
        array("id","nombre"),$options
        ))return false;
        $r=$this->db->query("drop database ".$options["nombre"]);
        if($r) {
            $this->db->delete("database_nexus",array("id"=>$options["id"]));
            return true;

        }
        return false;
    }
    /*
     *revokeUser kita los permisos a un usuario determinado de una base de datos determinada
     * option:value
     * -------------
     * database
     * user_database
     * @param: array $options
     * @return true or false
     */
    function revokeUser($options=array()) {
        if(!$this->_requiered(array("database","user_database"),$options))
            return false;


        $r=$this->db->query("revoke all on ".$options["database"].".* from ".$options["user_database"].";" );

        if($r) {
            $this->db->select("idBase",$options["idDataBase"]);
            $this->db->select("idBase_user",$options["idDataBaseUser"]);
            $this->db->delete("rel_data_user");
            return true;
        }
        return false;
    }
    /*
     * getBaseDataUser regresa la información de los usuarios y bases de datos que tienen permisos sobre las mismas
     * idPro
     * @param: array $options
     * @return list objects
     * idBase
     * nameBase
     * fecchaBase
     *
     */
    function getBaseDataUser($options=array()) {
        if(!$this->_requiered(array("idPro"),$options))
            return false;
        $res=$this->db->query("select base_user.id as idUser,database_nexus.id as idBase,database_nexus.nombre,database_nexus.fecha,base_user.nick,base_user.fecha  from rel_data_user,base_user,database_nexus where rel_data_user.idPro='".$options["idPro"]."' and rel_data_user.idBase=database_nexus.id and rel_data_user.idBase_user=base_user.id;");
        //echo"select base_user.id,database_nexus.id,database_nexus.nombre,database_nexus.fecha,base_user.nick,base_user.fecha  from rel_data_user,base_user,database_nexus where rel_data_user.idPro='".$options["idPro"]."' and rel_data_user.idBase=database_nexus.id and rel_data_user.idBase_user=base_user.id;";
        return $res->result();
    }

}
?>
