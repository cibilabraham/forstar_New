<?
	require("include/include.php");

	# List all Departments 
	$pondMasterRecords	=	$pondMasterObj->fetchAllRecords();
	$pondMasterSize		=	sizeof($pondMasterRecords);
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
			if (sizeof($pondMasterRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Farm Name</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Allotee Name</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Address</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">State</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">District</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Taluk</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Village</th>
												
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Location</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Registration Type</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Registration No</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Registration Date</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Registration Expiry Date</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Farm size</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Farm Size Unit</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Farm Qty </th>
</tr>
<?
		foreach($pondMasterRecords as $cr) {
		$i++;
		$pondmasterId		=	$cr[0];
		 $pondName		=	stripSlash($cr[1]);
		 $suppliercode	=	stripSlash($cr[2]);
		 $supplier1=$supplierMasterObj->fetchSupplier($suppliercode);
		foreach($supplier1 as $supplier)
		 $alloteeName		=	stripSlash($cr[3]);
		 $address		=	stripSlash($cr[4]);
		 $statecode		=	stripSlash($cr[5]);
		 $state1=$stateMasterObj->fetchState($statecode);
		foreach($state1 as $state)
		 $district		=	stripSlash($cr[6]);
		 $taluk		=	stripSlash($cr[7]);
		 $village		=	stripSlash($cr[8]);
		
		 $location		=	stripSlash($cr[9]);
		 $registrationTypecode		=	stripSlash($cr[10]);
		 $registrationType1=$registrationTypeObj->fetchRegistartionType($registrationTypecode);
		foreach($registrationType1 as $registrationType)
		 $registrationNo		=	stripSlash($cr[11]);
		 $registrationDate		=	dateformat($cr[12]);
		 $registrationExpiryDate		=	dateformat($cr[13]);
		 $pondSize		=	stripSlash($cr[14]);
		 $pondSizeUnitcode		=	stripSlash($cr[15]);
		 $pondSizeUnit1=$areaObj->fetchPondSizeUnit($pondSizeUnitcode);
		foreach($pondSizeUnit1 as $pondSizeUnit)
		 $pondQty		=	stripSlash($cr[16]);
		
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$pondName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplier[0];?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$alloteeName?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$address?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$state[0]?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$district?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$taluk?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$village?></td>
		
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$location?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$registrationType[0]?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$registrationNo?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$registrationDate?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$registrationExpiryDate?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$pondSize?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$pondSizeUnit[0]?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$pondQty?></td>
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
