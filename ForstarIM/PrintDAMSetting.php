<?
	require("include/include.php");

	# List all Departments 
	$damSettingsRecords	=	$damSettingObj->fetchAllRecords();
	$damSettingsRecordsSize		=	sizeof($damSettingsRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Activity Monitoring Setting</td>
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
										if ($damSettingsRecordsSize > 0) 
										{
											$i	=	0;
										?>
										<tr  bgcolor="#f2f2f2" align="center">
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Head</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">NOS</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Sub Head</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Produced</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Stocked</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">O/S Supply</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">O/S Sale</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">O/B</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Unit</th>
											<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">As On</th>
										</tr>
										<?
											foreach($damSettingsRecords as $dpr) {
											$i++;
											$damSettingId	=	$dpr[0];
											$head		=	stripSlash($dpr[1]);
											$nos		=	stripSlash($dpr[2]);
											$active		=	$dpr[3];
											$damEntry=$damSettingObj->getDamSettingEntry($damSettingId);
											
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$head;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$nos;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? 
												foreach($damEntry as $de)
												{
													echo $de[1].'<br/>';
												}
												?>
											</td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? 
												foreach($damEntry as $de)
												{
													if($de[2]=='Y')
													{
														echo "YES".'<br/>';
													}
													elseif($de[2]=='N')
													{
														echo "NO".'<br/>';
													}
												}
												?>
											</td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? 
												foreach($damEntry as $de)
												{
													if($de[3]=='Y')
													{
														echo "YES".'<br/>';
													}
													elseif($de[3]=='N')
													{
														echo "NO".'<br/>';
													}
												}
												?>
											</td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? 
												foreach($damEntry as $de)
												{
													if($de[4]=='Y')
													{
														echo "YES".'<br/>';
													}
													elseif($de[4]=='N')
													{
														echo "NO".'<br/>';
													}
												}
												?>
											</td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? foreach($damEntry as $de)
													{
														if($de[5]=='Y')
														{
															echo "YES".'<br/>';
														}
														elseif($de[5]=='N')
														{
															echo "NO".'<br/>';
														}
													}
													?>
											</td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
												<? 
												foreach($damEntry as $de)
												{
													 echo $de[6].'<br/>';
												}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? 
												foreach($damEntry as $de)
												{
													$unit=$damSettingObj->getStockUnit($de[7]);
													echo $unit.'<br/>';
												}
												?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? 
												foreach($damEntry as $de)
												{
													echo dateFormat($de[8]).'<br/>';
												}
												?>
											</td>
										</tr>
										<?
											}
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
