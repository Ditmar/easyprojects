<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Item{
    var $url="";
    var $cc=0;
}
class DashBoard extends CI_Controller{
    var $interface_data;
    var $xmldir="";
    var $counter=0;
    var $dirpath=array();
    function DashBoard(){
        parent::__construct();
        $this->load->helper("directory");
    }
    function Index(){
        $this->load->view("DashBoard/dashsystem");
    }

    function _getData($dir){
       $i=0;
       try{
        foreach($dir as $la){
            $i++;
        }
        }catch(Exception $e){
            return false;
        }
        return $i;
    }
    function _getLength($dir){
        $i=0;
        foreach($dir as $la){
            $i++;
        }
        return $i;
    }
    function _parse($dir,$path){
        $indices=array_keys($dir);
        $i=0;
        
        foreach($dir as $la){

            if($this->_getData($la)>0){
                $this->xmldir.="<node label='ss".$indices[$i]."'>";
                $path.=$indices[$i]."/";
                $this->_parse($la,$path);
                $this->xmldir.="</node>";
                //echo"-> ".$la." <br/>";
            }else{
                 $this->xmldir.="node label='".$path.$la."'></node>";
            }
            $i++;
            //subimos un nivel arriba en el directorio
            /*if($i==$this->_getLength($dir)){
                $aux=split("/",$path);
                $path="/";
                for($j=0;$j<count($aux)-2;$j++){
                    $path=$aux[$j]."/";
                }
            }*/
        }
    }
    function pruebas(){
        $directorio=directory_map("localhost:70/blog");
        /*foreach($directorio as $dir){
            $item=new Item();
            $dirpath[]=new Item();
        }*/
        $path="/";
        $this->xmldir.="<list>";
        $this->_parse($directorio,$path);
        $this->xmldir.="</list>";
        return $this->xmldir;
    }
}

?>
