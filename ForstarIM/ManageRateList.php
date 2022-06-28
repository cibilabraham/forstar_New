<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$prevPage 	= false;

	$mode 		= $g["mode"];
	/*
	if ($g["mode"]!="")
	else $mode = $p["hidMode"];
	*/

	$userId		= $sessObj->getValue("userId");
	$selection	= "?pageNo=".$p["pageNo"]."&functionFilter=".$p["functionFilter"]."&inIFrame=".$p["inIFrame"];
	

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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	#----------------------------------------------------------------

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") $addMode = true;

	# Setting Prev Page 
	//if ($p["prevPage"]!="") $prevPage = $p["prevPage"];

	if ($mode!="") $prevPage = true;	

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	# Resetting Values
	if ($g["selPage"]!="") $selPage = $g["selPage"]; 	
	else if ($p["selPage"]!="") $selPage	= $p["selPage"];

	if ($p["rateListName"]!="") $rateListName = $p["rateListName"];
	if ($p["startDate"]!="") $startDate = $p["startDate"];	
	
	/*
	$pageRedirection = "";
	if ($prevPage!="") {
		$pageRedirection = $mRateListUrl[$selPage];
	} else {
		$pageRedirection = $url_afterAddRateList.$selection;
	}
	*/
	

	#Insert a Record
	if ($p["cmdAdd"]!="") {	
		$rateListName	= addSlash(trim($p["rateListName"]));		
		$selDate	= mysqlDateFormat(trim($p["startDate"]));
		$copyRateList	= $p["copyRateList"];
		$selPage	= $p["selPage"];
		$pageCurrentRateListId = $p["hidCurrentRateListId"];
		# Duplication Checking
		/*
		$recExist = $manageRateListObj->checkRecExist($selDate, $selPage);
		if ($recExist) $startDate = $p["startDate"];
		*/

		$prevPage = $p["prevPage"];

		# Check valid Date
		$validDateEntry = $manageRateListObj->chkValidDateEntry($selDate, $selPage, $cId);		

		if ($rateListName!="" && $p["startDate"]!="" && $selPage!="" && $validDateEntry) {	
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $selDate, $copyRateList, $userId, $selPage, $pageCurrentRateListId);
				
			if ($rateListRecIns) {				
				$sessObj->createSession("displayMsg",$msg_succAddRateList);
				if ($prevPage!="") {
					header("location:$mRateListUrl[$selPage]");
				} else {
					$sessObj->createSession("nextPage",$url_afterAddRateList.$selection);
				}
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddRateList;
			}
			$rateListRecIns	=	false;
		} else {
			$addMode	= true;
			if (!$validDateEntry) $err = $msg_failAddRateList. $msgRateListRecExist;
			else $err = $msg_failAddRateList;
		}
	}


	# Edit 
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			=	$p["editId"];
		$editMode		=	true;		
		$rateListRec		=	$manageRateListObj->find($editId);		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);	
		$startDate		= 	dateFormat($rateListRec[2]);
		$selPage		= 	$rateListRec[3];

		/*
		$isRateListUsed = $manageRateListObj->checkRateListUse($editRateListId, $selPage);
		$readOnly = "";	
		if ($isRateListUsed) $readOnly = "readonly";
		*/
		$funtionDisabled = "Disabled";
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {		
		$rateListId	= $p["hidRateListId"];		
		$rateListName	= addSlash(trim($p["rateListName"]));		
		$selDate	= mysqlDateFormat($p["startDate"]);		
		$selPage	= trim($p["hidSelPage"]);
		# Check valid Date
		$validDateEntry = $manageRateListObj->chkValidDateEntry($selDate, $selPage, $rateListId);

		if ($rateListId!="" && $rateListName!="" && $validDateEntry) {
			$rateListRecUptd = $manageRateListObj->updateRateList($rateListName, $selDate, $rateListId);
		}
	
		if ($rateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateRateList.$selection);
		} else {
			$editMode	=	true;
			if (!$validDateEntry) $err = $msg_failUpdateRateList. $msgRateListRecExist;
			else $err	=	$msg_failUpdateRateList;
		}
		$rateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rateListId	= $p["delId_".$i];
			$selPage	= $p["hidPageType_".$i];
			
			$isRateListUsed = $manageRateListObj->checkRateListUse($rateListId, $selPage);
			
			if ($rateListId!="" && !$isRateListUsed) {
				$distMarginRateListRecDel = $manageRateListObj->deleteDistMarginRateList($rateListId, $selPage);
			}
		}
		if ($distMarginRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRateList);
			$sessObj->createSession("nextPage",$url_afterDelRateList.$selection);
		} else {
			$errDel	=	$msg_failDelRateList;
		}
		$distMarginRateListRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rateListId	=	$p["confirmId"];


			if ($rateListId!="") {
				// Checking the selected fish is link with any other process
				$distMarginRateListRecConfirm = $manageRateListObj->updateRateListconfirm($rateListId);
			}

		}
		if ($distMarginRateListRecConfirm) {   
			$sessObj->createSession("displayMsg",$msg_succConfirmMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$rateListId = $p["confirmId"];

			if ($rateListId!="") {
				#Check any entries exist
				
					$distMarginRateListRecConfirm = $manageRateListObj->updateRateListReleaseconfirm($rateListId);
				
			}
		}
		if ($distMarginRateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmMarginRateList);
			$sessObj->createSession("nextPage",$url_afterDelRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	if ($g["functionFilter"]!="") $functionFilterId = $g["functionFilter"];
	else $functionFilterId = $p["functionFilter"];	

	if ($mode!="") $functionFilterId = $selPage;

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidFunctionFilterId"]!=$p["functionFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	#List All Records
	$rateListRecords 	= $manageRateListObj->fetchAllPagingRecords($offset, $limit, $functionFilterId);	
	$rateListRecordSize	= sizeof($rateListRecords);

	## -------------- Pagination Settings II -------------------		
	$numrows	=  sizeof($manageRateListObj->fetchAllRecords($functionFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($selPage!="") {
		# Distibutor wise rate List
		$filterRateListRecs = $manageRateListObj->filterRateListRecords($selPage);

		# get Current Rate List of the Distributor
		$currentRateListId = $manageRateListObj->latestRateList($selPage);

		if ($p["copyRateList"]!="") $selRateList = $p["copyRateList"];
		else $selRateList	= $currentRateListId;		
	}

	
	# All Page $masterRateListPages Coming from CONFIG.PHP
	//print_r($masterRateListPages);

	$ON_LOAD_PRINT_JS = "libjs/ManageRateList.js";

	if ($editMode)	$heading = $label_editRateList;
	else 		$heading = $label_addRateList;
		
	# Include Template [topLeftNav.php]
	if ($g["inIFrame"]!="") $iFrameVal = $g["inIFrame"];
	else $iFrameVal	= $p["inIFrame"]; // N - Not in Iframe

	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmManageRateList" action="ManageRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<tr> 
			<td align="center" class="listing-item" style="color:Maroon;" height="20">
			<?php
				if (!$functionFilterId) {
			?>
			<strong>Latest Function Wise Rate List.</strong>
			<?php
				}
			?>
			</td>
		</tr>	
		<? if($err!="" ){?>	
			<tr>
				<td height="10" align="center" class="err1" ><?=$err;?></td>
			</tr>
		<?}?>
<!-- S 1 -->
<tr>
	<td>
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
	<tr>
		<td nowrap="true">
		<!-- Form fields start -->
		<?php	
			$bxHeader="Manage Rate List Master";
			include "template/boxTL.php";
		?>
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr>
		<td align="center" colspan="3">
		<table width="50%" align="center">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageRateListMaster(document.frmManageRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageRateListMaster(document.frmManageRateList);">					
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
<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" autoComplete="off" <?=$readOnly?> >
</td>
											</tr>
	<tr>
		<td nowrap class="fieldName">*Function</td>
		<td nowrap>
                        <select name="selPage" id="selPage" onchange="this.form.submit();" style="width:160px;" <?=$funtionDisabled?>>
                        <option value="">-- Select --</option>
			<?	
				
				foreach ($masterRateListPages as $pageType=>$functionName) {			
					$selected = "";
					if ($selPage==$pageType) $selected = "selected";	
			?>
                	<option value="<?=$pageType?>" <?=$selected?>><?=$functionName?></option>
			<? 
				}
			?>
			</select>
			<input type="hidden" name="hidSelPage" value="<?=$selPage?>">
		</td>
	</tr>
		<? if($addMode==true){?>
		<tr>
		<td class="fieldName" nowrap>Copy From</td>
			<td>
		      <select name="copyRateList" id="copyRateList" style="width:160px;" title="Click here if you want to copy all data from the Existing Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($filterRateListRecs as $dmrl) {
				$rateListId	=	$dmrl[0];
				$rateListName		=	stripSlash($dmrl[1]);
				$array			=	explode("-",$dmrl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$rateListId) $selected = "Selected";
				?>
                      <option value="<?=$rateListId?>" <?=$selected?>><?=$displayRateList?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageRateListMaster(document.frmManageRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageRateListMaster(document.frmManageRateList);">												</td>

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
		<?php
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
				<td class="listing-item">Function:&nbsp;</td>
				<td>
				<select name="functionFilter" id="functionFilter" onchange="this.form.submit();">
				<option value="">--Select All--</option>		 
					<?	
						foreach ($masterRateListPages as $pageType=>$functionName) {
							$selected = "";
							if ($functionFilterId==$pageType) $selected = "selected";	
					?>
					<option value="<?=$pageType?>" <?=$selected?>><?=$functionName?></option>
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
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Manage Rate List Master  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageRateList.php?functionFilter=<?=$functionFilterId?>',700,600);"><? }?></td>
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
                <?php
			if( sizeof($rateListRecords) > 0 ) {
				$i	=	0;
		?>
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
      				$nav.= " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\"  class=\"link1\">>></a> ";
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
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Function </th>
			<? if($edit==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
                      </tr>
	</thead>
	<tbody>
                      <?php
			$functionName = "";
			foreach ($rateListRecords as $rl) {
				$i++;
				$rateListId	= $rl[0];
				$rateListName	= stripSlash($rl[1]);				
				$startDate	= dateFormat($rl[2]);
				$pageType	= $rl[3];
				$functionName	= $masterRateListPages[$pageType];	
				$active=$rl[4];
			?>
                      <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
                        <td width="20">
				<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rateListId;?>" class="chkBox">
				<input type="hidden" name="hidPageType_<?=$i;?>" id="hidPageType_<?=$i;?>" value="<?=$pageType;?>">
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px; text-align:center;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$functionName?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$rateListId;?>,'editId'); this.form.action='ManageRateList.php';"><? } ?></td>
			<? }?>

			 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$rateListId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$rateListId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageRateList.php?pageNo=$page&functionFilter=$functionFilterId\"  class=\"link1\">>></a> ";
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
                      <?php
				}
			?>
		</tbody>
                    </table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageRateList.php?functionFilter=<?=$functionFilterId?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">	
<input type="hidden" name="hidFunctionFilterId" value="<?=$functionFilterId?>">	
<input type="hidden" name="prevPage" id="prevPage" value="<?=$prevPage?>">	

		    <tr>
		      <td height="10"></td>
      </tr>
	<!--<tr>
		<td height="10" align="center">
			<a href="DistMarginStructure.php" class="link1">Distributor Margin Structure</a>
		</td>
	</tr>-->
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
	<? 
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
	ensureInFrameset(document.frmManageRateList);
	//-->
	</script>
<?php 
	}
?>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>