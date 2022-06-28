<?
	require("include/include.php");

	#List All Common Reason
	$chkListRecs	=	$commonReasonObj->fetchAllRecords();
	$comReasonRecSize		=	sizeof($chkListRecs);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Common Reason</td>
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
												if( sizeof($chkListRecs) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" >
												<td class="listing-head" nowrap>&nbsp;Account Type</td>
												<td class="listing-head" nowrap>&nbsp;&nbsp;Reason </td>
												<td class="listing-head" nowrap>&nbsp;&nbsp;Check List </td>
											</tr>
											<?
										
													foreach($chkListRecs as  $icmR)
													{
															$i++;
															$cmnReasonId = $icmR[0];
															$accountType=$codArr[$icmR[1]];
															$reason	=	stripSlash($icmR[2]);
															$checklistVal	=	stripSlash($icmR[3]);
															$checklist=($checklistVal=='Y')?"YES":"NO";
														
											?>
											<tr  bgcolor="WHITE">
												
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$accountType;?>&nbsp;</td>
												<td class="listing-item" nowrp>&nbsp;&nbsp;<?=$reason?></td>
												<td class="listing-item" nowrp>&nbsp;&nbsp;<?=$checklist?></td>
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
