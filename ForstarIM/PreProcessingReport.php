<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;
	$checked	= "";
	$checked1	=  "";
	$checked2	= "";
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# FInd DailyProcessinjgRecords based on Date
	$selFishId		=	$p["fish"];	
	$processId		=	$p["preProcessCode"];
	
	if ($p["details"]!="") $details	= $p["details"];
	if ($p["summary"]!="") $summary	= $p["summary"];
	if ($p["qtySummary"]!="") $qtySummary = $p["qtySummary"];	

	if ($p["details"]!="") {
		$checked1 = "Checked";
		$checked2 = "";
	} else if ($p["summary"]!="") {
		$checked2 = "Checked";
		$checked1 = "";
	} else {
		$checked1 = "";
		$checked2 = "";
	}

	if ($qtySummary!="") $qtySummaryChk = "checked";
	
	$dateFrom 	= $p["dateFrom"];
	$dateTo 	= $p["dateTo"];
		
	$selProcessorId = $p["selPreProcessor"];
	$preProcessorName = $preprocessorObj->findPreProcessor($selProcessorId);
	
	if ($dateFrom!="" && $dateTo !="") {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTo);

		# List All Fishes
		$fishMasterRecords = $preprocessingreportObj ->fetchFishRecords($fromDate, $tillDate);
		
		# Pre-Process Code Records
		$preProcessCodeRecords = $preprocessingreportObj->getPreProcessCodeRecords($fromDate, $tillDate, $selFishId);	
		
		# For Drop down Lisitng
		$distinctPreProcessorLisit = $preprocessingreportObj->fetchDistinctPreProcessorRecords($fromDate, $tillDate, $selFishId, $processId);
	}

	# Search starts Here
	if ($p["cmdSearch"]!="") {
		$preProcessorRecords = $preprocessingreportObj->getPreProcessorRecords($fromDate, $tillDate, $selProcessorId);
	
		$preProcessingReportRecords = $preprocessingreportObj->filterDailyPreProcessingRecords($fromDate, $tillDate, $selFishId, $processId, $selProcessorId, $details, $summary);
		
		# PRE-PROCESS SUMMARY
		$preProcessingSummaryRecords = $preprocessingreportObj-> getPreProcessingSummaryRecords($fromDate, $tillDate, $selFishId, $processId);
		
		$preProcessorSummaryRecords = $preprocessingreportObj->getPreProcessorSummaryRecords($fromDate, $tillDate, $selFishId, $processId, $selProcessorId);	
		
		if ($selProcessorId && ($details!="" || $summary!="")) {
			$getAllPreProcessingRecords =  $preprocessingreportObj->filterPreProcessingRecords($fromDate, $tillDate, $selFishId, $processId, $selProcessorId, $details, $summary);
		}		
	}	

	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();

	# Display heading
	if ($editMode)	$heading = $label_editPreProcessingReport;	
	else		$heading = $label_addPreProcessingReport;

	
	$ON_LOAD_PRINT_JS	= "libjs/preprocessingreport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPreProcessingReport" action="PreProcessingReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
									<td colspan="2"  align="center">
					<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="10" >&nbsp;</td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value="  Print Pre-Processor Memo" class="button" onClick="return printWindow('PrintPreProcessingReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&selPreProcessor=<?=$selProcessorId?>&details=<?=$details?>&fish=<?=$selFishId?>&preProcessCode=<?=$processId?>&summary=<?=$summary?>',700,600);" <? if(sizeof($getAllPreProcessingRecords)==0) echo "disabled";?> style="width:170px;">&nbsp;&nbsp;<input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintPreProcessingSummary.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&qtySummary=<?=$qtySummary?>&selPreProcessor=<?=$selProcessorId?>&details=<?=$details?>&fish=<?=$selFishId?>&preProcessCode=<?=$processId?>&summary=<?=$summary?>',700,600);" <? if (sizeof($preProcessingSummaryRecords)==0 && $details=="" &&	$summary=="" && $selProcessorId=="") echo "disabled";?> ><? }?></td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap class="fieldName" ></td>
                        </tr>
                      <tr>
                        <td colspan="3" nowrap class="fieldName"  align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="3" nowrap align="center"><table width="200" border="0">
                          <tr>
                            <td valign="top"><table width="125" border="0">
                              <tr>
                                <td class="fieldName">From:</td>
                                <td nowrap><? $dateFrom = $p["dateFrom"];?>
                                    <input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('dateFrom','dateTo', frmPreProcessingReport);" autocomplete="off" /></td>
                              </tr>
                              <tr>
                                <td class="fieldName">To:</td>
                                <td nowrap><? $dateTo = $p["dateTo"];?>
                                    <input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateTo?>" onchange="submitForm('dateFrom','dateTo', frmPreProcessingReport);" autocomplete="off" /></td>
                              </tr>
                            </table></td>
                            <td valign="top"><table width="200" border="0">
                              <tr>
                                <td class="fieldName">Fish:</td>
                                <td nowrap align="left">
				<select name="fish" onchange="this.form.submit();">
                                        <option value="">--Select--</option>
                                           <?
						foreach($fishMasterRecords as $fr) {
							$Id		=	$fr[0];
							$fishName	=	stripSlash($fr[1]);
							$selected	=	"";
							if ( $selFishId==$Id) $selected	="selected";
					?>
                                         <option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
                                                   <? }?>
                                      </select></td>
                              </tr>
                              <tr>
                                <td class="fieldName" nowrap>Pre-Process Code:</td>
                                <td nowrap="true" align="left">
					<? $processId	=	$p["preProcessCode"];?>
					<select name="preProcessCode" id="preProcessCode" onchange="this.form.submit();">
                                         <option value="">-- Select --</option>
                                        <?
					foreach ($preProcessCodeRecords as $fl) {
						$processCodeId		=	$fl[0];
						$processCode		=	$fl[1];
						$selected	=	"";
						if ( $processId==$processCodeId) $selected	=	"selected";
					?>
					<option value="<?=$processCodeId;?>" <?=$selected;?> >
                                                 <?=$processCode;?>
                                      </option>
                                                          <?
															}
															?>
                                      </select></td>
                              </tr>
                              <tr>
                                <td class="fieldName">Pre-Processor:</td>
                                <td nowrap="true" align="left">
				<select name="selPreProcessor" id="selPreProcessor" >
					<option value="">-- Select All --</option>
					<?
						foreach ($distinctPreProcessorLisit as $dppr)
							{
								$preProcessorId		=	$dppr[0];
								$processorName	=	stripSlash($dppr[1]);
								$selected = "";
								if($selProcessorId==$preProcessorId) $selected = "Selected";
					?>
					<option value="<?=$preProcessorId?>" <?=$selected?>><?=$processorName?></option>
										<? }?>
                                        </select></td>
                              </tr>
                            </table></td>
                            <td valign="top">&nbsp;</td>
                            <td valign="top"><table width="200" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td valign="top">
				<fieldset>
                                        <legend class="fieldName">Search Options </legend>
				<table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td align="center"><input type="checkbox" name="details" id="details" value="Y" <?=$checked1?> class="chkBox" onclick="hideSummaryOption();"></td>
                                    <td class="listing-item">Detailed</td>
                                    </tr>
                                  <tr>
                                    <td align="center"><input name="summary" type="checkbox" class="chkBox" id="summary" onclick="hideDetailedOption();" value="Y" <?=$checked2?>></td>
                                    <td class="listing-item">Summary</td>
                                  </tr>
				  <tr>
                                    <td align="center">
					<input name="qtySummary" type="checkbox" class="chkBox" id="qtySummary" onclick="hideOtherOption();" value="Y" <?=$qtySummaryChk?>></td>
                                    <td class="listing-item" nowrap="true">Qty Summary</td>
                                  </tr>	
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td class="listing-item">
					<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validatePreProcessingReport(document.frmPreProcessingReport);">
				     </td>
                                  </tr>
				<tr><TD height="5"></TD></tr>
                                </table>
                                    </fieldset></td>
                                  </tr>
                                </table></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr> 
                        <td colspan="3" nowrap align="center">
			<!--Here end-->
			</td>
                        </tr>
                      <tr> 
                        <td colspan="3" nowrap class="fieldName" > </td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap > </td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap ></td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr>
                      <tr>
                        <td colspan="3"  height="5"></td>
                      </tr>
                      <tr> 
                        <td colspan="3"  height="10" class="listing-item" ><table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <? if($err!="" ){ ?>
                            <? }?>
                        <?
				$totalPreProcessCost	= "";
				if (sizeof($preProcessingReportRecords) && $selProcessorId=="" && ($details!="" || $summary!="")) {
			?>
                            <tr bgcolor="#FFFFFF"> 
                              <td width="85%" colspan="17" align="center" style="padding-left:10px;padding-right:10px;"> 
                                <table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
                                  <tr align="left" bgcolor="#FFFFFF">
                                    <td colspan="<?=(9+sizeof($preProcessorRecords))?>" class="fieldName">&nbsp;<? if ($details!="") {?>Detailed View<? } else if ($summary!="") {?>Summary View <? }?></td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center"> 
				    <? if($details!=""){?>
                            		<td  class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
				    <? }?>	
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Pre-Process Code </td>
                                    <?
					foreach ($preProcessorRecords as $pr)
					{
						$processorName	=	stripSlash($pr[1]);
				    ?>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="50px">
						<?=$processorName?>
					</td>
					<? }?>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total PreProcessed Qty</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual Yield </td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Diff. Yield </td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate <br>Amt</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Commn <br>Amt</td>
					<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total Amt </td>
					</tr>
                                  <?
					$prevFishId = 0;	
					$prevProcessId	=	0;
					$prevRecDate = "";				 
					foreach ($preProcessingReportRecords as $ppr) {
						$fishId			=	$ppr[1];
						$fishName		= "";
						if ($prevFishId!=$fishId){
							$fishRec	=	$fishmasterObj->find($ppr[1]);
							$fishName	=	$fishRec[1];
						}
						$enteredDate	=	"";
						$recDate	= $ppr[2];	
						if ($prevRecDate!=$recDate) {
							$enteredDate	= dateFormat($recDate);
						}
						$preProcessId		=	stripSlash($ppr[4]);
						$processRec		=	$processObj->find($preProcessId);
						//$preProcessCommission	=	$processRec[5];
						//$preProcessCriteria	=	$processRec[6];
						$preProcessCode		=	$processRec[7];			
					
						//$processRate		=	$dailypreprocessObj->findProcessRate($preProcessId);		
						//$commissionRate		=	$preprocessingreportObj->findCommissionRate($preProcessId);	
					
						$dailyPreProcessEntryId	= 	$ppr[3];	
						$totalArrivalQty	=	$ppr[7];
						$totalPreProcessedQty	=	$ppr[8];
								
						$actualYield		=	$ppr[9];
						$idealYield		=	$ppr[10];			
						#Count Number of Pre-Process
						$numPreProcess	=  $ppr[12];
						if ($summary!="") {					
							$actualYield 	=	number_format(($actualYield/$numPreProcess),2,'.','');
							$idealYield	=	number_format(($idealYield/$numPreProcess),2,'.','');
						} 								
						$diffYield = number_format(($actualYield-$idealYield),2);
						$IYield	  = ($idealYield/100);
						$aYield	  = ($actualYield/100);						
						#Criteria Calculation 1=> From/ 0=>To
							
				$totalPreProcessAmt = 0;
				$calcRateAmt = 0;
				$calcCommiAmt = 0;
				$cnt = 0;
				foreach ($preProcessorRecords as $ppr)
				{
					$mPProcessorId	=	$ppr[0];
					$ppQty = "";
					if ($details!="") {
						$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId, $mPProcessorId);
						$ppQty	=	$preProcessorRec[3];
					} else {
						$preProcessorHOQty = $preprocessingreportObj->findPreProcessorHOQty($fishId, $preProcessId, $fromDate, $tillDate, $mPProcessorId);
						$ppQty	=	$preProcessorHOQty[1];	
					}
					$preProcessorAmt = 0;
					if ($ppQty!=0) {
						$cnt++;
						list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $mPProcessorId);
						//echo "$ppeRate, $ppeCommission, $ppeCriteria";						
						$selPPRate = $ppeRate;
						$calcRateAmt += $selPPRate;
						$selPPCommi = $ppeCommission;
						$calcCommiAmt += $selPPCommi;
						$selPPCriteria = $ppeCriteria;
						$selRateAmount = 0;
						$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
						if ($selPPCriteria==1) {
					//if (From) and actual yield> ideal yield  then yield=actual yield
							if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
								$preProcessorAmt = ($ppQty/$aYield)*$selPPRate+$ppQty*$selPPCommi;
								 $selRateAmount = number_format(($ppQty/$aYield)*$selPPRate,2,'.','');
							} else {
								$preProcessorAmt = ($ppQty/$IYield)*$selPPRate+ $ppQty*$selPPCommi;
								$selRateAmount = number_format(($ppQty/$IYield)*$selPPRate,2,'.','');
							}					
						} else {
							$preProcessorAmt = $ppQty*$selPPRate+$ppQty*$selPPCommi;
							#Calc Rate Amount {To)
							$selRateAmount = number_format(($ppQty*$selPPRate),2,'.','');
						}
						$totalPreProcessAmt += $preProcessorAmt;
						//echo $selRateAmount;
						//$calcRateAmt += $selRateAmount;
						$selCommiAmt =	number_format(($ppQty*$selPPCommi),2,'.','');
						//$calcCommiAmt += $selCommiAmt;
					} // Qty check ends here
				} // Pre Processor Loops Ends here
					//echo "$ppeRate, $ppeCommission<br>";
					$calcRateAmt = ($calcRateAmt/$cnt);
					$calcCommiAmt = ($calcCommiAmt/$cnt);
					$totalPreProcessCost+=$totalPreProcessAmt;
					?>
                                  <tr bgcolor="#FFFFFF"> 
				     <? if($details!=""){?>
                            		<td  class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$enteredDate?>&nbsp;</td>
				     <? }?>	
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
                                    <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$preProcessCode?></td>
                                    <?
					foreach ($preProcessorRecords as $pr)
					{
						$masterPreProcessorId	=	$pr[0];
						if ($details!="") {
							$preProcessorRec = $dailypreprocessObj->findPreProcessorRec($dailyPreProcessEntryId, $masterPreProcessorId);
							$preProcessorQty	=	$preProcessorRec[3];
						} else {
							$preProcessorHOQty = $preprocessingreportObj->findPreProcessorHOQty($fishId, $preProcessId, $fromDate, $tillDate, $masterPreProcessorId);
							$preProcessorQty	=	$preProcessorHOQty[1];	
						}
					?>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessorQty?></td>
									<? }?>
								    <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$totalPreProcessedQty?></td>
								    
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$actualYield?></td>
									
									
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$diffYield?></td>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=number_format($calcRateAmt,2,'.','');//$processRate?></td>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=number_format($calcCommiAmt,2,'.','');//$commissionRate?></td>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($totalPreProcessAmt,2);?></td>
                                  </tr>
                                  <?
								  $prevFishId 		= 	$fishId;
								 // $prevProcessId	=	$processes;
								$prevRecDate=$recDate;
								   }?>
								  <tr bgcolor="white">
								<? if($details!=""){ ?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:10px;">&nbsp;</td>
			<? }?>
								  <td>&nbsp;</td>
								  <td class="listing-head" align="center">&nbsp;</td>
								  <?
									foreach ($preProcessorRecords as $pr)
											{
										$processorName	=	stripSlash($pr[1]);
									
									?>
								  <td class="listing-head" align="center">&nbsp;</td><? }?>
								  <td align="right" class="listing-item">&nbsp;</td>
								  
								  <td align="right" class="listing-item">&nbsp;</td>
								  
								  <td align="right" class="listing-head" style="padding-right:10px;">&nbsp;</td>
								  <td align="right" class="listing-item">&nbsp;</td>
								  <td align="right"><span class="listing-head" style="padding-right:10px;">Total:</span></td>
								  <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalPreProcessCost,2);?></strong></td>
								  </tr>
								</table>							  </td>
                            </tr>
							<? 
							} else if($dateFrom!="" && $dateTo!="" && sizeof($getAllPreProcessingRecords)<0) {?>
	  <tr bgcolor="white"> 
      <td  class="err1" height="5" align="center" colspan="17"><?=$msgNoPreProcess;?></td>
    </tr>
	<? }?>
         <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
  </table></td>
    </tr>
	<? if (sizeof($getAllPreProcessingRecords)>0) { ?>
                      <tr>
                        <td colspan="4" align="center">
						<table width="100%">
						<tr>
						<td><table width="100" cellpadding="0" cellspacing="0">
                              <tr>
                                <td colspan="2" height="5"></td>
                                </tr>
                              <tr>
                                <td class="fieldName" nowrap="nowrap">Pre-Processor:</td>
					      <td class="listing-item" nowrap="nowrap"><b><?=$preProcessorName?></b> </td>
                              </tr>
                            </table></td>
						</tr>
						<tr><td><table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
                          <tr bgcolor="#f2f2f2" align="center">
						  <? if($details!=""){?>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
						 <? }?>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Pre-Process code </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Arrival Qty</td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">PreProcessed Qty</td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Actual<br /> Yield</td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Diff <br />Yield </td>
			    <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Final <br />Yield </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Base Rate </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Base Commn </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Rate<br />Amt </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Commn <br />Amt </td>
                            <td  class="listing-head" style="padding-left:5px; padding-right:5px;">Total <br />Amt </td>
                          </tr>
			<?
				$prevFishId = 0;
				$prevProcessId	=	0;
				$totalCost = 0;
				$totalArrivalQty = 0;
				$totalPreProcessorQty = 0;
				$totalRateAmount = 0;
				$totalCommissionAmt = 0;
				foreach ($getAllPreProcessingRecords as $ppr) {
					$fishId			=	$ppr[1];
					$fishName		= "";
					if ($prevFishId!=$fishId) {
						$fishRec		=	$fishmasterObj->find($ppr[1]);
						$fishName		=	$fishRec[1];
					}
							
					$recDate		=	$ppr[2];
					$enteredDate	=	"";
					//|| $prevFishId!=$fishId removed on 12-11-07
					if ($details!="") {
						if ($prevRecDate!=$recDate) {
							$array 		= explode("-",$recDate);
							$enteredDate	= $array[2]."/".$array[1]."/".$array[0];
						}
					} else {
						if ($prevRecDate!=$recDate) {
							$array		= explode("-",$recDate);
							$enteredDate	= $array[2]."/".$array[1]."/".$array[0];
						}
					}
							
					$preProcessId		=	stripSlash($ppr[4]);

					$processRec		=	$processObj->find($preProcessId);
					
					//Rekha commented date 22 june 2018
					//$processRec		=	$processObj->findProcess($preProcessId);
					//echo $preProcessCriteria	;
					$preProcessCriteria		=	$processRec[6];
					$preProcessCode			=	$processRec[7];
							
					$baseRate			=	$processRec[4];
					$baseCommission			=	$processRec[5];
							
					$processorCommission 	= $ppr[12];				
					$processorRate		= $ppr[13];
					# Exception Pre Processors
					list($ppeRate, $ppeCommission, $ppeCriteria, $ppYieldTolerance) = $dailypreprocessObj->getPProcessorExpt($preProcessId, $selProcessorId);
				
				
				///echo "hii".$processorRate;		
					
					if ($processorRate!=0) {
						$processRate	=	$processorRate;
						$preProcessCriteria = $preProcessCriteria;
					} else {					
						$processRate = $ppeRate;
						$preProcessCriteria = $ppeCriteria;
					}
					if ($processorCommission!=0) {
						$preProcessCommission	=	$processorCommission;
					} else {
						$preProcessCommission	= $ppeCommission;
					}
							
					#Count Number of Pre-Process
					$numPreProcess	=  $ppr[19];
														
					$totalPreProcessedQty	=	$ppr[8];
					//echo "hii".$summary;								
					if ($summary!="") {
						$selProcessRate 	=	number_format(($processRate/$numPreProcess),2,'.','');
						$selProcessCommission	=	number_format(($preProcessCommission/$numPreProcess),2,'.','');
								
						$actualYield		=	number_format(($ppr[9]/$numPreProcess),2,'.','');
						$idealYield		=	number_format(($ppr[10]/$numPreProcess),2,'.','');
					} else {
						$selProcessRate 	=	$processRate;
						$selProcessCommission	=	$preProcessCommission;
								
						$actualYield		=	$ppr[9];
						$idealYield		=	$ppr[10];
					}
							
					$diffYield	=	number_format(($actualYield-$idealYield),2);
					$aYield	  = ($actualYield/100); 
					$IYield	  = ($idealYield/100);
							
					$preProcessorQty = $ppr[18];
					$totalPreProcessorQty +=$preProcessorQty;				
					$arrivalQty = number_format(($preProcessorQty/$aYield),2);	
					$totalArrivalQty += $arrivalQty; //Find the total Arrival Qty

					#Criteria Calculation 1=> From/ 0=>To
					$finalYield = "";
					$yieldTolerance = ($ppYieldTolerance!=0)?$ppYieldTolerance:$defaultYieldTolerance;
					//		echo "hii".$preProcessCriteria;
					if ($preProcessCriteria==1) {
						//if (From) and actual yield> ideal yield  then yield=actual yield edited on 15-1-08
						if ($actualYield>$idealYield && $diffYield<$yieldTolerance) {
							$totalPreProcessAmt 	=	($totalPreProcessedQty/$aYield)*$processRate + $totalPreProcessedQty * $preProcessCommission;
							#Calc Rate Amount (FROM)
							$rateAmount = number_format(($preProcessorQty/$aYield)*$selProcessRate,2,'.','');
							//echo "hii".$rateAmount;
							$finalYield = $actualYield;
						} else {
							$totalPreProcessAmt 	=	($totalPreProcessedQty/$IYield) *$processRate + $totalPreProcessedQty * $preProcessCommission;

							#Calc Rate Amount (FROM)
							$rateAmount = number_format(($preProcessorQty/$IYield)*$selProcessRate,2,'.','');
							$finalYield = $idealYield;
						}
									
					} else {
							$totalPreProcessAmt		=	$totalPreProcessedQty*$processRate + $totalPreProcessedQty * $preProcessCommission;
							#Calc Rate Amount {To)
							$rateAmount = number_format(($preProcessorQty*$selProcessRate),2,'.','');
							$finalYield = $idealYield;
					}		
		
					$KgRate		=	 $totalPreProcessAmt/$totalPreProcessedQty;
							
					$commissionAmount =	number_format(($preProcessorQty*$selProcessCommission),2,'.','');

					$totalRateAmount += $rateAmount;
					$totalCommissionAmt += $commissionAmount;
							
					if ($ppr[14]!="") $PreProcessorAmt   = $ppr[14];
					else $PreProcessorAmt = $preProcessorQty*$KgRate;
					$totalCost+=$PreProcessorAmt;
				?>
                          <tr bgcolor="#FFFFFF">
						  <? if($details!=""){?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$enteredDate?>&nbsp;</td>
							<? }?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$fishName?></td>
                            <td  class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$preProcessCode?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$arrivalQty?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessorQty?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$actualYield?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$diffYield?></td>
			    <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$finalYield?></td>	
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$selProcessRate?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$selProcessCommission?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$rateAmount?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$commissionAmount?></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($PreProcessorAmt,2);?></td>
                          </tr>
			<?
				$prevFishId=$fishId;
				$prevRecDate	=	$recDate;
			}
			?>
                        <tr bgcolor="#FFFFFF">
			<? if($details!=""){ ?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:10px;">&nbsp;</td>
			<? }?>
                            <td  class="listing-item" nowrap="nowrap" style="padding-left:10px;">&nbsp;</td>
                            <td  class="listing-head" style="padding-right:10px;" align="right">Total:</td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalArrivalQty,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalPreProcessorQty,2);?></strong></td>
			    <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>	
                            <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-head" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-right:10px;">&nbsp;</td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRateAmount,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalCommissionAmt,2);?></strong></td>
                            <td  class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalCost,2);?></strong></td>
                          </tr>
                        </table></td></tr>
			</table>
		</td>
                      </tr>
			  <? } else if (!sizeof($getAllPreProcessingRecords) && $selProcessorId!="" && ($details!="" || $summary!="")) {?>
			
			<tr bgcolor="white"> 
     				 <td  class="err1" height="5" align="center" colspan="17">No Settlement Records found.</td>
   			 </tr>
			<? }?>
		  <!-- Her-->
                      <tr>
                        <td colspan="2" align="center">&nbsp;</td>
                        <td align="center" colspan="2">&nbsp;</td>
                      </tr>
