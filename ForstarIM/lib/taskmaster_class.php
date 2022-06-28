<?php
class TaskMaster
{
	/****************************************************************
	This class deals with all the operations relating to Distributor Margin Structure
	*****************************************************************/
	var $databaseConnect_taskmgmt;
	var $filterProduct ;
	
	//Constructor, which will create a db instance for this class
	function TaskMaster(&$databaseConnect_taskmgmt)
	{
        	$this->databaseConnect_taskmgmt =&$databaseConnect_taskmgmt;
	}

	# Add a Record
	function addDistMarginStructure($selDistributor, $selProduct, $distMarginRateListId, $userId)
	{
		$qry = "insert into m_distributor_margin (distributor_id, product_id, rate_list_id, created, createdby) values('$selDistributor', '$selProduct', '$distMarginRateListId', Now(), '$userId')";
		$insertStatus = $this->databaseConnect_taskmgmt->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $insertStatus;
	}

	# Add Dist Margin State Wise Rec
	function addDistMarginStateWiseRec($distMarginLastId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin)
	{
		$qry = "insert into m_distributor_margin_state (distributor_margin_id, state_id, avg_margin, transport_cost, octroi, vat, freight, dist_state_entry_id, actual_margin, final_margin, city_id, area_ids, vat_cst_include, excise_duty, basic_margin) values('$distMarginLastId', '$selStateId', '$avgMargin', '$transportCost', '$octroi', '$vat', '$freight', '$distStateEntryId', '$actualMargin', '$finalMargin', '$selCityId', '$selAreaIds', '$vatorCstInc', '$exciseDutyPercent', '$basicMargin')";

		$insertStatus = $this->databaseConnect_taskmgmt->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $insertStatus;
	}
	

	# Add margin Structure Entry
	function addDistMarginStructureEntry($lastId, $marginStructureId, $distMarginPercent)
	{
		$qry = "insert into m_distributor_margin_entry (dist_state_entry_id, margin_structure_id, percentage) values('$lastId', '$marginStructureId', '$distMarginPercent')";
		$insertStatus = $this->databaseConnect_taskmgmt->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $insertStatus;
	}

	 
	# Copy From Recs 	
	# Get Dist Copy Records
	function getCopyFromDistRecords($copyFromDistId, $copyFromDistRateListId, $selDistMargin)
	{		
		$qry = " select a.id, a.product_id, b.id, b.state_id, b.avg_margin, b.transport_cost, b.octroi, b.vat, b.freight, b.dist_state_entry_id, b.actual_margin, b.final_margin, b.vat_cst_include, b.excise_duty, b.basic_margin  from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$copyFromDistId' and a.rate_list_id='$copyFromDistRateListId' and FORMAT(b.final_margin,4)=$selDistMargin group by FORMAT(b.final_margin,4)";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}


	# Returns all Paging Records
	//function getTaskPagingRecords($offset, $limit, $distributorFilterId, $distributorRateListFilterId)

