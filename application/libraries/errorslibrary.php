<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorsLibrary
 *
 * @author Ditmar
 */
class ErrorsLibrary {
    //put your code here
    var $error=array("keyerror1"=>"Página restringida solo para usuarios","keyError2"=>"Permiso restringido");
    public function getErrors(){
        return $this->error;
    }
}
?>
