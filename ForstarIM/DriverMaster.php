<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/


	# Add Registration Type Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Registration Type
	if ($p["cmdAdd"]!="") {

		$name		=	addSlash(trim($p["name"]));
		$permanentAddress		=	addSlash(trim($p["permanentAddress"]));
		$presentAddress		=	addSlash(trim($p["presentAddress"]));
		$telephoneNo		=	addSlash(trim($p["telephoneNo"]));
		$mobileNo		=	addSlash(trim($p["mobileNo"]));
		$drivingLicenceNo	=	addSlash(trim($p["drivingLicenceNo"]));
		$licenceExpiryDate		=	mysqlDateFormat($p["licenceExpiryDate"]);
		//$vehicleType		=	addSlash(trim($p["vehicleType"]));
		$vehicleTypeTableRowCount	= $p["hidVehicleTypeTableRowCount"];
		$lastId = "";
		
		
		if ($name!="" && $drivingLicenceNo!="" & $licenceExpiryDate!="") {
			$driverMasterRecIns	=	$driverMasterObj->addDriverMaster($name, $permanentAddress, $presentAddress,$telephoneNo,$mobileNo,$drivingLicenceNo,$licenceExpiryDate,$vehicleType, $userId);
		
		#Find the Last inserted Id From m_customer Table
			if ($driverMasterRecIns) $lastId = $databaseConnect->getLastInsertedId();
			
		# Multiple Brand Adding
			if ($vehicleTypeTableRowCount>0 ) {
				for ($i=0; $i<$vehicleTypeTableRowCount; $i++) {
					$status = $p["bStatus_".$i];
					
					if ($status!='N') {
						$vehicleType	= addSlash(trim($p["vehicleType_".$i]));
						
						# IF SELECT ALL STATE
						if ($lastId!="" && $vehicleType!="") {
							
							$vehicleTypeIns = $driverMasterObj->addVehicleType($lastId, $vehicleType);
						}  # If 										
					} # Status check ends here
				} # For Loop Ends Here
			} # Table Row Count Ends Here
		
		
			if ($driverMasterRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddDriverMaster);
				$sessObj->createSession("nextPage", $url_afterAddDriverMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDriverMaster;
			}
			$driverMasterRecIns		=	false;
		}
	}
		
	# Edit Registration Type 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$driverMasterRec		=	$driverMasterObj->find($editId);
		$driverMasterId			=	$driverMasterRec[0];
		$name			=	$driverMasterRec[1];
		$permanentAddress			=	stripSlash($driverMasterRec[2]);
		$presentAddress				=	stripSlash($driverMasterRec[3]);
		$telephoneNo	=	stripSlash($driverMasterRec[4]);
		$mobileNo	=	stripSlash($driverMasterRec[5]);
		$drivingLicenceNo	=	stripSlash($driverMasterRec[6]);
		$licenceExpiryDate	=	dateformat($driverMasterRec[7]);
		//$vehicleType	=	stripSlash($driverMasterRec[3]);
		$vehicleTypeRecs		= $driverMasterObj->getVehicleType($driverMasterId);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$driverMasterId		=	$p["hidDriverMasterId"];
		$name		=	addSlash(trim($p["name"]));
		$permanentAddress	=	addSlash(trim($p["permanentAddress"]));
		$presentAddress		=	addSlash(trim($p["presentAddress"]));
		$telephoneNo		=	addSlash(trim($p["telephoneNo"]));
		$mobileNo		=	addSlash(trim($p["mobileNo"]));
		$drivingLicenceNo		=	addSlash(trim($p["drivingLicenceNo"]));
		$licenceExpiryDate		=	mysqlDateFormat(trim($p["licenceExpiryDate"]));
		//$vehicleType		=	addSlash(trim($p["vehicleType"]));
		$vehicleTypeTableRowCount	= $p["hidVehicleTypeTableRowCount"];
		if ($driverMasterId!="" && $name!="") {
			$driverMasterRecUptd = $driverMasterObj->updateDriverMaster($driverMasterId, $name, $permanentAddress, $presentAddress,$telephoneNo,$mobileNo,$drivingLicenceNo,$licenceExpiryDate);
			
			# ----------------------------Vehicle master
			for ($i=0; $i<$vehicleTypeTableRowCount; $i++) {
				$status 	 	 = $p["bStatus_".$i];
				$vehicleTypeId  		= $p["vehicleTypeId_".$i];
				if ($status!='N') {
					$vehicleType= addSlash(trim($p["vehicleType_".$i]));
					
					if ($driverMasterId!="" && $vehicleType!="" && $vehicleTypeId!="") {
					//echo 'hi';
						$updatevehicletypeRec = $driverMasterObj->updateVehicleType($vehicleTypeId, $vehicleType);
						
					} else if ($driverMasterId!="" && $vehicleType!="" && $vehicleTypeId=="") {	
						//echo 'test';
						$vehicleTypeIns = $driverMasterObj->addVehicleType($driverMasterId, $vehicleType);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $vehicleTypeId!="") {
					# Check Test master In use
					/*$driverMasterInUse = $driverMasterObj->testMethodRecInUse($vehicleTypeId);
					if (!$driverMasterInUse)*/ $delDriverMasterRec = $driverMasterObj->delVehicleTypeRec($vehicleTypeId);
						
				}
			} // Brand Loop ends here
		}
	
		if ($driverMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDriverMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDriverMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failDriverMasterUpdate;
		}
		$driverMasterRecUptd	=	false;
	}


	# Delete Registration Type
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$drivermasterId	=	$p["delId_".$i];

			if ($drivermasterId!="") {
				// Need to check the selected Department is link with any other process
				$driverCheckDel     =   $driverMasterObj->checkDriverName($drivermasterId);
				if(sizeOf($driverCheckDel)>0)
				{
					echo  "<script>alert('Failed to delete the selected record as it is already in use.');</script>";
				}
				else
				{				
					$driverMasterRecDel	=	$driverMasterObj->deleteDriverMaster($drivermasterId);
					$delVehicleType	 = $driverMasterObj->deleteVehicletypeRecs($drivermasterId);
				}			
			}
		}
		if ($driverMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDriverMaster);
			$sessObj->createSession("nextPage",$url_afterDelDriverMaster.$selection);
		} else {
			$errDel	=	$msg_failDelDriverMaster;
		}
		$driverMasterRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$drivermasterId	=	$p["confirmId"];
			if ($drivermasterId!="") {
				// Checking the selected fish is link with any other process
				$driverMasterRecConfirm = $driverMasterObj->updateDrivermasterconfirm($drivermasterId);
			}

		}
		if ($driverMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmDriverMaster);
			$sessObj->createSession("nextPage",$url_afterDelDriverMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$drivermasterId = $p["confirmId"];
			if ($drivermasterId!="") {
				#Check any entries exist
				
					$driverMasterRecConfirm = $driverMasterObj->updateDriverMasterReleaseconfirm($drivermasterId);
				
			}
		}
		if ($driverMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmDriverMaster);
			$sessObj->createSession("nextPage",$url_afterDelDriverMaster.$selection);
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

	# List all Registration type ;
	$driverMasterRecords	=	$driverMasterObj->fetchAllPagingRecords($offset, $limit);
	$driverMasterSize		=	sizeof($driverMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($driverMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editDriverMaster;
	else 		$heading = $label_addDriverMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/DriverMaster.js";
	
	$declarVehicleTypeRecords = $driverMasterObj->fetchAlldeclarVehicleType();
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
				
	?>	
	<form name="frmDriverMaster" action="DriverMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
	<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Manage Driver Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DriverMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDriverMaster(document.frmDriverMaster);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DriverMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddDriverMaster(document.frmDriverMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidDriverMasterId" value="<?=$driverMasterId;?>">
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Name of Person</td>
												<td><INPUT TYPE="text" NAME="name" size="15" value="<?=$name;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Permanent Address</td>
												<td ><textarea name="permanentAddress"><?=$permanentAddress;?></textarea></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*Present address</td>
													<td ><textarea name="presentAddress"><?=$presentAddress;?></textarea></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Telephone no</td>
												<td><INPUT TYPE="text" NAME="telephoneNo" size="15" value="<?=$telephoneNo;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >Mobile no</td>
												<td><INPUT TYPE="text" NAME="mobileNo" size="15" value="<?=$mobileNo;?>"></td>
											</tr>
											
											<tr>
												<td class="fieldName" nowrap >Vehicle Type</td>
											<td>
											<table>
												<!--  Dynamic Row Starts Here-->
											<tr id="catRow1">
												<td colspan="2" style="padding-left:5px;padding-right:5px;">
													<table  id="tblvehicleType">
													<tr bgcolor="#f2f2f2" align="center">
																
														
													</tr>				
													</table>
												</td>
											</tr>
											<input type='hidden' name="hidVehicleTypeTableRowCount" id="hidVehicleTypeTableRowCount" value="">
																				<!--  Dynamic Row Ends Here-->
									<tr id="catRow2"><TD height="5"></TD></tr>
									<tr id="catRow3">
										<TD style="padding-left:5px;padding-right:5px;">
											<a href="###" id='addRow' onclick="javascript:addNewVehicleType();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
										</TD>
									</tr>
										
										</table>
											
											
											
											<!--<table>
											<tr id="catRow1">
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table   id="tblvehicleType">
								
				</table>
			</td>
		</tr>
			<!--  Dynamic Row Starts Here
		<tr id="catRow1">
			<td colspan="2" style="padding-left:5px;padding-right:5px;">
				<table  id="tblvehicleType">
				<tr bgcolor="#f2f2f2" align="center">
							
					<td>&nbsp;</td>
				</tr>				
				</table>
			</td>
		</tr>
		<input type='hidden' name="hidVehicleTypeTableRowCount" id="hidVehicleTypeTableRowCount" value="">
<!--  Dynamic Row Ends Here
<tr id="catRow2"><TD height="5"></TD></tr>
<tr id="catRow3">
	<TD style="padding-left:5px;padding-right:5px;">
		<a href="###" id='addRow' onclick="javascript:addNewVehicleType();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
	</TD>
</tr>
		</table>-->
								</td>			
								</tr>
								<tr>
												<td class="fieldName" nowrap >*Driving Licence no</td>
												<td><INPUT TYPE="text" NAME="drivingLicenceNo" size="15" value="<?=$drivingLicenceNo;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Licence Expiry Date</td>
											<td><input type="text" name="licenceExpiryDate" id="licenceExpiryDate" size="9" value="<?=$licenceExpiryDate;?>" autocomplete="off" /></td>
											</tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DriverMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDriverMaster(document.frmDriverMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DriverMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddDriverMaster(document.frmDriverMaster);">												</td>

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
			
			# Listing Registration Type Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Department </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$driverMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDriverMaster.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if ( sizeof($driverMasterRecords) > 0 ) {
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
      				$nav.= " <a href=\"DriverMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DriverMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DriverMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name of Person</th>
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Permanent Address</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Present address </th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Telephone no </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Mobile no </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Driving Licence no </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Licence Expiry Date </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Vehicle Type </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Current status </th>
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Procurement Number</th>-->
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($driverMasterRecords as $cr) {
		$i++;
		 $driverMasterId		=	$cr[0];
		 $name		=	stripSlash($cr[1]);
		 $permanentAddress	=	stripSlash($cr[2]);
		 $presentAddress		=	stripSlash($cr[3]);
		 $telephoneNo		=	stripSlash($cr[4]);
		 $mobileNo		=	stripSlash($cr[5]);
		 $drivingLicenceNo		=	stripSlash($cr[6]);
		 $licenceExpiryDate		=	stripSlash($cr[7]);
		 $licenceExpiryDateFormat		=	dateFormat(stripSlash($cr[7]));
		 $vehicleType		=	$driverMasterObj->getVehicleType($driverMasterId);
		 $active=$cr[8];
		 $currentstatus = $cr[9];
		$procurementnumber = $cr[10];
		$existingrecords=$cr[11];
		
		$todays_date = date("Y-m-d");
		 $currentDate = strtotime($todays_date);
		 $expiryDate=strtotime($licenceExpiryDate);
		

			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";	
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Permenent Address</td>";
			$displayHtml .= "<td class=listing-head>Present Address</td>";			
			$displayHtml .= "</tr>";
			$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $permanentAddress;
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $presentAddress;
				$displayHtml .=	"</td>";
				$displayHtml .= "</tr>";
				$displayHtml .= "</table>";
			//$display = "onMouseOver=\"ShowTip('$displayHtml');\" onMouseOut=\"UnTip();\" ";
			
		
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$driverMasterId;?>" ></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"  ><a style="text-decoration:underline;" onMouseOver="ShowTip('<?=$displayHtml;?>'); " onMouseOut="UnTip();" ><?=$name;?></a> &nbsp; 
		<?php
		$driverSchedule	=	$driverMasterObj->getschedulehistory($driverMasterId);
		if(sizeof($driverSchedule)>0) { ?>
											<?php
												$detailsvalue='';
												
												if(sizeof($driverSchedule)>0) {
												$detailsvalue='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head colspan=2>SCHEDULE DETAILS </th></tr><tr bgcolor=#D9F3FF ><th  class=listing-head>Procurement ID</th><th  class=listing-head>Schedule date</th></tr>';
												
												 foreach($driverSchedule as $driverHistory )
												 {
												 $procurementId= $driverHistory[0];
													$schedule_date= dateformat($driverHistory[1]);
												
												
												$detailsvalue.='<tr bgcolor=#f2f2f2><td class=listing-item>'.$procurementId.'&nbsp;</td><td class=listing-item>'.$schedule_date.'&nbsp;</td></tr>';
												 } 
												
														
												$detailsvalue.='</table>';
																								?>

													<a onMouseOver="ShowTip('<?=$detailsvalue;?>');" onMouseOut="UnTip();" style="text-decoration:underline;color:red;font-size:10px">
													SCHED</a><?php } ?>
													<?php } ?>
		</td>
		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$telephoneNo?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$mobileNo?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
		<?=$drivingLicenceNo?></td>
		<?php if($expiryDate < $currentDate)
			{  ?>
			<td class="listing-item" style="padding-left:10px; padding-right:10px; background-color:red;"><?=$licenceExpiryDateFormat?></td>	
			<?php 		
				} else {?>
			
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$licenceExpiryDateFormat?></td>
		<? } ?>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 3;
			if (sizeof($vehicleType)>0) {
				$nextRec = 0;						
				foreach ($vehicleType as $cR) {					
					$type = $cR[1];
					$vehicle=$driverMasterObj->getVehicleTypeName($type);
					$vehicleType1=$vehicle[0];
					$nextRec++;
					if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $vehicleType1;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>	
		
		</td>
		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?if ($currentstatus=="0")
		{
		echo "FREE";
		}
		else
		{
		echo "BLOCKED".'-'.$procurementnumber;
		}
		?>
		</td>
	
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$driverMasterId;?>,'editId'); this.form.action='DriverMaster.php';"  ><?php } ?>
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$driverMasterId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$driverMasterId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"DriverMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DriverMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DriverMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$driverMasterSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDriverMaster.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<?php 
		//if ($addMode || $editMode) {
		if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewVehicleType()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addNewRow('tblvehicleType','','');
		}

		function addNewItems()
		{
			addNewVehicleType();
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
		if ($editMode) {
	
		//if (sizeof($declarVehicleTypeRecords)>0) {
		if (sizeof($vehicleTypeRecs)>0){
			$j=0;
			//foreach ($declarVehicleTypeRecords as $ver) {	
				foreach($vehicleTypeRecs as $ver) {	
				$vehicleTypeId 	= $ver[0];
				$vehicleType	= rawurlencode(stripSlash($ver[1]));
						
	?>	
		addNewRow('tblvehicleType','<?=$vehicleTypeId?>', '<?=$vehicleType?>');		
	<?
			$j++;
			}
		} 
	?>
		//addNewVehicleType();
		
		function addNewVehicleType()
		{
			//addNewRow('tblvehicleType', '', '', '', '','');	
			addNewRow('tblvehicleType','','');
		}

		
		
		
		
		
	<?
		 }
	?>
	
	
	</script>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "licenceExpiryDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "licenceExpiryDate", 
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