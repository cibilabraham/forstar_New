<?php
	require("include/include.php");
	ob_start();

	$err			= "";
	$errDel			= "";

	$editConfirmRecId	= "";
	$displayRecordId	= "";
	$noRec			= "";
	
	$editMode		= true;
	$addMode		= false;
	
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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	
	if ($p["cmdSaveChange"]!="") {
		$confirmId	=	$p["hidConfirmId"];
		$rmConfirm	=	($p["rmConfirm"]=="")?N:$p["rmConfirm"];				
		$acConfirm	=	($p["acConfirm"]=="")?N:$p["acConfirm"];
		$dppConfirm	=	($p["dppConfirm"]=="")?N:$p["dppConfirm"];
		$proConfirm=	($p["proConfirm"]=="")?N:$p["proConfirm"];
		$weightDataConfirm=	($p["weightDataConfirm"]=="")?N:$p["weightDataConfirm"];
		$receiptGateConfirm=	($p["receiptGateConfirm"]=="")?N:$p["receiptGateConfirm"];
		$deliveryDateConfirm 	= ($p["adnDeliveryDateConfirm"]=="")?N:$p["adnDeliveryDateConfirm"];
		$pkgDetailsConfirm 	= ($p["pkgDetailsConfirm"]=="")?N:$p["pkgDetailsConfirm"];
		$validPCConfirm 	= ($p["validPCConfirm"]=="")?N:$p["validPCConfirm"];
		$dppValidPrePCConfirm	= ($p["dppValidPrePCConfirm"]=="")?N:$p["dppValidPrePCConfirm"];
		$advAmtRestriction	= ($p["advAmtRestriction"]=="")?N:$p["advAmtRestriction"];
		$convertLSToMC = $p["convertLSToMC"];
		
		if ($confirmId!="") {
			$confirmRecUptd = $manageconfirmObj->updateConfirmRecord($confirmId, $rmConfirm, $acConfirm, $dppConfirm, $deliveryDateConfirm, $pkgDetailsConfirm, $validPCConfirm, $dppValidPrePCConfirm, $advAmtRestriction, $convertLSToMC,$proConfirm,$weightDataConfirm,$receiptGateConfirm);
		}
	
		if ($confirmRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRecordUpdate);
			$sessObj->createSession("nextPage","ManageConfirm.php");
		} else {
			$editMode	=	true;
			$err		=	$msg_failConfirmRecordUpdate;
		}
		$confirmRecUptd		=	false;
	}
	
	//$manageconfirmObj->advAmtRestrictionEnabled();
	//$manageconfirmObj->isACConfirmEnabled();
	//$manageconfirmObj->isRMConfirmEnabled();
	//$manageconfirmObj->isDPPConfirmEnabled();
	//$dDateCnfmEnabled = $manageconfirmObj->deliveryDateConfirmEnabled();
	//$manageconfirmObj->pkgConfirmEnabled();
	//$manageconfirmObj->pkgValidPCEnabled();
	//$manageconfirmObj->dppValidPrePCEnabled();
	// $manageconfirmObj->getLS2MCConversionType();

	# Edit 
		$confirmRec		=	$manageconfirmObj->find();
		$editConfirmRecId	=	$confirmRec[0];
		$rmConfirm		=	$confirmRec[1];
		$acConfirm		=	$confirmRec[2];
		$dppConfirm		= 	$confirmRec[3];
		$adnDeliveryDateConfirm = 	$confirmRec[4];
		$pkgDetailsConfirm		= $confirmRec[5];
		$validPCConfirm		= $confirmRec[6];
		$dppValidPrePCConfirm	= $confirmRec[7];
		$advAmtRestriction	= $confirmRec[8];
		$convertLSToMC = $confirmRec[9];
		$proConfirm = $confirmRec[10];
		$weightDataConfirm=$confirmRec[11];
		$receiptGateConfirm=$confirmRec[12];
		if ($rmConfirm=='Y') 	$rmConfirm	=	"Checked";
		if ($acConfirm=='Y') 	$acConfirm	=	"Checked";
		if ($dppConfirm=='Y') 	$dppConfirm	=	"Checked";
		if ($adnDeliveryDateConfirm=='Y')	$adnDeliveryDateConfirm = "checked";
		if ($pkgDetailsConfirm=='Y') 		$pkgDetailsConfirm = "checked";
		if ($validPCConfirm=='Y') 		$validPCConfirm = "checked";
		if ($dppValidPrePCConfirm=='Y') 	$dppValidPrePCConfirm = "checked";
		if ($advAmtRestriction=='Y') 		$advAmtRestriction = "checked";
		if ($proConfirm=='Y') 		$proConfirm = "checked";
		if ($weightDataConfirm=='Y') 		$weightDataConfirm = "checked";
		if ($receiptGateConfirm=='Y') 		$receiptGateConfirm = "checked";
		$autoLSToMC = $manuallyLSToMC = "";
		if ($convertLSToMC=="AC") $autoLSToMC = "checked";
		else if ($convertLSToMC=="MC") $manuallyLSToMC = "checked";

	$ON_LOAD_PRINT_JS	= "libjs/manageconfirm.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmManageConfirm" action="ManageConfirm.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="59%">
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<?php
		if ( $editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Confirm  </td>
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
										  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateConfirmation(document.frmManageConfirm)">	<? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<input type="hidden" name="hidConfirmId" value="<?=$editConfirmRecId;?>">
										
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
	<tr>
	  <td colspan="2" nowrap align="center">
	 <table><TR><TD align="center">
	<fieldset>
	<legend class="listing-item">RM</legend>
	  <table width="200" align="center">
                <tr>
                         <td class="fieldName" nowrap ><input name="rmConfirm" type="checkbox" id="rmConfirm" value="Y" <?=$rmConfirm?> class="chkBox"></td>					
			<td class="listing-item">Raw Material Confirmation</td>
                </tr>
                            <tr>
                              <TD><input name="acConfirm" type="checkbox" id="acConfirm" value="Y" <?=$acConfirm?> class="chkBox">
                              </TD>
                              <TD class="listing-item">Account Confirmation</TD>
                            </tr>
			     <tr>
                              <TD>
					<input name="dppConfirm" type="checkbox" id="dppConfirm" value="Y" <?=$dppConfirm?> class="chkBox">
                              </TD>
                              <TD class="listing-item" nowrap="true">Daily Pre-Process Entry Confirmation</TD>
                            </tr>	
                          </table>
	</fieldset>
	</TD></TR>
	
	<TR><TD align="center">
	<fieldset>
	<legend class="listing-item">PRO</legend>
	  <table width="200" align="center">
                <tr>
                         <td class="fieldName" nowrap ><input name="proConfirm" type="checkbox" id="proConfirm" value="Y" <?=$proConfirm?> class="chkBox"></td>					
			<td class="listing-item" nowrap="true">Procurment Order Confirmation</td>
                </tr>
				
				<tr>
                         <td class="fieldName" nowrap ><input name="weightDataConfirm" type="checkbox" id="weightDataConfirm" value="Y" <?=$weightDataConfirm?> class="chkBox"></td>					
			<td class="listing-item" nowrap="true">Weightment Data Sheet Confirmation</td>
                </tr>
				
				<tr>
                         <td class="fieldName" nowrap ><input name="receiptGateConfirm" type="checkbox" id="receiptGateConfirm" value="Y" <?=$receiptGateConfirm?> class="chkBox"></td>					
			<td class="listing-item" nowrap="true">Receipt Gate Pass Confirm</td>
                </tr>
                            	
                          </table>
	</fieldset>
	</TD></TR>
	
	<tr><TD>
	<fieldset>
	<legend class="listing-item">RTE</legend>
	<table width="200" align="center">
                <tr>
                         <td class="fieldName" nowrap >
				<input name="adnDeliveryDateConfirm" type="checkbox" id="adnDeliveryDateConfirm" value="Y" <?=$adnDeliveryDateConfirm?> class="chkBox">
			</td>					
			<td class="listing-item" onMouseover="ShowTip('In Assign docket no, delivery date confirmation.');" onMouseout="UnTip();" nowrap="true">Delivery date confirmation</td>
                </tr>
		<tr>
                         <td class="fieldName" nowrap >
				<input name="pkgDetailsConfirm" type="checkbox" id="pkgDetailsConfirm" value="Y" <?=$pkgDetailsConfirm?> class="chkBox">
			</td>					
			<td class="listing-item" onMouseover="ShowTip('Packing details confirmation.');" onMouseout="UnTip();" nowrap="true">Packing Details confirmation</td>
                </tr>
		<tr>
                         <td class="fieldName" nowrap>
				<input name="advAmtRestriction" type="checkbox" id="advAmtRestriction" value="Y" <?=$advAmtRestriction?> class="chkBox">
			</td>					
			<td class="listing-item" onMouseover="ShowTip('Restriction for entering advance amt when overdue amt exist.');" onMouseout="UnTip();" nowrap="true" align="left">Distributor Account Advance Amt</td>
                </tr>
         </table>
	</fieldset>
	</TD></tr>
	<tr><TD>
	<fieldset>
	<legend class="listing-item">PACKING</legend>
	<table width="200" align="center">
                <tr>
                         <td class="fieldName" nowrap >
							<input name="validPCConfirm" type="checkbox" id="validPCConfirm" value="Y" <?=$validPCConfirm?> class="chkBox">
						</td>					
						<td class="listing-item" onMouseover="ShowTip('Activate day\'s valid Process code for production.');" onMouseout="UnTip();" nowrap="true">Day's valid process code confirmation</td>
                </tr>
				<tr>
					<td colspan="2">
						<table>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td><input type="radio" name="convertLSToMC" id="convertLSToMC" value="AC" <?=$autoLSToMC?> class="chkBox"></td>
											<td class="listing-item" nowrap onMouseover="ShowTip('Automatically convert LS to MC Packing in Daily Frozen Packing');" onMouseout="UnTip();">Auto convert LS to MC</td>
										</tr>
									</table>
									
								</td>
								<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td><input type="radio" name="convertLSToMC" id="convertLSToMC" value="MC" <?=$manuallyLSToMC?> class="chkBox"></td>
											<td class="listing-item" nowrap onMouseover="ShowTip('Manually convert LS to MC Packing in Daily Frozen Packing');" onMouseout="UnTip();">Manually convert LS to MC</td>
										</tr>
									</table>									
								</td>
							</tr>
						</table>
					</td>
				</tr>
         </table>
	</fieldset>
	</TD></tr>
	<tr><TD>
	<fieldset>
	<legend class="listing-item">PROCESSING</legend>
	<table width="200" align="center">
                <tr>
                         <td class="fieldName" nowrap >
				<input name="dppValidPrePCConfirm" type="checkbox" id="dppValidPrePCConfirm" value="Y" <?=$dppValidPrePCConfirm?> class="chkBox">
			</td>					
			<td class="listing-item" onMouseover="ShowTip('Activate day\'s valid pre-process code for Peeling.<br>This activation allows the user to enter only the day\'s valid process code in daily pre-process section.');" onMouseout="UnTip();" nowrap="true">Day's valid pre-process entry confirmation</td>
                </tr>
         </table>
	</fieldset>
	</TD></tr>
	</table>	
	</td>
			  </tr>
			<tr>
				<td colspan="4"  height="10" ></td>
			</tr>
			<tr>
				<? if($editMode){?>
				  <td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
				  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateConfirmation(document.frmManageConfirm);"><? }?></td>
				<?} else{?>
				  <td align="center">&nbsp;&nbsp;</td>
				<?}?>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
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
  </table>
</form>
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
	?>