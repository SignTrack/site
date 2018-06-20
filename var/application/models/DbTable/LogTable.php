<?php
class Application_Model_DbTable_LogTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'log';
    
    /**
     *
     * @param int $campaign_id
     * @return mixed 
     */
    public function getAll($campaign_id)
    {
        $sql = "SELECT log_id,action,status,name,date_log,user_id,img,distance, DATE_FORMAT(date_log, '%m/%d/%y') AS logdate,sign_id
        FROM log
        WHERE campaign_id='".$campaign_id."'
        ORDER BY date_log DESC
        ";
        $db = $this->getAdapter();
        return $db->fetchAll($sql);
        
    }
    
  /**
   *
   * @param int $sign_id
   * @param int $campaign_id
   * @param int $user_id
   * @param string $name
   * @param string $action
   * @param string $status
   * @return int
   */
    public function add($sign_id,$campaign_id,$user_id,$name,$action,$status)
    {
        $id = $this->insert(array(       
                    'user_id' => $user_id,
                    'name' => $name,
                    'campaign_id'      => $campaign_id,
                    'sign_id'      => $sign_id,
                    'status'         => $status,
                    'action'         => $action,
                    'date_log'   => new Zend_Db_Expr('NOW()')
        ));
        return $id;
    }
    public function getUsersByCampaignId($campaign_id){
        $sql = "SELECT distinct(user_id),name
        FROM log
        WHERE campaign_id='".$campaign_id."' ORDER by name";
        return $this->getAdapter()->fetchAll($sql);
    }
    public function deleteByCampaignId($campaign_id)
    {
        $db = $this->getAdapter();
        return $this->delete("campaign_id = " . $campaign_id);
    }
}

