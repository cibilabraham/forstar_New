<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;

	$redirectLocation = "?selDate=".$p["selDate"]."&pageNo=".$p["pageNo"];

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
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else  {
		//$dateFrom = date("d/m/Y");
		$dateFrom = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))); 
		$dateTill = date("d/m/Y");
	}
	// get selected date end

	$repType 	= ($p["repType"]!="") ? $p["repType"] : 'SW';
	$repSubType 	= ($p["repSubType"]!="") ? $p["repSubType"] : 'S';
		
		
	
	require("template/topLeftNav.php");
?>

	<form name="frmStockWastageReport" action="StockWastageReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="95%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Wastage Report</td>
									<td background="images/heading_bg.gif"  align='right' >
																
									</td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td >
											<table align='center' cellspacing='1'  cellspacing='1' >
												
												
												<tr>
													<td height='2' colspan='3' ></td>
												</tr>
												
												<tr>
													<td class="fieldName" nowrap >Type:&nbsp;<select name='repType' id='repType' >
															<option value='SW' <?if( $repType=='SW') echo 'selected';?> >Stock Wise</option>
															<option value='DW' <?if( $repType=='DW') echo 'selected';?> >Department Wise</option>
														</select>&nbsp;&nbsp;</td>
														
															<?
																$selSumm = "";
																$selDet = "";
																if( $repSubType=='D') $selDet = "checked";
																else if( $repSubType=='S') $selSumm = "checked";

															?>
														
													<td class="fieldName" nowrap="nowrap"  colspan='2' >	
														<INPUT TYPE="radio" NAME="repSubType" <?=$selSumm;?> Style='vertical-align:middle;' value='S' class="chkBox">Summary &nbsp;&nbsp;
														<INPUT TYPE="radio" NAME="repSubType" Style='vertical-align:middle;' value='D' class="chkBox" <?=$selDet;?> >Detailed &nbsp;&nbsp;
														
													</td>
												</tr>
												<tr>
													<td class="fieldName" align='right' nowrap  >
													From:&nbsp; <? 
																	if ($dateFrom=="") $dateFrom=date("d/m/Y");
																?>
																<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>">&nbsp;&nbsp;
													</td>
													<td class="fieldName"  nowrap >	
														To:&nbsp;
														<? 
																	if($dateTill=="") $dateTill=date("d/m/Y");
																?>
																<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>">&nbsp;&nbsp;
													</td>
													<td class="listing-item"  nowrap>	
														<INPUT TYPE="submit" class="button" name="cmdRepSearch" value='Search' >
													</td>
												</tr>
											</table>
									   </td>
                                    </tr>
									<tr>
										<td height='20' ></td>
									</tr>
                                    <tr>		
										<td colspan="3" align="center">
	<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockWastageReport.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&repType=<?=$repType?>&repSubType=<?=$repSubType?>',700,600);" <? //if (!$poReportRecords) echo "disabled";?>>
										</td>
									
	
        </tr>
              <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
         <tr>
              <td colspan="3" nowrap>&nbsp;</td>
         </tr>
         <tr>
           <td  height="10" colspan="3" >
		<table width="100%" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
		
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
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Stock Item </td>

			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Lost </td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Stolen</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Damaged</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Deteriorated</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Quantity</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Unit<br>Price</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Replacement<br>Cost (Gross)</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Scrap<br>Value</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Replacement<br>Cost (Net)</td>
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
						else if( $reasonType=='DR' )  $deterioQty += $tqr[0];
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
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stkName?></td>
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
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$unitPriceOfStock?></td>
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementNet;?></td>
		</tr>
	<? 
				}
				else if( $repSubType == 'D' )
				{
					
				

					if( $prevStkId != $stkId )
					{
						echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><u><b>'.$stkName.'</b></u></td></tr>';
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
								else if( $reasonType=='DR' )  $deterioQty += $tqr[0];
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
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$deptName;?></td>
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
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$unitPriceOfStock?></td>
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementNet;?></td>
		</tr>
	<?		
						
						//echo "<br> $prevgStkId = $gStkId  ";
				
						
							
					}
						
						if( $prevgStkId != $stkId )
						{

	?>
		<tr bgcolor="white" >
			<td class="listing-head" nowrap align='right'>Total</td>
			<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalLostQty;?></td>
			<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalStolenQty;?></td>
			<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalDamagedQty;?></td>
			<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalDeterioratedQty;?></td>
			<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalTotalQty;?></td>
			<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalUnitPrice);?></td>
			<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementGross);?></td>
			<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($stotalScrapValue);?></td>
			<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementNet);?></td>
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
		<td class="listing-head" nowrap align='right'>Grand Total</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalLostQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalStolenQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtoalDamagedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalDeterioratedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtoalTotalQty;?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($gtotalUnitPrice);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($gtotalReplacementGross);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($gtotalScrapValue);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($gtotalReplacementNet);?></td>
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
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Department Name </td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Lost </td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Stolen</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Damaged</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Deteriorated</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Quantity</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Replacement<br>Cost (Gross)</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Scrap<br>Value</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap >Total<br>Replacement<br>Cost (Net)</td>
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
						if( $repSubType == 'D' )echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><u><b>'.$depName.'</b></u></td></tr>';

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
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stockName?></td>
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
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementGross?></td>
			<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=$scrapValue;?></td>
            <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$totalReplacementNet;?></td>
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
		<td class="listing-head" nowrap align='right'>Total</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalLostQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalStolenQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalDamagedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalDeterioratedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalTotalQty;?></td>
		
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementGross);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($stotalScrapValue);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementNet);?></td>
	</tr>
	<?
						}
						else if ( $repSubType == 'S' )
						{
	?>
	<tr  bgcolor="white">
		<td class="listing-item" nowrap><?=$depName;?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
			<?
				if( $stotalLostQty != 0 ) echo $stotalLostQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
			<?
				if( $stotalStolenQty !=0 ) echo $stotalStolenQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
			<?
				if( $stoalDamagedQty !=0) echo $stoalDamagedQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;">
			<?
				if( $stotalDeterioratedQty!=0 ) echo $stotalDeterioratedQty;
			?>
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalTotalQty;?></td>
		
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementGross);?></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($stotalScrapValue);?></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementNet);?></td>
	</tr>
	<?
						}
				}
				else if ( $repSubType == 'S' )
				{
	?>
	<tr bgcolor="#F7F7F7" >
		<td class="listing-head" nowrap align='right'>Total</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalLostQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalStolenQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalDamagedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stotalDeterioratedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$stoalTotalQty;?></td>
		
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementGross);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($stotalScrapValue);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($stotalReplacementNet);?></td>
	</tr>		
	<?

				}
			}
		
		
	?>
	<tr bgcolor="#F7F7F7" >
		<td class="listing-head" nowrap align='right'>Grand Total</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalLostQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalStolenQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtoalDamagedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtotalDeterioratedQty;?></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><?=$gtoalTotalQty;?></td>
		
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($gtotalReplacementGross);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;" nowrap><?=formatAmount($gtotalScrapValue);?></td>
		<td class="listing-head" align="right" style="padding-left:10px; padding-right:10px;"><?=formatAmount($gtotalReplacementNet);?></td>
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
       </table></td></tr>
	<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
				
                               	
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                     
                                      <td colspan="3" align="center">
				                           <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockWastageReport.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&repType=<?=$repType?>&repSubType=<?=$repSubType?>',700,600);" <? //if (!$poReportRecords) echo "disabled";?>></td>
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
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
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
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
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