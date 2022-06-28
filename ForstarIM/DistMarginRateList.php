<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$mode		=	$g["mode"];
	
	$selection	= "?pageNo=".$p["pageNo"]."&distributorFilter=".$p["distributorFilter"];

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


	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	# Resetting Values
	if ($p["selDistributor"]!="") $selDistributor	= $p["selDistributor"];
	if ($p["rateListName"]!="") $rateListName = $p["rateListName"];
	if ($p["startDate"]!="") $startDate = $p["startDate"];	
	//if ($p["copyRateList"]!="") $copyRateList = $p["copyRateList"];

	//$seldate	= "2008-08-02";
	//$selDistributorId = 11;
	//$checkVaildDateEntry = $distMarginRateListObj->chkValidDateEntry($seldate, $selDistributorId, 14);
	//$changesUpdateMasterObj->getSORecords("2009-03-01", "2009-03-31", 16);
	//$distMarginRateListObj->prevRateListId(7, 11);

	#Insert a Record
	if ($p["cmdAdd"]!="") {
	
		$rateListName	= addSlash(trim($p["rateListName"]));		
		$startDate	= mysqlDateFormat(trim($p["startDate"]));
		$copyRateList	= $p["copyRateList"];
		$selDistributor	= $p["selDistributor"];
		$distCurrentRateListId = $p["hidCurrentRateListId"];
		# Duplication Checking
		//$recExist = $distMarginRateListObj->checkRecExist($startDate, $selDistributor);
		$validDateEntry = $distMarginRateListObj->chkValidDateEntry($startDate, $selDistributor, $cId);
		if (!$validDateEntry) $startDate = $p["startDate"];
		
		if ($rateListName!="" && $p["startDate"]!="" && $selDistributor!="" && $validDateEntry) {	
				$distMarginRateListRecIns = $distMarginRateListObj->addDistMarginRateList($rateListName, $startDate, $copyRateList, $userId, $selDistributor, $distCurrentRateListId);
				
				# Update SO records (Changes Update Master
				if ($distMarginRateListRecIns) {
					$updateSORecords = $changesUpdateMasterObj->getSORecords($startDate, $prevStartDate, $selDistributor);

					# Update Dist margin recs					
					if ($distMarginRateListRecIns) {
						$selDistMarginRateListId =$distMarginRateListObj->latestRateList($selDistributor);
						$updateDistMgnRec = $changesUpdateMasterObj->updateDistributorMgnStructRecs($selDistributor, $selDistMarginRateListId);
					}
				}

				if ($distMarginRateListRecIns) {
					$sessObj->createSession("displayMsg",$msg_succAddDistMarginRateList);
					$sessObj->createSession("nextPage",$url_afterAddDistMarginRateList.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddDistMarginRateList;
				}
				$distMarginRateListRecIns	=	false;
		} else {
			$addMode	= true;
			$err = $msg_failAddDistMarginRateList;
		}
	}


	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$distMarginRateListObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);	
		$startDate		= 	dateFormat($rateListRec[2]);
		$selDistributor		= 	$rateListRec[3];
		$endDate		= 	$rateListRec[4];

		//$isRateListUsed = $distMarginRateListObj->checkRateListUse($editRateListId);
		$readOnly = "";	
		/*
		if ($isRateListUsed) $readOnly = "readonly";
		*/

		$readOnly   = ($endDate!='0000-00-00' && $endDate!="")?"readonly":"";
		$disabled   = ($endDate!='0000-00-00' && $endDate!="")?"disabled='true'":"";
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$distMarginRateListId	=	$p["hidRateListId"];
		
		$rateListName		=	addSlash(trim($p["rateListName"]));		
		$startDate		=	mysqlDateFormat($p["startDate"]);	
		$prevStartDate		=	mysqlDateFormat($p["hidStartDate"]);
		$selDistributor		= 	$p["selDistributor"];
		# Check Valid  Date Entry
		$validDateEntry = $distMarginRateListObj->chkValidDateEntry($startDate, $selDistributor, $distMarginRateListId);
		if (!$validDateEntry) $startDate = $p["startDate"];

		if ($distMarginRateListId!="" && $rateListName!="" && $p["startDate"]!="" && $validDateEntry) {
			$distMarginRateListRecUptd = $distMarginRateListObj->updateDistMarginRateList($rateListName, $startDate, $distMarginRateListId);

			if ($startDate!=$prevStartDate && $distMarginRateListRecUptd) {
				# Get Previous Dist Rate List Id
				$prevRateListId = $distMarginRateListObj->prevRateListId($selDistributor, $distMarginRateListId);
				# Update Prev Rate List Rec
				if ($prevRateListId) $updatePrevRateListRec = $distMarginRateListObj->updateDistributorRateListRec($prevRateListId, $startDate);
				if ($updatePrevRateListRec) {
					# Update SO records
					$updateSORecords = $changesUpdateMasterObj->getSORecords($startDate, $prevStartDate, $selDistributor);		
				}
				# Update Dist margin recs
				$changesUpdateMasterObj->updateDistributorMgnStructRecs($selDistributor);
			}
		}
	
		if ($distMarginRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDistMarginRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateDistMarginRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDistMarginRateList;
		}
		$distMarginRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distMarginRateListId	=	$p["delId_".$i];
			$distributorId		= $p["hidDistId_".$i];
			
			$isRateListUsed = $distMarginRateListObj->checkRateListUse($distMarginRateListId);
			
			if ($distMarginRateListId!="" && !$isRateListUsed) {
				$distMarginRateListRecDel = $distMarginRateListObj->deleteDistMarginRateList($distMarginRateListId, $distributorId);
			}
		}
		if ($distMarginRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelDistMarginRateList.$selection);
		} else {
			$errDel	=	$msg_failDelDistMarginRateList;
		}
		$distMarginRateListRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distributorId	=	$p["confirmId"];


			if ($distributorId!="") {
				// Checking the selected fish is link with any other process
				$distributorRecConfirm = $distMarginRateListObj->updatedistributorconfirm($distributorId);
			}

		}
		if ($distributorRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmDistMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelDistMarginRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$distributorId = $p["confirmId"];
			if ($distributorId!="") {
				#Check any entries exist				
					$distributorRecConfirm = $distMarginRateListObj->updatedistributorReleaseconfirm($distributorId);
				
			}
		}
		if ($distributorRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmDistMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelDistMarginRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	if ($g["distributorFilter"]!="") $distributorFilterId = $g["distributorFilter"];
	else $distributorFilterId = $p["distributorFilter"];	
	
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidDistributorFilterId"]!=$p["distributorFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	#List All Records
	$distMarginRateListRecords = $distMarginRateListObj->fetchAllPagingRecords($offset, $limit, $distributorFilterId);	
	$distMarginRateListRecordSize	= sizeof($distMarginRateListRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($distMarginRateListObj->fetchAllRecords($distributorFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		$distributorResultSetObj =$distributorMasterObj->fetchAllRecordsActiveDistributor();
	}

	# Filter all Distributor
	//$distributorResultSetFilterObj = $distributorMasterObj->fetchAllRecords();
	$distributorResultSetFilterObj =$distributorMasterObj->fetchAllRecordsActiveDistributor();

	
	if ($selDistributor!="") {
		# Distibutor wise rate List
		$filterDistMarginListRecs = $distMarginRateListObj->filterDistributorWiseRecords($selDistributor);
		# get Current Rate List of the Distributor
		$currentRateListId = $distMarginRateListObj->latestRateList($selDistributor);

		if ($p["copyRateList"]!="") $selRateList = $p["copyRateList"];
		else $selRateList	= $currentRateListId;		
	}

	if ($editMode)	$heading = $label_editDistMarginRateList;
	else 		$heading = $label_addDistMarginRateList;

	$ON_LOAD_PRINT_JS	= "libjs/DistMarginRateList.js";
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistMarginRateList" action="DistMarginRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<!--<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>-->
	<tr>
	  	<td height="10" align="center">
			<a href="DistMarginStructure.php" class="link1">Distributor Margin Structure</a>
		</td>
	</tr>
		<? if($err!="" ){?>
			<tr>
				<td height="10" align="center" class="err1" ><?=$err;?></td>
			</tr>
		<?}?>
	
		<tr> 
			<td align="center" class="listing-item" style="color:Maroon;" height="20">
			<?php
				if (!$distributorFilterId) {
			?>
			<strong>Latest Distributor Margin Rate List.</strong>
			<?php
				}
			?>
			</td>
		</tr>
<tr>
	<td>
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
	<tr>
		<td nowrap="true">
		<!-- Form fields start -->
		<?php	
			$bxHeader="Distributor Margin Rate List";
			include "template/boxTL.php";
		?>
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr>
		<td align="center" colspan="3">
		<table width="50%" align="center">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>							
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistMarginRateListMaster(document.frmDistMarginRateList);" <?=$disabled?> >	
											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistMarginRateListMaster(document.frmDistMarginRateList);">					
<input type="hidden" name="cmdAddNew" value="1">
						</td>

						

												<?}?>
											</tr>
											<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Start Date </td>
			<td>
				<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$readOnly?>>
				<input type="hidden" name="hidStartDate" id="hidStartDate" value="<?=$startDate?>">
			</td>
											</tr>
<? 
if($addMode==true){
?>
	<tr>
		<td nowrap class="fieldName">*Distributor</td>
		<td nowrap>
                        <select name="selDistributor" id="selDistributor" onchange="this.form.submit();">
                        <option value="">-- Select --</option>
			<?	
				while ($dr=$distributorResultSetObj->getRow()) {
					$distributorId	 = $dr[0];
					$distributorCode = stripSlash($dr[1]);
					$distributorName = stripSlash($dr[2]);	
					$selected = "";
					if ($selDistributor==$distributorId) $selected = "selected";	
			?>
                	<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
			<? 
				}
			?>
			</select>
		</td>
	</tr>
<?
	} else {
?>
	<input type="hidden" name="selDistributor" id="selDistributor" value="<?=$selDistributor?>" />
<? }?>
											<? if($addMode==true){?>
											<tr>
												<td class="fieldName" nowrap>Copy From  </td>
			<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($filterDistMarginListRecs as $dmrl) {
				$distMarginRateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);
				$array			=	explode("-",$dmrl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$distMarginRateListId) $selected = "Selected";
				?>
                      <option value="<?=$distMarginRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select></td></tr>
									<? }?>		
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistMarginRateListMaster(document.frmDistMarginRateList);" <?=$disabled?>>	
											</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistMarginRateListMaster(document.frmDistMarginRateList);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
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
	<td align="center" colspan="3">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">
			<tr>
				<td class="listing-item">Distributor:&nbsp;</td>
				<td>
					<select name="distributorFilter" onchange="this.form.submit();">
					<option value="">-- Select All --</option>		 
						<?	
							while ($dr=$distributorResultSetFilterObj->getRow()) {
								$distributorId	 = $dr[0];
								$distributorCode = stripSlash($dr[1]);
								$distributorName = stripSlash($dr[2]);	
								$selected = "";
								if ($distributorFilterId==$distributorId) $selected = "selected";	
						?>
						<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
						<? 
							}
						?>
					</select> 
				</td>
			</tr>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">							
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Margin Rate List  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDistMarginRateList.php?distributorFilter=<?=$distributorFilterId?>',700,600);"><? }?></td>
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
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<thead>
                <?
			if( sizeof($distMarginRateListRecords) > 0 ) {
				$i	=	0;
		?>
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
      				$nav.= " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\"  class=\"link1\">>></a> ";
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
                      <tr> 
                        <th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Distributor </th>
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
			foreach ($distMarginRateListRecords as $dmrl) {
				$i++;
				$distMarginRateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);
				$startDate		=	dateFormat($dmrl[2]);
				$distributorId		= $dmrl[3];
				$distributorName	= $dmrl[4];
				$active=$dmrl[5];
			?>
                      <tr <?php if ($active==0){?> bgcolor="#afddf8" <?php }?>> 
                        <td width="20">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$distMarginRateListId;?>" class="chkBox">
				<input type="hidden" name="hidDistId_<?=$i;?>" id="hidDistId_<?=$i;?>" value="<?=$distributorId;?>">
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px; text-align:center;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$distributorName?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distMarginRateListId;?>,'editId'); this.form.action='DistMarginRateList.php';"><? } ?></td>
			<? }?>


			 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$distMarginRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$distMarginRateListId;?>,'confirmId');" >
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
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistMarginRateList.php?pageNo=$page&distributorFilter=$distributorFilterId\"  class=\"link1\">>></a> ";
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
                        <td colspan="5"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
												}
											?>
			</tbody>
                    </table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDistMarginRateList.php?distributorFilter=<?=$distributorFilterId?>',700,600);"><? }?></td></tr></table></td></tr>
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
<input type="hidden" name="hidDistributorFilterId" value="<?=$distributorFilterId?>">		
		    <tr>
		      <td height="10"></td>
      </tr>
	    <tr>
		<td height="10" align="center">
			<a href="DistMarginStructure.php" class="link1">Distributor Margin Structure</a>
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
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>