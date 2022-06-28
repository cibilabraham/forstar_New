<?
	require("include/include.php");

	#List all Purchase Order
	$fromDate	= $_GET["fd"];
	$tillDate	= $_GET["td"];
			
	$ingredientReceiptRecords = $ingredientReceiptObj->fetchAllRecords($fromDate, $tillDate);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Receipt Note</td>
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
									<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if (sizeof($ingredientReceiptRecords)>0) {
			$i = 0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">GRN No </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
	</tr>
		<?
		foreach ($ingredientReceiptRecords as $grr) {
			$i++;
			$ingredientReceiptId	=	$grr[0];
			$poId			=	$grr[1];						
			$storeEntry		=	$grr[5];
			$Date			=	explode("-",$grr[7]);
			$createdDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
			$supplierName		=	$grr[9];
			$generatedPOId		=	$grr[10];
		?>
		<tr  bgcolor="WHITE">
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$storeEntry;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$generatedPOId;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
			<td class="listing-item" width="60" style="padding-left:10px; padding-right:10px;"><?=$createdDate?></td>			
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
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
