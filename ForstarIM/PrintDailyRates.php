<?php
	require("include/include.php");

	$recordsFilterId				= $g["selFilter"];
	$recordsDate					= $g["selDate"];
	$supplierFilterId 				= $g["supplierFilter"];
	if ($recordsFilterId!=0 || $recordsDate!="") {	
		$dailyRatesRecords	=	$dailyratesObj->dailyRateRecFilter($recordsFilterId,$recordsDate,$supplierFilterId);
	} /*else {
		$dailyRatesRecords	=	$dailyratesObj->fetchAllRecords();
	}*/
	
	$dailyRatesRecordsSize		=	sizeof($dailyRatesRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Rates On <?=dateFormat($recordsDate);?></td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if (sizeof($dailyRatesRecords)>0) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2"  >		
		<td width="58" class="listing-head" nowrap style="padding-left:2px; padding-right:2px;font-size:11px;">Date</td>
		<td width="37" nowrap class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Landing Center</td>
		<td width="37" nowrap class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Supplier</td>
		<td width="37" nowrap class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Fish</td>
		<td width="56" nowrap class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Grade</td>
		<td width="68" align="center" class="listing-head" nowrap style="padding-left:2px; padding-right:2px;font-size:11px;">Process Code </td>
		<td width="68" align="center" class="listing-head" nowarp style="padding-left:2px; padding-right:2px;font-size:11px;">Count Average</td>
		<td width="89" align="center" class="listing-head" nowarp style="padding-left:2px; padding-right:2px;font-size:11px;">Market Rate </td>
		<td width="90" align="center" nowrap class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;">Decl.Rate</td>		
	</tr>
	<?
	$selSupplierId = "";	
	foreach ($dailyRatesRecords as $dr) {
		$i++;
		$dailyRateId	=	$dr[0];		
		$enteredDate		= 	dateFormat($dr[4]);
		$selLandingCenterId 	= $dr[3];
			$centerRec		= $landingcenterObj->find($selLandingCenterId);
			$landingCenterName	= ($centerRec[1]!="")?$centerRec[1]:"ALL";
		$selSupplierId		= $dr[5];
			$supplierRec	= $supplierMasterObj->find($selSupplierId);
			$selSupplierName = ($supplierRec[2]!="")?$supplierRec[2]:"ALL";
		$marketRate		=	$dr[6];
		$decRate		=	$dr[7];
		$processCodeRecs	=	$processcodeObj->find($dr[9]);		
		$process_code		=	stripSlash($processCodeRecs[2]);
		$fishName		=	$dr[10];
		$gradeId		=	$dr[2];
		$gradeRec		=	$grademasterObj->find($gradeId);
		$gradeCode		=	$gradeRec[1];
		$count			=	$dr[8];
		if($count==0) $count="";
		$dailyRatesEntryId	= $dr[11];	
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$enteredDate?></td>
		<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$landingCenterName;?></td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$selSupplierName;?></td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$fishName;?></td>
		<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$gradeCode;?></td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$process_code?></td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$count?></td>		
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$marketRate?></td>
		<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;"><?=$decRate;?></td>		
	</tr>
	<?
		}
	?>
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="9"  class="err1" height="9" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
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
