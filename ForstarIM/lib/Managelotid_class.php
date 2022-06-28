<?php
class Managelotid
{  

	var $databaseConnect;

	function Managelotid(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}
	function fetchAllPagingRecords($offset, $limit,$filterFunctionType)
	{
		$cDate = date("Y-m-d");
		
		$whr = "";
		if ($filterFunctionType!="") $whr .= " a.process_type='$filterFunctionType' ";
		
		
		$orderBy = " id asc";
		$limit	 = " $offset,$limit";
	

		 $qry	= "SELECT a.*,b.process_type as processType FROM m_lotid_generate a 
				   LEFT JOIN m_lotid_process_type b ON a.process_type = b.id ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit)		$qry .= " limit ".$limit ;
		// echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllProcessType()
	{
		$qry	= "SELECT id,process_type FROM m_lotid_process_type";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAlphaPrefix($alpha_code_prefix,$start,$end)
	{
		$qry	= "SELECT lot_id FROM m_lotid_generate 
				   WHERE alpha_code_prefix='".$alpha_code_prefix."' 
				   ORDER BY id DESC LIMIT 0,1";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function addLotId($insertArray)
	{
		$insertStatus = '';
		if(sizeof($insertArray) > 0)
		{
			$qry = "INSERT INTO m_lotid_generate SET ";
			$i = 0;
			foreach($insertArray as $field => $value)
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
			// echo $qry;
			$insertStatus	= $this->databaseConnect->insertRecord($qry);		
			if ($insertStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function getEditRecords($id)
	{
		$qry	= "SELECT a.*,b.process_type as processType FROM m_lotid_generate a 
				   LEFT JOIN m_lotid_process_type b ON a.process_type = b.id 
				   WHERE a.id = ".$id;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function updateLotId($updateArray,$id)
	{
		$updateStatus = '';
		if(sizeof($updateArray) > 0)
		{
			$qry = "UPDATE m_lotid_generate SET ";
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
			// echo $qry;
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $updateStatus;
	}
	function deleteLotId($delIds)
	{
		$qry	= " delete from rm_lot_id where lot_id IN($delIds) ";
		$result	= $this->databaseConnect->delRecord($qry);
		
		$qry	= " delete from m_lotid_generate where id IN($delIds) ";
		$result	= $this->databaseConnect->delRecord($qry);
		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}
}