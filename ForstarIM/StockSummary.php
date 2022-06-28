<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;

	// Cheking access control
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
	// Cheking access control end 

	$selStockId = $p["selStock"];
	
	
	$dateFrom = $p["stockFrom"];
	$dateTill = $p["stockTo"];
	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);


	
	if ($p["cmdSearch"]!="") {

		# List all Stocks

		$stkSummaryResultSetObj = $stockSummaryObj->fetchStkSummaryRecords($fromDate, $tillDate, $selStockId);

		$stkSummaryRecords = $stkSummaryResultSetObj->getNumRows();
		//$stockSize	=	sizeof($stkSummaryRecords);
	}


	# List all Stocks
	//$stockRecords		=	$stockObj->fetchAllRecords();
	 $stockRecords		=	$stockObj->fetchAllRecordsConfirm();

	$ON_LOAD_PRINT_JS	= "libjs/StockSummary.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStockSummary" action="StockSummary.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Wise Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
                                    <tr>
                                      <? if($addMode){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <!--input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintStockSummary.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selSupplier=<?=$selSupplierId?>',700,600);" <? if (!$stkSummaryRecords) echo "disabled";?>-->&nbsp;&nbsp; </td>
                                      <?}?>
				<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4"  align="center">
						<table><tr> 
                                    <td class="fieldName" align='right' nowrap>* From:</td>
                                    <td> 
                                       <input type="text" id="stockFrom" name="stockFrom" size="8" value="<?=$dateFrom?>"></td>
					<td class="fieldName"  align='right' nowrap>* To:</td>
					<td>
                                        <input type="text" id="stockTo" name="stockTo" size="8"  value="<?=$dateTill?>"></td>
					<td class="fieldName"  align='right' nowrap>* Stock:</td>
					<td nowrap>
					<select name="selStock" id="selStock">
             				<option value="">-- Select --</option>
                                        <?
					foreach ($stockRecords as $sr) {
						$stockId =	$sr[0];
						$stockCode = stripSlash($sr[1]);
						$stockName = stripSlash($sr[2]);
						$selected	=	"";
						if ($selStockId==$stockId) $selected="selected";
					?>
                                        <option value="<?=$stockId?>" <?=$selected?>><?=$stockName?></option>
                                        <? }?>
                                        </select></td>
					<td nowrap>&nbsp;<input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validateStkSummarySearch(document.frmStockSummary)"></td>
                                  </tr>

					</table>
					</td>
                                    </tr>
                                    </tr>
                                    <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
                                    <tr>
                                      <td colspan="3" nowrap>&nbsp;</td>
                                    </tr>
	<?
	if ($stkSummaryRecords) {
	$i=0;
	?>
                                    <tr>
                                      <td  height="10" colspan="4" >
				<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	
        <tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Opening Qty </td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Received Qty </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Issuance Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Rejected Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Closing Balance Qty</td>
        </tr>
	<?
	while ($sr=$stkSummaryResultSetObj->getRow()) {
		$i++;
		$stockId	= $sr[0];
		$receivedQty	= $sr[1];
		$rejectedQty    = $sr[2];
		$issuanceQty 	= $sr[3];

		$dateF	  = explode("/", $dateFrom);
		$openingDate = date("Y-m-d",mktime(0, 0, 0,$dateF[1],$dateF[0]-1,$dateF[2])); //latest record before the date
		#Find the opening Qty
		$openingQty = $stockreportObj->getOpeningQty($stockId, $openingDate);

		$closingBalanceQty = ($openingQty + $receivedQty)- ($issuanceQty);
	?>
        <tr bgcolor="white">
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$openingQty?></td>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$receivedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$issuanceQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$rejectedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$closingBalanceQty?></td>
         </tr>
         <?
	 }
	 ?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<? } else if ($dateFrom!="" && $dateTill!="") {?>
	<tr>
		<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<? }?>
				    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <? if($addMode){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <!--input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintStockSummary.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selSupplier=<?=$selSupplierId?>',700,600);" <? if (!$stkSummaryRecords) echo "disabled";?>-->&nbsp;&nbsp; </td>
                                      <?} ?>
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
	</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "stockFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "stockFrom", 
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
			inputField  : "stockTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "stockTo", 
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