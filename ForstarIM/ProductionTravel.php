<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$prodTravel 	= "TC";
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
		$addMode  = false;
		$editMode = false;
	}
	
	#Add 
	if ($p["cmdAdd"]!="") {
		//$mktgPositionName	= addSlash(trim($p["mktgPositionName"]));
		$marketingPerson	= $p["marketingPerson"];
		$mktgActual	= $p["mktgActual"];
		$mktgIdeal	= $p["mktgIdeal"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$avgCost	= $p["avgCost"];				
		$tcRateListId	= $p["tcRateList"];	
		# Creating a New Rate List
		if ($tcRateListId=="") {
			$rateListName = "TRAVEL"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $prodTravel, $pCurrentRateListId);
			if ($rateListRecIns) $tcRateListId = $manageRateListObj->latestRateList($prodTravel);;	
		}

		# Check Rec Exist
		$recExist = $productionTravelObj->checkRecExist($marketingPerson, $tcRateListId, $cId);
	
		if ($marketingPerson!="" && $mktgActual!="" && $mktgIdeal!="" && $puCost!="" && $totCost!="" && $avgCost!="" && $tcRateListId!="" && !$recExist) {
			
			$travelCostRecIns = $productionTravelObj->addTravelCost($marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $tcRateListId);

			if ($travelCostRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductionTravelCost);
				$sessObj->createSession("nextPage",$url_afterAddProductionTravelCost.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductionTravelCost;
			}
			$travelCostRecIns = false;
		}
		if ($recExist) {
			$addMode = true;
			$err	 = $msg_failAddProductionTravelCost."<br/> The selected record is already in our database.";
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {		
		$travelCostRecId	= $p["hidTravelCostRecId"];
		//$mktgPositionName	= addSlash(trim($p["mktgPositionName"]));
		$mktgActual	= $p["mktgActual"];
		$mktgIdeal	= $p["mktgIdeal"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$avgCost	= $p["avgCost"];
		$tcRateListId	= $p["tcRateList"];
		$marketingPerson	= $p["marketingPerson"];	
		
		# Check Rec Exist
		$recExist = $productionTravelObj->checkRecExist($marketingPerson, $tcRateListId, $travelCostRecId);			

		if ($travelCostRecId!=""  && $marketingPerson!="" && $mktgActual!="" && $mktgIdeal!="" && $puCost!="" && $totCost!="" && $avgCost!="" && $tcRateListId!="" && !$recExist) {
			$travelCostRecUptd = $productionTravelObj->updateTravelCost($travelCostRecId, $marketingPerson, $mktgActual, $mktgIdeal, $puCost, $totCost, $avgCost, $tcRateListId);
		}
	
		if ($travelCostRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductionTravelCostUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionTravelCost.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failAddProductionTravelCost."<br/> The selected record is already in our database.";
			else $err = $msg_failProductionTravelCostUpdate;
		}
		$travelCostRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$travelCostRec	=	$productionTravelObj->find($editId);
		$editTravelCostId =	$travelCostRec[0];
		$marketingPerson = 	$travelCostRec[1];
		$mktgActual	=	$travelCostRec[2];
		$mktgIdeal	=	$travelCostRec[3];
		$puCost		=	$travelCostRec[4];
		$totCost	=	$travelCostRec[5];
		$avgCost	=	$travelCostRec[6];
		$tcRateListId	=	$travelCostRec[7];
		
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$travelCostRecId	=	$p["delId_".$i];
			if ($travelCostRecId!="") {
				// Need to check the selected Category is link with any other process
				$travelCostRecDel = $productionTravelObj->deleteTravelCostRec($travelCostRecId);
			}
		}
		if ($travelCostRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionTravelCost);
			$sessObj->createSession("nextPage",$url_afterDelProductionTravelCost.$selection);
		} else {
			$errDel	=	$msg_failDelProductionTravelCost;
		}
		$travelCostRecDel	=	false;
	}

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$travelCostRecId	=	$p["confirmId"];
			if ($travelCostRecId!="") {
				// Checking the selected fish is link with any other process
				$travelCostRecConfirm = $productionTravelObj->updateTravelCostconfirm($travelCostRecId);
			}

		}
		if ($travelCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmProductionTravelCost);
			$sessObj->createSession("nextPage",$url_afterDelProductionTravelCost.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmtravelCost;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$travelCostRecId = $p["confirmId"];
			if ($travelCostRecId!="") {
				#Check any entries exist
				
					$travelCostRecConfirm = $productionTravelObj->updateTravelCostReleaseconfirm($travelCostRecId);
				
			}
		}
		if ($travelCostRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmProductionTravelCost);
			$sessObj->createSession("nextPage",$url_afterDelProductionTravelCost.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmtravelCost;
		}
		}

	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($prodTravel);			
	#--------------------------------------------


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Man Power
	$travelCostResultSetObj = $productionTravelObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$travelCostRecordSize	= $travelCostResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allTravelCostResultSetObj = $productionTravelObj->fetchAllRecords($selRateList);
	$numrows	=  $allTravelCostResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Rate List
	$tcRateListRecords = $manageRateListObj->fetchAllRecords($prodTravel);
	if ($addMode) $tcRateListId = $manageRateListObj->latestRateList($prodTravel);

	#List all Mktg Cost 
	$prodMarketing = "MC";
	$mcRateList = $manageRateListObj->latestRateList($prodMarketing);	
	$marketingCostResultSetObj = $productionMarketingObj->fetchAllRecords($mcRateList);

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editProductionTravelCost;
	else	       $heading	=	$label_addProductionTravelCost;

	$ON_LOAD_PRINT_JS = "libjs/ProductionTravel.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProductionTravel" action="ProductionTravel.php" method="post">
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
					$bxHeader = "Travel Cost Master";
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductionTravel.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionTravel(document.frmProductionTravel);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionTravel.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionTravel(document.frmProductionTravel);">												</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidTravelCostRecId" value="<?=$editTravelCostId;?>">
	<tr>
		<td colspan="2"  height="10"></td>
	</tr>
	<tr>
		<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
			<table>
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
				<td class="fieldName" nowrap >*Marketing Person</td>
				<td>
					<select name="marketingPerson" id="marketingPerson">
					<option value="">-- Select --</option>
					<?php
						while ($mcr=$marketingCostResultSetObj->getRow()) {
							$mpRecId 	= $mcr[0];
							$mpName		= stripSlash($mcr[1]);
							$selected	= ($mpRecId==$marketingPerson)?"selected":"";
					?>					
					<option value="<?=$mpRecId?>" <?=$selected?>><?=$mpName?></option>
					<?php
						}
					?>
					</select>	
				</td>
			  	</tr>					
					<tr>
					  <td class="fieldName" nowrap >*Actual</td>
					  <td><input type="text" name="mktgActual" size="5" id="mktgActual" value="<?=$mktgActual;?>" style="text-align:right;" onkeyup="prodnTravelAverageCost('mktgActual', 'puCost', 'avgCost');" autocomplete="off"></td>
					  </tr>
					<tr>
					  <td class="fieldName" nowrap >*Ideal</td>
					  <td><input type="text" name="mktgIdeal" size="5" id="mktgIdeal" value="<?=$mktgIdeal;?>" style="text-align:right;" onkeyup="prodnTravelTotalCost('mktgIdeal', 'puCost', 'totCost');" autocomplete="off"></td>
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
		<!-- 	“Purchase Cost” should be replaced with “Unit cost” -->
					<tr>
					  <td class="fieldName" nowrap >*Unit Cost</td>
					  <td><input type="text" name="puCost" size="7" id="puCost" value="<?=$puCost;?>" style="text-align:right;" onkeyup="prodnTravelTotalCost('mktgIdeal', 'puCost', 'totCost'); prodnTravelAverageCost('mktgActual', 'puCost', 'avgCost');" autocomplete="off"></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Total Cost</td>
					  <td><input type="text" name="totCost" size="7" id="totCost" value="<?=$totCost;?>" style="text-align:right; border:none;" readonly></td>
					</tr>
