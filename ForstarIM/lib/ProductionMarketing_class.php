<?php
class ProductionMarketing
{
	/****************************************************************
	This class deals with all the operations relating to Production Marketing
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionMarketing(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addMarketingCost($mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $mcRateListId)
	{
		$qry	=	"insert into m_prodn_marketing (name, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id) values('$mktgPositionName', '$mktgActual', '$mktgIdeal', '$puCost', '$totCost', '$avgCost', '$mcRateListId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select id, name, actual, ideal, pu_cost, tot_cost, avg_cost,active from m_prodn_marketing where rate_list_id='$selRateList' order by name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));		
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry	= "select id, name, actual, ideal, pu_cost, tot_cost, avg_cost,active from m_prodn_marketing where rate_list_id='$selRateList' and active=1 order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($marketingCostRecId)
	{
		$qry = "select id, name, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id from m_prodn_marketing where id=$marketingCostRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateMarketingCost($marketingCostRecId, $mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $mcRateListId)
	{
		$qry = "update m_prodn_marketing set name='$mktgPositionName', actual='$mktgActual', ideal='$mktgIdeal', pu_cost='$puCost', tot_cost='$totCost', avg_cost='$avgCost', rate_list_id='$mcRateListId' where id=$marketingCostRecId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Record
	function deleteMarketingCostRec($marketingCostRecId)
	{
		$qry	=	" delete from m_prodn_marketing where id=$marketingCostRecId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateMarketingCostconfirm($marketingCostRecId)
	{
		$qry	= " update m_prodn_marketing set active='1' where id=$marketingCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateMarketingCostReleaseconfirm($marketingCostRecId)
	{
		$qry	= " update m_prodn_marketing set active='0' where id=$marketingCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>