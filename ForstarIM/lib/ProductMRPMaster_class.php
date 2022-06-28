<?php
class ProductMRPMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Product MRP Master
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductMRPMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert A Rec
	function addProductMRP($selProduct, $mrp, $productMRPRateList, $userId)
	{		
		$qry = "insert into m_product_mrp (product_id, mrp, rate_list_id, created, createdby) values ('$selProduct', '$mrp', '$productMRPRateList', NOW(), '$userId')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging  Records
	function fetchAllPagingRecords($offset, $limit, $selRateList, $selProductCategoryId, $selProductStateId, $selProductGroupId, $srchName)
	{
		
		//exit;
		
		
		$whr = " a.product_id=b.id and a.rate_list_id='$selRateList' ";
			
		if ($selProductCategoryId=="") $whr .= "";
		else $whr .= " and b.category_id=".$selProductCategoryId;

		if ($selProductStateId=="") $whr .= "";
		else $whr .= " and b.product_state_id=".$selProductStateId;

		if ($selProductGroupId=="") $whr .= "";
		else if ($selProductGroupId!="" && $selProductStateId!="") {
			$whr .= " and b.product_group_id=".$selProductGroupId;
		}

		if ($srchName!=''){
			if($whr=="")
			//$whr .= " name like '% ".$srchName. "%'";
			$whr .= " (b.name like '%".$srchName."%' or b.name like '".$srchName."%'" ." or b.name like '%".$srchName."')" ;

			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			 $whr .= " and (b.name like '%".$srchName."%' or b.name like '".$srchName."%'" ." or b.name like '%".$srchName."')" ;
		} 
		
		$orderBy 	= " b.name asc ";
		$limit 		= " $offset,$limit";

		$qry = " select a.id, a.product_id, a.mrp, a.rate_list_id, b.name, b.net_wt from m_product_mrp a, m_product_manage b ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords($selRateList, $selProductCategoryId, $selProductStateId, $selProductGroupId, $srchName)
	{
		$whr = " a.product_id=b.id and a.rate_list_id='$selRateList' ";
			
		if ($selProductCategoryId=="") $whr .= "";
		else $whr .= " and b.category_id=".$selProductCategoryId;

		if ($selProductStateId=="") $whr .= "";
		else $whr .= " and b.product_state_id=".$selProductStateId;

		if ($selProductGroupId=="") $whr .= "";
		else if ($selProductGroupId!="" && $selProductStateId!="") {
			$whr .= " and b.product_group_id=".$selProductGroupId;
		}

		if ($srchName!=''){
			
			if($whr=="")
			//$whr .= " name like '% ".$srchName. "%'";
			$whr .= " (b.name like '%".$srchName."%' or b.name like '".$srchName."%'" ." or b.name like '%".$srchName."')" ;

			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			 $whr .= " and (b.name like '%".$srchName."%' or b.name like '".$srchName."%'" ." or b.name like '%".$srchName."')" ;

			
		} 
		
		
		$orderBy 	= " b.name asc ";
		
		$qry = " select a.id, a.product_id, a.mrp, a.rate_list_id, b.name, b.net_wt from m_product_mrp a, m_product_manage b ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		//echo($qry);
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}	
	
	# Get a Record based on Id
	function find($productMRPId)
	{
		$qry = "select id, product_id, mrp, rate_list_id from m_product_mrp where id=$productMRPId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateProductMRPMaster($productMRPId, $selProduct, $mrp, $productMRPRateList)
	{
		$qry = "update m_product_mrp set product_id='$selProduct', mrp='$mrp', rate_list_id='$productMRPRateList' where id='$productMRPId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Purchase Order
	function deleteProductMRPMaster($productMRPId)
	{
		$qry	= " delete from m_product_mrp where id=$productMRPId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check Rec Exist
	function chkRecExist($selProduct, $productMRPRateList, $productMRPId)
	{
		$uptdQry = "";
		if ($productMRPId) $uptdQry = " and id!=$productMRPId";
		else $uptdQry	= "";
		$qry = " select id from m_product_mrp where product_id='$selProduct' and rate_list_id='$productMRPRateList' $uptdQry";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Checking Product MRP using in any where
	function chkProductMRPUsed($selProductId, $selRateListId)
	{
		$qry = " select a.id from t_salesorder a, t_salesorder_entry b  where a.id=b.salesorder_id and b.product_id='$selProductId' and a.rate_list_id='$selRateListId'  ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# get Prev rate List Product MRP
	function getPrevProductMRP($productId, $lastRateListId)
	{
		$qry = " select mpme.mrp as prodMRP from m_product_mrp pm join m_product_mrp_expt mpme on mpme.product_mrp_id=pm.id where pm.product_id='$productId' and pm.rate_list_id='$lastRateListId' and (mpme.state_id+mpme.distributor_id)=0";
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "<br>$qry==".$result[0][0];
		return (sizeof($result)>0)?$result[0][0]:"";
	}

	# Get Distributor Recs
	function getDistributorRecs($stateId)
	{
		$qry = " select md.id, md.code, md.name from m_distributor md join m_distributor_state mds on mds.distributor_id=md.id where mds.state_id='$stateId' group by md.id order by md.name asc ";
		return $this->databaseConnect->getRecords($qry);;
	}

	# Add Product MRP Exception
	function addProductMRPExpt($productMRPEntryId, $selState, $selDistributor, $mrp)
	{
		$qry = "insert into m_product_mrp_expt (product_mrp_id, state_id, distributor_id, mrp) values ('$productMRPEntryId', '$selState', '$selDistributor', '$mrp')";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Product MRP Recs
	function getProductMRPExptRecs($productMRPId)
	{
		$qry = " select id, state_id, distributor_id, mrp from m_product_mrp_expt where product_mrp_id='$productMRPId' order by id asc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Update Expt Entry
	function updateProductMRPExpt($productExptEntryId, $selState, $selDistributor, $mrp)
	{
		$qry = "update m_product_mrp_expt set state_id='$selState', distributor_id='$selDistributor', mrp='$mrp' where id='$productExptEntryId'";
		//echo $qry;

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteProductMRPExpt($productExptEntryId)
	{
		$qry	= " delete from m_product_mrp_expt where id=$productExptEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getDefaultMRP($productMRPId)
	{
		$qry = "select mrp from m_product_mrp_expt where product_mrp_id='$productMRPId' and state_id=0 and distributor_id=0";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}

	# Fetch only Excepted Recs
	function fetchExptedMRP($productMRPId)
	{
		$qry	= "select mpme.id, mpme.state_id, mpme.distributor_id, mpme.mrp, ms.name as stateName, md.name as distName from m_product_mrp_expt mpme left join m_state ms on mpme.state_id=ms.id left join m_distributor md on mpme.distributor_id=md.id where product_mrp_id='$productMRPId' and (mpme.state_id+mpme.distributor_id)!=0 order by ms.name asc, md.name asc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Display Area
	function displayMRPException($productMRPId)
	{	
		$mrpExptRecs = $this->fetchExptedMRP($productMRPId);
		$displayMRPE = "";	
		if (sizeof($mrpExptRecs)>0) {		
			$displayMRPE	= "<table cellspacing=1 bgcolor=#999999 cellpadding=2><tr bgcolor=#fffbcc align=center class=listing-head><td>State</td><td>Distributor</td><td>MRP</td></tr>";	
			
			foreach ($mrpExptRecs as $r) {
				$exptStateId 		= $r[1];
				$exptDistributorId 	= $r[2];
				$mrp			= $r[3];
				$stateName		= $r[4];	
				$distName		= $r[5];
				$diplayStateName = ($stateName)?$stateName:"ALL";
				$diplayDistName = ($distName)?$distName:"ALL";
				$displayMRPE	.= "<tr bgcolor=#fffbcc><td class=listing-item>$diplayStateName</td><td class=listing-item align=center>$diplayDistName</td><td class=listing-item align=right>$mrp</td></tr>";
			}					
			$displayMRPE	.= "</table>";
		}
		return array($displayMRPE, sizeof($mrpExptRecs));
	}
	
	# Delete All Product MRP recs
	function delProductMRPException($productMRPId)
	{
		$qry	= " delete from m_product_mrp_expt where product_mrp_id=$productMRPId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


}

?>