<?
	require("include/include.php");

	#List All Plants
	
	$plantRecords	=	$plantandunitObj->fetchAllRecords();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PLANT / UNIT  MASTER</td>
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
									<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($plantRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
												<td class="listing-head" nowrap>&nbsp;&nbsp;Plant No</td>
												<td class="listing-head" >&nbsp;&nbsp;Name</td>
												<td class="listing-head" >&nbsp;&nbsp;Company</td>
											</tr>
											<?

													foreach($plantRecords as $pr)
													{
														$i++;
														$plantId		=	$pr[0];
														$plantNo		=	stripSlash($pr[1]);
														$plantName		=	stripSlash($pr[2]);
														$company_name		=	stripSlash($pr[3]);
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$plantNo;?></td>
												<td class="listing-item" >&nbsp;&nbsp;<?=$plantName;?></td>
												<td class="listing-item" >&nbsp;&nbsp;<?=$company_name;?></td>
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
