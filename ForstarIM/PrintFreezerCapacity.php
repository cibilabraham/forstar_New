<?
	require("include/include.php");

	#List All Records	
		$freezerRecords		=	$freezercapacityObj->fetchAllRecords();
	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Freezer Capacity</td>
								</tr>
								
								<tr>
									<td colspan="3" height="5" ></td>
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
<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?
				if (sizeof($freezerRecords) > 0) {
					$i	=	0;
				?>
				<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Freezer Name </td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Capacity</td>
<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Freezing Time<br>(Hrs) </td>
<td class="listing-head" style="padding-left:10px; padding-right:10px;">Description</td>
</tr>
			<?
			foreach($freezerRecords as $fr)
			{
				$i++;
				$freezerId	=	$fr[0];
				$freezerName	=	stripSlash($fr[1]);
				$capacity	=	$fr[2];
				$freezingTime	=	$fr[3];
				$freezerDescr	=	stripSlash($fr[4]);
			?>
<tr  bgcolor="WHITE">
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezerName;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$capacity;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingTime;?></td>
<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezerDescr;?></td>
											</tr>
											<?
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
