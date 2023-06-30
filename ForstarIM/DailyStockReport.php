<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";	
	$searchMode	= false;	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	$selCustomerId 		= $p["customer"];
	$selFishCategoryId 	= $p["fishCategory"];
	$dateFrom 		= $p["dateFrom"];
	$dateTo 		= $p["dateTo"];
	$selFishId 		= $p["selFish"]; //Multiple selection	
	$selFish = "";
	if (sizeof($selFishId)>0) $selFish = implode(",",$selFishId);	
	$dateC	   =	explode("/", $dateFrom);
	$reportType	= $p["reportType"];
	$packType	= $p["packType"];
	$checkboxSeledVAL = false;	
	$checkboxSeled='';
	
	# Drive to Daily Thawing
	if ($p["cmdAddDailyThawing"]) {
		$listRec = $_SESSION["listRec"];
		$stkReportPC = $_SESSION["stkReportPC"];
		$stkReportGrade = $_SESSION["stkReportGrade"];
		$_SESSION["stkTillDate"] = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-1,$dateC[2]));
		header("Location:DailyThawing.php?STOCKREPORT=Y");
		exit;
	}		
		$maxdate=$dailyStockReportObj->getMaxDate();
		$maximumdt= dateFormat($maxdate[0]);
		$defaultDFPDateRe			=	dateformat($displayrecordObj->getDefaultDFPDate());
		$dateFromMax=($maximumdt!="")?$maximumdt:$defaultDFPDateRe;
		$dateFrom=$dateFromMax;
		$dateTo = date("d/m/Y");
		$fromDateMax = mysqlDateFormat($dateFromMax);
		$tillDate = mysqlDateFormat($dateTo);		
		# Search starts Here
		if ($dateFrom && $dateTo) {
		# Get Selected Fish
		$getFishRecords = $dailyStockReportObj->getFishRecords($fromDate, $tillDate);
		if ($p["cmdSearch"]!="" || $g[flag]!="") {
			
			$searchMode = true;
			$stkGroupListEnabled = false;			
			$selectDate=$p["dateTo"];
			$dateSelect=mysqlDateFormat($selectDate);
			if (strtotime($selectDate)-strtotime($maximumdt) >0){
				// echo $fromDateMax;
				$fromDate=$fromDateMax;

				$sdateFrom=$dateFromMax;
				if(isset($p["checkboxSel"])){
					$fromDate=date("Y-m-d");
					$checkboxSeled=' checked ';
					$checkboxSeledVAL = true;				
				}else{
					$checkboxSeled='';
					$checkboxSeledVAL = false;					
				}
				$gradeRecs = $dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate);
				$colnum=sizeof($dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate));		
			}
			else {
				$fromDate=$dateSelect;				
				$maxNextdate=  $dailyStockReportObj->getMaxNextDate($fromDate);
				$maximumNextdt= dateFormat($maxNextdate[0]);
				//$currYear=Date("Y");
				//$currFinanYear="01/04/$currYear";
				$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
				$dateFromNextMax=($maximumNextdt!="")?$maximumNextdt:$defaultDFPDate;
				$fromDateNextMax = mysqlDateFormat($dateFromNextMax);		
				$sdateFrom=$dateFromNextMax;
				$tillDate=$dateSelect;	
				$fromDate=$fromDateNextMax;	
				if(isset($p["checkboxSel"])){
					$fromDate=date("Y-m-d");
					$fromDateNextMax=date("Y-m-d");
					$checkboxSeled=' checked ';				
				}else{
					$checkboxSeled='';				
				}			
				$gradeRecs = $dailyStockReportObj->getProductionGradeRecs($fromDateNextMax,$dateSelect);
				$colnum=sizeof($dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate));		
			}
			
			$stocktype=$p["stockType"];
			$packType	= $p["packType"];
			$checkboxSel = $p["checkboxSel"];
		 #dailyfrozenreport starts					  
		## -------------- Pagination Settings I ------------------
		if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
		else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
		else $pageNo=1;	
		$offset = ($pageNo - 1) * $limit; 
		## ----------------- Pagination Settings I End ------------
		
		if ($g["flag"]!=""){
			$fromDate=$g["frozenPackingFrom"];	
			$sdateFrom=dateFormat($g["frozenPackingFrom"]);
			
			$stocktype=$g["stocktype"];
			$packType	= $g["packType"];
			$reportType=$g["reportType"];
			
		}
		if ($tillDate=="")
		{
			$tillDate=$g["frozenPackingTill"];
			$selectDate=dateFormat($g["frozenPackingTill"]);
			$gradeRecs = $dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate);
			$colnum=sizeof($dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate));
		}
			$dailyFrozenPackingRecs = $dailyStockReportObj->getPagingDFPRecs($fromDate, $tillDate, $offset, $limit);
			$numrows=sizeof($dailyStockReportObj->getDFPForDateRange($fromDate, $tillDate));
			
			## -------------- Pagination Settings II -------------------
			$maxpage	=	ceil($numrows/$limit);
			
			## ----------------- Pagination Settings II End ------------		
		if ($reportType=="PRIR"){
			$stocktype="";
		}
		}
		}	
				# Customer Records
				//$customerRecords = $customerObj->fetchAllRecords();
				$customerRecords = $customerObj->fetchAllRecordsActiveCustomer();
				# Fish Category Recs
				//$fishCategoryRecs = $fishcategoryObj->fetchAllRecords();
				$fishCategoryRecs = $fishcategoryObj->fetchAllRecordscategoryActive();
				# Define Array
				$reportTypeArr 	= array("STKR"=>"Stock Report", "PRIR"=>"Price Report");
				$packTypeArr 	= array("MC"=>"MC", "LS"=>"Loose Slab");
				$stockTypeArr 	= array("2"=>"Total Stock","1"=>"Free Stock","3"=>"Allocated Stock");
				# Display heading	
				$heading = "Daily Stock Report";	
				# Include JS
				$ON_LOAD_PRINT_JS	= "libjs/DailyStockReport.js";
				# Include Template [topLeftNav.php]
				require("template/topLeftNav.php");
