<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$productionManPower = "MPC";	
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
		header("Location: ErrorPageIFrame.php");
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
		$addMode 	= false;
		$editMode	= false;
	}

	
	#Add a stock
	if ($p["cmdAdd"]!="") {
		$name		= addSlash(trim($p["manPowerName"]));
		$manPowerType	= $p["manPowerType"];
		$manPowerUnit	= $p["manPowerUnit"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$manPowerRateListId = $p["manPowerRateList"];	
		# Creating a New Rate List
		if ($manPowerRateListId=="") {
			$rateListName = "MANPOWER"."(".date("dMy").")";
			$startDate    = date("Y-m-d");
			$rateListRecIns = $manageRateListObj->addRateList($rateListName, $startDate, $cpyRateList, $userId, $productionManPower, $pCurrentRateListId);
			if ($rateListRecIns) $manPowerRateListId = $manageRateListObj->latestRateList($productionManPower);;	
		}


		if ($name!="" && $manPowerType!="" && $manPowerUnit!="" && $puCost!="" && $totCost!="" && $manPowerRateListId!="") {

			$manPowerRecIns = $productionManPowerObj->addManPower($name, $manPowerType, $manPowerUnit, $puCost, $totCost, $manPowerRateListId);

			if ($manPowerRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddManPower);
				$sessObj->createSession("nextPage",$url_afterAddManPower.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddManPower;
			}
			$manPowerRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$manPowerId	= $p["hidManPowerId"];
		$name		= addSlash(trim($p["manPowerName"]));
		$manPowerType	= $p["manPowerType"];
		$manPowerUnit	= $p["manPowerUnit"];
		$puCost		= $p["puCost"];	
		$totCost	= $p["totCost"];
		$manPowerRateListId = $p["manPowerRateList"];	
		
		if ($manPowerId!="" && $name!="" && $manPowerType!="" && $manPowerUnit!="" && $puCost && $totCost) {
			$manPowerRecUptd = $productionManPowerObj->updateManPower($manPowerId, $name, $manPowerType, $manPowerUnit, $puCost, $totCost, $manPowerRateListId);
		}
	
		if ($manPowerRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succManPowerUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateManPower.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failManPowerUpdate;
		}
		$manPowerRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$manPowerRec	=	$productionManPowerObj->find($editId);
		$editManPowerId =	$manPowerRec[0];
		$name		=	stripSlash($manPowerRec[1]);
		$manPowerType	=	$manPowerRec[2];
		$manPowerUnit	=	$manPowerRec[3];
		$puCost		=	$manPowerRec[4];
		$totCost	=	$manPowerRec[5];
		$manPowerRateListId = $manPowerRec[6];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$manPowerId	=	$p["delId_".$i];
			if ($manPowerId!="") {
				// Need to check the selected Category is link with any other process
				$manPowerRecDel = $productionManPowerObj->deleteManPower($manPowerId);
			}
		}
		if ($manPowerRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelManPower);
			$sessObj->createSession("nextPage",$url_afterDelManPower.$selection);
		} else {
			$errDel	=	$msg_failDelManPower;
		}
		$manPowerRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$manPowerId	=	$p["confirmId"];


			if ($manPowerId!="") {
				// Checking the selected fish is link with any other process
				$manPowerRecConfirm = $productionManPowerObj->updateManPowerconfirm($manPowerId);
			}

		}
		if ($manPowerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmManPower);
			$sessObj->createSession("nextPage",$url_afterDelManPower.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$manPowerId= $p["confirmId"];

			if ($manPowerId!="") {
				#Check any entries exist
				
					$manPowerRecConfirm = $productionManPowerObj->updateManPowerReleaseconfirm($manPowerId);
				
			}
		}
		if ($manPowerRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmManPower);
			$sessObj->createSession("nextPage",$url_afterDelManPower.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	
	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($productionManPower);			
	#--------------------------------------------
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Man Power
	$manPowerResultSetObj = $productionManPowerObj->fetchAllPagingRecords($offset, $limit, $selRateList);
	$manPowerRecordSize	= $manPowerResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allManPowerResultSetObj = $productionManPowerObj->fetchAllRecords($selRateList);
	$numrows	=  $allManPowerResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	# Rate List
	$manPowerRateListRecords = $manageRateListObj->fetchAllRecords($productionManPower);
	if ($addMode) $manPowerRateListId = $manageRateListObj->latestRateList($productionManPower);


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	#heading Section
	if ($editMode) $heading	=	$label_editManPower;
	else	       $heading	=	$label_addManPower;

	$ON_LOAD_PRINT_JS = "libjs/ProductionManPower.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");	
