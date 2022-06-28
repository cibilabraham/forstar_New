<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	true;
	$addMode		=	false;
	$distributorName	=	"";

	$userId		= $sessObj->getValue("userId");

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
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

	#Setting Values
	$selClaimId = $p["selClaim"];

	if ($selClaimId!="") {
		# List all Claim items
		$claimItemrecs = $claimProcessingObj->filterClaimRecs($selClaimId);
		#find the distributor Name
		$distributorName = $claimProcessingObj->findDistributorName($selClaimId);

		list ($claimNumber, $claimType, $cod, $fixedAmt, $mrAmt, $createDate, $distributorId, $selSetledDate, $selStatusId) = $claimProcessingObj->getClaimRec($selClaimId);

		if ($claimType=='MR') 		$claimAmt = $mrAmt;
		else if ($claimType=='FA') 	$claimAmt = $fixedAmt;
		//echo "$claimAmt";
		//echo "$claimNumber, $claimType, $cod, $fixedAmt, $mrAmt";
	}

	#Update Claim Rec
	if ($p["cmdSaveChange"]!="") {		
		$selClaimId 	= $p["selClaim"];
		$dispatchDate	= mysqlDateFormat($p["dispatchDate"]);
		$selStatus	= $p["selStatus"];
		$isComplete	= ($p["isComplete"]=="")?0:$p["isComplete"];
		
		if ($selClaimId!="") {
			$claimProcessingRecUptd = $claimProcessingObj->updateClaim($selClaimId, $dispatchDate, $selStatus, $isComplete);
			# Insert Distributor Account
			if ($claimProcessingRecUptd && $p["isComplete"]!="") { 
				$distributorAccountRecIns = $distributorAccountObj->addDistributorAccount($createDate, $distributorId, $claimAmt, $cod, "Claim No:$claimNumber", $userId, '', $selClaimId);
			}
		}
	
		if ($claimProcessingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateClaimProcessing);
			$sessObj->createSession("nextPage",$url_afterUpdateClaimProcessing);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateClaimProcessing;
		}
		$claimProcessingRecUptd	=	false;
	}
	

	#List All Status Record
	//$statusRecords = $statusObj->fetchAllRecords();
	$statusRecords = $statusObj->fetchAllRecordsActiveStatus();
	#$List all Sales Order Records
	$claimPendingRecords = $claimProcessingObj->fetchNotCompleteClaimRecords();

	$ON_LOAD_PRINT_JS	= "libjs/ClaimProcessing.js";
		
	//$help_lnk="help/hlp_king.html";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmClaimProcessing" action="ClaimProcessing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
		 if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Claim Processing</td>
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
		  <input type="submit" name="cmdSaveChange" class="button" value=" Save " onClick="return validateClaimProcessing(document.frmClaimProcessing);" <? if (!$selClaimId) { echo "disabled";}?>><? }?>&nbsp;&nbsp;</td>
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
	<table width="65%" align="center" cellpadding="0" cellspacing="0">
        	<tr>
                	<td valign="top">
			<table width="200">
                               <tr>
                        	       <td class="fieldName" nowrap="nowrap">Claim</td>
                                        <td>
						  <select name="selClaim" id="selClaim" onchange="this.form.submit();">
						  <option value="">-- Select --</option>
						 <?
						foreach ($claimPendingRecords as $cpr) {
							$claimId	      =	$cpr[0];
							$claimGenerateId =	$cpr[1];
							$selected="";
							if($selClaimId==$claimId) $selected="Selected";
						?>
						<option value="<?=$claimId?>" <?=$selected?>><?=$claimGenerateId?></option>
						<? }?>
                                                      </select>                                                      </td>
                                                    </tr>
						<? if($selClaimId){?>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Distributor</td>
                                                      <td class="listing-item"><?=$distributorName?></td>
                                                    </tr>
						<? }?>
						</table></td>
                                                  <td valign="top">&nbsp;</td>
                                                </tr>
                                              </table></td>
				</tr>
			<? if($selClaimId){?>
				<tr>
				  <td colspan="2" align="center">
	<table cellpadding="1"  width="55%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	$i	=	0;
	if (sizeof($claimItemrecs)>0) {
		
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Purchased Quantity</td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Defect Quantity</td>
	</tr>
	<?
	$totalAmount = 0;
	foreach ($claimItemrecs as $sor) {
		$i++;
		$productName 	= $sor[6];
		$prodRate	= $sor[3];
		$prodQty	= $sor[4];
		$prodTotalAmt 	= $sor[5];
		$defectQty	= $sor[7];

		$soNumber	= $sor[9];
		//$totalAmount +=$prodTotalAmt;
	?>
	<tr  bgcolor="WHITE"  >
		<td height="25" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<?=$productName?><br>
		<span class="fieldName" style="line-height:normal;">(SO of <?=$soNumber?>)</span>
		</td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$prodQty?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$defectQty?></td>		
	</tr>
	<?
		}
	?>
			
	<!--tr bgcolor="white">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
                <td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><? //echo number_format($totalAmount,2,'.','');?></td>
	</tr-->
	<?
	} else if ($selClaimId!="" && $claimType=='MR') {
	?>
	<tr bgcolor="white">
		<td colspan="3"  class="err1" height="10" align="center">No Material Return Records<?//$msgNoRecords;?></td>
	</tr>
	<?
		} else if ($selClaimId!="" && $claimType=='FA') {
	?>
		<tr bgcolor="white">
		<td colspan="3" align="center">
			<table>					
					<tr>
						<TD class="fieldName">Total Amount:</TD>
						<td class="listing-item"><?=$fixedAmt?></td>
					</tr>
					<tr>
						<TD class="fieldName">Credit/Debit:</TD>
						<td class="listing-item"><?=($cod=='D')?"Debit":"Creit"?></td>
					</tr>
				</table>
		</td>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidReturnMaterialCount" id="hidReturnMaterialCount" value="<?=$i?>" >	
										</table></td>
										  </tr>
										  <? }?>
										  <!-- Here -->
											<tr>
											  <td colspan="2" align="center">&nbsp;</td>
										  </tr>
		<? if($selClaimId){?>
		<tr>
			<td colspan="2" align="center">
			<table width="200" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                                <td valign="top"><table width="200" border="0">
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Date of Settling :</td>
                                                      <td>
							<input type="text" name="dispatchDate" id="dispatchDate"  size="8" value="<?=($selSetledDate!="")?dateFormat($selSetledDate):"";?>" autoComplete="off">
						</td>
                                                    </tr>                                              
                                                  </table></td>
                                                  <td valign="top">
						<table width="200" border="0">
                                                    <tr>
                                                      <td nowrap="nowrap" class="fieldName">Status</td>
                                                      <td nowrap="nowrap" class="fieldName">
						<table width="200" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                      <td nowrap="nowrap">
						  <select name="selStatus" id="selStatus">
						  <option value="">-- Select --</option>
					  <?
					 foreach ($statusRecords as $sr) {
						$statusId		=	$sr[0];
						$status			=	stripSlash($sr[1]);
						$selected	= "";
						if ($selStatusId==$statusId) $selected = " Selected ";
					?>
					<option value="<?=$statusId?>" <?=$selected?>><?=$status?></option>
						<? }?>
                                                     </select>                                                      </td>
                                                      <td nowrap="nowrap" ><table width="100">
                                                        <tr>
                                                          <td align="right"><input name="isComplete" type="checkbox" id="isComplete" value="C" class="chkBox"></td>
                                                          <td class="listing-item">Confirm</td>
                                                        </tr>
                                                      </table>                                                        </td>
                                                    </tr>
                                                  </table></td>
                                                    </tr>
                                                  </table></td>
                                                  <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                  <td colspan="3"></td>
                                                </tr>
                                              </table></td>
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
		  <input type="submit" name="cmdSaveChange" class="button" value=" Save " onClick="return validateClaimProcessing(document.frmClaimProcessing);" <? if (!$selClaimId) { echo "disabled";}?>><? }?>&nbsp;&nbsp;</td>
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
	<input type="hidden" name="hidClaimType" id="hidClaimType" value="<?=$claimType?>">
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dispatchDate",         // ID of the input field
			eventName   : "click",	    // name of event
			button : "dispatchDate", 
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