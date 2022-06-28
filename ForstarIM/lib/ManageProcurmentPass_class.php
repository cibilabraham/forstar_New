<?php
class ManageProcurmentPass
{  

	var $databaseConnect;

	function ManageProcurmentPass(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}
	
	function updateProcurmentPass($updateArray,$id)
	{
		$updateStatus = '';
		if(sizeof($updateArray) > 0)
		{
			$qry = "UPDATE manage_procrment_gate_pass SET ";
			$i = 0;
			foreach($updateArray as $field => $value)
			{
				if($i == 0)
				{
					$qry.= $field." = '".$value."' ";
				}
				else
				{
					$qry.= ",".$field." = '".$value."' ";
				}
				$i++;
			}
			$qry.= " WHERE id = ".$id;
			//echo $qry;
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $updateStatus;
	}
	
	function getRecords()
	{
		$qry	= "SELECT * FROM manage_procrment_gate_pass  WHERE id =1 ";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
}