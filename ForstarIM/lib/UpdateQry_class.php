<?php
class UpdateQry
{
	/****************************************************************
	This class deals with all the operations relating to Update Qry
	*****************************************************************/
	var $databaseConnect;
	//Constructor, which will create a db instance for this class
	function UpdateQry(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function getAllShipmentInvRecs()
	{

		$qry = " select im.id, im.invoice_no, im.invoice_date, im.proforma_no, im.confirmed, me.alpha_code from t_invoice_main im left join m_exporter me on me.id=im.exporter_id where im.confirmed='Y'";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;		
	}

	function updateShipmentInvoiceRec($mainId, $expInvNum)
	{
		$qry	= "update t_invoice_main set exp_invoice_no='$expInvNum' where id='$mainId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getContainerRecs()
	{
		$qry = "select id, container_id, select_date, created from t_container_main where container_no is not null and cid_year='2012'  order by  id asc";
		//echo "$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function updateContainer()
	{
		$qry = "update t_container_main set container_id=0";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateContainerRec($id, $cId)
	{
		$qry = "update t_container_main set container_id='$cId' where id='$id'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function updateLocationName($stateEntryId, $locationName)
	{
		$qry = "update m_distributor_state set loc_name='$locationName' where id='$stateEntryId' ";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	function getDistRecs()
	{
		$qry = "select a.id,b.id, b.loc_name from m_distributor a, m_distributor_state b where a.id=b.distributor_id ";
		//echo "$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function getSelAreaRecs($distributorId, $distStateEntryId)
	{		
		$qry = " select b.area_id, c.name, e.name as cityName, a.tax_type, a.billing_form, a.octroi_applicable, a.octroi_percent, a.octroi_exempted, a.entry_tax_applicable, a.entry_tax_percent, a.entry_tax_exempted, a.state_id, d.city_id 
			from 
				m_distributor_state a join m_distributor_city d on a.id=d.dist_state_entry_id 
				left join m_city e on d.city_id=e.id 
				join m_distributor_area b on d.id=b.dist_city_entry_id 
				left join m_area c on b.area_id=c.id 
			where a.distributor_id='$distributorId' and a.id='$distStateEntryId' order by c.name asc";

		//echo "<br>$qry<br>";			
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}

	function updateDAcDebCreRec($distACId, $debitAmt, $creditAmt)
	{
		$qry = "update t_distributor_ac set debit_amt='$debitAmt', credit_amt='$creditAmt' where id='$distACId'";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function getRefInvRecs($invoiceId)
	{
		$qry = "select tdac.id, tdac.distributor_id, tdac.amount, tdac.cod, tdac.so_id, tdaci.invoice_id, tdac.value_date from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join m_common_reason mcr on mcr.id=tdac.reason_id where (mcr.de_code!='SI' or mcr.de_code is null) and tdaci.invoice_id='$invoiceId' ";
		//echo "Ref:: $invoiceId=<br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getDACRecs()
	{
		$qry = "select tdac.id, tdac.distributor_id, tdac.amount, tdac.cod, tdac.so_id from t_distributor_ac tdac join t_distributor_ac_invoice tdaci on tdac.id=tdaci.dist_ac_id join m_common_reason mcr on mcr.id=tdac.reason_id where mcr.de_code='SI' ";
		//echo "<br>$qry<br>";

		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getParentRec($parentACId)
	{
		$qry = "select value_date from t_distributor_ac where id=$parentACId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}

	function getNotPRRecs()
	{
		$qry = " select id, reason_id, select_date, parent_ac_id, value_date from t_distributor_ac ";
		return $this->databaseConnect->getRecords($qry);
	}	

	function updateDistAC($distAcId, $selDate)
	{
		$qry = "update t_distributor_ac set value_date='$selDate' where id='$distAcId'";
		//echo "$qry";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function truncateDistACMainTable()
	{
		$qry1 = "truncate table `t_distributor_ac_invoice`";
		$qry2 = "truncate table `t_distributor_ac_chk_list`";
		$qry3 = "truncate table `t_distributor_ac`";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry1);
		$result = $this->databaseConnect->updateRecord($qry2);
		$result = $this->databaseConnect->updateRecord($qry3);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateDistributorMasterRec()
	{
		$qry = "update m_distributor set amount=0";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function getConfirmedSalesOrderRecs()
	{
		$qry = " select id, distributor_id, invoice_date, so as invNum, ROUND((grand_total_amt+round_value),2) as totalAmt, createdby, city_id from t_salesorder where complete_status='C' and invoice_type='T' and so!=0";	
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);		
	}
	

	function getPreProcessRateList()
	{
		$qry = " select id, name, start_date, end_date from m_processratelist order by id desc";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update m_processratelist set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function getProductMRPList() 
	{		
		$qry = " select id, mrp from m_product_mrp order by id asc";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
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

	function getDefaultMRP($productMRPId)
	{
		$qry = "select mrp from m_product_mrp_expt where product_mrp_id='$productMRPId' and state_id=0 and distributor_id=0";
		//echo $qry;
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}

	function getDailyPProcessRecs()
	{
		# $qry = "select a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, c.id, c.preprocess_qty, c.select_commission, c.select_rate, c.actual_amount, c.paid, c.settlement_date, b.center_id, d.criteria from t_dailypreprocess a , t_dailypreprocess_entries b, t_dailypreprocess_processor_qty c, m_process d where a.id=b.dailypreprocess_main_id and b.id=c.dailypreprocess_entry_id and b.process=d.id and b.process='$processId'";
		
		$qry = "select 
				a.id, a.fish_id, a.date, b.id, b.process, b.opening_bal_qty, b.arrival_qty, b.total_qty, b.total_preprocess_qty, b.actual_yield, b.ideal_yield, b.diff_yield, b.center_id, d.criteria, d.rate, d.commi, d.processes 
			from t_dailypreprocess a, t_dailypreprocess_entries b, m_process d 
			where a.id=b.dailypreprocess_main_id and b.process=d.id and a.confirmed='N'
			";
		//and a.date>='2009-12-06' and a.date<='2009-12-06'
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getPPMQtyRecs($dppEId)
	{
		$qry = " select id, preprocess_qty, select_commission, select_rate, actual_amount, paid, settlement_date, preprocessor_id from t_dailypreprocess_processor_qty c where dailypreprocess_entry_id='$dppEId'";
		//echo "<br>$qry<br>";
		//$rec	= $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?array($rec[0], $rec[1], $rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7]):array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	

	#Update t_dailypreprocess_entries table 
	function updatePPQtyRec($preProcessorQtyId, $actualAmount)
	{
		$qry	= "update t_dailypreprocess_processor_qty set actual_amount='$actualAmount' where id='$preProcessorQtyId'";		
		$result	=	$this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatePPEntryRec($preProcessorEntryId, $actualYield, $diffYield, $availableQty, $totalQty) 
	{
		$qry = "update t_dailypreprocess_entries set actual_yield='$actualYield', diff_yield='$diffYield', available_qty='$availableQty', total_qty='$totalQty' where id='$preProcessorEntryId'";
		$result	= $this->databaseConnect->updateRecord($qry);
		//echo $qry."<br>";
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	

	function getPreProcessList()
	{
		$qry = " select id, rate, commi, criteria, rate_list_id, flag from m_process";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Check Unique Records
	function ppDefaultRateExist($processMainId)
	{
		$qry	= "select id from m_process_pre_processor where pre_processor_id=0 and process_id='$processMainId'";
		if ($exceptionEntryId) $qry .= " and id!='$exceptionEntryId'"; 
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true,$exceptionId):array(false,'');
	}

	#Add Processor Exception
	function addProcessorExmpt($selPreProcessor, $processMainId, $rate, $commission, $criteria)
	{
		$qry="insert into m_process_pre_processor (pre_processor_id, process_id, rate, commission, criteria) values('$selPreProcessor', '$processMainId', '$rate', '$commission', '$criteria')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	#Update Exception Processor Rec
	function updateProcessorExmpt($exceptionEntryId, $processRate, $processCommission, $processCriteria)
	{	
		$qry = " update m_process_pre_processor set  rate='$processRate', commission='$processCommission', criteria='$processCriteria' where id=$exceptionEntryId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	# --------------------------------------

	# Change Table Engine
	function changeTableType($tblType)
	{		
		$tables = $this->databaseConnect->getRecords("SHOW TABLE STATUS");
		foreach($tables as $table){
			// table Name = $table[0] & Engine = table[1]	//innodb// InnoDB	
			echo "converting $table[1] = {$table[0]} <br>";
			if ($table[1] != "MyISAM" && $tblType=="") {
				echo "converting {$table[0]} <br>";
				$updatedTble = $this->databaseConnect->updateRecord("ALTER TABLE {$table[0]} type = MyISAM");
				if ($updatedTble) echo "<b>updated</b><br>";
				else "<b>Failed to Updated</b><br>";
			} else if ($tblType!="" && $table[1]!=$tblType) {
				echo "converting {$table[0]} <br>";
				$updatedTble = $this->databaseConnect->updateRecord("ALTER TABLE {$table[0]} type = $tblType");
				if ($updatedTble) echo "<b>$tblType::updated</b><br>";
				else "<b>$tblType::Failed to Updated</b><br>";
			}
		}
	}


	# Year updation in So
	function getSOInvoiceRecords()
	{
		$qry = " select id, invoice_date from t_salesorder";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function uptdSOInvoice($soinvId, $soYear)
	{
		$qry = " update t_salesorder set so_year='$soYear' where id='$soinvId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# --------------------


	# Product Status Updation ----------------- starts here
	function getProductStatusRecs()
	{
		$qry = " select a.id, a.product_id, a.distributor_id, a.state_id, b.name, b.id from m_product_status a left join m_distributor b on a.distributor_id=b.id";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}	

	function getDistMgnCityId($productId, $distributorId, $stateId, $selRateListId)
	{
		$qry = "select b.city_id from m_distributor_margin a, m_distributor_margin_state b where a.id=b.distributor_margin_id and a.distributor_id='$distributorId' and a.product_id='$productId' and a.rate_list_id='$selRateListId' and b.state_id='$stateId' ";
		//echo "$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}

	function getDistCity($distributorId, $stateId)
	{
		$qry = "select c.city_id from m_distributor a, m_distributor_state b, m_distributor_city c where a.id=b.distributor_id and b.id=c.dist_state_entry_id and a.id='$distributorId' and b.state_id='$stateId'";
		//echo "$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}

	function delPSRec($psId)
	{
		$qry =	" delete from m_product_status where id=$psId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updatePSRec($psId, $selCity)
	{
		$qry = " update m_product_status set city_id='$selCity' where id='$psId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	# Product Status Updation Ends here -----------------------	

	
	function getAreaDemarcationRecs()
	{
		$qry = "select a.id, a.zone_id, b.name from m_area_demarcation a, m_zone b where a.zone_id=b.id order by b.name asc";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getADStateRecs($adId)
	{
		$qry = "select id, main_id from m_area_demarcation_state where main_id='$adId' and uptd='N'";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateAdSRec($adsRId, $zoneId)
	{
		$qry = " update m_area_demarcation_state set main_id='$zoneId', uptd='Y' where id='$adsRId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;		
	}

	function updateADState($adId, $zoneId)
	{
		$qry = " update m_area_demarcation_state set main_id='$zoneId' where main_id='$adId'";
		//echo "<br>$qry";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function addColumnInADStateTable()
	{
		$qry = " ALTER TABLE  `m_area_demarcation_state` ADD COLUMN `uptd` ENUM('Y','N')  DEFAULT 'N' COMMENT 'temp' AFTER `state_id`";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function removeColumnInADStateTable()
	{
		$qry = " ALTER TABLE  `m_area_demarcation_state` DROP COLUMN `uptd`";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function renameADOldTable()
	{
		$qry = " ALTER TABLE `m_area_demarcation` RENAME TO `m_area_demarcation_r`";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	

	# ---------------------------------------------

	function uptdSORec($invoiceId, $insInvoiceDate)
	{
		$qry = " update t_salesorder set entry_date='$insInvoiceDate' where id='$invoiceId'";
		//$qry
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}	


	function getSalesOrderRecs()
	{
		$qry = " select id, dispatch_date, transporter_rate_list_id, trans_oc_rate_list_id, transporter_id, so from t_salesorder where complete_status='C' ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateSORLRec($soId, $cTRMRateListId, $cTOCRateListId)
	{	
		$qry = " update t_salesorder set transporter_rate_list_id='$cTRMRateListId', trans_oc_rate_list_id='$cTOCRateListId' where id='$soId'";
		//$qry
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# ~~~~~~~~~~~~~~~~~~~~~~~ SO STARTS HERE	
	function delProductStatus($pstatusId)
	{
		$qry =	" delete from m_product_status where id=$pstatusId";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Checking Product MRP using in any where
	function chkSOProductUsed($selProductId, $selStateId)
	{
		$qry = " select a.id from t_salesorder a, t_salesorder_entry b  where a.id=b.salesorder_id and b.product_id='$selProductId' and a.state_id='$selStateId'  ";
		//echo $qry."<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkProductStatusUsed($selProductId, $selStateId)
	{
		$qry = " select a.id from m_product_status a  where a.product_id='$selProductId' and a.state_id='$selStateId'  ";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array(true, $result[0][0]):false;
	}

	function getDistMarginProductRecords()
	{
		$qry = "select a.id, b.id, b.avg_margin, a.product_id, p.id, p.name, b.state_id from (m_distributor_margin a, m_distributor_margin_state b) left join m_product_manage p on a.product_id=p.id where a.id=b.distributor_margin_id and p.id is null";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ WENS

	function updateMainSORec()
	{		
		$qry = " update t_salesorder set proforma_no=0, proforma_date=0, sample_invoice_no=0, sample_invoice_date=0";
		//$qry
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateSORec($invoiceId, $invoiceNo, $siType, $invoiceDate)
	{
		$uptdQry = "";
		if ($siType=='T') $uptdQry =  "proforma_no='$invoiceNo', proforma_date='$invoiceDate'";
		else if ($siType=='S') $uptdQry =  "sample_invoice_no='$invoiceNo', sample_invoice_date='$invoiceDate'";
		
		$qry = " update t_salesorder set $uptdQry where id=$invoiceId";
		echo "<br>UPdate=====>$invoiceId, $invoiceNo, $siType, $invoiceDate";
		//$qry
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getNextProformaNo()
	{
		$qry = " select (max(proforma_no)+1) from t_salesorder  ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];	
	}

	function getNextSampleNo()
	{
		$qry = " select (max(sample_invoice_no)+1) from t_salesorder ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];	
	}

	function getAllSORecs()
	{
		$qry = " select id, invoice_type, so, proforma_no, sample_invoice_no, created_on, invoice_date from t_salesorder order by created_on asc, id asc ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;	
	}
	# ~~~~~~~~~~~~~~~~~~~~~~~ SO ENDS HERE
	
	function uptdDistributorAccount($distAccountId, $description)
	{
		$qry = " update t_distributor_ac set description='".trim($description)."' where id=$distAccountId";
		//$qry
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function getDistAccountRecs()
	{
		$qry = "select a.id, a.select_date, a.distributor_id, a.amount, a.cod, a.description, a.so_id from t_distributor_ac a order by a.select_date desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;		
	}

	function getSORec($soId)
	{
		$qry = " select id, ROUND((grand_total_amt+round_value),2), invoice_type, so from t_salesorder where id='$soId' and complete_status='C' and invoice_type='T' ";	
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0], $rec[1], $rec[2], $rec[3]);
	}

	# ```````````````````

	function getDailyRates()
	{
		$qry = " select id, grade_id, marketrate, decrate, count from t_dailyrates";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function insertRec($mainId, $gradeId, $mktRate, $declRate, $countAvg)
	{
		$qry	= " insert into t_dailyrates_entry (main_id, grade_id, count_avg, market_rate, decl_rate) values('$mainId', '$gradeId', '$countAvg', '$mktRate', '$declRate')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else			 $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function updateDailyRatesTable()
	{		
		$qry = " ALTER TABLE t_dailyrates CHANGE COLUMN `grade_id` `grade_id_R` INT(3)  DEFAULT NULL, CHANGE COLUMN `marketrate` `marketrate_R` float(5,2) default '0.00', CHANGE COLUMN `decrate` `decrate_R` float(5,2) default '0.00', CHANGE COLUMN `count` `count_R` INT(3)  DEFAULT NULL ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	/*
	function updateDailyCatchMainTable()
	{
		$qry = " ALTER TABLE `t_dailycatch_main` ADD COLUMN `sub_supplier` int(5), ADD COLUMN `supplier_challan_no` varchar(20) ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	function getMainTableRecords()
	{
		// where payment_by='D' and weighment_challan_no is not null
		$qry 	= " select id, main_supplier, sub_supplier_R from t_dailycatch_main " ;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getEntryRecords($mainId)
	{
		$qry = " select distinct sub_supplier, supplier_challan_no from t_dailycatchentry where main_id='$mainId' ";

		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?array($result[0],$result[1]):array();
	}

	
	function updateMainRec($mainId, $subSupplierId, $supplierChallanNo)
	{
		$qry = " update t_dailycatch_main set sub_supplier='$subSupplierId', supplier_challan_no='$supplierChallanNo' where id=$mainId";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
		
	}


	function getDeclaredRecs()
	{		
		$qry = " select a.id, b.id, a.sub_supplier from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id where a.payment_by='D' and a.weighment_challan_no is not null "; 

		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateDeclaredRec($entryId, $selSubSupplierId)
	{
		$qry = " update t_dailycatch_declared set sub_supplier='$selSubSupplierId' where entry_id=$entryId ";
		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	

	}

	function updateDailyCatchEntryTable()
	{
		$qry = " ALTER TABLE t_dailycatchentry CHANGE COLUMN `sub_supplier` `sub_supplier_R` INT(5)  DEFAULT NULL, CHANGE COLUMN `supplier_challan_no` `supplier_challan_no_R` VARCHAR(20)  CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	*/

	/*
	function getFunctionTableRecords()
	{
		$qry = "select id from function order by id asc";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateFunctionTable($functionId, $orderId)
	{
		$qry = " update function set menu_order=$orderId where id=$functionId";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	*/


	/*
	function getDailyCatchEntryRecords()
	{
		$qry = " select a.id, a.sub_supplier, a.supplier_challan_no, b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id order by a.weighment_challan_no desc ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateCatchEntryRec($entyId, $subSupplier, $supplierChallanNo)
	{
		$qry = " update t_dailycatchentry set sub_supplier='$subSupplier', supplier_challan_no='$supplierChallanNo' where id=$entyId";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateMainTable()
	{
		$qry = " ALTER TABLE `t_dailycatch_main` CHANGE COLUMN `sub_supplier` `sub_supplier_R` INT(5)  DEFAULT NULL, CHANGE COLUMN `supplier_challan_no` `supplier_challan_no_R` VARCHAR(20)  CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL ";
		//echo $qry."<br>";
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	*/

		
	
}