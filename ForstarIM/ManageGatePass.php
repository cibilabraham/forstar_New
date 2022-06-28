<?php
	require("include/include.php");
	require_once("lib/ManageGatePass_ajax.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$selection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

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
	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId,$urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;
	//echo "$urlFnId, $urlModuleId, $urlSubModuleId;;; ==>$refreshTimeLimit";	
	/*-----------------------------------------------------------*/

	# Add Category Start 
	if ($p["cmdAddNew"]!="") {
		$addMode  =   true;
		//print_r($p);
		/*
		if($p['company']!="") $company_id = $p['company'];
		else $company_id = $p['hid_defcomp'];
		if($p['unit']!="") $unit_id = $p['unit']; 
		else $unit_id = $p['hid_unit']; 		
		//print_r($p);

		$gatePassNo = $manageGatePassObj->getNextGatePassNo($company_id,$unit_id);
		$sessObj->updateSession("soRedirectUrl",'');
		if($gatePassNo==""){
		 $PurchaseOrderMsg="Please set the Purchase Order Id in Settings"; 
		 //$elementstr = "document.getElementById('divPOIdExistTxt').innerHTML";
		 //echo "<script language='javascript'>";
		 //echo("alert('".$elementstr."');");
		 //echo("document.getElementById('divPOIdExistTxt').innerHTML='jjjjjj';");
		 //echo("</script>");
		 
		 //$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
		}
		//echo "hjh".$gatePassNo ;
	*/
	}

	# Redirect Section Starts Here
	$urlFrom = $g["urlFrom"];		
	if ($urlFrom) {			
		$extractUrl = stristr(curPageURL(), "?");	
		$soRedirectUrl = "SalesOrder.php$extractUrl";
		$sessObj->createSession("soRedirectUrl",$soRedirectUrl);
	} 
	$redirectUrl = $sessObj->getValue("soRedirectUrl");
	
	# Redirect Section Ends Here

	if ($p["cmdCancel"]!="" || $p["editingGatePassId"]!="") {
		$addMode   =  false;
		$editMode  = false;
		$sessObj->updateSession("soRedirectUrl",'');

		$cGPId = $sessObj->getValue("gatePassId");
		# Update Rec
		if ($cGPId!=0) {
			$updateModifiedRec = $manageGatePassObj->updateMGPModifiedRec($cGPId, '', 'U');
			$sessObj->updateSession("gatePassId",0);
		}

		$editId = "";
		$p["editId"] = "";
		$editGatePassId = "";
	}

	if ($p["selSOId"]!="")	$selSOId = $p["selSOId"];

	# Add a Record	
	if ($p["cmdAdd"]!="") {	

		$gatePassNo 	= $p["gatePassNo"];
		$partyAddress	= addSlash(trim($p["partyAddress"]));
		$consignmentDetails = addSlash(trim($p["consignmentDetails"]));
		$vehicleNo	= addSlash(trim($p["vehicleNo"]));
		$company 	= $p["company"];
		$unit 	= $p["unit"];
		$numbergenId 	= $p["number_gen_id"];
		if ($gatePassNo!="") {

			$gatePassRecIns = $manageGatePassObj->addGatePass($gatePassNo, $partyAddress, $consignmentDetails, $vehicleNo, $userId,$company,$unit,$numbergenId);

			if ($gatePassRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddManageGatePass);
				$sessObj->createSession("nextPage",$url_afterAddManageGatePass.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddManageGatePass;
			}
			$gatePassRecIns = false;
		} else {
			$addMode = true;
			$err = $msg_failAddManageGatePass;
		}
	}


	
	# Update a Record
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveConfirm"]!="") {
		
		$gatePassId 	= $p["hidGatePassId"];
		$gatePassNo 	= $p["gatePassNo"];
		//echo $gatePassNo;
		//die();
		$partyAddress	= addSlash(trim($p["partyAddress"]));
		$consignmentDetails = addSlash(trim($p["consignmentDetails"]));
		$vehicleNo	= addSlash(trim($p["vehicleNo"]));
		$company 	= $p["company"];
		$unit 	= $p["unit"];
		$numbergenId 	= $p["number_gen_id"];

		if ($p["cmdSaveConfirm"]!="") $gPassConfirm = 'C';
		else $gPassConfirm = 'P';		

		$selSOId		= $p["hidSelSOId"];
		$alreadyConfirmed = $p["alreadyConfirmed"];

		$canUpdate = false;
		if ($alreadyConfirmed=="" || $isAdmin==true || $reEdit==true) {
			$canUpdate = true;
		}	

		if ($gatePassId!="" && $canUpdate) {
			# Update main Table
			$company = $p["hidcompany"];
			$unit = $p["hidunit"];
			$numbergenId = $p["hidnugenid"]; 
			
			//echo("djjd".$company_id);
			//exit;
			$pkngInstRecUptd = $manageGatePassObj->updateGatePass($gatePassId, $partyAddress, $consignmentDetails, $vehicleNo, $userId, $gPassConfirm, $gatePassNo,$company,$unit,$numbergenId);
		} // Main Condition ends here	

		# Update Sales Order 
		if ($gPassConfirm=='C' && $canUpdate) {
			$updateSalesOrderRec = $manageGatePassObj->updateSOGatePass($selSOId, "Y");	
		} else if ($alreadyConfirmed!="" && $gPassConfirm!='C') {
			$updateSalesOrderRec = $manageGatePassObj->updateSOGatePass($selSOId, "N");
		}

		if ($pkngInstRecUptd) {
			# Update Rec
			if ($gatePassId!=0) {
				$updateModifiedRec = $manageGatePassObj->updateMGPModifiedRec($gatePassId, '', 'U');
				$sessObj->updateSession("gatePassId",0);
			}
			$sessObj->createSession("displayMsg",$msg_succManageGatePassUpdate);
			if ($redirectUrl!="") $sessObj->createSession("nextPage",$redirectUrl);	
			else $sessObj->createSession("nextPage",$url_afterUpdateManageGatePass.$selection);
		} else {
			$editMode	=	true;
			$err = $msg_failManageGatePassUpdate;
		}
		$pkngInstRecUptd	=	false;
	}



	
	
	# Edit  a Record
	if ($p["editId"]!="" || $g["editMode"]) {
		if ($g["soId"]!="") {
			$uSOId	= trim($g["soId"]);
			$editId	=	$manageGatePassObj->getGPEditId($uSOId);
		} else $editId		=	$p["editId"];
		$editMode	=	true;

		# Chk already modified
		$selUsername = $manageGatePassObj->chkMGPRecModified($editId);	
		if ($selUsername && $g["editId"]=="") {
			$err	= "<b>$selUsername has been editing this record.</b>";	
			$editMode = false;
			$editId = "";
		}

		$gatePassRec	= $manageGatePassObj->find($editId);

		$editGatePassId	= $gatePassRec[0];
		$sessObj->createSession("gatePassId",$editGatePassId);
		# Update Rec
		if ($editGatePassId) $updateModifiedRec = $manageGatePassObj->updateMGPModifiedRec($editGatePassId, $userId, 'E');

		$selSOId	= $gatePassRec[1];
		if ($selSOId) {
			list($selSO, $selDistributorId, $selStateId, $selCityId, $selAreaId, $selInvType, $selPFNO, $selSANo) = $manageGatePassObj->getSOMainRec($selSOId);
			$invNo = getInvFormat($selInvType, $selSO, $selPFNO, $selSANo);
			$distributorRec		= $salesOrderObj->getDistributorRec($selDistributorId, $selStateId, $selCityId, $selAreaId);	
			$distributorName	= stripSlash($distributorRec[2]);
			$address		= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[12]));
		}
		$invType = $gatePassRec[2];
		$soNo 	= $gatePassRec[3];
		$pfNo 	= $gatePassRec[4];
		$saNo	= $gatePassRec[5];				
		$sInvoiceNo = "";
		if ($soNo!=0) $sInvoiceNo=$soNo;
		else if ($invType=='T') $sInvoiceNo = "P$pfNo";
		else if ($invType=='S') $sInvoiceNo = "S$saNo";	
		//$gatePassNo 	= ($gatePassRec[8]!=0)?$gatePassRec[8]:"";
		//rekha updated code here 
		$gatePassNo = $gatePassRec[8];
		//end 
		
		//$gatePassNo 	= ($gatePassRec[8]!=0)?$gatePassRec[8]:$manageGatePassObj->getNextGatePassNo();
		$partyAddress	= stripSlash($gatePassRec[9]);
		$consignmentDetails = stripSlash($gatePassRec[10]);
		$vehicleNo	= stripSlash($gatePassRec[11]);
		$gatePassConfirmChk	= ($gatePassRec[7]=='C')?"checked":"";	
		$company	= $gatePassRec[12];
		$unit	= $gatePassRec[13];
		$numbergenId= $gatePassRec[14];
	}
	
	
	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$gatePassId	= $p["delId_".$i];
			$gatePassStatus	= $p["hidGatePassStatus_".$i];
			$soId		= $p["hidSOId_".$i];

			if ($gatePassId!="" && $gatePassStatus!='C') {

				$gatePassRecDel = $manageGatePassObj->deleteGatePass($gatePassId);
				# update So
				if ($gatePassRecDel) {
					$updateSOMainRec = $manageGatePassObj->changeSOStatusRec($soId);
				}
			}
		}
		if ($gatePassRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelManageGatePass);
			$sessObj->createSession("nextPage",$url_afterDelManageGatePass.$selection);
		} else {
			$errDel	=	$msg_failDelManageGatePass;
		}
		$gatePassRecDel	=	false;
	}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}

	# List all gate pass recs
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
	
		$gatePassRecords 	= $manageGatePassObj->fetchAllPagingRecords($offset, $limit, $fromDate, $tillDate);
		$gatePassRecordSize	= sizeof($gatePassRecords);

		$fetchAllGPassRecs	= $manageGatePassObj->fetchAllRecords($fromDate, $tillDate);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllGPassRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else $mode = "";

	#heading Section
	if ($editMode) $heading	= $label_editManageGatePass;
	else	       $heading	= $label_addManageGatePass;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/ManageGatePass.js";
	
	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

	?>
	
