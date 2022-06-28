<?php
	require("include/include.php");	
	require_once("lib/purchaseorder_ajax.php");
	//require_once ('components/base/DocumentationInstructions_model.php');
	//$docInstructions_m = new DocumentationInstructions_model();
	ob_start();		
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;	
	$selection 	= "?pageNo=".$p["pageNo"]."&selFilterStkType=".$p["selFilterStkType"];
	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
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
	$getfirstrecord=$phyStkEntryPackingObj->getPhysicalStockMaxdate() ;
	$getfirstrec= dateFormat($getfirstrecord[0]);
	//echo "<pre>".print_r($p)."</pre>";
	# Add New
	//echo $p["cmdAddNew"];
	if ($p["cmdAddNew"]!="") $addMode = true;
	#List All Fishes
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
				
		$editMode		= true;			
		$purchaseOrderRec	= $phyStkEntryPackingObj->find($editId);		
		$mainId1 		= $purchaseOrderRec[0];
	
	}	

 
	if ($addMode || $editMode) { 
		//$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();
		#List All Freezing Stage Record
			//$freezingStageRecords	= $freezingstageObj->fetchAllRecords();
		#List All EU Code Records
			//$euCodeRecords		= $eucodeObj->fetchAllRecords();
		#List All Brand Records
			//$brandRecs		= $brandObj->fetchAllRecords();		
		#List All Customer Records
			//$customerRecords	= $customerObj->fetchAllRecords();
			$customerRecords	= $customerObj->fetchAllRecordsActiveCustomer();
		
		# List All Country
			//$countryMasterRecs	= $countryMasterObj->fetchAllRecords(); 
			$countryMasterRecs	= $countryMasterObj->fetchAllRecordsActivecountry();
			
		# Get Invoice Type Recs
			//$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecords();
			$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecordsActiveinvoice();

		# List Carriage Mode Recs
			//$carriageModeRecs = $carriageModeObj->fetchAllRecords();	
			$carriageModeRecs = $carriageModeObj->fetchAllRecordsActivecarriagemode();

			$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();
		
		#List All Freezing Stage Record
			$freezingStageRecords	= $freezingstageObj->fetchAllRecordsActivefreezingstage();
		
		#List All EU Code Records
			$euCodeRecords		= $eucodeObj->fetchAllRecordsActiveEucode();
			//echo "hui";
		#List All Brand Records
			$brandRecs		= $brandObj->fetchAllRecordsActivebrand();	

		# Get All Currency Recs
			$currencyRecs	= $usdvalueObj->getCYRecs();		
			$brandRecs     = $purchaseorderObj->getBrandRecords($selCustomerId);
			$poRawItemRecs = $phyStkEntryPackingObj->fetchAllPOItem($mainId1);	
			$maxdate=$phyStkEntryPackingObj->getMaxDate();
			$maximumdt= dateFormat($maxdate[0]);
			$nextalloweddate=$phyStkEntryPackingObj->getAllowedDate();	
			$nextalloweddt= dateFormat($nextalloweddate[0]);
		//echo "hii";			
	}
			$maxdate=$phyStkEntryPackingObj->getMaxDate() ;
			$maximumdt= dateFormat($maxdate[0]);
			$nextalloweddate=$phyStkEntryPackingObj->getAllowedDate() ;
			$nextalloweddt= dateFormat($nextalloweddate[0]);

	
	#Cancel
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	if ($p["selStkType"]!="")	$selStkType = $p["selStkType"];
	if ($p["selDate"]!="")		$selDate	= $p["selDate"];	

	# Add a Product
	if ($p["cmdAdd"]!="") {

	#read rowcount.
		$rowcount=$p["hidTableRowCount"];

	if ( $rowcount <= 0 ) return;
		$selDate	= mysqlDateFormat($p["selDate"]);
		//Vari true cond li>0 rem else error
		$phyStkEntry=$phyStkEntryPackingObj->addMainRecord($selDate, $userId);
		if ($phyStkEntry)
		{
		$mainId = $databaseConnect->getLastInsertedId();
		if ($mainId >0)
			{
				$frozenMain=""; $frozenMainRm=""; $rmlot_idPrev="";
				
					for($i=0 ; $i < $rowcount; $i++)
						{
							$fish_id=$p["selFish_".$i];
							$processcode_id=$p["selProcessCode_".$i];
							$freezing_stage=$p["selFreezingStage_".$i];
							$frozencode_id=$p["selFrozenCode_".$i];
							$mcpacking_id=$p["selMCPacking_".$i];
							$grade_id=$p["selGrade_".$i];
							$rmlot_id=$p["selRMLotID_".$i];
							$num_mc_used=$p["numMC_".$i];
							$num_ls_used=$p["numLS_".$i];
							
							$phyStkEntryPackingObj->addPhysicalStockEntries($mainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$rmlot_id,$num_mc_used,$num_ls_used);
							if($frozenMain=="" && $rmlot_id=="")
							{
								$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$mainId);
								$dailymainId = $databaseConnect->getLastInsertedId();
								$frozenMain=$dailymainId;
								
							}
							elseif($rmlot_id!="" && $rmlot_id!=$rmlot_idPrev)
							{
								$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmainRmLot($selDate,$rmlot_id,$userId,$mainId);
								$frozenMainRm = $databaseConnect->getLastInsertedId();
								
							}
							if($rmlot_id=="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							}
							elseif($rmlot_id!="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntriesRmLotId($frozenMainRm,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							
							}
								if ($dailyentrymainId && $rmlot_id=="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}
								elseif ($dailyentrymainId && $rmlot_id!="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntriesRmLotId($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}
								else
								{
									$errDel="$msg_failPhysicalStockEntry";
								}
									$getfirstrecord=$phyStkEntryPackingObj->getPhysicalStockMaxdate() ;
									$getfirstrec= dateFormat($getfirstrecord[0]);
							$rmlot_idPrev=$rmlot_id;
						}
				
				
				/*$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$mainId);
				$dailymainId = $databaseConnect->getLastInsertedId();
						for($i=0 ; $i < $rowcount; $i++)
						{
							$fish_id=$p["selFish_".$i];
							$processcode_id=$p["selProcessCode_".$i];
							$freezing_stage=$p["selFreezingStage_".$i];
							$frozencode_id=$p["selFrozenCode_".$i];
							$mcpacking_id=$p["selMCPacking_".$i];
							$grade_id=$p["selGrade_".$i];
							$rmlot_id=$p["selRMLotID_".$i];
							$num_mc_used=$p["numMC_".$i];
							$num_ls_used=$p["numLS_".$i];
							$phyStkEntryPackingObj->addPhysicalStockEntries($mainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$rmlot_id,$num_mc_used,$num_ls_used);
							if($rmlot_id=="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							}
							elseif($rmlot_id!="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntriesRmLotId($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							
							}
								if ($dailyentrymainId && $rmlot_id=="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}
								elseif ($dailyentrymainId && $rmlot_id!="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntriesRmLotId($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}
								else
								{
									$errDel="$msg_failPhysicalStockEntry";
								}
									$getfirstrecord=$phyStkEntryPackingObj->getPhysicalStockMaxdate() ;
									$getfirstrec= dateFormat($getfirstrecord[0]);
								}

						*/
						
						}
							else
						{
								$errDel="$msg_failPhysicalStockMain";
								
								
						}
					}			
		}
	# Edit 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$physicalStockEntryRec	=	$phyStkEntryPackingObj->find($editId);		
		$editPhysicalStockRecId	= $physicalStockEntryRec[0];
		$selDate		= dateFormat($physicalStockEntryRec[1]);
		$disableField		= "disabled";
	}
	#Update Record
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveChangeBottom"]!="") {
		//echo "xxxxxxxxxxxxx";
		$physicalStockRecId = $p["hidPhysicalStockRecId"];
		//echo $physicalStockRecId;
		//$physicalStockRecId=1;
		$selDate	= mysqlDateFormat($p["selDate"]);		
		$rowCount	= $p["hidTableRowCount"];
		//$rowCount=208;
	//echo "RC---$rowCount";
		# Check for existing rec
		$recExist	= $phyStkEntryPackingObj->chkRecExist($selDate, $physicalStockRecId);
		//echo "ppppp---$physicalStockRecId***pppp";
		
		if ($selDate!="" && !$recExist) {
		 //echo "ppppp---$physicalStockRecId***pppp";
			$physicalStockEntryRecUptd = $phyStkEntryPackingObj->updatePhysicalStock($physicalStockRecId, $selDate);
			$getfirstrecord=$phyStkEntryPackingObj->getPhysicalStockMaxdate() ;
			$getfirstrec= dateFormat($getfirstrecord[0]);
		
		}
		
				$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainid($physicalStockRecId);
				$getNo=sizeof($getdailyfrozenid);
				if($getNo>0)
				{
					$dailymainIdmn=$getdailyfrozenid[0];
					$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryid($getdailyfrozenid[0]);				
					$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
					foreach ($getdailyfrozenentryid as $dailyfrozen) {
						$dailyfrozenId = $dailyfrozen[0];	
							//echo "kkkk<br>";
						$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGrade($dailyfrozenId);
					}
					$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntry($getdailyfrozenid[0]);
					$deletedailyfrozenmain=$phyStkEntryPackingObj->deletedailyfrozenphysicalid($getdailyfrozenid[0]);
				}
					
				$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainidRmLotIDAll($physicalStockRecId);
				$getNo=sizeof($getdailyfrozenid);
					//echo "hii";
					//die();
				if($getNo>0)
				{
					foreach($getdailyfrozenid as $dailymainIdVal)
					{
						$dailymainId=$dailymainIdVal[0];
						$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryidRmLotID($dailymainId);				
						$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
						foreach ($getdailyfrozenentryid as $dailyfrozen) {
							$dailyfrozenId = $dailyfrozen[0];	
								//echo "kkkk<br>";
							$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGradeRMlotid($dailyfrozenId);
						}
						$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntryRMlotID($dailymainId);
							
						$deletedailyfrozenmain=$phyStkEntryPackingObj->deletedailyfrozenphysicalidRmLot($dailymainId);
					}
				
				}
				
		
		$frozenMain=""; $frozenMainRm=""; $rmlot_idPrev="";
		
			for ($i=0; $i<$rowCount; $i++) {
			//echo "cnt=".$i;
					//echo "ppppp---$physicalStockRecId***pppp";
				$status 	= $p["status_".$i];						
				$hidId=$p["hidid_".$i];
				if ($status!='N'){
				//if ($status!='N' && $hidId!="") {
							//if ($hidId!="")
							//{
				$recExistphyEntry= $phyStkEntryPackingObj->chkRecExistphyEntry($hidId);		
				$fish_id=$p["selFish_".$i];
				$processcode_id=$p["selProcessCode_".$i];
				$freezing_stage=$p["selFreezingStage_".$i];
				$frozencode_id=$p["selFrozenCode_".$i];
				$mcpacking_id=$p["selMCPacking_".$i];
				$grade_id=$p["selGrade_".$i];
				$rmlot_id=$p["selRMLotID_".$i];
				$num_mc_used=$p["numMC_".$i];
				$num_ls_used=$p["numLS_".$i];
				$selPreviousRMLotID=$p["selPreviousRMLotID_".$i];			
							
				if($recExistphyEntry!="" && $hidId!="")
				{
				//echo "hii";
					$physicalStockEntryRecIns = $phyStkEntryPackingObj->updatePhysicalStockEntries($hidId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id,$grade_id,$rmlot_id,$num_mc_used,$num_ls_used);
				}
				elseif($hidId=="")
				{
				//echo "hui";
					$physicalStockEntryRecIns = $phyStkEntryPackingObj->addPhysicalStockEntries($physicalStockRecId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$rmlot_id,$num_mc_used,$num_ls_used);
				}
				
				
					if($frozenMain=="" && $rmlot_id=="")
							{
								$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$physicalStockRecId);
								$dailymainId = $databaseConnect->getLastInsertedId();
								$frozenMain=$dailymainId;
								
							}
							elseif($rmlot_id!="" && $rmlot_id!=$rmlot_idPrev)
							{
								$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmainRmLot($selDate,$rmlot_id,$userId,$physicalStockRecId);
								$frozenMainRm = $databaseConnect->getLastInsertedId();
								
							}
							if($rmlot_id=="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							}
							elseif($rmlot_id!="")
							{
								$phyStkEntryPackingObj->adddailyfrozenEntriesRmLotId($frozenMainRm,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
								$dailyentrymainId = $databaseConnect->getLastInsertedId();
							
							}
								if ($dailyentrymainId && $rmlot_id=="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}
								elseif ($dailyentrymainId && $rmlot_id!="")
								{
									$phyStkEntryPackingObj->adddailyFrozenGradeEntriesRmLotId($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
								}				 
					
					
					
					
					
				$rmlot_idPrev=$rmlot_id;
					
					
				/*if($rmlot_id=="")
				{
					$phyStkEntryPackingObj->updatedailyPhysicalStock($physicalStockRecId, $selDate);
					
					$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainid($physicalStockRecId,$selDate);
					$getNo=sizeof($getdailyfrozenid);
					
					if($getNo>0)
					{
						$dailymainIdmn=$getdailyfrozenid[0];
						$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryid($getdailyfrozenid[0]);				
						$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
						foreach ($getdailyfrozenentryid as $dailyfrozen) {
							$dailyfrozenId = $dailyfrozen[0];	
							//echo "kkkk<br>";
							$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGrade($dailyfrozenId);
						}
						$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntry($getdailyfrozenid[0]);
					} 
					else {
						$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$physicalStockRecId);
						$dailymainIdmn = $databaseConnect->getLastInsertedId();
					}
				
					$phyStkEntryPackingObj->adddailyfrozenEntries($dailymainIdmn,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
					$dailyentrymainId = $databaseConnect->getLastInsertedId();
					$phyStkEntryPackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
				
				}
				else
				{
				//if on editing the already existing rm lotid is changed to new rm lotid  how can we get the details using rm lot id and id.
				//==> previous rm lotid is saving and if new rm lotid is added the edit the frozen main table using the date,physicalStockRecId and previous rm lotid ==> get the getdailyfrozenid and update that with the new rm lotid.
					$phyStkEntryPackingObj->updatedailyRmLotPhysicalStock($physicalStockRecId, $selDate,$rmlot_id,$selPreviousRMLotID);
					$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainidRmLotID($physicalStockRecId,$selDate,$rmlot_id);
					$getNo=sizeof($getdailyfrozenid);
					//echo "hii";
					//die();
					if($getNo>0)
					{
						$dailymainId=$getdailyfrozenid[0];
						$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryidRmLotID($getdailyfrozenid[0]);				
						$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
						foreach ($getdailyfrozenentryid as $dailyfrozen) {
							$dailyfrozenId = $dailyfrozen[0];	
							//echo "kkkk<br>";
							$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGradeRMlotid($dailyfrozenId);
						}
						$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntryRMlotID($getdailyfrozenid[0]);
					} 
					else {
						$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmainRmLot($selDate, $userId,$physicalStockRecId);
						$dailymainId = $databaseConnect->getLastInsertedId();
					}
					
					$phyStkEntryPackingObj->adddailyfrozenEntriesRmLotId($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
					$dailyentrymainId = $databaseConnect->getLastInsertedId();
					$phyStkEntryPackingObj->adddailyFrozenGradeEntriesRmLotId($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
				}*/
			
		} // Status Check Ends here
		else if ($status=='N' && $hidId!="") {
			$delPORawEntry = $phyStkEntryPackingObj->deletePhysicalStockEntries($hidId);
		}

		}
		
		//die();
		
		
		
		
	 /*if ($selDate!="" && !$recExist) {
		 //echo "ppppp---$physicalStockRecId***pppp";
			$physicalStockEntryRecUptd = $phyStkEntryPackingObj->updatePhysicalStock($physicalStockRecId, $selDate);
			$physicalStockEntrydailyRecUptd = $phyStkEntryPackingObj->updatedailyPhysicalStock($physicalStockRecId, $selDate);
			$getfirstrecord=$phyStkEntryPackingObj->getPhysicalStockMaxdate() ;
			$getfirstrec= dateFormat($getfirstrecord[0]);
		
		}
	#deleting records
				//$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainid($physicalStockRecId);
				$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainid($physicalStockRecId,$selDate);
				$getNo=sizeof($getdailyfrozenid);
				//echo "ppppp---$physicalStockRecId***pppp";
				//if ($getdailyfrozenid!=""){
				if ($getNo>0){
					$dailymainId=$getdailyfrozenid[0];
					$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryid($getdailyfrozenid[0]);				
					$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
					foreach ($getdailyfrozenentryid as $dailyfrozen) {
						$dailyfrozenId = $dailyfrozen[0];	
						//echo "kkkk<br>";
						$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGrade($dailyfrozenId);
					}
					$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntry($getdailyfrozenid[0]);
				} else {
					$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$physicalStockRecId);
					$dailymainId = $databaseConnect->getLastInsertedId();
				}
				//$phyStkEntryPackingObj->addPhysicalStkdailyFrozenmain($selDate, $userId,$physicalStockRecId);
				//$dailymainId = $databaseConnect->getLastInsertedId();
			//if ($physicalStockEntryRecUptd) {
				for ($i=0; $i<$rowCount; $i++) {
					//echo "ppppp---$physicalStockRecId***pppp";
				$status 	= $p["status_".$i];						
				$hidId=$p["hidid_".$i];
						if ($status!='N' && $hidId!="") {
							//if ($hidId!="")
							//{
								$recExistphyEntry= $phyStkEntryPackingObj->chkRecExistphyEntry($hidId);		
								$fish_id=$p["selFish_".$i];
								$processcode_id=$p["selProcessCode_".$i];
								$freezing_stage=$p["selFreezingStage_".$i];
								$frozencode_id=$p["selFrozenCode_".$i];
								$mcpacking_id=$p["selMCPacking_".$i];
								$grade_id=$p["selGrade_".$i];
								$num_mc_used=$p["numMC_".$i];
								$num_ls_used=$p["numLS_".$i];
								if($recExistphyEntry)
								{
									$physicalStockEntryRecIns = $phyStkEntryPackingObj->updatePhysicalStockEntries($hidId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id,$grade_id,$num_mc_used,$num_ls_used);
								}
								else
								{

									$physicalStockEntryRecIns = $phyStkEntryPackingObj-> addPhysicalStockEntries($physicalStockRecId,$fish_id,$processcode_id, $freezing_stage, $frozencode_id, $mcpacking_id, $grade_id,$num_mc_used,$num_ls_used);
								}
			
			//$phyStkEntryPackingObj->adddailyfrozenEntries($getdailyfrozenid[0],$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
			$phyStkEntryPackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processcode_id,$freezing_stage, $frozencode_id, $mcpacking_id);
			$dailyentrymainId = $databaseConnect->getLastInsertedId();
			$phyStkEntryPackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$grade_id,$num_mc_used,$num_ls_used);
		} // Status Check Ends here
		else if ($status=='N' && $hidId!="") {
			$delPORawEntry = $phyStkEntryPackingObj->deletePhysicalStockEntries($hidId);
		}

		}
		*/
		//}
			$maxdate=$phyStkEntryPackingObj->getMaxDate();
			$maximumdt= dateFormat($maxdate[0]);
			$nextalloweddate=$phyStkEntryPackingObj->getAllowedDate();	
			$nextalloweddt= dateFormat($nextalloweddate[0]);		
	}
			$dailyfrozenusedstatus=$phyStkEntryPackingObj->getDailyFrozenusedStatus();
			$dailyfrozenusedsts=$dailyfrozenusedstatus[4];
				 if ($dailyfrozenusedsts==1)
				 {
					 $disabled="disabled";
					 $disMessage="Started using this Physical Stock Entries in Daily Frozen Packing and You Cannot do further edit or delete.";
				}

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];

		for ($i=1; $i<=$rowCount; $i++) {
			$physicalStockRecId	=	$p["delId_".$i];
			if ($physicalStockRecId!="" ) {		
				# Delete Physical Stk Entries (entry Table)
				$deletePhysicalStkEntries = $phyStkEntryPackingObj->delPhysicalStockEntries($physicalStockRecId);

				# Main Table Rec Del		
				$physicalStockEntryRecDel = $phyStkEntryPackingObj->deletePhysicalStock($physicalStockRecId);
				#deleting records
				
				$getdailyfrozenid=$phyStkEntryPackingObj->getDailyFrozenMainid($physicalStockRecId);
				if(sizeof($getdailyfrozenid)>0)
				{
					$delmainentrydailyfrozen=$phyStkEntryPackingObj->deletedailyfrozenphysicalid($getdailyfrozenid[0]);
					$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryid($getdailyfrozenid[0]);
					
					$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
					foreach ($getdailyfrozenentryid as $dailyfrozen) {
					$dailyfrozenId = $dailyfrozen[0];
					
					$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGrade($dailyfrozenId);
					}
					$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntry($getdailyfrozenid[0]);
				}
				$getdailyfrozenidRm=$phyStkEntryPackingObj->getDailyFrozenMainidRmLotIDOnDel($physicalStockRecId);
				
				if(sizeof($getdailyfrozenidRm)>0)
				{
					foreach($getdailyfrozenidRm as $getdailyfrozen) 
					{
					$delmainentrydailyfrozen=$phyStkEntryPackingObj->deletedailyfrozenphysicalidRmLot($getdailyfrozen[0]);
					$getdailyfrozenentryid=$phyStkEntryPackingObj->getDailyFrozenEntryidRmLotID($getdailyfrozen[0]);
					
					$getdailyfrozenentryidItemSize = sizeof($getdailyfrozenentryid);		
					foreach ($getdailyfrozenentryid as $dailyfrozen) {
					$dailyfrozenId = $dailyfrozen[0];
					
					$deletedailyfrozengrade=$phyStkEntryPackingObj->deleteDailyFrozenGradeRMlotid($dailyfrozenId);
					}
					$deletedailyfrozenentry=$phyStkEntryPackingObj->deleteDailyFrozenEntryRMlotID($getdailyfrozen[0]);
					}
				}
				//die();
			}
		}
		if ($physicalStockEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPhyStkEntryPacking);
			$sessObj->createSession("nextPage",$url_afterDelPhyStkEntryPacking.$selection);
		} else {
			$errDel	=	$msg_failDelPhyStkEntryPacking;
		}
		$physicalStockEntryRecDel	=	false;

		 if ($dailyfrozenusedsts==1)
				 {
					 $disMessage="Started using this Physical Stock Entries in Daily Frozen Packing and You Cannot do further edit or delete.";
					 $disabled="disabled";
				}
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 

	//echo "kkkkkkkkkkkkkkkkk$offset";
	## ----------------- Pagination Settings I End ------------	

	
	if ($g["selFilterStkType"]!="") $selFilterStkType = $g["selFilterStkType"];
	else 				$selFilterStkType = $p["selFilterStkType"];
	
	# List all Records
	$physicalStockRecords 		= $phyStkEntryPackingObj->fetchAllPagingRecords($offset, $limit, $selFilterStkType);
	$physicalStockRecordSize   	= sizeof($physicalStockRecords);

	//echo $physicalStockRecordSize;

	## -------------- Pagination Settings II -------------------
	// Fetch All Records
	$fetchPhysicalStockRecords = $phyStkEntryPackingObj->fetchAllRecords($selFilterStkType);
	$numrows	=  sizeof($fetchPhysicalStockRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ingredient
	if ($addMode || $editMode) {	
		$frozenPackingRecords = $frozenpackingObj->fetchAllRecords();
	}
	
	if ($editMode) $heading	= $label_editPhyStkEntryPacking;
	else	       $heading	= $label_addPhyStkEntryPacking;


	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/PhyStkEntryPacking.js"; // For Printing JS in Head section
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	require("template/topLeftNav.php");
	?>

<form name="frmPhyStkEntryPacking" action="PhyStkEntryPacking.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?php
		if ($disMessage) {
	?>
		<tr> 
		<td height="10" align="center" class="listing-item" style="color:Maroon;">
			<strong><?php echo $disMessage;?></strong>
		</td>
	</tr>
	<?php }?>
	
		<?
			if( ($editMode || $addMode) ) {
		?>
		
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);"  <?php echo $disabled;?> >
												</td>
												
												<?} else{?>												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);">
												</td>

												<?}?>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);"  <?php echo $disabled;?> >	
												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);">
												</td>

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
			}
			
			# Listing Fish Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Physical Stock Entry (Packing)";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">								
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								 
								require("template/rbTop.php");
								
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">								
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);"  <?php echo $disabled;?> >	
											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);">
												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidFishId" value="<?=$fishId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td colspan="2"  >
													<table width="50%" align="center">
													<tr>
													<td></td>
													</tr>
														<tr>
														 <td class="fieldName">Date:</td>
														 <td class="listing-item">
														<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" />
														<?php 
														$nextalloweddt1 = ($addMode)?$maximumdt:$nextalloweddt;
														?>
														<input name="allowDate" type="hidden" id="allowDate" value="<?=$nextalloweddt1?>" size="9" autoComplete="off" />
														</td>
														</tr>
														</table>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="padding-left:10px; padding-right:10px;">
													<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPOItem1" border=0>
															<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head">Sr.<br>No</td> 
														<td class="listing-head">Fish</td>
														<td class="listing-head">Process Code</td>		
														<td class="listing-head">Freezing Stage</td>
														<td class="listing-head">Frozen Code</td>	
														<td class="listing-head">MC Pkg</td>
														<td class="listing-head">Grade</td>
														<td class="listing-head">RM Lot ID</td>
														<td class="listing-head">No of MC</td>
														<td class="listing-head">No of LS</td>
														<td>&nbsp;</td>
																</tr>
											<?php
												// When Edit Mode Products are loading from Top function
												$totalAmount = 0;
												$k = 0;
												$rawItemSize = sizeof($poRawItemRecs);
												$totalNumMC=0;
												$totalNumLS=0;
												foreach ($poRawItemRecs as $rec) {
													$poEntryId = $rec[0];
													//echo "$k----$poEntryId";
													//echo "<br>";
													$selFishId = $rec[1];
													$selProcessCodeId = $rec[2];
													$selGradeId	  = $rec[3];
													$selFreezingStageId = $rec[4];
													$selFrozenCodeId  = $rec[5];
													$selMCPackingId	  = $rec[6];
													$numMC		= $rec[7];
													$numLS		= $rec[8];
													$selrmId=$rec[9];
													$rmLotRecs = $phyStkEntryPackingObj->getRmLotID($fishId,$processCodeId,$gradeId);
													# PC Recs
													$pcRecs		= $processcodeObj->getProcessCodeRecs($selFishId);
													
													# Grade Recs
													$gradeRecs	= $purchaseorderObj->getFrozenGradeRecs($selProcessCodeId);

													# Filled Wt
													list($filledWt, $declaredWt, $frznCodeUnit) = $purchaseorderObj->getFrznPkgFilledWt($selFrozenCodeId);

													# Get Num of Packs
													$numPacks  = $purchaseorderObj->numOfPacks($selMCPackingId);

													# Get Num of Available MC
													$availableMC	= $purchaseorderObj->chkFrznPkngMCExist($mainId, $selFishId, $selProcessCodeId, $selGradeId);

													# QEL Wise Recs
													$frznCodeRecs = $purchaseorderObj->qelFrzncode($selFishId, $selProcessCodeId);
											
													# QEL MC Pkg
													$mcPkgRecs = $purchaseorderObj->qelMCPkg($selFrozenCodeId);

													$selPrdWt = ($wtType=='NW')?$declaredWt:$filledWt;
													$selPrdWt = ($selUnit==2 && $frznCodeUnit=='Kg')?number_format((KG2LBS*$selPrdWt),3,'.',''):$selPrdWt;
													$prdWt	  = $selPrdWt*$numPacks*$numMC;

												?>
											<tr align="center" class="whiteRow" id="row_<?=$k?>">
											  <td align="center" id="srNo_<?=$k?>" class="listing-item">
											 
											   <?=$k+1?> 
											  </td>
											  <td align="center" class="listing-item">
												<input type="hidden" style="text-align: right;"  value="<?=$poEntryId?>" size="6" id="hidid_<?=$k?>" name="hidid_<?=$k?>"/>
												<select id="selFish_<?=$k?>" name="selFish_<?=$k?>" onchange="xajax_getProcessCodes(document.getElementById('selFish_<?=$k?>').value, '<?=$k?>', ''); xajax_getFrznCodes('<?=$k?>', document.getElementById('selFish_<?=$k?>').value, '', '');" >
												<option value="">--Select--</option>
												<?php
												if (sizeof($fishMasterRecords)>0) {	
													foreach($fishMasterRecords as $fr) {
														$fishId		= $fr[0];
														$fishName	= stripSlash($fr[1]);
														$selFR = ($selFishId==$fishId)?"selected":"";
												?>
												<option value="<?=$fishId?>" <?=$selFR?>><?=$fishName?></option>	
												<?php }
													}
												?>
											</select>
											  </td>
											   <td align="center" class="listing-item">
												<select id="selProcessCode_<?=$k?>" name="selProcessCode_<?=$k?>" onchange="xajax_getGradeRecs(document.getElementById('selProcessCode_<?=$k?>').value, '<?=$k?>', ''); xajax_getFrznCodes('<?=$k?>', document.getElementById('selFish_<?=$k?>').value, document.getElementById('selProcessCode_<?=$k?>').value, '');" >
												<?php
												if (!sizeof($pcRecs)) {
												?>
												<option value="">-- Select --</option>
												<?php
												}
												?>
												<?php
													foreach ($pcRecs as $pcrId=>$pCode) {
														$selPC = ($selProcessCodeId==$pcrId)?"selected":"";
												?>
													<option value="<?=$pcrId?>" <?=$selPC?>><?=$pCode?></option>
												<?php
													}
												?>
											</select>
											  </td>
											  <td nowrap="" align="center" class="listing-item">
												<select id="selFreezingStage_<?=$k?>" name="selFreezingStage_<?=$k?>">
												<option value="">--Select--</option>
												<?php
												if (sizeof($freezingStageRecords)>0) {	
													foreach($freezingStageRecords as $fsr) {
														$freezingStageId	= $fsr[0];
														$freezingStageCode	= stripSlash($fsr[1]);				
														$selFrzngStage = ($selFreezingStageId==$freezingStageId)?"selected":""; 
												?>	
													<option value="<?=$freezingStageId?>" <?=$selFrzngStage?>><?=$freezingStageCode?></option>
												<?php
														}
													}
												?>
											</select>
											  </td>
											  <td nowrap="" align="center" class="listing-item">
												<select id="selFrozenCode_<?=$k?>" name="selFrozenCode_<?=$k?>" onchange="xajax_getFilledWt(document.getElementById('selFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('selFrozenCode_<?=$k?>').value, '');" >
												<?php		
												if (sizeof($frznCodeRecs)>0) {	
													 foreach($frznCodeRecs as $frozenPackingId=>$frozenPackingCode) {
														$selFrznPkg = ($selFrozenCodeId==$frozenPackingId)?"selected":"";
												?>	
													<option value="<?=$frozenPackingId?>" <?=$selFrznPkg?>><?=stripslashes($frozenPackingCode)?></option>	
												<?php
														}
												} else {
												?>
												<option value="">-- Select --</option>
												<?php
												}
												?>	
												</select>
											  </td>
											  <td nowrap align="center" class="listing-item">
												<select onchange="xajax_getNumMC(document.getElementById('selMCPacking_<?=$k?>').value, '<?=$k?>');" id="selMCPacking_<?=$k?>" name="selMCPacking_<?=$k?>">
												<?php
												if (sizeof($mcPkgRecs)>0) {	
													 foreach($mcPkgRecs as $mcpackingId=>$mcpackingCode) {
														$selMCPkg = ($selMCPackingId==$mcpackingId)?"selected":"";
												?>	
													<option value="<?=$mcpackingId?>" <?=$selMCPkg?>><?=$mcpackingCode?></option>	
												<?php
														}
													} else {
												?>
												<option value="">--Select--</option>
												<?php
												}
												?>
											</select>
											  </td>
											   <td nowrap="" align="center" class="listing-item">
												<select id="selGrade_<?=$k?>" name="selGrade_<?=$k?>" onchange="xajax_getRmLotId(document.getElementById('selFish_<?=$k?>').value,document.getElementById('selProcessCode_<?=$k?>').value,document.getElementById('selGrade_<?=$k?>').value, '<?=$k?>');">
												<?php
												if (!sizeof($gradeRecs)) {
												?>
												<option value="">-- Select --</option>
												<?php
												}
												?>
												<?php
													foreach ($gradeRecs as $grdId=>$grdName) {
														$selGrade = ($selGradeId==$grdId)?"selected":""; 
												?>
													<option value="<?=$grdId?>" <?=$selGrade?>><?=$grdName?></option>
												<?php
													}
												?>
											</select>
											  </td>
											     <td nowrap="" align="center" class="listing-item">
												<select id="selRMLotID_<?=$k?>" name="selRMLotID_<?=$k?>" >
												<?php
												if (!sizeof($rmLotRecs)) {
												?>
												<option value="">-- Select --</option>
												<?php
												}
												?>
												<?php
													foreach ($rmLotRecs as $rmId=>$rmName) {
														$selrm = ($selrmId==$rmId)?"selected":""; 
												?>
													<option value="<?=$rmId?>" <?=$selrm?>><?=$rmName?></option>
												<?php
													}
												?>
											</select>
												<input type="hidden" id="selPreviousRMLotID_<?=$k?>" name="selPreviousRMLotID_<?=$k?>" value="<?=$selrmId?>"/>
											  </td>
											   <td nowrap="" align="center" class="listing-item"><?php //echo $k;?> 
												<input type="text" style="text-align: right;" onkeyup="totRowVal('<?=$k?>');" value="<?=$numMC?>" size="6" id="numMC_<?=$k?>" name="numMC_<?=$k?>" />
											  </td>
											   <td nowrap="" align="center" class="listing-item">
												<input type="text" style="text-align: right;" onkeyup="totRowVal('<?=$k?>');" value="<?=$numLS?>" size="6" id="numLS_<?=$k?>" name="numLS_<?=$k?>"/>
											  </td>
											   <td nowrap="" align="center" class="listing-item">
												<a onclick="setPOItemStatus('<?=$k?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a><input type="hidden" value="" id="status_<?=$k?>" name="status_<?=$k?>"/><input type="hidden" value="N" id="IsFromDB_<?=$k?>" name="IsFromDB_<?=$k?>"/>
											  </td>
											 
										</tr>	
										<?php
												$k++;	
													$totalNumMC=$totalNumMC+$numMC;
													$totalNumLS=$totalNumLS+$numLS;
												} // Product Loop Ends here
