<?
	require("include/include.php");

	# List all supplier group 
	$supplierGroupRecords	=	$supplierGroupObj->fetchAllRecords();
	$supplierGroupSize		=	sizeof($supplierGroupRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier group</td>
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
			if (sizeof($supplierGroupRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group Name</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Supplier Location </th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Farm</th>
</tr>
<?
	foreach($supplierGroupRecords as $cr) {
		$i++;
		 $supplierGroupNameId		=	$cr[0];
		 $supplierGroupName		=	stripSlash($cr[1]);
		 $supplierData	=	$supplierGroupObj->getSupplierData($supplierGroupNameId);
		
		 $active=$cr[3];
		$existingrecords=$cr[4];
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplierGroupName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$supplier = $cR[1];
					$supName=$supplierGroupObj->getSupplierName($supplier);
						$name=$supName[0];					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $name;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$loc= $cR[2];
						$supLocation=$supplierGroupObj->getSupplierLocation($loc);
						$location=$supLocation[0];			
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $location;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$pond = $cR[3];	
						$supPond=$supplierGroupObj->getSupplierPond($pond);
						$supplierPond=$supPond[0];					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $supplierPond;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		</td>
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
