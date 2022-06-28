<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	

	$editDisplayRecId	=	"";
	$displayRecordId	=	"";
	$noRec			=	"";
	
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
	if (!$accesscontrolObj->canAccess()) { 
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
	
	# Setting Inventory Purchase Order
	
	
	if ($p["cmdInvPO"]!="") {
		$invpoid=$p["hidinvpoid"];
		//$termsConditionsinvpo=$p["termsConditionsinvpo"];
		$termsConditionsinvpo=nl2br($p["termsConditionsinvpo"]);
		$paymenttermsinvpo=$p["paymenttermsinvpo"];						
		if ($invpoid!="") {
			$displayRecUptd = $displayrecordObj->updateInventoryPurchaseOrder($termsConditionsinvpo,$paymenttermsinvpo);
		} else {
			$displayRecUptd	=	$displayrecordObj->addInventoryPurchaseOrder($termsConditionsinvpo,$paymenttermsinvpo);
		}
	}

	# Update 
	if ($p["cmdSaveChange"]!="") {
		
		$displayRecordId  	= $p["hidDisplayRecordId"];
		$noRec			= $p["noRec"];
		$defaultYieldTol	= trim($p["defaultYieldTol"]);
						
		if ($noRec!="" && $displayRecordId!="") {
			$displayRecUptd = $displayrecordObj->updateDisplayRecord($displayRecordId,$noRec);
		} else {
			$displayRecUptd	=	$displayrecordObj->addDisplayRecord($noRec);
		}

		# yield Tolerance
		$updateDefaultYield = $displayrecordObj->updateDefaultYieldTolerance($defaultYieldTol);
			
		if ($displayRecUptd) {
			//$msg_succDisplayRecordUpdate
			$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRecUpdate;
			//$msg_failDisplayRecordUpdate
		}
		$displayRecUptd		=	false;
	}
	

	# Update 
	if ($p["cmdDFPSaveChange"]!="") {
	$selDate  	= $p["frozenPackingFrom"];
	$dfpSelDate=mysqldateformat($selDate);
	$displayRecUptd = $displayrecordObj->updateDailyFrozenPackingSetDate($dfpSelDate);
	if ($displayRecUptd) {
			//$msg_succDisplayRecordUpdate
			$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRecUpdate;
			//$msg_failDisplayRecordUpdate
		}
		$displayRecUptd		=	false;
	}


	if ($p["cmdSTEntrySaveChange"]!="") {
	$stNum  	= $p["STEntry"];
	//$dfpSelDate=mysqldateformat($selDate);
	$displayRecUptd = $displayrecordObj->updateStockstartNum($stNum);
	if ($displayRecUptd) {
			//$msg_succDisplayRecordUpdate
			$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRecUpdate;
			//$msg_failDisplayRecordUpdate
		}
		$displayRecUptd		=	false;
	}


if ($p["cmdSUPSaveChange"]!="") {
	$stNum  	= $p["supplierStnum"];
	$displayRecUptd = $displayrecordObj->updateSupplierstartNum($stNum);
	if ($displayRecUptd) {
			//$msg_succDisplayRecordUpdate
			$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRecUpdate;
			//$msg_failDisplayRecordUpdate
		}
		$displayRecUptd		=	false;
	}

	/*Add Invoice*/
	if ($p["cmdSaveInvoiceChange"]!="") {
		
		$displayRecordId  	= $p["hidDisplayRecordId"];
		$certification			= $p["certification"];
		$termsConditions		= $p["termsConditions"];
		$policyAgreement		= $p["policyAgreement"];
						
		if ($displayRecordId!="") {
			$displayInvoiceRecUptd = $displayrecordObj->addDisplayAgreementRec($displayRecordId,$certification,$termsConditions,$policyAgreement);
		} 

		# yield Tolerance
		$updateDefaultYield = $displayrecordObj->updateDefaultYieldTolerance($defaultYieldTol);
			
		if ($displayInvoiceRecUptd) {
			//$msg_succDisplayRecordUpdate
			$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRecUpdate;
			//$msg_failDisplayRecordUpdate
		}
		$displayInvoiceRecUptd		=	false;
	}
	
	# Edit 

		$displaySettingsRec	=	$displayrecordObj->find();
		$editDisplayRecId		=	$displaySettingsRec[0];
		$noRec					=	$displaySettingsRec[1];
		$certification			=	$displaySettingsRec[2];
		$termsConditions		=	$displaySettingsRec[3];
		$policyAgreement		=	$displaySettingsRec[4];	
		$defaultYieldTolerance  = 	$displayrecordObj->getDefaultYieldTolerance();
		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
		$stockEntrystnum  = 	$displayrecordObj->getStockEntrystnum();
		$supplierstnum  = 	$displayrecordObj->getSupplierstnum();
		
		$displaySettingsRecInv	=	$displayrecordObj->findInvPurchaseOrder();
		$editDisplayRecIdInv		=	$displaySettingsRecInv[0];
		$termsConditionsInv		=	$displaySettingsRecInv[1];
		$paymenttermsInv		=	$displaySettingsRecInv[2];	


	$ON_LOAD_PRINT_JS	= "libjs/displayrecord.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmDisplayRecord" action="DisplayRecord.php" method="post">
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
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%" >
		<tr><td ><!--  a1 -->	
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
		<tr><td bgcolor="white" valign="top">
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="285" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Record Display Setting  </td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
										  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateDisplayRecord(document.frmDisplayRecord)">	<? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<input type="hidden" name="hidDisplayRecordId" value="<?=$editDisplayRecId;?>">
										<tr>
										  <td colspan="2" nowrap height="10"></td>
								  		</tr>
										<tr>
										  <td colspan="2" nowrap align="center">
										<table cellpadding="2" cellspacing="0">
										<TR><TD>
										 <fieldset>
										  <table align="center">
					                                            <tr>
                                        					      <td class="fieldName" nowrap >No.of Record Display</td>
											<td>
											<INPUT NAME="noRec" TYPE="text" id="noRec" value="<?=$noRec;?>" size="4" style="text-align:right;">											</td>
					                                           </tr>
                                         </table>
										</fieldset>	
										</TD></TR>
										<tr><TD></TD></tr>
										<TR><TD>
										 <fieldset>
										  <table align="center">
					                                            <tr>
                                        					      <td class="fieldName" nowrap onMouseover="ShowTip('Default settings for Pre-Process calculation');" onMouseout="UnTip();"
>Default Yield Tolerance</td>
											<td class="listing-item" nowrap>
												<INPUT NAME="defaultYieldTol" TYPE="text" id="defaultYieldTol" value="<?=$defaultYieldTolerance;?>" size="3" style="text-align:right;">&nbsp;%											</td>
					                                           </tr>
                                         </table>
										</fieldset>	
										</TD></TR>
										</table>										</td>
									  </tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

										  <td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
										  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateDisplayRecord(document.frmDisplayRecord);"><? }?></td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
								</table>							  </td>
							</tr>
					  </table>	
					  </td>
					  </tr>
					  </table>
					  
		
		
		
		</td>
		<td valign="top">&nbsp;</td>
		<td valign="top"><!--a2-->		
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
		<tr><td bgcolor="white" valign="top">
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="285" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Frozen Packing Setting  </td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
											  <input type="submit" name="cmdDFPSaveChange" class="button" value=" Save Date " onclick="return validateDailyFrozenPackingDate(document.frmDisplayRecord)" />
										    <? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<input type="hidden" name="hidDisplayRecordId" value="<?=$editDisplayRecId;?>">
										<tr>
										  <td colspan="2" nowrap height="10"></td>
								  		</tr>
										<tr>
										  <td colspan="2" nowrap align="center">
										<table cellpadding="2" cellspacing="0">
										
										<tr><TD></TD></tr>
										<TR><TD>
										 <fieldset>
										  <table align="center">
					                                            <tr>
																
                                        					      <td class="fieldName" nowrap onMouseover="ShowTip('Daily Frozen Packing Start Date');" onMouseout="UnTip();"
>Daily Frozen Packing Start Date</td>
											<td class="listing-item" nowrap>
												<input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$defaultDFPDate?>">								</td>
					                                           </tr>
                                         </table>
										</fieldset>	
										</TD></TR>
										</table>										</td>
									  </tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

										  <td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
										    <input type="submit" name="cmdDFPSaveChange" class="button" value=" Save Date " onclick=" return validateDailyFrozenPackingDate(document.frmDisplayRecord);" />
									      <? }?></td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
								</table>							  </td>
							</tr>
					  </table>		
					 </td></tr>
		
		</table>
		
		</td></tr>
		
		</table>
		</td>
	</tr>	
	<?
		}
		
		# Listing LandingCenter Starts
	?>

	<tr>
				<td height="10" align="center" ></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
