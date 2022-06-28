<?
	require("include/include.php");

	# List all RM Test Data 
	# List all PHT Montoring 
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$WeightGradingRecords	=	$weightmentAfterGradingObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		$WeightGradingSize		=	sizeof($WeightGradingRecords);
	}
	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="94%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;WEIGHTMENT AFTER GRADING </td>
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
									<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
			<?
			if ($WeightGradingSize > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Rm lot Id</td>
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Fish</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Process code</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Grade</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Weight</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Wt</td>
				<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Diff in Weight</td>-->
				
</tr>
<?
	foreach($WeightGradingRecords as $sir) {
		$i++;
		$WeightGradingId	=	$sir[0];
		$LotId		=	$sir[1];
		$alpha		=	$sir[2];
		//$lot = $weightmentAfterGradingObj->getLotNm($LotId);
		//$newLot=$lot[1];
		$supplierName		=	$sir[3];
		$PondName		=	$sir[4];
		$sumtotal		=	$sir[5];
		$differ		=	$sir[7];
		$active		=	$sir[8];
		$method = $weightmentAfterGradingObj->getWeightAfterGradingDetail($WeightGradingId);
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alpha.$LotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php 
			foreach($method as $detail)
			{
			echo $fish=$detail[8];
			echo '<br/>';
			}
			?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $processcode=$detail[9];
			echo '<br/>';
			}
			?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
		<?php 
		foreach($method as $detail)
		{
		$gradeID=$detail[1];
		$grade = $weightmentAfterGradingObj->getGradeNm($gradeID);
		echo $grade[1];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($method as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> 
		</td>
		
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sumtotal;?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$differ;?></td>-->
		
		
	</tr>
	<?
		}
	} else {
	?>
	<tr bgcolor="white">
		<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
