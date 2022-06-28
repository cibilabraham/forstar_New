<?php
Class WeighmentDataSheet{

	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WeighmentDataSheet(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect;
	}
	function getAllWeighmentDataSheet()
	{
		 $qry	= "select a.id,i.lot_Id,a.data_sheet_sl_no,a.data_sheet_date,d.chemical_name,e.pond_name,
					   b.gatePass,j.name,l.name,k.name,f.name as supplier,g.name as location,
				       e.pond_qty,e.pond_size,e.address,h.name as state,e.district,e.taluk,e.village,
					   e.registration_expiry_date,c.chemical_issued,c.difference,i.procurment_Gate_PassId,
					   e.id as pond_id,i.supplier_Challan_No,i.date_Of_Entry,m.seal_number from weighment_data_sheet a 
					   left join t_rmreceiptgatepass i on i.lot_Id = 
					   (select new_lot_Id from t_unittransfer where id=a.rm_lot_id) 
					   left join t_rmprocurmentorder b on i.procurment_Gate_PassId = b.id 
					   left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
					   left join m_harvesting_chemical_master d on d.id = c.chemical 
					   left join m_pond_master e on e.id=a.pond_id 
					   left join supplier f on f.id = e.supplier 
					   left join m_landingcenter g on g.id = e.location 
					   left join m_state h on h.id = e.state 
					   left join m_employee_master j on j.id = a.purchase_supervisor 
					   left join m_employee_master k on k.id = a.receiving_supervisor 
					   left join m_plant l on l.id = a.received_at_unit 
					   left join m_seal_master m on m.id=a.gate_pass 
					   group by a.id ";
					   
					   
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllLotIds()
	{
		$qry	= "select id,new_lot_Id from t_unittransfer where active='1'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllChemicalDetails()
	{
		$qry	= "select id,chemical_name from m_harvesting_chemical_master ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllGatePassDetails()
	{
		 $qry	= "select a.id,a.gatePass from t_rmprocurmentorder  a right join t_rmreceiptgatepass b on (a.id=b.procurment_Gate_PassId)";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllEmployee()
	{
		$qry	= "select id,name from m_employee_master where active='1'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllUnits()
	{
		$qry	= "select id,name from m_plant ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllProcessCodes()
	{
		$qry	= "select id,code from m_processcode ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllPackageTypes()
	{
		$qry	= "select id,name from m_packagingstructure ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllFishDetails()
	{
		$qry	= "select id,name from m_fish ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllProcessCodeDetails($fishId)
	{
		$qry	= "select id,code from m_processcode where fish_id = '".$fishId."' ";		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		
		// if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		// else if (sizeof($result)==1) $resultArr = array();
		// else

		$resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getProcurementGatePassDetails($rmLotID)
	{
		$qry	= "select a.id,d.chemical_name,e.pond_name,f.name as supplier,g.name as location,
				   e.pond_qty,e.pond_size,e.address,h.name as state,e.district,e.taluk,e.village,
				   e.registration_expiry_date,c.chemical_issued,c.difference,a.procurment_Gate_PassId,
				   e.id as pond_id,a.supplier_Challan_No,a.date_Of_Entry,i.seal_number from t_rmreceiptgatepass a 
				   left join t_rmprocurmentorder b on a.procurment_Gate_PassId = b.id 
				   left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
				   left join m_harvesting_chemical_master d on d.id = c.chemical 
				   left join m_pond_master e on e.id=b.pond_name 
				   left join supplier f on f.id = e.supplier 
				   left join m_landingcenter g on g.id = e.location 
				   left join m_state h on h.id = e.state 
				   left join m_seal_master i on a.in_Seal=i.id
				   where a.lot_Id = (select new_lot_Id from  t_unittransfer where id='".$rmLotID."') ";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getProcurementGateDetails($gassid)
		{
	$qry	= "select a.id,a.supplier_Challan_No,a.date_Of_Entry,h.seal_number from t_rmreceiptgatepass a 
				left join m_seal_master h on(a.in_Seal=h.id)
				where a.procurment_Gate_PassId = '$gassid'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function addData($insertArray)
	{
			
		$insertStatus = '';
		if(sizeof($insertArray) > 0)
		{
			 $qry = "INSERT INTO weighment_data_sheet SET ";
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
			
			$insertStatus	= $this->databaseConnect->insertRecord($qry);		
				
			if ($insertStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();		  
		}
		return $insertStatus;
	}
	function addMultipleData($insertQry)
	{
		$insertStatus	= $this->databaseConnect->insertRecord($insertQry);		
				
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $insertStatus;
	}
	function getEditDatas($id)
	{
		$qry	= "select * from weighment_data_sheet where id=".$id;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getEditDatasMultiple($fieldName,$tableName,$main_id)
	{
		$qry	= "select ".$fieldName." from ".$tableName." where weightment_data_sheet_id=".$main_id;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function deleteMultipleDatas($tableName,$mainID)
	{
		$qry	= " delete from ".$tableName." where  weightment_data_sheet_id in(".$mainID.")";
		$this->databaseConnect->delRecord($qry);
		return true;
	}
	function updateData($updateArray,$id)
	{
		$updateStatus = '';
		if(sizeof($updateArray) > 0)
		{
			$qry = "UPDATE weighment_data_sheet SET ";
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
			
			$updateStatus	= $this->databaseConnect->updateRecord($qry);		
			if ($updateStatus) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();			
			
		}
		return $updateStatus;
	}
	function deleteData($deleteIDS)
	{
		$qry	= " delete from weighment_data_sheet where id in(".$deleteIDS.")";
		$this->databaseConnect->delRecord($qry);
		return true;
	}
	function getWeighmentDataSheetForView($id)
	{
		$qry	= "select a.id,i.lot_Id,a.data_sheet_sl_no,a.data_sheet_date,b.gatePass,e.pond_name,a.pond_details,
				   a.farmer_at_harvest,a.product_species,j.name,m.code,a.grade_count,a.count_code,a.weight,a.soft_percent,
				   a.soft_weight,n.name,a.pkg_nos,a.total_quantity,l.name,k.name,d.chemical_name,
				   a.issued,a.used,a.returned,a.different,a.gatepass_details from weighment_data_sheet a 
					   left join t_rmreceiptgatepass i on i.lot_Id = 
					   (select new_lot_Id from t_unittransfer where id=a.rm_lot_id) 
					   left join t_rmprocurmentorder b on i.procurment_Gate_PassId = b.id 
					   left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
					   left join m_harvesting_chemical_master d on d.id = c.chemical 
					   left join m_pond_master e on e.id=a.pond_id 
					   left join supplier f on f.id = e.supplier 
					   left join m_landingcenter g on g.id = e.location 
					   left join m_state h on h.id = e.state 
					   left join m_employee_master j on j.id = a.purchase_supervisor 
					   left join m_employee_master k on k.id = a.receiving_supervisor 
					   left join m_plant l on l.id = a.received_at_unit 
					   left join m_processcode m on m.id = a.product_code 
					    left join m_packagingstructure n on a.package_type = n.id 
					   where a.id = ".$id." 
					   group by a.id ";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getMultipleFields($fieldNameSelect,$tableName,$id)
	{
		
		$sql = "SELECT ".$fieldNameSelect." FROM ".$tableName." WHERE main_id = ".$id;
		$result = $this->databaseConnect->getRecords($sql);
		return $result;
	}
	
	function getMultipleFieldsNew($qry)
	{
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
}