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
		$qry = "select id,CONCAT(alpha_character,rm_lotid) as lot_Id from t_manage_rm_lotid where id not in  (select lot_id_origin from t_manage_rm_lotid)  and status='0' and active='1' and id not in(select rm_lot_id from weighment_data_sheet)";
		//echo $qry;
		// $qry = "select c.id,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id from `t_rmreceiptgatepass` a 
				// inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				// inner join t_manage_rm_lotid c on b.id = c.receipt_id ";
		// $qry.= " where c.id not in(select rm_lot_id from weighment_data_sheet) ";
		
		
		// $qry	= "select id,lot_Id from `t_rmreceiptgatepass` where active='1' and lot_Id != ''";
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
		// $qry	= "select id,rm_lot_id,data_sheet_sl_no,data_sheet_date,gate_pass,gatepass_details,farmer_at_harvest,purchase_supervisor,receiving_supervisor,supply_area,supplier_group,total_quantity,procurement_gatepass_available,updated_date,created_on,created_by,active from weighment_data_sheet where id=".$id;
		$qry = "select a.id,a.rm_lot_id,a.data_sheet_sl_no,data_sheet_date,b.procurment_Gate_PassId,gatepass_details,farmer_at_harvest,
				purchase_supervisor,receiving_supervisor,supply_area,supplier_group,total_quantity,procurement_gatepass_available,
				updated_date,a.created_on,a.created_by,a.active,
				(SELECT count(*) as total FROM t_rm_receipt_gatepass_supplier WHERE 
				receipt_gatepass_id = a.rm_lot_id) as total,CONCAT(c.alpha_character,c.rm_lotid)   
				from weighment_data_sheet a 
				left join t_rmreceiptgatepass b on b.id = a.rm_lot_id 
				left join t_manage_rm_lotid c on c.id = a.rm_lot_id 
				where a.id=".$id;
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
		$qry="select 		a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,
				   a.created_by,a.active,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id ,d.name from weighment_data_sheet a 
				   left join t_rmreceiptgatepass b on a.rm_lot_id=b.id 
				   left join t_manage_rm_lotid c on a.rm_lot_id = c.id
					left join m_employee_master d on a.receiving_supervisor=d.id
				   where a.id='$id'"; 
		/*$qry="select a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,a.created_by,a.active,b.lot_Id ,c.gate_pass_id,d.name,f.name,g.name,h.supplier_group_name from weighment_data_sheet a 
		left join t_rmreceiptgatepass b on a.rm_lot_id=b.id
		left join m_rm_gate_pass c on a.gate_pass=c.id
		left join m_employee_master d on a.purchase_supervisor=d.id
		left join m_employee_master f on a.receiving_supervisor=f.id
		left join m_landingcenter g on a.supply_area=g.id
		left join m_supplier_group h on a.supplier_group=h.id
		where a.id='$id'";*/
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
		/*if($pondId != '' && $pondId != 0)
		{
			$qry="select b.id,b.name from t_phtcertificate a join m_fish b on a.species=b.id ";
			
				$qry.= "where a.pond_Name='$pondId'";
			
			$qry.= "order by b.name asc";
		}
		else
		{
			$qry	= "select id,name from m_fish where active=1 and source_id != 1 order by name";
		}*/

		$qry	= "select id,name from m_fish where active=1 and source_id != 1 order by name";
	// echo $qry;	
		
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
	function filterSpeciesForEdit($pondId)
	{
		$qry="select b.id,b.name from t_phtcertificate a join m_fish b on a.species=b.id where a.pond_Name='$pondId' order by b.name asc";
		return $this->databaseConnect->getRecords($qry);		
	}
	
	function filterProcessCode($fishId)
	{
	
	$qry="select b.id,b.code from m_fish a left join m_processcode b on b.fish_id=a.id where a.id='$fishId' and b.active='1'  order by b.code asc";
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
	
	function chkValidDataSheetId($selDate,$company,$unit)
	{
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and  type='WC' and auto_generate=1 and 	billing_company_id='$company' and (unitid='$unit' or unitid='0')";
		
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
		//return (sizeof($rec)>0)?true:false;
	}
	
	function getAlphaCode($selDate,$company,$unit)
	{
		$qry = "select alpha_code from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and  type='WC' and auto_generate=1 and 	billing_company_id='$company' and (unitid='$unit' or unitid='0') ";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}
	
	function checkDataSheetDisplayExist($numbergen)
	{
	  $qry = "select (count(*)) from weighment_data_sheet where number_gen_id='$numbergen'";
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		//$qry = "select (count(*)) from t_rmprocurmentorder";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidDataSheetId($selDate,$company,$unit)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and  type='WC' and auto_generate=1 and 	billing_company_id='$company' and (unitid='$unit' or unitid='0')  ";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getmaxDataSheetId($numbergen)
	{
		$qry = "select data_sheet_sl_no from weighment_data_sheet where number_gen_id='$numbergen' order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoDataSheetId($selDate,$company,$unit)
	{
		
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and  type='WC' and auto_generate=1 and 	billing_company_id='$company' and (unitid='$unit' or unitid='0') ";
		
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
		// echo $qry; 
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	
	function addWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$total_quantity,$procurementAvailable,$userId,$number_gen_id)
	{
		$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,total_quantity, procurement_gatepass_available, created_on, created_by,number_gen_id) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$total_quantity','$procurementAvailable',Now(),'$userId','$number_gen_id')";
		// echo $qry;die;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if($insertStatus) 
		{
			$this->databaseConnect->commit();
		} 
		else 
		{
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addWeightmentProcurementValue_backup_04_07_2014($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$procurementGatePass,$gate_pass_details,$farmer_at_harvest,$purchase_supervisor,$total_quantitypro,$procurementAvailable,$userId)
	{
	$qry	= "insert into weighment_data_sheet(rm_lot_id,data_sheet_sl_no,data_sheet_date,receiving_supervisor,gate_pass, gatepass_details,farmer_at_harvest,purchase_supervisor,total_quantity,procurement_gatepass_available, created_on, created_by) values('$rm_lot_id','$data_sheet_slno','$data_sheet_date','$receiving_supervisor','$procurementGatePass', '$gate_pass_details','$farmer_at_harvest','$purchase_supervisor','$total_quantitypro','$procurementAvailable',Now(),'$userId')";
	//echo $qry;
	//die();
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function updateWeightmentProcurementValues($id,$rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$total_quantity,$procurementAvailable,$userId)
	{
		$qry	= "update weighment_data_sheet set rm_lot_id = '$rm_lot_id',data_sheet_sl_no = '$data_sheet_slno',data_sheet_date = '$data_sheet_date',
			   receiving_supervisor = '$receiving_supervisor',total_quantity = '$total_quantity',procurement_gatepass_available = '$procurementAvailable',
			   created_by = '$userId' WHERE id = '$id' ";
		// echo $qry;die;
			
		$insertStatus	=	$this->databaseConnect->updateRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addWeightmentSupplierProcurementValue_backup_04_07_2014($lastId, $supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro)
	{
		$qry	= "insert into t_weightment_data_entries(weightment_data_sheet_id,supplier_name,pond_name,product_species,process_code_id,count_code, weight, soft_per,soft_weight,packaging_type,package_nos) values('$lastId','$supplierNamepro','$pondNamepro','$product_speciespro','$processCodeValue','$count_codepro', '$weightpro','$soft_precentpro','$soft_weightpro','$pkg_typepro','$pkg_nospro')";
			// echo $qry;
				
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			
			if ($insertStatus) {
				$this->databaseConnect->commit();
			} else {
				 $this->databaseConnect->rollback();
			}
			return $insertStatus;
	}
	function addWeightmentSupplierProcurementValue($lastId, $supplierNamepro,$pondNamepro,$procurementCenter,$product_speciespro,$processCodeValue,$quality,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$item_in_lotid)
	{
		$qry	= "insert into t_weightment_data_entries (weightment_data_sheet_id,supplier_name,pond_name,procurement_center,product_species,process_code_id,quality_id,count_code, weight, soft_per,soft_weight,packaging_type,package_nos,item_in_lotid) 
		values('$lastId','$supplierNamepro','$pondNamepro','$procurementCenter','$product_speciespro','$processCodeValue','$quality','$count_codepro', '$weightpro','$soft_precentpro','$soft_weightpro','$pkg_typepro','$pkg_nospro','$item_in_lotid')";
			// echo $qry;
				
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			
			if ($insertStatus) {
				$this->databaseConnect->commit();
			} else {
				 $this->databaseConnect->rollback();
			}
			return $insertStatus;
	}
	function updateWeightmentSupplierProcurementValue($id,$lastId, $supplierNamepro,$pondNamepro,$procurementCenter,$product_speciespro,$processCodeValue,$quality,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$item_in_lotid)
	{
		$qry	= "update t_weightment_data_entries set weightment_data_sheet_id = '$lastId',supplier_name = '$supplierNamepro',pond_name='$pondNamepro',procurement_center='$procurementCenter', product_species='$product_speciespro',process_code_id='$processCodeValue',quality_id='$quality',count_code='$count_codepro',weight='$weightpro',soft_per='$soft_precentpro',soft_weight='$soft_weightpro',item_in_lotid='$item_in_lotid' WHERE id = '$id' ";
			// echo $qry;
				
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
			
			if ($insertStatus) {
				$this->databaseConnect->commit();
			} else {
				 $this->databaseConnect->rollback();
			}
			return $insertStatus;
	}

	function deleteWeightmentSupplierProcurementValue($id)
	{
		$qry	= " delete from  t_weightment_data_entries where id='$id'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
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
		$qry	= "select 		a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,
				   a.created_by,a.active,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id  from weighment_data_sheet a 
				   left join t_rmreceiptgatepass b on a.rm_lot_id=b.id 
				   left join t_manage_rm_lotid c on a.rm_lot_id = c.id  
				   where data_sheet_date>='$fromDate' and data_sheet_date<='$tillDate' order by data_sheet_date desc limit $offset, $limit";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllDateRangeRecords($fromDate, $tillDate)
	{
		$qry	= "select 		a.id,a.rm_lot_id,a.data_sheet_sl_no,a.data_sheet_date,a.gate_pass,a.gatepass_details,a.farmer_at_harvest,a.purchase_supervisor,a.receiving_supervisor,a.supply_area,a.supplier_group,a.total_quantity,a.procurement_gatepass_available,a.updated_date,a.created_on,
				   a.created_by,a.active,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id  from weighment_data_sheet a 
				   left join t_rmreceiptgatepass b on a.rm_lot_id=b.id 
				   left join t_manage_rm_lotid c on a.rm_lot_id = c.id  
				   where data_sheet_date>='$fromDate' and data_sheet_date<='$tillDate' order by data_sheet_date desc";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSupplierData($weightmentId)
	{
	 //$qry	= "select a.*,b.name from t_weightment_data_entries a left join m_fish b on a.product_species=b.id where weightment_data_sheet_id='$weightmentId'";
		 $qry	= "select a.id,a.supplier_name,a.pond_name,a.procurement_center,a.product_species,a.process_code_id,a.quality_id,a.count_code,a.weight,a.soft_per,a.soft_weight,a.packaging_type,a.package_nos,a.pond_active,a.item_in_lotid,b.code from t_weightment_data_entries a left join m_processcode b on a.process_code_id=b.id where weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSupplierDataView($weightmentId)
	{
		$qry	= "select 		a.id,a.weightment_data_sheet_id,a.supplier_name,a.pond_name,a.product_species,a.process_code_id,a.count_code,
			   a.weight,a.soft_per,a.soft_weight,a.packaging_type,a.package_nos,a.pond_active,
			   b.name,c.name,d.pond_name,f.name_of_equipment,g.code from t_weightment_data_entries a 
			 left join m_fish b on a.product_species=b.id
			left join supplier c on a.supplier_name=c.id
			left join m_pond_master d on a.pond_name=d.id
			left join m_processcode g on a.process_code_id=g.id
			left join m_harvesting_equipment_master f on a.packaging_type=f.id
			 where a.weightment_data_sheet_id='$weightmentId'";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		// echo $qry;
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
			$qry	= "select a.id,a.weightment_data_sheet_id,a.supplier_name,a.pond_name,a.product_species,
				   a.process_code_id,a.count_code,a.weight,a.soft_per,a.soft_weight,a.packaging_type,
				   a.package_nos,a.pond_active,b.name,c.name,d.pond_name,f.name_of_equipment,a.quality_id 
				   from t_weightment_data_entries a 
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
		echo $qry="select a.id,a.supplier_id,b.name from t_rmprocurmentsupplier a join supplier b on a.supplier_id=b.id where rmProcurmentOrderId='$proID' order by b.name asc";
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

	function updateWeightmentSupplierProcurementVal($rmId,$supplierNamepro,$pondNamepro,$product_speciespro,$processCodeValue,$count_codepro,$weightpro,$soft_precentpro,$soft_weightpro,$pkg_typepro,$pkg_nospro,$quality_id)
	{
		$qry = "update t_weightment_data_entries set supplier_name='$supplierNamepro',pond_name='$pondNamepro',product_species='$product_speciespro',process_code_id='$processCodeValue',count_code='$count_codepro',weight='$weightpro',soft_per='$soft_precentpro',soft_weight='$soft_weightpro',packaging_type='$pkg_typepro',package_nos='$pkg_nospro',quality_id='$quality_id' where id='$rmId'";
		echo $qry;
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
	
	function getTableRowBasedRmLotId($rm_gate_pass_id)
	{
		
		/*SELECT a.supplier_id,d.name,a.pond_id,e.pond_name,(select group_concat(id,'$$',name) from m_fish where id in (select species from t_phtcertificate where pond_Name = a.pond_id)) as species from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id left join supplier d on d.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id where c.id='".$rm_gate_pass_id."'*/
		//WHERE id='".$rm_gate_pass_id."'";
		
	//echo $qry;
		$qry1="select lot_id_origin from t_manage_rm_lotid where id='$rm_gate_pass_id'";
		$result1	=	$this->databaseConnect->getRecord($qry1);
		
		if($result1[0]=='' || $result1[0]=='0')
		{
			//function 1
			$supplyDet=$this->getSupplyDetail($rm_gate_pass_id);
			$resultval=$supplyDet;
		}
		else
		{
			//function 2
			$detail=$this->getTableRowBasedRmLotId($result1[0]);
			$rm_gate_pass_id=$result1[0];
			$resultval=$detail;
		}

		return $resultval;

		
	}

	function getSupplyDetail($rm_gate_pass_id)
	{
		//$result=array();	
		$qry = "SELECT a.supplier_id,d.name,a.pond_id,e.pond_name,a.landing_center_id,f.name,'procurement' as status from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id left join supplier d on d.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id 
		left join m_landingcenter f ON f.id = a.landing_center_id  where c.id='".$rm_gate_pass_id."' union SELECT a.supplier_id,c.name,a.pond_id,e.pond_name,a.landingcenter_id,f.name,'notprocurement' as status from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join supplier c on c.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id left join m_landingcenter f ON f.id = a.landingcenter_id  where b.rmlot_main_id='".$rm_gate_pass_id."' and b.receipt_id='0'";//WHERE id='".$rm_gate_pass_id."'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getSupplierBasedOnRmLotId($rm_gate_pass_id)
	{
		$qry = "SELECT a.supplier_id,d.name,a.pond_id,e.pond_name,(select group_concat(id,'$$',name) from m_fish where id in (select species from t_phtcertificate where pond_Name = a.pond_id)) as species from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id left join supplier d on d.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id where c.id='".$rm_gate_pass_id."' union SELECT a.supplier_id,c.name,a.driver,a.labours,a.out_seal from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join supplier c on c.id=a.supplier_id where b.rmlot_main_id='".$rm_gate_pass_id."' and b.receipt_id='0'";//WHERE id='".$rm_gate_pass_id."'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getPondBasedOnRmLotId($rm_gate_pass_id)
	{
		$qry = "SELECT a.supplier_id,d.name,a.pond_id,e.pond_name,(select group_concat(id,'$$',name) from m_fish where id in (select species from t_phtcertificate where pond_Name = a.pond_id)) as species from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id left join supplier d on d.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id where c.id='".$rm_gate_pass_id."' union SELECT a.supplier_id,c.name,a.driver,a.labours,a.out_seal from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join supplier c on c.id=a.supplier_id where b.rmlot_main_id='".$rm_gate_pass_id."' and b.receipt_id='0'";//WHERE id='".$rm_gate_pass_id."'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);

		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[2]] = $v[3];
		}
		return $resultArr;
	}

	function getPondBasedOnRmLotIdAndSupplier($rm_lot_id,$supplier_id)
	{
		$qry = "SELECT a.pond_id,e.pond_name from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id  left join m_pond_master e ON e.id = a.pond_id where c.id='".$rm_lot_id."' and a.supplier_id='$supplier_id' union  SELECT a.pond_id,e.pond_name from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id  left join m_pond_master e ON e.id = a.pond_id where  c.id='".$rm_lot_id."' and a.supplier_id='$supplier_id'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	function getLandBasedOnRmLotIdAndSupplier($rm_lot_id,$supplier_id,$pondid)
	{
		$qry = "SELECT b.landing_center_id,e.name from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id  left join m_landingcenter e ON e.id = b.landing_center_id
		where c.id='".$rm_lot_id."' and a.supplier_id='$supplier_id'";
		if($pondid!="") $qry.=" and b.farm_id='$pondid'";
		$qry.= " union  SELECT b.landing_center_id,e.name from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id  left join m_landingcenter e ON e.id = b.landing_center_id where  c.id='".$rm_lot_id."' and a.supplier_id='$supplier_id'";
		if($pondid!="") $qry.=" and b.farm_id='$pondid'";	
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}


	function getTableRowBasedRmLotId_athi($rm_gate_pass_id)
	{
		$returnVal = array();
		$qry = "SELECT a.receipt_gatepass_number,a.supplier_id,c.receipt_id FROM t_rmreceiptgatepass a ";//WHERE id='".$rm_gate_pass_id."'";
		$qry.= "inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				inner join t_manage_rm_lotid c on b.id = c.receipt_id 
				where c.id = '".$rm_gate_pass_id."' ";
		 //echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		if(sizeof($result) > 0)
		{
			if($result[0] != '')
			{
				// $query = "SELECT a.supplier_id,b.name,c.id,c.pond_name,
						 // (select group_concat(id,'$$',name) from m_fish where id in
						 // (select species from t_phtcertificate where pond_Name = a.pond_id)) as species 
						  // FROM t_rm_receipt_gatepass_supplier a 
				          // LEFT JOIN supplier b ON b.id = a.supplier_id 
						  // LEFT JOIN m_pond_master c ON c.id = a.pond_id 
						  // WHERE a.receipt_gatepass_id = '".$rm_gate_pass_id."'";
				// $query = "SELECT a.supplier_id,b.name,c.id,c.pond_name,
						 // (select group_concat(id,'$$',name) from m_fish where id in
						 // (select species from t_phtcertificate where pond_Name = a.pond_id)) as species 
						  // FROM t_rm_receipt_gatepass_supplier a 
				          // LEFT JOIN supplier b ON b.id = a.supplier_id 
						  // LEFT JOIN m_pond_master c ON c.id = a.pond_id 
						  // LEFT JOIN t_manage_rm_lotid d on a.id = d.receipt_id 
						  // WHERE d.id = '".$rm_gate_pass_id."'";
						  // $qry = "select c.id,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id from `t_rmreceiptgatepass` a 
				// inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				// inner join t_manage_rm_lotid c on b.id = c.receipt_id ";
				
				
				//athi
				//sELECT * FROM `t_rm_receipt_gatepass_supplier` WHERE id =(select receipt_id from t_manage_rm_lotid where id='receipt_id')
				
				if($result[2] != '')
				$query = "SELECT a.supplier_id,b.name,c.id,c.pond_name, (select group_concat(id,'$$',name) from m_fish where id in
						 (select species from t_phtcertificate where pond_Name = a.pond_id)) as species  from  supplier b left join `t_rm_receipt_gatepass_supplier` a on b.id = a.supplier_id
					LEFT JOIN m_pond_master c ON c.id = a.pond_id 
					WHERE a.id in($result[2])";
				
				//velmurugan
				/*$query = "SELECT a.supplier_id,b.name,c.id,c.pond_name,
						 (select group_concat(id,'$$',name) from m_fish where id in
						 (select species from t_phtcertificate where pond_Name = a.pond_id)) as species 
						  FROM t_rm_receipt_gatepass_supplier a 
				          LEFT JOIN supplier b ON b.id = a.supplier_id 
						  LEFT JOIN m_pond_master c ON c.id = a.pond_id 
						  WHERE a.receipt_gatepass_id = (select receipt_gatepass_id  from t_rm_receipt_gatepass_supplier 
						  where id = (select receipt_id from t_manage_rm_lotid where id ='".$rm_gate_pass_id."'))";*/
			}
			else
			{
				$query = "SELECT id,name FROM supplier WHERE id = '".$result[1]."' ";
			}
			$returnVal = $this->databaseConnect->getRecords($query);
		}
		return $returnVal;
	}

	function getAllSpecies()
	{
		$qry	= "select id,name from m_fish where active=1 and source_id != 1 order by name";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllQuality()
	{
		$qry	= "select id,name from m_quality where  active='1' order by name";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllSuppliers()
	{
		$query = "SELECT id,name FROM supplier";
		$result	= $this->databaseConnect->getRecords($query);
		return $result;
	}

	function getMultipleRowForEdit($id)
	{
		$qry = "select a.id,a.supplier_name,b.name,a.pond_name,c.pond_name,a.product_species,d.name,a.process_code_id,e.code,a.quality_id,		f.name,a.count_code,a.weight,a.soft_per,a.soft_weight,a.item_in_lotid,a.procurement_center,lc.name from t_weightment_data_entries a		left join supplier b on a.supplier_name = b.id left join m_pond_master c on a.pond_name = c.id left join m_fish d on				a.product_species = d.id left join m_processcode e on a.process_code_id = e.id left join m_quality f on a.quality_id = f.id left		join m_landingcenter lc on lc.id=a.procurement_center where weightment_data_sheet_id = ".$id;
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	function getChemicalValues($id)
	{
		// $qry	= " select b.chemical_name,required_quantity,issued_quantity,difference_quantity from t_rmprocurmentchemical a 
					// left join m_harvesting_chemical_master b on a.chemical_id = b.id 
					// where a.rmProcurmentOrderId = (select procurment_Gate_PassId from t_rmreceiptgatepass where id = '".$id."') ";
		$qry	= " select b.chemical_name,required_quantity,issued_quantity,difference_quantity from t_rmprocurmentchemical a 
					left join m_harvesting_chemical_master b on a.chemical_id = b.id ";
		$qry.= "  where a.rmProcurmentOrderId = (SELECT a.procurment_Gate_PassId FROM t_rmreceiptgatepass a inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				inner join t_manage_rm_lotid c on b.id = c.receipt_id 
				where c.id = '".$id."' )";
		// $qry.= "  WHERE a.rmProcurmentOrderId = (select receipt_gatepass_id  from t_rm_receipt_gatepass_supplier 
						  // where id = (select receipt_id from t_manage_rm_lotid where id ='".$rm_gate_pass_id."'))";
					// where a.rmProcurmentOrderId = (select procurment_Gate_PassId from t_rmreceiptgatepass where id = '".$id."') ";
		return $this->databaseConnect->getRecords($qry);
	}

	function getEquipmentValues($id)
	{
		// $qry	= " select b.name_of_equipment,required_quantity,issued_quantity,difference_quantity from t_rmprocurmentequipment a 
					// left join m_harvesting_equipment_master b on a.equipment_id = b.id  
					// where a.rmProcurmentOrderId = (select procurment_Gate_PassId from t_rmreceiptgatepass where id = '".$id."') ";
		$qry	= " select b.name_of_equipment,required_quantity,issued_quantity,difference_quantity from t_rmprocurmentequipment a 
					left join m_harvesting_equipment_master b on a.equipment_id = b.id  ";
		$qry.= "  where a.rmProcurmentOrderId = 
				  (SELECT a.procurment_Gate_PassId FROM t_rmreceiptgatepass a 
				  inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				  inner join t_manage_rm_lotid c on b.id = c.receipt_id 
				  where c.id = '".$id."' )";
		// $qry.= "  WHERE a.rmProcurmentOrderId = (select procurment_Gate_PassId from t_rmreceiptgatepass where procurment_Gate_PassId = (select receipt_gatepass_id  from t_rm_receipt_gatepass_supplier 
						  // where id = (select receipt_id from t_manage_rm_lotid where id ='".$rm_gate_pass_id."')))";
					//where a.rmProcurmentOrderId = (select procurment_Gate_PassId from t_rmreceiptgatepass where id = '".$id."') ";
		return $this->databaseConnect->getRecords($qry);
	}

	function checkDuplicate($rmlot)
	{
		$qry	= "SELECT id FROM weighment_data_sheet WHERE  rm_lot_id='$rmlot'";
		return  $this->databaseConnect->getRecord($qry);
	}

	function getPhtCerificateDetail($i,$supplierName,$pondName,$product_species,$weight)
	{	
		$result='';
		$qry	= "SELECT tphtc.id,tphtc.PHTCertificateNo,s.name,mpm.pond_name,mf.name,s.id,mpm.id,mf.id FROM t_phtmonitoring tphtm join t_phtcertificate tphtc on tphtc.id =tphtm.certificate_id left join supplier s on s.id=tphtc.supplier left join m_pond_master mpm on mpm.id=tphtc.pond_Name left join m_fish mf on mf.id=tphtc.species where tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' and tphtc.active='1' and (select balance_quantity from t_phtmonitoring_quantity where pht_certificate_number=tphtc.id and tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' order by pht_certificate_number desc limit 1) is null or (select balance_quantity from t_phtmonitoring_quantity where pht_certificate_number=tphtc.id and tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' order by pht_certificate_number desc limit 1) !=0";
				
		//echo $qry;
		$cerificate=$this->databaseConnect->getRecords($qry);
		
		if(sizeof($cerificate)>0)
		{
			$certificatedropDown = '<select name="certificateNo_0" id="certificateNo_0" onchange="xajax_certificateNo(this.value,0,'.$i.');">';
			$certificatedropDown.= '<option value=""> --Select-- </option>';
			foreach($cerificate as $cerificateDetail)
			{	
				$certificatedropDown.= '<option value="'.$cerificateDetail[0].'">'.$cerificateDetail[1].'</option>';
			}

			$certificatedropDown.= '</select>';

			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr>";
			$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
			$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'  width='40%'>Supplier :</td><td class='listing-item'>$cerificateDetail[2]<input type='hidden' name='supplierId' id='supplierId' value='$cerificateDetail[5]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Pond :</td><td class='listing-item'>$cerificateDetail[3]<input type='hidden' name='pondId' id='pondId' value='$cerificateDetail[6]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Product Species :</td><td class='listing-item'>$cerificateDetail[4]<input type='hidden' name='speciesId' id='speciesId' value='$cerificateDetail[7]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Supply Quantity :</td><td class='listing-item'><input type='hidden' name='supplyQnty' id='supplyQnty' value='$weight'/>$weight</td></tr>";
			$result.="</table>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head' align='center' style='padding:20px 0px 5px 0px'>";
			$result.="<table><tr><td>";	
			$result.="<table id='tblAddCerificateDetail' cellspacing='1' cellpadding='6' bgcolor='#999999' name='tblAddCerificateDetail'>
				<tbody>
				<tr bgcolor='#f2f2f2' align='center'>
				<td class='listing-head' nowrap=''>Certificate No </td>
				<td class='listing-head' nowrap=''>Available Qty</td>
				<td class='listing-head' nowrap=''>Balance Qty </td>
				<td></td>
				</tr>
				<tr id='srow_0' class='whiteRow'>
				<td class='listing-item' align='center' id='allCertificateNo_0'>
				$certificatedropDown
				</td>
				<td class='listing-item' align='center'><input id='availableQnty_0' type='text' style='text-align:right; border:none;' readonly size='15' value='' name='availableQnty_0'>
				</td>
				<td class='listing-item' align='center'><input id='balanceQnty_0' type='text' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='balanceQnty_0'><input id='qntyStatus_0' type='hidden' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_0'></td>
				<td class='listing-item' align='center'><a onclick='setIssuanceCertificateStatus(0);' href='###'>
				<img border='0' style='border:none;' src='images/delIcon.gif' title='Click here to remove this item'>
				</a>
				<input id='sstatus_0' type='hidden' value='' name='sstatus_0'>
				<input id='sIsFromDB_0' type='hidden' value='N' name='sIsFromDB_0'>
				<input id='srmId_0' type='hidden' value='' name='srmId_0'>
				</td>
				</tr>
				</tbody>
				</table>
				</td></tr>
			<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewCertificateTableRow($i);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='certificateSize' name='certificateSize' value='1'/><input type='hidden' id='differenceInValue' name='differenceInValue' value=''/></tr>";
			$result.="</table>";			
			$result.="</td></tr>";
			$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='checkCertificate($i);'  value=' Tag Certificate' name='tagCertificate' id='tagCertificate' ></td></tr>";
			$result.="</table></p>";
		}
		else
		{
			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr><tr bgcolor='#fff'><td colspan='2' class='listing-item'>No certificate with these supplier detail</td></tr></table></p>";
		}
		return $result;
	}

	function getPhtCerificateDetailTag($i,$supplierName,$pondName,$product_species,$weight,$phtTagData)
	{	
		$result='';
		$qry	= "SELECT tphtc.id,tphtc.PHTCertificateNo,s.name,mpm.pond_name,mf.name,s.id,mpm.id,mf.id FROM t_phtcertificate tphtc left join supplier s on s.id=tphtc.supplier left join m_pond_master mpm on mpm.id=tphtc.pond_Name left join m_fish mf on mf.id=tphtc.species WHERE  tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' and tphtc.active='1'";
		//echo $qry;
		$cerificate=$this->databaseConnect->getRecords($qry);
		
		//echo $phtTagData;

		if(sizeof($cerificate)>0)
		{
			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr>";
			$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
			$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'  width='40%'>Supplier :</td><td class='listing-item'>".$cerificate[0][2]."<input type='hidden' name='supplierId' id='supplierId' value='".$cerificate[0][5]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Pond :</td><td class='listing-item'>".$cerificate[0][3]."<input type='hidden' name='pondId' id='pondId' value='".$cerificate[0][6]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Product Species :</td><td class='listing-item'>".$cerificate[0][4]."<input type='hidden' name='speciesId' id='speciesId' value='".$cerificate[0][7]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Supply Quantity :</td><td class='listing-item'><input type='hidden' name='supplyQnty' id='supplyQnty' value='$weight'/>$weight</td></tr>";
			$result.="</table>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head' align='center' style='padding:20px 0px 5px 0px'>";
			$result.="<table><tr><td>";	
			$result.="<table id='tblAddCerificateDetail' cellspacing='1' cellpadding='6' bgcolor='#999999' name='tblAddCerificateDetail'>
				<tbody>
				<tr bgcolor='#f2f2f2' align='center'>
				<td class='listing-head' nowrap=''>Certificate No </td>
				<td class='listing-head' nowrap=''>Available Qty</td>
				<td class='listing-head' nowrap=''>Balance Qty </td>
				<td></td>
				</tr>";
				$j=0;
				$objt=json_decode($phtTagData);
				//printr($objt);
				foreach ($objt->items as $item )
				{
					$certificateId=$item->certificateNo;
					$availbleQnty=$item->availableQnty;
					$balanceQnty=$item->balanceQnty;
					$qntyStatus=$item->qntyStatus;
					//echo "certificateNo=".$certificate;
				
				$result.="<tr id='srow_".$j."' class='whiteRow'>
				<td class='listing-item' align='center' id='allCertificateNo_".$j."'>";
				$result.="<select name='certificateNo_".$j."' id='certificateNo_".$j."' onchange='xajax_certificateNo(this.value,0,".$j.");'>
					<option value=''> --Select-- </option>";
					foreach($cerificate as $cerificateDetail)
					{	
						if($certificateId==$cerificateDetail[0]){ $sel="selected"; } else { $sel=""; }
						$result.= "<option value='".$cerificateDetail[0]."' $sel>".$cerificateDetail[1]."</option>";
					}

				$result.="</select>
				</td>
				<td class='listing-item' align='center'><input id='availableQnty_".$j."' type='text' style='text-align:right; border:none;' readonly size='15' value='".$availbleQnty."' name='availableQnty_".$j."'>
				</td>
				<td class='listing-item' align='center'><input id='balanceQnty_".$j."' type='text' value='".$balanceQnty."' tabindex='0' style='text-align:right; border:none;' size='15' name='balanceQnty_".$j."'><input id='qntyStatus_".$j."' type='hidden' value='".$qntyStatus."' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_".$j."'></td>
				<td class='listing-item' align='center'><a onclick='setIssuanceCertificateStatus(".$j.");' href='###'>
				<img border='0' style='border:none;' src='images/delIcon.gif' title='Click here to remove this item'>
				</a>
				<input id='sstatus_".$j."' type='hidden' value='' name='sstatus_".$j."'>
				<input id='sIsFromDB_".$j."' type='hidden' value='N' name='sIsFromDB_".$j."'>
				<input id='srmId_".$j."' type='hidden' value='' name='srmId_".$j."'>
				</td>
				</tr>";	
				$j++;	
				}
				$result.="</tbody>
				</table>
				</td></tr>
			<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewCertificateTableRow($i);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='certificateSize' name='certificateSize' value='$j'/><input type='hidden' id='differenceInValue' name='differenceInValue' value=''/></tr>";
			$result.="</table>";			
			$result.="</td></tr>";
			$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='checkCertificate($i);'  value=' Tag Certificate' name='tagCertificate' id='tagCertificate' ></td></tr>";
			$result.="</table></p>";
		}
		else
		{
			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr><tr bgcolor='#fff'><td colspan='2' class='listing-item'>No certificate with these supplier detail</td></tr></table></p>";
		}
		return $result;
	}	
	
	function getCertificate($cerificateID)
	{
		$qry1= "SELECT balance_quantity FROM t_phtmonitoring_quantity where pht_certificate_number='$cerificateID' order by pht_quantity asc limit 1 ";
		$result1= $this->databaseConnect->getRecord($qry1);
		if(sizeof($result1)>0)
		{
			$result=$result1[0];
		}
		else
		{
			$qry2= "SELECT pht_Qty FROM t_phtcertificate where id='$cerificateID'";
			$result2= $this->databaseConnect->getRecord($qry2);
			$result=$result2[0];
		}
		return $result;
	}

	function getCerificateQty($cerificateID)
	{
		$qry1= "SELECT balance_quantity FROM t_phtmonitoring_quantity where pht_certificate_number='$cerificateID' order by pht_quantity desc limit 1 ";
		$result1= $this->databaseConnect->getRecord($qry1);
		if(sizeof($result1)>0)
		{
			$result=$result1[0];
		}
		else
		{
			$qry2= "SELECT pht_Qty FROM t_phtcertificate where id='$cerificateID'";
			$result2= $this->databaseConnect->getRecord($qry2);
			$result=$result2[0];
		}
		return $result;
	}

	function addPhtCertificateQuantity($lastId,$phtCertificateNo,$phtQuantity,$setoffQuantity,$balanceQuantity,$weightmentId,$rmlotidCertify,$hideSupplyQty,$adjustedQty,$supplyBalanceQty)
	{
		$qry	=	"insert into t_phtmonitoring_quantity (pht_monitoring_id,pht_certificate_number, pht_quantity,setoff_quantity,balance_quantity,weightment_id,rmlotid,supply_qty,adjusted_qty,supply_balance_qty) values('".$lastId."','".$phtCertificateNo."','".$phtQuantity."','".$setoffQuantity."','".$balanceQuantity."','".$weightmentId."','".$rmlotidCertify."','".$hideSupplyQty."','".$adjustedQty."','".$supplyBalanceQty."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getMonitoringId($certificateId)
	{
		$qry	= "SELECT id FROM `t_phtmonitoring` where certificate_id='$certificateId'";
		 $result=$this->databaseConnect->getRecord($qry);
		 if(sizeof($result)>0) return $result[0];
		 //return $result;
	}

	function getPhtTagDetail($weightmentId)
	{
		$qry	= "SELECT supply_balance_qty FROM t_phtmonitoring_quantity where weightment_id='$weightmentId' order by supply_balance_qty asc limit 1";
		 $result=$this->databaseConnect->getRecord($qry);
		 //echo $qry;
		 if(sizeof($result)>0) return $result[0];
		 //return $result;
	}

	function getPhtMonitoringData($weightmentId)
	{
		$qry	= "SELECT id,pht_certificate_number,pht_quantity,setoff_quantity,balance_quantity,supply_balance_qty FROM t_phtmonitoring_quantity where weightment_id='$weightmentId' order by id ";
		$result=$this->databaseConnect->getRecords($qry);
	//echo $qry;
		 return $result;
		 //return $result;
	}

	function getPhtCerificateDetailEdit($i,$supplierName,$pondName,$product_species,$weight)
	{	
		$result='';
		$qry	= "SELECT tphtc.id,tphtc.PHTCertificateNo,s.name,mpm.pond_name,mf.name,s.id,mpm.id,mf.id FROM t_phtmonitoring tphtm join t_phtcertificate tphtc on tphtc.id =tphtm.certificate_id left join supplier s on s.id=tphtc.supplier left join m_pond_master mpm on mpm.id=tphtc.pond_Name left join m_fish mf on mf.id=tphtc.species where tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' and tphtc.active='1' and (select balance_quantity from t_phtmonitoring_quantity where pht_certificate_number=tphtc.id and tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' order by pht_certificate_number desc limit 1) is null or (select balance_quantity from t_phtmonitoring_quantity where pht_certificate_number=tphtc.id and tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' order by pht_certificate_number desc limit 1) !=0";
				
		//echo $qry;
		$cerificate=$this->databaseConnect->getRecords($qry);
		
		if(sizeof($cerificate)>0)
		{
			$certificatedropDown = '<select name="certificateNo_0" id="certificateNo_0" onchange="xajax_certificateNo(this.value,0,'.$i.');">';
			$certificatedropDown.= '<option value=""> --Select-- </option>';
			foreach($cerificate as $cerificateDetail)
			{	
				$certificatedropDown.= '<option value="'.$cerificateDetail[0].'">'.$cerificateDetail[1].'</option>';
			}

			$certificatedropDown.= '</select>';

			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr>";
			$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
			$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'  width='40%'>Supplier :</td><td class='listing-item'>$cerificateDetail[2]<input type='hidden' name='supplierId' id='supplierId' value='$cerificateDetail[5]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Pond :</td><td class='listing-item'>$cerificateDetail[3]<input type='hidden' name='pondId' id='pondId' value='$cerificateDetail[6]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Product Species :</td><td class='listing-item'>$cerificateDetail[4]<input type='hidden' name='speciesId' id='speciesId' value='$cerificateDetail[7]'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Supply Quantity :</td><td class='listing-item'><input type='hidden' name='supplyQnty' id='supplyQnty' value='$weight'/>$weight</td></tr>";
			$result.="</table>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head' align='center' style='padding:20px 0px 5px 0px'>";
			$result.="<table><tr><td>";	
			$result.="<table id='tblAddCerificateDetail' cellspacing='1' cellpadding='6' bgcolor='#999999' name='tblAddCerificateDetail'>
				<tbody>
				<tr bgcolor='#f2f2f2' align='center'>
				<td class='listing-head' nowrap=''>Certificate No </td>
				<td class='listing-head' nowrap=''>Available Qty</td>
				<td class='listing-head' nowrap=''>Balance Qty </td>
				<td></td>
				</tr>
				<tr id='srow_0' class='whiteRow'>
				<td class='listing-item' align='center' id='allCertificateNo_0'>
				$certificatedropDown
				</td>
				<td class='listing-item' align='center'><input id='availableQnty_0' type='text' style='text-align:right; border:none;' readonly size='15' value='' name='availableQnty_0'>
				</td>
				<td class='listing-item' align='center'><input id='balanceQnty_0' type='text' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='balanceQnty_0'><input id='qntyStatus_0' type='hidden' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_0'></td>
				<td class='listing-item' align='center'><a onclick='setIssuanceCertificateStatus(0);' href='###'>
				<img border='0' style='border:none;' src='images/delIcon.gif' title='Click here to remove this item'>
				</a>
				<input id='sstatus_0' type='hidden' value='' name='sstatus_0'>
				<input id='sIsFromDB_0' type='hidden' value='N' name='sIsFromDB_0'>
				<input id='srmId_0' type='hidden' value='' name='srmId_0'>
				</td>
				</tr>
				</tbody>
				</table>
				</td></tr>
			<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewCertificateTableRow($i);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='certificateSize' name='certificateSize' value='1'/><input type='hidden' id='differenceInValue' name='differenceInValue' value=''/></tr>";
			$result.="</table>";			
			$result.="</td></tr>";
			$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='checkCertificate($i);'  value=' Tag Certificate' name='tagCertificate' id='tagCertificate' ></td></tr>";
			$result.="</table></p>";
		}
		else
		{
			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr><tr bgcolor='#fff'><td colspan='2' class='listing-item'>No certificate with these supplier detail</td></tr></table></p>";
		}
		return $result;
	}

	function getPhtCerificateDetailTagEdit($i,$supplierName,$pondName,$product_species,$weight,$phtTagData)
	{	
		$result='';
		$qry	= "SELECT tphtc.id,tphtc.PHTCertificateNo,s.name,mpm.pond_name,mf.name,s.id,mpm.id,mf.id FROM t_phtcertificate tphtc left join supplier s on s.id=tphtc.supplier left join m_pond_master mpm on mpm.id=tphtc.pond_Name left join m_fish mf on mf.id=tphtc.species WHERE  tphtc.supplier='$supplierName' and tphtc.pond_Name='$pondName' and tphtc.species='$product_species' and tphtc.active='1'";
		//echo $qry;
		$cerificate=$this->databaseConnect->getRecords($qry);
		//echo $phtTagData;
		if(sizeof($cerificate)>0)
		{
			

			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr>";
			$result.="<tr><td align='center' bgcolor='#ffffff' style='padding:20px 0px 20px 0px'>";
			$result.="<table cellpadding='4' cellspacing='1' width='85%' bgcolor='#999999'>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'  width='40%'>Supplier :</td><td class='listing-item'>".$cerificate[0][2]."<input type='hidden' name='supplierId' id='supplierId' value='".$cerificate[0][5]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Pond :</td><td class='listing-item'>".$cerificate[0][3]."<input type='hidden' name='pondId' id='pondId' value='".$cerificate[0][6]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Product Species :</td><td class='listing-item'>".$cerificate[0][4]."<input type='hidden' name='speciesId' id='speciesId' value='".$cerificate[0][7]."'/></td></tr>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head'>Supply Quantity :</td><td class='listing-item'><input type='hidden' name='supplyQnty' id='supplyQnty' value='$weight'/>$weight</td></tr>";
			$result.="</table>";
			$result.="<tr bgcolor='#ffffff'><td class='listing-head' align='center' style='padding:20px 0px 5px 0px'>";
			$result.="<table><tr><td>";	
			$result.="<table id='tblAddCerificateDetail' cellspacing='1' cellpadding='6' bgcolor='#999999' name='tblAddCerificateDetail'>
				<tbody>
				<tr bgcolor='#f2f2f2' align='center'>
				<td class='listing-head' nowrap=''>Certificate No </td>
				<td class='listing-head' nowrap=''>Available Qty</td>
				<td class='listing-head' nowrap=''>Balance Qty </td>
				<td></td>
				</tr>";
				$j=0;
				$objt=json_decode($phtTagData);
				//printr($objt);
				foreach ($objt->items as $item )
				{
					$certificateId=$item->certificateNo;
					$availableQnty=$item->availableQnty;
					$balanceQnty=$item->balanceQnty;
					$qntyStatus=$item->qntyStatus;
					$phtMonitoringEntryId=$item->phtMonitoringEntryId;
					$weightmentEntryId=$item->weightmentEntryId;
					//echo "certificateNo=".$certificate;
				
				$result.="<tr id='srow_".$j."' class='whiteRow'>
				<td class='listing-item' align='center' id='allCertificateNo_".$j."'>";
				$result.="<select name='certificateNo_".$j."' id='certificateNo_".$j."' onchange='certificateNoEdit(this.value,".$j.",".$i.");' disabled='disabled'>
					<option value=''> --Select-- </option>";
					foreach($cerificate as $cerificateDetail)
					{	
						if($certificateId==$cerificateDetail[0]){ $sel="selected"; } else { $sel=""; }
						$result.= "<option value='".$cerificateDetail[0]."' $sel>".$cerificateDetail[1]."</option>";
					}

				$result.="</select>
				<input name='certificateOld_".$j."' id='certificateOld_".$j."' value='".$certificateId."'type='hidden'/>
				</td>
				<td class='listing-item' align='center'><input id='availableQnty_".$j."' type='text' style='text-align:right; border:none;' readonly size='15' value='".$availableQnty."' name='availableQnty_".$j."'>
				<input id='availableQntyOld_".$j."' type='hidden' style='text-align:right; border:none;' readonly size='15' value='".$availableQnty."' name='availableQntyOld_".$j."'>
				</td>
				<td class='listing-item' align='center'><input id='balanceQnty_".$j."' type='text' value='".$balanceQnty."' tabindex='0' style='text-align:right; border:none;' size='15' name='balanceQnty_".$j."'><input id='qntyStatus_".$j."' type='hidden' value='".$qntyStatus."' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_".$j."'></td>
				<td class='listing-item' align='center'><!--<a onclick='setIssuanceCertificateStatus(".$j.");' href='###'>
				<img border='0' style='border:none;' src='images/delIcon.gif' title='Click here to remove this item'>
				</a>-->
				<input id='sstatus_".$j."' type='hidden' value='' name='sstatus_".$j."'>
				<input id='sIsFromDB_".$j."' type='hidden' value='N' name='sIsFromDB_".$j."'>
				<input id='srmId_".$j."' type='hidden' value='' name='srmId_".$j."'>
				<input id='phtMonitoringEntryId_".$j."' type='hidden' value='".$phtMonitoringEntryId."' name='phtMonitoringEntryId_".$j."'><input id='weightmentEntryId_".$j."' type='hidden' value='".$weightmentEntryId."' name='weightmentEntryId_".$j."'>
				</td>
				</tr>";	
				$j++;	
				}
				$result.="</tbody>
				</table>
				</td></tr>
			<tr id='addNew'><td colspan='4' align='left' bgcolor='#ffffff' class='listing-item'><a id='addRow' class='link1' title='Click here to add new item.' onclick='javascript:addNewCertificateTableRowEdit($i);' href='javascript:void(0);'>Add New</a></td><input type='hidden' id='certificateSize' name='certificateSize' value='$j'/><input type='hidden' id='differenceInValue' name='differenceInValue' value=''/></tr>";
			$result.="</table>";			
			$result.="</td></tr>";
			$result.="<tr height='40'><td colspan='4' align='center' bgcolor='#fff' class='listing-item'><input class='button' type='submit' onclick='checkCertificateEdit($i);'  value=' Tag Certificate' name='tagCertificate' id='tagCertificate' ></td></tr>";
			$result.="</table></p>";
		}
		else
		{
			$result.="<p><table cellpadding='3' cellspacing='1' width='100%' bgcolor='#999999'>";
			$result.="<tr><td align='center' colspan='4' bgcolor='#e8edff' class='listing-head' height='30'>PHT Cerificate List</td></tr><tr bgcolor='#fff'><td colspan='2' class='listing-item'>No certificate with these supplier detail</td></tr></table></p>";
		}
		return $result;
	}
	
	function deleteWeightmentPhtTag($id)
	{
		$qry	= "delete from t_phtmonitoring_quantity where weightment_id='$id'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
		
	}

	function deletePhtMonitoring($id)
	{
		$qry	= "delete from t_phtmonitoring_quantity where id='$id'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
		
	}

	function deleteWeightmentPhtTagSupplier($weightmentId)
	{
		$qry	= "SELECT id from t_weightment_data_entries where weightment_data_sheet_id='$weightmentId'";
		//echo $qry;
		$res=$this->databaseConnect->getRecords($qry);
		if(sizeof($res)>0)
		{
			foreach($res as $rest)
			{
				$query	= "delete from t_phtmonitoring_quantity where weightment_id='".$rest[0]."'";
				$result	= $this->databaseConnect->delRecord($query);
				if ($result) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
				return $result;
			}
		}
	}

	function updatePhtCertificateQuantity($lastId,$phtCertificateNo,$phtQuantity,$setoffQuantity,$balanceQuantity,$weightmentId,$rmlotidCertify,$hideSupplyQty,$adjustedQty,$supplyBalanceQty,$phtMonitoringIdEntry)
	{
		$qry	=	"update t_phtmonitoring_quantity set pht_monitoring_id='$lastId',pht_certificate_number='$phtCertificateNo', pht_quantity='$phtQuantity',setoff_quantity='$setoffQuantity',balance_quantity='$balanceQuantity',weightment_id='$weightmentId',rmlotid='$rmlotidCertify',supply_qty='$hideSupplyQty',adjusted_qty='$adjustedQty',supply_balance_qty='$supplyBalanceQty' where id='$phtMonitoringIdEntry'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getPhtQuantityLessValue($phtcertificateId,$phtQty)
	{
		$qry	=	"SELECT * FROM `t_phtmonitoring_quantity` where pht_certificate_number='".$phtcertificateId."'";
		if($phtQty!='') $qry.=" and pht_quantity<'".$phtQty."'";
		//echo $qry;
		$result=$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId)
	{
		$qry	=	"update t_phtmonitoring_quantity set pht_quantity='$phtQt',setoff_quantity='$setOff',balance_quantity='$balQty' where id='$monId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getPhtMonitoringWeightmentId($weightmentEntryId,$id)
	{
		//$qry	=	"SELECT * FROM `t_phtmonitoring_quantity` where weightment_id='".$weightmentEntryId."' and id!='$id'";
		$qry	=	"SELECT * FROM `t_phtmonitoring_quantity` where weightment_id='".$weightmentEntryId."' and id>'$id'";
		$result=$this->databaseConnect->getRecords($qry);
		//echo "hii".$qry;
		//die();
		return $result;
	}

	function landingCenter($pondNameId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="select a.id,a.name from m_landingcenter a inner join  m_pond_master b on a.id=b.location  where b.id='$pondNameId'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getCompanyUnit($rm_lot_id)
	{
		$qry="select company_id,unit_id from t_manage_rm_lotid  where id='$rm_lot_id'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0],$result[1]):"";
	}
}