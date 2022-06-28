<?
	require("include/include.php");

	#List All Fishes
	$staffMasterRecords	=	$staffMasterObj->fetchAllRecords();
	$staffMasterSize		=	sizeof($staffMasterRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Staff Master</td>
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
									<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($staffMasterRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" >
												<td class="listing-head" nowrap>&nbsp;Name</td>
												<td class="listing-head" >&nbsp; Function</td>
												<td class="listing-head" nowrap>&nbsp;&nbsp;Cost </td>
												<td class="listing-head" nowrap>&nbsp;&nbsp;Allowance </td>
												<th class="listing-head" nowrap>Actual Cost</th>
											</tr>
											<?
														$displayStatus = "";
													foreach($staffMasterRecords as $sr)
													{
														$i++;
														$staffId		=	$sr[0];
														$staffName	=	stripSlash($sr[1]);
														$function	=	stripSlash($sr[10]);
														$cost	=	stripSlash($sr[3]);
														$actualCost	=	$sr[8];		
														$allowance  =   $sr[9];
											?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$staffName;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$function;?>&nbsp;</td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$cost?></td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$allowance?></td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$actualCost?></td>
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
