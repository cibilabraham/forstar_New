<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$prodFishCutting = "FCC";
	$selection 	= "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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
		$addMode  = false;
		$editMode = false;
		$p["editId"] = "";
		$editId = "";
	}


	if ($p["selFish"]!="") $selFishId = $p["selFish"];	

	#Add a stock
	if ($p["cmdAdd"]!="") {			
		$ingMainCategory = $p["ingMainCategory"];
		$selFish	= $p["selFish"];
		$costPerKg	= $p["costPerKg"];			
		$fcRateListId	= $p["fcRateList"];
		# Creating a New Rate List
		if ($fcRateListId=="") {
			$rateListName = "FISHCUTTING"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $prodFishCutting, $pCurrentRateListId);
			if ($rateListRecIns) $fcRateListId = $manageRateListObj->latestRateList($prodFishCutting);;	
		}

		$recExist	= $productionFishCuttingObj->chkRecExist($selFish, $fcRateListId, $cRecId);
		if ($ingMainCategory!="" && $selFish!="" && $costPerKg!="" && $fcRateListId!="" && !$recExist) {

			$fishCuttingRecIns = $productionFishCuttingObj->addFishCutting($ingMainCategory, $selFish, $costPerKg, $fcRateListId);

			if ($fishCuttingRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductionFishCutting);
				$sessObj->createSession("nextPage",$url_afterAddProductionFishCutting.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductionFishCutting;
			}
			$fishCuttingRecIns = false;
		} else {
			$addMode = true;
			$err	 = $msg_failAddProductionFishCutting."<br>".$msgCodeExist;
		}
	}

	# Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$fishCuttingRecId = $p["hidFishCuttingId"];
		$ingMainCategory = $p["ingMainCategory"];
		$selFish	= $p["selFish"];	
		$costPerKg	= $p["costPerKg"];
		$fcRateListId	= $p["fcRateList"];	
		
		$recExist	= $productionFishCuttingObj->chkRecExist($selFish, $fcRateListId, $fishCuttingRecId);

		if ($fishCuttingRecId!="" && $selFish!="" && $ingMainCategory!="" && $costPerKg!="" && $fcRateListId!="" && !$recExist) {
			$fishCuttingRecUptd = $productionFishCuttingObj->updateFishCuttingRec($fishCuttingRecId, $ingMainCategory, $selFish, $costPerKg, $fcRateListId);
		}
	
		if ($fishCuttingRecUptd) {
			$p["editId"] = "";
			$editId = "";
			$editMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succProductionFishCuttingUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionFishCutting.$selection);	
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failProductionFishCuttingUpdate."<br>".$msgCodeExist;
			else $err	= $msg_failProductionFishCuttingUpdate;
		}
		$fishCuttingRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$fishCuttingRec	=	$productionFishCuttingObj->find($editId);
		$editFishCuttingId =	$fishCuttingRec[0];		
		$ingCategoryId 	= 	$fishCuttingRec[1];
		$selFishId	= 	$fishCuttingRec[2];
		$costPerKg	=	$fishCuttingRec[3];	
		$fcRateListId	= 	$fishCuttingRec[4];

		if ($p["editSelectionChange"]=='1'|| $p["ingMainCategory"]=="") {
			$ingCategoryId 	= $fishCuttingRec[1];
		} else {
			$ingCategoryId	= $p["ingMainCategory"];
		}	
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fishCuttingRecId	=	$p["delId_".$i];

			if ($fishCuttingRecId!="") {
				// Need to check the selected Category is link with any other process
				$fishCuttingRecDel = $productionFishCuttingObj->deleteFishCuttingRec($fishCuttingRecId);
			}
		}
		if ($fishCuttingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionFishCutting);
			$sessObj->createSession("nextPage",$url_afterDelProductionFishCutting.$selection);
		} else {
			$errDel	=	$msg_failDelProductionFishCutting;
		}
		$fishCuttingRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fishCuttingRecId =	$p["confirmId"];
			if ($fishCuttingRecId!="") {
				// Checking the selected fish is link with any other process
				$fishRecConfirm = $productionFishCuttingObj->updateFishcuttingconfirm($fishCuttingRecId);
			}

		}
		if ($fishRecConfirm) {
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

			$fishCuttingRecId = $p["confirmId"];
			if ($fishCuttingRecId!="") {
				#Check any entries exist
				
					$fishRecConfirm = $productionFishCuttingObj->updateFishcuttingReleaseconfirm($fishCuttingRecId);
				
			}
		}
		if ($fishRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirm);
			$sessObj->createSession("nextPage",$url_afterDelCountryMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($prodFishCutting);
	#--------------------------------------------

	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Fish Cutting
	$fishCuttingResultSetObj = $productionFishCuttingObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$fishCuttingRecordSize	= $fishCuttingResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allFishCuttingResultSetObj = $productionFishCuttingObj->fetchAllRecords($selRateList);
	$numrows	=  $allFishCuttingResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Rate List
	$fcRateListRecords = $manageRateListObj->fetchAllRecords($prodFishCutting);
	if ($addMode) $fcRateListId = $manageRateListObj->latestRateList($prodFishCutting);	

	if ($addMode || $editMode) {
		# Get Main Category Records
		//$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecords();
		$ingMainCategoryRecords = $ingMainCategoryObj->fetchAllRecordsActiveCategory();
		if ($p["ingMainCategory"]!="" && $addMode) $ingCategoryId = $p["ingMainCategory"];
		# List all Ingredients
		$ingredientResultSetObj = $ingredientMasterObj->ingredientRecFilter($subCategoryId, $ingCategoryId);
	}

	# Get Ingredient Rate Master Rate List Id
	$selIngRateListId = $ingredientRateListObj->latestRateList();

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editProductionFishCutting;
	else	       $heading	=	$label_addProductionFishCutting;

	$ON_LOAD_PRINT_JS = "libjs/ProductionFishCutting.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProductionFishCutting" action="ProductionFishCutting.php" method="post">
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
					$bxHeader = "Fish Cutting Cost Master ";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ProductionFishCutting.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionFishCutting(document.frmProductionFishCutting);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionFishCutting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionFishCutting(document.frmProductionFishCutting);">												</td>

												<?}?>
											</tr>
				<input type="hidden" name="hidFishCuttingId" value="<?=$editFishCuttingId;?>">
			<tr><TD height="10"></TD></tr>
			<tr>
			  	<td colspan="2" nowrap>
					<table width="200">
					<tr>
						<td class="fieldName" nowrap >*Category</td>
						<td>
							<select name="ingMainCategory" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
								<option value="">-- Select --</option>
								<?
								foreach ($ingMainCategoryRecords as $cr) {
									$ingMainCategoryId	= $cr[0];
									$ingCategoryName	= stripSlash($cr[1]);
									$selected = ($ingCategoryId==$ingMainCategoryId)?"selected":"";
								?>	
								<option value="<?=$ingMainCategoryId?>" <?=$selected?>><?=$ingCategoryName?></option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >*Fish</td>
					  <td>
						<select name="selFish" id="selFish">
							<option value="">-- Select --</option>
							<?php
								while($ir=$ingredientResultSetObj->getRow()) {	
									$ingredientId = $ir[0];			
									$ingredientName	= stripSlash($ir[2]);	
									$selected = ($selFishId==$ingredientId)?"selected":"";
							?>
							<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
							<?
								}
							?>
						</select>
					  </td>
				  	</tr>	
					<!--<tr>
					  <td class="fieldName" nowrap >*Fish Name</td>
					  <td>
					  <input type="text" name="fishName" size="20" value="<?=$fishName?>" /></td>
				  	</tr>					
					<tr>
					  <td class="fieldName" nowrap >*Code</td>
					  <td><input type="text" name="fishCode" size="5" id="fishCode" value="<?=$fishCode?>"></td>
					  </tr>-->
					<tr>
					  <td class="fieldName" nowrap >*Cost (in Rs.)</td>
					  <td class="listing-item">
						<input type="text" name="costPerKg" size="5" id="costPerKg" value="<?=$costPerKg?>" style="text-align:right;" autocomplete="off">&nbsp;per Kg
					  </td>
					</tr>
	<tr><TD>
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
	<input type="hidden" name="fcRateList" id="fcRateList" value="<?=$fcRateListId?>">
	</TD></tr>
		<!--<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="fcRateList">
			<?
			/*
			if (sizeof($fcRateListRecords)>0) {
				foreach ($fcRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $fcRateListId;
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
						</tr>-->
				</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="5" ></td>
					</tr>
					<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionFishCutting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionFishCutting(document.frmProductionFishCutting);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionFishCutting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionFishCutting(document.frmProductionFishCutting);">												</td>
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
		foreach ($fcRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected = ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select>
		</td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$prodFishCutting?>'"></td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Cutting Cost Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishCuttingRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionFishCutting.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($fishCuttingRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost <br/>(Per Kg)</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost/Kg <br/>Raw</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost/Kg <br/>Cleaned</th>	
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
			while ($fcr=$fishCuttingResultSetObj->getRow()) {
				$i++;
				$fishCuttingRecId 	= $fcr[0];
				$fName			= stripSlash($fcr[1]);
				$fCode			= $fcr[2];	
				$fishCuttingCost	= $fcr[3];
				$selIngredientId	= $fcr[4];
				$active=$fcr[5];
				list($rawRatePerKg, $yield, $cleanRatePerKg) = $ingredientRateMasterObj->getIngRate($selIngredientId, $selIngRateListId);
			?>
	<tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fishCuttingRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$fName?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$fCode?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$fishCuttingCost?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$rawRatePerKg?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$cleanRatePerKg?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fishCuttingRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='ProductionFishCutting.php';" ><? } ?></td>
		<? }?>
		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$fishCuttingRecId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$fishCuttingRecId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
	<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionFishCutting.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
			<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishCuttingRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionFishCutting.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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