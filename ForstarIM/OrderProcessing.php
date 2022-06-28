<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	true;
	$addMode		=	false;
		

//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if(!$accesscontrolObj->canAccess())
	{ 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------	
	

$selPOId = $p["selPOId"];

$pORecords = $orderprocessingObj->filterPurchaseOrderRecs($selPOId);

if($selPOId!="") {
	$editMode		=	true;
	
	$pORec	=	$orderprocessingObj->findPORecord($selPOId);
	
	$customer		=	$customerObj->findCustomer($pORec[2]);
	$paymentTerms	=	$paymenttermsObj->findPaymentTerm($pORec[3]);
	$shipmentDet=$orderprocessingObj->findShipmentDetails($selPOId);
	$labelOld=$shipmentDet[0];
	//echo $labelOld;
	$paymentStatus=$shipmentDet[1];
	$invoiceNo=$shipmentDet[2];
	$shipmentDate=dateformat($shipmentDet[3]);
	$statusOld=$shipmentDet[4];
	$complete=$shipmentDet[5];
	/*$fish			=	$fishmasterObj->findFishName($pORec[3]);
	$processCode 	= 	$processcodeObj->findProcessCode($pORec[4]);
	$selGrade 		= 	$grademasterObj->findGradeCode($pORec[5]);
	$freezingStage 	= 	$freezingstageObj->findFreezingStageCode($pORec[6]);
	$eUCode 		= 	$eucodeObj->findEUCode($pORec[7]);
	$brand 			= 	$brandObj->findBrandCode($pORec[8]);
	$frozenCode 	= 	$frozenpackingObj->findFrozenPackingCode($pORec[9]);
	
	$frozenPackingRec	=	$frozenpackingObj->find($pORec[9]);
	$filledWt			=	$frozenPackingRec[6];
	
	
	$mCPackingCode 		= 	$mcpackingObj->findMCPackingCode($pORec[10]);
	$mcpackingRec		=	$mcpackingObj->find($pORec[10]);
	$numPacks			=	$mcpackingRec[2];
	
	$numMC			=	$pORec[11];
	$pricePerKg		=	$pORec[12];
	$valueInUSD		=	$pORec[13];
	$valueInINR		=	$pORec[14];
	*/
	
	//$eDate				=	explode("-",$pORec[16]);
	//$shipmentDate			=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
	
	/*if($orderprocessingObj->checkFrozenPackingReady($selPOId)){
		$stockStatus	=	"Ready";
	}
	else {
		$stockStatus	=	"Not Ready";
	}*/
}


if( $p["cmdSaveChange"]!="" ){
		
		$selPOId = $p["selPOId"];
	
		$labelling		=	$p["labelling"];
		$paymentStatus	=	$p["paymentStatus"];
		$invoiceNo		=	$p["invoiceNo"];
				
		$Date2			=	explode("/",$p["shipmentDate"]);
		$shipmentDate	=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
		
		$selStatus		=	$p["selStatus"];
		
		$isComplete		=	($p["isComplete"]=="")?"":$p["isComplete"];
		
		if( $selPOId!="" )
		{
			$orderProcessingRecUptd	=	$orderprocessingObj->updateOrder($selPOId, $labelling, $paymentStatus, $invoiceNo, $shipmentDate, $selStatus, $isComplete);
		}
	
		if($orderProcessingRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateOrderProcessing);
			$sessObj->createSession("nextPage",$url_afterUpdateOrderProcessing);
		}
		else
		{
			$editMode	=	false;
			$err		=	$msg_failUpdateOrderProcessing;
		}
		$orderProcessingRecUptd	=	false;
		$selPOId='';
	}
	


	
#$List all PO Records
//$purchaseOrderRecords		=	$orderprocessingObj->fetchNotCompleteRecords();
$purchaseOrderRecords		=	$orderprocessingObj->fetchPONotComplete();
#List All Status Record
	$statusRecords		=	$statusObj->fetchAllRecords();

