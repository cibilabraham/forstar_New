<?
	require("include/include.php");

	#List All Record
	$statusRecords		=	$statusObj->fetchAllRecords();
	$statusRecordsize	=	sizeof($statusRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;STAGES-ORDER PROCESSING </td>
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
									<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($statusRecords) > 0 )
												{
													$i	=	0;
											?>
											<? if($maxpage>1){?>

	  <? }?>
											<tr  bgcolor="#f2f2f2"  >
												<td class="listing-head" nowrap>&nbsp;&nbsp;Stage </td>
												<td class="listing-head" >&nbsp;&nbsp;Description</td>
												<? if($edit==true){?>
												<? }?>
											</tr>
											<?

													foreach($statusRecords as $sr)
													{
														$i++;
														$statusId		=	$sr[0];
														$status			=	stripSlash($sr[1]);
														$description	=	stripSlash($sr[2]);
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$status;?></td>
												<td class="listing-item" nowrap="nowrap" >&nbsp;&nbsp;<?=$description;?>&nbsp;</td>
												<? if($edit==true){?>
											  <? }?>
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											
											<? if($maxpage>1){?>

	  <? }?>
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
