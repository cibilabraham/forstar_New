<?
	require("include/include.php");
	
	#List All port of loading
	$fromDate=$g["selectFrom"];
	$tillDate=$g["selectTill"];
	//$fetchAllInvoiceRecs	= $invoiceObj->fetchAllRecordsInvoice();
	$fetchAllInvoiceRecs	=  $invoiceObj->fetchAllRecords($fromDate, $tillDate, $invoiceTypeFilter);
	$fetchAllInvoiceRecsSize		=	sizeof($fetchAllInvoiceRecs);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Invoice </td>
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
												if( sizeof($fetchAllInvoiceRecs) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); "  class="chkBox"></td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice No </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Invoice Type</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Customer</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total (<span class="replaceCY">US$</span>)</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Container</td>
											
												
											</tr>
												<?php
												$invoiceMainId = "";
												foreach ($fetchAllInvoiceRecs as $ir) {
													$i++;
													$invoiceMainId	= $ir[0];
													$sInvoiceNo		= $ir[1];	
													$sInvoiceDate	= $ir[2];
													$iCustomerId	= $ir[3];
													$iProformaNo	= $ir[4];
													$ientryDate	= $ir[5];
													$iPOId		= $ir[6];
													$iInvoiceTypeId	= $ir[7];
													
													$invoiceTypeName= $ir[8];
													$customer	= $ir[9];
													$invoiceStatus 	= $ir[10];
													//$disableEdit = ($invoiceStatus=='Y')?"disabled":"";

													$totalUSDAmt	= $ir[11];

													# Get Sel container
													$invContainerRecs = $invoiceObj->getContainerRecs($invoiceMainId);

													$invoiceNo = "";
													if ($sInvoiceNo!=0 && $invoiceStatus=='Y') $invoiceNo=sprintf("%02d",$sInvoiceNo);
													else if ($iProformaNo) $invoiceNo = "P$iProformaNo";

													$shipBillNo		= $ir[12];
													$billLaddingNo	= $ir[13];
													$exporterAlphaCode = $ir[14];
													$invoiceunitid=$ir[15];
													$invoiceunitno=$plantandunitObj->find($invoiceunitid);
														$invoiceunitno=$invoiceunitno[1];
													$unitalphacode=$ir[16];
													//echo "The invoiceid is $invoiceunitid";

													$sInvDate = ($sInvoiceNo=="" && $sInvoiceNo==0)?date('y-m-d'):$sInvoiceDate;
													$sInvYearRange = getFinancialYearRange($sInvDate);
													if (($invoiceunitid!="") && ($invoiceunitid!="0"))
													{
														//if ($exporterAlphaCode=="FFFPL")
														//{
															$exporterAlphaCode=$unitalphacode;
														//}
													if (!empty($exporterAlphaCode)) $invoiceNo = $exporterAlphaCode."/"."U-".$invoiceunitno."/".$invoiceNo."/".$sInvYearRange;
													}
													else
													{
													if (!empty($exporterAlphaCode)) $invoiceNo = $exporterAlphaCode."/".$invoiceNo."/".$sInvYearRange;
													}
													//echo "----------$invoiceNo";
													$disableEdit = "";
													if ($invoiceStatus=='Y' && !empty($shipBillNo) && !empty($billLaddingNo)) {
														$disableEdit = "disabled";
													}
													if ($reEdit) $disableEdit = "";

													$poRec = $invoiceObj->getPORec($iPOId);
													$selCurrencyCode	=  $poRec[9];

											?>
											<tr <?=$listRowMouseOverStyle?>>
												<td width="20">
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$invoiceMainId;?>" class="chkBox" />
													<!--<input type="hidden" name="invoiceContainerId_<?=$i;?>" value="<?=$invoiceContainerId?>">
													<input type="hidden" name="invoicePOId_<?=$i;?>" value="<?=$invoicePOId?>">-->
													<input type="hidden" name="invoiceStatus_<?=$i;?>" id="invoiceStatus_<?=$i;?>" value="<?=$invoiceStatus?>" readonly="true">
													<input type="hidden" name="invAmt_<?=$invoiceMainId;?>" id="invAmt_<?=$invoiceMainId;?>" value="<?=$totalUSDAmt?>" readonly="true">
													<input type="hidden" name="invCurrencyCode_<?=$invoiceMainId;?>" id="invCurrencyCode_<?=$invoiceMainId;?>" value="<?=$selCurrencyCode?>" readonly="true">
													<input type="hidden" name="hdnInvoiceNumber_<?=$invoiceMainId;?>" id="hdnInvoiceNumber_<?=$invoiceMainId;?>" value="<?=$invoiceNo?>" readonly="true">
												</td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$invoiceNo;?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$invoiceTypeName;?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$customer?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=number_format($totalUSDAmt,2,'.','')?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
													<?php
															$numCol = 3;
															if (sizeof($invContainerRecs)>0) {
																$nextRec=	0;						
																$selName = "";
																foreach ($invContainerRecs as $r) {							
																	$selName = $r[2];
																	$nextRec++;
																	if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
																	if($nextRec%$numCol == 0) {
																		echo "<br/>";
																	}
																}
															}
													?>
												</td>
												
											</tr>
											<?		}
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
