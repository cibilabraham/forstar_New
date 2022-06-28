<?php
	require("include/include.php");
	require_once("lib/customer_ajax.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated 	= false;

	$selection 	= "?pageNo=".$p["pageNo"];

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

	#For Refreshing the main Window when click PopUp window
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];
	if ($g["returnUrl"]=="") $returnUrl = $p["returnUrl"];
	else $returnUrl = $g["returnUrl"];
	
	$hideNav = false;
	if ($popupWindow || $returnUrl)  $hideNav = true;

	# Add New
	if( $p["cmdAddNew"]!="" ){
		$addMode	= true;
		$editMode	= false;
	}

	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;
	}
		
	# Add
	if ($p["cmdAdd"]!="") {
		//$customerCode		=	addSlash(trim($p["customerCode"]));
		$customerCode 		= 	"CUST_".autoGenNum();
		$customerName		=	addSlash(trim($p["customerName"]));
		
		/*
		$cuCountry		=	addSlash(trim($p["cuCountry"]));
		$cuContactPerson	=	addSlash(trim($p["cuContactPerson"]));
		$cuContactNo		=	addSlash(trim($p["cuContactNo"]));
		$agName			=	addSlash(trim($p["agName"]));
		$agContactPerson	=	addSlash(trim($p["agContactPerson"]));
		$agContactNo		=	addSlash(trim($p["agContactNo"]));
		*/

		$officeAddress		= addSlash(trim($p["officeAddress"]));
		$selCountry		= $p["selCountry"];		
		$cuContactNo		= addSlash(trim($p["telephoneNo"]));
		$faxNo			= addSlash(trim($p["faxNo"]));
		$telephoneNos		= addSlash(trim($p["telephoneNos"]));
		$faxNos			= addSlash(trim($p["faxNos"]));

		//$selBrands		= explode(",",$p["selBrandId"]);
		if ($p["selShippingLineId"]) $selShippingLines	= explode(",",$p["selShippingLineId"]);
		else $selShippingLines = array();

		$email			= addSlash(trim($p["email"]));
		$website		= addSlash(trim($p["website"]));
		$description		= addSlash(trim($p["description"]));

		$tableRowCount		= $p["hidTableRowCount"];
		$brandTableRowCount	= $p["hidBrandTableRowCount"];
		$lastId = "";
		
		if ($p["selAgentId"]) $selAgents = explode(",",$p["selAgentId"]);
		else $selAgents = array();
		
		if ($p["selPaymentTermId"]) $selPaymentTerms	= explode(",",$p["selPaymentTermId"]);
		else $selPaymentTerms = array();		
		
		if ($customerCode!="" && $customerName && $selCountry) {

			$customerRecIns	= $customerObj->addCustomer($customerCode, $customerName, $officeAddress, $selCountry, $cuContactNo, $faxNo, $telephoneNos, $faxNos, $email, $website, $description, $userId);
			
			#Find the Last inserted Id From m_customer Table
			if ($customerRecIns) $lastId = $databaseConnect->getLastInsertedId();			
		
			# Add Customer to Brand
			/*
			if (sizeof($selBrands)>0 && $customerRecIns!="" && $lastId) {
				$addCust2Brand = $customerObj->addCust2Brand($lastId, $selBrands);
			}
			*/
			# Add Customer to Shipping
			if (sizeof($selShippingLines)>0 && $customerRecIns!="" && $lastId) {
				$addCust2Shipping = $customerObj->addCust2Shipping($lastId, $selShippingLines);
			}

			# Add Customer 2 Agent
			if (sizeof($selAgents)>0 && $customerRecIns!="" && $lastId) {
				$addCustomer2Agent = $customerObj->addCustomer2Agent($lastId, $selAgents);
			}

			# Add Customer 2 Payment Term
			if (sizeof($selPaymentTerms)>0 && $customerRecIns!="" && $lastId) {
				$addCustomer2PaymentTerm = $customerObj->addCustomer2PaymentTerm($lastId, $selPaymentTerms);
			}

			

			# Multiple Brand Adding
			if ($brandTableRowCount>0 && $customerRecIns!="") {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["bStatus_".$i];
					if ($status!='N') {
						$brand	= addSlash(trim($p["brand_".$i]));
						
						# IF SELECT ALL STATE
						if ($lastId!="" && $brand!="") {
							$customerBrandIns = $customerObj->addCustomerBrand($lastId, $brand);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here	

			if ($tableRowCount>0 && $customerRecIns!="") {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$personName	= addSlash(trim($p["personName_".$i]));
						$designation	= addSlash(trim($p["designation_".$i]));
						$role		= addSlash(trim($p["role_".$i]));
						$contactNo	= addSlash(trim($p["contactNo_".$i]));

						# IF SELECT ALL STATE
						if ($lastId!="" && $personName!="") {
							$customerContactIns = $customerObj->addCustomerContact($lastId, $personName, $designation, $role, $contactNo);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here	

			if ($customerRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddCustomer);
				if (!$hideNav) $sessObj->createSession("nextPage",$url_afterAddCustomer.$selection);
				else  $recUpdated = true;
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddCustomer;
			}
			$customerRecIns	=	false;
		}
	}
	
	# Edit 
	
	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$editMode		=	true;
		$customerRec		=	$customerObj->find($editId);
		
		$editCustomerId		=	$customerRec[0];
		$customerCode		=	stripSlash($customerRec[1]);
		$customerName		=	stripSlash($customerRec[2]);
		
		/*
		$cuCountry			=	stripSlash($customerRec[3]);
		$cuContactPerson	=	stripSlash($customerRec[4]);
		$cuContactNo		=	stripSlash($customerRec[5]);
		$agName				=	stripSlash($customerRec[6]);
		$agContactPerson	=	stripSlash($customerRec[7]);
		$agContactNo		=	stripSlash($customerRec[8]);
		$description		=	stripSlash($customerRec[9]);
		*/
		$officeAddress		= stripSlash($customerRec[3]);
		$selCountry		= $customerRec[4];		
		$telephoneNo		= stripSlash($customerRec[5]);
		$faxNo			= stripSlash($customerRec[6]);
		$telephoneNos		= stripSlash($customerRec[7]);
		$faxNos			= stripSlash($customerRec[8]);

		$email			= stripSlash($customerRec[9]);
		$website		= stripSlash($customerRec[10]);
		$description		= stripSlash($customerRec[11]);

		# Entry Records
		$customerContactRecs 	= $customerObj->getCustomerContactRecs($editCustomerId);
		$customerBrandRecs		= $customerObj->getSelBrandRecs($editCustomerId);
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$customerId		=	$p["hidCustomerId"];
		//$customerCode		=	addSlash(trim($p["customerCode"]));
		$customerName		=	addSlash(trim($p["customerName"]));
		/*
		$cuCountry			=	addSlash(trim($p["cuCountry"]));
		$cuContactPerson	=	addSlash(trim($p["cuContactPerson"]));
		$cuContactNo		=	addSlash(trim($p["cuContactNo"]));
		$agName				=	addSlash(trim($p["agName"]));
		$agContactPerson	=	addSlash(trim($p["agContactPerson"]));
		$agContactNo		=	addSlash(trim($p["agContactNo"]));
		$description		=	addSlash(trim($p["description"]));
		*/
		$officeAddress		= addSlash(trim($p["officeAddress"]));
		$selCountry		= $p["selCountry"];		
		$cuContactNo		= addSlash(trim($p["telephoneNo"]));
		$faxNo			= addSlash(trim($p["faxNo"]));
		$telephoneNos		= addSlash(trim($p["telephoneNos"]));
		$faxNos			= addSlash(trim($p["faxNos"]));

		//$selBrands		= explode(",",$p["selBrandId"]);
		if ($p["selShippingLineId"]) $selShippingLines	= explode(",",$p["selShippingLineId"]);
		else $selShippingLines = array();
		//print_r($selShippingLines);

		$email			= addSlash(trim($p["email"]));
		$website		= addSlash(trim($p["website"]));
		$description		= addSlash(trim($p["description"]));

		$tableRowCount		= $p["hidTableRowCount"];
		$brandTableRowCount	= $p["hidBrandTableRowCount"];

		if ($p["selAgentId"]) $selAgents = explode(",",$p["selAgentId"]);
		else $selAgents = array();
		
		if ($p["selPaymentTermId"]) $selPaymentTerms	= explode(",",$p["selPaymentTermId"]);
		else $selPaymentTerms = array();
		
		if ($customerId!="" && $customerName!="") {
			$customerRecUptd = $customerObj->updateCustomer($customerId, $customerName, $officeAddress, $selCountry, $cuContactNo, $faxNo, $telephoneNos, $faxNos, $email, $website, $description);
			
			# Add Customer 2 Agent
			if (sizeof($selAgents)>0 && $customerRecUptd!="" && $customerId) {
				$addCustomer2Agent = $customerObj->addCustomer2Agent($customerId, $selAgents);
			}

			# Delete Existing Shipping Line
			$delShippingLine = $customerObj->deleteCustomer2Shipping($customerId);
			# Add Customer to Shipping
			if (sizeof($selShippingLines)>0 && $customerId) {
				$addCust2Shipping = $customerObj->addCust2Shipping($customerId, $selShippingLines);
			}

			# Del payment Terms
			$delPaymentTerms = $customerObj->deleteCustomer2paymentTerms($customerId);
			# Add Customer 2 Payment Term
			if (sizeof($selPaymentTerms)>0 && $customerId) {
				$addCustomer2PaymentTerm = $customerObj->addCustomer2PaymentTerm($customerId, $selPaymentTerms);
			}

			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$agentContactId  = $p["shipCompanyContactId_".$i];
				if ($status!='N') {
					$personName	= addSlash(trim($p["personName_".$i]));
					$designation	= addSlash(trim($p["designation_".$i]));
					$role		= addSlash(trim($p["role_".$i]));
					$contactNo	= addSlash(trim($p["contactNo_".$i]));
					
					if ($customerId!="" && $personName!="" && $agentContactId!="") {
						$updateShippingCompanyContactRec = $customerObj->updateCustomerContact($agentContactId, $personName, $designation, $role, $contactNo);
					} else if ($customerId!="" && $personName!="" && $agentContactId=="") {				
						$companyContactIns = $customerObj->addCustomerContact($customerId, $personName, $designation, $role, $contactNo);
					}
				} // Status Checking End

				if ($status=='N' && $agentContactId!="") {
					$delCustomerContactRec = $customerObj->delCustomerContactRec($agentContactId);
				}
			} // Contact Loop ends here

			# ----------------------------Brand
			for ($i=0; $i<$brandTableRowCount; $i++) {
				$status 	 	 = $p["bStatus_".$i];
				$brandEntryId  		= $p["custBrandEId_".$i];
				if ($status!='N') {
					$brand	= addSlash(trim($p["brand_".$i]));
					
					if ($customerId!="" && $brand!="" && $brandEntryId!="") {
						$updateBrandRec = $customerObj->updateBrand($brandEntryId, $brand);
					} else if ($customerId!="" && $brand!="" && $brandEntryId=="") {				
						$brandIns = $customerObj->addCustomerBrand($customerId, $brand);
					}
				} // Status Checking End

				if ($status=='N' && $brandEntryId!="") {
					# Check Brand In use
					$brandInUse = $customerObj->customerBrandRecInUse($brandEntryId);
					if (!$brandInUse) $delCustomerBrandRec = $customerObj->delBrandRec($brandEntryId);
						
				}
			} // Brand Loop ends here
			
		}
	
		if($customerRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateCustomer);
			if (!$hideNav) $sessObj->createSession("nextPage",$url_afterUpdateCustomer.$selection);
			else  $recUpdated = true;
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateCustomer;
		}
		$customerRecUptd	=	false;
	}
	
	




	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		$recordInUse   = false;
		for($i=1; $i<=$rowCount; $i++) {
			$customerId	=	$p["delId_".$i];
			
			if ($customerId!="") {
				# Rec In use check
				$recInUse = $customerObj->customerRecInUse($customerId);
				if (!$recInUse) {					
					# Delete Existing Shipping Line
					$delShippingLine = $customerObj->deleteCustomer2Shipping($customerId);
					# Contacts
					$delContacts	 = $customerObj->deleteCustomerContactRecs($customerId);
					# Bands
					$delBrands	 =  $customerObj->deleteCustomerBrandRecs($customerId);
					# payment Terms
					$delPaymentTerms = $customerObj->deleteCustomer2paymentTerms($customerId);
					#main
					$customerRecDel = $customerObj->deleteCustomer($customerId);					
				} else {
					$recordInUse   = true;
				}
			}
		}
		if ($customerRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCustomer);
			$sessObj->createSession("nextPage",$url_afterDelCustomer.$selection);
		} else {
			if ($recordInUse) $errDel = $msg_failDelCustomer." Customer is already in use.<br>Please check in Agent Master/ Quick Entry List/ Daily Frozen Packing/ Purchase Order/ Invoice";
			else $errDel	=	$msg_failDelCustomer;
		}
		$customerRecDel	=	false;
	}
	

