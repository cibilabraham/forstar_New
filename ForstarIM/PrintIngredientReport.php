<?
	require("include/include.php");

	if ($g["selDate"]!="") {
		$reportDate	=	$g["selDate"];		
	} else if($p["selDate"]=="") {
		$reportDate	=	date("d/m/Y");
	} else {
		$reportDate	=	$p["selDate"];
	}
	
	$dateS		=	explode("/",$reportDate);
	$selectDate	=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
	//$selectDate		= mysqlDateFormat($reportDate);
	$lastDate  	= date("Y-m-d",mktime(0, 0, 0,$dateS[1],$dateS[0]-1,$dateS[2])); //latest record before the date

	# List all Stocks
	$ingredientRecords = $ingredientReportObj->fetchIngredientRecords($selectDate);
	
	$dateSelected = dateFormat($reportDate);
		
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Report</td>
								<td  background="images/heading_bg.gif" class="pageName" nowrap style="padding-right:10px;" align="right">Date:<?=$reportDate?></td>
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
					<?
					if (sizeof($ingredientRecords)) {
					$i=0;
					?>
							<tr>
								<td width="1" ></td>
								<td colspan="3" >
<table width="80%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">RM Item </td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Opening Balance Qty </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Accepted Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Used Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Closing Balance Qty</td>
	  	
        </tr>
	<?
	foreach ($ingredientRecords as $sr) {
		$i++;
		$ingredientId	=	$sr[0];
		$ingredientName	=	stripSlash($sr[1]);
		
		$acceptedQty 	=	$sr[3];
		$usedQty	=	$sr[4];
		#Find the opening Qty
		$openingQty = $ingredientReportObj->getOpeningQty($ingredientId, $lastDate);

		$closingBalanceQty = ($openingQty + $acceptedQty)- $usedQty;	
		
	?>
        <tr bgcolor="#FFFFFF" title="<?=$displayTitle?>">
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$ingredientName?></td>
               <td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$acceptedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$usedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$closingBalanceQty?></td>
         </tr>
         <? }	?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table>								</td>
							</tr>
							<? } else {?>
							<tr>
								<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
							</tr>
							<? }?>
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
