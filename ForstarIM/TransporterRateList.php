<?php
	$insideIFrame = "Y";
	require("include/include.php");
	ob_start();
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$mode		=	$g["mode"];

	$selection	= "?pageNo=".$p["pageNo"]."&transporterFilter=".$p["transporterFilter"];

	#------------  Checking Access Control Level  ----------------
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		//header("Location: ErrorPage.php");
		header("Location: ErrorPageIFrame.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	#----------------------------------------------------------

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	
	# Resetting Values
	if ($p["selTransporter"]!="") 	$selTransporter	= $p["selTransporter"];
	if ($p["rateListName"]!="") 	$rateListName 	= $p["rateListName"];
	if ($p["startDate"]!="") 	$startDate 	= $p["startDate"];
	if ($p["selFunctionality"]!="") $selFunctionality = $p["selFunctionality"];	
	
	// "TRM"=>"Transporter Rate Master","TOC"=>"Transporter Other Charges"
	
	# Insert a Record
	if ($p["cmdAdd"]!="") {

		$rateListName	= addSlash(trim($p["rateListName"]));
		$startDate	= mysqlDateFormat(trim($p["startDate"]));
		$copyRateList	= $p["copyRateList"];
		$selTransporter	= $p["selTransporter"];
		$currentRateListId = $p["hidCurrentRateListId"];
		$selFunctionality = $p["selFunctionality"];

		# Duplication Checking
		//$recExist = $transporterRateListObj->checkRecExist($startDate, $selTransporter, $selFunctionality);				
		//if ($recExist) $startDate = $p["startDate"];
		# Check for valid start date
		$validDateEntry = $transporterRateListObj->chkValidDateEntry($startDate, $selTransporter, $selFunctionality, $cId);
		//die();
		$validRateListDate = false;	
		if (!$validDateEntry) {
			$activeDateList = $transporterRateListObj->getActiveRateListForSelDate($startDate, $selTransporter, $selFunctionality, $cId);
			//die();
			printr($activeDateList);
			$startDate = $p["startDate"];
			$validRateListDate = true;
		}
		
		//die();

		if ($rateListName!="" && $p["startDate"]!="" && $selTransporter!="" && $validDateEntry) {	
				$transporterRateListRecIns = $transporterRateListObj->addTransporterRateList($rateListName, $startDate, $copyRateList, $userId, $selTransporter, $currentRateListId, $selFunctionality);
				
				if ($transporterRateListRecIns) {
					$addMode		=	false;
					$sessObj->createSession("displayMsg",$msg_succAddTransporterRateList);
					$sessObj->createSession("nextPage",$url_afterAddTransporterRateList.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddTransporterRateList;
				}
				$transporterRateListRecIns	=	false;
		} else {
			$addMode	= true;
			if (!$vaildDateEntry) $err = $msg_failAddTransporterRateList."<br> Please check the start date.";
			else $err = $msg_failAddTransporterRateList;
		}
	}


	# Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$transporterRateListId	= $p["hidRateListId"];
		
		$rateListName		= addSlash(trim($p["rateListName"]));		
		$startDate		= mysqlDateFormat($p["startDate"]);
		$selTransporter		= $p["selTransporter"];
		$selFunctionality 	= $p["selFunctionality"];
		$prevDate		= mysqlDateFormat($p["hidStartDate"]);

		# Check for valid start date
		$validDateEntry = $transporterRateListObj->chkValidDateEntry($startDate, $selTransporter, $selFunctionality, $transporterRateListId);
		if (!$validDateEntry) $startDate = $p["startDate"];

		if ($transporterRateListId!="" && $rateListName!="" && $validDateEntry) {
			$transporterRateListRecUptd = $transporterRateListObj->updateTransporterRateList($rateListName, $startDate, $transporterRateListId, $selTransporter, $selFunctionality, $prevDate);
		}
	
		if ($transporterRateListRecUptd) {
			$editMode = false;
			$p["editId"] = "";
			$editId = "";
			$sessObj->createSession("displayMsg",$msg_succUpdateTransporterRateList);
			//$sessObj->createSession("nextPage",$url_afterUpdateTransporterRateList.$selection);
		} else {
			$editMode	=	true;
			if (!$vaildDateEntry) $err = $msg_failUpdateTransporterRateList."<br> Please check the start date.";
			else $err	= $msg_failUpdateTransporterRateList;
		}
		$transporterRateListRecUptd	=	false;
	}
	

	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$transporterRateListObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);	
		$startDate		= 	dateFormat($rateListRec[2]);
		$selTransporter		= 	$rateListRec[3];
		$selFunctionality	= 	$rateListRec[4];

		//$rateListExist = $transporterRateListObj->checkRateListUse($editRateListId);
		//$readOnly = "";	
		//if ($rateListExist) $readOnly = "readonly";
		$funtionDisabled = "Disabled";
	}

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$transporterRateListId	= $p["delId_".$i];
			$transporterId		= $p["hidTransporterId_".$i];
			$functionType		= $p["hidFunctionType_".$i];
						
			if ($transporterRateListId!="") {
				# Check Transporter Rec Exist
				$rateListExist = $transporterRateListObj->checkRateListUse($transporterRateListId, $functionType);

				if (!$rateListExist) {
					$transporterRateListRecDel = $transporterRateListObj->deleteTransporterRateList($transporterRateListId, $transporterId, $functionType);
				}
			}
		}
		if ($transporterRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterRateList);
			$sessObj->createSession("nextPage",$url_afterDelTransporterRateList.$selection);
		} else {
			if ($rateListExist) $errDel = $msg_failDelTransporterRateList."<br/>".$msgTransporterRateRecExist;
			else $errDel	=	$msg_failDelTransporterRateList;			
		}
		$transporterRateListRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterId	=	$p["confirmId"];
			if ($transporterId!="") {
				// Checking the selected fish is link with any other process
				$transporterRecConfirm = $transporterRateListObj->updateTransporterRateListconfirm($transporterId);
			}

		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterId = $p["confirmId"];
			if ($transporterId!="") {
				#Check any entries exist
				
					$transporterRecConfirm = $transporterRateListObj->updateTransporterRateListReleaseconfirm($transporterId);
				
			}
		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	if ($g["transporterFilter"]!="") $transporterFilterId = $g["transporterFilter"];
	else $transporterFilterId = $p["transporterFilter"];	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo=$g["pageNo"];
	else 				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidTransporterFilterId"]!=$p["transporterFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	# List All Records
	$transporterRateListRecords = $transporterRateListObj->fetchAllPagingRecords($offset, $limit, $transporterFilterId);	
	$distMarginRateListRecordSize	= sizeof($transporterRateListRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($transporterRateListObj->fetchAllRecords($transporterFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Transporter		
	$transporterRecords	= $transporterMasterObj->fetchAllRecordsActiveTransporter();

	
	if ($selTransporter!="") {
		# Distibutor wise rate List
		$filterTransporterListRecs = $transporterRateListObj->filterTransporterWiseRecords($selTransporter, $selFunctionality);
		# get Current Rate List of the Distributor
		$currentRateListId = $transporterRateListObj->latestRateList($selTransporter, $selFunctionality);

		if ($p["copyRateList"]!="") $selRateList = $p["copyRateList"];
		else $selRateList	= $currentRateListId;		
	}


	# Setting Transporter Function Recs
	$transporterFunctionRecs = array("TRM"=>"Transporter Rate Master","TOC"=>"Transporter Other Charges");

	if ($editMode)	$heading = $label_editTransporterRateList;
	else 		$heading = $label_addTransporterRateList;

	$ON_LOAD_PRINT_JS	= "libjs/TransporterRateList.js";
		
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterRateList" action="TransporterRateList.php" method="post">	
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>
	<?php
		if (!$transporterFilterId) {
	?>
	<tr> 
		<td height="10" align="center" class="listing-item" style="color:Maroon;">
			<strong>Transporter wise current rate list.</strong>
		</td>
	</tr>
	<tr>
	  <td height="5" align="center">&nbsp;</td>
	  </tr>
	<?php
		}
	?>
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
					$bxHeader = "Transporter Rate List";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">
		<?php
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" align="center" >
									<?php
									if ($validRateListDate) {
									?>
									<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
									<tr>
										<td height="10" ></td>
									</tr>
									<tr>
										<TD class="listing-item">
										The selected date (<?php echo $startDate?>) is between the following Rate Lists
										<br>
										1.<?php echo $activeDateList[0][1]."(".dateFormat($activeDateList[0][2]).")"?> 
										<br>
										2.<?php echo $activeDateList[1][1]."(".dateFormat($activeDateList[1][2]).")"?>
										</TD>
									</tr>
									<tr><TD class="listing-item">
									Do you wish to continue?
									</TD></tr>
									<tr><TD align="center">
									<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateList.php');">&nbsp;&nbsp;
										<input type="submit" name="cmdContinue" class="button" value=" Continue " >
									</TD></tr>
									<tr>
										<td height="10" ></td>
									</tr>
									</table>
									<?php
									} else {
									?>
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistMarginRateListMaster(document.frmTransporterRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistMarginRateListMaster(document.frmTransporterRateList);">					
<input type="hidden" name="cmdAddNew" value="1">
						</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
	<tr><TD height="10"></TD></tr>
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
	<tr>
	<td class="fieldName" nowrap >*Start Date </td>
	<td>
		<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$readOnly?> />
		<input type="hidden" name="hidStartDate" id="hidStartDate" value="<?=$startDate?>" />
	</td>
	</tr>
<?php 
	if ($addMode==true) {
?>
	<tr>
		<td nowrap class="fieldName">*Transporter</td>
		<td nowrap>
                        <select name="selTransporter" id="selTransporter" onchange="this.form.submit();">
                        <option value="">-- Select --</option>
			<?php
				foreach ($transporterRecords as $tr) {
					$transporterId	 = $tr[0];					
					$transporterName = stripSlash($tr[2]);	
					$selected =  ($selTransporter==$transporterId)?"selected":"";	
			?>
                	<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
			<?php 
				}
			?>
			</select>
		</td>
	</tr>
<?php
	} else if ($editMode) {
?>
		<input type="hidden" name="selTransporter" value="<?=$selTransporter?>" />
	<?php
		 }
	?>
	<tr>
		<td nowrap class="fieldName">*Function</td>
		<td>	
			<select name="selFunctionality" onchange="this.form.submit();" <?=$funtionDisabled?>>
				<option value="">-- Select --</option>
				<?php
					foreach ($transporterFunctionRecs as $tfrKey=>$tfrValue) {
						$selected =  ($selFunctionality==$tfrKey)?"Selected":"";
				?>
				<option value="<?=$tfrKey?>" <?=$selected?>><?=$tfrValue?></option>	
				<?php
					}
				?>
			</select>
	<?
		if ($editMode) {
	?>
		<input type="hidden" name="selFunctionality" value="<?=$selFunctionality?>" />
	<?php
		 }
	?>
		</td>
	</tr>

	<? if($addMode==true){ ?>
	<tr>
		<td class="fieldName" nowrap>Copy From  </td>
		<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?php
			foreach($filterTransporterListRecs as $trl) {
				$transporterRateListId	=	$trl[0];
				$rateListName		=	stripSlash($trl[1]);
				$startDate		= 	dateFormat($trl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected =  ($selRateList==$transporterRateListId)?"Selected":"";
				?>
                      <option value="<?=$transporterRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select>
		</td></tr>
			<? }?>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistMarginRateListMaster(document.frmTransporterRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistMarginRateListMaster(document.frmTransporterRateList);">	
											</td>
										<?}?>
									</tr>
									<tr>
										<td colspan="2"  height="10" ></td>
									</tr>
								</table>
							<?php
							}
							?>
							</td>
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
			# Listing Grade Starts
		?>
	</table>
		</td>
	</tr>	
			<tr>
				<td height="10" align="center" ></td>
			</tr>
	<tr>
				<td colspan="3" align="center">
						<table width="25%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="0">
					  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item">Transporter:&nbsp;</td>
                <td>
		<select name="transporterFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>		 
			<?php	
				foreach ($transporterRecords as $tr) {
					$transId 	= $tr[0];
					$transName 	= stripSlash($tr[2]);
					$selected =  ($transporterFilterId==$transId)?"selected":"";	
			?>
                	<option value="<?=$transId?>" <?=$selected?>><?=$transName?></option>
			<? 
				}
			?>
		
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
          <td>&nbsp;</td>
                          </tr>
                    </table>
		</td></tr>
		</table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
						</td>
						</tr>
						</table>
								</td>
							</tr>		
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Rate List  </td>
<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintTransporterRateList.php?transporterFilter=<?=$transporterFilterId?>',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
                <?php
			if ( sizeof($transporterRateListRecords) > 0 ) {
				$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\"  class=\"link1\">>></a> ";
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
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Transporter</th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Function</th>
			<? if($edit==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
                      </tr>
	</thead>
	<tbody>
                      <?php
			foreach ($transporterRateListRecords as $trl) {
				$i++;
				$transporterRateListId	=	$trl[0];
				$rateListName		=	stripSlash($trl[1]);				
				$startDate		=	dateFormat($trl[2]);
				$transporterId		= 	$trl[3];
				$distributorName	= 	$trl[4];
				$selFunctionName	= 	$trl[5];
				$active=$trl[6];
			?>
                      <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
                        <td width="20">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterRateListId;?>" class="chkBox">
				<input type="hidden" name="hidTransporterId_<?=$i;?>" id="hidTransporterId_<?=$i;?>" value="<?=$transporterId;?>">
				<input type="hidden" name="hidFunctionType_<?=$i;?>" id="hidFunctionType_<?=$i;?>" value="<?=$selFunctionName;?>">
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap="true"><?=$distributorName?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap="true"><?=$transporterFunctionRecs[$selFunctionName]?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$transporterRateListId;?>,'editId'); this.form.action='TransporterRateList.php';"><? } ?></td>
			<? }?>

			<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterRateListId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
                      </tr>
                      <?
			}
			?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterRateList.php?pageNo=$page&transporterFilter=$transporterFilterId\"  class=\"link1\">>></a> ";
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
                        <td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
                      </tr>
                      <?php 
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintTransporterRateList.php?transporterFilter=<?=$transporterFilterId?>',700,600);"><? }?></td></tr></table></td></tr>
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
<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">	
<input type="hidden" name="hidTransporterFilterId" value="<?=$transporterFilterId?>">		
		    <tr>
		      <td height="10"></td>
      </tr>	    
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<?php 
	if ($iFrameVal=="") { 
	?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	//ensureInFrameset(document.frmTransporterRateList);
	//-->
	</script>
<?php 
	}
?>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>