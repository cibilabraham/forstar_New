<?php
	require("include/include.php");
	require_once("lib/supplierstock_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$addAnother	= false;
	$layer		= "";
	
	$selection = "?pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"]."&supplierRateListFilter=".$p["supplierRateListFilter"];

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

	$supplierRateList = "";
	# Add Supplier Stock Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode	= false;	
		$editMode	= false;
		$p["editId"]	= "";
	}

	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];	
	if ($p["selStock"]!="") $selStockId = $p["selStock"];
	
	
	if ($p["cmdAdd"]!="" || $p["cmdAddAnother"]!="") {
	
		$selSupplierId		=	$p["selSupplier"];
		$selStockId		=	$p["selStock"];
		$supplierRateList	= $p["supplierRateList"];
		$plantUnitid=$p["plantUnit"];
		$negoPrice	=	trim($p["negotiatedPrice"]);
					//echo "the plant unit is $plantUnitid";
		//echo "----$supplierRateList";
		# Creating a New Rate List
		/*if ($supplierRateList=="") {
			$supplierRec	= $supplierMasterObj->find($selSupplierId);
			$supplierName = str_replace (" ",'',$supplierRec[2]);
			$selName 	 = substr($supplierName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			//$startDate    = date("Y-m-d");
			$startDate =mysqlDateFormat($p["startDate"]);
			//$plantUnitid=$p["plantUnit"];
			$rateListName = $selName." - ".date("dMy", strtotime($startDate));
			//$rateListName = $selName." - ".date("dMy", strtotime($startDate))."-U-$plantUnitid";
			$supplierRateListRecIns = $supplierRateListObj->addSupplierRateList($rateListName, $startDate, $cRList, $selSupplierId, $dCurrentRListId,$negoPrice,$selStockId);
			//if ($supplierRateListRecIns) //$supplierRateList = $supplierRateListObj->latestRateList($selSupplierId);
			$supplierRateList=$databaseConnect->getLastInsertedId();
		}*/
		$vaildDateEntry	=$supplierRateListObj->chkValidDateEntry($startDate,"",$selSupplier,$supplierRateList); 
	//	$uniqueRecords = $supplierstockObj->fetchAllUniqueRecords($selSupplierId, $selStockId, $supplierRateList);	

		if (sizeof($uniqueRecords)==0) {

			$quotePrice	=	trim($p["quotedPrice"]);
			
			$exciseRate	=	$p["exciseRate"];
			$cstRate	=	$p["cstRate"];
			$schedule	=	$p["schedule"];
			$remarks	=	$p["remarks"];
			$stockType	=	$p["stockType"];

			$layerKgRate	=	$p["layerKgRate"];
			$layerConverRate	=	$p["layerConverRate"];
			
			$unitPricePer	 	= $p["unitPricePer"]; 
			$unitPricePerOneItem	= $p["unitPricePerOneItem"];	
			//$plantUnitid=$p["plantUnit"];
			$startdate=mysqlDateFormat($p["startDate"]);
			//$dateStart=mysqlDateformat
			
					
			if ( $selSupplierId!="" && $selStockId!="" && $stockType!="") {
				//echo "Haii";
				$supplierStockRecIns	=	$supplierstockObj->addSupplierStock($selSupplierId, $selStockId, $quotePrice,  $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $userId, $supplierRateList, $unitPricePer, $unitPricePerOneItem,$plantUnitid,$startdate);

				#$For Layer Adding
				if ($stockType=='P') {
					$hidLayerCount	=	$p["hidLayerCount"];
					$lastId = $databaseConnect->getLastInsertedId();
					for ($i=0; $i<$hidLayerCount; $i++) {
						$paperQuality	=	$p["paperQuality_".$i];
						$layerBrand	=	$p["layerBrand_".$i];
						$layerGsm	=	$p["layerGsm_".$i];
						$layerBf	=	$p["layerBf_".$i];
						$layerCobb	=	$p["layerCobb_".$i];
						$layerNo	=	$p["layerNo_".$i];
						if ($paperQuality!="" && $layerBrand!="") {
							$layerRecIns	=	$supplierstockObj->addLayer($lastId,$paperQuality,$layerBrand,$layerGsm,$layerBf,$layerCobb,$layerNo);
						}
					}
				}
			}

			if ($supplierStockRecIns) {
				$addMode	=	false;
				
				$sessObj->createSession("displayMsg",$msg_succAddSupplierStock);
				//$sessObj->createSession("nextPage",$url_afterAddSupplierStock.$selection);
				if ($p["cmdAddAnother"]!="") {
					$addMode	=	true;
					$addAnother	=	true;
					$selSupplierId	=	"";
					$selStockId	=	"";
					$quotePrice	=	"";
					$negoPrice	=	"";
					$exciseRate	=	"";
					$cstRate	=	"";
					$unitPricePer	= 	""; 
					$unitPricePerOneItem	= "";
					$selDate	=	"";
					$schedule	=	"";
				
					$remarks	=	"";
					$stockType	=	"";
					$layerKgRate		=	"";
					$layerConverRate	=	"";
					$quotedPrice		=	"";
					$negotiatedPrice	=	"";
					$editStockId		=	"";
					$editSupplierId		= 	"";
				} else if ($p["cmdAdd"]!="") {
					//$sessObj->createSession("nextPage",$url_afterAddSupplierStock.$selection);
				}
			} else {
				$addMode	=	true;
			$err		=	$msg_failAddSupplierStock;
			}
			$supplierStockRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddSupplierStock;
		}
		$uniqueRecords = false;
	}
	
	
	# Edit Supplier Stock
	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$supplierStockRec	=	$supplierstockObj->find($editId);
		
		$editSupplierStockId	=	$supplierStockRec[0];		
		
		if ($p["editSelectionChange"]=='1' || $p["selSupplier"]=="") {
			$selSupplierId		= 	$supplierStockRec[1];
		} else {
			$selSupplierId		=	$p["selSupplier"];
		}
		//$editSupplierId	=	$supplierStockRec[1];
		$quotedPrice		=	$supplierStockRec[3];
		$negotiatedPrice	=	$supplierStockRec[4];
		$exciseRate		=	$supplierStockRec[5];
		$cstRate		=	$supplierStockRec[6];
		
		//$Date		=	explode("-",$supplierStockRec[7]);
		$schedule	=	$supplierStockRec[7];
			
		$layerRate		=	$supplierStockRec[8];
		$layerConverRate	=	$supplierStockRec[9];
		$remark			=	$supplierStockRec[10];

		$supplierRateList	= $supplierStockRec[12];

		//echo "--$supplierRateList";
		
		if ($p["editSelectionChange"]=='1' || $p["selStock"]=="") {
			$editStockId		=	$supplierStockRec[2];
		} else {
			$editStockId		=	$p["selStock"];
		}
		
		$unitPricePer	 	= $supplierStockRec[13]; 
		$unitPricePerOneItem	= $supplierStockRec[14];
		//$startdate=dateFormat($supplierStockRec[15]);
		//$srateListId=$supplierStockRec[15];
		$startdate=dateFormat($supplierStockRec[15]);
		if($supplierStockRec[16]!='0000-00-00')
		{
			$enddate=dateFormat($supplierStockRec[16]);
		}
		//$startdate=dateFormat($supplierRateListObj->getStartDate($srateListId));
		$stockRec		=	$stockObj->find($editStockId);
		$stockType		=	$stockRec[9];
		$layer			=	$stockRec[16];
		echo "The value of layer is $layer";
		$selUnitId		= 	$stockRec[6];
		if ($selUnitId!="") {
			$stockItemUnitRec	= $stockItemUnitObj->find($selUnitId);			
			$unitName		= stripSlash($stockItemUnitRec[1]);
		}
		
		$layerRecs		=	$supplierstockObj->fetchLayerRecords($editId);
		
		//$layer			=	sizeof($layerRecs);
		echo "The value of layer is $layer";
	}


	#Update 
	if ($p["cmdSaveChange"]!="") {
		
		$supplierStockId	=	$p["hidSupplierStockId"];

		$selSupplierId		=	$p["selSupplier"];
		$selStockId		=	$p["selStock"];
		
		$quotePrice		=	trim($p["quotedPrice"]);
		$negoPrice		=	trim($p["negotiatedPrice"]);
		$exciseRate		=	$p["exciseRate"];
		$cstRate		=	$p["cstRate"];		
		$schedule		=	$p["schedule"];
					
		$remarks		=	$p["remarks"];
		$stockType		=	$p["stockType"];
					
		$layerKgRate		=	$p["layerKgRate"];
		$layerConverRate	=	$p["layerConverRate"];

		$supplierRateList	= $p["supplierRateListedit"];
		
		$unitPricePer	 	= $p["unitPricePer"]; 
		$unitPricePerOneItem	= $p["unitPricePerOneItem"];
		$startdate=mysqlDateFormat($p["startDate"]);
		$hidNegoPrice		= trim($p["hidNegoPrice"]);
		$newstartDate=mysqlDateFormat($p["newstartDate"]);
		//echo $newstartDate;
		//die();
		#checking supplier stock using in PO
		if ($negoPrice!=$hidNegoPrice) {
			$supplierStockInPO  = $supplierstockObj->chkSupplierStockExist($selSupplierId, $supplierRateList, $selStockId);
		} else $supplierStockInPO = false;

		if (!$supplierStockInPO) {
			if ($supplierStockId!="" && $selSupplierId!="" && $stockType!="" && $newstartDate!="") {
				$supplierStockRecUptd	=	$supplierstockObj->updateSupplierStock($supplierStockId, $selSupplierId, $selStockId, $quotePrice, $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $supplierRateList, $unitPricePer, $unitPricePerOneItem,$startdate,$newstartDate);
				
				$deleteLayerRecs = $supplierstockObj->deleteLayerRecs($supplierStockId);
				
				# For Layer Adding
				if ($stockType=='P') {							
					$hidLayerCount	=	$p["hidLayerCount"];
				
					for ($i=0; $i<$hidLayerCount; $i++) {
								
						$paperQuality	=	$p["paperQuality_".$i];
						$layerBrand		=	$p["layerBrand_".$i];
						$layerGsm		=	$p["layerGsm_".$i];
						$layerBf		=	$p["layerBf_".$i];
						$layerCobb		=	$p["layerCobb_".$i];
						$layerNo		=	$p["layerNo_".$i];
						if ($paperQuality!="" && $layerBrand!="") {
							$layerRecIns	=	$supplierstockObj->addLayer($supplierStockId, $paperQuality, $layerBrand, $layerGsm, $layerBf, $layerCobb, $layerNo);
						}
					}
				}
			}	
		}

		if ($newstartDate!="")
		{
			
			$supplierStockRecIns	=	$supplierstockObj->addSupplierStock($selSupplierId, $selStockId, $quotePrice,  $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $userId, $suppRateListId, $unitPricePer, $unitPricePerOneItem,$plantUnitid,$newstartDate);

				# For Layer Adding
				if ($stockType=='P') {							
					$hidLayerCount	=	$p["hidLayerCount"];
				
					for ($i=0; $i<$hidLayerCount; $i++) {
								
						$paperQuality	=	$p["paperQuality_".$i];
						$layerBrand		=	$p["layerBrand_".$i];
						$layerGsm		=	$p["layerGsm_".$i];
						$layerBf		=	$p["layerBf_".$i];
						$layerCobb		=	$p["layerCobb_".$i];
						$layerNo		=	$p["layerNo_".$i];
						if ($paperQuality!="" && $layerBrand!="") {
							$layerRecIns	=	$supplierstockObj->addLayer($supplierStockId, $paperQuality, $layerBrand, $layerGsm, $layerBf, $layerCobb, $layerNo);
						}
					}
				}
		}

	/*	$supplierRec	= $supplierMasterObj->find($selSupplierId);
		$supplierName = str_replace (" ",'',$supplierRec[2]);
		$selName 	 = substr($supplierName, 0,9);	
		//$rateListName = $selName."-".date("dMy");
		$newstartDate=mysqlDateFormat($p["newstartDate"]);
			$rateListName = $selName." - ".date("dMy", strtotime($newstartDate));
		if ($newstartDate!="")
		{
			//echo "hai";
			//$cyRLRecIns = $usdvalueObj->addCurrencyRateList($rateListName, $sDate, $userId, $currencyValue, $description);
			$suppRecIns = $supplierstockObj->addSupplierRateList($rateListName,$newstartDate, $userId, $currencyValue, $description,$selSupplierId,$selStockId,$negoPrice);
			if ($suppRecIns) {
						$suppRateListId = $supplierstockObj->insertedSUPPLatestRateList();
						$cyLatestId		= $databaseConnect->getLastInsertedId();
						$suppRateListId=$cyLatestId;
						//$supplierRateList=$cyLatestId;
						$updatePrevRateListRec =$supplierstockObj->updatesupRateListRec($supplierRateList, $newstartDate,$rate,$stockid);

						if ($suppRateListId!="" && $suppRateListId!=0) {
								//$updateRateListRec = $supplierstockObj->updateSupplierRateList($cyRateListId, $currencyId, $currencyValue, $description);
								//$currencyRecUptd = true;
								//$currencyRecUptd	= $supplierstockObj->updateSupplier($currencyId, $currencyCode, $currencyValue, $description, $cyRateListId);
								$supplierStockRecIns	=	$supplierstockObj->addSupplierStock($selSupplierId, $selStockId, $quotePrice,  $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerKgRate, $layerConverRate, $userId, $suppRateListId, $unitPricePer, $unitPricePerOneItem);
						}
			}


		}*/

		if (($supplierStockRecUptd) || ($supplierStockRecIns)) {
			$editMode	=	false;
			$editId		= "";	
			$p["editId"]	= "";
			$sessObj->createSession("displayMsg",$msg_succSupplierStockUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierStock.$selection);
		} else {
			$editMode	=	true;
			if ($supplierStockInPO) $err 	= $msg_failSupplierStockUpdUsingInPO;
			else $err		=	$msg_failSupplierStockUpdate;			
		}
		$supplierStockRecUptd	=	false;
	}
	

	# Delete Supplier Stock
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierStockId	= $p["delId_".$i];
			$stockId		= $p["hidStockId_".$i];
			$supplierId		= $p["hidSupplierId_".$i];
			$rateListId		= $p["hidRateListId_".$i];

			#checking supplier stock using in PO
			//$supplierStockInPO  = $supplierstockObj->chkSupplierStockExist($supplierId, $rateListId, $stockId);
			//if ($supplierStockId!=""  && !$supplierStockInPO) {
			if ($supplierStockId!="") {
				$deleteLayerRecs	 =	$supplierstockObj->deleteLayerRecs($supplierStockId);
				$supplierStockRecDel =	$supplierstockObj->deleteSupplierStock($supplierStockId);	
			}
		}
		if ($supplierStockRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierStock);
			$sessObj->createSession("nextPage",$url_afterDelSupplierStock.$selection);
		} else {
			$errDel	=	$msg_failDelSupplierStock;
		}
		$supplierStockRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierStockId	=	$p["confirmId"];
			if ($supplierStockId!="") {
				// Checking the selected fish is link with any other process
			//	$supplierDetail=$supplierstockObj->find($supplierStockId);	
				//die();
				$supplierRecConfirm = $supplierstockObj->updateSupplierStockconfirm($supplierStockId);
			//	$supplierRateRecConfirm = $supplierstockObj->updateSupplierRateListconfirm($supplierDetail[1],$supplierDetail[2]);
			}

		}
		if ($supplierRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsupplierstock);
			$sessObj->createSession("nextPage",$url_afterDelSupplierStock.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
	$rowCount	=	$p["hidRowCount"];
	for ($i=1; $i<=$rowCount; $i++) {

			$supplierStockId = $p["confirmId"];
			if ($supplierStockId!="") {
				#Check any entries exist
				
					$supplierRecConfirm = $supplierstockObj->updateSupplierStockReleaseconfirm($supplierStockId);
				
			}
		}
		if ($supplierRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsupplierstock);
			$sessObj->createSession("nextPage",$url_afterDelSupplierStock.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	#----------------Rate list--------------------	
	/*
		if ($g["selRateList"]!="") {
			$selRateList	= $g["selRateList"];
		} else if($p["selRateList"]!="") {
			$selRateList	= $p["selRateList"];
		} else {
			$selRateList = $supplierRateListObj->latestRateList();	
		}
	*/
	#---------------------------------------------

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];	

	/*if ($g["supplierRateListFilter"]!="") $supplierRateListFilterId = $g["supplierRateListFilter"];
	else $supplierRateListFilterId = $p["supplierRateListFilter"];*/	

	# Resettting offset values
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;	
		$supplierRateListFilterId = "";	
	} /*else if ($p["hidSupplierRateListFilterId"]!=$p["supplierRateListFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}	*/

	#List all Supplier Stock
	$supplierStockRecords	= $supplierstockObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId);
	//$supplierStockRecords	= $supplierstockObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId, $supplierRateListFilterId);
	//print_r($supplierStockRecords);
	$supplierStockSize	= sizeof($supplierStockRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($supplierstockObj->fetchAllRecords($supplierFilterId));
	//$numrows	=  sizeof($supplierstockObj->fetchAllRecords($supplierFilterId, $supplierRateListFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Supplier
	$supplierRecords	=	$supplierMasterObj->fetchAllRecordsActivesupplier("INV");
	//$supplierRecords	=	$supplierMasterObj->fetchAllRecords("INV");
	# List all Stocks
	$plantUnit=$p["plantUnit"];
	$stockRecords		=	$stockObj->fetchAllActiveplantUnitRecords($plantUnit);

	$plantUnitRecords=$plantandunitObj->fetchAllRecordsPlantsActive();
	
	if ($addMode==true) {
	#For selecting Packing or Ordinary
		if ($addAnother	==true) $selStockId="";
		else $selStockId  =	$p["selStock"];
		
		$stockRec	= $stockObj->find($selStockId);
		$stockType	= $stockRec[9];
		$layer		= $stockRec[16];
		$selUnitId	= $stockRec[6];
		if ($selUnitId!="") {
			$stockItemUnitRec	= $stockItemUnitObj->find($selUnitId);			
			$unitName		= stripSlash($stockItemUnitRec[1]);
		}
	}
	
	if ($p["newline"]!="")  $layer = $p["new"]+$p["newline"];
	else if ($p["new"]!="") $layer = $p["new"];
	
	#Supplier Price Rate List
	//$supplierRateListRecords = $supplierRateListObj->fetchAllRecords();	
	
	/*
	if ($selSupplierId!="") {
		# Get Supplier Rate List
		$filterSupplierRateListRecords = $supplierRateListObj->fetchAllSupplierRateListRecords($selSupplierId);
		// Find the Latest Rate List Id
		if ($addMode) $selRateList = $supplierRateListObj->latestRateList($selSupplierId);
	}
	*/

	# List Page Filter
	if ($supplierFilterId) {
		# Get Supplier Rate List
		$supplierRateListFilterRecords = $supplierRateListObj->fetchAllSupplierRateListRecords($supplierFilterId);
	}
		
	# Bulk Update
	if ($p["cmdUpdate"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$priceModified  = $p["priceModified"];
		$scheduleModified = $p["scheduleModified"];
		
	/*	if ($supplierFilterId!="" && $supplierRateListFilterId!="" && $priceModified!="") {
				$getPORecords = $supplierstockObj->getPurchaseOrderRec($supplierRateListFilterId);
				if (sizeof($getPORecords)>0) $warning = "The selected Supplier have some pending PO's. So price change will Create a new Price List";
		if ($warning!="") {
	?>

		<SCRIPT LANGUAGE="JavaScript">
			<!--
				alert("<?=$warning;?>");
			//-->
		</SCRIPT>

	<?php
		}
		
		if (sizeof($getPORecords)>0) {
			$rateListName	=	"Rate List ".date("dmy");		
			$startDate	=	date("Y-m-d");					
					
			$supplierRateListRecIns = $supplierRateListObj->addSupplierRateList($rateListName, $startDate, $supplierRateListFilterId, $supplierFilterId, $supplierRateListFilterId);
			# Find the Current Rate List Id
			$currentRateListId = $supplierRateListObj->latestRateList($supplierFilterId);		
			$newRateListCreated = true;					
		}
	}	*/
	
		
		
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierStockId	= $p["hidSupplierStockId_".$i];
			$negotiatedPrice	= $p["negotiatedPrice_".$i];	
			$hidNegotiatedPrice	= $p["hidNegotiatedPrice_".$i];
			$supplySchedule		= $p["supplySchedule_".$i];
			$hidSupplySchedule	= $p["hidSupplySchedule_".$i];
			$stockId		= $p["hidStockId_".$i];
			$supplierId		= $p["hidSupplierId_".$i];			
			
			
			if ($negotiatedPrice!=$hidNegotiatedPrice || $supplySchedule!=$hidSupplySchedule) {	
				if ($newRateListCreated) {
					$supplierStockRecUptd = $supplierstockObj->uptdSupplierStockRec($supplierId, $stockId, $currentRateListId, $negotiatedPrice, $supplySchedule);
				 } else if (($priceModified!="" || $scheduleModified!="") && !$newRateListCreated) {
					$updateSupplierStockRec = $supplierstockObj->bulkUpdateSupplierStockRec($supplierStockId, $negotiatedPrice, $supplySchedule, $priceModified);			
				}						
			}			
			
		}
		if ($updateSupplierStockRec && !$newRateListCreated) {
			$sessObj->createSession("displayMsg",$msg_succSupplierStockUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierStock.$selection);
		} else if (!$newRateListCreated) {
			$err = $msg_failSupplierStockUpdate;
		}
		$updateSupplierStockRec	= false;
	}

	
	# Revised PO
	if ($p["cmdRevisePOUpdate"]!="") {
		$rowCount	=	$p["hidReviseRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$poMainId = $p["poMainId_".$i];
			$supplierId = $p["supplierId_".$i];
			$poStatus  = $p["hidStatus_".$i];
			# Current Rate List
				$currentRateListId = $supplierRateListObj->latestRateList($supplierId);
			if ($poMainId!="" && $poStatus!='PC') {							
				$updatePORec = $supplierstockObj->updatePORecs($poMainId, $currentRateListId, $supplierId);
			}
			# Patially completed
			if ($poMainId!="" && $poStatus=='PC') {					
				# Get PO Number
				list($isMaxId,$purchaseOrderNo)	= $idManagerObj->generateNumberByType("PO"); 
				# Get all Received Records
				$getPurchaseOrderRecords = $supplierstockObj->getPurchaseOrderRecords($poMainId);
				$prevPOId = "";
				foreach ($getPurchaseOrderRecords as $por) {
					$cPOId	   = $por[1];
					$stockId    = $por[2];						
					$orderedQty = $por[4];	
					$receivedQty = $supplierstockObj->getReceivedQtyOfStock($stockId, $poMainId);
					$balanceQty = $orderedQty-$receivedQty;				
					if ($balanceQty>0) {
						if ($prevPOId!=$cPOId) {
							//echo "Here";
							# Add New PO and Updating Current PO Status
					 		$addNewPORec = $supplierstockObj->addPurchaseOrder($purchaseOrderNo, $supplierId, $userId, $currentRateListId, $poMainId);
							# Get Last Inserted Id
							$poEntryId = $databaseConnect->getLastInsertedId();			
						}
						# Get current unit Price
						$unitPrice = $supplierstockObj->getUnitPrice($supplierId, $stockId, $currentRateListId);
						$totalAmt = $balanceQty * $unitPrice;
						// insert PO Entry Recs
						$poEntryRecIns = $supplierstockObj->addPurchaseEntries($poEntryId, $stockId, $unitPrice, $balanceQty, $totalAmt);				
					}
					$prevPOId = $cPOId;
				}
			}
			$urlSelection = "?pageNo=".$p["pageNo"]."&supplierFilter=".$supplierId."&supplierRateListFilter=".$currentRateListId;
		}
		if ($updatePORec || $addNewPORec) {
			$sessObj->createSession("displayMsg","Purchase order updated sucessfully");
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierStock.$urlSelection);
		} else {
			$err = $msg_failSupplierStockUpdate;
		}
		$updatePORec	= false;
	}
	
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";


	if ($editMode)	$heading	= $label_editSupplierStock;
	else		$heading	= $label_addSupplierStock;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/supplierstock.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");


	//echo "--&&&$supplierRateList";
