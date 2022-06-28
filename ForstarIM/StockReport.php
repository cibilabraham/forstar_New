<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;

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

	


	if ($g["selDate"]!="") {
		$recordsDate	=	$g["selDate"];		
	} else if($p["selDate"]=="") {
		$recordsDate	=	date("d/m/Y");
	} else {
		$recordsDate	=	$p["selDate"];
	}
	
	#Create multiple PO
	if ($p["cmdPO"]!="") {
		$rt = $p["stockReportType"];
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

	$Date		= explode("/",$recordsDate);
	$selectDate	= $Date[2]."-".$Date[1]."-".$Date[0];
	//$selectDate	= mysqlDateFormat($recordsDate);
	$lastDate  	= date("Y-m-d",mktime(0, 0, 0,$Date[1],$Date[0]-1,$Date[2])); //latest record before the date

	/* 
		SO	- Show out of stock
		SR	- Show reorder stock
		SA	- Show all
	*/
		
	$stockReportType	= $p["stockReportType"];
	if ($stockReportType=='SO') {
		$showOutOfStock = "checked";
	} else if ($stockReportType=='SR') {
		$showReorder	= "checked";
	} else {
		$showAll	= "Checked";
	}

	# List all Stocks
	$stockRecords		= $stockreportObj->fetchStockRecords($selectDate, $stockReportType);
	$stockSize		= sizeof($stockRecords);

	
	if ($editMode)	$heading	= $label_editStockReport;
	else 		$heading	= $label_addStockReport;	

	$ON_LOAD_PRINT_JS	= "libjs/StockReport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmStockReport" action="StockReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="95%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
									<td background="images/heading_bg.gif"  >
		<table cellpadding="0" cellspacing="0" align="right">	
		<tr>
			<td class="listing-item" nowrap>&nbsp;Date:&nbsp;&nbsp;</td>
			<td nowrap style="padding-right:15px;">
										<? 
						if ($recordsDate=="") {
							$recordsDate	=	date("d/m/Y");
						}
						
						?>
						<input type="text" id="selDate" name="selDate" size="8" value="<?=$recordsDate?>" onchange="this.form.submit();"></td>
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
                                      <td colspan="3" align="center">
					<? if (sizeof($stockRecords)>0 && $edit==true) { ?> 
					<input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmStockReport)"><? }?>&nbsp;&nbsp;&nbsp;&nbsp;
					<? if($print==true){?>
					<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockReport.php?selDate=<?=$recordsDate?>&stockReportType=<?=$stockReportType?>',700,600);" <? if (sizeof($stockRecords)==0) echo "disabled";?>><?}?>&nbsp;&nbsp; </td>
<tr>
                                      <td  height="10" colspan="4"  align="center">
				      </td>
                                    </tr>
	<tr>
             <td  height="10" colspan="4">
		<table align="center">
			<TR>
				<TD>
					<table>
						<TR>
						<TD>
							<INPUT type="radio" class="chkBox" name="stockReportType" value="SO" onclick="this.form.submit();" <?=$showOutOfStock?>>
						</TD>
						<td class="listing-item" nowrap>Show Out of stock Items</td>
						</TR>
					</table>
				</TD>
				<td>
					<table>
						<TR>
						<TD>
							<INPUT type="radio" class="chkBox" name="stockReportType" value="SR" onclick="this.form.submit();" <?=$showReorder?>>
						</TD>
						<td class="listing-item" nowrap>Show Items that need to be reordered</td>
						</TR>
					</table>
				</td>
				<td>
					<table>
						<TR>
						<TD>
							<INPUT type="radio" class="chkBox" name="stockReportType" value="SA" onclick="this.form.submit();" <?=$showAll?>>
						</TD>
						<td class="listing-item" nowrap>Show All</td>
						</TR>
					</table>
				</td>
			</TR>
		</table>
	     </td>
        </tr>
        </tr>
              <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
         <tr>
              <td colspan="3" nowrap>&nbsp;</td>
         </tr>
         <tr>
           <td  height="10" colspan="6" >
		<table width="100%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	<?
	if (sizeof($stockRecords)) {
		$i=0;
	?>
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Stock Item </td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Opening Balance Qty </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Accepted Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Used Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Closing Balance Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Re-Order Point</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Has Supplier(s)</td>
	  	<td>&nbsp;</td>
        </tr>
	<?
		/*
		echo "StockSize=".$stockSize."<br>";
		echo "<pre>";
		print_r($stockRecords);
		echo "</pre>";*/

		foreach ($stockRecords as $sr) {
		$i++;
		$stockId	=	$sr[0];
		$stockName	=	stripSlash($sr[1]);
		$quantity	=	$sr[2];
		$acceptedQty 	=	$sr[3];
		$usedQty	=	$sr[4];
		#Find the opening Qty
		$openingQty = $stockreportObj->getOpeningQty($stockId, $lastDate);
		$closingBalanceQty = ($openingQty + $acceptedQty)- $usedQty;

		#find the Reorder Point
		list($reOrderPoint, $actualQuantity) = $stockreportObj->findReOrderPoint($stockId);
		//echo "$actualQuantity<$reOrderPoint";
		$displayClosingQty= "";
		$displayTitle = "";
		if ($closingBalanceQty<$reOrderPoint) {
			$displayClosingQty = "<span style=\"color:#FF0000\">".$closingBalanceQty."</span>";
			$displayTitle = "This stock is below Re-order Point";
		} else {
			$displayClosingQty  = $closingBalanceQty;
			$displayTitle = "";
		}
		
		//if($displayTitle!="")
		//$mo ='style="background-color: #ffffff;"  onmouseover="this.style.backgroundColor=\'#E1FFFF\'; ShowTip(\''.$displayTitle.'\')" onmouseout="this.style.backgroundColor=\'#ffffff\'; UnTip()"';
		//else $mo = $listRowMouseOverStyle;
		
		$suppCount = sizeof($stockreportObj->checkSupplierExistForStock( $stockId ));
		if( $suppCount > 0 ) $displaySupplier = '<IMG SRC="images/y.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="">';
		else $displaySupplier = '<IMG SRC="images/x.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="">';
	?>
        <tr bgcolor="white" title="<?=$displayTitle?>">
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stockName?></td>
               <td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$acceptedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$usedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$displayClosingQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$reOrderPoint?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$displaySupplier?></td>
	       <td style="padding-left:3px; padding-right:3px;">
			<?
				if( $suppCount > 0 )
				{
			?>
			<input type="checkbox" name="stockId_<?=$i?>" id="stockId_<?=$i?>" value="<?=$stockId?>" class="chkBox">
			<?
				} 
			?>
			<input type="hidden" name="hidSuppCount_<?=$i?>" id="hidSuppCount_<?=$i?>" value="<?=$suppCount?>" class="chkBox">	
			<? 
				
			?>
		</td>
         </tr>
         <? 
		}
	 } else {
	?>
	<tr bgcolor="white">
		<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoStockItemRecords;?></td>
	</tr>
	<? }?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
				
                                    <tr>
                                      <td  height="10" colspan="4"  align="center"><!--input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmStockReport)"--></td>
                                    </tr>
				
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                     
                                      <td colspan="3" align="center">
					<? if (sizeof($stockRecords)>0 && $edit==true) { ?><input name="cmdPO" type="submit" class="button" id="cmdPO" value=" Update Orders " onclick="return validateUpdatePOOrder(document.frmStockReport)"><? }?>&nbsp;&nbsp;&nbsp;&nbsp; <? if($print==true){?>
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockReport.php?selDate=<?=$recordsDate?>&stockReportType=<?=$stockReportType?>',700,600);" <? if (sizeof($stockRecords)==0) echo "disabled";?>><?} ?>&nbsp;&nbsp; </td>
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
			inputField  : "schedule",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule", 
			ifFormat    : "%m/%d/%Y",    // the date format
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