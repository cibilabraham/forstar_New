<?php
	require("include/include.php");
	require_once("libjs/xajax_core/xajax.inc.php");
	$xajax = new xajax();
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$recUpdated 	= false;
		
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

	# From customer Master
	if ($g["returnUrl"]=="") $returnUrl = $p["returnUrl"];
	else $returnUrl = $g["returnUrl"];
	
	$hideNav = false;
	if ($returnUrl)  $hideNav = true;
	# --------------------------------------------------------

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}

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

		$tableRowCount		= $p["hidTableRowCount"];

		if ($companyName!="") {						
			$shippingCompanyRecIns = $shippingCompanyMasterObj->addShippingCompany($companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos, $userId);

			#Find the Last inserted Id From m_distributor Table
			$lastId = $databaseConnect->getLastInsertedId();
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
							$companyContactIns = $shippingCompanyMasterObj->addCompanyContact($lastId, $personName, $designation, $role, $contactNo);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here			
			if ($shippingCompanyRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddShippingCompanyMaster);
				if (!$hideNav) $sessObj->createSession("nextPage",$url_afterAddShippingCompanyMaster.$selection);
				else $recUpdated = true;
			} else {
				$addMode = true;
				$err	 = $msg_failAddShippingCompanyMaster;
			}
			$shippingCompanyRecIns = false;
		} else {
			$addMode = true;
			if ($entryExist) $err = $msg_failAddShippingCompanyMaster."<br>".$msgFailAddShippingCompanyExistRec;
			else $err = $msg_failAddShippingCompanyMaster;
		}
	}

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		$shippingCompanyId		= $p["hidShipCompanyId"];
		
		$companyName		= addSlash(trim($p["companyName"]));	
		$officeAddress		= addSlash(trim($p["officeAddress"]));
		$selCity		= $p["selCity"];
		$state			= $p["state"];
		$telephoneNo		= addSlash(trim($p["telephoneNo"]));
		$faxNo			= addSlash(trim($p["faxNo"]));
		$telephoneNos		= addSlash(trim($p["telephoneNos"]));
		$faxNos			= addSlash(trim($p["faxNos"]));

		$tableRowCount		= $p["hidTableRowCount"];
	
		if ($shippingCompanyId!="" && $companyName!="" ) {

			# Update Main Table			
			$shippingCompanyRecUptd = $shippingCompanyMasterObj->updateShippingCompany($shippingCompanyId, $companyName, $officeAddress, $selCity, $state, $telephoneNo, $faxNo, $telephoneNos, $faxNos);
			
			for ($i=0; $i<$tableRowCount; $i++) {
				$status 	  = $p["status_".$i];
				$shipCompanyContactId  = $p["shipCompanyContactId_".$i];
				if ($status!='N') {
					$personName	= addSlash(trim($p["personName_".$i]));
					$designation	= addSlash(trim($p["designation_".$i]));
					$role		= addSlash(trim($p["role_".$i]));
					$contactNo	= addSlash(trim($p["contactNo_".$i]));
					
					if ($shippingCompanyId!="" && $personName!="" && $shipCompanyContactId!="") {
						$updateShippingCompanyContactRec = $shippingCompanyMasterObj->updateShipCompanyContact($shipCompanyContactId, $personName, $designation, $role, $contactNo);
					} else if ($shippingCompanyId!="" && $personName!="" && $shipCompanyContactId=="") {				
						$companyContactIns = $shippingCompanyMasterObj->addCompanyContact($shippingCompanyId, $personName, $designation, $role, $contactNo);
					}
				} // Status Checking End

				if ($status=='N' && $shipCompanyContactId!="") {
					$delShipCompanyContactRec = $shippingCompanyMasterObj->delShipCompanyContactRec($shipCompanyContactId);
				}
			} // Loop ends here
		}
	
		if ($shippingCompanyRecUptd || $shippingCompanyRecIns) {
			$sessObj->createSession("displayMsg",$msg_succShippingCompanyMasterUpdate);
			if (!$hideNav) $sessObj->createSession("nextPage",$url_afterUpdateShippingCompanyMaster.$selection);
			else $recUpdated = true;		
		} else {
			$editMode	=	true;
			//$err		=	$msg_failShippingCompanyMasterUpdate;
			if ($entryExist) $err = $msg_failShippingCompanyMasterUpdate."<br>".$msgFailAddShippingCompanyExistRec;
			else $err = $msg_failShippingCompanyMasterUpdate;
		}
		$shippingCompanyRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$shipCompanyRec	= $shippingCompanyMasterObj->find($editId);
		$editShipCompanyId  = $shipCompanyRec[0];
		$companyName	= $shipCompanyRec[1];		
		$officeAddress	= stripSlash($shipCompanyRec[2]);
		$selCity	= stripSlash($shipCompanyRec[3]);
		$state		= stripSlash($shipCompanyRec[4]);
		$telephoneNo		= stripSlash($shipCompanyRec[5]);
		$faxNo			= stripSlash($shipCompanyRec[6]);
		$telephoneNos		= stripSlash($shipCompanyRec[7]);
		$faxNos			= stripSlash($shipCompanyRec[8]);
	
		# Entry Records
		$companyContactRecs = $shippingCompanyMasterObj->getCompanyContactRecs($editShipCompanyId);
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$shippingCompanyId	= $p["delId_".$i];
						
			if ($shippingCompanyId!="") {

				//$stateEntryExist = $shippingCompanyMasterObj->stateEntryExist($shippingCompanyId); && !$stateEntryExist

				// Need to check the selected Category is link with any other process
				# Delete From Entry Table
				$contactEntryRecDel = $shippingCompanyMasterObj->deleteShippingCompanyContactRec($shippingCompanyId);
				# Delete From Main Table
				$shippingCompanyRecDel = $shippingCompanyMasterObj->deleteShippingCompanyRec($shippingCompanyId);
			}
		}
		if ($shippingCompanyRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelShippingCompanyMaster);
			if (!$hideNav) $sessObj->createSession("nextPage",$url_afterDelShippingCompanyMaster.$selection);
			else $recUpdated = true;
		} else {
			$errDel	=	$msg_failDelShippingCompanyMaster;
		}
		$shippingCompanyRecDel	= false;
	}	



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$shipCompanyContactId	=	$p["confirmId"];
			if ($shipCompanyContactId!="") {
				// Checking the selected fish is link with any other process
				$ShippingCompanyRecConfirm = $shippingCompanyMasterObj->updateShippingCompanyconfirm($shipCompanyContactId);
			}

		}
		if ($ShippingCompanyRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmShippingCompany);
			$sessObj->createSession("nextPage",$url_afterDelShippingCompanyMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$shipCompanyContactId = $p["confirmId"];
			if ($shipCompanyContactId!="") {
				#Check any entries exist
				
					$ShippingCompanyRecConfirm = $shippingCompanyMasterObj->updateShippingCompanyReleaseconfirm($shipCompanyContactId);
				
			}
		}
		if ($ShippingCompanyRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmShippingCompany);
			$sessObj->createSession("nextPage",$url_afterDelShippingCompanyMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	
		
	
	# List all Recs
	$shippingCompanyRecs = $shippingCompanyMasterObj->fetchAllPagingRecords($offset, $limit);
	$shippingCompanyRecordSize = sizeof($shippingCompanyRecs);

	## -------------- Pagination Settings II -------------------
	$fetchAllRecs = $shippingCompanyMasterObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	if ($addMode || $editMode ) {
		#List all City
		$cityResultSetObj = $cityMasterObj->fetchAllRecordsCityActive('');

		# List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();

	}


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editShippingCompanyMaster;
	else	       $heading	=	$label_addShippingCompanyMaster;

		
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ShippingCompanyMaster.js"; 

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav

	# Xajax Settings	
 	$xajax->register(XAJAX_FUNCTION, 'chkShipNameExist', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
	$xajax->register(XAJAX_FUNCTION, 'shipCompanyState', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

 	$xajax->ProcessRequest();

	# Include Template [topLeftNav.php]
	if (!$hideNav) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmShippingCompanyMaster" action="ShippingCompanyMaster.php" method="post">
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
								$bxHeader="Shipping Company Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Shipping Company Master</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap">	
	</td>
	</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="90%" align="center">
	<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ShippingCompanyMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateShippingCompanyMaster(document.frmShippingCompanyMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ShippingCompanyMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateShippingCompanyMaster(document.frmShippingCompanyMaster);">	
											</td>
										<?}?>
										</tr>
	<input type="hidden" name="hidShipCompanyId" value="<?=$editShipCompanyId;?>">
	<tr><TD colspan="2" nowrap="true" style="padding-left:5px;padding-right:5px;"><span id="divStateIdExistTxt" class="err1" style="font-size:11px;line-height:normal;"></span></TD></tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
		<table width="200">
		<tr><TD colspan="2">
		<table>
		<tr>
	  		<td class="fieldName" nowrap >*Company Name</td>
			<td>
				<input type="text" name="companyName" id="companyName" value="<?=$companyName?>" size="32" onchange="xajax_chkShipNameExist(document.getElementById('companyName').value, '<?=$editShipCompanyId?>', '<?=$mode?>');" />	
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
					<td nowrap class="fieldName" >*City</td>
					<td nowrap>
						<select name="selCity" id="selCity" onchange="xajax_shipCompanyState(document.getElementById('selCity').value,'');">
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
						<table>
							<tr>
								<td class="fieldName" nowrap >Telephone Nos.</td>
								<td>
									<input type="text" name="telephoneNos" id="telephoneNos" value="<?=$telephoneNos?>" size="32" />	
								</td>
								<td>&nbsp;</td>
								<td class="fieldName" nowrap >Fax Nos.</td>
								<td>
									<input type="text" name="faxNos" id="faxNos" value="<?=$faxNos?>" size="32" />	
								</td>
							</tr>							
						</table>
			
		</TD></tr>
               </table>
		</td>
		</tr>	
		<tr><TD colspan="2" style="padding-left:5px;padding-right:5px;">
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
		</TD></tr>	
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ShippingCompanyMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateShippingCompanyMaster(document.frmShippingCompanyMaster);">				
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ShippingCompanyMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateShippingCompanyMaster(document.frmShippingCompanyMaster);">						
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
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$shippingCompanyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintShippingCompanyMaster.php',700,600);"><? }?></td>
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
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($shippingCompanyRecordSize) {
			$i	=	0;
		?>
	<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ShippingCompanyMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ShippingCompanyMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ShippingCompanyMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th style="padding-left:10px; padding-right:10px;">Name</th>		
		<th style="padding-left:10px; padding-right:10px;">City</th>			
		<th style="padding-left:10px; padding-right:10px;">State</th>
		<? if($edit==true){?>
				<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
			<?php			
			foreach ($shippingCompanyRecs as $svr) {
				$i++;
				$shippingCompanyId 	= $svr[0];	
				$cntryName		= $svr[1];	
				$selCityName		= $svr[2];
				$selStateName		= $svr[3];	
				$active=$svr[4];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$shippingCompanyId;?>" class="chkBox">			
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$cntryName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?=$selCityName?>
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">
			<?=$selStateName?>	
		</td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$shippingCompanyId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ShippingCompanyMaster.php';" >
		<? } ?>
		</td>
<? }?>

 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$shippingCompanyId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$shippingCompanyId;?>,'confirmId');" >
			<?php }?>
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
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ShippingCompanyMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ShippingCompanyMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ShippingCompanyMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete ** " style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$shippingCompanyRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintShippingCompanyMaster.php',700,600);"><? }?></td>
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
		xajax_shipCompanyState('<?=$selCity?>','<?=$state?>');

	<?php
		if (sizeof($companyContactRecs)>0) {
			$j=0;
			foreach ($companyContactRecs as $ver) {			
				$shipCompanyContactId 	= $ver[0];
				$personName	= rawurlencode(stripSlash($ver[1]));
				$designation	= rawurlencode(stripSlash($ver[2]));
				$role		= rawurlencode(stripSlash($ver[3]));
				$contactNo	= rawurlencode(stripSlash($ver[4]));		
	?>	
		addNewRow('tblContact','<?=$shipCompanyContactId?>', '<?=$personName?>', '<?=$designation?>', '<?=$role?>', '<?=$contactNo?>');		
	<?php
			$j++;
			}
		}
	?>
	</script>

	<?php	
	/**
	* Customer Master
	* customer.js
	*/
	if ($recUpdated && $returnUrl=='CUSTM') {
	?>
		<script language="JavaScript" type="text/javascript">
			parent.reloadShippingLine();
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