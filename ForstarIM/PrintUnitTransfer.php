<?
	require("include/include.php");

	# List all RM Test Data 
	$unitTransferDataRecords	=	$unitTransferObj->fetchAllRecords();
	$unitTransferDataSize		=	sizeof($unitTransferDataRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Farm Master</td>
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
			if (sizeof($unitTransferDataRecords) > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM LOT ID</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Supply Details</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Current Unit name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Current Processing Stage</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transfer to Unit Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transfer to Processing Stage</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">New RM LOT ID</td>
</tr>
<?
	foreach($unitTransferDataRecords as $sir) {
		$i++;
		$unitTransferDataId	=	$sir[0];
		//$lotRec		=	$rmTestDataObj->findLot($sir[1]);
		$lotRec		=	$unitTransferObj->findLot($sir[1]);
		$rmlotId		=	$lotRec[1];
		//echo $unit		=	$sir[2];
		//$supplierRec		=	$rmReceiptGatePassObj->find($sir[2]);
		//$supplierDetails		=	$unitRec[14];
		$supplierDetails		=	$sir[2];
		//$rmLotId		=	$sir[2];
		$unitRec		=	$plantandunitObj->find($sir[3]);
		$currentUnitName		=	$unitRec[2];
		//$currentUnitName		=	$sir[3];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[4]);
		$currentProcessingStage		=	$type[1];
		//$currentProcessingStage		=	$sir[4];
		//$rmTestName		=	$sir[3];
		
		$newUnitRec		=	$plantandunitObj->find($sir[5]);
		$unitName		=	$newUnitRec[2];
		$newProcess		=	$rmReceiptGatePassObj->findProcessType($sir[6]);
		$processType		=	$newProcess[1];
		//$newLotRec		=	$unitTransferObj->findLot($sir[7]);
		$lotId		=	$sir[7];
	?>
	<tr  bgcolor="WHITE">
	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierDetails;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentUnitName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentProcessingStage;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unitName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$lotId;?></td>
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
