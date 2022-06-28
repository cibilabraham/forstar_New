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
	{	$qry	= "select id,lot_Id from `t_rmreceiptgatepass` where active='1'";
		//$qry	= "select id,new_lot_Id from t_unittransfer where active='1'";
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
	function getProcurementGatePassDetails($gatePass)
	{
		// $qry	= "select a.id,d.chemical_name,e.pond_name,f.name as supplier,g.name as location,
				   // e.pond_qty,e.pond_size,e.address,h.name as state,e.district,e.taluk,e.village,
				   // e.registration_expiry_date,c.chemical_issued,c.difference,a.procurment_Gate_PassId,
				   // e.id as pond_id,a.supplier_Challan_No,a.date_Of_Entry,i.seal_number from t_rmreceiptgatepass a 
				   // left join t_rmprocurmentorder b on a.procurment_Gate_PassId = b.id 
				   // left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
				   // left join m_harvesting_chemical_master d on d.id = c.chemical 
				   // left join m_pond_master e on e.id=b.pond_name 
				   // left join supplier f on f.id = e.supplier 
				   // left join m_landingcenter g on g.id = e.location 
				   // left join m_state h on h.id = e.state 
				   // left join m_seal_master i on a.in_Seal=i.id
				   // where a.lot_Id = (select new_lot_Id from  t_unittransfer where id='".$rmLotID."') ";
				   
				  /* $qry	= "select a.id,d.chemical_name,e.pond_name,f.name as supplier,g.name as location,
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
				   where a.lot_Id ='".$rmLotID."'";*/
				   
				   $qry="select supplier_Challan_No,date_Of_Entry,in_Seal from t_rmreceiptgatepass where procurment_Gate_PassId='$gatePass'";
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
		$qry	= "select id,rm_lot_id,data_sheet_sl_no,data_sheet_date,gate_pass,gatepass_details,farmer_at_harvest,purchase_supervisor,receiving_supervisor,supply_area,supplier_group,total_quantity,procurement_gatepass_available,updated_date,created_on,created_by,active from weighment_data_sheet where id=".$id;
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
		$qry="select a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,a.created_by,a.active,b.lot_Id ,c.gate_pass_id,d.name,f.name,g.name,h.supplier_group_name from weighment_data_sheet a 
	left join t_rmreceiptgatepass b on a.rm_lot_id=b.id
	left join m_rm_gate_pass c on a.gate_pass=c.id
	left join m_employee_master d on a.purchase_supervisor=d.id
	left join m_employee_master f on a.receiving_supervisor=f.id
	left join m_landingcenter g on a.supply_area=g.id
	left join m_supplier_group h on a.supplier_group=h.id
	where a.id='$id'";
		// $qry	= "select a.id,i.lot_Id,a.data_sheet_sl_no,a.data_sheet_date,b.gatePass,e.pond_name,a.pond_details,
				   // a.farmer_at_harvest,a.product_species,j.name,m.code,a.grade_count,a.count_code,a.weight,a.soft_percent,
				   // a.soft_weight,n.name,a.pkg_nos,a.total_quantity,l.name,k.name,d.chemical_name,
				   // a.issued,a.used,a.returned,a.different,a.gatepass_details from weighment_data_sheet a 
					   // left join t_rmreceiptgatepass i on i.lot_Id = 
					   // (select new_lot_Id from t_unittransfer where id=a.rm_lot_id) 
					   // left join t_rmprocurmentorder b on i.procurment_Gate_PassId = b.id 
					   // left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
					   // left join m_harvesting_chemical_master d on d.id = c.chemical 
					   // left join m_pond_master e on e.id=a.pond_id 
					   // left join supplier f on f.id = e.supplier 
					   // left join m_landingcenter g on g.id = e.location 
					   // left join m_state h on h.id = e.state 
					   // left join m_employee_master j on j.id = a.purchase_supervisor 
					   // left join m_employee_master k on k.id = a.receiving_supervisor 
					   // left join m_plant l on l.id = a.received_at_unit 
					   // left join m_processcode m on m.id = a.product_code 
					    // left join m_packagingstructure n on a.package_type = n.id 
					   // where a.id = ".$id." 
					   // group by a.id ";
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
	
/////////////////**********************************************************************///////////////////////////////////////	
	
	
	
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
	
	
	
	function filterSpecies($pondId)
	{
	
	$qry="select b.id,b.name from t_phtcertificate a join m_fish b on a.species=b.id where a.pond_Name='$pondId' order by b.name asc";
	//echo $qry;	
		
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	
	
	function filterProcessCode($fishId)
	{
	
	$qry="select b.id,b.code from m_fish a left join m_processcode b on b.fish_id=a.id where a.id='$fishId' order by b.code asc";
	//echo $qry;	
		
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	
	function filterPondList($supplierNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
	 $qry="select id,pond_name from m_pond_master where supplier='$supplierNameId'";
		
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	function fetchAllProcurementGatePass()
	{
	$qry="select id,gate_pass_id from m_rm_gate_pass order by id desc";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllSupplyArea()
	{
		//$qry="SELECT id, name FROM  m_landingcenter ORDER BY name ASC ";
		$qry="select id, name from  m_landingcenter order by name asc ";
		//echo $qry;
		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function chkValidDataSheetId($selDate)
	{
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	
	function getAlphaCode($processType)
	{
		$qry = "select alpha_code from number_gen where type='WC'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}
	
	function checkDataSheetDisplayExist()
	{
	  $qry = "select (count(*)) from weighment_data_sheet";
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		//$qry = "select (count(*)) from t_rmprocurmentorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidDataSheetId($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MG'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxDataSheetId()
	{
	
		$qry = "select data_sheet_sl_no from weighment_data_sheet order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoDataSheetId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate')";
		
		//$qry	= "select number_to from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function addWeightmentProcurementNo($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$supplyArea, $selRMSupplierGroup,$total_quantity,$userId)
	{
		$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,supply_area, supplier_group,total_quantity,procurement_gatepass_available,created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$supplyArea', '$selRMSupplierGroup','$total_quantity',0,Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addWeightmentSupplierProcurementNo($lastId, $supplierName,$pondName,$product_species,$process_code,$count_code,$weight,$soft_precent,$soft_weight)
	{
		$qry	= "insert into t_weightment_data_entries(weightment_data_sheet_id,supplier_name,pond_name,product_species,process_code_id,count_code, weight, soft_per,soft_weight) values('$lastId','$supplierName','$pondName','$product_species','$process_code','$count_code', '$weight','$soft_precent','$soft_weight')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function addWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$procurementGatePass,$gate_pass_details,$farmer_at_harvest,$purchase_supervisor,$total_quantitypro,$procurementAvailable,$userId)
	{
	$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,gate_pass, gatepass_details,farmer_at_harvest,purchase_supervisor,total_quantity,procurement_gatepass_available, created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$procurementGatePass', '$gate_pass_details','$farmer_at_harvest','$purchase_supervisor','$total_quantitypro','$procurementAvailable',Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addWeightmentSupplierProcurementValue($lastId, $supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro)
	{
		$qry	= "insert into t_weightment_data_entries(weightment_data_sheet_id,supplier_name,pond_name,product_species,process_code_id,count_code, weight, soft_per,soft_weight,packaging_type,package_nos) values('$lastId','$supplierNamepro','$pondNamepro','$product_speciespro','$processCodeValue','$count_codepro', '$weightpro','$soft_precentpro','$soft_weightpro','$pkg_typepro','$pkg_nospro')";
			//echo $qry;
				
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			
			if ($insertStatus) {
				$this->databaseConnect->commit();
			} else {
				 $this->databaseConnect->rollback();
			}
			return $insertStatus;
	}
	function addWeightmentEquipmentProcurementValue($lastId, $equipmentNameId,$equipmentIssued,$equipmentReturned,$balanceQty)
	{
		 $qry	= "insert into t_weightment_equipment_entries(weightment_data_sheet_id,equipment_name,equipment_issued,equipment_returned,difference) values('$lastId','$equipmentNameId', '$equipmentIssued','$equipmentReturned','$balanceQty')";
		//echo $qry;
		//die;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function addWeightmentChemicalProcurementValue($lastId, $chemicalNameId,$chemicalIssued,$chemicalUsed,$chemicalReturned,$differenceQty)
	{
		 $qry	= "insert into t_weightment_chemical_entries(weightment_data_sheet_id,chemical_name,chemical_issued,chemical_used,chemical_returned,difference) values('$lastId','$chemicalNameId','$chemicalIssued','$chemicalUsed','$chemicalReturned','$differenceQty')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function getProcurementOrderID($gatepass)
	{
	$qry = "select procurment_id from m_rm_gate_pass where id='$gatepass'";
			//echo $qry;
			$rec = $this->databaseConnect->getRecord($qry);
			//return (sizeof($rec)>0)?1:0;
			//return (sizeof($rec)>0)?$rec[0]:0;
			return $rec;
	}
	function filterPurchaseProList($gatePass)
	{
		$qry="select a.verified,b.id,b.name from t_rmreceiptgatepass a join m_employee_master b on a.verified=b.id where a.procurment_Gate_PassId='$gatePass' order by b.name asc";
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
	function filterSupplierProList($proID)
	{
		$qry="select a.id,a.supplier_id,b.name from t_rmprocurmentsupplier a join supplier b on a.supplier_id=b.id where rmProcurmentOrderId='$proID' order by b.name asc";
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
	function filterPondProList($proID)
	{
		 $qry="select a.id,a.pond_id,b.pond_name from t_rmprocurmentsupplier a join m_pond_master b on a.pond_id=b.id where rmProcurmentOrderId='$proID' order by b.pond_name asc";
		//echo $qry;
		 //$qry="select id,pond_name from m_pond_master where supplier='$supplierNameId'";
		
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
		//return (sizeof($result)>0)?$result[0]:0;
	}
	function filterPondProValue($supplier,$proid)
	{
	$qry="select a.id,a.pond_id,b.pond_name from t_rmprocurmentsupplier a join m_pond_master b on a.pond_id=b.id where a.supplier_name='$supplier' and a.rmProcurmentOrderId='$proid' order by b.pond_name asc";
		
	 //$qry="select id,pond_name from m_pond_master where supplier='$supplier'";
		
		
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
	
	function filterEquipmentProList($proID)
	{
	$qry="select a.id,a.equipment_Name,b.name_of_equipment from t_rmprocurmentequipment a join m_harvesting_equipment_master  b on a.equipment_Name=b.id where rmProcurmentOrderId='$proID' order by b.name_of_equipment asc";
		//echo $qry;
		 //$qry="select id,pond_name from m_pond_master where supplier='$supplierNameId'";
		
		
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
	function filterChemicalProList($proID)
	{
	$qry="select a.id,a.chemical_Name,b.chemical_Name from t_rmprocurmentchemical a join m_harvesting_chemical_master  b on a.chemical_Name=b.id where rmProcurmentOrderId='$proID' order by b.chemical_Name asc";
		//echo $qry;
		 //$qry="select id,pond_name from m_pond_master where supplier='$supplierNameId'";
		
		
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
	function filterEquipmentIssue($equipmentNameId,$proId)
	{
	
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select equipment_issued from t_rmprocurmentequipment where equipment_Name='$equipmentNameId' and rmProcurmentOrderId='$proId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	
	}
	function filterChemicalIssue($chemicalNameId,$proId)
	{
	
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select chemical_issued from t_rmprocurmentchemical where chemical_Name='$chemicalNameId' and rmProcurmentOrderId='$proId'";
		//echo $qry;
		
		//$result = array();
		$result = $this->databaseConnect->getRecord($qry);
		
		return (sizeof($result)>0)?$result[0]:0;
	
	}
	function fetchUnit($rm_lot_id)
	{
	$qry = "select 	lot_Id,unit from t_rmreceiptgatepass where id='$rm_lot_id'";
			//echo $qry;
			$rec = $this->databaseConnect->getRecord($qry);
			//return (sizeof($rec)>0)?1:0;
			//return (sizeof($rec)>0)?$rec[0]:0;
			return $rec;
	}
	function fetchProcessID()
	{
	$qry = "select 	id from m_lotid_process_type where process_type='Fresh'";
			//echo $qry;
			$rec = $this->databaseConnect->getRecord($qry);
			//return (sizeof($rec)>0)?1:0;
			//return (sizeof($rec)>0)?$rec[0]:0;
			return $rec;
	}
	function addUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno,$userId)
	{
	 $qry	= "insert into t_unittransfer(rm_lot_Id,supplier_Details,unit_Name,process_Type,new_lot_Id,first_lot_id, created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$unitVal','$processType','$free_rm_lotId','$free_rm_lotId', Now(),'$userId')";
		//echo $qry;
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		$qry	= "select a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,a.created_by,a.active,b.lot_Id  from weighment_data_sheet a left join t_rmreceiptgatepass b on a.rm_lot_id=b.id where data_sheet_date>='$fromDate' and data_sheet_date<='$tillDate' order by data_sheet_date desc limit $offset, $limit";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplierData($weightmentId)
	{
	 //$qry	= "select a.*,b.name from t_weightment_data_entries a left join m_fish b on a.product_species=b.id where weightment_data_sheet_id='$weightmentId'";
		 $qry	= "select a.*,b.code from t_weightment_data_entries a left join m_processcode b on a.process_code_id=b.id where weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplierDataView($weightmentId)
	{
	 $qry	= "select a.*,b.name,c.name,d.pond_name,f.name_of_equipment,g.code from t_weightment_data_entries a 
			 left join m_fish b on a.product_species=b.id
			left join supplier c on a.supplier_name=c.id
			left join m_pond_master d on a.pond_name=d.id
			left join m_processcode g on a.process_code_id=g.id
			left join m_harvesting_equipment_master f on a.packaging_type=f.id
			 where a.weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getEquipmentDataView($weightmentId)
	{
	 $qry	= "select a.*,b.name_of_equipment from t_weightment_equipment_entries a 
			 left join m_harvesting_equipment_master b on a.equipment_name=b.id
			 where a.weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getChemicalDataView($weightmentId)
	{
	 $qry	= "select a.*,b.chemical_name from t_weightment_chemical_entries a 
			 left join m_harvesting_chemical_master b on a.chemical_name=b.id
			 where a.weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateWeighmentconfirm($weighmentId){
		$qry	= "update weighment_data_sheet set active='1' where id=$weighmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function updateWeighmentReleaseconfirm($weighmentId){
	$qry	= "update weighment_data_sheet set active='0' where id=$weighmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function getWeightmentSupplierProNo($weighmentId)
	{
	 $qry	= "select a.*,b.name,c.name,d.pond_name,f.name_of_equipment from t_weightment_data_entries a 
			 left join m_fish b on a.product_species=b.id
			left join supplier c on a.supplier_name=c.id
			left join m_pond_master d on a.pond_name=d.id
			left join m_harvesting_equipment_master f on a.packaging_type=f.id
			 where a.weightment_data_sheet_id='$weighmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getfilterPondList($supplierNames)
	{
		$qry="SELECT a.supplier,a.id, a.pond_name FROM m_pond_master a JOIN supplier b ON a.supplier = b.id WHERE a.supplier = '$supplierNames'";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getfilterPondSpecies($pondName,$supplierNames)
	{
		$qry="SELECT b.pond_Name,a.id, a.name FROM m_fish a JOIN t_phtcertificate b ON a.id = b.species WHERE b.pond_Name = '$pondName' and b.supplier='$supplierNames' ORDER BY a.name";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getfilterProcessCode($speciesvals)
	{
		//$qry="SELECT b.pond_Name,a.id, a.name FROM m_fish a JOIN t_phtcertificate b ON a.id = b.species WHERE b.pond_Name = '$pondName' ORDER BY a.name";
		$qry="SELECT a.id, a.code FROM m_processcode a JOIN t_weightment_data_entries b ON a.id = b.process_code_id WHERE b.product_species = '$speciesvals' group by a.code";
		//echo $qry;
		
		$result = array();
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function deleteWeightmentGroup($weightmentId)
	{
		$qry	= " delete from weighment_data_sheet where id=$weightmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	# Delete a rmProcurment Supplier  
	function deleteWeightmentSupplier($weightmentId)
	{
		$qry	= " delete from t_weightment_data_entries where weightment_data_sheet_id=$weightmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
		# Delete a rmProcurment detail  
	function deleteWeightmentChemical($weightmentId)
	{
		$qry	= " delete from t_weightment_chemical_entries where weightment_data_sheet_id=$weightmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function deleteWeightmentEquipment($weightmentId)
	{
		$qry	= " delete from t_weightment_equipment_entries where weightment_data_sheet_id=$weightmentId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function WeightmentEditSupplier($proID)
	{
		$qry="select a.id,a.supplier_id,b.name from t_rmprocurmentsupplier a join supplier b on a.supplier_id=b.id where rmProcurmentOrderId='$proID' order by b.name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function WeightmentEditPond($proID)
	{
		$qry="select a.id,a.pond_id,b.pond_name from t_rmprocurmentsupplier a join m_pond_master b on a.pond_id=b.id where rmProcurmentOrderId='$proID' order by b.pond_name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function WeightmentEditSpecies($pondId)
	{ 		
		$qry="select a.species,b.id,b.name from t_phtcertificate a join m_fish b on a.species=b.id where a.pond_Name='$pondId' order by b.name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function WeightmentEditProcessCode($speciesvals)
	{ 		
		$qry="SELECT a.id, a.code FROM m_processcode a JOIN t_weightment_data_entries b ON a.id = b.process_code_id WHERE b.product_species = '$speciesvals' group by a.code";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function WeightmentEditPackage($proID)
	{
		$qry="select a.id,a.equipment_Name,b.name_of_equipment from t_rmprocurmentequipment a join m_harvesting_equipment_master  b on a.equipment_Name=b.id where rmProcurmentOrderId='$proID' order by b.name_of_equipment asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getWeightmentEditEquipment($weighmentId)
	{
	 $qry="select * from t_weightment_equipment_entries where weightment_data_sheet_id='$weighmentId'";
	// echo	$qry="select a.*,b.name,c.name,d.pond_name,f.name_of_equipment from t_weightment_data_entries a 
			 // left join m_fish b on a.product_species=b.id
			// left join supplier c on a.supplier_name=c.id
			// left join m_pond_master d on a.pond_name=d.id
			// left join m_harvesting_equipment_master f on a.packaging_type=f.id
			 // where a.weightment_data_sheet_id='$weighmentId'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getWeightmentEditChemical($weighmentId)
	{
	 $qry="select * from t_weightment_chemical_entries where weightment_data_sheet_id='$weighmentId'";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function WeightmentEditChemicalVal($proID)
	{
		$qry="select a.id,a.chemical_Name,b.chemical_name from t_rmprocurmentchemical a join m_harvesting_chemical_master b on a.chemical_Name=b.id where a.rmProcurmentOrderId='$proID' order by b.chemical_name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateWeightmentProcurementNo($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$supplyArea, $selRMSupplierGroup,$total_quantity,$weightmentId)
	{
		$qry = " update weighment_data_sheet set rm_lot_id='$rm_lot_id',data_sheet_sl_no='$data_sheet_slno',data_sheet_date='$data_sheet_date',receiving_supervisor='$receiving_supervisor',supply_area='$supplyArea',supplier_group='$selRMSupplierGroup',total_quantity='$total_quantity',gatepass_details='0' where id='$weightmentId'";
		// echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
		//$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,supply_area, supplier_group, procurement_gatepass_available,created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$supplyArea', '$selRMSupplierGroup',0,Now(),'$userId')";
	
	}	
	function updateWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$procurementGatePass,$gate_pass_details,$farmer_at_harvest,$purchase_supervisor,$total_quantitypro,$procurementAvailable,$weightmentId)
	{
		$qry = "update weighment_data_sheet set rm_lot_id='$rm_lot_id',data_sheet_sl_no='$data_sheet_slno',data_sheet_date='$data_sheet_date',receiving_supervisor='$receiving_supervisor',gate_pass='$procurementGatePass',gatepass_details='$gate_pass_details',farmer_at_harvest='$farmer_at_harvest',purchase_supervisor='$purchase_supervisor',total_quantity='$total_quantitypro',procurement_gatepass_available='$procurementAvailable' where id='$weightmentId'";
		 echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
		//$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,gate_pass, gatepass_details,farmer_at_harvest,purchase_supervisor,procurement_gatepass_available, created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$procurementGatePass', '$gate_pass_details','$farmer_at_harvest','$purchase_supervisor','$procurementAvailable',Now(),'$userId')";
	}
	function updateUnittransfer($rm_lot_id,$free_rm_lotId,$unitVal,$processType,$data_sheet_slno,$userId)
	{
	 //$qry	= "insert into t_unittransfer(rm_lot_Id,supplier_Details,unit_Name,process_Type,new_lot_Id,first_lot_id, created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$unitVal','$processType','$free_rm_lotId','$free_rm_lotId', Now(),'$userId')";
		$qry = "update t_unittransfer set supplier_Details='$data_sheet_slno',unit_Name='$unitVal',process_Type='$processType',new_lot_Id='$free_rm_lotId',first_lot_id='$free_rm_lotId' where rm_lot_Id='$rm_lot_id'";
		 echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
		
	}
	function updateWeightmentSupplierProcurementNo($rmId,$supplierName,$pondName,$product_species,$process_code,$count_code,$weight,$soft_precent,$soft_weight)
	{
		//$qry	= "insert into t_weightment_data_entries(weightment_data_sheet_id,supplier_name,pond_name,product_species,count_code, weight, soft_per,soft_weight) values('$lastId','$supplierName','$pondName','$product_species','$count_code', '$weight','$soft_precent','$soft_weight')";
		//echo $qry;
			
		$qry = "update t_weightment_data_entries set supplier_name='$supplierName',pond_name='$pondName',product_species='$product_species',process_code_id='$process_code',count_code='$count_code',weight='$weight',soft_per='$soft_precent',soft_weight='$soft_weight' where id='$rmId'";
		// echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function delProcurementSupplierNo($rmId)
	{
		  $qry = "delete from t_weightment_data_entries where id=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateWeightmentSupplierProcurementVal($rmId,$supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro)
	{
		$qry = "update t_weightment_data_entries set supplier_name='$supplierNamepro',pond_name='$pondNamepro',product_species='$product_speciespro',process_code_id='$processCodeValue',count_code='$count_codepro',weight='$weightpro',soft_per='$soft_precentpro',soft_weight='$soft_weightpro',packaging_type='$pkg_typepro',package_nos='$pkg_nospro' where id='$rmId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function updateEquipmentProcurementValue($rmId,$equipmentNameId,$equipmentIssued,$equipmentReturned,$balanceQty)
	{
	//$qry	= "insert into t_weightment_equipment_entries(weightment_data_sheet_id,equipment_name,equipment_issued,equipment_returned,difference) values('$lastId','$equipmentNameId', '$equipmentIssued','$equipmentReturned','$balanceQty')";
	$qry = "update t_weightment_equipment_entries set equipment_name='$equipmentNameId',equipment_issued='$equipmentIssued',equipment_returned='$equipmentReturned',difference='$balanceQty' where id='$rmId'";
	 //echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}
	function delWeighmentEquipmentRec($rmId)
	{
	  $qry = "delete from t_weightment_equipment_entries where id=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateWeightmentChemicalProcurementValue($rmId,$chemicalNameId,$chemicalIssued,$chemicalUsed,$chemicalReturned,$differenceQty)
	{
	//$qry	= "insert into t_weightment_chemical_entries(weightment_data_sheet_id,chemical_name,chemical_issued,chemical_used,chemical_returned,difference) values('$lastId','$chemicalNameId','$chemicalIssued','$chemicalUsed','$chemicalReturned','$differenceQty')";
	$qry = "update t_weightment_chemical_entries set chemical_name='$chemicalNameId',chemical_issued='$chemicalIssued',chemical_used='$chemicalUsed',chemical_returned='$chemicalReturned',difference='$differenceQty' where id='$rmId'";
		// echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function delWeighmentChemicalRec($rmId)
	{
	  $qry = "delete from t_weightment_chemical_entries where id=$rmId";
		// die;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
}