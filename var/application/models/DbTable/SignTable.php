<?php
class Application_Model_DbTable_SignTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'sign';
    
    
    public function getAll($campaign_id,$is_over)
    {
        if($is_over){
            $sql = "SELECT sign.sign_id,sign.inventory_id,sign.user_id,sign.address,sign.lat,sign.lng,sign.note,'Recover' AS status,sign.date_last,DATE_FORMAT(date_last, '%m/%d/%y') AS last, CONCAT(lname, ', ', fname) as name, num_material, material_id
        FROM sign
        LEFT JOIN user
        ON(sign.user_id=user.user_id)
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE inventory.campaign_id='".$campaign_id."' && status!='Deactivated' && status!='Recovered'";
        }else{
            $sql = "SELECT sign.*,DATE_FORMAT(date_last, '%m/%d/%y %l:%i%p') AS last, CONCAT(lname, ', ', fname) as name, num_material, material_id
        FROM sign
        LEFT JOIN user
        ON(sign.user_id=user.user_id)
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE inventory.campaign_id='".$campaign_id."' && status!='Deactivated' && status!='Recovered'";
        }
        
        $db = $this->getAdapter();
        return $db->fetchAll($sql);
        
    }

    public function getOneById($id)
    {
$db = $this->getAdapter();
        $sql = $this->select()->from($this->_name,array('address', 'status', 'user_id', 'inventory_id'))
             ->setIntegrityCheck(false) // VERY IMPORTANT TO TURN IT DOWN
             ->joinLeft('inventory', "sign.inventory_id = inventory.inventory_id", array('campaign_id'))
             ->where("sign.sign_id=$id");
        return $db->fetchRow($sql);             
    }
    public function updateStatus($id,$status){

        $row = array(
            'status'      => $status,
            'date_last'   => new Zend_Db_Expr('NOW()'),
        );  
       return $this->update($row, "sign_id = $id");
    }
    public function edit($postvars){
        
        $row = array(
            'status'      => $postvars['status'],
            'note' => $postvars['note'],
//            'address'      => $postvars['address'],
            'user_id'      => $postvars['user_id'],
            'date_last'   => new Zend_Db_Expr('NOW()'),
            'inventory_id' => $postvars['sign_id']
        );  
       return $this->update($row, "sign_id = ".$postvars['id']);
    }
    public function unassignDeletedId($user_id){
        
        $row = array(
            'user_id'      => 0
        );  
       return $this->update($row, "user_id = ".$user_id);
    }
    
    
    /**
     *
     * @param int $sign_id
     * @param int $user_id
     * @return bool 
     */
    public function editAssigned($sign_id,$user_id){
  
        $row = array(
            'user_id'      => $user_id
        );  
       return $this->update($row, "sign_id = ".$sign_id);
    }
    public function add($postvars, $addr, $note)
    {
        $id = $this->insert(array(       
                    'address' => $addr,
                    'user_id'      => $postvars['user_id'],
                    'lat'         => $postvars['lat'],
                    'lng'         => $postvars['lng'],
                    'note'         => $note,
                    'inventory_id'   => $postvars['inventory_id'],
                    'date_last'   => new Zend_Db_Expr('NOW()'),
                    'status'  => $postvars['status']
        ));
        return $id;
    }
    public function populate($postvars, $addr,$status)
    {
        $id = $this->insert(array(       
                    'address' => $addr,
                    'user_id'      => $postvars['user_id'],
                    'lat'         => $postvars['lat'],
                    'lng'         => $postvars['lng'],
                    'inventory_id'   => $postvars['inventory_id'],
                    'date_last'   => new Zend_Db_Expr('NOW()'),
                    'status'  => $status
        ));
        return $id;
    }
    public function getSignsUsed($id){
        $db = $this->getAdapter();
        $sql = "SELECT FORMAT(COUNT(sign_id),0) AS num_signs
FROM sign
LEFT JOIN inventory
ON(sign.inventory_id=inventory.inventory_id)
WHERE sign.status!='Deactivated' &&  sign.status!='Recovered' && campaign_id=".$id;
        return $db->fetchRow($sql);
    }
     public function getDashboard($id){
        $db = $this->getAdapter();
        $sql = "SELECT SUM(if(status = 'Place', 1, 0)) AS s_place,FORMAT(SUM(if(status = 'OK', 1, 0)),0) AS s_ok, num_used/num_total*100 AS percent_used, FORMAT(SUM(if(status = 'Replace', 1, 0)),0) AS s_replace, FORMAT(SUM(if(status = 'Fix', 1, 0)),0) AS s_fix, FORMAT(SUM(if(status = 'Recover', 1, 0)),0) AS s_recover, FORMAT(COUNT(sign_id),0) AS num_signs
FROM sign
LEFT JOIN inventory
ON(sign.inventory_id=inventory.inventory_id)
WHERE sign.status!='Deactivated' &&  sign.status!='Recovered' && campaign_id=".$id;
        return $db->fetchRow($sql);
    }
    public function deleteAll($campaign_id){
        $db = $this->getAdapter();
        $sql = "DELETE sign
FROM sign
LEFT JOIN inventory
ON(sign.inventory_id=inventory.inventory_id)
WHERE campaign_id=$campaign_id";
        $rows = $db->query($sql);
    }

}