?>
	<form name="frmProductionManPower" action="ProductionManPower.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>	
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1"><?=$err;?></td>
	</tr>
	<?php }?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Man Power Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
		<?php
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductionManPower.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionManPower(document.frmProductionManPower);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionManPower.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionManPower(document.frmProductionManPower);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidManPowerId" value="<?=$editManPowerId;?>">	
			<tr>
				<td colspan="2"  height="10" ></td>
			</tr>
			<tr>
				<td colspan="2" nowrap >
					<table width="200">
					<tr>
					  <td class="fieldName" nowrap >*Name</td>
					  <td nowrap>
					  <input type="text" name="manPowerName" size="20" value="<?=$name;?>" /></td>
				  	</tr>
					<tr>
					<td nowrap class="fieldName">*Type</td>
					<td nowrap>
                                        <select name="manPowerType" id="manPowerType" onchange="changeManPowerType();">
					<option value="">-- Select --</option>
					<option value="F" <? if ($manPowerType=='F') echo "Selected";?>>Fixed</option>
					<option value="V" <? if ($manPowerType=='V') echo "Selected";?>>Variable</option>
					</select>
					</td></tr>
					<tr>
					  <td class="fieldName" nowrap >*No.of Unit</td>
					  <td nowrap><input type="text" name="manPowerUnit" size="5" id="manPowerUnit" value="<?=$manPowerUnit;?>" onkeyup="productionManPowerTotalCost('manPowerUnit','puCost','totCost');" style="text-align:right;"></td>
					  </tr>
					<tr>
					  <td class="fieldName" nowrap >*Unit Cost</td>
					  <td nowrap><input type="text" name="puCost" size="7" id="puCost" value="<?=$puCost;?>" onkeyup="productionManPowerTotalCost('manPowerUnit','puCost','totCost');" style="text-align:right;"></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Total Cost</td>
					  <td nowrap><input type="text" name="totCost" size="7" id="totCost" value="<?=$totCost;?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr><TD>
		<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
		<input type="hidden" name="manPowerRateList" id="manPowerRateList" value="<?=$manPowerRateListId;?>">	
					</TD></tr>
					<!--<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="manPowerRateList">
			<?
			/*
			if (sizeof($manPowerRateListRecords)>0) {
				foreach ($manPowerRateListRecords as $prl) {
					$mRateListId	= $prl[0];
					$rateListName		= stripSlash($prl[1]);
					$startDate		= dateFormat($prl[2]);
					$displayRateList = $rateListName."&nbsp;(".$startDate.")";
					if ($addMode) $rateListId = $selRateList;
					else $rateListId = $manPowerRateListId;
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
                                                                      </table>
				</td>
			</tr>
						<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionManPower.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionManPower(document.frmProductionManPower);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionManPower.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionManPower(document.frmProductionManPower);">												</td>
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
		foreach ($manPowerRateListRecords as $prl) {
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
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$productionManPower?>'"></td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Man Power Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$manPowerRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionManPower.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
		if ($manPowerRecordSize) {
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
      				$nav.= " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
	<tr  align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox" ></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Type</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Pu Cost</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Total Cost</th>
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
			while ($mpr=$manPowerResultSetObj->getRow()) {
				$i++;
				$manPowerId 	= $mpr[0];
				$mPName		= stripSlash($mpr[1]);
				//$mPType		= $mpr[2];	
				$mPType		= ($mpr[2]=='F')?"Fixed":"Variable";	
				$mPUnit		= $mpr[3];
				$mPPuCost	= $mpr[4];
				$mpTotCost	= $mpr[5];
				$active=$mpr[6];
			?>
	<tr <?php if ($active==0) { ?>  bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$manPowerId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$mPName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$mPType;?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mPUnit?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mPPuCost?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$mpTotCost?></td>
		<? if($edit==true){?>
				<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$manPowerId;?>,'editId');this.form.action='ProductionManPower.php';" ><? } ?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$manPowerId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$manPowerId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductionManPower.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
	<?php
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$manPowerRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionManPower.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<? if ($iFrameVal=="") { ?>
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
<?php 
	}
?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");	
?>