<?php
	require("include/include.php");
# select record between selected date

	//$dateFrom = $g["supplyFrom"];
	//$dateTill = $g["supplyTill"];
	$selSupplierId = $g["supplier"];
	$selFish = $g["selFish"];
	$selProcesscode = $g["selProcesscode"];
	$selRecord = $g["selRecord"];
	

	# select record between selected date
	if ($g["supplyFrom"]!="" && $g["supplyTill"]!="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	if($dateFrom!="" && $dateTill!=""){
		$fromDate		=	mysqlDateFormat($dateFrom);
		$tillDate		=	mysqlDateFormat($dateTill);

		$catchEntryResultSetObj = $dailycatchentryObj->filterDateRangeCatchEntryRecords($fromDate, $tillDate, $selRecord, $selSupplierId, $selFish, $selProcesscode);
		$catchEntrySize		=	$catchEntryResultSetObj->getNumRows();		
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="95%" align="center">
	<tr>
		<Td height="10" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Catch </td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" >
									<table  width="50%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">
									<?
									if ($catchEntrySize > 0 ) {
									$i	=	0;
									?>
										<tr  bgcolor="#f2f2f2" align="center"> 
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">RM Lot ID</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Weighment Challan No </th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Count</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Quantity</th>
											 <th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade/Count Adj </th>
											<? if($edit==true){?>
											<? }?>
										</tr>
                     					<?php
											$gradeCode	=	"";
											$effectiveQty	=	"";
											$grandTotalEffectiveQty = "";
											$prevSupplierId = "";
											$prevCatchEntryDate = "";
											$prevEntryProcessCodeId = "";
											$prevEntryCountAverage = "";
											$prevCatchEntryCount  = "";
											$prevGradeCode = "";
											$prevCatchEntryWeighChallanNo = "";
											$totalGradeCtAdj = 0;			
											while ($cer=$catchEntryResultSetObj->getRow()) {
											$i++;
											$catchEntryId		=	$cer[0];
											$catchEntryWeighChallanNo =	stripSlash($cer[1]);
											$challanAlphaCode	=  $cer[18];
											$displayChallanNo 	= ($challanAlphaCode!="")?$challanAlphaCode.$catchEntryWeighChallanNo:$catchEntryWeighChallanNo;
											$catchEntryDate		=	stripSlash($cer[14]);
											$cEntryDate = "";
											if ($prevCatchEntryDate != $catchEntryDate) {
												$cEntryDate	= dateFormat($catchEntryDate);
											}
											$catchEntryFlag		=	$cer[3];
											$catchEntryCount	=	stripSlash($cer[4]);
											$gradeCode = "";
											$raWReceivedBy		=	$cer[10];
											if ($catchEntryCount==""|| $catchEntryCount==0 || $raWReceivedBy=='B' ) {					
												$gradeCode	= $grademasterObj->findGradeCode($cer[5]);
											}
											$recordDailyCatchentryId	=	$cer[6];				
											$processCode		= $processcodeObj->findProcessCode($cer[7]);
											$effectiveQty	=		$cer[8];
											$grandTotalEffectiveQty	+= $effectiveQty;
											$confirmed		=	$cer[9];
											$gradeCountWt		= 	$cer[11];
											$gradeCountReason	=	$cer[12];
											($cer[13]!='0')?$entrySupplierId=$cer[13]:$entrySupplierId=$cer[19];
											
											$entrySupplierName = "";
											if ($prevSupplierId!=$entrySupplierId) {					
												$entrySupplierName	= $supplierMasterObj->getSupplierName($entrySupplierId);
											}
											$displayGradeCount	=	"";
											if ($gradeCountWt!=0) {
												$displayGradeCount = $gradeCountWt."&nbsp;(<span style=\"font-size:10px;\">".$gradeCountReason."</span>)";	
											}
											$totalGradeCtAdj	+= $gradeCountWt;
											$paidStatus 	=	$cer[15];
											$settledStatus 	=	$cer[16];
											$disabled = "";	
											if ($confirmed==1 && $reEdit==false) {
												$disabled = "disabled";
											}
											# If Re Edit true then check paid status and settled status release
											//if($confirmed==1 && $reEdit==true && $paidStatus=='Y' && $settledStatus=='Y') edited 08-01-08
											if (($paidStatus=='Y' || $settledStatus=='Y') && $confirmed==1 && $reEdit==true) {
												$disabled = "disabled";
											}	
											$entryProcessCodeId = $cer[7];
											$entryCountAverage  = $cer[17];
											$rmlotid  = $cer[20];
											$rowColor = "";
											if (($prevEntryProcessCodeId==$entryProcessCodeId && $prevEntryCountAverage== $entryCountAverage && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo) && $entryCountAverage!=0) {					
												$rowColor = "lightYellow";
											} else if (($prevEntryProcessCodeId==$entryProcessCodeId && $prevCatchEntryCount== $catchEntryCount && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo && $catchEntryCount!="") || ($prevEntryProcessCodeId==$entryProcessCodeId && $prevGradeCode==$gradeCode && $prevCatchEntryWeighChallanNo==$catchEntryWeighChallanNo && $gradeCode!="")) {
												$rowColor = "#FFFFCC";					
											} else {
												//$rowColor = "WHITE";
											}
											//These Transactions are incomplete
											//onMouseover="ShowTip('These Transactions are incomplete.');" onMouseout="UnTip();"
											$displayRowMsg = "";
											if ($catchEntryWeighChallanNo=="") $displayRowMsg = "onMouseover=\"ShowTip('These Transactions are incomplete.');\" onMouseout=\"UnTip();\"";
											?>
											<tr  bgcolor="WHITE"  > 
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$cEntryDate?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotid?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
												  <? if($catchEntryWeighChallanNo==""){?>
												  <img src="images/X_N.gif" width="20" height="20">
												  <? } else { echo $displayChallanNo;}?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$entrySupplierName?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$processCode;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;" ><?=$catchEntryCount?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$gradeCode?></td>
												<td class="listing-item" nowrap="nowrap" align="right" style="padding-left:10px; padding-right:10px;"><?=$effectiveQty;?></td>
												<td class="listing-item" nowrap style="padding-left:7px; padding-right:7px;"><?=$displayGradeCount?></td>
											</tr>
                      
											  <?
												
											$prevSupplierId=$entrySupplierId;
											$prevCatchEntryDate = $catchEntryDate;
											$prevEntryProcessCodeId = $entryProcessCodeId;
											$prevEntryCountAverage = $entryCountAverage;
											$prevCatchEntryCount  = $catchEntryCount;
											$prevGradeCode = $gradeCode;
											$prevCatchEntryWeighChallanNo = $catchEntryWeighChallanNo;
											  }
											?>	
												 
											
											<tr bgcolor="#FFFFFF"><td colspan="7" class="listing-head" align="right">Total:</td>
											<td class="listing-item" style="padding-right:10px;" nowrap="nowrap" align="right"><strong><? echo number_format($grandTotalEffectiveQty,2);?></strong></td>
											<td>&nbsp;</td>
											<? if($edit==true){?>
											<? }?>
											</tr>
											  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
									 
											  <?
												}
												
												else
												{
											 ?>
											  <tr bgcolor="white"> 
												<td colspan="9"  class="err1" height="10" align="center"> 
												  <?=$msgNoRecords;?> </td>
											  </tr>
											  <?
													}
											  ?>
											  <input type="hidden" name="addMode" value="<?=$addMode?>">
											  <input type="hidden" name="editMode" value="<?=$editMode?>">
											  <input type="hidden" name="enteredRMId" value="<?=$editId;?>">
											  <input type="hidden" name="dailyCatchentryId" value="<?=$dailyCatchentryId?>">
											  <input type="hidden" name="editId" value="">
											  <input type="hidden" name="editChellan" value="">
											  <input type="hidden" name="editSelectionChange" value="0">
											  <input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>">
											  <input type="hidden" name="catchEntryNewId" value="<?=$catchEntryNewId;?>">
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="3" height="5" ></td>
									</tr>
								</table>
							</td>
						</tr>
				  </table>
					<!-- Form fields end   -->
				</td>
			</tr>	
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		window.print();
		//-->
		</SCRIPT>
	</table>
