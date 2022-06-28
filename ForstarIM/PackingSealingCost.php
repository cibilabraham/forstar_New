<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$packingSealingCost = "PSC";
	$selection 	= "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"];

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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$itemName	= addSlash(trim($p["itemName"]));
		$itemCode	= trim($p["itemCode"]);		
		$costPerItem	= $p["costPerItem"];			
		$pscRateListId  = $p["pscRateList"];
		# Creating a New Rate List
		if ($pscRateListId=="") {
			$rateListName = "PKGLABOUR"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $packingSealingCost, $pCurrentRateListId);
			if ($rateListRecIns) $pscRateListId = $manageRateListObj->latestRateList($packingSealingCost);	
		}

		$recExist	= $packingSealingCostObj->chkRecExist($itemCode, $pscRateListId, $cRecId);

		if ($itemName!="" && $itemCode!="" && $costPerItem!="" && $pscRateListId!="" && !$recExist) {

			$packingSealingCostRecIns = $packingSealingCostObj->addPackingSealingCost($itemName, $itemCode, $costPerItem, $pscRateListId);

			if ($packingSealingCostRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingSealingCost);
				$sessObj->createSession("nextPage",$url_afterAddPackingSealingCost.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPackingSealingCost;
			}
			$packingSealingCostRecIns = false;
		} else {
			$addMode = true;
			$err	 = $msg_failAddPackingSealingCost."<br>".$msgCodeExist;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$packingSealingCostRecId = $p["hidPackingSealingCostId"];
		$itemName	= addSlash(trim($p["itemName"]));
		$itemCode	= trim($p["itemCode"]);		
		$costPerItem	= $p["costPerItem"];	
		$pscRateListId  = $p["pscRateList"];

		$recExist	= $packingSealingCostObj->chkRecExist($itemCode, $pscRateListId, $packingSealingCostRecId);	

		if ($packingSealingCostRecId!="" && $itemName!="" && $itemCode!="" && $costPerItem!="" && $pscRateListId!="" && !$recExist) {
			$packingSealingCostRecUptd = $packingSealingCostObj->updatePackingSealingCostRec($packingSealingCostRecId, $itemName, $itemCode, $costPerItem, $pscRateListId);
		}
	
		if ($packingSealingCostRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingSealingCostUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingSealingCost.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failPackingSealingCostUpdate."<br>".$msgCodeExist;
			else $err		=	$msg_failPackingSealingCostUpdate;
		}
		$packingSealingCostRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$packingSealingCostRec	=	$packingSealingCostObj->find($editId);
		$editPackingSealingCostId =	$packingSealingCostRec[0];
		$itemName	=	stripSlash($packingSealingCostRec[1]);
		$itemCode	=	$packingSealingCostRec[2];
		$costPerItem	=	$packingSealingCostRec[3];
		$pscRateListId	= 	$packingSealingCostRec[4];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingSealingCostRecId	=	$p["delId_".$i];

			if ($packingSealingCostRecId!="") {
				// Need to check the selected Category is link with any other process
				$packingSealingCostRecDel = $packingSealingCostObj->deletePackingSealingCostRec($packingSealingCostRecId);
			}
		}
		if ($packingSealingCostRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingSealingCost);
			$sessObj->createSession("nextPage",$url_afterDelPackingSealingCost.$selection);
		} else {
			$errDel	=	$msg_failDelPackingSealingCost;
		}
		$packingSealingCostRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$packingSealingCostRecId =	$p["confirmId"];
			if ($packingSealingCostRecId!="") {
				// Checking the selected fish is link with any other process
				$packingSealingCostRecConfirm = $packingSealingCostObj->updatePackingSealingconfirm($packingSealingCostRecId);
			}

		}
		if ($packingSealingCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$packingSealingCostRecId = $p["confirmId"];
			if ($packingSealingCostRecId!="") {
				#Check any entries exist
				
					$packingSealingCostRecConfirm = $packingSealingCostObj->updatePackingSealingReleaseconfirm($packingSealingCostRecId);
				
			}
		}
		if ($packingSealingCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}


	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($packingSealingCost);			
	#--------------------------------------------

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Sealing Cost
	$packingSealingCostResultSetObj = $packingSealingCostObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$packingSealingCostRecordSize	= $packingSealingCostResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allPackingSealingCostResultSetObj = $packingSealingCostObj->fetchAllRecords($selRateList);
	$numrows	=  $allPackingSealingCostResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Rate List
	$pscRateListRecords = $manageRateListObj->fetchAllRecords($packingSealingCost);
	if ($addMode) $pscRateListId = $manageRateListObj->latestRateList($packingSealingCost);

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editPackingSealingCost;
	else	       $heading	=	$label_addPackingSealingCost;

	$ON_LOAD_PRINT_JS = "libjs/PackingSealingCost.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmPackingSealingCost" action="PackingSealingCost.php" method="post">
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
					$bxHeader = "Packing Sealing Cost Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="35%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('PackingSealingCost.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingSealingCost(document.frmPackingSealingCost);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingSealingCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingSealingCost(document.frmPackingSealingCost);">												</td>

												<?}?>
											</tr>
				<input type="hidden" name="hidPackingSealingCostId" value="<?=$editPackingSealingCostId;?>" />
			<tr><TD height="10"></TD></tr>
			<tr>
				<td colspan="2" nowrap align="center">
					<table width="200">
					<tr>
					  <td class="fieldName" nowrap >*Sealing Item Name</td>
					  <td>
					<input type="text" name="itemName" size="20" value="<?=$itemName?>" autocomplete="off"/></td>
				  	</tr>					
					<tr>
					  <td class="fieldName" nowrap >*Code</td>
					  <td><input type="text" name="itemCode" size="5" id="itemCode" value="<?=$itemCode?>" autocomplete="off"></td>
					  </tr>
					<tr>
					  <td class="fieldName" nowrap >*Cost (in Rs.)</td>
					  <td class="listing-item"><input type="text" name="costPerItem" size="5" id="costPerItem" value="<?=$costPerItem?>" style="text-align:right;" autocomplete="off">&nbsp; per Item</td>
					</tr>
	<tr><TD colspan="2">
			<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
			<input type="hidden" name="pscRateList" id="pscRateList" value="<?=$pscRateListId?>">
	</TD></tr>
					<!--<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="pscRateList">
			<?
			/*
			if (sizeof($pscRateListRecords)>0) {
				foreach ($pscRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $pscRateListId;
					$selected = "";
					if ($rateListId==$mRateListId) $selected = "Selected";
			*/
			?>
                    	  <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      	<? 
				//}
			?>
			<?
			//} else {
			?>
			 <option value="">-- Select --</option>
			<?
			//}
			?>
                                            </select></td>
						</tr>-->
				</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="5" ></td>
					</tr>
					<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingSealingCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePackingSealingCost(document.frmPackingSealingCost);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingSealingCost.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingSealingCost(document.frmPackingSealingCost);">												</td>
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
				<td height="10" align="center" ></td>
			</tr>
