<?php
class SchemeMaster
{
	/****************************************************************
	This class deals with all the operations relating to Scheme Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SchemeMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addSchemeMaster($schemeName, $buyNum, $buyBasedOn, $selProduct, $selMrp, $getNum, $getProductType, $getMrpProductType, $getMrpGroupType, $selGroupMrp, $selIndProduct, $selSampleProduct, $userId)
	{
		$qry = "insert into m_scheme (name, buy_num, buy_type, buy_mrp, get_num, get_type, get_mrp_type, get_mrp_group_type, get_group_mrp, get_sample_product_id, created, createdby) values('$schemeName', '$buyNum', '$buyBasedOn', '$selMrp', '$getNum', '$getProductType', '$getMrpProductType', '$getMrpGroupType', '$selGroupMrp', '$selSampleProduct', Now(), '$userId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			$schemeId = $this->databaseConnect->getLastInsertedId();
			if ($buyBasedOn=='P' && $selProduct!="") { // If Product multiple selection of Product
				$selectType = 'P';
				$this->addSchemeProduct($schemeId, $selProduct, $selectType);
			}
			if ($getMrpProductType=='I' && $selIndProduct!="") { // If MRP Product Type== individual
				$selectType = 'I';
				$this->addSchemeProduct($schemeId, $selIndProduct, $selectType);
			}			
		}
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Scheme Product
	function addSchemeProduct($schemeId, $selProduct, $selectType)
	{
 	 	if($selProduct){
			foreach ($selProduct as $pId){
				$selProductId	=	"$pId";
				$qry	=	"insert into m_scheme_product (scheme_id, product_id, select_type) values('".$schemeId."','".$selProductId."','".$selectType."')";
				//echo $qry;
				$insertGrade	=	$this->databaseConnect->insertRecord($qry);
				if ($insertGrade) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		}	
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry = "select id, name, buy_num, buy_type, buy_mrp, get_num, get_type, get_mrp_type, get_mrp_group_type, get_group_mrp, get_sample_product_id,active,(select count(a1.id) from m_scheme_assign a1 where a1.scheme_id=a.id)as tot from m_scheme a order by name asc limit $offset, $limit";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, name, buy_num, buy_type, buy_mrp, get_num, get_type, get_mrp_type, get_mrp_group_type, get_group_mrp, get_sample_product_id,active,(select count(a1.id) from m_scheme_assign a1 where a1.scheme_id=a.id)as tot from m_scheme a order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	function fetchAllRecordsActiveScheme()
	{
		$qry = "select id, name, buy_num, buy_type, buy_mrp, get_num, get_type, get_mrp_type, get_mrp_group_type, get_group_mrp, get_sample_product_id,active from m_scheme where active=1 order by name asc";
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}
	
	# Get a Record based on id
	function find($schemeMasterId)
	{
		$qry = "select id, name, buy_num, buy_type, buy_mrp, get_num, get_type, get_mrp_type, get_mrp_group_type, get_group_mrp, get_sample_product_id from m_scheme where id=$schemeMasterId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update  a  Record
	function updateSchemeMasterRec($schemeMasterId, $schemeName, $buyNum, $buyBasedOn, $selProduct, $selMrp, $getNum, $getProductType, $getMrpProductType, $getMrpGroupType, $selGroupMrp, $selIndProduct, $selSampleProduct)
	{
		$qry = "update m_scheme set name='$schemeName', buy_num='$buyNum', buy_type='$buyBasedOn', buy_mrp='$selMrp', get_num='$getNum', get_type='$getProductType', get_mrp_type='$getMrpProductType', get_mrp_group_type='$getMrpGroupType', get_group_mrp='$selGroupMrp', get_sample_product_id='$selSampleProduct' where id=$schemeMasterId ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			if ($buyBasedOn=='P' && $selProduct!="") { // If Product multiple selection of Product
				$selectType = 'P';
				$this->addSchemeProduct($schemeMasterId, $selProduct, $selectType);
			}
			if ($getMrpProductType=='I' && $selIndProduct!="") { // If MRP Product Type== individual
				$selectType = 'I';
				$this->addSchemeProduct($schemeMasterId, $selIndProduct, $selectType);
			}	
		}
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# Delete Scheme Master Product Entry
	function delSchemeMasterProductRec($schemeMasterId)
	{
		$qry =	" delete from m_scheme_product where scheme_id=$schemeMasterId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Record
	function deleteSchemeMasterRec($schemeMasterId)
	{
		$qry =	" delete from m_scheme where id=$schemeMasterId";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Edit mode
	function fetchBuyProductRecs($editId)
	{
		$qry = " select a.id, a.code, a.name, b.product_id from t_combo_matrix a left join m_scheme_product b on a.id=b.product_id and b.scheme_id='$editId' and select_type='P' order by b.id desc, a.code asc";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));		
	}
	function fetchGetIndProductRecs($editId)
	{
		$qry = " select a.id, a.code, a.name, b.product_id from t_combo_matrix a left join m_scheme_product b on a.id=b.product_id and b.scheme_id='$editId' and select_type='I' order by b.id desc, a.code asc";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));		
	}

	/*
	#Checking same entry exist
	function checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, $schemeMasterId)
	{
		if ($schemeMasterId!="") $updateQry = " and id!='$schemeMasterId'"; // While Updating
		else $updateQry = "";

		$qry = "select id from m_scheme where retail_counter_id='$selRetailCounter' and rate_list_id='$retCtMarginRateListId' and product_id='$selProduct' $updateQry";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	*/
	function chkAllProductSelectedRec($schemeId, $selectType)
	{
		$qry = " select product_id from m_scheme_product where scheme_id='$schemeId' and select_type='$selectType'";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]==0)?true:false;		
	}

	function listSelProduct($schemeId, $selectType)
	{
		$fetchSchemeProductRecords = $this->getSchemeProductRecs($schemeId, $selectType);
		
		$numLine = 3;
		if (sizeof($fetchSchemeProductRecords)>0) {
			$proTble = "<table cellpadding='0' cellspacing='0' align='center'><tr>";
			$nextRec	=	0;
			$k=0;
			foreach ($fetchSchemeProductRecords as $pR) {
				$j++;
				$proName = $pR[1];
				$nextRec++;
			$proTble .= "<td class=\"listing-item\">";
				if($nextRec>1) {
			$proTble .= ","; 
				}
			$proTble .= $proName;
			$proTble .= "</td>";
				if($nextRec%$numLine == 0) {
		$proTble .= "</tr><tr>";
				}	
			}
		$proTble .= "</tr></table>";
		}
			
		echo $proTble;
	}
	// Get Scheme Product
	function getSchemeProductRecs($schemeId, $selectType)
	{
		$qry = "select a.product_id, b.code from m_scheme_product a, t_combo_matrix b where a.product_id=b.id and a.scheme_id='$schemeId' and a.select_type='$selectType'";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateSchemeMasterconfirm($schemeId){
		$qry	= "update m_scheme set active='1' where id=$schemeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateSchemeMasterReleaseconfirm($schemeId){
	$qry	= "update m_scheme set active='0' where id=$schemeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

}
?>