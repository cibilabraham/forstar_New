<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	
	$currentDollarValue	=	"";
	$dollarDescr		=	"";
	$currencyValue		=	"";
	$description		=	"";
	$editUSDId			=	"";
	$USDId				=	"";
	
	$editMode		=	false;
	$addMode		=	false;
	$selection 		=	"?pageNo=".$p["pageNo"];
	
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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Add New	
	if ($p["cmdAddNew"]!="") $addMode	=	true;

	/*
	# Update
	if ($p["cmdSaveChange"]!="") {		
		$USDId		=	$p["hidUSDId"];
		$currencyValue	=	$p["currencyValue"];
		$description	=	addSlash($p["description"]);
						
		if ($currencyValue!="" && $USDId!="") {
			$USDRecUptd = $usdvalueObj->updateUSDValue($USDId,$currencyValue,$description);
		} else {
			$USDRecUptd	=	$usdvalueObj->addUSDValue($currencyValue,$description);		
		}
	
		if($USDRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUSDUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUSDUpdate;
		}
		$USDRecUptd		=	false;
	}
	
	# Edit 
		$USDRec				=	$usdvalueObj->find();
		$editUSDId			=	$USDRec[0];
		$currentDollarValue		=	$USDRec[1];
		$dollarDescr			=	stripSlash($USDRec[2]);
*/
// ==============================================================================================================================================================

	# Add
	if ($p["cmdAdd"]!="") {
	
		$currencyCode	=	trim($p["currencyCode"]);
		$currencyValue	=	trim($p["currencyValue"]);
		$description	=	addSlash(trim($p["description"]));
		
		if ($currencyCode!="" && $currencyValue) {
			$cyRateListId = "";
			
			$currencyExist = $usdvalueObj->chkCurrencyExist($currencyCode);

			if (!$currencyExist) {
				
				if ($p["startDate"] != "") {
					$startDate = mysqlDateFormat($p["startDate"]);
					$rateListName = $currencyCode." - ".date("dMy", strtotime($startDate));
				
					$cyRateListRecIns = $usdvalueObj->addCurrencyRateList($rateListName, $startDate, $userId, $currencyValue, $description);
				
					if ($cyRateListRecIns) $cyRateListId = $usdvalueObj->insertedCYLatestRateList();
				}

				if ($cyRateListId!="" && $cyRateListId!=0) {
						$currencyRecIns	= $usdvalueObj->addCurrency($currencyCode, $currencyValue, $description, $cyRateListId);
						# Last CY Id
						$cyLatestId		= $databaseConnect->getLastInsertedId();
						$updateRateListRec = $usdvalueObj->updateCurrencyRateList($cyRateListId, $cyLatestId, $currencyValue, $description);
				}
			}

			if ($currencyRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddCurrency);
				$sessObj->createSession("nextPage",$url_afterAddCurrency.$selection);
			} else {
				$addMode		=	true;
				if ($currencyExist) $err = $msg_failAddCurrency.$msgCyCodeDuplicate."<br>The currency you have entered is already exist.";
				else $err = $msg_failAddCurrency;
			}
			$currencyRecIns	=	false;
		}
	}
	
	# Edit 	
	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$CYRLEditId		=	$p["editCYRLId"];
		$editMode		=	true;
		$currencyRec		=	$usdvalueObj->findCY($editId);
		
		$editCurrencyId		=	$currencyRec[0];
		$currencyCode		=	$currencyRec[1];
		$currencyValue		=	$currencyRec[2];
		$description		=	stripSlash($currencyRec[3]);
		$cyValDisplayMsg	=	"1 ".$currencyCode." In INR";
		$currencyRLId		=	($CYRLEditId!=0)?$CYRLEditId:$currencyRec[4];

		$selCYRL = $usdvalueObj->cyRLRec($currencyRLId);
		$selStartDate	= $selCYRL[2];
		$endDate		= $selCYRL[3];
		$readOnly   = ($endDate!='0000-00-00' && $endDate!="")?"readonly":"";
		$disabled   = ($endDate!='0000-00-00' && $endDate!="")?"disabled='true'":"";

		//$cyRLRecs = $usdvalueObj->getCYRLRecs($editCurrencyId);
	}


	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$currencyId	=	$p["hidCurrencyId"];	
		
		$currencyCode	=	trim($p["currencyCode"]);
		$currencyValue	=	trim($p["currencyValue"]);
		$description	=	addSlash(trim($p["description"]));

		$hidCurrencyCode	= $p["hidCurrencyCode"];
		$hidCurrencyValue	= $p["hidCurrencyValue"];
		$editCYRLId			= $p["hidCYRLId"];

		if (($currencyCode!=$hidCurrencyCode || $currencyValue!=$hidCurrencyValue) && $currencyValue!="" && $editCYRLId!="") {
			$effectType = $p["effectType"];
			$sDate = mysqlDateFormat($p["sDate"]);

			# Future
			if ($effectType == 'F') {
				#Check valid rate list
				$recExist = $usdvalueObj->chkValidRateListDate($sDate, $editCYRLId, $currencyId);
				if (!$recExist) {
					$rateListName = $currencyCode." - ".date("dMy", strtotime($sDate));
					$cyRLRecIns = $usdvalueObj->addCurrencyRateList($rateListName, $sDate, $userId, $currencyValue, $description);
					if ($cyRLRecIns) {
						$cyRateListId = $usdvalueObj->insertedCYLatestRateList();
						$updatePrevRateListRec = $usdvalueObj->updateCYRateListRec($editCYRLId, $sDate);

						if ($cyRateListId!="" && $cyRateListId!=0) {
								//$currencyRecIns	= $usdvalueObj->addCurrency($currencyCode, $currencyValue, $description, $cyRateListId);
								# Last CY Id
								//$cyLatestId		= $databaseConnect->getLastInsertedId();
								$updateRateListRec = $usdvalueObj->updateCurrencyRateList($cyRateListId, $currencyId, $currencyValue, $description);
								//$currencyRecUptd = true;
								$currencyRecUptd	= $usdvalueObj->updateCurrency($currencyId, $currencyCode, $currencyValue, $description, $cyRateListId);
						}
					}
				} else {
					$errMsg = "Please select a valid date.";

				}
			// Update Present
			} else {
				if ($currencyId!="" && $currencyValue!="" ) {
					$currencyRecUptd	= $usdvalueObj->updateCurrency($currencyId, $currencyCode, $currencyValue, $description, $editCYRLId);
					$updateRateListRec = $usdvalueObj->updateCurrencyRateList($editCYRLId, $currencyId, $currencyValue, $description);
				}
			}
		// If No Change
		} else {
				$startDate = mysqlDateFormat($p["startDate"]);
				$hidStartDate = mysqlDateFormat($p["hidStartDate"]);
				if ($p["hidStartDate"] != "" && $p["startDate"] != $p["hidStartDate"] && $editCYRLId != "") {
					#Check valid rate list
					$recExist = $usdvalueObj->chkValidRateListDate($startDate, $editCYRLId, $currencyId);
					if (!$recExist) {
						$updateRateListRec = $usdvalueObj->updateRateListRec($editCYRLId, $startDate);
						$currencyRecUptd = true;
					} else {
						$currencyRecUptd = false;	
						$errMsg = "Please select a valid date.";
					}
				}
				
				if ($currencyId!="" && $currencyValue!="" ) {
					$currencyUptd	= $usdvalueObj->updateCurrency($currencyId, $currencyCode, $currencyValue, $description, $editCYRLId);
					$updateRateListRec = $usdvalueObj->updateCurrencyRateList($editCYRLId, $currencyId, $currencyValue, $description);
				}
		}

	
		if ($currencyRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateCurrency);
			$sessObj->createSession("nextPage",$url_afterUpdateCurrency.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateCurrency;
			if ($errMsg) $err .= "<br>$errMsg";
		}
		$currencyRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$currencyId	= $p["delId_".$i];

			if ($currencyId!="") {
				$selCYRateListId = $p["hdnCYRLId_".$i];

				# Check CY RL Rec Exist
				$rateListExist = $usdvalueObj->checkRateListUse($selCYRateListId);
				if (!$rateListExist) {
					// Delete CY RL
					$currencyRLRecDel = $usdvalueObj->deleteCurrencyRL($currencyId, $selCYRateListId);
					$currencyRecDel = true;

					$currencyInUse = $usdvalueObj->checkCurrencyInUse($currencyId);
					if (!$currencyInUse) {
						// Delete Currency	
						$currencyRecDel = $usdvalueObj->deleteCurrency($currencyId);
					}
				}		
			}
		}

		if ($currencyRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCurrency);
			$sessObj->createSession("nextPage",$url_afterDelCurrency.$selection);
		} else {
			if ($rateListExist) $errDel	=	$msg_failDelCurrency."&nbsp;".$msg_failDelCurrencyRLInUse; 
			else $errDel	=	$msg_failDelCurrency;
		}
		$currencyRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$currencyId	=	$p["confirmId"];
			if ($currencyId!="") {
				// Checking the selected fish is link with any other process
				$currencyConfirm = $usdvalueObj->updateUsdconfirm($currencyId);
			}

		}
		if ($currencyConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmusd);
			$sessObj->createSession("nextPage",$url_afterDelCurrency.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$currencyId = $p["confirmId"];
			if ($currencyId!="") {
				#Check any entries exist
				
					$currencyConfirm = $usdvalueObj->updateUsdReleaseconfirm($currencyId);
				
			}
		}
		if ($currencyConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmusd);
			$sessObj->createSession("nextPage",$url_afterDelCurrency.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	if ($g["currencyFilter"]!="") $currencyFilterId = $g["currencyFilter"];
	else $currencyFilterId = $p["currencyFilter"];
	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit;  
	## ----------------- Pagination Settings I End ------------	
	
	#List All Record	
	$currencyRecords		=	$usdvalueObj->fetchPagingRecords($offset, $limit, $currencyFilterId);
	$currencyRecordsize	=	sizeof($currencyRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($usdvalueObj->fetchAllRecords($currencyFilterId));
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Get All Currency Recs
	$cyRecs	= $usdvalueObj->getCYRecs();

	if ($editMode) $heading = $label_editCurrency;
	else $heading = $label_addCurrency;

	$ON_LOAD_PRINT_JS	= "libjs/usdvalue.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmUSDValue" action="USDValue.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
			<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="CURRENCY MASTER";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">								
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if( $editMode || $addMode) {
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
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('USDValue.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateUSDValue(document.frmUSDValue);" <?=$disabled?>>		
												</td>												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('USDValue.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateUSDValue(document.frmUSDValue);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidUSDId" value="<?=$editUSDId;?>">
											<input type="hidden" name="hidCurrencyId" value="<?=$editCurrencyId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" align="center"> 
													<table align="center" cellpadding="2" cellspacing="2" style="padding-top:10px;padding-bottom:10px;">
															<!--tr>
																	<td class="fieldName" nowrap>Rate List </td>
																	<td>
																	<select name="selRateList" id="selRateList" onchange="this.form.submit();">
																		<option value="">--Select--</option>
																		<?php
																		foreach ($cyRLRecs as $crl) {
																			$rateListId = $crl[0];
																			$rateListName = stripSlash($crl[1]);
																			$rlStartDate = dateFormat($crl[2]);
																			$displayRateList = $rateListName . "&nbsp;(" . $rlStartDate . ")";
																			$selected = ($currencyRLId == $rateListId) ? "Selected" : "";
																		?>
																			<option value="<?= $rateListId ?>" <?= $selected ?>><?= $displayRateList ?></option>
																		<? } ?>
																		</select>
																		</td>
																		<? if ($del == true && sizeof($cyRLRecs) > 1) { ?>
																		<td>
																			<input name="cmdDelRateList" type="submit" class="button" id="cmdDelRateList" value="Delete Rate List" title="click here to delete the selected rate list " onclick="return cfmDel();" />
																		</td>
																		<? } ?>
																	</tr-->
																	<INPUT NAME="hidCYRLId" TYPE="hidden" id="hidCYRLId" value="<?=$currencyRLId;?>" size="8">
															<tr>
																<td class="fieldName" nowrap>Currency Code</td>
																<td nowrap>
																	<INPUT NAME="currencyCode" TYPE="text" id="currencyCode" value="<?=$currencyCode;?>" size="8">
																	<INPUT NAME="hidCurrencyCode" TYPE="hidden" id="hidCurrencyCode" value="<?=$currencyCode;?>" size="8">
																</td>
															</tr>
															<tr>
																<td class="fieldName" nowrap title="Current Currency Value in INR" id="currencyValTD"><?=($cyValDisplayMsg!="")?$cyValDisplayMsg:"Value"?></td>
																<td nowrap>
																	<INPUT NAME="currencyValue" TYPE="text" id="currencyValue" value="<?=$currencyValue;?>" size="4" style="text-align:right;">
																	<INPUT NAME="hidCurrencyValue" TYPE="hidden" id="hidCurrencyValue" value="<?=$currencyValue;?>" size="4" style="text-align:right;">
																	<INPUT NAME="currencyDisplayMsg" TYPE="hidden" id="currencyDisplayMsg" value="<?=$cyValDisplayMsg?>" readonly>
																</td>
															</tr>
															<tr>
																<td class="fieldName" nowrap >Description</td>
																<td nowrap>
																	<textarea name="description" rows="2" id="description"><?=$description;?></textarea>
																</td>
															</tr>
															<tr>
																<TD colspan="2">
																<fieldset>
																	<legend class="listing-item">Rate List</legend>
																	<table>
																	<tr>
																		<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
																		<td>
																		<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" <?= $readOnly ?>>
																		<INPUT NAME="hidStartDate" TYPE="hidden" id="hidStartDate" value="<?= ($selStartDate) ? dateFormat($selStartDate) : ""; ?>" size="8" autocomplete="off" readonly="true">
																		</td>
																	</tr>
																	</table>
																</fieldset>
																</TD>
															</tr>
															<tr id="rateListRow" style="display:none;">
															<TD colspan="2">
															<fieldset>
																<legend class="listing-item">Rate List section</legend>
																<table>
																<tr>
																	<td class="fieldName" nowrap style="line-height:normal;">*When does the change <br>come into effect?</td>
																	<td>
																	<select name="effectType" id="effectType" onchange="changeEffectType()">
																		<option value="">--Select--</option>
																		<option value="F">Future</option>
																		<option value="P">Present</option>
																	</select>
																	</td>
																</tr>
																<tr id="futureRow">
																	<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
																	<td>
																	<INPUT NAME="sDate" TYPE="text" id="sDate" value="" size="8" autocomplete="off">
																	</td>
																</tr>
																</table>
															</fieldset>
															</TD>
														</tr>
												  </table>
											  </td>
											</tr>
											<tr>
											  <td colspan="2" align="center" height="5"></td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('USDValue.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateUSDValue(document.frmUSDValue);" <?=$disabled?>>												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('USDValue.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateUSDValue(document.frmUSDValue);">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
								<?php 
									if ($addMode || $editMode) {
								?>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<?php
									}
								?>
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
				<td class="listing-item">Currency&nbsp;</td>
				<td>
					<select name="currencyFilter" onchange="this.form.submit();">
					<option value="">-- Select All --</option>		 
						<?php	
							foreach ($cyRecs as $cr) {
								$cyId	 = $cr[0];
								$cyCode	 = $cr[1];
								$selected = ($currencyFilterId==$cyId)?"selected":"";	
						?>
						<option value="<?=$cyId?>" <?=$selected?>><?=$cyCode?></option>
						<?php 
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
								<tr>
									<td colspan="3" height="10"></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$currencyRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintUSDValue.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
						<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?php
												if ( sizeof($currencyRecords) > 0 )
												{
													$i	=	0;
											?>
	<thead>
		<? if($maxpage>1){?>
<tr>
<td colspan="6" style="padding-right:10px" class="navRow">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   				}
   				else
   				{
					$nav.= " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div>
	  </td>
	  </tr>
	  <? }?>
											<tr>
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th nowrap>Code</th>
												<th nowrap>Start Date</th>
												<th nowrap>Value (In INR)</th>
												<th>Description</th>
												<? if($edit==true){?>
												<th width="45">&nbsp;</th>
												<? }?>
												<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
											</tr>
	</thead>	
	<tbody>
											<?php
												foreach($currencyRecords as $sr) {
													$i++;
													$currencyId		= $sr[0];
													$cyCode			= $sr[1];
													$cyValue		= $sr[2];
													$description	= stripSlash($sr[3]);
													$cyStartDate	= dateFormat($sr[5]);
													$cyListRLId		= $sr[6];
													$active=$sr[7];
													$existingrecords=$sr[8];
											?>
											<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();"  <?php }?>>
												<td width="20" align="center">
												<?php if ($existingrecords==0){?>
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$currencyId;?>" class="chkBox">
												<?php }
												?>
													<input type="hidden" name="hdnCYRLId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$cyListRLId;?>" readonly>
												</td>
												<td class="listing-item" nowrap ><?=$cyCode;?></td>
												<td class="listing-item" nowrap ><?=$cyStartDate;?></td>
												<td class="listing-item" nowrap align="right"><?=$cyValue;?></td>
												<td class="listing-item" nowrap="nowrap" ><?=$description;?></td>
												<? if($edit==true){?>
											  		<td class="listing-item" width="45" align="center">
													 <?php if ($active!=1) {?>
													<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$currencyId;?>,'editId');assignValue(this.form,<?=$cyListRLId;?>,'editCYRLId');">
													<? } ?>
													</td>
											  <? }?>

											  <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$currencyId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$currencyId;?>,'confirmId');" >
			<?php 
			//} 
			} }?>
			
			
			
			
			</td>
												
<? }?>
											</tr>
											<?
													}
											?>												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="editCYRLId" value="">
											<input type="hidden" name="confirmId" value="">
											
	<? if($maxpage>1){?>
<tr>
<td colspan="6" style="padding-right:10px" class="navRow">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   				}
   				else
   				{
					$nav.= " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"USDValue.php?pageNo=$page&currencyFilter=$currencyFilterId\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div>
	  </td>
	  </tr>
	  <? }?>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$currencyRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintUSDValue.php',700,600);"><? }?></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>		
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
<script>
$(document).ready(function () {
	$('#currencyCode').change(function () {
		var strVal = $(this).val();
		var ucVal = strVal.toUpperCase();
		$(this).val(ucVal); 
		var curMsg = "1 "+ucVal+" In INR";
		$("#currencyValTD").html(curMsg);		
		$("#currencyDisplayMsg").val(curMsg); 
		chkCYChange();
    });
	
	$('#currencyValue').change(function () {
		chkCYChange();
	});
});	

Calendar.setup
({
	inputField  : "startDate",         // ID of the input field
	eventName	: "click",	    // name of event
	button		: "startDate",
	ifFormat    : "%d/%m/%Y",    // the date format
	singleClick : true,
	step : 1
});

Calendar.setup
({
	inputField  : "sDate",         // ID of the input field
	eventName	  : "click",	    // name of event
	button : "sDate",
	ifFormat    : "%d/%m/%Y",    // the date format
	singleClick : true,
	step : 1
});
</script>
</form>
<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>