<? 	
	if (sizeof($preProcessorSummaryRecords) && $qtySummary!="") {
?>
        <tr>
             <td colspan="4" align="center">
		<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
                	<tr align="left" bgcolor="#FFFFFF">
			      <td colspan="<?=(3+(2*sizeof($preProcessorSummaryRecords)))?>" class="fieldName">&nbsp;PRE-PRPCESS SUMMARY<br />&nbsp;FOR THE PERIOD FROM <?=$dateFrom?> TO <?=$dateTo?> </td>
                        </tr>
                        <tr bgcolor="#f2f2f2" align="center">
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Variety</td>
                                    <td class="listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" colspan="<?=sizeof($preProcessorSummaryRecords)+1;?>">HO QTY</td>
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="<?=sizeof($preProcessorSummaryRecords)+1;?>">PEELED QTY</td>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <?php
					foreach ($preProcessorSummaryRecords as $pr) {
						$processorName	=	stripSlash($pr[1]);
					?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="50px"><?=$processorName?></td>
				<? }?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total </td>
				<?
					foreach ($preProcessorSummaryRecords as $pr)
					{
						$processorName	=	stripSlash($pr[1]);
				?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;" width="50px"><?=$processorName?> </td>
				<? }?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total </td>
			        </tr>
                                  <?
					$prevFishId = 0;	
					$prevProcessId	=	0;				 
					foreach ($preProcessingSummaryRecords as $ppr) {
						$fishId			=	$ppr[1];
						$fishName		= "";
						if ($prevFishId!=$fishId) { 
							$fishRec = $fishmasterObj->find($ppr[1]);
							$fishName	=  $fishRec[1];
						}
						$preProcessId		=	stripSlash($ppr[4]);
						$processRec		=	$processObj->find($preProcessId);
						$preProcessCode			=	$processRec[7];
						$totalPreProcessedQty	=	$ppr[8];
				?>
                                  <tr bgcolor="#FFFFFF"> 
                                    <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$preProcessCode?></td>
                                    <?
										$totalPreProcessQty = "";
										foreach ($preProcessorSummaryRecords as $pr)
											{
										$masterPreProcessorId	=	$pr[0];
										
										$preProcessorHOQty	=	$preprocessingreportObj->findPreProcessorHOQty($fishId,$preProcessId,$fromDate,$tillDate,$masterPreProcessorId);
										//printr($preProcessorHOQty);
										$preProcessQty 	=  number_format(($preProcessorHOQty[0]),'2','.','');
										//echo $preProcessQty;
										$totalPreProcessQty +=	$preProcessQty;			
									?>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessQty?></td>
									<? }?>
								    <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><b><? echo number_format($totalPreProcessQty,'2','.','');?></b></td>
								    <?php
									$preProcessorQty = "";
									$totalPeeledQty = 0;	
									foreach ($preProcessorSummaryRecords as $pr) {
										$masterPreProcessorId	=	$pr[0];						
										$preProcessorHOQty	=	$preprocessingreportObj->findPreProcessorHOQty($fishId,$preProcessId,$fromDate,$tillDate,$masterPreProcessorId);
										$preProcessorQty	=	$preProcessorHOQty[1];										
										//$preProcessorQty	=	$preProcessorHOQty[3];	
										$totalPeeledQty += $preProcessorQty;
										//$totalPreProcessedQty	edited20								
									?>
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($preProcessorQty,'2','.','');?></td>
									<? }?>
									
									<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;">
										<b><? echo number_format($totalPeeledQty,'2','.','');?></b>
									</td>
								
                                  <?								  
								  $prevFishId 		= 	$fishId;
								 
								   }?>
								  <tr bgcolor="white">
								  <!--td>&nbsp;</td-->
								  <td class="listing-head" align="right" >Grand Total:</td>
								  <?
								  $actualYield = "";
								  $preProcessedQty = "";
								  $preProcessQty = "";
								  $totalPreProcessQty = "";
								  $grandTotalSummaryQty = "";
								  $eachRecordYield = "";
									foreach ($preProcessorSummaryRecords as $pr)
										{
										$masterPreProcessorId	=	$pr[0];
										//$processorName			=	stripSlash($pr[1]);
										$preProcessSummary = $preprocessingreportObj->getPreProcessorProcessedQty($fromDate,$tillDate,$masterPreProcessorId, $selFishId, $processId);
										$grandTotalPreProcessQty = "";
										foreach ($preProcessSummary as $pps)
										{			
											$preProcessQty 	= $pps[0];
											$grandTotalPreProcessQty += $preProcessQty;		
										}
										
										$grandTotalSummaryQty +=$grandTotalPreProcessQty;
									?>
								  <td class="listing-head" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($grandTotalPreProcessQty,'','.','');?></td>
								  <? }?>
								  <td align="right" class="listing-head" style="padding-left:5px; padding-right:5px;"><? echo number_format($grandTotalSummaryQty,'','.','');?></td>
								  <?
								  $actualYield = "";
								  $preProcessedQty = "";
								  $preProcessQty = "";
								  $totalPreProcessQty = "";
								  $grandTotalSummaryQty = "";
								  $grandTotalPreProcessedQty = "";
									foreach ($preProcessorSummaryRecords as $pr)
										{
										$masterPreProcessorId	=	$pr[0];
										
										$preProcessSummary = $preprocessingreportObj->getPreProcessorProcessedQty($fromDate,$tillDate,$masterPreProcessorId, $selFishId, $processId);
										$grandTotalPreProcessedQty = "";
										foreach ($preProcessSummary as $pps)
										{
											$preProcessedQty	= $pps[1];				
										$grandTotalPreProcessedQty +=	$preProcessedQty;		
										}
										
										$grandTotalSummaryQty +=$grandTotalPreProcessedQty;
									?>
								  <td align="right" class="listing-head" style="padding-left:5px; padding-right:5px;"><? echo number_format($grandTotalPreProcessedQty,'','.','');?></td>
								  <? }?>
								  <td align="right" class="listing-head" style="padding-left:5px; padding-right:5px;"><? echo number_format($grandTotalSummaryQty,'','.','');?></td>								 
								  </tr>
								</table></td>
                      </tr>
					  <? }?>
                      <tr> 
                        <td colspan="4" align="center">&nbsp;</td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value="  Print Pre-Processor Memo" class="button" onClick="return printWindow('PrintPreProcessingReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&selPreProcessor=<?=$selProcessorId?>&details=<?=$details?>&fish=<?=$selFishId?>&preProcessCode=<?=$processId?>&summary=<?=$summary?>',700,600);" <? if(sizeof($getAllPreProcessingRecords)==0) echo "disabled";?> style="width:170px;">&nbsp;&nbsp;<input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintPreProcessingSummary.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>&qtySummary=<?=$qtySummary?>&selPreProcessor=<?=$selProcessorId?>&details=<?=$details?>&fish=<?=$selFishId?>&preProcessCode=<?=$processId?>&summary=<?=$summary?>',700,600);" <? if(sizeof($preProcessingSummaryRecords)==0 && $details=="" &&	$summary=="" && $selProcessorId=="" ) echo "disabled";?> ><? }?></td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
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
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
		?>			
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
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>