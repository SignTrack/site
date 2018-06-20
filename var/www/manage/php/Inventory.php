<?php
class Inventory
{

     public static function debit($mysqli,$inventory_id,$quantity,$sign){
//         if ($stmt = $mysqli->prepare("SELECT num_used FROM inventory WHERE inventory_id=?")) {
//            $stmt->bind_param("i", $inventory_id);
//            $stmt->execute();
//            $stmt->bind_result($num);
//            if($stmt->fetch()){
//                
//                ($sign=='+')?$num2 = $num+$quantity:$num2 = $num-$quantity;
//                $stmt->close();
//                if ($stmt = $mysqli->prepare("UPDATE inventory SET num_used=$num2 WHERE inventory_id=?")) {
//                    //get salt first
//                    $stmt->bind_param("i",$inventory_id);
//                    return($stmt->execute());
//                 }
//            }
//         }
         if($sign=='+'){
             $sql = "UPDATE inventory SET num_used=num_used + ? WHERE inventory_id=?";
         }else{
             $sql = "UPDATE inventory SET num_used=num_used - ? WHERE inventory_id=?";
         }
         if ($stmt = $mysqli->prepare($sql)) {
                    //get salt first
                    $stmt->bind_param("ii",$quantity,$inventory_id);
                    return($stmt->execute());
                 }
         return false;
    }
    public static function update($mysqli,$user_id){
        $result = $mysqli->query("SELECT campaign_id,name,DATEDIFF(date_end,CURDATE()) AS days,date_login 
                FROM user_campaigns
                LEFT JOIN campaign
                USING(campaign_id)
                WHERE user_id=$user_id");
        if($result){
            $rows=[];
            while($row = $result->fetch_assoc()){
                $rows[]=$row;
            }
            
            }
            return $rows;
    }
    public static function getInventoriesBelowThreshohold($mysqli,$id,$threshold){
        $result = $mysqli->query("SELECT name, num_total-num_used AS num
                FROM inventory
                WHERE campaign_id='$id' && notified=0 && num_total-num_used<$threshold");
        if($result){
            $rows=[];
            while($row = $result->fetch_assoc()){
                $rows[]=$row;
            }
            
            }
            $result2 = $mysqli->query("UPDATE inventory
                set notified=1 WHERE campaign_id='$id' && notified=0 && num_total-num_used<$threshold");
            return $rows;
    }
    
}
?>