?>
	<form name="frmSupplierStock" action="SupplierStock.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr>
	  <td align="center"><a href="SupplierRateList.php" class="link1"> Supplier Rate List <?php //echo "--$supplierRateList";?></a></td>
	</tr>	
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
	<tr><td height="5" align="center">&nbsp;</td></tr>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Supplier Stock";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="96%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierStock(document.frmSupplierStock);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Save & Exit " onClick="return validateSupplierStock(document.frmSupplierStock);" title="Save and Close"> &nbsp;&nbsp;<input type="submit" name="cmdAddAnother" id="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierStock(document.frmSupplierStock);">												</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>">
	<tr>
		  <td nowrap height="10"></td>		
	</tr>
<!-- Message display row -->
	<tr>
	  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divRecExistTxt">
		</td>
	</tr>
	<tr>
		  <td colspan="2" nowrap style="padding-left:10px; padding-right:10px;" >
		  <table width="200" align="center">
                    <tr>
                        <td colspan="2" valign="top">
	  <table>
		  <tr>
			  <td class="fieldName">*Supplier</td>
			  <td>
			  <select name="selSupplier" id="selSupplier" onchange="xajax_getSupplierRec(document.getElementById('selSupplier').value, document.getElementById('selStock').value, '<?=$supplierRateList?>', '<?=$mode?>', '<?=$editSupplierStockId?>');" style="width:150px;">
                          <option value="">--Select--</option>
                           <?						  
			  foreach($supplierRecords as $sr) {
				$supplierId	=	$sr[0];				
				$supplierName	=	stripSlash($sr[2]);
				$selected = ($selSupplierId==$supplierId || $editSupplierId==$supplierId)?"selected":"";
			?>
                       <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
                       <? }?>
                        </select></td>
		  </tr>


		 <!--  <tr>
			  <td class="fieldName">*Unit</td>
			  <td>
			  <select name="plantUnit" id="plantUnit" style="width:165px;" onChange="this.form.submit();">



                        <option value="">--select--</option>
                        <?
			foreach ($plantUnitRecords as $pur) {				
				$plantId		=	$pur[0];
				$plantName	=	stripSlash($pur[2]);
				$selected = ($plantId==$plantUnit)?"Selected":"";	
			?>
                        <option value="<?=$plantId?>" <?=$selected;?>><?=$plantName;?></option>
			<? }?>
                        </select></td>
		  </tr>-->
		  <tr>
		    <td class="fieldName">*Stock</td>
		    <td nowrap="true">			
			<select name="selStock" id="selStock" onchange="<? if ($addMode==true) { ?>this.form.submit();<? } else { ?> this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>" style="width:150px;">			
                       <option value="">--select--</option>
                       <?php
		  	foreach ($stockRecords as $sr) {
				$stockId	=	$sr[0];
				$stockName	=	stripslashes($sr[2]);
				$selected	= ($selStockId==$stockId || $editStockId==$stockId)?"selected":"";
			?>
                        <option value="<?=$stockId?>" <?=$selected?>><?=$stockName?></option>
                       <? }?>
                       </select></td>
	    </tr>
      <!--tr>
	    <td class="fieldName">Quoted.Price</td>
	    <td><input name="quotedPrice" type="text" value="<?=$quotedPrice;?>" size="3" style="text-align:right;"></td>
      </tr>
  	<tr>
		<td class="fieldName">*Negoti.Price</td>
		<td><input name="negotiatedPrice" type="text" value="<?=$negotiatedPrice;?>" size="3" style="text-align:right;"></td>
	</tr-->
