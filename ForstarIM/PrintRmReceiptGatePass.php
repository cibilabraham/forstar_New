<?
	require("include/include.php");

	# List all RM Test Data 
	$rmReceiptDataRecords	=	$rmReceiptGatePassObj->fetchAllRecords();
	$rmReceiptDataSize		=	sizeof($rmReceiptDataRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;RmRecipt GatePass</td>
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
			if (sizeof($rmReceiptDataRecords) > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Process Type</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM Lot Id</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Procurment Gate Pass Id</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Vehicle Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Driver</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">In Seal Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Result</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">New Seal No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Out Seal</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Verified</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Labours</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Company Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Challan No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Challan Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date Of Entry</td>
</tr>
<?
	foreach($rmReceiptDataRecords as $sir) {
		$i++;
		
		$rmReceiptGatePassId	=	$sir[0];
		//$processType		=	$sir[1];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[1]);
		 $processType		=	$type[1];
		
		$lotId		=	$sir[2];
		//$procurmentGatePassId 		=	$sir[3];
		$gatePass		=	$rmProcurmentOrderObj->find($sir[3]);
		$procurmentGatePassId		=	$gatePass[1];
		
		//$vehicleNumbers		=	$sir[4];
		$vehicle		=	$vehicleMasterObj->find($sir[4]);
		$vehicleNumbers		=	$vehicle[1];
		
		//$driver		=	$sir[5];
		$DriverName		=	$driverMasterObj->find($sir[5]);
		$driver		=	$DriverName[1];
		
		//$inSeal		=	$sir[6];
		$insealNumber		=	$sealNumberObj->find($sir[6]);
		 $inSeal		=	$insealNumber[1];
		
		$result		=	$sir[7];
		
		$sealNo 		=	$sir[8];
		//$sealNum	=	$sealNumberObj->find($sir[8]);
		//$sealNo		=	$sealNum[1];
		
		//$outSeal 		=	$sir[9];
		$oSealNum	=	$sealNumberObj->find($sir[9]);
		$outSeal		=	$oSealNum[1];
		
	   $verif 		=	$sir[10];
		$verifiedBy	=	$employeeMasterObj->find($sir[10]);
		$verified		=	$verifiedBy[1];
		
		$labours 		=	$sir[11];
		//$labour	=	$rmReceiptGatePassObj->find($sir[11]);
		//echo $labours		=	$labour[11];
		
		//$selCompanyName 		=	$sir[12];
		$company	=	$companydetailsObj->find($sir[12]);
		$selCompanyName		=	$company[1];
		
		//$unit		=	$sir[13];
		$untName	=	$plantandunitObj->find($sir[13]);
		$unit		=	$untName[2];
		
		$supplierChallanNo 		=	$sir[14];
		$supplierChallanDate 		=	dateFormat($sir[15]);
		$dateOfEntry		=	dateFormat($sir[16]);
	?>
	<tr  bgcolor="WHITE">
	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$lotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$procurmentGatePassId ;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$vehicleNumbers;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$driver;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$inSeal;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$result ;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sealNo ;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$outSeal ;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$verified;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$labours;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selCompanyName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierChallanNo;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierChallanDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$dateOfEntry;?></td>
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
