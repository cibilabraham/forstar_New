<?
	require("include/include.php");
	require_once('lib/PhtMonitoring_ajax.php');
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


	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId, $urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;

	
	$requestNo		= $p["requestNo"];
	$selDepartment		= $p["selDepartment"];

	if($g["inputType"]!="")
	{
		$inputData= $g["inputData"];
		$addMode	=	true;
	}



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
	if ($p["cmdAdd"]!="" ) 
	{
		//die();
		$phtCertificate=$p["phtCertificate"];
		$rmlotidCertify=$p["rmlotidCertify"];
		$selectDate=$p["select_date"];
		$rmLotId=$p["rm_lot_id"];
		if($phtCertificate!='' && $rmlotidCertify!='')
		{	//echo "hii";
			$phtMonitoringId=$phtMonitorngObj->getMonitoringId($phtCertificate);
			//echo $phtMonitoringId;
			//die();
			$rowCnt=$p["rowCnt"];
			for($i=0; $i<$rowCnt; $i++)
			{
				$weightmentId=$p["weightmentId_".$i];
				$phtMonitoringIdEntry=$p["hidePhtMonitoringId_".$i];
				if($weightmentId!='' && $phtMonitoringIdEntry=="")
				{
					$certifyQty=$p["hidePhtCertifyQty_".$i];
					$setoffQuantity=$p["hideAdjustedQty_".$i];
					$balanceQty=$certifyQty-$setoffQuantity;
					$hideSupplyQty=$p["hideSupplyQty_".$i];
					$adjustedQty=$p["hideAdjustedQty_".$i];
					$supplyBalanceQty=$p["hideBalanceQty_".$i];
					
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId,$phtCertificate,$certifyQty,$setoffQuantity,$balanceQty,$weightmentId,$rmlotidCertify,$hideSupplyQty,$adjustedQty,$supplyBalanceQty);

					if($supplyBalanceQty!=0)
					{
						
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity("","",$supplyBalanceQty,"","",$weightmentId,$rmlotidCertify,$supplyBalanceQty,"",$supplyBalanceQty);
					}
					//echo $weightmentId;
				}
				else if($weightmentId!='' && $phtMonitoringIdEntry!="")
				{
					$certifyQty=$p["hidePhtCertifyQty_".$i];
					$setoffQuantity=$p["hideAdjustedQty_".$i];
					$balanceQty=$certifyQty-$setoffQuantity;
					$hideSupplyQty=$p["hideSupplyQty_".$i];
					$adjustedQty=$p["hideAdjustedQty_".$i];
					$supplyBalanceQty=$p["hideBalanceQty_".$i];
					
					$phtMonitoringIns=$phtMonitorngObj->updatePhtCertificateQuantity($phtMonitoringId,$phtCertificate,$certifyQty,$setoffQuantity,$balanceQty,$weightmentId,$rmlotidCertify,$hideSupplyQty,$adjustedQty,$supplyBalanceQty,$phtMonitoringIdEntry);

					if($supplyBalanceQty!=0)
					{
						$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity("","",$supplyBalanceQty,"","",$weightmentId,$rmlotidCertify,$supplyBalanceQty,"",$supplyBalanceQty);
					}
					//echo $weightmentId;
				}
			}
			
		}
		else if($selectDate!='' && $rmLotId!='')
		{  //echo "ppp";
			$supplierRowCnt=$p["supplierRowCnt"];
			for($j=0; $j<$supplierRowCnt; $j++)
			{
				$certifyId=$p["certificateNo_".$j];
				$phtMonitoringEntryId=$p["phtMonitoringEntryId_".$j];
				if($certifyId!='' && $phtMonitoringEntryId=="")
				{
					$phtMonitoringId=$phtMonitorngObj->getMonitoringId($certifyId);
					$availableQty=$p["availableQtySupplier_".$j];
					$adjustedQtySupplier=$p["adjustedQtySupplier_".$j];
					$balanceQtySupplier=$p["balanceQtySupplier_".$j];
					$weightmentSupplier=$p["weightmentSupplier_".$j];
					$supplyQtyVal=$p["supplyQtyVal_".$j];
					//$supplyQtyVal=$p["supplyQtyVal_".$j];
					$supplyBalanceQty=$supplyQtyVal-$adjustedQtySupplier;
					
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId,$certifyId,$availableQty,$adjustedQtySupplier,$balanceQtySupplier,$weightmentSupplier,$rmLotId,$supplyQtyVal,$adjustedQtySupplier,$supplyBalanceQty);

					if($supplyBalanceQty!=0)
					{
						
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity("","",$supplyBalanceQty,"","",$weightmentSupplier,$rmLotId,$supplyBalanceQty,"",$supplyBalanceQty);
					}

					$delphtTemp=$phtMonitorngObj->deleteTemporaryDetail($weightmentSupplier,$certifyId, $availableQty,$adjustedQtySupplier,$balanceQtySupplier,$userId);
				
				}
				else if($certifyId!='' && $phtMonitoringEntryId!="")
				{
					$phtMonitoringId=$phtMonitorngObj->getMonitoringId($certifyId);
					$availableQty=$p["availableQtySupplier_".$j];
					$adjustedQtySupplier=$p["adjustedQtySupplier_".$j];
					$balanceQtySupplier=$p["balanceQtySupplier_".$j];
					$weightmentSupplier=$p["weightmentSupplier_".$j];
					$supplyQtyVal=$p["supplyQtyVal_".$j];
					//$supplyQtyVal=$p["supplyQtyVal_".$j];
					$supplyBalanceQty=$supplyQtyVal-$adjustedQtySupplier;
					
					$phtMonitoringIns=$phtMonitorngObj->updatePhtCertificateQuantity($phtMonitoringId,$certifyId,$availableQty,$adjustedQtySupplier,$balanceQtySupplier,$weightmentSupplier,$rmLotId,$supplyQtyVal,$adjustedQtySupplier,$supplyBalanceQty,$phtMonitoringEntryId);

					//$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId,$certifyId,$availableQty,$adjustedQtySupplier,$balanceQtySupplier,$weightmentSupplier,$rmLotId,$supplyQtyVal,$adjustedQtySupplier,$supplyBalanceQty);

					if($supplyBalanceQty!=0)
					{
						
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity("","",$supplyBalanceQty,"","",$weightmentSupplier,$rmLotId,$supplyBalanceQty,"",$supplyBalanceQty);
					}

					$delphtTemp=$phtMonitorngObj->deleteTemporaryDetail($weightmentSupplier,$certifyId, $availableQty,$adjustedQtySupplier,$balanceQtySupplier,$userId);
					
				}


			}
		}
		//die();

		if ($phtMonitoringIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddPHTMonitoring);
				$sessObj->createSession("nextPage",$url_afterAddPHTMonitoring.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPHTMonitoring;
			}
			$phtMonitoringIns		=	false;



		
	/*	$date		=	mysqlDateFormat($p["date"]);
		$rmLotId		=	$p["rmLotId"];
		$supplier		=	($p["supplier"]);
		$supplierGroupName		=	$p["supplierGroupName"];
		$specious		=	($p["specious"]);
		$supplyQty		=	$p["supplyQty"];
		$hidTableRowCount		=	$p["hidTableRowCount"];
		// $phtCertificateNo		=	$p["phtCertificateNo"];
		// $phtQty		=	$p["phtQty"];
		// $setOfQty		=	($p["setOfQty"]);
		// $balance		=	$p["balance"];
		
		
		
		if ($date!="" ) {	
			//$phtMonitoringIns	=	$phtMonitorngObj->addPHTMonitoring($date, $rmLotId, $supplier, $supplierGroupName, $specious,$supplyQty, $phtCertificateNo, $phtQty, $setOfQty, $balance, $userId);
			$phtMonitoringIns	=	$phtMonitorngObj->addPHTMonitoring($date, $rmLotId, $supplier, $supplierGroupName, $specious,$supplyQty,$userId);
			if($phtMonitoringIns)					
				$lastId = $databaseConnect->getLastInsertedId();
				
				if ($hidTableRowCount>0 ) {
				//echo "hii";
				//echo $hidTableRowCount;
					for ($k=0; $k<$hidTableRowCount; $k++) {
					//echo "aa";
						$status = $p["status_".$k];
						  if ($status!='N') {
						
						$phtCertificateNo		=	$p["phtCertificateNo_".$k];
						
						$phtQuantity		=	$p["phtQuantity_".$k];
						$setoffQuantity		=	$p["setoffQuantity_".$k];
						$balanceQuantity		=	$p["balanceQuantity_".$k];
						//if ($lastId!="" ) {
						if ($lastId!="" && $phtCertificateNo!="" && $phtQuantity!="" && $setoffQuantity!="" && $balanceQuantity!="" ) {
						
							$phtMonitoringIns	=	$phtMonitorngObj->addPhtCertificateQuantity($lastId, $phtCertificateNo, $phtQuantity,$setoffQuantity,$balanceQuantity);
							$phtBalance=$phtMonitorngObj->updatePhtCertificate($phtCertificateNo,$balanceQuantity);
						}
					}
				  }
			  }	
			
//die();
			if ($phtMonitoringIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddPHTMonitoring);
				$sessObj->createSession("nextPage",$url_afterAddPHTMonitoring.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPHTMonitoring;
			}
			$phtMonitoringIns		=	false;
		}
		*/
	}
	

	# Edit
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$inputData="Certificate";
		$phtMonitoringDataRec	=	$phtMonitorngObj->find($editId);		
		$phtMonitoringId	=	$phtMonitoringDataRec[0];
		$certificate=$phtMonitoringDataRec[7];
		
		$phtCertificate=$phtMonitorngObj->certificateNo($certificate);
		$certificateQnty= $phtMonitorngObj->getCertificate($certificate);
		$res=$phtMonitorngObj->getPhtQuantityDetail($phtMonitoringId);
		
		/*$date		=	dateFormat($phtMonitoringDataRec[1]);		
		$rmLotId	=	$phtMonitoringDataRec[2];
		$supplier	=	$phtMonitoringDataRec[3];
		$supplierGroupName	=	$phtMonitoringDataRec[4];
		$specious	=	$phtMonitoringDataRec[5];
		$supplyQty	=	$phtMonitoringDataRec[6];
		$certificate	=	$phtMonitoringDataRec[7];
		$phtDT=mysqlDateFormat($date);
		//$rmLotRecords	= $phtMonitorngObj->getLots($phtDT,$rmLotId);
		//printr($rmLotRecords);
	
		// $phtCertificateNo	=	$phtMonitoringDataRec[7];
		// $phtQty	=	$phtMonitoringDataRec[8];
		// $setOfQty	=	$phtMonitoringDataRec[9];
		// $balance	=	$phtMonitoringDataRec[10];
		//$phtCertificateRecords	= $phtMonitorngObj->getPhtNumber($editId);
		$phtQuantityRec = $phtMonitorngObj->getCertificateQuantity($editId);

		$phtCertificateRecords=$phtMonitorngObj->getPhtCertificate($rmLotId);
		//printr($phtCertificateRecords);*/
	}

	#Update 
	if ($p["cmdSaveChange"]!="")
		{		
		$phtMonitoringId	=	$p["hidphtMonitoringId"];
		$cerificateqtyOriginal=$p["cerificateqtyOriginal"];
		$phtCertificate=$p["hidphtCertificateId"];
		$certificateCnt=$p["certificateCnt"];
		for($i=0; $i<$certificateCnt; $i++)
		{
			$phtMonitoringIdEntry=$p["editPhtMonitoring_".$i];
			$weightmentId=$p["weightmentId_".$i];
			$rmlotidCertify=$p["rmlotidCerify_".$i];
			$dStatus=$p["dStatus_".$i];
			if($dStatus!='N')
			{
				$hideAdjustedQty=$p["hideAdjustedQty_".$i];	
				$setoffQuantity=$p["adjustedQty_".$i];
				$balanceQty=$p["hideCertificateBalanceQty_".$i];
				$certifyQty=$setoffQuantity+$balanceQty;
				
				$supplyQty=$p["hidesupplyQnt_".$i];
				$adjustedQty=$p["adjustedQty_".$i];
				if($hideAdjustedQty>$adjustedQty)
				{
					$hideSupplyQty=$adjustedQty;
				}
				else
				{
					$hideSupplyQty=$supplyQty;
				}
				$supplyBalanceQty=$p["balanceQty_".$i];
				$phtMonitoringIns=$phtMonitorngObj->updatePhtCertificateQuantity($phtMonitoringId,$phtCertificate,$certifyQty,$setoffQuantity,$balanceQty,$weightmentId,$rmlotidCertify,$hideSupplyQty,$adjustedQty,$supplyBalanceQty,$phtMonitoringIdEntry);
				if($supplyBalanceQty!=0)
				{
					$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity("","",$supplyBalanceQty,"","",$weightmentId,$rmlotidCertify,$supplyBalanceQty,"",$supplyBalanceQty);
				}
			}
			else if($dStatus=='N')
			{	$hideSupplyQty=$p["hidesupplyQnt_".$i];
				$phtMonitoringIns=$phtMonitorngObj->updatePhtCertificateQuantity("","",$hideSupplyQty,"","",$weightmentId,$rmlotidCertify,$hideSupplyQty,"",$hideSupplyQty,$phtMonitoringIdEntry);
			}

		}




		/*for($i=0; $i<$certificateCnt; $i++)
		{
			$dStatus=$p["dStatus_".$i];
			if($dStatus=='N')
			{
				$phtMonitoringQty=$p["editPhtMonitoring_".$i];
				$removeStatus=1;
				//$certificate=$phtMonitorngObj->deletePhtMonitoringQuantity($phtMonitoringQty);
			}
		}
		if($removeStatus==1)
		{	$newCertificateQty="";
			//$certificate=$phtMonitorngObj->deletePhtMonitoring($phtMonitoringId);
			for($j=0; $j<$certificateCnt; $j++)
			{
				$dStatus=$p["dStatus_".$j];
				$weightmentId=$p["weightmentId_".$j];
				$rmlotidCertify=$p["rmlotidCerify_".$j];
				if($dStatus!='N' && $weightmentId!='')
				{	
					if($newCertificateQty=="")
					{
						$newCertificateQty=$cerificateqtyOriginal;
						$adjustedQty=$p["hideAdjustedQty_".$j];
						$balanceQty=$newCertificateQty-$adjustedQty;
						$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId,$phtCertificate,$newCertificateQty,$adjustedQty,$balanceQty,$weightmentId,$rmlotidCertify);
						//echo $weightmentId;
					}
					else if($newCertificateQty!="")
					{
						$newCertificateQty=$balanceQty;
						$adjustedQty=$p["hideAdjustedQty_".$j];
						$balanceQty=$newCertificateQty-$adjustedQty;
						$phtMonitoringIns=$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId,$phtCertificate,$newCertificateQty,$adjustedQty,$balanceQty,$weightmentId,$rmlotidCertify);
					}
				}
			}

		}*/
		//echo $certificateCnt; 
		/*$date		=	mysqlDateFormat($p["date"]);
		$rmLotId		=	$p["rmLotId"];
		$supplier		=	($p["supplier"]);
		$supplierGroupName	=	$p["supplierGroupName"];
		$specious		=	$p["specious"];
		$supplyQty		=	$p["supplyQty"];
		// $phtCertificateNo		=	$p["phtCertificateNo"];
		// $phtQty		=	$p["phtQty"];
		// $setOfQty		=	($p["setOfQty"]);
		// $balance		=	$p["balance"];
				
		$hidTableRowCount		=	$p["hidTableRowCount"];
		
		if($phtMonitoringId!="" && $date!="" && $rmLotId!="" && $supplier!="" && $supplierGroupName!="" && $specious!="" && $supplyQty!="" ) 
		{	
		//echo "hii";
			//$phtMonitoringRecUptd	=	$phtMonitorngObj->updatePhtMonitoring($phtMonitoringId, $date,$rmLotId, $supplier,$supplierGroupName,$specious,$supplyQty,$phtCertificateNo,$phtQty,$setOfQty,$balance);
		
			$phtMonitoringRecUptd	=	$phtMonitorngObj->updatePhtMonitoring($phtMonitoringId, $date,$rmLotId, $supplier,$supplierGroupName,$specious,$supplyQty);
							
		}
		//die();
				for ($e=0; $e<$hidTableRowCount; $e++) {
			   $status = $p["status_".$e];
			   $rmId  		= $p["rmId_".$e];
			   //echo $rmId  		= $p["IsFromDB_".$e];
			  // die;
			   if ($status!='N') {
				
				$phtCertificateNo		=	$p["phtCertificateNo_".$e];
				$phtQuantity		=	$p["phtQuantity_".$e];
				$setoffQuantity		=	$p["setoffQuantity_".$e];
				$balanceQuantity		=	$p["balanceQuantity_".$e];
				//$chemicalQty	=	$p["chemicalQty_".$i];
				//$chemicalIssued	=	$p["chemicalIssued_".$i];
					
					if ($phtMonitoringId!="" && $phtCertificateNo!="" && $phtQuantity!="" && $setoffQuantity!="" && $balanceQuantity!=""  && $rmId!="" ) {
						
							$phtMonitoringIns	=	$phtMonitorngObj->updatePhtCertificateQuantity($rmId, $phtCertificateNo, $phtQuantity,$setoffQuantity,$balanceQuantity);
							
						}
					
					
					else if ($phtMonitoringId!="" && $phtCertificateNo!="" && $phtQuantity!="" && $setoffQuantity!="" && $balanceQuantity!=""  && $rmId=="" ) {
						
							$phtMonitoringIns	=	$phtMonitorngObj->addPhtCertificateQuantity($phtMonitoringId, $phtCertificateNo, $phtQuantity,$setoffQuantity,$balanceQuantity);
							
						}
					
					
				
					//die;
				} // Status Checking End

				if ($status=='N' && $rmId!="") {
					# Check Test master In use
					//$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					//if (!$testMethodInUse)$phtMonitoringRec = $phtMonitorngObj->delPhtMonitorngQuantityRec($rmId);
						
				}
			}

			*/

		//die();
		if ($phtMonitoringIns) {
			$sessObj->createSession("displayMsg",$msg_succPHTMonitoringUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePHTMonitoring.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPHTMonitoringUpdate;
		}
		$phtMonitoringRecUptd	=	false;		
	}
	
	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$phtMonitoringId	=	$p["delId_".$i];

			if ($phtMonitoringId!="" && $isAdmin!="") {

				$phtMonitoringRecDel =	$phtMonitorngObj->deletePhtMonitorng($phtMonitoringId);	
				$phtMonitoringRecDel =	$phtMonitorngObj->deletePhtMonitorngQuantity($phtMonitoringId);	
			}
		}
		if ($phtMonitoringRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPHTMonitoring);
			$sessObj->createSession("nextPage",$url_afterDelPHTMonitoring.$selection);
		} else {
			$errDel	=	$msg_failDelPHTMonitoring;
		}
		$phtMonitoringRecDel	=	false;
		
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
	
	#List all Rm Test Data
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$phtMonitoringRecords	= $phtMonitorngObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$phtMonitoringDataSize	= sizeof($phtMonitoringRecords);
		$fetchAllphtMonitoringDataRecs = $phtMonitorngObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllphtMonitoringDataRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	if($addMode)
	{
		# List all Stocks
		//$stockRecords		= $stockObj->fetchAllActiveRecords();
		//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
		
		# List all Supplier
		//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
		
		# List all records
		//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
		$unitRecords	= $plantandunitObj->fetchAllRecordsPlantsActive();
		//$rmLotRecords	= $phtMonitorngObj->fetchAllRecordsRMLotIdVal();
		$rmTestNameRecords	= $rmTestMasterObj->fetchAllRecordsActive();
		//$phtCertificateRecords	= $phtCertificateObj->fetchAllRecords();
		$supplierRecords	= $supplierMasterObj->fetchAllRMSupplierActive();
		$supplierGroupRecords	= $supplierGroupObj->fetchAllRecords();
		$phtCertificate=$phtMonitorngObj->getPhtCerificate();
	}
	
	if ($editMode) $heading	=	$label_editPHTMonitoring;
	else $heading	=	$label_addPHTMonitoring;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/PHTMonitoring.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

	<!--<link rel="stylesheet" href="libjs/jquery-ui.css">-->
	<script src="libjs/jquery/jquery-1.10.2.js"></script>
	<script src="libjs/jquery/jquery-ui.js"></script>
	<script src="libjs/json2.js"></script>

	<form name="frmPhtMonitoring" id='frmPhtMonitoring' action="PhtMonitoring.php" method="post">
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
											<tr><td align='center' class="listing-item" colspan="4" id='refreshMsgRow' style='color:red'></td></tr>
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhtMonitoring.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validatePhtMonitoring(document.frmPhtMonitoring);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit"  name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhtMonitoring.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validatePhtMonitoring(document.frmPhtMonitoring);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidphtMonitoringId" value="<?=$phtMonitoringId;?>">
											<input type='hidden' name='inputStat' id='inputStat' value='<?=$inputData?>'/>
											<tr>
											  <td colspan="2" nowrap class="fieldName" height="20px" >
										</td>
										</tr>
										<?php if($inputData=="Certificate") { ?>

										<tr>
											<td>
												<table width="200" align="center">
												<tr>
													<td colspan="2"  align="center">
													<?php							
														$entryHead = "";
														$rbTopWidth = "50%";
														require("template/rbTop.php");
													?>
														<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
															<tr><td class="fieldName" nowrap>Pht Certificate:&nbsp;</td>
																<td>
																<select name="phtCertificate" id="phtCertificate" onchange="xajax_getRMLotIdDate(document.getElementById('phtCertificate').value);">
																<option value="">--select--</option>
     																<?php 
																	foreach($phtCertificate as $pht)
																	{
																		$certificateId		=	$pht[0];
																		$certificateName	=	stripSlash($pht[1]);
																		$selected="";
																		if($certificate==$certificateId ) echo $selected="Selected";
																	?>
																	<option value="<?=$certificateId?>" <?=$selected?>><?=$certificateName?></option>
																	<? 
																	}
																	?>
																</select>
																</td>
																<? if($addMode){?><td>&nbsp;</td>
																<td class="fieldName" nowrap>RM Lot ID:&nbsp;</td>
																<td>
																<select name="rmlotidCertify" id="rmlotidCertify" onchange="xajax_getWeightmentData(document.getElementById('rmlotidCertify').value,document.getElementById('phtCertificate').value);">
																<option value="">--select--</option>
   																
																</select>
																</td>
																<?php }
																?>
															</tr>
														</table>
															<?php
																require("template/rbBottom.php");
															?>
														</td>
													</tr>
													<tr><td id="lotIdCertificate"></td></tr>
													<tr><td id="selectedlotId"></td></tr>
												</table>
											</td>
										</tr>
										<?php
											}
											else if($inputData=="Supplier")
											{ 
										?>
										<tr>
											<td>
												<table width="200" align="center">
												<tr>
													<td colspan="2"  align="center">
													<?php							
														$entryHead = "";
														$rbTopWidth = "50%";	
														require("template/rbTop.php");
													?>
														<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
															<tr>
																<td class="fieldName" nowrap>*Date:&nbsp;</td>
																<td><input type="text" name="select_date" id="select_date" size="9" value="<?=$date;?>" onchange="xajax_getRMLotIDS(this.value);" autocomplete="off" /></td>
																<td>&nbsp;</td>
																<td class="fieldName" nowrap>*RM Lot ID:&nbsp;</td>
																<td>
																	<select id="rm_lot_id" name="rm_lot_id" onchange="xajax_getRMLotIDResult(document.getElementById('select_date').value,document.getElementById('rm_lot_id').value); ">
																			<option value=""> Select RM LOT ID </option>
																		</select>
																	<!--<input type="text" name="rmlotid" id="rmlotid" size="9" value="<?=$date;?>" onchange="xajax_getlots(document.getElementById('date').value,'');" autocomplete="off" />-->
																</td>
																<td>&nbsp;</td>

															</tr>
														</table>
															<?php
																require("template/rbBottom.php");
															?>
														</td>
													</tr>

													<tr ><td id="lotIdDetail"></td></tr>
												</table>
											</td>
										</tr>
											<?php
											}
											?>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
					<? if($editMode){?>
					<tr>
					  <td colspan="2">
						<table>
						<tr>
							<td>
							<table cellpadding='4' cellspacing='1' bgcolor='#999999' align='center' id='tblAddcerificateDetail'>
								<tr align='center'>
									<th valign='center' bgcolor='#ffffff' colspan='5' class='listing-head'>
									<div style='height:100%; float: left; vertical-align:middle;'><img width='11' height='15' border='0' src='images/topLink.jpg'></div><div style='float: left; vertical-align:middle;'>Weightment Data sheet Details</div>
									</th>
								</tr>
								<tr bgcolor='#ffffff'>
									<td colspan='5' >
										<table align='center' width='90%' bgcolor='#999999' cellpadding='3' cellspacing='1'>
											<tr bgcolor='#f2f2f2'>
												<td class='listing-head' nowrap>Certificate No</td>
												<td class='listing-head' nowrap>Species</td>
												<td class='listing-head' nowrap>Certificate Qty</td>
												<td class='listing-head' nowrap>Adjusted Qty </td>
												<td  class='listing-head' nowrap>Balance Qty </td>
											</tr>
											<tr bgcolor='#e8edff'>
												<td class='listing-item' nowrap><?=$certificateQnty[0]?><input type='hidden' name='hidphtCertificateId' id='hidphtCertificateId' value='<?=$certificate?>'/></td>
												<td class='listing-item' nowrap><?=$certificateQnty[1]?></td>
												<td class='listing-item' nowrap><?=$certificateQnty[2]?><input type='hidden' name='cerificateqtyOriginal' id='cerificateqtyOriginal' value='<?=$certificateQnty[2]?>'/></td>
												<td class='listing-item' nowrap id='adjustedQty'><?=$certificateQnty[3]?></td>
												<td  class='listing-item' nowrap id='balanceQty'><?=$certificateQnty[4]?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr bgcolor='#ffffff'>
									<td colspan='5' >
										<table align='center' width='90%' bgcolor='#999999' cellpadding='3' cellspacing='1'>
											<tr bgcolor='#f2f2f2'>
												<td nowrap class='listing-head'>RM Lot ID</td>
												<td class='listing-head' nowrap>Supplier Name</td>
												<td class='listing-head' nowrap>Pond Name</td>
												<td class='listing-head' nowrap>Process Code</td>
												<td class='listing-head' nowrap>GRADE/COUNT</td>
												<td class='listing-head' nowrap>Supply Qty</td>
												<td class='listing-head' nowrap>Adjusted Qty </td>
												<td class='listing-head' nowrap>Bal Qty</td>
												<td>&nbsp;</td>
											</tr>
										<?php
										$i=0; $j=0;
										foreach($res as $weighment)
										{	//$adjustQty='0';
											$phtQntyId=$weighment[0];
											$rmlot=$weighment[1];
											$supplier=$weighment[2];
											$pond=$weighment[3];
											$processCode=$weighment[4];
											$count=$weighment[5];
											$supplyQnt=$weighment[6];
											$adjustQnty=$weighment[8];
											$cerificateBalanceQnty=$weighment[9];
											//$rmlot=$weighment[8];
											if($supplyQnt>=$adjustQnty)
											{
												$balanceQty=$supplyQnt-$adjustQnty;
											}
											else if($adjustQnty>$supplyQnt)
											{
												$balanceQty=$adjustQnty-$supplyQnt;
											}
										?>
											<tr bgcolor='#e8edff' id='drow_<?=$i?>'>
												<td class='listing-item' nowrap><?=$rmlot?><input type='hidden' name='rmlotidCerify_<?=$i?>' id='rmlotidCerify_<?=$i?>' value='<?=$weighment[10]?>'/></td>
												<td class='listing-item' nowrap><?=$supplier?><input type='hidden' name='weightmentId_<?=$i?>' id='weightmentId_<?=$i?>' value='<?=$weighment[11]?>'/></td>
												<td class='listing-item' nowrap><?=$pond?></td>
												<td class='listing-item' nowrap><?=$processCode?></td>
												<td class='listing-item' nowrap><?=$count?></td>
												<td class='listing-item' nowrap id='supplyQty_<?=$i?>'><?=$supplyQnt?><input type='hidden' name='hidesupplyQnt_<?=$i?>' id='hidesupplyQnt_<?=$i?>' value='<?=$supplyQnt?>'/></td>
												<td class='listing-item' nowrap ><input  type='text' name='adjustedQty_<?=$i?>' id='adjustedQty_<?=$i?>' value='<?=$adjustQnty?>' onkeyup='chkAdjustQty(<?=$i?>);'/><input  type='hidden' name='hideAdjustedQty_<?=$i?>' id='hideAdjustedQty_<?=$i?>' value='<?=$adjustQnty?>'/></td>
												<td class='listing-item' nowrap ><input  type='text' name='balanceQty_<?=$i?>' id='balanceQty_<?=$i?>' value='<?=$balanceQty?>' readonly/><input  type='hidden' name='hideCertificateBalanceQty_<?=$i?>' id='hideCertificateBalanceQty_<?=$i?>' value='<?=$cerificateBalanceQnty?>'/></td>
												<td class="listing-item" align="center">
													<a onclick="setCerificateEdit(<?=$i?>);" href="###">
													<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
													</a>
													<input id="dStatus_<?=$i?>" type="hidden" value="" name="dStatus_<?=$i?>">
													<input id="dIsFromDB_<?=$i?>" type="hidden" value="N" name="dIsFromDB_<?=$i?>">
													<input id="editPhtMonitoring_<?=$i?>" type="hidden" value="<?=$phtQntyId?>" name="editPhtMonitoring_<?=$i?>">
												</td>
	
											</tr>
										</td>
									</tr>

									<? 
									$i++;
									} ?>
								</table>
							</td>
							<input type='hidden' name='certificateCnt' id='certificateCnt' value='<?=$i?>' />
						</tr>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>


					<?php
					}
					?> 
				</td>
			</tr>
					 
		
	
	<tr>
	<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhtMonitoring.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhtMonitoring(document.frmPhtMonitoring);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhtMonitoring.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhtMonitoring(document.frmPhtMonitoring);">												</td>

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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;PHT Monitoring  </td>
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
												<td nowrap><? if($del==true){?><!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$phtMonitoringDataSize;?>);"><? }?>-->&nbsp;<? if($add==true){?><input type="button" onclick="displayPopUp();" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPHTMonitoring.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
												if( sizeof($phtMonitoringRecords) > 0 )
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
      				$nav.= " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<!--<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>-->
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">PHT Certificate No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Species</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supply Qty</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM Lot ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PHT Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Set off Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Balance Qty</td>
		
		<td class="listing-head"></td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($phtMonitoringRecords as $sir) {
		$i++;
		$phtMonitoringId	=	$sir[0];
		$date		=	dateFormat($sir[1]);
		$selLotId = $sir[2];
		$supplierId=$sir[3];
		$supplier=$sir[7];
		$supplierGroupName=$sir[8];
		$speciousId		=	$sir[5];
		$specious	=	$sir[9];
		$supplyQty		=	$sir[6];
		$phtcetificate=$sir[10];
		$phtcert=$phtMonitorngObj->getCertificateQuantity($sir[0]);
		
		
	?>
	<tr  bgcolor="WHITE">
		<!--<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$phtMonitoringId;?>" class="chkBox"></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$date;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$phtcetificate;?></td>
		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplier;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierGroupName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$specious;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplyQty;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php 
		foreach($phtcert as $detail)
		{
		echo $detail[3];
		echo '<br/>';
		}
		?> </td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[7];
		echo '<br/>';
		}
		?> 
		</td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[0];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[1];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<?php 
		foreach($phtcert as $detail)
		{
		echo $detail[2];
		echo '<br/>';
		}
		?> 
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<a href="javascript:printWindow('ViewPHTMonitoringDetails.php?phtMonitoringId=<?=$phtMonitoringId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
		</td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$phtMonitoringId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PhtMonitoring.php';"></td>
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
      				$nav.= " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhtMonitoring.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$phtMonitoringDataSize;?>);"><? }?>-->&nbsp;<? if($add==true){?><input type="button" value=" Add New " name="cmdAddNew"  class="button" onclick="displayPopUp();"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPHTMonitoring.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
												
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
		
		
	</table>
	</form>
	
	<div id="dialog" title="Data Input Type" style="display:none" >
		<p>
			<form action="#" id="inputVal" name="inputVal">
			<table cellpadding="6" cellspacing="1" bgcolor="#999999" align="center">
				<tr bgcolor="#ffffff">
					<td colspan="2" bgcolor="#e8edff" class="listing-head" height="30"><img width='11' height='15' border='0' src='images/topLink.jpg'>Input Type</td>
				</tr>
				<tr bgcolor="#ffffff" height="40">
					<td class="listing-item"><b><input type="radio" name="inputData" value="Certificate">Certificate based</b></td>
					<td class="listing-item"><b><input type="radio" name="inputData" value="Supplier">Supplier based</b></td>
				</tr>
				<tr bgcolor="#ffffff" height="40" align='center'><td colspan="2"><input class='button' type='submit' style="height:18px; font-size:11px; font-align:center" value='Submit' name='inputType' id='inputType' ></td></tr>
			</table>
			</form>
		</p>
	</div>

<?php
if($inputData!='')
{
?>
<script>
	beginrefresh();
</script>
<?
}
?>
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
			inputField  : "select_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "select_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	//window.load = beginrefresh();
	
	var t ='<?=$refreshTimeLimit?>';	
	var sTime = Math.floor(t/60)+":"+(t%60);	
	var limit= sTime;		
	
	if (document.images){	
		var parselimit=limit.split(":");
		parselimit=parselimit[0]*60+parselimit[1]*1;
	}

	var curtime = 0;
	function beginrefresh()
	{		
		if (!document.images) return;
		if (parselimit==1) {
			xajax_deleteTemporaryData();
			//document.getElementById("frmPhtMonitoring").submit();
			}
		else { 			
			parselimit = parselimit-1 ;
			var curmin=Math.floor(parselimit/60);
			var cursec=parselimit%60;
			if (curmin!=0)  
				curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
			else
				curtime=cursec+" seconds left until page refresh!";
			//window.status=curtime;
			document.getElementById("refreshMsgRow").innerHTML = curtime;
			setTimeout("beginrefresh()",1000);
		}
	}


	</SCRIPT>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>