<?
	require("include/include.php");

	#List All Records	
	$euCodeRecords		=	$eucodeObj->fetchAllRecords();
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >EU CODE </td>
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
												if( sizeof($euCodeRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
												<td class="listing-head" nowrap style="padding:0 10px;">Code</td>
												<td class="listing-head" style="padding:0 10px;">Description</td>
												<td class="listing-head" style="padding:0 10px;">Address</td>
												<? if($edit!=true){?>
												<? }?>
											</tr>
											<?

													foreach($euCodeRecords as $eucr)
													{
														$i++;
														$euCodeId		=	$eucr[0];
														$euCodeCode	=	stripSlash($eucr[1]);
														$description	=	stripSlash($eucr[2]);
														$address		=	stripSlash($eucr[3]);
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" nowrap style="padding:0 10px;"><?=$euCodeCode;?></td>
												<td class="listing-item" nowrap="nowrap" style="padding:0 10px;"><?=$description;?></td>
												<td class="listing-item" nowrap style="padding:0 10px;"><?=nl2br($address);?></td>
												<? if($edit!=true){?>
											  <? }?>
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