<?
	require("include/include.php");
	#List all Stock Issuance
	$stockIssuanceRecords		=	$stockissuanceObj->fetchAllRecords();
	$stockIssuanceSize		=	sizeof($stockIssuanceRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Issuance</td>
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
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($stockIssuanceRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" >
												
												<td class="listing-head" align="center">Request No</td>
												<td class="listing-head">&nbsp;&nbsp;Department</td>

											</tr>
											<?
																										
													foreach($stockIssuanceRecords as $sir)
													{
														$i++;
														
														$stockIssuanceId	=	$sir[0];
														$requestNo			=	$sir[1];
														
														
											$departmentRec		=	$departmentObj->find($sir[2]);
											$departmentId			=	$departmentRec[0];
											$departmentName			=	stripSlash($departmentRec[1]);
														
		
												
																																								
			
											?>
											<tr  bgcolor="WHITE">
												
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$requestNo;?></td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$departmentName;?></td>
												<!--td class="listing-item" width="60" align="center" nowrap="nowrap"><input name="cmdGo" type="submit" id="cmdGo" value="Go"><input type="hidden" name="SupplierStatusId_<?=$i?>" value="<?=$stockIssuanceId?>"></td-->

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
