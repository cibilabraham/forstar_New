<?php
class ProductIdentifierMaster
{
	/****************************************************************
	This class deals with all the operations relating to Product Identifier Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductIdentifierMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addProductIdentifier($selDistributor, $selProduct, $indexNo, $userId)
	{
		$qry = "insert into m_product_identifier (distributor_id, product_id, index_no, created, created_by) values('$selDistributor', '$selProduct', '$indexNo', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $selDistributorFilter)
	{
		$whr = "  a.distributor_id=b.id and a.product_id=c.id";
		
		if ($selDistributorFilter!="") $whr .= " and a.distributor_id='$selDistributorFilter'";
				
		$orderBy  = "  b.name asc, c.name asc";
		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.distributor_id, a.product_id, a.index_no, b.name, c.name,a.active from m_product_identifier a, m_distributor b, m_product_manage c ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;		
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function fetchAllRecords($selDistributorFilter)
	{
		$whr = "  a.distributor_id=b.id and a.product_id=c.id";
		
		if ($selDistributorFilter!="") $whr .= " and a.distributor_id='$selDistributorFilter'";
				
		$orderBy  = "  b.name asc, c.name asc";
		$limit 	  = " $offset,$limit";

		$qry = "select a.id, a.distributor_id, a.product_id, a.index_no, b.name, c.name,a.active from m_product_identifier a, m_distributor b, m_product_manage c ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Get a Record based on id
	function find($productIdentifierId)
	{
		$qry = "select id, distributor_id, product_id, index_no from m_product_identifier where id=$productIdentifierId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Record
	function deleteProductIdentifier($productIdentifierId)
	{
		$qry	= " delete from m_product_identifier where id=$productIdentifierId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update  a  Record
	function updateProductIdentifier($productIdentifierId, $selDistributor, $selProduct, $indexNo)
	{
		$qry = "update m_product_identifier set distributor_id='$selDistributor', product_id='$selProduct', index_no='$indexNo' where id=$productIdentifierId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Returns all MRP Products
	function getActiveProducts($distributorId, $distMgnRateListId)
	{	
		$qry = " select distinct a.product_id, b.name from m_distributor_margin a, m_product_manage b where a.product_id=b.id and a.rate_list_id='$distMgnRateListId' and a.distributor_id='$distributorId' and a.product_id not in (select product_id from m_product_status where (distributor_id='$distributorId' or distributor_id=0)) order by b.name asc ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check Product Ientifier Exist
	function chkProductIdentifierExist($selDistributor, $selProduct, $cId)
	{
		if ($cId) $uptdQry = " and id!=$cId";

		$qry = " select id from m_product_identifier where distributor_id='$selDistributor' and product_id='$selProduct' $uptdQry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateProductIdentifierconfirm($productIdentifierId){
		$qry	= "update m_product_identifier set active='1' where id=$productIdentifierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateProductIdentifierReleaseconfirm($productIdentifierId){
		$qry	= "update m_product_identifier set active='0' where id=$productIdentifierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
}

?>