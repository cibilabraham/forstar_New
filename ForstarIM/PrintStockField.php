<?php
	require("include/include.php");

	# List all Recs
	$stockFieldRecords	= $stockFieldObj->fetchAllRecords();
	$stockFieldRecSize	= sizeof($stockFieldRecords);

	$inputTypeArr = array("T"=>"Text", "C"=>"Checkbox");
	$validationArr = array("N"=>"NO", "Y"=>"YES");
	$fieldDTypeArr = array("NUM"=>"NUMBER", "ANUM"=>"ALPHANUMERIC");
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Field</td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if (sizeof($stockFieldRecords) > 0 ) {
			$i	=	0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Label Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Field Type</td>
		<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Field Name</td>-->
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Default Value</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Size</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Data Type</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Unit Group</td>
	</tr>
	<?php
	foreach ($stockFieldRecords as $sfr) {
		$i++;
		$stockFieldId = $sfr[0];		
		$stkLName = stripSlash($sfr[1]);
		$fieldType	= $sfr[2];
		$fieldName		= $sfr[3];
		$fieldDefaultValue 	= $sfr[4];
		$fieldSize		= $sfr[5];
		$fieldDTypeValue	= $sfr[6];
		$selUnitGroupName	= $sfr[8];
		
	?>
	<tr  bgcolor="WHITE" title="<?=$displayTitle?>">
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stkLName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$inputTypeArr[$fieldType];?></td>
		<!--<td class="listing-item" nowarp align="left" style="padding-left:10px; padding-right:10px;"><?=$fieldName?></td>-->
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$fieldDefaultValue?></td>	
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=($fieldSize!=0)?$fieldSize:"";?></td>
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$fieldDTypeArr[$fieldDTypeValue]?></td>
		<td class="listing-item" nowarp align="left" style="padding-left:10px; padding-right:10px;"><?=$selUnitGroupName?></td>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
