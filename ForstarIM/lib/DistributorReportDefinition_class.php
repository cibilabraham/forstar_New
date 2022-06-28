<?php
class DistributorReportDefinition
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Report Definition
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function DistributorReportDefinition(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addDistReportDefinition ($selDistributor, $selProductMgn, $userId, $selOptionValue)
	{
		$qry = "insert into m_dist_report_definition (distributor_id, rate_margin_id, created, createdby, grouped_mgn_ids) values('$selDistributor', '$selProductMgn', NOW(), '$userId', '$selOptionValue')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function addDistReportDefinitionEntry ($lastId, $mgnStructId, $mgnName)
	{
		$qry = "insert into m_dist_report_definition_entry (main_id, margin_structure_id, display_name) values('$lastId', '$mgnStructId', '$mgnName')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selDistributorFilter)
	{
		$whr = "  a.distributor_id=b.id and a.rate_margin_id=c.id ";
		
		if ($selDistributorFilter!="") $whr .= " and a.distributor_id='$selDistributorFilter'";
		else $whr .= "";
		
		$orderBy  = "  b.name asc, c.name asc";
		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.distributor_id, a.rate_margin_id, b.name, c.name,a.active from m_dist_report_definition a, m_distributor b, m_margin_structure c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;		
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function fetchAllRecords($selDistributorFilter)
	{
		$whr = "  a.distributor_id=b.id and a.rate_margin_id=c.id ";
		
		if ($selDistributorFilter!="") $whr .= " and a.distributor_id='$selDistributorFilter'";
		else $whr .= "";
		
		$orderBy  = "  b.name asc, c.name asc";
		
		$qry = "select a.id, a.distributor_id, a.rate_margin_id, b.name, c.name,a.active from m_dist_report_definition a, m_distributor b, m_margin_structure c";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Get a Record based on id
	function find($distReportDefinitionId)
	{
		$qry = "select id, distributor_id, rate_margin_id, grouped_mgn_ids from m_dist_report_definition where id=$distReportDefinitionId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteDistReportDefinition($distReportDefinitionId)
	{
		$qry	= " delete from m_dist_report_definition where id=$distReportDefinitionId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update a Record
	function updateDistReportDefinition($distReportDefinitionId, $selDistributor, $selProductMgn, $groupedOptionValues)
	{
		$qry = "update m_dist_report_definition set distributor_id='$selDistributor', rate_margin_id='$selProductMgn', grouped_mgn_ids='$groupedOptionValues' where id=$distReportDefinitionId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Check Product Ientifier Exist
	function chkDistReportDefinitionExist($selDistributor, $cId)
	{
		if ($cId) $uptdQry = " and id!=$cId";

		$qry = " select id from m_dist_report_definition where distributor_id='$selDistributor' $uptdQry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Margin Structure Records
	function getMarginStructureRecords($mainId, $selMgnHead)
	{
		$qry = "select a.id, a.name, a.descr, a.price_calc, a.use_avg_dist, a.code, a.display_order, a.billing_form_f, b.id, b.margin_structure_id, b.display_name from m_margin_structure a left join m_dist_report_definition_entry b on b.margin_structure_id=a.id and b.main_id='$mainId' where a.id not in ($selMgnHead) order by a.use_avg_dist asc, a.display_order asc";
		// echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Delete Entry Recs
	function deleteDistReportDefinitionEntryRecs($distReportDefinitionId)
	{
		$qry	= " delete from m_dist_report_definition_entry where main_id=$distReportDefinitionId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Discount Splitup Recs
	function  getDiscountSplitupRecs($distReportDefinitionId)
	{
		$qry = " select b.name from m_dist_report_definition_entry a, m_margin_structure b where a.margin_structure_id=b.id and main_id='$distReportDefinitionId' ";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function getAllMgnStructureRecords($selMgnHead)
	{
		//$qry = "select id, name, descr, price_calc, use_avg_dist, code, display_order, billing_form_f from m_margin_structure where id!=$selMgnHead order by use_avg_dist asc, display_order asc";
		$qry = "select id, name, descr, price_calc, use_avg_dist, code, display_order, billing_form_f from m_margin_structure where id not in ($selMgnHead) order by use_avg_dist asc, display_order asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


function updatedistReportconfirm($distReportDefinitionId){
		$qry	= "update m_dist_report_definition set active='1' where id=$distReportDefinitionId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updatedistReportReleaseconfirm($distReportDefinitionId){
		$qry	= "update m_dist_report_definition set active='0' where id=$distReportDefinitionId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}



	
}

?>