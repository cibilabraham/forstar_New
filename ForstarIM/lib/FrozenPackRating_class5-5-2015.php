<?php
class FrozenPackRating
{
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing Quick Entry List
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function FrozenPackRating(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert Rec
	function addFznPckRate($qeName,$freezingStage,$frozenCode,$selQuality,$userId)
	{
		$qry	 = "insert into m_frznpackrating (name,freezing_id,frozen_id,quality_id,created_on, created_by) values('$qeName','$freezingStage','$frozenCode', '$selQuality',NOW(),'$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Insert Rec
	function addFznPkgRateEntry($qelId, $selFish, $selProcessCode)
	{
		$qry	 = "insert into t_frznpackrate_entry (frznrate_id, fish, processcode_id) values('$qelId', '$selFish', '$selProcessCode')";
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
			$qry = " delete from t_frznpackrate_grade where frznrate_id='$qelEntryId' ";
		} else {
			$qry = " delete from t_frznpackrate_grade where (frznrate_id is null || frznrate_id=0) and created_by='$userId' ";
		}
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	function getGradeRecords($entryId)
	{
		$qry = " select a.grade_id, c.code, a.id, a.display_order from t_frznpackrate_grade a, m_grade c where a.grade_id = c.id and a.frznrate_id='$entryId' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function addSelGradeRec($entryId, $gradeId, $displayOrder,$rate,$userId,$gradeStatus)
	{
		$qry	 = "insert into t_frznpackrate_grade (frznrate_id, grade_id, display_order,rate,created_by) values('$entryId', '$gradeId', '$displayOrder','$rate','$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

		
	function getSelGradeRecords($userId, $qelEntryId)
	{
		$whr = " a.grade_id = c.id ";

		if ($qelEntryId=="") $whr .= " and a.created_by='$userId' and (a.frznrate_id is null or a.frznrate_id=0) ";
		else $whr .= " and a.frznrate_id='$qelEntryId' ";

		$orderBy	= " a.display_order asc ";		

		$qry = " select a.grade_id, c.code, a.id, a.display_order, a.frznrate_id,a.rate from t_frznpackrate_grade a, m_grade c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy) $qry .= " order by ".$orderBy;
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
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
		$qry	= " delete from temp_pc_rate where user_id=$userId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	
	# Insert Temp recs
	function insertTempPCRec($processCodeId, $userId)
	{
		$qry = "insert into temp_pc_rate (`processcode_id`, `user_id`) values('$processCodeId', '$userId')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getTempPCRecs($userId) 
	{
		$qry = "select id from temp_pc_rate ";
		if ($userId) $qry .= " where user_id='$userId'";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	function getGradeNotSelPC($gradeId, $userId)
	{
		$qry = "select a.id, b.id, mpc.code, a.processcode_id from temp_pc_rate a left join m_processcode2grade b on a.processcode_id=b.processcode_id and b.grade_id='$gradeId' and b.unit_select='f' left join m_processcode mpc on a.processcode_id=mpc.id where a.user_id='$userId' and b.id is null order by mpc.code asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	# Process Code Grade Records
	function getPCGradeRecords($processCodes)
	{
		$qry = " select distinct a.grade_id, c.code from m_processcode2grade a, m_grade c where a.grade_id = c.id and a.processcode_id  in ($processCodes) and a.unit_select='f' order by c.code asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function addGradeRec($qelEntryId, $gradeId, $g, $userId,$rate)
	{
		$qry	 = "insert into t_frznpackrate_grade (frznrate_id, grade_id, display_order, created_by,rate) values('$qelEntryId','$gradeId', '$g', '$userId', '$rate')";
		//echo $qry;
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

		$qry	= " select a.id, a.name, a.freezing_id, a.frozen_id, a.quality_id from m_frznpackrating a ";
		
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
		
		$qry	= "select a.id, a.name, a.freezing_id, a.frozen_id, a.quality_id from m_frznpackrating a";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Processcode Records
	function getProcessCodeRecs($fznPkngRateId)
	{
		$qry = " select a.processcode_id, b.code from t_frznpackrate_entry a, m_processcode b where a.processcode_id=b.id and a.frznrate_id='$fznPkngRateId' order by a.id asc ";
	//	echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Packing  based on id 
	function find($fznPkngRateId)
	{
		$qry	= "select a.id, a.name, a.freezing_id, a.frozen_id,a.quality_id from m_frznpackrating a where a.id='$fznPkngRateId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Raw data Records
	function getQELRawRecords($fznPkngRateId)
	{
		$qry = " select id, fish, processcode_id from t_frznpackrate_entry where frznrate_id='$fznPkngRateId' order by id asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update
	function updateFznPkngRateRec($fznPkngRateId, $qeName, $freezingStage,$frozenCode,$selQuality)
	{	
		$qry = "update m_frznpackrating set  name='$qeName', freezing_id='$freezingStage',frozen_id='$frozenCode', quality_id='$selQuality' where id='$fznPkngRateId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# del rate Entry Rec
	function delRateRawData($fznPkngRateId)
	{
		$qry = " delete from t_frznpackrate_entry where frznrate_id='$fznPkngRateId'";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Del Temp Grade Rec
	function deleteRateGradeRec($qelEntryId)
	{
		$qry = " delete from t_frznpackrate_grade where frznrate_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	

	# Delete 
	function deleteFznPkngRateRec($fznPkngRateId)
	{
		$qry	=	" delete from m_frznpackrating where id='$fznPkngRateId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
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
			$updateDisOrderRecF = $this->updateDisplayOrder($entryIdF, $disOrderIdF);
		}

		if ($entryIdS!="") {
			$updateDisOrderRecS = $this->updateDisplayOrder($entryIdS, $disOrderIdS);
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
	function updateDisplayOrder($entryId, $displayOrder)
	{
		$qry = "update t_frznpackrate_grade set display_order='$displayOrder' where id='$entryId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/























	function addFznPkngQuickEntryList($qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $userId, $brandFrom)
	{
		$qry	 = "insert into t_fznpakng_quick_entry (name, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, created, createdby, brand_from) values('$qeName', '$freezingStage', '$eUCode', '$brand', '$frozenCode', '$mCPacking', '$frozenLotId', '$exportLotId', '$selQuality', '$selCustomerId', NOW(), '$userId', '$brandFrom')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
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
	function updateFznPkngEntryRec($fznPkngQuickEntryListId, $qeName, $freezingStage, $selQuality, $eUCode, $brand, $selCustomerId, $frozenCode, $mCPacking, $frozenLotId, $exportLotId, $brandFrom)
	{	
		$qry = "update t_fznpakng_quick_entry set  name='$qeName', freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', frozen_lot_id='$frozenLotId', export_lot_id='$exportLotId', quality_id='$selQuality', customer_id='$selCustomerId', brand_from='$brandFrom' where id='$fznPkngQuickEntryListId' ";
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

	
/*	# Get Raw data Records
	function getQELRawRecords($fznPkngQuickEntryListId)
	{
		$qry = " select id, fish_id, processcode_id from t_fznpakng_qel_entry where frznrate_id='$fznPkngQuickEntryListId' order by id asc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}*/	

	# el QEL Entry Rec
	function delQELRawData($fznPkngQuickEntryListId)
	{
		$qry = " delete from t_fznpakng_qel_entry where frznrate_id='$fznPkngQuickEntryListId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}



	
	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
/*
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
	
*/
	/********************* Display Order End Here****************************/

/*	function addSelGradeRec($entryId, $gradeId, $displayOrder, $userId, $gradeStatus)
	{
		$qry	 = "insert into t_fznpakng_qel_grade (frznrate_id, grade_id, display_order, created_by, active) values('$entryId', '$gradeId', '$displayOrder', '$userId', '$gradeStatus')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	function getGradeRecords($entryId)
	{
		$qry = " select a.grade_id, c.code, a.id, a.display_order from t_fznpakng_qel_grade a, m_grade c where a.grade_id = c.id and a.frznrate_id='$entryId' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}*/

	# Del Temp Grade Rec
	function deleteQELGradeRec($qelEntryId)
	{
		$qry = " delete from t_fznpakng_qel_grade where frznrate_id='$qelEntryId' ";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	# Get Max Dis Order Id
	function getMaxDisplayOrderId($qelEntryId)
	{
		$qry = " select max(display_order) from t_fznpakng_qel_grade where frznrate_id='$qelEntryId'";
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

	function addGradeToPC($processCodeId, $gradeId)
	{
		$qry	= "insert into m_processcode2grade (processcode_id, grade_id, unit_select) values('".$processCodeId."','".$gradeId."','f')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
}
?>