//echo "The value of K in edit Mode $k";
											
										?>		<tr bgcolor="#FFFFFF" align="center" id="totRowId">				

														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>	
														<td>&nbsp;</td>															
													   <td class="listing-head" align="right" style="padding-left:5px; padding-right:5px; font-size:8pt;">Total:</td>
														<td>			
															<input name="totalNumMC" type="text" id="totalNumMC" size="8" style="text-align:right;border:none;" readonly value="<?=$totalNumMC;?>" >
														</td>
														<td>			
															<input name="totalNumLS" type="text" id="totalNumLS" size="8" style="text-align:right;border:none;" readonly value="<?=$totalNumLS;?>">
														</td>
														<td>&nbsp;</td>			
														</tr>				
												</table>
												<!--  Hidden Fields-->
										<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rawItemSize;?>">
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<table>
													<tr>
															<TD style="padding-left:10px;padding-right:10px;">
																<a href="###" id='addRow' onclick="javascript:addNewItem1();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add Item(New)</a>
																&nbsp;&nbsp;
																<a href="###" id='addRow' onclick="javascript:addNewItemCpy();"  class="link1" title="Click here to copy item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add Item(Copy)</a>
																<input type="hidden" name="hidPhysicalStockRecId" value="<?=$editPhysicalStockRecId;?>">
															</TD>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChangeBottom" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);"  <?php echo $disabled;?> >	
												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhyStkEntryPacking.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhyStkEntryPacking);">
												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>
									</td>
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
			# Listing Fish Starts
		?>
	</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockRecordSize;?>);" <?php echo $disabled;?> ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhyStkEntryPacking.php?selFilterStkType=<?=$selFilterStkType?>',700,600);"><?}?></td>
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
							<table cellpadding="2"  width="20%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if ( sizeof($physicalStockRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\" class=\"link1\">$page</a> ";		
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\">>></a> ";
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
	</thead>
	<tbody>
	<?php
	foreach ($physicalStockRecords as $psr) {
		$i++;
		$physicalStockRecId	= $psr[0];		
		$selEntryDate	= dateFormat($psr[1]);
		$selStockType   = $stkTypes[$psr[2]];
		$disabled="";
		if ($selEntryDate!="$getfirstrec")
		{
		$disabled="disabled";
		}
		?>
	<tr>
		<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$physicalStockRecId;?>" class="chkBox"  <?php echo $disabled;?>></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selEntryDate;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$physicalStockRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PhyStkEntryPacking.php';" <?php echo $disabled;?>>
		</td>
		<? }?>
	</tr>
	<?
		}
	?>
