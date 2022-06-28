<?php
class FuelRate
{
	/****************************************************************
	This class deals with all the operations relating to Recipe Main Category
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function FuelRate(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add Category
	function addFuelRate($date, $fuelRate, $userId)
	{
		$qry	=	"insert into fuel_rate (rate_date, rate,createdby,createdon) values('".$date."','".$fuelRate."','".$userId."',Now())";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Return Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select  id, rate_date, rate,active from fuel_rate order by id desc  limit $offset,$limit";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Categorys 
	function fetchAllRecords()
	{
		$qry	= "select  id, rate_date, rate,active from fuel_rate order by id desc ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsActiveCategory()
	{
		$qry	= "select   id, rate_date, rate,active from fuel_rate where active=1 order by id desc ";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Category based on id 
	function find($categoryId)
	{
		$qry	= "select id, rate_date, rate,active from fuel_rate where id=$categoryId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Category 
	function deleteFuelRate($rateId)
	{
		$qry	=	" delete from fuel_rate where id=$rateId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Category
	function updateFuelRate($fuelId, $rateDate, $rate)
	{
		$qry	= " update fuel_rate set rate_date='$rateDate', rate='$rate' where id=$fuelId";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Check whether the selected category link with any other screen
	function checkMoreEntriesExist($categoryId)
	{
		$qry = "select id from recipe_subcategory where main_category_id='$categoryId'";
		//echo $qry;
		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateFuelRateconfirm($rateId)
	{
	$qry	= "update fuel_rate set active='1' where id=$rateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateFuelRateReleaseconfirm($rateId)
	{
		$qry	= "update fuel_rate set active='0' where id=$rateId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}

?>