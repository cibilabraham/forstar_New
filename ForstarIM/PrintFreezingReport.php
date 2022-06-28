<?php
	require("include/include.php");
	
	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];
	
	$rmlotid		= $g["rmlotid"];	
	
	$Date1			=	explode("/",$dateFrom);
	$fromDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$Date2			=	explode("/",$dateTo);
	$tillDate		=	$Date2[2]."-".$Date2[1]."-".$Date2[0];

	$freezingRecords = $freezingReportObj->getFreezingDetail($fromDate,$tillDate,$rmlotid);

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
<title>Pending RM Lot Report</title>
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
						<td colspan="17" align="center" class="listing-item"><font size="2"><b>Freezing Report</b> </font></td>
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
											<tr  bgcolor="#f2f2f2" align="center" >
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Sl no.</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>Date</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>RM Lot Id</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Fish</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Brand</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Packing</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Glaze</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Weight</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Grade</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>MC</td>
												<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>LS</td>
											</tr>
											
											<?php
											$i = 0; $j=0;														
											#Setting No.of Rows
											$numRows 	=	15;
											$totalCount	= 0;
											$totalQty	= 0;
											$freezingRecordsSize = sizeof($freezingRecords);
											$totalPage = ceil($freezingRecordsSize/$numRows);
											$oldFreezeDate=""; $oldfish=''; $oldbrand=''; $oldmcPack='';
											foreach($freezingRecords as $fr)
											{	$i++;
												$freezId=$fr[0];
												$freezDate=dateFormat($fr[1]);
												$rmLotIdName=$fr[3];
												$fish=$fr[5];
												$brand=$fr[7];
												$mcPack=$fr[9];
												(($oldFreezeDate!=$freezDate) || ($oldj!=$j))?$frzDate=$freezDate:$frzDate="";
												($oldfish!=$fish || ($oldj!=$j))?$fishName=$fish:$fishName="";
												($oldbrand!=$brand || ($oldj!=$j))?$brandName=$brand:$brandName="";
												($oldmcPack!=$mcPack || ($oldj!=$j))?$mcPackName=$mcPack:$mcPackName="";
												$grade=$fr[11];
												$mc=$fr[12];
												$lc=$fr[13];
												$wt=$fr[14];
												($fr[15]!="0")?$glaze=$fr[15]:$glaze="";
											?>
											<tr>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$i?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$frzDate?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$rmLotIdName?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$fishName?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$brandName?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$mcPackName?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$glaze?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$wt?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$grade?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$mc?></td>
												<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$lc?></td>
											</tr>
												<?
												$oldj=$j;
												if ($i%$numRows==0 && $freezingRecordsSize!=$numRows) {
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

						<tr bgcolor=white> 
							<td colspan="17" align="center" class="listing-head" ><font size="3"><?=$exportAddrArr[1]?></font> </td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="right" class="listing-item" height="5"></td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item" height="5"></td>
						</tr>
						<tr bgcolor=white>
							<td colspan="17" align="center" class="listing-item"><font size="2"><b>Freezing Report</b> </font> - Cont.</td>
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
									<tr  bgcolor="#f2f2f2" align="center" >
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap></td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>Date</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>RM Lot Id</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Fish</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Brand</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Packing</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Glaze</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Weight</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Grade</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>MC</td>
										<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>LS</td>
									</tr>
									<?
									}

									$oldFreezeDate=$freezDate;
									$oldfish=$fish;
									$oldbrand=$brand;
									$oldmcPack=$mcPack;
									
									?>
								<? 
								
								}
								?>
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