	function getTaskPagingRecords($offset, $limit)
	{		
		
		
		$cDate = date("Y-m-d");

		/*
		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id";
			
		if ($distributorFilterId!="") $whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") $whr .= " and a.rate_list_id=".$distributorRateListFilterId;
				
		if ($distributorFilterId=="" && $distributorRateListFilterId=="") {			
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_distmargin_ratelist f";
			$groupBy        = " a.distributor_id, d.state_id, h.city_id, ds.export_active, d.final_margin ";
		} else {
			$whr .= " and a.rate_list_id=f.id ";
			$tableUpdate = " , m_distmargin_ratelist f";
			$groupBy        = " a.rate_list_id, a.distributor_id, d.state_id, h.city_id, ds.export_active, d.final_margin ";
		}
		
		$orderBy 	= " b.name asc, e.name asc, g.name asc";
		$limit 		= " $offset,$limit";
		*/
		$orderBy 	= " task_id desc";
		$limit 		= " $offset,$limit";
		//$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code, d.id, e.name, d.avg_margin, d.dist_state_entry_id, g.name, d.state_id, h.city_id, d.final_margin, f.name, ds.export_active from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h left join m_distributor_state ds on h.dist_state_entry_id=ds.id $tableUpdate";
		$qry = "select * from task_list";

		//if ($whr!="") 		$qry .= " where ".$whr;
		//if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			
echo($qry);
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}
	/*
		Get distributor wise state wise location wise assigned Records
	*/
	function getDistMarginProductRecs($distributorId, $stateId, $cityId, $distFinalMargin, $rateListId, $exportEnabled)
	{
		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id and a.distributor_id='$distributorId' and d.state_id='$stateId' and h.city_id='$cityId' and FORMAT(d.final_margin,4)='$distFinalMargin' and a.rate_list_id='$rateListId' and ds.export_active='$exportEnabled'  ";
		
		
		$orderBy 	= " c.code asc ";

		$qry = "select distinct a.product_id, c.code, c.name, a.id, d.id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h left join m_distributor_state ds on h.dist_state_entry_id=ds.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	/*
		Not assigned Product
	*/
	function getProductNotAssignedRecs($distributorId, $stateId, $cityId, $avgMargin, $rateListId)
	{
		$qry = " select p.id, p.code from m_product_manage p where p.id not in (select a.product_id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h where c.id=a.product_id and b.id=a.distributor_id and a.id=d.distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id and a.distributor_id='$distributorId' and d.state_id='$stateId' and h.city_id='$cityId' and FORMAT(d.final_margin,4)=$avgMargin and a.rate_list_id='$rateListId' order by c.code asc) ";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	# Returns all Paging Records
	function getAllPagingRecords($offset, $limit, $distributorFilterId, $distributorRateListFilterId)
	{		
		$tableUpdate = "";
		$cDate = date("Y-m-d");

		$whr = " c.id=a.product_id and b.id=a.distributor_id  ";
		if ($distributorFilterId!="") 		$whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$distributorRateListFilterId;

		if ($distributorRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_distmargin_ratelist f";
		} 
		
		$groupBy        = " a.distributor_id";
		$orderBy 	= " b.name asc, c.code asc";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code from m_distributor_margin a, m_distributor b, m_product_manage c $tableUpdate ";
	
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}


	function getDistributorWiseRecords($distributorFilterId, $distributorRateListFilterId)
	{		
		$tableUpdate = "";
		$cDate = date("Y-m-d");

		$whr = " c.id=a.product_id and b.id=a.distributor_id  ";
		if ($distributorFilterId!="") 		$whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$distributorRateListFilterId;

		if ($distributorRateListFilterId=="") {
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_distmargin_ratelist f";
		} 

		$orderBy 	= " b.name asc, c.code asc";
		
		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code from m_distributor_margin a, m_distributor b, m_product_manage c $tableUpdate ";
	
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;					

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	function getDistributorStateWiseRecs($distributorMarginId)
	{
		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=d.distributor_margin_id and d.state_id=e.id and d.distributor_margin_id='$distributorMarginId'  ";
			
		if ($distributorFilterId!="") 		$whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$distributorRateListFilterId;

		$orderBy 	= " b.name asc, c.code asc, e.name asc";
		
		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code, d.id, e.name, d.avg_margin, d.dist_state_entry_id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $distributorFilterId, $distributorRateListFilterId)
	{		
		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id ";
			
		if ($distributorFilterId!="") 		$whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") 	$whr .= " and a.rate_list_id=".$distributorRateListFilterId;

		$orderBy 	= " b.name asc, c.code asc, e.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code, d.id, e.name, d.avg_margin, d.dist_state_entry_id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;			

		return new ResultSetIterator($this->databaseConnect_taskmgmt->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($distributorFilterId, $distributorRateListFilterId)
	{
		$cDate = date("Y-m-d");

		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id";
			
		if ($distributorFilterId!="") $whr .= " and a.distributor_id=".$distributorFilterId;
		if ($distributorRateListFilterId!="") $whr .= " and a.rate_list_id=".$distributorRateListFilterId;

		if ($distributorFilterId=="" && $distributorRateListFilterId=="") {			
			$whr .= " and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_distmargin_ratelist f";
			$groupBy        = " a.distributor_id, d.state_id, h.city_id, d.final_margin ";
		} else {
			$whr .= " and a.rate_list_id=f.id ";
			$tableUpdate = " , m_distmargin_ratelist f";
			$groupBy        = " a.rate_list_id, a.distributor_id, d.state_id, h.city_id, d.final_margin ";
		}
		
		$orderBy 	= " b.name asc, e.name asc, g.name asc";
		

		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code, d.id, e.name, d.avg_margin, d.dist_state_entry_id, g.name, d.state_id, h.city_id, d.final_margin from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h $tableUpdate";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		
		

		return new ResultSetIterator($this->databaseConnect_taskmgmt->getResultSet($qry));
	}

	#find the margin structure When listing
	function  getDistMarginStructure($distMarginStateEntryId, $marginStructureId)
	{
		$qry = "select a.id, a.dist_state_entry_id, a.margin_structure_id, a.percentage from m_distributor_margin_entry a where a.dist_state_entry_id=$distMarginStateEntryId and a.margin_structure_id=$marginStructureId";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return (sizeof($rec)>0)?$rec[3]:0;
	}
	
	# Get a Record based on id
	function find($distMarginId)
	{
		$qry = "select id, distributor_id, product_id, rate_list_id from m_distributor_margin where id=$distMarginId";
		return $this->databaseConnect_taskmgmt->getRecord($qry);
	}

	# Update  a  Record
	function updateDistMarginStructure($distMarginId, $selDistributor, $selProduct, $distMarginRateListId)
	{
		$qry = "update m_distributor_margin set distributor_id='$selDistributor', product_id='$selProduct', rate_list_id='$distMarginRateListId' where id=$distMarginId ";

		$result = $this->databaseConnect_taskmgmt->updateRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $result;	
	}
	
	# update Dist Margin State Wise Rec
	function updateDistMarginStateWiseRec($distMarginStateEntryId, $selStateId, $avgMargin, $octroi, $vat, $freight, $transportCost, $distStateEntryId, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin)
	{
		$qry = "update m_distributor_margin_state set state_id='$selStateId', avg_margin='$avgMargin', transport_cost='$transportCost', octroi='$octroi', vat='$vat', freight='$freight', dist_state_entry_id='$distStateEntryId', actual_margin='$actualMargin', final_margin='$finalMargin', city_id='$selCityId', area_ids='$selAreaIds', vat_cst_include='$vatorCstInc', excise_duty='$exciseDutyPercent', basic_margin='$basicMargin' where id='$distMarginStateEntryId'";

		$result = $this->databaseConnect_taskmgmt->updateRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $result;	
	}

	# update Dist Margin State Wise Rec
	function updateDistMarginStateWiseGroupRec($distMarginStateEntryId, $avgMargin, $octroi, $vat, $freight, $transportCost, $actualMargin, $finalMargin, $selCityId, $selAreaIds, $vatorCstInc, $exciseDutyPercent, $basicMargin)
	{
		$qry = "update m_distributor_margin_state set avg_margin='$avgMargin', transport_cost='$transportCost', octroi='$octroi', vat='$vat', freight='$freight', actual_margin='$actualMargin', final_margin='$finalMargin', city_id='$selCityId', area_ids='$selAreaIds', vat_cst_include='$vatorCstInc', excise_duty='$exciseDutyPercent', basic_margin='$basicMargin' where id='$distMarginStateEntryId'";

		$result = $this->databaseConnect_taskmgmt->updateRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $result;	
	}

	# update Dist Margin State Wise Rec
	function updateDistSwitchedMgnStateWiseRec($distMarginStateEntryId, $avgMargin, $octroi, $vat, $freight, $transportCost, $actualMargin, $finalMargin, $vatorCstInc, $exciseDutyPercent, $basicMargin)
	{
		$qry = "update m_distributor_margin_state set avg_margin='$avgMargin', transport_cost='$transportCost', octroi='$octroi', vat='$vat', freight='$freight', actual_margin='$actualMargin', final_margin='$finalMargin', vat_cst_include='$vatorCstInc', excise_duty='$exciseDutyPercent', basic_margin='$basicMargin' where id='$distMarginStateEntryId'";

		$result = $this->databaseConnect_taskmgmt->updateRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $result;	
	}
	

	#update margin structure entry Rec
	function  updateDistMarginStructureEntry($distMarginEntryId, $distMarginPercent)
	{
		$qry = "update m_distributor_margin_entry set percentage='$distMarginPercent' where id='$distMarginEntryId' ";

		$result = $this->databaseConnect_taskmgmt->updateRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();		
		return $result;	
	}

	# filter Structure entry Rec
	function filterStructureEntryRecs($distMarginStructureId)
	{
		$qry = "select a.id, a.name, a.descr, a.price_calc, a.use_avg_dist, b.id, b.percentage, a.billing_form_f from m_margin_structure a left join m_distributor_margin_entry b on a.id=b.margin_structure_id where b.dist_state_entry_id='$distMarginStructureId' order by  a.use_avg_dist asc, a.display_order asc, a.name asc";

		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}
	
	# delete Stucture Entry table Rec
	function delDistMagnStructEntryRecs($distMarginId)
	{
		# Get all Dist State Recs
		$distStateRecs = $this->getDistMarginStructStateRecs($distMarginId);
		foreach ($distStateRecs as $dsr) {
			$distStateEntryId = $dsr[0];
			# Delete Margin against each rec
			$deleteMarginEntryRec = $this->delDistMarginEntryRec($distStateEntryId);
		}
		# Delete Dist Magn State Entry Rec
		$qry =	" delete from m_distributor_margin_state where distributor_margin_id=$distMarginId";
		$result	= $this->databaseConnect_taskmgmt->delRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();
		return $result;
	}
	# Delete margin Entry Rec
	function delDistMarginEntryRec($distStateEntryId)
	{
		$qry =	" delete from m_distributor_margin_entry where dist_state_entry_id=$distStateEntryId";

		$result	= $this->databaseConnect_taskmgmt->delRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();
		return $result;
	}

	# Delete margin Entry Rec
	function delDistMarginStateEntryRec($distStateEntryId)
	{
		$qry =	" delete from m_distributor_margin_state where id=$distStateEntryId";

		$result	= $this->databaseConnect_taskmgmt->delRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();
		return $result;
	}

	function chkDistStateRecSize($distMarginId)
	{
		$qry = " select id from m_distributor_margin_state where distributor_margin_id='$distMarginId'";

		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Delete a Record
	function deleteDistMarginStructure($distMarginId)
	{
		$qry =	" delete from m_distributor_margin where id=$distMarginId";

		$result = $this->databaseConnect_taskmgmt->delRecord($qry);
		if ($result) $this->databaseConnect_taskmgmt->commit();
		else $this->databaseConnect_taskmgmt->rollback();
		return $result;
	}

	function getDistMarginStructStateRecs($distMarginId)
	{
		$qry = " select id from m_distributor_margin_state where distributor_margin_id='$distMarginId'";
		
		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	#Checking same entry exist
	function checkEntryExist($selDistributor, $distMarginRateListId, $selProduct, $cId)
	{		

		if ($cId!="") $uptdQry = " and id!=$cId ";
		else $uptdQry = "";

		$qry = "select id from m_distributor_margin where distributor_id='$selDistributor' and rate_list_id='$distMarginRateListId' and product_id='$selProduct' $uptdQry";		

		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# Get Edit Mode Records
	function getFilterDistStateRecords($distributorId, $stateId, $locationId, $avgMargin, $rateListId, $distMarginEntryId, $selProduct, $individualSelected, $exportEnabled)
	{
		if ($distMarginEntryId && $selProduct && $individualSelected) {
			$updateQry = " and c.id='$distMarginEntryId' ";
		} else {
			$updateQry = "";
		}

		$qry = " select a.id, a.distributor_id, a.state_id, a.billing_address, a.delivery_address, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.vat_no, a.tin_no, a.cst_no, a.tax_type, a.billing_form, b.id, b.avg_margin, b.octroi, b.vat, b.freight, b.transport_cost, b.actual_margin, b.final_margin, b.vat_cst_include, a.export_active, b.excise_duty, b.basic_margin, a.octroi_applicable, a.octroi_exempted, a.ex_billing_form from m_distributor_state a join m_distributor_city e on a.id=e.dist_state_entry_id and a.state_id='$stateId' and  e.city_id='$locationId' left join m_distributor_margin_state b on a.id=b.dist_state_entry_id join m_distributor_margin c on b.distributor_margin_id=c.id and c.rate_list_id='$rateListId' and FORMAT(b.final_margin,4)='$avgMargin' $updateQry where a.distributor_id='$distributorId' and a.export_active='$exportEnabled' group by FORMAT(b.final_margin,4)";
		

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	# get in Edit Mode margin Entry Rec
	function getMarginEntryRec($distributorMgnStateEntryId, $marginStructureId)
	{
		$qry = "select id, percentage from m_distributor_margin_entry where dist_state_entry_id='$distributorMgnStateEntryId' and margin_structure_id='$marginStructureId'";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return ($rec>0)?array($rec[0],$rec[1]):0;
	}


	# Returns all Paging Records
	function fetchAllLastUpdatedRecords($offset, $limit)
	{		
		$cDate = date("Y-m-d");

		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and  a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) ";
		
		$orderBy 	= " b.name asc, c.code asc, e.name asc";
		$limit 		= " $offset,$limit";


		$qry = "select a.id, a.distributor_id, a.product_id, a.rate_list_id, b.name, c.code, d.id, e.name, d.avg_margin from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_distmargin_ratelist f";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		

		return new ResultSetIterator($this->databaseConnect_taskmgmt->getResultSet($qry));
	}

	
	# Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	# Filter State List
	function filterProductGroupList($productGroupExist)
	{		
		$qry	=	"select  id, name from m_product_group order by name";

		$result = array();
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		if (!$productGroupExist) $resultArr = array('0'=>'-- No Group --');		
		else if ($productGroupExist) {			
			$resultArr = array('0'=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	function getDistributorRecords($selRateList, $selDistributor)
	{
		$cDate = date("Y-m-d");
		
		$qry = " select distinct a.distributor_id, b.name from m_distributor_margin a, m_distributor b, m_distmargin_ratelist c  where a.distributor_id=b.id and a.distributor_id!='$selDistributor' and  a.rate_list_id=c.id and (('$cDate'>=c.start_date && (c.end_date is null || c.end_date=0)) or ('$cDate'>=c.start_date and '$cDate'<=c.end_date)) order by b.name asc";

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	/**
	* Get Product based on category wise, state wise and group wise selection
	*/
	function getProductRecords($selPCategory, $selPState, $selPGroup)
	{
		//whr	= " category_id='$selPCategory' "; Edited on 12-12-08
		$whr = "";		

		$cSelected = 0;
		if ($selPCategory!="") {
			$whr .= " category_id='$selPCategory' ";
			$cSelected = 1;
		}

		if ($cSelected==1 && $selPState!=0)  $whr .= " and ";		
		if ($selPState!=0) $whr .= " product_state_id='$selPState' ";
		if ($selPGroup!=0) $whr .= " and product_group_id='$selPGroup' ";
				
		$qry = " select id, name from m_product_manage";
		if ($whr!="") $qry .= " where ".$whr;
		

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	/*
		Get Select Location List
	*/
	function getSelCityList($stateEntryId)
	{
		$qry = "select a.id, a.name, b.city_id from m_city a, m_distributor_city b where a.id=b.city_id and b.dist_state_entry_id='$stateEntryId' order by a.name asc";

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}	

	# Get a Record based on id
	function getDistMarginEntryId($selDistributor,$selProduct,$distMarginRateListId)
	{
		$qry = "select id, distributor_id, product_id, rate_list_id from m_distributor_margin where distributor_id='$selDistributor' and product_id='$selProduct' and rate_list_id='$distMarginRateListId' ";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return $rec[0];
	}
	
	/*
		Get Dist Margin State Entry Id
	*/
	function getDistMarginStateEntryId($selDistributor, $distStateId, $locationId, $distMargin, $selProduct, $distMarginRateListId)
	{
		$qry = " select d.id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h where c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id and a.distributor_id='$selDistributor' and d.state_id='$distStateId' and h.city_id='$locationId' and FORMAT(d.final_margin,4)='$distMargin' and a.rate_list_id='$distMarginRateListId' and a.product_id='$selProduct' order by c.code asc ";
		

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return $rec[0];
	}

	/*
		Get distinct Margin
	*/
	function getDistinctMarginRecs($selDistributor, $selProduct, $distMargin, $rateListId, $locExportEnabled)
	{
		$qry = " select 
				b.id, b.final_margin 
			from m_distributor_margin a, m_distributor_margin_state b left join  m_distributor_state ds on b.dist_state_entry_id=ds.id
			where a.id=b.distributor_margin_id and a.distributor_id='$selDistributor' and a.product_id='$selProduct' and a.rate_list_id='$rateListId' and FORMAT(b.final_margin,4)!='$distMargin' and ds.export_active='$locExportEnabled' group by FORMAT(b.final_margin,4) ";

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}
	
	# Get a Record based on id
	function getDistStateEntryId($selDistributor,$selProduct,$distMarginRateListId, $stateId, $distMargin, $locExportEnabled)
	{
		$qry = "select b.id from m_distributor_margin a, m_distributor_margin_state b left join m_distributor_state ds on b.dist_state_entry_id=ds.id where a.id=b.distributor_margin_id and a.distributor_id='$selDistributor' and a.product_id='$selProduct' and a.rate_list_id='$distMarginRateListId' and b.state_id='$stateId' and FORMAT(b.final_margin,4)='$distMargin' and  ds.export_active='$locExportEnabled'";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return $rec[0];
	}
	
	# update Dist Margin State Wise Rec
	function getDistMarginStateWiseRec($distMarginStateEntryId)
	{
		$qry = "select avg_margin, transport_cost, octroi, vat, freight, actual_margin, final_margin, vat_cst_include, excise_duty, basic_margin from m_distributor_margin_state where id='$distMarginStateEntryId'";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3],$rec[4],$rec[5], $rec[6], $rec[7], $rec[8], $rec[9]);		
	}

	function getDistwiseMarginRecs($selDistributor, $rateListId)
	{		
		$qry = " select b.id, b.final_margin  from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$selDistributor' and a.rate_list_id='$rateListId' group by FORMAT(b.final_margin,4) ";

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[1];
		}		
		return $resultArr;
	}


	/**
	*	State Wise Vat Percent Return Vat Percent
	*/
	function getStateWiseVatPercent($productId, $stateId, $distMarginRateList)
	{
		list($categoryId, $pStateId, $pGroupId) = $this->findProductRec($productId);
		
		list($startDate, $endDate) = $this->getDistMgnRateListRec($distMarginRateList);
				
		$vatPercent	= $this->getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $startDate);

		return ($vatPercent!="")?$vatPercent:0;
	}

	function getDistMgnRateListRec($distMarginRateList)
	{
		$qry = " select start_date, end_date from m_distmargin_ratelist where id='$distMarginRateList' ";
		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);

		return (sizeof($rec)>0)?array($rec[0],$rec[1]):"";
	}

	# Get Category, Product State Id, Product Group Id
	function findProductRec($productId)
	{
		$qry = " select category_id, product_state_id, product_group_id from m_product_manage where id='$productId'";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2]);		
	}

	# Get Vat Percent
	function getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $startDate)
	{
		
		$qry = " select b.vat from m_state_vat a join m_state_vat_entry b on a.id=b.main_id join m_statevat_ratelist f on f.id=a.rate_list_id where a.state_id='$stateId' and b.product_category_id='$categoryId' and b.product_state_id='$pStateId' and b.product_group_id='$pGroupId' and date_format(f.start_date,'%Y-%m-%d')<='$startDate' and  (date_format(f.end_date,'%Y-%m-%d')>='$startDate' or (f.end_date is null || f.end_date=0)) order by f.start_date desc ";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return $rec[0];
	}

	# Checking dist Margin Used
	function chkDistMgnUsed($distMarginStateEntryId)
	{
		$qry = " select id from t_salesorder_entry where dist_mgn_state_id='$distMarginStateEntryId'";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function getDistTaxType($distributorId, $stateId)
	{
		$qry = " select tax_type, billing_form, billing_state_id from m_distributor_state where distributor_id='$distributorId' and state_id='$stateId'";		
		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);		
		return array($rec[0],$rec[1], $rec[2]);	
	}

	function getCSTPercent($startDate=null)
	{
		if ($startDate) {
			//$qry = " select base_cst from m_tax where active='Y'";
			$qry = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$startDate' and (date_format(mtrl.end_date,'%Y-%m-%d')>='$startDate' or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		} else {
			$qry = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<=NOW() and (date_format(mtrl.end_date,'%Y-%m-%d')>=NOW() or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		}
		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Get Dist wise State Wise Tax Percent	VAT/CST
	# Get distributor Wise Tax Invoice Calc (NEW)
	function  getDistWiseTaxPercent($productId, $stateId, $distributorId, $distMarginRateList)
	{		
		list($taxType, $billingForm, $billingStateId) 	= $this->getDistTaxType($distributorId, $stateId);
		$vatRate = $this->getStateWiseVatPercent($productId, $stateId, $distMarginRateList);

		//$qCstTax = " select base_cst from m_tax where active='Y' ";	
		list($startDate, $endDate) = $this->getDistMgnRateListRec($distMarginRateList);

		$qCstTax = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$startDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$startDate' or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		$cstResult = $this->databaseConnect_taskmgmt->getRecord($qCstTax);
		if (sizeof($cstResult)>0)	$cstRate =  $cstResult[0];
		else				$cstRate =  0;

		if ($billingForm=='FN')		$cstRate = $this->getStateWiseVatPercent($productId, $billingStateId, $distMarginRateList);
		else if ($billingForm=='FC')	$cstRate = $cstResult[0];
		else if ($billingForm=='FF') 	$cstRate = 0;	

		if ($taxType=='VAT')	return $vatRate;
		if ($taxType=='CST')	return $cstRate;
	}

	# When Aing Product Through Category Wise
	function getDistWiseTPercent($distributorId, $stateId, $categoryId, $pStateId, $pGroupId, $distMarginRateList)
	{
		list($taxType, $billingForm, $billingStateId) 	= $this->getDistTaxType($distributorId, $stateId);
		
		list($startDate, $endDate) = $this->getDistMgnRateListRec($distMarginRateList);
		$vatRate	= $this->getVatPercent($categoryId, $pStateId, $pGroupId, $stateId, $startDate);

		//$qCstTax = " select base_cst from m_tax where active='Y' ";	
		$qCstTax = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$startDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$startDate' or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";

		$cstResult = $this->databaseConnect_taskmgmt->getRecord($qCstTax);
		if (sizeof($cstResult)>0)	$cstRate =  $cstResult[0];
		else				$cstRate =  0;

		if ($billingForm=='FN')		$cstRate = $this->getVatPercent($categoryId, $pStateId, $pGroupId, $billingStateId, $startDate);
		else if ($billingForm=='FC')	$cstRate = $cstResult[0];
		else if ($billingForm=='FF') 	$cstRate = 0;	

		if ($taxType=='VAT')	return $vatRate;
		if ($taxType=='CST')	return $cstRate;
	}


	/**
	*	Get Select Location List
	*/
	function getSelAreaList($selDistributor, $selStateId, $selCityId)
	{
		$qry = " select  e.id, e.name, d.area_id
			from m_distributor a, m_distributor_state b, m_distributor_city c, m_distributor_area d, m_area e 
			where a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.id=d.dist_city_entry_id 
			and  if(d.area_id=0, c.city_id=e.city_id,d.area_id=e.id) and
			a.id='$selDistributor' and b.state_id='$selStateId' and c.city_id='$selCityId' order by e.name asc ";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}

	# Get  Dist State And City Recs
	function getDistributorStateRecords($distributorId, $selState, $selPMCityId, $distMStateEntryId)
	{
		if ($selState) $uptdQry = " and a.state_id='$selState' ";
		if ($selPMCityId) $uptdQry .= " and b.city_id='$selPMCityId'";
		if ($distMStateEntryId!="") $uptdQry .= " and a.id=$distMStateEntryId"; 

		$qry = " select a.id, a.distributor_id, a.state_id, a.billing_address, a.delivery_address, a.pin_code, a.tel_no, a.fax_no, a.mob_no, a.vat_no, a.tin_no, a.cst_no, a.tax_type, a.billing_form, a.billing_state_id, a.same_billing_adr, a.export_active, a.octroi_applicable, a.octroi_exempted, a.ex_billing_form from m_distributor_state a, m_distributor_city b where a.id=b.dist_state_entry_id and a.distributor_id='$distributorId' $uptdQry group by a.state_id, b.city_id, a.export_active order by a.id asc";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}
	
	# Get Not Sel State Records
	function getNotSelStateRecords($selDistributor, $distMarginRateListId, $selProduct, $selStateId, $selPMCityId)
	{
		if ($selProduct!="") $uptQry = " and a.product_id='$selProduct'";
		if ($selStateId!="" && $selPMCityId!="") $uptQry = " and b.state_id!='$selStateId' and b.city_id='$selPMCityId'";

		$qry = " select distinct mds.state_id, ms.name from m_distributor_state as mds, m_state ms
			 where 
				mds.state_id=ms.id and mds.distributor_id='$selDistributor' 
			 	and mds.state_id not in 
				(select b.state_id from m_distributor_margin a, m_distributor_margin_state b 
					where 
						b.distributor_margin_id=a.id and 
						a.distributor_id='$selDistributor' 
						and a.rate_list_id='$distMarginRateListId' $uptQry
				)				
				";		
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result;
	}
	# Dist MGN Id
	function getDistMarginId($selDistributor, $selProduct, $distMarginRateListId)
	{
		$qry = " select id from m_distributor_margin where distributor_id='$selDistributor' and rate_list_id='$distMarginRateListId' and product_id='$selProduct' ";
		$result = $this->databaseConnect_taskmgmt->getRecords($qry);
		return $result[0][0];
	}

	# Get Comma seperated Area Records
	function commaSepAreaList($selDistributor, $selStateId, $selCityId)
	{
		$qry = " select  e.id, e.name, d.area_id
			from m_distributor a, m_distributor_state b, m_distributor_city c, m_distributor_area d, m_area e 
			where a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.id=d.dist_city_entry_id 
			and  if(d.area_id=0, c.city_id=e.city_id,d.area_id=e.id) and
			a.id='$selDistributor' and b.state_id='$selStateId' and c.city_id='$selCityId' order by e.name asc ";
		$getRecords = $this->databaseConnect_taskmgmt->getRecords($qry);
		if (sizeof($getRecords)>0) {
			$areaArr = array();
			$n = 0;
			foreach ($getRecords as $cR) {
				$areaArr[$n] = $cR[0];
				$n++;
			}
			$selAreaIds =  implode(',',$areaArr);
		}

		return $selAreaIds;	
	}

	# ------------------- Production Selection Starts here
	# Returns all Records
	function filterAllProductRecs($selProductIds)
	{
		$whr = " a.id is not null ";
		
		if ($selProductIds) $whr .= " and a.id in ($selProductIds)";
		$orderBy 	= " b.name asc, a.name asc ";
		
		$qry = " select a.id, a.code, a.name, b.id, b.name from m_product_manage a left join m_product_category b on a.category_id=b.id ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);
		$this->filterProduct = $result;

		$resultArr = array();
		$prevCategoryId 	= "";
		$i = 0;
		foreach ($result as $r) {
			$productId 	= $r[0];
			$code		= $r[1];
			$name		= $r[2];
			$categoryId 	= $r[3];
			$categoryName	= $r[4];
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('','',"----- $categoryName -------");	
				$i++;
			}	
		
			$resultArr[$i] 		= array($productId,$code,$name);
			$prevCategoryId 	= $categoryId;
			$i++;
		}
		return $resultArr;
	}

	function implodeFilterProduct()
	{
		$result	= $this->filterProduct;
		$i = 0;
		$resultArr = array();
		foreach ($result as $r) {
			$code		= $r[1];
			$name		= $r[2];		
			$resultArr[$i] 		= $code;			
			$i++;
		}
		return (sizeof($resultArr))?implode(",",$resultArr):"";	
	}

	# get all Selected product
	function getFilterProducts($selProductIds)
	{
		$whr = " a.id is not null ";
		
		if ($selProductIds) $whr .= " and a.id in ($selProductIds)";

		$orderBy 	= " b.name asc, a.name asc ";
		
		$qry = " select a.id, a.code, a.name, b.id, b.name from m_product_manage a left join m_product_category b on a.category_id=b.id ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		

		$result	= $this->databaseConnect_taskmgmt->getRecords($qry);		
		return $result;
	}
	# ------------------- Production Selection Ends here


	function getExciseDutyPercent($productId, $selDate, $groupSelected, $gpCategoryId, $gpStateId, $gpGroupId)
	{
		$whr = " mtrl.ex_duty_active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$selDate' or (mtrl.end_date is null || mtrl.end_date=0)) and mt.product_id=0 ";

		if ($groupSelected) $whr .= " and mt.product_category_id='$gpCategoryId' and  mt.product_state_id='$gpStateId' and mt.product_group_id='$gpGroupId' ";
		else $whr .= " and pm.id='$productId' ";

		$orderBy = " mtrl.start_date desc ";

		$qry = "select mt.excise_duty from m_excise_duty mt join m_excise_duty_ratelist mtrl on mtrl.id=mt.excise_rate_list_id ";
		if (!$groupSelected) $qry .= " join m_product_manage pm on pm.category_id=mt.product_category_id and pm.product_state_id=mt.product_state_id and pm.product_group_id=mt.product_group_id ";

		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;

		//$qry = "select mt.excise_duty from m_excise_duty mt join m_excise_duty_ratelist mtrl on mtrl.id=mt.excise_rate_list_id join m_product_manage pm on pm.category_id=mt.product_category_id and pm.product_state_id=mt.product_state_id and pm.product_group_id=mt.product_group_id where mtrl.ex_duty_active='Y' and date_format(mtrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mtrl.end_date,'%Y-%m-%d')>='$selDate' or (mtrl.end_date is null || mtrl.end_date=0)) and mt.product_id=0 and pm.id='$productId' order by mtrl.start_date desc";

		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}


	function getEduCessDuty($selDate)	
	{		
		$qry = "select mec.base_cst, mecrl.id as eduCessRLId from m_edu_cess mec join m_edu_cess_ratelist mecrl on mecrl.id=mec.rate_list_id where mec.active='Y' and date_format(mecrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mecrl.end_date,'%Y-%m-%d')>='$selDate' or (mecrl.end_date is null || mecrl.end_date=0)) order by mecrl.start_date desc";
		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getSecEduCessDuty($selDate)	
	{
		$qry = "select sec.base_cst, secrl.id from m_sec_edu_cess sec join m_sec_edu_cess_ratelist secrl on secrl.id=sec.rate_list_id where sec.active='Y' and date_format(secrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(secrl.end_date,'%Y-%m-%d')>='$selDate' or (secrl.end_date is null || secrl.end_date=0)) order by secrl.start_date desc";
		$rec = $this->databaseConnect_taskmgmt->getRecord($qry);
		return array($rec[0],$rec[1]);
	}


	/**
		Qry logic is same as getDistMarginProductRecs
		Only for checking the grouped product category
	*/
	function getDMGroupedProducts($distributorId, $stateId, $cityId, $distFinalMargin, $rateListId, $exportEnabled)
	{
		$whr = " c.id=a.product_id and b.id=a.distributor_id and a.id=distributor_margin_id and d.state_id=e.id and g.id = h.city_id and h.dist_state_entry_id=d.dist_state_entry_id and a.distributor_id='$distributorId' and d.state_id='$stateId' and h.city_id='$cityId' and FORMAT(d.final_margin,4)='$distFinalMargin' and a.rate_list_id='$rateListId' and ds.export_active='$exportEnabled'  ";
				
		//$orderBy 	= " c.code asc ";
		$groupBy	= " c.category_id, c.product_state_id, c.product_group_id ";

		$qry = "select c.category_id, c.product_state_id, c.product_group_id from m_distributor_margin a, m_distributor b, m_product_manage c, m_distributor_margin_state d, m_state e, m_city g, m_distributor_city h left join m_distributor_state ds on h.dist_state_entry_id=ds.id";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		$result = $this->databaseConnect_taskmgmt->getRecords($qry);		
		return (sizeof($result)>0)?array($result[0][0], $result[0][1], $result[0][2], sizeof($result)):array();
	}

}
?>