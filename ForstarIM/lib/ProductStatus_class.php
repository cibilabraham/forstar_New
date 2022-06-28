<?php
class ProductStatus
{
	/****************************************************************
	This class deals with all the operations relating to State Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ProductStatus(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add Records
	function addProductStatusRecs($selProduct, $selDistributor, $selState, $inActive, $userId, $cityId)
	{
		$qry = "insert into m_product_status (product_id, distributor_id, state_id, inactive, created, createdby, city_id) values('$selProduct', '$selDistributor', '$selState', '$inActive', NOW(), $userId, '$cityId')";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else 		  $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Vat Entry Records
	function getInactiveProductRecords()
	{
		$qry = " select id, product_id, distributor_id, state_id, inactive from m_product_status order by id asc ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete All Records	
	function deleteInactiveRecords()
	{
		$qry = " delete from m_product_status ";
		$result		= $this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}

	
	# ------------------------------------------------------------------------------------	
	function getDistributorMarginRecs($productId)
	{

		$cDate = date("Y-m-d");

		$whr 		= " b.id=a.distributor_id and a.product_id='$productId'  and a.rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date))";
		$orderBy 	= " b.name asc ";
		$qry 		= " select distinct a.distributor_id, b.name from m_distributor_margin a, m_distributor b, m_distmargin_ratelist f ";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		//echo "<br>".$qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		$resultArr = array('0'=>'-- Select All --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Filter State List
	function filterStateList($selDistributorId)
	{
		$qry = " select  distinct c.id, c.name from m_distributor a, m_distributor_state b, m_state c where a.id=b.distributor_id and b.state_id=c.id and a.id='$selDistributorId' order by c.name asc ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		$resultArr = array('0'=>'-- Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Filter Distributor List
	function getDistributorRecs($selStateId)
	{
		$qry = "select  distinct a.id, a.name from m_distributor a, m_distributor_state b where a.id=b.distributor_id and b.state_id='$selStateId' order by a.name asc";
		
		//rekha updated code dated 26 june 2019
		//$qry="select distinct a.id, a.name from m_distributor a inner join m_distributor_state b on a.id=b.distributor_id where b.state_id = '$selStateId' order by a.name asc";
		
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select All --');
		/*
		$arrstr="";
		foreach ($result as $key=>$val) {
		//$objResponse->alert($val[1]);
			if($arrstr!=""){
			$arrstr = $arrstr.","."$val[0]=>$val[1]" ;
			}
			else{
				$arrstr = "$val[0]=>$val[1]";
				
			}
		}
		
		$resultArr = array_merge($resultArr, array($arrstr));
		*/
		
		//array_push($resultArr,'fhdfhh');
		//$resultArr = array_merge($resultArr, array("cat"=>"wagon","foo"=>"baar"));
		
	/*	
		foreach($result as $key => $value){
			$resultArr = array_merge($resultArr, array("cat"=>"wagon","foo"=>"baar"));	
		}
	*/	
		
		
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
			//$resultArr = array_merge($resultArr, array("cat"=>"wagon","foo"=>"baar"));
		}
		
		return $resultArr;
	}

	# Filter Dist Recs
	function filterDistRecs($selStateId, $distributorId)
	{		
		$whr = " a.id=b.distributor_id and b.id=c.dist_state_entry_id and c.city_id=d.id and b.state_id='$selStateId' ";

		if ($distributorId!="") $whr .= " and a.id='$distributorId' ";
		
		//$groupBy = " c.city_id, b.export_active "; 
		$orderBy = " a.name asc, d.name asc ";

		$qry = " select c.city_id, a.id, a.name as distName, d.name as cityName, b.export_active, b.id as distStateEntryId from m_distributor a, m_distributor_state b, m_distributor_city c, m_city d  ";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy)		$qry .= " group by ".$groupBy;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		//echo "<br>$qry";

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function chkProductAssign($productId, $distributorId, $stateId, $selRateListId, $cityId, $distMStateEntryId)
	{
		$qry = "select a.id from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and a.product_id='$productId' and a.rate_list_id='$selRateListId' and b.state_id='$stateId' and b.city_id='$cityId'";
		if ($distMStateEntryId!="") $qry .= " and b.dist_state_entry_id='$distMStateEntryId' "; 

		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Delete All Records	
	function deleteProductStatus($selProduct, $selDistributor, $selState, $cityId)
	{
		$qry = " delete from m_product_status where product_id='$selProduct' and distributor_id='$selDistributor' and state_id='$selState' and city_id='$cityId'";
		$result		= $this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;
	}
	
	# Checking Prouct inactive
	function chkProductInactive($selStateId, $selDistributorId, $mproductId, $cityId)
	{
		$qry = " select id  from m_product_status where product_id='$mproductId' and distributor_id='$selDistributorId' and state_id='$selStateId' and city_id='$cityId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;	
	}

	function getDistMgnRec($productId, $distributorId, $stateId, $selRateListId, $cityId)
	{
		$qry = " select a.id, b.id from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and a.product_id='$productId' and a.rate_list_id='$selRateListId' and b.state_id='$stateId' and b.city_id='$cityId'  ";

		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	# check Product used in Sales Order
	function chkProductInUse($stateId, $distributorId, $productId, $cityId)
	{
		$qry = " select a.so, a.invoice_type, a.proforma_no, a.sample_invoice_no from t_salesorder a, t_salesorder_entry b where a.id=b.salesorder_id and a.state_id='$stateId' and a.distributor_id='$distributorId' and b.product_id='$productId' and a.city_id='$cityId' ";
		$soRecords = $this->databaseConnect->getRecords($qry);
		$invArr = array();
		$resultArrVal = "";
		if (sizeof($soRecords)>0) {
			$i = 0;			
			foreach ($soRecords as $dsor) {
				$soNo 	= $dsor[0];		
				$invType = $dsor[1];			
				$pfNo 	= $dsor[2];
				$saNo	= $dsor[3];
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "S$saNo";
				$invArr[$i] = $invoiceNo;
				$i++;
			}
			$resultArrVal = implode(",",$invArr);
		}

		return (sizeof($invArr)>0)?$resultArrVal:"";	
	}

	# Get Dist Margin State Records
	function getDistMarginStateRecs($mProductId, $distributorId, $stateId, $rateListId)
	{
		$whr = " a.id=b.distributor_margin_id and a.product_id='$mProductId' and a.distributor_id='$distributorId' and b.state_id='$stateId' and a.rate_list_id='$rateListId'  ";	

		$qry = "select a.id, b.id from m_distributor_margin a, m_distributor_margin_state b ";
		if ($whr!="") 		$qry .= " where ".$whr;	
		
		//echo "<br>".$qry."<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Distibutor Rate List Recs
	function filterDistMgnRLRecs($distributorId)
	{
		$qry = "select id, CONCAT(name,' (',DATE_FORMAT(start_date,'%d/%m/%Y') ,')') as RLFormat from m_distmargin_ratelist where distributor_id='$distributorId' order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	
}
?>