<form name="frmManageGatePass" id="frmManageGatePass" action="ManageGatePass.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="75%" >
	<?php
	if (sizeof($gatePassRecords)>0) 
	{
	?>	
		<tr>
			<td align="center" id="refreshMsgRow" class="err1" style="font-size:9pt;line-height:20px;">	
			</td>			
		</tr>
	<?php
	}
	?>
		<tr>
			<TD height="10">
			<input type="hidden" name="hidSelSOId" value="<?=$selSOId?>">
			</TD>
		</tr>
		<?
		if ($editMode) 
		{
		?>
		<tr>
			<TD height="5"></TD>
		</tr>
		<tr>
			<td align="center" id="timeTickerRow" class="err1" height="20" style="font-size:14pt;" onMouseover="ShowTip('Time remaining to cancel the selected record.');" onMouseout="UnTip();">	
			</td>			
		</tr>
		<?
		}
		?>	
		<tr>
			<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			<td></td>
		</tr>
		<?
		if ( $editMode || $addMode) {
			
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<input type="hidden" name="hidGatePassId" id="hidGatePassId" value="<?=$editGatePassId;?>">
											<input type="hidden" name="hidcompany" id="hidcompany" value="<?=$company;?>">
											<input type="hidden" name="hidunit" id="hidunit" value="<?=$unit;?>">
											
											<input type="hidden" name="hidnugenid" id="hidnugenid" value="<?=$numbergenId;?>">
											<tr>
												<td colspan="2" nowrap>
													
													<table width="200">
														<tr>
															<td class="fieldName" nowrap="nowrap">*Company</td>
															<td nowrap>
																<select name="company" id="company"  onchange="xajax_getUnit(this.value,'','');" <?if($editMode){ ?> disabled<? } ?>>
																	<option value="">-- Select --</option>
																	<? 
																	foreach($companyRecords as $cr=>$crName)
																	{
																		$companyId	=	$cr;
																		$companyNm	=	stripSlash($crName);
																		$sel="";
																		//if(($companyId== $company) || ($company=="" && $companyId==$defaultCompany)) $sel	=	"selected";
																		if($companyId== $company) $sel	=	"selected";

																	?>
																	<option value="<?=$companyId?>" <?=$sel?>><?=$companyNm?></option>
																	<? }?>
																</select>		
															</td>
														</tr>
														<tr>
															<td class="fieldName" nowrap="nowrap">*Unit</td>
															<td nowrap>
																<select id="unit" name="unit" required onchange="xajax_getPONumber(document.getElementById('company').value,document.getElementById('unit').value);" <?if($editMode){ ?> disabled<? } ?>>
																	<option value="">--select--</option>
																	<?php
																	if($company!=""){
																	$units=$unitRecords[$company];}
																    else{$units=$unitRecords[$defaultCompany];}
																	if(sizeof($units) > 0)
																	{
																		//$ind=1;
																		foreach($units as $untId=>$untName)
																		{
																			
																			//if($ind=1)
																			$unitId=$untId;
																			$unitName=$untName;
																			/*
																			$sel = '';
																			//if($unit == $unitId) $sel = 'selected';
																			if($ind=1){ 
																				$def_unit= $unitId;
																				$sel = 'selected' ;
																			}	*/																			
																			
																			if($unit==$unitId) $sel = 'selected';
																			echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																		//$ind++;
																		}
																	}
																	?>
																</select>	
															</td>
														</tr>
														<tr>
															<TD class="fieldName" nowrap="true">Gate Pass No:</TD>
															<td>
																<input type="text" name="gatePassNo" id="gatePassNo" value="<?=$gatePassNo?>" size="16" style="font-weight:bold; border:none;" readonly="true">
																<input type="hidden" name="number_gen_id" id="number_gen_id"/>
																<br/><span id="divPOIdExistTxt" class="listing-item" style="line-height:normal; font-size:10px; color:red;"><?=$PurchaseOrderMsg; ?></span>
															</td>
														</tr>
														<?php
														if($selSOId)
														{
														?>
														<tr>
															<TD class="fieldName" nowrap="true">Invoice No:</TD>
															<td class="listing-item">
																<?=$invNo?>
															</td>
														</tr>
														<?php
														}
														?>
														<tr>
															<TD class="fieldName" nowrap="true" valign="top">Name & Address:</TD>
															<?php
															if(!$selSOId) 
															{
															?>
															<td>
																<textarea name="partyAddress" id="partyAddress" rows="3"><?=$partyAddress?></textarea>	
															</td>
															<? 
															} 
															else if ($selSOId!="") 
															{
															?>
															<td valign="top">
																<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
																	<tr>
																		<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="font-size:11px;">
																			<strong>M/S.&nbsp;<?=$distributorName?></strong>
																		</td>
																	</tr>
																	<tr> 
																		<td class="listing-item" width='350' height="20" colspan="2" style="font-size:11px;">
																			<?=$address?>
																		</td>
																	</tr>
																</table>
															</td>
															<? }?>
														</tr>
														<tr>
															<TD class="fieldName" nowrap="true">*Details of the Consignment:</TD>
															<td>
																<textarea name="consignmentDetails" id="consignmentDetails" rows="4"><?=$consignmentDetails?></textarea>	
															</td>
														</tr>
														<tr>
															<TD class="fieldName" nowrap="true">Vehicle No:</TD>
															<td>
																<input type="text" name="vehicleNo" id="vehicleNo" value="<?=$vehicleNo?>" size="15">
															</td>
														</tr>	
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center" nowrap="true">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageGatePass.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateGatePass(document.frmManageGatePass, false);">	
													&nbsp;&nbsp;
													<input type="submit" name="cmdSaveConfirm" id="cmdSaveConfirm" class="button" value=" Save & Confirm " onClick="return validateGatePass(document.frmManageGatePass, true);">	
													<input type="hidden" name="alreadyConfirmed" id="alreadyConfirmed" value="<? if($gatePassConfirmChk) echo 'Y';?>">	
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageGatePass.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateGatePass(document.frmManageGatePass, true);">			
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
		<?
		}	# Listing Category Starts
		?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white" nowrap="true">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" >&nbsp;Manage Gate Pass</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td nowrap="nowrap">
													<table cellpadding="0" cellspacing="0">
														<tr>
															<td class="listing-item"> From:</td>
															<td nowrap="nowrap"> 
															<?php 
															if ($dateFrom=="") $dateFrom=date("d/m/Y");
															?>
															<input type="text" name="selectFrom" id="selectFrom" size="8" value="<?=$dateFrom?>" autocomplete="off" />
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="listing-item"> Till:</td>
															<td> 
															<? 
															if($dateTill=="") $dateTill=date("d/m/Y");
															?>
																<input type="text" name="selectTill" id="selectTill" size="8"  value="<?=$dateTill?>" autocomplete="off" />
															</td>
															<td class="listing-item">&nbsp;</td>
															<td>
															<?php 
															//echo $defaultCompany;
																	//Rekha added code here //
																	/*
																	$units_rs=$unitRecords[$defaultCompany];
																	if(Sizeof($units_rs)>0)
																	{
																		$ind=1;
																		foreach($units_rs as $untId=>$untName)
																		{
																		$unitId=$untId;
																		$unitName=$untName;
																			if($ind=1)$def_unit= $unitId;
																		}
																		$ind++;
																	}
																	//end code 	
																	//print_r($units_rs);
															*/
															?>
																<!--
																<input type="text" name="hid_unit" id="hid_unit" value='<?=$def_unit ;?>'>
																<input type="text" name="hid_defcomp" id="hid_defcomp" value="<?=$defaultCompany ;?>">
																-->
																<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search">
															</td>
															<td class="listing-item" nowrap >&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gatePassRecordSize;?>);" ><? }?>&nbsp;
												<? if($add==true){?>
													<input type="submit" value=" Add New " name="cmdAddNew" class="button">
												<? }?>&nbsp;	
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
									<td colspan="2" style="padding-left:10px;padding-right:10px;">
										<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
										<?php
										if ($gatePassRecordSize) 
										{
											$i	=	0;
										?>
										<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="9" align="right" style="padding-right:10px;">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
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
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Gate Pass No</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Invoice No</td>	
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Gate Pass Date</td>
												<? if($print==true){?>	
												<td class="listing-head" style="padding-left:10px; padding-right:10px;"></td>
												<? }?>
												<? if($edit==true){?>
												<td class="listing-head"></td>
												<? }?>
											</tr>
											<?php
											//print_r($gatePassRecords);
											
											foreach ($gatePassRecords as $pir) {
												$i++;
												$gatePassId = $pir[0];
												$sDistributorName    = $pir[6];
												$invType = $pir[2];
												$soNo 	= $pir[3];
												$pfNo 	= $pir[4];
												$saNo	= $pir[5];				
												$invoiceNo = getInvFormat($invType, $soNo, $pfNo, $saNo);
												$selInvoiceType = "";
												if ($invType=='T' && $soNo!=0) $selInvoiceType = $invoiceTypeArr['TI'];
												else if ($invType=='T' && $pfNo!=0) $selInvoiceType = $invoiceTypeArr['PI'];
												else if ($invType=='S') $selInvoiceType = $invoiceTypeArr['SI'];	
															
												# --------------- Edit Section ---------------
												$editedTimeInSec = ($pir[11]!="")?$pir[11]:0; // In seconds
												//echo "<br>$editedTimeInSec>=$refreshTimeLimit";
												if ($editedTimeInSec>=$refreshTimeLimit) { 
													# Update Rec
													$updateModifiedRec = $manageGatePassObj->updateMGPModifiedRec($gatePassId, '', 'U');
												}
												$modifiedBy	= $pir[9];
												$displayEditStatus = "";
												if ($modifiedBy!=0) {
													$lockedUser = $manageusersObj->getUsername($modifiedBy);
													$displayEditStatus = "Locked by $lockedUser";
												}
												# ------------------------------

												$gatePassStatus  = $pir[7];
												$displayGatePassStatus = ($gatePassStatus=='C')?"COMPLETE":"PENDING";

												$displayColor = "";
												if ($gatePassStatus=='C') $displayColor = "#90EE90"; // LightGreen		
												else $displayColor = "white";
												
												$soId	= $pir[1];
												$gPassNo = $pir[8];
												$invConfirmStatus	= $pir[12];
												$disableRow = "";
												if ($gatePassStatus=='C' && $invConfirmStatus=='C') {
													$disableRow = "disabled";
												} else if (($gatePassStatus=='C' && !$isAdmin && !$reEdit) || $modifiedBy!=0) {
													$disableRow = "disabled";
												} 
												$gPass_date	= dateFormat($pir[13]);
												
												
											?>
											<tr  bgcolor="WHITE">
												<td width="20">
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$gatePassId;?>" class="chkBox">
													<input type="hidden" name="hidGatePassStatus_<?=$i;?>" value="<?=$gatePassStatus?>">
													<input type="hidden" name="hidSOId_<?=$i;?>" id="hidSOId_<?=$i;?>" value="<?=$soId?>">
												</td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$gPassNo;?></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$invoiceNo;?></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" bgcolor="<?=$displayColor?>"><?=$displayGatePassStatus;?></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" ><?=$gPass_date;?></td>
												
												<? if($print==true){?>	
												<!-- KD Added-->
												<? $comprecs = $manageGatePassObj->fetchcompanyrecs($gatePassId);
														$compID = $comprecs[0]; 
												?>	
												<!-- KD Added Ends-->												
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
													<? if($print==true && $gatePassStatus=='C'){?>			
													<!-- <a href="javascript:printWindow('PrintGatePass.php?selSOId=<?=$soId?>&gatePassId=<?=$gatePassId?>',700,600)" class="link1" title="Click here to Print gate Pass">
													PRINT
													</a> --> <!-- KD comment -->
													<a href="javascript:printWindow('PrintGatePass.php?selSOId=<?=$soId?>&gatePassId=<?=$gatePassId?>&companyId=<?=$compID?>',700,600)" class="link1" title="Click here to Print gate Pass">
													PRINT
													</a> <!-- KD Added-->
													<? }?>
												</td>
												<? }?>
												<? if($edit==true){?>
												<td class="listing-item" width="60" align="center" style="line-height:normal;">
													<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$gatePassId;?>,'editId');this.form.action='ManageGatePass.php';" <?=$disableRow?>>
													<?php
														if ($displayEditStatus!="") {
													?>
													<br/>
													<span class="err1" style="line-height:normal;font-size:8px;"><?=$displayEditStatus?></span>
													<? }?>
												</td>
												<? }?>
											</tr>
											<?php
												}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="9" align="right" style="padding-right:10px;">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"ManageGatePass.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
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
											<?php
											}
											else
											{
											?>
											<tr bgcolor="white">
												<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
											}
											?>
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
												<td>
													<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$gatePassRecordSize;?>);" ><? }?>&nbsp;
													<? if($add==true){?>
														<input type="submit" value=" Add New " name="cmdAddNew" class="button">
													<? }?>&nbsp;
												</td>
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
		<tr>
			<td height="10">
				<input type="hidden" name="editingGatePassId" id="editingGatePassId" value="" readonly="true" />
			</td>
		</tr>
	</table>
	<?php
		if ($editMode) {
	?>
	<script>
		// Set time D=300
		tickTimer(<?=$refreshTimeLimit?>, '<?=$editGatePassId?>');
	</script>
		<?
		if($gatePassNo=="")
		{
		?>
		<script>
		xajax_getPONumber('<?=$company?>','<?=$unit?>');
		</script>
		<?
		}
	 }?>
	<?php
		if (!$addMode && !$editMode && sizeof($gatePassRecords)>0) {
	?>
	<script>
		window.load = beginrefresh();
	</script>
	<? }?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>