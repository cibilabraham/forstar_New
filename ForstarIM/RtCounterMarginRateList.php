<?
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$mode		=	$g["mode"];
	$userId		= $sessObj->getValue("userId");

	$selection	= "?pageNo=".$p["pageNo"]."&rtCounterFilter=".$p["rtCounterFilter"];

	#------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add = true;
	if ($accesscontrolObj->canEdit()) $edit = true;
	if ($accesscontrolObj->canDel()) $del = true;
	if ($accesscontrolObj->canPrint()) $print = true;
	if ($accesscontrolObj->canConfirm()) $confirm = true;	
	#----------------------------------------------------------

	# Resetting Values	
	if ($p["rateListName"]!="") $rateListName = $p["rateListName"];
	if ($p["startDate"]!="") $startDate = $p["startDate"];	
	if ($p["selRetailCounter"]!="") $selRetailCounter	= $p["selRetailCounter"];

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	#Insert a Record
	if ($p["cmdAdd"]!="") {	
		$rateListName	= addSlash(trim($p["rateListName"]));		
		$startDate	= mysqlDateFormat($p["startDate"]);
		$copyRateList	= $p["copyRateList"];
		$selRetailCounter	= $p["selRetailCounter"];

		# Duplication Checking
		$recExist = $rtCountMarginRateListObj->checkRecExist($startDate, $selRetailCounter, $cId);
		if ($recExist) $startDate = $p["startDate"];

		if ($rateListName!="" && $p["startDate"]!="" && $selRetailCounter!="" && !$recExist) {
			$rtCounterMarginRateListRecIns = $rtCountMarginRateListObj->addRtCounterMarginRateList($rateListName, $startDate, $copyRateList, $userId, $selRetailCounter);
				if ($rtCounterMarginRateListRecIns) {
					$sessObj->createSession("displayMsg",$msg_succAddRtCountMarginRateList);
					$sessObj->createSession("nextPage",$url_afterAddRtCountMarginRateList.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddRtCountMarginRateList;
				}
				$rtCounterMarginRateListRecIns	=	false;
		}
	}

	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;		
		$rateListRec		=	$rtCountMarginRateListObj->find($editId);		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);		
		$startDate		= dateFormat($rateListRec[2]);
		$selRetailCounter	= 	$rateListRec[3];
		# Checking Rate List used in margin defining screen
		$rateListUsed = $rtCountMarginRateListObj->checkRateListUse($editRateListId, $selRetailCounter);
		$readOnly	= "";
		$displayStyle	= "";
		$disableField = "";
		if ($rateListUsed) {
			$readOnly = "readOnly";
			$displayStyle	  = "style=\"border:none\"";
			$disableField = "disabled";
		}
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$rtCounterMarginRateListId	=	$p["hidRateListId"];		
		$rateListName		=	addSlash(trim($p["rateListName"]));
		$dateS			=	explode("/",$p["startDate"]);
		$startDate		=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
		
		if ($rtCounterMarginRateListId!="" && $rateListName!="") {
			$rtCounterMarginRateListRecUptd = $rtCountMarginRateListObj->updateRtCounterMarginRateList($rateListName, $startDate, $rtCounterMarginRateListId);
		}
	
		if ($rtCounterMarginRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateRtCountMarginRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateRtCountMarginRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateRtCountMarginRateList;
		}
		$rtCounterMarginRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rtCounterMarginRateListId	= $p["delId_".$i];
			$rtCounterId			= $p["rtCounterId_".$i];
			
			if ($rtCounterMarginRateListId!="") {
				# Checking Rate List used any where
				$rateListUsed = $rtCountMarginRateListObj->checkRateListUse($rtCounterMarginRateListId, $rtCounterId);
				if (!$rateListUsed) {
					$rtCounterMarginRateListRecDel = $rtCountMarginRateListObj->deleteRtCounterMarginRateList($rtCounterMarginRateListId, $rtCounterId);
				}
			}
		}
		if ($rtCounterMarginRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRtCountMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelRtCountMarginRateList.$selection);
		} else {
			if ($rateListUsed) $errDel	= $msg_failDelUsedRtCtMarginRateList;
			else $errDel	= $msg_failDelRtCountMarginRateList;
			
		}
		$rtCounterMarginRateListRecDel	=	false;
	}



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rtCounterId	=	$p["confirmId"];
			if ($rtCounterId!="") {
				// Checking the selected fish is link with any other process
				$rtCounterRecConfirm = $rtCountMarginRateListObj->updateRtCounterMarginRateListconfirm($rtCounterId);
			}

		}
		if ($rtCounterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRtCountMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelRtCountMarginRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$rtCounterId = $p["confirmId"];
			if ($rtCounterId!="") {
				#Check any entries exist
				
					$rtCounterRecConfirm = $rtCountMarginRateListObj->updateRtCounterMarginRateListReleaseconfirm($rtCounterId);
				
			}
		}
		if ($rtCounterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRtCountMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelRtCountMarginRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	if ($g["rtCounterFilter"]!="") $rtCounterFilterId = $g["rtCounterFilter"];
	else $rtCounterFilterId = $p["rtCounterFilter"];	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidRtCounterFilterId"]!=$p["rtCounterFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}


	#List All Records
	$rtCounterMarginRateListRecords = $rtCountMarginRateListObj->fetchAllPagingRecords($offset, $limit, $rtCounterFilterId);	
	$rtCounterMarginRateListRecordSize	= sizeof($rtCounterMarginRateListRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($rtCountMarginRateListObj->fetchAllRecords($rtCounterFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	#List All Records
	//$rtCounterMarginRateListRecords		= $rtCountMarginRateListObj->fetchAllRecords($rtCounterFilterId);
	//$rtCounterMarginRateListRecordSize	= sizeof($rtCounterMarginRateListRecords);



	if ($addMode || $editMode) {
		#List all Retail Counter
		//$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
		$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	}
	
	#List all Retail Counter (For Listing)
	$rtCounterFilterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');

	if ($selRetailCounter!="") {
		# RT CT wise rate List
		$filterRtCtMarginListRecs = $rtCountMarginRateListObj->filterRtCounterWiseRecords($selRetailCounter);
		# get Current Rate List of the RT CT
		$currentRateListId = $rtCountMarginRateListObj->latestRateList($selRetailCounter);

		if ($p["copyRateList"]!="") $selRateList = $p["copyRateList"];
		else $selRateList	= $currentRateListId;		
	}

	if ($editMode)	$heading = $label_editRtCountMarginRateList;
	else 		$heading = $label_addRtCountMarginRateList;

	$ON_LOAD_PRINT_JS	= "libjs/RtCounterMarginRateList.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRtCounterMarginRateList" action="RtCounterMarginRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="65%">
	<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>
	<tr>
	  <td height="10" align="center"><a href="RtCounterMarginStructure.php" class="link1"> Retail Counter Margin Structure </a></td>
	</tr>
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!=""){?><?=$err;?><?}?></td>
	</tr>
		<?php
			if ($editMode || $addMode) {
		?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRtCounterMarginRateList(document.frmRtCounterMarginRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRtCounterMarginRateList(document.frmRtCounterMarginRateList);">	
<input type="hidden" name="cmdAddNew" value="1">											</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Start Date </td>
												<td>
<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$readOnly?> <?=$displayStyle?>>
</td>
		</tr>
	<tr>
		<td nowrap class="fieldName">*Retail Counter</td>
		<td nowrap>
                         <select name="selRetailCounter" id="selRetailCounter" onchange="this.form.submit();" <?=$disableField?>>
                         <option value="">-- Select --</option>
					<?	
					while ($rc=$retailCounterResultSetObj->getRow()) {
						$retailCounterId	= $rc[0];
						$retailCounterCode 	= stripSlash($rc[1]);
						$retailCounterName 	= stripSlash($rc[2]);
						
						$selected = "";
						if ($selRetailCounter==$retailCounterId) $selected = "selected";	
					?>
                            		<option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
					<? }?>
					</select>
					</td></tr>
	<? if($addMode==true && sizeof($filterRtCtMarginListRecs)>0){?>
	<tr>
		<td class="fieldName" nowrap>Copy From  </td>
		<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($filterRtCtMarginListRecs as $dmrl) {
				$rtCounterMarginRateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);				
				$startDate		=	dateFormat($dmrl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$rtCounterMarginRateListId) $selected = "Selected";
				?>
                      <option value="<?=$rtCounterMarginRateListId?>" <?=$selected?>><?=$displayRateList?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRtCounterMarginRateList(document.frmRtCounterMarginRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RtCounterMarginRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRtCounterMarginRateList(document.frmRtCounterMarginRateList);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true" >&nbsp;Retail Counter Margin Rate List  </td>
		<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
	<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item" nowrap="true">Retail Counter:&nbsp;</td>
                <td>
		<select name="rtCounterFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>		 
			<?	
			while ($rc=$rtCounterFilterResultSetObj->getRow()) {
				$retailCounterId	= $rc[0];
				$retailCounterCode 	= stripSlash($rc[1]);
				$retailCounterName 	= stripSlash($rc[2]);	
				$selected = "";
				if ($rtCounterFilterId==$retailCounterId) $selected = "selected";	
			?>
                	<option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
			<? 
				}
			?>
		
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
          <td>&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rtCounterMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRtCounterMarginRateList.php',700,600);"><? }?></td>
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
									<td colspan="2" >
	<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
         <?
		if (sizeof($rtCounterMarginRateListRecords)>0) {
			$i	=	0;
	?>
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\"  class=\"link1\">>></a> ";
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
                      <tr  bgcolor="#f2f2f2"  > 
                        <td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
                        <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</td>
                        <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </td>
			 <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Retail Counter</td>
			<? if($edit==true){?>
                        <td class="listing-head" width="45"></td>
			<? }?>
			<? if($confirm==true){?>
                        <td class="listing-head" width="45"></td>
			<? }?>
                      </tr>
                      <?
			foreach ($rtCounterMarginRateListRecords as $dmrl) {
				$i++;
				$rtCounterMarginRateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);				
				$startDate		=	dateFormat($dmrl[2]);
				$selRtCtId		= $dmrl[3];
				$retailCounterName	= $dmrl[4];
				$active=$dmrl[5];
				$existingrecords=$dmrl[6];
			?>
                      <tr  <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?>bgcolor="WHITE" <?php }?> > 
                        <td width="20">
				<?php 
				if($existingrecords==0){
				?>
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rtCounterMarginRateListId;?>" class="chkBox">
				<?php 
				}
				?>
				<input type="hidden" name="rtCounterId_<?=$i;?>" id="rtCounterId_<?=$i;?>" value="<?=$selRtCtId;?>">	
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$retailCounterName?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$rtCounterMarginRateListId;?>,'editId'); this.form.action='RtCounterMarginRateList.php';"><? } ?></td>
			<? }?>

			<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$rtCounterMarginRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$rtCounterMarginRateListId;?>,'confirmId');" >
			<?php
//			} 
}?>
			<? }?>
			
                      </tr>
                      <?
			}
			?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RtCounterMarginRateList.php?pageNo=$page&rtCounterFilter=$rtCounterFilterId\"  class=\"link1\">>></a> ";
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
                      <tr bgcolor="white"> 
                        <td colspan="4"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
												}
											?>
                    </table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rtCounterMarginRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintRtCounterMarginRateList.php',700,600);"><? }?></td></tr></table></td></tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		    <tr>
		      <td height="10"></td>
      </tr>
	<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">
	<input type="hidden" name="rateListRecSize" id="rateListRecSize" value="<?=sizeof($filterRtCtMarginListRecs)?>">
	<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
	<input type="hidden" name="hidRtCounterFilterId" value="<?=$rtCounterFilterId?>">	
	<tr>
	  <td height="10" align="center"><a href="RtCounterMarginStructure.php" class="link1"> Retail Counter Margin Structure </a></td>
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