if ($p["btnConfirm"]!="")
{
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$customerId = $p["confirmId"];
			if ($customerId!="") {
				#Check any entries exist				
					$customerRecDel = $customerObj->updateCustomerconfirm($customerId);				
			}
}
if ($customerRecDel) {
$sessObj->createSession("displayMsg",$msg_succConfirmCustomerCategory);
$sessObj->createSession("nextPage",$url_afterDelCustomer.$selection);
} else {
$errConfirm	=	$msg_failConfirm;
}
}

if ($p["btnRlConfirm"]!="")
{
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
$customerId = $p["confirmId"];
if ($customerId!="") {
#Check any entries exist

$customerRecDel = $customerObj->updateCustomerReleaseconfirm($customerId);
				
}
}
		if ($customerRecDel) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmCustomerCategory);
			$sessObj->createSession("nextPage",$url_afterDelCustomer.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}










	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
		
	#List All Record	
	$customerRecords	= $customerObj->fetchPagingRecords($offset, $limit);
	$customerRecordSize	= sizeof($customerRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($customerObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode ) {
		#List all City
		$cityResultSetObj = $cityMasterObj->fetchAllRecords('');
		
		/*
		# Get All Brand
		$fetchBrandRecs 	= $brandObj->fetchAllRecords();
		$selBrandRecs 	= array();		
		if ($editMode) {
			$selBrandRecs = $agentMasterObj->getSelCustomerRecs($editId);
		}
		$brandRecords = ary_diff($fetchBrandRecs, $selBrandRecs);
		*/
		# --Get All ShippingLines
		$fetchShippingCompanyRecs = $customerObj->getShippingComapnyRecords();
		$selShippingRecs 	= array();		
		if ($editMode) {
			$selShippingRecs = $customerObj->getSelShippingRecs($editCustomerId);
		}
		$shippingCompanyRecords = ary_diff($fetchShippingCompanyRecs, $selShippingRecs);
		
		
		# Get All Agent
		$fetchAgentRecs  = $customerObj->fetchAllAgentRecs();
		$selAgentRecs 	= array();
		if ($editMode) {
			$selAgentRecs = $customerObj->getAgentList($editId);
		}
		$agentRecords = ary_diff($fetchAgentRecs, $selAgentRecs);

		# Get all Country Recs
		$countryMasterRecs = $countryMasterObj->fetchAllRecordsActivecountry();

		# Get All Payment Terms
		$fetchPaymentTermsRecs  = $customerObj->fetchAllPaymentTermsRecs();
		$selPaymentTermsRecs 	= array();
		if ($editMode) {
			$selPaymentTermsRecs = $customerObj->getPaymentTermList($editId);
		}
		$paymentTermsRecs = ary_diff($fetchPaymentTermsRecs, $selPaymentTermsRecs);
		
	}

	if ($editMode) $heading	= $label_editCustomer;
	else $heading	= $label_addCustomer;

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";
	

	$ON_LOAD_PRINT_JS	= "libjs/customer.js";
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav

	# Include Template [topLeftNav.php]
	if (!$hideNav) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>

<div id="spo-filter"></div>
<div id="spo-box">
  <iframe width="95%" height="400" id="addNewIFrame" src="" style="border:none;" frameborder="0"></iframe>	
  <p align="center"> 
      <input type="button" name="cancel" value="Close" onClick="closeLightBox()">
  </p>
</div>
	<form name="frmCustomer" action="Customer.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
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
								$bxHeader="CUSTOMERS";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;CUSTOMERS</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="90%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%">
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Customer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateAddCustomer(document.frmCustomer);">												</td>
												
												<?} else{?>

												
												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Customer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateAddCustomer(document.frmCustomer);">												</td>

												<?}?>
											</tr>
	<input type="hidden" name="hidCustomerId" id="hidCustomerId" value="<?=$editCustomerId;?>">
	<tr>
		<td colspan="2" height="5"></td>
	</tr>
	<tr>
	<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;" align="center">
	<table>
		<TR>
			<TD valign="top">
			<table width="200" border="0">
		<tr><TD colspan="2">
		<table>
		 <tr>
                         <td class="fieldName" nowrap="nowrap">*Customer Name </td>
                         <td class="listing-item">
				<input name="customerName" type="text" id="customerName" value="<?=$customerName?>" onchange="xajax_chkCustomerNameExist(document.getElementById('customerName').value, '<?=$editCustomerId?>', '<?=$mode?>');">
				<span id="divNameExistMsg" class="err1" style="font-size:11px;line-height:normal;"></span>				
			</td>
                </tr>
		</table>
		</TD></tr>						
		
		<tr><TD colspan="2">
			<!--<fieldset><legend class="listing-item">Office</legend>-->
			<?php			
				$entryHead = "Office";
				require("template/rbTop.php");
			?>
			<table>
				<tr>
					<TD>
					<table>
					<TR>
					<td class="fieldName" nowrap >Address</td>
					<td>
						<textarea name="officeAddress"><?=$officeAddress?></textarea>	
					</td>
				</TR>
				<tr>
					<td nowrap class="fieldName" >*Country</td>
					<td nowrap>
						<select name="selCountry" id="selCountry">
						<option value="">-- Select --</option>
						<?php
						foreach ($countryMasterRecs as $cmr) {
							$countryId 	= $cmr[0];	
							$cntryName	= $cmr[1];
							$selected = ($selCountry==$countryId)?"selected":""
						?>
						<option value="<?=$countryId?>" <?=$selected?>><?=$cntryName?></option>
						<?  }?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Telephone No.</td>
					<td>
						<input type="text" name="telephoneNo" id="telephoneNo" value="<?=$telephoneNo?>" />	
					</td>
				</tr>
				</table>
					</TD>
					<td>&nbsp;</td>
					<td valign="top">
						<table>							
							<tr>
								<td class="fieldName" nowrap >Fax No.</td>
								<td>
									<input type="text" name="faxNo" id="faxNo" value="<?=$faxNo?>" />	
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Email</td>
								<td>
									<input type="text" name="email" id="email" value="<?=$email?>" />	
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Website</td>
								<td>
									<input type="text" name="website" id="website" value="<?=$website?>" />	
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
		</TD></tr>
		<tr><TD colspan="2">
		<table><TR><TD>
						<table>
							<tr>
								<td class="fieldName" nowrap >Telephone Nos.</td>
								<td>
									<input type="text" name="telephoneNos" id="telephoneNos" value="<?=$telephoneNos?>" size="32" />	
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap >Fax Nos.</td>
								<td>
									<input type="text" name="faxNos" id="faxNos" value="<?=$faxNos?>" size="32" />	
								</td>
							</tr>
						</table>
		</TD>
		<td valign="top">
		<table><TR>
			 <td class="fieldName" nowrap="nowrap">Description</td>
                         <td class="listing-item"><textarea name="description" id="description"><?=$description?></textarea></td>
		</TR></table>
		</td>
		</TR>
		</table>	
		</TD>
		
		</tr>
               </table>
			</TD>
			<TD>&nbsp;</TD>
			<TD valign="top">
	<table>
		<TR>
			<TD>
			<table>
				<TR>
					<TD valign="top">
					<Table cellpadding="0" cellspacing="0">
	<TR><TD>
	<!--<fieldset><legend class="listing-item">Shipping Line</legend>-->
	<?php			
		$entryHead = "Shipping Line";
		require("template/rbTop.php");
	?>
	<table>
				<tr>					
					<td>
					<table>
						<TR>
							<TD>
				<select name="selAllShipping[]" size="7" multiple id="selAllShipping">
                                <option value="" >--Rejected--</option>
                                <?php
				if (sizeof($shippingCompanyRecords)> 0) {
					foreach ($shippingCompanyRecords as $gl) {
						$id		= $gl[0];
						$displayCustName	= $gl[1];
						$selected	= "";						
				?>
                                <option value="<?=$id;?>" <?=$selected;?> ><?=$displayCustName;?></option>
                                <?php
				  	}
				}
			  	?>
                              </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selAllShipping'), document.getElementById('selShipping'), document.getElementById('selShippingLineId'));" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selAllShipping'), document.getElementById('selShipping'), document.getElementById('selShippingLineId'));" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selAllShipping'), document.getElementById('selShipping'), document.getElementById('selShippingLineId'));" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selAllShipping'), document.getElementById('selShipping'), document.getElementById('selShippingLineId'));" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selShipping[]" size="7" multiple id="selShipping">
                                	<option value="" >--Preferred--</option>
					<?php
					$sCustom = array();
					$sr = 0;
					foreach ($selShippingRecs as $gl) {
						$selCustId = $gl[0];
						$selCustName = $gl[1];
						$sCustom[$sr] = $selCustId;
						
						/*
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $agentMasterObj->pcGradeRecInUse($processCodeId, $selCustId);
							if ($chkRecExist) $style = "style='color:red'";
		
						}
						*/
				?>
                                <option value="<?=$selCustId;?>" <?=$style?>><?=$selCustName;?></option>
				<?php 
					$sr++;
					}
				?>
                              	</select>
				<input type="hidden" name="selShippingLineId" id="selShippingLineId" value="<?=implode(",",$sCustom);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
	<?php
	if (!$hideNav) {
	?>
	<tr>
		<TD nowrap="true">
		<span>
			<a href="###" class="link1" title="Click to add New shipping Line." onClick="return loadShippingLine();" onMouseover="ShowTip('Click to add New shipping Line.');" onMouseout="UnTip();">New shipping Line</a>
		</span>
		</TD>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
		require("template/rbBottom.php");
	?>
	<!--</fieldset>-->
	</TD></TR></table>
					</TD>
					<TD valign="top">
	<!-- Payment Terms section -->
			<!--<fieldset><legend class="listing-item">*Payment Terms</legend>-->
			<?php			
				$entryHead = "*Payment Terms";
				require("template/rbTop.php");
			?>
			<table>
				<tr>
					<td>
					<table>
						<TR>
							<TD>
				<select name="selAllPTerms[]" size="7" multiple id="selAllPTerms">
                                <option value="" >--Select--</option>
                                <?php
				if (sizeof($paymentTermsRecs)> 0) {
					$displayName = "";
					foreach ($paymentTermsRecs as $gl) {
						$id		= $gl[0];
						$displayName	= $gl[1];
						$selected	= "";						
				?>
                                <option value="<?=$id;?>" <?=$selected;?> ><?=$displayName;?></option>
                                <?php
				  	}
				}
			  	?>
                              </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selAllPTerms'), document.getElementById('selPaymentTerms'), document.getElementById('selPaymentTermId'));" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selAllPTerms'), document.getElementById('selPaymentTerms'), document.getElementById('selPaymentTermId'));" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selAllPTerms'), document.getElementById('selPaymentTerms'), document.getElementById('selPaymentTermId'));" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selAllPTerms'), document.getElementById('selPaymentTerms'), document.getElementById('selPaymentTermId'));" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selPaymentTerms[]" size="7" multiple id="selPaymentTerms">
                                	<option value="">--Active--</option>
					<?php
					$sptCustom = array();
					$sr = 0;
					foreach ($selPaymentTermsRecs as $gl) {
						$selPTId = $gl[0];
						$selPTName = $gl[1];
						$sptCustom[$sr] = $selPTId;
						
						/*
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $agentMasterObj->pcGradeRecInUse($processCodeId, $selCustId);
							if ($chkRecExist) $style = "style='color:red'";
		
						}
						*/
				?>
                                <option value="<?=$selPTId;?>" <?=$style?>><?=$selPTName;?></option>
				<?php 
					$sr++;
					}
				?>
                              	</select>
				<input type="hidden" name="selPaymentTermId" id="selPaymentTermId" value="<?=implode(",",$sptCustom);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
					</TD>
				</TR>
			</table>
			</TD>
		</TR>
		<TR>
			<TD>
			<table>
				<TR>
					<TD valign="top">
					<!-- Agent section -->
		<!--<fieldset> <legend class="listing-item">Agent</legend>-->
			<?php			
				$entryHead = "Agent";
				require("template/rbTop.php");
			?>
			<table>
				<tr>
					<td>
					<table>
						<TR>
							<TD>
				<select name="selAllAgent[]" size="7" multiple id="selAllAgent">
                                <option value="" >--Select--</option>
                                <?php
				if (sizeof($agentRecords)> 0) {
					$displayName = "";
					foreach ($agentRecords as $gl) {
						$id		= $gl[0];
						$displayName	= $gl[1];
						$selected	= "";						
				?>
                                <option value="<?=$id;?>" <?=$selected;?> ><?=$displayName;?></option>
                                <?php
				  	}
				}
			  	?>
                              </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selAllAgent'), document.getElementById('selCustomer'), document.getElementById('selAgentId'));" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selAllAgent'), document.getElementById('selCustomer'), document.getElementById('selAgentId'));" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selAllAgent'), document.getElementById('selCustomer'), document.getElementById('selAgentId'));" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selAllAgent'), document.getElementById('selCustomer'), document.getElementById('selAgentId'));" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selCustomer[]" size="7" multiple id="selCustomer">
                                	<option value="" >Active Agent</option>
					<?php
					$sCustom = array();
					$sr = 0;
					foreach ($selAgentRecs as $gl) {
						$selCustId = $gl[0];
						$selCustName = $gl[1];
						$sCustom[$sr] = $selCustId;
						
						/*
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $agentMasterObj->pcGradeRecInUse($processCodeId, $selCustId);
							if ($chkRecExist) $style = "style='color:red'";
		
						}
						*/
				?>
                                <option value="<?=$selCustId;?>" <?=$style?>><?=$selCustName;?></option>
				<?php 
					$sr++;
					}
				?>
                              	</select>
				<input type="hidden" name="selAgentId" id="selAgentId" value="<?=implode(",",$sCustom);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
				<?php
				if (!$hideNav) {
				?>
				<tr>
					<TD nowrap="true">
					<span>
						<a href="###" class="link1" title="Click to add New Agent." onClick="return loadAgent();" onMouseover="ShowTip('Click to add New Agent.');" onMouseout="UnTip();">New Agent</a>
					</span>
					</TD>
				</tr>
				<?php
					}
				?>
			</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
					</TD>
					<TD valign="top">
		<!--<fieldset><legend class="listing-item">Brand</legend>-->
		<?php			
			$entryHead = "Brand";
			require("template/rbTop.php");
		?>
		<table>
			<!--  Dynamic Row Starts Here-->
		<tr id="catRow1">
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblBrand">
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Name</td>		
					<td>&nbsp;</td>
				</tr>				
				</table>
			</td>
		</tr>
		<input type='hidden' name="hidBrandTableRowCount" id="hidBrandTableRowCount" value="">
