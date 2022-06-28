<?php
	require("include/include.php");
	$selStateFilter = $g["selStateFilter"];
	#List All Records
	$cityResultSetObj = $cityMasterObj->fetchAllRecords($selStateFilter);
	$cityRecordSize	= $cityResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;City Master</td>
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
								<td colspan="2" >
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($cityRecordSize) {
			$i	=	0;
		?>

											<tr  bgcolor="#f2f2f2" align="center">
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">State</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Octroi</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Octroi (%)</td>
	</tr>
			<?
			$prevStateId = "";
			while(($cr=$cityResultSetObj->getRow())) {
				$i++;
				$cityId = $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);	
				$stateId	= $cr[3];
				$stateName	= $cr[4];
				$sOctroi	= ($cr[5]=='Y')?"YES":"NO";
				$sOctroiPercent	= $cr[6];
			?>
	<tr  bgcolor="WHITE">	
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$cityName;?></td>
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sOctroi;?></td>
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=($sOctroiPercent!=0)?$sOctroiPercent:"";?></td>
		</tr>
		<?
			$prevStateId = $stateId;
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
