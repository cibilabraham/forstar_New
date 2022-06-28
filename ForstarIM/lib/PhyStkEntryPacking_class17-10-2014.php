<?php
class PhysicalStockEntryPacking
{  
	/****************************************************************
	This class deals with all the operations relating to Physical Stock Entry Packing
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function PhysicalStockEntryPacking(&$databaseConnect)
    {
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert A Rec
	# Add to Main Table

	function addMainRecord($selDate, $userId)
	{

		$qry = "insert into m_physical_stk_packing (date, created, createdby) values ('$selDate', NOW(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;

	}


	function addPhysicalStkdailyFrozenmain($selDate, $userId,$mainId)
	{

		$qry = "insert into t_dailyfrozenpacking_main(select_date,user_id,physical_stock_main_id) values ('$selDate','$userId','$mainId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	function adddailyfrozenEntries($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id)
	{		
		$qry = "insert into t_dailyfrozenpacking_entry(main_id,fish_id,processcode_id,freezing_stage_id, frozencode_id, mcpacking_id) values ('$mainId','$fish_id','$processcode_id', ' $freezing_stage', ' $frozencode_id','$mcpacking_id')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	function adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used)
	{

		$qry = "insert into t_dailyfrozenpacking_grade(entry_id,grade_id,number_mc,number_loose_slab) values ('$dailyentrymainId','$grade_id','$num_mc_used','$num_ls_used')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	function updatePhysicalStockEntries($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$num_mc_used,$num_ls_used)
	{
		$qry	= "update m_physical_stk_packing_entry set fish_id='$fish_id',processcode_id='$processcode_id',grade_id='$grade_id',freezing_stage='$freezing_stage', frozencode_id='$frozencode_id', mcpacking_id=' $mcpacking_id', num_mc='$num_mc_used',num_ls='$num_ls_used' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

function updatedailyfrozenpacking_grade($mainId,$grade_id,$num_mc_used,$num_ls_used)
	{
		$qry	= "update t_dailyfrozenpacking_grade set grade_id='$grade_id',number_mc='$num_mc_used',number_loose_slab='$num_ls_used' where id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

function updatedailyfrozenpacking_entry($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id)
	{
		$qry	= "update t_dailyfrozenpacking_entry set fish_id='$fish_id',processcode_id='$processcode_id',freezing_stage_id='$freezing_stage', frozencode_id='$frozencode_id', mcpacking_id='$mcpacking_id' where main_id='$mainId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function addPhysicalStock($selDate, $userId)
	{		
		$qry = "insert into m_physical_stk_packing (date, created, createdby) values ('$selDate', NOW(), '$userId')";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	# Add to entry table
	function addPhysicalStockEntries($mainId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$num_mc_used,$num_ls_used)
	{		
		$qry = "insert into m_physical_stk_packing_entry (main_id,fish_id,processcode_id,freezing_stage, frozencode_id, mcpacking_id,grade_id,num_mc,num_ls) values ('$mainId','$fish_id','$processcode_id', ' $freezing_stage', ' $frozencode_id', '$mcpacking_id', ' $grade_id','$num_mc_used','$num_ls_used')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit)
	{
		 $whr = "";			

		$orderBy 	= " a.date desc ";
		//$limit 		= " $offset,$limit";
		$qry = " select a.id, a.date from m_physical_stk_packing a ";
		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
	 	$whr = "";	
		
		$orderBy 	= " a.date asc ";
		$qry = " select a.id, a.date from m_physical_stk_packing a ";
		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Record based on Id
	function find($physicalStockRecId)
	{
		$qry = "select id, date from m_physical_stk_packing where id=$physicalStockRecId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getPhysicalStkRec($physicalStockRecId, $stockId)
	{
		$qry = " select id, physical_stk_qty, stk_qty, diff_stk_qty from m_physical_stk_packing_entry where main_id='$physicalStockRecId' and stock_id='$stockId' ";
		
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[2], $rec[1], $rec[3]);
	}

	# Update
	function updatePhysicalStock($physicalStockRecId, $selDate)
	{
		$qry = "update m_physical_stk_packing set date='$selDate' where id='$physicalStockRecId'";	
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

function updatedailyPhysicalStock($physicalStockRecId, $selDate)
	{

		$qry = "update t_dailyfrozenpacking_main set select_date='$selDate' where physical_stock_main_id='$physicalStockRecId'";	
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function fetchAllPOItem($poMainId)
	{
		$qry = "select id,fish_id,processcode_id,grade_id, freezing_stage, frozencode_id, mcpacking_id, num_mc,num_ls from m_physical_stk_packing_entry where main_id='$poMainId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function delPhysicalStockEntries($physicalStockRecId)
	{
		$qry	= " delete from m_physical_stk_packing_entry where main_id=$physicalStockRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Main Rec
	function deletePhysicalStock($physicalStockRecId)
	{
		$qry	= " delete from m_physical_stk_packing where id=$physicalStockRecId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function deletedailyfrozenphysicalid($phystkId)
	{
		$qry	= " delete from t_dailyfrozenpacking_main where id=$phystkId";
        //echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function deleteDailyFrozenGrade($entryId)
	{
		$qry	= " delete from t_dailyfrozenpacking_grade where entry_id=$entryId";
        //echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function deleteDailyFrozenEntry($mainId)
	{
		$qry	= " delete from t_dailyfrozenpacking_entry where main_id=$mainId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Rec Exist
	function chkRecExist_old($selDate, $physicalStkId)
	{
		$uptdQry = "";
		if ($physicalStkId) $uptdQry = " and id!=$physicalStkId";
		else $uptdQry	= "";
		$qry = " select id from m_physical_stk_packing where date='$selDate' $uptdQry";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	

	function chkRecExist($selDate, $physicalStkId)
	{
		
		$qry = " select id from m_physical_stk_packing where date='$selDate'";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	


	function chkRecExistphyEntry($hidid)
	{

		$qry = " select id from m_physical_stk_packing_entry where id='$hidid'";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	

	function deletePhysicalStockEntries($poEntryId)
	{
		$qry	=	" delete from  m_physical_stk_packing_entry where id=$poEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getMaxDate()
	{
		
		$qry = "select max(date) maxdate from m_physical_stk_packing";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function getDailyFrozenusedStatus()
	{
	$qry="select * from m_physical_stk_packing where id=(select max(id) from m_physical_stk_packing)";
	return $this->databaseConnect->getRecord($qry);
	}


	function getDailyFrozenMainid($id,$selDate)
	{
		
	$qry = "select id from t_dailyfrozenpacking_main where physical_stock_main_id=$id and select_date='$selDate'";
	//echo $qry;
	return $this->databaseConnect->getRecord($qry);
	}

	function getDailyFrozenEntryid($id)
	{
	$qry = "select id from t_dailyfrozenpacking_entry where main_id=$id";
	//echo $qry;
	$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getPhysicalStockMaxdate()
	{
		
	$qry = "select date from m_physical_stk_packing order by date desc limit 1";
	//echo $qry;
	return $this->databaseConnect->getRecord($qry);
	}
	function getAllowedDate()
	{
		
	$qry = "SELECT max(date) FROM m_physical_stk_packing WHERE date NOT IN (SELECT max(date) FROM m_physical_stk_packing);";
	return $this->databaseConnect->getRecord($qry);
	}

}
?>