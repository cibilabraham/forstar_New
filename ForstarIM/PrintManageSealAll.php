<?php
	require("include/include.php");
# select record between selected date
		$sealManage	=	$manageSealObj->fetchAllRecords();
		$sealManageRecssize	=	sizeof($sealManage);	
	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
	 	Manage Seal </td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?php
									if($sealManageRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<th nowrap >Seal</th>
										<th>Status</th>
										<th>Purpose</th>
									</tr>
									<?
									foreach($sealManage as $fr)
									{
										$i++;
										$sealId		=	$fr[0];
										//$sealId
										$sealVal=$fr[4];
										if($sealVal!="out_seal")
										{
											$historyDetail	=	$manageSealObj->getSealHistory($sealId);
											if(sizeof($historyDetail) > 0)
											{
												 $historymode=1;
											}
											else
											{
												$historymode=0;
											}
										}
										else
										{
											$historymode=0;
										}
											$seal	=	$fr[1];
											$sealDetail	=	$fr[2];
											$number_gen_id	=	$fr[3];
											$alpha	=	$fr[5];
															
											if(($sealDetail!="0") && ($sealDetail!="1") &&($sealDetail!="2"))
											{
											//echo outseal;
												if($sealDetail!="")
												{
													$outseal="Out seal".'-'.$sealDetail;
												}
												else
												{
													$outseal="Out seal";
												}	
											}
											else{
												// echo $sealId;
												 $sealIn	=	$manageSealObj->procurementNumberInseal($sealId);
												 // echo inseal;
											}
														  
														
											?>
											<tr bgcolor="#fff">
												<td class="listing-item" nowrap  style='padding:0px 5px 0px 5px'> <?php if($historymode==1) { ?>
												<?php
												$detailsvalue='';
												$displaySealHistory=$manageSealObj->getAllSealNumberData($alpha,$seal);
												if(sizeof($displaySealHistory)>0) {
												$detailsvalue='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head>Procurement ID</th><th  class=listing-head>Seal type</th><th  class=listing-head>Seal status</th></tr>';
												
												 foreach($displaySealHistory as $displaySeal )
												 {
													$type= $displaySeal[0];
													$status= $displaySeal[1];
													$sealIdVal=$displaySeal[2];
													$sealsIn	=	$manageSealObj->procurementNumberOutseal($sealIdVal);
													$detailsvalue.='<tr bgcolor=#f2f2f2><td class=listing-item>'.$sealsIn[0].'&nbsp;</td><td class=listing-item>'.$type.'&nbsp;</td><td class=listing-item>'.$status.'&nbsp;</td></tr>';
												 } 
												
														
												$detailsvalue.='</table>';
												}
												?>
												<a onMouseOver="ShowTip('<?=$detailsvalue;?>');" onMouseOut="UnTip();"><?php } ?><?=$alpha.$seal;?></td>
												<td class="listing-item" nowrap style='padding:0px 5px 0px 5px'>
												<?php
												if($sealDetail== '0')
												{
													
													echo "Blocked";
													
												}
												elseif($sealDetail== '1')
												{
													
													echo "Used";
												}
												elseif($sealDetail== '2')
												{
													echo "Free";
												}
												else
												{
													echo "Used";
												}
												//echo $sealDetail;
												?></td>
												<td style='padding:0px 5px 0px 5px' class="listing-item" nowrap>
												<?php
												if($sealDetail== '0')
												{
													if($sealIn[0]!="")
													{
													echo $sealIn[0];
													}
													
												}
												elseif($sealDetail== '1')
												{
													if($sealIn[0]!="")
													{
														echo "In seal".'-'.$sealIn[0];
													}
													
												}
												elseif($sealDetail== '2')
												{
												//echo "Free";
												}
												else
												{
												echo $outseal;
												}
												//echo $sealDetail;
												?>
												
												
												
												</td>
											</tr>
										<?php
										}
										?>
									<?
									}
									else
									{
									?>
									<tr bgcolor="white">
										<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
									}?>
									</table>
								</td>
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