<tr>
		<TD class="fieldName" nowrap>*Unit Price Per</TD>
		<td>
		<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
				<input name="unitPricePer" type="text" id="unitPricePer" size="3" value="<?=$unitPricePer?>" style="text-align:right;" onchange="displaySupplierStockUnitPrice();">
			</TD>
			<td class="listing-item"><div id="unitPricePerTxt"></div></td>
			</TR>
		</table>
		</td>
	</tr>
	<tr>
		<TD class="fieldName" nowrap>*<?php if ($editMode){?>
Current <?php }?> Start Date</TD>
		<td>
		<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
				<input name="startDate" type="text" id="startDate" size="8" value="<?=$startdate?>" style="text-align:right;" onchange="displaySupplierStockUnitPrice();" <?php if ($editMode){?>readonly <?php }?> >
			</TD>
			<td class="listing-item"><div id="unitPricePerTxt"></div></td>
			</TR>
		</table>
		</td>
	</tr>
<?php if ($editMode){?>
	<tr>
		<TD class="fieldName" nowrap>*New Start Date</TD>
		<td>
		<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
				<input name="newstartDate" type="text" id="newstartDate" size="8" value="<?=$enddate?>" style="text-align:right;" onchange="displaySupplierStockUnitPrice();" <? if($enddate) {?> readonly <? } ?>>
			</TD>
			<td class="listing-item"><div id="unitPricePerTxt"></div></td>
			</TR>
		</table>
		</td>
	</tr>
	<?php }?>
	<tr>
		<TD class="fieldName" nowrap>*Unit Price Per <span id="unitPricePerItemTxt"></span> (Rs.):</TD>
		<td>
			<table>
				<TR>
					<td class="fieldName">Quoted.Price</td>
	    				<td>
						<input name="quotedPrice" id="quotedPrice" type="text" value="<?=$quotedPrice;?>" size="3" style="text-align:right;">
					</td>
					<td class="fieldName">Negoti.Price</td>
					<td>
						<input name="negotiatedPrice" id="negotiatedPrice" type="text" value="<?=$negotiatedPrice;?>" size="3" style="text-align:right;" onchange="displaySupplierStockUnitPrice();">
						<input name="hidNegoPrice" id="hidNegoPrice" type="hidden" value="<?=$negotiatedPrice;?>" size="3">
					</td>
				</TR>
			</table>
			<!--input name="unitPricePerItem" type="text" size="3" id="unitPricePerItem" value="<?=$unitPricePerItem?>" style="text-align:right;" onchange="displaySupplierStockUnitPrice();"-->
		</td>
	</tr>
	<tr id="rowOfOneItemPrice">
		<TD class="fieldName" nowrap>Unit Price Per <span id="unitPricePerOneItemTxt"></span> (Rs.)</TD>
		<td>
			<input name="unitPricePerOneItem" type="text" size="3" id="unitPricePerOneItem" value="<?=$unitPricePerOneItem?>" style="text-align:right; border:none;" readonly />
		</td>
	</tr>
	<tr>
		<td class="fieldName">Excise Rate</td>
		<td><input name="exciseRate" type="text" value="<?=$exciseRate;?>" size="3" style="text-align:right;"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap="nowrap">CST/VAT Rate</td>
		<td><input name="cstRate" type="text" value="<?=$cstRate;?>" size="3" style="text-align:right;"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap="nowrap">Supply Schedule</td>
		<td class="listing-item"><input name="schedule" type="text" id="schedule" size="3" value="<?=$schedule?>" style="text-align:right;">&nbsp; Days</td>
	</tr>
	<tr>
		<TD>
		<?php //echo "--$supplierRateList";
		
		
		?>

		<input type="hidden" name="supplierRateListedit" id="supplierRateListedit" value="<?=$supplierRateList?>">
			<input type="hidden" name="supplierRateList" id="supplierRateList" value="<?=$supplierRateList?>">
			<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
		</TD>
	</tr>
	  <tr>
		    <td class="fieldName">Remarks</td>
		    <td><textarea name="remarks"><?=$remark?></textarea></td>
	    </tr>
   </table>												
 </td>
	  <? if($stockType=='P'){?>
         <td valign="top">
		  <table>
			  <tr>
			<td colspan="2" class="listing-item">
			  <!--<fieldset><legend>Details of Layer</legend>-->
			<?php
				$entryHead = "Details of Layer";
				$rbTopWidth = "";
				require("template/rbTop.php");
			?>
			<table>
				<TR>
				<TD style="padding:10px;">
		  <table width="200" border="0" cellpadding="1" cellspacing="1" class="newspaperType">
			<tr align="center">
				<th nowrap="nowrap" style="text-align:center;">Layer of</th>
				<th style="text-align:center;">Quality</th>
				<th style="text-align:center;">Brand</th>
				<th style="text-align:center;">GSM</th>
				<th style="text-align:center;">BF</th>
				<th style="text-align:center;">COBB</th>
			</tr>
		<? 
			$editSelChange = $p["editSelectionChange"];
			echo "<b style='font-size:12px; color:#004080'>The value of layer is $layer</b>";
			for($i=0;$i<$layer;$i++) {
				$rec = $layerRecs[$i];
				$hidId=$rec[0];
				if ($p["editSelectionChange"]=='1' || $p["paperQuality_".$i]=="") {
					$quality	=	$rec[2];
				} else {
					$quality	=	$p["paperQuality_".$i];
				}
						
				if ($p["editSelectionChange"]=='1' || $p["layerBrand_".$i]==""){
					$brand		=	$rec[3];
				} else {
					$brand		=	$p["layerBrand_".$i];
				}
								
				if ($p["editSelectionChange"]=='1' || $p["layerGsm_".$i]==""){
					$gsm		=	$rec[4];
				} else {
					$gsm		=	$p["layerGsm_".$i];
				}
				
				if ($p["editSelectionChange"]=='1' || $p["layerBf_".$i]==""){
					$bf			=	$rec[5];
				} else {
					$bf		=	$p["layerBf_".$i];
				}
				
				if($p["editSelectionChange"]=='1' || $p["layerCobb_".$i]==""){
					$cobb		=	$rec[6];
				} else {
					$cobb		=	$p["layerCobb_".$i];
				}	
								
				if($p["editSelectionChange"]=='1' || $p["layerNo_".$i]==""){
						$layerNo	=	$rec[7];
				} 	else {
						$layerNo	=	$p["layerNo_".$i];
				}
				?>
	<tr align="center">
		<td nowrap="true"><input name="layerNo_<?=$i?>" type="text" id="layerNo_<?=$i?>" size="2" value="<?=$layerNo;?>" style="text-align:center;"> </td>
		<td nowrap="true"><input name="paperQuality_<?=$i?>"  id="paperQuality_<?=$i?>" type="text" size="8" value="<?=$quality?>" style="text-align:center;"></td>
		<td nowrap="true"><input name="layerBrand_<?=$i?>"  id="layerBrand_<?=$i?>" type="text" size="8" value="<?=$brand?>" style="text-align:center;"></td>
		<td nowrap="true"><input name="layerGsm_<?=$i?>" id="layerGsm_<?=$i?>" type="text" size="4" value="<?=$gsm?>" style="text-align:center;"></td>
		<td nowrap="true"><input name="layerBf_<?=$i?>"  id="layerBf_<?=$i?>" type="text" size="4" value="<?=$bf?>" style="text-align:center;"></td>
		<td nowrap="true"><input name="layerCobb_<?=$i?>" id="layerCobb_<?=$i?>" type="text" size="4" value="<?=$cobb?>" style="text-align:center;"></td>
	</tr>
		<? 
			}
		?>
	<input type='hidden' name='hidLayerCount' id='hidLayerCount' value="<?=$i;?>">
	<input type="hidden" name="newline" value="">
	<input type="hidden" name="new" value="<?=$i?>" />							
		</table>
				</td>
				</tr>
				<tr>
					<td class="fieldName">
					<? if($addMode==true){?>
					<a href="javascript:newLayer()">Add Another Layer </a><? } else {?><a href="javascript:newLayer()" onclick="document.frmSupplierStock.editId.value=<?=$editId?>;">Add Another Layer </a><? }?>
					</td>
				</tr>
				</table>
				<!--</fieldset>-->
				<?php
					require("template/rbBottom.php");
				?>
				</td></tr>
												  <tr>
												    <td colspan="2" class="fieldName"><table width="200">
                                                      <tr>
                                                        <td class="fieldName">*Rate</td>
                                                        <td class="listing-item" nowrap="nowrap"><input name="layerKgRate" type="text" id="layerKgRate" size="3" value="<?=$layerRate?>" style="text-align:right;">
(Per Kg) </td>
                                                      </tr>
                                                      <tr>
                                                        <td class="fieldName" nowrap="nowrap">*Conversion rate</td>
                                                        <td class="listing-item"><input name="layerConverRate" type="text" id="layerConverRate" size="3" value="<?=$layerConverRate?>" style="text-align:right;"></td>
                                                      </tr>
                                                    </table></td>
												    </tr>
												  </table>		
										  </td>
												  <? }?>
                                                </tr>
                                              </table></td>
										  </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateSupplierStock(document.frmSupplierStock);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierStock.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save & Exit " onClick="return validateSupplierStock(document.frmSupplierStock);" title="Save and Close">&nbsp;&nbsp;<input type="submit" name="cmdAddAnother" id="cmdAddAnother1" class="button" value=" Save & Add Another " onClick="return validateSupplierStock(document.frmSupplierStock);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
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
	<?php 
		if (!$newRateListCreated) { 
	?>
	<tr>
		<td colspan="3" align="center">
				<table width="35%">
				<TR><TD>
				<?php			
					$entryHead = "";
					require("template/rbTop.php");
				?>
				<table cellpadding="4" cellspacing="4">
				<tr>
			<td nowrap="nowrap" style="padding:5px;">
			<table cellpadding="0" cellspacing="0">
        	<tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item">Supplier&nbsp;</td>
                <td>
		<select name="supplierFilter" id="supplierFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>
		<?						  
		foreach($supplierRecords as $sr) {
			$supplierId	= $sr[0];			
			$supplierName	= stripSlash($sr[2]);
			$selected = ($supplierFilterId==$supplierId)?"selected":"";
		?>
               <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
                <? }?>
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	<!--   <td class="listing-item">Rate List&nbsp;</td>
	<td>
		<select name="supplierRateListFilter" id="supplierRateListFilter" onchange="this.form.submit();">
                        <option value="">-- Select All --</option>
			<?
			foreach ($supplierRateListFilterRecords as $srl) {
				$rateListRecId	=	$srl[0];
				$rateListName	=	stripslashes($srl[1]);				
				$startDate	=	dateFormat($srl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = ($supplierRateListFilterId==$rateListRecId)?"Selected":"";
			?>
                      <option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? }?>
                      </select>
	</td>	-->
                          </tr>
                    </table></td></tr></table>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier Stock </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
	<tr>
		<TD colspan="3">
			<table width="900" align="center">
				<TR>
					<TD width="300"></TD>
					<TD width="300">
						<table>
							<TR>
								<TD nowrap>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierStockSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierStock.php?selRateList=<?=$selRateList?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300"><table><TR>	
		<TD>
			<? if($edit==true){?>
				<input type="submit" value=" Bulk Update " class="button"  name="cmdUpdate" onClick="return validateSupplierStockBulkUpdate();">
			<? }?>
		</td>
		</TR>
			</table>
		</TD>
		</TR>
			</table>
		</TD>
	</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
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
			<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
			<?
				if ( sizeof($supplierStockRecords) > 0 ) {
					$i	=	0;
			?>
			<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Stock</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Negoti.Price</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supply Schedule</th>
		
<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
<? }?>
<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		$prevSupplierId		=	0;
//print_r($supplierStockRecords);
		foreach ($supplierStockRecords as $ssr) {
			$i++;
			$supplierStockId	= $ssr[0];
			$supplierId		= $ssr[1];
			$supplierName		= "";
			if ($prevSupplierId!=$supplierId) $supplierName = stripslashes($ssr[12]);
			$stockName		= stripslashes($ssr[13]);
			$negotiatedPrice	= $ssr[4];
			$supplySchedule		= ($ssr[7]!=0)?$ssr[7]:"";
			$stockId		= $ssr[2];
			$rateListId		= $ssr[14];
			$disableBtn	= "";
			//if ($supplierFilterId=="" || $supplierRateListFilterId=="") $disableBtn = "disabled";
			$active=$ssr[15];
			$existingrecords=$ssr[16];
			$plantUnit=$ssr[17];
			//$startdate=$ssr[17];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierStockId;?>" class="chkBox">
			<input type="hidden" name="hidRateListId_<?=$i?>" id="hidRateListId_<?=$i?>" value="<?=$rateListId?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$plantUnit;?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$stockName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<input type="hidden" name="hidSupplierStockId_<?=$i?>" id="hidSupplierStockId_<?=$i?>" value="<?=$supplierStockId?>">
			<input type="text" size="6" name="negotiatedPrice_<?=$i?>" id="negotiatedPrice_<?=$i?>" value="<?=$negotiatedPrice;?>" style="text-align:right" tabindex="<?=$i?>" onkeyup="supplierStockValueChanged();">
			<input type="hidden" name="hidNegotiatedPrice_<?=$i?>" id="hidNegotiatedPrice_<?=$i?>" value="<?=$negotiatedPrice;?>" style="text-align:right">
			<input type="hidden" name="hidStockId_<?=$i?>" id="hidStockId_<?=$i?>" value="<?=$stockId?>">
			<input type="hidden" name="hidSupplierId_<?=$i?>" id="hidSupplierId_<?=$i?>" value="<?=$supplierId?>">		
			<?//$negotiatedPrice;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<input type="text" size="4" name="supplySchedule_<?=$i?>" id="supplySchedule_<?=$i?>" value="<?=$supplySchedule;?>" style="text-align:right" tabindex="<?=$i?>" onkeyup="supplierStockValueChanged();">	
			<input type="hidden" size="4" name="hidSupplySchedule_<?=$i?>" id="hidSupplySchedule_<?=$i?>" value="<?=$supplySchedule;?>" style="text-align:right">	
			<?//$supplySchedule;?>
		</td>
		
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<? if ($active==0){?>	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierStockId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierStock.php';" <?=$disableBtn?>>
		<? } ?>
		</td>
<? }?>


<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierStockId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) { ?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierStockId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		$prevSupplierId=$supplierId;
	}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierStock.php?pageNo=$page&supplierFilter=$supplierFilterId&supplierRateListFilter=$supplierRateListFilterId\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr>
		<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
	<tr>
	<td colspan="3" height="5" >
		<table width="900" align="center">
				<TR>
					<TD width="300"></TD>
					<TD width="300">
						<table>
							<TR>
								<TD nowrap>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierStockSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierStock.php?selRateList=<?=$selRateList?>&supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300"><table><TR>	
	<TD><? if($edit==true){?><input type="submit" value=" Bulk Update " class="button"  name="cmdUpdate" onClick="return validateSupplierStockBulkUpdate();"><? }?></td></TR></table></TD>
				</TR>
			</table>
	</td>
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
	<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">	
	<input type="hidden" name="hidSupplierRateListFilterId" value="<?=$supplierRateListFilterId?>">	
	<input type="hidden" name="hidUnitName" id="hidUnitName" value="<?=$unitName?>">	
		<tr>
			<td height="10"></td>
		</tr>
	<tr>
	  <td height="10" align="center"><a href="SupplierRateList.php" class="link1"> Supplier Rate List </a></td>
	</tr>
<? }?>
<?
// PO Exist 
if (sizeof($getPORecords)>0) {
?>
<tr>
	<td>
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%">
		<tr>
			<td>					
			<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
			<tr>
				<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
				<td background="images/heading_bg.gif" class="pageName" >&nbsp;Revise Purchase Order </td>
				<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
			</tr>
			<tr>
				<td colspan="3" height="10" ></td>
			</tr>
			<tr>	
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" align="center">
				<tr>
				<td>
						<? if($edit==true){?>
							<input type="submit" value=" Revise PO " class="button"  name="cmdRevisePOUpdate" onclick="return validateRevisePO();">
						<? }?>
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
									<td colspan="2" >
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center">
	<?
		if (sizeof($getPORecords)>0) {
		$j	=	0;
	?>	
	<tr align="center">
		<td class="listing-head" width="40" style="padding-left:10px; padding-right:10px;">Revise<br>
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'poMainId_'); " class="chkBox">	
		</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO Id</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>		
	</tr>
	<?
		
		foreach ($getPORecords as $pr) {
			$j++;
			$purchaseOrderId = $pr[0];
			$poNumber	 = $pr[1];
			$status		= $pr[2];
			if ($status=='C') {
				$displayStatus	=	"Cancelled";
			} else if ($status=='R') {
				$displayStatus	=	"Received";
			} else if ($status=='PC') {
				$displayStatus	=	"Partially Completed";
			} else  { //($status=='P')
				$displayStatus	=	"Pending";
			}
			$supplierId	= $pr[3];
			# Update PO Revise
			$setReviseNeed = $supplierstockObj->updatePOReviseNeed($purchaseOrderId, 'Y');
			
	?>
	<tr>
		<td width="40" align="center">
			<input type="checkbox"  class="chkBox" name="poMainId_<?=$j;?>" id="poMainId_<?=$j;?>" value="<?=$purchaseOrderId;?>" >
			<input type="hidden" name="supplierId_<?=$j;?>" id="supplierId_<?=$j;?>" value="<?=$supplierId;?>" >
			<input type="hidden" name="hidStatus_<?=$j;?>" id="hidStatus_<?=$j;?>" value="<?=$status;?>" >
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poNumber;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus;?></td>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidReviseRowCount" id="hidReviseRowCount" value="<?=$j?>" >	
	<?
		} else 	{
	?>
											<tr>
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
								
<tr>	
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" align="center">
				<tr>
				<td>
						<? if($edit==true){?>
							<input type="submit" value=" Revise PO " class="button"  name="cmdRevisePOUpdate" onclick="return validateRevisePO();">
						<? }?>
				</td>
			</tr>
			</table>
			</td>
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
<? }?>

<input type="hidden" name="priceModified" id="priceModified">
<input type="hidden" name="scheduleModified" id="scheduleModified">
	</table>
	</form>
	<?php
		 if ($addMode!="" || $editMode!="") {
	?>
	<SCRIPT language="JavaScript">
		xajax_getSupplierRec('<?=$selSupplierId?>', '<?=$selStockId?>', '<?=$supplierRateList?>','<?=$mode?>','<?=$editSupplierStockId?>');
	</SCRIPT>
	<?
		}
	?>
	<? if ($addMode!="" || $editMode!="") {?>
		<script language="JavaScript">
			window.onLoad = callFn();
			function callFn()
			{		
				hidRowOfOneItemPrice();
				displaySupplierStockUnitPrice();
			}		
		</script>
	<? }?>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
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
			inputField  : "newstartDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "newstartDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>