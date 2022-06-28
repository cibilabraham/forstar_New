<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$mode		=	$g["mode"];
	
	$selection	= "?pageNo=".$p["pageNo"]."&stateFilter=".$p["stateFilter"];

	#------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
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
	#----------------------------------------------------------

	//$stateVatRateListObj->getValidRateList(1, '2009-06-22');	

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	# Resetting Values
	if ($p["selState"]!="") $selStateId	= $p["selState"];
	if ($p["rateListName"]!="") $rateListName = $p["rateListName"];
	if ($p["startDate"]!="") $startDate = $p["startDate"];	
	//if ($p["copyRateList"]!="") $copyRateList = $p["copyRateList"];	

	#Insert a Record
	if ($p["cmdAdd"]!="") {
	
		$rateListName	= addSlash(trim($p["rateListName"]));		
		$startDate	= mysqlDateFormat(trim($p["startDate"]));
		$copyRateList	= $p["copyRateList"];
		$selStateId	= $p["selState"];
		$stateVatCurrentRateListId = $p["hidCurrentRateListId"];
		# Duplication Checking
		$recExist = $stateVatRateListObj->checkRecExist($startDate, $selStateId, $cId);
		if ($recExist) $startDate = $p["startDate"];
		
		if ($rateListName!="" && $p["startDate"]!="" && $selStateId!="" && !$recExist) {	
			$stateVatRateListRecIns = $stateVatRateListObj->addStateVatRateList($rateListName, $startDate, $copyRateList, $userId, $selStateId, $stateVatCurrentRateListId);
				
			if ($stateVatRateListRecIns) {
				# get Current Rate List based on state
				$stateVatRateListId = $stateVatRateListObj->latestRateList($selStateId);
				#Upate Dist Margin State Rec
				$upateDistMarginState =	$stateVatMasterObj->updateDistMarginRecs($stateVatRateListId);
				
				# --------- Create a new rate list for dist margin starts here ----------------
				#
				$dmsRecs = $distMarginRateListObj->getDistMgnStateWiseRecs($selStateId, $startDate); 
				$prevDistributorId 	= "";
				$prevRateListId 	= "";		
				foreach ($dmsRecs as $dmr) {
					$distributorId 	= $dmr[1];
					$rateListId 	= $dmr[3];
		
					if ($prevDistributorId!=$distributorId) {
						$distName = $dmr[4];						
						$distriName = str_replace (" ",'',$distName);
						$selName =substr($distriName, 0,9);	
						$rateListName = $selName."-".date("dMy", strtotime($startDate));
											
						$distMarginRateListRecIns = $distMarginRateListObj->addDistMarginRateList($rateListName, $startDate, $rateListId, $userId, $distributorId, $rateListId);
						if ($distMarginRateListRecIns) {
							$distMarginRateListId =$distMarginRateListObj->latestRateList($distributorId);
							$updateDistMgnRec = $changesUpdateMasterObj->updateDistributorMgnStructRecs($distributorId, $distMarginRateListId);
						}
					} // Prev cond ends here
								
					$prevDistributorId = $distributorId;
				} // Loop Ends here
				# --------- Create a new rate list for dist margin Ends here ----------------

				$sessObj->createSession("displayMsg",$msg_succAddStateVatRateList);
				$sessObj->createSession("nextPage",$url_afterAddStateVatRateList.$selection);	
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddStateVatRateList;
			}
			$stateVatRateListRecIns	=	false;
		} else {
			$addMode	= true;
			$err = $msg_failAddStateVatRateList;
		}
	}


	# Edit Section
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$stateVatRateListObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);	
		$startDate		= 	dateFormat($rateListRec[2]);
		$selStateId		= 	$rateListRec[3];
		$endDate		= 	$rateListRec[4];
		//$isRateListUsed = $stateVatRateListObj->checkRateListUse($editRateListId);
		//$readOnly = "";	
		//if ($isRateListUsed) $readOnly = "readonly";
		$readOnly   = ($endDate!='0000-00-00' && $endDate!="")?"readonly":"";
		$disabled   = ($endDate!='0000-00-00' && $endDate!="")?"disabled='true'":"";
		$disableField = "disabled";
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$stateVatRateListId	= $p["hidRateListId"];
		
		$rateListName		= addSlash(trim($p["rateListName"]));		
		$startDate		= mysqlDateFormat($p["startDate"]);

		$selStateId		= $p["hidStateId"];

		# Duplication Checking
		$recExist = $stateVatRateListObj->checkRecExist($startDate, $selStateId, $stateVatRateListId);
		if ($recExist) $startDate = $p["startDate"];

		if ($stateVatRateListId!="" && $rateListName!="" && !$recExist) {
			$stateVatRateListRecUptd = $stateVatRateListObj->updateStateVatRateList($rateListName, $startDate, $stateVatRateListId);
		}
	
		if ($stateVatRateListRecUptd) {
			#Upate Dist Margin State Rec
			$upateDistMarginState =	$stateVatMasterObj->updateDistMarginRecs($stateVatRateListId);

			$sessObj->createSession("displayMsg",$msg_succUpdateStateVatRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateStateVatRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateStateVatRateList;
		}
		$stateVatRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stateVatRateListId	=	$p["delId_".$i];
			$stateId		= $p["hidStateId_".$i];
			
			$isRateListUsed = $stateVatRateListObj->checkRateListUse($stateVatRateListId);
			
			if ($stateVatRateListId!="" && !$isRateListUsed) {
				$distMarginRateListRecDel = $stateVatRateListObj->deleteDistMarginRateList($stateVatRateListId, $stateId);
			}
		}
		if ($distMarginRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStateVatRateList);
			$sessObj->createSession("nextPage",$url_afterDelStateVatRateList.$selection);
		} else {
			$errDel	=	$msg_failDelStateVatRateList;
		}
		$distMarginRateListRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stateVatRateListId	=	$p["confirmId"];


			if ($stateVatRateListId!="") {
				// Checking the selected fish is link with any other process
				$stateVatRateRecConfirm = $stateVatRateListObj->updatestateVatRateconfirm($stateVatRateListId);
			}

		}
		if ($stateVatRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmStateVatRateList);
			$sessObj->createSession("nextPage",$url_afterDelStateVatRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$stateVatRateListId = $p["confirmId"];

			if ($stateVatRateListId!="") {
				#Check any entries exist
				
					$stateVatRateRecConfirm = $stateVatRateListObj->updatestateVatRateReleaseconfirm($stateVatRateListId);
				
			}
		}
		if ($stateVatRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStateVatRateList);
			$sessObj->createSession("nextPage",$url_afterDelStateVatRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	if ($g["stateFilter"]!="") $stateFilterId = $g["stateFilter"];
	else $stateFilterId = $p["stateFilter"];	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo=$g["pageNo"];
	else 				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidDistributorFilterId"]!=$p["stateFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	#List All Records
	$stateVatRateListRecords = $stateVatRateListObj->fetchAllPagingRecords($offset, $limit, $stateFilterId);	
	$stateVatRateListRecordSize	= sizeof($stateVatRateListRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($stateVatRateListObj->fetchAllRecords($stateFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# List all State
		//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
		$stateResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
	}

	# Filter all State
	//$stateResultSetFilterObj = $stateMasterObj->fetchAllRecords();
	$stateResultSetFilterObj = $stateMasterObj->fetchAllRecordsActiveState();

	
	if ($selStateId!="") {
		# State wise Vat rate List Recs
		$filterStateVatRateListRecs = $stateVatRateListObj->filterStateWiseVatRateListRecords($selStateId);
		# get Current Rate List based on state
		$currentRateListId = $stateVatRateListObj->latestRateList($selStateId);

		if ($p["copyRateList"]!="") $selRateList = $p["copyRateList"];
		else $selRateList	= $currentRateListId;		
	}

	if ($editMode)	$heading = $label_editStateVatRateList;
	else 		$heading = $label_addStateVatRateList;

	$ON_LOAD_PRINT_JS	= "libjs/StateVatRateList.js";
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStateVatRateList" action="StateVatRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">	
	<tr>
	  	<td align="center">
			<a href="StateVatMaster.php" class="link1">State Wise VAT Master</a>
		</td>
	</tr>		
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<tr><TD height="5"></TD></tr>
		<?php
			if (!$stateFilterId) {
		?>
		<tr> 
			<td align="center" class="listing-item" style="color:Maroon;">
				<strong>Latest State Wise Vat Rate List.</strong>			
			</td>
		</tr>
		<?php
			}
		?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "State Wise Vat Rate List";
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
									<td colspan="2" >
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStateVatRateListMaster(document.frmStateVatRateList);" <?=$disabled?>>
												</td>
												
												<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatRateList.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStateVatRateListMaster(document.frmStateVatRateList);">					
			<input type="hidden" name="cmdAddNew" value="1">
		</td>
			<?}?>
	</tr>
	<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD colspan="2" align="center">
		<table>
			<tr>
		<td class="fieldName" nowrap >*Name </td>
		<td align="left">
			<INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20" autocomplete="off">
		</td>
	</tr>
	<tr>
	<td class="fieldName" nowrap >*Start Date </td>
	<td>
		<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$readOnly?> autocomplete="off">
		<input type="hidden" name="hidStartDate" id="hidStartDate" value="<?=$startDate?>" readonly="true">
	</td>
	</tr>
	<tr>
		<td nowrap class="fieldName">*State</td>
		<td nowrap>
                        <select name="selState" id="selState" onchange="this.form.submit();" style="width:120px;" <?=$disableField?>>
                        <option value="">-- Select --</option>
			<?	
				while ($sr=$stateResultSetObj->getRow()) {
					$stateId 	= $sr[0];
					$stateName	= stripSlash($sr[2]);	
					$selected 	= ($selStateId==$stateId)?"selected":"";	
			?>
                	<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
			<? 
				}
			?>
			</select>
			<input type="hidden" name="hidStateId" id="hidStateId" value="<?=$selStateId?>">
		</td>
	</tr> 
	<? 
		if ($addMode==true && $selStateId!="" && sizeof($filterStateVatRateListRecs)>0) {
	?>
	<tr>
		<td class="fieldName" nowrap>Copy From</td>
		<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list" style="width:120px;">
                      <option value="">-- Select --</option>
                      <?
			foreach($filterStateVatRateListRecs as $dmrl) {
				$stateVatRateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);
				$startDate		=	dateFormat($dmrl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected =  ($selRateList==$stateVatRateListId)?"Selected":"";
			?>
                      <option value="<?=$stateVatRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select>
		</td>
	</tr>
	<? }?>
		</table>
		</TD>
	</tr>			
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStateVatRateListMaster(document.frmStateVatRateList);" <?=$disabled?>>												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StateVatRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStateVatRateListMaster(document.frmStateVatRateList);">												</td>

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
				<td height="10" align="center" ></td>
			</tr>
	<tr>
				<td colspan="3" align="center">
						<table width="20%">
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
		<td class="listing-item">State&nbsp;</td>
                <td align="left">
			<select name="stateFilter" onchange="this.form.submit();">
			<option value="">--Select All--</option>		 
				<?php
					while ($sr=$stateResultSetFilterObj->getRow()) {
						$stateId 	= $sr[0];
						$stateName	= stripSlash($sr[2]);	
						$selected 	= ($stateFilterId==$stateId)?"selected":"";	
				?>
				<option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
				<?php
					}
				?>
			</select> 
                 </td>
                          </tr>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;State Wise Vat Rate List  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateVatRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintStateVatRateList.php?stateFilter=<?=$stateFilterId?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?php
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
                <?php
			if ( sizeof($stateVatRateListRecords) > 0 ) {
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
      				$nav.= " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\"  class=\"link1\">>></a> ";
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
		<tr align="center"  > 
			<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date</th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">State</th>
			<? if($edit==true){?>
			<th class="listing-head" width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
			<th class="listing-head" width="45">&nbsp;</th>
			<? }?>
		</tr>
		</thead>
		<tbody>
                      <?
			foreach ($stateVatRateListRecords as $dmrl) {
				$i++;
				$stateVatRateListId	= $dmrl[0];
				$rateListName		= stripSlash($dmrl[1]);
				$startDate		= dateFormat($dmrl[2]);
				$stateId		= $dmrl[3];
				$selStateName		= $dmrl[4];
				$active=$dmrl[5];
			?>
                      <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
                        <td width="20">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stateVatRateListId;?>" class="chkBox">
				<input type="hidden" name="hidStateId_<?=$i;?>" id="hidStateId_<?=$i;?>" value="<?=$stateId;?>">
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px; text-align:center;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$selStateName?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stateVatRateListId;?>,'editId'); this.form.action='StateVatRateList.php';"><? } ?></td>
			<? }?>


			 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stateVatRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$stateVatRateListId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
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
      				$nav.= " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StateVatRateList.php?pageNo=$page&stateFilter=$stateFilterId\"  class=\"link1\">>></a> ";
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
                      <? } else { ?>
                      <tr> 
                        <td colspan="5"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stateVatRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintStateVatRateList.php?stateFilter=<?=$stateFilterId?>',700,600);"><? }?></td></tr></table></td></tr>
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
<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">	
<input type="hidden" name="hidDistributorFilterId" value="<?=$stateFilterId?>">		
		    <tr>
		   	   <td height="10"></td>
      		    </tr>
	   <tr>
		<td height="10" align="center">
			<a href="StateVatMaster.php" class="link1">State Wise VAT Master</a>
		</td>
	</tr>
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
	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>