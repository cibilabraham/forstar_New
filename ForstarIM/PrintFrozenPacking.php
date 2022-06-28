<?
	require("include/include.php");

	#List All Records	
	$frozenPackingRecords		=	$frozenpackingObj->fetchAllRecords();
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Frozen Packing</td>
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
										<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($frozenPackingRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td class="listing-head" nowrap>Frozen Code</td>
												<td class="listing-head">Unit of Wt</td>
												<td class="listing-head">Freezing</td>
												<td class="listing-head">Declared Wt</td>
												<td class="listing-head">Glaze %</td>
												<td class="listing-head">Filled Wt</td>
												<td class="listing-head">Actual<br/> Filled Wt</td>
												<td class="listing-head">Description</td>
												<? if($edit!=true){?>
												<? }?>
											</tr>
											<?

													foreach($frozenPackingRecords as $fpr)
													{
														$i++;
														$frozenPackingId		=	$fpr[0];
														
														$frozenPackingCode		=	stripSlash($fpr[1]);
														$unit			=	$fpr[2];
														$freezingId		=	$fpr[3];
														$freezingCode	=	$freezingObj->findFreezingCode($freezingId);
														$declWt			=	$fpr[4];
														$glazeId		=	$fpr[5];
														$glaze			=	$glazeObj->findGlazePercentage($glazeId);	
														$filledWt		=	$fpr[6];
														$description	=	$fpr[7];
														$selActualFilledWt = ($fpr[8]!=0)?$fpr[8]:"";											
														
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" nowrap style="padding-left:10px;"><?=$frozenPackingCode;?></td>
												<td class="listing-item" nowrap style="padding-left:10px;"><?=$unit;?>&nbsp;</td>
												<td class="listing-item" style="padding-left:10px;"><?=$freezingCode?></td>
												<td class="listing-item" align="right" style="padding-right:10px;"><?=$declWt?></td>
												<td class="listing-item" align="right" style="padding-right:10px;"><?=$glaze?></td>
												<td class="listing-item" align="right" style="padding-right:10px;"><?=$filledWt?></td>
												<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$selActualFilledWt?></td>
												<td class="listing-item" style="padding-left:10px;"><?=$description?></td>
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
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
