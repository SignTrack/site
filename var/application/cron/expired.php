<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$username = "webteam";$password = "tDm4E5GtKCPrzquF";
$mysqli = new mysqli("localhost", $username, $password, "sig_site");
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}else{
    //run the sql
    $result = $mysqli->query("SELECT campaign_id FROM campaign WHERE date_end < NOW() - INTERVAL 30 DAY AND NOW()");
        echo("RESULTS: ".mysqli_num_rows($result)."\n\r");
        if($result && mysqli_num_rows($result)>0){
            while(($row = $result->fetch_assoc())!== null){
                $files = glob('/var/www/manage/images/upload/'.$row['campaign_id'].'-*');
                echo "NUMBER OF FILES: ".sizeof($files)."\n\r";
                for($i=0;$i<sizeof($files);$i++){
                    echo "DELETING: ".$files[$i]."\n\r";
                    unlink($files[$i]);
                }
                
             
                //delete sign
                $mysqli->query("DELETE sign
                    FROM sign
                    LEFT JOIN inventory
                    ON(sign.inventory_id=inventory.inventory_id)
                    WHERE campaign_id=".$row['campaign_id']);
                //delete inventory
                $mysqli->query("DELETE
                    FROM inventory
                    WHERE campaign_id=".$row['campaign_id']);
                //delete logs
                $mysqli->query("DELETE FROM log
                    WHERE campaign_id=".$row['campaign_id']);
                //delete user_campaigns
                $mysqli->query("DELETE FROM user_campaigns WHERE campaign_id=".$row['campaign_id']);
                
                //delete users with no campaigns assigned
                $mysqli->query("DELETE
                    FROM user
                    WHERE user_id NOT IN
                    (
                        SELECT user_id
                        FROM user_campaigns
                    )");
                //delete campaign
                $mysqli->query("DELETE
                    FROM campaign
                    WHERE campaign_id=".$row['campaign_id']);
                echo "CAMPAIGN DELETED".'\n\r';
                
            }
                
        }
}

?>