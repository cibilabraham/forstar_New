<?php
	require("include/include.php");

	#List All Records
	$customerRecords = $customerObj->fetchAllRecords();
	$customerRecordSize = sizeof($customerRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;CUSTOMERS Master</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($customerRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">			
												<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"> Name </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Country</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Contact No</td>		
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Brands</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Preferred <br>Shippling Line</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Agent</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Payment Terms</td> 									
											</tr>
											<?

													foreach($customerRecords as $cr)
													{
														$i++;
														$customerId	=	$cr[0];
														$customerCode	=	$cr[1];
														$customerName	=	stripSlash($cr[2]);
														$selCountryName	= $cr[3];
														$custContactNo	= $cr[4];
				$custBrandRecs 		= $customerObj->getSelBrandRecs($customerId);	
				$custShippingRecs	= $customerObj->getSelShippingRecs($customerId);
				$agentRecs		= $customerObj->getAgentList($customerId);
				$paymtTermRecs		= $customerObj->getPaymentTermList($customerId);	
											?>
											<tr  bgcolor="WHITE"  >					
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$customerName;?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCountryName?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$custContactNo;?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($custBrandRecs)>0) {
						$nextRec = 0;						
						foreach ($custBrandRecs as $cR) {
							$j++;
							$brdName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$brdName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($custShippingRecs)>0) {
						$nextRec = 0;						
						foreach ($custShippingRecs as $cR) {
							$j++;
							$shipName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$shipName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
			</table>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($agentRecs)>0) {
						$nextRec = 0;						
						foreach ($agentRecs as $cR) {
							$j++;
							$agentName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$agentName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
			</table>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($paymtTermRecs)>0) {
						$nextRec = 0;						
						foreach ($paymtTermRecs as $cR) {
							$j++;
							$termName = $cR[1];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$termName?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
			</table>
	</td>
						</tr>
											<?php
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
								<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