<!--New code-->
<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table  width="100%" cellspacing="0" border="0" align="center" cellpadding="0">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Entry   </td>

								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier    </td>
							</tr>											

							<tr>
							<td width="1" ></td>
								<td colspan="2" height="10" >
									<table width="100%" cellspacing="10" border="0" align="left" cellpadding="0" style="padding-right:60px;">
											<tr>
											<td width="1" ></td>
											  <td colspan="2" nowrap height="10"></td>
											</tr>
											<TR height="100px">
												<td width="1" ></td>
												 <td class="fieldName" align="right" nowrap >Start Number</td>
												 <td >
													<input type="text" id="STEntry" name="STEntry" size="8" value="<?=$stockEntrystnum?>">	
													
												</td>
<td>
												<? if($edit==true){?>
											  <input type="submit" name="cmdSTEntrySaveChange" class="button" value=" Save Number " onclick="return validateStockEntryDate(document.frmDisplayRecord)" />
										    <? }?>
											
											</td>
											 </TR>
</TR>										

									</table>
								
								</td>

								<td colspan="2" height="10" >
									<table width="100%" cellspacing="10" border="0" align="left" cellpadding="0" style="padding-right:60px;">
											<tr>
											<td width="1" ></td>
											  <td colspan="2" nowrap height="10"></td>
											</tr>
											<TR height="100px">
												<td width="1" ></td>
												 <td class="fieldName" align="right" nowrap >Start Number</td>
												 <td >
													<input type="text" id="supplierStdate" name="supplierStnum" size="8" value="<?=$supplierstnum?>">	
													
												</td>

												<td>
												<? if($edit==true){?>
											  <input type="submit" name="cmdSUPSaveChange" class="button" value=" Save Number " onclick="return validateSupplierDate(document.frmDisplayRecord)" />
										    <? }?>
											
											</td>
											 </TR>
