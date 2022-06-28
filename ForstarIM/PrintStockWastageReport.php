<?php	
	require("include/include.php");

	$dateFrom 	= $g["selectFrom"];
	$dateTill 	= $g["selectTill"];
	$repType 	= $g["repType"];
	$repSubType 	= $g["repSubType"];
	
	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Stocks Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintPurchaseOrder">
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<table width='100%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<th>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="4"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
  </tr>
 <!-- <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='98%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='3'>STOCK WASTAGE REPORT FROM <?=$dateFrom;?> TO <?=$dateTill;?></td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
  
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <!--tr bgcolor=white> 
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF ITEMS</td>
  </tr-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <th colspan="17" align="center" Style="padding-left:5px;padding-right:5px;" >
	<table width="100%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2" class="print">
		
	<?
	
	if( $repType=='SW' && ( $repSubType == 'S' || $repSubType == 'D') )
	{
	?>
		 
	<?
			$stockRecords =  $stockWastageReportObj->getSortedStockRecs(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill));

		if( sizeof($stockRecords) > 0 ) 
		{
	?>
		<tr bgcolor="#f2f2f2" align="center">
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Stock Item </th>

			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Lost </th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stolen</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Damaged</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Deteriorated</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Quantity</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Unit<br>Price</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Replacement<br>Cost (Gross)</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Scrap<br>Value</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Replacement<br>Cost (Net)</th>
		 </tr>
	<?

			$gtotalLostQty = 0; // grand total fields 
			$gtotalStolenQty = 0;
			$gtoalDamagedQty = 0;
			$gtotalDeterioratedQty = 0;
			$gtoalTotalQty = 0;
			$gtotalUnitPrice = 0;
			$gtotalReplacementGross = 0;
			$gtotalReplacementNet = 0;
			$gtotalScrapValue = 0; // end grand total fields

			$stotalLostQty = 0;
			$stotalStolenQty = 0;
			$stoalDamagedQty = 0;
			$stotalDeterioratedQty = 0;
			$stoalTotalQty = 0;
			$stotalUnitPrice = 0;
			$stotalReplacementGross = 0;
			$stotalReplacementNet = 0;
			$stotalScrapValue = 0;


			$prevStkId = 0;
			$prevgStkId = 0;
			while( list(, $stk ) = each ( $stockRecords ) )
			{
				$stkId = $stk[2];
				$stkName = $stk[3];
				
				if( $repSubType == 'S' )
				{
					$lostQty = 0;
					$stolenQty = 0;
					$dmgdQty = 0;
					$deterioQty = 0;
					$scrapValue = 0;
					$totalAmt = 0;

					$tqRecs = $stockWastageReportObj->getWastageDetialsofStock($stkId,mysqlDateFormat($dateFrom),mysqlDateFormat($dateTill),$repSubType,"");

					foreach($tqRecs as $tqr )
					{
						$reasonType = $tqr[3];
						$scrapValue = $tqr[1];
						$totalAmt = $tqr[2];

						if( $reasonType=='L' )  $lostQty += $tqr[0];
						else if( $reasonType=='S' )  $stolenQty += $tqr[0];
						else if( $reasonType=='D' )  $dmgdQty += $tqr[0];
						else if( $deterioQty=='DR' )  $deterioQty += $tqr[0];
						else 
						{
							$lostQty = 0;
							$stolenQty = 0;
							$dmgdQty = 0;
							$deterioQty = 0;
						}
					}
					$unitPriceOfStock = $stockWastageReportObj->getUnitPriceOfStock($stkId);
					$totalQuantity = ( $lostQty + $stolenQty ) + ( $dmgdQty + $deterioQty );
					$totalReplacementGross = number_format(( $totalQuantity * $unitPriceOfStock ),2,'.','');
					$totalReplacementNet = number_format(( $totalReplacementGross - $scrapValue ),2,'.','');
					
					$gtotalLostQty = $gtotalLostQty+$lostQty; // grand total fields 
					$gtotalStolenQty = $gtotalStolenQty+$stolenQty;
					$gtoalDamagedQty = $gtoalDamagedQty+$dmgdQty;
					$gtotalDeterioratedQty = $gtotalDeterioratedQty+$deterioQty;
					$gtoalTotalQty = $gtoalTotalQty+$totalQuantity;
					$gtotalUnitPrice = $gtotalUnitPrice+$unitPriceOfStock;
					$gtotalReplacementGross = $gtotalReplacementGross+$totalReplacementGross;
					$gtotalReplacementNet = $gtotalReplacementNet+$totalReplacementNet;
					$gtotalScrapValue = $gtotalScrapValue+$scrapValue; // end grand total fields

	?>
        <tr bgcolor="white">
			<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$stkName?></td>
            <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $lostQty!=0 ) echo $lostQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $stolenQty!=0 ) echo $stolenQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $dmgdQty!=0 ) echo $dmgdQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $deterioQty!=0) echo $deterioQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalQuantity?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$unitPriceOfStock?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementNet;?></td>
		</tr>
	<? 
				}
				else if( $repSubType == 'D' )
				{
					
				

					if( $prevStkId != $stkId )
					{
						echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><u><b>'.$stkName.'</b></u></td></tr>';
					}
					
					
					
					
					//$prevgStkId = 0;

					$wastageDepRecs = $stockWastageReportObj->getWastageDepartmentRecs($stkId,mysqlDateFormat($dateFrom),mysqlDateFormat($dateTill));
					$i = 0;
					foreach( $wastageDepRecs as $dipRec )
					{
						$deptId = $dipRec[1];
						$deptName = $dipRec[2];
						$gStkId = $dipRec[3];

						$tqRecs = $stockWastageReportObj->getWastageDetialsofStock($stkId,mysqlDateFormat($dateFrom),mysqlDateFormat($dateTill),$repSubType,$deptId);
						$lostQty = 0;
						$stolenQty = 0;
						$dmgdQty = 0;
						$deterioQty = 0;
						$scrapValue = 0;
						$totalAmt = 0;
						
						
						

						foreach($tqRecs as $tqr )
						{
								$reasonType = $tqr[3];
								$scrapValue = $tqr[1];
								$totalAmt = $tqr[2];
															
								
								if( $reasonType=='L' )  $lostQty += $tqr[0];
								else if( $reasonType=='S' )  $stolenQty += $tqr[0];
								else if( $reasonType=='D' )  $dmgdQty += $tqr[0];
								else if( $deterioQty=='DR' )  $deterioQty += $tqr[0];
								else 
								{
									$lostQty = 0;
									$stolenQty = 0;
									$dmgdQty = 0;
									$deterioQty = 0;

									
								}
								
							
								$unitPriceOfStock = $stockWastageReportObj->getUnitPriceOfStock($stkId);
								$totalQuantity = ( $lostQty + $stolenQty ) + ( $dmgdQty + $deterioQty );
								$totalReplacementGross = number_format(( $totalQuantity * $unitPriceOfStock ),2,'.','');
								$totalReplacementNet = number_format(( $totalReplacementGross - $scrapValue ),2,'.','');
									
					}
					$stotalLostQty += $lostQty; // grand total fields 
					$stotalStolenQty += $stolenQty;
					$stoalDamagedQty += $dmgdQty;
					$stotalDeterioratedQty += $deterioQty;
					$stoalTotalQty += $totalQuantity;
					$stotalUnitPrice += $unitPriceOfStock;
					$stotalReplacementGross += $totalReplacementGross;
					$stotalReplacementNet += $totalReplacementNet;
					$stotalScrapValue += $scrapValue; // end grand total fields
								
					$gtotalLostQty += $lostQty; // grand total fields 
					$gtotalStolenQty += $stolenQty;
					$gtoalDamagedQty += $dmgdQty;
					$gtotalDeterioratedQty += $deterioQty;
					$gtoalTotalQty += $totalQuantity;
					$gtotalUnitPrice += $unitPriceOfStock;
					$gtotalReplacementGross += $totalReplacementGross;
					$gtotalReplacementNet += $totalReplacementNet;
					$gtotalScrapValue += $scrapValue; // end grand total fields
		
	?>	
		<tr bgcolor="white">
			<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$deptName;?></td>
             <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $lostQty!=0 ) echo $lostQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $stolenQty!=0 ) echo $stolenQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $dmgdQty!=0 ) echo $dmgdQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $deterioQty!=0) echo $deterioQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalQuantity?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$unitPriceOfStock?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementNet;?></td>
		</tr>
	<?		
						
						//echo "<br> $prevgStkId = $gStkId  ";
				
						
							
					}
						
						if( $prevgStkId != $stkId )
						{

	?>
		<tr bgcolor="white" >
			<th class="listing-head" nowrap align='right' style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
			<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalLostQty;?></th>
			<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalStolenQty;?></th>
			<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalDamagedQty;?></th>
			<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalDeterioratedQty;?></th>
			<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalTotalQty;?></th>
			<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalUnitPrice);?></th>
			<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementGross);?></th>
			<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($stotalScrapValue);?></th>
			<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementNet);?></th>
		</tr>				
	<?		
						$stotalLostQty = 0;
						$stotalStolenQty = 0;
						$stoalDamagedQty  = 0;
						$stotalDeterioratedQty  = 0;
						$stoalTotalQty  = 0;
						$stotalUnitPrice  = 0;
						$stotalReplacementGross  = 0;
						$stotalReplacementNet  = 0;
						$stotalScrapValue  = 0;
						
						}
						$prevgStkId = $stkId;
					$prevStkId = $stkId;
				}
				
				
			}

	?>
	 <tr bgcolor="#F7F7F7" >
		<th class="listing-head" nowrap align='right' style="padding-left:5px; padding-right:5px; font-size:8pt">Grand Total</th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalLostQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalStolenQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtoalDamagedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalDeterioratedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtoalTotalQty;?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($gtotalUnitPrice);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($gtotalReplacementGross);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($gtotalScrapValue);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($gtotalReplacementNet);?></th>
	</tr>
	<?
		}
		else
		{
	?>
		 <tr bgcolor="white" >
			<td class="err1" align='center' colspan='10' >No wastage records found.</td>
		</tr>
	<?
		}
	}
	else if( $repType=='DW' && ( $repSubType == 'S' || $repSubType == 'D') )
	{
	
		$depWiseStkRecs = $stockWastageReportObj->getSortedDeptRecs(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill));
	
			if( sizeof($depWiseStkRecs) > 0 )
			{
	?>
		 <tr bgcolor="#f2f2f2" align="center">
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Department Name </th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Lost </th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Stolen</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Damaged</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Deteriorated</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Quantity</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Replacement<br>Cost (Gross)</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Scrap<br>Value</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap >Total<br>Replacement<br>Cost (Net)</th>
		 </tr>
	<?
				$gtotalLostQty = 0; // grand total fields 
				$gtotalStolenQty = 0;
				$gtoalDamagedQty = 0;
				$gtotalDeterioratedQty = 0;
				$gtoalTotalQty = 0;
				$gtotalUnitPrice = 0;
				$gtotalReplacementGross = 0;
				$gtotalReplacementNet = 0;
				$gtotalScrapValue = 0; // end grand total fields
			
				foreach( $depWiseStkRecs as $dipId=>$stockDetails )
				{
					/*
					echo "<br>DipId=$dipId <pre>";
					print_r($stockDetails);
					echo "</pre>";
					*/
					
					$dipNameRec =  $departmentObj->find($dipId);
					$depName = $dipNameRec[1];
					
					

					if( $repSubType == 'D' || $repSubType == 'S' )
					{
						if( $repSubType == 'D' )echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><u><b>'.$depName.'</b></u></td></tr>';

						$lostQty = 0;
						$stolenQty = 0;
						$dmgdQty = 0;
						$deterioQty = 0;
						$scrapValue = 0;
						$totalAmt = 0;
						$pStkId = 0;

						$stotalLostQty = 0;
						$stotalStolenQty = 0;
						$stoalDamagedQty = 0;
						$stotalDeterioratedQty = 0;
						$stoalTotalQty = 0;
						$stotalReplacementGross = 0;
						$stotalReplacementNet = 0;
						$stotalScrapValue = 0;


						foreach( $stockDetails as $sId =>$rec )
						{
							$stkId = $sId;
							$stockName = $rec[6];
							
							$lostQty = $rec[0];
							$stolenQty = $rec[1];
							$dmgdQty = $rec[2];
							$deterioQty = $rec[3];
							$scrapValue = $rec[7];
							$totalAmt = $rec[5];
							

							$totalQuantity = ( $lostQty + $stolenQty ) + ( $dmgdQty + $deterioQty );
							$totalReplacementGross = formatAmount( $totalAmt );
							$totalReplacementNet = formatAmount( $totalReplacementGross - $scrapValue );
							
							$gtotalLostQty = $gtotalLostQty+$lostQty; // grand total fields 
							$gtotalStolenQty = $gtotalStolenQty+$stolenQty;
							$gtoalDamagedQty = $gtoalDamagedQty+$dmgdQty;
							$gtotalDeterioratedQty = $gtotalDeterioratedQty+$deterioQty;
							$gtoalTotalQty = $gtoalTotalQty+$totalQuantity;
							$gtotalReplacementGross = $gtotalReplacementGross+$totalReplacementGross;
							$gtotalReplacementNet = $gtotalReplacementNet+$totalReplacementNet;
							$gtotalScrapValue = $gtotalScrapValue+$scrapValue; // end grand total fields
							
							$stotalLostQty = $stotalLostQty+$lostQty; // grand total fields 
							$stotalStolenQty = $stotalStolenQty+$stolenQty;
							$stoalDamagedQty = $stoalDamagedQty+$dmgdQty;
							$stotalDeterioratedQty = $stotalDeterioratedQty+$deterioQty;
							$stoalTotalQty = $stoalTotalQty+$totalQuantity;
							$stotalReplacementGross = $stotalReplacementGross+$totalReplacementGross;
							$stotalReplacementNet = $stotalReplacementNet+$totalReplacementNet;
							$stotalScrapValue = $stotalScrapValue+$scrapValue; // end grand total fields

							if( $stkId!=$pStkId )
							{
								if( $repSubType != 'S' )
								{
							
		?>
        <tr bgcolor="white">
			<td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$stockName?></td>
            <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $lostQty!=0 ) echo $lostQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $stolenQty!=0 ) echo $stolenQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $dmgdQty!=0 ) echo $dmgdQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
				<?
					if( $deterioQty!=0) echo $deterioQty;
				?>
			</td>
			<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalQuantity?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$totalReplacementNet;?></td>
		</tr>
	
	<?
								}
							}
							$pStkId = $stkId;
						}
						if( $repSubType == 'D' )
						{

	?>
	<tr bgcolor="#F7F7F7" >
		<th class="listing-head" nowrap align='right' style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalLostQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalStolenQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalDamagedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalDeterioratedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalTotalQty;?></th>
		
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementGross);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($stotalScrapValue);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementNet);?></th>
	</tr>
	<?
						}
						else if ( $repSubType == 'S' )
						{
	?>
	<tr  bgcolor="white">
		<td class="listing-item" nowrap><?=$depName;?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
			<?
				if( $stotalLostQty != 0 ) echo $stotalLostQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
			<?
				if( $stotalStolenQty !=0 ) echo $stotalStolenQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
			<?
				if( $stoalDamagedQty !=0) echo $stoalDamagedQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt">
			<?
				if( $stotalDeterioratedQty!=0 ) echo $stotalDeterioratedQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalTotalQty;?></td>
		
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementGross);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($stotalScrapValue);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementNet);?></td>
	</tr>
	<?
						}
				}
				else if ( $repSubType == 'S' )
				{
	?>
	<tr bgcolor="#F7F7F7" >
		<th class="listing-head" nowrap align='right' style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalLostQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalStolenQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalDamagedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stotalDeterioratedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$stoalTotalQty;?></th>
		
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementGross);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($stotalScrapValue);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($stotalReplacementNet);?></th>
	</tr>		
	<?

				}
			}
		
		
	?>
	<tr bgcolor="#F7F7F7" >
		<th class="listing-head" nowrap align='right' style="padding-left:5px; padding-right:5px; font-size:8pt">Grand Total</th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalLostQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalStolenQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtoalDamagedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtotalDeterioratedQty;?></th>
		<th class="listing-head" align="center" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=$gtoalTotalQty;?></th>
		
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($gtotalReplacementGross);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt" nowrap><?=formatAmount($gtotalScrapValue);?></th>
		<th class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt"><?=formatAmount($gtotalReplacementNet);?></th>
	</tr>
	<?
			}
		else 
		{
	?>
		<tr bgcolor="white" >
		<td class="err1" colspan="9" nowrap align='center'>No wastage records found.</td>
	</tr>
	<?
		}
	}
	?>

	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
       </table>
</td></tr>
<tr bgcolor=white>
    <td colspan="17" align="center"><table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>		  
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>		

	</table>
	</td></tr></table> 

</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>