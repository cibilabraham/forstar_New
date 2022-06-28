<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;

	//$redirectLocation = "?selDate=".$p["selDate"]."&pageNo=".$p["pageNo"];

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

	$selStockId = $p["selStock"];

	$details  =$p["details"];
	if ($details) $details = "Checked";

	$summary  = $p["summary"];
	if ($summary) $summary = "Checked";
	
	$dateFrom = $p["stockFrom"];
	$dateTill = $p["stockTo"];
	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	
	if ($p["cmdSearch"]!="") {
		# List all Stocks

		$stkConsumptionResultSetObj = $stockConsumptionObj->fetchStockConsumptionRecords($fromDate, $tillDate, $selStockId, $details, $summary);

		$stockConsumptionRecords = $stkConsumptionResultSetObj->getNumRows();
		//$stockSize	=	sizeof($stockConsumptionRecords);
	}
	$stockRecords		=	$stockObj->fetchAllRecordsStockActive();
	# List all Stocks
	//$stockRecords		=	$stockObj->fetchAllRecords();
		//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	$ON_LOAD_PRINT_JS	= "libjs/StockConsumption.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmStockConsumption" action="StockConsumption.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Consumption</td>
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
                                      <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintStockConsumption.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selStock=<?=$selStockId?>&details=<?=$details?>&summary=<?=$summary?>',700,600);" <? if (!$stockConsumptionRecords) echo "disabled";?>>&nbsp;&nbsp; </td>
                                      <?}?>
				<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4"  align="center">
						<table><tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                       <input type="text" id="stockFrom" name="stockFrom" size="8" value="<?=$dateFrom?>"></td>
					<td class="fieldName">To:</td>
					<td>
                                        <input type="text" id="stockTo" name="stockTo" size="8"  value="<?=$dateTill?>"></td>
					<td class="fieldName">Stock</td>
					<td nowrap>
					<select name="selStock" id="selStock">
              				<option value="">-- Select All --</option>
                                        <?
					foreach ($stockRecords as $sr) {
						$stockId	=	$sr[0];
						$stockCode	=	stripSlash($sr[1]);
						$stockName	=	stripSlash($sr[2]);
						$selected	=	"";
						if ($selStockId==$stockId) $selected="selected";
					?>
                                        <option value="<?=$stockId?>" <?=$selected?>><?=$stockName?></option>
                                        <? }?>
                                        </select></td>
					<!--<td nowrap>&nbsp;<input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validateStkConsumptionSearch(document.frmStockConsumption)"></td>-->
                                  </tr>
				<tr><TD colspan="7">
					<table width="200" border="0" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td valign="top">
				<fieldset>
                                        <legend class="fieldName">Search Options </legend><table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td align="center"><input type="checkbox" name="details" id="details" value="Y" <?=$details?> class="chkBox" onclick="hideConsumptionSummaryOption();"></td>
                                    <td class="listing-item">Detailed</td>
					<td align="center"><input name="summary" type="checkbox" class="chkBox" id="summary" onclick="hideConsumptionDetailedOption();" value="Y" <?=$summary?>></td>
                                    <td class="listing-item">Summary</td>
					<td class="listing-item" align="center" nowrap>&nbsp;&nbsp;<input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validateStkConsumptionSearch(document.frmStockConsumption)"></td>
                                    </tr>
                                  <!--<tr>
                                    <td>&nbsp;</td>
                                    <td class="listing-item" align="center"><input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validateStkConsumptionSearch(document.frmStockConsumption)"></td>
                                  </tr>-->
                                </table>
                                    </fieldset></td>
                                  </tr>
                                </table>
				</TD></tr>
					</table>
					</td>
                                    </tr>
                                    </tr>
                                    <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
                                    <tr>
                                      <td colspan="3" nowrap>&nbsp;</td>
                                    </tr>
	<?
	if ($stockConsumptionRecords) {
	$i=0;
	?>
                                    <tr>
                                      <td  height="10" colspan="4" >
				<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	
        <tr bgcolor="#f2f2f2" align="center">
		<? if ($details) {?>
		 <td class="listing-head" style="padding-left:10px; padding-right:10px;">Date </td>
		<? }?>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Stock Item </td>
		<? if ($selStockId) {?>
		 <td class="listing-head" style="padding-left:10px; padding-right:10px;">Department </td>
		<? }?>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Used Qty</td>
        </tr>
	<?
	//foreach ($stockConsumptionRecords as $sr) {
	$prevStockId = "";
	$prevDepartmentId = "";
	$netsum=0;
	while ($sr=$stkConsumptionResultSetObj->getRow()) {
		$i++;
		$stockId	=	$sr[0];
		$stockName = "";
		if ($prevStockId!=$stockId) {
			$stockName	=	stripSlash($sr[1]);
		}

		$departmentId 	= 	$sr[5];
		$departmentName = 	$sr[2];
// 		$departmentName = "";
// 		if ($prevDepartmentId!=$departmentId) {
// 			$departmentName = 	$sr[2];
// 		}

		$displaySelectedDate = "";
		$selectedDate = dateFormat($sr[4]);
		if ($prevSelectDate!=$selectedDate) {
			$displaySelectedDate 	= 	dateFormat($sr[4]);
		}
		
		$usedQty	=	$sr[3];
		$netsum=$netsum+$usedQty;
		
	?>
        <tr bgcolor="#FFFFFF" title="<?=$displayTitle?>">
		<? if ($details) {
	
		?>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$displaySelectedDate?></td>
		<? }?>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stockName?></td>
		<? if ($selStockId) {
			
			?>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$departmentName?></td>
		<? }?>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$usedQty?></td>
         </tr>
         <?
		$prevStockId = $stockId;
		$prevDepartmentId = $departmentId;
		$prevSelectDate = $selectedDate;
		}
	 
	?>
	<tr bgcolor="#FFFFFF">
	
	
	<td  <? if ($details) {
	if ($selStockId) {
		?> colspan="3" <?php } else  {?> colspan="2" <?php } } if (!$details){ if ($selStockId) {
		?> colspan="2" <?php } else  {?> colspan="1" <?php } }?> align="right">Total</td><td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$netsum;?></td></tr>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<? } else if($dateFrom!="" && $dateTill!="") {?>
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
                                      <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintStockConsumption.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selStock=<?=$selStockId?>&details=<?=$details?>&summary=<?=$summary?>',700,600);" <? if (!$stockConsumptionRecords) echo "disabled";?>>&nbsp;&nbsp; </td>
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