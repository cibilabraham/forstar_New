<?
	require("include/include.php");
	require_once('lib/soaking_ajax.php');
	
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	$genReqNumber	= "";

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	$requestNo		= $p["requestNo"];
	$selDepartment		= $p["selDepartment"];

	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$requestNo 	= "";
		$selDepartment  = "";
		//$hidEditId 	= "";
	}
	// end

	# Add RM soaking Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	

	#Add
	if ($p["cmdAdd"]!="" ) {
			
		$rmlotId		=	$p["rmlotId"];
		$currentProcessingStage		=	$p["currentProcessingStage"];
		$supplierDetails		=	$p["supplierDetails"];
		$availableQuantity		=	$p["availableQuantity"];
		$soakInCount		=	$p["soakInCount"];
		$soakInQuantity		=	$p["soakInQuantity"];
		$soakInTime		=	$p["soakInTime"];
		$soakOutCount		=	$p["soakOutCount"];
		$soakOutQunatity		=	$p["soakOutQunatity"];
		$soakOutTime		=	$p["soakOutTime"];
		$temperature		=	$p["temperature"];
		$gain		=	$p["gain"];
		$chemcalUsed		=	$p["chemcalUsed"];
		$chemcalQty		=	$p["chemcalQty"];
		
		
		
		if ($rmlotId!="" ) {	
			$soakingRecIns	=	$soakingObj->addSoaking($rmlotId, $currentProcessingStage, $supplierDetails, $availableQuantity, $soakInCount,$soakInQuantity,$soakInTime,$soakOutCount,$soakOutQunatity,$soakOutTime,$temperature,$gain,$chemcalUsed,$chemcalQty ,$userId);
				
			

			if ($soakingRecIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddSoaking);
				$sessObj->createSession("nextPage",$url_afterAddSoaking.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSoaking;
			}
			$soakingRecIns		=	false;
		}	
	}
	

	# Edit soaking Data
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$soakingRec	=	$soakingObj->find($editId);		
		$editsoakngDataId	=	$soakingRec[0];		
		$rmlotId		=	$soakingRec[1];		
		$currentProcessingStage	=	$soakingRec[2];
		$supplierDetails	=	$soakingRec[3];
		$availableQuantity	=	$soakingRec[4];
		$soakInCount	=	$soakingRec[5];
		$soakInQuantity	=	$soakingRec[6];
		$soakInTime	=	$soakingRec[7];
		$soakOutCount	=	$soakingRec[8];
		$soakOutQunatity	=	$soakingRec[9];
		$soakOutTime	=	$soakingRec[10];
		$temperature	=	$soakingRec[11];
		$gain	=	$soakingRec[12];
		$chemcalUsed	=	$soakingRec[13];
		$chemcalQty	=	$soakingRec[14];
		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$soakingDataId	=	$p["hidSoakingId"];		
		$rmlotId		=	$p["rmlotId"];
		$currentProcessingStage		=	$p["currentProcessingStage"];
		$supplierDetails		=	$p["supplierDetails"];
		$availableQuantity		=	$p["availableQuantity"];
		$soakInCount		=	$p["soakInCount"];
		$soakInQuantity		=	$p["soakInQuantity"];
		$soakInTime		=	$p["soakInTime"];
		$soakOutCount		=	$p["soakOutCount"];
		$soakOutQunatity		=	$p["soakOutQunatity"];
		$soakOutTime		=	$p["soakOutTime"];
		$temperature		=	$p["temperature"];
		$gain		=	$p["gain"];
		$chemcalUsed		=	$p["chemcalUsed"];
		$chemcalQty		=	$p["chemcalQty"];
				
		

		if ($soakingDataId!="" && $rmlotId!="" && $currentProcessingStage!="" && $supplierDetails!="" && $availableQuantity!="" && $soakInCount!="" && $soakInQuantity!="" && $soakInTime!=""&& $soakOutCount!="" && $soakOutQunatity!="" && $soakOutTime!="" && $temperature!="" && $gain!="" && $chemcalUsed!="" && $chemcalQty!=""  ) {
			$soakingDataRecUptd	=	$soakingObj->updateSoaking($soakingDataId, $rmlotId, $currentProcessingStage,$supplierDetails,$availableQuantity,$soakInCount,$soakInQuantity,$soakInTime, $soakOutCount,$soakOutQunatity,$soakOutTime,$temperature,$gain,$chemcalUsed,$chemcalQty);
							
		}	
		if ($soakingDataRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSoakingUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSoaking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSoakingUpdate;
		}
		$soakingDataRecUptd	=	false;		
	}
	
	# Delete soaking Data
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$soakingDataId	=	$p["delId_".$i];

			if ($soakingDataId!="" && $isAdmin!="") {

				$soakingDataRecDel =	$soakingObj->deleteSoaking($soakingDataId);	
			}
		}
		if ($soakingDataRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSoaking);
			$sessObj->createSession("nextPage",$url_afterDelSoaking.$selection);
		} else {
			$errDel	=	$msg_failDelSoaking;
		}
		$soakingDataRecDel	=	false;
		
	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
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
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	#List all soaking Data
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$soakingDataRecords	= $soakingObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$soakingDataSize	= sizeof($soakingDataRecords);
		$fetchAllsoakingDataRecs = $soakingObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllsoakingDataRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	//$rmLotId	= $unitTransferObj->fetchAllRecords();
	$rmLotId	= $rmTestDataObj->fetchAllRecordsRMLotId();
	$chemicalTypes	= $harvestingChemicalMasterObj->fetchAllChemicalRecordsActive();
	$processTypes	= $rmReceiptGatePassObj->fetchAllProcessType();
	
	
	if ($editMode) $heading	=	$label_editSoaking;
	else $heading	=	$label_addSoaking;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/Soaking.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSoaking" action="Soaking.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateSoaking(document.frmSoaking);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateSoaking(document.frmSoaking);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidSoakingId" value="<?=$editsoakngDataId;?>">
											<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			<table width="100%" align="center">
				
				<tr>	
					<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
					
							<tr>
								   <td class="fieldName" nowrap>*RM Lot Id:</td>
								 
												<td  height="10" ><select name="rmlotId" id="rmlotId" onchange="xajax_lotDetails(document.getElementById('rmlotId').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($rmLotId as $un)
													{
														$lot		=	$un[0];
														$lotName	=	stripSlash($un[1]);
															$selected = ($rmlotId==$lot)?"selected":""
														
											?>
											  <option value="<?=$lot?>" <?=$selected?>><?=$lotName?></option>
											  <? }?>
										        </select>										      </td>
											
                             </tr>
								<tr>		<td class="fieldName" nowrap>*Current Processing Stage:&nbsp;</td>						 
										<td  height="10" ><select name="currentProcessingStage" id="currentProcessingStage" >
											  <option value="">--select--</option>
											  <?
												foreach($processTypes as $rm)
													{
														 $cprocessTypeId		=	$rm[0];
														 $cprocessTypeName	=	stripSlash($rm[1]);
														$selected = ($currentProcessingStage==$cprocessTypeId)?"selected":""
														
											?>
											  <option value="<?=$cprocessTypeId?>" <?=$selected?>><?=$cprocessTypeName?></option>
											  <? }?>
										        </select>										      </td>
								</tr>
								<tr>
							   <td class="fieldName" nowrap>*Supplier challan No:</td>
							   <td><INPUT TYPE="text" NAME="supplierDetails" id="supplierDetails" size="15" value="<?=$supplierDetails?>"></td>
							   </tr>
							   <tr>
							   <td class="fieldName" align='right' nowrap>*Available Quantity:&nbsp;</td>
                                        <td><INPUT TYPE="text" NAME="availableQuantity" id="availableQuantity" size="15" value="<?=$availableQuantity?>"></td>
							   
							</tr>
						</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>
				
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
								
								<!--<tr>
                                	<td class="fieldName" nowrap>*Current Processing Stage:&nbsp;</td>
                                     
										<td  height="10" ><select name="currentProcessingStage" id="currentProcessingStage" >
											  <option value="">--select--</option>
											  <?
												foreach($processTypes as $rm)
													{
														 $cprocessTypeId		=	$rm[0];
														 $cprocessTypeName	=	stripSlash($rm[1]);
														$selected = ($currentProcessingStage==$cprocessTypeId)?"selected":""
														
											?>
											  <option value="<?=$cprocessTypeId?>" <?=$selected?>><?=$cprocessTypeName?></option>
											  <? }?>
										        </select>										      </td>
							   </tr>-->
							
							
												
						  
							  <!--<tr>
                                	<td class="fieldName" align='right'>*Available Quantity:&nbsp;</td>
                                        <td><INPUT TYPE="text" NAME="availableQuantity" id="availableQuantity" size="15" value="<?=$availableQuantity?>"></td>
                                                </tr> -->
												
							 <tr>
                                	<td class="fieldName" align='right'>*Soak in-count:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakInCount" id="soakInCount" size="15" value="<?=$soakInCount?>"></td>
							</tr>
							<tr>
									   <td class="fieldName" align='right'>*Soak in-quantity:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakInQuantity" id="soakInQuantity" size="15" value="<?=$soakInQuantity?>"></td>
                            </tr>
							
							<!--<tr>
                                	<td class="fieldName" align='right'>*Soak in-quantity:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakInQuantity" id="soakInQuantity" size="15" value="<?=$soakInQuantity?>"></td>
                            </tr>-->
							<tr>
                                	<td class="fieldName" align='right'>*Soak in-time:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakInTime" id="soakInTime" size="15" value="<?=$soakInTime?>"></td>
							</tr>
							<tr>
									   <td class="fieldName" align='right'>*Soak out-count:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakOutCount" id="soakOutCount" size="15" value="<?=$soakOutCount?>"></td>
                            </tr>
							</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>
				</tr>
				<tr>
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">			
							
							<!--<tr>
                                	<td class="fieldName" align='right'>*Soak out-count:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakOutCount" id="soakOutCount" size="15" value="<?=$soakOutCount?>"></td>
                            </tr>-->
							<tr>
                                	<td class="fieldName" align='right'>*Soak out-quantity:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakOutQunatity" id="soakOutQunatity" size="15" value="<?=$soakOutQunatity?>" onKeyUp="gaincal();"></td>
									  
									  
							</tr>
							<tr>				
							<td class="fieldName" align='right'>*Soak out-time:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakOutTime" id="soakOutTime" size="15" value="<?=$soakOutTime?>"></td>
                            </tr>
							<!--<tr>
                                	<td class="fieldName" align='right'>*Soak out-time:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="soakOutTime" id="soakOutTime" size="15" value="<?=$soakOutTime?>"></td>
                            </tr>-->
							<tr>
                                	<td class="fieldName" align='right'>*Temperature:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="temperature" id="temperature" size="15" value="<?=$temperature?>"></td>
							</tr>
					</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
							<tr>
									   <td class="fieldName" align='right'>*Gain%:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="gain" id="gain" size="15" value="<?=$gain?>"></td>
                            </tr>
							<!--<tr>
                                	<td class="fieldName" align='right'>*Gain%:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="gain" id="gain" size="15" value="<?=$gain?>"></td>
                            </tr>-->
							
							<tr>
                                	<td class="fieldName" align='right'>*Chemical used:&nbsp;</td>
                                       <td  height="10" ><select name="chemcalUsed" id="chemcalUsed" >
											  <option value="">--select--</option>
											  <?
												foreach($chemicalTypes as $rm)
													{
														 $chemicalId		=	$rm[0];
														 $chemical	=	stripSlash($rm[1]);
														$selected = ($chemcalUsed==$chemicalId)?"selected":""
														
											?>
											  <option value="<?=$chemicalId?>" <?=$selected?>><?=$chemical?></option>
											  <? }?>
										        </select>										      </td>
							</tr>
							<tr>
												<td class="fieldName" align='right'>*Chemical Qty:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="chemcalQty" id="chemcalQty" size="15" value="<?=$chemcalQty?>"></td>
                            </tr>
							<!--<tr>
                                	<td class="fieldName" align='right'>*Chemical Qty:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="chemcalQty" id="chemcalQty" size="15" value="<?=$chemcalQty?>"></td>
                            </tr>-->
						</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>						
							
                                              </table></td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSoaking(document.frmSoaking);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Soaking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSoaking(document.frmSoaking);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Soaking  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap"> 
                            		<? 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$soakingDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSoaking.php',700,600);"><? }?></td>
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
									<td colspan="2" >
										<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($soakingDataRecords) > 0 )
												{
													$i	=	0;
											?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" >
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM LOT ID</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Current Processing Stage</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supply Details</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Available Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Soak in-count</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">soak in-quantity</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">soak in-time</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">soak out-count</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">soak out-quantity</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">soak out-time</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Temperature</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Gain%</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Chemical used</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Chemical Qty</td>
		
		<td class="listing-head"></td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($soakingDataRecords as $sir) {
		$i++;
		$soakingDataId	=	$sir[0];
		//$lotRec		=	$rmTestDataObj->findLot($sir[1]);
		$lotRec		=	$unitTransferObj->findLot($sir[1]);
		$rmlotId		=	$lotRec[1];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[2]);
		$currentProcessingStage		=	$type[1];
		//$supplierRec		=	$rmReceiptGatePassObj->find($sir[3]);
		//$supplierDetails		=	$supplierRec[14];
		$supplierDetails		=	$sir[3];
		$availableQuantity=$sir[4];
		$soakInCount=$sir[5];
		$soakInQuantity=$sir[6];
		$soakInTime=$sir[7];
		$soakOutCount=$sir[8];
		$soakOutQunatity=$sir[9];
		$soakOutTime=$sir[10];
		$temperature=$sir[11];
		$gain=$sir[12];
		//$chemcalUsed=$sir[13];
		$chemical		=	$harvestingChemicalMasterObj->find($sir[13]);
		$chemcalUsed		=	$chemical[1];
		$chemcalQty=$sir[14];
		
		
		
		
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$soakingDataId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentProcessingStage;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierDetails;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$availableQuantity;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakInCount;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakInQuantity;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakInTime;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakOutCount;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakOutQunatity;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$soakOutTime;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$temperature;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$gain;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$chemcalUsed;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$chemcalQty;?></td>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<a href="javascript:printWindow('ViewSoaking.php?soakingDataId=<?=$soakingDataId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
		</td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$soakingDataId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='Soaking.php';"></td>
	<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Soaking.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$soakingDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSoaking.php',700,600);"><? }?></td>
												
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
	</table>

<SCRIPT LANGUAGE="JavaScript">
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
	<SCRIPT LANGUAGE="JavaScript">
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
	
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateOfTesting",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateOfTesting", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT>
	function gaincal()
	{
		var soakin=document.getElementById('soakInQuantity').value;
		var soakout=document.getElementById('soakOutQunatity').value;
		
		//alert(document.getElementById('soakInQuantity').value);
		 //var diff=parseInt(soakout) - parseInt(soakin);
		//var total=parseInt(soakout) + parseInt(soakin);
	//alert(diff+'--'+total);
	var inn=parseInt(soakin) ;
	var out=parseInt(soakout) ;
		//var gainvalue=(diff/total)*100;
		var gainvalue=(out/inn);
		document.getElementById("gain").value=gainvalue;
		
	}
	</SCRIPT>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>