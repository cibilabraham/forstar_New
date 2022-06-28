<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$fetchAllWeightmentRecs =$objWeighmentDataSheet->fetchAllDateRangeRecords($fromDate, $tillDate);
		$weightmentRecssize	=	sizeof($fetchAllWeightmentRecs);	
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
	 	 Weighment Data Sheet (Farm) </td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?php
									if($weightmentRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Data Sheet No </td>
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">LOT ID</td>
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head">Process Code</td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">Count </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">Qty  </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">Soft% </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">Soft Qty </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">Pht Tag </td>
									</tr>
									<?php
									$i = 1;
									foreach ($fetchAllWeightmentRecs as $sir) {
										$i++;
										$weightmentId	=	$sir[0];
										$rm_lot_id		=$sir[1];
										$rm_lot_idNm		=$sir[17];
										$data_sheet_sl_no		=$sir[2];
										$supplierData	=	$objWeighmentDataSheet->getSupplierData($sir[0]);
										$entryDate		= dateFormat($sir[5]);
										$active=$sir[16];
										$processCode = '';$countCodes = '';$quantities = '';$softPercents = '';
										$softQty = '';
										if (sizeof($supplierData)>0) 
										{
											foreach ($supplierData as $cR) 
											{	
											$processCode.= $cR[15];
											$countCodes.= $cR[7];
											$quantities.= $cR[8];
											$softPercents.= $cR[9];
											$softQty.= $cR[10];
											$processCode.= "<br/>";	
											$countCodes.= "<br/>";
											$quantities.= "<br/>";
											$softPercents.= "<br/>";
											$softQty.= "<br/>";
											}
										}
														// die;
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$data_sheet_sl_no;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rm_lot_idNm;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
											<?php
												echo $processCode;
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?php
												echo $countCodes;
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?php
											echo $quantities;
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
											<?php
												echo $softPercents;
											?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
											<?php
												echo $softQty;
											?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
											<? foreach($supplierData as $cR) 
											{	
												$weightmentEntryId=$cR[0];
												$phtTagStatus	=	$objWeighmentDataSheet->getPhtTagDetail($weightmentEntryId);
												if(sizeof($phtTagStatus)>0 && $phtTagStatus==0)
												{
													echo "Full".'<br/>';
												}
												elseif(sizeof($phtTagStatus)>0 && $phtTagStatus>0)
												{
													echo "Part".'<br/>';	
												}
																
											}
										?>
									</td>
									</tr>
									<?php
									}
									?>
									<?
									}
									else
									{
									?>
									<tr bgcolor="white">
										<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
									}?>
									</table>
								</td>
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