<!-- 	 “Average Cost” should be replaced with “Actual Cost” -->
					<tr>
					  <td class="fieldName" nowrap >Actual Cost</td>
					  <td><input type="text" name="avgCost" size="7" id="avgCost" value="<?=$avgCost;?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr><TD colspan="2">
					<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
					<input type="hidden" name="tcRateList" id="tcRateList" value="<?=$tcRateListId?>">
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
			<select name="tcRateList">
			<?
			/*
			if (sizeof($tcRateListRecords)>0) {
				foreach ($tcRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $tcRateListId;
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
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionTravel.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionTravel(document.frmProductionTravel);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionTravel.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionTravel(document.frmProductionTravel);">												</td>
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
                <?
		foreach ($tcRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected =  ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$prodTravel?>'"></td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Travel Cost Master </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$travelCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionTravel.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
		if ($travelCostRecordSize) {
			$i	=	0;
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
      				$nav.= " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
			while(($tcr=$travelCostResultSetObj->getRow())) {
				$i++;
				$travelCostRecId 	= $tcr[0];
				$headName	= stripSlash($tcr[1]);
				$mcActualUnit	= $tcr[2];
				$mcIdealUnit	= $tcr[3];		
				$mcPuCost	= $tcr[4];
				$mcTotCost	= $tcr[5];
				$mcAvgCost	= $tcr[6]; 	
				$active=$tcr[7];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$travelCostRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$headName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$mcActualUnit;?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcIdealUnit?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcPuCost?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcTotCost?></td>
<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mcAvgCost?></td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$travelCostRecId;?>,'editId');this.form.action='ProductionTravel.php';" ><? } ?></td>
<? }?>

<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$travelCostRecId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$travelCostRecId;?>,'confirmId');" >
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
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionTravel.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$travelCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionTravel.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
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
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>