<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;

	$redirectLocation = "?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	/*
		$averagePeriod = Q- Quarterly, H-Half yearly, Y- Yearly
	*/
	if ($p["cmdUpdate"]!="") {
		$averagePeriod = $p["averagePeriod"];
		$excessStockTolerance = trim($p["excessStockTolerance"]);
		$updateAveragePeriod = $stockHoldingCostReportObj->updateAveragePeriodType($averagePeriod, $excessStockTolerance);
		if ($updateAveragePeriod) {
			//$sessObj->createSession("displayMsg",$msg_succAveragePeriodTypeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateAveragePeriodType.$redirectLocation);
		} else {
			$err = $msg_failAveragePeriodTypeUpdate;
		}
		$updateAveragePeriod	= false;
	}
	
	# Find Average Period type
	list($averagePeriodType,$excessStockTolerance) = $stockHoldingCostReportObj->getAveragePeriodType();
	if ($averagePeriodType=='Q') {	
		$quarterlyType = "checked";
		$month = 4;
	} else if ($averagePeriodType=='H') {
		$halfYearlyType = "checked";  
		$month = 6;
	} else if ($averagePeriodType=='Y') { 
		$yearlyType = "checked";
		$month = 12;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Stocks
	$stockRecords =	$stockHoldingCostReportObj->fetchAllActiveRecords($offset, $limit);
	//$stockRecords		= $stockHoldingCostReportObj->fetchStockRecords($averagePeriodType);
	$stockSize		= sizeof($stockRecords);

	## -------------- Pagination Settings II -------------------
	//$stockObj->fetchAllRecords()
	$numrows =  sizeof($stockObj->fetchAllActiveRecords());
	$maxpage =  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode)	$heading	= $label_editStockReport;
	else 		$heading	= $label_addStockReport;	

	$ON_LOAD_PRINT_JS	= "libjs/StockHoldingCostReport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmStockHoldingCostReport" action="StockHoldingCostReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="95%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap>&nbsp;Stock Holding Cost Report</td>
								<td background="images/heading_bg.gif" style="padding-right:10px;" nowrap>
	<table cellpadding="0" cellspacing="0" align="right">
		<TR>
			<TD class="listing-head" nowrap>&nbsp;&nbsp;Excess Stock Tolerance:</TD>
			<td class="listing-item" nowrap>
				<input type="text" size="3" name="excessStockTolerance" id="excessStockTolerance" value="<?=$excessStockTolerance?>" style="text-align:right;">&nbsp;%&nbsp;
			</td>
			<TD class="listing-head" nowrap>Average Consumption Period:</TD>
			<td>
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD>
							<input type="radio" name="averagePeriod" value="Q" <?=$quarterlyType?>>
						</TD>
						<td class="listing-item" nowrap>
							Quarterly						
						</td>
						<TD>
							<input type="radio" name="averagePeriod" value="H" <?=$halfYearlyType?>>
						</TD>
						<td class="listing-item" nowrap>
							Half Yearly						
						</td>
						<TD>
							<input type="radio" name="averagePeriod" value="Y" <?=$yearlyType?>>
						</TD>
						<td class="listing-item" nowrap>
							Yearly						
						</td>
						<td nowrap>&nbsp;
							<input name="cmdUpdate" value="Update" class="button" type="submit" onclick="return validateStockHoldingCostReport();">
						</td>
					</TR>
				</table>
			</td>
		</TR>
	</table>
							</td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
                                    <tr>		
                                      <td colspan="3" align="center">					
					&nbsp;&nbsp;&nbsp;&nbsp;
					<? if($print==true){?>
					<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockHoldingCostReport.php',700,600);" <? if (sizeof($stockRecords)==0) echo "disabled";?>><?}?>&nbsp;&nbsp; </td>
<tr>
                                      <td  height="10" colspan="4"  align="center">
				      </td>
                                    </tr>
	
              <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
         <tr>
              <td colspan="3" nowrap>&nbsp;</td>
         </tr>
	<tr>
              <td colspan="3" nowrap>
			<table align="right" width="40%">
				<TR>
					<TD class="listing-item" style="line-height:normal;"></TD>
					<td>
						<table width="100%" align="right">
							<TR>
								<!--TD bgcolor="#FFA500" width="10">&nbsp;</TD-->
								<TD class="fieldName" style="line-height:normal;" nowrap bgcolor="#FFA500" align="center"><span style="color:white;">Over Stocked</span></TD>
								<td></td>
								<!--<TD bgcolor="#008000" width="10"></TD>-->
								<TD class="fieldName" style="line-height:normal;" nowrap bgcolor="#008000" align="center"><span style="color:white;">Optimally Stocked</span></TD>
								<td></td>
								<!--<TD bgcolor="#CC3300" width="10"></TD>-->
								<TD class="fieldName" style="line-height:normal;" nowrap bgcolor="#CC3300" align="center"><span style="color:white;">Under Stocked</span></TD>
								<td></td>
								<!--TD bgcolor="#CC3366" width="10"></TD>
								<TD class="fieldName" style="line-height:normal;" nowrap>-&nbsp;Suspicious Stock</TD-->
							</TR>
						</table>
					</td>
				</TR>
			</table>
	      </td>
         </tr>	
         <tr>
           <td  height="10" colspan="6" width="100%">
		<table width="100%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	<?
	if (sizeof($stockRecords)) {
		$i=0;
	?>
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="12" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockHoldingCostReport.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockHoldingCostReport.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockHoldingCostReport.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Stock Item</td>
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Current Qty <br>[A]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Stocking Frequency <br>(In Months)<br>[B] </td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Average Con-<br>sumption <br> (Last <?=$month?> Months)<br>[C]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Expected Consu-<br>mption<br>[D]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">
                                Average Avoidable 
                                <br>Return Qty<br>[E]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Excess <br>Stock In<br> Hand<br>[F=A-(<br>(C-E)*B)]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Unit Price <br>[G]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Holding Cost<br>[A*G]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Excess <br>Holding Cost<br>[F*G]</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Excess Holding (%)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Price Fluc-<br>tuation Indicator<br>(%)</td>		
        </tr>
	<?	
	$totalExcessHoldingcost = 0;
	$totalHoldingCost = 0;
	$excessHoldingCost = 0;
	
	foreach ($stockRecords as $sr) {
		$i++;
		$stockId	= $sr[0];
		$stockName	= stripSlash($sr[2]);
		$additionalHoldingPercent = $sr[10];
		$stockingPeriod	= $sr[11];

		#Find the opening Qty (A)
		$openingQty = $stockHoldingCostReportObj->getOpeningQty($stockId);
		# Stock Consumed Qty (B)		
		$averageConsumedQty = $stockHoldingCostReportObj->getStockConsumedQty($stockId, $averagePeriodType);
		# Expected Consumed Qty
		$expectedConsumedQty = $stockingPeriod*$averageConsumedQty;
		# Last Price  (E)
		$unitPrice = $stockHoldingCostReportObj->getUnitPriceOfStock($stockId);
		//echo "$stockId-$stockName-$unitPrice<br>";
		# get average return Qty (C)
		$averageReturnQty = $stockHoldingCostReportObj->getAverageReturnQty($stockId, $stockingPeriod);

		// Excess Stock in Hand (Cqty-(AConsumedQty-AverageReturnQty)=>D = A-(B-C))	
		$excessStockInHand = "";
		$calcExcessStockInHand	= $openingQty-(($averageConsumedQty-$averageReturnQty)*$stockingPeriod);
		if ($calcExcessStockInHand>0) $excessStockInHand = $calcExcessStockInHand;
		else $excessStockInHand = 0;

		// Holding Cost (CQty*Unit Price=>A*E)
		$holdingCost = $openingQty*$unitPrice;
		$totalHoldingCost += $holdingCost;

		// Excess holding Cost (Excess SH*$unit Price=>D*E)
		$excessHoldingCost = $excessStockInHand*$unitPrice;
		
		$totalExcessHoldingcost += $excessHoldingCost;
		// Excess Holding Percent "-$additionalHoldingPercent"
		$excessHoldingPercent = ((($openingQty-$averageConsumedQty)*100)/$openingQty);
		//echo "$averageConsumedQty-((($openingQty-$averageConsumedQty)*100)/$openingQty)-$additionalHoldingPercent<br>";		

		// Over Stock = Orange, under stock = Red, zero = Greeen
		
		if ($averageConsumedQty<=0) {
			$tdHPercentBgColor= "bgcolor=\"#ffffff\""; // white
			$excessHoldingPercent = 0;
		}
		
		
		$subtractExcessPercent = $additionalHoldingPercent-$excessStockTolerance;
		$addExcessPercent      = $additionalHoldingPercent+$excessStockTolerance;
		if (($excessHoldingPercent>=$subtractExcessPercent && $excessHoldingPercent<=$addExcessPercent) && $excessHoldingPercent!=0) {			
			$tdHPercentBgColor = "bgcolor=\"#008000\"";		// Green
		} else if ($excessHoldingPercent>0 && $excessHoldingPercent>=$addExcessPercent && $excessStockInHand!=0) {
			//echo "$excessHoldingPercent>=$addExcessPercent";
			$tdHPercentBgColor = 	"bgcolor=\"#FFA500\"";	// orange	
		} else if ($excessHoldingPercent>0 && $excessStockInHand!=0) {
			$tdHPercentBgColor = 	"bgcolor=\"#CC3300\"";		// Red	
		} 


		//echo "$stockName=>$excessHoldingPercent>=$addExcessPercent&&$excessHoldingPercent<=$addExcessPercent<br>";
		//if ($additionalHoldingPercent-$excessStockTolerance
		/*
		if ($averageConsumedQty<=0 && $averageReturnQty>0) {
			$tdHPercentBgColor= "bgcolor=\"#CC3366\"";	
			$excessHoldingPercent = "";
		}
		*/

		# Stock Item Price Variation
		$calcPriceFluctuationPercent = 0;
		list($currentStockPrice, $yearlyAveragePrice)= $stockHoldingCostReportObj->getStockItemPriceVariation($stockId);
		//$priceVariationAmt = $stockHoldingCostReportObj->getStockItemPriceVariation($stockId);
		if ($currentStockPrice>0)
		$calcPriceFluctuationPercent = (($yearlyAveragePrice-$currentStockPrice)*100)/$currentStockPrice;
		$priceVariationPercent = ($calcPriceFluctuationPercent)>0?number_format($calcPriceFluctuationPercent,0,'',''):"";
		$displayPriceVariation = "";
		if ($priceVariationPercent>0) {
			$displayPriceVariation = "<span style=\"color:#FF0000\">".$priceVariationPercent."</span>";
		} else {
			$displayPriceVariation 	= "<span style=\"color:#0ecd0e\">".$priceVariationPercent."</span>";
		}
	?>
        <tr bgcolor="white">
               <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$stockName?></td>
               <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=($stockingPeriod>0)?$stockingPeriod:"";?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=($averageConsumedQty>0)?number_format($averageConsumedQty,2,'.',','):"";?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=($expectedConsumedQty>0)?number_format($expectedConsumedQty,2,'.',','):"";?></td>		
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$averageReturnQty?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=number_format($excessStockInHand,2,'.','');?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$unitPrice?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;" ><?=($holdingCost>0)?number_format($holdingCost,2,'.',','):"";?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=($excessHoldingCost!=0)?number_format($excessHoldingCost,2,'.',','):"";?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;" <?=$tdHPercentBgColor?>><span style="color:white; font-weight:bold;font-size:12px;"><?=($excessHoldingPercent>0)?number_format($excessHoldingPercent,0,'',','):"";?></span></td>		
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$displayPriceVariation;?></td>		
         </tr>
         <? 
		}
	?>
	<tr bgcolor="White">
			<TD colspan="8" class="listing-head" align="right">Grand Total:</TD>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=number_format($totalHoldingCost,2,'.',',')?></td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=number_format($totalExcessHoldingcost,2,'.',',')?></td>
			<td></td>
			<td></td>
	</tr>
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="12" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockHoldingCostReport.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockHoldingCostReport.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockHoldingCostReport.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<?
	 } else {
	?>
	<tr bgcolor="white">
		<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoStockItemRecords;?></td>
	</tr>
	<? }?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
				
                                    <tr>
                                      <td  height="10" colspan="4"  align="center"><!--input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmStockHoldingCostReport)"--></td>
                                    </tr>
				
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" align="center">
					&nbsp;&nbsp;&nbsp;&nbsp; <? if($print==true){?>
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockHoldingCostReport.php',700,600);" <? if (sizeof($stockRecords)==0) echo "disabled";?>><?} ?>&nbsp;&nbsp; </td>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td><!-- Form fields end   --></td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "schedule",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule", 
			ifFormat    : "%m/%d/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
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
