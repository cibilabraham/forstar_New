<?php
	require("include/include.php");
	require_once("lib/DNReport_ajax.php");

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= true;
	$searchMode 		= false;
	$recEditable 		= false;
	$statusUpdated		= false;
	//$printMode		= true;
	$debitNoteRecs	= $shippingLineRecs = array();

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
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
	if ($isAdmin==true || $reEdit==true) $recEditable = true;	
	// Cheking access control end 


	# Get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	$fromDate	= mysqlDateFormat($dateFrom);
	$tillDate	= mysqlDateFormat($dateTill);

	if ($p["selShippingLine"]!="") $selShippingLineId = $p["selShippingLine"];
	else if ($g["selShippingLine"]!="") $selShippingLineId = $g["selShippingLine"];
	
	# Generate Report (Search From Dashboard)
	if ($p["cmdSearch"]!="" || $g["cmdSearch"]!="") {
		if ($fromDate!="" && $fromDate!="") {
			$debitNoteRecs = $dnReportObj->debitNoteRecs($fromDate, $tillDate, $selShippingLineId);		
			$searchMode = true;
		}
	}

	if ($searchMode) {
		$shippingLineRecs = $dnReportObj->getShippingLineRecs($fromDate, $tillDate);
	}


	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/DNReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDNReport" action="DNReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?php
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Debit Note Report</td>
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
		<TD colspan="3" align="center">
			<table>
				<TR>
					<TD valign="top" style="padding-left:10px;padding-right:10px;padding-bottom:10px;paing-top:10px;">
					<table><TR><TD>
						<fieldset>
						<table>
							<TR>
								<td class="fieldName" nowrap>*From:</td>
								<td>
							<?php 
								if ($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
								<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getShippingCompany(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('selShippingLine').value);">
								</td>
								<TD>&nbsp;</TD>
								<td class="fieldName"  nowrap >*To:</td>
								<td>
								<?php
									if ($dateTill=="") $dateTill=date("d/m/Y");
								?>
								<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getShippingCompany(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('selShippingLine').value);">
								</td>
								<TD>&nbsp;</TD>
								<td class="fieldName" nowrap>Shipping Line</td>
                                                  <td class="listing-item">
												<select name="selShippingLine" id="selShippingLine">			
												<?php if (sizeof($shippingLineRecs)<=0) {?><option value="">--Select All--</option><?php } ?>
												<?php
												foreach ($shippingLineRecs as $shippingLineId=>$shippingLine) {
													$selected = ($shippingLineId==$selShippingLineId)?"selected":"";
												?>
												<option value="<?=$shippingLineId?>" <?=$selected?>><?=$shippingLine?></option>	
												<? } ?>
												</select>
								</td>
								<TD>&nbsp;</TD>
								<td class="listing-item">
									<INPUT TYPE="submit" class="button" name="cmdSearch" value="Generate Report" onclick="return validateDNReport();">
								</td>
							</TR>						
						</table>
					</fieldset>
			</TD></TR>		
			</table>
					</TD>					
				</TR>
			</table>
		</TD>
	</tr>	
	<?php
	if (sizeof($debitNoteRecs)>0) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDNReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selShippingLine=<?=$selShippingLineId?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;">
		</td>
	</tr>
        <tr>
               <td colspan="4" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($debitNoteRecs)>0) {
		$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>D/B NO</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>S/LINE</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>B/L NO</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>FREIGHT</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>BKG<br>(2%)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>EX.RATE</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>BILL AMOUNT</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>GROSS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>TDS</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>NET</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>CHQ NO</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>DATE</td>
	</tr>
	<?php
	$totBillAmt = 0;
	$totGrossAmt = 0;
	$totTdsAmt = 0;
	$totNetAmt = 0;
	$totFreight = 0;
	$totBkgFreight = 0;
	foreach ($debitNoteRecs as $dnr) {
		$i++;
		$shippingLine = $dnr[1];
		$billLaddingNo		= $dnr[2];		
		$billAmt	= $dnr[4];
		$grossAmt	= $dnr[5];
		$tdsAmt		= $dnr[6];
		$netAmt		= $dnr[7];
		$chqNo		= $dnr[8];
		$chqDate	= ($dnr[9]!='0000-00-00' && $dnr[9]!="")?date('d.m.Y', strtotime($dnr[9])):"";
		$freight	= $dnr[10];
		$bkgFreight = $dnr[11];
		$exRate		= $dnr[12];
		$expInvNum	= $dnr[13];
		
		$totFreight		+= $freight;
		$totBkgFreight		+= $bkgFreight;
		$totBillAmt		+= $billAmt;
		$totGrossAmt	+= $grossAmt;
		$totTdsAmt		+= $tdsAmt;
		$totNetAmt		+= $netAmt;
	?>
	<tr bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$expInvNum?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$shippingLine;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left">
			<?
				if (!preg_match("/^[0]*$/",trim($billLaddingNo))) {
					echo $billLaddingNo;
				}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$freight;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$bkgFreight;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$exRate;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$billAmt;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$grossAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$tdsAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$netAmt;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$chqNo;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$chqDate;?></td>
	</tr>
	<?
		}
	?>
	<tr bgcolor="WHITE">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap colspan="3" align="right">Total:</td>		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totFreight,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totBkgFreight,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totBillAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totGrossAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totTdsAmt,2,'.','')?></b></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap align="right"><b><?=number_format($totNetAmt,2,'.','')?></b></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>&nbsp;</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>&nbsp;</td>
	</tr>		
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
	</td>
	</tr>
	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
	<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDNReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selShippingLine=<?=$selShippingLineId?>',700,600);">
<? }?>
		</td>
		</tr>
	<?php 
		} else if ($dateFrom!="" && $dateTill!="" && $searchMode) {			
	?>
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