<?
	require("include/include.php");

	$fromDate	=	$g["fromDate"];
	$tillDate	=	$g["tillDate"];
	$offset		=	$g["offset"];
	$limit		=	$g["limit"];

	$supplyCostRecords 	= 	$rmsupplycostObj->filterAllSupplyCostRecords($fromDate, $tillDate, $offset, $limit);
	$supplyCostRecordsSize	=	sizeof($supplyCostRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;RM Supply Cost</td>
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
								<th colspan="2" style="padding-left:10px;padding-right:10px;" >
					<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999" class="print">
                <?
			if( sizeof($supplyCostRecords) > 0 )
				{
					$i	=	0;
		?>
	         <tr  bgcolor="#f2f2f2" align="center">                        
                        <th class="listing-head" nowrap>Wt Challan No </th>
                        <th class="listing-head" align="center">
                                Ice
                              </th>
			<th class="listing-head" align="center">
                                Transportation
                              </th>
			<th class="listing-head" align="center">Commission</th>
			<th class="listing-head" align="center">Handling charge</th>
			<th class="listing-head" align="center">Total Amt</th>					
                      </tr>
                      <?
			$totalAmt = "";
			foreach($supplyCostRecords as $scr)
			{
				$i++;
				$supplyCostId	=	$scr[0];
				$challanNumber	=	$scr[1];
				$dailyCatchMainEntryId = $scr[21];
				$totalIceCost	=	$scr[4];
				$fixedIceCost	=	$scr[5];
				$displyIceCost = "";
				if ($fixedIceCost!=0) {
					$displyIceCost  = $fixedIceCost;
				} else {
					$displyIceCost  = $totalIceCost;
				}

				$totalTransCost 	= $scr[8];
				$fixedTransCost		= $scr[9];
				$displyTransCost = "";
				if ($fixedTransCost!=0) {
					$displyTransCost  = $fixedTransCost;
				} else {
					$displyTransCost  = $totalTransCost;
				}	
			
				// Detailed Sum	Section
				$commissionTotalAmt = $scr[19];		
				$handlingTotalAmt   = $scr[20];

				$totalCommiRate		= $scr[12];
				$fixedCommiRate		= $scr[13];
				$displyCommiCost = "";
				if ($fixedCommiRate!=0) {
					$displyCommiCost  = $fixedCommiRate;
				} else if ($totalCommiRate!=0) {
					$displyCommiCost  = $totalCommiRate;
				} else if ($commissionTotalAmt!=0) {
					$displyCommiCost = $commissionTotalAmt;
				}

				$totalHandlingAmt = $scr[17];
				$fixedHandlingAmt = $scr[18];				
				$displayHandlingCost = "";
				if ($fixedHandlingAmt!=0) {
					$displayHandlingCost = $fixedHandlingAmt;
				} else if ($totalHandlingAmt!=0) {
					$displayHandlingCost = $totalHandlingAmt;
				} else if ($handlingTotalAmt!=0) {
					$displayHandlingCost = $handlingTotalAmt;
				}
				//$displayHandlingCost = ($fixedHandlingAmt!=0)?$fixedHandlingAmt:$totalHandlingAmt;

				$totalAmt = $displyIceCost + $displyTransCost + $displyCommiCost + $displayHandlingCost;			
				$paidStatus	=	$scr[14];
				$disabled = "";
				if (($paidStatus=='Y' && $reEdit==true) ||  $reEdit==false) {
					$disabled = "disabled";
				}
				$displayRMChallanNum =  $scr[22];
 			?>
                      <tr  bgcolor="WHITE">
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayRMChallanNum;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyIceCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyTransCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displyCommiCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displayHandlingCost?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><b><? echo number_format($totalAmt,2,'.','');?></b></td>			
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
                        <td colspan="7"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?></td>
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