#List All payment Terms Record
	$paymentTermRecords		=	$paymenttermsObj->fetchAllRecords();
	
#List All Record
	$labellingStageRecords		=	$labellingstageObj->fetchAllRecords();

	if($editMode)	{
		$heading	=	$label_editOrderProcessing;
	}
	else{
		$heading	=	$label_addOrderProcessing;
	}

	$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/orderprocessing.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
<form name="frmOrderProcessing" action="OrderProcessing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
		if( $editMode || $addMode)
		{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">&nbsp;&nbsp;<? if($add==true){?>
												<input type="submit" name="cmdSaveChange" class="button" value=" Save " onClick="return validateOrderProcessing(document.frmOrderProcessing);">	<? }?>&nbsp;&nbsp;</td>
												<?} else{?>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidPurchaseOrderId" value="<?=$purchaseOrderId;?>">
											<tr>
											  <td nowrap class="fieldName"></td>
											</tr>
											<tr>
												<td colspan="2" style="padding-left:60px;">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" align="center">
													<table width="75%" align="center" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="top">
																<table width="200">
																	<tr>
																		<td class="fieldName" nowrap="nowrap">PO Id </td>
																		<td>
																			<select name="selPOId" id="selPOId" onchange="this.form.submit();">
																				<option value="">-- Select --</option>
																				  <?
																				  foreach($purchaseOrderRecords as $por)
																						{
																						$purchaseOrderId	=	$por[0];
																						$pOId				=	$por[1];
																						$selected 	=	 "";
																						if($selPOId == $purchaseOrderId) $selected = "Selected";
																					?>
																					<option value="<?=$purchaseOrderId?>" <?=$selected?>><?=$pOId?></option>
																					<? }?>
																				</select>                                                      
																			</td>
																		</tr>
																		<? if($selPOId){?>
																		<tr>
																			<td class="fieldName" nowrap="nowrap">Customer</td>
																			<td class="listing-item" nowrap="nowrap"><?=$customer?></td>
																		</tr>
																		<? }?>
																	</table>
																</td>
																<td valign="top">&nbsp;</td>
															</tr>
														</table>
													</td>
												</tr>
												<? if($selPOId){?>
												<tr>
													<td colspan="2" align="center">
														<table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999">
														<?
														if( sizeof($pORecords) > 0 )
														{
														$i	=	0;
														?>
															<tr  bgcolor="#f2f2f2" align="center">
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Fish</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Process Code</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Grade</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Freezing Stage</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Brand</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Frozen Code&nbsp; </td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">MC Pkg</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">No. MC</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Price/ Kg </td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Value in USD</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Value in INR</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">Status of Stock</td>
																<td class="listing-head" style="padding-left:4px; padding-right:4px;">&nbsp;</td>
																<? if($edit==true){?>
																<? }?>
															</tr>
															<?
															$totalValueInUSD 	= "";
															$totalValueInINR	=	"";

																foreach($pORecords as $por)
																{
																	$i++;
																	$POMainId		=	$por[0];
																	$PORMEntryId 	= 	$por[6];
																	//$POGradeEntryId =	$por[11];
																	//echo "Main=$POMainId,RM=$PORMEntryId,Grade=$POGradeEntryId"."<br>";echo "GId=".
																	
																	$gradeEntryId = $por[11];
																	
																	$pOId			=	$por[1];
																	$customer		=	$customerObj->findCustomer($por[2]);
																	$fishId = $por[7];
																	$fish	=	$fishmasterObj->findFishName($por[7]);
																	$processCodeId = $por[8];
																	$processCode = $processcodeObj->findProcessCode($por[8]);	
																	if($processCode=="")
																	{
																	$processCode = $secondaryProcessCodeObj->findSecondaryProcessCode($por[8]);	
																	}
																	$gradeId = $por[12];					
																	$selGrade = $grademasterObj->findGradeCode($por[12]);
																	$freezingStage = $freezingstageObj->findFreezingStageCode($por[13]);
																	//$eUCode = $eucodeObj->findEUCode($por[9]);
																	$brand = $brandObj->findBrandCode($por[10]);
																	
																	$frozenCode = $frozenpackingObj->findFrozenPackingCode($por[14]);
																	$mCPackingCode = $mcpackingObj->findMCPackingCode($por[15]);
																	$numMC			=	$por[16];
																	$PricePerKg		=	$por[17];
																	$valueInUSD		=	$por[18];
																	$totalValueInUSD += $valueInUSD;
																	$valueInINR		=	$por[19];
																	$totalValueInINR	+= $valueInINR;	
																	//echo "Num=".$orderprocessingObj->checkFrozenPackingReady($selPOId,$fishId, $processCodeId, $gradeId);
																	$totalStock = $orderprocessingObj->checkFrozenPackingReady($PORMEntryId);
																	//$totalStock = $orderprocessingObj->checkFrozenPackingReady($selPOId,$fishId, $processCodeId, $gradeId);
																//echo $totalStock.','.$numMC.'<br/>';	
																if($totalStock>=$numMC)
																{
																	$stockStatus	=	"Ready";
																} else {
																	$lessStock = abs($totalStock-$numMC);
																	//$stockStatus	=	"Not Ready";
																	$stockStatus = "$lessStock No. MC Less";
																	$noStock	=	1;
																}
																?>
																<tr  bgcolor="WHITE"  >
																	<td height="25" class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$fish?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;"><?=$processCode?></td>
																	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selGrade?></td>
																	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$freezingStage?></td>
																	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$brand?></td>
																	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$frozenCode;?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;"><?=$mCPackingCode?></td>
																	<td class="listing-item" align="right" style="padding-right:5px;"><?=$numMC?></td>
																	<td class="listing-item" align="right" style="padding-right:5px;"><?=$PricePerKg?></td>
																	<td class="listing-item" align="right" style="padding-right:5px;"><?=$valueInUSD?></td>
																	<td class="listing-item" align="right" style="padding-right:5px;"><?=$valueInINR?></td>
																	<td class="listing-item" style=" padding-left:5px; padding-right:5px;"><?=$stockStatus?><input type="hidden" name="noStock_<?=$i?>" id="noStock_<?=$i?>" value="<?=$noStock?>" ></td>
																	<td class="listing-item" style=" padding-left:5px; padding-right:5px;"><a href="javascript:printWindow('ViewOP.php?selPOId=<?=$POMainId?>&entryID=<?=$PORMEntryId?>&edit=<?=$edit?>&delete=<?=$del?>',700,600)" class="link1" title="Click here to View OP"> VIEW DETAILS</a>
																	</td>
																</tr>
																<?
																}
																?>
																<input type="hidden" name="hidRowRMCount"	id="hidRowRMCount" value="<?=$i?>" >	<tr bgcolor="white">
																	<td height="10" colspan="9" align="right" class="listing-head">Total :</td>
																	<td  height="10" align="center" class="listing-item" style="padding-right:5px; padding-left:5px;"><strong><? echo number_format($totalValueInUSD,2);?></strong></td>
																	<td class="listing-item" height="10" style="padding-right:5px; padding-left:5px;"><strong><? echo number_format($totalValueInINR,2);?></strong></td>
																	<td class="listing-item" style="padding-right:5px; padding-left:5px;" colspan='2'>&nbsp;</td>
																</tr>
																<?
																}
																else
																{
																?>
																<tr bgcolor="white">
																	<td colspan="13"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																</tr>	
																<?
																}
																?>
															</table>
														</td>
													</tr>
													<? }?>
													<!-- Here -->
													<tr>
														<td colspan="2" align="center">&nbsp;</td>
													</tr>
													<? if($selPOId){?>
													<tr>
														<td colspan="2" align="center">
															<table width="200" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td valign="top">
																		<table width="200" border="0">
																			<tr>
																				<td class="fieldName" nowrap>Payment Terms : </td>
																				<td><span class="listing-item"><?=$paymentTerms?></span>
																				</td>
																			</tr>
                                                                            <tr>
																				<td><span class="fieldName">Labelling : </span></td>
																				<td>
																					<select name="labelling" id="labelling">
																						<option value="">-- Select --</option>
																						<?
																						foreach($labellingStageRecords as $lsr)
																						{
																							$i++;
																							$labellingStageId		=	$lsr[0];
																							$labellingStageName	=	stripSlash($lsr[1]);
																							$selected=($labelOld==$labellingStageId)?"selected":"";
																						?>
																						<option value="<?=$labellingStageId?>" <?=$selected?>>
																						 <?=$labellingStageName?>
																						</option>
																						<? }?>
																					</select>
																				</td>
																			</tr>
																			<tr>
																				<td nowrap="nowrap"><span class="fieldName">Status of Payment :</span></td>
																				<td nowrap="nowrap"><span class="listing-item">
																					<input name="paymentStatus" type="text" id="paymentStatus" value="<?=$paymentStatus?>" />
																					</span>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td valign="top">
																		<table width="200" border="0">
																			<tr>
																				<td class="fieldName" nowrap="nowrap">Export Invoice No :</td>
																				<td><input name="invoiceNo" type="text" id="invoiceNo" value="<?=$invoiceNo?>" /></td>
																			</tr>
																			<tr>
																				<td class="fieldName" nowrap="nowrap">Date of Shipment :</td>
																				<td><input type="text" id="shipmentDate" name="shipmentDate" size="6" value="<?=$shipmentDate?>"  /></td>
																			</tr>
																			<tr>
																				<td nowrap="nowrap" class="fieldName">Status</td>
																				<td nowrap="nowrap" class="fieldName">
																					<table width="200" cellpadding="0" cellspacing="0">                                                    
																						<tr>
																							<td nowrap="nowrap">
																								<select name="selStatus" id="selStatus">
																									<option value="">-- Select --</option>
																									 <?
																									 foreach($statusRecords as $sr)
																										{
																										$statusId		=	$sr[0];
																										$status			=	stripSlash($sr[1]);
																										$sel	= ($statusOld==$statusId)?"selected":"";
																									?>
																									<option value="<?=$statusId?>"  <?=$sel?> ><?=$status?></option>
																									<? }?>
																								</select>                                                      
																							</td>
																							<td nowrap="nowrap" >
																								<table width="100">
																									<tr>
																									  <td align="right"><input name="isComplete" type="checkbox" id="isComplete" value="C" class="chkBox"></td>
																									  <td class="listing-item">Confirm</td>
																									</tr>
																								</table>                                                       
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td>&nbsp;</td>
																</tr>
																<tr>
																	<td colspan="3"></td>
																</tr>
															</table>
														</td>
													</tr>
													<? }?>
													<!-- Here-->
													<tr>
														<td align="center">&nbsp;</td>
														<td align="center">&nbsp;</td>
													</tr>
													<tr>
														<? if($editMode){?>
														<td colspan="2" align="center">&nbsp;&nbsp;<? if($add==true){?>
														<input type="submit" name="cmdSaveChange" class="button" value=" Save " onClick="return validateOrderProcessing(document.frmOrderProcessing);"><? }?>&nbsp;&nbsp;</td>
														<? } else{?>
														<? }?>
													</tr>
													<tr>
														<td  height="10" ></td>
													</tr>
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
				<?
				}
				# Listing Grade Starts
				?>
				<tr>
					<td height="10" align="center" ></td>
				</tr>
				<tr>
					<td><!-- Form fields end   --></td>
				</tr>	
				<tr>
					<td height="10"></td>
				</tr>	
			</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "shipmentDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "shipmentDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	
	</SCRIPT>
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>