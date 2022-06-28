<?
	require("include/include.php");

	# List all Departments 
	$PHTCertificateRecords	=	$phtCertificateObj->fetchAllRecords();
	$PHTCertificateSize		=	sizeof($PHTCertificateRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PHT Certificate</td>
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
									if (sizeof($PHTCertificateRecords) > 0) {
										$i	=	0;
									?>
									<tr  bgcolor="#f2f2f2" align="center">
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">PHT Certificate No</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Species</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Farm Name</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Available Qty</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date Of Issue</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">date of Expiry</th>
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">Received Date</th>
</tr>
<?
		
		foreach($PHTCertificateRecords as $cr) {
		$i++;
		 $phtCertificateId		=	$cr[0];
		 $phtCertificateNo		=	stripSlash($cr[1]);
		 $speciesId		=	stripSlash($cr[2]);
		 $speciesName=$phtCertificateObj->getSpeciousName($speciesId);
		 $species=$speciesName[0];
		 
		 $supplierGroupId		=	stripSlash($cr[3]);
		 $supplierGroupName=$phtCertificateObj->getSupplierGroupName($supplierGroupId);
		 $supplierGroup=$supplierGroupName[0];
		 
		 $supplierId		=	stripSlash($cr[4]);
		 $supplierName=$phtCertificateObj->getSupplierName($supplierId);
		 $supplier=$supplierName[0];
		 
		 $pondNameId		=	stripSlash($cr[5]);
		 $pond=$phtCertificateObj->getPondName($pondNameId);
		 $pondName=$pond[0];
		 
		$pondQtyId		=	stripSlash($cr[6]);
		 //$pondQty=$phtCertificateObj->getPondQty($pondQtyId);
		 //$phtQuantity=$pondQty[0];
		  $phtQuantity		=	stripSlash($cr[6]);
		 $dateOfIssue		=	dateFormat($cr[7]);
		 $dateOfExpiry		=	dateFormat($cr[8]);
		 $receivedDate		=	dateFormat($cr[9]);
		
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtCertificateNo;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$species;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$supplierGroup;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$supplier;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pondName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtQuantity?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$dateOfIssue;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$dateOfExpiry;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$receivedDate;?></td>
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
