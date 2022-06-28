<?php
	require("include/include.php");

	$salesZoneFilter = $g["salesZoneFilter"];

	#List All Records
	$stateResultSetObj = $stateMasterObj->fetchAllRecords($salesZoneFilter);
	$stateRecordSize = $stateResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;State Master</td>
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
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($stateRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Zone</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Billing</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Entry Tax</td>	
	</tr>
			<?
			while ($sr=$stateResultSetObj->getRow()) {
				$i++;
				$stateId = $sr[0];
				$stateCode	= stripSlash($sr[1]);
				$stateName	= stripSlash($sr[2]);				
				$billing	= ($sr[3]=='Y')?"YES":"NO";
				$sEntryTax	= ($sr[4]=='Y')?"YES":"NO";
				$sZoneName	= stripSlash($sr[6]);
			?>
		<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sZoneName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$billing;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$sEntryTax;?></td>
		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >		
											<?
												}
												else
												{
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
