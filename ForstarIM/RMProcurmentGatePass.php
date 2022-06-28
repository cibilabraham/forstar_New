<?php
	require("include/include.php");
	require_once('lib/RMProcurmentGatePass_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		    =	$sessObj->getValue("userId");
	$loginTime      =   $sessObj->getValue("loginTime");

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
	
	//$bseProcureID = explode(',',$g["procurementId"]);	$procurementIds = '';$procurmentOrderRec = array();
	
	$bseProcureID =$g["procurementId"];
	/*if(isset($g["procurementId"]) && sizeof($bseProcureID) > 0)
	{
		foreach($bseProcureID as $procurementNos)
		{
			if($procurementIds == '') $procurementIds = base64_decode($procurementNos);
			else $procurementIds.= ','.base64_decode($procurementNos);
		}
	}*/
	if(isset($g["procurementId"]))
	{
		if($procurementIds == '') $procurementIds = base64_decode($bseProcureID);
		$addMode==true;
	}
	else
	{
		$url_redirect = 'RMProcurmentOrder.php';
		$msg_notExistsProcurmentIds = "Procurements does not exists";
		// $sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
		// $sessObj->createSession("nextPage",$url_redirect.$selection);
		// header("Location: RMProcurmentOrder.php");
		// die();
	}
	$checkSealExist ='';

	if($p['cmdSaveChange'] || $p['cmdsave']=='1')
	{
		//die();
		//echo "hii";
		$checkSeals = array();
		$procurement_id      = $p['procurement_ids'];
		$procurement_gate_ids = $p['procurement_gate_ids'];
		$procurement_numbers  = $p['procurement_numbers'];
		$out_time             = $p['selectTimeHour'].'-'.$p['selectTimeMints'].'-'.$p['timeOption'];
		$outSealAlpha		  = $p['hidoutSealAlpha'];	
		$out_seal             = $p['out_seal'];
		$outseal_numgen_id    = $p['outseal_numgen_id'];
		$created_on           = date('Y-m-d');
		$active               = 1;
		$supervisor           = $p['supervisor'];
		$labour               = $p['labour'];
		$in_seals             = $p['in_seal'];
		//$in_seals           = $p['in_seal'];
		$countInseal          =$p['hidInSealSize'];
		$equipmentSize		  =$p['equipmentSize'];	
		$chemicalSize		  =$p['chemicalSize'];	
		$labourSize		  =$p['labourSize'];	
		//die();
		if($out_seal != '')
		{
			$checkSealExist = $rmProcurmentGatePassObj->checkSealUsedIns($out_seal,$outSealAlpha,$outseal_numgen_id);
		}
		if($countInseal > 0)
		{
			for($i=0; $i<$countInseal; $i++)
			{
				//echo "jii".$i;
				$in_seal=$p["in_seal_".$i];
				$insealAlpha=$p["hidinsealAlpha_".$i];
				$in_seal_num_genid=$p["in_seal_num_genid_".$i];
				//echo $in_seal.','.$insealAlpha.','.$in_seal_num_genid.'<br/>';
				$checkSealExist = $rmProcurmentGatePassObj->checkSealUsedIns($in_seal,$insealAlpha,$in_seal_num_genid);

			}
		}
		//die();
		if($checkSealExist != '')
		{
			$sessObj->createSession("displayMsg",$checkSealExist);
		}
		else
		{
			$outsealInsert=$rmProcurmentGatePassObj->addProcureMentOutSeal($procurement_id,$procurement_numbers,$out_time,$out_seal,$userId,$active,$outseal_numgen_id,$outSealAlpha,$supervisor);
			if($outsealInsert)					
				$lastId = $databaseConnect->getLastInsertedId();
			#---------------------------------------------------------------------------------------------------------
					# insert outseal in seal history table
					$receipt_id=$rmProcurmentGatePassObj->getReceiptGatePassId($procurement_id);
					//$alpha	=$manageSealObj->getAlphaPrefix($number_gen_id);
					//$alphacode=$alpha[0];
					$alphacode=$outSealAlpha;
					$seal_status="Out seal";
					$status="Used";
					
					//$receipt_id='';
					$outsealHistory = $manageSealObj->insertReleaseSeal($alphacode,$out_seal,$lastId,$receipt_id,$seal_status,$userId,$status);
					
					for($j=0; $j<$countInseal; $j++)
					{
						//echo "jii".$i;
						$status=$p["status_".$j];
						if($status!='N')
						{
							$in_seals=$p["in_seal_".$j];
							$insealAlpha=$p["hidinsealAlpha_".$j];
							$in_seal_num_genid=$p["in_seal_num_genid_".$j];
							//echo $in_seal.','.$insealAlpha.','.$in_seal_num_genid.'<br/>';

							$insealInsert=$rmProcurmentGatePassObj->addProcureMentInSeal($procurement_id,$in_seals,$userId,$active,$in_seal_num_genid,$insealAlpha,$lastId);
							if($insealInsert)					
							$inseallastId = $databaseConnect->getLastInsertedId();
							#---------------------------------------------------------------------------------------------------------
							# insert inseal in seal history table
							$seal_statusVal="In seal";
							$statusVal="Blocked";
							$insealHistory = $manageSealObj->addInSeal($insealAlpha,$in_seals,$inseallastId,$receipt_id,$seal_statusVal,$userId,$statusVal);
							$rmProcurmentGatePassObj->deleteSealAssigned($userId,$loginTime);
						}
					}
					
					for($m=0;$m<$labourSize;$m++)
					{
						$sstatus=$p["sstatus_".$m];
						if($sstatus!='N')
						{
						$labour  = $p["labour_".$m];
						
						$rmProcurmentGatePassObj->addLabourDetails($lastId,$labour);
						}
					}
					
					

					for($l=0;$l<$equipmentSize;$l++)
					{
						$id                  = $p["procurement_equipment_id_".$l];
						$issued_quantity     = $p["equipment_issued_quantity_".$l];
						$difference_quantity = $p["equipmifference_".$l];
						$rmProcurmentGatePassObj->updateProcurementEquipment($id,$issued_quantity,$difference_quantity);
					}
					for($l=0;$l<$chemicalSize;$l++)
					{
						$id                  = $p["procurement_chemical_id_".$l];
						$issued_quantity     = $p["chemical_issued_quantity_".$l];
						$difference_quantity = $p["chemical_difference_".$l];
						$rmProcurmentGatePassObj->updateProcurementChemical($id,$issued_quantity,$difference_quantity,$userId,$loginTime);
					}
					//die();
					
				$updateProcurementGenerate=$rmProcurmentOrderObj->updateProcurementOrderGenerate($procurement_id);	
				//$msg_succAddRMProcurmentGatePass="Procurement gate pass added succesfully";
				//$sessObj->createSession("displayMsg",$msg_succAddRMProcurmentGatePass);
				// $sessObj->createSession("nextPage",$url_redirect.$selection);
				//header("Location: RMProcurmentGatePass.php");
			
			//die();
				if ($updateProcurementGenerate) {
				
				$procurementIds	=	'';
				$sessObj->createSession("displayMsg",$msg_succAddRMProcurmentGatePass);
				$sessObj->createSession("nextPage",$url_afterAddRMProcurmentGatePass.$selection);
				}
			 else {
				$addMode	=	true;
				$err		=	$msg_failAddRMProcurmentGatePass;
			}
			$updateProcurementGenerate		=	false;
			$hidEditId 	=  "";
		}
		
	}

	if($procurementIds == '')
	{
		// $msg_notExistsProcurmentIds = "Procurements does not exists";
		// $sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
		// $sessObj->createSession("nextPage",$url_afterAddRMProcurmentGatePass.$selection);
		$url_redirect = 'RMProcurmentOrder.php';
		$msg_notExistsProcurmentIds = "Procurements does not exists";
		// $sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
		// $sessObj->createSession("nextPage",$url_redirect.$selection);
		// header("Location: RMProcurmentOrder.php");
		// die();
		
	}
	else
	{
		$procurmentOrderRec	=	$rmProcurmentGatePassObj->findProGatePass($procurementIds);
	}
	
	$harvestingEquipmentRecs = $harvestingEquipmentMasterObj->fetchAllRecordsActiveequipmentType();
	$harvestingChemicalRecs = $harvestingChemicalMasterObj->fetchAllChemicalRecordsActive();
	
	$sealNumbers = $rmProcurmentGatePassObj->getSealNo();
	$alpha_code = '';$startNo = '';$startSerialNo = '';$inSealFirst = '';$inSealFirstNo = '';$number_gen_id = '';
	if(sizeof($sealNumbers) == 0 && $procurementIds!='')
	{
		// $msg_notExistsProcurmentIds = "Seal numbers not available. Please set seal in manage challan";
		// $sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
		// $sessObj->createSession("nextPage",$url_afterAddRMProcurmentGatePass.$selection);
		
		$url_redirect = 'ManageChallan.php';
		$msg_notExistsProcurmentIds = "Seal numbers not available. Please set seal in manage challan";
		$sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
		// $sessObj->createSession("nextPage",$url_redirect);
		header("Location: ManageChallan.php");
		die();
	}
	elseif($procurementIds!="")
	{
		
		$outseal_numgen_id  = $sealNumbers[0]['id'];
		$in_seal_num_genid  = $sealNumbers[0]['id'];
		$alpha_code     = $sealNumbers[0]['alpha_code'];
		$hidoutSealAlpha     = $sealNumbers[0]['alpha_code'];
		$hidinsealAlpha     = $sealNumbers[0]['alpha_code'];
		$start_no       = $sealNumbers[0]['start_no'];
		$end_no         = $sealNumbers[0]['end_no'];
		$current_no     = $sealNumbers[0]['current_no'];
		$hidInSealSize=1;
		$avaArray = $rmProcurmentGatePassObj->getAvailableSealNos($start_no);
		// print_r($avaArray);
		if(sizeof($avaArray) < 0 && $addMode==true)
		{
			$msg_notExistsProcurmentIds = "Seal number or date may expired. Please set seal in manage challan";
			$sessObj->createSession("displayMsg",$msg_notExistsProcurmentIds);
			// $sessObj->createSession("nextPage",$url_redirect);
			header("Location: ManageChallan.php");
			die();
		}
		else
		{
			$startSerialNo = $avaArray[0];
			$rmProcurmentGatePassObj->insertSeal($outseal_numgen_id,$startSerialNo,$userId,$loginTime);
			if(isset($avaArray[1]))
			{			
				$inSealFirst = $avaArray[1];
				$rmProcurmentGatePassObj->insertSeal($in_seal_num_genid,$inSealFirst,$userId,$loginTime);
			}
			
		}
	}
	$gateSupervisor = $objWeighmentDataSheet->getAllEmployee();
	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$procurementGatePassId	=	$p["delId_".$i];
			
			if ($procurementGatePassId!="") {
				$selProcurement	 = $rmProcurmentGatePassObj->getProcurmentId($procurementGatePassId);
				$deleteProcurmentGatePassRecs	=	$rmProcurmentGatePassObj->deleteProcurmentGatePass($procurementGatePassId);
				$delLabour	 = $rmProcurmentGatePassObj->deleteLabour($procurementGatePassId);
					
					$delSeal	 = $rmProcurmentGatePassObj->deleteSeal($procurementGatePassId);
					$rmProcurmentGatePassObj->updateSealHistory($procurementGatePassId);
					//$delSealhistory	 = $rmProcurmentGatePassObj->deleteSealHistory($procurementGatePassId);
					$procurementId=$selProcurement[0][1];
					$updateProcurementGenerateDelete=$rmProcurmentOrderObj->updateProcurementOrderGenerateDelete($procurementId);
				
				//die();	
			}
		}
		if ($deleteProcurmentGatePassRecs && $delLabour && $delSeal) {
			$sessObj->createSession("displayMsg",$msg_succDelRMProcurmentGatePass);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurmentGatePass.$selection);
		} else {
			$errDel	=	$msg_failDelRMProcurmentGatePass;
		}
		$deleteProcurmentGatePassRecs	=	false;
		//$hidEditId 	= "";
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
	
	
	#List all Stock Issuance
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$rmProcurementGatePassRecords	= $rmProcurmentGatePassObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$rmProcurementSize	= sizeof($rmProcurementGatePassRecords);
	
	}
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/RMProcurmentGatePass.js"; // For Printing JS in Head section
	
	

	require("template/topLeftNav.php");
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

