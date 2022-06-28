<?php
class StateVatMaster
{
	/****************************************************************
	This class deals with all the operations relating to State Vat Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function StateVatMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add a Record
	function addStateVat($state, $userId, $copyFromStateVatId, $stateVatRateListId)
	{
		$qry = "insert into m_state_vat (state_id, created, createdby, rate_list_id) values('$state', NOW(), '$userId', '$stateVatRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Get Last Inserted Id
			$insertedStateVatId = $this->databaseConnect->getLastInsertedId();
			if ($copyFromStateVatId!="") {
				$vatEntryRecords = $this->getVatEntryRecords($copyFromStateVatId);
				if (sizeof($vatEntryRecords)>0) {
					foreach ($vatEntryRecords as $ver) {			
						$stateVatEntryId 	= $ver[0];
						$selProdCategory 	= $ver[2]; 
						$selProdState		= $ver[3]; 	
						$selProdGroup		= $ver[4]; 	
						$vat			= $ver[5];	
						$vatEntryIns = $this->addVatEntries($insertedStateVatId, $selProdCategory, $selProdState, $selProdGroup, $vat);		
					}
				}	
			} # Copy From Ends Here 
			
		} else  {
			$this->databaseConnect->rollback();		
		}
		return $insertStatus;
	}

	# Insert (Entry) Records
	function addVatEntries($lastId, $selPCategory, $selPState, $selPGroup, $vatPercent)
	{
		$qry = "insert into m_state_vat_entry (main_id, product_category_id, product_state_id, product_group_id, vat) values('$lastId', '$selPCategory', '$selPState', '$selPGroup', '$vatPercent')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $stateFilterId, $stateVatRateListFilterId)
	{
		//$qry = "select a.id, a.state_id, b.name from m_state_vat a, m_state b where a.state_id=b.id order by b.name asc limit $offset,$limit";		
		$cDate = date("Y-m-d");

		$whr = " a.state_id=b.id ";

		if ($stateFilterId)	$whr .= " and a.state_id= ".$stateFilterId; 
		if ($stateVatRateListFilterId)	$whr .= " and a.rate_list_id= ".$stateVatRateListFilterId; 
		

		if ($stateVatRateListFilterId=="" && $stateFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_statevat_ratelist f";
		} else {
			$whr .= " and a.rate_list_id=f.id";
			$tableUpdate = " , m_statevat_ratelist f";
		}

		$orderBy = " b.name asc, f.start_date desc ";
		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.state_id, b.name, a.rate_list_id, f.name,a.active from (m_state_vat a, m_state b) $tableUpdate "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($stateFilterId, $stateVatRateListFilterId)
	{
		//$qry	= " select a.id, a.state_id, b.name from m_state_vat a, m_state b where a.state_id=b.id order by b.name asc ";
		$cDate = date("Y-m-d");

		$whr = " a.state_id=b.id ";

		if ($stateFilterId)	$whr .= " and a.state_id= ".$stateFilterId; 
		if ($stateVatRateListFilterId)	$whr .= " and a.rate_list_id= ".$stateVatRateListFilterId; 
		
		if ($stateVatRateListFilterId=="" && $stateFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_statevat_ratelist f";
		} else {
			$whr .= " and a.rate_list_id=f.id";
			$tableUpdate = " , m_statevat_ratelist f";
		}

		$orderBy = " b.name asc ";	

		$qry = " select a.id, a.state_id, b.name from m_state_vat a, m_state b $tableUpdate "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Get a Record based on id
	function find($stateVatId)
	{
		$qry = "select id, state_id, rate_list_id from m_state_vat where id=$stateVatId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Vat Entry Records
	function getVatEntryRecords($stateVatId)
	{
		$qry = " select id, main_id, product_category_id, product_state_id, product_group_id, vat from m_state_vat_entry where main_id='$stateVatId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Update  a  Record
	function updateStateVat($stateVatId, $state, $stateVatRateListId)
	{
		$qry = "update m_state_vat set state_id ='$state', rate_list_id='$stateVatRateListId' where id=$stateVatId ";		
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	# Update Vat Entry
	function updateVatEntries($stateVatId, $selPCategory, $selPState, $selPGroup, $vatPercent)
	{
		$qry = " update m_state_vat_entry set product_category_id='$selPCategory', product_state_id='$selPState', product_group_id='$selPGroup', vat='$vatPercent' where id='$stateVatId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Vat Entry Rec
	function delVatEntryRec($stateVatEntryId)
	{
		$qry = " delete from m_state_vat_entry where id=$stateVatEntryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Record
	function deleteStateVat($stateVatId)
	{
		$qry = " delete from m_state_vat where id=$stateVatId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();

		return $result;
	}

	# Check State Exist
	/*
	function stateEntryExist($stateId)
	{
		$qry = " select id from m_city where state_id='$stateId'";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	*/

