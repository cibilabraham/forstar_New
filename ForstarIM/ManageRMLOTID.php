<?php
	require("include/include.php");
	
	require_once("lib/ManageRMLOTID_ajax.php");
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	
$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//$totalResultVal = $objManageRMLOTID->getLotIdTotalvalue(20);
		//printr($totalResultVal);
	
	
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
	//if($accesscontrolObj->canGenerate()) $generate=true;	
	/*-----------------------------------------------------------*/
	
	
	//$companyNames   = $objManageRMLOTID->getAllCompany();
	//$units          = $objManageRMLOTID->getAllUnit();
	if(isset($g["generateLotID"]) && sizeof($g["generateLotID"]) > 0)
	{
		$generateId= $g['generateLotID'];
	 	$receiptgatePassId=base64_decode($generateId);
		$procurementId = $objManageRMLOTID->getProcurementId($receiptgatePassId);
		$prid=$procurementId[0] ;
		if($prid > 0)
		{
		$supplierDetail= $objManageRMLOTID->getReceiptSupplierDetails($receiptgatePassId);
		$procurementAvailable="1";
		}
		else
		{
		//echo "hii";
		$supplierDetail=$objManageRMLOTID->getReceiptGatePassSupplier($receiptgatePassId);
		$procurementAvailable="0";
		}
		$cnt=sizeof($supplierDetail);
		$addMode	=	true;
	}
	
	if($p["save_rmlotid"]!="")
	{
	
	//$procurement_id=$p['procurement_id'];
	
		$rowCount	=	$p["supplierSize"];
		for ($i=1; $i<=$rowCount; $i++)
		{		//echo "hii";
				// $i;
				$alphaValue	=	$p["alphaValue_".$i];
				$rmId	=	$p["rmId_".$i];
				$number_genval=$p["number_genval_".$i];
				$company_idval	=	$p["company_idval_".$i];
				$unit_idval=$p["unit_idval_".$i];
			
				$supplier_id	=	$p["supplyDetail_".$i];
				$receipt_idval	=	$p["receipt_idval_".$i];
				$farmIdVal=$p["farmIdVal_".$i];
				$landingCenterIdVal=$p["landingCenterIdVal_".$i];
				$receiptGatePass=$p["receiptGatePass_".$i];
				$supplierChellanDate=$p["supplierChellanDate_".$i];
				$supplierChellanVal=$p["supplierChellan_".$i];

				if($alphaValue == ""){$alphaValue = "F";}
			

				if ($rmId !="" && $alphaValue !="" && $company_idval !="" && $unit_idval !="" && $number_genval !="") 
				{
					$rmlotIdList = $objManageRMLOTID->addManageLotId($rmId,$alphaValue,$company_idval,$unit_idval,$number_genval,$userId);
				} 
					if($rmlotIdList)					
					$lastId = $databaseConnect->getLastInsertedId();
					if($supplier_id!="")
					{
						$supplierSingle = explode(",", $supplier_id);
						$receipt_idSingle = explode(",", $receipt_idval);
						$farmIdSingle = explode(",", $farmIdVal);
						$receiptGatePassSingle = explode(",", $receiptGatePass);
						$supplierChellanDt=explode(",",$supplierChellanDate);
						$supplierChellanSingle = explode(",", $supplierChellanVal);
						$landingCenterSingle = explode(",", $landingCenterIdVal);
						$sizevalSupplier=sizeof($supplierSingle);
						
						if($sizevalSupplier>1)
						{
							for($j=0;$j<$sizevalSupplier;$j++)	
							{
							
								$supply=$supplierSingle[$j];	
								$receipe=$receipt_idSingle[$j];
								$farm=$farmIdSingle[$j];
								$receiptGate=$receiptGatePassSingle[$j];
								$supplierchellan=$supplierChellanSingle[$j];
								$supplierChellanDts=$supplierChellanDt[$j];
								$landingCenter=$landingCenterSingle[$j];
								$rmlotIdListVal = $objManageRMLOTID->addManageLotIdDetail($lastId,$receipe,$receiptGate,$supply,$farm,$supplierChellanDts,$supplierchellan,$landingCenter);
							}
						}
						else
						{
							if ($supplier_id!="" && $receiptGatePass!=""  ) 
							{
								$rmlotIdListVal = $objManageRMLOTID->addManageLotIdDetail($lastId,$receipt_idval,$receiptGatePass,$supplier_id,$farmIdVal,$supplierChellanDate,$supplierChellanVal,$landingCenterIdVal);
							} 
						}
					}
		
					
				$delRmLOtId = $objManageRMLOTID->deleteTemporary($rmId,$number_genval);	
				
				if($rmId!="")
				{
					if($rmIdVal=="")
					{
						$rmIdVal=$alphaValue.$rmId;
					}
					else
					{
						$rmIdVal.=','.$alphaValue.$rmId;
					}
				}
				
		}
		//die();
		
		#-----------------------------------------------------------------
				# insert last generated manage rmlotid to manage chellan
		
		$rmlastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($rmId,$number_genval);
		
		
		$rmAlpha=$rmIdVal;
		
		$recpt=$p["receiptgatePassId"];
		$valuealpha= $objManageRMLOTID->updateReceiptGatePass($rmAlpha,$recpt);
		/*
		$valueDetail= $objManageRMLOTID->getReceiptGatePassSupplier($recpt);
		$vehicleId=$valueDetail[0][7];
		$manageLotIdVehicleRecIns	=	$objManageRMLOTID->updateVehiclestatus($vehicleId);
		$driver=$valueDetail[0][2];
		$driverDetail=explode(",",$driver);
		$driverSize=sizeof($driverDetail);
		for($k=0; $k<$driverSize; $k++)
		{
		$driverId=$driverDetail[$k];
		$manageLotIdDriverRecIns	=	$objManageRMLOTID->updateDriverstatus($driverId);
		}*/

		//die();
		//$procurement_id;
	
		
		//echo $rmlotIdList;
		
	//

		echo "<script>alert('ManageRMLOTID added successfully');window.location='ManageRMLOTID.php';</script>";
		
		
		// $msg_succAddManageRMLOTID = "ManageRMLOTID added successfully";
		
		// $sessObj->createSession("displayMsg",$msg_succAddManageRMLOTID);
		// header("location:ManageRMLOTID.php");
		
		
		//$sessObj->createSession("nextPage",$url_afterAddManageRMLOTID);
			// if ($rmlotIdList!="") {
				//if( $err!="" ) printJSAlert($err);
				//$addMode	=	false;
				//echo $msg_succAddRmLotId;
				//die();
				// $sessObj->createSession("displayMsg",$msg_succAddRmLotId);
				//header("Location: $url_afterAddRmLotId");
				// $sessObj->createSession("nextPage",$url_afterAddRmLotId);
			// } else {
				// $addMode	=	true;
				// $err		=	$msg_failAddRmLotId;
			// }
			// $rmlotIdList		=	false;
			// $hidEditId 	=  "";
		
	}
	if ($p["editId"]!="") 
	{
	$i=0;
	$editId			=	$p["editId"];
	$editMode		=	true;
	$supplierDetail	=	$objManageRMLOTID->find($editId);
	$rmlotNmDet	=	$objManageRMLOTID->getRMLotName($editId);
	$manageRmLotIdEdit=$p["editId"];
	// $receiptSupplierId=$manageRmLotIdEdit[0];
	// $receiptGatePassId=$manageRmLotIdEdit[1];
	// $supplierId=$manageRmLotIdEdit[2];
	// $farmId=$manageRmLotIdEdit[3];
	 $companyId=$supplierDetail[0][0];
	 $unitId=$supplierDetail[0][1];
	// $rmlotId=$manageRmLotIdEdit[6].$manageRmLotIdEdit[7];
		// if($receiptSupplierId == 0)		
		// {
			// $supplierDetail= $objManageRMLOTID->getRMLotIdSupplierDetailsMain($receiptGatePassId,$supplierId);
		// }
		// elseif($receiptSupplierId > 0)
		// {
			// $supplierDetail= $objManageRMLOTID->getRMLotIdSupplierDetailsSub($receiptGatePassId,$supplierId,$farmId);
		// }
		$lot= $objManageRMLOTID->getAllRmlotIdOfSameCompanyUnit($companyId,$unitId,$editId);
	//	
	}
	if($p["cmdSaveChange"]!="")
	{
	
	
	//echo "hii";
	 $rowCounts	=	$p["manageRmSize"];
	 $j=0;
	for ($i=0; $i<$rowCounts; $i++)
		{		//echo "hii";
				// $i;
				
				$editRmlotID	=	$p["editRmlotID_".$i];
				$editRmlotIDName	=	$p["editRmlotIDName_".$i];
				$rmlotID_Detail=$p["rmlotID_Detail_".$i];
				$supplier_id	=	$p["supplier_id_".$i];
				$pond_id	=	$p["pond_id_".$i];
				$rm_lot_id	=	$p["rm_lot_id_".$i];
				$landing_center_id	=	$p["landing_center_id_".$i];
				$receipt=$p["receipt_".$i];
				$receiptGatePassId=$p["receiptGatePassId_".$i];
				$rmLotDet= $objManageRMLOTID->getLotIDInReceipt($editRmlotIDName);
				#---------------------------------------------
				#remove value from a string
				// $result_string= $objManageRMLOTID->removeFromString($rmLotDet[0], $editRmlotIDName); 
				// echo $result_string;
				
				if($rm_lot_id!="")
				{
				////update lotid in receipt gate pass
				$checkExist=$objManageRMLOTID->checkLotIdExist($rm_lot_id);
				//die();
				$rmlotid_name=$checkExist[0][0];
				$alpa=$objManageRMLOTID->receiptgatePassCheck($rmlotid_name,$receiptGatePassId);
					if(sizeof($alpa)==0)
					{
					$getlotId=$objManageRMLOTID->checkReceiptLot($receiptGatePassId);
					$result_string= $objManageRMLOTID->removeFromString($getlotId[0], $editRmlotIDName); 
					//$newlot=$getlotId[0].','.$editRmlotIDName;
					//$lotIdNm=$objManageRMLOTID->checkReceiptLot($receiptId);
					$result_string;
					
						if(sizeof($result_string)>0)
						{
							$newlot=$result_string.','.$rmlotid_name; 
						}
						else
						{
							$newlot=$rmlotid_name;
						}
						//ECHO  $newlot;
						$updateReceipt=$objManageRMLOTID->updatermLotInReceipt($receiptGatePassId,$newlot);
						//DIE();
					$j++;
					}
					else{
					$j++;
					}
				//die();//sleep($updateReceipt);
				
				//update lotid in manage rmlotid sub table
				$upSupplier= $objManageRMLOTID->updateDetailofRmlotId($rm_lot_id,$rmlotID_Detail,$editRmlotID,$receipt,$receiptGatePassId,$supplier_id,$pond_id,$landing_center_id);
					
				$checkExistNew=$objManageRMLOTID->checkNewLotIdExist($rm_lot_id);
				//die();
					if(sizeof($checkExistNew)>0)
					{	
						$weightmentIdNew=$checkExistNew[0][0];
						$supplierVal='0';
						$checkExistOld=$objManageRMLOTID->checkOldLotIdExist($editRmlotID);
						if(sizeof($checkExistOld)>0)
						{
							foreach($checkExistOld as $supplierDet)
								{
									$weightmentEntryID=$supplierDet[1];
									$supplier_old=$supplierDet[2];
									$pond_old=$supplierDet[3];
									if(($supplier_id==$supplier_old) && ($pond_id==$pond_old))
									{
									$supplierVal='1';
									//update
									$updateOldlot=$objManageRMLOTID->updatermLotWeightmentOld($weightmentEntryID,$weightmentIdNew);
									}
								}
						}
						else
						{
							$updateNewlot=$objManageRMLOTID->appendingDataEntryInWeightmentData($weightmentIdNew,$supplier_id,$pond_id);
						//die();
						}
					}
					
					

				}
			
		}
		
//die();
		
		if($rowCounts == $j)
				{
				//set status of rmlotid as free
				$updateStatus=$objManageRMLOTID->updateLotIdStatus($editRmlotID);
					foreach($rmLotDet as $rmlot)
					{
					$result_string= $objManageRMLOTID->removeFromString($rmlot[0], $editRmlotIDName); 
					$result_value= $objManageRMLOTID->updateLotIdReceipt($result_string,$rmlot[1]);
					
					}
				}
						
				$chckExist=$objManageRMLOTID->chkLotidExtra($editRmlotID);
				if(sizeof($chckExist)>0)
				{
					$getlotId=$objManageRMLOTID->checkReceiptLot($chckExist[0]);
					if(sizeof($getlotId)>0)
					{
						$result_strings= $objManageRMLOTID->removeFromString($getlotId[0], $editRmlotIDName); 
						$newlots=$result_strings.','.$editRmlotIDName;
						$updateReceipt=$objManageRMLOTID->updatermLotInReceipt($chckExist[0],$newlots);
					}
				}
				$chckWt=$objManageRMLOTID->chkWeightment($editRmlotID);
				if((sizeof($chckWt)==0) || (sizeof($chckWt)==""))
				{
					$objManageRMLOTID->deleteLotFrmWeightment($editRmlotID);
				}
	
				if ($upSupplier) {
				
				$sessObj->createSession("displayMsg",$msg_succRmLotIdUpdate);
				$sessObj->createSession("nextPage",$url_afterUpdateRmLotId.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failRmLotId;
			}
			$editMode		=	false;
	
	
	
	//die();
	}
	if($p["addUnit"]!="")
	{
		$Company_Name	=	$p["Company_Name"];
		$unit	=	$p["unit"];
		$alphavalue	=	$p["alphavalue"];
		$generateNewLotId	=	$p["generateNewLotId"];
		$rm_lot_id	=	$p["rm_lot_id"];
		$number_genval=$p["number_gen"];
		$unitTransferRecIns = $objManageRMLOTID->addManageLotIdNew($rm_lot_id,$alphavalue,$generateNewLotId,$Company_Name,$unit,$number_genval,$userId);
		$delRmLOtId = $objManageRMLOTID->deleteTemporary($generateNewLotId,$number_genval);	
			#-----------------------------------------------------------------
				# insert last generated manage rmlotid to manage chellan
		
		$rmlastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($generateNewLotId,$number_genval);
		if ($unitTransferRecIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddRmLotIdUnitTransfer);
				$sessObj->createSession("nextPage",$url_afterAddRmLotIdUnitTransfer.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRmLotIdUnitTransfer;
			}
			$unitTransferRecIns		=	false;
	}
	
	
	/*if ($p["cmdGenerate"]!="")
	{
		echo "hii";
		$rowCount	=	$p["supplierSize"];
		for ($i=1; $i<=$rowCount; $i++) {
			$receiptId	=	$p["rm_lot_".$i];
				if( $receiptId!="")
				{
				$supplier_id	=	$p["supplier_id_".$i];
				$unit	=	$p["unit_".$i];
				$Company_Name	=	$p["Company_Name_".$i];
				
				//$checkexist=$objManageRMLOTID->checkLotId($receiptId,$supplier_id,$unit,$Company_Name);
				
				///check this $receiptId,$supplier_id,$unit,$Company_Name exist in temporary table
				//if yes donot generate lotID
				//if no generate RMlotID USING CHALLAN AND INSERT TO TEMPORARY TABLE
				
				//$checkValue=
				
				
				//$tempInsert=$objManageRMLOTID->addLotID($receiptId,$supplier_id,$unit,$Company_Name);
				}
			}
	//die();
	}*/
	
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rmlotId	=	$p["confirmId"];
			if ($rmlotId!="") {
				// Checking the selected fish is link with any other process
				$rmlotRecConfirm = $objManageRMLOTID->updateRMLotIDconfirm($rmlotId);
			}

		}
		if ($rmlotRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRMLotID);
			$sessObj->createSession("nextPage",$url_afterUpdateRMLotID.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}

	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$rmlotId = $p["confirmId"];
			if ($rmlotId!="") {
				#Check any entries exist
				
					$rmlotRecConfirm = $objManageRMLOTID->updateRMLotIDReleaseconfirm($rmlotId);
				
			}
		}
		if ($rmlotRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRMLotID);
			$sessObj->createSession("nextPage",$url_afterUpdateRMLotID.$selection);
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
	
	#List all Stock Issuance
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		$companyNm="";  $unitNm=""; $processStage="";
		($p["CompanyName"]!="")? $companyNm=$p["CompanyName"] : "";
		($p["unitName"]!="")? $unitNm=$p["unitName"] : "";
		($p["processingStage"]!="")? $processStage=$p["processingStage"] : "";
		//$manageRmLotIdRec	= $objManageRMLOTID->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$manageRmLotIdRec	= $objManageRMLOTID->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit,$companyNm,$unitNm,$processStage);
		$manageRmLotIdSize	= sizeof($manageRmLotIdRec);
		$manageRmLotIdRecords = $objManageRMLOTID->fetchAllDateRangeRecords($fromDate, $tillDate,$companyNm,$unitNm,$processStage);
	}
	//$stockissuanceObj->fetchAllRecords()

	list($companyNames,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
	//echo $companyNm;
	
	//printr($units);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($manageRmLotIdRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/ManageRMLOTID.js"; // For Printing JS in Head section
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="ManageRMLOTID" action="" method="post">
	<table width="70%" align="center" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td height="20" align="center" class="err1"> </td>
			</tr>
			<tr>
				<td>
				<?php if(($receiptgatePassId)>0)
				{
				?>
					<table width="96%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
						<tbody>
							<tr>
								<td bgcolor="white">
									<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
												<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; Manage RM LOT ID </td>
											</tr>												
											<tr>
												<td height="10"></td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td colspan="4" align="center">
													<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">	
													<?php/* if(($receiptgatePassId)>0)
													{
													*/?>
														<input type="hidden" name="procurement_id" id="procurement_id" value="<?php echo $prid;?>"/>
														<tr>
															<td></td>
															<td>
																<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" 			name="tblAddProcurmentOrderSupplier">
																	<tr bgcolor="#f2f2f2" align="center">
																		<td class="listing-head" nowrap>
																			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'rm_lot_'); " class="chkBox"></td>
																		<td class="listing-head" nowrap>Supplier name </td>
																		<td class="listing-head" nowrap>Farm Name </td>
																		<td class="listing-head" nowrap>Landing Center</td>
																		<td class="listing-head" nowrap>Challan no </td> 
																		<td class="listing-head" nowrap>Date</td>
																		<td class="listing-head" nowrap>ALLOTED TO COMPANY  </td>
																		<td class="listing-head" nowrap>UNIT  </td>
																	</tr>
																	<?php
																	if(sizeof($supplierDetail)>0)
																	{
																		$n=0;
																		foreach($supplierDetail as $supplierVal)
																		{
																			//$objResponse->alert($n);
																			$supplierNm			    = $rmReceiptGatePassObj->getSupplierName($supplierVal[1]);
																			$supplierName=$supplierNm[0];
																			$pondId=$supplierVal[2];
																			$pondNm			    = $rmReceiptGatePassObj->getPondName($pondId);
																			$pondName=$pondNm[0];
																			$landId=$supplierVal[7];
																			$landingcntr			    = $objManageRMLOTID->getLandingCenter($landId);
																				if($pondId!='0' && $landingcntr=="")
																				{
																					$landingcntr =$objManageRMLOTID->filterPondLocationListEdit($pondId);
																					$landingCenter=$landingcntr;
																				}
																				else
																				{
																					 $landingCenter=$landingcntr;
																				}
																	?>
																	<tr  align="center"  bgcolor="#FFFFFF">
																		<td class="listing-head" nowrap>
																			<input type="checkbox" class="chkBox" value="<?php echo $supplierVal[0];?>" id="rm_lot_<?php echo $n;?>" name="rm_lot_<?php echo $n;?>"></td>
																		<td class="listing-head" nowrap><?php echo $supplierName;?>
																			<input type="hidden" value="<?php echo $supplierVal[1];?>" size="15" id="supplier_id_<?php echo $n;?>" name="supplier_id_<?php echo $n;?>" required />
																		</td>
																		<td class="listing-head" nowrap><?php echo $pondName;?>
																			<input type="hidden" value="<?php echo $pondId;?>" size="15" id="pond_id_<?php echo $n;?>" name="pond_id_<?php echo $n;?>" required />
																		</td>
																		<td class="listing-head" nowrap><?php echo $landingCenter;?>
																			<input type="hidden" value="<?php echo $landId;?>" size="15" id="landing_center_<?php echo $n;?>" name="landing_center_<?php echo $n;?>" required />
																		</td>
																		<td class="listing-head" nowrap>
																			<?php echo $supplierVal[3];?>
																			<input type="hidden" value="<?php echo $supplierVal[3];?>" size="15" id="challan_no_<?php echo $n;?>" name="challan_no_<?php echo $n;?>" required /></td>
																			<?php 
																			$challan_date = dateFormat($supplierVal[4]);
																			?>
																		<td class="listing-head" nowrap>
																			<?php echo $challan_date;?>
																		<input type="hidden" size="15" name="challan_date_<?php echo $n;?>" id="challan_date_<?php echo $n;?>" value="<?php echo$challan_date;?>" required />
																		</td>
																		<td class="listing-head" nowrap>
																			<select id="Company_Name_<?php echo $n;?>" name="Company_Name_<?php echo $n;?>" required onchange="xajax_getUnitMultipleRow(this.value,'<?php echo $n;?>','');"/>
																				<option value="">--select--</option>
																				<?php
																				
																				$Company_Name=$supplierVal[5];

																				if(sizeof($companyNames) > 0)
																				{
																					foreach($companyNames as $compId=>$compName)
																					{
																						$companyId=$compId;
																						$companyName=$compName;
																						$sel = '';
																						if(($Company_Name == $companyId) || ($Company_Name=="" && $companyId==$defaultCompany)) $sel = 'selected';
																						echo '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																					}
																				}
																				?>
																			</select>		
																		</td>
																		<td class="listing-head" nowrap>
																			<select id="unit_<?php echo $n;?>" name="unit_<?php echo $n;?>" required>
																				<option value="">--select--</option>
																					<?php
																					$unit=$supplierVal[6];
																					($unit!="")?$units=$unitRecords[$Company_Name]:$units=$unitRecords[$defaultCompany];
																					if(sizeof($units) > 0)
																					{
																						foreach($units as $untId=>$untNm)
																						{
																							$unitId=$untId;
																							$unitName=$untNm;
																							$sel = '';
																							if($unit == $unitId) $sel = 'selected';
																							echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																						}
																					}
																					?>							
																			</select>	
																		</td>
																		<input type="hidden" name="receipt_<?php echo $n;?>" value="<?php echo $supplierVal[0];?>"  id="receipt_<?php echo $n;?>"/>
																</tr>
																<?php
																	$n++;
																	}
																}
																?>
															</table>
															<input type="hidden" id='procurementAvailable' name='procurementAvailable' value="<?php echo $procurementAvailable;?>"/>
															<input type="hidden" id='generateLotid' name='generateLotid' value="<?php echo $g["generateLotID"];?>"/>
														</td>
													</tr>
													<tr><td colspan="4" height="10px"><span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span></td></tr>
													<tr>
														<td colspan="4" align="center">
															<table align="center" >
															<?php for($i=0; $i<=$cnt; $i++)
															{
															?>
																<tr>
																	<td  id='display_lotId_<?php echo $i;?>'  class="listing-head">
																	</td>
																	<td colspan="2">
																		<input type="hidden" name='alphaValue_<?php echo $i;?>' id='alphaValue_<?php echo $i;?>' />
																		<input type="hidden" name='rmId_<?php echo $i;?>' id='rmId_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='supplyDetail_<?php echo $i;?>' id='supplyDetail_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='receipt_idval_<?php echo $i;?>' id='receipt_idval_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='company_idval_<?php echo $i;?>' id='company_idval_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='unit_idval_<?php echo $i;?>' id='unit_idval_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='farmIdVal_<?php echo $i;?>' id='farmIdVal_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='landingCenterIdVal_<?php echo $i;?>' id='landingCenterIdVal_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='number_genval_<?php echo $i;?>' id='number_genval_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='receiptGatePass_<?php echo $i;?>' id='receiptGatePass_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='supplierChellanDate_<?php echo $i;?>' id='supplierChellanDate_<?php echo $i;?>' value=''/>
																		<input type="hidden" name='supplierChellan_<?php echo $i;?>' id='supplierChellan_<?php echo $i;?>' value=''/>
																	</td>
																</tr>					
																	<?php
																	}
																	?>
																		<input type="hidden" size="15" name="supplierSize" id="supplierSize" value="<?=$cnt;?>" /> 
																		<input type="hidden" size="15" name="hidcheck" id="hidcheck" value="" /> 
																		<input type="hidden" size="15" name="receiptgatePassId" id="receiptgatePassId" value="<?php echo $receiptgatePassId;?>" /> 
																		<input type="hidden" name='rowcnt' id='rowcnt' value=''/>
																		
																<tr><td colspan="3" height="10px"></td></tr>
																<tr>
																	<td><input type="submit" value="Generate " name="cmdGenerate" class="button" onClick="return GenerateRmLotId(this.form,'rm_lot_',<?=$cnt;?>); "></td>
																	<td id='reset_button'></td>
																	<td id='save_button'></td>
																</tr>										
															</table>
														</td>
													</tr>
													<tr>
														<td height="10px" colspan="2"></td>
													</tr>
													<tr>
														<td colspan="2" height="20px"></td>
													</tr>
											</table>
										</td>
									</tr>	
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>				
			<?php
			}
			elseif(($manageRmLotIdEdit)>0)
			{
			?>
			<table width="96%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
				<tbody>
					<tr>
						<td bgcolor="white">
							<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
										<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; Manage RM LOT ID </td>
									</tr>												
									<tr>
										<td height="10"></td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="4" align="center">
											<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">	
												<?php/* if(($receiptgatePassId)>0)
												{
												*/?>
												<tr>
													<td></td>
													<td>
														<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" 			name="tblAddProcurmentOrderSupplier">
															<tr bgcolor="#f2f2f2" align="center">
																<td class="listing-head" nowrap>RM lot ID</td>
																<td class="listing-head" nowrap>Supplier name </td>
																<td class="listing-head" nowrap>Farm Name </td>
																<td class="listing-head" nowrap>Landing Center</td>
																<td class="listing-head" nowrap>Challan no </td> 
																<td class="listing-head" nowrap>Date</td>
																<td class="listing-head" nowrap>ALLOTED TO COMPANY  </td>
																<td class="listing-head" nowrap>UNIT  </td>
																<td class="listing-head" nowrap></td>
															</tr>
															<?php
															if(sizeof($supplierDetail)>0)
															{
																$n=0;
																foreach($supplierDetail as $supplierVal)
																 {
															?>
															<tr  align="center"  bgcolor="#FFFFFF">
																<td class="listing-item" nowrap><?=$rmlotNmDet?><?/*=$supplierVal[2].$supplierVal[3];*/?>
																	<input type="hidden" id="editRmlotID_<?php echo $n;?>" name="editRmlotID_<?php echo $n;?>" value="<?php echo $editId;?>"  />
																	<input type="hidden" id="editRmlotIDName_<?php echo $n;?>" name="editRmlotIDName_<?php echo $n;?>" value="<?=$rmlotNmDet?><?/*=$supplierVal[2].$supplierVal[3];*/?>"  />
																	<input type="hidden" id="rmlotID_Detail_<?php echo $n;?>" name="rmlotID_Detail_<?php echo $n;?>" value="<?=$supplierVal[4];?>"  />
																</td>
																<td class="listing-item" nowrap><?php echo $supplierVal[9];?>
																	<input type="hidden" value="<?php echo $supplierVal[7];?>" size="15" id="supplier_id_<?php echo $n;?>" name="supplier_id_<?php echo $n;?>" required />
																</td>
																<td class="listing-item" nowrap><?php echo $supplierVal[10];?>
																	<input type="hidden" value="<?php echo $supplierVal[8];?>" size="15" id="pond_id_<?php echo $n;?>" name="pond_id_<?php echo $n;?>" required /></td>
																<td class="listing-item" nowrap><?php echo $supplierVal[17];?>
																	<input type="hidden" value="<?php echo $supplierVal[16];?>" size="15" id="landing_center_id_<?php echo $n;?>" name="landing_center_id_<?php echo $n;?>" required /></td>
																<td class="listing-item" nowrap>
																	<?php echo $supplierVal[14];?>
																	<input type="hidden" value="<?php echo $supplierVal[14];?>" size="15" id="challan_no_<?php echo $n;?>" name="challan_no_<?php echo $n;?>" required /></td>
																<td class="listing-item" nowrap>
																	<?php echo dateFormat($supplierVal[13]);?>
																	<input type="hidden" size="15" name="challan_date_<?php echo $n;?>" id="challan_date_<?php echo $n;?>" value="<?php echo$supplierVal[13];?>" required /></td>
																<td class="listing-item" nowrap>
																	<?php echo $supplierVal[11];?>
																</td>
																<td class="listing-item" nowrap>
																	<?php echo $supplierVal[12];?>
																</td>
																<td class="listing-item" nowrap>
																	<select  name="rm_lot_id_<?php echo $n;?>" id="rm_lot_id_<?php echo $n;?>">
																		<option value=""> -- Select--</option>
																			<?php
																			if(sizeof($lot) > 0)
																			{
																				foreach($lot as $lotID)
																				{	
																					$sel = '';
																					if($rm_lot_id == $lotID[0]) $sel = 'selected="selected"';
																					echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																				}
																			}
																			?>
																	</select>
																</td>
																	<input type="hidden" name="receipt_<?php echo $n;?>" value="<?php echo $supplierVal[5];?>"  id="receipt_<?php echo $n;?>"/>	
																	<input type="hidden" name="receiptGatePassId_<?php echo $n;?>" value="<?php echo $supplierVal[6];?>"  id="receiptGatePassId<?php echo $n;?>"/>
															</tr>
															<?php
																$n++;
															}
															?>
																<input type="hidden" name="manageRmSize" value="<?php echo sizeof($supplierDetail);?>"  id="manageRmSize"/>
																								
															<?php
															}
															?>
														</table>
													</td>
												</tr>
												<tr><td colspan="4" height="10px"></td></tr>
												<tr>
													<td colspan="4" align="center">
														<table align="center" >
															<tr>
																<td colspan="3" height="10px"></td>
															</tr>
															<tr>
																<td><input class="button" type="submit" onclick="return cancel('ManageRMLOTID.php');" value=" Cancel " name="cmdCancel"></td>
																<td>&nbsp;</td>
																<td><input type="submit" value="Save Changes " name="cmdSaveChange" id="cmdSaveChange" class="button"></td>
															</tr>										
														</table>
													</td>
												</tr>
												<tr>
													<td height="10px" colspan="2"></td>
												</tr>
												<tr>
													<td colspan="2" height="20px"></td>
												</tr>
											</table>
										</td>
									</tr>	
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>				
			<?php
			}
			?>
			<?php if($p['addUnitTransfer']!="")
			{
			?>	
			<table width="98%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
				<tbody>
					<tr>
						<td bgcolor="white">
							<!-- Form fields start -->
							<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
										<td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; Manage RM LOT ID </td>
									</tr>												
									<tr>
										<td height="10"></td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td colspan="4" align="center">
											<table width="80%" border="0" align="center" cellspacing="0" cellpadding="0">			
												<tr>
													<td colspan="2" >
													<?php
														$left_l=true;
														$entryHead = "";
														$rbTopWidth = "";
														require("template/rbTop.php");
													?>
														<table align="center" width="100%">
															<tr>
																<td width="80" class="fieldName">Date :</td>
																<td class="listing-item">
																	<input type="text" name="select_date" id="select_date" value="<?php echo $select_date;?>" onchange="xajax_getRMLotIDS(this.value);" />
																</td>								
																<td id="lotIDLabel" width="80" class="fieldName"> RM LOT ID</td>
																<td  class="listing-item">
																	<select id="rm_lot_id" name="rm_lot_id" onchange="xajax_getRMLotIDResult(document.getElementById('select_date').value,document.getElementById('rm_lot_id').value); ">
																		<option value=""> Select RM LOT ID </option>
																	</select>						
																</td>
															</tr>
														</table>
														<?php
															require("template/rbBottom.php");
														?>
													</td>
												</tr>
												<tr><td colspan="2" height="20px"></td></tr>
											</table>
										</td>
									</tr>
									<tr>
										<td width="1"></td>
										<!----------------------------------functionality for unit transfer---------------------------------->
										<td colspan="2" id="lotIdList">
											<table width="92%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
				}
			?>
		</td>
	</tr>
	<tr>
		<td height="20" align="center" class="err1"> </td>
	</tr>
	<tr>
		<td>
			<table width="75%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
				<tbody>							
					<tr>
						<td>
							<table width="100%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
								<tbody>
									<tr>
										<td bgcolor="white">
											<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" bgcolor="white">
												<tbody>
													<tr>
														<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
														<td nowrap=""  background="images/heading_bg.gif" class="pageName">&nbsp;RM LOT ID  </td>
													</tr>
													<tr><td>&nbsp;</td></tr>
													<tr align="center">
														<td  align="center" nowrap="nowrap" colspan="2"  >
															<?php
																$entryHead = "Data Search";
																$rbTopWidth = "95%";
																require("template/rbTop.php");
															?>
															<table cellpadding="0" cellspacing="0" >
																<tr>
																	<td nowrap="nowrap">
																		<table cellpadding="0" cellspacing="0" >
																			<tr>
																				<td class="fieldName1"> From:</td>
																				<td nowrap="nowrap"> 
																				<? 
																					if ($dateFrom=="") $dateFrom=date("d/m/Y");
																				?>
																					<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
																				<td class="listing-item">&nbsp;</td>
																				<td class="fieldName1"> Company:</td>
																				<td nowrap="nowrap"> 
																					<select id="CompanyName" name="CompanyName" onchange=""/>
																						<option value="">--select--</option>
																						<?php
																							if(sizeof($companyNames) > 0)
																							{
																								foreach($companyNames as $compId=>$compName)
																								{
																									$companyId=$compId;
																									$companyName=$compName;
																									$sel = '';
																									if(($companyNm == $companyId) || ($companyNm=="") && ($companyId==$defaultCompany)) $sel = 'selected';
																									echo '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																								}
																							}
																						?>
																					</select>		
																				</td>
																				<td class="listing-item">&nbsp;</td>
																				<td class="fieldName1">Processing Stage:</td>
																				<td>
																				<?php 
																					if ($processStage!="" && $processStage=="RM") $selectRM="selected";
																					if ($processStage!="" && $processStage=="GRADED") $selectGRADED="selected";
																					if ($processStage!="" && $processStage=="PRE-PROCESSED") $selectPREPROCESSED="selected";
																					if ($processStage!="" && $processStage=="SOAKED") $selectSOAKED="selected";
																					if ($processStage!="" && $processStage=="FROZEN") $selectFROZEN="selected";
																				?>
																				<select name="processingStage" id="processingStage">
																						<option value="">--Select--</option>
																						<option value="RM" <?=$selectRM?>>RM</option>
																						<option value="GRADED" <?=$selectGRADED?>>GRADED</option>
																						<option value="PRE-PROCESSED" <?=$selectPREPROCESSED?>>PRE-PROCESSED</option>
																						<option value="SOAKED" <?=$selectSOAKED?>>SOAKED</option>
																						<option value="FROZEN" <?=$selectFROZEN?>>FROZEN</option>
																						
																				</select>
																			</td>
																		</tr>
																					<!--<tr>
																					<td class="listing-item" colspan='2' height='5'>&nbsp;</td>
																					
																					</tr>-->
																		<tr>
																			<td class="fieldName1"> Till:</td>
																			<td>
																			<? 
																			   if($dateTill=="") $dateTill=date("d/m/Y");
																			 ?>
																			 <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
																			 <td class="listing-item">&nbsp;</td>
																			 <td class="fieldName1"> Unit</td>
																			<td nowrap="nowrap">
																				<select id="unitName" name="unitName" >
																					<option value="">--select--</option>
																					<?php
																					($companyNm!="")?$units=$unitRecords[$companyNm]:$units=$unitRecords[$defaultCompany];
																					//$units          = $unitRecords[$companyNm];
																					//printr($units);
																					if(sizeof($units) > 0)
																					{
																						foreach($units as $untId=>$untNm)
																						{
																							$unitId=$untId;
																							$unitName=$untNm;
																							$sel = '';
																							if($unitNm == $unitId) $sel = 'selected';
																							echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																						}
																					}
																					?>			
																				</select>	
																			</td>
																		</tr>
																	</table>
																</td>
																<td width="3%">&nbsp;</td>
																<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
															</tr>
														</table>
														<?php
															require("template/rbBottom.php");
														?>
													</td>
												</tr>
												<tr>
													<td height="10" colspan="3"></td>
												</tr>
												<tr>
													<td height="5" colspan="3"></td>
												</tr>
												<?php if(!$receiptgatePassId)
												{
												?>
												<tr>
													<td height="5" colspan="3" align="center">
														<input type="submit" id='addUnitTransfer' name="addUnitTransfer" class="button" value="Unit Transfer">&nbsp;
														<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageRmLotAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&company=<?=$companyNm?>&unit=<?=$unitNm?>&processingStage=<?=$processStage?>',700,600);"/><? } ?>
													</td>
												</tr>
												<tr>
													<td height="10" colspan="3" align="center">
																
													</td>
												</tr>
												<?php
												}
												?>
												<tr>
													<td width="1" ></td>
													<td colspan="2" style="padding-left:10px; padding-right:10px;"   >
														<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="999999">
														<?
															if ( sizeof($manageRmLotIdRec) > 0 ) {
																$i	=	0;
														?>
															<thead>
																<? if($maxpage>1){?>
																<tr bgcolor="#f2f2f2">
																	<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
																		<div align="right">
																		<?php
																		$nav  = '';
																		for ($page=1; $page<=$maxpage; $page++) {
																			if ($page==$pageNo) {
																					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																			} else {
																					$nav.= " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																				//echo $nav;
																			}
																		}
																		if ($pageNo > 1) {
																			$page  = $pageNo - 1;
																			$prev  = " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
																		} else {
																			$prev  = '&nbsp;'; // we're on page one, don't print previous link
																			$first = '&nbsp;'; // nor the first page link
																		}

																		if ($pageNo < $maxpage) {
																			$page = $pageNo + 1;
																			$next = " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
																<tr align="center" bgcolor="#f2f2f2">
																	<!--<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>-->
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>RM Lot ID</th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Company Name</th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</th>
																	
																	
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;">Current Processing Stage </th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;">History </th>
																	<? if($confirm==true ){?>
																				<td class="listing-head">&nbsp;</td>
																	<? }?>
																	<? if($edit==true){?>
																	<!--<td class="listing-head"></td>-->
																	<th class="listing-head"></th>
																	<? }?>
																	<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Procurement Number</th>-->
																	<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Harvesting Equipment </th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Equipment Quantity </th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Harvesting Chemical </th>
																	<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Chemical Quantity</th>-->
																</tr>
															</thead>
															<tbody>
															<?
															foreach($manageRmLotIdRec as $cr) {
																$i++;
																 $rmmainId		=	$cr[0];
																 $rmExist = $objManageRMLOTID->lotIdExist($rmmainId);
																 $companyname		=	$cr[8];
																 $unit		=	$cr[9];
																 $rmlotidnum		=	$cr[5];
																 $alpha		=	$cr[6];
																 $processingStage= $objManageRMLOTID->getRMProgressStage($rmmainId);
																
																 //$processingStage		=	$cr[7];
																 $originId		=	$cr[10];
																 $active=$cr[11];
															?>
																<tr bgcolor="white"  bgcolor="#afddf8"  
																	<?php /*<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rmmainId;?>" ></td>*/?> >
																	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$alpha.$rmlotidnum;?></td>
																	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$companyname;?></td>
																	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
																	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
																		<?php
																			echo $processingStage;
																		/*if($processingStage==0)
																		{
																		echo "Raw Material";
																		}*/
																		?>
																		</td>
																		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
																		<?php
																		if($originId==0)
																		{
																			echo "No";
																		}
																		else
																		{
																		?>
																			
																		<?php
																		/*$detailsvalue='';
																		$detailsvalue='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head>RM lot ID</th>
																								<th  class=listing-head>Company Name</th>
																								<th  class=listing-head>Unit</th></tr>';
																		$detailsvalue.='</table>';*/
																		?>
																		<?php
																		 $detailsvalue="";
																		$displayRMLotIdHistory=$objManageRMLOTID->getLotIdTotalvalueHistory($originId);
																		//echo sizeof($displayRMLotIdHistory);
																		if(sizeof($displayRMLotIdHistory)>0) {
																		$detailsvalue.='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head>RM lot ID</th><th  class=listing-head>Company Name</th><th  class=listing-head>Unit</th></tr>';
																		 foreach($displayRMLotIdHistory as $displayRMLotId )
																		 {
																			foreach($displayRMLotId as $drm)
																			 {
																				$historyrmlotid= $drm[0];	
																				$historyrmlot= $drm[1].$drm[2];
																				$historyunit= $drm[11];
																				$historycompany=$drm[12];
																				
																				$detailsvalue.='<tr bgcolor=#f2f2f2><td class=listing-item>'.$historyrmlot.'&nbsp;</td><td class=listing-item nowrap>'.$historycompany.'&nbsp;</td><td class=listing-item nowrap>'.$historyunit.'&nbsp;</td></tr>';
																			 }
																		 } 
																		
																				
																		$detailsvalue.="</table>";
																		
																		}
																		?>
																			<a onMouseOver="ShowTip('<?=$detailsvalue?>');" onMouseOut="UnTip();">yes</a>
																		<?php
																		 
																			}
																		?>
																	</td>
																	<td <?php if ($active==1) {?> class="listing-item" <?php }else {?>  <?php }?> width="45" align="center" >
																		<?php 
																		 if ($confirm==true && sizeof($rmExist)>0){	
																		if ($active=="0"){ ?>
																		<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$rmmainId;?>,'confirmId');" >
																		<?php } else if ($active==1){  if($processingStage=='RM' && sizeof($rmExist)>0) { ?>
																		<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$rmmainId;?>,'confirmId');" >
																		<?php } } 
																		}?>
																	</td>
																	<? 
																	if($edit==true ){
																	?>
																	<td class="listing-item" width="60" align="center">
																		<? if($active!='1') { ?>
																		<?php if($processingStage=='RM' && sizeof($rmExist)>0) { ?>
																			<input type="submit" value="Edit" name="cmdEdit" onClick="assignValue(this.form,<?=$rmmainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='ManageRMLOTID.php';">
																		<?php } } ?>
																	</td>
																	<? } ?>
																</tr>
																<?
																	}
																?>
																<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
																<input type="hidden" name="editId" value="">
																<input type="hidden" name="confirmId" value="">
																<? if($maxpage>1){?>
																<tr bgcolor="#f2f2f2">
																	<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
																		<div align="right">
																		<?php
																		 $nav  = '';
																		for ($page=1; $page<=$maxpage; $page++) {
																			if ($page==$pageNo) {
																					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																			} else {
																					$nav.= " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																				//echo $nav;
																			}
																		}
																		if ($pageNo > 1) {
																			$page  = $pageNo - 1;
																			$prev  = " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
																		} else {
																			$prev  = '&nbsp;'; // we're on page one, don't print previous link
																			$first = '&nbsp;'; // nor the first page link
																		}

																		if ($pageNo < $maxpage) {
																			$page = $pageNo + 1;
																			$next = " <a href=\"ManageRMLOTID.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
																<?
																	} else {
																?>
																<tr bgcolor="white">
																	<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																</tr>	
																<?
																	}
																?>
															</tbody>
														</table>	
														</td>
														</tr>
														<tr>
															<td height="5" colspan="3"></td>
														</tr>
														<tr>
															<td height="5" colspan="3"></td>
														</tr>															
														</tbody>
													</table>		
												</td>
											</tr>
										</tbody>
									</table>
								<!-- Form fields end   -->			
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "select_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "select_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	
	
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
	
	
	</SCRIPT>
<?php 
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>