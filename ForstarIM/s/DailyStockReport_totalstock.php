<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";	
	$searchMode	= false;	
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
	# Drive to Daily Thawing
	if ($p["cmdAddDailyThawing"]) {
		$listRec = $_SESSION["listRec"];
		$stkReportPC = $_SESSION["stkReportPC"];
		$stkReportGrade = $_SESSION["stkReportGrade"];
		$_SESSION["stkTillDate"] = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-1,$dateC[2]));
		header("Location:DailyThawing.php?STOCKREPORT=Y");
		exit;
		}
		$maxdate= $dailyfrozenpackingObj->getMaxDate();
		$maximumdt= dateFormat($maxdate[0]);
		$dateFrom=($maximumdt!="")?$maximumdt:date("d/m/Y");
		$dateTo = date("d/m/Y");
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTo);
		# Search starts Here
		if ($dateFrom && $dateTo) {
		# Get Selected Fish
		$getFishRecords = $dailyStockReportObj->getFishRecords($fromDate, $tillDate);
		if ($p["cmdSearch"]!="") {
			$searchMode = true;
			$stkGroupListEnabled = false;		
			$gradeRecs = $dailyStockReportObj->getProductionGradeRecs($fromDate, $tillDate);			
			$fromDate = mysqlDateFormat($dateFrom);
			$tillDate = mysqlDateFormat($dateTo);
		 #dailyfrozenreport starts					  
		## -------------- Pagination Settings I ------------------
		if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
		else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
		else $pageNo=1;	
		$offset = ($pageNo - 1) * $limit; 
		## ----------------- Pagination Settings I End ------------
		$dailyFrozenPackingRecs = $dailyStockReportObj->getPagingDFPRecs($fromDate, $tillDate, $offset, $limit);		
		$numrows	=  sizeof($dailyStockReportObj->getDFPForDateRange($fromDate, $tillDate));
		$stocktype=$p[stockType];
		}
		}	
	# Customer Records
	$customerRecords = $customerObj->fetchAllRecords();
	# Fish Category Recs
	$fishCategoryRecs = $fishcategoryObj->fetchAllRecords();
	# Define Array
	$reportTypeArr 	= array("STKR"=>"Stock Report", "PRIR"=>"Price Report");
	$packTypeArr 	= array("MC"=>"MC", "LS"=>"Loose Slab");
	$stockTypeArr 	= array("1"=>"Total Stock","2"=>"Free Stock","3"=>"Allocated Stock");
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
								<input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateTo?>" autocomplete="off" onchange="submitForm('dateFrom','dateTo', frmDailyStockReport)" readonly="readonly" />
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
						<TR>
						<td class="fieldName" nowrap="nowrap">*Report Type:</td>
						<td nowrap align="left">
							<select name="reportType" id="reportType">
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
						<tr>
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
						<tr>
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
                            <input type="button" name="Submit2" value=" View / Print" class="button" onclick="return printWindow('PrintdailystockreportSummary.php?stockFrom=<?=$dateFrom?>&stockTo=<?=$dateTo?>&stocktype=<?=$stocktype?>',700,600);"
							
                          <? }?></td>
                      </tr>
					  <tr> 
                        <td colspan="3" align="center" height="5"></td>
                        </tr>
	<tr>
<td style="padding-left:10px; padding-right:10px;">
 <table width="200" border="1" cellpadding="1" cellspacing="0" align="center" >
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
				<td  class="listing-head" colspan=3 style="padding-left:5px; padding-right:5px;"><?=$gradeId?></td>	
				<?php
				}}
				?>
			</tr>
			<tr bgcolor="#f2f2f2"  align="center">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<?php					
					foreach ($gradeRecs as $gR) {				
					if (($stocktype==2) || ($stocktype==1))
	 {?>
				<td  class="listing-head" >FS</td>
				<?php } 
					if (($stocktype==3) || ($stocktype==1))
	 {?>
				
				<td  class="listing-head" >AS</td>
				
				<?php } if ($stocktype==1){?>
				<td  class="listing-head" >TS</td>	
				<?php }?>
				<?php
					}
				?>
			</tr>
	<?php		
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
					$totNumMCs = $numMC+$anumMC;							
				if (($stocktype==2) || ($stocktype==1))
				{?>
				<td  class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
				<?=($numMC!=0)?$numMC:"&nbsp;";?>		
				</td>
				<?php }
				if (($stocktype==3) || ($stocktype==1))
				{?>
				<td class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
				<?=($anumMC!=0)?$anumMC:"&nbsp;";?>
				<?php }?>				
				</td>
				<?php 
				if ($stocktype==1){
				?>
				<td class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
				<?=($totNumMCs!=0)?$totNumMCs:"&nbsp;";?>				
				</td>
				<?php }?>
				<?php }?>			
		</tr>	
	<?php
		} // Main loopEnds here 				
	?>
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
	           
     

	</td>
        </tr>		
        
                      <tr> 
                        <td colspan="3" align="center" height="5"></td>
                        </tr>
                      <tr>                        
                        <td colspan="4" align="center">
			<?php 
				if($confirm==true && (sizeof($dailyFrozenPackingRecords)>0) && $singleDay!="" && $details) {
			?>
			<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm " <? if($confirmed || (sizeof($dailyFrozenPackingRecords)<=0)) echo "disabled";?> onclick="return validateUpdateFrozenPackingReport(document.frmFrozenPackingReport);">
			<? }?>	&nbsp;&nbsp;&nbsp;&nbsp;<? if ($searchMode==true){?>
                            <input type="button" name="Submit2" value=" View / Print" class="button" onclick="return printWindow('PrintdailystockreportSummary.php?stockFrom=<?=$dateFrom?>&stockTo=<?=$dateTo?>&stocktype=<?=$stocktype?>',700,600);"
							
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