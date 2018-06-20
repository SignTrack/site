<?php
class Application_Model_DbTable_InventoryTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'inventory';
    
    public function getTypes($campaign_id){
        $db = $this->getAdapter();
        $sql = $this->select()->from($this->_name)
               ->where('campaign_id = ?', $campaign_id);
        return $db->fetchAll($sql);
        
    }
    public function getOneById($inventory_id)
    {
       $db = $this->getAdapter();
       $sql = $db->select()->from('inventory');
       $sql->where("inventory_id=$inventory_id");
       return $db->fetchRow($sql);  
    }
    
    public function add($postvars, $campaign_id)
    {
        $id = $this->insert(array(       
                    'name'      => $postvars['name'],
                    'num_material'         => $postvars['num_material'],
                    'material_id'         => $postvars['material_id'],
                    'inventory_type'         => $postvars['inventory_type'],
                    'num_total'         => $postvars['num_total'],
                    'num_used'   => 0,
                    'price'   => $postvars['price'],
                    'campaign_id'   => $campaign_id
        ));
        return $id;
    }
    public function debitInventory($inventory_id,$quantity,$sign){
         $row = array(
            'num_used'      => new Zend_Db_Expr("num_used $sign $quantity")
        );  
       return $this->update($row, "inventory_id = ".$inventory_id);
    }
   
    public function getInventoriesBelowThreshohold($id,$threshold){
        $db = $this->getAdapter();
        $sql = "SELECT name, num_total-num_used AS num
            FROM inventory
            WHERE campaign_id='$id' && notified=0 && num_total-num_used<$threshold";
        $all = $db->fetchAll($sql);
        $row = array(
            'notified'      => 1
        );  
        
       $this->update($row, "campaign_id='$id' && notified=0 && num_total-num_used<$threshold");
       
        return $all;

    }
    public function edit($postvars)
    {
        $db = $this->getAdapter();
        $id = $postvars['id'];
        
            $row = array(
                'name'      => trim(strip_tags($postvars['name'])),
                'num_total'      => $postvars['num_total'],
                'price'          => $postvars['price'],
                'material_id'          => $postvars['material_id'],
                'notified'          => $postvars['notified'],
                'num_material'          => $postvars['num_material']
            ); 

        
       return $this->update($row, "inventory_id = $id");
    }
    public function getDashboard($id){
        $db = $this->getAdapter();
        $sql = "SELECT inventory.name, inventory.inventory_type,inventory.material_id,inventory.inventory_id, inventory.num_material, inventory.num_total, format((inventory.num_used * inventory.price),2) AS money_used, format(((inventory.num_total-inventory.num_used) * inventory.price),2) AS money_inventory, format(inventory.num_total-inventory.num_used,0) as num_remaining,format(inventory.num_used/inventory.num_total*100,0) AS fraction,inventory.price,material.name as material_name
FROM inventory
LEFT OUTER JOIN inventory material
ON(material.inventory_id=inventory.material_id)
WHERE inventory.campaign_id=".$id;
        return $db->fetchAll($sql);
    }
    public function deleteByCampaignId($campaign_id)
    {
        $db = $this->getAdapter();
        return $this->delete("campaign_id = " . $campaign_id);
    }
}

