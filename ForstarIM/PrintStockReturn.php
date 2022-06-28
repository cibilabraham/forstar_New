<?
	require("include/include.php");
	$fromDate=$g["fd"];
	$tillDate=$g["td"];
#List all Stock Issuance
	$stockRetRecords		=$stockReturnObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	$stockReturnSize		=	sizeof($stockRetRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Return</td>
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
												if( sizeof($stockReturnSize) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" >
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" align='center'>Date</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" align='center'>Return No</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" align='center' >Department</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" align='center' nowrap >Total<br>Scrap Value</td>
												
												
											</tr>
											<?
											$subTotalScrapVal  = 0 ;
											foreach ($stockRetRecords as $sir) {
												$i++;
												$stockRetId	=	$sir[0];
												$requestNo	=	$sir[1];
												$departmentRec	=	$departmentObj->find($sir[2]);
												$departmentId	=	$departmentRec[0];
												$departmentName	=	stripSlash($departmentRec[1]);
												$createdDate	= dateFormat($sir[3]);
												$totScrapVal = $stockReturnObj->getTotalVal($stockRetId,'scrap_value');
												$subTotalScrapVal =  $subTotalScrapVal+$totScrapVal;
											?>
											<tr bgcolor="white">
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='center' ><?=$createdDate;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='center' ><?=$requestNo;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" ><?=$departmentName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='right' ><?=$totScrapVal;?></td>
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
