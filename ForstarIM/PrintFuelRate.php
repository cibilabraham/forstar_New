<?
	require("include/include.php");

	# List all Category ;
	$fuelRecords	=	$fuelRateObj->fetchAllRecords();
	$fuelSize		=	sizeof($fuelRecords);
	$offval=$offset+1;
	$nextfuelRateRecords	=	$fuelRateObj->fetchAllPagingRecords($offval, $limit);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fuel Rate</td>
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
												if( sizeof($fuelRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
												<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Rate </td>
												<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Difference In Rate% </td>
											</tr>
											<?		$j=0;
													foreach($fuelRecords as $fs)
													{
														$i++;
														$fuelId		=	$fs[0];
														$date	=	dateFormat(stripSlash($fs[1]));
														$rate	=	stripSlash($fs[2]);
														$prevRate=$nextfuelRateRecords[$j][2];
														//echo $prevRate;
														($prevRate!="")?$perDifference=$rate/$prevRate:$perDifference="";
			
											?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$date;?></td>
												<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$rate?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=number_format($perDifference, 2, '.', ' ');?></td>
											</tr>
											<?
												$j++;	
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
