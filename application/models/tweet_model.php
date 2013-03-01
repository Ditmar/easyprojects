<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class tweet_model extends CI_Model{
    function tweet_model(){
        parent::__construct();
    }
    /*
     * NOs regresa nuestros Feeds
     */
    function getAllTweets(){
        
    }
    function getTweetsById($idUs,$start,$limit){
       // if(isset($start)&&isset($limit)){
            $result=$this->db->query("select tw.*,fo.idFo,us.avatar,us.nick,us.id as idUs from followtweet as fo,rel_tweet_us as ret,tweet as tw,usuario as us where fo.idUs='".$idUs."' and ret.idUs=fo.idFo and ret.idTw=tw.id and fo.idFo=us.id order by tw.id desc  limit ".$start.",".$limit);
        //}/*else{
            //$result=$this->db->query("select tw.*,us.avatar,us.id as idUs,us.nick from rel_tweet_us as rel, tweet as tw, usuario as us where us.id='".$idUs."' and rel.idUs=us.id and rel.idTw=tw.id order by id desc");
        //}
        return $result;
 }
    function getCount($idUs){
        $result=$this->db->query("select tw.*,fo.idFo,us.avatar from followtweet as fo,rel_tweet_us as ret,tweet as tw,usuario as us where fo.idUs='".$idUs."' and ret.idUs=fo.idFo and ret.idTw=tw.id and fo.idFo=us.id order by tw.id desc");
        return count($result->result_array());
    }
    function followTweetsUser($idUs){
        $data=array("idFo"=>$idUs,"idUs"=>$this->session->userdata("us_id"));
        $this->db->insert("followtweet",$data);
        return $this->db->insert_id();
    }
    function followSetTweet($idUs,$idRel){
        $data=array("idFo"=>$idUs,"idUs"=>$idRel);
        $this->db->insert("followtweet",$data);
        return $this->db->insert_id();

    }
    function insertTweets($data){
        $this->db->insert("tweet",$data);
        $idTweet=$this->db->insert_id();
        $rel=array("idUs"=>$this->session->userdata("us_id"),"idTw"=>$idTweet);
        $this->db->insert("rel_tweet_us",$rel);
        return $idTweet;
    }
}
?>