</TR>										

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






<!--New code end--->





<!-- Invoice section-->
		<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table  width="100%" cellspacing="0" border="0" align="center" cellpadding="0">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Invoice Display Setting  </td>
							</tr>											

							<tr>
							<td width="1" ></td>
								<td colspan="2" height="10" >
									<table width="100%" cellspacing="10" border="0" align="center" cellpadding="0" style="padding-right:60px;">
											<tr>
											<td width="1" ></td>
											  <td colspan="2" nowrap height="10"></td>
											</tr>
											<TR height="100px">
												<td width="1" ></td>
												 <td class="fieldName" align="right" nowrap >Certification Display</td>
												 <td width="581">
													<textarea NAME="certification"  id="certification" value="<?php echo $certification;?>"  style="text-align:left;padding-left:5px;" rows="10" cols="75"><?php echo $certification;?></textarea>
													
												</td>
											 </TR>
											<TR height="100px">
											<td width="1" ></td>
																	  <td class="fieldName" align="left" nowrap>Terms & conditions</td>
												<td  width="581" height="100px">

												<?php $termsConditions=str_replace("<br />","\n",$termsConditions);  ?>
													<textarea NAME="termsConditions" id="termsConditions" value="<?php echo $termsConditions;?>" style="text-align:left;padding-left:5px" rows="10" cols="75" ><?php echo str_replace("<br />","rr",$termsConditions);?></textarea>
												</td>
												</TR>
												<TR height="100px">
													<td width="1" ></td>
													<td class="fieldName" align="right">Law & Policy</td>
													<td width="581">
														<textarea NAME="policyAgreement" id="policyAgreement" value="<?php echo $policyAgreement;?>" style="text-align:left;padding-left:5px" rows="10" cols="75"><?php echo $policyAgreement;?></textarea>
													</td>
												</TR>										

									</table>
								
								</td>
							</tr>

								<tr>
								<td width="1" ></td>
									<td colspan="2"  height="10" ></td>
								</tr>
								<tr>
								<td width="1" ></td>
									<? if($editMode){?>

								  <td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
								  <input type="submit" name="cmdSaveInvoiceChange" class="button" value=" Save Invoice Settings " onclick=" return validateInvoiceDisplayRec(document.frmDisplayRecord);"><? }?></td>
									
									<?} else{?>

								  <td align="center">&nbsp;&nbsp;</td>

									<?}?>
								</tr>
								<tr>
								<td width="1" ></td>
									<td colspan="2"  height="10" ></td>
								</tr>									
					  </table>
					</td>
				</tr>
			</table>
			<p>&nbsp;</p>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
			  <tr>
			    <td   bgcolor="white"><!-- Form fields start -->
			      <table  width="100%" cellspacing="0" border="0" align="center" cellpadding="0">
			        <tr>
			          <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			          <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Inventory Purchase Order Display Setting </td>
		            </tr>
			        <tr>
			          <td width="1" ></td>
			          <td colspan="2" height="10" ><table width="100%" cellspacing="10" border="0" align="center" cellpadding="0" style="padding-right:60px;">
			            <tr>
			              <td width="1" ></td>
			              <td colspan="2" nowrap="nowrap" height="10"></td>
		                </tr>
			            <tr height="100px">
			              <td width="1" ></td>
			              <td class="fieldName" align="right" nowrap="nowrap"><label for="hidinvpoid"></label>
                            <input type="hidden" name="hidinvpoid" id="hidinvpoid" value="<?php echo $editDisplayRecIdInv;?>" />