<!--  Dynamic Row Ends Here-->
<tr id="catRow2"><TD height="5"></TD></tr>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewBrand();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
					</TD>
				</TR>
			</table>
			</TD>
		</TR>
	</table>
			</TD>
		</TR>
	</table>	
	</td>
	</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;" align="center">
		
		</td>
		</tr>
<tr>
<TD colspan="2" style="padding-left:5px;padding-right:5px;" align="center">
	<table><TR><TD valign="top">
		
	</TD>
	<td valign="top">
	
	</td>
	</TR></table>
		</TD></tr>
		<tr>
		<TD colspan="2" style="padding-left:5px;padding-right:5px;" align="center">
		<table><TR><TD>
		<!--<fieldset><legend class="listing-item">Contact</legend>-->
		<?php			
			$entryHead = "Contact";
			require("template/rbTop.php");
		?>
		<table>
			<!--  Dynamic Row Starts Here-->
		<tr><TD height="5"></TD></tr>
		<tr id="catRow1">
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table  cellspacing="1" cellpadding="3" id="tblContact" class="newspaperType">				
				<tr align="center">
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">Person Name</th>
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">Designation</th>
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">Role</th>
					<th style="padding-left:5px;padding-right:5px;" nowrap="true">Contact No</th>	
					<th>&nbsp;</th>
				</tr>				
				</table>
			</td>
		</tr>
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="">
<!--  Dynamic Row Ends Here-->
<tr id="catRow2"><TD height="5"></TD></tr>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New Item</a>
	</TD>
