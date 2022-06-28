<?
	require("include/include.php");
	require_once('lib/Weightment_AfrGrad_ajax.php');
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

	# Add RM Test Data Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	

	#Add
	if ($p["cmdAdd"]!="" ) {
		//echo "hii";
//die();		
		$materialType		=	$p["material_type"];
		$rmLotId		=	$p["rm_lot_id"];
		$supplier		=$p["supplier"];
		$pondName		=$p["pondName"];
		$total		=$p["total"];
		$effectiveWeight		=$p["effectiveWeight"];
		//$difference		=$p["difference"];
		///for loop
		$hidTableRowCount		=	$p["hidTableRowCount"];	
		
		// echo '<pre>';print_r($p);echo '</pre>';die;
		
		//$dateOfTesting		=	mysqlDateFormat($p["dateOfTesting"]);
		
		 if ($rmLotId!="") {
		 $WeightAfterGradingRecIns	=	$weightmentAfterGradingObj->addWeightAfterGrading($materialType,$rmLotId, $supplier,$pondName, $total, $effectiveWeight,$userId);
			//$WeightAfterGradingRecIns	=	$weightmentAfterGradingObj->addWeightAfterGrading($rmLotId, $supplier,$pondName, $total, $effectiveWeight,$difference,$userId);
			//$WeightAfterGradingRecIns	=	$weightmentAfterGradingObj->addWeightAfterGrading($rmLotId, $supplyDetails, $total, $totalwt,$difference,$userId);
			if($WeightAfterGradingRecIns)
			$lastId = $databaseConnect->getLastInsertedId();
			
			
			if ($hidTableRowCount>0 ) {
			 //echo $hidTableRowCount;
			
					for ($k=0; $k<$hidTableRowCount; $k++) {
					//echo "aa";
						$status = $p["status_".$k];
						// echo $status.'---'.$lastId.'----'.$p["grading_".$k].'-----'.$p["weight_".$k];die;
						  if ($status!='N') {
						
						$grandsingle		=	$p["grading_".$k];
						$weightsingle		=	$p["weight_".$k];
						$fish_id            =   $p["fish_id_".$k];
						$process_code       =   $p["process_code_".$k];
						//$count_code         =   $p["count_code_".$k];
						$lotidAvailable		  =   $p["lotidAvailable_".$k];
						
						//if ($lastId!="" ) {
						if ($lastId!="" ) { 
						// echo 'hh';
						$WeightAfterGradingDetailsRecIns	=	$weightmentAfterGradingObj->addWeightAfterGradingDetails($lastId,$fish_id,$process_code,$grandsingle, $weightsingle,$lotidAvailable,$userId);
						//$WeightAfterGradingDetailsRecIns	=	$weightmentAfterGradingObj->addWeightAfterGradingDetails($lastId,$fish_id,$process_code,$count_code, $grandsingle, $weightsingle,$lotidAvailable,$userId);
						
						}
					}
				  }
			  }
			// die;
	

			if ($WeightAfterGradingRecIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddWeightment);
				$sessObj->createSession("nextPage",$url_afterAddWeightment.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddWeightment;
			}
			$WeightAfterGradingDetailsRecIns		=	false;
		}	
		
	
		// if ($unit!="" ) {	
			// $rmTestDataRecIns	=	$rmTestDataObj->addRmTestData($unit, $rmLotId, $rmTestName, $rmtestMethod, $dateOfTesting,$result, $userId);
				
			

			// if ($rmTestDataRecIns) {
				
				// $sessObj->createSession("displayMsg",$msg_succAddWeightment);
				// $sessObj->createSession("nextPage",$url_afterAddWeightment.$selection);
			// } else {
				// $addMode	=	true;
				// $err		=	$msg_failAddWeightment;
			// }
			// $rmTestDataRecIns		=	false;
		// }	
	}
	

	# Edit weightment after grading
	$rm_lot_id_value = '';$companyName = ''; $unitName  = '';
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$weightmentAfterGradingRec	=	$weightmentAfterGradingObj->find($editId);	
		// print_r($weightmentAfterGradingRec);		
		$editweightmentGradingId	=	$weightmentAfterGradingRec[0];		
		$rm_lot_id	=	$weightmentAfterGradingRec[1];
		$material_type	=	$weightmentAfterGradingRec[2];
		$rm_lot_id_value = $weightmentAfterGradingRec[11];
		$supplierRecs	=	$weightmentAfterGradingObj->getSupplierNm($rm_lot_id);
		$supplierName	=	$weightmentAfterGradingRec[3];
		$pondRecs	=	$weightmentAfterGradingObj->getPond($rm_lot_id,$supplierName);
		
		$pondName	=	$weightmentAfterGradingRec[4];
		$total	=	$weightmentAfterGradingRec[5];
		$effectiveWeight=	$weightmentAfterGradingRec[6];
		//$difference	=	$weightmentAfterGradingRec[6];
		
		$gradewtedit=$weightmentAfterGradingObj->getWeightAfterGradingDetail($editweightmentGradingId);
		$Species 			= $weightmentAfterGradingObj->filterSpeciesList($pondName,$rm_lot_id);
		 $mainID= $Species[0];
		 $Fish 			= $weightmentAfterGradingObj->filterFishList($mainID);
		 //$fishID= $Fish[0];
		 $fishCode= $Fish[1];
		//$gradingRecs=$weightmentAfterGradingObj->filterGradeList($fishCode);
		
		$gradingRecs=$weightmentAfterGradingObj->getGradeVal($fishCode);
		$result 			= $weightmentAfterGradingObj->getCompayAndUnit($rm_lot_id);
		if(sizeof($result) > 0)
		{
			$companyName = $result[1];
			$unitName    = $result[3];
		}
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$rmLotId		=	$p["rm_lot_id"];
		$supplier		=$p["supplier"];
		$pondName		=$p["pondName"];
		$total		=$p["total"];
		$effectiveWeight		=$p["effectiveWeight"];
		//$difference		=$p["difference"];
		///for loop
		$hidTableRowCount		=	$p["hidTableRowCount"];	
		$editId		=$p["hidweightmentGrading"];
		$weightmentAfterGradingRecUptd=$weightmentAfterGradingObj->updateweightmentAfterGrading($editId,$rmLotId, $supplier,$pondName, $total, $effectiveWeight);
		
		//$weightmentAfterGradingRecUptd=$weightmentAfterGradingObj->updateweightmentAfterGrading($editId,$rmLotId, $supplier,$pondName, $total, $effectiveWeight,$difference);
		///for loop
		
		if ($hidTableRowCount>0 ) {
				//echo $hidTableRowCount;
					for ($k=0; $k<$hidTableRowCount; $k++) {
					//echo "aa";
					$status = $p["status_".$k];
					$rmId  		= $p["rmId_".$k];
					
						  if ($status!='N') {
						
						$grandsingle		=	$p["grading_".$k];
						$weightsingle		=	($p["weight_".$k]);
						$fish_id            =   $p["fish_id_".$k];
						$process_code       =   $p["process_code_".$k];
						$count_code         =   $p["count_code_".$k];
						$lotidAvailable		  =   $p["lotidAvailable_".$k];
						
					if ($editId!="" && $grandsingle!="" && $weightsingle!=""  && $rmId!="") {
						
						$weightmentAfterGradingRecUptd	=	$weightmentAfterGradingObj->updateweightmentAfterGradingDetails($rmId, $fish_id,$process_code,$count_code,$grandsingle, $weightsingle,$lotidAvailable);
					
					} else if($editId!="" && $grandsingle!="" && $weightsingle!="" && $rmId=="" ) {

					//($procurementId!="" &&  $equipmentName!=""  && $equipmentQty!="" && $equipmentIssued!="" && $balanceQty!=""  && $rmId=="")  {	
						$weightmentAfterGradingRecUptd	=	$weightmentAfterGradingObj->addWeightAfterGradingDetails($editId,$fish_id,$process_code,$count_code, $grandsingle, $weightsingle,$lotidAvailable,$userId);
								
						
					}
					
					}
				 
				  
				  	if ($status=='N' && $rmId!="") {
					//echo "ho";
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delAfterGradingRec = $weightmentAfterGradingObj->deleteweightmentGradingDetails($rmId);
					
				}
				  // die();	
				 }  
			  }
		//die();

		// if ($rmTestDataId!="" && $unit!="" && $rmLotId!="" && $rmTestName!="" && $rmtestMethod!="" && $dateOfTesting!="" && $result!="" ) {
			// $rmTestDataRecUptd	=	$rmTestDataObj->updateStockIssuance($rmTestDataId, $unit, $rmLotId,$rmTestName,$rmtestMethod,$dateOfTesting,$result);
							
		// }	
		if ($weightmentAfterGradingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succWeightmentUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightment.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failWeightmentUpdate;
		}
		$weightmentAfterGradingRecUptd	=	false;		
	}
	
	# Delete weightment after grading
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$WeightGradingId	=	$p["delId_".$i];

			if ($WeightGradingId!="" && $isAdmin!="") {

				$WeightGradingRecDel =	$weightmentAfterGradingObj->deleteWeightAfterGrading($WeightGradingId);
				$WeightGradingRecDel2 =$weightmentAfterGradingObj->deleteWeightAfterGradingDetails($WeightGradingId);
			}
		}
		if ($WeightGradingRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelWeightment);
			$sessObj->createSession("nextPage",$url_afterDelWeightment.$selection);
		} else {
			$errDel	=	$msg_failDelWeightment;
		}
		$WeightGradingRecDel	=	false;
		
	}
	
	if ($p["btnConfirm"]!="")
	{
		 $rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightmentgradingId	=	$p["confirmId"];
		
			if ($weightmentgradingId!="") {
				// Checking the selected fish is link with any other process
				$weightmentRecConfirm = $weightmentAfterGradingObj->updateWeighmentgradingconfirm($weightmentgradingId);
			}
		//die();
		}
		if ($weightmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmWeightmentAfterGrading);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightment.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}

		if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$weightmentgradingId = $p["confirmId"];
			if ($weightmentgradingId!="") {
				#Check any entries exist
				
					$weightmentRecConfirm = $weightmentAfterGradingObj->updateWeighmentgradingReleaseconfirm($weightmentgradingId);
				
			}
		}
		if ($weightmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmWeightmentAfterGrading);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightment.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
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
	
	
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$weightmentAfterGradingRecords	= $weightmentAfterGradingObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$rmTestDataSize	= sizeof($weightmentAfterGradingRecords);
		$weightmentAfterGradingRecs = $weightmentAfterGradingObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($weightmentAfterGradingRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	//$rmLotIds  = $weightmentAfterGradingObj->getAllLotIds();
	$fishes_master  = $weightmentAfterGradingObj->getAllFishesMaster();
	if ($editMode) $heading	=	$label_editWeightment;
	else $heading	=	$label_addWeightment;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/WeightmentAfterGrading.js"; // For Printing JS in Head section

	
	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>


	   
	<form name="frmWeightmentAfterGrading" action="WeightmentAfterGrading.php" method="post">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightmentAfterGrading.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateWeightmentAfterGrading(document.frmWeightmentAfterGrading);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightmentAfterGrading.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateWeightmentAfterGrading(document.frmWeightmentAfterGrading);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidweightmentGrading" value="<?=$editweightmentGradingId;?>">
											<tr>
											  <td colspan="2" nowrap class="fieldName" height="10px" >
										</td></tr>
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
											  																				
		<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
				
		?>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  class="fieldName" nowrap="">*Material type :</td>
					<td nowrap="">
					
					<select name="material_type" id="material_type" onchange="xajax_getRmLotId(document.getElementById('material_type').value,'');">
								<?php if($material_type=="")
								{
									echo "<option>--Select--</option>";
								}
								?>
								<?php 
							
								if($material_type=="" || $material_type=="Raw material")
								{
								?>
									<option>Raw material</option>
									<option>Pre process</option>
								<?php
								}
								elseif($material_type=="Pre process")
								{
								?>
									<option>Pre process</option>
									<option>Raw material</option>
								<?php
								}
								?>
								
								</select>
					</td>
				</tr>
               <tr>
						<td  class="fieldName">* Rm Lot ID :</td>
						<td nowrap="">
						<?php
							if($p['cmdEdit'])
							{
						?>
								<select name="rm_lot_id" id="rm_lot_id">
									<option value="<?php echo $rm_lot_id;?>"> <?php echo $rm_lot_id_value;?></option>
								</select>
						<?php 
							}
							else
							{
						?>
							<select onchange="xajax_supplierDetails(document.getElementById('rm_lot_id').value,document.getElementById('material_type').value);addNewRowWithFish(this.value);" id="rm_lot_id" name="rm_lot_id">
								<option value=""> -- Select Lot ID --</option>
								<?php
									if(sizeof($rmLotIds) > 0)
									{
										foreach($rmLotIds as $lotID)
										{	
											$sel = '';
											if($rm_lot_id == $lotID[0]) $sel = 'selected="selected"';
																								
											echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
										}
									}
								?>
							</select>
						<?php
							}
						?>
						</td>										
										
						<!--<td nowrap="" class="fieldName1">Supplier Name</td>
						<td nowrap>
						<select name="supplier" id="supplier" onchange="xajax_pondNames(document.getElementById('supplier').value,document.getElementById('rm_lot_id').value,'');" required >
						<option value="">-- Select --</option>
						<?php 
										foreach($supplierRecs as $sr)
										{
						$supplierNameId		=	$sr[0];
						$supplierNameVal	=	stripSlash($sr[1]);
						$selected="";
						if($supplierName==$supplierNameId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$supplierNameId?>" <?=$selected?>><?=$supplierNameVal?></option>
                                                    <? }
										
										
										?>
						</select>
					</td>-->
				</tr>
				<tr id="companyandunit">
						
					<?php
						if($companyName != '') 
						{
							echo '<td nowrap="" class="fieldName"> Billing Company : </td> <td class="listing-item" nowrap> '.$companyName.' </td>  ';
							echo '<td nowrap="" class="fieldName"> Unit : </td> <td class="listing-item" nowrap> '.$unitName.' </td>  ';
						}
						else
						{
							echo '<td>&nbsp;</td>';
							echo '<td>&nbsp;</td>';
						}
					?>					
						
						<!--<td nowrap="" class="fieldName1">Farm Name</td>
						<td nowrap>
						<select name="pondName" id="pondName" onchange="xajax_getGrading(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,'0','');" required >
						<option value="">-- Select --</option>
						<?php 
										foreach($pondRecs as $sr)
										{
						$pondNameId		=	$sr[0];
						$pondNameVal	=	stripSlash($sr[1]);
						$selected="";
						if($pondName==$pondNameId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$pondNameId?>" <?=$selected?>><?=$pondNameVal?></option>
                                                    <? }
										
										
										?>
						</select>
					</td>-->
						
				</tr>
			
				
				</table>
				<?php
			require("template/rbBottom.php");
		?>
			</td>
					  </tr>	
			<tr>
											  <td colspan="2" nowrap class="fieldName" height="10px" >
										</td></tr>		  
			<tr><td colspan="2" nowrap class="fieldName" >
			<table width="10%" cellspacing="1" align="center"  bgcolor="#999999" cellpadding="3" id="tblAddWeightmentAfterGrading" name="tblAddWeightmentAfterGrading">
													<tr bgcolor="#f2f2f2" align="center">
															
															<td class="listing-head" nowrap> Fish </td>
															<td class="listing-head" nowrap> Process Code </td>
															<!--<td class="listing-head" nowrap>Count Code </td>-->
															<td class="listing-head" nowrap>Grading </td>
															<td class="listing-head">Weight </td>
															
															
															
															
												<td></td>
													</tr>
											<?php 
											if (sizeof($gradewtedit)>0) {
		echo '<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="'.sizeof($gradewtedit).'" />';
		//echo '<input type="hidden" name="effectiveWeight" id="effectiveWeight" value="'.$effectiveWeight.'" />';
		$m=0;
	//echo $rm_lot_id;	
		// echo '<pre>'; print_r($gradewtedit);echo '</pre>';
			foreach ($gradewtedit as $pr) {	
		
			$editWeightmentGradeId=$pr[0];						
			$gradeID	=	$pr[1];
			$weight=$pr[2];		 
			$fish_id=$pr[3];
			$process_code_id = $pr[4];
			$count_code = $pr[5];
			$process_codes = $pr[6];
			$lotid_available=	$pr[7];
			// $chemicalQty	=	$pr[9];
			// $chemicalIssued=	$pr[10];
			$fishes = '';
			$process_code_dropdown='';
			
			if($lotid_available=='1')
			{	
				if($material_type=="Pre process")
				{
					$material=1;
					$fishRecords  = $weightmentAfterGradingObj->getAllFishesInDailyPreprocess($rm_lot_id);
				}
				else
				{
					$material=0;
					$fishRecords  = $weightmentAfterGradingObj->getAllFishes($rm_lot_id);
				}
		
				//$fishRecords  = $weightmentAfterGradingObj->getAllFishes($rm_lot_id);
				if(sizeof($fishRecords) > 0)
				{
					$fishes.= '<select name="fish_id_'.$m.'" id="fish_id_'.$m.'" onchange="xajax_getProcessCode(this.value,'.$rm_lot_id.','.$m.','.$material.');"><option value=""> -- Select -- </option>';
					foreach($fishRecords as $fish)
					{	
						$sel = '';
						if($fish[0] == $fish_id) $sel = 'selected';
						
						$fishes.= '<option '.$sel.' value="'.$fish[0].'">'.$fish[1].'</option>';
					}
					$fishes.= '</select>';
				}
			}
			else
			{
				$fishRecords  = $weightmentAfterGradingObj->getAllFishesMaster();
				if(sizeof($fishRecords) > 0)
				{	$vals='0';
					$fishes.= '<select name="fish_id_'.$m.'" id="fish_id_'.$m.'" onchange="xajax_getAllProcessCode(this.value,'.$m.','.$vals.');"><option value=""> -- Select -- </option>';
					foreach($fishRecords as $fish)
					{	
						$sel = '';
						if($fish[0] == $fish_id) $sel = 'selected';
						
						$fishes.= '<option '.$sel.' value="'.$fish[0].'">'.$fish[1].'</option>';
					}
					$fishes.= '</select>';
				}
			}
			
			
			if($lotid_available=='1')
			{
			
				if($material=="1")
				{
				$process_codes 			= $weightmentAfterGradingObj->getProcessCodeDailyProcessList($fish_id,$rm_lot_id);
				}
				elseif($material=="0")
				{
				
				$process_codes 			= $weightmentAfterGradingObj->getProcessCodeList($fish_id,$rm_lot_id);
				}
			
				//$process_codes  = $weightmentAfterGradingObj->getProcessCodeList($fish_id,$rm_lot_id);
				if($process_codes>0)
				{
					$process_code_dropdown.= '<select name="process_code_'.$m.'" id="process_code_'.$m.'" onchange="xajax_getGrading(this.value,'.$m.');"><option value=""> -- Select -- </option>';
					foreach($process_codes as $process)
					{	
						$sel = '';
						if($process[0] == $process_code_id) $sel = 'selected';
						
						$process_code_dropdown.= '<option '.$sel.' value="'.$process[0].'">'.$process[1].'</option>';
					}
					
					$process_code_dropdown.= '</select>';
				}
				
			}
			else
			{
				$process_codes  = $weightmentAfterGradingObj->getAllProcessCodeList($fish_id);
				if($process_codes>0)
				{
					$process_code_dropdown.= '<select name="process_code_'.$m.'" id="process_code_'.$m.'" onchange="xajax_getGrading(this.value,'.$m.');"><option value=""> -- Select -- </option>';
					foreach($process_codes as $process)
					{	
						$sel = '';
						if($process[0] == $process_code_id) $sel = 'selected';
						
						$process_code_dropdown.= '<option '.$sel.' value="'.$process[0].'">'.$process[1].'</option>';
					}
					
					$process_code_dropdown.= '</select>';
				}
				
			}
			/*if($process_codes != '')
			{
				$process_code_list = explode(',',$process_codes);
				if(sizeof($process_code_list) > 0)
				{
					foreach($process_code_list as $processCodes)
					{
						$processCodesDis = explode('$$',$processCodes);
						
						$sel = '';
						if($process_code_id == $processCodesDis[0]) $sel = 'selected';
						
						$process_code_dropdown.= '<option '.$sel.' value="'.$processCodesDis[0].'">'.$processCodesDis[1].'</option>';
					}
				}
				
			}*/
			
			$count_code_input   = '<input type="text" size="12" name="count_code_'.$m.'" id="count_code_'.$m.'" value="'.$count_code.'" />';
			$GradeVal= '';
			$GradeValues = $weightmentAfterGradingObj->filterGradeListEdit($process_code_id);
			$GradeVal.= '<select name="grading_'.$m.'" id="grading_'.$m.'"><option value=""> -- Select -- </option>';
			if(sizeof($GradeValues) > 0)
			{
				foreach($GradeValues as $GradeOption)
				{	
					$sel = '';
					if($GradeOption[0] == $gradeID) $sel = 'selected';
					
					$GradeVal.= '<option '.$sel.' value="'.$GradeOption[0].'">'.$GradeOption[1].'</option>';
				}
			}
			$GradeVal.= '</select>';
			$weight_input   = '<input type="text" name="weight_'.$m.'" id="weight_'.$m.'" size="4" style="text-align:right;" onkeyup="checkValue('.$m.');" value="'.$weight.'" />';
			$imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatus(".$m.");\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			$hiddenFields = "<input name='status_".$m."' type='hidden' id='status_".$m."' value=''><input name='IsFromDB_".$m."' type='hidden' id='IsFromDB_".$m."' value='N'><input type='hidden' name='rmId_".$m."' id='rmId_".$m."' value='".$editWeightmentGradeId."'>";
			echo '<tr class="whiteRow" id="row_'.$m.'">';
			echo '<td class="listing-item">'.$fishes.'</td>';
			echo '<td class="listing-item">'.$process_code_dropdown.'</td>';
			//echo '<td class="listing-item">'.$count_code_input.'</td>';
			echo '<td class="listing-item">'.$GradeVal.'</td>';
			echo '<td class="listing-item">'.$weight_input.'</td>';
			echo '<td class="listing-item">'.$imageButton.' '.$hiddenFields.'</td>';
			echo '</tr>';
			
	 		$m++;
			}
			
		}
		else
		{
		?>
			<tr bgcolor="#e8edff">
				
					<td colspan="6" >
						<table width="80%" border="0"  align="center" cellspacing="1" cellpadding="2">
							<tbody><tr>
										<td height="10" align="center" class="err1" colspan="6">No records found.</td>
									</tr>	
							</tbody>
						</table>									
					</td>
			</tr>
		<?php 
		}
		?>
	
										
										</table>
										
									
									
											<tr><TD height="10"></TD></tr>
											<tr id="hiderowequipment">
												<TD nowrap style="padding-left:5px; padding-right:5px;" align="left">
													<a href="javascript:void(0);" id='addRow' onclick="javascript:addNewWeightmentAfterGrading();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >
														Add New Item
													</a>
												</TD>
												<?php 
												if($reEdit=true)
												{
												?>
												<TD nowrap style="padding-left:5px; padding-right:5px;"  align="left">
													<a href="javascript:void(0);" id='addRow' onclick="javascript:addNewWeightmentAfterGradingSpecies();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >
														Add New Fish
													</a>
												</TD>
												<?php
												}
												?>
												
											</tr>
			<tr><td colspan="2" nowrap class="fieldName" height="10px">	</TD></tr>
			<tr><td colspan="2" nowrap class="fieldName" >
			<?php
				$left_l=true;
				$entryHead = "";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>			
			<table width="200" align="right">
			   					  <tr>
								  
								 
                                	<td class="fieldName" nowrap>Total Weight:&nbsp;</td>
                                       <td><input type="text" name="total" id="total" size="5" readonly value="<?=$total;?>"  /></td>
									   <!--<td class="fieldName" nowrap>Difference in Weight&nbsp;</td>
                                        <td><input type="text" name="difference" id="difference" size="5" readonly value="<?=$difference;?>"  /></td>-->
                                                </tr>
								
								<tr>
                                	<!--<td class="fieldName" nowrap>*Total:&nbsp;</td>-->
									<?php 
									// if($effectiveWeight == '') $effectiveWeight = 0; 
									?>
                                       <td> <!--<input type="hidden" name="effectiveWeight" id="effectiveWeight" size="9" value="<?=$effectiveWeight;?>"  />--></td>
									   
                                                </tr>
												
							 <tr>
							 
							 
                                	
                                                </tr>
                                              </table>
											<?php
												require("template/rbBottom.php");
											?>
											  </td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightmentAfterGrading.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateWeightmentAfterGrading(document.frmWeightmentAfterGrading);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('WeightmentAfterGrading.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateWeightmentAfterGrading(document.frmWeightmentAfterGrading);">												</td>

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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Weightment After Grading  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintWeightAfterGrading.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
												if( sizeof($weightmentAfterGradingRecords) > 0 )
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
      				$nav.= " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Rm lot Id</td>
		<!--<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Supply Details</td>-->
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Fish</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Process code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Grade</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Weight</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Total Wt</td>
		<!--<td class="listing-head" style="padding-left:10px; padding-right:10px;">Diff in Weight</td>-->
		<td class="listing-head"></td>
		<? if($confirm==true){?>
                        <td class="listing-head">&nbsp;</td>
			<? }?>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($weightmentAfterGradingRecords as $sir) {
		$i++;
		$WeightGradingId	=	$sir[0];
		$LotId		=	$sir[1];
		$alpha		=	$sir[2];
		//$lot = $weightmentAfterGradingObj->getLotNm($LotId);
		//$newLot=$lot[1];
		$supplierName		=	$sir[3];
		$PondName		=	$sir[4];
		$sumtotal		=	$sir[5];
		$differ		=	$sir[7];
		$active		=	$sir[8];
		$method = $weightmentAfterGradingObj->getWeightAfterGradingDetail($WeightGradingId);
		//$unit		=	$sir[1];
		
		// $unitRec		=	$plantandunitObj->find($sir[1]);
		// $unit		=	$unitRec[2];
		////$rmLotId		=	$sir[2];
		// $lotRec		=	$rmTestDataObj->findLot($sir[2]);
		// $rmLotId		=	$lotRec[1];

		////$rmTestName		=	$sir[3];
		// $testNameRec		=	$rmTestMasterObj->find($sir[3]);
		// $rmTestName		=	$testNameRec[1];
		// $rmtestMethod		=	$testNameRec[2];
		// $dateOfTesting		=	dateFormat($sir[5]);
		// $result		=	$sir[6];
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$WeightGradingId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alpha.$LotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $fish=$detail[8];
			echo '<br/>';
			}
			?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
			<?php 
			foreach($method as $detail)
			{
			echo $processcode=$detail[9];
			echo '<br/>';
			}
			?> 
		</td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><a onMouseOver="ShowTip('<?=$PondName;?>');" onMouseOut="UnTip();"><?=$supplierName;?></td>-->
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"> 
		<?php 
		foreach($method as $detail)
		{
		 $gradeID=$detail[1];
		
		$grade = $weightmentAfterGradingObj->getGradeNm($gradeID);
		echo $grade[1];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($method as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> 
		</td>
		
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sumtotal;?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$differ;?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<a href="javascript:printWindow('ViewWeightmentAfterGradingDetails.php?WeightGradingId=<?=$WeightGradingId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
		</td>
			<? if ($confirm==true ){?>
															<td <?php if ($active==1) {?> class="listing-item" <?php }else {?>  <?php }?> width="45" align="center" >
															
															<?php 
															 if ($confirm==true){	
															if ($active=="0"){ ?>
															<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$WeightGradingId;?>,'confirmId');" >
															<?php } else if ($active==1){ ?>
															<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$WeightGradingId;?>,'confirmId');" >
															<?php  } 
															}?>
														</td>
													<?}?>
	
	<? if($edit==true){?>
	<?php if ($active!=1) { ?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$WeightGradingId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='WeightmentAfterGrading.php';"></td>
	<? }
	else
	{
	echo "<td></td>";
	}
	
	}?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"WeightmentAfterGrading.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintWeightAfterGrading.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
												
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

<? if ($addMode || $editMode) {?>	
<SCRIPT LANGUAGE="JavaScript">
	
	 function addNewWeightmentAfterGrading() 
	{ //alert("equipment");
		addNewWeightmentGrading('tblAddWeightmentAfterGrading', '','', '','addmode');
		//return true;
	}
	
	function addNewWeightmentAfterGradingSpecies()
	{
		//alert("hii");
		addNewWeightmentGradingSpecies('tblAddWeightmentAfterGrading', '','','', '','addmode');		
	}
	
</SCRIPT>		
<? }?> 	
	<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">

//window.load = addNewWeightmentAfterGrading();
//window.onLoad = addNewRMProcurmentChemicalItem();

</SCRIPT>
<? }?>


<? 
	if ($editMode!="") {
	
		
		
		
		?>
		<SCRIPT LANGUAGE="JavaScript">
		 function addNewWeightmentAfterGrading() 
	{ 
		addNewWeightmentGrading('tblAddWeightmentAfterGrading', '','', '','addmode');
		
	}
	
		</SCRIPT>
		<?
		
		
		
		
	}
	?>


	
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
	
	function addNewRowWithFish(rm_lot_id)
	{
		// jQuery('#tblAddWeightmentAfterGrading').html('');
		// if(rm_lot_id != '')
		// {
			// addNewWeightmentGrading('tblAddWeightmentAfterGrading', '','', '','addmode');
		// }
	}
	</SCRIPT>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>