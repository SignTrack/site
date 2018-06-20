<?php
class Application_Model_DbTable_UserCampaignsTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'user_campaigns';
    
    public function hasCampaigns($user_id,$campaign_id){

        $sql = $this->select()->from($this->_name)
               ->where('user_id = ?', $user_id)
               ->where('campaign_id = ?', $campaign_id);
        if(count($this->fetchAll($sql)->toArray()) > 0){
            return true;
        }else{
            return false;
        }
    }
    public function getUserRole($user_id)
    {
        $db = $this->getAdapter();
        $sql = $this->select()->from($this->_name,array('role','campaign_id'))
                ->where('user_id = ?', $user_id)
               ->where('active = 1');
        return $db->fetchRow($sql);
    } 
    public function deleteById($user_id,$campaign_id){

        return $this->getAdapter()->delete($this->_name, array(
            'user_id = ?' => $user_id,
            'campaign_id = ?' => $campaign_id
        ));
    }
    public function deleteByCampaignId($campaign_id)
    {
        $db = $this->getAdapter();
        return $this->delete("campaign_id = " . $campaign_id);
    }
    
    public function hasOtherCampaigns($user_id){
        $sql = $this->select()->from($this->_name)
               ->where('user_id = ?', $user_id);
        if(count($this->fetchAll($sql)->toArray()) > 0){
            return true;
        }else{
            return false;
        }
    }
   public function updateUserLogin($user_id,$campaign_id)
    {
        $row = array(
                'last_login'      => new Zend_Db_Expr('NOW()')
            ); 
        return $this->update($row, "user_id = '$user_id' & campaign_id = '$campaign_id'");
    } 
 public function addToCampaign($user_id,$campaign_id,$role){
        $id = $this->insert(array(       
                    'user_id'      => $user_id,
                    'campaign_id'  => $campaign_id,
                    'date_added'    => new Zend_Db_Expr('NOW()'),
                    'role'    => $role
        ));
        return $id;
    }
}

