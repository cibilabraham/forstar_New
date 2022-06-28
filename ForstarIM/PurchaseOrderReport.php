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

	$selSupplierId  = $p["selSupplier"];
	$selStatus	= $p["selStatus"];	
	
	$dateFrom = $p["stockFrom"];
	$dateTill = $p["stockTo"];
	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);
	
	if ($p["cmdSearch"]!="") {

		# List all Records

		$pOReportResultSetObj = $purchaseOrderReportObj->fetchPurchaseOrderRecords($fromDate, $tillDate, $selSupplierId, $selStatus);

		$poReportRecords = $pOReportResultSetObj->getNumRows();
		//$stockSize	=	sizeof($poReportRecords);
	}


	# List all Supplier
	//$supplierRecords			=	$supplierMasterObj->fetchAllRecords("INV");
$supplierRecords			=	$supplierMasterObj->fetchAllRecordsActivesupplier("INV");
	$ON_LOAD_PRINT_JS	= "libjs/PurchaseOrderReport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmPurchaseOrderReport" action="PurchaseOrderReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%" >
	
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Order Report</td>
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
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintPurchaseOrderReport.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selSupplier=<?=$selSupplierId?>&selStatus=<?=$selStatus?>',700,600);" <? if (!$poReportRecords) echo "disabled";?>>&nbsp;&nbsp; </td>
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
					<td class="fieldName" align='right'>Supplier:&nbsp;</td>
					<td nowrap>
					<select name="selSupplier" id="selSupplier">
                                        <option value="">-- Select All --</option>
                                        <?						  
					 foreach ($supplierRecords as $sr) {
						$supplierId	=	$sr[0];
						$supplierCode	=	stripSlash($sr[1]);
						$supplierName	=	stripSlash($sr[2]);
						$selected ="";
						if($selSupplierId==$supplierId) $selected="selected";
					?>
                                        <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
                                        <? }?>
                                        </select></td>
					<td nowrap>&nbsp;<input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validatePurchaseOrderReport(document.frmPurchaseOrderReport)"></td>
                                  </tr>
                                        <tr>
					<td class="fieldName" align='right'>* To:&nbsp;</td>
<td>
                                        <input type="text" id="stockTo" name="stockTo" size="8"  value="<?=$dateTill?>"></td>
					<TD class="fieldName" align='right'>Status:&nbsp;</TD>
<td>
						<select name="selStatus">
						<option value="">-- Select All--</option>
						<option value="P" <? if ($selStatus=='P') echo "Selected";?>>Pending</option>
						<option value="R" <? if ($selStatus=='R') echo "Selected";?>>Received</option>
						</select>
					</td>
                                                                           <TD>
                                                                           </TD>
                                                                           <TD>
                                                                           </TD>
                                                                           <TD>
                                                                           </TD>
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
	if ($poReportRecords) {
	$i=0;
	?>
                                    <tr>
                                      <td  height="10" colspan="6" >
	<table width="80%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Date </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amount</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
        </tr>
	<?
	$prevSupplierName = "";
	$prevCreateDate = "";
	while ($pr=$pOReportResultSetObj->getRow()) {
		$i++;
		$createDate = dateFormat($pr[3]);
		$displayCreateDate = "";
		if ($prevCreateDate!=$createDate) {
			$displayCreateDate = dateFormat($pr[3]);
		}
		$poId	  = $pr[0];
		$poNumber = $pr[1];
		$totalPOAmount = $pr[4];
		$supplierName = $pr[6];
		$displaySupplierName = "";
		if ($prevSupplierName!=$supplierName) {
			$displaySupplierName = $pr[6];
		}
		$status		=	$pr[5];
		if ($status=='C') 	$displayStatus	= "Cancelled";
		else if ($status=='R')	$displayStatus	= "Received";
		else if ($status=='PC')	$displayStatus	= "Partially Completed";
		else 			$displayStatus	= "Pending";		
	?>
        <tr bgcolor="White">
		
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$displayCreateDate?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
		<a href="javascript:printWindow('ViewPOReportDetails.php?selPOId=<?=$poId?>&status=<?=$status?>',700,600)" class="link1" title="Click here to view details."><?=$poNumber?></a>
		</td>
		<td class="listing-item" align="left" style="padding-left:10px; padding-right:10px;" nowrap><?=$displaySupplierName?></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalPOAmount?></td>
		<td class="listing-item" align="left" style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>
         </tr>
         <?
		$prevSupplierName = $supplierName;
		$prevCreateDate  = $createDate;
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
                                      <td  height="5" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                     <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintPurchaseOrderReport.php?stockFrom=<?=$dateFrom?> &stockTo=<?=$dateTill?>&selSupplier=<?=$selSupplierId?>&selStatus=<?=$selStatus?>',700,600);" <? if (!$poReportRecords) echo "disabled";?>>&nbsp;&nbsp; </td>
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
	</table><SCRIPT LANGUAGE="JavaScript"><!--
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
	//--></SCRIPT><SCRIPT LANGUAGE="JavaScript"><!--
	
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
	//--></SCRIPT>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
