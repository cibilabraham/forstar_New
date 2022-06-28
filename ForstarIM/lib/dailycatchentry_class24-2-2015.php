<?php
class DailyCatchEntry
{
	/****************************************************************
	This class deals with all the operations relating to Daily Catch Entry
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyCatchEntry(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function getSupplierDetail($rmLotId)
	{
		 // $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="select a.id,c.vehicle_Number, a.Company_Name,a.unit,a.supplier_Challan_No,d.display_name,e.name,g.supplier_group_name,
				(select GROUP_CONCAT(supplier.id) from supplier  WHERE  id IN(select supplier_name FROM t_weightment_data_entries WHERE weightment_data_sheet_id=b.id) AND name != '') as supplier_id,
				(select GROUP_CONCAT(supplier.name) from supplier  WHERE  id IN(select supplier_name FROM t_weightment_data_entries WHERE weightment_data_sheet_id=b.id) AND name != '') as supplier_name
				from t_rmreceiptgatepass a 
				left join weighment_data_sheet b on b.rm_lot_id=a.id
				LEFT JOIN m_vehicle_master c ON c.id = a.vehicle_number
				 left join m_supplier_group g on g.id=b.supplier_group
				left join m_billing_company d on d.id=a.Company_Name
				left join m_plant e on e.id=a.unit 
				 where b.rm_lot_id='$rmLotId'";
		
		//select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecord($qry);
		
		return $result;
	}
	
	function getAllLotIds()
	{	
		$qry = "select id,CONCAT(alpha_character,rm_lotid) as lot_Id from t_manage_rm_lotid where id not in  (select lot_id_origin from t_manage_rm_lotid)  and status='0' and active='1'";
	
		//
		//$qry = "select id,CONCAT(alpha_character,rm_lotid) as lot_Id from t_manage_rm_lotid where lot_id_origin='0'";
		
		// $qry = "select c.id,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id from `t_rmreceiptgatepass` a 
				// inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				// inner join t_manage_rm_lotid c on b.id = c.receipt_id ";
	
	
	//$qry	= "select id,lot_Id from `t_rmreceiptgatepass` where active='1'";
		//$qry	= "select id,new_lot_Id from t_unittransfer where active='1'";
		// $qry="select a.id,a.lot_Id from t_rmreceiptgatepass a  join weighment_data_sheet b on b.rm_lot_id=a.id where a.active='1' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplier1($rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT DISTINCT a.id, a.name
					FROM supplier a
					LEFT JOIN t_weightment_data_entries b ON b.supplier_name = a.id
					LEFT JOIN weighment_data_sheet c ON c.id = b.weightment_data_sheet_id
					WHERE c.rm_lot_id = '$rm_lot_id'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	function getSupplierNm($rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
			$qry="SELECT DISTINCT a.id, a.name
					FROM supplier a
					LEFT JOIN t_dailycatch_main b ON b.payment = a.id
					
					WHERE b.rm_lot_id = '$rm_lot_id'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	
	function getPondNm($rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT  a.id, a.pond_name
					FROM m_pond_master a
					LEFT JOIN t_dailycatch_main b ON b.pond_name = a.id
					WHERE b.rm_lot_id = '$rm_lot_id' ";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	
		function getPondNmAll()
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT id,pond_name FROM m_pond_master ";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	/*function getPond($rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT  a.id, a.pond_name
					FROM m_pond_master a
					LEFT JOIN t_weightment_data_entries b ON b.pond_name = a.id
					LEFT JOIN weighment_data_sheet c ON c.id = b.weightment_data_sheet_id
					WHERE c.rm_lot_id = '$rm_lot_id' ";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	*/
	
