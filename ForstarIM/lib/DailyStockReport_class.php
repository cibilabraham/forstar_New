<?php
Class DailyStockReport
{
	/****************************************************************
	This class deals with all the operations relating to Production Analysis Report
	*****************************************************************/
	var $databaseConnect;
	var $tempPCodeRecs;


	//Constructor, which will create a db instance for this class
	function DailyStockReport(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}


	# Grade Recs
	function getGradeRecs($fromDate, $tillDate, $fishId)
	{
		$whr = "a.select_date>='$fromDate' and a.select_date<='$tillDate' and a.processor_id is not null";
		
		if ($fishId) $whr .= " and b.fish_id in ($fishId)";
		
		$groupBy	= "c.grade_id";	
		$orderBy	= "mg.code asc";

		$qry = " select c.grade_id, mg.code as gradeCode from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
				left join t_dailyfrozenpacking_grade c on b.id=c.entry_id
				left join m_grade mg on c.grade_id=mg.id ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "<br>$qry<br>"; 

		return $this->databaseConnect->getRecords($qry);
	}

	# Get Production Details
	function getFishRecords($fromDate, $tillDate)
	{
		$qry = "select tdfpe.fish_id, mf.name as fishName from t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id left join m_fish mf on mf.id=tdfpe.fish_id  where tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpe.fish_id is not null group by tdfpe.fish_id order by mf.name asc";
		//echo "to display in cmb Fish Recs=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Process Code Recs
	function processCodeRecs($fromDate, $tillDate, $fishId)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null";
		
		if ($fishId) $whr .= " and tdfpe.fish_id in ($fishId)";
				
		$groupBy	= "mfp.freezing_id, mf.category_id, tdfpe.processcode_id";
		$orderBy	= "mfc.category asc, mfrz.code asc, mf.name asc, mpc.code asc";		
		$qry = " select tdfpe.processcode_id, tdfpe.fish_id, mpc.code as processCode, mf.name as fishName, mfp.code as frznCode, tdfpe.frozencode_id, mfc.category as fishCategory, mfc.id as fCategoryId, mfrz.code as freezingStyle, mfrz.id as frzngStyleId
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join m_fish mf on mf.id=tdfpe.fish_id 
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id  
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id 
				left join m_fishcategory mfc on mfc.id=mf.category_id 
				left join m_freezing mfrz on mfrz.id=mfp.freezing_id 
				";
		//left join pre_process_sequence pps on mpc.id=pps.processcode_id
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "PC Recs=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	/**
	* Get Selected grade recs
	*/
	function getSelGradeRecs($fromDate, $tillDate, $fishCategoryId, $freezingStyleId)
	{
		$whr = "tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' and tdfpm.processor_id is not null and mfc.id='$fishCategoryId' and mfrz.id='$freezingStyleId'";		
		
		$groupBy	= "tdfpg.grade_id";	
		$orderBy	= "mg.code asc";

		$qry = " select tdfpg.grade_id, mg.code as gradeCode from 
				t_dailyfrozenpacking_main tdfpm left join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_grade mg on tdfpg.grade_id=mg.id 
				left join m_fish mf on mf.id=tdfpe.fish_id 
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id  
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id 
				left join m_fishcategory mfc on mfc.id=mf.category_id 
				left join m_freezing mfrz on mfrz.id=mfp.freezing_id	
				";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "<br>Grade is****......==><br>$qry<br>"; 

		return $this->databaseConnect->getRecords($qry);
	}

	/**
	* Process Code Sequence
	*/
	function getPCSequence()
	{	
		$qry = "select pps.processcode_id, pps.fish_id, mpc.code as processcode, mf.name as fishName from pre_process_sequence pps left join m_fish mf on mf.id=pps.fish_id left join m_processcode mpc on mpc.id=pps.processcode_id order by mf.name asc, pps.process_criteria desc, pps.sort_id asc, mpc.code asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}

	/*
	Group => freezing_stage_id, freezing_style_id, quality_id, eucode_id, customer_id, brand_id, brand_from, mcpacking_id
	*/

	function getStockReport($fromDate, $tillDate, $fishId, $qelId, $selFrozenCodeId, $customerId, $euCodeId, $selBrandId, $freezingStageId, $qualityId, $stkGroupListEnabled)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null and tdfpe.frozencode_id='$selFrozenCodeId' and tdfpe.customer_id='$customerId' and tdfpe.eucode_id='$euCodeId' and tdfpe.brand_id='$selBrandId' and  tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.quality_id='$qualityId' ";		
		
		if ($fishId) $whr .= " and tdfpe.fish_id in ($fishId)"; 
				
		$groupBy	= "tdfpe.processcode_id";		
		$orderBy	= "mpc.code asc";

		$selQry = "";
		if ($stkGroupListEnabled) {			
			$selQry  = "left join t_fpstk_report_group_entry tfrge on tfrge.qel_id=tfqe.id
				join t_fpstk_report_group tfrg on tfrg.id=tfrge.main_id";
		}
 		
		$qry = " select  tdfpe.processcode_id, tdfpe.fish_id, mpc.code as processCode, mf.name as fishName, mfp.code as frznCode, tdfpe.frozencode_id, mfc.category as fishCategory, mfc.id as fCategoryId, mfrz.code as freezingStyle, mfrz.id as frzngStyleId, tfqe.freezing_stage_id, mfs.rm_stage as freezingStage
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join m_fish mf on mf.id=tdfpe.fish_id 
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id  
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id 
				left join m_fishcategory mfc on mfc.id=mf.category_id 
				left join m_freezing mfrz on mfrz.id=mfp.freezing_id
				left join m_mcpacking mmcp on mmcp.id=tdfpe.mcpacking_id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id and tdfpe.quality_id=tfqe.quality_id		
				left join m_freezingstage mfs on mfs.id=tfqe.freezing_stage_id
				$selQry
				";
		
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Report Recs=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	// Stk Report Group List
	function stkReportGroupList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $stkGroupList)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null";
		
		if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";

		$groupBy 	= "tdfpe.customer_id, tdfpe.eucode_id, tdfpe.brand_id, tdfpe.frozencode_id, tdfpe.freezing_stage_id, tdfpe.quality_id";	
		$orderBy	= "tfqe.name asc";

		$selOption = "tfqe.id, tfqe.name as groupName";
		$selQry = "";
		if ($stkGroupList) {
			$selOption = "tfrg.id, tfrg.name as groupName";
			$selQry  = "left join t_fpstk_report_group_entry tfrge on tfrge.qel_id=tfqe.id
				join t_fpstk_report_group tfrg on tfrg.id=tfrge.main_id";
			$groupBy = "tfqe.id";	
		}
 
		$qry = " select $selOption, tfqe.id, tfqe.name as qeName, tfqe.customer_id, mc.customer_name as customerName, tfqe.quality_id, mq.name as quality, tfqe.eucode_id, mec.code as euCode, tdfpe.processcode_id,  mpc.code as processCode, tfqe.freezing_stage_id, mfs.rm_stage as freezingStage, tdfpe.frozencode_id, tdfpe.brand_id, mb.brand 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id
				left join m_fish mf on mf.id=tdfpe.fish_id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id and tdfpe.quality_id=tfqe.quality_id
				left join m_customer mc on mc.id=tfqe.customer_id
				left join m_quality mq on mq.id=tfqe.quality_id 
				left join m_eucode mec on mec.id=tfqe.eucode_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tfqe.freezing_stage_id
				left join m_brand mb on tdfpe.brand_id=mb.id
				$selQry
				";
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Group List=====&&><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	/**
	*@pending : If customer defined a grade then no data display grade blank grade also other no display
	*/

	function selGradeRecs($fromDate, $tillDate, $fishId, $customerId, $euCodeId, $selBrandId, $qelId, $selFrozenCodeId, $freezingStageId, $qualityId)
	{		
		
		$whr = "tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' and tdfpm.processor_id is not null and (tdfpg.number_mc!=0 or tdfpg.number_loose_slab!=0)  and tdfpe.frozencode_id='$selFrozenCodeId' and tdfpe.customer_id='$customerId' and tdfpe.eucode_id='$euCodeId' and tdfpe.brand_id='$selBrandId' and  tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.quality_id='$qualityId'";

		if ($fishId) $whr .= " and tdfpe.fish_id in ($fishId)";

		$groupBy	= "tdfpg.grade_id";	
		$orderBy	= "qelg.display_order asc";

		$qry = " select tdfpg.grade_id, mg.code as gradeCode from 
				t_dailyfrozenpacking_main tdfpm left join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_grade mg on tdfpg.grade_id=mg.id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id and tdfpe.quality_id=tfqe.quality_id	
				left join t_fznpakng_qel_grade qelg on qelg.qe_entry_id=tfqe.id and tdfpg.grade_id=qelg.grade_id	
				";
		
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>Grade==****><br>$qry<br>"; 

		return $this->databaseConnect->getRecords($qry);
	}

	# =======================================

	function getSlab($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
	{
		$qry1 = "select tdfpe.id as mainId,sum(number_mc) as numMc, sum(number_loose_slab) as numLc from t_dailyfrozenpacking_grade tdfpg left join t_dailyfrozenpacking_entry tdfpe on tdfpe.id=tdfpg.entry_id left join t_dailyfrozenpacking_main tdfpm on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$selProcessCodeId' and tdfpe.freezing_stage_id='$selFreezingStageId' and tdfpe.frozencode_id='$selFrozenCodeId' and tdfpe.mcpacking_id='$selMCPackingId' and tdfpg.grade_id='$gradeId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,tdfpg.grade_id";
		
		$qry2= "select tdfpe.id as mainId,sum(number_mc) as numMc, sum(number_loose_slab) as numLc from t_dailyfrozenpacking_grade_rmlotid tdfpg left join t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpe.id=tdfpg.entry_id left join t_dailyfrozenpacking_main_rmlotid tdfpm on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$selProcessCodeId' and tdfpe.freezing_stage_id='$selFreezingStageId' and tdfpe.frozencode_id='$selFrozenCodeId' and tdfpe.mcpacking_id='$selMCPackingId' and tdfpg.grade_id='$gradeId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,tdfpg.grade_id";
		
		$qry="select mainId,sum(numMc),sum(numLc) from ($qry1 union all $qry2) dum";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		// echo "<br>=============================<br>";
		// print_r($result);
		// foreach($result AS $row){
		// 	print_r($row);
		// 	echo "<br><br>";

		// }
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
	}



	function getSlab_Old($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
	{
		$qry = "select tdfpe.id,sum(number_mc), sum(number_loose_slab) from t_dailyfrozenpacking_grade tdfpg left join t_dailyfrozenpacking_entry tdfpe on tdfpe.id=tdfpg.entry_id left join t_dailyfrozenpacking_main tdfpm on tdfpm.id=tdfpe.main_id where tdfpe.processcode_id='$selProcessCodeId' and tdfpe.freezing_stage_id='$selFreezingStageId' and tdfpe.frozencode_id='$selFrozenCodeId' and tdfpe.mcpacking_id='$selMCPackingId' and
		tdfpg.grade_id='$gradeId' and tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' group by tdfpe.processcode_id, tdfpe.freezing_stage_id, tdfpe.frozencode_id, tdfpe.mcpacking_id,tdfpg.grade_id
		";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
	}


function getallocateSlab($selProcessCodeId, $selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
{
	//$qry = "select tdfpa.id,number_mc, number_loose_slab from t_dailyfrozenpacking_po tdfpo left join t_dailyfrozenpacking_allocate tdfpa on tdfpo.id=tdfpa.po_entry_id  where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezing_stage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and
		//tdfpa.grade_id='$gradeId' and tdfpo.created_on>='$fromDate' and tdfpo.created_on<='$tillDate'";
	$qry1= "select tdfpa.id as id,sum(number_mc) as numMc,sum(number_loose_slab) as numLc from t_dailyfrozenpacking_po tdfpo left join t_dailyfrozenpacking_allocate tdfpa on tdfpo.id=tdfpa.po_entry_id  where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezing_stage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpa.grade_id='$gradeId' and tdfpa.created_on>='$fromDate' and tdfpa.created_on<='$tillDate'";

	$qry2= "select tdfpa.id as id,sum(number_mc)  as numMc,sum(number_loose_slab) as numLc from t_dailyfrozenpacking_po tdfpo left join t_dailyfrozenpacking_allocate tdfpa on tdfpo.id=tdfpa.po_entry_id  where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezing_stage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpa.grade_id='$gradeId' and tdfpa.created_on>='$fromDate' and tdfpa.created_on<='$tillDate'";

	$qry="SELECT id,SUM(numMc),SUM(numLc) from ($qry1 union all $qry2) dum";
	//echo $qry;
	//echo "<br>";
	$result	= $this->databaseConnect->getRecords($qry);
	return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
}

function getallocateSlab_old($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
{
		//$qry = "select tdfpa.id,number_mc, number_loose_slab from t_dailyfrozenpacking_po tdfpo left join t_dailyfrozenpacking_allocate tdfpa on tdfpo.id=tdfpa.po_entry_id  where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezing_stage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and
		//tdfpa.grade_id='$gradeId' and tdfpo.created_on>='$fromDate' and tdfpo.created_on<='$tillDate'";
		$qry = "select tdfpa.id,sum(number_mc),sum(number_loose_slab) from t_dailyfrozenpacking_po tdfpo left join t_dailyfrozenpacking_allocate tdfpa on tdfpo.id=tdfpa.po_entry_id  where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezing_stage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and
		tdfpa.grade_id='$gradeId' and tdfpa.created_on>='$fromDate' and tdfpa.created_on<='$tillDate'";
		//echo $qry;
		//echo "<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2]):array();	
}


function getPrice($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
{
	
	$qry = "select tdfpa.price_per_kg from t_purchaseorder_rm_entry tdfpo left join t_invoice_rm_entry tdfpa on tdfpo.id=tdfpa.po_entry_id left join t_invoice_main tim on tdfpa.main_id=tim.id where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezingstage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpo.grade_id='$gradeId' order by tim.invoice_date desc limit 0,1";
	//echo $qry."<br>";
	$result	= $this->databaseConnect->getRecords($qry);
	if($result[0][0]>0)
	{
		return (sizeof($result)>0)?array($result[0][0]):null;	
	}
	else
	{
		$qry1 = "select tdfpo.priceperkg from t_purchaseorder_rm_entry tdfpo where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezingstage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpo.grade_id='$gradeId'  order by tdfpo.id desc limit 0,1";
		//echo $qry1."<br>";
		$results	= $this->databaseConnect->getRecords($qry1);
		return (sizeof($results)>0)?array($results[0][0]):null;
	}
}

function getPrice_old($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate,$tillDate)
{
	
	$qry = "select tdfpa.price_per_kg from t_purchaseorder_rm_entry tdfpo left join t_invoice_rm_entry tdfpa on tdfpo.id=tdfpa.po_entry_id left join t_invoice_main tim on tdfpa.main_id=tim.id where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezingstage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpo.grade_id='$gradeId' order by tim.invoice_date desc limit 0,1";
	//$qry = "select tdfpa.price_per_kg from t_purchaseorder_rm_entry tdfpo left join t_invoice_rm_entry tdfpa on tdfpo.id=tdfpa.po_entry_id left join t_invoice_main tim on tdfpa.main_id=tim.id where tdfpo.processcode_id='$selProcessCodeId' and tdfpo.freezingstage_id='$selFreezingStageId' and tdfpo.frozencode_id='$selFrozenCodeId' and tdfpo.mcpacking_id='$selMCPackingId' and tdfpo.grade_id='$gradeId' and  tim.confirmed='Y' order by tim.invoice_date desc limit 0,1";
	//echo $qry;
	//echo $qry."<br>";
	$result	= $this->databaseConnect->getRecords($qry);
	return (sizeof($result)>0)?array($result[0][0]):null;	
}


	function dailyStkReport($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId, $stkGroupListEnabled)
	{
		$stkReportGroupList = $this->stkReportGroupList($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId, $stkGroupListEnabled);
		if (sizeof($stkReportGroupList)) {
			$prevSrglId = "";
			$prevQelId  = "";
			$pcCol = 0;
			$grandTotalNumMc = 0;
			$gradeCountArr = array();
			foreach ($stkReportGroupList as $srgl) {
				$srglId 	= $srgl[0];
				$srglName 	= $srgl[1];
				$qelId		= $srgl[2];
				$qelName	= $srgl[3];
				$customerId 	= $srgl[4];
				$customName	= $srgl[5];
				$qualityId	= $srgl[6];
				$qualityCode	= $srgl[7];
				$euCodeId	= $srgl[8];
				$euCode		= $srgl[9];
				$selProcessCodeId = $srgl[10];
				$selProcessCode = $srgl[11];
				$freezingStageId = $srgl[12];
				$freezingStage  = $srgl[13];
				$selFrozenCodeId   = $srgl[14];
				$selBrandId = $srgl[15];
				$selBrandName = $srgl[16];
				$processCodeRecs = $this->getStockReport($fromDate, $tillDate, $selFish, $qelId, $selFrozenCodeId, $customerId, $euCodeId, $selBrandId, $freezingStageId, $qualityId, $stkGroupListEnabled);
				$i = 0;
				$prevFishCategoryId  = "";
				$prevFreezingStyleId = "";
				foreach ($processCodeRecs as $pcr) {
					$i++;
					$processCodeId	= $pcr[0];
					$fishId		= $pcr[1];
					$processCode	= $pcr[2];
					$fishName	= $pcr[3];
					$frozenCode	= $pcr[4];
					$frozenCodeId	= $pcr[5];
					$fishCategory	= $pcr[6];
					$fishCategoryId	= $pcr[7];
					$freezingStyle	= $pcr[8];
					$freezingStyleId = $pcr[9];					
					if ($prevQelId != $qelId) {						
						# Get Row Wise Selected grades
						$selGradeRecs = $this->selGradeRecs($fromDate, $tillDate, $selFish, $customerId, $euCodeId, $selBrandId, $qelId, $selFrozenCodeId, $freezingStageId, $qualityId);
						$grSize = sizeof($selGradeRecs);
						$sTdWidth = ((100)/$grSize);
						$tdWidth = $grSize*100;		
						//Buyer, eucode, brand, qe, fs, quality
						$displaySubhead = $customName." ".$euCode." ".$selBrandName." ".$qelName." ".$freezingStage." ".$qualityCode;				
						$c = 0;
						$dPCHead = "";
						
						foreach ($selGradeRecs as $gr) {
							$c++;
							$disPC	 = $gr[1];
							//strlen($disPC);
						}
					} // Head print 
					$gradeCountArr[] = $c;
				//echo "<br>count=>".$c;
				$prevFishCategoryId 	= $fishCategoryId;	
				$prevFreezingStyleId 	= $freezingStyleId;
				$prevSrglId = $srglId;
				$prevQelId = $qelId;
				
				} // PC Loop
			} // Main Loop Ends here
		} // StkReportGroupList ends here

		$gradeMaxColCount = max($gradeCountArr);
		return $gradeMaxColCount;
	}

	function getMaxDate()
	{
		
	$qry = "select max(date) maxdate from m_physical_stk_packing;";
	//echo $qry;
	return $this->databaseConnect->getRecord($qry);
	}

function getMaxNextDate($nextDate)
	{
		
	$qry = "select max(date) maxdate from m_physical_stk_packing where date<='$nextDate';";
	//echo $qry;
	return $this->databaseConnect->getRecord($qry);
	}



	function dsReportRecs($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null and (tdfpg.number_mc!=0 or tdfpg.number_loose_slab!=0)";
		
		if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";

		$groupBy 	= "tdfpe.customer_id, tdfpe.eucode_id, tdfpe.brand_id, tdfpe.frozencode_id, tdfpe.freezing_stage_id, tdfpe.quality_id, tdfpe.processcode_id , tdfpg.grade_id";	
		$orderBy	= "tfqe.name asc";
 
		$qry = " select  tfqe.id, tfqe.name as qeName, tfqe.customer_id, mc.customer_name as customerName, tfqe.quality_id, mq.name as quality, tfqe.eucode_id, mec.code as euCode, tdfpe.processcode_id, mpc.code as processCode, tfqe.freezing_stage_id, mfs.rm_stage as freezingStage, tdfpe.frozencode_id, tdfpe.brand_id, mb.brand, tdfpg.grade_id, mg.code as gradeCode,qelg.display_order, sum(tdfpg.number_mc), sum(tdfpg.number_loose_slab) 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_grade mg on tdfpg.grade_id=mg.id
				left join m_fish mf on mf.id=tdfpe.fish_id 
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id 
				join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id 
				left join t_fznpakng_qel_grade qelg on qelg.qe_entry_id=tfqe.id and tdfpg.grade_id=qelg.grade_id
				left join m_customer mc on mc.id=tfqe.customer_id 
				left join m_quality mq on mq.id=tfqe.quality_id 
				left join m_eucode mec on mec.id=tfqe.eucode_id 
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id 
				left join m_freezingstage mfs on mfs.id=tfqe.freezing_stage_id 
				left join m_brand mb on tdfpe.brand_id=mb.id				
				";
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "DSR=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	
	function createDailyStockReport($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $userId)
	{
		# Get Recs
		$reportRecs = $this->dsReportRecs($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId);

		if (sizeof($reportRecs)>0) {
			# Create table
			$this->createDSRTempTable();
			$this->deleteDSRTempRecs($userId);

			foreach ($reportRecs as $dsr) {
				$qelId		= $dsr[0];
				$qelName	= $dsr[1];
				$customerId 	= $dsr[2];
				$customName	= $dsr[3];
				$qualityId	= $dsr[4];
				$qualityCode	= $dsr[5];
				$euCodeId	= $dsr[6];
				$euCode		= $dsr[7];
				$selProcessCodeId 	= $dsr[8];
				$selProcessCode 	= $dsr[9];
				$freezingStageId 	= $dsr[10];
				$freezingStage  	= $dsr[11];
				$selFrozenCodeId   	= $dsr[12];
				$selBrandId 	= $dsr[13];
				$selBrandName 	= $dsr[14];
				$selGradeId 	= $dsr[15];
				$selGradeCode 	= $dsr[16];
				$gradeDisplayOrder = $dsr[17];
				

				$this->insertDSRTempRec($qelId, $qelName, $customerId, $customName, $qualityId, $qualityCode, $euCodeId, $euCode, $selProcessCodeId, $selProcessCode, $freezingStageId, $freezingStage, $selFrozenCodeId, $selBrandId, $selBrandName, $selGradeId, $selGradeCode, $gradeDisplayOrder, $userId);
			}
		}

		return $this->getDSRTempRecs($userId);
	}

	# Create temp table Daily Stock report temp table
	
	function createDSRTempTable()
	{
		//temporary
		$qry = "create table IF NOT EXISTS `ds_report_tmp` ( 
			`id` int(2) NOT NULL auto_increment,
			`customer_id` int(2) default NULL,
			`customer_name` varchar(50) default NULL,
			`eucode_id` int(2) default NULL,
			`eu_code` varchar(50) default NULL,
			`brand_id` int(2) default NULL,
			`brand` varchar(50) default NULL,
			`qel_id` int(2) default NULL,
			`qel_name` varchar(50) default NULL,
			`freezing_stage_id` int(2) default NULL,
			`freezing_stage` varchar(50) default NULL,
			`quality_id` int(2) default NULL,
			`quality` varchar(50) default NULL,
			`processcode_id` int(2) default NULL,
			`processcode` varchar(50) default NULL,
			`grade_id` int(2) default NULL,
			`grade` varchar(50) default NULL,
			`grade_order` int(2) default NULL,
			`frozencode_id` int(2) default NULL,
			`user_id` int(2) default NULL,
			PRIMARY KEY  (`id`)
			)";
		//echo $qry;
		$result =  $this->databaseConnect->createTable($qry);
		return $result;
	}

	# Insert Temp recs
	function insertDSRTempRec($qelId, $qelName, $customerId, $customName, $qualityId, $qualityCode, $euCodeId, $euCode, $selProcessCodeId, $selProcessCode, $freezingStageId, $freezingStage, $selFrozenCodeId, $selBrandId, $selBrandName, $selGradeId, $selGradeCode, $gradeDisplayOrder, $userId)
	{	
		$qry = "insert into ds_report_tmp (`customer_id`, `customer_name`, `eucode_id`, `eu_code`, `brand_id`, `brand`, `qel_id`, `qel_name`, `freezing_stage_id`, `freezing_stage`, `quality_id`, `quality`, `processcode_id`, `processcode`, `grade_id`, `grade`, `grade_order`, `frozencode_id`, `user_id`) values('$customerId', '$customName', '$euCodeId', '$euCode', '$selBrandId', '$selBrandName', '$qelId', '$qelName', '$freezingStageId', '$freezingStage', '$qualityId', '$qualityCode', '$selProcessCodeId', '$selProcessCode', '$selGradeId', '$selGradeCode', '$gradeDisplayOrder', '$selFrozenCodeId', '$userId')";
		//echo "<br>$qry<br>";
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();
		return $insertStatus;
	}

	function getDSRTempRecs($userId)
	{
		$qry = "select * from ds_report_tmp drt where drt.user_id='$userId' ";
		//echo "Temp:==><br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function deleteDSRTempRecs($userId)
	{
		$qry	= " delete from ds_report_tmp where user_id='$userId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	
	

	function stkRGroupList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null";
		
		if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";

		$groupBy 	= "mf.category_id, tdfpe.processcode_id";
		$orderBy	= "mfc.category asc, mf.name asc, pps.process_criteria desc, pps.sort_id asc, mpc.code asc";

 
		$qry = " select 
				mfc.category as fishCategory, mpc.code as processcode, tdfpe.fish_id, tdfpe.processcode_id
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id
				left join m_fish mf on mf.id=tdfpe.fish_id
				left join m_fishcategory mfc on mfc.id=mf.category_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join pre_process_sequence pps on mpc.id=pps.processcode_id and tdfpe.processcode_id=pps.processcode_id
				";
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "New Group List=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	function sgGradeRecs($fromDate, $tillDate, $fishId, $processCodeId)
	{	
		

		$whr = "tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' and tdfpm.processor_id is not null and (tdfpg.number_mc!=0 or tdfpg.number_loose_slab!=0) and tdfpe.fish_id='$fishId' and tdfpe.processcode_id='$processCodeId'";

		$groupBy	= "tdfpg.grade_id";	
		$orderBy	= "qelg.display_order asc";

		$qry = " select tdfpg.grade_id, mg.code as gradeCode from 
				t_dailyfrozenpacking_main tdfpm left join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_grade mg on tdfpg.grade_id=mg.id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id and tdfpe.quality_id=tfqe.quality_id	
				left join t_fznpakng_qel_grade qelg on qelg.qe_entry_id=tfqe.id and tdfpg.grade_id=qelg.grade_id	
				";
	
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>SG Grade?==><br>$qry<br>"; 

		return $this->databaseConnect->getRecords($qry);
	}

	# daily Stock Product List
	function stkGroupProductList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $sgFishId, $sgProcessCodeId)
	{
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null and tdfpe.fish_id='$sgFishId' and tdfpe.processcode_id='$sgProcessCodeId'";
		
		if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";

		$groupBy 	= "tdfpe.customer_id, tdfpe.eucode_id, tdfpe.brand_id, tdfpe.frozencode_id, tdfpe.freezing_stage_id, tdfpe.quality_id";	
		$orderBy	= "tfqe.name asc";
		
		$qry = " select tfqe.id, tfqe.name as qeName, tfqe.customer_id, mc.customer_name as customerName, tfqe.quality_id, mq.name as quality, tfqe.eucode_id, mec.code as euCode, tdfpe.processcode_id,  mpc.code as processCode, tfqe.freezing_stage_id, mfs.rm_stage as freezingStage, tdfpe.frozencode_id, tdfpe.brand_id, mb.brand 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id
				left join m_fish mf on mf.id=tdfpe.fish_id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id and tdfpe.quality_id=tfqe.quality_id
				left join m_customer mc on mc.id=tfqe.customer_id
				left join m_quality mq on mq.id=tfqe.quality_id 
				left join m_eucode mec on mec.id=tfqe.eucode_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_freezingstage mfs on mfs.id=tfqe.freezing_stage_id
				left join m_brand mb on tdfpe.brand_id=mb.id
				";
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Group List=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Find Max Grade Column count
	function maxGradeCount($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId)
	{
		$stkRGroupList = $this->stkRGroupList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId);	

		$gradeCountArr = array();
		foreach ($stkRGroupList as $sgl) {
				$sgFishId 	=  $sgl[2];
				$sgProcessCodeId =  $sgl[3];
				# grade
				$sgGrades = $this->sgGradeRecs($fromDate, $tillDate, $sgFishId, $sgProcessCodeId);
				$c = 0;
				foreach ($sgGrades as $grr) {
					$c++;
				}
				$gradeCountArr[] = $c;
		}

		$maxGradeCount = max($gradeCountArr);		
		return $maxGradeCount;
	}

	# get Process Sequence
	function getPCSeq()
	{		
		$qry = "select pps.`processcode_id`, pps.`fish_id`, mp.`code`, mf.`name` from pre_process_sequence pps left join m_processcode mp on mp.id=pps.processcode_id left join m_fish mf on mf.id=mp.fish_id  order by mf.`name` asc, pps.process_criteria desc, pps.sort_id asc, mp.`code` asc";
		//echo "<br>$qry";
		return $this->databaseConnect->getRecords($qry);
	}



function getPagingDFPRecs($fromDate, $tillDate, $offset, $limit)
	{

		
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		$groupBy	= " processCodeId, freezingStageId,frozencodeId,mcpackingId";
		$orderBy	= " processCode asc, freezingStage asc, frznPkgCode asc";

		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";	

			$qry1 	= " select a.id as mainid, a.select_date as selectDate, b.fish_id as fishId, b.processcode_id as processCodeId, b.freezing_stage_id as freezingStageId, b.frozencode_id as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm as confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id as mcpackingId , mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id as physical_stock
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
			$qry2= " select a.id as mainid, a.select_date as selectDate, b.fish_id as fishId, b.processcode_id as processCodeId, b.freezing_stage_id  as freezingStageId, b.frozencode_id  as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm  as confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id  as mcpackingId, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  as physical_stock
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";

		
		//if ($whr!="") $qry .= " where ".$whr;
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy!="") $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//echo $qry2;
		$qry="select mainid, selectDate,fishId, processCodeId, freezingStageId,frozencodeId, processCode,freezingStage,  frznPkgCode,confirm, sum(allocatedCount),mcpackingId , mcPkgCode,sum(numMcs), sum(frozenQty),sum(availableQty),physical_stock  from ($qry1 union all $qry2 ) dum";
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit) $qry .= " limit ".$limit;
		

	//echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;
		// echo "<br>-------------------------<br>";
		// foreach($result AS $row){
		// 	print_r($row);
		// 	echo "<br><br>";

		// }
		

	 
	  return $result;
	}

	

	

function getPagingDFPRecs_old($fromDate, $tillDate, $offset, $limit)
	{

		
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		$groupBy	= " processCodeId, freezingStageId,frozencodeId,mcpackingId";
		$orderBy	= " processCode asc, freezingStage asc, frznPkgCode asc";

		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";	

			$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id as processCodeId, b.freezing_stage_id as freezingStageId, b.frozencode_id as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id as mcpackingId , mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
			$qry2= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id  as processCodeId, b.freezing_stage_id  as freezingStageId, b.frozencode_id  as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id  as mcpackingId, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";

		
		//if ($whr!="") $qry .= " where ".$whr;
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		if ($groupBy!="") $qry1 .= " group by ".$groupBy; $qry2 .= " group by ".$groupBy;
		//echo $qry2;
		$qry="select * from ($qry1 union all $qry2 ) dum";
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		// if ($limit) $qry .= " limit ".$limit;

	// echo "<br>$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;
		

	 
	  return $result;
	}



	function getdailystkPagingDFPRecs($fromDate, $tillDate)
	{

		//$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.unit is not null" ;
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";

			$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit) $qry .= " limit ".$limit;

		//echo "<br>Hai$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;
		

	  
	  return $result;
	}

# get Grade Recs
	function getProductionGradeRecs($fromDate, $tillDate)
	{
		$qry1 = "select tdfpg.grade_id as gradeId, mg.code as code from 
			t_dailyfrozenpacking_main tdfpm join
			t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpg.number_mc!=0 and tdfpg.number_mc is not null group by tdfpg.grade_id ";	
		$qry2= "select tdfpg.grade_id as gradeId, mg.code as code  from 
			t_dailyfrozenpacking_main_rmlotid tdfpm join
			t_dailyfrozenpacking_entry_rmlotid tdfpe on tdfpm.id=tdfpe.main_id join t_dailyfrozenpacking_grade_rmlotid tdfpg on tdfpe.id=tdfpg.entry_id left join m_grade mg on tdfpg.grade_id=mg.id
			where tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpg.number_mc!=0 and tdfpg.number_mc is not null group by tdfpg.grade_id";
		$qry="select * from ($qry1 union all $qry2) dum group by gradeId order by code asc " ;
		// echo "Grade===><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		// print_r($result);
		return $result;
	}

	function getDFPForDateRange($fromDate, $tillDate)
	{		
		/*$whr		= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.unit is not null" ;

		$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		$orderBy	= "mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		
		$qry 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id, b.freezing_stage_id, b.frozencode_id, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm 
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
			";
		
		if ($whr!="") $qry .= " where ".$whr;
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;*/


	
		$whr		= " a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";
		$groupBy	= " processCodeId, freezingStageId,frozencodeId,mcpackingId";
		$orderBy	= " processCode asc, freezingStage asc, frznPkgCode asc";

		//$groupBy	= " b.processcode_id, b.freezing_stage_id, b.frozencode_id, b.mcpacking_id";
		//$orderBy	= " mpc.code asc, mfs.rm_stage asc, mfp.code asc";
		$limit 		= " $offset, $limit";	

			$qry1 	= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id as processCodeId, b.freezing_stage_id as freezingStageId, b.frozencode_id as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id as mcpackingId , mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  
			    from 
				t_dailyfrozenpacking_main a left join t_dailyfrozenpacking_entry b on a.id=b.main_id left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";
			$qry2= " select 
				a.id, a.select_date, b.fish_id, b.processcode_id  as processCodeId, b.freezing_stage_id  as freezingStageId, b.frozencode_id  as frozencodeId, mpc.code as processCode, mfs.rm_stage as freezingStage, mfp.code as frznPkgCode, a.report_confirm, (select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) as allocatedCount, b.mcpacking_id  as mcpackingId, mcp.code as mcPkgCode,((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, sum(tdfpg.number_mc) as numMcs, ((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) as frozenQty,((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) as availableQty,a.physical_stock_main_id  
			    from 
				t_dailyfrozenpacking_main_rmlotid a left join t_dailyfrozenpacking_entry_rmlotid b on a.id=b.main_id left join t_dailyfrozenpacking_grade_rmlotid tdfpg on b.id=tdfpg.entry_id 
				left join m_processcode mpc on mpc.id=b.processcode_id
				left join m_freezingstage mfs on mfs.id=b.freezing_stage_id
				left join m_frozenpacking mfp on mfp.id=b.frozencode_id
				left join m_mcpacking mcp on b.mcpacking_id=mcp.id
			";

		
		//if ($whr!="") $qry .= " where ".$whr;
		if ($whr!="") $qry1 .= " where ".$whr; $qry2 .= " where ".$whr;
		$qry="$qry1 union  $qry2";
		if ($groupBy) $qry .= " group by ".$groupBy;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit) $qry .= " limit ".$limit;
		//echo "<br>Hai$qry<br>";
		$result	=	array();
		$result	= $this->databaseConnect->getRecords($qry);
		$fa=$result;	 
	  return $result;



	}
	


	function stkRGList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $reportType, $packType)
	{
		
		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null";
	if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";

		if ($packType=='LS')	$whr .= " and tdfpg.number_loose_slab!=0";
		else $whr .= " and tdfpg.number_mc!=0";
		
	
		$groupBy 	= "tfrg.sort_order";
		$orderBy	= "tfrg.sort_order asc";		
 
		$qry = " select 
				mfc.category as fishCategory, mpc.code as processcode, tdfpe.fish_id, tdfpe.processcode_id, tfrg.freezing_style_id, tfrg.freezing_stage_id, mfz.code as freezingStyle, rm_stage as freezingStage, tfrg.id as groupId, tfrg.name as groupName, tfrg.sort_order
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_fish mf on mf.id=tdfpe.fish_id
				left join m_fishcategory mfc on mfc.id=mf.category_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
				left join pre_process_sequence pps on mpc.id=pps.processcode_id and tdfpe.processcode_id=pps.processcode_id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tfqe.frozencode_id=tdfpe.frozencode_id 
				left join t_fpstk_report_group_entry tfrge on tfrge.qel_id=tfqe.id
				left join t_fpstk_report_group tfrg on tfrg.id=tfrge.main_id and tfrg.freezing_stage_id=tdfpe.freezing_stage_id and mfp.freezing_id=tfrg.freezing_style_id
				left join m_freezing mfz on mfz.id=tfrg.freezing_style_id 
				left join m_freezingstage mfs on mfs.id=tfrg.freezing_stage_id
				";			
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo "New SEP Group Listttttt=====><br/>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	




	function stkRGroupGradeMaxCount($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $reportType, $packType)
	{
		$stkRGroupList = $this->stkRGList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $reportType, $packType);	

		$gradeCountArr = array();
		foreach ($stkRGroupList as $sgl) {
				$sgFishId 	=  $sgl[2];
				$sgProcessCodeId =  $sgl[3];
				$stkGroupId	= $sgl[8];
				# grade
				$sgGrades = $this->stkGrGradeRecs($fromDate, $tillDate, $stkGroupId, $reportType, $packType);
				$c = 0;
				foreach ($sgGrades as $grr) {
					$c++;
				}
				$gradeCountArr[] = $c;
		}

			
		$maxGradeCount = max($gradeCountArr);		
		return $maxGradeCount;
	}

	function stkGProductList($fromDate, $tillDate, $fishId, $selCustomerId, $selFishCategoryId, $stkGroupId, $reportType, $packType)
	{
		

		$whr = "tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpm.processor_id is not null and tfrg.id='$stkGroupId'";
		
		if ($fishId) 		$whr .= " and tdfpe.fish_id in ($fishId)";
		if ($selCustomerId)	$whr .= " and tdfpe.customer_id='$selCustomerId'";
		if ($selFishCategoryId)	$whr .= " and mf.category_id='$selFishCategoryId'";
		if ($packType=='LS')	$whr .= " and tdfpg.number_loose_slab!=0";
		else $whr .= " and tdfpg.number_mc!=0";

		
		$groupBy 	= "tdfpe.frozencode_id, tdfpe.processcode_id";
		
		$orderBy	= "tfqe.name asc, mfs.rm_stage asc, mq.name asc";
		

		$qry = " select tfqe.id, tfqe.name as qeName, tfqe.customer_id, mc.customer_name as customerName, tfqe.quality_id, mq.name as quality, tfqe.eucode_id, mec.code as euCode, tdfpe.processcode_id,  mpc.code as processCode, tfqe.freezing_stage_id, mfs.rm_stage as freezingStage, tdfpe.frozencode_id, tdfpe.brand_id, mb.brand, mf.id as fishId, mf.name as fishName 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_fish mf on mf.id=tdfpe.fish_id
				left join m_fishcategory mfc on mfc.id=mf.category_id
				left join m_processcode mpc on mpc.id=tdfpe.processcode_id
				left join m_frozenpacking mfp on mfp.id=tdfpe.frozencode_id
                                left join m_customer mc on mc.id=tdfpe.customer_id
				left join m_quality mq on mq.id=tdfpe.quality_id 
				left join m_eucode mec on mec.id=tdfpe.eucode_id
				
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tfqe.frozencode_id=tdfpe.frozencode_id 
				left join t_fpstk_report_group_entry tfrge on tfrge.qel_id=tfqe.id
				left join t_fpstk_report_group tfrg on tfrg.id=tfrge.main_id 
				left join m_freezing mfz on mfz.id=tfrg.freezing_style_id 
				left join m_freezingstage mfs on mfs.id=tfrg.freezing_stage_id
				left join m_brand mb on tdfpe.brand_id=mb.id
				";
			
				
	 
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "Group List=====><br/>$qry<br>";		

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function stkGrGradeRecs($fromDate, $tillDate, $stkGroupId, $reportType, $packType)
	{		
		
		$whr = "tdfpm.select_date>='$fromDate' and tdfpm.select_date<='$tillDate' and tdfpm.processor_id is not null and (tdfpg.number_mc!=0 or tdfpg.number_loose_slab!=0) and tfrg.id='$stkGroupId'";

		if ($packType=='LS')	$whr .= " and tdfpg.number_loose_slab!=0";
		else $whr .= " and tdfpg.number_mc!=0";

		$groupBy	= "tdfpg.grade_id";	
		$orderBy	= "qelg.display_order asc";

		$qry = " select tdfpg.grade_id, mg.code as gradeCode from 
				t_dailyfrozenpacking_main tdfpm left join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				left join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
				left join m_grade mg on tdfpg.grade_id=mg.id
				left join t_fznpakng_qel_entry qele on qele.processcode_id=tdfpe.processcode_id
				left join t_fznpakng_quick_entry tfqe on qele.qe_entry_id=tfqe.id and tdfpe.frozencode_id=tfqe.frozencode_id 	
				left join t_fznpakng_qel_grade qelg on qelg.qe_entry_id=tfqe.id and tdfpg.grade_id=qelg.grade_id
				left join t_fpstk_report_group_entry tfrge on tfrge.qel_id=tfqe.id
				left join t_fpstk_report_group tfrg on tfrg.id=tfrge.main_id and tfrg.freezing_stage_id=tdfpe.freezing_stage_id	
				";
		
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy;
		if ($orderBy)	$qry .= " order by ".$orderBy;

		//echo "<br>SG Grade==><br>$qry<br>"; 

		return $this->databaseConnect->getRecords($qry);
	}

	# Num MC
	function getNumMC($fromDate, $tillDate, $processCodeId, $frozenCodeId, $gradeId, $customerId, $euCodeId, $selBrandId, $freezingStageId, $qualityId, $reportType, $packType)
	{
		
		$whr = "tdfpe.processcode_id='$processCodeId' and tdfpe.frozencode_id='$frozenCodeId' and tdfpg.grade_id='$gradeId' and tdfpm.select_date>='".$fromDate."' and tdfpm.select_date<='".$tillDate."' and tdfpe.customer_id='$customerId' and tdfpe.eucode_id='$euCodeId' and tdfpe.brand_id='$selBrandId' and  tdfpe.freezing_stage_id='$freezingStageId' and tdfpe.quality_id='$qualityId'";

		if ($packType=='LS')	$whr .= " and tdfpg.number_loose_slab!=0";
		else $whr .= " and tdfpg.number_mc!=0";

		$groupBy = " tdfpg.grade_id";

		$qry = "select sum(tdfpg.number_mc), sum(tdfpg.number_loose_slab) 
			from 
				t_dailyfrozenpacking_main tdfpm join t_dailyfrozenpacking_entry tdfpe on tdfpm.id=tdfpe.main_id 
				join t_dailyfrozenpacking_grade tdfpg on tdfpe.id=tdfpg.entry_id
			";
		if ($whr) 	$qry .= " where ".$whr;
		if ($groupBy)	$qry .= " group by ".$groupBy; 
		//echo "MC=====><br/>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);

		if ($packType=='LS')	$numPack = $result[0][1];
		else $numPack = $result[0][0];
		
		return $numPack;		
	}
	
	function getThaGradeQty($processId,$freezingStage,$frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId)
	{
		$qry1="select sum(number_mc_thawing) as numThawing from t_dailythawing td left join t_dailythawing_grade tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
		$qry2="select sum(number_mc_thawing) as numThawing from t_dailythawing_rmlotid td left join t_dailythawing_grade_rmlotid tdg on td.id=tdg.main_id where processcode_id='$processId' and freezing_stage_id='$freezingStage' and frozencode_id='$frozenCode' and select_date>='$selectDate' and mcpacking_id='$stkAllocateMCPkgId' and grade_id='$sGradeId' ";
		$qry="select sum(numThawing) from ($qry1 union all $qry2) dum";
		//echo "%%".$qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array($result[0][0]):array();	
	
	}

}	
?>