<?
	require("include/include.php");

	# List all Category ;
	$installedCapacityRecords=$installedCapacityObj->fetchAllRecords();
	$installedCapacityRecordsSize		=	sizeof($installedCapacityRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Installed Capacity</td>
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
												if( sizeof($installedCapacityRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Machinery</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Type of Operation</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Capacity</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Per</th>
												<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Monitor</th>
											</tr>
											<?
											foreach($installedCapacityRecords as $mpr) {
												$i++;
												$installedCapacityId	=	$mpr[0];
												$machinery		=	stripSlash($mpr[1]);
												$description		=	stripSlash($mpr[2]);
												$typeOperation		=	stripSlash($mpr[3]);
												$capacity		=	stripSlash($mpr[4]);
												$unit		=	stripSlash($mpr[5]);
												$per			=	stripSlash($mpr[6]);
												$Monitor		=	stripSlash($mpr[7]);
												($Monitor=='S')? $MonitorNm="Single" : $MonitorNm="Multiple";
			
											?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$machinery;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$typeOperation;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$capacity;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$per;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$MonitorNm;?></td></tr>
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
												<td colspan="2"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
