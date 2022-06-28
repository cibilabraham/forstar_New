<?php
class VarianceReport
{  
	/****************************************************************
	This class deals with all the operations relating to Report
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function VarianceReport(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	function findCompany($companyName)
	{
	 $qry	= "select name from m_companydetails where id='$companyName'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}
	function fetchAllProcurmentMenus()
	{
		$qry	= "select id, name from function where pmenu_id='24' order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
		function fetchAllSupplierGroupName()
	{
		$qry	= "select id, supplier_group_name from m_supplier_group where active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllPondName()
	{
		$qry	= "select id, pond_name from m_pond_master where active='1' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllLotId()
	{
		$qry	= "select id, lot_Id from t_rmreceiptgatepass";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function filterSupplierList($supplierGroupId)
	{
		$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	function fetchAllCompanyName()
	{
		$qry	= "select id, name from m_companydetails ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getvarianceReport($companyName,$SupplierGroup,$supplierName,$pondName,$rmlotId)
	{
		 $qry = "select a.id,a.gatePass,a.date_of_entry,b.lot_id,b.supplier_Challan_No,b.supplier_Challan_Date,b.date_Of_Entry,c.name,d.supplier_group_name,e.name,e.address,f.pond_name,f.address,g.labours,h.seal_number,i.name from t_rmprocurmentorder a 
		left join t_rmreceiptgatepass b on (a.id=b.procurment_Gate_PassId) 
		left join m_companydetails c on(a.company=c.id) 
		left join m_supplier_group d on(a.suppler_group_name=d.id) 
		left join supplier e on(a.supplier_name=e.id) 
		left join m_pond_master f on(a.pond_name=f.id)
		left join procurement_gate_pass g on(b.verified=g.id) 
		left join m_seal_master h on(b.in_Seal=h.id)
		left join m_plant i on(b.unit=i.id) where a.company='$companyName' and a.suppler_group_name='$SupplierGroup' and a.supplier_name='$supplierName' and a.pond_name='$pondName' and b.lot_Id='$rmlotId'";
///echo $qry;
		$result = array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getvarianceReportentry($entryid)
	{
     $qry = "SELECT a.equipment_issued, a.difference, a.chemical_issued, b.name_of_person, c.vehicle_number, d.name_of_equipment, e.chemical_name
			FROM t_rmprocurmentorderentries a
			LEFT JOIN m_driver_master b ON ( a.driver_Name = b.id ) 
			LEFT JOIN m_vehicle_master c ON ( a.vehicle_No = c.id ) 
			LEFT JOIN m_harvesting_equipment_master d ON ( a.equipment_Name = d.id ) 
			LEFT JOIN m_harvesting_chemical_master e ON ( a.chemical = e.id ) 
			 where a.rmProcurmentOrderId='$entryid'";
///echo $qry;
		$result = array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	
	// function getvarianceReport()
	// {
		// echo $qry = "select a.gatePass,a.date_of_entry,b.lot_id,b.supplier_Challan_No,b.supplier_Challan_Date,b.date_Of_Entry,c.name,d.supplier_group_name,e.name,e.address,f.pond_name,f.address,g.labours,h.seal_number,i.name from t_rmprocurmentorder a left join t_rmreceiptgatepass b on (a.id=b.procurment_Gate_PassId) left join m_companydetails c on(a.company=c.id) left join m_supplier_group d on(a.suppler_group_name=d.id) left join supplier e on(a.supplier_name=e.id) left join m_pond_master f on(a.pond_name=f.id)
		// left join procurement_gate_pass g on(b.verified=g.id) left join m_seal_master h on(b.in_Seal=h.id)
		// left join m_plant i on(b.unit=i.id)";
/////echo $qry;
		// $result = array();
		// $result	=	$this->databaseConnect->getRecords($qry);
		// return $result;
	// }
	
	
}
?>