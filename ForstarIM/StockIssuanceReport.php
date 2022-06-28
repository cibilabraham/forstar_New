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

	// get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))); 
		$dateTill = date("d/m/Y");
	}
	// get selected date end

	// SW-> Stock Wise , DW-> Department Wise
	//$repType = ($p["repType"]!="") ? $p["repType"] : 'SW';	

	$selStockId		= $p["selStock"];
	$selDepartmentId	= $p["selDepartment"];
		
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	# Generate Report
	if ($p["cmdSearch"]!="") {
		if ($selStockId!="" || $selDepartmentId!="") {
			$stockIssuanceRecords = $stockIssuanceReportObj->getStockIssunaceRecords($fromDate, $tillDate, $selStockId, $selDepartmentId);
		}
	}

	# List all Stocks
	//$stockRecords		=	$stockObj->fetchAllRecords();
		$stockRecords		=	$stockObj->fetchAllRecordsConfirm();
	# List all Departments
	//$departmentRecords	= $departmentObj->fetchAllRecords();

	$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();

	$ON_LOAD_PRINT_JS = "libjs/StockIssuanceReport.js";	// include in template

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStockIssuanceReport" action="StockIssuanceReport.php" method="post">
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Issuance & Returns Report</td>
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
				<table cellpadding="0" cellspacing="0" align="center">
					<TR>
						<TD>
							<table>
							<TR>
								<TD class="fieldName" nowrap>* By Stock:</TD>
								<td>
					<select name="selStock" id="selStock" onchange="changeSelValue('S');">
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
                                        </select>
								</td>
							</TR>
						</table>
						</TD>
						<TD class="listing-item" nowrap>&nbsp;OR&nbsp;</TD>
						<TD>
							<table cellpadding="0" cellspacing="0">
							<TR>
								<TD class="fieldName" nowrap>* By Department:</TD>
								<td>
					<select name="selDepartment" id="selDepartment" onchange="changeSelValue('D');">
                                        <option value="">--select--</option>
                                         <?
					foreach($departmentRecords as $cr)
					{
						$departmentId		=	$cr[0];
						$departmentName	=	stripSlash($cr[1]);
						$selected="";
						if($selDepartmentId==$departmentId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
                                                    <? }?>
                                                  </select>
								</td>
							</TR>
						</table>
						</TD>
					</TR>
				</table>	
			</td>
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
				if($dateTill=="") $dateTill=date("d/m/Y");
			?>
		<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>">&nbsp;&nbsp;
		</td>		
	</tr>
		</table>
	</TD>
	<TD colspan="2">
		<table cellpadding="0" cellspacing="0" align="right"><TR>
			<td class="listing-item"  nowrap>	
				<INPUT TYPE="submit" class="button" name="cmdSearch" value='Generate Report' onclick="return validateStockIssuanceReport();">
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
	if ($stockIssuanceRecords) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockIssuanceReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selStock=<?=$selStockId?>&selDepartment=<?=$selDepartmentId?>',700,600);">
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
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">
			<? if ($selStockId!="") {?>
				Department 
			<? } else if($selDepartmentId!="") { ?>
				Stock Name 
			<? }?>
		</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Issued Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="5">Return Qty</td>		
        </tr>
	<tr bgcolor="#f2f2f2">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Lost </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Stolen</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Damaged</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Deteriorated</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total</td>
	</tr>
	<?
	foreach ($stockIssuanceRecords as $sir) {
		$i++;		
		$stockId 	= $sir[0];
		$departmentId 	= $sir[1];
		$issuedQty 	= $sir[2];
		$stkName	= $sir[3];
		$departmentName = $sir[4];

		$wastageRecs = $stockIssuanceReportObj->getWastageRecDetials($fromDate, $tillDate, $stockId, $departmentId);
		/*
		if ($selStockId!="") { // Pass DepartmentId
			$wastageRecs = $stockIssuanceReportObj->getWastageRecDetials($fromDate, $tillDate, "", $departmentId, $selStockId, "");
		} else if($selDepartmentId!="") { // Pass Stock Id
			$wastageRecs = $stockIssuanceReportObj->getWastageRecDetials($fromDate, $tillDate, $stockId, "", "", $selDepartmentId);
		}
		*/
			$lostQty = 0;
			$stolenQty = 0;
			$dmgdQty = 0;
			$deterioQty = 0;
			$totalQuantity = "";
		if (sizeof($wastageRecs)>0) {			 
			
			foreach($wastageRecs as $tqr ) {			
				$reasonType = $tqr[3];
				if ($reasonType=='L')  $lostQty += $tqr[0];
				else if( $reasonType=='S' )  $stolenQty += $tqr[0];
				else if( $reasonType=='D' )  $dmgdQty += $tqr[0];
				else if( $reasonType=='DR' )  $deterioQty += $tqr[0];
				else  {
					$lostQty = 0;
					$stolenQty = 0;
					$dmgdQty = 0;
					$deterioQty = 0;
				}
			}
			$totalQuantity = ( $lostQty + $stolenQty ) + ( $dmgdQty + $deterioQty );	
		}
	
	?>
        <tr bgcolor="white">
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<? if ($selStockId!="") {?>
				<?=$departmentName?>
			<? } else if($selDepartmentId!="") { ?>
				<?=$stkName?>
			<? }?>
		</td>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><?=$issuedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
				<?
					if( $lostQty!=0 ) echo $lostQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
				<?
					if( $stolenQty!=0 ) echo $stolenQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
				<?
					if( $dmgdQty!=0 ) echo $dmgdQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
				<?
					if( $deterioQty!=0) echo $deterioQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$totalQuantity?></td>		
         </tr>
         <?
	 }
	 ?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td></tr>
	<tr>
               <td  height="20" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockIssuanceReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selStock=<?=$selStockId?>&selDepartment=<?=$selDepartmentId?>',700,600);">
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