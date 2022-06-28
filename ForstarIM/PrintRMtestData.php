<?
	require("include/include.php");

	# List all RM Test Data 
	$rmTestDataRecords	=	$rmTestDataObj->fetchAllRecords();
	$rmTestDataSize		=	sizeof($rmTestDataRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Rm Test Data</td>
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
			if (sizeof($rmTestDataRecords) > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Unit Name</td>
				<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM Lot ID</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">RM Test Name</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Test Method</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date of Testing</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">Result</td>
</tr>
<?
	foreach($rmTestDataRecords as $sir) {
		$i++;
		$rmTestDataId	=	$sir[0];
		//$unit		=	$sir[1];
		$unitRec		=	$plantandunitObj->find($sir[1]);
		$unit		=	$unitRec[2];
		//$rmLotId		=	$sir[2];
		$lotRec		=	$rmTestDataObj->findLot($sir[2]);
		$rmLotId		=	$lotRec[1];

		//$rmTestName		=	$sir[3];
		$testNameRec		=	$rmTestMasterObj->find($sir[3]);
		$rmTestName		=	$testNameRec[1];
		$rmtestMethod		=	$testNameRec[2];
		$dateOfTesting		=	dateFormat($sir[5]);
		$result		=	$sir[6];
	?>
	<tr  bgcolor="WHITE">
	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$unit;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmLotId;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmTestName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$rmtestMethod;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$dateOfTesting;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$result;?></td>
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
