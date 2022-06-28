<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode	= true;
	
	if ($p["selRole"]!="") 	$selRole	= $p["selRole"];
	if ($p["selUser"])	$selUser	= $p["selUser"];
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
		$selRole = "";
	}
	
		
	# Insert
	if ($p["cmdAdd"]!="") {

		$selRole		= $p["selRole"];
		$dashBoardRowCount	= $p["dashBoardRowCount"];
		$selUser		= $p["selUser"];
		
		if ($selRole!="") {
			for ($i=1; $i<=$dashBoardRowCount; $i++) {
				$selDashBoard 	= $p["selDashBoard_".$i];
				$entryId	= $p["entryId_".$i];
				if ($selDashBoard!="" && $entryId=="") {
					$dashBoardRecIns = $dashboardManagerObj->addDashboardRec($selRole, $selDashBoard, $selUser, $userId);
				} else if ($selDashBoard!="" && $entryId!="") {
					$dashboardRecUptd = $dashboardManagerObj->updateDashboardRec($selRole, $selDashBoard, $entryId, $selUser);
					$dashBoardRecIns = true;
				} else if ($selDashBoard=="" && $entryId!="") {
					$deleteDashboardRec = $dashboardManagerObj->deleteDashboardEntry($entryId);
				}				
			}
			if ($dashBoardRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDashboard);
				$sessObj->createSession("nextPage",$url_afterAddDashboard);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDashboard	;
			}
			$dashBoardRecIns	=	false;			
		}
			
	}
	
	# Edit 
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;		
		$dashboardRec		= $dashboardManagerObj->find($editId);
		$editDashboardRec	= $dashboardRec[0];			
		
		if ($p["editSelectionChange"]=='1' || $p["selRole"]=="") {
			$selRole	= $dashboardRec[1];
		} else {
			$selRole	= $p["selRole"];
		}

		if ($p["editSelectionChange"]=='1' || $p["selUser"]=="") {
			$selUser	= $dashboardRec[2];
		} else {
			$selUser	= $p["selUser"];
		}			
	}
	
	# Update
	if ($p["cmdSaveChange"]!="") {		
		$selRole		= $p["selRole"];
		$dashBoardRowCount	= $p["dashBoardRowCount"];
		$selUser		= $p["selUser"];
		
		if ($selRole!="") {
			for ($i=1; $i<=$dashBoardRowCount; $i++) {
				$selDashBoard	= $p["selDashBoard_".$i];
				$entryId	= $p["entryId_".$i];
				if ($selDashBoard!="" && $entryId=="") {
					$dashBoardRecIns = $dashboardManagerObj->addDashboardRec($selRole, $selDashBoard, $selUser, $userId);
					$dashboardRecUptd = true;
				} else if ($selDashBoard!="" && $entryId!="") {
					$dashboardRecUptd = $dashboardManagerObj->updateDashboardRec($selRole, $selDashBoard, $entryId, $selUser);
				} else if ($selDashBoard=="" && $entryId!="") {
					$deleteDashboardRec = $dashboardManagerObj->deleteDashboardEntry($entryId);
					$dashboardRecUptd = true;
				}
			}			
		}	
		if ($dashboardRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDashboard	);
			$sessObj->createSession("nextPage",$url_afterUpdateDashboard	);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDashboard	;
		}
		$dashboardRecUptd	=	false;
	}
	
	
	# Delete 	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dashboardEntryId	= $p["delId_".$i];
			$selRoleId		= $p["roleId_".$i];
			$selUserId		= $p["hidUserId_".$i];

			if ($dashboardEntryId!="") {				
				$dashboardRecDel = $dashboardManagerObj->deleteDashboardRec($selRoleId, $selUserId);	
			}
		}
		if ($dashboardRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDashboard);
			$sessObj->createSession("nextPage",$url_afterDelDashboard);
		} else {
			$errDel	=	$msg_failDelDashboard	;
		}
		$dashboardRecDel	=	false;
	}
	
	# Update Pending cheque
	if ($p["cmdUpdate"]!="") {
		$pChqDays 		= $p["pChqDays"];
		$crBalDisplayLimit	= $p["crBalDisplayLimit"];
		$overdueDisplayLimit	= $p["overdueDisplayLimit"];

		if ($pChqDays!="" || $crBalDisplayLimit!="" || $overdueDisplayLimit!="") {
			$updatePendingChqDisplayDays = $dashboardManagerObj->updatePendingChqDisplayDays($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit);
			if ($updatePendingChqDisplayDays) {
				$sessObj->createSession("displayMsg",$msgSuccRecUpdate);
				$sessObj->createSession("nextPage",$url_afterUptdPChqDisplayDays);
			} else {
				$err	=	$msgFailRecUpdate;
			}
		}
	}

	list($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit) = $dashboardManagerObj->getPendingChqDisplayDays();
	
	# List All Records
	$dashBoardRecords	= $dashboardManagerObj->fetchAllRecords();
	$dashBoardRecordsSize	= sizeof($dashBoardRecords);

	// $dashboardManagerObj->chkDashboardEnabled($roleId, $type, $selUserId);

	if ($addMode || $editMode) {
		# List All Role
		$roleRecords	= $manageroleObj->fetchAllRecords();

		if ($selRole!="") $userRecs	= $dashboardManagerObj->getUserList($selRole);
	}

	if ($editMode)	$heading	= $label_editDashboard	;	
	else 		$heading	= $label_addDashboard	;

	$ON_LOAD_PRINT_JS = "libjs/ManageDashboard.js"; // For Printing JS in Head SCRIPT section
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
    <form name="frmManageDashboard" action="ManageDashboard.php" method="post">
    <table cellspacing="0"  align="center" cellpadding="0" width="60%">	
