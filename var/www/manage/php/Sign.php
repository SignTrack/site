<?php
class Sign
{
    
    public function getSignsByCampaign($mysqli,$campaign,$user_id,$latitude,$longitude){
        
            $result = $mysqli->query("(SELECT sign.*,DATE_FORMAT(date_last, '%m/%d/%y %l:%i%p') AS last, num_material, material_id, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance
        FROM sign
        LEFT JOIN user
        ON(sign.user_id=user.user_id)
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE inventory.campaign_id='".$campaign['campaign_id']."' && status!='Deactivated' && status!='Recovered' && sign.user_id=$user_id)
        UNION
        (SELECT sign.*,DATE_FORMAT(date_last, '%m/%d/%y %l:%i%p') AS last, num_material, material_id, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance
        FROM sign
        LEFT JOIN user
        ON(sign.user_id=user.user_id)
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE inventory.campaign_id='".$campaign['campaign_id']."' && status!='Deactivated' && status!='Recovered'
        LIMIT 5)
        ORDER By distance ASC");
            
            $num_place = 0;
            $num_replace = 0;
            $num_fix =0;
            $num_recover =0;
            $num_recover =0;
            $num_ok =0;
            $signs = array();
            $recover=false;
            if($campaign['days']<0){
                $recover=true;
            }
            while ($data = $result->fetch_assoc())
            {
                if($data['user_id']==$user_id){
                switch($data['status']){
                    case 'Place':
                        $num_place++;
                        break;
                    case 'Replace':
                        $num_replace++;
                        break;
                    case 'Fix':
                        $num_fix++;
                        break;
                    case 'Recover':
                        $num_recover++;
                        break;
                    case 'OK':
                        $num_ok++;
                        break;
                } 
                }
                if($recover){
                    if($data['status']=='Fix' || $data['status']=='OK'){
                        $data['status']='Recover';
                        $signs[] = $data;
                    }
                    
                }else{
                    $signs[] = $data;
                }
            }
            //if this is after the election everything should be recover
            if($recover){
                $num_place=0;
                $num_recover=$num_fix+$num_ok+$num_replace;
                $num_fix=0;
                $num_replace=0;
            }    
            
            $campaign['num_place']=$num_place;
            $campaign['num_replace']=$num_replace;
            $campaign['num_fix']=$num_fix;
            $campaign['num_recover']=$num_recover;
            $campaign['signs']=$signs;
        
        return $campaign;
    }
    
     public static function getSignStatus($mysqli,$sign_id){
         if ($stmt = $mysqli->prepare("SELECT status FROM sign WHERE sign_id=? LIMIT 1")) {
            //get salt first
            $stmt->bind_param("i", $sign_id);
            $stmt->execute();
            $stmt->bind_result($status);
            if($stmt->fetch()){
                return $status;
            }
         }
         return false;
    }
    public static function update($mysqli,$user_id,$sign_id,$status,$note){
         if ($stmt = $mysqli->prepare("UPDATE sign SET status=?,note=?, date_last=NOW() WHERE sign_id=?")) {
            $stmt->bind_param("ssi", $status,$note,$sign_id);
            return ($stmt->execute());
         }
         return false;
    }
    public static function add($mysqli,$user_id,$postvars){

         //if ($stmt = $mysqli->prepare("INSERT into sign(inventory_id,user_id,lat,lng,note,status,date_last) values(?,?,?,?,?,?,NOW())")) {
        if ($stmt = $mysqli->prepare("INSERT into sign(inventory_id,user_id,lat,lng,note,status,date_last) values(?,?,?,?,?,?,NOW())")) {    

            $stmt->bind_param("iiddss",$postvars['inventory_sign'],$user_id,$postvars['latitude'],$postvars['longitude'],$postvars['note'],$postvars['status']);
            //$stmt->bind_param("iissss", $postvars['inventory_sign'],$postvars['user_id'],$postvars['latitude'],$postvars['longitude'],$postvars['notes'],$postvars['status']);
            $stmt->execute();
            $id=$mysqli->insert_id;
            return $id;
         }
         return false;
    }
    public static function allowAdd($mysqli,$campaign_id){
        $query = $mysqli->stmt_init();
         //if ($stmt = $mysqli->prepare("INSERT into sign(inventory_id,user_id,lat,lng,note,status,date_last) values(?,?,?,?,?,?,NOW())")) {
        if ($query->prepare("SELECT count(sign_id) AS sign_used,sign_limit FROM campaign
LEFT JOIN inventory
USING(campaign_id)
LEFT JOIN sign
ON(inventory.inventory_id=sign.inventory_id)
WHERE campaign.campaign_id=? && sign.status!='Recovered' && sign.status!='Deactivated'")) {    

            $query->bind_param("i",$campaign_id);
            //$stmt->bind_param("iissss", $postvars['inventory_sign'],$postvars['user_id'],$postvars['latitude'],$postvars['longitude'],$postvars['notes'],$postvars['status']);
             if($result = $query->execute()){
                 $result = $query->get_result();
                        $row = $result->fetch_array(MYSQLI_ASSOC); 
                        if($row['sign_used']<$row['sign_limit']){
                            return true;
                        }
             }
            
         }
         return false;
    }
    public static function searchById($mysqli,$campaign_id,$sign_id){
        $query = $mysqli->stmt_init();
         if ($query->prepare("SELECT sign.*,DATE_FORMAT(date_last, '%m/%d/%y %l:%i%p') AS last, num_material, material_id
        FROM sign
        LEFT JOIN user
        ON(sign.user_id=user.user_id)
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE inventory.campaign_id=? && status!='Deactivated' && status!='Recovered' && sign.sign_id=?
        LIMIT 1")) {
                    $query->bind_param("ii", $campaign_id, $sign_id);
                    if($result = $query->execute()){
                        //return user information
                        $result = $query->get_result();
                        return $result->fetch_array(MYSQLI_ASSOC); 
                        $stmt->close();
                    }
                }   
         return false;
    }
}
?>
