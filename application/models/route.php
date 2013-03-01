<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of route
 *
 * @author Ditmar
 */
class route extends CI_Model{
    function route(){
        parent::__construct();
    }
    function getConfigs(){
        $c=$this->db->query("select * from configs");
    
        return $c;
    }
    //put your code here
}
?>