<? if($err!="" ){?>	
    <tr> 
      <td height="10" align="center" class="err1" > 
        <?=$err;?>        
      </td>
    </tr>	
<?}?>
    		<?php
			if ($editMode || $addMode) {
		?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; 
                    <?=$heading;?>
                  </td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageDashboard.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageDashboard(document.frmManageDashboard);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageDashboard.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageDashboard(document.frmManageDashboard);">    
			<input type="hidden" name="cmdAddNew" value="1">
		                    </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidIPAddressId" value="<?=$editDashboardRec;?>">
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td  height="10" colspan="2" align="center">
			<table width="200">
                          <tr>
                            <td colspan="2" nowrap>
				<table width="200" align="center" cellpadding="0" cellspacing="0">
				<tr>
                            <td class="fieldName" nowrap>*Role </td>
                            <td>				
				<select name="selRole" id="selRole" <?if($addMode==true){?> onchange="this.form.submit();" <? } else {?> onChange="this.form.editId.value=<?=$editId?>;this.form.submit();" <?}?>>				
                              <option value="">--Select--</option>
                              <?
				foreach($roleRecords as $rrec) {
					$roleId		= $rrec[0];
					$roleName	= stripSlash($rrec[1]);
					$selected	= "";
					if ($selRole==$roleId) $selected="Selected";
				?>
                              <option value="<?=$roleId?>" <?=$selected?>>
                                <?=$roleName?>
                                </option>
                              <? }?>
                            </select></td>
                          </tr>
			<tr>
                            <td class="fieldName" nowrap>User</td>
                            <td>				
				<select name="selUser" id="selUser" <?if($addMode==true){?> onchange="this.form.submit();" <? } else {?> onChange="this.form.editId.value=<?=$editId?>;this.form.submit();" <?}?>>				
                              <option value="0">--Select All--</option>
                              <?
				foreach($userRecs as $ur) {
					$userId		= $ur[0];
					$userName	= stripSlash($ur[1]);
					$selected	= ($selUser==$userId)?"Selected":"";
				?>
                              <option value="<?=$userId?>" <?=$selected?>><?=$userName?></option>
                              <? }?>
                            </select></td>
                          </tr>
	                        </table></td>
                            </tr>
			<tr>
				<TD style="padding-left:5px;padding-right:5px;">
					<table  cellspacing="1" bgcolor="#999999" cellpadding="1" width="100%">
						<TR bgcolor="#f2f2f2" align="center">
							<TD class="listing-head">Dashboard</TD>
							<td class="listing-head">Access</td>
						</TR>
						<?php
							$i = 0;
							foreach ($dashBoardFunctions as $code=>$dashBoardType) {
								$i++;
								# get Rec
								list($entryId,$selType) = $dashboardManagerObj->getDashboardRec($selRole, $code, $selUser);
								//echo "<br>$entryId,$selType<br>";	
								$checked = "";
								if ($selType==$code) $checked = "checked";
						?>
						<tr bgcolor="White">	
							<TD class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$dashBoardType?></TD>
							<TD class="listing-item" align="center" style="padding-left:5px;padding-right:5px;">
								<input type="hidden" name="entryId_<?=$i?>" value="<?=$entryId?>">
								<input type="checkbox" name="selDashBoard_<?=$i?>" id="selDashBoard_<?=$i?>" value="<?=$code?>" class="chkBox" <?=$checked?>>
							</TD>
						</tr>	
						<?php
							}
						?>
						<input type="hidden" name="dashBoardRowCount" id="dashBoardRowCount"  value="<?=$i?>">
					</table>
				</TD>
			</tr>
                        </table></td>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageDashboard.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageDashboard(document.frmManageDashboard);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageDashboard.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageDashboard(document.frmManageDashboard);"></td>
                        <?}?>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
						<td colspan="2"  height="10" ></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
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
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName" >&nbsp;Manage 
                    Dashboard </td>
                  <td background="images/heading_bg.gif" class="pageName" colspan="2"></td>
                </tr>
		<tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
		<!--<tr>
		<TD colspan="3" style="padding-left:10px;padding-right:10px;" align="right">
			<table>
			<TR><TD align="center">
			<fieldset>
				<legend class="listing-item">Distributor Account</legend>
				<table>
					<TR>
					<TD>
						<table>
							<tr>
								<td class="fieldName" nowrap onMouseover="ShowTip('Pending cheque display upto the current date or (current date+cheque display days)');" onMouseout="UnTip();">Pending cheque display:</td>
								<td class="listing-item" nowrap="true">
									<input type="text" size="2" name="pChqDays" id="pChqDays" value="<?=($pChqDays!=0)?$pChqDays:"";?>" style="text-align:right;" />&nbsp;days
								</td>
								</tr>
							<tr>
								<td class="fieldName" nowrap onMouseover="ShowTip('Credit balance amount display limit Rs.(+/-).');" onMouseout="UnTip();">Credit balance display limit ( &#177; Rs.):</td>
								<td class="listing-item" nowrap="true">
									<input type="text" size="5" name="crBalDisplayLimit" id="crBalDisplayLimit" value="<?=($crBalDisplayLimit!=0)?$crBalDisplayLimit:"";?>" style="text-align:right;" />
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap onMouseover="ShowTip('Overdue amount display limit.');" onMouseout="UnTip();">Overdue Amount display limit (Rs.):</td>
								<td class="listing-item" nowrap="true">
									<input type="text" size="5" name="overdueDisplayLimit" id="overdueDisplayLimit" value="<?=($overdueDisplayLimit!=0)?$overdueDisplayLimit:"";?>" style="text-align:right;" />
								</td>
							</tr>
						</table>
					</TD>
					<td nowrap="true" align="right" style="padding-left:5px; padding-right:5px;">
						<?php
							if ($edit) {
						?>
							<input type="submit" name="cmdUpdate" class="button" value=" Update " onClick="return pChqUpdate();">
						<?php
							}
						?>
					</td>
					</TR>
				</table>
				
			</fieldset>
			</TD>
			</TR>			
			</table>
		</TD>
		</tr>-->
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dashBoardRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageDashboard.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <?
									if($errDel!="")
									{
								?>
                <tr> 
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?>                  </td>
                </tr>
                <?
									}
								?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" style="padding-left:10px;padding-right:10px;"> 
			<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?php
			if (sizeof($dashBoardRecords)>0) {
			$i	=	0;
			?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td width="20">
				<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
			</td>
                        <td class="listing-head" nowrap>Role</td>
			<td class="listing-head" nowrap>User</td>
                        <td class="listing-head" align="center">Access</td>
			<? if($edit==true){?>
                        <td class="listing-head" width="50"></td>
			<? }?>
                      </tr>
                      <?php
			foreach ($dashBoardRecords as $dbr) {
				$i++;
				$dashBoardEntryId = $dbr[0];
				$selRoleId	  = $dbr[1];				
				$selRoleName	= $dbr[3];
				$selUserId	= $dbr[4];
				$selUserName	= ($dbr[5])?$dbr[5]:"ALL";
				# Get Dash Board Records
				$getDashboardAccessRecords = $dashboardManagerObj->dashboardAccessRecords($selRoleId, $selUserId);
			?>
                      <tr  bgcolor="WHITE" > 
                        <td width="20" height="25">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dashBoardEntryId;?>" class="chkBox">
				<input type="hidden" name="roleId_<?=$i;?>" id="roleId_<?=$i;?>" value="<?=$selRoleId;?>" >
				<input type="hidden" name="hidUserId_<?=$i;?>" id="hidUserId_<?=$i;?>" value="<?=$selUserId;?>" >
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selRoleName;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selUserName;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
				<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($getDashboardAccessRecords)>0) {
						$nextRec	=	0;
						$k=0;
						$vatPercent = "";
						foreach ($getDashboardAccessRecords as $cR) {
							$j++;
							$dashboardType = $cR[0];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$dashBoardFunctions[$dashboardType]?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}
				?>
				</tr>
			</table>
			</td>
						<? if($edit==true){?>
                        <td class="listing-item" align="center" width="40"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dashBoardEntryId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');this.form.action='ManageDashboard.php';"></td>
						<? }?>
                      </tr>
                      <?
													}
											?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
					  <input type="hidden" name="editSelectionChange" value="0">
                      <?
												}
												else
												{
											?>
                      <tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
												}
											?>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <tr > 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dashBoardRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageDashboard.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
              </table></td>
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
?>