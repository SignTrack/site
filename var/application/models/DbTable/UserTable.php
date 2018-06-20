<?php
class Application_Model_DbTable_UserTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';
    
    public function getUserByUsername($username){	
        $db = $this->getAdapter();
            $sql = $this->select()->from($this->_name,'user_id')
                    ->where('username=?',$username);
            return $db->fetchRow($sql);
    }
    public function getUserByEmail($email, $useHashCompare=false)
    {
        $db = $this->getAdapter();
        $sql = $db->select()->from('user');
        if ($useHashCompare){
            $sql->where("MD5(username)=?",$email);
        }else{
            $sql->where("username=?",$email);
        }
        return $db->fetchRow($sql);             
    }

    public function getOneById($user_id)
    {
        $db = $this->getAdapter();
        $sql = $db->select()->from('user');
        $sql->where("user_id=$user_id");
        return $db->fetchRow($sql);             
    }

    public function getAllbyCampaignId($id)
    {
        ($id==0)?$role="&& user_campaigns.role='Master'":$role='';
        $sql = "SELECT user.user_id,fname,lname,username,phone,user_campaigns.role, DATE_FORMAT(date_added, '%m/%d/%y') AS added, DATE_FORMAT(date_login, '%m/%d/%y') AS login
            FROM user_campaigns
            LEFT JOIN user
            ON(user.user_id=user_campaigns.user_id)
            WHERE user_campaigns.active=1
            && user_campaigns.campaign_id=".$id."
            ".$role."
            GROUP BY user.user_id
            ORDER BY lname ASC";
        
        $db = $this->getAdapter();
        return $db->fetchAll($sql);
        
    }

    public function getUserList()
    {
        $db = $this->getAdapter();
        $sql = $this->select()->from($this->_name,array('username', 'user_id'))
                ->order('username ASC');
        return $db->fetchAll($sql);
    }
    public function getUserById($id)
    {
        $db = $this->getAdapter();
        $sql = "SELECT fname,lname,username,phone,campaign_id,role
            FROM user
            LEFT JOIN user_campaigns
            ON(user.user_id=user_campaigns.user_id)
            WHERE user.user_id=$id LIMIT 1";
        
        $user = $db->fetchRow($sql);
        $sql2 = "UPDATE user_campaigns set date_login=NOW() WHERE campaign_id=".$user['campaign_id']." && user_id=".$id;
        $db->query($sql2);
        return $user;
    }
    public function getUserRole($username)
    {
        $db = $this->getAdapter();
        $sql = $this->select()->from($this->_name,'role')
                ->where('username=?',$username);
        $user = $db->fetchRow($sql);
        return $user['role'];
    }
    public function getUserByHash($hash)
    {
        $db = $this->getAdapter();
        $sql = $this->select()
                ->from($this->_name)
                ->where('password=?',$hash);
        return $db->fetchRow($sql);
    }
    public function add($postvars,$pass)
    {
        //if user exists, don't create a new account, just assign them to this campaign as well
            $salt = $this->randomString(20);
            $db = $this->getAdapter();
            $id = $this->insert(array(          
                'username'      => $postvars['username'],
                'phone'      => $postvars['phone'],
                'reset'     => 1,
                'fname'         => trim(strip_tags($postvars['fname'])),
                'lname'         => trim(strip_tags($postvars['lname'])),
                'password'      => sha1($pass.$salt),
                'salt'          => $salt
            ));
            return $id;
        
    }
    public function deleteById($user_id)
    {
        $db = $this->getAdapter();
        return $this->delete("user_id = " . $user_id);
    }
    
    //when deleteing a campaign, delete user without associated campaign
    public function deleteUnassigned(){
        $sql = "DELETE
                    FROM user
                    WHERE user_id NOT IN
                    (
                        SELECT user_id
                        FROM user_campaigns
                    )";
        $this->getAdapter()->query($sql);
    }
    public function edit($postvars)
    {
        $db = $this->getAdapter();
        $id = $postvars['user_id'];
        
//        if($this->getUserByHash($postvars['password'])){
            $row = array(
                'username'      => trim(strip_tags($postvars['username'])),
                'phone'      => trim(strip_tags($postvars['phone'])),
                'fname'          => trim(strip_tags($postvars['fname'])),
                'lname'          => trim(strip_tags($postvars['lname']))
            ); 

        
       return $this->update($row, "user_id = $id");
    }
    public function resetPassword($password,$username){
        $salt = $this->randomString(20);
            $row = array( 
                'password'      => sha1($password.$salt),
                'salt'          => $salt
            ); 
        
       return $this->update($row, "username = '$username'");
    }
    
    public function randomString($length=6){
            $str = '';
            $chars = '01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $maxchars = strlen($chars);
            for($i=0; $i<$length; $i++){			
                    $str .= substr($chars, rand(0, $maxchars), 1);
            }
            return $str;
    }
}