</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
		</TD></TR></table>
		</TD>
	</tr>	

											<tr>
											  <td colspan="2" align="center" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Customer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddCustomer(document.frmCustomer);">												</td>
												<? } else{?>

												<td align="center" colspan="2">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Customer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateAddCustomer(document.frmCustomer);">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
											</tr>
										</table>
									</td>
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
			# Listing Grade Starts
		?>
	</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
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
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$customerRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCustomer.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
									<td colspan="2" style="padding-left:5px; padding-right:5px;" >
						<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($customerRecords) > 0 )
												{
													$i	=	0;
											?>
<thead>
<? if($maxpage>1){?>
<tr>
<td colspan="10" style="padding-right:10px" class="navRow">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   				}
   				else
   				{
					$nav.= " <a href=\"Customer.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Customer.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Customer.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div>
	  </td>
	  </tr>
	  <? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:5px; padding-right:5px;"> Name </th>
		<th style="padding-left:5px; padding-right:5px;">Country</th>
		<th style="padding-left:5px; padding-right:5px;">Contact No</th>		
		<th style="padding-left:5px; padding-right:5px;">Brands</th>
		<th style="padding-left:5px; padding-right:5px;">Preferred <br>Shippling Line</th>
		<th style="padding-left:5px; padding-right:5px;">Agent</th>
		<th style="padding-left:5px; padding-right:5px;">Payment Terms</th> 		
		<? if($edit==true){?>
			<th width="50">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th width="50">&nbsp;</th>
		<? }?>
	</tr>
	</thead>	
	<tbody>
	<?php
	foreach($customerRecords as $cr) {
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
		$active=$cr[5];
		$existingcount=$cr[6];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"  <?php }?>>
		<td width="20" align="center">
			<?php 
			if($existingcount){
			?>
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$customerId;?>" class="chkBox">
			<?php 
			}
			?>
		</td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$customerName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCountryName?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$custContactNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
			<?php
			$numLine = 3;
			if (sizeof($custBrandRecs)>0) {
				$nextRec = 0;						
				foreach ($custBrandRecs as $cR) {					
					$brdName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $brdName;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>			
		</td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
		<?php
			$numLine = 3;
			if (sizeof($custShippingRecs)>0) {
				$nextRec = 0;						
				foreach ($custShippingRecs as $cR) {
					$shipName = $cR[1];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $shipName;
					if($nextRec%$numLine == 0) echo "<br/>";
				 }
			}
		?>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
	<?php
		$numLine = 3;
		if (sizeof($agentRecs)>0) {
			$nextRec = 0;						
			foreach ($agentRecs as $cR) {
				$agentName = $cR[1];
				$nextRec++;
				if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $agentName;
				if($nextRec%$numLine==0)  echo "<br/>";
			}
		}
	?>
	</td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
	<?php
		$numLine = 3;
		if (sizeof($paymtTermRecs)>0) {
			$nextRec = 0;						
			foreach ($paymtTermRecs as $cR) {
				$termName = $cR[1];
				$nextRec++;
				if ($nextRec>1) echo "&nbsp;,&nbsp;"; echo $termName;
				if ($nextRec%$numLine == 0) echo "<br/>";
			}
		}
	?>
	</td>
	<?php
		 if($edit==true){
	?>
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;">
		 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$customerId;?>,'editId');"><? } ?>
		</td>
	<?php
		 }
	?>


	<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$customerId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$customerId;?>,'confirmId');" >
			<?php 
			//} 
			}?>
			<? }?>
			
			
			
			</td>
		</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
<tr>
<td colspan="10" style="padding-right:10px" class="navRow">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   				}
   				else
   				{
					$nav.= " <a href=\"Customer.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Customer.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"Customer.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div>
	  </td>
	  </tr>
	  <? }?>
		<?
		} else {
		?>
		<tr>
			<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$customerRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCustomer.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10">
				<input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>" readonly="true">
				<input type="hidden" name="returnUrl" id="returnUrl" value="<?=$returnUrl?>" readonly="true" />
			</td>
		</tr>	
	</table>
	<?php 
		if ($addMode || $editMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewItem()
		{
			addNewRow('tblContact', '', '', '', '','');	
		}

		function addNewBrand()
		{
			addNewBrandRow('tblBrand', '', '', '', '','');	
		}
		
		function addNewItems()
		{
			addNewItem();
			addNewBrand();
		}
	</SCRIPT>
	<?php 
		} 
	?>

	<?php		
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewItems();
	</SCRIPT>
	<?php 
		}
	?>
	<!-- Edit Record -->
	<script language="JavaScript" type="text/javascript">	
		// Get state
	<?php
		if (sizeof($customerContactRecs)>0) {
			$j=0;
			foreach ($customerContactRecs as $ver) {			
				$agentContactId 	= $ver[0];
				$personName	= rawurlencode(stripSlash($ver[1]));
				$designation	= rawurlencode(stripSlash($ver[2]));
				$role		= rawurlencode(stripSlash($ver[3]));
				$contactNo	= rawurlencode(stripSlash($ver[4]));		
	?>	
		addNewRow('tblContact','<?=$agentContactId?>', '<?=$personName?>', '<?=$designation?>', '<?=$role?>', '<?=$contactNo?>');		
	<?
			$j++;
			}
		} else if ($editMode) {
	?>
		addNewItem();
	<?
		 }
	?>
	
	<?php
		if (sizeof($customerBrandRecs)>0) {
			$j=0;
			foreach ($customerBrandRecs as $ver) {			
				$custBrandEId 	= $ver[0];
				$selBrandName	= rawurlencode(stripSlash($ver[1]));
	?>	
		addNewBrandRow('tblBrand','<?=$custBrandEId?>', '<?=$selBrandName?>');		
	<?
			$j++;
			}
		} else if ($editMode) {
	?>
		addNewBrand();
	<?
		 }
	?>
	</script>
	<? if ($recUpdated && $popupWindow!="") {?>
		<script language="JavaScript" type="text/javascript">
		closeWindow();
		function closeWindow()
		{
			//var myParentWindow = opener.document.forms.frmPurchaseOrder;
			//myParentWindow.test();
			window.opener.reLoadCustomer();
			//alert (myParentWindow);
			close();
		}
		</script>
	<? }?>

	<?php	
	/**
	* Agent Master
	* AgentMaster.js
	*/
	if ($recUpdated && $returnUrl=='AGENTM') {
	?>
		<script language="JavaScript" type="text/javascript">
			parent.reloadCustomer();
			parent.closeLightBox();	
		</script>	
	<?php
		}
	?>

	</form>
<?
	# Include Template [bottomRightNav.php]
	if (!$hideNav) require("template/bottomRightNav.php");
?>