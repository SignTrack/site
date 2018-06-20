<?php
class Campaign
{
    protected $table='inventory';
     public static function getInventory($mysqli,$id){
        $result = $mysqli->query("SELECT * FROM inventory WHERE campaign_id=$id");
        if($result){
            $rows=[];
            while($row = $result->fetch_assoc()){
                $rows[]=$row;
            }
            
            }
            return $rows;
    }
    public static function getOneById($mysqli,$campaign_id){
        $query = $mysqli->stmt_init();
        if ($query->prepare("SELECT * FROM campaign WHERE campaign_id=?")) {
                    $query->bind_param("i", $campaign_id);
                    if($result = $query->execute()){
                        $result = $query->get_result();
                        return $result->fetch_array(MYSQLI_ASSOC); 
                        $stmt->close();
                    }
                } 
                return false;
    }
    public static function getUserCampaigns($mysqli,$user_id){
        $result = $mysqli->query("SELECT campaign_id,name,DATEDIFF(date_election,CURDATE()) AS days,date_login 
                FROM user_campaigns
                LEFT JOIN campaign
                USING(campaign_id)
                WHERE user_id=$user_id && role='Volunteer'");
        if($result){
            $rows=[];
            while($row = $result->fetch_assoc()){
                $rows[]=$row;
            }
            
            }
            return $rows;
    }
    
}
?>
