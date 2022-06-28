<?php
class Brand
{  
	/****************************************************************
	This class deals with all the operations relating to Brand
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function Brand(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	#Add
	function addBrand($brand)
	{
		$qry	=	"insert into m_brand (brand) values('".$brand."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	

	# Returns all Records (using in other frozen packing screen)
	function fetchPagingRecords($offset, $limit)
	{
		//$qry	= "select a.id, a.customer_id, a.brand, b.customer_name from m_brand a, m_customer b where a.customer_id=b.id order by b.customer_name asc limit $offset, $limit";	
	
		$qry	= "select a.id, a.brand,a.active,((select COUNT(a1.id) from t_fznpakng_quick_entry a1 where a1.brand_from='F' and a1.brand_id=a.id)+(select COUNT(a2.id) from t_dailyfrozenpacking_entry a2 where a2.brand_from='F' and a2.brand_id=a.id)+(select count(a3.id) from t_purchaseorder_rm_entry a3 where a3.brand_from='F' and a3.brand_id=a.id)) as tot from m_brand a order by a.brand asc limit $offset, $limit";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records (using in other frozen packing screen)
	function fetchAllRecords()
	{
	
		$qry	= "select a.id, a.brand from m_brand a order by a.brand asc";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivebrand()
	{
	
		$qry	= "select a.id, a.brand from m_brand a where a.active=1 order by a.brand asc";
		
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Record  based on id 

	function find($brandId)
	{
		$qry	=	"select id, brand from m_brand where id=$brandId";
		return $this->databaseConnect->getRecord($qry);
	}

	
	# Update 
	function updateBrand($brandId, $brand)
	{
		$qry	=	" update m_brand set brand='$brand' where id=$brandId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete
	function deleteBrand($brandId)
	{
		$qry	=	" delete from m_brand where id=$brandId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	
	# Returns Filter all Records Based on CustomerId 
	function filterBrandRecords($customerId)
	{
		$qry	=	"select id, customer_id, brand from m_brand where customer_id='$customerId' order by brand asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Returns Filter all Records Based on CustomerId (PAGING)
	function filterBrandRecordsPaging($customerId, $offset, $limit)
	{
		$qry	=	"select id, brand from m_brand order by brand asc limit $offset, $limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function findBrandCode($brandId)
	{
		$rec = $this->find($brandId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}
	
	function distinctBrandRecords()
	{
		$qry	=	"select distinct brand, id from m_brand order by customer_id asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function chkRecExist($brand, $brandId=null)
	{
		$qry	= "select id from m_brand where brand='$brand'";
		if ($brandId) $qry .= " and id!=$brandId";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;	
	}

	# Returns Default brand + customer brand Based on CustomerId 
	function getBrandRecords($customerId)
	{
		# Common Brand
		$qry1    = "select id, brand from m_brand where active=1 order by brand asc";		
		$result1	 = $this->databaseConnect->getRecords($qry1);
		# Customer Based Brand
		$qry2    = "select id, brand from m_customer_brand where customer_id='$customerId' and active=1 order by brand asc";		
		$result2  = $this->databaseConnect->getRecords($qry2);

		$resultArr = array(''=>'--Select--');
		while (list(,$v) = each($result1)) {
			$resultArr[$v[0].'_F'] = $v[1];
		}
		
		while (list(,$v1) = each($result2)) {
			$resultArr[$v1[0].'_C'] = $v1[1];
		}
		//ksort($resultArr);	
		return $resultArr;
	}


	# -----------------------------------------------------
	# Checking Brand Id in (t_fznpakng_quick_entry, t_dailyfrozenpacking_entry, t_purchaseorder_rm_entry) # t_dailyfrozenrepacking
	# -----------------------------------------------------
	function brandRecInUse($brandId)
	{	
		$qry = " select id from (
				select a.id as id from t_fznpakng_quick_entry a where a.brand_from='F' and a.brand_id='$brandId'
			 union
				select a1.id as id from t_dailyfrozenpacking_entry a1 where a1.brand_from='F' and a1.brand_id='$brandId'
			 union
				select a2.id as id from t_purchaseorder_rm_entry a2 where a2.brand_from='F' and a2.brand_id='$brandId'
			
			) as X group by id ";
		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}


	function updateBrandconfirm($brandId)
	{
	$qry	= "update m_brand set active='1' where id=$brandId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateBrandReleaseconfirm($brandId)
	{
		$qry	= "update m_brand set active='0' where id=$brandId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
		
}
?>