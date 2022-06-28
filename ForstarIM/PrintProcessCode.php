<?
	require("include/include.php");
#List All Process Codes

$processCodeRecords	=	$processcodeObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >PROCESS CODE  MASTER </td>
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
												if( sizeof($processCodeRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
											  <td nowrap class="listing-head" align="center">Fish Name </td>
												<td nowrap class="listing-head" align="center" > Code</td>
												<td class="listing-head" align="center">Description</td>
												<td align="center" class="listing-head">Basket<br />Wt (Kg) </td>
												
                                                
                            <td class="listing-head" align="center">Received By</td>
                                              <td class="listing-head" align="center">Unit of Raw </td>
												<td class="listing-head" align="center" width="70">Raw Grades</td>
												<td class="listing-head" align="center">Unit of Frozen </td>
												<td class="listing-head" width="70" align="center">Frozen Grades </td>
											</tr>
											<?

													foreach($processCodeRecords as $fr)
													{
														$i++;
														$codeId		=	$fr[0];
														$Code		=	stripSlash($fr[2]);
																												
														$displayCode		= 	$Code;
														
														$Descr		=	stripSlash($fr[3]);
														$fishName	=	stripSlash($fr[9]);
														$basketWt	=	$fr[4];
														//$unitRaw	=	$fr[5];
												$unitRawRec		=	$unitmasterObj->find($fr[5]);
												$unitRaw		=	stripSlash($unitRawRec[1]);
														
														//$unitFrozen	=	$fr[6];
														
												$unitFrozenRec	=	$unitmasterObj->find($fr[6]);
												$unitFrozen		=	stripSlash($unitFrozenRec[1]);
														
												$availableFor	=	$fr[7];
												
												if($availableFor=='G'){
													$displayAvailable	=	"Grade";
												} else if($availableFor=='C'){
													$displayAvailable	=	"Count";
												}
												else {
													$displayAvailable	=	"";
												}
														
									#Find the Grade from The procescode2grade TABLE
									 $gradeRecords	= $processcodeObj->fetchGradeRecords($codeId);
														$displayGrade = "";
														
														$j=0;
														foreach($gradeRecords as $gradeR)
															{
															$j++;
															$grade	=	$gradeR[4];
															if( $j>1 && $grade!="") $displayGrade.=", &nbsp;";
															$displayGrade.="$grade";
															}
															
						#Find FROZEN Grade from The procescode2grade TABLE
												$frozenGradeRecords	= $processcodeObj->fetchFrozenGradeRecords($codeId);
												$displayFrozenGrade = "";
												$k=0;
														foreach($frozenGradeRecords as $gradeF)
															{
															$k++;
															$gradeF	=	$gradeF[4];
															if( $k>1 && $gradeF!="") $displayFrozenGrade.=", &nbsp;";
															$displayFrozenGrade.="$gradeF";
															}
														
														
											?>
											<tr  bgcolor="#FFFFFF">
											  <td class="listing-item" nowrap>&nbsp;&nbsp;<?=$fishName;?></td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$displayCode;?>&nbsp;</td>
												<td class="listing-item" align="left"><?=$Descr;?></td>
												<td class="listing-item" align="right"><?=$basketWt?>&nbsp;&nbsp;&nbsp;</td>
												<td class="listing-item">&nbsp;&nbsp;<?=$displayAvailable?></td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$unitRaw?></td>
												<td class="listing-item" nowrap="nowrap" width="70"><?=$displayGrade?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$unitFrozen?></td>
												<td class="listing-item" width="70"><?=$displayFrozenGrade;?></td>
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
												<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
									  </table>
							  </td>
						  </tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>











			