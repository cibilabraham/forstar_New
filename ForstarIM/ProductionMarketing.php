<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;	

	$prodMarketing 	= "MC";		// Rate List Type
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
	
	#Add a stock
	if ($p["cmdAdd"]!="") {
		$mktgPositionName	= addSlash(trim($p["mktgPositionName"]));
		$mktgActual	= $p["mktgActual"];
		$mktgIdeal	= $p["mktgIdeal"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$avgCost	= $p["avgCost"];
		$mcRateListId	= $p["mcRateList"];	
		# Creating a New Rate List
		if ($mcRateListId=="") {
			$rateListName = "MARKETING"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $prodMarketing, $pCurrentRateListId);
			if ($rateListRecIns) $mcRateListId = $manageRateListObj->latestRateList($prodMarketing);;	
		}
	
		if ($mktgPositionName!="" && $mktgActual!="" && $mktgIdeal!="" && $puCost!="" && $totCost!="" && $avgCost!="" && $mcRateListId!="") {
			
			$marketingCostRecIns = $productionMarketingObj->addMarketingCost($mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $mcRateListId);

			if ($marketingCostRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductionMarketingCost);
				$sessObj->createSession("nextPage",$url_afterAddProductionMarketingCost.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductionMarketingCost;
			}
			$marketingCostRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$marketingCostRecId	= $p["hidMktgCostRecId"];
		$mktgPositionName	= addSlash(trim($p["mktgPositionName"]));
		$mktgActual	= $p["mktgActual"];
		$mktgIdeal	= $p["mktgIdeal"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$avgCost	= $p["avgCost"];
		$mcRateListId	= $p["mcRateList"];

		if ($marketingCostRecId!=""  && $mktgPositionName!="" && $mktgActual!="" && $mktgIdeal!="" && $puCost!="" && $totCost!="" && $avgCost!="" && $mcRateListId!="") {
			$mktgCostRecUptd = $productionMarketingObj->updateMarketingCost($marketingCostRecId, $mktgPositionName, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $mcRateListId);
		}
	
		if ($mktgCostRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductionMarketingCostUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionMarketingCost.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductionMarketingCostUpdate;
		}
		$mktgCostRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$mktgCostRec	=	$productionMarketingObj->find($editId);
		$editMktgCostId =	$mktgCostRec[0];
		$mktgPositionName = 	stripSlash($mktgCostRec[1]);
		$mktgActual	=	$mktgCostRec[2];
		$mktgIdeal	=	$mktgCostRec[3];
		$puCost		=	$mktgCostRec[4];
		$totCost	=	$mktgCostRec[5];
		$avgCost	=	$mktgCostRec[6];
		$mcRateListId	= 	$mktgCostRec[7];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$marketingCostRecId	=	$p["delId_".$i];
			if ($marketingCostRecId!="") {
				// Need to check the selected Category is link with any other process
				$mktgCostRecDel = $productionMarketingObj->deleteMarketingCostRec($marketingCostRecId);
			}
		}
		if ($mktgCostRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionMarketingCost);
			$sessObj->createSession("nextPage",$url_afterDelProductionMarketingCost.$selection);
		} else {
			$errDel	=	$msg_failDelProductionMarketingCost;
		}
		$mktgCostRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$marketingCostRecId	=	$p["confirmId"];
			if ($marketingCostRecId!="") {
				// Checking the selected fish is link with any other process
				$marketingRecConfirm = $productionMarketingObj->updateMarketingCostconfirm($marketingCostRecId);
			}

		}
		if ($marketingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$marketingCostRecId = $p["confirmId"];
			if ($marketingCostRecId!="") {
				#Check any entries exist
				
					$marketingRecConfirm = $productionMarketingObj->updateMarketingCostReleaseconfirm($marketingCostRecId);
				
			}
		}
		if ($marketingRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}


	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($prodMarketing);			
	#--------------------------------------------	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Man Power
	$marketingCostResultSetObj = $productionMarketingObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$marketingCostRecordSize	= $marketingCostResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allMarketingCostResultSetObj = $productionMarketingObj->fetchAllRecords($selRateList);
	$numrows	=  $allMarketingCostResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# Rate List
	$mcRateListRecords = $manageRateListObj->fetchAllRecords($prodMarketing);
	if ($addMode) $mcRateListId = $manageRateListObj->latestRateList($prodMarketing);

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editProductionMarketingCost;
	else	       $heading	=	$label_addProductionMarketingCost;

	$ON_LOAD_PRINT_JS = "libjs/ProductionMarketing.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProductionMarketing" action="ProductionMarketing.php" method="post">
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
					$bxHeader = "Marketing Cost Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="50%">
		<?php
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductionMarketing.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionMarketing(document.frmProductionMarketing);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMarketing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionMarketing(document.frmProductionMarketing);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidMktgCostRecId" value="<?=$editMktgCostId;?>">
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
			<table cellspacing="4">
				<TR>
					<TD valign="top">
					<!--<fieldset>-->
					<?php
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
						<td class="fieldName" nowrap >*Name</td>
						<td>
						<input type="text" name="mktgPositionName" size="20" value="<?=$mktgPositionName;?>" /></td>
						</tr>
						<tr>
						<td class="fieldName" nowrap >*Actual</td>
						<td><input type="text" name="mktgActual" size="5" id="mktgActual" value="<?=$mktgActual;?>" style="text-align:right;" onkeyup="prodnMktgAverageCost('mktgActual', 'puCost', 'avgCost');"></td>
						</tr>
						<tr>
						<td class="fieldName" nowrap >*Ideal</td>
						<td><input type="text" name="mktgIdeal" size="5" id="mktgIdeal" value="<?=$mktgIdeal;?>" style="text-align:right;" onkeyup="prodnMktgTotalCost('mktgIdeal', 'puCost', 'totCost');"></td>
						</tr>
					</table>
					<?php
						require("template/rbBottom.php");
					?>
					<!--</fieldset>-->
					</TD>
					<TD valign="top">&nbsp;</TD>
					<TD valign="top">
					<!--<fieldset>-->
					<?php
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
					  <td class="fieldName" nowrap >*Unit Cost</td>
					  <td><input type="text" name="puCost" size="7" id="puCost" value="<?=$puCost;?>" style="text-align:right;" onkeyup="prodnMktgTotalCost('mktgIdeal', 'puCost', 'totCost'); prodnMktgAverageCost('mktgActual', 'puCost', 'avgCost');"></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Total Cost</td>
					  <td><input type="text" name="totCost" size="7" id="totCost" value="<?=$totCost;?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Actual Cost</td>
					  <td><input type="text" name="avgCost" size="7" id="avgCost" value="<?=$avgCost;?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr><TD colspan="2">
					<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
					<input type="hidden" name="mcRateList" id="mcRateList" value="<?=$mcRateListId?>">
					</TD></tr>
					</table>
					<?php
						require("template/rbBottom.php");
					?>
					<!--</fieldset>-->
					</TD>
				</TR>
			</table>
		</td>
	</tr>
		<!--<tr>
			  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
					<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="mcRateList">
			<?
			/*
			if (sizeof($mcRateListRecords)>0) {
				foreach ($mcRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $mcRateListId;
					$selected = "";
					if ($rateListId==$mRateListId) $selected = "Selected";
			*/
			?>
                    	  <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      	<? 
			//	}
			?>
			<?
			//} else {
			?>
			 <option value="">-- Select --</option>
			<?
			//}
			?>
                                            </select></td>
						</tr>
                                       </table></td>
				  </tr>-->
						<tr>
							<td colspan="2"  height="5" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMarketing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionMarketing(document.frmProductionMarketing);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMarketing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionMarketing(document.frmProductionMarketing);">												</td>
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
				<!-- Form fields end   -->			
			</td>
		</tr>	
		<?php
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
		foreach ($mcRateListRecords as $prl) {
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
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$prodMarketing?>'"></td>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Marketing Cost Master </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$marketingCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionMarketing.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
			if ($marketingCostRecordSize) {
				$i = 0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Actual</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Ideal</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Unit Cost</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Total Cost</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Actual Cost</th>
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
			while ($mcr=$marketingCostResultSetObj->getRow()) {
				$i++;
				$marketingCostRecId 	= $mcr[0];
				$headName	= stripSlash($mcr[1]);
				$mcActualUnit	= $mcr[2];
				$mcIdealUnit	= $mcr[3];		
				$mcPuCost	= $mcr[4];
				$mcTotCost	= $mcr[5];
				$mcAvgCost	= $mcr[6]; 
				$active=$mcr[7];
		?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$marketingCostRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$headName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$mcActualUnit;?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcIdealUnit?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcPuCost?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcTotCost?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcAvgCost?></td>
		<? if($edit==true){?>
				<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$marketingCostRecId;?>,'editId');this.form.action='ProductionMarketing.php';" ><? }?></td>
		<? }?>
		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$marketingCostRecId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$marketingCostRecId;?>,'confirmId');" >
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
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionMarketing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
			<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$marketingCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionMarketing.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
		<tr>
			<td height="10"></td>
		</tr>		
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
	ensureInFrameset(document.frmProductionManPower);
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