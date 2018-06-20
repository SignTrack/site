<?php
class Application_Model_DbTable_CampaignTable extends Zend_Db_Table_Abstract
{

    protected $_name = 'campaign';

    public function getAll()
    {
        
        $db = $this->getAdapter();
        $sql = $this->select()->from($this->_name)
                ->order('name ASC');
        return $db->fetchAll($sql);
    }
    public function getCampaigns(){
        $db = $this->getAdapter();
        
        $sql = "SELECT campaign.*, DATE_FORMAT(campaign.date_start, '%m/%d/%y') AS start, DATE_FORMAT(campaign.date_end, '%m/%d/%y') AS end,fname,lname,username,phone
            FROM campaign
            RIGHT JOIN user_campaigns
            ON(campaign.campaign_id = user_campaigns.campaign_id)
            RIGHT JOIN user
            ON(user_campaigns.user_id=user.user_id)
            WHERE  user_campaigns.role='Admin'
            GROUP BY campaign.campaign_id
        ";
        //echo $sql;
        return $db->fetchAll($sql);
    }
    public function getOneById($id){
        $db = $this->getAdapter();
        $sql = "SELECT campaign.*, DATE_FORMAT(campaign.date_start, '%d/%m/%y') AS start, DATE_FORMAT(campaign.date_end, '%d/%m/%y') AS end
            FROM campaign WHERE campaign_id='$id'
        ";
        //echo $sql;
        return $db->fetchRow($sql);
    }
    public function updateSetup($postvars,$user_id,$campaign_id){
        $row = array(
                'num_notify'      => trim(strip_tags($postvars['num_notify'])),
                'notifications'      => trim(strip_tags($postvars['notifications'])),
                'date_election'          => trim(strip_tags($postvars['date_election'])),
                'email'          => trim(strip_tags($postvars['email']))
            ); 

        
       return $this->update($row, "campaign_id = $id");
    }
    public function updateMap($postvars,$campaign_id){
        $row = array(
                'nelat'      => $postvars['nelat'],
                'nelng'      => $postvars['nelng'],
                'swlat'          => $postvars['swlat'],
                'swlng'          => $postvars['swlng']
            ); 

       return $this->update($row, "campaign_id = $campaign_id");
    }
    public function edit($postvars,$id){
            $row = array(
                'num_notify'      => trim(strip_tags($postvars['num_notify'])),
                'notifications'      => trim(strip_tags($postvars['notifications'])),
                'date_election'          => trim(strip_tags($postvars['date_election'])),
                'email'          => trim(strip_tags($postvars['email']))
            ); 

        
       return $this->update($row, "campaign_id = $id");
    }
    public function completeSetup($postvars,$campaign_id)
    {
      
            $db = $this->getAdapter();
            $row = array(
                'name'      => trim(strip_tags($postvars['name'])),
                'locale'      => trim(strip_tags($postvars['locale'])),
                'nelat'      => trim($postvars['nelat']),
                'nelng'      => trim($postvars['nelng']),
                'swlat'      => trim($postvars['swlat']),
                'swlng'      => trim($postvars['swlng']),
                'date_election'          => trim(strip_tags($postvars['date_election']))
            ); 

        
       return $this->update($row, "campaign_id = $campaign_id");
        
    }
    public function deleteById($campaign_id)
    {
        $db = $this->getAdapter();
        return $this->delete("campaign_id = " . $campaign_id);
    }
    public function setup($postvars)
    {
            isset($postvars['qty'])?$qty=$postvars['qty']:$qty=6;
            $db = $this->getAdapter();
            $id = $this->insert(array(          
                'name'      => '',
                'locale'      => '',
                'email'          => $postvars['email'],
                'date_start'         => new Zend_Db_Expr('CURDATE()'),
                'date_end'         => new Zend_Db_Expr('CURDATE() + INTERVAL '.$qty.' MONTH'),
                'package'      => $postvars['package_name'],
                'sign_limit'          => $postvars['sign_limit']
            ));
            return $id;
        
    }
    public function getDashboard($id){
        $db = $this->getAdapter();
        $sql = "SELECT persons.*, numbers.num_used, numbers.num_remaining, numbers.num_total
FROM 
	(SELECT
			DATEDIFF(date_end,CURDATE()) AS days,
			FORMAT(SUM(num_used),0) AS num_used,
                        FORMAT(SUM(num_total),0) AS num_total,
			FORMAT(SUM(num_total)-SUM(num_used),0) AS num_remaining
		FROM campaign
		INNER JOIN inventory
		ON(campaign.campaign_id=inventory.campaign_id)
		INNER JOIN user_campaigns
		ON(campaign.campaign_id=user_campaigns.campaign_id)
		INNER JOIN user
		ON(user_campaigns.user_id=user.user_id)
		WHERE inventory_type='Sign' && campaign.campaign_id=$id
			and role = 'admin') as numbers
	
LEFT JOIN

	(SELECT DATEDIFF(date_end,CURDATE()) AS days, 
		email, 
		notifications,
		num_notify,
		DATE_FORMAT(date_election, '%m/%d/%Y') AS date_election,
		date_election AS date_election_raw, 
		DATE_FORMAT(date_end, '%d/%m/%y') AS date_end,COUNT(DISTINCT user.user_id) as num_users
	FROM campaign
	INNER JOIN inventory
	ON(campaign.campaign_id=inventory.campaign_id)
	INNER JOIN user_campaigns
	ON(campaign.campaign_id=user_campaigns.campaign_id)
	INNER JOIN user
	ON(user_campaigns.user_id=user.user_id)
	WHERE inventory_type='Sign' && campaign.campaign_id=$id) as persons
ON numbers.days = persons.days";
        return $db->fetchRow($sql);
    }
}

