<?php
	require("include/include.php");
	
	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];
	
	$rmlotid		= $g["rmlotid"];	
	$supplier		= $g["supplier"];
	
	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTo);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	$rmLotRecords = $rmVarianceReportObj->getRmLotRec($fromDate,$tillDate,$rmlotid);
	
	/*$processorRec	=	$preprocessorObj->find($selProcessorId);*/

	# Get Company Details
	list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;/&nbsp;".$faxNo;	

	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();	

	$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
?>
<html>
<head>
<title>RM Variance Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function printDoc()
{
	window.print();	
	return false;
}

function printThisPage(printbtn)
{	
	document.getElementById("printButton").style.display="none";	
	if (!printDoc()) {
		document.getElementById("printButton").style.display="block";			
	}		
}
</script>
</head>
<body>
	<table width="90%" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this)" style="display:block"></td>
		</tr>
	</table>
	<table width="90%" align="center" cellpadding="0" cellspacing="0">
		<tr bgcolor=white>
			<td colspan="17" align="center" class="listing-head" height="10"><img src="images/ForstarLogo.png" alt=""></td>
		</tr>
		<tr>
			<td align="right">
				<table cellpadding="1" cellspacing="1" >
					<tr>
						<TD>
							<table cellpadding="0" cellspacing="0">
								<TR>
									<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$exportAddrArr[1]?></font></TD>
								</TR>													
							</table>
						</TD>
					</tr>
					<tr>
						<TD class="print-SOTHead-item"><?=$exportAddrArr[2]?></TD>
					</tr>
					<tr>
						<TD class="print-SOTHead-item"><?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?></TD>
					</tr>							
					<tr>
						<TD class="print-SOTHead-item"><?php 
						foreach($exportAddrContact as $expt1)
						{
							if($expt1[1]!='') echo $expt1[1].',';
						}
						?>
						</TD>
					</tr>
					<tr>
						<TD class="print-SOTHead-item"><?php 
						foreach($exportAddrContact as $expt2)
						{
							if($expt2[2]!='')  echo $expt2[2].',';
						}
						?>
						</TD>
					</tr>
					<tr>
						<TD class="print-SOTHead-item">
						<?php 
						foreach($exportAddrContact as $expt3)
						{
							if($expt3[3]!='') echo  $expt3[3].',';
						}
						?>
						</td>
					</tr>
					<tr>
						<TD class="print-SOTHead-item"><?php 
						foreach($exportAddrContact as $expt4)
						{
							if($expt4[4]!='') echo $expt4[4].',';
						}
						?>
						</TD>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
		<tr>
			<td align="right" bgcolor="#fff">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-head" height="5">
						</td>
					</tr>
					
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-item" height="5"></td>
					</tr>
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-item"><font size="2"><b>RM Variance Report</b> </font></td>
					</tr>
					<tr bgcolor=white>
						<td colspan="17" align="center" class="listing-item" height="5"></td>
					</tr>
					<tr bgcolor=white> 
						<td colspan="17" align="center" class="listing-item">
							<table width="100%">
								<tr>
									<td>
										<table border="0" align="center">
											<tr>
												<td class="fieldName">From:</td>
												<td class="listing-item" nowrap> <?=$dateFrom?> </td>
												<td class="fieldName" nowrap>&nbsp; Till: </td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$dateTo?> </td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
											<tr   bgcolor="#f2f2f2" align="center">	
												<td  class="listing-head" style="padding-left:5px; padding-right:5px; height:40px; background-color:#f2f2f2;" colspan="4" >Procurement</td>
												<td  class="listing-head" style="padding-left:5px; padding-right:5px;  background-color:#f2f2f2;" colspan="7" >Receiving</td>
											</tr>
											<tr  bgcolor="#f2f2f2" align="center" >
												<!--td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">PO</td-->
												<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px; background-color:#f2f2f2;"  nowrap>RM Lot Id</td>
												<td colspan="3" style="background-color:#f2f2f2;">
													<table width="332px" cellpadding='0' cellspacing='0' border='0'>
														<tr>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999; background-color:#f2f2f2;"  nowrap>Supplier</td>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999; background-color:#f2f2f2;"  >Gate Supervisor</td>
															<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px; background-color:#f2f2f2;"  nowrap>Date</td>
														</tr>
													</table>
												</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999;background-color:#f2f2f2;"  nowrap>Supervisor</td>
												<td colspan="6" style="background-color:#f2f2f2;">
													<table width="600px"  cellpadding='0' cellspacing='0' border='0'>
														<tr >	
															<td  class="listing-head"   style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Date</td>
															<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; height: 38px; background-color:#f2f2f2;" nowrap>Variety </td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:170px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Item Name</td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Count</td>
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999; background-color:#f2f2f2;"  nowrap>Qty</td>	
															<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; background-color:#f2f2f2;" nowrap>Soft%</td>
														</tr>
													</table>
												</td>
											</tr>
											<?php
											$i = 0; $j=0;														
											#Setting No.of Rows
											$numRows 	=	25;
											$totalCount	= 0;
											$totalQty	= 0;
											$rmLotRecordsSize = sizeof($rmLotRecords);
											$totalPage = ceil($rmLotRecordsSize/$numRows);
											foreach($rmLotRecords as $rmlot)
											{
												$i++;
												($rmlot[2]!=0)? $rmlotid=$rmlot[2]:$rmlotid=$rmlot[0];
												$rmVarianceRecords = $rmVarianceReportObj->getDFPForADateRmLot($rmlotid,$supplier);
											?>
											<tr>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$rmlot[1];?></td>
												<td colspan="10" bgcolor="#fff">
													<table  cellpadding='1' cellspacing='1' width="100%" border="0" bgcolor="#ccc">
													<?php
													foreach ($rmVarianceRecords as $dfpr) 
													{
														$supplierNm=$dfpr[0];
														$gateSupervisor=$dfpr[1];
														$date=dateformat($dfpr[2]);
														$supervisor=$dfpr[3];
														$supplierId=$dfpr[6];
														$farmId=$dfpr[7];
														$WeighmentRecords	= $rmVarianceReportObj->getGroupedWeighment($rmlot[0],$supplierId,$farmId);
														//$i++;
													?>
														<tr  bgcolor="#fff">	
															<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$supplierNm?></td>	
															<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$gateSupervisor?></td>
															<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$date?></td>
															<!--<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$eUCode;?></td>
															<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$brand?></td>
															<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$frozenCode;?></td>
															<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
															<td class="listing-item"  style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
															<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>
															<td class="listing-item" style="padding-left:5px; padding-right:5px;" width="8%">&nbsp;<?=$mCPackingCode?></td>-->
															<td class="listing-item"  style="padding-left:5px; padding-right:5px; width:100px;" >&nbsp;<?=$supervisor?></td>
															<!--<td>
															<table border="0" cellpadding="2" cellspacing="0" width="100%">

															</table>
															</td>-->
															<td colspan="6" align="center" >&nbsp;
																<table border="0" cellpadding="1" cellspacing="1"  width="100%"  bgcolor="#fff" >
																<?php
																if ( sizeof($WeighmentRecords)) 
																{
																?>
																	<?php
																	foreach ( $WeighmentRecords as $wr ) 
																	{
																		$weightmentDate		=	dateformat($wr[0]);
																		$variety			=	$wr[1];
																		$itemName			=	$wr[2];
																		$count			=	$wr[3];
																		$totalCount+=$count;
																		$weight		=$wr[4];	
																		$totalQty+=$weight;
																		$soft		=$wr[5];	
																	?>
																		<tr>	
																			<td class="listing-item" nowrap style="width:100px;  padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$weightmentDate?></td>
																			<td class="listing-item" nowrap style="width:100px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$variety?></td>
																			<td class="listing-item" nowrap style="width:170px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" >&nbsp;<?=$itemName?></td>
																			<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">&nbsp;<?=$count;?></td>
																			<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">&nbsp;<?=$weight;?></td>
																			<td class="listing-item" nowrap style="width:50px; padding-left:5px; padding-right:5px; " align="right">&nbsp;<?=$soft;?></td>
																		</tr>
																		<?php
																		}
																	} 
																	?>
																	</table>
																</td>
															</tr>
															<?
															}
															?>
														</table>
													</td>
												</tr>
												<?
												if ($i%$numRows==0 && $rmLotRecordsSize!=$numRows) {
													$j++;
												?>
											</table>
										</td>
									</tr> 
									<tr>
										<td bgcolor="#fff">
											<table width="98%" align="center" cellpadding="2">
												<tr>
													<td colspan="6" height="5"></td>
												</tr>
												<tr valign="top">
													<td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
													<td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
													<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
													<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
													<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
													<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
												</tr>
												<tr valign="top">
													<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
												</tr>
												<tr valign="top">
													<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<!-- Setting Page Break start Here-->
		<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
		<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
			<tr>
				<td>
					<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-head" height="5"></td>
						</tr>
						 
						<TR bgcolor="#fff" align="center">
							<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$exportAddrArr[1]?></font></TD>
						</TR>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item" height="5"></td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item"><font size="2"><b>RM Variance Report</b> </font>- Cont.</td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item" height="5"></td>
						</tr>
						<tr bgcolor=white> 
							<td colspan="17" align="center" class="listing-item">
								<table width="100%">
									<tr>
										<td>
											<table align="center">
												<tr>
    												<td class="fieldName">From:</td>
													<td class="listing-item" nowrap><?=$dateFrom?></td>
													<td class="fieldName" nowrap>&nbsp; Till: </td>
													<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$dateTo?> </td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
									<tr  bgcolor="#f2f2f2" align="center">	
										<td class="listing-head" style="padding-left:5px; padding-right:5px; height:40px; background-color:#f2f2f2;" colspan="4" >Procurement</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px; background-color:#f2f2f2;" colspan="7" >Receiving</td>
									</tr>
									<tr  bgcolor="#f2f2f2" align="center" >
										<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px; background-color:#f2f2f2;"  nowrap>RM Lot Id</td>
										<td colspan="3" style="background-color:#f2f2f2;">
											<table width="332px" cellpadding='0' cellspacing='0' border='0'>
												<tr>
													<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999; background-color:#f2f2f2;"  nowrap>Supplier</td>
													<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px;  border-right:1px solid #999999; background-color:#f2f2f2;"  >Gate Supervisor</td>
													<td class="listing-head" style="width:100px; padding-left:5px; padding-right:5px; background-color:#f2f2f2;"  nowrap>Date</td>
												</tr>
											</table>
										</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; background-color:#f2f2f2;"  nowrap>Supervisor</td>
										<td colspan="6" style="background-color:#f2f2f2;">
											<table width="600px" cellpadding='0' cellspacing='0' border='0'>
												<tr >	
													<td  class="listing-head"   style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Date</td>
													<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; border-right:1px solid #999999; background-color:#f2f2f2; height: 38px" nowrap>Variety </td>
													<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:170px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Item Name</td>
													<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999; background-color:#f2f2f2;" nowrap>Count</td>
													<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; border-right:1px solid #999999; background-color:#f2f2f2;"  nowrap>Qty</td>	
													<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:50px; background-color:#f2f2f2;" nowrap>Soft%</td>
												</tr>
											</table>
										</td>
									</tr>
									<?
									}
									?>
								<? 
								}
								?>
								<tr bgcolor="White">	
									<td width="450px" class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="5" align="right">Total:</td>	
									<td colspan="3" width="410px">&nbsp;</td>
									<td  width="60px" align="right" style="padding-right:5px"><strong><?=$totalCount?></strong></td>
									<td  width="60px" align="right" style="padding-right:5px"><strong><?=$totalQty?></strong></td>
									<td  width="64px">&nbsp;</td>
									

									

								<!--	<td>
										<table border="0" cellpadding="1" cellspacing="1" width="100%">
											<tr>
												<td colspan="3" class="listing-item" nowrap  style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																							&nbsp;</td>
												<td class="listing-item" nowrap  style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																								<strong><?=$totalCount?></strong>
																							</td>
												<td class="listing-item" nowrap  style="width:50px; padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right">
																								<strong><?=$totalQty?></strong>
																							</td>
												<td style='width:59px;'>&nbsp;</td>
											</tr>
										</table>
										<input type="hidden" name="hdnRowCount" id="hdnRowCount" value="<?=$i?>" readonly />
									</td>	-->
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=white> 
			<td colspan="17" align="center" class="fieldName">
				<table width="98%" cellpadding="2">
					<tr>
						<td colspan="6" height="5"></td>
					</tr>
					<tr valign="top">
						<td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
						<td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
						<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
						<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
						<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
						<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
					</tr>
					<tr valign="top">
						<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
					</tr>
					<tr valign="top">
						<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
					</tr>
				</table>
			</td>
		</tr>
 
  <SCRIPT LANGUAGE="JavaScript">
	
	window.print();
	
	</SCRIPT>
	</table>
	</td>
</tr>
</table>
</body>
</html>


