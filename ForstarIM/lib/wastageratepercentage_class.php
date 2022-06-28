<?php
class WastageRatePercentage
{  
	/****************************************************************
	This class deals with all the operations relating to Wastage Rate Percentage
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WastageRatePercentage(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}

	# Get Record
	function find() 
	{
		$qry	= "select id, local_rate_percentage, wastage_rate_percentage, soft_rate_percentage from m_wastage_rate_percentage where id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateWastageRatePercentRecord($wastageRatePercentId, $localQtyRatePercent, $wastageQtyRatePercent, $softQtyRatePercent) 
	{
		$qry	= " update m_wastage_rate_percentage set  local_rate_percentage='$localQtyRatePercent', wastage_rate_percentage='$wastageQtyRatePercent', soft_rate_percentage='$softQtyRatePercent' where id=$wastageRatePercentId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	#Find wastagePercentage
	function getWastageRatePercentage() 
	{
		$rec = $this->find();
		return (sizeof($rec)>0)?array($rec[1],$rec[2],$rec[3]):false;
	}

}
?>