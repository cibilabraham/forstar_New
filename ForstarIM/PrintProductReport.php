<?
	require("include/include.php");

	if ($g["selDate"]!="") $reportDate = $g["selDate"];		
	else if($p["selDate"]=="") $reportDate = date("d/m/Y");
	else $reportDate = $p["selDate"];
	
	$dateS		=	explode("/",$reportDate);
	$selectDate	=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
	//$selectDate		= mysqlDateFormat($reportDate);
	$lastDate  	= date("Y-m-d",mktime(0, 0, 0,$dateS[1],$dateS[0]-1,$dateS[2])); //latest record before the date

	// List all Product
	$productRecords = $productReportObj->fetchProductRecords($selectDate);
	
	$dateSelected = dateFormat($reportDate);		
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Report</td>
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
					if (sizeof($productRecords)) {
					$i=0;
					?>
							<tr>
								<td width="1" ></td>
								<td colspan="3" >
<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	<?
	if (sizeof($productRecords)) {
	$i=0;
	?>
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Product</td>
                <td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Code</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Net Wt <br>(Gms)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Packs under observ - OB</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Prodn</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Samp</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Test</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Wastage & Spoilage</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Des-Patch</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:11px;">Packs under observ - CB</td>
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
               <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:11px;" nowrap><?=$productName?></td>
               <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$productCode?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$productNetWt?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$openingQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$t?></td>
		 <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:11px;" nowrap><?=$t?></td>
               <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$t?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$t?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$despatchQty?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:11px;"><?=$closingBalanceQty?></td>
         </tr>
         <? }
	 }
	?>
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
