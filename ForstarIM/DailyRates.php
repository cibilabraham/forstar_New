<?php
	require("include/include.php");
	require("lib/dailyrates_ajax.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$editFishId		= 	"";
	$fishId			=	"";
	$recordsFilterId	=	0;
	$receivedBy		=	"";
	$processId		=	"";
	
	$selection = "?selFilter=".$p["selFilter"]."&selDate=".$p["selDate"]."&pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"];	
	
	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;

	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Add Daily Rate
	if ($p["cmdAddNew"]!="") {
		$addMode	= true;		
	}
	
	if ($p["cmdCancel"]!="") {
		$p["selFish"]	=  "";
		$addMode	= false;
		$editMode	= false;
	}
	
	
	# Reset Values
	$cpyFrmChk	= false;
	if ($p["cpyFrmDate"]) 		$cpyFrmDate = $p["cpyFrmDate"];
	if ($p["cpyFrmLandingCenter"])	$cpyFrmLandingCenter = $p["cpyFrmLandingCenter"];
	if ($p["cpyFrmSupplier"]) 	$cpyFrmSupplier = $p["cpyFrmSupplier"];
	if ($p["cpyFrmFish"]) 		$cpyFrmFish = $p["cpyFrmFish"];
	if ($p["cpyFrmProcessCode"]) 	$cpyFrmProcessCode = $p["cpyFrmProcessCode"];
	if ($p["cmdCopyFromSelection"]) $cpyFrmChk = true;
	
	# Add Rate
	if ($p["cmdAddDailyRate"]!="") {
		$currentDate		= mysqlDateFormat($p["currentDate"]);
		$landingCenterId	= $p["landingCenter"];
		$supplier		= $p["supplier"];
		$fishId			= $p["selFish"];		
		$processCodeId		= $p["processCode"];
		$itemCount	  	= $p["hidTableRowCount"];
		
		if ($fishId!="" && $landingCenterId!="" && $fishId!="" && $processCodeId!="") {
			
				$dailyrateRecIns = $dailyratesObj->addDailyRate($currentDate, $landingCenterId, $supplier, $fishId, $processCodeId);
				#Find the Last inserted Id From Main Table
				if ($dailyrateRecIns) $lastId = $databaseConnect->getLastInsertedId();

				for ($i=0; $i<$itemCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$selGradeId 	= ($p["selGrade_".$i]=="")?0:$p["selGrade_".$i];
						$countAverage   = ($p["countAverage_".$i]=="")?0:$p["countAverage_".$i];
						$higherCount	= $p["higherCount_".$i];
						$lowerCount    	= $p["lowerCount_".$i];
						$marketRate	= $p["marketRate_".$i];
						$decRate	= $p["decRate_".$i];
						if ($lastId!="" && $decRate!="") {
							$dailyRateEntryRecIns = $dailyratesObj->addDailyRateEntryRec($lastId, $selGradeId, $countAverage, $higherCount, $lowerCount, $marketRate, $decRate);
						}
					}
				} # Item Loop Ends Here	

			if ($dailyrateRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDailyRate);
				$sessObj->createSession("nextPage",$url_afterAddDailyRate.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddDailyRate;
			}
			$dailyrateRecIns	=	false;
		}
		$addMode=false;
	}

	# Update 
	if ($p["cmdSaveChange"]!="") {
		$dailyRateId		= $p["hidDailyRateId"];
		$landingCenterId	= $p["landingCenter"];
		$supplier		= $p["supplier"];
		$fishId			= $p["selFish"];		
		$processCodeId		= $p["processCode"];
	
		$itemCount	  = $p["hidTableRowCount"];

		if ($dailyRateId!="" && $fishId!="") {
			$dailyRateRecUptd = $dailyratesObj->updateDailyRate($dailyRateId, $landingCenterId, $supplier, $fishId, $processCodeId);
			if ($dailyRateRecUptd) {
				# Del Old Recs
				$delDailyRateEntryRecs = $dailyratesObj->delDailyRateEntryRecs($dailyRateId);
				if ($delDailyRateEntryRecs) {
					for ($i=0; $i<$itemCount; $i++) {
						$status = $p["status_".$i];
						if ($status!='N') {
							$selGradeId = ($p["selGrade_".$i]=="")?0:$p["selGrade_".$i];
							$countAverage   = ($p["countAverage_".$i]=="")?0:$p["countAverage_".$i];
							$higherCount	= $p["higherCount_".$i];
							$lowerCount    	= $p["lowerCount_".$i];
							$marketRate	= $p["marketRate_".$i];
							$decRate	= $p["decRate_".$i];
							if ($dailyRateId!="" && $decRate!="") {
								$dailyRateEntryRecIns = $dailyratesObj->addDailyRateEntryRec($dailyRateId, $selGradeId, $countAverage, $higherCount, $lowerCount, $marketRate, $decRate);
							}
						}
					} # Item Loop Ends Here	
				}
			}	
		}
		if ($dailyRateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyRate);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyRate.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyRate;
		}
		$dailyRateRecUptd = false;
	}

	# Edit Daily Rate 	
	if ($p["editId"]!="" || $cpyFrmChk) {

		if (!$cpyFrmChk) $editMode = true;

		if ($cpyFrmChk) $editId	= $dailyratesObj->getEditRecId(mysqlDateFormat($cpyFrmDate), $cpyFrmLandingCenter, $cpyFrmSupplier, $cpyFrmFish, $cpyFrmProcessCode);
		else $editId	= $p["editId"];

		$dailyRateRec	= $dailyratesObj->find($editId);

		$dailyRateId		=	$dailyRateRec[0];
		/*
		if ($p["editSelectionChange"]=='1'|| $p["landingCenter"]=="") $dailyRateCenterId = $dailyRateRec[1];
		else $dailyRateCenterId	= $p["landingCenter"];		
		if ($p["editSelectionChange"]=='1'||$p["selFish"]=="") $editFishId	= $dailyRateRec[2];
		else $editFishId	= $p["selFish"];
		if ($p["editSelectionChange"]=='1' || $p["processCode"]=="") $recordProcessCode	= $dailyRateRec[4];
		else $recordProcessCode	= $p["processCode"];
		*/

		$dailyRateCenterId	= $dailyRateRec[1];
		$editFishId		= $dailyRateRec[2];
		$dailyRateSupplier	= $dailyRateRec[3];
		$recordProcessCode	= $dailyRateRec[4];	
		
		$enteredDate		= dateFormat($dailyRateRec[5]);		
		
		$processCodeRec		= $processcodeObj->find($recordProcessCode);
		$receivedBy		= $processCodeRec[7];

		$getDailyRateEntryRecs = $dailyratesObj->getDailyRateEntryRecs($dailyRateId);
	}	
				
	# Delete Daily Rate	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailyRateId		= $p["delId_".$i];
			$dailyRateEntryId 	= $p["dailyRateEntryId_".$i];
			if ($dailyRateId!="") {
				$dailyRateEntryRecDel = $dailyratesObj->deleteDailyRateEntryRec($dailyRateEntryId);
				# Checking More entry Exist
				$chkMoreEntryExist = $dailyratesObj->chkMoreEntryExist($dailyRateId);
				if (!$chkMoreEntryExist) {
					//echo "All delete";
					$dailyRateRecDel = $dailyratesObj->deleteDailyRate($dailyRateId);
				}
			}
		}

		if ($dailyRateRecDel || $dailyRateEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyRate);
			$sessObj->createSession("nextPage",$url_afterDelDailyRate.$selection);
		} else {
			$errDel		=	$msg_failDelDailyRate;
		}
	}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# List records based on filter 
	if ($g["selFilter"]!="" && $g["selDate"]!="") {
		$recordsFilterId	=	$g["selFilter"];
		$selDate		=	$g["selDate"];
	} else if($p["selDate"]=="") {
		$recordsFilterId	=	$p["selFilter"];
		$selDate		=	date("d/m/Y");
	} else {
		$recordsFilterId	=	$p["selFilter"];
		$selDate		=	$p["selDate"];
	}
	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];
	
	#Condition for Select a Fish 	
	if ($p["existRecordsFilterId"]==0 && $p["selFilter"]!=0 ) {
		$offset = 0;
		$pageNo = 1;
	}
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;
	}
	
	if ($recordsFilterId!=0 || $selDate!="") {	
		$recordsDate	= mysqlDateFormat($selDate);	
		$dailyRatesRecords = $dailyratesObj->dailyRateRecPagingFilter($recordsFilterId, $recordsDate, $offset, $limit, $supplierFilterId);
		$numrows	=  sizeof($dailyratesObj->dailyRateRecFilter($recordsFilterId,$recordsDate, $supplierFilterId));
	}	
	$dailyRatesRecordsSize		=	sizeof($dailyRatesRecords);

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#for selecting Date
	//$dailyRateDateRecords	= $dailyratesObj->fetchAllDateRecords();
	
	#List all Landing Centers
	//$landingCenterRecords	= $landingcenterObj->fetchAllRecords();
	$landingCenterRecords	= $landingcenterObj->fetchAllRecordsActiveLanding();
	# Returns all fish master records 
	//$fishMasterRecords	= $fishmasterObj->fetchAllRecords();
	$fishMasterRecords	= $fishmasterObj->fetchAllRecordsFishactive();

	# Get All Supplier Records
	//$fetchSupplierRecords	= $supplierMasterObj->fetchAllRecords("FRN");
	$fetchSupplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("FRN");
	
	# Setting Mode Here
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";
	
	# Display heading
	if ($editMode)	$heading	= $label_editDailyRate;
	else 		$heading	= $label_addDailyRate;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/dailyrates.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDailyRate" id="frmDailyRate" action="DailyRates.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
		
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
									<td colspan="2"  align="center" style="padding-left:10px; padding-right:10px;">
										<table cellpadding="0"  width="75%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" >
	<input type="hidden" name="hidReceived" id="hidReceived" value="<?=$receivedBy?>">
