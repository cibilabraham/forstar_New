<?php
	require("include/include.php");
	$selDistributorFilter = $g["selDistributorFilter"];
	#List All Records
	$productIdentifierRecords 	= $productIdentifierObj->fetchAllRecords($selDistributorFilter);
	$productIdentifierRecordSize	= sizeof($productIdentifierRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Identifier Master</td>
							</tr>
							<tr>
								<td colspan="3" height="15" ></td>
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
								<td colspan="2" style="padding:10 10 10 10px">
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($productIdentifierRecordSize) {
			$i	=	0;
		?>
<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Index No</td>
	</tr>
			<?php			
			foreach ($productIdentifierRecords as $pir) {
				$i++;
				$productIdentifierId = $pir[0];
				$sDistributorName    = $pir[4];
				$sProductName	     = $pir[5];
				$indexNo	     = $pir[3];				
			?>
 <tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sProductName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$indexNo;?></td>
</tr>
		<?php
			}
		?>
		
	
											<?php
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
