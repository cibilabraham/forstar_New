<?php
class FrozenPackingQuickEntryList
{
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing Quick Entry List
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackingQuickEntryList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert Rec
	function addFznPkngQuickEntryList($qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $userId, $brandFrom,$codeType)
	{
		$qry	 = "insert into t_fznpakng_quick_entry (name, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, created, createdby, brand_from,processcode_type) values('$qeName', '$freezingStage', '$eUCode', '$brand', '$frozenCode', '$mCPacking', '$frozenLotId', '$exportLotId', '$selQuality', '$selCustomerId', NOW(), '$userId', '$brandFrom','$codeType')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert Rec
	function addFznPkgRawEntry($qelId, $selFish, $selProcessCode,$codeType)
	{
		$qry	 = "insert into t_fznpakng_qel_entry (qe_entry_id, fish_id, processcode_id,process_type) values('$qelId', '$selFish', '$selProcessCode','$codeType')";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# get All Records
	function fetchAllPagingRecords($offset, $limit)
	{
		//$whr	= " a.name is not null" ;
						
		$orderBy = "a.name asc";
		$limit  = " $offset, $limit ";

		$qry	= " select a.id, a.name, a.freezing_stage_id, a.eucode_id, a.brand_id, a.frozencode_id, a.mcpacking_id, a.frozen_lot_id, a.export_lot_id from t_fznpakng_quick_entry a ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="")   $qry .= " limit ".$limit;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords()
	{
		//$whr	= " a.name is not null" ;
						
		$orderBy = "a.name asc";
		
		$qry	= "select a.id, a.name, a.freezing_stage_id, a.eucode_id, a.brand_id, a.frozencode_id, a.mcpacking_id, a.frozen_lot_id, a.export_lot_id from t_fznpakng_quick_entry a ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Packing  based on id 
	function find($fznPkngQuickEntryListId)
	{
		$qry	= "select a.id, a.name, a.freezing_stage_id, a.eucode_id, a.brand_id, a.frozencode_id, a.mcpacking_id, a.frozen_lot_id, a.export_lot_id, a.quality_id, a.customer_id, a.brand_from,a.processcode_type from t_fznpakng_quick_entry a where a.id='$fznPkngQuickEntryListId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	#Filter Lot Id Records Based on Date
	function fetchLotIdRecords($selDate)
	{
		$qry	=	"select a.id, b.id, c.freezer_name from t_dailyactivitychart_main a, t_dailyactivitychart_entry b, m_freezercapacity c where a.id=b.main_id and c.id=b.freezer_no and a.entry_date='$selDate' and a.flag=1 order by a.id asc, b.id asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update
	function updateFznPkngEntryRec($fznPkngQuickEntryListId, $qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $brandFrom,$codeType)
	{	
		$qry = "update t_fznpakng_quick_entry set  name='$qeName', freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', frozen_lot_id='$frozenLotId', export_lot_id='$exportLotId', quality_id='$selQuality', customer_id='$selCustomerId', brand_from='$brandFrom' ,processcode_type='$codeType'	where id='$fznPkngQuickEntryListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete 
	function deleteFznPkngQuickEntryRec($fznPkngQuickEntryListId)
	{
		$qry	=	" delete from t_fznpakng_quick_entry where id='$fznPkngQuickEntryListId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Get Processcode Records
	function getProcessCodeRecs($fznPkngQuickEntryListId)
	{
		$qry = " select a.processcode_id, b.code from t_fznpakng_qel_entry a, m_processcode b where a.processcode_id=b.id and a.qe_entry_id='$fznPkngQuickEntryListId' order by a.id asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Raw data Records
	function getQELRawRecords($fznPkngQuickEntryListId)
	{
		$qry = " select id, fish_id, processcode_id from t_fznpakng_qel_entry where qe_entry_id='$fznPkngQuickEntryListId' order by id asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# el QEL Entry Rec
	function delQELRawData($fznPkngQuickEntryListId)
	{
		$qry = " delete from t_fznpakng_qel_entry where qe_entry_id='$fznPkngQuickEntryListId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Process Code Grade Records
	function getPCGradeRecords($processCodes)
	{
		$qry = " select distinct a.grade_id, c.code from m_processcode2grade a, m_grade c where a.grade_id = c.id and a.processcode_id  in ($processCodes) and a.unit_select='f' order by c.code asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function addGradeRec($qelEntryId, $gradeId, $g, $userId, $gradeStatus='N')
	{
		$qry	 = "insert into t_fznpakng_qel_grade (qe_entry_id, grade_id, display_order, created_by, active) values('$qelEntryId','$gradeId', '$g', '$userId', '$gradeStatus')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Del Temp Grade Rec
	function delTempGradeRec($userId, $qelEntryId)
	{
		$selGradeRecs = "";
		if ($qelEntryId) $selGradeRecs	= $this->getGradeRecords($qelEntryId);
		if (sizeof($selGradeRecs)>0) {
			$qry = " delete from t_fznpakng_qel_grade where qe_entry_id='$qelEntryId' ";
		} else {
			$qry = " delete from t_fznpakng_qel_grade where (qe_entry_id is null || qe_entry_id=0) and created_by='$userId' ";
		}
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	function getSelGradeRecords($userId, $qelEntryId)
	{
		$whr = " a.grade_id = c.id ";

		if ($qelEntryId=="") $whr .= " and a.created_by='$userId' and (a.qe_entry_id is null or a.qe_entry_id=0) ";
		else $whr .= " and qe_entry_id='$qelEntryId' ";

		$orderBy	= " a.display_order asc ";		

		$qry = " select a.grade_id, c.code, a.id, a.display_order, a.qe_entry_id, a.active from t_fznpakng_qel_grade a, m_grade c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy) $qry .= " order by ".$orderBy;

		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	
	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
	function changeDisplayOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeDisOrderF = $splitRec[0];
		$changeDisOrderS = $splitRec[1];
		list($entryIdF, $disOrderIdF) = $this->getSplittedRec($changeDisOrderF);
		list($entryIdS, $disOrderIdS) = $this->getSplittedRec($changeDisOrderS);
		if ($entryIdF!="") {
			$updateDisOrderRecF = $this->updateQELDisplayOrder($entryIdF, $disOrderIdF);
		}

		if ($entryIdS!="") {
			$updateDisOrderRecS = $this->updateQELDisplayOrder($entryIdS, $disOrderIdS);
		}
		return ($updateDisOrderRecF || $updateDisOrderRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getSplittedRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):array();
	}

	# update Menu Order
	function updateQELDisplayOrder($entryId, $displayOrder)
	{
		$qry = "update t_fznpakng_qel_grade set display_order='$displayOrder' where id='$entryId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/

	function addSelGradeRec($entryId, $gradeId, $displayOrder, $userId, $gradeStatus,$codeType)
	{
		$qry	 = "insert into t_fznpakng_qel_grade (qe_entry_id, grade_id, display_order, created_by, active,process_type) values('$entryId', '$gradeId', '$displayOrder', '$userId', '$gradeStatus','$codeType')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	
	function addSelGradeSubRec($entryId, $gradeId, $displayOrder, $userId, $gradeStatus,$codeType,$sublId)
	{
		$qry	 = "insert into t_fznpakng_qel_grade (qe_entry_id, grade_id, display_order, created_by, active,process_type,qel_subentry_id) values('$entryId', '$gradeId', '$displayOrder', '$userId', '$gradeStatus','$codeType','$sublId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getGradeRecords($entryId)
	{
		$qry = " select a.grade_id, c.code, a.id, a.display_order from t_fznpakng_qel_grade a, m_grade c where a.grade_id = c.id and a.qe_entry_id='$entryId' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# Del Temp Grade Rec
	function deleteQELGradeRec($qelEntryId)
	{
		$qry = " delete from t_fznpakng_qel_grade where qe_entry_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Get Max Dis Order Id
	function getMaxDisplayOrderId($qelEntryId)
	{
		$qry = " select max(display_order) from t_fznpakng_qel_grade where qe_entry_id='$qelEntryId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];	
	}

	# get Imploded Process Code
	function getDefaultGradeRecs($fznPkngQuickEntryListId)
	{
		$result = $this->getQELRawRecords($fznPkngQuickEntryListId);
		$nResultArr = array();
		$i = 0;
		foreach ($result as  $r) {
			$rPc = $r[2];
			$nResultArr[$i] = $rPc;		
			$i++;
		}
		$selPCodes = implode(",",$nResultArr);
		if (sizeof($selPCodes)>0) $defaultPCodeResult = $this->getPCGradeRecords($selPCodes);
		return $defaultPCodeResult;
	}

	function getGradeRecDiffSize($userId, $gradeQELId)
	{
		$getGradeRecords = $this->getDefaultGradeRecs($gradeQELId);
		# If QEL ID Exist
		$searchArr = "";	
		if (sizeof($getGradeRecords)>0 && $gradeQELId!="") {
			$selGradeArr = array();
			# Get Sel Grade Recs
			$getSelGradeRecords	= $this->getSelGradeRecords($userId, $gradeQELId);
			$nSelGradeArr = array();
			$k = 0;
			foreach ($getGradeRecords as $gr) {
				$nsGradeId = $gr[0];				
				$nSelGradeArr[$k] = $nsGradeId;
				$k++;
			}
			$sGradeArr = array();
			$m = 0;
			foreach ($getSelGradeRecords as $cRec) {
				$sGradeId = $cRec[0];
				$sGradeArr[$m] = $sGradeId;
				$m++;
			}
			$searchArr = array_diff($nSelGradeArr,$sGradeArr);
			
		} # Chk $gradeQELId Ends Here

		return sizeof($searchArr);
	}

	# insert recs
	function insertTempPCRecs($selProcesscodes, $userId)
	{
		$this->deletePCTempTable($userId);
		$processCodes = explode(",",$selProcesscodes);
		for ($k=0; $k<sizeof($processCodes);$k++) {	
			$processCodeId = $processCodes[$k];
			$this->insertTempPCRec($processCodeId, $userId);
		}
		return true;
	}
	
	# Create temp table
	function deletePCTempTable($userId)
	{
		$qry	= " delete from temp_pc_qel where user_id=$userId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Insert Temp recs
	function insertTempPCRec($processCodeId, $userId)
	{
		$qry = "insert into temp_pc_qel (`processcode_id`, `user_id`) values('$processCodeId', '$userId')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getTempPCRecs($userId) 
	{
		$qry = "select id from temp_pc_qel ";
		if ($userId) $qry .= " where user_id='$userId'";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function getGradeNotSelPC($gradeId, $userId)
	{
		$qry = "select a.id, b.id, mpc.code, a.processcode_id from temp_pc_qel a left join m_processcode2grade b on a.processcode_id=b.processcode_id and b.grade_id='$gradeId' and b.unit_select='f' left join m_processcode mpc on a.processcode_id=mpc.id where a.user_id='$userId' and b.id is null order by mpc.code asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function addGradeToPC($processCodeId, $gradeId)
	{
		$qry	= "insert into m_processcode2grade (processcode_id, grade_id, unit_select) values('".$processCodeId."','".$gradeId."','f')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

# Filter cheical List
	function getSecondaryGrade($secondaryId)
	{
		//$qry="select a.id,a.supplier_name,b.name from m_supplier_group_details a join supplier b on a.supplier_name=b.id where supplier_group_name_id='$supplierGroupId' order by supplier_name asc";
		$qry="SELECT  b.id,b.code from m_secondary_processcode a left join  m_grade b on a.secondary_grade=b.id where a.id='$secondaryId'"; 
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		/*if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');
	*/
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getFishId()
	{
		$qry="SELECT  b.id from m_fish where name='Assorted'"; 
		$result= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
	function getGradeRec($qelEntryId)
	{
		$qry="SELECT  grade_id from t_fznpakng_qel_grade where qel_subentry_id='$qelEntryId'"; 
		//echo $qry;
		$result= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
}
?>