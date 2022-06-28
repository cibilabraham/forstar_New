<?
	require("include/include.php");
	
	#List All port of loading
	$exporterMasterRecs	=	$exporterMasterObj->fetchAllRecords();
	$exporterMasterRecSize		=	sizeof($exporterMasterRecs);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Exporter Master</td>
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
												if( sizeof($exporterMasterRecs) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center" >
												
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Alpha Code</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Display Name</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Name</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Address</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Tel. No.</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Fax No.</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Unit</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Unit Code</td>
											</tr>
											<?
										
													foreach($exporterMasterRecs as $emR)
													{
															$i++;
															
															$Id = $emR[0];
															$companyID	=	stripSlash($emR[1]);
															$companyRecs 			= $billingCompanyObj->find($companyID);
															$name=$companyRecs[1];
															$address	=	$companyRecs[2];
															$place	=	$companyRecs[3];
															$pin	=	$companyRecs[4];
															$country	=	$companyRecs[5];
															$telno	=	$companyRecs[6];
															$faxno	=	$companyRecs[7];
															$alphaCode	=	$companyRecs[8];
															$defaultRow	=	$emR[9];
															$displayName	=	$emR[10];
															$active	=	$emR[11];
															$contactDetails = $billingCompanyObj->findContactdetail($companyID);
													$unitCodesRecs=$exporterMasterObj->fetchAllUnitCodesdis($Id);					
													?>
											<tr  bgcolor="WHITE">
												
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alphaCode?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;">
												<?=$displayName?>
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$address.','.$place.','.$pin.','.$country?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php
													foreach($contactDetails as $cdt)
													 {
														echo $cdt[1].'<br/>';
													 }
													?>
												<?/*=$telno*/?>
												</td>		
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php
													foreach($contactDetails as $cdt)
													 {
														echo $cdt[3].'<br/>';
													 }
													?>
												<?/*=$faxno*/?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?php foreach($unitCodesRecs as $unitdet)
														{
														echo $unitdet[21].'<br/>';
														
														}
														?>
												</td>	
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?php foreach($unitCodesRecs as $unitdet)
														{
														echo $unitdet[2].'<br/>';
														
														}
														?>
												</td>
													</tr>
											<?
													}
											?>
												
											
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
