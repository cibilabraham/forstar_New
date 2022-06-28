<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$fetchAllReceiptRecs = $rmReceiptGatePassObj->fetchAllDateRangeRecords($fromDate, $tillDate);
		$receiptRecssize	=	sizeof($fetchAllReceiptRecs);	
	}
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
	 RM Receipt gate pass</td>
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
									if($receiptRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> DATE OF ENTRY </td>
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> Receipt Gate Pass Number</td>
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> Supervisor</td>
										<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"> PROCUREMENT ID</td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head">  SUPPLIER CHALLAN NO </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head"> COMPANY NAME</td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head"> UNIT </td>
										<td style="padding-left:10px; padding-right:10px;" class="listing-head"> LOT ID </td>
									</tr>
									<?php
									$i = 1;
										foreach($fetchAllReceiptRecs as $data)
										{
											$supplier= $data['id'];
											$datasupplier=$rmReceiptGatePassObj->getAllReceiptGatePassSupplier($supplier);
										?>
										<tr bgcolor="WHITE">
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php $dateval=$data['date_Of_Entry'];
												echo dateFormat($dateval);
											?>
											</td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['receipt_gatepass_number'];?></td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['Supervisor'];?></td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['gate_pass_id'];?></td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
											<?php if($data['gate_pass_id']!="")
											{
												if(sizeof($datasupplier)>0)
												{
													foreach($datasupplier as $dataval)
													{
														echo $dataval['challan_no'];
														echo '<br/>';
													}
												}
															
											}
											else
											{
												echo $data['supplier_Challan_No'];
											}
											?>
											</td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
												<?php if($data['gate_pass_id']!="")
												{
													if(sizeof($datasupplier)>0)
													{
														foreach($datasupplier as $dataval)
														{
															$cmpID= $dataval['company_id'];
															$Company_Name_Value = $rmReceiptGatePassObj->getCompanyName($cmpID);
															echo $Company_Name_Value[1];
															echo '<br/>';
														}
													}
															
												}
												else{
													echo $data['company_name'];
												}
											?>
											</td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
											<?php 
											if($data['gate_pass_id']!="")
											{
												if(sizeof($datasupplier)>0)
												{
													foreach($datasupplier as $dataval)
													{
														$untid= $dataval['unit_id'];
														$Unit_Name_Value = $rmReceiptGatePassObj->getUnitName($untid);
														echo $Unit_Name_Value[1];
														echo '<br/>';
													}
												}
															
											}
											else{
												echo $data['unit_name'];
											}
											?>
											</td>
											<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item"><?php echo $data['lot_Id'];?></td>
											<?php
											if($receiptGateConfirmEnabled)
											{
											?>
												<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
												<?php
													if($data['lot_Id'] == '')
													{
														$generateLotID= $data['id'];
														$baseGenerate=base64_encode($generateLotID);
														?>
													<input type="button" value="Generate RM LotId" onClick="return page('ManageRMLOTID.php?generateLotID=<?php echo $baseGenerate;?>');">
												<?php
											}
											?>
											</td>
										<?php 
											}
										?>
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
