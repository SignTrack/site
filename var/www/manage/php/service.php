<?php

//number of results to show for posts
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type');
include('AuthUser.php');

$username = "webteam";$password = "tDm4E5GtKCPrzquF";

//$username = "root";$password = "";
/*
 * respones codes
 * 
 * -3 credentials not recognized
 */

isset($_POST['service'])?$service = $_POST['service']:$service=null;
if($service){
        $mysqli = new mysqli("localhost", $username, $password, "sig_site");
        /* check connection */
        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        switch($service){
            case 'login':
                $auth = new AuthUser();
                 if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    include('Campaign.php');
                    $campaign = new Campaign();
                    $campaigns = $campaign->getUserCampaigns($mysqli, $user_info['user_id']);
                    if(sizeof($campaigns)>0){
                        if($user_info['reset']==1){
                            echo 2;
                        }else{
                            echo 1;
                        }
                    }else{
                        //user exists, but not a volunteer, throw error for not recognized credentials
                        echo -3;
                    }
                 }else{
                     echo -3;
                 }
                break;
            case 'getdata':
                $auth = new AuthUser();
         
                if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    include('Sign.php');
                    $Sign = new Sign();
                    include('Campaign.php');
                    $campaign = new Campaign();
                    
                    $campaigns = $campaign->getUserCampaigns($mysqli, $user_info['user_id']);
                    if(sizeof($campaigns)>0){
                    $selected_campaign=0;
                    if($_POST['campaign_id']!=0){
                        for($i=0;$i<sizeof($campaigns);$i++){
                            if($campaigns[$i]['campaign_id']==$_POST['campaign_id']){
                                $selected_campaign=$i;
                                break;
                            }
                        }
                    }
                    }else{
                        echo -1;
                    }
                    //echo "test: ".$campaigns[$selected_campaign]['campaign_id'];
                    $inventory = $campaign->getInventory($mysqli, $campaigns[$selected_campaign]['campaign_id']);
                    $selected_campaign = $Sign->getSignsByCampaign($mysqli, $campaigns[$selected_campaign], $user_info['user_id'],$_POST['latitude'],$_POST['longitude']);
                    unset($user_info['salt']);
                    $data = array(
                        'profile' => $user_info,
                        'campaigns' => $campaigns,
                        'selected_campaign' => $selected_campaign,
                        'inventory' => $inventory
                    );
                    echo json_encode($data);
                }else{
                    echo -3;
                }
                break;
            case 'forgot':
                $auth = new AuthUser();
                if($auth->forgot($_POST['username'],$mysqli)){
                    echo 1;
                }else{
                    echo -2;
                }
                break;
            case 'reset':
                $auth = new AuthUser();
                if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    if($auth->updateLastLogin($user_info['user_id'],$mysqli)){
                            if($auth->reset($user_info['username'],$user_info['salt'],$_POST['reset'],$mysqli)){
                                echo 1;
                            }else{
                                echo -1;
                            }
                        }else{
                            echo -1;
                        }
                }else{
                    echo 0;
                }
                break;
            case 'settings':
                $auth = new AuthUser();
                if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    //passed user/pass validation
                    if($_POST['username']!=$_POST["newusername"]){
                        //attempting to change username
                        if(!$auth->usernameExists($_POST['newusername'],$mysqli)){
                            //username does not exist
                            if($auth->updateUser($_POST,$mysqli)){
                                echo 1;
                            }else{
                                //some kind of error
                                echo -1;
                            }
                        }else{
                            //attempting to change, but username exists
                            echo -5;
                        }
                    }else{
                        //not attempting to change username
                        if($auth->updateUser($_POST,$mysqli)){
                                echo 1;
                            }else{
                                //some kind of error
                                echo -1;
                            }
                    }
            
                }else{
                    echo -3;
                }
                break;
            case 'search':
                $auth = new AuthUser();
                if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    include('Sign.php');
                    $Sign = new Sign();
                    if($result = $Sign->searchById($mysqli,$_POST['campaign_id'],$_POST['sign_id'])){
                        echo json_encode($result);
                    }else{
                        echo 0;
                    }
                }else{
                    echo -3;
                }
                break;
            
            case 'update':
                $notify=false;
                $auth = new AuthUser();
                if($user_info = $auth->validate($_POST['username'],$_POST['password'],$mysqli)){
                    include('Sign.php');
                    include('Log.php');
                    include('Inventory.php');
                    include('Campaign.php');
                    //update sign
                    $Sign = new Sign();
                    $sign_id = $_POST['sign_id'];
                    if($sign_id==0){
                        
                        if($Sign->allowAdd($mysqli,$_POST['campaign_id'])){
                            $sign_id = $Sign->add($mysqli,$user_info['user_id'],$_POST);
                        }else{
                            echo -6;
                        }
                        
                    }else{
                        $Sign->update($mysqli,$user_info['user_id'],$_POST['sign_id'],$_POST['status'],$_POST['note']);   
                    }
                    echo $sign_id;
                    //add to log
                    $distance = getDistance($_POST['latitude'],$_POST['longitude'],$_POST['userlat'],$_POST['userlng']);
                    
                    $Log = new Log();
                     $Log->add($mysqli,$_POST['campaign_id'],$user_info['user_id'],$user_info['lname'],$user_info['fname'],$_POST['action'],$sign_id,$_POST['status'],$_POST['img'],$distance);
                    
                    //update sign inventory
                    if($_POST['inventory_sign_num']>0 && ($_POST['action']=='Sign Placed' || $_POST['action']=='Sign Replaced' || $_POST['action']=='Sign Fixed' || $_POST['action']=='Sign Recovered')){
                        $_POST['status']=='Recovered'?$sign='-':$sign='+';
                        $Inventory = new Inventory();
                        $Inventory->debit($mysqli,$_POST['inventory_sign'],$_POST['inventory_sign_num'],$sign);
                    }
                    //update material inventory
                    if($_POST['inventory_material_num']>0 && ($_POST['action']=='Sign Placed' || $_POST['action']=='Sign Replaced' || $_POST['action']=='Sign Fixed' || $_POST['action']=='Sign Recovered')){
                        $_POST['status']=='Recovered'?$sign='-':$sign='+';
                        $Inventory = new Inventory();
                        $Inventory->debit($mysqli,$_POST['inventory_material'],$_POST['inventory_material_num'],$sign);
                    }
                    
                    //notifications
                    $Campaign = new Campaign();
                    $Camp = $Campaign->getOneById($mysqli, $_POST['campaign_id']);
                    if($Camp['notifications']==1 && ($_POST['inventory_material_num']>0 || $_POST['inventory_sign_num']>0)  && ($_POST['action']=='Sign Replaced' || $_POST['action']=='Sign Fixed' || $_POST['action']=='Sign Recovered')){
                        //notifications are turned on
                        $alerts = $Inventory->getInventoriesBelowThreshohold($mysqli,$_POST['campaign_id'],$Camp['num_notify']);
                        if(sizeof($alerts)>0){
                        $list='';
                        for($i=0;$i<sizeof($alerts);$i++){
                           $list.= $alerts[$i]['name'].":".$alerts[$i]['num']." units\n"; 
                        }
                                $subject = 'Reset Password';
                                $to = $username;
                                $headers = "From: SignTrack App <noreply@signtrackapp.com>" . "\n" .
                                "Reply-To: noreply@signtrackapp.com";
                                $message = "This is just a notice that you are below the inventory threshold you set up for your account. The following inventory is below ".$Camp['num_notify']." units:\n\r$list\n\rSignTrack App Team";
                                mail($to, $subject, $message, $headers);
                            
                        }
                    }
                }else{
                    echo -3;
                }