?>
	<form name="frmDailyStockReport" action="DailyStockReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<? if($err!="" ){?>
		<tr>
			<td height="30" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?php if ($statusChangeMode) {?>
		<tr>
			<td height="30" align="center" class="err1" ><?=($releaseAllocation)?"<span style='color:green;'>Successfully released allocation</span>":"Failed to release allocation"?></td>
		</tr>
		<?php } ?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2"  align="center">
									<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						          <tr> 
                                  <td colspan="4" align="center">
				  <? if($print==true){?>
				&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; </td>
                        <? } ?>
                      </tr>
	<tr> 
        <td colspan="3" nowrap align="center">
		<table width="200" border="0">
			<tr>
				<TD valign="top" nowrap="true">
					<fieldset>
					<table>
						<TR>
						<TD>
						<table>
							<TR>
							<td class="fieldName" style="display:none">From:</td>
								<td nowrap align="left" style="display:none">
									<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>"  autocomplete="off" onchange="submitForm('dateFrom','dateTo', frmDailyStockReport);" />
								</td>
								<td class="fieldName">As&nbsp;On:</td>
								<td nowrap align="left">
								<?php
								if ($p["dateTo"]!="")
								{
									$dateToVal=$p["dateTo"];
								}
								else
								{
									$dateToVal=$dateTo;
								}

								 if (!$isAdmin)
								{
									 $readOnly="readOnly";					
								}
								?>
								<input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateToVal?>" autocomplete="off"   <?=$readOnly;?> />
								</td>
							</TR>
						</table>
						</TD>
						</TR>
						<tr>
						<TD>
						<table style="display:none">
							<tr>
							<td class="fieldName">Fish:</td>
							<td nowrap align="left">
								<select name="selFish[]" id="selFish" multiple="true" size="7">
									<option value="">--Select All--</option>
									<?php						
									foreach ($getFishRecords as $gfr) {
										$sFishId 	= $gfr[0];
										$sFishName	= $gfr[1];
										$selected = (in_array($sFishId,$selFishId))?"selected":"";	
									?>
									<option value="<?=$sFishId?>" <?=$selected?>><?=$sFishName?></option>
									<? }?>
								</select>
							</td>
							</tr>
						</table>
						</TD>
						</tr>
					</table>
					</fieldset>

					<br>
					<fieldset>
					<table>
						<TR>
						<TD>
						<table>
							<TR>
							<td class="fieldName" style="display:none">From:</td>
								<td nowrap align="left" style="display:none">
									<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>"  autocomplete="off" onchange="submitForm('dateFrom','dateTo', frmDailyStockReport);" />
								</td>
								<td class="fieldName"><input type="checkbox" id="checkboxSel" name="checkboxSel"  <?=$checkboxSeled;?>/> Daily Report</td>
								<td nowrap align="left">
								</td>
							</TR>
						</table>
						</TD>
						</TR>
						
					</table>
					</fieldset>


				</TD>
				<TD valign="top" nowrap="true">
					<!--<fieldset>-->
					<table style="display:none">
						<TR>
							<td class="fieldName" nowrap="nowrap">Buyer:</td>
							<td nowrap align="left">	  
								<select name="customer" id="customer">
								<option value="">--Select All--</option>
								<?php
									foreach ($customerRecords as $cr) {
										$customerId	= $cr[0];
										$customerName	= stripslashes($cr[2]);
										$selected 	=  ($selCustomerId==$customerId)?"Selected":"";
								?>
								<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
								<? }?>
								</select>
								</td>
						</TR>
						<tr>
							<td class="fieldName" nowrap="nowrap">Fish Category:</td>
							<td nowrap align="left">
								<select name="fishCategory" id="fishCategory">
								<option value="">--Select All--</option>
								<?php
									foreach ($fishCategoryRecs as $fcr) {
										$fCategoryId	= $fcr[0];
										$fCategoryName	= stripslashes($fcr[1]);
										$selected 	=  ($selFishCategoryId==$fCategoryId)?"Selected":"";
								?>
								<option value="<?=$fCategoryId?>" <?=$selected?>><?=$fCategoryName?></option>
								<? }?>
								</select>
							</td>
							</tr>						
					</table>
					<!--</fieldset>-->
				</TD>
				<TD valign="top" nowrap="true">
				<fieldset>
					<table>
						<TR >
						<td class="fieldName" nowrap="nowrap">*Report Type:</td>
						<td nowrap align="left">
							<select name="reportType" id="reportType" onchange="disabPckStk(this.value)" >
								<!--<option value="">-- Select --</option>-->
								<?php
								foreach ($reportTypeArr as $rtKey=>$rtValue) {
									$selected = ($reportType==$rtKey)?"selected":"";
								?>
								<option value="<?=$rtKey?>" <?=$selected?>><?=$rtValue?></option>
								<?php
								}
								?>
							</select>
						</td>
						</TR>
						<tr  id="packType" style="<?=($reportType=="PRIR")?"display:none":""?>" >
							<td class="fieldName" nowrap="nowrap">*Pack Type:</td>
							<td nowrap align="left">
								<select name="packType" id="packType">
									<!--<option value="">-- Select --</option>-->
									<?php
									foreach ($packTypeArr as $ptKey=>$ptValue) {
										$selected = ($packType==$ptKey)?"selected":"";
									?>
									<option value="<?=$ptKey?>" <?=$selected?>><?=$ptValue?></option>
									<?php
									}
									?>
								</select>
							</td>
						</tr>
						<tr  id="stockType" style="<?=($reportType=="PRIR")?"display:none":""?>">
							<td class="fieldName" nowrap="nowrap">*Stock Type:</td>
							<td nowrap align="left">
								<select name="stockType" id="stockType">
								
								<?php
								
									foreach ($stockTypeArr as $stKey=>$stValue) {
										$selected = ($stocktype==$stKey)?"selected":"";
									?>
									<option value="<?=$stKey?>" <?=$selected?>><?=$stValue?></option>
									<?php
									}
									?>
									
								</select>
							</td>
						</tr>
					</table>
					</fieldset>
				</TD>

				
				<td valign="top" nowrap="true">
					<table>
						<TR>
							<TD style="padding-top:35px;">
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateDailyStockReport(document.frmDailyStockReport);">
							</TD>
						</TR>
					</table>
				    </td>	
			</tr>                          
                        </table>
		</td>
        </tr>
 		  <tr>
                        <td colspan="3"  height="10" class="listing-item">&nbsp;</td>
                  </tr>
                      <tr> 
                        <td colspan="3" class="listing-item" style="padding-left:10px; padding-right:10px;">
						
			<?php 
					  if ($searchMode)
						{
					?>


	<?	if (sizeof($dailyFrozenPackingRecs)>0) {
		$i	=	0;
	?>

	  <tr>                        
                        <td colspan="4" align="center">
			<?php 
				if($confirm==true && (sizeof($dailyFrozenPackingRecords)>0) && $singleDay!="" && $details) {
			?>
			<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm " <? if($confirmed || (sizeof($dailyFrozenPackingRecords)<=0)) echo "disabled";?> onclick="return validateUpdateFrozenPackingReport(document.frmFrozenPackingReport);">
			<? }?>	&nbsp;&nbsp;&nbsp;&nbsp;<? if ($searchMode==true){?>
                            <input type="button" name="Submit2" value=" View / Print" class="button" onclick="return printWindow('PrintdailystockreportSummary.php?stockFrom=<?=$sdateFrom?>&stockTo=<?=$selectDate?>&stocktype=<?=$stocktype?>&packType=<?=$packType?>&reportType=<?=$reportType?>&checkboxSel=<?=$checkboxSel?>',700,600);"
							
                          <? }?></td>
                      </tr>
					  <tr> 
                        <td colspan="3" align="center" height="5"></td>
                        </tr>
	<tr>
<td style="padding-left:10px; padding-right:10px;">
 <table width="200" border="1" cellpadding="1" cellspacing="0" align="center" >

<?
	if (sizeof($dailyFrozenPackingRecs)>0) {
		$i	=	0;
	?>
	 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+5;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>
			


			<tr   align="center" bgcolor="#f2f2f2">		
				<td  class="listing-head" style="padding-left:5px; padding-right:5px;" >ProcessCode</td>	
				<td  class="listing-head" style="padding-left:5px; padding-right:5px;" >Freezing Stage</td>
				<td  class="listing-head" style="padding-left:5px; padding-right:5px;" >
					Frozen Code
				</td>				
				<td  class="listing-head" style="padding-left:5px; padding-right:5px;" >MC Pkg</td>	
				<?php					
					foreach ($gradeRecs as $gR) {
						$gradeId = $gR[1];
						if (($stocktype==2) || ($stocktype==3))
						 {
				?>
				<td  class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px;"><?=$gradeId?></td>	
				<?php } else {?>				
				<td  class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px;"><?=$gradeId?></td>	
				<?php
				}}
				?>
				<td  class="listing-head" >TOTAL</td>	
			</tr>
			<tr bgcolor="#f2f2f2"  align="center">
			<td class="listing-head" style="padding-left:5px; padding-right:5px;border-right:1px solid #999999;">&nbsp;</td>
			<td class="listing-head">&nbsp;</td>
			<td class="listing-head">&nbsp;</td>
			<td class="listing-head">&nbsp;</td>
			<?php					
					foreach ($gradeRecs as $gR) {
					if ($reportType=="PRIR")
					{
					
					?>

					<td  class="listing-head" >Price per KGS</td>
					<?php }?>
        				 	


					<?php if ($stocktype==1) 
	 {?>
				<td  class="listing-head" >FS</td>
				<?php } 
					if ($stocktype==3)
	 {?>
				
				<td  class="listing-head" >AS</td>
				
				<?php } if ($stocktype==2){?>
				<td  class="listing-head" >TS</td>	
				<?php }?>
				<?php
					}
				?>
			<td  class="listing-head" >&nbsp;</td>	



			</tr>
	<?php	$totalStkPrice=0;	
			foreach ($dailyFrozenPackingRecs as $dfpr) {
			$i++;
			$dailyFrozenPackingMainId	= $dfpr[0];
			$selProcessCodeId	= $dfpr[3];
			$selProcessCode		= $dfpr[6];	
			$selFreezingStageId	= $dfpr[4];
			$selFreezingStage	= $dfpr[7];
			$selFrozenCodeId	= $dfpr[5];
			$selFrozenCode		= $dfpr[8];
			$reportConfirmed	= $dfpr[9];
			$allocatedCount		= $dfpr[10];
			$selMCPackingId		= $dfpr[11];
			$selMCPkgCode	    = $dfpr[12];
			$rnumMC="";
			$lnumLS="";
	?>	
	
			<tr   align="center" <?=$listRowMouseOverStyle?>>		
				<td  class="listing-item" nowrap width="90"  align="left" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;"><?=$selProcessCode?></td>	
				<td class="listing-item" nowrap width="90" align="left" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;"><?=$selFreezingStage?></td>
				<td class="listing-item" nowrap width="90" align="left" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >
					<?=$selFrozenCode?>
				</td>
			<td class="listing-item" nowrap width="80" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;"><?=$selMCPkgCode?>&nbsp;</td>
				<?php
				
				foreach ($gradeRecs as $gR) {
					$gradeId = $gR[0];
					list($gradeEntryId, $numMC, $numLS)=$dailyStockReportObj->getSlab($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					list($agradeEntryId, $anumMC, $anumLS)=$dailyStockReportObj->getallocateSlab($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					list($stkPrice)=$dailyStockReportObj->getPrice($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					list($tnumMC)=$dailyStockReportObj->getThaGradeQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromDate,$gradeId);
					//list($tnumMC)=$frozenStockAllocationObj->getThaGradeQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromDate,$gradeId);
					$totNumMCs = $numMC-($tnumMC+$anumMC);
					$totNumLSs = $numLS-($tnumLS+$anumLS);
					
					if($reportType=="PRIR")
					{
					?>
					<td  class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
					<? //list($stkPrice)=$dailyStockReportObj->getPrice($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					?>
					<?=($stkPrice!=0)?$stkPrice:"&nbsp;";
					if($stkPrice>0) $totalStkPrice+=$stkPrice;
					//echo $totalStkPrice;
					?></td>
					<?php }
        			 if ($stocktype==2)
				{?>
				<td  class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >

				<?php if ($packType=="MC")
					{?>
				<?=($numMC!=0)?$numMC:"&nbsp;";?>
				<?php 
				$rnumMC=$rnumMC+$numMC;
				
				
				
				} else if ($packType=="LS"){?>
				<?=($numLS!=0)?$numLS:"&nbsp;";?>
				<?php
				$lnumLS=$lnumLS+$numLS;
				
				}

				
				}  ?>
				</td>

				<?php  if ($stocktype==3) 
				{?>
				<td class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
				<?php if ($packType=="MC")
					{?>
				<?=($anumMC!=0)?$anumMC:"&nbsp;";?>
				<?php 
				$rnumMC=$rnumMC+$anumMC;
				
				} else if ($packType=="LS"){?>
				<?=($anumLS!=0)?$anumLS:"&nbsp;";?>
				<?php 
				$lnumLS=$lnumLS+$anumLS;
				
				} }?>
				</td>



				<?php  if ($stocktype==1){
				?>
				<td class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
				<?php if ($packType=="MC")
					{?>
				<?=($totNumMCs!=0)?$totNumMCs:"&nbsp;";?>				
				
				<?php $rnumMC=$rnumMC+$totNumMCs;} else if ($packType=="LS"){?>
				<?=($totNumLSs!=0)?$totNumLSs:"&nbsp;";?>	
				<?php } 
				$lnumLS=$lnumLS+$totNumLSs;
				
				
				}}?>
				</td>
				<td>
				<?php if ($packType=="MC")
					{?>
				<?=$rnumMC;?>
				<?php }else if ($packType=="LS"){?> <?=$lnumLS;?>  <?php }
				
				$totRSumMC=$totRSumMC+$rnumMC;
				$totLSumLS=$totLSumLS+$lnumLS;
				?></td>
						
			</tr>	
	<?php
			} // Main loopEnds here 				
	?>

	

<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+4;?>" style="padding-right:10px">&nbsp;</td><td>&nbsp;
			
			 <?
			if($reportType=="PRIR")
			{
				echo $totalStkPrice;
			}
			else if($reportType=="STKR")
			{
				if ($packType=="MC")
				{?>
				<?=$totRSumMC;?>
				<?php }
				else if ($packType=="LS")
				{?><?=$totLSumLS;?> 
				<?php
				} 
			}
			?>
			</td>
			
			</tr>

	
 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+5;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyStockReport.php?pageNo=$page&flag=1&frozenPackingFrom=$fromDate&frozenPackingTill=$tillDate&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>

	  


		</table>
</td>
</tr>
<?php }	 else 	{?>
	<tr bgcolor="white">
	<td colspan="14"  class="err1" height="11" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>

	<?php 
	}
	?>
	           
	<?php	if (sizeof($dailyFrozenPackingRecs) == 0) {
	?>
	<tr bgcolor="white">
		<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php 
		}
	?>

	</td>
        </tr>		
        
                      <tr> 
                        <td colspan="3" align="center" height="5"></td>
                        </tr>
						<?php
		} 
	
	
	
	// Main loopEnds here 
	?>
                      <tr>                        
                        <td colspan="4" align="center">
			<?php 
				if($confirm==true && (sizeof($dailyFrozenPackingRecords)>0) && $singleDay!="" && $details) {
			?>
			<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm " <? if($confirmed || (sizeof($dailyFrozenPackingRecords)<=0)) echo "disabled";?> onclick="return validateUpdateFrozenPackingReport(document.frmFrozenPackingReport);">
			<? }?>	&nbsp;&nbsp;&nbsp;&nbsp;<? if ($searchMode==true){?>
                            <input type="button" name="Submit2" value=" View / Print" class="button" onclick="return printWindow('PrintdailystockreportSummary.php?stockFrom=<?=$sdateFrom?>&stockTo=<?=$selectDate?>&stocktype=<?=$stocktype?>&packType=<?=$packType?>&reportType=<?=$reportType?>&checkboxSel=<?=$checkboxSel?>',700,600);"
							
                          <? }?></td>
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                    </table>
									</td>
								</tr>
							</table>
						
				<!-- Form fields end   -->
			</td>
		</tr>		
	</table>

	</td>	
	</tr>
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
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
			inputField  : "dateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTo", 
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