<tr>
	<td colspan="3" align="center">
		<table width="30%">
		<TR><TD>
		<?php			
			$entryHead = "";
			require("template/rbTop.php");
		?>
		<table cellpadding="4" cellspacing="0">
		<tr>
		<td nowrap="nowrap" style="padding:5px;">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?php
		foreach ($pscRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected = ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$packingSealingCost?>'"></td>
		<? }?>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Sealing Cost Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingSealingCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingSealingCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px" >
		<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($packingSealingCostRecordSize) {
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
      				$nav.= " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Code</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost (Per Kg)</th>	
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	while ($pscr=$packingSealingCostResultSetObj->getRow()) {
		$i++;
		$packingSealingCostRecId 	= $pscr[0];
		$itemName	= stripSlash($pscr[1]);
		$itemCode	= $pscr[2];	
		$itemCost	= $pscr[3];
		$active=$pscr[4];
		
	?>
	<tr  <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$packingSealingCostRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$itemName?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$itemCode?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$itemCost?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingSealingCostRecId;?>,'editId'); this.form.action='PackingSealingCost.php';" ><? } ?></td>
		<? }?>


<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$packingSealingCostRecId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$packingSealingCostRecId;?>,'confirmId');" >
			<?php } ?>
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
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PackingSealingCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingSealingCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingSealingCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
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
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
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
	ensureInFrameset(document.frmPackingSealingCost);
	//-->
	</script>
<? 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>