</td>
											</tr>
	
											<tr>
												<? if($editMode){?>

												<td colspan="4" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('DailyRates.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyRates(document.frmDailyRate);">												</td>
												
												<?} else{?>

												<td align="center" colspan="4">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRates.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDailyRate" class="button" value=" Add " onClick="return validateAddDailyRates(document.frmDailyRate);">												</td>
		<?} ?>
	</tr>
	<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
	<tr>
		<td height="10" colspan="2"></td>
	</tr>
	<?php
			 if ($addMode) { 
		?>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
			<fieldset>
			<legend class="listing-item" onMouseover="ShowTip('Copy from existing Entry and save after editing.');" onMouseout="UnTip();">Copy From</legend>
			<table>
				<TR>
				<TD valign="top" nowrap>
				<table>
				<TR>
				<TD class="fieldName" nowrap>*Date:</TD>
				<td class="listing-item" nowrap="true">
					<input name="cpyFrmDate" type="text" id="cpyFrmDate" size="9" value="<?=$cpyFrmDate?>" autocomplete="off" onchange="xajax_getCpyFrmFishRecs(document.getElementById('cpyFrmDate').value,'','',''); xajax_getCpyFrmLandgCenters(document.getElementById('cpyFrmDate').value, '');" />
				</td>
				</tr>
				<tr>
						<td class="fieldName" nowrap >Landing Center</td>
						<td nowrap>		
						<select name="cpyFrmLandingCenter" id="cpyFrmLandingCenter" onchange="xajax_getCpyFrmSupplierRecs(document.getElementById('cpyFrmDate').value, document.getElementById('cpyFrmLandingCenter').value, ''); xajax_getCpyFrmFishRecs(document.getElementById('cpyFrmDate').value, document.getElementById('cpyFrmLandingCenter').value,'','');">
						<option value="0">-- Select All --</option>			
						</select>
						</td>		
					</tr>
					<tr>
					<td class="fieldName" nowrap>Supplier</td>
					<td nowrap>		
					<select name="cpyFrmSupplier" id="cpyFrmSupplier" onchange="xajax_getCpyFrmFishRecs(document.getElementById('cpyFrmDate').value, document.getElementById('cpyFrmLandingCenter').value, document.getElementById('cpyFrmSupplier').value,'');" style="width:120px;">		
						<option value="0">-- Select All --</option>
					</select>
					</td>
					</tr>	
				</table>
				</TD>	
				<td nowrap>&nbsp;</td>					
				<td valign="top" nowrap>
					<table>					
					<tr>
				<td class="fieldName" nowrap >*Fish</td>
		<td nowrap>		
			<select name="cpyFrmFish" id="cpyFrmFish" onchange="xajax_getCpyFrmPcsCodeRecs(document.getElementById('cpyFrmDate').value, document.getElementById('cpyFrmLandingCenter').value, document.getElementById('cpyFrmSupplier').value, document.getElementById('cpyFrmFish').value, '');">
			<option value="" >--- Select --- </option>			
			  </select>									
		</td>
		</tr>
		<tr>	
		<td class="fieldName" nowrap>*Process Code</td>
	          	<td nowrap>			
			<select name="cpyFrmProcessCode" id="cpyFrmProcessCode">
                     		<option value="">-- Select --</option>	
                       </select>
			</td>	
			</TR>	
		<tr><TD colspan="2" align="center" nowrap="true">
			<input type="submit" name="cmdCopyFromSelection" class="button" value=" Copy From the Selection " onclick="return validateDRateCpyFrom(document.frmDailyRate);" style="width:180px;">&nbsp;&nbsp;<input type="reset" name="cmdResetCopyFrom" class="button" value=" Reset " onclick="resetValues();" >
		</TD></tr>
		<!--<tr>
			<TD>
				<input type="checkbox" name="cpyFrmChk" id="cpyFrmChk" value="Y" class="chkBox" onclick="return validateDRateCpyFrom(document.frmDailyRate);" <?=$cpyFrmChk?>>
			</TD>
			<td class="listing-item">Copy From</td>
		</tr>-->
					</table>
				</td>
				</TR>
			</table>
			</fieldset>
		  </td>
		</tr>
	<?php
	if ($cpyFrmDate && $cpyFrmChk) {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_getCpyFrmLandgCenters('<?=$cpyFrmDate?>', '<?=$cpyFrmLandingCenter?>');
		xajax_getCpyFrmSupplierRecs('<?=$cpyFrmDate?>', '<?=$cpyFrmLandingCenter?>', '<?=$cpyFrmSupplier?>'); 
		xajax_getCpyFrmFishRecs('<?=$cpyFrmDate?>', '<?=$cpyFrmLandingCenter?>', '<?=$cpyFrmSupplier?>', '<?=$cpyFrmFish?>');
		xajax_getCpyFrmPcsCodeRecs('<?=$cpyFrmDate?>', '<?=$cpyFrmLandingCenter?>', '<?=$cpyFrmSupplier?>', '<?=$cpyFrmFish?>', '<?=$cpyFrmProcessCode?>');
	</script>	
	<? }?>
		<?php
			}
		?>
	<tr>
	<td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
	<table>
		<TR>
		<TD valign="top">
		<fieldset>
		<table>
		<tr>
			<td class="fieldName" nowrap >*Date</td>
			<td nowrap><input name="currentDate" type="text" id="currentDate" size="9" value="<? if($editMode==true) { echo $enteredDate; } else { echo date("d/m/Y");}?>" autocomplete="off" /></td>
		</tr>
	<tr>
		<td class="fieldName" nowrap >*Fish</td>
		<td nowrap>		
			<select name="selFish" id="selFish" onchange="xajax_getProcessCodeRecs(document.getElementById('selFish').value,'');">
			<option value="" >--- Select Fish --- </option>
			<? 
				if (sizeof($fishMasterRecords)> 0) {	
					$id	= "";
					foreach ($fishMasterRecords as $fl) {
						$id		=	$fl[0];
						$name	=	$fl[1];
						$selected		=	"";
						if ($fishId==$id || $editFishId==$id) {
							$selected	=	"selected";
						}
			?>
			<option value="<?=$id?>" <?=$selected?> ><?=$name?></option>
			<?
					}
				}
			?>
			  </select>									
		</td>			
		</tr>
		<tr>
			<td class="fieldName" nowrap >*Process Code</td>
	          	<td nowrap>			
			<select name="processCode" id="processCode" onchange="xajax_getReceivedByTypes(document.getElementById('processCode').value, '<?=$mode?>', '<?=$cpyFrmChk?>', '<?=$receivedBy?>');">
                     		<option value="">-- Select --</option>	
                       </select>
			</td>	 
          	</tr>
		</table>
		</fieldset>
		</TD>
		<TD nowrap>&nbsp;</TD>
		<TD valign="top" nowrap>
		<fieldset>
		<table>
		<tr>
			<td class="fieldName" nowrap >Landing Center</td>
			<td nowrap>		
			<select name="landingCenter" id="landingCenter" onchange="xajax_getSupplierRecords(document.getElementById('landingCenter').value,'');">
			<option value="0">-- Select All --</option>
			<?php
				foreach ($landingCenterRecords as $fr) {
					$centerId	=	$fr[0];
					$centerName	=	stripSlash($fr[1]);
					$selected="";
					if ($centerId== $landingCenterId || $dailyRateCenterId==$centerId) {
						$selected	=	"selected";
					}
			?>
			<option value="<?=$centerId?>" <?=$selected?>><?=$centerName?></option>
		<? } ?>
		</select></td>		
		</tr>
		<tr>
		<td class="fieldName" nowrap>Supplier</td>
		<td nowrap>		
		<select name="supplier" id="supplier" style="width:150px;">		
                	<option value="0">-- Select All --</option>
                </select>
		</td>
		</tr>		
		</table>
		</fieldset>
		</TD>
		</TR>
		
	</table>
	</td>
	</tr>
<!--  Dynamic Row Starts Here -->
	<tr>
		<td colspan="4" align="center">
			<table>
			<TR>
				<TD id="addTable">
					<? if (sizeof($getDailyRateEntryRecs)>0) {?>
						
						<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddRecivedType">
						<tr bgcolor="#f2f2f2" align="center">
							<? if ($receivedBy=='G'  || $receivedBy=='B'){ ?>	
							<td class="listing-head">*Grade</td>
							<? }?>
							<? if ($receivedBy=='C' || $receivedBy=='B') {?>
							<td class="listing-head" nowrap>*Count<br>(Avg)</td>
							<? }?>
							<td class="listing-head">Rate <br>Increase for<br> Higher Count</td>
							<td class="listing-head">Rate <br>Decrease for <br>Lower Count</td>
							<td class="listing-head">Market Rate</td>
							<td class="listing-head">*Decl. Rate</td>
							<td></td>
						</tr>
						</table>
					<? }?>
				</td>
			</tr>
			<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="">
			<tr><TD height="5"></TD></tr>
			<tr>
				<TD align="left">
					<a href="###" id='addRow' onclick="javascript:addNewSOItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
				</TD>
			</tr>
			</table>
		</td>
	</tr>
<!--  Dynamic Row Ends Here -->
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="4" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRates.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyRates(document.frmDailyRate);">												</td>
												
												<?} else{?>

												<td align="center" colspan="4">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRates.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddDailyRate" class="button" value=" Add " onClick="return validateAddDailyRates(document.frmDailyRate);">												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
			
			# Listing Fish-Grade Starts
		?>
		
		<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Daily Rates</td>
								<td background="images/heading_bg.gif" nowrap="true">
			<table cellpadding="0" cellspacing="0" align="right">	
			<tr>
				<td class="listing-item">Supplier:</td>		
				<td nowrap="true">
					<select name="supplierFilter" onchange="this.form.submit();" style="width:90px;">
					<option value="">-- Select All --</option>
					<?
					foreach ($fetchSupplierRecords as $fsr) {				
						$fSupplierId	= $fsr[0];
						$fSupplierName	= stripSlash($fsr[2]);
						$selected = "";
						if ($supplierFilterId==$fSupplierId) $selected = "selected";
					?>
					<option value="<?=$fSupplierId?>" <?=$selected?>><?=$fSupplierName?></option>
					<?
						}	
					?>
					</select>
					&nbsp;
				</td>
				<td class="listing-item" nowrap > Fish:&nbsp;</td>
				<td nowrap="true">
				<select name="selFilter" onChange="this.form.submit();">
				<option value="0"> All Fish </option>
				<? 
				if (sizeof($fishMasterRecords)>0) {
					foreach ($fishMasterRecords as $fl) {
						$fishId		=	$fl[0];
						$fishName	=	$fl[1];
						$selected	=	"";
						if( $fishId == $recordsFilterId ){
							$selected	=	"selected";
						}
				?>
				<option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
				<?
					}
				}
				?>
				</select>								
			</td>
			<td class="listing-item" nowrap>&nbsp;&nbsp;Date:&nbsp;</td>
			<td nowrap>
			<? 
				if($selDate=="") $selDate=date("d/m/Y");
			?>
                            <input type="text" id="selDate" name="selDate" size="9" value="<?=$selDate?>" onchange="this.form.submit();" autocomplete="off">&nbsp;
			</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10" >
									
									
								</td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyRatesRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyRates.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?></td>
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
		<td colspan="2" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if (sizeof($dailyRatesRecords)>0) {
			$i	=	0;
		?>
		<? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF"><td colspan="11" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			 $nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				} else {
	      				$nav.= " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   				}
			}
			if ($pageNo > 1) {
   				$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   				$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
   				$page = $pageNo + 1;
   				$next = " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr>
	<? }?>
	<tr  bgcolor="#f2f2f2" align="center"  >
		<td width="20" height="1">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Date</td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Landing Center</td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Process<br/> Code </td>
		<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Count Average</td>
		<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Market Rate </td>
		<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Decl.<br>Rate</td>
		<? if($edit==true){?>
			<td class="listing-head">&nbsp;</td>
		<? }?>
	</tr>
	<?
	$selSupplierId = "";	
	foreach ($dailyRatesRecords as $dr) {
		$i++;
		$dailyRateId	=	$dr[0];		
		$selDailyRateDate		= 	dateFormat($dr[4]);
		$selLandingCenterId 	= $dr[3];
			$centerRec		= $landingcenterObj->find($selLandingCenterId);
			$landingCenterName	= ($centerRec[1]!="")?$centerRec[1]:"ALL";
		$selSupplierId		= $dr[5];
			$supplierRec	= $supplierMasterObj->find($selSupplierId);
			$selSupplierName = ($supplierRec[2]!="")?$supplierRec[2]:"ALL";
		$marketRate		=	$dr[6];
		$decRate		=	$dr[7];
		$processCodeRecs	=	$processcodeObj->find($dr[9]);		
		$process_code		=	stripSlash($processCodeRecs[2]);
		$fishName		=	$dr[10];
		$gradeId		=	$dr[2];
		$gradeRec		=	$grademasterObj->find($gradeId);
		$gradeCode		=	$gradeRec[1];
		$count			=	$dr[8];
		if($count==0) $count="";
		$dailyRatesEntryId	= $dr[11];	
	?>
	<tr  bgcolor="WHITE">
		<td width="20" height="1" class="listing-item">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyRateId;?>" class="chkBox">
			<input type="hidden" name="dailyRateEntryId_<?=$i;?>" id="dailyRateEntryId_<?=$i;?>" value="<?=$dailyRatesEntryId;?>">
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selDailyRateDate?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" nowrap><?=$landingCenterName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$selSupplierName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$fishName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$gradeCode;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap><?=$process_code?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$count?></td>		
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$marketRate?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$decRate;?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="45" align="center" style="padding-left:3px; padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyRateId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyRates.php';"  ></td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF"><td colspan="11" style="padding-right:10px"><div align="right">
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
      	$nav.= " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"DailyRates.php?selFilter=$recordsFilterId&pageNo=$page&selDate=$selDate&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
									</table>
	<input type="hidden" name="existRecordsFilterId" value="<?=$recordsFilterId?>">
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyRatesRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyRates.php?selFilter=<?=$recordsFilterId?>&selDate=<?=$recordsDate?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">
	<tr>
		<td height="10"></td>
	</tr>	
	<input type="hidden" name="hidMode" value="<?=$mode?>">
	</table>
	<?php
		if ($addMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript">
			function addNewSOItem()
			{				
				addNewDailyRateItemRow('tblAddRecivedType',document.getElementById("hidReceived").value, '', '', '', '', '', '');
				xajax_getGradeRecords(document.getElementById("hidTableRowCount").value,document.getElementById('processCode').value);		
			}
		</SCRIPT>
	<?php
		} else if ($editMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript">
			function addNewSOItem()
			{
				addNewDailyRateItemRow('tblAddRecivedType',document.getElementById("hidReceived").value, '', '', '', '', '', '');
				xajax_getGradeRecords(document.getElementById("hidTableRowCount").value,document.getElementById('processCode').value);	
			}
			/* Get Supplier Records*/
			xajax_getSupplierRecords(document.getElementById('landingCenter').value,'<?=$dailyRateSupplier;?>');
			/* Get Process Code Records*/
			xajax_getProcessCodeRecs(document.getElementById('selFish').value,'<?=$recordProcessCode?>');
		</SCRIPT>
	<? }?>
	<? if ($cpyFrmChk) {?>
		<script language="JavaScript" type="text/javascript">
			/* Get Supplier Records*/
			xajax_getSupplierRecords('<?=$dailyRateCenterId?>','<?=$dailyRateSupplier;?>');
			/* Get Process Code Records*/
			xajax_getProcessCodeRecs('<?=$editFishId?>','<?=$recordProcessCode?>');
		</script>
	<? }?>
	<? if ($addMode) {?>
		<SCRIPT LANGUAGE="JavaScript">	
			//window.load = addNewSOItem();
		</SCRIPT>
	<? }?>
	<?
		if (sizeof($getDailyRateEntryRecs)>0) {
			$k = 0;
			foreach($getDailyRateEntryRecs as $gdr) {
				$entryId 	= $gdr[0];
				$selGradeId 	= $gdr[1];
				$countAvg  	= $gdr[2];
				$highCount	= $gdr[3];
				$lowCount	= $gdr[4];
				$mktRate	= $gdr[5];
				$declRate	= $gdr[6];				
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			//alert(<?=$selGradeId;?>);
			addNewDailyRateItemRow('tblAddRecivedType', '<?=$receivedBy;?>', '<?=$selGradeId;?>', '<?=$countAvg;?>', '<?=$highCount;?>', '<?=$lowCount;?>', '<?=$mktRate;?>', '<?=$declRate;?>');
			xajax_getGradeRecords('<?=sizeof($getDailyRateEntryRecs);?>','<?=$recordProcessCode;?>');
		</SCRIPT>
	<?	
			}
		}
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "currentDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "currentDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "cpyFrmDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "cpyFrmDate", 
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

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>
