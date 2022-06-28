<?php
class ProductionTravel
{
	/****************************************************************
	This class deals with all the operations relating to Production Travel
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductionTravel(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addTravelCost($marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $tcRateListId)
	{
		$qry	= "insert into m_prodn_travel (marketing_person_id, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id) values('$marketingPerson', '$mktgActual', '$mktgIdeal', '$puCost', '$totCost', '$avgCost', '$tcRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selRateList)
	{
		$qry = "select a.id, b.name, a.actual, a.ideal, a.pu_cost, a.tot_cost, a.avg_cost,a.active from m_prodn_travel a left join m_prodn_marketing b on a.marketing_person_id=b.id  where a.rate_list_id='$selRateList' order by b.name asc limit $offset,$limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));		
	}

	# Returns all Records
	function fetchAllRecords($selRateList)
	{
		$qry = "select a.id, b.name, a.actual, a.ideal, a.pu_cost, a.tot_cost, a.avg_cost from m_prodn_travel a left join m_prodn_marketing b on a.marketing_person_id=b.id  where a.rate_list_id='$selRateList' order by b.name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($travelCostRecId)
	{
		$qry = "select id, marketing_person_id, actual, ideal, pu_cost, tot_cost, avg_cost, rate_list_id from m_prodn_travel where id=$travelCostRecId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateTravelCost($travelCostRecId, $marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $tcRateListId)
	{
		$qry = "update m_prodn_travel set marketing_person_id='$marketingPerson', actual='$mktgActual', ideal='$mktgIdeal', pu_cost='$puCost', tot_cost='$totCost', avg_cost='$avgCost', rate_list_id='$tcRateListId' where id=$travelCostRecId ";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Record
	function deleteTravelCostRec($travelCostRecId)
	{
		$qry	=	" delete from m_prodn_travel where id=$travelCostRecId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else  $this->databaseConnect->rollback();
		return $result;
	}

	# Check Rec Exist
	function checkRecExist($marketingPerson, $tcRateListId, $cId)
	{
		$uptdQry = "";
		if ($cId) $uptdQry = " and id!=$cId";

		$qry = " select id from m_prodn_travel where marketing_person_id='$marketingPerson' and rate_list_id='$tcRateListId' $uptdQry";

		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false; 
	}

	function updateTravelCostconfirm($travelCostRecId)
	{
		$qry	= " update m_prodn_travel set active='1' where id=$travelCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateTravelCostReleaseconfirm($travelCostRecId)
	{
		$qry	= " update m_prodn_travel set active='0' where id=$travelCostRecId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	
}
?>