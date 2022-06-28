<?php
	require("include/include.php");
	require_once("lib/dailythawing_ajax.php");
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$selection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"];
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) 
	{
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------	
	
	# From Stock Report
	$stockReport = false;
	if ($g["STOCKREPORT"]=='Y') 
	{
		$listRec 	= $_SESSION["listRec"];
		$stkReportPC 	= $_SESSION["stkReportPC"];
		$stkReportPCSize = sizeof($stkReportPC);
		$stkReportGrade = $_SESSION["stkReportGrade"];
		$stkReportGradeSize = sizeof($stkReportGrade);
		$stkTillDate = $_SESSION["stkTillDate"];
		$stockReport = true;
	}
	

	# Add New
	if ($p["cmdAddNew"]!="" || $stockReport)
	{
		$addMode	=	true;
		if ($p["mainId"]==""  && $dailythawingObj->checkBlankRecord($userId)) 
		{
			$mainId		=	$dailythawingObj->checkBlankRecord($userId);
		} 
		else 
		{
			if ($p["mainId"]=="") 
			{
				$tempMainTableRecIns=$dailythawingObj->addTempDataMainTable($userId);
				if ($tempMainTableRecIns!="") 
				{
					$mainId	=	$databaseConnect->getLastInsertedId();
				}
			} 
			else 
			{
				$mainId 	=	$p["mainId"];
			}
		}
	}
	$maxdate=$dailythawingObj->getMaxDate();
	$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
	//$maximumdt= dateFormat($maxdate[0]);
	$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):$defaultDFPDate;
	
	# Add
	if( $p["cmdAdd"]!="")
	{
	
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		//$brand			=	$p["brand"];
		$frozenCode		=	$p["frozenCode"];
		$mCPacking		=	$p["mCPacking"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];
		$customer	= $p["customer"];
		
		if ($fishId!="" && $processCode!="") 
		{
			$updateDailyThawingRec = $dailythawingObj->updateDailyThawingRec($mainId, $fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $selectDate, $brandFrom, $customer);
			if ($updateDailyThawingRec) 
			{
				$sessObj->createSession("displayMsg",$msg_succAddDailyThawing);
				 $sessObj->createSession("nextPage",$url_afterAddDailyThawing.$selection);
			} 
			else 
			{
				$addMode		=	true;
				$err			=	$msg_failAddDailyThawing;
			}
			$updateDailyThawingRec	=	false;
		}
	}


	# Edit 
	if ( $p["editId"]!="" ) 
	{
		$editId			=	$p["editId"];
		$hidrmlotID=$p["hidrmlotID"];	
		//$hidfishID=$p["hidfishID"];	
		$editMode		=	true;
		if($hidrmlotID=='0')
		{
			$dailyThawingRec	=	$dailythawingObj->find($editId);
		}
		else
		{
			$dailyThawingRec	=	$dailythawingObj->findRMLot($editId);
			$rmlotName=$dailyThawingRec[9];
		}
		$mainId			=	$dailyThawingRec[0];
			
		$selDate		=	dateFormat($dailyThawingRec[8]);
		
		if($p["editSelectionChange"]=='1'|| $p["fish"]==""){
			$fishId			=	$dailyThawingRec[1];
		} else {
			$fishId		=	$p["fish"];
		}
		
		if($p["editSelectionChange"]=='1'|| $p["processCode"]==""){
			 $processId	=	$dailyThawingRec[2];
		} else {
			$processId	=	$p["processCode"];
		}
		
		$freezingStage	=	$dailyThawingRec[3];
		$eUCode		=	$dailyThawingRec[4];
		$frozenCode	=	$dailyThawingRec[6];
		$mCPacking	=	$dailyThawingRec[7];
		
		$tillDate = mysqlDateFormat($dateTill);
		$frmDate=mysqlDateFormat($maximumdt);
		if($hidrmlotID=='0')
		{
			$productRecs = $dailythawingObj->getAllocateProductionRecs($frmDate, $tillDate, $processId, $freezingStage, $frozenCode, $mCPacking);
			$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
		}
		else
		{
			$productRecs = $dailythawingObj->getAllocateProductionRMLotIDRecs($frmDate, $tillDate, $processId, $freezingStage, $frozenCode, $mCPacking,$hidrmlotID);
			$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecordsRmLotId($processId, $entryId);
		}
		# grade
		//$gradeRecs = $dailythawingObj->getProductionGradeRecs($frmDate, $tillDate, $processId, $freezingStage, $frozenCode, $mCPacking);
		$selBrd		= $dailyThawingRec[5];
		$selBrdFrom	= $dailyThawingRec[10]; // Company
		$brand	  	= $selBrd."_".$selBrdFrom;
		$customer	= $dailyThawingRec[9];
		if ($frozenCode) $filledWt = $dailythawingObj->frznPkgFilledWt($frozenCode);
		$entrySel = "DE";
		$entrySection = false;	
		if ($mCPacking)$numPacks=$dailythawingObj->getnumMC($mCPacking);
		$editCriteria		= $p["editCriteria"];
		//echo $editCriteria;
		$editCriteriaVal=$p["editCriteriaVal"];
		if($rmlotName)
		$displayEditMsg		= strtoupper($editCriteria).'-'.$rmlotName;
		else
		$displayEditMsg		= strtoupper($editCriteria);
		$i=1;
		if($hidrmlotID=='0')
		{
			$getDate=$dailythawingObj->getEditDate($editId);
		}
		else
		{
			$getDate=$dailythawingObj->getEditDateRMLotid($editId);
		}
		$getdatedt= dateFormat($getDate[0]);
		$dateFrom=$p["hidfromdate"];
	}


	# Update
	if ($p["cmdSaveChange"]!="") 
	{
		$mainId 	=	$p["mainId"];
		$rmlotID=$p["editrmlotID"];	
		$i=1;
		$selectDate		=	mysqlDateFormat($p["editDate"]);
		$gradeRowCount	= $p["hidAllocateGradeRowCount"];
		$fishId			=	$p["fish"];
		$processCode		=	$p["hidProcessId"];
		$freezingStage		=	$p["hidFreezingStage"];
		$eUCode			=	$p["eUCode"];
		//$brand			=	$p["brand"];
		$frozenCode		=	$p["hidFrozenCode"];
		$mCPacking		=	$p["hidMCPkgId"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];
		$customer	= $p["customer"];		
		$dailyThawingMainId=$mainId;
		$editCriteria=$p["editCriteria"];
		//echo $editCriteria;
		$editCriteriaVal=$p["editCriteriaVal"];
		$editCriteriaVal=explode("/",$editCriteriaVal);
		//echo $editCriteriaVal="10-2-134-1";
		$editCriteriaValPrd="$editCriteriaVal[0]";
		$delDate="$editCriteriaVal[1]";
		$editCriteriaValPrdSplt=explode("-",$editCriteriaValPrd);
		$selProcessCodeId=$editCriteriaValPrdSplt[0];
		$selFreezingStageId=$editCriteriaValPrdSplt[1];
		$selFrozenCodeId=$editCriteriaValPrdSplt[2];
		$frznStkMCPkgId=$editCriteriaValPrdSplt[3];
		$eCriteria=explode("-",$editCriteria);
		//print_r($eCriteria);
		$filledWt=$eCriteria[4];
		//echo $filledWt;
		//echo "----";
			$glazeId=$frozenpackingObj->frznPkgglaze($selFrozenCodeId);
			//echo $glazeId;
			//echo ")))";
			$glaze=$glazeObj->findGlazePercentage($glazeId);
			//echo "---**".$glaze;
			$Wt=$filledWt-($filledWt*$glaze/100);
			//echo $Wt;
		
		//die();
		if($rmlotID=='0')
		{
			$thawEntryId=$dailythawingObj->getThadelEntryId($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$delDate);
			//printr($thawEntryId);
			foreach($thawEntryId as $Id)
			{
				$thawEntryIdRow=$dailythawingObj->getThadelGradeId($Id[0]);
				foreach($thawEntryIdRow as $tr)
				{
					$dailyThawingGradeRecDel = $dailythawingObj-> deleteDailyThawingGradeRec($tr[0]);
				}
				$dailyThawingMainRecDel	=	$dailythawingObj->deleteDailyThawingMainRec($tr[1]);
			}
			$selLogRecords=$dailythawingObj->fetchLogRecords($dailyThawingMainId);
			foreach($selLogRecords as $selRe)
			{
				$processcodeidL=$selRe[2];
				$freestidL=$selRe[3];
				$frozidL=$selRe[6];
				$mcpidL=$selRe[7];
				$gradeidL=$selRe[10];
				$numcL=$selRe[9];
				$type="T";
				$thawLog=$dailythawingObj->insDailyfrozenpackingAlloc($processcodeidL,$freestidL,$frozidL,$mcpidL,$gradeidL,$numcL,$purchaseOrderId,$id,$type,$invoiceId,$userId);
			}
			
				$insertDailyThawing = $dailythawingObj->insertDailyThawing($selectDate,$processCode,$freezingStage,$frozenCode,$mCPacking,$fishId);
				if ($insertDailyThawing)
				$dfpPOEntryId = $databaseConnect->getLastInsertedId();
				for ($j=1; $j<=$gradeRowCount; $j++) 
				{
					$gradeId = $p["sGradeId_".$j."_".$i];
					$numMC = $p["numMC_".$j."_".$i];
					$netWt=0;
					if (($gradeId>0) && ($numMC>0 ))
					{
						$netWt=$numMC*$Wt;
						$insertPOGradeRec = $dailythawingObj->insertDFPPOGradeForThawing($dfpPOEntryId, $gradeId, $numMC,$netWt);
											//$updatePOGradeRec = $dailythawingObj->updateDFPPOGradeForThawing($dailyThawingMainId,$gradeId, $numMC,$numLS);
					}
				}
			
			
			}
			else
			{
				$thawEntryId=$dailythawingObj->getThadelEntryIdRMLotid($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$delDate,$rmlotID);
				//print_r($thawEntryId);
				//echo "8888888888888888";
				foreach($thawEntryId as $Id)
				{
					$thawEntryIdRow=$dailythawingObj->getThadelGradeIdRMLotID($Id[0]);
					foreach($thawEntryIdRow as $tr)
					{
						$dailyThawingGradeRecDel = $dailythawingObj-> deleteDailyThawingGradeRMLotIDRec($tr[0]);
					}
					$dailyThawingMainRecDel	=	$dailythawingObj->deleteDailyThawingMainRMLotIDRec($tr[1]);
				}
				
				$selLogRecords=$dailythawingObj->fetchLogRecordsRMLot($dailyThawingMainId);
				foreach($selLogRecords as $selRe)
				{
					$processcodeidL=$selRe[2];
					$freestidL=$selRe[3];
					$frozidL=$selRe[6];
					$mcpidL=$selRe[7];
					$gradeidL=$selRe[10];
					$numcL=$selRe[9];
					$type="T";
					
					//add a column in this table and add rm lot id
					$thawLog=$dailythawingObj->insDailyfrozenpackingAllocRMLotId($processcodeidL,$freestidL,$frozidL,$mcpidL,$gradeidL,$numcL,$purchaseOrderId,$id,$type,$invoiceId,$userId,$rmlotID);
				}
				
				$insertDailyThawing = $dailythawingObj->insertDailyThawingRMLotID($selectDate,$processCode,$freezingStage,$frozenCode,$mCPacking,$rmlotID,$fishId);
				if ($insertDailyThawing)
					$dfpPOEntryId = $databaseConnect->getLastInsertedId();
					for ($j=1; $j<=$gradeRowCount; $j++) 
					{
						$gradeId = $p["sGradeId_".$j."_".$i];
						$numMC = $p["numMC_".$j."_".$i];
						$netWt=0;
						if (($gradeId>0) && ($numMC>0 ))
						{
							$netWt=$numMC*$Wt;
							$insertPOGradeRec = $dailythawingObj->insertDFPPOGradeForThawingRMLotID($dfpPOEntryId, $gradeId, $numMC,$netWt);
							//$updatePOGradeRec = $dailythawingObj->updateDFPPOGradeForThawing($dailyThawingMainId,$gradeId, $numMC,$numLS);
						}
					}
				}
		
				if ($insertPOGradeRec)
				{
					$sessObj->createSession("displayMsg",$msg_succUpdateDailyThawing);
					$sessObj->createSession("nextPage",$url_afterUpdateDailyThawing.$selection);
				}
				$editMode	=	false;
			}

	# Delete 
	if ($p["cmdDelete"]!="") 
	{
		$hidrmlotID=$p["hidrmlotID"];	
		$editCriteriaVal=$p["editCriteriaVal"];	
		$editCriteriaVal=explode("/",$editCriteriaVal);
		//echo $editCriteriaVal="10-2-134-1";
		$editCriteriaValPrd="$editCriteriaVal[0]";
		$delDate="$editCriteriaVal[1]";
		$editCriteriaValPrdSplt=explode("-",$editCriteriaValPrd);
		$selProcessCodeId=$editCriteriaValPrdSplt[0];
		$selFreezingStageId=$editCriteriaValPrdSplt[1];
		$selFrozenCodeId=$editCriteriaValPrdSplt[2];
		$frznStkMCPkgId=$editCriteriaValPrdSplt[3];
		if($hidrmlotID=='0')
		{
			$thawEntryId=$dailythawingObj->getThadelEntryId($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$delDate);
			//print_r($thawEntryId);
			//echo "8888888888888888";
			foreach($thawEntryId as $Id)
			{
				$newLot=$Id[1];
				$thawEntryIdRow=$dailythawingObj->getThadelGradeId($Id[0]);
				foreach($thawEntryIdRow as $tr)
				{
					$dailyThawingGradeRecDel = $dailythawingObj-> deleteDailyThawingGradeRec($tr[0]);
				}
				$dailyThawingMainRecDel	=	$dailythawingObj->deleteDailyThawingMainRec($tr[1]);
				$dailyRmLot	=	$dailythawingObj->deleteRmLot($newLot);
			}
		}
		else
		{
			$thawEntryId=$dailythawingObj->getThadelEntryIdRMLotid($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$delDate,$hidrmlotID);
			//print_r($thawEntryId);
			//echo "8888888888888888";
			foreach($thawEntryId as $Id)
			{
				$thawEntryIdRow=$dailythawingObj->getThadelGradeIdRMLotID($Id[0]);
				foreach($thawEntryIdRow as $tr)
				{
					$dailyThawingGradeRecDel = $dailythawingObj-> deleteDailyThawingGradeRMLotIDRec($tr[0]);
				}
				$dailyThawingMainRecDel	=	$dailythawingObj->deleteDailyThawingMainRMLotIDRec($tr[1]);
			}
		}
	
		if ($dailyThawingMainRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelDailyThawing);
			$sessObj->createSession("nextPage",$url_afterDelDailyThawing.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelDailyThawing;
		}
		$dailyThawingMainRecDel	=	false;
		$editMode		=	false;
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	#List All select Daily Thawing Records
	if ($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!="") 
	{
		$dateFrom = $g["frozenPackingFrom"];
		$dateTill = $g["frozenPackingTill"];
	}
	else if ($p["frozenPackingFrom"]!="" && $p["frozenPackingTill"]!="") 
	{
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];
	} 
	else
	{
		//$dateFrom =
		$maxdate= $dailyfrozenpackingObj->getMaxDate();
		//$currYear=Date("Y");
		//$currFinanYear="01/04/$currYear";
		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):$defaultDFPDate;
		//$dateFrom=$maximumdt;
		$dateFrom=date("1/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	# Search
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) 
	{	
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		$dailyThawingRecords	=	$dailythawingObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$dailyThawingRecordSize	=	sizeof($dailyThawingRecords);
		$numrows	=  sizeof($dailythawingObj->fetchAllRecords($fromDate, $tillDate));
	}
	

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	if ($addMode || $editMode) 
	{
		if ($fishId != "") $processCodeRecords	= $processcodeObj->processCodeRecFilter($fishId);	

		#List All Plants
		$plantRecords	=	$plantandunitObj->fetchAllRecords();
		
		#List All Fishes
		$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();
		
		#List All Freezing Stage Record
		$freezingStageRecords	=	$freezingstageObj->fetchAllRecords();
			
		#List All EU Code Records
		$euCodeRecords		=	$eucodeObj->fetchAllRecords();
				
		#List All Frozen Code Records
		$frozenPackingRecords	=	$frozenpackingObj->fetchAllRecords();
			
		#List All MC Packing Records
		$mcpackingRecords	=	$mcpackingObj->fetchAllRecords();
				
		#List All Customer Records
		$customerRecords	= 	$customerObj->fetchAllRecords();

		#List All Brand Records
		//$brandRecords	= $brandObj->fetchAllRecords();
		$brandRecords = array();
		if ($customer) $brandRecords	= $brandObj->getBrandRecords($customer);
		
		# get all QEL recs
		$fznPkngQuickEntryListRecords = $dailythawingObj->fetchAllQELRecords();
		if ($selQuickEntryList) 
		{
 			# Grade Records
			$qeGradeRecords = $dailythawingObj->qelGradeRecords($selQuickEntryList);
			# Get QEL Selected Process Code Records
			$selProcessCodeRecs = $dailythawingObj->qelProcessCodeRecs($selQuickEntryList);

			$displayQE = "DMCLS";

			# Common Setting
			if ($selQuickEntryList)
			{
				list($qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom) = $dailythawingObj->qelRec($selQuickEntryList);
				$numPacks = "";
				if ($qeMCPackingId!=0) 
				{
					$mcpackingRec	= $mcpackingObj->find($qeMCPackingId);
					$numPacks	= $mcpackingRec[2];
					$qelMCPackingCode = $mcpackingRec[1];
				}
			}
		}

	} // Add Mode/ Edit check ends here
	

	if ($editMode) 	$heading 	= $label_editDailyThawing;
	else 		$heading	= $label_addDailyThawing;

	$ON_LOAD_PRINT_JS	= "libjs/dailythawing.js";
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmDailyThawing" id="frmDailyThawing" action="DailyThawing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
												<td colspan="2" height="10"  ></td>
											</tr>
											<tr>
												<TD colspan="2" align="center" style="padding:10px">
													<table width="200" cellpadding="0" cellspacing="0">
														<tr>
															<TD class="listing-head" nowrap="true">
																PRODUCTION DETAILS OF <?=$displayEditMsg?>
																<?php
																	$leftBdr 	= "border-left: 1px solid #999999;";
																	$rightBdr 	= "border-right: 1px solid #999999;";
																	$topBdr 	= "border-top: 1px solid #999999;";
																	$bottomBdr 	= "border-bottom: 1px solid #999999;";
																	$fullBdr	= "border: 1px solid #999999;";
																?>
															</TD>
														</tr>
														<TR>
															<TD align="center">
																<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">
																	<tr bgcolor="#f2f2f2"  align="center">		
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+4?>">&nbsp;SLABS OF EACH GRADE/COUNT</td>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																	</tr>
																	<tr bgcolor="#f2f2f2"  align="center">
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
																		<?php
																		$g = 1;
																		foreach ($gradeRecs as $gR)
																		{
																			$gId = $gR[0];
																			$gradeCode = $gR[1];
																		?>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																			<?=$gradeCode?>
																			<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																		</td>
																		<?php
																				$g++;
																			}
																		?>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																	</tr>
																	<tr bgcolor="#f2f2f2"  align="center">
																	<?php 
																	if ($thawingStatus==0)
																	{
																	?>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" >
																		Available Qty&nbsp;</td><?php } else{?>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" >
																		Available Qty&nbsp;</td>
																	<?php }?>
																		<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																			<table cellpadding="1" cellspacing="0" width="100%">
																				<tr>
																					<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC</td>
																				</tr>
																			</table>
																		</td>
																		<?php
																		$totAvailableMC = 0;
																		$totAvailableLS = 0;
																		$thaweditDate=mysqldateformat($getdatedt);
																		if($mCPacking)
																		{
																			$mcpackingRec	= $mcpackingObj->find($mCPacking);
																			$numPacks	= $mcpackingRec[2];
																		}
																		$selectDate=mysqlDateFormat($maximumdt);
																		foreach ($gradeRecs as $gR) 
																		{
																			$j++;
																			$sGradeId   = $gR[0];
																			//echo $mCPacking;
																			//echo $hidrmlotID;
																			###commented by athira on 4-11-2014
																			if($hidrmlotID=='0')
																			{
																				list($availableMC, $availableLS) = $dailythawingObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $mCPacking,$selectDate,$tillDate);
																				list($thawingGrdTotal)=$dailythawingObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$thaweditDate,$sGradeId,$editId);
																				list($thawTotaldt)=$dailythawingObj->getThadtGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId);
																				list($thawFreeTotal)=$dailythawingObj->getThaFreeGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$editId);
																			}
																			else
																			{
																				list($availableMC, $availableLS) = $dailythawingObj->getAvailablePacksRMLotID($processId, $freezingStage, $frozenCode, $sGradeId, $mCPacking,$selectDate,$tillDate,$hidrmlotID);
																				list($thawingGrdTotal)=$dailythawingObj->getThaGradeQtyRMLot($processId, $freezingStage, $frozenCode,$mCPacking,$thaweditDate,$sGradeId,$editId,$hidrmlotID);
																				list($thawTotaldt)=$dailythawingObj->getThadtGradeQtyRMLotID($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$hidrmlotID);
																				//echo $hidrmlotID;
																				list($thawFreeTotal)=$dailythawingObj->getThaFreeGradeQtyRMLotID($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$editId,$hidrmlotID);
																			}
																			//$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
																			//list($thawingGrdTotal)=$dailythawingObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$sGradeId);
																			//list($thawingGrdTotal)=$dailythawingObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$thaweditDate,$sGradeId);
																			
																			
																			
																			//$availableNetMC=$availableMC-$thawTotaldt;
																			$availableNetMC=$availableMC;
																			$totAvailableMC += $availableMC;
																			//$totAvailableLS += $availableLS;
																			
																				$numMC=$thawingGrdTotal;
																			?>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																				<table cellpadding="1" cellspacing="0" width="100%">
																					<tr>
																						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																							<input type="hidden" name="tothidAvailableallocSlabs_<?=$j?>" id="tothidAvailableallocSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																							<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" /><?//=$thawTotaldt;?>
																							<b><?=($availableNetMC!=0)?$availableNetMC:"&nbsp;"?></b>
																						</td>
																					</tr>
																				</table>
																			</td>
																			<?php
																			} // Grade Loop Ends here

																			# Total Available Slabs
																			$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																			
																			?>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																				<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																				&nbsp;
																			</td>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																		</tr>
																		<?php
																		$i = 0;
																		$pkgGroupArr = array();
																		$lsPkgGroupArr = array();
																		//foreach ($productRecs as $pr) {
																			$i++;
																			
																			$dFrznPkgEntryId = $pr[0];
																			$frozenLotId 	= $pr[1];
																			$mcPackingId	= $pr[7];
																			
																			$mcPkgCode	= $pr[3];
																			
																			$selProcessCodeId=$pr[7];
																			$selFreezingStageId=$pr[8];
																			$selFrozenCodeId=$pr[9];
																			$frznStkMCPkgId=$pr[10];
																		?>	
																		<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
																			<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																			<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																			<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																			<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="3">
																				<table cellpadding="1" cellspacing="0" width="100%">
																					<tr>
																						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;" >MC </td>
																					</tr>
																				</table>
																			</td>
																			<?php
																			# MC Section
																			$j=0;
																			$totNumMC = 0;
																			$totNumLS = 0;
																			foreach ($gradeRecs as $gR) 
																			{
																				$j++;
																				$sGradeId   = $gR[0];
																				$gradeCode = $gR[1];
																				# Find MC
																				$allocateGradeEntryId = "";
																				
																				if($hidrmlotID=='0')
																				{
																					list($availableMC, $availableLS) = $dailythawingObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $mCPacking,$selectDate,$tillDate);
																					list($thawingGrdTotal)=$dailythawingObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$thaweditDate,$sGradeId,$editId);
																					list($thawTotaldt)=$dailythawingObj->getThadtGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId);
																					list($thawFreeTotal)=$dailythawingObj->getThaFreeGradeQty($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$editId);
																				}
																				else
																				{
																					list($availableMC, $availableLS) = $dailythawingObj->getAvailablePacksRMLotID($processId, $freezingStage, $frozenCode, $sGradeId, $mCPacking,$selectDate,$tillDate,$hidrmlotID);
																					list($thawingGrdTotal)=$dailythawingObj->getThaGradeQtyRMLot($processId, $freezingStage, $frozenCode,$mCPacking,$thaweditDate,$sGradeId,$editId,$hidrmlotID);
																					list($thawTotaldt)=$dailythawingObj->getThadtGradeQtyRMLotID($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$hidrmlotID);
																					list($thawFreeTotal)=$dailythawingObj->getThaFreeGradeQtyRMLotID($processId, $freezingStage, $frozenCode,$mCPacking,$selectDate,$thaweditDate,$sGradeId,$editId,$hidrmlotID);
																				}
																				
																				$availableNetMC=$thawFreeTotal;
																				$totNumMC += $availableNetMC;
																				$numMC=$thawingGrdTotal;
																				$totAvailableLS += $availableLS;
																			?>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																				<table cellpadding="1" cellspacing="0" width="100%">
																					<tr>
																						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																							<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																							<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																							<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																							<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																							<?//=$numMC;?>
																							<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />			
																						</td>
																					</tr>
																				</table>
																			</td>
																			<?php
																				} // Grade Loop Ends here

																				# Total Slabs
																				//$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;
																				$totalSlabs 	= ($totNumMC*$numPacks);
																				//$totalSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																				#total Qty	
																				$totalQty	= $totalSlabs*$filledWt;
																			?>	
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																				<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																			</td>
																			<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																				<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																			</td>
																		</tr>
																		<?php
																			//} // Loop Ends here
																		?>
																		<input type="hidden" name="hidProcessId" id="hidProcessId" value="<?=$processId?>" readonly />
																		<input type="hidden" name="hidFreezingStage" id="hidFreezingStage" value="<?=$freezingStage?>" readonly />
																		<input type="hidden" name="hidFrozenCode" id="hidFrozenCode" value="<?=$frozenCode?>" readonly />
																		<input type="hidden" name="hidMCPkgId" id="hidMCPkgId" value="<?=$mCPacking?>" readonly />
																		<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																		<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																		<input type="hidden" name="filledWt" id="filledWt" value="<?=$filledWt?>" readonly/>
																		<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
																		<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />	
																		<input type="hidden" name="hidMCPkgCode" id="hidMCPkgCode" value="<?=$stkAllocateMCPkgCode?>" />
																		<input type="hidden" name="editrmlotID" id="editrmlotID" value="<?=$hidrmlotID?>" />
																	</table>
																</TD>
															</tr>
															<tr style="display:none;">
																<td colspan="2" align="center">
																	<table width="75%" align="center" cellpadding="0" cellspacing="0">
																		<tr>
																			<td valign="top">
																				<table width="200">
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Date</td>
																						<td>
																						<?php						
																						if ($selDate=="") $selDate = date("d/m/Y");	
																						?>
																						<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>"></td>
																					</tr>
																					<?php 
																					if ($addMode) 
																					{
																					?>	
																					<tr>
																						<TD class="fieldName" nowrap="true">Quick Entry List</TD>
																						<td>
																							<select name="selQuickEntryList" id="selQuickEntryList" onchange="this.form.submit();">
																								<option value="">-- Select --</option>
																								<?php
																								foreach ($fznPkngQuickEntryListRecords as $fznPkngQuickEntryListId=>$qEntryName) {
																									$selected = ($selQuickEntryList==$fznPkngQuickEntryListId)?"selected":"";	
																								?>
																								<option value="<?=$fznPkngQuickEntryListId?>" <?=$selected?>><?=$qEntryName?></option>
																								<?php
																									}
																								?>
																							</select>
																						</td>
																					</tr>
																					<?php 
																					}
																					?>	
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Fish</td>
																						<td nowrap>							
																							<select name="fish" id="fish" onchange="<? if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>; this.form.submit();<? }?>">
																								<option value="">--Select--</option>
																								<?php
																								foreach ($fishMasterRecords as $fr) {
																									$Id		=	$fr[0];
																									$fishName	=	stripSlash($fr[1]);
																									$selected	= ($fishId==$Id)?"selected":"";
																								?>
																								<option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Process Code</td>
																						<td nowrap>
																							<select name="processCode" id="processCode" onchange="<? if($addMode==true){ ?>this.form.submit();<? } else { ?>this.form.editId.value=<?=$editId?>; this.form.submit();<? }?>">
																								<option value="">-- Select --</option>
																								<?php
																								foreach ($processCodeRecords as $fl) {
																									$processCodeId		=	$fl[0];
																									$processCode		=	$fl[2];
																									$selected	= ($processId==$processCodeId)?"selected":"";
																								?>
																								<option value="<?=$processCodeId;?>" <?=$selected;?> ><?=$processCode;?></option>
																								<?
																								}
																								?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Freezing Stage</td>
																						<td nowrap="true">
																							<select name="freezingStage" id="freezingStage">
																								<option value="">--Select--</option>
																								<?php
																								foreach($freezingStageRecords as $fsr) {
																									$freezingStageId	= $fsr[0];
																									$freezingStageCode	= stripSlash($fsr[1]);
																									$selected		= ($freezingStage==$freezingStageId)?"selected":"";
																								?>
																								<option value="<?=$freezingStageId?>" <?=$selected?>><?=$freezingStageCode?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">EU Code</td>
																						<td nowrap>
																							<select name="eUCode" id="eUCode">
																								<option value="">-- Select--</option>
																								 <?php
																								  foreach ($euCodeRecords as $eucr) {
																									$euCodeId	=	$eucr[0];
																									$euCode		=	stripSlash($eucr[1]);
																									$selected	= ($eUCode==$euCodeId)?"selected":"";
																								  ?>
																								<option value="<?=$euCodeId?>" <?=$selected?>><?=$euCode?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>	
																				</table>
																			</td>
																			<td valign="top">
																				<table width="200">
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Customer</td>
																						<td nowrap>
																							<select name="customer" id="customer" onchange="xajax_getBrandRecs(document.getElementById('customer').value, '');">
																								<option value="">-- select --</option>
																								<?php
																								foreach ($customerRecords as $cr) {
																									$customerId	=	$cr[0];
																									$customerName	=	stripSlash($cr[2]);
																									$selected 	=	($customerId==$customer)?"Selected":"";
																								?>
																								<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Brand</td>
																						<td nowrap>
																							<select name="brand" id="brand">
																							<? if (!sizeof($brandRecords)) {?> 
																								<option value="0">-- Select --</option><? }?>
																								<?php							
																								foreach($brandRecords as $brandId=>$brandName) 
																								{
																									$selected = ($brand==$brandId)?"selected":"";
																								?>
																								<option value="<?=$brandId?>" <?=$selected?>><?=$brandName?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">Frozen Code</td>
																						<td nowrap="nowrap">
																							<select name="frozenCode" id="frozenCode">
																								<option value="">-- Select --</option>
																								 <?php
																								 foreach ($frozenPackingRecords as $fpr) 
																								{
																									$frozenPackingId	= $fpr[0];
																									$frozenPackingCode	= stripSlash($fpr[1]);			
																									$selected		= ($frozenCode==$frozenPackingId)?"selected":"";
																								  ?>
																								<option value="<?=$frozenPackingId?>" <?=$selected?>><?=$frozenPackingCode?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap="nowrap">MC Pkg</td>
																						<td nowrap="nowrap">
																							<select name="mCPacking" id="mCPacking">
																								<option value="">-- Select --</option>
																								  <?php
																								  foreach($mcpackingRecords as $mcp) {
																									$mcpackingId	=	$mcp[0];
																									$mcpackingCode	=	stripSlash($mcp[1]);
																									$selected	= ($mCPacking==$mcpackingId)?"selected":"";
																								  ?>
																								<option value="<?=$mcpackingId?>" <?=$selected?>><?=$mcpackingCode?></option>
																								<? }?>
																							</select>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
<!-- ============================================================================= NEW STARTS HERE ========================== -->
															<?php
															if ($stockReport)
															{
																$displayQE='DMCLS';
															?>
															<tr>
																<TD colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
																	<table>		
																		<TR>
																			<TD>
																				<table width="200" border="0" cellpadding="1" cellspacing="1" align="center" bgcolor="#999999">
																				<?php
																				if ($stkReportPCSize>0 && $stkReportGradeSize>0) 
																				{
																					$i = 0;
																				?>
																					<tr bgcolor="#f2f2f2"  align="center">		
																						<td nowrap style="padding-left:2px;padding-right:2px;" class="listing-head">Grade</td>
																						<?php
																							$spc = 0;
																							foreach ($stkReportPC as $pCode=>$srpc) {
																								$stkRPCId = $srpc[0];
																								$spc++;
																						?>
																						<td nowrap style="padding-left:2px;padding-right:2px;">				
																							<table cellpadding="1" cellspacing="0" width="100%">
																								<TR bgcolor="#f2f2f2">
																									<TD class="listing-head" colspan="3" align="center" id="processCodeCol_<?=$spc?>">
																										<?=$pCode;?>
																										<input type="hidden" name="packEntered_<?=$spc?>" id="packEntered_<?=$spc?>" value=""/>
																										<input type="hidden" name="hProcesscodeId_<?=$spc?>" id="hProcesscodeId_<?=$spc?>" value="<?=$stkRPCId?>"/>
																										<input type="hidden" name="hFishId_<?=$spc?>" id="hFishId_<?=$spc?>" value="<?=$qelSFishId?>"/>	
																										<input type="hidden" name="recExist_<?=$spc?>" id="recExist_<?=$spc?>" value=""/>
																										<span id="qelPCErr_<?=$spc?>" class="listing-item" style="font-size:9px; color:black;"></span>
																									</TD>
																								</TR>
																								<tr>
																									<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																								</tr>
																								<tr>
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DMC') { 
																									?>
																									<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																									<?php }?>
																									<?php
																										if ($displayQE=='DMCLS') {
																									?>
																									<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																									<? }?>	
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																									?>
																									<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																									<? }?>
																								</tr>
																							</table>
																						</td>
																						<?php
																						}
																						?>
																					</tr>
																					<?php	
																					$g = 0;
																					foreach ($stkReportGrade as $displayGrade=>$gradeId) 
																					{
																							$g++;
																					?>
																					<tr bgcolor="WHITE">
																						<td class="listing-item" nowrap style="padding-left:2px;padding-right:2px;" id="gradeRow_<?=$g?>">
																							<input type="hidden" name="gradeId_<?=$g;?>" value="<?=$gradeId?>">
																								<?=$displayGrade?>
																						</td>
																						<?php
																						$p=0;
																						foreach ($stkReportPC as $pcr=>$srpc) 
																						{
																							$p++;			
																							$sProcessCodeId = $srpc[0];
																							# Check The selected PC has Grade Exist
																							$pcHasGrade = $dailyfrozenpackingObj->processCodeHasGrade($sProcessCodeId, $gradeId);
																							# Find the total number of stock items(MC and Loos Slab)					
																							//list($totNumMC, $totNumLS) = $dailythawingObj->getTotalStock($sProcessCodeId, $gradeId);
																							list($totNumMC, $totNumLS) = $dailythawingObj->getFPClosingStock($sProcessCodeId, $gradeId, $stkTillDate);
																							$cellReadonly = "";
																							$styleDisplay = "";
																							if (!$pcHasGrade) { 
																								$cellReadonly = "readonly";
																								$styleDisplay = "border:none;";
																							}
																						?>
																						<td nowrap align="right" height="25" style="padding-left:2px;padding-right:2px;">
																							<input type="hidden" name="hidProcesscodeId_<?=$p?>" id="hidProcesscodeId_<?=$p?>" value="<?=$sProcessCodeId?>"/>
																							<input type="hidden" name="hidFishId_<?=$p?>" id="hidFishId_<?=$p?>" value="<?=$sFishId?>"/>	
																							<!--<input type="text" name="recExist_<?=$p?>" id="recExist_<?=$p?>" value=""/>-->
																							<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																								<TR>
																								<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																								?>
																									<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;" class="listing-item">
																										<?php
																										if ($totNumMC!=0) echo "<span style='color:Maroon;'>$totNumMC</span><br>";
																										?>
																										<input name="numMC_<?=$p?>_<?=$g?>" type="text" id="numMC_<?=$p?>_<?=$g?>" size="4" value="<?=$numMC?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyThawing','numMC_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" autocomplete="off" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" <?=$cellReadonly?> />
																									</TD>
																									<? } ?>
																									<?php
																									if ($displayQE=='DMCLS') {
																									?>
																									<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																									<? 
																									}?>
																									<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																									?>
																									<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;" class="listing-item">
																										<?php
																										if ($totNumLS!=0) echo "<span style='color:Maroon;'>$totNumLS</span><br>";
																										?>
																										<input name="numLooseSlab_<?=$p?>_<?=$g?>" type="text" id="numLooseSlab_<?=$p?>_<?=$g?>" size="4" value="<?=$numLooseSlab?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyThawing','numLooseSlab_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" autocomplete="off" <?=$cellReadonly?> />
																									</TD>
																									<? }?>
																								</TR>
																							</table>
																						</td>
																						<?php
																							} // Process code Loop ends here
																						?>
																					</tr>
																					<?php 
																						} // Grade Loop Ends Here
																					?>					
																					<tr bgcolor="White">
																						<TD class="listing-head" align="right" style="padding-left:2px;padding-right:2px;">Total:</TD>
																						<?php
																						$p=0;
																						foreach ($stkReportPC as $pcr) {
																							$p++;
																						?>
																						<TD align="right" style="padding-left:2px;padding-right:2px;">
																							<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																								<TR>
																								<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																								?>
																									<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																										<input name="totalMCPack_<?=$p?>" type="text" id="totalMCPack_<?=$p?>" size="4" value="<?=$totMcPack?>" style="text-align:right;font-weight:bold;border:none;" readonly />				
																									</td>
																								<? } ?>
																								<?php
																								if ($displayQE=='DMCLS') 
																								{
																								?>
																									<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																								<? }?>
																								<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DLS') 
																								{
																								?>
																									<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																										<input name="totLooseSlab_<?=$p?>" type="text" id="totLooseSlab_<?=$p?>" size="4" value="<?=$totLooseSlab?>" style="text-align:right;font-weight:bold;border:none;" readonly />
																										<!--TotSlab Display -->
																										<span id="totSlabs_<?=$p?>" onMouseover="ShowTip('Total Slabs');" onMouseout="UnTip();"></span>
																									</td>
																								<? }?>
																								</TR>
																								<tr style="display:none;">
																									<TD colspan="3" align="center">
																										<select name="frozenCode_<?=$p?>" id="frozenCode_<?=$p?>" style="width:80px;">
																											<option value="">-- Select --</option>
																											<?php
																											foreach ($frozenPackingRecords as $fpr) {
																												$frozenPackingId	= $fpr[0];
																												$frozenPackingCode	= stripSlash($fpr[1]);			
																												$selected		= ($frozenCodeId==$frozenPackingId)?"selected":"";
																											?>
																											<option value="<?=$frozenPackingId?>" <?=$selected?>><?=$frozenPackingCode?></option>
																											<? }?>
																										</select>
																									</TD>
																								</tr>
																							</table>
																						</TD>
																						<?php
																						}
																						?>
																					</tr>	
																					<?php
																					 // Size of Grade Check
																					 } else if (sizeof($qeGradeRecords)>0) {
																					?>
																					<tr bgcolor="White">
																						<TD class="err1" nowrap="true" style="padding:10 10 10 10px;">No process codes are valid for the selected day.</TD></tr>
																						<? }?>
																							<input type="hidden" name="hidPCRowCount" id="hidPCRowCount" value="<?=$p?>"/>
																							<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=$g;?>" >
																						</TD>
																					</tr>
																				</table>
																			</TD>
																		</TR>
																	</table>
																</TD>
															</tr>
															<?php
															}
															?>
<!-- ==================================================================== NEW ENDS HERE ================================== -->
															<?php
															if ($selQuickEntryList) 
															{
															?>
															<tr>
																<TD colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
																	<table>
																		<tr bgcolor="White">
																			<TD class="listing-head" colspan="2">
																			MC Packing = &nbsp;			
																				<? if ($qelMCPackingCode!="") { ?>
																				<span class="listing-item"><b><?=$qelMCPackingCode?></b></span>
																				<? } else {?>
																				<select name="qeMCPacking" id="qeMCPacking" onchange="xajax_getMCNumPack(document.getElementById('qeMCPacking').value); calcMCPack('<?=$displayQE?>');">
																					<option value="">-- Select --</option>
																					<?php
																					  foreach($mcpackingRecords as $mcp) {
																						$mcpackingId	= $mcp[0];
																						$mcpackingCode	= stripSlash($mcp[1]);
																						$selected	= ($mCPacking==$mcpackingId)?"selected":"";
																					?>
																					<option value="<?=$mcpackingId?>" <?=$selected?>><?=$mcpackingCode?></option>
																					<? }?>
																				</select>
																				<?php
																					}		
																				?>
																			</TD>
																		</tr>
																		<TR>
																			<TD>
																				<table width="200" border="0" cellpadding="1" cellspacing="1" align="center" bgcolor="#999999">
																				<?php
																				if (sizeof($qeGradeRecords) && sizeof($selProcessCodeRecs)>0) 
																				{
																					$i = 0;
																				?>
																					<tr bgcolor="#f2f2f2"  align="center">		
																						<td nowrap style="padding-left:2px;padding-right:2px;" class="listing-head">Gradee</td>
																						<?php
																							$spc = 0;
																							foreach ($selProcessCodeRecs as $pcr) {
																								$spc++;
																								//$pCode = $pcr[3];
																								$pCode = $pcr[2];
																								$qelSFishId	= $pcr[1];
																								$qelSPCId 	= $pcr[0];
																						?>
																						<td nowrap style="padding-left:2px;padding-right:2px;">				
																							<table cellpadding="1" cellspacing="0" width="100%">
																								<TR bgcolor="#f2f2f2">
																									<TD class="listing-head" colspan="3" align="center" id="processCodeCol_<?=$spc?>">
																										<?=$pCode;?>
																										<input type="hidden" name="packEntered_<?=$spc?>" id="packEntered_<?=$spc?>" value=""/>
																										<input type="hidden" name="hProcesscodeId_<?=$spc?>" id="hProcesscodeId_<?=$spc?>" value="<?=$qelSPCId?>"/>
																										<input type="hidden" name="hFishId_<?=$spc?>" id="hFishId_<?=$spc?>" value="<?=$qelSFishId?>"/>	
																										<input type="hidden" name="recExist_<?=$spc?>" id="recExist_<?=$spc?>" value=""/>
																										<span id="qelPCErr_<?=$spc?>" class="listing-item" style="font-size:9px; color:black;"></span>
																									</TD>
																								</TR>
																								<tr>
																									<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																								</tr>
																								<tr>
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																									?>
																									<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																									<?php }?>
																									<?php
																										if ($displayQE=='DMCLS') {
																									?>
																									<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																									<? }?>	
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																									?>
																									<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																									<? }?>
																								</tr>
																							</table>
																						</td>
																						<?php
																							}
																						?>
																					</tr>
																					<?php	
																					$g = 0;
																					foreach ($qeGradeRecords as $gr)
																					{
																						$g++;
																						$gradeId 	= $gr[0];
																						$displayGrade	= $gr[1];
																					?>
																					<tr bgcolor="WHITE">
																						<td class="listing-item" nowrap style="padding-left:2px;padding-right:2px;" id="gradeRow_<?=$g?>">
																							<input type="hidden" name="gradeId_<?=$g;?>" value="<?=$gradeId?>">
																							<?=$displayGrade?>
																						</td>
																						<?php
																						$p=0;
																						foreach ($selProcessCodeRecs as $pcr) {
																							$p++;
																							 
																							$sFishId	= $pcr[1];
																							$sProcessCodeId = $pcr[0];

																							# Check The selected PC has Grade Exist
																							$pcHasGrade = $dailyfrozenpackingObj->processCodeHasGrade($sProcessCodeId, $gradeId);

																							//Find the total number of stock items(MC and Loos Slab)					
																							list($totNumMC, $totNumLS) = $dailythawingObj->getTotalStock($sProcessCodeId, $gradeId);

																							$cellReadonly = "";
																							$styleDisplay = "";
																							if (!$pcHasGrade) { 
																								$cellReadonly = "readonly";
																								$styleDisplay = "border:none;";
																							}
																						?>
																						<td nowrap align="right" height="25" style="padding-left:2px;padding-right:2px;">
																								<input type="hidden" name="hidProcesscodeId_<?=$p?>" id="hidProcesscodeId_<?=$p?>" value="<?=$sProcessCodeId?>"/>
																								<input type="hidden" name="hidFishId_<?=$p?>" id="hidFishId_<?=$p?>" value="<?=$sFishId?>"/>	
																								<!--<input type="text" name="recExist_<?=$p?>" id="recExist_<?=$p?>" value=""/>-->
																							<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																								<TR>
																								<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																								?>
																									<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;" class="listing-item">
																									<?php
																									if ($totNumMC!=0) echo "<span style='color:Maroon;'>$totNumMC</span><br>";
																									?>
																									<input name="numMC_<?=$p?>_<?=$g?>" type="text" id="numMC_<?=$p?>_<?=$g?>" size="4" value="<?=$numMC?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyThawing','numMC_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" autocomplete="off" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" <?=$cellReadonly?> />
																									</TD>
																								<? } ?>
																								<?php
																									if ($displayQE=='DMCLS') {
																								?>
																									<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																									<? }?>
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																									?>
																									<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;" class="listing-item">
																										<?php
																										if ($totNumLS!=0) echo "<span style='color:Maroon;'>$totNumLS</span><br>";
																										?>
																										<input name="numLooseSlab_<?=$p?>_<?=$g?>" type="text" id="numLooseSlab_<?=$p?>_<?=$g?>" size="4" value="<?=$numLooseSlab?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyThawing','numLooseSlab_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" autocomplete="off" <?=$cellReadonly?> />
																									</TD>
																									<? }?>
																								</TR>
																							</table>
																						</td>
																						<?php
																						} // Process code Loop ends here
																						?>
																					</tr>
																					<?php 
																						} // Grade Loop Ends Here
																					?>					
																					<tr bgcolor="White">
																						<TD class="listing-head" align="right" style="padding-left:2px;padding-right:2px;">Total:</TD>
																							<?php
																								$p=0;
																								foreach ($selProcessCodeRecs as $pcr)
																								{
																									$p++;
																							?>
																							<TD align="right" style="padding-left:2px;padding-right:2px;">
																								<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																									<TR>
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																									?>
																										<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																											<input name="totalMCPack_<?=$p?>" type="text" id="totalMCPack_<?=$p?>" size="4" value="<?=$totMcPack?>" style="text-align:right;font-weight:bold;border:none;" readonly />				
																										</td>
																									<? } ?>
																									<?php
																										if ($displayQE=='DMCLS') {
																									?>
																										<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																									<? }?>
																									<?php
																										if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																									?>
																										<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																											<input name="totLooseSlab_<?=$p?>" type="text" id="totLooseSlab_<?=$p?>" size="4" value="<?=$totLooseSlab?>" style="text-align:right;font-weight:bold;border:none;" readonly />
																											<!--TotSlab Display -->
																											<span id="totSlabs_<?=$p?>" onMouseover="ShowTip('Total Slabs');" onMouseout="UnTip();"></span>
																										</td>
																									<? }?>
																									</TR>
																									<tr>
																										<TD colspan="3" align="center">
																											<select name="frozenCode_<?=$p?>" id="frozenCode_<?=$p?>" style="width:80px;">
																												<option value="">-- Select --</option>
																												 <?php
																												 foreach ($frozenPackingRecords as $fpr) 
																												{
																													$frozenPackingId	= $fpr[0];
																													$frozenPackingCode	= stripSlash($fpr[1]);			
																													$selected		= ($frozenCodeId==$frozenPackingId)?"selected":"";
																												  ?>
																												<option value="<?=$frozenPackingId?>" <?=$selected?>><?=$frozenPackingCode?></option>
																												<? }?>
																											</select>
																										</TD>
																									</tr>
																								</table>
																							</TD>
																							<?php
																								}
																							?>
																						</tr>	
																						<?php
																						 // Size of Grade Check
																						 } else if (sizeof($qeGradeRecords)>0) {
																						?>
																						<tr bgcolor="White">
																							<TD class="err1" nowrap="true" style="padding:10 10 10 10px;">No process codes are valid for the selected day.</TD>
																						</tr>
																						<? }?>
																						<input type="hidden" name="hidPCRowCount" id="hidPCRowCount" value="<?=$p?>"/>
																						<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=$g;?>" >
																					</TD>
																				</tr>
																			</table>
																		</TD>
																	</TR>
																</table>
															</TD>
														</tr>
														<?php
														// QE List Ends Here
														}  
														?>
														<tr>
															<td colspan="2" align="center">&nbsp;</td>
														</tr>
														<tr >
															<? if($editMode){?>
															<td  class="fieldname" style="float:left" >Thawed material to be used on
																<input type="text" id="editDate" name="editDate" size="8" value="<?=$getdatedt?>">
															</td>
															<? }?>
														</tr>
														<tr>
															<? if($editMode){?>
															<td align="center">
																<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyThawing.php');">&nbsp;&nbsp;
																<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateThawing(document.frmDailyThawing);"></td>
															<? 
															} 
															else
															{
															?>
															<td align="center">
																<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyThawing.php');">&nbsp;&nbsp;
																<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyThawing(document.frmDailyThawing);">&nbsp;&nbsp;												</td>
															<? }?>
														</tr>
														<tr>
															<td  height="10" ></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<!-- Form fields end   -->
						</td>
					</tr>	
					<?
					}# Listing Grade Starts
					?>
					<tr>
						<td height="10" align="center" ></td>
					</tr>
					<tr>
						<td>
							<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
								<tr>
									<td   bgcolor="white">
										<!-- Form fields start -->
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
												<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Thawing </td>
												<td background="images/heading_bg.gif" class="pageName" align="right" >
													<table cellpadding="0" cellspacing="0" width="200">
														<tr> 
															<td class="listing-item" nowrap>From&nbsp;</td>
															<td nowrap="nowrap"> 
																<? 
																if ($dateFrom==""){
																//$currYear=Date("Y");
																//$currFinanYear="01/04/$currYear";
																$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																$dateFrom=$defaultDFPDate;
																}
																?>
																<input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$dateFrom?>">
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="listing-item" nowrap>Till&nbsp;</td>
															<td nowrap> 
															<? 
															if($dateTill=="") $dateTill=date("d/m/Y");
															?>
																<input type="text" id="frozenPackingTill" name="frozenPackingTill" size="8"  value="<?=$dateTill?>">
															</td>
															<td class="listing-item">&nbsp;</td>
															<td nowrap>
																<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value=" Search ">
															</td>
															<td class="listing-item" nowrap >&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="3" height="10" ></td>
											</tr>
											<tr>	
												<td colspan="3">
												</td>
											</tr>
											<tr>
												<td colspan="3" height="5" ></td>
											</tr>
											<?
											if($errDel!="")
											{
											?>
											<tr>
												<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
											</tr>
											<?
											}
											?>
											<tr>
												<td width="1" ></td>
												<td colspan="2" style="padding-left:10px; padding-right:10px;" >
													<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
													<?
													if ( sizeof($dailyThawingRecords) > 0 ) {
														$i	=	0;
													?>
														<? if($maxpage>1){?>
														<tr bgcolor="#FFFFFF">
															<td colspan="11" style="padding-right:10px">
																<div align="right">
																<?php 				 			  
																$nav  = '';
																for($page=1; $page<=$maxpage; $page++) 
																{
																	if ($page==$pageNo) {
																		$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																	} else {
																			$nav.= " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
																	}
																}
																if ($pageNo > 1) 
																{
																	$page  = $pageNo - 1;
																	$prev  = " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
																} else {
																	$prev  = '&nbsp;'; // we're on page one, don't print previous link
																$first = '&nbsp;'; // nor the first page link
																}
																if ($pageNo < $maxpage)	{
																	$page = $pageNo + 1;
																	$next = " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
																} else {
																	$next = '&nbsp;'; // we're on the last page, don't print next link
																	$last = '&nbsp;'; // nor the last page link
																}
																// print the navigation link
																$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																echo $first . $prev . $nav . $next . $last . $summary; 
																?>	
																<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
																</div>		
															</td>
														</tr>
														<? }?>
														<tr  bgcolor="#f2f2f2" align="center">
															<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');assignValue(this.form,<?=$dailyThawingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');assignValue(this.form,'<?=$dateFrom?>','hidfromdate');" ></td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>RM Lot ID</td>
															
															<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</td>-->
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">Process Code</td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">Freezing Stage</td>
															<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">EU Code</td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">Brand</td>-->
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">Frozen Code&nbsp; </td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">MC Pkg</td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">MC</td>
															<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Grade</td>-->
															<td class="listing-head" style="padding-left:10px; padding-right:10px;">Thawed Stock</td>
															<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Rm lot id linked</td>

															<? if($edit==true){?>
															<td class="listing-head" width="45">&nbsp;</td>
															<? }?>
														</tr>
														<?php
														$thawSum=0;
														$thawMCSum=0;
														foreach ($dailyThawingRecords as $dtr) 
														{
															$i++;
															
															$dailyThawingMainId	=	$dtr[0];
															$entryDate 	=	$dtr[8];
															$selectedDate	=	dateFormat($entryDate);
															$hidfishid=$dtr[1];
															$fish	=	$fishmasterObj->findFishName($dtr[1]);
															$processCode = $processcodeObj->findProcessCode($dtr[2]);
															if($processCode=="")
															{
																$processCode = $secondaryProcessCodeObj->findSecondaryProcessCode($dtr[2]);
															}
															$freezingStage = $freezingstageObj->findFreezingStageCode($dtr[3]);
															$eUCode = $eucodeObj->findEUCode($dtr[4]);
															$brand = $brandObj->findBrandCode($dtr[5]);
															$frozenCode = $frozenpackingObj->findFrozenPackingCode($dtr[6]);
															$filledWt=$frozenpackingObj->frznPkgFilledWt($dtr[6]);
															/*echo $dtr[6];
															echo $filledWt;
															$glazeId=$frozenpackingObj->frznPkgglaze($dtr[6]);
															echo $glazeId;
															$glaze=$glazeObj->findGlazePercentage($glazeId);
															$thawStock=$filledWt-($filledWt*$glaze/100);*/
															$thawStock=$dtr[12];
															$rmlotID=$dtr[13];
															if ($rmlotID>0 ? $rmlotIDName=$dtr[14] : $rmlotIDName='');
														
															$mCPackingCode = $mcpackingObj->findMCPackingCode($dtr[7]);
															$thawMC=$dtr[9];
															$gradeCode=$grademasterObj->findGradeCode($dtr[10]);
															$selEditCriteria="$processCode-$freezingStage-$frozenCode-$mCPackingCode-$filledWt";
															$selEditCriteriaVal="$dtr[2]-$dtr[3]-$dtr[6]-$dtr[7]/$entryDate";
															$thawMCSum=$thawMC+$thawMCSum;
															$thawSum=$thawStock+$thawSum;
															///$entryDate;
															//echo "hii$i".$rmlotIDName;
															if($rmlotIDName=="")
															{
																$rmlotIds=$dailythawingObj->getNewRm($dtr[2],$dtr[3],$dtr[6],$dtr[7],$entryDate);
															}
															else
															{
																$rmlotIds="";
															}
														?>
														<tr  bgcolor="WHITE">
															<td width="20"><?//=$dailyThawingMainId;?><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" onClick="assignValue(this.form,<?=$dailyThawingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');
															assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');assignValue(this.form,'<?=$selEditCriteriaVal?>','editCriteriaVal');assignValue(this.form,'<?=$dateFrom?>','hidfromdate');  assignValue(this.form,'<?=$rmlotID?>','hidrmlotID'); assignValue(this.form,'<?=$hidfishid?>','hidfishID'); toggle(this);" value="<?=$dailyThawingMainId;?>"><input type="hidden" name="dailyFrozenPackingEntryId_<?=$i;?>" value="<?=$dailyFrozenPackingEntryId?>"></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectedDate;?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotIDName?></td>
															<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$fish?></td>-->
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processCode?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingStage?></td>
															<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$eUCode;?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$brand?></td>-->
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$frozenCode;?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$mCPackingCode;?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><span class="listing-item" style="padding-left:10px; padding-right:10px;align:right">
															  <?=$thawMC;?>
															</span></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$thawStock;?></td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$rmlotIds?></td>
															<? if($edit==true){?>
															<td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyThawingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');
															assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria'); assignValue(this.form,'<?=$selEditCriteriaVal?>','editCriteriaVal'); assignValue(this.form,'<?=$dateFrom?>','hidfromdate'); assignValue(this.form,'<?=$rmlotID?>','hidrmlotID'); assignValue(this.form,'<?=$hidfishid?>','hidfishID'); this.form.action='DailyThawing.php';"></td>
															 <? }?>
														</tr>
														<?
														}
														?>		
														<tr  bgcolor="WHITE">
															<td>&nbsp;</td>
															<td colspan="5" nowrap class="listing-item" style="padding-left:10px; padding-right:10px;">&nbsp;</td>
															<td nowrap class="listing-item" style="padding-left:10px; padding-right:10px;"><span class="listing-head" style="padding-left:10px; padding-right:10px;">Total:</span></td>
															<td nowrap class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$thawMCSum;?>&nbsp;</td>
															<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$thawSum;?></td>
															<td class="listing-item" align="center">&nbsp;</td>
															<td class="listing-item" align="center">&nbsp;</td>
														</tr>	
														<tr bgcolor="WHITE">
															<td colspan="11" align="center"><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyThawingRecordSize;?>);assignValue(this.form,<?=$dailyThawingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');
																assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');assignValue(this.form,'<?=$dateFrom?>','hidfromdate');"><? }?>
															</td>
														</tr>
																<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
																<input type="hidden" name="hidfromdate" value="">
																<input type="hidden" name="editId" value="">
																<input type="hidden" name="editFrozenPackingEntryId" value="<?=$frozenPackingEntryId;?>">
																<input type="hidden" name="editSelectionChange" value="0">
																<input type="hidden" name="editMode" value="<?=$editMode?>">
																<input type="hidden" name="editCriteria" id="editCriteria" value="<?=$editCriteria?>" readonly="true">
																<input type="hidden" name="editCriteriaVal" id="editCriteriaVal" value="<?=$selEditCriteriaVal/*=$editCriteriaVal*/?>" readonly="true">
																<input type="hidden" name="hidrmlotID"	id="hidrmlotID" value="<?=$rmlotID?>" >
																<input type="hidden" name="hidfishID"	id="hidfishID" value="<?=$hidfishid?>" >
														<? if($maxpage>1){?>
														<tr bgcolor="#FFFFFF">
															<td colspan="11" style="padding-right:10px">
																<div align="right">
																<?php 				 			  
																 $nav  = '';
																for($page=1; $page<=$maxpage; $page++) {
																	if ($page==$pageNo) {
																		$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
																	} else {
																		$nav.= " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
																	}
																}
																if ($pageNo > 1) {
																	$page  = $pageNo - 1;
																	$prev  = " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
																} else {
																	$prev  = '&nbsp;'; // we're on page one, don't print previous link
																	$first = '&nbsp;'; // nor the first page link
																}

																if ($pageNo < $maxpage) {
																	$page = $pageNo + 1;
																	$next = " <a href=\"DailyThawing.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
																 } else {
																	$next = '&nbsp;'; // we're on the last page, don't print next link
																	$last = '&nbsp;'; // nor the last page link
																}
																// print the navigation link
																$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																echo $first . $prev . $nav . $next . $last . $summary; 
																 ?>	
																<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
																</div>
															</td>
														</tr>
														<? }?>
														<?php
															} else {
														?>
														<tr bgcolor="white">
															<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
														</tr>	
														<?php
															}
														?>
													</table>
													<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
												</td>
											</tr>
											<tr>
												<td colspan="3" height="5" ></td>
											</tr>
											<tr >	
												<td colspan="3">
													<table cellpadding="0" cellspacing="0" align="center">
														<tr>
															<td><? //if($del==true){?><!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyThawingRecordSize;?>);assignValue(this.form,<?=$dailyThawingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');
								assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');assignValue(this.form,'<?=$dateFrom?>','hidfromdate');">--><? //}?>&nbsp;<!--<? //if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? //}?>&nbsp;<? //if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyThawing.php',700,600);"><? //}?>--></td>
														</tr>
													</table>									
												</td>
											</tr>
											<tr>
												<td colspan="3" height="5" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							<!-- Form fields end   -->
						</td>
					</tr>
					<tr>
						<td height="10">
						<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
						</td>
					</tr>	
				</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>

	 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "editDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "editDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>