//                if($campaign->notifications==1){
//                    $inv = $inventoryTable->getInventoriesBelowThreshohold($campaign->campaign_id, $campaign->num_notify);
//                    //echo 'test1:::';
//                    if(sizeof($inv)>0){
//                              //if below inventory threshold send email
//                            //echo 'starting';
//                            $list='';
//                            for($i=0;$i<sizeof($inv);$i++){
//                                $list.= $inv[$i]['name'].':'.$inv[$i]['num'].' units<br>';
//                            }
//                            //echo $list;
//                            $mail = new Zend_Mail();
//                            $mail->setBodyText("This is just a notice that you are below the inventory threshold you set up for your account. The following inventory is below ".$campaign->num_notify." units:\n\r$list\n\rSign Track Support");
//                            $mail->setBodyHtml("This is just a notice that you are below the inventory threshold you set up for your account. The following inventory is below ".$campaign->num_notify." units:<br><br>$list<br><br>Sign Track Support");
//                            $mail->setFrom(Zend_Registry::get('config')->mim->email->noreply, 'SignTrack App');
//                            $mail->addTo($campaign->email);
//                            $mail->setSubject('Account Access');
//                            $mail->send();
//                           // echo var_dump($mail);
//                    }
//                }
                break;
        }
}
function getDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return (round((($angle * $earthRadius)/1609.34)*10)/10);
}


?>
