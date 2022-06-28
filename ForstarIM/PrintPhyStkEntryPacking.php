<?php
	require("include/include.php");

	#List All Records
	$physicalStockRecords = $phyStkEntryPackingObj->fetchAllRecords();
	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Physical Stock Entry</td>
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
	if ( sizeof($physicalStockRecords) > 0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>		
	</tr>
	<?
	foreach ($physicalStockRecords as $psr) {
		$i++;
		$physicalStockRecId	= $psr[0];		
		$selEntryDate	= dateFormat($psr[1]);		
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selEntryDate;?></td>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	
	<?
		} else {
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
