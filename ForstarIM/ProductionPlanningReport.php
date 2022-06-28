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

	$reportType 	= ($p["reportType"]!="") ? $p["reportType"] : 'S';
	$selSumm = "";
	$selDet = "";
	if ($reportType=='D') $selDet = "checked";
	else if( $reportType=='S') $selSumm = "checked";
		

	// get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y");
		//$dateFrom = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))); 
		$dateTill = date("d/m/Y");
	}
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	# Generate Report
	if ($p["cmdSearch"]!="") {
		if ($fromDate!="" && $fromDate!="" && $reportType!="") {
			$productionPlanningRecords = $productionPlanningReportObj->getProductionPlannedRecords($fromDate, $tillDate, $reportType);
			//uksort($productionPlanningRecords);
			/*
			echo "<pre>";
			print_r($productionPlanningRecords);
			echo "</pre>";
			*/
			//echo "H=".sizeof($productionPlanningRecords); 
		}
	}


	$ON_LOAD_PRINT_JS = "libjs/ProductionPlanningReport.js";	// include in template

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductionPlanningReport" action="ProductionPlanningReport.php" method="post">
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Production Planning Report</td>
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
                                      <td height="5" ></td>
                                    </tr>
<tr>
	<TD colspan="4">
	<table align='center' cellspacing='1'  cellspacing='1' >
		<tr>
			<td height='2' colspan='3' ></td>
		</tr>		
		<tr>
			<td colspan='3' >
				<table cellpadding="0" cellspacing="0" align="left">
					<tr>
						<TD>
							<table cellpadding="0" cellspacing="0">
								<tr>
		<td class="fieldName" align='right' nowrap  >* From:&nbsp; 
		<? 
			if ($dateFrom=="") $dateFrom=date("d/m/Y");
		?>
		<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>">&nbsp;&nbsp;
		</td>
		<td class="fieldName"  nowrap >* To:&nbsp;
			<? 
				if ($dateTill=="") $dateTill=date("d/m/Y");
			?>
		<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>">&nbsp;&nbsp;
		</td>	
		<!--<td class="fieldName" nowrap="nowrap">
			<INPUT TYPE="radio" NAME="reportType" <?=$selSumm;?> Style='vertical-align:middle;' value='S' class="chkBox">Summary &nbsp;&nbsp;
			<INPUT TYPE="radio" NAME="reportType" Style='vertical-align:middle;' value='D' class="chkBox" <?=$selDet;?> >Detailed &nbsp;&nbsp;
		</td>-->	
	</tr>
		</table>
	</TD>
	<TD colspan="2">
		<table cellpadding="0" cellspacing="0" align="right"><TR>
			<td class="listing-item"  nowrap>	
				<INPUT TYPE="submit" class="button" name="cmdSearch" value='Generate Report' onclick="return validateProductionPlanningReport();">
			</td></TR>
			</table>
			</TD>
			</tr>
				</table>
			</td>
		</tr>	
	</table>	
	</TD>
</tr>
        <tr>
               <td colspan="3" nowrap>&nbsp;</td>
       </tr>
	<?
	if ($productionPlanningRecords) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductionPlanningReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&reportType=<?=$reportType?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="20" colspan="4" style="padding:left:10px; padding-right:10px;">
		</td>
	</tr>
        <tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;">
		<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
        <tr bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredient</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Qty<br>(Kg)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Price</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt<br>(Rs.)</td>
        </tr>
	<?
	$grandTotalAmt	= 0;
	//foreach ($productionPlanningRecords as $ppr) {
	foreach ($productionPlanningRecords as $ingredientId=>$ppr) {
		$i++;	
		//$ingredientId	= $ppr[1];	
		//$ingName	= $ppr[2];	
		//$quantity	= $ppr[3];
		$quantity	= $ppr[0];
		$ingName	= $ppr[1];
		# Find the Lowest Price of the Ing
		$unitPrice	= $productionPlanningReportObj->getIngPrice($ingredientId);
		$totalAmt	= number_format(($quantity*$unitPrice),2,'.','');
		$grandTotalAmt += $totalAmt;	
	?>
        <tr bgcolor="white">
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<?=$ingName?>			
		</td>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right">
			<?=$quantity?>
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
			<?=$unitPrice?>
		</td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;">
			<?=$totalAmt?>
		</td>				
         </tr>
         <?
	 	}
	 ?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
	<tr bgcolor="white">
		<TD class="listing-head" colspan="3" align="right">Grand Total:</TD>
		<TD class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?=number_format($grandTotalAmt,2,'.','');?></strong>
		</TD>
	</tr>
        </table>
	</td>
	</tr>
	<tr>
               <td  height="20" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintProductionPlanningReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&reportType=<?=$reportType?>',700,600);">
<? }?>
		</td>
		</tr>
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
			<td height="10"></td>
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
			inputField  : "dateTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTill", 
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