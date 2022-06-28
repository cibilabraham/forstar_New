<?php
	require("include/include.php");
	require_once("lib/ChangesUpdateMaster_ajax.php");	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	= "?pageNo=".$p["pageNo"];
	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;

	#Cancel
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	//$billingCompanyObj->fetchAllCompanyBankACs();
		
	# Add 
	if ($p["cmdAdd"]!="") {	
		$companyName	= addSlash(trim($p["companyName"]));
		$address	= addSlash(trim($p["address"]));
		$place		= addSlash(trim($p["place"]));
		$pinCode	= addSlash(trim($p["pinCode"]));
		$country	= addSlash(trim($p["country"]));
		/*$telNo		= addSlash(trim($p["telNo"]));
		$faxNo		= addSlash(trim($p["faxNo"]));	*/
		$alphaCode      = trim($p["alphaCode"]);
		$displayName		= trim($p["displayName"]);
		$vatTin					=	addSlash(trim($p["vatTin"]));
		$cstTin					= 	addSlash(trim($p["cstTin"]));

		$range					=	addSlash(trim($p["range"]));
		$division				=	addSlash(trim($p["division"]));
		$commissionerate		=	addSlash(trim($p["commissionerate"]));
		$exciseNo				=	addSlash(trim($p["exciseNo"]));
		$notificationDetails	=	addSlash(trim($p["notificationDetails"]));
		$panNo					= trim($p["panNo"]);
		$eicApprovalNo			= trim($p["eicApprovalNo"]);
		$tblRowCount		= $p["hidTableRowCount"];
	 	$tblRowCountContact		= $p["hidTableRowCountContact"];

		if ($companyName!="") {	
			//$billingCompanyRecIns = $billingCompanyObj->addBillingCompany($companyName, $address, $place, $pinCode, $country, $telNo, $faxNo, $userId, $alphaCode, $displayName,$vatTin,$cstTin,$range,$division,$commissionerate,$exciseNo,$notificationDetails,$panNo,$eicApprovalNo);
			$billingCompanyRecIns = $billingCompanyObj->addBillingCompany($companyName, $address, $place, $pinCode, $country, $userId, $alphaCode, $displayName,$vatTin,$cstTin,$range,$division,$commissionerate,$exciseNo,$notificationDetails,$panNo,$eicApprovalNo);
			if ($billingCompanyRecIns) {

				# Billing company Id
				$billingCompanyMainId = $databaseConnect->getLastInsertedId();


					if ($tblRowCountContact>0) {	
							
					for ($j=0; $j<$tblRowCountContact; $j++) {
						
						$status = $p["cstatus_".$j];						
						if ($status!='N') {
							$telephoneNo	= trim($p["telephoneNo_".$j]);							
							$mobileNo	= trim($p["mobileNo_".$j]);
							$fax	= trim($p["fax_".$j]);
							$email	= trim($p["email_".$j]);
							$defaultCD	= ($p["defaultCD_".$j]!="")?$p["defaultCD_".$j]:'N';

							//if ($telephoneNo!="" && $mobileNo!="") {
								# Add Bank AC
								$billingCompanyRecIns = $billingCompanyObj->addBillingCmpnyContactDetails($billingCompanyMainId,$telephoneNo, $mobileNo, $fax,$email, $defaultCD);				
						//	}
						} // Status Ends here
					} // For loop ends here
				}

				if ($tblRowCount>0) {					
					for ($i=0; $i<$tblRowCount; $i++) {
						$status = $p["status_".$i];						
						if ($status!='N') {
							$bankName	= trim($p["bankName_".$i]);							
							$accountNo	= trim($p["accountNo_".$i]);
							$defaultAC	= ($p["defaultAC_".$i]!="")?$p["defaultAC_".$i]:'N';

							if ($bankName!="" && $accountNo!="") {
								# Add Bank AC
								$cmpnyBankACRecIns = $billingCompanyObj->addBillingCmpnyBankAC($billingCompanyMainId, $bankName, $accountNo, $defaultAC);				
							}
						} // Status Ends here
					} // For loop ends here
				} // Tble row ends here
			}
		}
		//die();
		if ($billingCompanyRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddBillingCompany);
			$sessObj->createSession("nextPage",$url_afterAddBillingCompany.$selection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddBillingCompany;
		}
		$billingCompanyRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$billingCompanyRec	=	$billingCompanyObj->find($editId);
		
		$editBillingCompanyId		= $billingCompanyRec[0];
		$companyName		= $billingCompanyRec[1];
		$address		= $billingCompanyRec[2];
		$place			= $billingCompanyRec[3];
		$pinCode		= $billingCompanyRec[4];
		$country		= $billingCompanyRec[5];
	/*	$telNo			= $billingCompanyRec[6];
		$faxNo			= $billingCompanyRec[7];*/
		$alphaCode      	= $billingCompanyRec[8];
		$displayName		= $billingCompanyRec[9];
		$vatTin			=	stripSlash($billingCompanyRec[11]);
		$cstTin			= 	stripSlash($billingCompanyRec[12]);
		$notificationDetails	=	stripSlash($billingCompanyRec[13]);
		$range			=	stripSlash($billingCompanyRec[14]);
		$division		=	stripSlash($billingCompanyRec[15]);
		$commissionerate	=	stripSlash($billingCompanyRec[16]);
		$exciseNo		=	stripSlash($billingCompanyRec[17]);	
		$panNo			= 	$billingCompanyRec[18];
		$eicApprovalNo = 	$billingCompanyRec[19];
		
		# Billing company bank ac
		$billingCmpnyBankACRecs = $billingCompanyObj->getCompanyBankACRecs($editBillingCompanyId);
		$bankACArrSize = sizeof($billingCmpnyBankACRecs);
		$contactDetailsRecs = $billingCompanyObj->displayContactDtls($editBillingCompanyId);
		$contactDetArrSize = sizeof($contactDetailsRecs);
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$billingCompanyId = $p["hidBillingCompanyId"];		
		$companyName	= addSlash(trim($p["companyName"]));
		$address	= addSlash(trim($p["address"]));
		$place		= addSlash(trim($p["place"]));
		$pinCode	= addSlash(trim($p["pinCode"]));
		$country	= addSlash(trim($p["country"]));
	/*	$telNo		= addSlash(trim($p["telNo"]));
		$faxNo		= addSlash(trim($p["faxNo"]));	*/

		$alphaCode      = trim($p["alphaCode"]);	
		$displayName	= trim($p["displayName"]);
		$hidAlphaCode	= trim($p["hidAlphaCode"]);
		$vatTin					=	addSlash(trim($p["vatTin"]));
		$cstTin					= 	addSlash(trim($p["cstTin"]));

		$range					=	addSlash(trim($p["range"]));
		$division				=	addSlash(trim($p["division"]));
		$commissionerate		=	addSlash(trim($p["commissionerate"]));
		$exciseNo				=	addSlash(trim($p["exciseNo"]));
		$notificationDetails	=	addSlash(trim($p["notificationDetails"]));
		$panNo					= trim($p["panNo"]);
		$eicApprovalNo			= trim($p["eicApprovalNo"]);
		$tblRowCount		= $p["hidTableRowCount"];
		$tblRowCountContact		= $p["hidTableRowCountContact"];
		
		if ($billingCompanyId!="" && $companyName!="") {
			$billingCompanyRecUptd = $billingCompanyObj->updateBillingCompany($billingCompanyId, $companyName, $address, $place, $pinCode, $country, $telNo, $faxNo, $alphaCode, $displayName,$vatTin,$cstTin,$range,$division,$commissionerate,$exciseNo,$notificationDetails,$panNo,$eicApprovalNo);

			if ($alphaCode!=$hidAlphaCode && $billingCompanyRecUptd) {
				$updateDailyCatchEntryRec = $billingCompanyObj->updateDailyCatchEntryRec($billingCompanyId, $alphaCode);
			}


			if ($tblRowCountContact>0) {	
							
					for ($j=0; $j<$tblRowCountContact; $j++) {
						
						$status = $p["cstatus_".$j];
						if ($status!='N') {
							$telephoneNo	= trim($p["telephoneNo_".$j]);							
							$mobileNo	= trim($p["mobileNo_".$j]);
							$fax	= trim($p["fax_".$j]);
							$email	= trim($p["email_".$j]);
							$defaultCD	= ($p["defaultCD_".$j]!="")?$p["defaultCD_".$j]:'N';
							
							$contactEntryId=trim($p["contactEntryId_".$j]);
								if ($contactEntryId=="") {
									//echo "hii";
											# Add Bank AC
											$billingCompanyRecIns = $billingCompanyObj->addBillingCmpnyContactDetails($billingCompanyId,$telephoneNo, $mobileNo, $fax,$email, $defaultCD);					
								} else if ($contactEntryId!="") {
											# update Bank AC
										//	echo "jkk";
											$updatebillingCompanyRecIns = $billingCompanyObj->updateBillingCmpnyContactDetail($telephoneNo, $mobileNo, $fax,$email, $defaultCD,$contactEntryId);
											//$updateCmpnyBankACRec = $billingCompanyObj->updateBillingCmpnyBankAC($bankACEntryId, $bankName, $accountNo, $defaultAC);				
										}
							} // Status Ends here
							if ($status=='N' && $contactEntryId!="" ) {
									$delContactRec = $billingCompanyObj->delContactRec($contactEntryId);
									//	$delBankACRec = $billingCompanyObj->delBankACRec($bankACEntryId);
							} 

							/*if ($telephoneNo!="" && $mobileNo!="") {
								# Add Bank AC
								$billingCompanyRecIns = $billingCompanyObj->addBillingCmpnyContactDetails($billingCompanyMainId,$telephoneNo, $mobileNo, $fax,$email, $defaultCD);				
							}*/
						
					} // For loop ends here
				}

	//	die();

			# bank AC
			if ($tblRowCount>0) {		
				for ($i=0; $i<$tblRowCount; $i++) {
					$status 	= $p["status_".$i];
					$bankACEntryId	= $p["bankACEntryId_".$i];					
					$bankAcInUse   = $billingCompanyObj->chkCpnyBankAcInUse($bankACEntryId);
					if ($status!='N') {
						$bankName	= trim($p["bankName_".$i]);							
						$accountNo	= trim($p["accountNo_".$i]);
						$defaultAC	= ($p["defaultAC_".$i]!="")?$p["defaultAC_".$i]:'N';

						if ($bankName!="" && $accountNo!="" && $bankACEntryId=="") {
							# Add Bank AC
							$cmpnyBankACRecIns = $billingCompanyObj->addBillingCmpnyBankAC($billingCompanyId, $bankName, $accountNo, $defaultAC);				
						} else if ($bankName!="" && $accountNo!="" && $bankACEntryId!="") {
							# update Bank AC
							$updateCmpnyBankACRec = $billingCompanyObj->updateBillingCmpnyBankAC($bankACEntryId, $bankName, $accountNo, $defaultAC);				
						}
					} // Status Ends here
					
					# Need to check bank ac in use
					if ($status=='N' && $bankACEntryId!="" && !$bankAcInUse) {
						$delBankACRec = $billingCompanyObj->delBankACRec($bankACEntryId);
					} 
				} // For loop ends here
			} // Tble row ends here
		}
	
		if ($billingCompanyRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succBillingCompanyUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateBillingCompany.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failBillingCompanyUpdate;
		}
		$billingCompanyRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		$companyInUse	= false;
		$bankACInUse    = false;

		for ($i=1; $i<=$rowCount; $i++) {
			$billingCompanyId = $p["delId_".$i];

			# Checking Bill Company Used any where
			$alreadyUsed  = $billingCompanyObj->chkBillCompanyUsed($billingCompanyId);
			if ($billingCompanyId!="" && $alreadyUsed) $companyInUse = true;
			//die();
			if ($billingCompanyId!="" && !$alreadyUsed) {
				# Need to Chk 	
				$bankACInUse = $billingCompanyObj->chkBillingCpnyBankAcInUse($billingCompanyId);

				if (!$bankACInUse) {

					# Delete Bank AC Recs
					$delBankACRecs = $billingCompanyObj->deleteBankACRecs($billingCompanyId);
	
					# Delete Company recs
					$billingCompanyRecDel = $billingCompanyObj->deleteBillingCompany($billingCompanyId);
				}
				$delContactDetailRecs = $billingCompanyObj->deleteContactDetailRecs($billingCompanyId);
			}
		} // Loop ends here

		if ($billingCompanyRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelBillingCompany);
			$sessObj->createSession("nextPage",$url_afterDelBillingCompany.$selection);
		} else {			
			if ($companyInUse) $errDel = $msg_failDelBillingCompany.$msgBillingCompanyRecUsed;
			else if ($bankACInUse) $errDel = $msg_failDelBillingCompany."Bank AC is already in use.";
			else  $errDel	= $msg_failDelBillingCompany;			
		}
		$billingCompanyRecDel	=	false;
	}

	# Make efault
	if ($p["cmdDefault"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$billingCompanyId	=	$p["delId_".$i];
			if ($billingCompanyId) {
				# Update N For All Rec
				$updateDCForAllRec = $billingCompanyObj->updateAllDefaultChk();
				# Update  Y For selected Rec
				$updateBillingCompanyRec = $billingCompanyObj->updateDefaultChk($billingCompanyId);
			}
		}
	}



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$billingCompanyId	=	$p["confirmId"];


			if ($billingCompanyId!="") {
				// Checking the selected fish is link with any other process
				$billingRecConfirm = $billingCompanyObj->updateBillingCompanyconfirm($billingCompanyId);
			}

		}
		if ($billingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmbilling);
			$sessObj->createSession("nextPage",$url_afterDelBillingCompany.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$billingCompanyId = $p["confirmId"];

			if ($billingCompanyId!="") {
				#Check any entries exist
				
					$billingRecConfirm = $billingCompanyObj->updateBillingCompanyReleaseconfirm($billingCompanyId);
				
			}
		}
		if ($billingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmbilling);
			$sessObj->createSession("nextPage",$url_afterDelBillingCompany.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List all Records
	$billingCompanyRecords = $billingCompanyObj->fetchAllPagingRecords($offset, $limit);
	$billingCompanyRecordSize    = sizeof($billingCompanyRecords);

	## -------------- Pagination Settings II -------------------
	$fetchBillingCompanyRecords = $billingCompanyObj->fetchAllRecords();	// fetch All Records
	$numrows	=  sizeof($fetchBillingCompanyRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	if ($editMode) $heading	= $label_editBillingCompany;
	else	       $heading	= $label_addBillingCompany;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/BillingCompanyMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmBillingCompanyMaster" action="BillingCompanyMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>			
		</tr>
		<?}?>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Company Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName">&nbsp;Billing Company Master</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
</td>
								</tr>-->
<tr>
		<td colspan="3" align="center">
		<table width="80%" align="center">
<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%" >
					<tr>
						<td>
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
	<tr>
		<td width="1" ></td>
		<td colspan="2" >
		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('BillingCompanyMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateBillingCompanyMaster(document.frmBillingCompanyMaster);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('BillingCompanyMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateBillingCompanyMaster(document.frmBillingCompanyMaster);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidBillingCompanyId" value="<?=$editBillingCompanyId;?>">
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<TD colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
		<table>
		<TR>
		<TD valign="top">
			<!--<fieldset>-->
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table cellspacing="2">
			<tr>
				<td class="fieldName" nowrap >*Name</td>
				<td>
					<INPUT TYPE="text" NAME="companyName" size="40" value="<?=$companyName;?>">
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap >*Address</td>
				<td>
					<textarea name="address" cols="27" rows="3"><?=$address;?></textarea>
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>*Place</td>
				<td>
					<INPUT TYPE="text" NAME="place" size="30" value="<?=$place;?>">
				</td>				
			</tr>
			
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
			</TD>
			<td>&nbsp;</td>
		<td valign="top">
		<!--<fieldset>-->
		<?php			
			$entryHead = "";
			require("template/rbTop.php");
		?>
			<table cellspacing="3">
			
			<tr>
				<td class="fieldName" nowrap>*Pin Code</td>
				<td>
					<input type="text" name="pinCode" size="10" value="<?=$pinCode;?>" />
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>*Country</td>
				<td>
					<input type="text" name="country" size="10" value="<?=$country;?>" />
				</td>
			</tr>
			<!--<tr>
				<td class="fieldName" nowrap>*Tel.No</td>
				<td>
					<INPUT TYPE="text" NAME="telNo" size="30" value="<?=$telNo;?>">		
				</td>
			</tr>	
			<tr>
				<td nowrap></td>
				<td class="listing-item" style="line-height:normal;font-size:9px;">
					Eg:0471-2222222
				</td>
			</tr>		
			<tr>
				<td class="fieldName" nowrap>Fax No</td>
				<td>
					<INPUT TYPE="text" NAME="faxNo" size="30" value="<?=$faxNo;?>">
				</td>
			</tr>-->
			<tr>
				<td class="fieldName" nowrap>*Alpha Code</td>
				<td>
					<input type="text" name="alphaCode" id="alphaCode" size="5" value="<?=$alphaCode;?>" />
					<input type="hidden" name="hidAlphaCode" id="hidAlphaCode" size="5" value="<?=$alphaCode;?>" />
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>*Display Name</td>
				<td>
					<input type="text" name="displayName" id="displayName" size="8" value="<?=$displayName;?>" />
				</td>
			</tr>
				</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
		</td>
			</TR>
			
		<tr>
		
		<TD valign="top">
			<!--<fieldset>-->
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table>
				
				<tr>
						<td class="fieldName" nowrap >VAT TIN</td>
						<td >
						<INPUT TYPE="text" NAME="vatTin" size="30" value="<?=$vatTin;?>">
						</td>
						<tr>
						<td class="fieldName" nowrap >CST TIN</td>
						<td >
						<INPUT TYPE="text" NAME="cstTin" size="30" value="<?=$cstTin;?>">
						</td>
						</tr>
						
						<tr>
							<td class="fieldName" nowrap>ECC NO
							</td>
							<td >
									<INPUT TYPE="text" NAME="exciseNo" size="30" value="<?=$exciseNo;?>">
							</td>
						</tr>
						<tr>
								<td class="fieldName" nowrap>PAN NO</td>
								<td >
										<INPUT TYPE="text" NAME="panNo" size="30" value="<?=$panNo;?>">
								</td>
						</tr>
						<tr>
								<td class="fieldName" nowrap>EIC Approval NO</td>
								<td >
										<INPUT TYPE="text" NAME="eicApprovalNo" id="eicApprovalNo" size="30" value="<?=$eicApprovalNo;?>">
								</td>
						</tr>
				</tr>
				
				
			
				
			
			</table>
			<?php
				require("template/rbBottom.php");
			?>
		
		</td>
		<td>&nbsp;</td>
		<TD valign="top">
			<!--<fieldset>-->
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table cellpadding="2" cellspacing="6">
				
				<tr>
						
										<td class="fieldName" nowrap>Notification Details
										</td>
										<td >
												<INPUT TYPE="text" NAME="notificationDetails" size="30" value="<?=$notificationDetails;?>">
										</td>

								
								<tr>
										<td class="fieldName" nowrap>Range
										</td>
										<td >
												<INPUT TYPE="text" NAME="range" size="30" value="<?=$range;?>">
										</td>

								</tr>
								
								<tr>
										<td class="fieldName" nowrap>Division
										</td>
										<td >
												<INPUT TYPE="text" NAME="division" size="30" value="<?=$division;?>">
										</td>

								</tr>
								<tr>
										<td class="fieldName" nowrap>Commissionerate
										</td>
										<td >
												<INPUT TYPE="text" NAME="commissionerate" size="30" value="<?=$commissionerate;?>">
										</td>

								</tr>

				
				
				</tr>
		</table>
			<?php
				require("template/rbBottom.php");
			?>
		
		
		</td>
		
		
		
		
		
		
		</tr>
		
		
		
		
			
			
			
			
			
			</table>
		</TD>
	</tr>

	<tr>
		<TD colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
		<?php	
		$entryHead = "Contact Details";
		require("template/rbTop.php");
		?>
		<table>
			<TR>
			<TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblContactDetail" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Telephone NO</th>
					<th nowrap style="text-align:center;">Mobile NO</th>
					<th nowrap style="text-align:center;">Fax</th>
					<th nowrap style="text-align:center;">E-mail</th>
					<th nowrap style="text-align:center;">Default</th>
					<th>&nbsp;</th>
				</tr>
	<?php
	if ($contactDetArrSize>0) {
		$i=0;
		$totPendingAmt = 0;
		foreach ($contactDetailsRecs as $cdtr) {
			//echo $i++;
		$contactEntryId 	= $cdtr[0];
			$telephoneNo 	= $cdtr[1];
			$mobileNo	= $cdtr[2];
			$fax	= $cdtr[3];
			$email	= $cdtr[4];
			$defaultCD	= $cdtr[5];
	?>
	<tr align="center" class="whiteRow" id="crow_<?=$i?>">
		<td align="center" class="listing-item">
			<input type="text" size="16" id="telephoneNo_<?=$i?>" name="telephoneNo_<?=$i?>" value="<?=$telephoneNo?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="16" id="mobileNo_<?=$i?>" name="mobileNo_<?=$i?>" value="<?=$mobileNo?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="16" id="fax_<?=$i?>" name="fax_<?=$i?>" value="<?=$fax?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="24" id="email_<?=$i?>" name="email_<?=$i?>" value="<?=$email?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="checkbox" name="defaultCD_<?=$i?>" id="defaultCD_<?=$i?>" value="Y" class="chkBox" onclick="checkDefaultContact('<?=$i?>');" <?=($defaultCD=='Y')?"checked":"";?> />
		</td>
		<td align="center" class="listing-item">
			<a onclick="setContactItemStatus('<?=$i?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
			</a>
			<input type="hidden" value="" id="cstatus_<?=$i?>" name="cstatus_<?=$i?>"/>
			<input type="hidden" value="N" id="cIsFromDB_<?=$i?>" name="cIsFromDB_<?=$i?>"/>
			<input type="hidden" name="contactEntryId_<?=$i?>" id="contactEntryId_<?=$i?>" value="<?=$contactEntryId?>" />
		</td>
	</tr>
	<?php
				$i++;
			} // Loop ends here
		}
	?>	
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCountContact" id="hidTableRowCountContact" value="<?=$contactDetArrSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addContactDetails();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		<?php
		require("template/rbBottom.php");
		?>
		</TD>
	</tr>
	
	
	<tr>
		<TD colspan="2" nowrap style="padding-left:5px; padding-right:5px; padding-top:5px" valign="top">
		<?php	
		$entryHead = "BANK ACCOUNT";
		require("template/rbTop.php");
		?>
		<table>
			<TR>
			<TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblBankAc" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Bank Name</th>
					<th nowrap style="text-align:center;">Account No.</th>
					<th nowrap style="text-align:center;">Default</th>
					<th>&nbsp;</th>
				</tr>
	<?php
	if ($bankACArrSize>0) {
		$j = 0;
		$totPendingAmt = 0;
		foreach ($billingCmpnyBankACRecs as $bcb) {
			$bankACEntryId 	= $bcb[0];
			$bankName 	= $bcb[1];
			$accountNo	= $bcb[2];
			$defaultAC	= $bcb[3];
	?>
	<tr align="center" class="whiteRow" id="row_<?=$j?>">
		<td align="center" class="listing-item">
			<input type="text" size="24" id="bankName_<?=$j?>" name="bankName_<?=$j?>" value="<?=$bankName?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="24" id="accountNo_<?=$j?>" name="accountNo_<?=$j?>" value="<?=$accountNo?>" />
		</td>
		<td align="center" class="listing-item">
			<input type="checkbox" name="defaultAC_<?=$j?>" id="defaultAC_<?=$j?>" value="Y" class="chkBox" onclick="cpnyDefaultAcChk('<?=$j?>');" <?=($defaultAC=='Y')?"checked":"";?> />
		</td>
		<td align="center" class="listing-item">
			<a onclick="setBankACItemStatus('<?=$j?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
			</a>
			<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
			<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
			<input type="hidden" name="bankACEntryId_<?=$j?>" id="bankACEntryId_<?=$j?>" value="<?=$bankACEntryId?>" />
		</td>
	</tr>
	<?php
				$j++;
			} // Loop ends here
		}
	?>	
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$bankACArrSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addNewBankACItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		<?php
		require("template/rbBottom.php");
		?>
		</TD>
	</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('BillingCompanyMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateBillingCompanyMaster(document.frmBillingCompanyMaster);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('BillingCompanyMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateBillingCompanyMaster(document.frmBillingCompanyMaster);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
	<!-- Form fields end   -->
		</td>
		</tr>
		<?
			}			
			# Listing Category Starts
		?>
		</table>
		</td>
	</tr>
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php
		}
	?>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>								
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$billingCompanyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintBillingCompanyMaster.php',700,600);"><?}?>
	<?php
		if ($add || $edit) {
	?>
	&nbsp;
	<input type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmMakeDefault('delId_', '<?=$billingCompanyRecordSize;?>');" >
	<?php
		}
	?>
</td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
		<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if (sizeof($billingCompanyRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"BillingCompanyMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" >Alpha Code</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" >Display Name</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" >Name</th>
		<th style="padding-left:10px; padding-right:10px;">Address</th>
		<th style="padding-left:10px; padding-right:10px;">Tel. No.</th>
		<th style="padding-left:10px; padding-right:10px;">Default</th>
		<!--<th style="padding-left:10px; padding-right:10px;">Dr Status</th>-->
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?
		foreach ($billingCompanyRecords as $bcr) {
			$i++;
			$billingCompanyId	= $bcr[0];
			$cName			= $bcr[1];
			$cAddress		= $bcr[2];
			$CPlace			= $bcr[3];
			$cPinCode		= $bcr[4];
			$cCountry		= $bcr[5];
			$telNo			= $bcr[6];
			$faxNo			= $bcr[7];	

			$displayAddress		= "";
			if ($cAddress)	$displayAddress .= $cAddress;
			if ($CPlace)	$displayAddress .= "<br>".$CPlace;
			if ($cPinCode)	$displayAddress .= "<br>".$cPinCode;
			if ($cCountry)	$displayAddress .= "<br>".$cCountry;

			$companyAlphaCode  = $bcr[8];	
			$selDisplayName	   = $bcr[9];
			$defaultRowChk	   = $bcr[10];
			$active=$bcr[11];
			$existingrecords=$bcr[12];
			$drStatus=$bcr[13];
			$displayBankAC = "";
			$contactDetails = $billingCompanyObj->displayContactDtls($billingCompanyId);
			$showBankAC = $billingCompanyObj->displayBankACDtls($billingCompanyId);
			if ($showBankAC!="") $displayBankAC = "onMouseover=\"ShowTip('$showBankAC');\" onMouseout=\"UnTip();\" ";
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
			<?php 
			if(!$existingrecords){
			?>
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$billingCompanyId;?>" class="chkBox">
			<?php 
			}
			?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$companyAlphaCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selDisplayName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" <?=$displayBankAC?>>
			<?//=$cName;?>
			<?=($displayBankAC!="")?"<a href='###' class='link5'>$cName</a>":$cName;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayAddress;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php
		foreach($contactDetails as $cdt)
		 {
			echo $cdt[1].'<br/>';
		 }
		?>
		<?/*=$telNo;*/?></td>		
		<td align="center" style="padding-left:5px; padding-right:5px;">
			<? if($defaultRowChk=='Y'){?><img src="images/y.png" /><? } ?>
		</td>
		
		<!--
		<td align="center" id="statusRow_<?=$i?>">
			<?php // ($drStatus!='0') ? $drStatus : ''; ?>

			<?php 
			//echo $active;
			/*
			if ($active==0){?>
			
				<img src="images/x.png" border="0" onMouseover="ShowTip('Click here to Activate Dr');" onMouseout="UnTip();" onclick="return validateCompanyStatus('<?=$billingCompanyId?>','<?=$i?>');"/>
				<?php } 
				
				
				
				else {?>
				<a href="###" class="link5">
					<? if($drStatus=='Y'){?>
						<img src="images/y.png" border="0" onMouseover="ShowTip('Click here to Inactive Dr');" onMouseout="UnTip();" onclick="return validateCompanyStatus('<?=$billingCompanyId?>','<?=$i?>');"/>
					<? } else { ?>
						<img src="images/x.png" border="0" onMouseover="ShowTip('Click here to Activate Dr');" onMouseout="UnTip();" onclick="return validateCompanyStatus('<?=$billingCompanyId?>','<?=$i?>');"/>
					<? }?>
				</a>

		<?php }
		*/
		?>
		</td>-->
		
		<? if($edit==true){?>
		
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) {?>
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$billingCompanyId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='BillingCompanyMaster.php';">
			<?} ?>
		</td>
		 <? if ($confirm==true){?>
		 <td 
		 		<?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
				
				<?php if ($active==0){ ?>
				<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$billingCompanyId;?>,'confirmId');" >
				<?php } else if ($active==1){?>
				<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$billingCompanyId;?>,'confirmId');" >
				<?php }?>
				<? }?>

		<?//=$existingrecords ?>
			</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"BillingCompanyMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<?
		} else {
	?>
	<tr>
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</tbody>
	</table>
</td>
							</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$billingCompanyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintBillingCompanyMaster.php',700,600);"><?}?>
	<?php
		if ($add || $edit) {
	?>
	&nbsp;
	<input type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmMakeDefault('delId_', '<?=$billingCompanyRecordSize;?>');" >
	<?php
		}
	?>
</td>
											</tr>
										</table>			</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
					<?php
					include "template/boxBR.php"
				?>
					</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>
		
		<tr>
			<td height="10"></td>
		</tr>		
	</table> 	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addMultipleItem()
		{
			addNewBankACItem();
			addContactDetails();
		}
		function addNewBankACItem()
		{
			addNewBankAC('tblBankAc');
			//if (!validateBankACRepeat()) return false;
		}
		function addContactDetails()
		{
			addNewContacts('tblContactDetail');
			//if (!validateBankACRepeat()) return false;
		}
	</SCRIPT>
		<?php
			if ($addMode) {
		?>
		<SCRIPT LANGUAGE="JavaScript">
			//window.load = addNewBankACItem();	
			window.load = addMultipleItem();		
		</SCRIPT>
		<?php
			} 
		 if ($bankACArrSize>0) {
		?>
		<script language="JavaScript">
			fieldId = '<?=$bankACArrSize?>';
		</script>
		<?php
			}
	
		 if ($contactDetArrSize>0) {
		?>
		<script language="JavaScript">
			fieldvalue = '<?=$contactDetArrSize?>';
		</script>
		<?php
			}
		?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>