	# Check Entry Exist
	function chkEntryExist($state, $productCategory, $cId)
	{
		$updateQry = "";
		if ($cId) $updateQry = " and id!=$cId";
			
		$qry = " select id from m_state_vat where state_id='$state' and product_category_id='$productCategory' $updateQry";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	# Get Vat Rate of Selected state
	function getVatRates($stateVatId)
	{
		$qry = " select distinct vat from m_state_vat_entry where main_id='$stateVatId'";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	# Get No .of Combination Entered
	function getCombination($stateVatId)
	{
		$qry = " select id from m_state_vat_entry where main_id='$stateVatId'";
		//echo $qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete Entry Table Rec
	function deleteStateVatEntryRec($stateVatId)
	{
		$qry 	= " delete from m_state_vat_entry where main_id=$stateVatId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete Selected State Rec
	function deleteStateVatRec($stateVatId)
	{
		$qry 	= " delete from m_state_vat where id=$stateVatId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	# Filter State List
	function filterProductGroupList($productGroupExist)
	{		
		$qry	=	"select  id, name from m_product_group order by name asc";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (!$productGroupExist) $resultArr = array('N'=>'-- No Group --');		
		else if ($productGroupExist) {			
			$resultArr = array('0'=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	function getStateVatRecords()
	{
		//$qry	= " select a.id, a.state_id, b.name from m_state_vat a, m_state b where a.state_id=b.id order by b.name asc ";
		$cDate = date("Y-m-d");

		$qry	= " select a.id, a.state_id, b.name from m_state_vat a, m_state b, m_statevat_ratelist f where a.state_id=b.id and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) order by b.name asc ";
		//echo "State h=<br>$qry<br>";		
		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# ----------------------------
	# Check State Exist
	# ----------------------------
	function checkStateExist($stateId, $stateVatId, $selRateList)
	{
		if ($stateVatId) $uptdQry = " and id!=$stateVatId";
		else $uptdQry = "";

		$qry = " select id from m_state_vat where state_id='$stateId' and rate_list_id='$selRateList' $uptdQry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?true:false;	
	}

	/**
	* Get State Vat Entry Id
	*/
	function getStateVatEntryId($copyFromStateId, $copyFromStateVatRateList) 
	{
		$qry = " select id from m_state_vat where state_id='$copyFromStateId' and rate_list_id='$copyFromStateVatRateList'" ;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	/**
	* Dist Margin Rec Update 
	*/
	# Update Dist Margin State Rec	
	function updateDistMarginRecs($stateVatRateListId)
	{
		list($startDate, $endDate, $stateId) = $this->getStateVatRateListRec($stateVatRateListId);
		$getDistMarginRecs = $this->getDistMarginRecs($startDate,$endDate,$stateId);
		//echo "Size=".sizeof($getDistMarginRecs);
		if (sizeof($getDistMarginRecs)>0) {
			foreach ($getDistMarginRecs as $dr) {
				$productId 		= $dr[1];
				$distMarginStateEntryId = $dr[2];
				list($categoryId, $pStateId, $pGroupId) = $this->findProductRec($productId);
				# Vat Percent
				$vatPercent	= $this->getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $stateVatRateListId);
				if ($vatPercent) {
					$updateDistMarginStateEntryRec = $this->updateDistMarginStateEntry($distMarginStateEntryId,$vatPercent);
				}
			}
		}
	}

	# State Vat Rate List Rec based on rate list id
	function getStateVatRateListRec($stateVatRateListId)
	{
		$qry = " select start_date, end_date, state_id from m_statevat_ratelist where id='$stateVatRateListId' ";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):"";
	}

	# Get Dist Margin Recs
	function getDistMarginRecs($startDate, $endDate, $stateId)
	{
		$whr .= " a.id=b.distributor_margin_id and a.created>='$startDate' and b.state_id='$stateId' ";

		if ($endDate!=0) $whr .= " and a.created<='$endDate' ";
		else $whr .= "";

		$qry = " select a.id, a.product_id, b.id from m_distributor_margin a, m_distributor_margin_state b ";

		if ($whr!="") $qry .= " where ".$whr;
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	# Get Category, Product State Id, Product Group Id
	function findProductRec($productId)
	{
		$qry = " select category_id, product_state_id, product_group_id from m_product_manage where id='$productId'";
		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2]);		
	}

	# Get Vat Percent
	function getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $stateVatRateListId)
	{
		$qry = " select b.vat from m_state_vat a, m_state_vat_entry b where a.id=b.main_id and a.state_id='$stateId' and b.product_category_id='$categoryId' and b.product_state_id='$pStateId' and b.product_group_id='$pGroupId' and rate_list_id='$stateVatRateListId' ";
		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}
	
	function updateDistMarginStateEntry($distMarginStateEntryId,$vatPercent)
	{
		$qry = " update m_distributor_margin_state set vat='$vatPercent' where id=$distMarginStateEntryId";
		//echo "<br>Upate========$qry<br>";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	/**
	* Dist Margin Rec Update Ends Here 
	*/

	# check state vat rec in use
	function chkStateVatRecInUse($selDate)
	{
		$qry = "select id from m_distmargin_ratelist where date_format(start_date,'%Y-%m-%d')>='$selDate'";
		//echo "<br>$qry<br>";

		$distMgnRLRecs = $this->databaseConnect->getRecords($qry);

		if (sizeof($distMgnRLRecs)>0) return true;
		else return false;		
	}

function updatestateVatconfirm($stateVatRateListId)
	{
	$qry	= "update m_state_vat set active='1' where id=$stateVatRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updatestateVatReleaseconfirm($stateVatRateListId)
	{
		$qry	= "update m_state_vat set active='0' where id=$stateVatRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	# -------------------- Dist margin Copy from existing and insert new starts here--------------------
	/*
	function distMgnStateRecs($stateId, $userId)
	{
		$dmsRecs = $this->getDistMgnStateWiseRecs($stateId);
		$prevDistributorId = "";
		$prevRateListId = "";
		
		foreach ($dmsRecs as $dmr) {
			$distributorId 	= $dmr[1];
			$rateListId 	= $dmr[3];

			if ($prevDistributorId!=$distributorId) {
				$distName = $dmr[4];
				//echo "<br>$distributorId-$distName";
				
				$distriName = str_replace (" ",'',$distName);
				$selName =substr($distriName, 0,9);	
				$rateListName = $selName."-".date("dMy");
				$startDate    = date("Y-m-d");
				//echo "<br>$rateListName, $startDate, $rateListId, $userId, $distributorId, $rateListId";
			
				//$distMarginRateListRecIns = $distMarginRateListObj->addDistMarginRateList($rateListName, $startDate, $rateListId, $userId, $distributorId, $rateListId);
				//if ($distMarginRateListRecIns) $distMarginRateListId =$distMarginRateListObj->latestRateList($selDistributor);
			} // Prev cond ends here
						
			$prevDistributorId = $distributorId;
		} // Loop Ends here
	}	
	*/

	

	# -------------------- Dist margin Copy from existing and insert new Ends here--------------------
	
}
?>