<!--<link rel="stylesheet" href="libjs/jquery-ui.css">-->
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>

<form method="post" action="RMProcurmentGatePass.php?procurementId=<?php echo $_REQUEST['procurementId'];?>" name="RMProcurmentGatePass" id="RMProcurmentGatePass">
	<input type="hidden" name="sealsAvailable" id="sealsAvailable" />
	<input type="hidden" name="number_gen_id" id="number_gen_id" value="<?php echo $number_gen_id;?>" />
	<table width="70%" align="center" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td height="20" align="center" class="err1" id="err1"> </td>
			</tr>
			<?php
				if(sizeof($procurmentOrderRec) > 0)
				{
			?>
			<tr>
				<td>
					<table width="70%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
						<tbody>
							<tr>
								<td bgcolor="white">
									<!-- Form fields start -->
									<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
												<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; Update RMProcurment Gate Pass</td>
											</tr>
											<tr>
												<td width="1"></td>
												<td colspan="2">
													<table width="75%" border="0" align="center" cellspacing="0" cellpadding="0">
														<tbody>
															<tr>
																<td height="10" colspan="2"></td>
															</tr>
															<tr>
																<td align="center" colspan="2">
																	<input type="button" onclick="cancel('RMProcurmentGatePass.php');" value=" Cancel " class="button" name="cmdCancel">&nbsp;&nbsp;
																	<input type="submit" onclick="return  validateRMProcurmentGatePass(document.RMProcurmentGatePass);" value=" Save Changes " class="button" id="cmdSaveChange2" name="cmdSaveChange">
																</td>
															</tr>										
															<tr>
															  <td nowrap="" class="fieldName">&nbsp;</td>
															  <td>&nbsp;</td>
															</tr>
															<tr><td nowrap=""></td></tr>
															
															<tr>
																<td align="center">
																	<table>
																		<tbody>
																			<tr>
																				<td valign="top">
																					<table valign="top">
																						<tbody>
																							<tr>
																								<td>
																									<table width="10%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblAddProcurmentOrder" id="tblHarvestingEquipment">
																										<tbody>		
																										<tr align="center" class="whiteRow" id="Row_0">
																											<td nowrap align="center" id="srNo_0" class="listing-item">
																													Out Seal 
																											</td>
																											<td nowrap align="center" class="listing-item" id='outSealAlpha' >
																											 <?php echo $alpha_code;?>
																											 
																											</td>	<td>	 
																											 <input onblur="getAvailableSeals(this.value);" type="text" value="<?php echo $startSerialNo;?>" autocomplete="off" size="15" id="out_seal" name="out_seal">
<input type="hidden"	 name="outseal_numgen_id" value="<?=$outseal_numgen_id?>"	id="outseal_numgen_id"/>																			<input type="hidden" value="<?=$hidoutSealAlpha?>" name="hidoutSealAlpha" id="hidoutSealAlpha"/>				  
																												</td>
																											</tr>
																											<tr align="center" class="whiteRow" id="Row_1">
																												<td nowrap align="center" id="srNo_1" class="listing-item">
																													Out Seal Time
																												</td>
																												
																												<td colspan="2"nowrap align="center" class="listing-item">
																													<input type="text" autocomplete="off" style="text-align:center;" onkeyup="return timeCheck();" tabindex="9" value="11" size="1" name="selectTimeHour" id="selectTimeHour"> :
																													<input type="text" autocomplete="off" style="text-align:center;" onkeyup="return timeCheck();" tabindex="10" value="38" size="1" name="selectTimeMints" id="selectTimeMints">
																													<select tabindex="11" id="timeOption" name="timeOption">
																														<option selected="" value="AM">AM</option>
																														<option value="PM">PM</option>
																													</select>
																												</td>
																											</tr>
																										</tbody>
																									</table>
																								</td>
																								<tr>		
																				<td>
																					<table>
																						<tbody>
																							<tr>
																								<td valign="top">
																									<table width="10%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblNewLabour" id="tblNewLabour">
																										<tbody>
																											<tr align="center" class="whiteRow">
																												<th colspan="2" nowrap align="center" class="listing-item">
																													Labour
																												</th>
																											</tr>
																											<tr class="whiteRow" id="SlNLABRow_0">
																												<td id="srNoLa_0" nowrap align="center" class="listing-item">
																													<input type="text" size="15" id="labour_0" name="labour_0">
																												</td>
																												<td nowrap align="center" class="listing-item">
								<a onclick="setTestRowItemStatusLabour('0')" href="javascript:void(0);"><img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item"></a><input name="sstatus_0" type="hidden" id="sstatus_0" value=""><input name="IsFromDB_0" type="hidden" id="IsFromDB_0" value="ds">
																									<!--	<a onclick="setTestRowItemStatusLabour('0');" href="javascript:void(0);">
																														<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																													</a>-->
																												</td>
																											</tr>
																											
																											<tr class="whiteRow">
			<input type="hidden"	name="labourSize" id="labourSize" value="1"/>																								<td class="listing-item" nowrap="" colspan="3" style="padding-left:5px; padding-right:5px;">
																													<a title="Click here to add new item." class="link1" onclick="javascript:addNewLabourRow();" id="addRow" href="javascript:void(0);">
																														<img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item</a>
																												</td>		
																											</tr>
																										</tbody>
																									</table>
																								</td>

																								<td valign="top">
																					<table>
																						<tbody>
																							<tr>																						
																								<td>
																									<table width="10%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblNewLabour" id="tblNewLabour">
																										<tbody>
																											<tr nowrap align="center" class="whiteRow">
																												<td nowrap class="listing-item">
																													Receiving Supervisor
																												</td>
																											</tr>
																											<tr class="whiteRow">
																												<td>
																													<select name="supervisor" id="supervisor">
																														<option value=""> Select </option>
																														<?php 
																															foreach($gateSupervisor as $sp)
																															{
																																$supervisorId		=	$sp[0];
																																$supervisorName	=	stripSlash($sp[1]);
																																$selected="";
																																if($supervisor==$supervisorId) echo $selected="Selected";
																														?>
																															<option value="<?=$supervisorId?>" <?=$selected?>><?=$supervisorName?></option>
																														<? } ?>
																													</select>
																												</td>
																											</tr>
																										</tbody>
																									</table>
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</td>
																							</tr>
																							<!--<tr>
																								<td nowrap="" colspan="3" style="padding-left:5px; padding-right:5px;">
																									<a title="Click here to add new item." class="link1" onclick="javascript:addNewHarvestingChemical();" id="addRow" href="###"><img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item</a>
																								</td>		
																							</tr>-->
																						</tbody>
																					</table>
																				</td>
																							</tr>
																						</tbody>
																					</table>
																				</td>
																				<td width="8%"></td>
																				<td valign="top">
																					<table>
																						<tbody>
																							<tr>
																								<td>
																									<table width="10%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblNewInSeal" id="tblNewInSeal">
																										<tbody>
																											<tr align="center" class="whiteRow" id="SlNRow_0">
																												<td nowrap align="center" id="srNo_0" class="listing-item">
																													In Seal 
																												</td>
																												<td nowrap align="center" class="listing-item" id="insealAlpha_0">
																												 <?php echo $alpha_code;?>
																												 </td>
																												 <td><input class="in_seal_class" type="text" size="15" id="in_seal_0" value="<?php echo $inSealFirst;?>" name="in_seal_0" >
<input  type="hidden" size="15" id="in_seal_num_genid_0" value="<?=$in_seal_num_genid?>" name="in_seal_num_genid_0" >
<input  type="hidden" size="15" id="hidinsealAlpha_0" value="<?=$hidinsealAlpha?>" name="hidinsealAlpha_0" >
																												</td>
							<td nowrap align="center" class="listing-item">
							<a onclick="setTestRowItemStatus('0)" href="javascript:void(0);"><img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item"></a><input name="status_0" type="hidden" id="status_0" value=""><input name="IsFromDB_0" type="hidden" id="IsFromDB_0" value="ds">
																													
								
																													</a>
																												</td>
																											</tr>
																											<input type="hidden" value="<?=$hidInSealSize?>" name="hidInSealSize" id="hidInSealSize"/>
																											<tr class="whiteRow">
																												<td class="listing-item" nowrap="" colspan="4" style="padding-left:5px; padding-right:5px;">
																													<a title="Click here to add new item." class="link1" onclick="javascript:addNewInSealRow();" id="addRow" href="javascript:void(0);">
																														<img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item</a>
																												</td>		
																											</tr>
																										</tbody>
																									</table>
																								</td>
																							</tr>
																							<!--<tr>
																								<td nowrap="" colspan="3" style="padding-left:5px; padding-right:5px;">
																									<a title="Click here to add new item." class="link1" onclick="javascript:addNewHarvestingChemical();" id="addRow" href="###"><img border="0" style="border:none;padding-right:4px;vertical-align:middle;" src="images/addIcon.gif">Add New Item</a>
																								</td>		
																							</tr>-->
																						</tbody>
																					</table>
																				</td>
																				
																				
																				
																			</tr>
																			
																			
																				<td width="8%"></td>
																				
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
			
															
															<tr>
																<td height="10" colspan="2"></td>
															</tr>
															<?php
																if(sizeof($procurmentOrderRec) > 0)
																{
																	foreach($procurmentOrderRec as $procurementOrder)
																	{
																		$equipments = array();$chemicals = array();
																		if($procurementOrder['equipments'] != '')
																		{
																			$equipments = explode(',',$procurementOrder['equipments']);
																		}
																		if($procurementOrder['chemicals'] != '')
																		{
																			$chemicals = explode(',',$procurementOrder['chemicals']);
																		}
															?>
															<tr>
																<td>
																	<table style="margin-top:20px;border:1px solid #69c;">
																		<tr class="">
																			<td class="listing-head">
																			&nbsp;&nbsp;<?php echo $procurementOrder['procurement_number'];?>
																			<input type="hidden" name="procurement_ids" value="<?php echo $procurementOrder['id'];?>" id='procurement_ids' />
																			<input type="hidden" name="procurement_gate_ids" value="<?php echo $procurementOrder['gate_pass_id'];?>" />
																			<input type="hidden" name="procurement_numbers" value="<?php echo $procurementOrder['procurement_number'];?>" />
																			</td>
																		</tr>
																		<tr class="">
																			<td align="left">
																				<table>
																					<tbody>
																						<tr>
																							<td>
																								<table>
																									<tbody>
																										<tr>
																											<td>
																												<table width="100%" bgcolor="#999999" cellspacing="1" cellpadding="3" id="tblHarvestingEquipment" name="tblAddProcurmentOrder">
																													<tbody>
																														<tr bgcolor="#f2f2f2" align="center">
																															<td nowrap="" class="listing-head">Equipment name </td>
																															<td nowrap="" class="listing-head">Equipment required quantity</td>
																															<td nowrap class="listing-head">Issued quantity</td>
																															<td nowrap class="listing-head">Difference</td> 
																														</tr>	
																														<?php
																														if(sizeof($equipments) > 0)
																														{
		$eqpsz=sizeof($equipments);																													
	echo '<input type="hidden" name="equipmentSize" id="equipmentSize" value="'.$eqpsz.'" >';	
							}
							else
							{
								echo '<input type="hidden" name="equipmentSize" id="equipmentSize" value="0" >';
							}
									if(sizeof($equipments) > 0)
																														{
	$i=0;
																																foreach($equipments as $eqp)
																																{
																																	$eqpdetails = explode('$$',$eqp);
																														?>
																																	<tr align="center" class="" bgcolor="#FFFFFF" id="Row_0">
																																		<td align="center" id="srNo_0" class="listing-item">
																																			<input type="hidden" name="procurement_equipment_id_<?php echo $i;?>" value="<?php echo $eqpdetails[0];?>" />
																																			<select id="harvestingEquipment_<?php echo $i;?>" name="harvestingEquipment_<?php echo $i;?>">
																																				<option value="">--Select--</option>
																																				<?php
																																					if(sizeof($harvestingEquipmentRecs) > 0)
																																					{
																																						foreach($harvestingEquipmentRecs as $option)
																																						{
																																							$sel = '';
																																							if($eqpdetails[1] == $option[0]) $sel = 'selected';
																																							
																																							echo '<option '.$sel.' value="'.$option[0].'">'.$option[1].'</option>';
																																						}
																																					}
																																				?>						
																																			</select>
																																		</td>
																																		<td align="center" class="listing-item">
																																			<input type="text" readonly value="<?php echo $eqpdetails[2];?>" size="15" id="equipment_required_quantity_<?php echo $i;?>" name="equipment_required_quantity_<?php echo $i;?>"  required>
																																		</td>
																																		<td align="center" class="fieldName">
																																			<input type="text" onkeyup="calculateEquipDiff(this.value,'equipment_required_quantity_<?php echo $i;?>','equipmifference_<?php echo $i;?>')" size="15" id="equipment_issued_quantity_<?php echo $i;?>" name="equipment_issued_quantity_<?php echo $i;?>"  autocomplete="off">
																																		</td>
																																		<td align="center" class="listing-item">
																																			<input type="text"  readonly size="15" id="equipmifference_<?php echo $i;?>" name="equipmifference_<?php echo $i;?>" >
																																		</td>
																																	</tr>
																														<?php
																																	$i++;
																																}
																															}
																															else
																															{
																																echo '<tr><td colspan="4" align="center" class="whiteRow"> No equipments found</td></tr>';
																															}
																														?>
																													</tbody>
																												</table>
																											</td>
																										</tr>																					
																									</tbody>
																								</table>																	
																							</td>
																						</tr>
																						
																					</tbody>
																				</table>																
																			</td>
																		</tr>
																		<tr class="">
																			<td align="center">
																				<table>
																					<tbody>
																						<tr>
																							<td>
																								<table>
																									<tbody>
																										<tr>
																											<td>
																												<table width="100%" bgcolor="#999999" cellspacing="1" cellpadding="3" id="tblHarvestingEquipment" name="tblAddProcurmentOrder">
																													<tbody>
																														<tr bgcolor="#f2f2f2" align="center">
																															<td nowrap="" class="listing-head">Chemical name</td>
																															<td nowrap="" class="listing-head">Chemical required quantity(Kgs)</td>
																															<td nowrap="" class="listing-head">Issued quantity</td>
																															<td nowrap="" class="listing-head">Difference</td> 
																														</tr>
																														<?php
																															if(sizeof($chemicals) > 0)
																															{

	$chmsz=sizeof($chemicals);																													
	echo '<input type="hidden" name="chemicalSize" id="chemicalSize" value="'.$chmsz.'" >';	
							}
							else
							{
								echo '<input type="hidden" name="chemicalSize" id="chemicalSize" value="0" >';	

						}
									if(sizeof($chemicals) > 0)
																															{
										$j=0;
																																foreach($chemicals as $chem)
																																{
																																	$chemicalDetails = explode('$$',$chem);
																														?>
																														<tr align="center" id="Row_0" class="" bgcolor="#FFFFFF">
																															<td align="center" class="listing-item" id="srNo_0">
																																<input type="hidden" name="procurement_chemical_id_<?php echo $j;?>" value="<?php echo $chemicalDetails[0];?>" />
																																<select name="chemicalName_<?php echo $j;?>" id="chemicalName_<?php echo $j;?>">
																																	<option value="">--Select--</option>
																																	<?php
																																		if(sizeof($harvestingChemicalRecs) > 0)
																																		{
																																			foreach($harvestingChemicalRecs as $option)
																																			{
																																				$sel = '';
																																				if($chemicalDetails[1] == $option[0]) $sel = 'selected';
																																				
																																				echo '<option '.$sel.' value="'.$option[0].'">'.$option[1].'</option>';
																																			}
																																		}
																																	?>
																																</select>
																															</td>
																															<td align="center" class="listing-item">
																																<input type="text" readonly name="chemical_required_quantity_<?php echo $j;?>" id="chemical_required_quantity_<?php echo $j;?>" size="15" value="<?php echo $chemicalDetails[2];?>">
																															</td>
																															<td align="center" class="listing-item">
																																<input type="text" onkeyup="calculateChemicalDiff(this.value,'chemical_required_quantity_<?php echo $j;?>','chemical_difference_<?php echo $j;?>')" name="chemical_issued_quantity_<?php echo $j;?>" id="chemical_issued_quantity_<?php echo $j;?>" size="15">
																															</td>
																															<td align="center" class="listing-item">
																																<input type="text" readonly name="chemical_difference_<?php echo $j;?>" id="chemical_difference_<?php echo $j;?>" size="15">
																															</td>
																														</tr>
																														<?php
																																	$j++;
																																}
																															}
																															else
																															{
																																echo '<tr><td colspan="4" align="center" class="whiteRow"> No chemical found</td></tr>';
																															}
																														?>
																													</tbody>
																												</table>
																											</td>
																										</tr>																												
																									</tbody>
																								</table>																	
																							</td>
																						</tr>
																						
																					</tbody>
																				</table>																
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<?php
																	}
																}
															?>
															
														</tbody>
													</table>
												</td>
											</tr>
											<tr>
											  <td colspan="2">&nbsp;</td>
											</tr>
											
											<tr>
												<td align="center" colspan="2">
													<input type="button" onclick="cancel('RMProcurmentGatePass.php');" value=" Cancel " class="button" name="cmdCancel">&nbsp;&nbsp;
													<input type="submit" onclick="return  validateRMProcurmentGatePass(document.RMProcurmentGatePass);" value=" Save Changes " class="button" id="cmdSaveChange2" name="cmdSaveChange">
													<!--<input type="submit" onclick="return checkSealNos() && return validateRMProcurmentGatePass();" value=" Save Changes " class="button" id="cmdSaveChange2" name="cmdSaveChange">-->
												</td>
											</tr>
											<tr>
												<td height="20" colspan="2"></td>
											</tr>
										</tbody>
									</table>	
									<!-- Form Fields End -->
								</td>
							</tr>
							
						</tbody>
					</table>						
				</td>
			</tr>
			<?php
				}
			?>
			
			<tr>
												<td height="10" colspan="2"></td>
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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;RM Procurment GatePass  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintRMProcurementgatepass.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?>
												<!--<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>-->&nbsp;</td>
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
												if( sizeof($rmProcurementGatePassRecords) > 0 )
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
      				$nav.= " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">ID</td>
		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Out Date & Time</td>
		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Out Seal Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supervisor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Other Seals</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Labours</td>
		
	
		
	</tr>
	<?
	foreach ($rmProcurementGatePassRecords as $sir) {
	
		$i++; $existReceipt="";
		 $procurmentGatePassId	=	$sir[0];
		
		 $procurmentGatePass		=	$sir[1];
		 $outTime       =	$sir[2];
		 $sealNoOutId		=	$sir[3];
		 $sealNoOut=$rmProcurmentGatePassObj->getSealNumber($sealNoOutId);
		  $sealNo=$sealNoOut[1];
		 $supervisorId       =	$sir[4];
		 $current_date       =	dateFormat($sir[5]);
		 $numbergen=$sir[6];
		 $outalpha=$sir[7]; 
		 
		 $supervisor=$rmProcurmentGatePassObj->getSupervisor($supervisorId);
		 $supervisorName=$supervisor[1];
		 $sealNumbers= $rmProcurmentGatePassObj->getSealNumbers($procurmentGatePassId);
		// echo '<pre>'; print_r($sealNumbers);echo '</pre>';
		 $labours= $rmProcurmentGatePassObj->getLabours($procurmentGatePassId);
		 //$vehicleType		=	$driverMasterObj->getVehicleType($driverMasterId);
		// $active=$cr[3];
		//$existingrecords=$cr[4];
		$procurmentorderId=$sir[8]; 
		$existReceipt= $rmProcurmentGatePassObj->checkExistInReceipt($procurmentorderId);
		($existReceipt!='') ? $disabled="disabled" :$disabled="";
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$procurmentGatePassId;?>" class="chkBox" <?=$disabled?>></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$procurmentGatePass;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$current_date;?> & <?=$outTime;?></td>
		
		<?php /*<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$outTime;?></td>*/?>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$outalpha.$sealNoOutId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supervisorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		<?php
			if (sizeof($sealNumbers)>0) {
				$nextRec = 0;						
				foreach ($sealNumbers as $cR) {	
					echo $cR[2].$cR[1].'<br/>';
				}
			}
		?>
		</td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
		 <?php
			$numLine = 3;
			if (sizeof($sealNumbers)>0) {
				$nextRec = 0;						
				foreach ($sealNumbers as $cR) {					
					$seal = $cR[1];
					$sealNum=$rmProcurmentGatePassObj->getSealNumber($seal);
					//$supName=$supplierGroupObj->getSupplierName($supplier);
						$sealNumber=$sealNum[1];
						//$address=$supName[1];						
					$nextRec++;
					//$detailsvalue="Address:$address<br>"; 
					if($nextRec>1) echo "<br>";  echo $sealNumber;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		</td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 3;
			if (sizeof($labours)>0) {
				$nextRec = 0;						
				foreach ($labours as $cR) {					
					$labourName = $cR[1];	
					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $labourName;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		</td>
		
		
		</td>
		
	
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMProcurmentGatePass.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmProcurementSize;?>);"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintRMProcurementgatepass.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?>
												<!--<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>-->&nbsp;</td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->	
				<input type="hidden" name="cmdsave" id="cmdsave" value="">		</td>
		</tr>	
		</tbody>
	</table>		
</form>
<?php	
	require("template/bottomRightNav.php");
?>
<!--<button id="popup_window" data-popup-target="#example-popup">Open The Light Weight Popup Modal</button>-->

<!--<div id="example-popup" class="popup">
    <div class="popup-body"><span class="popup-exit"></span>
        <div class="popup-content" id="popupcontent"></div>
    </div>
</div>
<div class="popup-overlay"></div>-->

	<!---design for pop up---->
	<div id="dialog" title="Available seal numbers "  >
	<!--<p>
	This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
		<div id="tabs">
			<ul>
			  <li><a href="#tabs-1">tab-1</a></li>
			  <li><a href="#tabs-2">tab-2</a></li>
			</ul>
			<div id="tabs-1" style=" height:auto;">Big content1...</div>
			<div id="tabs-2" style=" height:auto;">Big content2...</div>
		  </div>


	</p>-->
	</div>


<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "outTime",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "outTime", 
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

	// Calendar.setup 
	// (	
		// {
			// inputField  : "out_time",         // ID of the input field
			// eventName	  : "click",	    // name of event
			// button : "out_time", 
			// ifFormat    : "%d/%m/%Y",    // the date format
			// singleClick : true,
			// step : 1
		// }
	// );
	//-->
	
	function page(fileName)
	{
		
			window.location = fileName;
		
	}
	
	</SCRIPT>
<script type='text/javascript'>//<![CDATA[ 

	<?php 
	if($procurementIds!="")
	{
	?>	//activate popup for out seal
		$(function() {
		xajax_getAllAvailableAlphaPrefix(0,0);
		//$( "#dialog" ).dialog({ width: 500, modal: true  });
		$("#dialog").dialog({width: 500, height:570, resizable: true, modal: true });
		});
		/*alert("hii");
		xajax_getAllAvailableAlphaPrefix();
		$('html').addClass('overlay');
     	$('#example-popup').addClass('visible');*/
	<?php
	}
	?>
//activate tab inside pop up
function tabactive()
{
	$( "#tabs" ).tabs();
}
//assign seal number for fields
function assignSeals(id,start_no,end_no,seal,inputStatus,row,alphacode)
{	//for out seal inputStatus=0 and for in seal inputStatus=1
	if(inputStatus=='0')
	{
		xajax_assignSealsInsert(id,seal,'<?=$userId?>','<?=$loginTime?>');
		//alert(alphacode);
		$('#outSealAlpha').html(alphacode);
		$('#hidoutSealAlpha').val(alphacode);
		$('#out_seal').val(seal);
		$('#outseal_numgen_id').val(id);
		$( "#dialog" ).dialog( "close" ); 
		setTimeout(function() {
		//activate popup for in seal
			xajax_getAllAvailableAlphaPrefix(1,0);
			$( "#dialog" ).dialog({ width: 500, height:570, resizable: true, modal: true  });
		 }, 1000);
	}
	else if(inputStatus=='1')
	{
		xajax_assignSealsInsert(id,seal,'<?=$userId?>','<?=$loginTime?>');
		var totalRow=parseInt(row)+1;
		//alert(alphacode+'--'+row);	
		$('#insealAlpha_'+row).html(alphacode);
		$('#hidinsealAlpha_'+row).val(alphacode);
		$('#in_seal_'+row).val(seal);
		$('#in_seal_num_genid_'+row).val(id);
		$('#hidInSealSize').val(totalRow);
		$( "#dialog" ).dialog( "close" );
	}
}

// displaying pop up for add new in seal
function fillInsealRow(fld)
{
	xajax_getAllAvailableAlphaPrefix(1,fld);
	$( "#dialog" ).dialog({ width: 500, height:570, resizable: true, modal: true   });
}

function getAvailableSeals()
{
	xajax_getAllAvailableAlphaPrefix(0,0);
		//$( "#dialog" ).dialog({ width: 500, modal: true  });
		$( "#dialog" ).dialog({width: 500, height:570, resizable: true, modal: true});
}
/*$(window).load(function(){
jQuery(document).ready(function ($) {

	$('#out_seal').keyup(function(){
		checkSealNos();
	});
	$('.in_seal_class').keyup(function(){
		checkSealNos();
	});
    $('[data-popup-target]').click(function () {
        $('html').addClass('overlay');
        var activePopup = $(this).attr('data-popup-target');
        $(activePopup).addClass('visible');

    });

    $(document).keyup(function (e) {
        if (e.keyCode == 27 && $('html').hasClass('overlay')) {
            clearPopup();
        }
    });

    $('.popup-exit').click(function () {
        clearPopup();
    });

    $('.popup-overlay').click(function () {
        clearPopup();
    });

    function clearPopup() {
        $('.popup.visible').addClass('transitioning').removeClass('visible');
        $('html').removeClass('overlay');

        setTimeout(function () {
            $('.popup').removeClass('transitioning');
        }, 200);
    }
});
});//]]> */

/*function assignSeals_old(id,start_no,end_no,current_no)
{
	
	//$('#outSealAlpha').val(start_no);
	//document.getElementById('outSealAlpha').innerHTML =start_no;
	$('.popup.visible').addClass('transitioning').removeClass('visible');
    $('html').removeClass('overlay');
}

function getAvailableSeals(sealNo)
{
	// alert(sealNo);
	xajax_getAvailableSealNos(sealNo);
	 $('html').addClass('overlay');
     // var activePopup = $(this).attr('data-popup-target');
     $('#example-popup').addClass('visible');
}
function assignToOutSeal(outSealNo)
{
	$('#out_seal').val(outSealNo);
	 $('.popup.visible').addClass('transitioning').removeClass('visible');
        $('html').removeClass('overlay');

        setTimeout(function () {
            $('.popup').removeClass('transitioning');
        }, 200);	
	xajax_addSealAssigned('<?=$number_gen_id?>',outSealNo,'<?=$userId?>','<?=$loginTime?>');
}
function assignToInSeal(inSealNo,inSealId)
{
	$('#in_seal_'+inSealId).val(inSealNo);
	 $('.popup.visible').addClass('transitioning').removeClass('visible');
        $('html').removeClass('overlay');

        setTimeout(function () {
            $('.popup').removeClass('transitioning');
        }, 200);	
	xajax_addSealAssigned('<?=$number_gen_id?>',inSealNo,'<?=$userId?>','<?=$loginTime?>');
}
function fillInSeal(inSealId)
{	
	sealNos  = jQuery('#out_seal').val();
	sealFrom = jQuery('#out_seal').val();
	var in_seal = document.getElementsByName('in_seal[]');
	for(i=0;i<in_seal.length-1;i++)
	{
		sealNos+= ','+in_seal[i].value;
	}
	xajax_getAvailableSealNosForInseals(sealFrom,sealNos,inSealId);
	$('html').addClass('overlay');
     // var activePopup = $(this).attr('data-popup-target');
     $('#example-popup').addClass('visible');
}
function checkSealNos()
{
	//alert("hii");
	var checkSeals = '';
	var out_seal = jQuery('#out_seal').val();
	var in_seals = document.getElementsByName('in_seal[]');
	if(out_seal != '')
	{
		checkSeals = out_seal;
	}
	if(in_seals.length > 0)
	{
		for(i=0;i<in_seals.length;i++)
		{
			if(checkSeals == '')
			{
				// alert('hi');
				checkSeals = in_seals[i].value;
			}
			else
			{
				// alert('ggggg');
				checkSeals+= ','+in_seals[i].value;
			}
		}
	}
	// alert(checkSeals);
	if(checkSeals != '')
	{
		xajax_checkSealUsed(checkSeals);
		var sealsAvailable = document.getElementById('sealsAvailable').value;
		// alert(sealsAvailable);
		// return false;
	}

}*/
</script>


