<?php
class Report_vel
{  
	/****************************************************************
	This class deals with all the operations relating to Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Report_vel(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function fetchAllProcurmentMenus()
	{
		$qry	= "select id, name from function where pmenu_id='24' order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function findCompany($billingCompanyId)
	{
		$qry = "select name from m_billing_company where id=$billingCompanyId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function getQuery($qry)
	{
		$query = $qry;
		return $this->databaseConnect->getRecords($query);
	}
	function getWeight($id)
	{
		$returnVal = '';
		$sql = "SELECT weight FROM t_rmweightaftergradingdetails WHERE gradeID = ".$id;
		$result = $this->databaseConnect->getRecords($sql);
		if(sizeof($result) > 0)
		{
			foreach($result as $res)
			{
				if($returnVal == '')
				{
					$returnVal.= $res[0];
				}
				else
				{
					$returnVal.= '<br/>'.$res[0];
				}
			}
		}
		return $returnVal;
	}
	function getMultipleFields($fieldNameSelect,$tableName,$id)
	{
		$returnVal = '';
		$sql = "SELECT ".$fieldNameSelect." FROM ".$tableName." WHERE main_id = ".$id;
		$result = $this->databaseConnect->getRecords($sql);
		if(sizeof($result) > 0)
		{
			foreach($result as $res)
			{
				if($returnVal == '')
				{
					$returnVal.= $res[0];
				}
				else
				{
					$returnVal.= '<br/>'.$res[0];
				}
			}
		}
		return $returnVal;
	}
}
?>