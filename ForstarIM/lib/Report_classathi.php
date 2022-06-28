<?php
class Reportathi
{  
	/****************************************************************
	This class deals with all the operations relating to Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Reportathi(&$databaseConnect)
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
	
	function describe($tableName)
	{
		$qry	= "SELECT column_name FROM information_schema.columns WHERE table_name = '$tableName'";
		//echo $qry;
		// $result	=	$this->databaseConnect->getRecords($qry);
		// return $result;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[0];
		}
		return $resultArr;
	}
	
	function getQuery($query)
	{
		 $qry	=$query;
		
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
	
	function fetchDriverName($id)
	{
		$returnVal = '';
		$sql = "select b.name_of_person from t_rmprocurmentorderentries a left join m_driver_master b  on (a.driver_Name=b.id) where a.rmProcurmentOrderId= ".$id;
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
	function fetchvehicleName($id)
	{
		$returnVal = '';
		$sql = "select b.vehicle_number from t_rmprocurmentorderentries a left join m_vehicle_master b on (a.vehicle_No=b.id) where a.rmProcurmentOrderId= ".$id;
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
	function fetchequipmentName($id)
	{
		$returnVal = '';
		$sql = "select b.name_of_equipment from t_rmprocurmentorderentries a left join m_harvesting_equipment_master b on  (a.equipment_Name=b.id)  where a.rmProcurmentOrderId= ".$id;
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
	function fetchequipmentmax($id)
	{
		$returnVal = '';
	 	$sql = "select max_equipment from t_rmprocurmentorderentries   where rmProcurmentOrderId= ".$id;
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
	function fetchequipmentissuedmax($id)
	{
		$returnVal = '';
		$sql = "select equipment_issued from t_rmprocurmentorderentries   where rmProcurmentOrderId= ".$id;
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
	function fetchdifference($id)
	{
		$returnVal = '';
		$sql = "select difference from t_rmprocurmentorderentries  where rmProcurmentOrderId= ".$id;
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
	function fetchchemical($id)
	{
		$returnVal = '';
		$sql = "select b.chemical_name from t_rmprocurmentorderentries a left join m_harvesting_chemical_master b on  (a.chemical=b.id)  where a.rmProcurmentOrderId= ".$id;
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
	function fetchchemicalrequired($id)
	{
		$returnVal = '';
		$sql = "select chemical_required from t_rmprocurmentorderentries  where rmProcurmentOrderId= ".$id;
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
	function fetchchemicalissued($id)
	{
		$returnVal = '';
		$sql = "select chemical_issued from t_rmprocurmentorderentries  where rmProcurmentOrderId= ".$id;
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