<?php
class AuthUser
{
    private $_siteKey;
    public function __construct()
    {
        
    }

    public function forgot($username,$mysqli){
        if ($stmt = $mysqli->prepare("SELECT salt FROM user WHERE username=?")) {
            //get salt first
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($salt);
            if($stmt->fetch()){
                    $query = $mysqli->stmt_init();
                    $temp_pass = $this->generateRandomString(6);
                    $hash = sha1($temp_pass.$salt);
                    $stmt->close();
                    if ($query->prepare("UPDATE user SET password=?,reset=1 WHERE username=?")) {
                    $query->bind_param("ss", $hash, $username);
                    if($result = $query->execute()){
                        //send email
                        $subject = 'Reset Password';
                        $to = $username;
                        $headers = "From: SignTrack App <noreply@signtrackapp.com>" . "\n" .
                        "Reply-To: noreply@signtrackapp.com";
                        $message = "Hello,\nA request has been made to reset your password for SignTrack App. Please use the following temporary password to login:\n\rTemporary Password: ".$temp_pass."\n\rThank You,\nSignTrack App Team";
                        mail($to, $subject, $message, $headers);
                        return true;
                    }
                }   
            }
        }
        return false;
    }
    public function validate($username,$password,$mysqli){
  
        if ($stmt = $mysqli->prepare("SELECT salt FROM user WHERE username=?")) {
            //get salt first
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($salt);
            if($stmt->fetch()){
                $stmt->close();
                $query = $mysqli->stmt_init();
                //get user using username and hash
                if ($query->prepare("SELECT user_id,fname,lname,username,phone,salt,reset FROM user WHERE username=? && password=?")) {
                    $hash = sha1($password.$salt);
                    $query->bind_param("ss", $username, $hash);
                    if($result = $query->execute()){
                        //return user information
                        $result = $query->get_result();
                        return $result->fetch_array(MYSQLI_ASSOC); 
                        $stmt->close();
                    }
                }   
            
            }
        }
        $stmt->close();
        return false;
    }
    public function reset($username,$salt,$reset,$mysqli){
            $query = $mysqli->stmt_init();
                    $hash = sha1($reset.$salt);
                    if ($query->prepare("UPDATE user SET password=?, reset=0 WHERE username=?")) {
                    $query->bind_param("ss", $hash, $username);
                    if($result = $query->execute()){
                        return true;
            }   
        }
        return false;
    }
    public function updateUser($user,$mysqli){
            $query = $mysqli->stmt_init();
                    if ($query->prepare("UPDATE user SET fname=?,lname=?,username=?,phone=? WHERE username=?")) {
                    $query->bind_param("sssss", $user['fname'],$user['lname'],$user['newusername'],$user['phone'],$user['username']);
                    if($result = $query->execute()){
                        return true;
            }   
        }
        return false;
    }
    public function usernameExists($username,$mysqli){

         if ($stmt = $mysqli->prepare("SELECT user_id FROM user WHERE username=?")) {
            //get salt first
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($salt);
            if($stmt->fetch()){
                return true;
                }
                else{
                    return false;
                }
            }
               
        return false;
    }
    public function updateLastLogin($user_id,$mysqli){
        $query = $mysqli->stmt_init();
                    if ($query->prepare("UPDATE user_campaigns SET date_login=NOW() WHERE user_id=?")) {
                    $query->bind_param("i", $user_id);
                    if($result = $query->execute()){
                        return true;
            }   
    }
    }
    public function generateRandomString($length = 4) {
        $characters = '123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
?>
