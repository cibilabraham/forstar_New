<?php
class Report
{  
	/****************************************************************
	This class deals with all the operations relating to Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Report(&$databaseConnect)
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
		 $query;
		return $this->databaseConnect->getRecords($query);
	}
	
	function getMaxEquipment($id)
	{
		$returnVal = '';
		$sql = "SELECT max_equipment FROM t_rmprocurmentorderentries WHERE rmProcurmentOrderId = ".$id;
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
	
	function getEquipmentIssued($id)
	{
		$returnVal = '';
		$sql = "SELECT equipment_issued FROM t_rmprocurmentorderentries WHERE rmProcurmentOrderId = ".$id;
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
	function getDifference($id)
	{
		$returnVal = '';
		$sql = "SELECT difference FROM t_rmprocurmentorderentries WHERE rmProcurmentOrderId = ".$id;
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
	
	function getChemicalReq($id)
	{
		$returnVal = '';
		$sql = "SELECT chemical_required FROM t_rmprocurmentorderentries WHERE rmProcurmentOrderId = ".$id;
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
	
	function getChemicalIssued($id)
	{
		$returnVal = '';
		$sql = "SELECT chemical_issued FROM t_rmprocurmentorderentries WHERE rmProcurmentOrderId = ".$id;
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
	
	function getDriverName($id)
	{
		$returnVal = '';
		$sql = "SELECT b.name_of_person FROM t_rmprocurmentorderentries a left join m_driver_master b on a.driver_Name=b.id WHERE rmProcurmentOrderId = ".$id;
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
	function getVehicleNumber($id)
	{
		$returnVal = '';
		$sql = "SELECT b.vehicle_number FROM t_rmprocurmentorderentries a left join m_vehicle_master b on a.vehicle_No=b.id WHERE rmProcurmentOrderId = ".$id;
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
	
	function getEquipmentName($id)
	{
		$returnVal = '';
		$sql = "SELECT b.name_of_equipment FROM t_rmprocurmentorderentries a left join m_harvesting_equipment_master b on a.equipment_Name=b.id WHERE rmProcurmentOrderId = ".$id;
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
	
	function getChemicalName($id)
	{
		$returnVal = '';
		$sql = "SELECT b.chemical_name FROM t_rmprocurmentorderentries a left join m_harvesting_chemical_master b on a.chemical=b.id WHERE rmProcurmentOrderId = ".$id;
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
	
	
	
	
	function getgradetype($id)
	{
		$returnVal = '';
		 		//$sql = "SELECT grade_count FROM t_rmweightaftergradingdetails  LEFT JOIN weighment_data_sheet_grade_count on (gradeType=id)  WHERE gradeID = ".$id;
		$sql = "SELECT  b.grade_count FROM t_rmweightaftergradingdetails a LEFT JOIN weighment_data_sheet_grade_count b on (a.gradeType=b.id)  WHERE a.gradeID = ".$id;
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