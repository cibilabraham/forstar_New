<?php
class SampleProduct
{
	/****************************************************************
	This class deals with all the operations relating to Sample Product Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SampleProduct(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Record
	function addSampleProduct($code, $sampleProductName, $description, $cUserId)
	{
		$qry = "insert into m_sample_product (code, name, description, created, createdby) values('$code', '$sampleProductName', '$description', Now(), '$cUserId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, code, name, description,active from m_sample_product order by name asc limit $offset,$limit";
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, code, name, description,active from m_sample_product order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

function fetchAllRecordsActiveProducts()
	{
		$qry = "select id, code, name, description,active from m_sample_product where active=1 order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	
	# Get Sample Product based on id 
	function find($sampleProductId)
	{
		$qry = "select id, code, name, description from m_sample_product where id=$sampleProductId";
		return $this->databaseConnect->getRecord($qry);
	}

	#fOR SELECTING THE SELECTED Working Area
	function fetchSelectedAreaRecords($editId, $selCityId)
	{
		$qry 	= "select a.id, a.code, a. name, b.id, b.area_id from m_area a left join m_sample_product_area b on a.id=b.area_id and b.sales_staff_id='$editId' where a.city_id='$selCityId' order by b.id desc, a.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Sample Product
	function updateSampleProduct($sampleProductId, $sampleProductName, $description)
	{
		$qry = "update m_sample_product set name='$sampleProductName', description='$description' where id='$sampleProductId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();			
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Delete a Sample Product
	function deleteSampleProduct($sampleProductId)
	{
		$qry = "delete from m_sample_product where id=$sampleProductId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	//function 


	function updateSampleProductconfirm($sampleProductId){
		$qry	= "update m_sample_product set active='1' where id=$sampleProductId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateSampleProductReleaseconfirm($sampleProductId){
	$qry	= "update m_sample_product set active='0' where id=$sampleProductId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
}