Terms &amp; conditions</td>
			              <td  width="581" height="100px"><textarea name="termsConditionsinvpo" id="termsConditionsinvpo" value="<?php echo $termsConditionsInv;?>" style="text-align:left;padding-left:5px" rows="10" cols="75" ><?php echo $termsConditionsInv;?></textarea></td>
		                </tr>
			            <tr height="100px">
			              <td width="1" ></td>
			              <td class="fieldName" align="right">Payment Terms</td>
			              <td width="581"><input name="paymenttermsinvpo" type="text" id="paymenttermsinvpo" style="text-align:left;padding-left:5px" value="<?php echo $paymenttermsInv;?>" size="75" /></td>
		                </tr>
			            </table></td>
		            </tr>
			        <tr>
			          <td width="1" ></td>
			          <td colspan="2"  height="10" ></td>
		            </tr>
			        <tr>
			          <td width="1" ></td>
			          <? if($editMode){?>
			          <td colspan="2" align="center"><? if($edit==true){?>
			            &nbsp;&nbsp;
			            <input type="submit" name="cmdInvPO" class="button" value=" Save Purchase Order Settings " onclick=" return validateInvoiceDisplayRec(document.frmDisplayRecord);" />
			            <? }?></td>
			          <?} else{?>
			          <td align="center">&nbsp;&nbsp;</td>
			          <?}?>
		            </tr>
			        <tr>
			          <td width="1" ></td>
			          <td colspan="2"  height="10" ></td>
		            </tr>
		          </table></td>
		      </tr>
		  </table>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<!-- Form fields end   -->
		</td>
	</tr>




<!--Invoice section ends here-->		
	<input type="hidden" name="hidDisplayRecordId" value="<?=$editDisplayRecId;?>">
  </table>
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

	
</form>
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>
