<?php
class Log
{
    /*
     * 
     */
     public static function add($mysqli,$campaign_id,$user_id,$lname,$fname,$action,$sign_id,$status,$img,$distance){
         if ($stmt = $mysqli->prepare("INSERT into log(campaign_id,user_id,name,action,sign_id,status,img,distance,date_log) VALUES(?,?,?,?,?,?,?,?,NOW())")) {
            $name = $lname.', '.$fname;
             $stmt->bind_param("iississs", $campaign_id,$user_id,$name,$action,$sign_id,$status,$img,$distance);
            return($stmt->execute());
         }
         return false;
    }
    
    
}
?>