	function getFishNm($pondName,$rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT a.id, a.name FROM m_fish a left join t_dailycatchentry b  on b.fish=a.id left join t_dailycatch_main c on c.id=b.main_id where
					
					 c.pond_name='$pondName' and c.rm_lot_id = '$rm_lot_id'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	
	
	/*function getFish($pondName,$rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT  a.id, a.name
					FROM m_fish a
					LEFT JOIN t_weightment_data_entries b ON b.product_species = a.id
					LEFT JOIN weighment_data_sheet c ON c.id = b.weightment_data_sheet_id
					WHERE b.pond_name='$pondName' and c.rm_lot_id = '$rm_lot_id'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}*/
	
	#Insert one blank record in Main Table
	function addTempMaster($currentUserId)
	{
		$qry	=	"insert into t_dailycatch_main (flag, entry_date, select_date, createdby) values('0', Now(), Now(), '$currentUserId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert One Blan Record in t_dailycatch_Entry Table
	function addTempRecDailyCatchEntry($lastId)
	{
		$qry	= "insert into t_dailycatchentry (main_id,select_date) values('$lastId',Now())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	#Add Gross Wt in Count Details Iframe
	function addGrossWt($grossWt, $basketWt, $entryId)
	{
		//$grossWt=100;
		$qry	= "insert into t_dailycatchitem (gross,basket,entry_id) values('".$grossWt."','".$basketWt."','".$entryId."')";
		//echo "<br>$qry";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#paging in Count Details Iframe
	function fetchAllPagingRecords($entryId, $offset, $limit)
	{
		$qry = "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc limit $offset, $limit ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getPondName($supplier,$rmLotId)
	{
		//$qry = "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc limit $offset, $limit ";
		//$qry="select a.count_code,a.product_species from t_weightment_data_entries a left join t_dailycatchitem b on b.weightment_data_sheet_id  ";
		$qry="SELECT a.pond_name, c.pond_Name
				FROM t_weightment_data_entries a
				LEFT JOIN weighment_data_sheet b ON a.weightment_data_sheet_id = b.id
				LEFT JOIN m_pond_master c ON a.pond_name = c.id
				WHERE a.supplier_name = '$supplier' ";
				//AND b.rm_lot_id = '$rmLotId' AND a.pond_active='' ";
		//echo $qry;		
		// $result	= $this->databaseConnect->getRecord($qry);
		// return $result;
		
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
	
	
	function getCode($pondId,$rmLotId)
	{
		//$qry = "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc limit $offset, $limit ";
		//$qry="select a.count_code,a.product_species from t_weightment_data_entries a left join t_dailycatchitem b on b.weightment_data_sheet_id  ";
		$qry="SELECT a.count_code, a.product_species, c.name,a.process_code_id,d.code
				FROM t_weightment_data_entries a
				LEFT JOIN weighment_data_sheet b ON a.weightment_data_sheet_id = b.id
				LEFT JOIN m_fish c ON a.product_species = c.id
				left join m_processcode d on a.process_code_id=d.id
				WHERE a.pond_name = '$pondId'
				AND b.rm_lot_id = '$rmLotId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getCodeWeightment($pondId,$rmLotId,$weightmentId)
	{
		//$qry = "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc limit $offset, $limit ";
		//$qry="select a.count_code,a.product_species from t_weightment_data_entries a left join t_dailycatchitem b on b.weightment_data_sheet_id  ";
		$qry="SELECT a.count_code, a.product_species, c.name,a.process_code_id,d.code
				FROM t_weightment_data_entries a
				LEFT JOIN weighment_data_sheet b ON a.weightment_data_sheet_id = b.id
				LEFT JOIN m_fish c ON a.product_species = c.id
				left join m_processcode d on a.process_code_id=d.id
				WHERE a.pond_name = '$pondId' and a.id='$weightmentId'
				AND b.rm_lot_id = '$rmLotId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	
	function getCountDetails($entryId)
	{
		$qry = "select sum(gross), sum(basket), (sum(gross)-sum(basket)) as netwt from t_dailycatchitem where entry_id='".$entryId."' group by entry_id";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	
	//getting all records based on Entry Id
	function fetchAllGrossRecords($entryId)
	{
		$qry	= "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Insert Quality (Quality Iframe)
	function addQuality($currentId, $quality, $percentage)
	{
		$qry	=	"insert into t_qualityentry (quality,percent,entry_id) values('".$quality."','".$percentage."','".$currentId."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Insert Quality  15'th July,2014 
	function addEntryQuality($currentId, $quality, $entry_wt,$percentage,$reason,$weightment_status,$billing)
	{
	 	$qry	=	"insert into t_qualityentry (quality,percent,entry_id,entry_weight,reason,weightment_status,billing_status) values
					('".$quality."','".$percentage."','".$currentId."','".$entry_wt."','".$reason."','".$weightment_status."','".$billing."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function updateEntryQuality($id,$catchEntryId,$quality,$entry_wt,$percentage,$reason,$weightmentStatus,$billing)
	{
		$qry	=	"update t_qualityentry set quality = '".$quality."',percent = '".$percentage."',
					 entry_id = '".$catchEntryId."',entry_weight = '".$entry_wt."',reason = '".$reason."',weightment_status='".$weightmentStatus."',billing_status='".$billing."'  
					 where id = '".$id."' ";
		// echo $qry;die;
		$updateStatus	=	$this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $updateStatus;
	}
	#Select all Quality records Based on Current Entry Id
	function fetchAllQualityRecords($currentId)
	{
		//$qry	= "select id, quality, percent from t_qualityentry where entry_id='".$currentId."'";
		$qry	= "select tqe.id, tqe.quality, tqe.percent, mq.name,tqe.entry_weight,tqe.reason,tqe.billing_status from t_qualityentry tqe left join m_quality mq on mq.id=tqe.quality where entry_id='".$currentId."' and weightment_status='1' order by mq.name asc";
				
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getQualityAdd($dailyCatchentryId)
	{
			$qry	= "select a.id,a.quality,a.percent,a.entry_id,a.entry_weight,a.reason,b.name,a.billing_status from  t_qualityentry a left join m_quality b on b.id=a.quality where a.weightment_status!='1' and a.entry_id='$dailyCatchentryId'";
				
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Update quality 
	function updateQuality($qualityId, $percentage, $currentId)
	{
		$qry	=	" update t_qualityentry set percent='$percentage' where id=$qualityId and entry_id=$currentId";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Delete a Quality
	function deleteQuality($qualityId)
	{
		$qry	=	" delete from t_qualityentry where id=$qualityId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Update Basket Weight
	function updateBasketWt($resetBasketWt, $entryId)
	{
		$qry	=	" update t_dailycatchitem set basket='$resetBasketWt' where entry_id=$entryId";
	
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Update Gross Entry Wt
	function updateGrossWt($grossId, $grossWt, $basketWt, $entryId)
	{
		$qry	=	" update t_dailycatchitem set gross='$grossWt', basket='$basketWt' where id=$grossId and entry_id=$entryId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	
	# Delete Gross Entry Weight	
	function deleteGrossEntryWt($grossId)
	{
		$qry	=	" delete from t_dailycatchitem where id=$grossId ";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		return $result;
	}

	# Insert Rec in daily catch entry table 
   // function addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $entryId,$catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling, $rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code)
	// {
		// $qry	= "update t_dailycatchentry set fish='$fish', fish_code='$processCode', ice_wt='$ice', count_values='$count', average='$countAverage', local_quantity='$entryLocal', wastage='$entryWastage', soft='$entrySoft', reason='$reasonAdjust', adjust='$entryAdjust', good='$goodPack', peeling='$peeling', remarks='$entryRemark', actual_wt='$entryActualWt', effective_wt='$entryEffectiveWt', gross='$entryTotalGrossWt', total_basket='$entryTotalBasketWt', net_wt='$entryGrossNetWt', decl_wt='$declWeight', decl_count='$declCount', select_date='$selectDate', grade_id='$gradeId', basket_wt='$basketWt', reason_local='$reasonLocal', reason_wastage='$reasonWastage', reason_soft='$reasonSoft', entry_option='$entryOption', grade_count_adj='$gradeCountAdj', grade_count_adj_reason='$gradeCountAdjReason', received_by='$receivedBy', no_billing='$noBilling' ,rm_lot_id='$rm_lot_id',supplyDetails='$supplyDetails',make_payment='$make_payment',payment='$payment',count_code='$count_code'  where id='$catchEntryNewId' and main_id='$entryId' ";
		// echo $qry;
		// $result	=	$this->databaseConnect->updateRecord($qry);
		
		// if ($result) $this->databaseConnect->commit();
		// else $this->databaseConnect->rollback();
		// return $result;
	// }
	
	function addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $entryId,$catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling,$weightId)
	{
		$qry	= "update t_dailycatchentry set fish='$fish', fish_code='$processCode', ice_wt='$ice', count_values='$count', average='$countAverage', local_quantity='$entryLocal', wastage='$entryWastage', soft='$entrySoft', reason='$reasonAdjust', adjust='$entryAdjust', good='$goodPack', peeling='$peeling', remarks='$entryRemark', actual_wt='$entryActualWt', effective_wt='$entryEffectiveWt', gross='$entryTotalGrossWt', total_basket='$entryTotalBasketWt', net_wt='$entryGrossNetWt', decl_wt='$declWeight', decl_count='$declCount', select_date='$selectDate', grade_id='$gradeId', basket_wt='$basketWt', reason_local='$reasonLocal', reason_wastage='$reasonWastage', reason_soft='$reasonSoft', entry_option='$entryOption', grade_count_adj='$gradeCountAdj', grade_count_adj_reason='$gradeCountAdjReason', received_by='$receivedBy', no_billing='$noBilling' , weightment_id_available='$weightId' where id='$catchEntryNewId' and main_id='$entryId'  ";
		// echo $qry;
		 //die;
		$result	=	$this->databaseConnect->updateRecord($qry);
		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Update t_dailycatch_main (When ever press the update button Confirm, Payment_confirm, report_confirm-> field values will clear in this section) 	
	function updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo,$weighChallanNo,$selectDate, $selectTime,$entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode,$rm_lot_id,$supplyDetails,$make_payment,$payment,$count_code,$lotIdAvailable,$supplierGroup,$pondName)
	{
		//echo $entryId;
		$qry	= " update t_dailycatch_main set unit='$unit', entry_date=Now(), vechile_no='$vechicleNo',supplier_group_name='$supplierGroup',pond_name='$pondName', weighment_challan_no='$weighChallanNo', landing_center='$landingCenter', main_supplier='$mainSupplier', select_date='$selectDate', select_time='$selectTime', flag='1', payment_by='$paymentBy', confirm=0, payment_confirm='N', report_confirm='N', sub_supplier='$subSupplier', supplier_challan_no='$supplyChallanNo', billing_company_id='$billingCompany', alpha_code='$alphaCode',rm_lot_id='$rm_lot_id',supplyDetails='$supplyDetails',make_payment='$make_payment',payment='$payment',count_code='$count_code',rm_id_available='$lotIdAvailable' where id='$entryId' ";
	//echo $qry;
	//die;
		$result	= $this->databaseConnect->updateRecord($qry);		
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}
	
	function updateWeightmentPond($pondName,$payment,$weightmentId)
	{
		$qry	= " update t_weightment_data_entries set pond_active='1' where supplier_name='$payment' and pond_name='$pondName' and weightment_data_sheet_id='$weightmentId' ";
		 // echo $qry;
		 // die;
		$result	= $this->databaseConnect->updateRecord($qry);		
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}

	function getWeightmentmentId($rm_lot_id)
	{
		$qry	= "select id from weighment_data_sheet where rm_lot_id='$rm_lot_id' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	
	#Select all daily catch entry details
	function fetchAllDailyRecords()
	{
		$qry	= "select a.id, a.weighment_challan_no, a.entry_date, a.flag,b.count_values, b.grade_id,b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select all daily catch entry for Add New Raw material //Edited on 08-08-07 
	function fetchAllRawMaterialDailyRecords($catchEntryMainId)
	{	
		$qry	= "select a.id, a.entry_date, a.weighment_challan_no, b.fish, b.fish_code, c.id, c.name, c.code, d.id, d.code, b.id, b.received_by, b.count_values, b.grade_id from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d where c.id=b.fish and d.id=b.fish_code and a.id=b.main_id and a.id='$catchEntryMainId' order by a.weighment_challan_no desc ";

		/*
		$qry	= "select a.id, a.entry_date, a.weighment_challan_no, b.fish, b.fish_code, c.id, c.name, c.code, d.id, d.code, b.id, b.received_by, b.count_values, b.grade_id from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id left join m_fish c on c.id=b.fish left join m_processcode d on d.id=b.fish_code where a.id='$catchEntryMainId' order by a.weighment_challan_no desc ";
		*/
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Select date wise list
	function dailyCatchEntryRecFilter($recordsDate)
	{
		$qry	=	"select a.id, a.weighment_challan_no, a.entry_date, a.flag,b.count_values, b.grade_id,b.id,b.fish_code, b.effective_wt,a.confirm from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id where a.select_date='$recordsDate' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#slect date from recod
	function fetchAllDateRecords()
	{
		$qry	= "select distinct entry_date from t_dailycatchentry";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get daily catch entry based on id 
	function find($catchEntryId, $dailyCatchentryId)
	{
		$qry	= "select a.id, a.unit, a.entry_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, a.sub_supplier, b.fish, b.fish_code, b.ice_wt, b.count_values, b.average, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.actual_wt, b.effective_wt, b.gross, b.total_basket, b.net_wt, b.decl_wt, b.decl_count, a.select_date, b.grade_id, b.basket_wt, b.reason_local, b.reason_wastage, b.reason_soft,b.entry_option, b.id, a.select_time,a.payment_by, b.grade_count_adj, b.grade_count_adj_reason, a.billing_company_id, a.alpha_code, b.no_billing,a.rm_lot_id,a.supplyDetails,a.make_payment,a.payment,a.count_code,a.supplier_group_name,a.pond_name,a.rm_id_available,b.weightment_id_available from t_dailycatch_main a, t_dailycatchentry b  where a.id='$catchEntryId' and a.id=b.main_id and b.id='$dailyCatchentryId' ";
	 //echo $qry;
		 //die;
		return $this->databaseConnect->getRecord($qry);
	}


	# Get daily catch main entry based on id 
	function findDailyCatchMainRec($catchEntryId)
	{
		$qry	= "select a.id, a.unit, a.entry_date, a.vechile_no, a.weighment_challan_no, a.landing_center, a.main_supplier, a.select_date, a.select_time, a.payment_by, a.sub_supplier, a.supplier_challan_no, a.billing_company_id, a.alpha_code,a.supplier_group_name,a.pond_name,a.rm_id_available from t_dailycatch_main a where a.id=$catchEntryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Daily Catch Entry Sub Supplier Details
	function getDailyCatchEntryRec($catchEntryId)
	{
		$qry = " select distinct sub_supplier, supplier_challan_no from t_dailycatchentry where main_id='$catchEntryId' and fish!='' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array():array($result[0][0],$result[0][1]);
	}

	#Delete Rec from t_dailycatch_main Table based on Id
	function delEntryMainId($entryId)
	{
		$qry	=	" delete from t_dailycatch_main where id=$entryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete Rec from t_dailycatchentry Table based on entry Id
	function delLastInsertId($dailyCatchEntryId)
	{
		$qry	=	" delete from t_dailycatchentry where id=$dailyCatchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Catch Entry
	function deleteDailyCatchEntryWt($catchEntryId)
	{
		$qry	=	"delete from t_dailycatchentry where id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete GrossWt
	function deleteDailyCatchEntryGrossWt($catchEntryId)
	{
		$qry	=	"delete from t_dailycatchitem where entry_id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete Quality Wt
	function deleteDailyCatchEntryQualityWt($catchEntryId)
	{
	$qry	=	"delete from t_qualityentry where entry_id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	###########

	#For finding Grade based on count average
	function fetchAllGradeRecords($recordAverage)
	{
		$qry	= "select a.id,a.code from m_grade a where '$recordAverage'<= a.max and '$recordAverage' >=a.min";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Check Record Exist of Main Id
	function checkRecordsExist($catchMainId)
	{
		$qry	= "select b.main_id from t_dailycatch_main a, t_dailycatchentry b  where  a.id=b.main_id and b.main_id='$catchMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select Date Range wise pagination Records
	function filterCatchEntryPagingRecords($fromDate, $tillDate, $selRecord, $offset, $limit, $supplierId, $selFish, $selProcesscode, $fBillingCompany) 
	{
		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'" ;
		
		if ($selRecord=='I') $whr .= " and a.weighment_challan_no is null";
		else if ($selRecord=='C') $whr .= " and a.weighment_challan_no is not null";
		else if ($selRecord=='Z') $whr .= " and b.fish is null and b.fish_code is null ";
		else $whr	.=	"";
		
		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($selFish=="") $whr .= "";
		else $whr .= " and b.fish=$selFish";

		if ($selProcesscode=="") $whr .= "";
		else $whr .= " and b.fish_code=$selProcesscode";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";
		
		$orderBy	= "a.weighment_challan_no desc";
		
		$limit		= " ".$offset.", ".$limit."";
		
		$qry		= "select a.id, a.weighment_challan_no, a.entry_date, a.flag, b.count_values, b.grade_id, b.id, b.fish_code, b.effective_wt, a.confirm, b.received_by, b.grade_count_adj, b.grade_count_adj_reason, a.main_supplier, a.select_date, a.payment_confirm, b.paid, b.average, a.alpha_code,a.payment,concat(tmgrm.alpha_character,tmgrm.rm_lotid) from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id left join t_manage_rm_lotid tmgrm on tmgrm.id=a.rm_lot_id" ;


	//	$qry		= "select a.id, a.weighment_challan_no, a.entry_date, a.flag, b.count_values, b.grade_id, b.id, b.fish_code, b.effective_wt, a.confirm, b.received_by, b.grade_count_adj, b.grade_count_adj_reason, a.main_supplier, a.select_date, a.payment_confirm, b.paid, b.average, a.alpha_code,a.payment from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="") $qry   .=" limit ".$limit;
				
		//echo $qry."<br>";	
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	## Select Date Range wise list
	function filterDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $supplierId, $selFish, $selProcesscode, $fBillingCompany)
	{
		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'" ;
		
		if ($selRecord=='I') $whr .= "and a.weighment_challan_no is null";
		else if ($selRecord=='C') $whr .= "and a.weighment_challan_no is not null";
		else if ($selRecord=='Z') $whr .= " and b.fish is null and b.fish_code is null ";
		else $whr .= "";
		
		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($selFish=="") $whr .= "";
		else $whr .= " and b.fish=$selFish";

		if ($selProcesscode=="") $whr .= "";
		else $whr .= " and b.fish_code=$selProcesscode";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";

		$orderBy	= "a.weighment_challan_no desc";
		
		$qry		= "select a.id, a.weighment_challan_no, a.entry_date, a.flag, b.count_values, b.grade_id,b.id,b.fish_code, b.effective_wt,a.confirm, b.received_by, b.grade_count_adj, b.grade_count_adj_reason, a.main_supplier, a.select_date, a.payment_confirm, b.paid, b.average, a.alpha_code from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
						
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	## Select Distinct Records based on date range wise Date Range wise list
	function filterDistinctDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $supplierId, $fBillingCompany)
	{
		$whr = " a.id=b.main_id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.flag=1 " ;
		if ($selRecord=='I') {
			$whr	.=	"and a.weighment_challan_no is null";
		} else if ($selRecord=='C') {
			$whr	.=	"and a.weighment_challan_no is not null";
		} else if ($selRecord=='Z') {
			$whr	.=	" and b.fish is null and b.fish_code is null ";
		} else {
			$whr	.=	"";
		}

		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";

		$orderBy	=	"a.weighment_challan_no desc";
		
		$qry		=	"select distinct a.id, a.weighment_challan_no, a.alpha_code from t_dailycatch_main a, t_dailycatchentry b";
		if ($whr!="") $qry   .= " where ".$whr;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
				
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For finding the Main Id based on Entry Id
	function getMainId($entryId)
	{
		$qry	=	"select main_id from t_dailycatchentry where id=$entryId";
		//echo "qry=$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);	
		return $result[0][0];
	}

	#Check any entry against main Id 
	function moreEntriesExist($mainId)
	{
		$qry	=	"select id from t_dailycatchentry where main_id=$mainId";	
		$result	=	$this->databaseConnect->getRecords($qry);		
		return  ( sizeof($result) > 0 ) ? true : false ;
	}

	#Check Blank Record Exist
	function checkBlankRecord($currentUserId)
	{
		$qry = "select a.id, a.weighment_challan_no, b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id where a.createdby='$currentUserId' and a.weighment_challan_no is null order by a.id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[2]):false;	
	}

	//--------------------------- Declared Section -----------------------------
	#Insert Declared Details
	function addDeclaredDetails($currentId, $supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier)
	{
		$qry	= "insert into t_dailycatch_declared (entry_id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier) values('".$currentId."', '".$supplierChallanNo."', '".$supplierChallanDate."', '".$declWeight."', '".$declCount."', '".$declIce."', '$subSupplier' )";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Select all records from Declared Record Table
	function fetchAllDeclaredRecords($currentId)
	{
		$qry	= "select id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier from t_dailycatch_declared where entry_id='".$currentId."' order by supplier_challan_no asc" ;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Update Declared Entry
	function updateDeclaredRecord($declaredRecordId, $supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier)
	{
		$qry	= " update t_dailycatch_declared set supplier_challan_no='$supplierChallanNo', supplier_challan_date='$supplierChallanDate', decl_wt='$declWeight', decl_count='$declCount', decl_ice='$declIce', sub_supplier='$subSupplier' where id=$declaredRecordId";
		$result		=	$this->databaseConnect->updateRecord($qry);
		if ($result)		$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $result;	
	}

	#Find Declared Rec based on Edit Id
	function findDeclaredRec($editId)
	{
		$qry	= "select id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier from t_dailycatch_declared where id='".$editId."'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Delete a Declared Record
	function deleteDeclaredRec($declaredId)
	{
		$qry	= " delete from t_dailycatch_declared where id=$declaredId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	//---------------------------------------------
	#Checking the main Id is Updated or not
	function checkEntryUpdatedRecord($catchMainId)
	{
		$qry	=	"select b.main_id from t_dailycatch_main a, t_dailycatchentry b  where  a.id=b.main_id and b.main_id='$catchMainId' and (a.payment_confirm='Y' or b.paid='Y')";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return sizeof($result)>0?true:false;
	}

	# Delete a Catch Entry Declared Record (When Press Add New in Daily Catch Entry Screen)
	function deleteCatchEntryDeclaredRec($catchEntryNewId)
	{
		$qry	=	" delete from t_dailycatch_declared where entry_id=$catchEntryNewId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# ---------------- For AJAX Search -------------------
	#Select distinct Supplier
	function getSupplierList($fromDate, $tillDate, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr	=	"a.main_supplier=b.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";
		
		$orderBy = "b.name asc";		
		$qry	= "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b $tableName";
		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
		//echo "<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}

	#Fish Records for a date range	
	function getFishList($fromDate, $tillDate, $selectSupplier, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr = "b.fish=c.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null ";
	
		if 	($selectSupplier==0) $whr .="";
		else	$whr .=" and a.main_supplier=".$selectSupplier;

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";
		
		$orderBy	= "c.name asc";
	
		$qry = "select distinct b.fish,c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;
	}

	#Process Code Records
	function getProcessCodeList($fromDate, $tillDate, $selectSupplier, $fishId, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr = "b.fish_code=c.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."'";
	
		if ($selectSupplier==0) $whr .="";
		else $whr .=" and a.main_supplier=".$selectSupplier;

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";		
		$orderBy	=	"c.code asc";	

		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;	
	}

	#Same Entry Exist
	function checkEntryExist($challanId, $fishId, $processCodeId, $count, $gradeId)
	{
		
		$whr = "main_id='".$challanId."' and fish='".$fishId."' and fish_code='".$processCodeId."'";
	
		if ($count=="") $whr .="";
		else $whr .=" and count_values= '".$count."'";

		if ($gradeId=="") $whr .="";
		else $whr .=" and grade_id= '".$gradeId."'";
		
		$orderBy	=	"";
	
		$qry = "select id from t_dailycatchentry";
	
		if ($whr!="") $qry .= " where ".$whr;
		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?true:false;	
	}

	#Same Count Average Entry Exist
	function checkSameCountAverage($challanId, $fishId, $processCodeId, $countAverage)
	{	
		$whr = "main_id='".$challanId."' and fish='".$fishId."' and fish_code='".$processCodeId."' and average='".$countAverage."' ";
	
		$qry = "select id from t_dailycatchentry";	
		if ($whr!="") $qry .= " where ".$whr; 
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?true:false;	
	}

	# ---------------- For AJAX Search Ends Here-------------------	

	#-----------------For Modify Challan Ajax-----------------------
	#Fish Records for a Challan No
	function getRMChallanWiseFishList($challanNo, $billingCompany)
	{
		$whr = "b.fish=c.id and a.weighment_challan_no='$challanNo' and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id='$billingCompany' ";
		
		$orderBy	=	"c.name asc";
	
		$qry = "select distinct b.fish,c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";
	
		if($whr!="") $qry .= " where ".$whr;
		
		if($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Challan Wise Process Code
	function getRMChallanWiseProcessCodeList($challanNo, $fishId, $billingCompany)
	{
		$whr = "b.fish_code=c.id and a.weighment_challan_no='$challanNo' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' and a.billing_company_id='$billingCompany' ";	
				
		$orderBy	=	"c.code asc";
	
		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;	
	}

	function getRMEntryRecords($challanNo, $fishId, $processCodeId, $billingCompany)
	{
		$whr = " a.weighment_challan_no='$challanNo' and a.id=b.main_id and b.fish='".$fishId."' and b.fish_code='$processCodeId' and a.billing_company_id='$billingCompany' ";	
				
		$orderBy	= "b.count_values asc,c.code asc";
	
		$qry = "select b.id, b.grade_id, c.code, b.count_values, b.effective_wt from (t_dailycatch_main a, t_dailycatchentry b) left join m_grade c on b.grade_id=c.id";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# ----------------  Modify Challan Ajax Ends-------------------	
	# For Modification (Return Main ID & Entry ID)
	function findRMEntryRec($rMEntryId)
	{	
		$qry = "select a.id, b.id from t_dailycatch_main a, t_dailycatchentry b  where a.id=b.main_id and b.id='$rMEntryId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}
	

	# Update Modified Rec
	function updateModifiedRMChallanEntryRec($challanMainId, $challanEntryId, $entryAdjust, $reasonAdjust, $gradeCountAdj, $gradeCountAdjReason, $entryActualWt, $entryLocal, $reasonLocal, $entryWastage, $reasonWastage, $entrySoft, $reasonSoft, $currentUserId)
	{
		$qry = "update t_dailycatchentry set adjust='$entryAdjust', reason='$reasonAdjust', grade_count_adj='$gradeCountAdj', grade_count_adj_reason='$gradeCountAdjReason', actual_wt='$entryActualWt', local_quantity='$entryLocal', reason_local='$reasonLocal', wastage='$entryWastage', reason_wastage='$reasonWastage', soft='$entrySoft', reason_soft='$reasonSoft', modified=NOW(), modified_by='$currentUserId' where id='$challanEntryId' and main_id='$challanMainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check valid challan num
	function chkValidChallanNum($selDate, $challanNum, $billingCompany,$unitId)
	{
		$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$challanNum' and end_no>='$challanNum' and type='RM' and auto_generate='1' and unitid='0'";
		if($unitId!="") $qry.=" or  billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$challanNum' and end_no>='$challanNum' and type='RM' and auto_generate='1' and unitid='$unitId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	# Check Challan Exist
	function chkChallanExist($challanNum, $billingCompany, $cId, $unitId)
	{
		if ($cId) $uptQry = " and id!=$cId";

		$qry = " select id from t_dailycatch_main where weighment_challan_no='$challanNum' and billing_company_id='$billingCompany' and unit='$unitId' $uptQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	#Billing company Records for a date range	
	function getBillingCompanyList($fromDate, $tillDate, $selectSupplier)
	{
		$whr		= "a.billing_company_id=b.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	

		if ($selectSupplier) $whr .= " and a.main_supplier='$selectSupplier' ";
	
		$orderBy	= "b.display_name asc";

		$qry	= "select a.billing_company_id, b.display_name from t_dailycatch_main a, m_billing_company b";
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get date Wise Supplier
	function getDateWiseSupplier($selDate, $landingCenter)
	{
		$qry = "select tdm.main_supplier, supp.name from t_dailycatch_main tdm, supplier supp where tdm.main_supplier=supp.id and tdm.select_date='$selDate' and tdm.landing_center='$landingCenter' group by tdm.main_supplier order by supp.name asc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Filter Process Code
	function pcRecFilter($fishId)
	{
		$qry	= "select id, code, b_weight from m_processcode where fish_id='$fishId' and active='1' order by code asc";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getBasketWtProcess($processId)
	{
		$qry	= "select b_weight from m_processcode where id='$processId' order by code asc";
		//echo $qry;		
		$result = $this->databaseConnect->getRecord($qry);
		return ($result!='') ? $result[0]:"";
	}

	#Grade selection for Raw Grades
 	function gradeRecFilter($codeId)
	{
 		$qry =	"select b.id, b.code from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$codeId' and a.unit_select='r' order by b.code asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 	}

	# Get Recived Type
	function pcReceivedType($processCodeId)
	{
		$qry	= "select available_option from m_processcode where id='$processCodeId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}
	
	function fetchAllRecordsActivebillingCompany()
	{
		$qry	= "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active from m_billing_company where default_row='Y' order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllActiveRecordsbillingCompany()
	{
		$qry	= "select id, name, address, place, pin, country, telno, faxno, alpha_code, display_name, default_row,active from m_billing_company where active='1' order by name asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getLotIdName($rm_lot_id)
	{
		$qry	= "select CONCAT(alpha_character,rm_lotid) as lot_Id from t_manage_rm_lotid where id='$rm_lot_id'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function getSuppierDetails($rm_gate_pass_id)
	{
				
		$qry1="select lot_id_origin from t_manage_rm_lotid where id='$rm_gate_pass_id'";
		$result1	=	$this->databaseConnect->getRecord($qry1);
		
		if($result1[0]=='' || $result1[0]=='0')
		{
			//function 1
			$supplyDet=$this->getSuppierDet($rm_gate_pass_id);
			$resultval=$supplyDet;
			//print_r($supplyDet);
			//return $supplyDet;
		}
		else
		{
			//function 2
			$detail=$this->getSuppierDetails($result1[0]);
			$rm_gate_pass_id=$result1[0];
			$resultval=$detail;
		}

		return $resultval;

		
	}








	function getSuppierDet($rm_lot_id)
	{	
		
		$query="SELECT a.id,a.supplier_id,d.name,a.pond_id,e.pond_name,(select group_concat(id,'$$',name) from m_fish where id in (select species from t_phtcertificate where pond_Name = a.pond_id)) as species,'procurement' as value from t_rm_receipt_gatepass_supplier a left join t_manage_rmlotid_details b on a.id=b.receipt_id left join t_manage_rm_lotid c on c.id=b.rmlot_main_id left join supplier d on d.id=a.supplier_id left join m_pond_master e ON e.id = a.pond_id where c.id='$rm_lot_id' union SELECT a.id,a.supplier_id,c.name,a.driver,a.labours,a.out_seal,'receipt' as value from t_rmreceiptgatepass a left join t_manage_rmlotid_details b on a.id=b.receipt_gatepass_id left join supplier c on c.id=a.supplier_id where b.rmlot_main_id='$rm_lot_id' and b.receipt_id='0'";
		//$query="SELECT receipt_id,receipt_gate_pass_id FROM t_manage_rm_lotid where id='$rm_lot_id'";
		//echo $query;
		$results	=	$this->databaseConnect->getRecords($query);
		
		if(sizeof($results) > 0)
		{
			$result	=array();
			//echo $results[0];
			foreach($results as $res)
			{
				if($res[6]== 'procurement')
				{
					$qry1 = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
					a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
					b.supplier_id,f.name,b.challan_no,CONCAT(k.alpha_character,k.rm_lotid) as lot_Id,
					e.payment_by,f.payment_by,g.name,h.name,i.name,j.name,b.pond_id ,'procurement' as value
					from `t_rmreceiptgatepass` a 
					left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
					left join t_manage_rmlotid_details c on b.id = c.receipt_id 
					left join t_manage_rm_lotid k on k.id = c.rmlot_main_id 
					left join m_vehicle_master d on d.id = a.vehicle_Number 
					left join supplier e on e.id = a.supplier_id 
					left join supplier f on f.id = b.supplier_id 
					left join m_plant g on g.id=a.unit
					left join m_plant h on h.id=b.unit_id
					left join m_billing_company i on i.id=a.Company_Name
					left join m_billing_company j on j.id=b.company_id
					where b.id in ($res[0])";
					$qry1.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
					$qry1.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
					//$resultVal1	= $this->databaseConnect->getRecord($qry1);
					//array_push($result, $resultVal1);
					
					//echo $qry1;
					
				}
				else
				{
					$qry1 = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
					a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
					b.supplier_id,f.name,b.challan_no,CONCAT(k.alpha_character,k.rm_lotid) as lot_Id,
					e.payment_by,f.payment_by,g.name,h.name,i.name,j.name,b.pond_id,'receipt' as value  
					from `t_rmreceiptgatepass` a 
					left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
					left join t_manage_rmlotid_details c on b.id = c.receipt_id 
					left join t_manage_rm_lotid k on k.id = c.rmlot_main_id 
					left join m_vehicle_master d on d.id = a.vehicle_Number 
					left join supplier e on e.id = a.supplier_id 
					left join supplier f on f.id = b.supplier_id 
					left join m_plant g on g.id=a.unit
					left join m_plant h on h.id=b.unit_id
					left join m_billing_company i on i.id=a.Company_Name
					left join m_billing_company j on j.id=b.company_id
					where a.id in ($res[0])";
					$qry1.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
					//$qry.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
					//$resultVal2	= $this->databaseConnect->getRecord($qry2);
					//array_push($result, $resultVal2);
					
				//echo $qry2;
				}
				//echo $qry1;
				$resultVal	= $this->databaseConnect->getRecord($qry1);
				//array_push($result, $resultVal);
				$result[]=$resultVal;
				//$resultval=$result;	
			}
		}
		
		
	//echo $qry;
		//print_r($result);
		//$result	= $this->databaseConnect->getRecords($qry);
				return $result;
	}	
	function getSuppierDetails_old_last($rm_lot_id)
	{
		$query="SELECT receipt_id,receipt_gate_pass_id FROM t_manage_rm_lotid where id='$rm_lot_id'";
		$results	=	$this->databaseConnect->getRecord($query);
		if(sizeof($results) > 0)
		{
		//echo $results[0];
			if($results[0] != '0')
			{
				$qry = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
				b.supplier_id,f.name,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by,f.payment_by,g.name,h.name,i.name,j.name,b.pond_id  
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on b.id = c.receipt_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				left join m_plant g on g.id=a.unit
				left join m_plant h on h.id=b.unit_id
				left join m_billing_company i on i.id=a.Company_Name
				left join m_billing_company j on j.id=b.company_id
				where b.id in ($results[0])";
				$qry.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				$qry.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				
				
				
				//echo $qry;
				
			}
			else
			{
				$qry = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
				b.supplier_id,f.name,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by,f.payment_by,g.name,h.name,i.name,j.name,b.pond_id  
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on a.id = c.receipt_gate_pass_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				left join m_plant g on g.id=a.unit
				left join m_plant h on h.id=b.unit_id
				left join m_billing_company i on i.id=a.Company_Name
				left join m_billing_company j on j.id=b.company_id
				where a.id in ($results[1])";
				$qry.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				//$qry.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
			
			}
		}
		
		
	//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
				return $result;
	}	
	function getSuppierDetails_old($rm_lot_id)
	{
		$query="SELECT receipt_id FROM t_manage_rm_lotid where id='$rm_lot_id'";
		$results	=	$this->databaseConnect->getRecord($query);
		if(sizeof($results) > 0)
		{
			if($results[0] != '')
			{
				$qry = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
				b.supplier_id,f.name,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by,f.payment_by,g.name,h.name,i.name,j.name,b.pond_id  
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on b.id = c.receipt_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				left join m_plant g on g.id=a.unit
				left join m_plant h on h.id=b.unit_id
				left join m_billing_company i on i.id=a.Company_Name
				left join m_billing_company j on j.id=b.company_id
				where b.id in ($results[0])";
				$qry.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				$qry.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				
				
				
				//echo $qry;
				$result	= $this->databaseConnect->getRecords($qry);
				return $result;
			}
		}
	}	
	function getSuppierDetails_weightment($rm_lot_id)
	{
		$query="SELECT receipt_id,alpha_character,rm_lotid FROM t_manage_rm_lotid where id='$rm_lot_id'";
		$results	=	$this->databaseConnect->getRecord($query);
		if(sizeof($results) > 0)
		{
			$rmLd=$results[1].$results[2];	
			if($results[0] != '')
			{
				$qry1 = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id as supplierSingle,e.name  as supplierName,b.company_id,b.unit_id,d.vehicle_number as vehicle,
				b.supplier_id as SupplierMul,f.name as SupplierMulName,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by as paymentSingle,f.payment_by  as paymentMul
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on b.id = c.receipt_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				where b.id in ($results[0])";
				$qry1.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				$qry1.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
			//echo $qry1;
				
				
		
				
			
				/*impt code*/
				$qry2 ="SELECT d.id,d.procurment_Gate_PassId,d.Company_Name,d.unit,d.vehicle_number_other,d.supplier_Challan_No,b.supplier_name as supplierSingle,h.name as supplierName,e.company_id,e.unit_id,g.vehicle_number as vehicle,b.supplier_name as SupplierMul,j.name as SupplierMulName,e.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id, h.payment_by as paymentSingle,j.payment_by as paymentMul FROM weighment_data_sheet a left join t_weightment_data_entries b on a.id=b.weightment_data_sheet_id left join t_manage_rm_lotid c on c.id=a.rm_lot_id left join t_rmreceiptgatepass d on d.id=c.receipt_gate_pass_id left join t_rm_receipt_gatepass_supplier e on e.receipt_gatepass_id=d.id left join m_vehicle_master g on g.id = d.vehicle_Number left join supplier h on h.id = b.supplier_name left join supplier j on j.id = b.supplier_name where a.rm_lot_id='".$rm_lot_id."'";
				$qry2.= " and b.supplier_name not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				//$qry2.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
						 /*$qry2 ="SELECT a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,a.supplier_Challan_No,a.supplier_id as supplierSingle,h.name as supplierName,b.company_id,b.unit_id,g.vehicle_number as vehicle,d.supplier_name as SupplierMul,j.name as SupplierMulName, b.challan_no,CONCAT(e.alpha_character,e.rm_lotid) as lot_Id, h.payment_by as paymentSingle,j.payment_by as paymentMul FROM `t_rmreceiptgatepass` a left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id left join t_manage_rm_lotid e on a.lot_Id=CONCAT(e.alpha_character,e.rm_lotid) left join weighment_data_sheet c on c.rm_lot_id=e.id left join t_weightment_data_entries d on c.id=d.weightment_data_sheet_id left join m_vehicle_master g on g.id = a.vehicle_Number left join supplier h on h.id = a.supplier_id left join supplier j on j.id = d.supplier_name WHERE `lot_Id` REGEXP '(^|,)".$rmLd."($|,)'";*/
					//echo $qry2;
					
				$qry=" select * from ($qry1 union $qry2)  as X group BY id";
			//echo $qry2;
				echo $qry;
				$result	= $this->databaseConnect->getRecords($qry);
				return $result;
				
				
				
			}
		}
	}
	
		
		function getSuppierDetails_athi($rm_lot_id)
	{
		$query="SELECT receipt_id,alpha_character,rm_lotid FROM t_manage_rm_lotid where id='$rm_lot_id'";
		$results	=	$this->databaseConnect->getRecord($query);
		if(sizeof($results) > 0)
		{
			if($results[0] != '')
			{
				$qry1 = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id as supplierSingle,e.name  as supplierName,b.company_id,b.unit_id,d.vehicle_number as vehicle,
				b.supplier_id as SupplierMul,f.name as SupplierMulName,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by as paymentSingle,f.payment_by  as paymentMul
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on b.id = c.receipt_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				where b.id in ($results[0])";
				$qry1.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				$qry1.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				//echo $qry1;
				$result1	= $this->databaseConnect->getRecords($qry1);
				
		
				
			$rmLd=$results[1].$results[2];	
				/*impt code*/
					 $qry2 ="SELECT a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,a.supplier_Challan_No,a.supplier_id as supplierSingle,h.name as supplierName,b.company_id,b.unit_id,g.vehicle_number as vehicle,d.supplier_name as SupplierMul,j.name as SupplierMulName, b.challan_no,CONCAT(e.alpha_character,e.rm_lotid) as lot_Id, h.payment_by as paymentSingle,j.payment_by as paymentMul FROM `t_rmreceiptgatepass` a left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id left join t_manage_rm_lotid e on a.lot_Id=CONCAT(e.alpha_character,e.rm_lotid) left join weighment_data_sheet c on c.rm_lot_id=e.id left join t_weightment_data_entries d on c.id=d.weightment_data_sheet_id left join m_vehicle_master g on g.id = a.vehicle_Number left join supplier h on h.id = a.supplier_id left join supplier j on j.id = d.supplier_name WHERE `lot_Id` REGEXP '(^|,)".$rmLd."($|,)'";
					 $qry2.= " and a.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
					$qry2.= " and b.supplier_id not in (select payment  from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
				$result2	= $this->databaseConnect->getRecords($qry2);			
				//$qry=" select * from ($qry1 union $qry2)  as X group BY id";
				//echo $qry;
				if($result2[11] > 0)
				{
				//echo $qry2;
				$result	=$result2;
				}
				else{
				//echo "hui";
				$result	=$result1;
				}
				//$result	= $this->databaseConnect->getRecords($qry);
				return $result;
				
				
				
			}
		}
		#--------------------------------------------------------------------------------------------------------------
		#query vel murugan
		/*$qry = "select a.id,a.procurment_Gate_PassId,a.Company_Name,a.unit,a.vehicle_number_other,
				a.supplier_Challan_No,a.supplier_id,e.name,b.company_id,b.unit_id,d.vehicle_number,
				b.supplier_id,f.name,b.challan_no,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id,
				e.payment_by,f.payment_by  
				from `t_rmreceiptgatepass` a 
				left join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				left join t_manage_rm_lotid c on b.id = c.receipt_id 
				left join m_vehicle_master d on d.id = a.vehicle_Number 
				left join supplier e on e.id = a.supplier_id 
				left join supplier f on f.id = b.supplier_id 
				where a.id = (select receipt_gatepass_id from t_rm_receipt_gatepass_supplier where id = 
				(select receipt_id from t_manage_rm_lotid where id = '".$rm_lot_id."')) ";
		$qry.= " and a.supplier_id not in (select main_supplier from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
		$qry.= " and b.supplier_id not in (select main_supplier from t_dailycatch_main where rm_lot_id='".$rm_lot_id."') ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;*/
	}
	function getQualityDetails($supplier_name,$pond_name,$rm_lot_id)
	{
		$qry = "select id,name,billing_include from m_quality where id in
				(select quality_id from t_weightment_data_entries where supplier_name = '$supplier_name' and pond_name = '$pond_name' 
				and weightment_data_sheet_id = (select id from weighment_data_sheet where rm_lot_id = '$rm_lot_id')) and id not in(5,6,7)";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllSpecies()
	{
		$qry	= "select id,name from m_fish where active=1 and source_id != 1 order by name";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllQualityMasterRecords()
	{
		
	
		$qry	= "select id,name,billing_include from m_quality where active=1 and change_requirement!= 1 order by name";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSingleQuantityDet($quality)
	{	
		$qry	= "select id,name,billing_include from m_quality where id='$quality'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function deleteDailyCatchEntryQualityWtSingle($id)
	{
		$qry	=	"delete from t_qualityentry where id=$catchEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			return $result;
	}
	function checkProcurementStatus($rmlotid)
	{
		$qry	=	"SELECT receipt_id FROM t_manage_rmlotid_details where rmlot_main_id='$rmlotid' and receipt_id!='0'";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function checkLotIdInWeightmentDatasheet($rmLotId)
	{
		$qry	=	"SELECT id,active FROM weighment_data_sheet where rm_lot_id='$rmLotId'";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllDetailsWeighment($rmLotId)
	{
		$qry	=	"SELECT b.id, b.supplier_name,b.pond_name,b.product_species,b.process_code_id,b.quality_id,b.count_code,b.weight,b.soft_per,b.soft_weight,c.name,d.pond_name,e.name,f.code,concat(g.alpha_character,g.rm_lotid) as rmlotid,h.id,h.name,i.id,i.name,c.payment_by FROM weighment_data_sheet a left join  t_weightment_data_entries b  on a.id= b.weightment_data_sheet_id left join supplier c on c.id=b.supplier_name left join m_pond_master d on d.id=b.pond_name  left join m_fish e on e.id=b.product_species left join m_processcode f on f.id=b.process_code_id left join t_manage_rm_lotid g on g.id=a.rm_lot_id left join m_plant h on h.id=g.unit_id left join m_billing_company i on i.id=g.company_id
		where a.rm_lot_id='$rmLotId' and b.id not in (select k.weightment_id_available from t_dailycatchentry k left join t_dailycatch_main l on k.main_id=l.id where l.rm_lot_id='$rmLotId')";
		$result	=	$this->databaseConnect->getRecords($qry);
		$returnVal = '';
		$makePayment = 0;
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%" border="1" cellpadding="0" cellspacing="0" >';
			$returnVal.='<tr height="30" align="center"><td class="listing-head" colspan="7" > '.$result[0][14].' - Suppliers  </td></tr>';
			//$returnVal.='<tr><td class="listing-head" colspan="4"> '.$result[0][14].' - Suppliers  </td></tr>';
			$returnVal.='<tr height="30" align="center">';	
			$returnVal.='<td class="listing-head" style="padding:5px; ">Supplier Name</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Pond Name</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Unit</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Company</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Species</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Process code</td>';
			$returnVal.='<td class="listing-head" style="padding:5px; ">Count code</td>';
			//$returnVal.='<td>Supplier Name</td>';
			//$returnVal.='<td>Supplier Name</td>';
			$returnVal.='</tr>';	
			foreach($result as $res)
			{ 
				/*if($i!=0)
				{
					if($i%4 == 0)
					{
						$returnVal.= '</tr><tr>';
					}
				}*/
				
					$weightmentId=$res[0];
					$supplierId = $res[1];
					$supplierName = $res[10];
					$unitId = $res[15];
					$unitName=$res[16];
					$companyId=$res[17];
					$companyNm=$res[18];
					$pondId=$res[2];
					$pondNm=$res[11];
					$species=$res[12];
					$speciesId=$res[3];
					$processcodeId=$res[4];
					$processcode=$res[13];
					
					$countcode=$res[6];
					$chellan  = $this->getAllSupplierChellanNumber($rmLotId,$supplierId,$pondId);
					$supplierChallanNo = $chellan[0];
					if($res[19] == 'D')
					{
						$makePayment = 1;
					}
					
				$returnVal.='<tr height="30" align="center">';	
					$returnVal.= '<td  class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$supplierName.'</a></td>';
					
					$returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$pondNm.'</a></td>';
					
					$returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$unitName.'</a></td>';
					
					 $returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$companyNm.'</a></td>';
					 
					 $returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$species.'</a></td>';
					 
					 $returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$processcode.'</a></td>';
					 
					$returnVal.= '<td class="listing-item" style="padding:5px; "><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetailsWeightment('.$weightmentId.','.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.','.$speciesId.','.$processcodeId.','.$countcode.');" style="color:#800000; text-decoration:none;">'.$countcode.'</a></td>';
				
				$returnVal.= '</tr>';
				

				###********************** protected query
				
				/*$returnVal.='<tr>';	
				$returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.','.$pondNm.','.$unitName.','.$companyNm.'</a></td>';*/
				
				###**********************************************************************
				 
				$returnVal.= '</tr>';
				/*$returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$pondId.','.$unitId.','.$companyId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';*/
				 
				 
				// $returnVal.= '<td class="listing-head"><a class="supplier_name_'.$supplierId.'" href="javascript:void(0);" onclick="assignSupplierDetails('.$supplierId.','.$unitId.','.$supplierChallanNo.','.$makePayment.');">'.$supplierName.'</a></td>';
				// $i++;
				
				
			}
			$returnVal.= '</table>';
			
			//$returnVal.= '</tr></table>';
		}
		return $returnVal;
		
	}
	function getAllSupplierChellanNumber($rmLotId,$supplierId,$pondId)
	{
		$qry	=	"SELECT challan_no FROM  t_manage_rmlotid_details where rmlot_main_id='$rmLotId' and supplier_id='$supplierId' and 	farm_id='$pondId'";
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function getAlphaCode($unitId,$companyId)
	{
		$date=date("Y-m-d");
		$qry	=	"select alpha_code from number_gen where '$date' between start_date and end_date and billing_company_id='$companyId' AND challan_status!='1' and type='RM' and auto_generate='1' and unitid='0'";
		if ($unitId!="") $qry .= "or '$date' between start_date and end_date and billing_company_id='$companyId' AND challan_status!='1' and type='RM' and auto_generate='1' and unitid=$unitId";

		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}
}
?>