<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="3" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhyStkEntryPacking.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\">>></a> ";
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
		<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockRecordSize;?>);" <?php echo $disabled;?> ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhyStkEntryPacking.php?selFilterStkType=<?=$selFilterStkType?>',700,600);"><?}?></td>
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
				<!-- Form fields end   -->
			</td>
			
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>			
	</table>
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
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
	<?php 
	if ($iFrameVal=="") { 
?>
	<!--script language="javascript">
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
	ensureInFrameset(document.frmPhyStkEntryPacking);
	//-->
<?php 
	}
?>	
<?php
if ($addMode || $editMode) {
	?>		
	<script language="JavaScript" type="text/javascript">
		// Split Row
		function addSplitRow()
		{
			splitPO('tblSplitItem', '', '', '', '', '', '', '');			
		}
	</script>
	<?php
		}
	?>
	<script language="javascript">
<?php
				if (sizeof($poRawItemRecs)>0) {

				// Set Value to Main table
			?>
				fieldId = <?=sizeof($poRawItemRecs)?>;
			<?php
				}
			?>
	

function addNewItem1()
			{		
				
				addNewPOItem1('tblPOItem1', '', '','','','','','','','','<?=$mode?>');
				//xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
			
			function addNewItemCpy()
			{
				addNewPOItem1('tblPOItem1','', '','','','','','','','C', '<?=$mode?>');
				//xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
		</script>
	<?php 
		if ($addMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			window.load = addNewItem1();
		</SCRIPT>
	<?php 
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>