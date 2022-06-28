<?php
	require("include/include.php");
	require("lib/DailyRMCB_ajax.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection = "?selFishFilter=".$p["selFishFilter"]."&selFilterDate=".$p["selFilterDate"]."&pageNo=".$p["pageNo"];	
	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	$reEdit  = false;

	
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
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------

	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	= true;		
	}
	
	# Reset value
	if ($p["processCode"]) $recordProcessCode = $p["processCode"];
	if ($p["closingBalance"]) $closingBalance = trim($p["closingBalance"]);
	if ($p["selectDate"]) 	$selectedDate = $p["selectDate"];
	if ($p["selFish"])	$selFishId  = $p["selFish"];

	if ($p["cmdCancel"]!="") {
		$p["selFish"]	=  "";
		$addMode	= false;
		$editMode	= false;
		$recordProcessCode = "";
		$closingBalance = "";
		$selectedDate = "";
		$selFishId = "";
	}


	# Add 
	if ($p["cmdAdd"]!="") {
		$selectDate	= mysqlDateFormat($p["selectDate"]);
		$pcRowCount	= $p["pcRowCount"];	
		$rowCount 	= $p["hidTableRowCount"];
		$company 	= $p["company"];
		$unit 	= $p["unit"];
		
		if ($selectDate!="" && $pcRowCount!=0) {
			$c = 0;
			for ($i=1; $i<=$pcRowCount; $i++) {
				$fishId			= $p["fishId_".$i];		
				$processCodeId		= $p["processCodeId_".$i];
				$preProcessCS		= trim($p["ppmCB_".$i]);
				$productionCS		= trim($p["prodnCB_".$i]);
				$totalCS		= trim($p["totalCB_".$i]);
				$dailyRMCSEId		= $p["dailyRMCSEId_".$i];
				$reProcessedCS		= trim($p["rpmCB_".$i]);
				if ($fishId && $processCodeId && ($preProcessCS!="" || $productionCS!="" || $reProcessedCS!="") && $dailyRMCSEId=="") {
					$dailyRMCBRecIns = $dailyRMCBObj->addDailyRMCB($selectDate, $fishId, $processCodeId, $productionCS, $userId, $preProcessCS, $totalCS, $reProcessedCS,"",$company,$unit);
				} else if ($dailyRMCSEId!="") {
					$c++;
				}
			}
			if ($pcRowCount==$c) $entryExist = true;

			# ----------------------Exception Adding ------------------- 
			$fishId = "";
			$processCodeId = "";
			$preProcessCS	= "";
			$productionCS	= "";
			$totalCS	= "";
			$reProcessedCS	= "";
			for ($i=0; $i<$rowCount; $i++) {
				$status = $p["status_".$i];
				if ($status!='N') {
					$fishId			= $p["exptfishId_".$i];		
					$processCodeId		= $p["exptPCode_".$i];
					$preProcessCS		= trim($p["exptPPCS_".$i]);
					$productionCS		= trim($p["exptProdnCS_".$i]);
					$totalCS		= trim($p["exptTotalCS_".$i]);
					$reProcessedCS		= trim($p["exptRPMCS_".$i]);
					if ($fishId && $processCodeId && ($preProcessCS!="" || $productionCS!="" || $reProcessedCS!="") ) {
						$dailyRMCBRecIns = $dailyRMCBObj->addDailyRMCB($selectDate, $fishId, $processCodeId, $productionCS, $userId, $preProcessCS, $totalCS, $reProcessedCS, 'Y',$company,$unit);
					}
				}
			}
			# ----------------------Exception Ends here ------------------- 

			if ($dailyRMCBRecIns) {
				$addMode	= false;
				$editMode	= false;
				$sessObj->createSession("displayMsg",$msg_succAddDailyRMCB);
				$sessObj->createSession("nextPage",$url_afterAddDailyRMCB.$selection);
			} else {
				$addMode	=	true;
				if ($entryExist) $err = $msg_failAddDailyRMCB."<br>The selected records already exist in database.";
				else $err = $msg_failAddDailyRMCB;
			}
			$dailyRMCBRecIns	=	false;
		}
		/*	
		$fishId			= $p["selFish"];		
		$processCodeId		= $p["processCode"];
		$closingBalance		= trim($p["closingBalance"]);
		$preProcessCS		= trim($p["preProcessCS"]);		
		if ($fishId!="" && $processCodeId!="") {
			# Entry Exist
			$entryExist = $dailyRMCBObj->chkEntryExist($selectDate, $fishId, $processCodeId, '');
			if (!$entryExist) {
				$dailyRMCBRecIns = $dailyRMCBObj->addDailyRMCB($selectDate, $fishId, $processCodeId, $closingBalance, $userId, $preProcessCS);
			}					
			if ($dailyRMCBRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDailyRMCB);
				$sessObj->createSession("nextPage",$url_afterAddDailyRMCB.$selection);
			} else {
				$addMode	=	true;
				if ($entryExist) $err = $msg_failAddDailyRMCB."<br>The selected records already exist in database.";
				else $err = $msg_failAddDailyRMCB;
			}
			$dailyRMCBRecIns	=	false;
		}	
		*/	
	}

	# Update 
	if ($p["cmdSaveChange"]!="") {

		$selectDate	= mysqlDateFormat($p["selectDate"]);
		$pcRowCount	= $p["pcRowCount"];
		$rowCount 	= $p["hidTableRowCount"];
		$company 	= $p["company"];
		$unit 	= $p["unit"];
		
		if ($selectDate!="" && $pcRowCount!=0) {
			for ($i=1; $i<=$pcRowCount; $i++) {	
				$fishId			= $p["fishId_".$i];		
				$processCodeId		= $p["processCodeId_".$i];
				$preProcessCS		= trim($p["ppmCB_".$i]);
				$productionCS		= trim($p["prodnCB_".$i]);
				$totalCS		= trim($p["totalCB_".$i]);
				$dailyRMCSEId		= $p["dailyRMCSEId_".$i];
				$reProcessedCS		= trim($p["rpmCB_".$i]);

				# Delete Entry if No CS Entered
				if ($dailyRMCSEId!="" && $preProcessCS=="" && $productionCS=="" && $reProcessedCS=="") {
					$deleteDRMCBRec = $dailyRMCBObj->deleteDailyRMCB($dailyRMCSEId);
				}	 

				if ($fishId && $processCodeId && ($preProcessCS!="" || $productionCS!="" || $reProcessedCS!="") && $dailyRMCSEId=="") {
					$dailyRMCBRecIns = $dailyRMCBObj->addDailyRMCB($selectDate, $fishId, $processCodeId, $productionCS, $userId, $preProcessCS, $totalCS, $reProcessedCS);
					$dailyRMCBRecUptd = true;
				} else if ($fishId && $processCodeId && ($preProcessCS!="" || $productionCS!="" || $reProcessedCS!="") && $dailyRMCSEId!="") {			
					$dailyRMCBRecUptd = $dailyRMCBObj->updateDailyRMCB($dailyRMCSEId, $selectDate, $fishId, $processCodeId, $productionCS, $preProcessCS, $totalCS, $reProcessedCS);
				}
			} // For Loop Ends here


			# ----------------------Exception Adding ------------------- 
			$fishId = "";
			$processCodeId = "";
			$preProcessCS	= "";
			$productionCS	= "";
			$totalCS	= "";
			$reProcessedCS	= "";
			for ($i=0; $i<$rowCount; $i++) {
				$status = $p["status_".$i];
				if ($status!='N') {
					$fishId			= $p["exptfishId_".$i];		
					$processCodeId		= $p["exptPCode_".$i];
					$preProcessCS		= trim($p["exptPPCS_".$i]);
					$productionCS		= trim($p["exptProdnCS_".$i]);
					$totalCS		= trim($p["exptTotalCS_".$i]);
					$reProcessedCS		= trim($p["exptRPMCS_".$i]);
					if($fishId && $processCodeId && ($preProcessCS!="" || $productionCS!="" || $reProcessedCS!="") ) 
					{
						$dailyRMCBRecIns = $dailyRMCBObj->addDailyRMCB($selectDate, $fishId, $processCodeId, $productionCS, $userId, $preProcessCS, $totalCS, $reProcessedCS, 'Y');
					}
				}
			}
			# ----------------------Exception Ends here ------------------- 	

		} // Condition Check Ends here

		if ($dailyRMCBRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyRMCB);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyRMCB.$selection);
		} else {
			$editMode	=	true;
			if ($entryExist) $err = $msg_failUpdateDailyRMCB."<br>The selected records already exist in database.";
			else $err = $msg_failUpdateDailyRMCB;
		}
		$dailyRMCBRecUptd = false;

		/*
		$dailyRMCBId		= $p["hidDailyRateId"];
		$selectDate		= mysqlDateFormat($p["selectDate"]);
		$fishId			= $p["selFish"];		
		$processCodeId		= $p["processCode"];
		$closingBalance		= trim($p["closingBalance"]);
		$preProcessCS		= trim($p["preProcessCS"]);
		if ($dailyRMCBId!="" && $fishId!="" && $processCodeId) {
			# Entry Exist
			$entryExist = $dailyRMCBObj->chkEntryExist($selectDate, $fishId, $processCodeId, $dailyRMCBId);
			if (!$entryExist) {
				$dailyRMCBRecUptd = $dailyRMCBObj->updateDailyRMCB($dailyRMCBId, $selectDate, $fishId, $processCodeId, $closingBalance, $preProcessCS);
			}			
		}

		if ($dailyRMCBRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyRMCB);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyRMCB.$selection);
		} else {
			$editMode	=	true;
			if ($entryExist) $err = $msg_failUpdateDailyRMCB."<br>The selected records already exist in database.";
			else $err = $msg_failUpdateDailyRMCB;
		}
		$dailyRMCBRecUptd = false;
		*/
	}

	# Edit 	
	//echo $p["cmdEdit"];
	if ($p["cmdEdit"]!="" ) 
	{
		$editMode = true;
		$editId	= $p["editId"];
		$dailyRMCBRec	= $dailyRMCBObj->find($editId);
		//printr($dailyRMCBRec);
		$dailyRMCBId		=	$dailyRMCBRec[0];	
		$selectedDate		= dateFormat($dailyRMCBRec[1]);
		$selFishId		= $dailyRMCBRec[2];
		$recordProcessCode	= $dailyRMCBRec[3];
		$closingBalance		= $dailyRMCBRec[4];
		$preProcessCS		= $dailyRMCBRec[5];
		$companyId		= $dailyRMCBRec[9];
		$unitId		= $dailyRMCBRec[10];
		//echo "hiii".$companyId.$unitId;
	}	
				
	# Delete 	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailyRMCBId		= $p["delId_".$i];			
			if ($dailyRMCBId!="") {				
				$dailyRMCBRecDel = $dailyRMCBObj->deleteDailyRMCB($dailyRMCBId);		
			}
		}

		if ($dailyRMCBRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyRMCB);
			$sessObj->createSession("nextPage",$url_afterDelDailyRMCB.$selection);
		} else {
			$errDel		=	$msg_failDelDailyRMCB;
		}
	}

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# List records based on filter 
	if ($g["selFishFilter"]!="" || $g["selFilterDate"]!="") {
		$recordsFilterId	=	$g["selFishFilter"];
		$filterDate		=	$g["selFilterDate"];
	} else if($p["selFilterDate"]=="") {
		$recordsFilterId	=	$p["selFishFilter"];
		$filterDate		=	date("d/m/Y");
	} else {
		$recordsFilterId	=	$p["selFishFilter"];
		$filterDate		=	$p["selFilterDate"];
	}
	
	#Condition for Select a Fish 	
	if ($p["existRecordsFilterId"]==0 && $p["selFishFilter"]!=0 ) {
		$offset = 0;
		$pageNo = 1;
	}
		
	if ($recordsFilterId!=0 || $filterDate!="") {	
		$recordsDate	= mysqlDateFormat($filterDate);	
		$dailyRMCBRecords = $dailyRMCBObj->fetchAllPagingRecords($recordsFilterId, $recordsDate, $offset, $limit);
		$numrows = sizeof($dailyRMCBObj->fetchAllRecords($recordsFilterId, $recordsDate));
	}	
	$dailyRatesRecordsSize	= sizeof($dailyRMCBRecords);

	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# Returns all fish master records 
	$fishMasterRecords	= $fishmasterObj->fetchAllRecords();

	if ($filterDate!=date("d/m/Y")) $selectedDate = $filterDate;
	else if ($p["selectDate"]=="") $selectedDate = date("d/m/Y");
	if($addMode)
	{
		($p["company"]!="")?$companyId=$p["company"]:$companyId="";
		($p["unit"]!="")?$unitId=$p["unit"]:$unitId="";
	}

	if ($selectedDate!="") 
	{
		$selDate = mysqlDateFormat($selectedDate);

		# Check RM CB Exist
		$rmCBExist = $dailyRMCBObj->chkRMCBEntryExist($selDate);

		# Get Selected Fish
		$getFishRecords = $dailyRMCBObj->getFishRecords($selDate, '',$companyId,$unitId);
		//printr($getFishRecords);
		$fishRecSize = sizeof($getFishRecords);
		# Get All Pre-Process Code recs
		if ( $companyId!="" && $unitId!="" && $fishRecSize>0 ) {
			$dailyRMProcessCodeRecs = $dailyRMCBObj->dailyRMProcessCodeRecs($selDate, $selFishId,$companyId,$unitId);
		}
		
	}


	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
	if($companyId=="")
	{
		$units=$unitRecords[$defaultCompany];
	}
	else
	{
		$units=$unitRecords[$companyId];
	}

	# Setting Mode Here
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";
	
	# Display heading
	if ($editMode)	$heading	= $label_editDailyRMCB;
	else 		$heading	= $label_addDailyRMCB;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/DailyRMCB.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDailyRMCB" id="frmDailyRMCB" action="DailyRMCB.php" method="Post">
		<table cellspacing="0"  align="center" cellpadding="0" width="60%">
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
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRMCB.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyRMCB(document.frmDailyRMCB);">												
													</td>
													<?} else{?>
													<td align="center" colspan="4">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRMCB.php');">&nbsp;&nbsp;
														<input type="submit" id="cmdAdd" name="cmdAdd" class="button" value=" Add " onClick="return validateDailyRMCB(document.frmDailyRMCB);">
													</td>
													<?} ?>
											</tr>
											<input type="hidden" name="hidDailyRateId" value="<?=$dailyRMCBId;?>">
											<tr>
												<TD class="fieldName" colspan="4" align="center" style="text-align:center;">
												<span id="divEntryExistTxt" style='line-height:normal; font-size:14px; color:red;'></span>
												</TD>
											</tr>
											<tr>
												<td height="10" colspan="2"></td>
											</tr>	
											<tr>
												<TD colspan="2">
													<table>
														<TR>
															<TD>
																<table>
																	<tr>
																		<td class="fieldName" nowrap >*Date</td>
																		<td nowrap>
																			<input name="selectDate" type="text" id="selectDate" size="9" value="<? if($editMode==true || $selectedDate) { echo $selectedDate; } else { echo date("d/m/Y");}?>" autocomplete="off" onchange="this.form.submit();" <? if ($editMode) echo "readonly";?>/>
																		</td>
																		<td class="fieldName">Fish:</td>
																		<td nowrap>
																			<!--<select name="selFish" id="selFish" onchange="this.form.submit();" <? if ($editMode) echo "disabled";?>>-->
																			<select name="selFish" id="selFish" onchange="fishLoad(this);" <? if ($editMode) echo "disabled";?>>
																				<option value="">--Select All--</option>
																				<?php
																				foreach ($getFishRecords as $sFishId=>$sFishName)
																				{		
																					$selected = ($selFishId==$sFishId)?"selected":"";	
																				?>
																				<option value="<?=$sFishId?>" <?=$selected?>><?=$sFishName?></option>
																				<? }?>
																			</select>
																			<? if ($editMode) {?>
																			<input type="hidden" name="selFish" id="selFish" value="<?=$selFishId?>"/>
																			<? }?>
																		</td>
																		<td class="fieldName" nowrap>Company:</td>
																		<td nowrap>
																			<!--<select name="company" id="company" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >-->
																			<select name="company" id="company" <?php  if($addMode==true) { ?> onchange="fishLoad(this);" <?php } else { ?> disabled <?php }?> >
																				<option value="" >---Select--- </option>
																				<?php
																				if (sizeof($companyRecords)>0)
																				{	
																					foreach ($companyRecords as $cr=>$crs) 
																					{
																						$companyIds		= $cr;
																						$companyName	= stripSlash($crs);
																						if($companyId!="")
																						{
																							$sel  = ($companyId==$companyIds)?"Selected":"";
																						}
																						else
																						{
																							$sel  = ($defaultCompany==$companyIds)?"Selected":"";
																						}
																							//$sel  = (($company==$companyIds) || ($defaultCompany==$companyIds))?"Selected":"";
																					?>	
																					<option value="<?=$companyIds?>" <?=$sel?>><?=$companyName?></option>
																					<?
																					}
																				}
																				?>
																			</select>
																		</td>	
																		<td class="fieldName" nowrap>Unit:</td>
																		<td nowrap>
																			<!--<select name="unit" id="unit" <?php if($addMode==true) { ?> onchange="functionLoad(this);" <?php } else { ?> onchange="assignValue(this.form,'<?php echo $editId;?>','editId');this.form.submit();" <?php }?> >-->
																			<select name="unit" id="unit" <?php if($addMode==true) { ?> onchange="fishLoad(this);" <?php } else { ?> disabled <?php }?> >
																				<option value="" >---Select--- </option>
																				<?php 
																				if($units>0)
																				{
																					foreach($units as $unt=>$ut)
																					{	
																						$sel = '';
																						$unitIds=$unt;
																						$unitName=$ut;
																						if($unitId == $unitIds) $sel = 'selected';
																						?>
																						<option value="<?=$unitIds;?>" <?=$sel?> ><?=$unitName;?></option>
																						<?php
																					}
																				}
																				?>
																			</select>
																		</td>	
																	</tr>
																</table>
															</TD>
														</TR>
														<?php 
														if (sizeof($dailyRMProcessCodeRecs)) {
														?>
														<tr>
															<td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
																<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999"> 	
																	<tr bgcolor="#f2f2f2" align="center">
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">Item</td>
																		 <td class="listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">OPENING BAL</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">RM ARRIVAL</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">PPM</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">RPM</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">TOTAL RM</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">PRODN</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">CS[PPM]<br/>(Kg)</td>				
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">CS[PRODN]<br/>(Kg)</td>
																		 <td class="listing-head" style="padding-left:5px; padding-right:5px;">TOTAL CS<br/>(Kg)</td>				
																		<td class="listing-head" style="padding-left:5px; padding-right:5px;" title="Thawing Material CS">CS[RPM]<br/>(Kg)</td>
																	</tr>
																	<?php			
																	$totalRMOBQty 		= 0;	
																	$totalRMArrivalQty 	= 0;
																	$totalPreProcessedQty  = 0;
																	$totalRePreProcessedQty	= 0;
																	$totalNetQty = 0;	
																	$totalProdPackingQty = 0;
																	$prevFishId = "";
																	$j = 0;		
																	$filterPCArr = array();			                                                                                                                                                                                                         foreach ($dailyRMProcessCodeRecs as $pcr) 
																	{
																		//echo "Test1";
																		$j++;
																		$processCodeId	= $pcr[0];
																		$fishId		= $pcr[1];
																		if (!isset($filterPCArr[$fishId])) $filterPCArr[$fishId] = array();
																		array_push($filterPCArr[$fishId], $processCodeId);

																		$processCode	= $pcr[2];
																		$fishName	= $pcr[3];

																		# get RM Opening Balance
																		list($rmOBQty, $displayOBDtls) = $productionAnalysisReportObj->getRMOpeningBalance($processCodeId, $selDate,$companyId,$unitId);
																		$totalRMOBQty += $rmOBQty;
																				
																		# get RM Arival Qty
																		list($rmArrivalQty,$displayArrival) = $productionAnalysisReportObj->getRMArrivalQty($processCodeId, $selDate, $selDate,$companyId,$unitId);
																		$totalRMArrivalQty += $rmArrivalQty;
																				
																		# Get Pre-Processed Qty
																		list($preProcessedQty, $displayPPMCalc) = $productionAnalysisReportObj->getRMPreProcessedQty($processCodeId, $selDate, $selDate, $preProcessRateListId, $fishId,$companyId,$unitId);
																		$totalPreProcessedQty += $preProcessedQty;

																		# ReProcessing Qty (RPM)
																		$rePreProcessedQty = $productionAnalysisReportObj->getRMThawedQty($processCodeId, $selDate, $selDate, $fishId,$companyId,$unitId);
																		$totalRePreProcessedQty += $rePreProcessedQty;

																		# Find Net Qty
																		$netQty = $rmOBQty+$rmArrivalQty+$preProcessedQty+$rePreProcessedQty;
																		$totalNetQty += $netQty;

																		# Packing Qty (Production)
																		list($prodPackingQty, $displayProdnCalc) = $productionAnalysisReportObj->getRMPackingQty($processCodeId, $selDate, $selDate, $fishId,$companyId,$unitId);
																		$totalProdPackingQty += $prodPackingQty;
																		
																		$showProdnCalc = "";
																		if ($prodPackingQty!="") {
																			$showProdnCalc = "onMouseover=\"ShowTip('$displayProdnCalc');\" onMouseout=\"UnTip();\" ";

																		}

																		// Fish Head Display
																		if ($prevFishId!=$fishId && !$selFishId) {
																			$disMHead = '<tr bgcolor="white"><td class="fieldname" colspan="11" style="padding-left:10px; padding-right:10px; line-height:normal; text-align:left;" nowrap height="15"><b>'.$fishName.'</b></td></tr>';
																			echo $disMHead;
																		}
																		//$dailyRMCSEId = "";
																		//if ($editMode) {
																	
																		list($dailyRMCSEId, $prodnCB, $ppmCB, $totalCB, $rpmCB) = $dailyRMCBObj->getDailyCBRec($selDate, $fishId, $processCodeId,$companyId,$unitId);
																		if ($rpmCB=="")
																		{
																			$rpmCB=$productionAnalysisReportObj->getNextDayRMThawedQty($processCodeId, $selDate, $selDate, $fishId,$companyId,$unitId);
																		}
																		

																		//}
																		$readOnly = "";
																		$disRowStatus = "";
																		if ($dailyRMCSEId!="" && $addMode)
																		{
																			$readOnly = "readonly";
																			$disRowStatus = "onMouseover=\"ShowTip('Record already exist in database.');\" onMouseout=\"UnTip();\"";
																		}
																		?>
																		<tr <?=$listRowMouseOverStyle?>> 
																			<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" height="30">
																				<?=$processCode?>
																				<input type="hidden" name="fishId_<?=$j?>" id="fishId_<?=$j?>" size="9" value="<?=$fishId?>" autocomplete="off" readonly />
																				<input type="hidden" name="processCodeId_<?=$j?>" id="processCodeId_<?=$j?>" size="9" value="<?=$processCodeId?>" autocomplete="off" readonly />
																				<input type="hidden" name="dailyRMCSEId_<?=$j?>" id="dailyRMCSEId_<?=$j?>" size="9" value="<?=$dailyRMCSEId?>" autocomplete="off" readonly />
																			</td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$rmOBQty?></td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$rmArrivalQty?></td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$preProcessedQty?></td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=$rePreProcessedQty?></td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><?=($netQty!=0)?number_format($netQty,2,'.',''):"";?></td>
																			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;" <?=$showProdnCalc?> ><?=$prodPackingQty?></td>
																			<td class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px;" <?=$disRowStatus?>>
																			<input type="text" name="ppmCB_<?=$j?>" id="ppmCB_<?=$j?>" size="9" value="<?=($ppmCB!=0)?$ppmCB:"";?>" autocomplete="off" style="text-align:right;" onkeydown="return nCBTxtBox(event,'document.frmDailyRMCB','ppmCB_<?=$j?>');" onkeyup="calcTotCS();" <?=$readOnly?>/>
																			</td>			
																			<td class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px;" <?=$disRowStatus?>>
																			<input type="text" name="prodnCB_<?=$j?>" id="prodnCB_<?=$j?>" size="9" value="<?=($prodnCB!=0)?$prodnCB:""?>" autocomplete="off" style="text-align:right;" onkeydown="return nCBTxtBox(event,'document.frmDailyRMCB','prodnCB_<?=$j?>');" onkeyup="calcTotCS();" <?=$readOnly?> />
																			</td>
																			<td class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px;">
																			<input type="text" name="totalCB_<?=$j?>" id="totalCB_<?=$j?>" size="9" value="<?=($totalCB!=0)?$totalCB:""?>" autocomplete="off" style="text-align:right; border:none;" readonly />
																			</td>
																			<td class="listing-item" nowrap align="center" style="padding-left:5px; padding-right:5px;" <?=$disRowStatus?>>
																				<input type="text" name="rpmCB_<?=$j?>" id="rpmCB_<?=$j?>" size="9" value="<?=($rpmCB!=0)?$rpmCB:"";?>" autocomplete="off" style="text-align:right;" onkeydown="return nCBTxtBox(event,'document.frmDailyRMCB','rpmCB_<?=$j?>');" onkeyup="calcTotCS();" <?=$readOnly?>/>
																			</td>
																		</tr>		
																		<?php				
																		$prevFishId = $fishId;
																		} // Loop Ends here
																		?>		
																	<tr bgcolor="white">
																		<td class="listing-head" align="right" >Total:</td>
																		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRMOBQty,2,'.','');?></strong></td>  
																		<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRMArrivalQty,2,'.','');?></strong></td>
																		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalPreProcessedQty,2,'.','');?></strong></td>
																		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalRePreProcessedQty,2,'.','');?></strong></td>	
																		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalNetQty,2,'.','');?></strong></td>  
																		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalProdPackingQty,2,'.','');?></strong></td>
																		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;">
																			<input type="text" name="totalPPMCB" id="totalPPMCB" size="9" value="<?=$totalPPMCB?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly />
																		 </td>		
																		 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;">
																			<input type="text" name="totalProdCB" id="totalProdCB" size="9" value="<?=$totalProdCB?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly />
																		 </td>	
																		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
																			<input type="text" name="grandTotalCS" id="grandTotalCS" size="9" value="<?=$grandTotalCS?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly />
																		</td> 
																		<td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;">
																			<input type="text" name="totalRPMCB" id="totalRPMCB" size="9" value="<?=$totalRPMCB?>" autocomplete="off" style="text-align:right; border:none; font-weight:bold;" readonly />
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<?php
														if ($fishRecSize>0 && ($isAdmin || $reEdit)) {
														?>
														<tr>
															<TD height="5"></TD>
														</tr>
														<!-- Add New PC wise CB Starts here -->
														<tr>
															<TD>
																<table>
																	<TR>
																		<TD>
																			<fieldset>
																				<table>
																					<!--  Dynamic Row adding starts here-->
																					<tr>
																						<td colspan="2" style="padding-left:5px; padding-right:5px;">
																							<table cellspacing="1" bgcolor="#999999" cellpadding="2" id="tblExptPC">
																								<TR bgcolor="#f2f2f2" align="center">
																									<td class="listing-head" style="padding-left:5px; padding-right:5px;">*Fish</td>
																									<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">*Process Code</td>
																									<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CS[PPM]<br>(Kg)</td>
																									<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CS[PRODN]<br>(Kg)</td>
																									<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">TOTAL CS<br>(Kg)</td>
																									<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CS[RPM]<br>(Kg)</td>
																									<td>&nbsp;</td>
																								</TR>	
																							</table>
																						</td>
																					</tr>
																					<tr><TD height="10"></TD></tr>
																					<tr>
																						<TD nowrap style="padding-left:5px; padding-right:5px;">
																						<a href="###" id='addRow' onclick="javascript:addNewItemRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																						</TD>
																					</tr>
																					<!--  Dynamic Row adding ends here-->
																				</table>
																			</fieldset>
																		</TD>
																	</TR>
																</table>
															</TD>
														</tr>
														<?php
														}
														?>
														<!-- Add New PC wise CB Ends here -->
														<?php 
															# Main Condition ends here
														} 
														else if (!sizeof($getFishRecords) && $searchMode) 
														{
														?>
														<tr>
															<TD class="err1"><?=$msgNoRecords?></TD>
														</tr>
														<? }?>
														<input type="hidden" name="pcRowCount" id="pcRowCount" value="<?=$j?>" readonly />
														<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="" reaonly />
													</table>
												</TD>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="4" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRMCB.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyRMCB(document.frmDailyRMCB);">												
												</td>
												<?} else{?>
												<td align="center" colspan="4">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyRMCB.php');">&nbsp;&nbsp;
													<input type="submit" id="cmdAdd1" name="cmdAdd" class="button" value=" Add " onClick="return validateDailyRMCB(document.frmDailyRMCB);">												
												</td>
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
		}	# Listing Fish-Grade Starts
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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Daily RM Closing Balance</td>
									<td background="images/heading_bg.gif" nowrap="true">
										<table cellpadding="0" cellspacing="0" align="right">	
											<tr>				
												<td class="listing-item" nowrap > Fish:&nbsp;</td>
												<td nowrap="true">
												<!--<select name="selFishFilter" onChange="this.form.submit();">-->
													<select name="selFishFilter" onChange="fishLoad(this);">
														<option value="0"> All Fish </option>
														<? 
														if (sizeof($fishMasterRecords)>0) {
															foreach ($fishMasterRecords as $fl) {
																$fishId		=	$fl[0];
																$fishName	=	$fl[1];
																$selected = ($fishId == $recordsFilterId)?"selected":"";	
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
													if($filterDate=="") $filterDate=date("d/m/Y");
													?>
												  <input type="text" id="selFilterDate" name="selFilterDate" size="9" value="<?=$filterDate?>" onchange="this.form.submit();" autocomplete="off">&nbsp;
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
												<td><? if($del==true){?>
													<!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyRatesRecordsSize;?>);">-->
													<? }?>&nbsp;
													<? /* if($add==true && !$rmCBExist){*/?>
													<? if($add==true){?>
													<input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } else if ($edit && $rmCBExist) {?>
														<input type="submit" value=" Edit " name="cmdEdit" class="button">
													<? }?>
													&nbsp;
													<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyRMCB.php?selFishFilter=<?=$recordsFilterId?>&selFilterDate=<?=$recordsDate?>',700,600);"><? }?>
													</td>
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
											<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
											if (sizeof($dailyRMCBRecords)>0) {
												$i	=	0;
											?>
											<? if($maxpage>1){?>
												<tr bgcolor="#FFFFFF">
													<td colspan="5" style="padding-right:10px">
														<div align="right">
															<?php 				 			  
															 $nav  = '';
															for($page=1; $page<=$maxpage; $page++) {
																if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																} else {
																		$nav.= " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\" class=\"link1\">$page</a> ";
																//echo $nav;
																}
															}
															if ($pageNo > 1) {
																$page  = $pageNo - 1;
																$prev  = " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\"  class=\"link1\"><<</a> ";
															} else {
																$prev  = '&nbsp;'; // we're on page one, don't print previous link
																$first = '&nbsp;'; // nor the first page link
															}
															if ($pageNo < $maxpage)	{
																$page = $pageNo + 1;
																$next = " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\"  class=\"link1\">>></a> ";
															} else {
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
										<tr  bgcolor="#f2f2f2" align="center"  >
											<!--<td width="20" height="1">
												<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
											</td>-->
											<!--<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Date</td>-->
											<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Company</td>		
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Unit </td>
											<td nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Fish</td>		
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Process<br/> Code </td>
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Pre-Process<br/> CS (Kg)</td>
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Production<br/> CS (Kg)</td>	
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;">Re-Process<br/> CS (Kg)</td>		
											<? if($edit==true){?>
											<td class="listing-head">&nbsp;</td>
											<? }?>
										</tr>
										<?php
										$totPrdnCS	= 0;
										$totPPCS	= 0;
										$totRPCS	= 0;
										foreach ($dailyRMCBRecords as $drmcb) {
											//echo "first";
											$i++;
											$companyId=$drmcb[1];
											$unitId=$drmcb[2];
											$companyName=$drmcb[3];
											$unitName=$drmcb[4];
											$selectDate=dateFormat($drmcb[5]);
											$dailyRMCBId	= $drmcb[0];
											$selDate=$drmcb[5];
											$fishRec=$dailyRMCBObj->getFishDetail($companyId,$unitId,$selDate);
										?>
										<tr <?=$listRowMouseOverStyle?>>
											<!--<td width="20" height="1" class="listing-item">
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyRMCBId;?>" class="chkBox">
											</td>-->
											<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$selectDate?></td>-->
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$companyName;?></td>		
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$unitName?></td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishName=$fr[1];
												echo $fishName;
											} 
											?>
											</td>		
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$processCode=$pc[1];
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$processCode?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? 
											foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$preProcessCs=$pc[2];
													$totPPCS+=$preProcessCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$preProcessCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$productionCs=$pc[3];
													$totPrdnCS+=$productionCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$productionCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap>
											<? foreach($fishRec as $fr)
											{
												$fishId=$fr[0];
												$processCodes=$dailyRMCBObj->getProcessCodes($companyId,$unitId,$selDate,$fishId);
											?>
												<table>
												<? 
												foreach($processCodes as $pc)
												{
													$reProcessCs=$pc[4];
													$totRPCS+=$reProcessCs;
												?>
													<tr>
														<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" nowrap><?=$reProcessCs?></td>
													</tr>
												<? 
												}
												?>
												</table>
											<?
											} 
											?>
											</td>
											<? if($edit==true){?>
											<td class="listing-item" width="45" align="center" style="padding-left:3px; padding-right:3px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyRMCBId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyRMCB.php';"  ></td>
											<? }?>
										</tr>
										<?
											}
										?>
										<tr bgcolor="White">
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;" align="right" colspan="4">Total:</td>
											<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right">
												<strong><?=($totPPCS!=0)?number_format($totPPCS,2,'.',','):""?></strong>
											</td>
											<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right">
												<strong><?=($totPrdnCS!=0)?number_format($totPrdnCS,2,'.',','):""?></strong>
											</td>
											<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="right">
												<strong><?=($totRPCS!=0)?number_format($totRPCS,2,'.',','):""?></strong>
											</td>
											<td>&nbsp;</td>
										</tr>
										<?php
										$grandTotalCS = $totPPCS+$totPrdnCS+$totRPCS;
										?>
										<tr bgcolor="White">
											<td class="listing-head" nowarp style="padding-left:5px; padding-right:5px;" align="right" colspan="3">RM CB Total Qty:</td>
											<td class="listing-item" nowarp style="padding-left:5px; padding-right:5px;" align="left" colspan="5">
												<strong><?=($grandTotalCS!=0)?number_format($grandTotalCS,2,'.',','):""?></strong>
											</td>		
										</tr>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="<?=$editId?>">
										<input type="hidden" name="editSelectionChange" value="0">
										<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="5" style="padding-right:10px">
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
													$nav.= " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1)
													{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\"  class=\"link1\"><<</a> ";
													}
													else
													{
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage)
														{
														$page = $pageNo + 1;
														$next = " <a href=\"DailyRMCB.php?selFishFilter=$recordsFilterId&pageNo=$page&selFilterDate=$filterDate\"  class=\"link1\">>></a> ";
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
										<tr bgcolor="white">
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
											<td>
												<? if($del==true){?>
												<!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyRatesRecordsSize;?>);">-->
												<? }?>&nbsp;
												<?/* if($add==true && !$rmCBExist){ */?>
												<? if($add==true){?>
													<input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } else if ($edit && $rmCBExist) {?>
													<input type="submit" value=" Edit " name="cmdEdit" class="button">
												<? }?>
												&nbsp;
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyRMCB.php?selFishFilter=<?=$recordsFilterId?>&selFilterDate=<?=$recordsDate?>',700,600);"><? }?></td>
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
	if ($editMode || $recordProcessCode) {
	?>
		<SCRIPT LANGUAGE="JavaScript">		
			/* Get Process Code Records*/
			//xajax_getProcessCodeRecs(document.getElementById('selFish').value,'<?=$recordProcessCode?>');
		</SCRIPT>
	<? }?>	
	<?php
	if ($addMode || $editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		calcTotCS();
	</script>
	<?php
		}
	?>

	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selFilterDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selFilterDate", 
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
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<script type="text/javascript" language="javascript">
	/*
	function init_fields()
	{
		alert("h");
		var el, els, e, f = 0, form, forms = document.getElementsByTagName('form');
		while (form = forms.item(f++))
		{
			e = 0; els = form.getElementsByTagName('input');
			while (el = els.item(e++))
				if (el.readOnly || el.readonly)
					
					el.className = 'readonly';
		}
	}
	*/
	//window.onLoad = init_fields();
	</script>
	<?php 
		if ($addMode || $editMode ) {
	?>
		<script language="JavaScript">
			function addNewItemRow()
			{
				addNewPCItemRow('tblExptPC');
			}
			
			<?php
			if (sizeof($filterPCArr)>0 && $fishRecSize>0) {
				foreach ($filterPCArr as $filterFishId=>$fpc) {		
					$pCodes = implode(":",$fpc);
			?>
				pcArr['<?=$filterFishId?>'] = '<?=$pCodes?>';
			<?php
				}
			}
			?>
		</script>
	<?php 
		}
	?>
	<?php
		if ( ($addMode || $editMode) && $fishRecSize>0 && ($isAdmin || $reEdit) && $companyId!="" && $unitId!="") {
	?>
	<script language="JavaScript">
		window.onLoad = addNewItemRow();
		xajax_getDailyClosingBalance('<?=$selectedDate?>','<?=$selFishId?>','<?=$companyId?>','<?=$unitId?>','<?=$mode?>');
	</script>
	<?php
		 }
	?>		
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>