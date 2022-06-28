<?php
class DailyThawing
{
	/****************************************************************
	This class deals with all the operations relating to Daily Thawing
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyThawing(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Check Blank Record Exist
	function checkBlankRecord($userId)
	{
		$qry = "select id from t_dailythawing where flag=0 and created_by='$userId' order by id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?($result[0]):false;
	}

	#Insert blank record in main table
	function addTempDataMainTable($userId)
	{
		$qry	=	"insert into t_dailythawing (created, created_by) values(Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#Updating the Frozen packing thawing table
	function updateDailyThawingRec($mainId, $fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $selectDate, $brandFrom, $customer)
 	{
		$qry	= "update t_dailythawing  set fish_id='$fishId', processcode_id='$processCode', freezing_stage_id='$freezingStage', eucode_id='$eUCode', brand_id='$brand', frozencode_id='$frozenCode', mcpacking_id='$mCPacking', select_date='$selectDate', brand_from='$brandFrom', customer_id='$customer', created=NOW(), flag=1 where id='$mainId'";
		//echo $qry;

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getEditDate($mainId)
	{
	$qry = "select select_date from t_dailythawing where id='$mainId'";
		//echo $qry;
	return $this->databaseConnect->getRecord($qry);

	}
	function getMaxDate()
	{
		
		$qry = "select max(date) maxdate from m_physical_stk_packing";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	} 
	function insertDailyThawing($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId)
	{
		$qry	= "insert into t_dailythawing(select_date,processcode_id,freezing_stage_id,frozencode_id,mcpacking_id) values ('$selectDate',$processId,$freezingStageId,$frozenCodeId,$MCPkgId)";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function updateDFPPOGradeForThawing($dailyThawingMainId,$gradeId,$numMC,$numLS)
	{
	$qry="update t_dailythawing_grade set number_mc_thawing='$numMC',number_loose_slab_thawing='$numLS' where main_id='$dailyThawingMainId' and grade_id='$gradeId'";
	//echo $qry;
	$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function insertDFPPOGradeForThawing($dfpPOEntryId, $gradeId, $numMC, $numMCStock)
	{
		$qry	= "insert into t_dailythawing_grade(main_id,grade_id,number_mc_thawing,number_mc_stock) values('$dfpPOEntryId', '$gradeId', '$numMC', '$numMCStock')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	function getThaGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId,$editId)
	{
	//$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' and td.id='$editId'";

	$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId'";
		//echo "^^^^".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}
	function getThaFreeGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$thaweditDate,$sGradeId)
	//function getThaFreeGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$thaweditid,$sGradeId)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and   mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";

		//$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate'  and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
		//echo "new".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
		
	}
	function getThadtGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId)
	{
	$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date='$thaweditDate' and mcpacking_id='$mCPacking' and grade_id='$sGradeId'";
		//echo "^^^^".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();
	}
	#Get Records For Selected Date Range
	function getDailyThawingRecords($fromDate, $tillDate)
	{
		$whr		=	"select_date>='".$fromDate."' and select_date>='".$tillDate."' and flag=1" ;
						
		$orderBy	=	"select_date asc";
		
		$qry		=	"select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;

		//echo "<br>$qry";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

function getThadelEntryId($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$delDate)
	{
		
		
		$qry		=	"select id from t_dailythawing where processcode_id='$selProcessCodeId' and freezing_stage_id='$selFreezingStageId' and frozencode_id='$selFrozenCodeId' and mcpacking_id='$frznStkMCPkgId' and select_date='$delDate'";
		
		
		//echo "<br>$qry";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;

	}
	function getThadelGradeId($Id)
	{
		
		
		$qry		=	"select id,main_id from t_dailythawing_grade where main_id='$Id'";
		
		
		//echo "<br>$qry";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Fetch All Paging Recs
	function fetchAllPagingRecords1($fromDate, $tillDate, $offset, $limit)
	{
		//$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
		$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=0" ;
		$groupBy	= "td.processcode_id, td.freezing_stage_id, td.frozencode_id, td.mcpacking_id,td.select_date";

		$orderBy	= "select_date asc";
		//$limit		= "$offset, $limit";
		
		//$qry		= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing";

		$qry		= "select td.id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date,sum(number_mc_thawing) as thawStock,tdg.grade_id from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;
		//if ($limit!="") 	$qry   .=" limit ".$limit;

		//echo "<br>$qry<br>";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{
		//$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
		$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
		//$groupBy	= "td.processcode_id, td.freezing_stage_id, td.frozencode_id, td.mcpacking_id,td.select_date,tdg.grade_id";
		//$groupBy	= "td.processcode_id, td.freezing_stage_id, td.frozencode_id, td.mcpacking_id,td.select_date,tdg.id";
		$groupBy	= "td.processcode_id, td.freezing_stage_id, td.frozencode_id, td.mcpacking_id,td.select_date";

		$orderBy	= "select_date asc";
		//$limit		= "$offset, $limit";
		
		//$qry		= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing";

		$qry		= "select td.id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date,sum(number_mc_thawing) as thawStock,tdg.grade_id,tdg.id,sum(number_mc_stock) as stocksum from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;
		//if ($limit!="") 	$qry   .=" limit ".$limit;

		//echo "<br>$qry<br>";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchLogRecords($mainId)
	{
		//$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;
		$whr		= "td.id='$mainId'" ;		

		$qry		= "select td.id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date,number_mc_thawing,grade_id from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;
		if ($limit!="") 	$qry   .=" limit ".$limit;

		//echo "<br>$qry<br>";

		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Fetch All Recs
	function fetchAllRecords($fromDate, $tillDate )
	{
		$whr		= "select_date>='".$fromDate."' and select_date<='".$tillDate."' and flag=1" ;

		$orderBy	= "select_date asc";		
		
		$qry		= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing";
		
		if ($whr!="")		$qry   .=" where ".$whr;
		if ($orderBy!="") 	$qry   .=" order by ".$orderBy;

		//echo "<br>$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	
	
	# Get Thawing Record  based on id
	function find($dailyThawingId)
	{
		//$qry	= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date, customer_id, brand_from from t_dailythawing where id='$dailyThawingId'";
		$qry	= "select id, fish_id, processcode_id, freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, select_date from t_dailythawing where id='$dailyThawingId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	function getProductionGradeRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		//tdfpm.select_date>='".$fromDate."' and
		/*$qry = "select tdfpg.grade_id, mg.code  from 
			t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
			join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId'   
			and tdfpm.select_date<='".$tillDate."' group by tdfpg.grade_id order by mg.code asc ";	*/
			$qry = "select tdfpg.grade_id, mg.code  from 
			t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
			join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId' group by tdfpg.grade_id order by mg.code asc ";
		//echo "Grade===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getAllocateProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId, $mcPkgId)
	{
		//tdfpm.select_date>='".$fromDate."' and  and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		$qry = "select tdfpe.id, tdfpe.frozen_lot_id, tdfpe.mcpacking_id, mcp.code as mcPkgCode, tdfpm.id as MainId, dfppo.id as POEntryId, dfppo.po_id as POID,tdfpe.processcode_id,tdfpe.freezing_stage_id,tdfpe.frozencode_id,tdfpe.mcpacking_id  
		from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
		left join m_mcpacking mcp on tdfpe.mcpacking_id=mcp.id 
		left join t_dailyfrozenpacking_po dfppo ON dfppo.entry_id=tdfpe.id  
		where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpe.mcpacking_id='$mcPkgId'
		 limit 0,1";
			
		//echo "Production Details(Daily Frzn Pkg Entry=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAvailablePacks($processCodeId, $freezingStageId, $frozenCodeId, $gradeId, $mcPkgId,$fromDate,$tillDate)
	{
		// and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		/*
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		*/

		/*$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		*/

		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
			 //echo $qry;
		/*$fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId' and dfppo.created_on<='$tillDate'
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id
			 ";	*/
			$fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId'  and dfpa.created_on>='$fromDate' and dfpa.created_on<='$tillDate'
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id
			 ";	

			
		//echo "<br>$fsQry";
		$uQry = " select sum(numMc), sum(numLS), sum(allocatedMC), sum(allocatedLS), processCodeId, freezingStageId, frozenCodeId, mcPackingId, (sum(numMc)-sum(allocatedMC)) as balMC, (sum(numLS)-sum(allocatedLS)) as balLS from (
					$qry
					union
					$fsQry
				 ) as x	group by processCodeId, freezingStageId, frozenCodeId, mcPackingId";
		//echo "<br>+++++++$uQry";

		//echo "getAvailablePacks===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($uQry);
		/*
		$totNumMc = 0;
		$totNumLS = 0;
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$numMc	= $r[0];
				$numLS	= $r[1];
				$totNumMc += $numMc;
				$totNumLS += $numLS;
			}
		}
		*/
		return (sizeof($result)>0)?array($result[0][8],$result[0][9]):array();
		
	}

//------------------------ Delete From Main Table------------------------------
# Delete a Daily Thawing Grade Rec
	function getThaQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}
	function getThaFreeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate)
	{
		$qry="select sum(number_mc_thawing) from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	

	}
	function getAvailablePacks1($processCodeId, $freezingStageId, $frozenCodeId, $gradeId, $mcPkgId,$fromDate,$tillDate)
	{
		// and tdfpe.id not in (select entry_id from t_dailyfrozenpacking_po)
		/*
		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
				left join m_frozenpacking mfp on tdfpe.frozencode_id=mfp.id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		*/

		$qry = "select sum(tdfpg.number_mc) as numMc, sum(tdfpg.number_loose_slab) as numLS, 0 as allocatedMC , 0 as allocatedLS, tdfpe.processcode_id as processCodeId, tdfpe.freezing_stage_id as freezingStageId, tdfpe.frozencode_id as frozenCodeId, tdfpe.mcpacking_id as mcPackingId   
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id 
			where tdfpe.processcode_id='$processCodeId' and tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.frozencode_id='$frozenCodeId'  and tdfpg.grade_id='$gradeId' and tdfpe.mcpacking_id='$mcPkgId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate'
			group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id
			 ";	
		
		$fsQry = "select 0 as numMc, 0 as numLS, sum(dfpa.number_mc) as allocatedMC , sum(dfpa.number_loose_slab) as allocatedLS, dfppo.processcode_id as processCodeId, dfppo.freezing_stage_id as freezingStageId, dfppo.frozencode_id as frozenCodeId, dfppo.mcpacking_id as mcPackingId 
			from 
				t_dailyfrozenpacking_po dfppo join t_dailyfrozenpacking_allocate dfpa on dfppo.id=dfpa.po_entry_id 
			where dfppo.processcode_id='$processCodeId' and dfppo.freezing_stage_id='$freezingStageId' and dfppo.frozencode_id='$frozenCodeId'  and dfpa.grade_id='$gradeId' and dfppo.mcpacking_id='$mcPkgId' and dfppo.created_on<='$tillDate'
			group by dfppo.processcode_id, dfppo.freezing_stage_id, dfppo.frozencode_id, dfppo.mcpacking_id
			 ";	
		//echo "<br>$fsQry";
		$uQry = " select sum(numMc), sum(numLS), sum(allocatedMC), sum(allocatedLS), processCodeId, freezingStageId, frozenCodeId, mcPackingId, (sum(numMc)-sum(allocatedMC)) as balMC, (sum(numLS)-sum(allocatedLS)) as balLS from (
					$qry
					union
					$fsQry
				 ) as x	group by processCodeId, freezingStageId, frozenCodeId, mcPackingId";
		//echo "<br>+++++++$uQry";

		//echo "getAvailablePacks===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($uQry);
		/*
		$totNumMc = 0;
		$totNumLS = 0;
		if (sizeof($result)>0) {
			foreach ($result as $r) {
				$numMc	= $r[0];
				$numLS	= $r[1];
				$totNumMc += $numMc;
				$totNumLS += $numLS;
			}
		}
		*/
		return (sizeof($result)>0)?array($result[0][8],$result[0][9]):array();
		
		}
	function insDailyfrozenpackingAlloc($processcodeid,$freestid,$frozid,$mcpid,$gradeid,$numc,$purchaseOrderId,$id,$type,$invoiceId,$userId)
	{
	$date=Date(Y-m-d);
	$qry="insert into t_frozenpacking_log(processcode_id,freezingstage_id,frozencode_id,mcpacking_id,purchase_order_id,grade_id,num_mc,type,invoice_id,date_allocated,modified_by) values ('$processcodeid','$freestid','$frozid','$mcpid','$purchaseOrderId','$gradeid','$numc','$type','$invoiceId',now(),'$userId')";
	//echo $qry;
	$insertStatus	= $this->databaseConnect->insertRecord($qry);
	if ($insertStatus) $this->databaseConnect->commit();
	else $this->databaseConnect->rollback();
	}
	function deleteDailyThawingGradeRec($dailyThawingMainId)
	{
		$qry	=	" delete from t_dailythawing_grade where id=$dailyThawingMainId";
		//echo $qry;

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Daily Thawing Main Rec
	function deleteDailyThawingMainRec($dailyThawingMainId)
	{
		$qry	= " delete from t_dailythawing where id=$dailyThawingMainId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result)  $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
//------------------------ Delete End------------------------------

	#IFRAME SECTION
	function addFrozenPackingThawingGrade($mainId, $gradeId, $numMCThawing, $numLooseSlabThawing, $totalNumMC, $totalNumLooseSlab)
	{
		$qry	=	" insert into t_dailythawing_grade (main_id, grade_id, number_mc_thawing, number_loose_slab_thawing, number_mc_stock,  number_loose_slab_stock) values($mainId, $gradeId, $numMCThawing, $numLooseSlabThawing, $totalNumMC, $totalNumLooseSlab)";
	
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}	

	#update Frozen Packing Thawing Grades
	function updateFrozenPackingThawingGrade($gradeEntryId, $gradeId, $numMCThawing, $numLooseSlabThawing, $totalNumMC, $totalNumLooseSlab)
	{
		$qry	=	" update t_dailythawing_grade set grade_id='$gradeId', number_mc_thawing='$numMCThawing', number_loose_slab_thawing='$numLooseSlabThawing', number_mc_stock='$totalNumMC',  number_loose_slab_stock='$totalNumLooseSlab' where id='$gradeEntryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	#Delete a Grade Rec
	function deletePackingGradeRec($gradeEntryId)
	{
		$qry	=	" delete from t_dailythawing_grade where id=$gradeEntryId";
	
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	#Grade selection for Frozen Grades
	function fetchFrozenGradeRecords($codeId, $mainId)
 	{
		$qry	=	"select a.grade_id, c.code, b.id, b.main_id, b.number_mc_thawing, b.number_loose_slab_thawing, b.number_mc_stock, b.number_loose_slab_stock from m_processcode2grade a left join t_dailythawing_grade b on a.grade_id=b.grade_id and b.main_id='$mainId', m_grade c where a.grade_id = c.id and a.processcode_id='$codeId' and a.unit_select='f' order by c.code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Get total Stock of Num of MC and Num of Loose Slab
	function getTotalStock($processcodeId, $gradeId)
	{
		$qry = "select b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_entry a, t_dailyfrozenpacking_grade b where a.id=b.entry_id and b.grade_id='$gradeId' and a.processcode_id='$processcodeId'";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>0) {
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach ($result as $rec) {
				$numMc	= $rec[0];
				$numLooseSlab = $rec[1];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}		
		}	
		return array($totalNumMC, $totalNumLooseSlab);
	}

	# get all recs
	function fetchAllQELRecords()
	{	
		$orderBy = "a.name asc";
		
		$qry	= "select a.id, a.name from t_fznpakng_quick_entry a ";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$fznArr = array();
		foreach ($result as $qr) {
			$qelMainId 	= $qr[0];
			$qelName	= $qr[1];
			$fznArr[$qelMainId] = $qelName;
		}
		return $fznArr;
	}

	# QE Grade Records
	function qelGradeRecords($selQuickEntryList)
	{
		$qry = "select a.grade_id, c.code from t_fznpakng_qel_grade a, m_grade c where a.grade_id = c.id and a.qe_entry_id='$selQuickEntryList' and a.active='Y' order by a.display_order asc";
		//echo "$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Raw data Records
	function qelProcessCodeRecs($selQuickEntryList)
	{	
		$qry = " select a.processcode_id, a.fish_id, b.code as pc, c.code as fc from t_fznpakng_qel_entry a, m_processcode b, m_fish c where a.fish_id=c.id and a.processcode_id=b.id and a.qe_entry_id='$selQuickEntryList'  order by a.id asc  ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get QE Rec
	function qelRec($selQuickEntryList)
	{
		$qry = " select freezing_stage_id, eucode_id, brand_id, frozencode_id, mcpacking_id, frozen_lot_id, export_lot_id, quality_id, customer_id, brand_from from t_fznpakng_quick_entry where id='$selQuickEntryList' ";
		$rec	= $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5],$rec[6],$rec[7],$rec[8], $rec[9]);

		//$qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom
	}

	function frznPkgFilledWt($frozenPackingId)
	{
		$qry	= "select filled_wt from m_frozenpacking where id='$frozenPackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}

	function getnumMC($mCPacking)
	{
		$qry	= "select number_packs from m_mcpacking where id='$mCPacking'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}

	#Get total Stock of Num of MC and Num of Loose Slab
	function getFPClosingStock($processcodeId, $gradeId, $tillDate)
	{
		$qry = "select b.number_mc, b.number_loose_slab from t_dailyfrozenpacking_main dfpm, t_dailyfrozenpacking_entry a, t_dailyfrozenpacking_grade b where dfpm.id=a.main_id and a.id=b.entry_id and b.grade_id='$gradeId' and a.processcode_id='$processcodeId' and dfpm.select_date='$tillDate' and (b.number_mc!=0 or b.number_loose_slab!=0) ";
		//echo "<br>$qry<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		
		if (sizeof($result)>0) {
			$totalNumMC = "";
			$totalNumLooseSlab = "";
			foreach ($result as $rec) {
				$numMc	= $rec[0];
				$numLooseSlab = $rec[1];
				$totalNumMC += $numMc;
				$totalNumLooseSlab += $numLooseSlab;
			}		
		}	
		return array($totalNumMC, $totalNumLooseSlab);
	}

}

?>