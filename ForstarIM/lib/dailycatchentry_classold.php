<?php
class DailyCatchEntry
{
	/****************************************************************
	This class deals with all the operations relating to Daily Catch Entry
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyCatchEntry(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert one blank record in Main Table
	function addTempMaster($currentUserId)
	{
		$qry	=	"insert into t_dailycatch_main (flag, entry_date, select_date, createdby) values('0', Now(), Now(), '$currentUserId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	#Insert One Blan Record in t_dailycatch_Entry Table
	function addTempRecDailyCatchEntry($lastId)
	{
		$qry	= "insert into t_dailycatchentry (main_id,select_date) values('$lastId',Now())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}


	#Add Gross Wt in Count Details Iframe
	function addGrossWt($grossWt, $basketWt, $entryId)
	{
		//$grossWt=100;
		$qry	= "insert into t_dailycatchitem (gross,basket,entry_id) values('".$grossWt."','".$basketWt."','".$entryId."')";
		//echo "<br>$qry";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	#paging in Count Details Iframe
	function fetchAllPagingRecords($entryId, $offset, $limit)
	{
		$qry = "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc limit $offset, $limit ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getCountDetails($entryId)
	{
		$qry = "select sum(gross), sum(basket), (sum(gross)-sum(basket)) as netwt from t_dailycatchitem where entry_id='".$entryId."' group by entry_id";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}
	
	//getting all records based on Entry Id
	function fetchAllGrossRecords($entryId)
	{
		$qry	= "select id, gross, basket from t_dailycatchitem where entry_id='".$entryId."' order by id asc";
		//echo "<br>$qry<br>";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Insert Quality (Quality Iframe)
	function addQuality($currentId, $quality, $percentage)
	{
		$qry	=	"insert into t_qualityentry (quality,percent,entry_id) values('".$quality."','".$percentage."','".$currentId."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Select all Quality records Based on Current Entry Id
	function fetchAllQualityRecords($currentId)
	{
		//$qry	= "select id, quality, percent from t_qualityentry where entry_id='".$currentId."'";
		$qry	= "select tqe.id, tqe.quality, tqe.percent, mq.name from t_qualityentry tqe left join m_quality mq on mq.id=tqe.quality where entry_id='".$currentId."' order by mq.name asc";
				
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	#Update quality 
	function updateQuality($qualityId, $percentage, $currentId)
	{
		$qry	=	" update t_qualityentry set percent='$percentage' where id=$qualityId and entry_id=$currentId";
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Delete a Quality
	function deleteQuality($qualityId)
	{
		$qry	=	" delete from t_qualityentry where id=$qualityId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Update Basket Weight
	function updateBasketWt($resetBasketWt, $entryId)
	{
		$qry	=	" update t_dailycatchitem set basket='$resetBasketWt' where entry_id=$entryId";
	
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Update Gross Entry Wt
	function updateGrossWt($grossId, $grossWt, $basketWt, $entryId)
	{
		$qry	=	" update t_dailycatchitem set gross='$grossWt', basket='$basketWt' where id=$grossId and entry_id=$entryId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	
	# Delete Gross Entry Weight	
	function deleteGrossEntryWt($grossId)
	{
		$qry	=	" delete from t_dailycatchitem where id=$grossId ";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		return $result;
	}

	# Insert Rec in daily catch entry table 	
	function addDailyCatch($fish, $processCode, $ice, $count,$countAverage, $entryLocal, $entryWastage, $entrySoft, $reasonAdjust, $entryAdjust, $goodPack, $peeling, $entryRemark, $entryActualWt, $entryEffectiveWt, $entryTotalGrossWt, $entryTotalBasketWt,$entryGrossNetWt, $declWeight, $declCount, $gradeId, $basketWt, $reasonLocal, $reasonWastage, $reasonSoft, $entryOption, $selectDate, $entryId,$catchEntryNewId, $gradeCountAdj, $gradeCountAdjReason, $receivedBy, $noBilling)
	{
		$qry	= "update t_dailycatchentry set fish='$fish', fish_code='$processCode', ice_wt='$ice', count_values='$count', average='$countAverage', local_quantity='$entryLocal', wastage='$entryWastage', soft='$entrySoft', reason='$reasonAdjust', adjust='$entryAdjust', good='$goodPack', peeling='$peeling', remarks='$entryRemark', actual_wt='$entryActualWt', effective_wt='$entryEffectiveWt', gross='$entryTotalGrossWt', total_basket='$entryTotalBasketWt', net_wt='$entryGrossNetWt', decl_wt='$declWeight', decl_count='$declCount', select_date='$selectDate', grade_id='$gradeId', basket_wt='$basketWt', reason_local='$reasonLocal', reason_wastage='$reasonWastage', reason_soft='$reasonSoft', entry_option='$entryOption', grade_count_adj='$gradeCountAdj', grade_count_adj_reason='$gradeCountAdjReason', received_by='$receivedBy', no_billing='$noBilling' where id='$catchEntryNewId' and main_id='$entryId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Update t_dailycatch_main (When ever press the update button Confirm, Payment_confirm, report_confirm-> field values will clear in this section) 	
	function updateCurrentDailyCatch($unit, $landingCenter, $mainSupplier, $vechicleNo, $weighChallanNo, $selectDate, $selectTime, $entryId, $paymentBy, $subSupplier, $supplyChallanNo, $billingCompany, $alphaCode)
	{
		$qry	= " update t_dailycatch_main set unit='$unit', entry_date=Now(), vechile_no='$vechicleNo',  weighment_challan_no='$weighChallanNo', landing_center='$landingCenter', main_supplier='$mainSupplier', select_date='$selectDate', select_time='$selectTime', flag='1', payment_by='$paymentBy', confirm=0, payment_confirm='N', report_confirm='N', sub_supplier='$subSupplier', supplier_challan_no='$supplyChallanNo', billing_company_id='$billingCompany', alpha_code='$alphaCode' where id='$entryId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);		
		if ($result)	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;	
	}


	#Select all daily catch entry details
	function fetchAllDailyRecords()
	{
		$qry	= "select a.id, a.weighment_challan_no, a.entry_date, a.flag,b.count_values, b.grade_id,b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select all daily catch entry for Add New Raw material //Edited on 08-08-07 
	function fetchAllRawMaterialDailyRecords($catchEntryMainId)
	{	
		$qry	= "select a.id, a.entry_date, a.weighment_challan_no, b.fish, b.fish_code, c.id, c.name, c.code, d.id, d.code, b.id, b.received_by, b.count_values, b.grade_id from t_dailycatch_main a, t_dailycatchentry b, m_fish c, m_processcode d where c.id=b.fish and d.id=b.fish_code and a.id=b.main_id and a.id='$catchEntryMainId' order by a.weighment_challan_no desc ";

		/*
		$qry	= "select a.id, a.entry_date, a.weighment_challan_no, b.fish, b.fish_code, c.id, c.name, c.code, d.id, d.code, b.id, b.received_by, b.count_values, b.grade_id from t_dailycatch_main a join t_dailycatchentry b on a.id=b.main_id left join m_fish c on c.id=b.fish left join m_processcode d on d.id=b.fish_code where a.id='$catchEntryMainId' order by a.weighment_challan_no desc ";
		*/
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Select date wise list
	function dailyCatchEntryRecFilter($recordsDate)
	{
		$qry	=	"select a.id, a.weighment_challan_no, a.entry_date, a.flag,b.count_values, b.grade_id,b.id,b.fish_code, b.effective_wt,a.confirm from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id where a.select_date='$recordsDate' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#slect date from recod
	function fetchAllDateRecords()
	{
		$qry	= "select distinct entry_date from t_dailycatchentry";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get daily catch entry based on id 
	function find($catchEntryId, $dailyCatchentryId)
	{
		$qry	= "select a.id, a.unit, a.entry_date, a.vechile_no, a.supplier_challan_no, a.weighment_challan_no, a.landing_center, a.main_supplier, a.sub_supplier, b.fish, b.fish_code, b.ice_wt, b.count_values, b.average, b.local_quantity, b.wastage, b.soft, b.reason, b.adjust, b.good, b.peeling, b.remarks, b.actual_wt, b.effective_wt, b.gross, b.total_basket, b.net_wt, b.decl_wt, b.decl_count, a.select_date, b.grade_id, b.basket_wt, b.reason_local, b.reason_wastage, b.reason_soft,b.entry_option, b.id, a.select_time,a.payment_by, b.grade_count_adj, b.grade_count_adj_reason, a.billing_company_id, a.alpha_code, b.no_billing from t_dailycatch_main a, t_dailycatchentry b  where a.id='$catchEntryId' and a.id=b.main_id and b.id='$dailyCatchentryId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Get daily catch main entry based on id 
	function findDailyCatchMainRec($catchEntryId)
	{
		$qry	= "select a.id, a.unit, a.entry_date, a.vechile_no, a.weighment_challan_no, a.landing_center, a.main_supplier, a.select_date, a.select_time, a.payment_by, a.sub_supplier, a.supplier_challan_no, a.billing_company_id, a.alpha_code from t_dailycatch_main a where a.id=$catchEntryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Daily Catch Entry Sub Supplier Details
	function getDailyCatchEntryRec($catchEntryId)
	{
		$qry = " select distinct sub_supplier, supplier_challan_no from t_dailycatchentry where main_id='$catchEntryId' and fish!='' ";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?array():array($result[0][0],$result[0][1]);
	}

	#Delete Rec from t_dailycatch_main Table based on Id
	function delEntryMainId($entryId)
	{
		$qry	=	" delete from t_dailycatch_main where id=$entryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete Rec from t_dailycatchentry Table based on entry Id
	function delLastInsertId($dailyCatchEntryId)
	{
		$qry	=	" delete from t_dailycatchentry where id=$dailyCatchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Delete a Catch Entry
	function deleteDailyCatchEntryWt($catchEntryId)
	{
		$qry	=	"delete from t_dailycatchentry where id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete GrossWt
	function deleteDailyCatchEntryGrossWt($catchEntryId)
	{
		$qry	=	"delete from t_dailycatchitem where entry_id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Delete Quality Wt
	function deleteDailyCatchEntryQualityWt($catchEntryId)
	{
		$qry	=	"delete from t_qualityentry where entry_id=$catchEntryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	###########

	#For finding Grade based on count average
	function fetchAllGradeRecords($recordAverage)
	{
		$qry	= "select a.id,a.code from m_grade a where '$recordAverage'<= a.max and '$recordAverage' >=a.min";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Check Record Exist of Main Id
	function checkRecordsExist($catchMainId)
	{
		$qry	= "select b.main_id from t_dailycatch_main a, t_dailycatchentry b  where  a.id=b.main_id and b.main_id='$catchMainId' ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Select Date Range wise pagination Records
	function filterCatchEntryPagingRecords($fromDate, $tillDate, $selRecord, $offset, $limit, $supplierId, $selFish, $selProcesscode, $fBillingCompany) 
	{
		$whr	= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'" ;
		
		if ($selRecord=='I') $whr .= " and a.weighment_challan_no is null";
		else if ($selRecord=='C') $whr .= " and a.weighment_challan_no is not null";
		else if ($selRecord=='Z') $whr .= " and b.fish is null and b.fish_code is null ";
		else $whr	.=	"";
		
		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($selFish=="") $whr .= "";
		else $whr .= " and b.fish=$selFish";

		if ($selProcesscode=="") $whr .= "";
		else $whr .= " and b.fish_code=$selProcesscode";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";
		
		$orderBy	= "a.weighment_challan_no desc";
		
		$limit		= " ".$offset.", ".$limit."";
		
		$qry		= "select a.id, a.weighment_challan_no, a.entry_date, a.flag, b.count_values, b.grade_id, b.id, b.fish_code, b.effective_wt, a.confirm, b.received_by, b.grade_count_adj, b.grade_count_adj_reason, a.main_supplier, a.select_date, a.payment_confirm, b.paid, b.average, a.alpha_code from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
		if ($limit!="") $qry   .=" limit ".$limit;
				
		//echo $qry."<br>";	
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	## Select Date Range wise list
	function filterDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $supplierId, $selFish, $selProcesscode, $fBillingCompany)
	{
		$whr = "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'" ;
		
		if ($selRecord=='I') $whr .= "and a.weighment_challan_no is null";
		else if ($selRecord=='C') $whr .= "and a.weighment_challan_no is not null";
		else if ($selRecord=='Z') $whr .= " and b.fish is null and b.fish_code is null ";
		else $whr .= "";
		
		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($selFish=="") $whr .= "";
		else $whr .= " and b.fish=$selFish";

		if ($selProcesscode=="") $whr .= "";
		else $whr .= " and b.fish_code=$selProcesscode";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";

		$orderBy	= "a.weighment_challan_no desc";
		
		$qry		= "select a.id, a.weighment_challan_no, a.entry_date, a.flag, b.count_values, b.grade_id,b.id,b.fish_code, b.effective_wt,a.confirm, b.received_by, b.grade_count_adj, b.grade_count_adj_reason, a.main_supplier, a.select_date, a.payment_confirm, b.paid, b.average, a.alpha_code from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id";
		
		if ($whr!="") $qry   .=" where ".$whr;
		if ($orderBy!="") $qry   .=" order by ".$orderBy;
						
		//echo $qry;		
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	## Select Distinct Records based on date range wise Date Range wise list
	function filterDistinctDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $supplierId, $fBillingCompany)
	{
		$whr = " a.id=b.main_id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.flag=1 " ;
		if ($selRecord=='I') {
			$whr	.=	"and a.weighment_challan_no is null";
		} else if ($selRecord=='C') {
			$whr	.=	"and a.weighment_challan_no is not null";
		} else if ($selRecord=='Z') {
			$whr	.=	" and b.fish is null and b.fish_code is null ";
		} else {
			$whr	.=	"";
		}

		if ($supplierId=="") $whr .= "";
		else $whr .= " and a.main_supplier=$supplierId";

		if ($fBillingCompany) $whr .= " and a.billing_company_id='$fBillingCompany'";

		$orderBy	=	"a.weighment_challan_no desc";
		
		$qry		=	"select distinct a.id, a.weighment_challan_no, a.alpha_code from t_dailycatch_main a, t_dailycatchentry b";
		if ($whr!="") $qry   .= " where ".$whr;
		if ($orderBy!="") $qry   .= " order by ".$orderBy;
				
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For finding the Main Id based on Entry Id
	function getMainId($entryId)
	{
		$qry	=	"select main_id from t_dailycatchentry where id=$entryId";
		//echo "qry=$qry<br>";
		$result	=	$this->databaseConnect->getRecords($qry);	
		return $result[0][0];
	}

	#Check any entry against main Id 
	function moreEntriesExist($mainId)
	{
		$qry	=	"select id from t_dailycatchentry where main_id=$mainId";	
		$result	=	$this->databaseConnect->getRecords($qry);		
		return  ( sizeof($result) > 0 ) ? true : false ;
	}

	#Check Blank Record Exist
	function checkBlankRecord($currentUserId)
	{
		$qry = "select a.id, a.weighment_challan_no, b.id from t_dailycatch_main a left join t_dailycatchentry b on a.id=b.main_id where a.createdby='$currentUserId' and a.weighment_challan_no is null order by a.id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return 	(sizeof($result)>0)?array( $result[0], $result[2]):false;	
	}

	//--------------------------- Declared Section -----------------------------
	#Insert Declared Details
	function addDeclaredDetails($currentId, $supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier)
	{
		$qry	= "insert into t_dailycatch_declared (entry_id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier) values('".$currentId."', '".$supplierChallanNo."', '".$supplierChallanDate."', '".$declWeight."', '".$declCount."', '".$declIce."', '$subSupplier' )";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus)	$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	#Select all records from Declared Record Table
	function fetchAllDeclaredRecords($currentId)
	{
		$qry	= "select id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier from t_dailycatch_declared where entry_id='".$currentId."' order by supplier_challan_no asc" ;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Update Declared Entry
	function updateDeclaredRecord($declaredRecordId, $supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier)
	{
		$qry	= " update t_dailycatch_declared set supplier_challan_no='$supplierChallanNo', supplier_challan_date='$supplierChallanDate', decl_wt='$declWeight', decl_count='$declCount', decl_ice='$declIce', sub_supplier='$subSupplier' where id=$declaredRecordId";
		$result		=	$this->databaseConnect->updateRecord($qry);
		if ($result)		$this->databaseConnect->commit();
		else			$this->databaseConnect->rollback();		
		return $result;	
	}

	#Find Declared Rec based on Edit Id
	function findDeclaredRec($editId)
	{
		$qry	= "select id, supplier_challan_no, supplier_challan_date, decl_wt, decl_count, decl_ice, sub_supplier from t_dailycatch_declared where id='".$editId."'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Delete a Declared Record
	function deleteDeclaredRec($declaredId)
	{
		$qry	= " delete from t_dailycatch_declared where id=$declaredId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	//---------------------------------------------
	#Checking the main Id is Updated or not
	function checkEntryUpdatedRecord($catchMainId)
	{
		$qry	=	"select b.main_id from t_dailycatch_main a, t_dailycatchentry b  where  a.id=b.main_id and b.main_id='$catchMainId' and (a.payment_confirm='Y' or b.paid='Y')";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return sizeof($result)>0?true:false;
	}

	# Delete a Catch Entry Declared Record (When Press Add New in Daily Catch Entry Screen)
	function deleteCatchEntryDeclaredRec($catchEntryNewId)
	{
		$qry	=	" delete from t_dailycatch_declared where entry_id=$catchEntryNewId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# ---------------- For AJAX Search -------------------
	#Select distinct Supplier
	function getSupplierList($fromDate, $tillDate, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr	=	"a.main_supplier=b.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";
		
		$orderBy = "b.name asc";		
		$qry	= "select distinct a.main_supplier, b.id, b.name from t_dailycatch_main a, supplier b $tableName";
		
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
		//echo "<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[2];
		}
		return $resultArr;
	}

	#Fish Records for a date range	
	function getFishList($fromDate, $tillDate, $selectSupplier, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr = "b.fish=c.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null ";
	
		if 	($selectSupplier==0) $whr .="";
		else	$whr .=" and a.main_supplier=".$selectSupplier;

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";
		
		$orderBy	= "c.name asc";
	
		$qry = "select distinct b.fish,c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;
	}

	#Process Code Records
	function getProcessCodeList($fromDate, $tillDate, $selectSupplier, $fishId, $billingCompanyId)
	{
		$Date1			=	explode("/",$fromDate);
		$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

		$Date2			=	explode("/",$tillDate);
		$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

		$whr = "b.fish_code=c.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."'";
	
		if ($selectSupplier==0) $whr .="";
		else $whr .=" and a.main_supplier=".$selectSupplier;

		if ($billingCompanyId!="") $whr .= " and a.billing_company_id='$billingCompanyId' ";		
		$orderBy	=	"c.code asc";	

		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;	
	}

	#Same Entry Exist
	function checkEntryExist($challanId, $fishId, $processCodeId, $count, $gradeId)
	{
		
		$whr = "main_id='".$challanId."' and fish='".$fishId."' and fish_code='".$processCodeId."'";
	
		if ($count=="") $whr .="";
		else $whr .=" and count_values= '".$count."'";

		if ($gradeId=="") $whr .="";
		else $whr .=" and grade_id= '".$gradeId."'";
		
		$orderBy	=	"";
	
		$qry = "select id from t_dailycatchentry";
	
		if ($whr!="") $qry .= " where ".$whr;
		
		if ($orderBy!="") $qry .= " order by ".$orderBy;
			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?true:false;	
	}

	#Same Count Average Entry Exist
	function checkSameCountAverage($challanId, $fishId, $processCodeId, $countAverage)
	{	
		$whr = "main_id='".$challanId."' and fish='".$fishId."' and fish_code='".$processCodeId."' and average='".$countAverage."' ";
	
		$qry = "select id from t_dailycatchentry";	
		if ($whr!="") $qry .= " where ".$whr; 
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		return (sizeof($result)>0)?true:false;	
	}

	# ---------------- For AJAX Search Ends Here-------------------	

	#-----------------For Modify Challan Ajax-----------------------
	#Fish Records for a Challan No
	function getRMChallanWiseFishList($challanNo, $billingCompany)
	{
		$whr = "b.fish=c.id and a.weighment_challan_no='$challanNo' and a.id=b.main_id and a.weighment_challan_no is not null and a.billing_company_id='$billingCompany' ";
		
		$orderBy	=	"c.name asc";
	
		$qry = "select distinct b.fish,c.name from t_dailycatch_main a, t_dailycatchentry b, m_fish c $tableName";
	
		if($whr!="") $qry .= " where ".$whr;
		
		if($orderBy!="") $qry .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get Challan Wise Process Code
	function getRMChallanWiseProcessCodeList($challanNo, $fishId, $billingCompany)
	{
		$whr = "b.fish_code=c.id and a.weighment_challan_no='$challanNo' and a.id=b.main_id and a.weighment_challan_no is not null and b.fish='".$fishId."' and a.billing_company_id='$billingCompany' ";	
				
		$orderBy	=	"c.code asc";
	
		$qry = "select distinct b.fish_code,c.code from t_dailycatch_main a, t_dailycatchentry b, m_processcode c $tableName";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}

		return $resultArr;	
	}

	function getRMEntryRecords($challanNo, $fishId, $processCodeId, $billingCompany)
	{
		$whr = " a.weighment_challan_no='$challanNo' and a.id=b.main_id and b.fish='".$fishId."' and b.fish_code='$processCodeId' and a.billing_company_id='$billingCompany' ";	
				
		$orderBy	= "b.count_values asc,c.code asc";
	
		$qry = "select b.id, b.grade_id, c.code, b.count_values, b.effective_wt from (t_dailycatch_main a, t_dailycatchentry b) left join m_grade c on b.grade_id=c.id";
	
		if ($whr!="") $qry .= " where ".$whr;		
		if ($orderBy!="") $qry .= " order by ".$orderBy;			
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	# ----------------  Modify Challan Ajax Ends-------------------	
	# For Modification (Return Main ID & Entry ID)
	function findRMEntryRec($rMEntryId)
	{	
		$qry = "select a.id, b.id from t_dailycatch_main a, t_dailycatchentry b  where a.id=b.main_id and b.id='$rMEntryId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);		
		return (sizeof($rec)>0)?array($rec[0], $rec[1]):"";
	}
	

	# Update Modified Rec
	function updateModifiedRMChallanEntryRec($challanMainId, $challanEntryId, $entryAdjust, $reasonAdjust, $gradeCountAdj, $gradeCountAdjReason, $entryActualWt, $entryLocal, $reasonLocal, $entryWastage, $reasonWastage, $entrySoft, $reasonSoft, $currentUserId)
	{
		$qry = "update t_dailycatchentry set adjust='$entryAdjust', reason='$reasonAdjust', grade_count_adj='$gradeCountAdj', grade_count_adj_reason='$gradeCountAdjReason', actual_wt='$entryActualWt', local_quantity='$entryLocal', reason_local='$reasonLocal', wastage='$entryWastage', reason_wastage='$reasonWastage', soft='$entrySoft', reason_soft='$reasonSoft', modified=NOW(), modified_by='$currentUserId' where id='$challanEntryId' and main_id='$challanMainId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);		
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Check valid challan num
	function chkValidChallanNum($selDate, $challanNum, $billingCompany)
	{
		$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$challanNum' and end_no>='$challanNum'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}
	# Check Challan Exist
	function chkChallanExist($challanNum, $billingCompany, $cId)
	{
		if ($cId) $uptQry = " and id!=$cId";

		$qry = " select id from t_dailycatch_main where weighment_challan_no='$challanNum' and billing_company_id='$billingCompany' $uptQry";
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	#Billing company Records for a date range	
	function getBillingCompanyList($fromDate, $tillDate, $selectSupplier)
	{
		$whr		= "a.billing_company_id=b.id and a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'";	

		if ($selectSupplier) $whr .= " and a.main_supplier='$selectSupplier' ";
	
		$orderBy	= "b.display_name asc";

		$qry	= "select a.billing_company_id, b.display_name from t_dailycatch_main a, m_billing_company b";
		if ($whr!="") 		$qry	.= " where ".$whr;
		if ($orderBy!="")	$qry	.= " order by ".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'--All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# Get date Wise Supplier
	function getDateWiseSupplier($selDate, $landingCenter)
	{
		$qry = "select tdm.main_supplier, supp.name from t_dailycatch_main tdm, supplier supp where tdm.main_supplier=supp.id and tdm.select_date='$selDate' and tdm.landing_center='$landingCenter' group by tdm.main_supplier order by supp.name asc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Filter Process Code
	function pcRecFilter($fishId)
	{
		$qry	= "select id, code, b_weight from m_processcode where fish_id='$fishId' order by code asc";
		//echo $qry;		
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Grade selection for Raw Grades
 	function gradeRecFilter($codeId)
	{
 		$qry =	"select b.id, b.code from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$codeId' and a.unit_select='r' order by b.code asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 	}

	# Get Recived Type
	function pcReceivedType($processCodeId)
	{
		$qry	= "select available_option from m_processcode where id='$processCodeId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
	}
	
}
?>