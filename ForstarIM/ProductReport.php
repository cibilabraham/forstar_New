<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	true;

	$redirectLocation = "?selDate=".$p["selDate"]."&pageNo=".$p["pageNo"];

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
	#Create multiple PO
	if ($p["cmdPO"]!="") {

		$hidRowCount	=	$p["hidRowCount"];
		$count=0;
		for ($j=1; $j<=$hidRowCount; $j++) {
			
		 	$selStockId = $p["stockId_".$j];

			if ($selStockId) {
				if ($selStockId!="" && $count>0) $selStock .=",";
				$selStock	.="$selStockId";
				$count++;
			}
		}
		header("location:PurchaseOrderInventory.php?stockItem=$selStock");
	}

*/

	if ($g["selDate"]!="") $reportDate = $g["selDate"];		
	else if ($p["selDate"]=="") $reportDate = date("d/m/Y");
	else $reportDate = $p["selDate"];
		
	$dateS		=	explode("/",$reportDate);
	$selectDate	=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
	
	$lastDate	= date("Y-m-d",mktime(0, 0, 0,$dateS[1],$dateS[0]-1,$dateS[2])); //latest record before the date

	# List all Product
	$productRecords = $productReportObj->fetchProductRecords($selectDate);


	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductReport" action="ProductReport.php" method="post">
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Product Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
										<td class="listing-item" nowrap>&nbsp;Date&nbsp;&nbsp;</td>
											<td nowrap style="padding-right:15px;">
										<? 
						if ($reportDate=="") {
							$reportDate	=	date("d/m/Y");
						}
						
						?>
						<input type="text" id="selDate" name="selDate" size="8" value="<?=$reportDate?>" onchange="this.form.submit();"></td>
										</tr>
									</table>								</td>
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
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductReport.php?selDate=<?=$reportDate?>',700,600);">
&nbsp;&nbsp; </td>
                                      <?}?>
				<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
				<? if (sizeof($productRecords)>0) { ?>
                                    <tr>
                                      <td  height="10" colspan="4"  align="center"><!--input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmProductReport)"--></td>
                                    </tr>
				<? }?>
				
                                    </tr>
                                    <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
                                    <tr>
                                      <td colspan="3" nowrap>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" >
	<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	<?
	if (sizeof($productRecords)) {
	$i=0;
	?>
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Product</td>
                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Code</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Net Wt <br>(Gms)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Packs under observ - OB</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Prodn</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Samp</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Test</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Wastage & Spoilage</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Des-Patch</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Packs under observ - CB</td>
        </tr>
	<?
	foreach ($productRecords as $sr) {
		$i++;
		$productId	= $sr[0];
		$productCode 	= $sr[1];
		$productName	= stripSlash($sr[2]);

		$productNetWt	= $sr[3];
		
		#Find the opening Qty
		$openingQty = $productReportObj->getOpeningQty($productId, $lastDate);

		#Find the Despatch Qty
		$despatchQty = $productReportObj->getDespatchQty($productId, $lastDate);

		$closingBalanceQty = $openingQty-$despatchQty;			
	?>
        <tr bgcolor="#FFFFFF" title="<?=$displayTitle?>">
               <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$productName?></td>
               <td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$productCode?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$productNetWt?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$t?></td>
		 <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$t?></td>
               <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$t?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$t?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$despatchQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$closingBalanceQty?></td>
         </tr>
         <? }
	 }
	?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
				<? if (sizeof($productRecords)>0) { ?>
                                    <tr>
                                      <td  height="10" colspan="4"  align="center"><!--input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmProductReport)"--></td>
                                    </tr>
				<? }?>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductReport.php?selDate=<?=$reportDate?>',700,600);">&nbsp;&nbsp; </td>
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