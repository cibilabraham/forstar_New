<?php
	require("include/include.php");
	require_once("lib/AgentMaster_ajax.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated 	= false;
	
		$editFlag=0;
			//echo "EditFlag is $editFlag";	
	$selection 	= "?pageNo=".$p["pageNo"];	

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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

	#For Refreshing the main Window when click PopUp window
	# --------------------- redirect from other page --------
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];

	if ($g["returnUrl"]=="") $returnUrl = $p["returnUrl"];
	else $returnUrl = $g["returnUrl"];
	
	$hideNav = false;
	if ($popupWindow || $returnUrl)  $hideNav = true;
	# -------------------------------------------------------

	# Add Category Start 
	if ($p["cmdAddNew"]!=""){ $addMode = true; $editMode=false;}	
	if ($p["cmdCancel"]!="") $addMode = false;
//echo "$editMode---I";
	#Add a Record
	if ($p["cmdAdd"]!="") {

		$companyName		= addSlash(trim($p["companyName"]));	
		$officeAddress		= addSlash(trim($p["officeAddress"]));
		$selCity		= $p["selCity"];
		$state			= $p["state"];
		$telephoneNo		= addSlash(trim($p["telephoneNo"]));
		$faxNo			= addSlash(trim($p["faxNo"]));
		$telephoneNos		= addSlash(trim($p["telephoneNos"]));
		$faxNos			= addSlash(trim($p["faxNos"]));

		$selCustomers		= explode(",",$p["selCustomerId"]);

		$email			= addSlash(trim($p["email"]));
		$website		= addSlash(trim($p["website"]));

		$tableRowCount		= $p["hidTableRowCount"];

		if ($companyName!="") {						
			$shippingCompanyRecIns = $agentMasterObj->addShippingCompany($companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $userId, $email, $website);

			#Find the Last inserted Id From m_distributor Table
			$lastId = $databaseConnect->getLastInsertedId();

			# Add Agent to Customer
			if (sizeof($selCustomers)>0 && $shippingCompanyRecIns!="") {
				$addAgent2Customer = $agentMasterObj->addAgent2Customer($lastId, $selCustomers);
			}
		
			if ($tableRowCount>0 && $shippingCompanyRecIns!="") {
				for ($i=0; $i<$tableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$personName	= addSlash(trim($p["personName_".$i]));
						$designation	= addSlash(trim($p["designation_".$i]));
						$role		= addSlash(trim($p["role_".$i]));
						$contactNo	= addSlash(trim($p["contactNo_".$i]));

						# IF SELECT ALL STATE
						if ($lastId!="" && $personName!="") {
							$companyContactIns = $agentMasterObj->addCompanyContact($lastId, $personName, $designation, $role, $contactNo);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here			
			if ($shippingCompanyRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddAgentMaster);
				if (!$hideNav) $sessObj->createSession("nextPage",$url_afterAddAgentMaster.$selection);
				else $recUpdated = true;
			} else {
				$addMode = true;
				$err	 = $msg_failAddAgentMaster;
			}
			$shippingCompanyRecIns = false;
		} else {
			//$addMode = true;
			
$addMode =false;
			if ($entryExist) $err = $msg_failAddAgentMaster."<br>".$msgFailAddAgentExistRec;
			else $err = $msg_failAddAgentMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$agentId		= $p["hidAgentId"];
		
		$companyName		= addSlash(trim($p["companyName"]));	
		$officeAddress		= addSlash(trim($p["officeAddress"]));
		$selCity		= $p["selCity"];
		$state			= $p["state"];
		$telephoneNo		= addSlash(trim($p["telephoneNo"]));
		$faxNo			= addSlash(trim($p["faxNo"]));
		$telephoneNos		= addSlash(trim($p["telephoneNos"]));
		$faxNos			= addSlash(trim($p["faxNos"]));

		$selCustomers		= explode(",",$p["selCustomerId"]);
		$email			= addSlash(trim($p["email"]));
		$website		= addSlash(trim($p["website"]));


		$tableRowCount		= $p["hidTableRowCount"];
	
		if ($agentId!="" && $companyName!="" ) {

			# Update Main Table			
			$shippingCompanyRecUptd = $agentMasterObj->updateShippingCompany($agentId, $companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $email, $website);
			
			# Delete Existing Customer
			$delCustomer = $agentMasterObj->deleteAgent2Customer($agentId);
			# Add Agent to Customer
			if ($agentId!="" && $delCustomer) {
				$addAgent2Customer = $agentMasterObj->addAgent2Customer($agentId, $selCustomers);
			}

			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$agentContactId  = $p["shipCompanyContactId_".$i];
				if ($status!='N') {
					$personName	= addSlash(trim($p["personName_".$i]));
					$designation	= addSlash(trim($p["designation_".$i]));
					$role		= addSlash(trim($p["role_".$i]));
					$contactNo	= addSlash(trim($p["contactNo_".$i]));
					
					if ($agentId!="" && $personName!="" && $agentContactId!="") {
						$updateShippingCompanyContactRec = $agentMasterObj->updateShipCompanyContact($agentContactId, $personName, $designation, $role, $contactNo);
					} else if ($agentId!="" && $personName!="" && $agentContactId=="") {				
						$companyContactIns = $agentMasterObj->addCompanyContact($agentId, $personName, $designation, $role, $contactNo);
					}
				} // Status Checking End

				if ($status=='N' && $agentContactId!="") {
					$delShipCompanyContactRec = $agentMasterObj->delShipCompanyContactRec($agentContactId);
				}
			} // Loop ends here

	
		}
	
		if ($shippingCompanyRecUptd || $shippingCompanyRecIns) {
			$sessObj->createSession("displayMsg",$msg_succAgentMasterUpdate);
			//if (!$hideNav) $sessObj->createSession("nextPage",$url_afterUpdateAgentMaster.$selection);
			if (!$hideNav) $sessObj->createSession("nextPage",$url_afterUpdateAgentMaster.$selection);
			else  $recUpdated = true;
			$editMode = false;
			
			$editFlag=1;
			$editId=null;		
		} else {
			$editMode	=	true;
			//$err		=	$msg_failAgentMasterUpdate;
			if ($entryExist) $err = $msg_failAgentMasterUpdate."<br>".$msgFailAddAgentExistRec;
			else $err = $msg_failAgentMasterUpdate;
		}
		$shippingCompanyRecUptd	=	false;
	}
//echo "888$editMode---II";


	# Edit  a Record
	//if (($p["editId"]!="") && ($p["cmdCancel"]=="") && ($editMode==true)      ) {
	
if (($p["editId"]!="") && ($p["cmdCancel"]=="") && (!$addMode) &&($editFlag==0) ) {
		$editId		= $p["editId"];
		$editMode	= true;
		$agentRec	= $agentMasterObj->find($editId);
		$editAgentId  = $agentRec[0];
		$companyName	= $agentRec[1];		
		$officeAddress	= stripSlash($agentRec[2]);
		$selCity	= stripSlash($agentRec[3]);
		$state		= stripSlash($agentRec[4]);
		$telephoneNo		= stripSlash($agentRec[5]);
		$faxNo			= stripSlash($agentRec[6]);
		$telephoneNos		= stripSlash($agentRec[7]);
		$faxNos			= stripSlash($agentRec[8]);
		$email			= stripSlash($agentRec[9]);
		$website		= stripSlash($agentRec[10]);

		# Entry Records
		$companyContactRecs = $agentMasterObj->getCompanyContactRecs($editAgentId);
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$agentId	= $p["delId_".$i];
						
			if ($agentId!="") {

				//$stateEntryExist = $agentMasterObj->stateEntryExist($agentId); && !$stateEntryExist

				// Need to check the selected Category is link with any other process
				# Delete Existing Customer
				$delCustomer = $agentMasterObj->deleteAgent2Customer($agentId);
				# Delete From Entry Table
				$contactEntryRecDel = $agentMasterObj->deleteShippingCompanyContactRec($agentId);
				# Delete From Main Table
				$shippingCompanyRecDel = $agentMasterObj->deleteShippingCompanyRec($agentId);
			}
		}
		if ($shippingCompanyRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelAgentMaster);
			$sessObj->createSession("nextPage",$url_afterDelAgentMaster.$selection);
		} else {
			$errDel	=	$msg_failDelAgentMaster;
		}
		$shippingCompanyRecDel	= false;
	}	
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$agentId	=	$p["confirmId"];
			if ($agentId!="") {
				// Checking the selected fish is link with any other process
				$agentRecConfirm = $agentMasterObj->updateAgentconfirm($agentId);
			}

		}
		if ($agentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmagent);
			$sessObj->createSession("nextPage",$url_afterDelAgentMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmAgent;
		}
		}


		if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$agentId = $p["confirmId"];
			if ($agentId!="") {
				#Check any entries exist
				
					$agentRecConfirm = $agentMasterObj->updateAgentReleaseconfirm($agentId);
				
			}
		}
		if ($agentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmagent);
			$sessObj->createSession("nextPage",$url_afterDelAgentMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmAgent;
		}
		}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	
		
	
	# List all Recs
	$agentRecs = $agentMasterObj->fetchAllPagingRecords($offset, $limit);
	$agentRecordSize = sizeof($agentRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllRecs = $agentMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	if ($addMode || $editMode ) {
		#List all City
		//$cityResultSetObj = $cityMasterObj->fetchAllRecords('');
		$cityResultSetObj = $cityMasterObj->fetchAllRecordsCityActive('');

		# Get All Customers
		$fetchCustomerRecs 	= $agentMasterObj->fetchAllCustomerRecs();
		$selCustomerRecs 	= array();		
		if ($editMode) {
			$selCustomerRecs = $agentMasterObj->getSelCustomerRecs($editId);
		}
		$customerRecords = ary_diff($fetchCustomerRecs, $selCustomerRecs);
	}


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editAgentMaster;
	else	       $heading	=	$label_addAgentMaster;

		
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/AgentMaster.js"; 
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
	<form name="frmAgentMaster" action="AgentMaster.php" method="post">
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
								$bxHeader="Agent Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Agent Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">	
	</td>
	</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="90%" align="center">
	<? //echo $addMode."-".$editMode."-----".$editFlag;
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
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
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('AgentMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateShippingCompanyMaster(document.frmAgentMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AgentMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateShippingCompanyMaster(document.frmAgentMaster);">												</td>

												<?}?>
											</tr>
					<input type="hidden" name="hidAgentId" id="hidAgentId" value="<?=$editAgentId;?>">
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>	
	<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
		<table width="200" cellpadding="4" cellspacing="0">
		<tr><TD colspan="3">
		<table>
		<tr>
	  		<td class="fieldName" nowrap >*Company Name</td>
			<td>
				<input type="text" name="companyName" id="companyName" value="<?=$companyName?>" size="32" onchange="xajax_chkAgentNameExist(document.getElementById('companyName').value, '<?=$editAgentId?>', '<?=$mode?>');" />	
				<span id="divNameExistMsg" class="err1" style="font-size:11px;line-height:normal;"></span>
			</td>
		</tr>
		</table>
		</TD></tr>						
		
		<tr><TD colspan="2" valign="top">
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
					<td nowrap class="fieldName" >*City</td>
					<td nowrap>
						<select name="selCity" id="selCity" onchange="xajax_filterAgentState(document.getElementById('selCity').value,'');">
						<option value="">-- Select --</option>
						<?php
						while ($cr=$cityResultSetObj->getRow()) {
							$cityId = $cr[0];
							$cityCode	= stripSlash($cr[1]);
							$cityName	= stripSlash($cr[2]);	
							$stateId	= $cr[3];
							$stateName	= $cr[4];
							$selected = "";
							if ($selCity==$cityId) $selected = "Selected";			
						?>
						<option value="<?=$cityId?>" <?=$selected?>><?=$cityName?></option>
						<? }?>
						</select>
					</td>
		</tr>
		<tr>
					<td nowrap class="fieldName" >*State</td>
					<td nowrap>
						<select name="state" id="state">
						<option value="">-- Select --</option>
						<?php
						/*
						while ($sr=$stateResultSetObj->getRow()) {
							$stateId = $sr[0];
							$stateCode	= stripSlash($sr[1]);
							$stateName	= stripSlash($sr[2]);	
							$selected = "";
							if ($state==$stateId) $selected = "Selected";	
						*/		
						?>
						<!--<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>-->
						<? //}?>
						</select>
					</td>
		</tr>
					</table>
					</TD>
					<td>&nbsp;</td>
					<td valign="top">
						<table>
							<tr>
								<td class="fieldName" nowrap >Telephone No.</td>
								<td>
									<input type="text" name="telephoneNo" id="telephoneNo" value="<?=$telephoneNo?>" />	
								</td>
							</tr>
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
		</TD>
		<td valign="top">
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
				<TR><TD colspan="2">
				<table border="0">
				<tr>
					<td class="fieldName">*Customer</td>
					<td>
					<table>
						<TR>
							<TD>
				<select name="selAllCustomer[]" size="7" multiple id="selAllCustomer">
                                <option value="" >Select</option>
                                <?php
				if (sizeof($customerRecords)> 0) {
					foreach ($customerRecords as $gl) {
						$id		= $gl[0];
						$displayCustName	= $gl[2];
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
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selAllCustomer'), document.getElementById('selCustomer'));" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selAllCustomer'), document.getElementById('selCustomer'));" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selAllCustomer'), document.getElementById('selCustomer'));" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selAllCustomer'), document.getElementById('selCustomer'));" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<select name="selCustomer[]" size="7" multiple id="selCustomer">
                                	<option value="" >Active Customer</option>
					<?php
					$sCustom = array();
					$sr = 0;
					foreach ($selCustomerRecs as $gl) {
						$selCustId = $gl[0];
						$selCustName = $gl[2];
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
				<input type="hidden" name="selCustomerId" id="selCustomerId" value="<?=implode(",",$sCustom);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
			<?php
			if (!$hideNav) {
			?>
			<tr>
				<TD nowrap="true" style="padding-left:10px;" colspan="2">
				<span>
					<a href="###" class="link1" title="Click to add New Customer." onClick="return loadCustomer();" onMouseover="ShowTip('Click to add New Customer.');" onMouseout="UnTip();">New Customer</a>
				</span>
				</TD>
			</tr>
			<?php
				}
			?>
			</table>
				</TD></TR>
			</table>
		</td>
		</tr>		
               </table>
		</td>
		</tr>	
		<tr><TD colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
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
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
		</TD></TR></table>
		</TD></tr>			
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AgentMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateShippingCompanyMaster(document.frmAgentMaster);">				
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AgentMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateShippingCompanyMaster(document.frmAgentMaster);">						
		</td>
		<input type="hidden" name="cmdAddNew" value="1">
		<?}?>
	</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$agentRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAgentMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
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
	<td colspan="2" style="padding-left:10px;pading-right:10px;">
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($agentRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AgentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AgentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AgentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr align="center" >
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>		
		<th style="padding-left:10px; padding-right:10px;">City</th>			
		<th style="padding-left:10px; padding-right:10px;">State</th>
		<th style="padding-left:10px; padding-right:10px;">Customers</th>
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th>&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
			<?php			
			foreach ($agentRecs as $svr) {
				$i++;
				$agentId 	= $svr[0];	
				$cntryName		= $svr[1];	
				$selCityName		= $svr[2];
				$selStateName		= $svr[3];	
				$custRecs 		= $agentMasterObj->getSelCustomerRecs($agentId);
				$active=$svr[4];
				$existingrecords=$svr[5];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
			<?php 
			if($existingrecords==0){?>
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$agentId;?>" class="chkBox">			
			<?php 
			}?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$cntryName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?=$selCityName?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?=$selStateName?>	
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left">
			<?php
			$numLine = 3;
			if (sizeof($custRecs)>0) {
				$nextRec = 0;						
				foreach ($custRecs as $cR) {
					$prtName = $cR[2];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $prtName;
					if($nextRec%$numLine == 0) echo "<br/>";
				}
			}
			?>
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$agentId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='AgentMaster.php';" >
		<? } ?>
		</td>
<? }?>

<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$agentId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$agentId;?>,'confirmId');" >
			<?php 
			//} 
			}?>
			<? }?>
			
			
			
			</td>
			
		</tr>
		<?			
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AgentMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AgentMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AgentMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												}
												else
												{
											?>
											<tr>
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$agentRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button" ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAgentMaster.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
<input type="hidden" name="hidStateFilterId" value="<?=$stateFilterId?>">	
<input type="hidden" name="hidStateVatRateListFilterId" value="<?=$stateVatRateListFilterId?>">	
<input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>" readonly="true" /> 
<input type="hidden" name="returnUrl" id="returnUrl" value="<?=$returnUrl?>" readonly="true" />
		<tr>
			<td height="10"></td>
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
	</SCRIPT>
	<?php 
		} 
	?>

	<?php
		if ($addMode || (!sizeof($companyContactRecs) && $editMode)) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewItem();
	</SCRIPT>
	<?php 
		}
	?>
	<!-- Edit Record -->
	<script language="JavaScript" type="text/javascript">	
		// Get state		
	<?php
		if ($addMode || $editMode) {
	?>
		xajax_filterAgentState('<?=$selCity?>','<?=$state?>');
	<?php
		}
	?>

	<?php
		if (sizeof($companyContactRecs)>0) {
			$j=0;
			foreach ($companyContactRecs as $ver) {			
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
		}
	?>
	</script>	

	<?php if ($recUpdated && $popupWindow!="") {?>
		<script language="JavaScript" type="text/javascript">
		closeWindow();
		function closeWindow()
		{
			//var myParentWindow = opener.document.forms.frmPurchaseOrder;
			//myParentWindow.test();
			window.opener.reLoadAgent();
			//alert (myParentWindow);
			close();
		}
		</script>
	<?php }?>

<!-- Customer Master -->
	<?php	
	// customer.js
	if ($recUpdated && $returnUrl=='CUSTM') {
	?>
		<script language="JavaScript" type="text/javascript">
			parent.reloadAgent();
			parent.closeLightBox();	
		</script>	
	<?php
		}
	?>
	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if (!$hideNav) require("template/bottomRightNav.php");
?>