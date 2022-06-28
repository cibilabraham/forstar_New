<?php
class WeightmentAfterGrading
{  
	
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WeightmentAfterGrading(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function getSupplierDetail($rmLotId)
	{
		 $qry	=	"select id, farmer_at_harvest,total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		$result	= $this->databaseConnect->getRecord($qry);
		
		return $result;
	}
	
	# Filter grade List
	function getGrade($gradeId)
	{
	
		//$qry	=h	"select id, grade_count from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//$qry	=	"select id, grade_count from weighment_data_sheet_grade_count where main_id='$gradeId' order by id asc";
		$qry	=	"select id, grade_count from t_weightment_data_entries where weightment_data_sheet_id='$gradeId' order by id asc";
		//echo $qry;
		// $result	= $this->databaseConnect->getRecords($qry);
		// return $result;
		//$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# Filter get weight
	function getWeight($rmLotId)
	{
		$qry	=	"select id, total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
		// $result = array();
		// $result = $this->databaseConnect->getRecords($qry);
		// if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		// else if (sizeof($result)==1) $resultArr = array();
		// else $resultArr = array(''=>'-- Select --');

		// while (list(,$v) = each($result)) {
			// $resultArr[$v[0]] = $v[1];
		// }
		// return $resultArr;
	}
	
	function getGradeId($rmLotId)
	{
		$qry	=	"select id  from weighment_data_sheet where rm_lot_id='$rmLotId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
		
	}
	function addWeightAfterGrading($rmLotId, $supplyDetails, $total, $totalwt,$difference,$userId)
	{
		$qry	= "insert into t_rmWeightAfterGrading(rmLotId, supplyDetails,sumtotal,totalweight,difference,created_on, created_by) values('$rmLotId','$supplyDetails','$total','$totalwt','$difference', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function addWeightAfterGradingDetails($lastId, $grandsingle, $weightsingle,$userId)
	{
		$qry	= "insert into t_rmWeightAfterGradingDetails(gradeID,gradeType,weight,created_on, created_by) values('$lastId','$grandsingle','$weightsingle', Now(),'$userId')";
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
		$qry	= "select  ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all RM Test Data
	function fetchAllRecords()
	{
		$qry	= "select ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` order by created_on desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		 $qry	= "select  ID, rmLotId, supplyDetails,sumtotal,totalweight,difference  from `t_rmweightaftergrading` where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getWeightAfterGradingDetail($WeightGradingId)
	{
		$qry	=	"select id, gradeType,weight from t_rmweightaftergradingdetails where gradeID='$WeightGradingId' order by ID asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getLotNm($LotId)
	{
		$qry	=	"select id, new_lot_Id from t_unittransfer where id='$LotId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getGradeNm($gradeID)
	{
		$qry	=	"select id, grade_count,main_id from weighment_data_sheet_grade_count where id='$gradeID' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getGradeName($mainID)
	{
		 $qry	=	"select id, grade_count from weighment_data_sheet_grade_count where main_id='$mainID' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function deleteWeightAfterGrading($WeightGradingId)
	{
		$qry	= " delete from t_rmweightaftergrading where ID=$WeightGradingId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
		function deleteWeightAfterGradingDetails($WeightGradingId)
	{
		$qry	= " delete from  t_rmweightaftergradingdetails where gradeID=$WeightGradingId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function fetchAllWeightAfterGrading($WeightGradingId)
	{
		$qry	= "select ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` where ID='$WeightGradingId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function find($WeightAfterGradingId)
	{
		$qry	= "select * from t_rmweightaftergrading where id=$WeightAfterGradingId";
		return $this->databaseConnect->getRecord($qry);
	}
	function updateweightmentAfterGrading($editId,$rmLotId, $supplyDetails, $total, $totalwt,$difference)
	{
		$qry	= " update t_rmweightaftergrading set rmLotId='$rmLotId', supplyDetails='$supplyDetails', sumtotal='$total', totalweight='$totalwt', difference='$difference' where id=$editId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function updateweightmentAfterGradingDetails($gradeId,$editId, $grandsingle, $weightsingle,$userId)
	{	

		 $qry	= "update t_rmweightaftergradingdetails set gradeID='$editId', gradeType='$grandsingle', weight='$weightsingle' where id=$gradeId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function deleteweightmentGradingDetails($gradeId)
	{
	$qry	= " delete from  t_rmweightaftergradingdetails where ID=$gradeId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
}
?>