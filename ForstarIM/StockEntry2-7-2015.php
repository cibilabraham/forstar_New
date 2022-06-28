    <?php
	require("include/include.php");
	require_once("lib/Stockentry_ajax.php");
	ob_start();
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
		
	$selection = "?pageNo=".$p["pageNo"]."&categoryFilter=".$p["categoryFilter"]."&subCategoryFilter=".$p["subCategoryFilter"];
	
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
	
	//$companyRecords=$stockObj->getCompanyUser($userId);
	//printr($companyRecords);
	//$plantUnitRecords=$stockObj->getUnitUser($userId);
	//$plantUnitRecords=$plantandunitObj->fetchAllRecordsPlantsActive();
	//print_r($plantUnitRecords);
	
	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") $hidEditId = $p["editId"];
	else $hidEditId = $p["hidEditId"];

	
	
	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$p["stockCode"] = "";
		$code 		= "";
		$name		= "";
		$p["stockName"]	= "";
		$selCategory	= "";
		$p["category"]	= "";
		$selSubCategory	= "";
		$p["subCategory"] = "";
		$reOrder	= "";
		$p["reorderPoint"] = "";
		$descr		= "";
		$p["description"] = "";
		$quantity	= "";
		$p["stockQuantity"] = "";
		$active		= "";
		$p["active"]	= "";
		$brand		= "";
		$p["brand"]	= "";
		$unit		= "";
		$p["unit"]	= "";
		$size		= "";
		$p["size"]	= "";
		$dimension	= "";
		$p["dimension"] = "";
		$weight		= "";
		$p["weight"]	= "";
		$color		= "";
		$p["color"]	= "";
		$made		= "";
		$p["made"]	= "";
		$hidEditId	= "";
	}
	// End

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode = true;	
	
	# Variable Assigning
	if ($p["category"]!="")    $selCategoryId	= $p["category"];
	if ($p["subCategory"]!="") $selSubCategoryId 	= $p["subCategory"];
	if ($p["reorderRequired"]!="") $reorderRequired = $p["reorderRequired"];

	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
		$selCategoryId = "";
		$selSubCategoryId = "";
	}

	#Add a stock
	if ($p["cmdAdd"]!="") {

		$stockCount=$stockObj->getCountStockCode();
		if ($stockCount >0){		
		$code 		=	$stockObj->getStockCode();
		$code=$code+1;
		} else {

		//$code 		=1;
		$code=$displayrecordObj->getStockEntrystnum();
		}

		$name		=	addSlash(trim($p["stockName"]));
		$selCategory	=	$p["category"];
		$selSubCategory	=	$p["subCategory"];		
		
		$descr		=	addSlash(trim($p["description"]));
		$quantity	=	$p["stockQuantity"];
		$active		=	$p["active"];
		
		//For ordinary Stock
		$brand		=	$p["brand"];
		$unit		=	$p["unit"];
		$size		=	$p["size"];
		$dimension	=	$p["dimension"];
		$weight		=	$p["weight"];
		$color		=	$p["color"];
		$made		=	$p["made"];

		$reorderRequired = $p["reorderRequired"];
		$reOrder	 = $p["reorderPoint"];
		$basicUnitQty	 = $p["basicUnitQty"];
		//$unitPricePer	 = $p["unitPricePer"]; 
		//$unitPricePerItem = $p["unitPricePerItem"];
		//$unitPricePerOneItem	= $p["unitPricePerOneItem"]; $unitPricePer, $unitPricePerItem, $unitPricePerOneItem,
		$minOrderUnit	= $p["minOrderUnit"];
		$minOrderQtyPerUnit	= $p["minOrderQtyPerUnit"];
		$brandType	= $p["brandType"];
		$modelNo	= $p["modelNo"];
		$dimensionLength	= $p["dimensionLength"];		
		$dimensionBreadth	= $p["dimensionBreadth"];
		$dimensionHeight	= $p["dimensionHeight"];
		$dimensionDiameter	= $p["dimensionDiameter"];
		$dimensionRadius	= $p["dimensionRadius"];
		$particularsDescription	= $p["particularsDescription"];

		// common
		$stockType	=	$p["stockType"];
		//For packing Material		
		$layer		=	$p["numLayer"];
		$carton		=	$p["carton"];
		$packingBrand	=	$p["packingBrand"];
		$packingColor	=	$p["packingColor"];
		$packingWeight	=	$p["packingWeight"];
		$packing	=	$p["packing"];
		$numColors		=	$p["numColors"];
		$packingDimension	=	$p["packingDimension"];
		$cartonWeight		=	$p["cartonWeight"];
		//$selFrozenCode	=	$p["selFrozenCode"]; // Multiple Selection
		
		$selFrozenCode	=	$p["selRawGrade"];
		//print_r($selFrozenCode);
		$packingKg	=	$p["packingKg"];

		$additionalHoldingPercent=$p["additionalHoldingPercent"];	
		$stockingPeriod 	= $p["stockingPeriod"];

		$stkGroupRowCount	= $p["hidStkGroupRowCount"];
		$tolerancelevel=$p["toleranceLevel"];
		$plantunit=$p["plantUnit"];
		
		if ($code!="" && $name!="") {
		
		$chkStatus=$stockObj->checkDuplicate($name);
		if($chkStatus)
		{

			$stockRecIns =	$stockObj->addStock($code, $name, $selCategory, $selSubCategory, $reOrder, $descr, $stockType, $brand, $unit, $quantity, $size, $dimension, $weight, $color, $made, $layer, $carton, $packingBrand, $packingColor, $packingWeight, $packing, $numColors, $packingDimension, $cartonWeight, $active, $userId, $selFrozenCode, $packingKg, $reorderRequired, $basicUnitQty, $minOrderUnit, $minOrderQtyPerUnit, $brandType, $modelNo, $dimensionLength,  $dimensionBreadth, $dimensionHeight, $dimensionDiameter, $dimensionRadius, $particularsDescription, $additionalHoldingPercent, $stockingPeriod,$tolerancelevel,$plantunit);
			//$lastId = $databaseConnect->getLastInsertedId();
			$lastStockId = $stockObj->getMaxstkId();
			$lastId=$lastStockId[0];

			$rowCountQty	= $p["hidTableRowCount2"];
			for ($i=0; $i<$rowCountQty; $i++) 
			{
				$status 	= $p["statusUnit_".$i];						
				$hidId=$p["hidid_".$i];
					if ($status!='N')
					{
						///$packingKg=$p["packingKg_".$i];
						//$packing=$p["packing_".$i];
						$companyId=$p["companyId_".$i];
						$unitId=$p["punitId_".$i];
						//echo "***-$unitId".$i;
						//echo "(((---$stockQty".$i;
						$stockQty=$p["stockQty_".$i];
						$StockEntryRecInsqty = $stockObj->addUnitStock($companyId,$unitId,$lastId);
					} // Status Check Ends here
					else if ($status=='N') {
							$delStockEntryRecIns = $stockObj->deleteUnitStock($hidId);
					}	

			}
			if ($stockRecIns) {
				#Find the Last inserted Id 
				//$stkId = $databaseConnect->getLastInsertedId();
				//$stkId=$stockObj->getStkLastIdVal();
				//$stkId=$stockObj->addStock();
				$stkIdVal=$stockObj->getMaxstkId() ;
				$stkId=$stkIdVal[0];
				//echo "The value of last inserted id is $stkId";
				# Get Dynamic Field Recs
				$stockGroupRecs = $stockGroupObj->getStockGroupRecs($selCategory, $selSubCategory);
				$k = 0;
				foreach($stockGroupRecs as $sgr) {
					$k++;
					$stkGroupEntryId = $sgr[5];
					$stkLabelName	= $sgr[7];
					$stkFieldType	= $inputTypeArr[$sgr[8]];
					$stkFieldName	= $sgr[9];
					$stkFieldDefaultValue = $sgr[10];
					$stkFieldSize	= $sgr[11];
					$stkFieldVDation = $sgr[12];

					$stkFieldValue = $p[$stkFieldName."_".$k]; 
					$stkUnitId	= $p["stkUnitId_".$k];
				
					if ($stkId!="" && $stkGroupEntryId!="" && $stkFieldValue!="") {
						$stkDynamicFieldRecIns = $stockObj->addStkGroupField($stkId, $stkGroupEntryId, $stkFieldValue, $stkUnitId);
					}
				} // Field Loop Ends here				
			} // Stock Group Adding Ends here

			//New code start
			if ($stkId!=""){
			$rowCount	= $p["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
					//echo "ppppp---$physicalStockRecId***pppp";
				$status 	= $p["status_".$i];						
				$hidId=$p["hidid_".$i];
				//if ($status!='N' && $hidId!="") {
				//if ($hidId!="")
				//{
					if ($status!='N'){
								
					$packingKg=$p["packingKg_".$i];
					$packing=$p["packing_".$i];	
					$StockEntryRecIns = $stockObj->addPackingWeightStockEntries($packingKg,$packing,$stkId);
								
					} // Status Check Ends here
				//else if ($status=='N') {
					//$delStockEntryRecIns = $stockObj->deletePackingWeightStockEntries($hidId);
				//}

				}

			}
				//New code end

		}

			if ($stockRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockEntry);
				//$sessObj->createSession("nextPage",$url_afterAddStockEntry.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockEntry;
			}
			$stockRecIns		=	false;
		}
		$hidEditId = "";
	}

	#Update a Stock Entry
	if ($p["cmdSaveChange"]!="") {
		
		$stockId	=	$p["hidStockId"];
		$code		=	addSlash(trim($p["stockCode"]));
		//$code 		=	$stockObj->getStockCode(addSlash(trim($p["stockName"])));

		$name		=	addSlash(trim($p["stockName"]));
		$selCategory	=	$p["category"];
		$selSubCategory	=	$p["subCategory"];
		$reOrder	=	$p["reorderPoint"];
		$descr		=	addSlash(trim($p["description"]));
		
		$active		=	$p["active"];
		
		//For ordinary Stock
		$brand		=	$p["brand"];
		$unit		=	$p["unit"];
		$quantity	=	$p["stockQuantity"];
		$size		=	$p["size"];
		$dimension	=	$p["dimension"];
		$weight		=	$p["weight"];
		$color		=	$p["color"];
		$made		=	$p["made"];
		
		$reorderRequired = $p["reorderRequired"];
		$basicUnitQty	 = $p["basicUnitQty"];
		//$unitPricePer	 = $p["unitPricePer"]; 
		//$unitPricePerItem = $p["unitPricePerItem"];
		//$unitPricePerOneItem	= $p["unitPricePerOneItem"];
		$minOrderUnit	= $p["minOrderUnit"];
		$minOrderQtyPerUnit	= $p["minOrderQtyPerUnit"];
		$brandType	= $p["brandType"];
		$modelNo	= $p["modelNo"];
		$dimensionLength	= $p["dimensionLength"];		
		$dimensionBreadth	= $p["dimensionBreadth"];
		$dimensionHeight	= $p["dimensionHeight"];
		$dimensionDiameter	= $p["dimensionDiameter"];
		$dimensionRadius	= $p["dimensionRadius"];
		$particularsDescription	= $p["particularsDescription"];



		$stockType	=	$p["stockType"];
		//For packing Material
		
		$layer		=	$p["numLayer"];
		$carton		=	$p["carton"];
		$packingBrand	=	$p["packingBrand"];
		$packingColor	=	$p["packingColor"];
		$packingWeight	=	$p["packingWeight"];
		$packing	=	$p["packing"];
		$numColors	=	$p["numColors"];
		$packingDimension	=	$p["packingDimension"];
		$cartonWeight		=	$p["cartonWeight"];
		$oldStockQuantity	=	$p["oldStockQuantity"]; //Actual Quantity in m_stock
		//$selFrozenCode	=	$p["selFrozenCode"]; // Multiple Selection

		$selFrozenCode	=	$p["selRawGrade"];
		//print_r($selFrozenCode);
		$packingKg	=	$p["packingKg"];

		$additionalHoldingPercent=$p["additionalHoldingPercent"];	
		$stockingPeriod 	= $p["stockingPeriod"];	
		$tolerancelevel=$p["toleranceLevel"];
		
		if ($stockId!="" && $name!="" && $code!="") {
			$stockRecUptd	= $stockObj->updateStock($stockId, $code, $name, $selCategory, $selSubCategory, $reOrder, $descr, $stockType, $brand, $unit, $quantity, $size, $dimension, $weight, $color, $made, $layer, $carton, $packingBrand, $packingColor, $packingWeight, $packing, $numColors, $packingDimension, $cartonWeight, $active, $oldStockQuantity, $selFrozenCode, $packingKg, $reorderRequired, $basicUnitQty, $minOrderUnit, $minOrderQtyPerUnit, $brandType, $modelNo, $dimensionLength,  $dimensionBreadth, $dimensionHeight, $dimensionDiameter, $dimensionRadius, $particularsDescription, $additionalHoldingPercent, $stockingPeriod,$tolerancelevel);




		$rowCount2	= $p["hidTableRowCount2"];
		for ($j=0; $j<$rowCount2; $j++) {
		//echo "j value is $j--tc---$rowCount2";


				$statusUnit 	  = $p["statusUnit_".$j];
				$stockqtyid=$p["stockqtyid_".$j];
				//$agentContactId  = $p["shipCompanyContactId_".$i];
				$companyId=$p["companyId_".$j];
				$punitId=$p["punitId_".$j];
				if ($statusUnit!='N' && $stockqtyid!=""){ 
				
					//$stkQty=$p["stockQty_".$j];	
					//echo "up--RowValue-$j-UnitId-$punitId-StkQty-$stkQty-Stkid---$stockqtyid<br>";
					//$StockEntryRecInsup = $stockObj->updateUnitStock($punitId,$stkQty,$stockqtyid);
					$StockEntryRecInsup = $stockObj->updateUnitStock($companyId,$punitId,$stockqtyid);
				} else if ($statusUnit!='N' && $stockqtyid=="") {	
										//echo "in--RowValue-$j-UnitId-$punitId-StkQty-$stkQty-Stkid---$stockqtyid<br>";
						$StockEntryRecInsst = $stockObj->addUnitStock($companyId,$punitId,$stockId);
					}else if ($statusUnit=='N' && $stockqtyid!="") {
						
					//echo "del--RowValue-$j-UnitId-$punitId-StkQty-$stkQty-Stkid---$stockqtyid<br>";
					$delShipCompanyContactRecst =  $stockObj->deleteUnitStock($stockqtyid);
				}
			} // Loop ends here

			//die();
			// code end

			if ($stockRecUptd) 
			{
				# Get Dynamic Field Recs
				$stockGroupRecs = $stockGroupObj->getStockGroupRecs($selCategory, $selSubCategory);
				$k = 0;
				foreach($stockGroupRecs as $sgr) 
				{
					$k++;
					$stkGroupEntryId = $sgr[5];
					$stkLabelName	= $sgr[7];
					$stkFieldType	= $inputTypeArr[$sgr[8]];
					$stkFieldName	= $sgr[9];
					$stkFieldDefaultValue = $sgr[10];
					$stkFieldSize	= $sgr[11];
					$stkFieldVDation = $sgr[12];

					$stkFieldValue = $p[$stkFieldName."_".$k]; 
					$stkUnitId	= $p["stkUnitId_".$k];

						list($mStkGroupEntyId, $fieldValue, $sStkUnitId) = $stockObj->getStkGroupRecs($stockId, $stkGroupEntryId);
				
						if ($stockId!="" && $stkGroupEntryId!="" && $stkFieldValue!="" && $mStkGroupEntyId=="") {
							$stkDynamicFieldRecIns = $stockObj->addStkGroupField($stockId, $stkGroupEntryId, $stkFieldValue, $stkUnitId);
						} else if ($stockId!="" && $stkGroupEntryId!="" && $stkFieldValue!="" && $mStkGroupEntyId!="") {
							$stkDynamicFieldRecUptd = $stockObj->updateStkGroupField($mStkGroupEntyId, $stkFieldValue, $stkUnitId);
						} else if ($stockId!="" && $stkGroupEntryId!="" && $stkFieldValue=="" && $mStkGroupEntyId!="") {
							$delStkDynamicFieldRec = $stockObj->deleteStkGroupField($mStkGroupEntyId);
						}
					} // Field Loop Ends here				
				} // Stock Group Adding Ends here
			}
	


			//New code start
			/*if ($stkId!=""){
			$rowCount	= $p["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
								
							$status 	= $p["status_".$i];						
							$hidId=$p["hidid_".$i];
									
											if ($status!='N'){
											
											$packingKg=$p["packingKg_".$i];
											$packing=$p["packing_".$i];	

												$StockEntryRecIns = $stockObj->addPackingWeightStockEntries($packingKg,$packing,$stkId);
											
					} // Status Check Ends here
					else if ($status=='N') {
						
					}

					}

			}*/


			$rowCount	= $p["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
				$status 	  = $p["status_".$i];
				$packingId=$p["packingweightid_".$i];
				//$agentContactId  = $p["shipCompanyContactId_".$i];
				if ($status!='N') {
					$packingKg=$p["packingKg_".$i];
					$packing=$p["packing_".$i];	
					//$role		= addSlash(trim($p["role_".$i]));
					//$contactNo	= addSlash(trim($p["contactNo_".$i]));					
					//if ($agentId!="" && $personName!="" && $agentContactId!="") {
						if ($packingId!="")
						{
							$StockEntryRecIns = $stockObj->updatePackingWeightStockEntries($packingKg,$packing,$stockId,$packingId);
						//$updateShippingCompanyContactRec = $agentMasterObj->updateShipCompanyContact($agentContactId, $personName, $designation, $role, $contactNo);
					} else if ($packingId=="") {				
						//$companyContactIns = $agentMasterObj->addCompanyContact($agentId, $personName, $designation, $role, $contactNo);
						$StockEntryRecIns = $stockObj->addPackingWeightStockEntries($packingKg,$packing,$stockId);
					}
				} // Status Checking End

				if ($status=='N' && $packingId!="") {
					$delShipCompanyContactRec =  $stockObj->deletepackingWeightrow($packingId);
				}
			} // Loop ends here

		//New code end
		if ($stockRecUptd) {
			//$sessObj->createSession("displayMsg",$msg_succStockEntryUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateStockEntry.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockEntryUpdate;
		}
		$stockRecUptd	=	false;
		$hidEditId	= "";
	}



# Variable resetting section	
	//if ($p["selFrznPkgCode"]) $selFrznPkgCode = $p["selFrznPkgCode"];

	if ($p["copyfromstock"]) $copyfromstock = $p["copyfromstock"];

	

	# Edit  a Stock
	if ($p["editId"]!="" ) {
		$addMode	= false;
		$editId		=	$p["editId"];
		$editMode	=	true;
		$stockRec	=	$stockObj->find($editId);
		$editStockId	=	$stockRec[0];
		$code		=	$stockRec[1];
		//$name		=	stripslashes($stockRec[2]);
		$name		= stripslashes(htmlspecialchars($stockRec[2]));
		
		if ($p["editSelectionChange"]=='1'||$p["category"]=="") {
			$selCategoryId	= $stockRec[3];
		} else {
			$selCategoryId	= $p["category"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["subCategory"]=="") {
			$selSubCategoryId = $stockRec[4];
		} else {
			$selSubCategoryId = $p["subCategory"];
		}

		$reorder		=	$stockRec[7];
		$stockDescr		=	stripSlash($stockRec[8]);
		
		
		$stockType		=	$stockRec[9];
		$brand			=	$stockRec[10];
		$quantity		=	$stockRec[5];
		$unit			=	$stockRec[6];
		$size			=	$stockRec[11];
		$dimension		=	$stockRec[12];
		$weight			=	$stockRec[13];
		$color			=	$stockRec[14];
		$made			=	$stockRec[15];
		$layer			=	$stockRec[16];
		$carton			=	$stockRec[17];
		$packingBrand		=	$stockRec[18];
		$packingColor		=	$stockRec[19];
		$packingWeight		=	$stockRec[20];
		$packing		=	$stockRec[21];
		$numColors		=	$stockRec[22];
		$packingDimension	=	$stockRec[23];
		$cartonWeight		=	$stockRec[24];
		
		$active			=	$stockRec[25];
		$packingKg		=	$stockRec[26];

		$reorderRequired 	= $stockRec[27];
		$basicUnitQty	 	= $stockRec[28];
		//$unitPricePer	 	= $stockRec[29];
		//$unitPricePerItem 	= $stockRec[30];
		//$unitPricePerOneItem	= $stockRec[31];
		$minOrderUnit		= $stockRec[29];
		$minOrderQtyPerUnit	= $stockRec[30];
		$brandType		= $stockRec[31];
		$modelNo		= $stockRec[32];
		$dimensionLength	= ($stockRec[33]==0)?"":$stockRec[33];
		$dimensionBreadth	= ($stockRec[34]==0)?"":$stockRec[34];
		$dimensionHeight	= ($stockRec[35]==0)?"":$stockRec[35];
		$dimensionDiameter	= ($stockRec[36]==0)?"":$stockRec[36];
		$dimensionRadius	= ($stockRec[37]==0)?"":$stockRec[37];
		$particularsDescription	= $stockRec[38];

		$additionalHoldingPercent = $stockRec[39];	
		$stockingPeriod 	  = $stockRec[40];
		$tolerancelevel 	  = $stockRec[41];
		$plantunit=$stockRec[42];
		
		#Get Sub Category
		//$subCategoryRecords	=	$subcategoryObj->filterRecords($selCategoryId);
		#Get all Unit
		//$unitRecords = $stockObj->filterUnitRecs($selSubCategoryId);
		$packingWeightRecs = $stockObj->getPackingWeightRecs($editId);
		$plantUnitRecs=$stockObj->getplantUnitRecs($editId);
		//print_r($packingWeightRecs);

		//$quickentryfrozenids=$stockObj->getFrozencode($editId);
		$selFrozRecs=$stockObj->getFrozencode($editId);
		$sfrVal="";
		foreach($selFrozRecs as $sfr)
		{
		$sfrVal.=$sfr[0]."-".$sfr[2].",";
		
		}
		$sfrVal=trim($sfrVal,",");
		//echo "8888$sfrVal88888";
		//print_r($selFrozRecs);

		$frozenPackingRecords=$stockObj->getFrozenCodeWtquickEntryEdit($packingWeightRecs);
		//print_r($selpackingWtmcPacking);
	}


	# Delete Stock
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockId	=	$p["delId_".$i];

			if ($stockId!="") {
				# Chk Rec using in other section
				$isStockUsed = $stockObj->checkRecordUsed($stockId);
				if (!$isStockUsed) {
					// Need to check the selected Category is link with any other process
					$delStkGroupRec =  $stockObj->deleteStockGroupEntryRecs($stockId);
					$stockRecDel		= $stockObj->deleteStock($stockId);
					$frozenCodeStockRecDel 	= $stockObj->deleteStock2FrozenCode($stockId);
					$packingWeightRecDel 	= $stockObj->deletepackingWeight($stockId);
					
				}
			}
		}
		if ($stockRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockEntry);
			$sessObj->createSession("nextPage",$url_afterDelStockEntry.$selection);
		} else {
			if ($isStockUsed) $errDel = $msg_failDelStockEntry. "<br>Please make sure the stock does not exist in Supplier Stock/ Create PO/ Stock Issuance/ Goods Receipt Note/ Stock Return";
			else $errDel = $msg_failDelStockEntry;
		}
		$stockRecDel	=	false;
		$hidEditId	= 	"";
	}
	
if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockId	=	$p["confirmId"];
			if ($stockId!="") {
				// Checking the selected fish is link with any other process
				$stockRecConfirm = $stockObj->updateStockconfirm($stockId);
			}

		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstockentry);
			$sessObj->createSession("nextPage",$url_afterDelStockEntry.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$stockId = $p["confirmId"];
			if ($stockId!="") {
				#Check any entries exist
				
					$stockRecConfirm = $stockObj->updateStockReleaseconfirm($stockId);
				
			}
		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstockentry);
			$sessObj->createSession("nextPage",$url_afterDelStockEntry.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}


	# Bulk Update
	if ($p["cmdBulkUpdate"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockId	= $p["hidStockId_".$i];
			$holdingPercent = trim($p["holdingPercent_".$i]);
			$hidHoldingPercent = trim($p["hidHoldingPercent_".$i]);
			$stockingPeriod = $p["stockingPeriod_".$i];
			$hidStockingPeriod = $p["hidStockingPeriod_".$i];
			if ($holdingPercent!=$hidHoldingPercent || $stockingPeriod!=$hidStockingPeriod) {	
				$updateStockRec = $stockObj->updateStockRec($stockId, $holdingPercent, $stockingPeriod);		
			}
		}

		if ($updateStockRec) {
			$sessObj->createSession("displayMsg",$msg_succStockEntryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockEntry.$selection);
		} else {
			$err = $msg_failStockEntryUpdate;
		}
		$updateStockRec	= false;
		$hidEditId = "";
	}


if ($copyfromstock){
	//echo  $copyfromstock;
    //echo "entered";
	//$name="hai";
	//$name
$stockDescr="";
$reorderPoint="";
$QuantityinStock="";
$AdditionalHoldingPercent="";
$StockingPeriod="";
	//echo $name;
$stockRec	=	$stockObj->find($copyfromstock);
		$editStockId	=	$stockRec[0];
		$code		=	$stockRec[1];
		//$name		=	stripslashes($stockRec[2]);
		$name		= stripslashes(htmlspecialchars($stockRec[2]));
		
		if ($p["editSelectionChange"]=='1'||$p["category"]=="") {
			$selCategoryId	= $stockRec[3];
		} else {
			$selCategoryId	= $p["category"];
		}
		
		if ($p["editSelectionChange"]=='1' || $p["subCategory"]=="") {
			$selSubCategoryId = $stockRec[4];
		} else {
			$selSubCategoryId = $p["subCategory"];
		}

		$reorder		=	$stockRec[7];
		$stockDescr		=	stripSlash($stockRec[8]);
		
		
		$stockType		=	$stockRec[9];
		$brand			=	$stockRec[10];
		$quantity		=	$stockRec[5];
		$unit			=	$stockRec[6];
		$size			=	$stockRec[11];
		$dimension		=	$stockRec[12];
		$weight			=	$stockRec[13];
		$color			=	$stockRec[14];
		$made			=	$stockRec[15];
		$layer			=	$stockRec[16];
		$carton			=	$stockRec[17];
		$packingBrand		=	$stockRec[18];
		$packingColor		=	$stockRec[19];
		$packingWeight		=	$stockRec[20];
		$packing		=	$stockRec[21];
		$numColors		=	$stockRec[22];
		$packingDimension	=	$stockRec[23];
		$cartonWeight		=	$stockRec[24];
		
		$active			=	$stockRec[25];
		$packingKg		=	$stockRec[26];

		$reorderRequired 	= $stockRec[27];
		$basicUnitQty	 	= $stockRec[28];
		//$unitPricePer	 	= $stockRec[29];
		//$unitPricePerItem 	= $stockRec[30];
		//$unitPricePerOneItem	= $stockRec[31];
		$minOrderUnit		= $stockRec[29];
		$minOrderQtyPerUnit	= $stockRec[30];
		$brandType		= $stockRec[31];
		$modelNo		= $stockRec[32];
		$dimensionLength	= ($stockRec[33]==0)?"":$stockRec[33];
		$dimensionBreadth	= ($stockRec[34]==0)?"":$stockRec[34];
		$dimensionHeight	= ($stockRec[35]==0)?"":$stockRec[35];
		$dimensionDiameter	= ($stockRec[36]==0)?"":$stockRec[36];
		$dimensionRadius	= ($stockRec[37]==0)?"":$stockRec[37];
		$particularsDescription	= $stockRec[38];

		$additionalHoldingPercent = $stockRec[39];	
		$stockingPeriod 	  = $stockRec[40];
		$tolerancelevel 	  = $stockRec[41];
		$plantunit=$stockRec[42];
		
		#Get Sub Category
		//$subCategoryRecords	=	$subcategoryObj->filterRecords($selCategoryId);
		#Get all Unit
		//$unitRecords = $stockObj->filterUnitRecs($selSubCategoryId);
		$packingWeightRecs = $stockObj->getPackingWeightRecs($copyfromstock);
		$plantUnitRecs=$stockObj->getplantUnitRecs($copyfromstock);
		//print_r($packingWeightRecs);

		//$quickentryfrozenids=$stockObj->getFrozencode($editId);
		$selFrozRecs=$stockObj->getFrozencode($copyfromstock);
		$sfrVal="";
		foreach($selFrozRecs as $sfr)
		{
		$sfrVal.=$sfr[0]."-".$sfr[2].",";
		
		}
		$sfrVal=trim($sfrVal,",");
		//echo "8888$sfrVal88888";
		//print_r($selFrozRecs);

		$frozenPackingRecords=$stockObj->getFrozenCodeWtquickEntryEdit($packingWeightRecs);
		//print_r($selpackingWtmcPacking);

}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["categoryFilter"]!="") $categoryFilterId = $g["categoryFilter"];
	else $categoryFilterId = $p["categoryFilter"];	
	
	# List Sub category filter records
	if ($categoryFilterId!="")
		$subCategoryFilterRecords  = $subcategoryObj->filterRecords($categoryFilterId);

	if ($g["subCategoryFilter"]!="") $subCategoryFilterId = $g["subCategoryFilter"];
	else $subCategoryFilterId = $p["subCategoryFilter"];

	# Resettting offset values
	if ($p["hidCategoryFilterId"]!=$p["categoryFilter"]) {		
		$offset = 0;
		$pageNo = 1;
		$subCategoryFilterId = "";
	}
	if ($p["hidSubCategoryFilterId"]!=$p["subCategoryFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Stocks
	$stockRecords	= $stockObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId, $subCategoryFilterId);
	$stockSize	= sizeof($stockRecords);
	$numrows	=  sizeof($stockObj->fetchAllFilterRecords($categoryFilterId, $subCategoryFilterId));


/*echo "Category Filter Id is $categoryFilterId";
echo "<br>";
echo "Sub Category Filter Id is  $subCategoryFilterId";*/
	if ($p["cmdSearch"]!="")
	{
	$stockName=$p["stockitemsearch"];
	$stockRecords	= $stockObj->fetchAllPagingRecordsSearch($offset, $limit, $categoryFilterId, $subCategoryFilterId,$stockName);
	$numrows	=  sizeof($stockObj->fetchAllFilterRecordsSearch($categoryFilterId, $subCategoryFilterId,$stockName));

	$stockSize	= sizeof($stockRecords);
	}

	## -------------- Pagination Settings II -------------------
	
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Category ;
	//$categoryRecords	=	$categoryObj->fetchAllRecords();
	$categoryRecords	=	$categoryObj->fetchAllRecordsActivecategory();

	#List all MC Packing
	$mcpackingRecords = $mcpackingObj->fetchAllRecords();
	$declaredwtRecords = $stockObj->fetchAlldeclaredWt();
	//print_r($declaredwtRecords);

	#List all Frozen Packing Code
	//if ($addMode) $frozenPackingRecords = $frozenpackingObj->fetchAllRecords();
	//else $frozenPackingRecords = $stockObj->fetchSelectedFrozenCodeRecords($editId);

	#List All Brand Records
	$brandRecords		=	$brandObj->fetchAllRecords();


	if ($selCategoryId) 	$subCategoryRecords = $subcategoryObj->filterRecords($selCategoryId);	
	if ($selSubCategoryId) 	{
		$unitRecords = $stockObj->filterUnitRecs($selSubCategoryId);
		$subCatStockType = $subcategoryObj->getSubCategoryStockType($selSubCategoryId); // Y - Carton ie P)
		$stockType	= ($subCatStockType=='Y')?"P":"O";
	}

	if ($selCategoryId && $selSubCategoryId) {
		$stockGroupRecs = $stockGroupObj->getStockGroupRecs($selCategoryId, $selSubCategoryId);
		if (!$unit && $unit==0) $unit = $stockGroupRecs[0][4];
		$inputTypeArr = array("T"=>"Text", "C"=>"Checkbox", "R"=>"Radio");
		$validationArr = array("N"=>"NO", "Y"=>"YES");
	}
		
	
	//echo "$selCategoryId"."-"."$selSubCategoryId";
	$stockcopyRecords	= $stockObj->fetchAllStockRecords($selCategoryId,$selSubCategoryId);
	if ($editMode) $heading	= $label_editStockEntry;
	else $heading	= $label_addStockEntry;
	
	if ($addMode==true) $ON_LOAD_FN = "return Hide();";
	if ($selCategoryId && $selSubCategoryId) {
		 $ON_LOAD_FN = ($stockType=='P')?"return showPacking();":"return showOrdinary();";	
	}
	//if ($editMode==true && $stockType=='P') $ON_LOAD_FN = "return showPacking();";
	//if ($editMode==true && $stockType=='O') $ON_LOAD_FN = "return showOrdinary();";
	
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	# include JS
	$ON_LOAD_PRINT_JS	= "libjs/stockentry.js";
	
	list($companyRecords,$unitRecs,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
	if($addMode)
	{
		$plantUnitRecords=$unitRecs[$defaultCompany];
	}
	//printr($companyRecords);
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmStockEntry" action="StockEntry.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>	
		</tr>
		<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Stock Entry";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?php
			if ( $editMode || $addMode) {
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('StockEntry.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateStock(document.frmStockEntry);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockEntry.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStock(document.frmStockEntry);">												</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidStockId" value="<?=$editStockId;?>">
	<tr><TD height="10"></TD></tr>
		<tr>
			  <td colspan="2" nowrap>
			  <table align="center">
				<tr>
					<TD colspan="2">
					<table>
						<TR>
							<TD valign="top">
							<?php
								$entryHead = "";
								$rbTopWidth = "";
								require("template/rbTop.php");
							?>
				<table>
				<tr>
					  <td class="fieldName" nowrap >*Name</td>
					  <td nowrap>
						<? if($addMode==true)
					{	
							$code=$p["stockCode"];
							
							
							
					}?>
					  	<INPUT TYPE="hidden" NAME="stockCode" size="15" value="<?=$code;?>" readonly="true">
						  <? if($addMode==true){ 
						if ($p[copyfromstock]!=""){
							$name="$name";
							}
							else {
						$name=$p["stockName"];
							}
					
					}
					?>
						  <input type="text" name="stockName" size="24" value="<?=$name;?>" style="width:165px;" /></td>
				</tr>
				<tr>
					  <td class="fieldName" nowrap >Description</td>
					  <td nowrap>
					 <? if ($p["description"]!="") $stockDescr=$p["description"];?>
					<textarea name="description" style="width:165px;"><?=$stockDescr;?></textarea></td>
				  </tr>
			<tr>
					  <td nowrap class="fieldName" >*Category</td>
					<td nowrap>
                <!--<select name="category" id="category" <?if ($addMode==true) {?> onchange="this.form.submit();" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <? }?> style="width:165px;">-->


				<select name="category" id="category" <?if ($addMode==true) {?> onchange="getLoading(this);" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;getLoading(this);" <? }?> style="width:165px;">
                <option value="">--select--</option>
                <?
		foreach ($categoryRecords as $cr) {
			$categoryId	= $cr[0];
			$categoryName	= stripSlash($cr[1]);
			$selected = ($selCategoryId==$categoryId)?"Selected":"";				
		?>
               <option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
                 <? }?>
              </select></td>
						</tr>
		<tr>
			<td nowrap class="fieldName">*Sub Category</td>
			<td nowrap>			
			<!--<select name="subCategory" <?if ($addMode==true) {?> onchange="this.form.submit();" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <? }?> style="width:165px;">-->

			<select name="subCategory" <?if ($addMode==true) {?> onchange="getLoading(this);" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;getLoading(this);" <? }?> style="width:165px;">



                        <option value="">--select--</option>
                        <?
			foreach ($subCategoryRecords as $scr) {				
				$subCategoryId		=	$scr[0];
				$subCategoryName	=	stripSlash($scr[2]);
				$selected = ($selSubCategoryId==$subCategoryId)?"Selected":"";	
			?>
                        <option value="<?=$subCategoryId?>" <?=$selected;?>><?=$subCategoryName;?></option>
			<? }?>
                        </select>
			</td>
		  </tr>
<tr>
<td></td><td><table><tr>
			<td nowrap class="fieldName">*Company</td>
			<td nowrap  nowrap class="fieldName">*Unit</td></tr></table></td></tr>
			</tr>
		  <!--<tr>
			<td nowrap class="fieldName">*Unit</td>
			<td nowrap>			
			<!--<select name="subCategory" <?if ($addMode==true) {?> onchange="this.form.submit();" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <? }?> style="width:165px;">-->

			<!--<select name="plantUnit" style="width:165px;">



                        <option value="">--select--</option>
                        <?
			foreach ($plantUnitRecords as $pur) {				
				$plantId		=	$pur[0];
				$plantName	=	stripSlash($pur[2]);
				$selected = ($plantId==$plantunit)?"Selected":"";	
			?>
                        <option value="<?=$plantId?>" <?=$selected;?>><?=$plantName;?></option>
			<? }?>
                        </select>
			</td>
		  </tr>-->
		  <tr><td></td><td><table id="tblPOItem2"></table></td></tr>
<tr><td>&nbsp;<input type='hidden' name="hidTableRowCount2" id="hidTableRowCount2" value="<?=$k1+1;?>"></td><td>&nbsp;<a href="###" id='addRow' onclick="javascript:addNewItem2();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:0px;vertical-align:middle;' >Add Item</a></td></tr>

		  <tr>
			<td nowrap class="fieldName">*Copy From-Stock</td>
			<td nowrap>			
			<!--<select name="copyfromstock" id="copyfromstock" <?if ($addMode==true) {?> onchange="this.form.submit();" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <? }?> style="width:165px;">-->

			<select name="copyfromstock" id="copyfromstock" <?if ($addMode==true) {?> onchange="getLoading(this);" <? } else {?> onchange="this.form.editId.value=<?=$editId?>;getLoading(this);" <? }?> style="width:165px;">
                        <option value="">--select--</option>
                        <?
			  foreach ($stockcopyRecords as $scr){
			  $stockcopyId		=	$scr[0];
				$stockcopyName	=	stripSlash($scr[2]);
				$selected = ($stockcopyId==$copyfromstock)?"Selected":"";	
			?>
                        <option value="<?=$stockcopyId?>" <?=$selected;?>><?=$stockcopyName;?></option>
			<? }
			/*foreach ($subCategoryRecords as $scr) {				
				$subCategoryId		=	$scr[0];
				$subCategoryName	=	stripSlash($scr[2]);
				$selected = ($selSubCategoryId==$subCategoryId)?"Selected":"";	
			?>
                        <option value="<?=$subCategoryId?>" <?=$selected;?>><?=$subCategoryName;?></option>
			<? }*/?>
                        </select>

			<input type="hidden" name="hidcopyfromstock" value="<?=$name;?>" id="hidcopyfromstock" />
			</td>
		  </tr>




		  
		
			</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
		</TD>
		<TD valign="top">
		<!--<fieldset>-->
		<?php
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table>
		<tr>
			<TD class="fieldName" nowrap>Re-order Required</TD>
			<TD nowrap="true">
				<select name="reorderRequired" id="reorderRequired" onchange="hidReorderPointRow();">
				<option value="">-- Select --</option>
				<option value="Y" <? if ($reorderRequired=='Y') echo "selected";?>>Yes</option>
				<option value="N" <? if ($reorderRequired=='N') echo "selected";?>>No</option>
				</select>
			</TD>
		</tr>
		<tr id="reOrderPointRow">
			<td nowrap class="fieldName" >*Reorder Point</td>
			<td nowrap>
			<? if($addMode==true){ //$reorder=$p["reorderPoint"];
		if ($p[copyfromstock]!=""){
							$reorder="$reorder";
							}
							else {
						$reorder=$p["reorderPoint"];
							}
		
		}
		?>
                        <input name="reorderPoint" id="reorderPoint" type="text" style="text-align:right;" value="<?=$reorder;?>" size="3" maxlength="5" />
			</td>
		</tr>
		<!--<tr>
                       <td class="fieldName" nowrap>*Quantity in Stock</td>
                       <td nowrap="true">
			<? if($addMode==true){ 
			//$quantity=$p["stockQuantity"];

			if ($p[copyfromstock]!=""){
							$quantity="$quantity";
							}
							else {
						$quantity=$p["stockQuantity"];
							}
		
		}
		?>
                             <input type="text" name="stockQuantity" size="4" value="<?=$quantity;?>" style="text-align:right;">
			     <input type="hidden" name="oldStockQuantity" size="4" value="<?=$quantity;?>" style="text-align:right;">
			</td>
               </tr>-->
	<tr>
		<TD class="fieldName" nowrap align='left'>Additional Holding Percent</TD>
		<td class="listing-item" nowrap="true">
		<? if($p["stockQuantity"]!="") $additionalHoldingPercent=$p["additionalHoldingPercent"];?>
	                <input type="text" name="additionalHoldingPercent" size="4" value="<?=$additionalHoldingPercent;?>" style="text-align:right;">&nbsp;%
		</td>		
	</tr>
	<tr>
		<TD class="fieldName" nowrap align='left'>Stocking Period</TD>
		<td class="listing-item" nowrap="true">
			<? if($p["stockingPeriod"]!="") $stockingPeriod = $p["stockingPeriod"];?>
                        <input type="text" name="stockingPeriod" size="2" value="<?=$stockingPeriod;?>" style="text-align:right;">&nbsp;Month
		</td>		
	</tr>
               <tr>
			<td class="fieldName" nowrap >Active</td>
			<td nowrap="true">
			<select name="active">
			  <option value="Y" <? if($active=='Y') echo "Selected";?>>Yes</option>
			  <option value="N" <? if($active=='N') echo "Selected";?>>No</option>
			  </select>								
			</td>
		</tr>  
		</table>
		<?php
			require("template/rbBottom.php");
		?>
					<!--</fieldset>-->
					</TD>
				</TR>
					</table>
					</TD>
				</tr>
                </table></td>
	  </tr>
	<tr style="display:none;">
		<td colspan="2" nowrap class="fieldName">
	<table width="200" align="center">
        <tr>
                 <td class="fieldName"><input name="stockType" type="radio" value="P" onclick="showPacking(this.form)" <? if($stockType=='P') echo "Checked";?> class="chkBox">Packing</td>
                 <td class="fieldName"><input name="stockType" type="radio" value="O" onclick="showOrdinary(this.form)" <? if($stockType=='O') echo "Checked";?> class="chkBox">Ordinary</td>
        </tr>
        </table>
	</td>
	</tr>
	<tr>
		<td colspan="2" nowrap >		  
		<div id="ordinary" style="display:block">
		<table width="200">
		<tr>
		<TD colspan="2">
			<table>
				<TR>
					<TD valign="top">
					<!--<fieldset>-->
					<?php
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
						<td class="fieldName" nowrap >*Basic Unit</td>
						<td nowrap="true">
						<select name="unit" id="unit" onchange="displayActualWtUnit();">
						<option value="">-- Select --</option>
						<? 
							foreach($unitRecords as $ur) {
								$stockItemUnitId = $ur[0];
								$unitName = $ur[1];
								$selected = ($unit==$stockItemUnitId)?"Selected":"";
						?>						
                                                <option value="<?=$stockItemUnitId?>" <?=$selected?>><?=$unitName?></option>
						<? }?>
                                              </select></td>
					</tr>
	<tr>
		<TD class="fieldName" nowrap>*Basic Qty<!--Basic Unit Quantity--> <span id="basicUnitQtyTxt"></span></TD>
		<td nowrap="true">
			<input name="basicUnitQty" type="text" size="3" id="basicUnitQty" value="<?=$basicUnitQty?>" autocomplete="off" />
		</td>
	</tr>	
	<tr>
		<TD class="fieldName" nowrap>*Packed Qty<!--Minimum Order Unit--></TD>
		<td>
			<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
				<input name="minOrderUnit" type="text" id="minOrderUnit" size="3" value="<?=$minOrderUnit?>" style="text-align:right;" onchange="displayActualWtUnit();" autocomplete="off">
			</TD>
			<td class="listing-item"><div id="minOrderUnitTxt"></div></td>
			</TR>
			</table>
		</td>
	</tr>
	<tr>
		<TD class="fieldName" nowrap style="line-height:normal">*Min Order/Package<br><!--Minimum Order Quantity<br> Per Unit--> <span id="minOrderQtyRowTxt"></span></TD>
		<td>
			<table cellpadding="0" cellspacing="0">
			<TR>
			<TD>
				<input name="minOrderQtyPerUnit" type="text" id="minOrderQtyPerUnit" size="3" value="<?=$minOrderUnit?>" style="text-align:right;" onchange="displayActualWtUnit();" autocomplete="off">
			</TD>
			<td class="listing-item"><div id="minOrderQtyPerUnitTxt"></div></td>
			</TR>
			</table>
		</td>
	</tr>
		</table>
			<?php
				require("template/rbBottom.php");
			?>
			<!--</fieldset>-->
		</TD>
<!-- IInd Column -->
	<TD valign="top">
	<!--<fieldset>-->
<!-- Hide the static section -->
	<?php
		$entryHead = "";
		$rbTopWidth = "";
		require("template/rbTop.php");
	?>
	<table style="display:none;">
	<tr>
		<td class="fieldName" nowrap>Brand Name</td>
	        <td nowrap="true"><input name="brand" type="text" id="brand" value="<?=$brand?>" /></td>
	  </tr>
        <tr>
		<td class="fieldName" nowrap>Type</td>
	        <td nowrap="true">
			<input name="brandType" type="text" id="brandType" value="<?=$brandType?>" />
		</td>
	  </tr>
	 <tr>
		<td class="fieldName" nowrap>Model No.</td>
	        <td nowrap="true">
			<input name="modelNo" type="text" id="modelNo" value="<?=$modelNo?>" />
		</td>
	  </tr>
	<tr>
		<td class="fieldName" nowrap >Size</td>
		<td nowrap="true">
			<input name="size" type="text" size="7" value="<?=$size?>">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Dimension</td>
		<td>
			<table>
				<TR>
					<TD class="fieldName" style="padding-left:5px; padding-right:5px;">L:</TD>
					<TD>
						<input type="text" size="2" name="dimensionLength" id="dimensionLength" style="text-align:center" value="<?=$dimensionLength?>" onkeyup="disableDimensionOption();">
					</TD>
					<TD class="fieldName" style="padding-left:5px; padding-right:5px;">B:</TD>
					<TD>
						<input type="text" size="2" name="dimensionBreadth" id="dimensionBreadth" style="text-align:center" value="<?=$dimensionBreadth?>" onkeyup="disableDimensionOption();">
					</TD>
					<TD class="fieldName" style="padding-left:5px; padding-right:5px;">H:</TD>
					<TD>
						<input type="text" size="2" name="dimensionHeight" id="dimensionHeight" style="text-align:center" value="<?=$dimensionHeight?>">
					</TD>
					<TD class="fieldName" style="padding-left:5px; padding-right:5px;">DIA:</TD>
					<TD>
						<input type="text" size="2" name="dimensionDiameter" id="dimensionDiameter" style="text-align:center" value="<?=$dimensionDiameter?>" onkeyup="disableDimensionOption();">
					</TD>
					<TD class="fieldName" style="padding-left:5px; padding-right:5px;">RADIUS:</TD>
					<TD>
						<input type="text" size="2" name="dimensionRadius" id="dimensionRadius" style="text-align:center" value="<?=$dimensionRadius?>" onkeyup="disableDimensionOption();">
					</TD>
				</TR>
			</table>			
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap>Weight</td>
	  	<td nowrap>
			<table>
			<TR>
				<TD nowrap="true">
					<input name="weight" type="text" id="weight" size="4" value="<?=$weight?>" style="text-align:right;">
				</TD>
				<td class="listing-item"><div id="displayMTxt"></div></td>
			</tr>			
			</table>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Color Description</td>
		<td nowrap="true">
			<input name="color" type="text" id="color" value="<?=$color?>">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Made of </td>
		<td nowrap="true">
			<input name="made" type="text" id="made" value="<?=$made?>">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap>Particulars Description </td>
		<td nowrap="true">
			<textarea name="particularsDescription" id="particularsDescription" rows="2"><?=$particularsDescription?></textarea>			
		</td>
	</tr>
	</table>
	<?php
		if ($stockType=="O") {
	?>
	<table cellpadding="0" cellspacing="0">
		<?php
			$sg = 0;
			foreach($stockGroupRecs as $sgr) {
				$sg++;
				$stkGroupEntryId = $sgr[5];
				$stkLabelName	= $sgr[7];
				$stkFieldType	= $inputTypeArr[$sgr[8]];
				$stkFieldName	= $sgr[9];
				$stkFieldDefaultValue = $sgr[10];
				$stkFieldSize	= $sgr[11];
				$stkFieldVDation = $sgr[12];
				$stkDataType	 = $sgr[13];	
				$disSymbol = ($stkFieldVDation=='Y')?"*":"";
				$stkUnitGroupId	 = $sgr[14];
				
				$stkUnitRecs = array();
				if ($stkUnitGroupId!=0) {
					# Get Stock Unit recs
					$stkUnitRecs = $stockItemUnitObj->fetchAllRecords($stkUnitGroupId);
				}
				$stkFieldRowChecked = "";
				if ($editStockId) {
					list($mStkGroupEntyId, $fieldValue, $selStkUnitId) = $stockObj->getStkGroupRecs($editStockId, $stkGroupEntryId);
					if ($sgr[8]=='T') $stkFieldDefaultValue = $fieldValue;
					else if ($fieldValue==$stkFieldDefaultValue) $stkFieldRowChecked = "checked";
				}
		?>
			<TR>
				<td class="fieldName" nowrap>
					<input type="hidden" name="stkGroupEntryId_<?=$sg?>" id="stkGroupEntryId_<?=$sg?>" value="<?=$stkGroupEntryId?>" >
					<?=$disSymbol.$stkLabelName?>
				</td>
				<td nowrap="true" class="listing-item">
					<input name="<?=$stkFieldName?>_<?=$sg?>" type="<?=$stkFieldType?>" id="<?=$stkFieldName?>_<?=$sg?>" value="<?=$stkFieldDefaultValue?>" <?=$stkFieldRowChecked?>>
					<?php
					if (sizeof($stkUnitRecs)>0) {
					?>
					<select name="stkUnitId_<?=$sg?>" id="stkUnitId_<?=$sg?>">
						<option value="">-- Select --</option>
						<?php 
							foreach($stkUnitRecs as $sur) {								
								$stkUnitId = $sur[0];
								$stkUnitName = stripSlash($sur[1]);
								$selected = ($selStkUnitId==$stkUnitId)?"Selected":"";
						?>						
                                                <option value="<?=$stkUnitId?>" <?=$selected?>><?=$stkUnitName?></option>
						<?php
							 }
						?>
                                              </select>
					<?php
						}
					?>
				</td>
			</TR>
		<?php
			 }
		?>
		<input type="hidden" name="hidStkGroupRowCount" id="hidStkGroupRowCount" value="<?=$sg?>" />
		</table>
		<?php
		}	// Stock Type Checking ends here
		?>
<!--</fieldset>-->
		<?php
			require("template/rbBottom.php");
		?>
		</TD>
		</tr>
<!-- Dynamic Field	 -->
	
<!--  Ends-->
		</table>
	</TD></tr>
               </table>
	</div>
	</td>
	  </tr>
	<tr>
	  <td colspan="2" nowrap class="fieldName" >
		<div id="packing" style="display:block">
		<table width="200">
			<tr>
				<TD colspan="2">
					<table>
						<TR>
							<TD valign="top">
								<?php
									$entryHead = "";
									$rbTopWidth = "";
									require("template/rbTop.php");
								?>
								<table>
									<tr>
			 	<td class="fieldName" nowrap >*No of Layers</td>
				<td nowrap="true"><input name="numLayer" type="text" id="numLayer" size="5" value="<?=$layer?>"></td>
				 </tr>
				<tr>
				  	<td class="fieldName" nowrap >Type of Carton</td>
					<td nowrap="true">
					<select name="carton" id="carton">
					<option value="U" <? if($carton=='U') echo "selected";?>>Univ</option>
					<option value="T" <? if($carton=='T') echo "selected";?>>Top</option>
					<option value="B" <? if($carton=='B') echo "selected";?>>Bottom</option>
					</select></td>
				</tr>
				<!--<tr>
					<td class="fieldName" nowrap >Brand</td>
					<td nowrap="true">
					<input name="packingBrand" type="text" id="packingBrand" value="<?=$packingBrand?>" >-->
					<!--select name="packingBrand" id="packingBrand">
                                        <option value="">-- Select --</option>
                                        <?
			  		foreach ($brandRecords as $br) {
						$brandId = $br[0];
						$brandName	= stripSlash($br[2]);
						$customerName	= stripSlash($br[3]);
						$displayBrand   = $brandName."&nbsp;(".$customerName.")";
						$selected	= ($packingBrand==$brandId)?"selected":"";
				  	?>
					<option value="<?=$brandId?>" <?=$selected?>><?=$displayBrand?></option>
                                        <? }?>
                                        </select-->
					<!--</td>
				</tr>-->
				<!--<tr>
					<td class="fieldName" nowrap >Color</td>
					<td nowrap="true"><input name="packingColor" type="text" id="packingColor" value="<?=$packingColor?>" ></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Packing Weight</td>
					<td nowrap="true"><input name="packingWeight" type="text" id="packingWeight" size="5" value="<?=$packingWeight;?>"></td>
				</tr>-->
						</table>
				<!--</fieldset>-->
				<?php
					require("template/rbBottom.php");
				?>
							</TD>
							<TD valign="top">
		<?php
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
			//$k = 1;
			$k1=1;
		?>

		
		<table border=0>
			<tr>
					<td class="fieldName" nowrap >Packing(Kg x Nos)</td>
					<td nowrap="true">
				<table id="tblPOItem1">
					<tr>
					<!--<td style="padding-left:5px;padding-right:5px;" nowrap="true">Person Name</td>
					<td style="padding-left:5px;padding-right:5px;" nowrap="true">Designation</td>-->
					</tr>
						</table>
					</td>
					<td class="fieldName" nowrap ><!--<a href="###" id='addRow' onclick="javascript:addNewItem1();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:0px;vertical-align:middle;' >Add Item</a>--></td>
				</tr>
                                <!--<tr>
                                   <TD class="fieldName" nowrap>Suitable For:</TD>
                                  <td nowrap="true">
				<select name="selFrozenCode[]" size="7" multiple id="selFrozenCode">
                                <option value="" > Select Frozen Code </option>-->
                                <?
				/*if (sizeof($frozenPackingRecords)> 0) {
	 				foreach ($frozenPackingRecords as $fpr) {
						$frozenPackingId = $fpr[0];
						$frozenPackingCode = stripSlash($fpr[1]);
						if ($editMode) 	$selFrozenCodeId = $fpr[4];
						$selected = ($frozenPackingId==$selFrozenCodeId)?"Selected":"";*/
				?>
				<!--<option value="<?=$frozenPackingId;?>" <?=$selected;?>><?=$frozenPackingCode;?></option>-->
                                <?
				  	//}
				//}
				?>
                             <!-- </select>
				</td>
				</tr>-->
				<tr><td><a href="###" id='addRow' onclick="javascript:addNewItem1();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:0px;vertical-align:middle;' >Add Item</a></td></tr>

				
				<tr>
					<td class="fieldName">*Suitable For:</td>
					<td>
					<table>
						<TR>
							<TD>
				<!--<select name="selFullGrade[]" size="7" multiple id="selFullGrade">-->
				<select name="selFullFrozenCode[]" size="7" multiple id="selFullFrozenCode">
                                <option value="" >Select Frozen Code </option>
                                <?php
				/*if (sizeof($gradeMasterRecords)> 0) {
					foreach ($gradeMasterRecords as $gl) {
						$id		= $gl[0];
						$displayGrade	= $gl[1];
						$selected	= "";*/
						/*
						$recordGradeId	= $gl[4];	
						if ($gradeCodeId== $id || $recordGradeId == $id) {
							$selected	=	" selected ";
						}
						$grade ="";
						foreach ($gradeId as $gId) {
							$grade	=	"$gId";
							if ( strstr($grade,"$gl[0]") ) $selected	=	" selected ";
						}
						*/
				?>
                               <!-- <option value="<?=$id;?>" <?=$selected;?> ><?=$displayGrade;?></option>-->
                                <?php
				  	//}
				//}
			  	?>

<?php
				if (sizeof($frozenPackingRecords)> 0) {
	 				foreach ($frozenPackingRecords as $fpr) {
						//$frozenPackingId = $fpr[0];
						$frozenPackingId = $fpr[0]."-".$fpr[2];
						//$frozenPackingCode = stripSlash($fpr[1]);
						$frozenPackingCode = $fpr[1];
						//if ($editMode) 	$selFrozenCodeId = $fpr[4];
						//$selected = ($frozenPackingId==$selFrozenCodeId)?"Selected":"";
				?>
				<option value="<?=$frozenPackingId;?>" <?=$selected;?>><?=$frozenPackingCode;?></option>
                                <?
				  	}
				}
				?>
                              </select>
							</TD>
							<TD>
							<table>
								<TR><TD>
									<input type="button" value="Add All" onclick="addAll(document.getElementById('selFullFrozenCode'), document.getElementById('selFrozenCode'), 'R');" title="Add All" style="width:70px;"/>
								</TD></TR>
								<TR><TD><input type="button" value="Add" onclick="addAttribute(document.getElementById('selFullFrozenCode'), document.getElementById('selFrozenCode'), 'R');" title="Add one by one" style="width:70px;"/></TD></TR>
								<TR><TD></TD></TR>
								<TR><TD><input type="button" value="Remove" onclick="delAttribute(document.getElementById('selFullFrozenCode'), document.getElementById('selFrozenCode'), 'R');" title="Delete one by one" style="width:70px;"/></TD></TR>
								<TR><TD><input type="button" value="Remove All" onclick="delAll(document.getElementById('selFullFrozenCode'), document.getElementById('selFrozenCode'), 'R');" title="Delete All" style="width:70px;"/></TD></TR>
							</table>
							</TD>
							<TD>
				<!--<select name="selGrade[]" size="7" multiple id="selGrade">-->
				<select name="selFrozenCode[]" size="7" multiple id="selFrozenCode">
                                	<option value="" >Active Frozen Code </option>
					<?php
					$sRawGrade = array();
					$sr = 0;
					/*foreach ($selGradeRecs as $gl) {
					$selGrId = $gl[0];
						$selGradeDisplay = $gl[1];
						$sRawGrade[$sr] = $selGrId;
						
						if ($processCodeId) {
							$style = "";
							$chkRecExist = $processcodeObj->pcGradeRecInUse($processCodeId, $selGrId);
							if ($chkRecExist) $style = "style='color:red'";
		
						}

				?>
                                <option value="<?=$selGrId;?>" <?=$style?>><?=$selGradeDisplay;?></option>
				<?php 
					$sr++;
					}*/


$sr=0;
	foreach ($selFrozRecs as $gl) {
					//$selFrId = $gl[0];
					$selFrId = $gl[0]."-".$gl[2];
						$selFrozenDisplay = $gl[3];
						//$frquickid= $gl[$sr]."-".$g1[$sr];

						


				?>

				<option value="<?=$selFrId;?>" <?=$style?>><?=$selFrozenDisplay;?></option>
				<?php 
					$sr++;
					}?>
                              	</select>
				<input type="hidden" name="selRawGrade" id="selRawGrade" value="<?=$sfrVal//=implode(",",$sRawGrade);?>" />
				</TD>
				</TR>
				</table>				
				</td>
				</tr>
                                <!-- <tr>
					<td class="fieldName" nowrap >No.of Colors </td>
					<td nowrap="true"><input name="numColors" type="text" id="numColors" value="<?=$numColors?>" size="3"></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Dimension</td>
					<td nowrap="true">
					<input name="packingDimension" type="text" id="packingDimension" value="<?=$packingDimension?>"></td>
				</tr>-->
				<tr>
					<td class="fieldName">*Carton Weight </td>
					<td class="fieldName" nowrap="true" style="text-align:left;">
						<input name="cartonWeight" type="text" id="cartonWeight" size="5" value="<?=$cartonWeight?>">&nbsp;(Gms)
					</td>
				</tr>
				<tr>
					<td class="fieldName">* Tolerance level </td>
					<td class="fieldName" nowrap="true" style="text-align:left;">
						<input name="toleranceLevel" type="text" id="toleranceLevel" size="5" value="<?=$tolerancelevel?>">&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
								if ($stockType=="P") {
							?>
							<table cellpadding="0" cellspacing="0">
								<?php
									$sg = 0;
									foreach($stockGroupRecs as $sgr) {
										$sg++;
										$stkGroupEntryId = $sgr[5];
										$stkLabelName	= $sgr[7];
										$stkFieldType	= $inputTypeArr[$sgr[8]];
										$stkFieldName	= $sgr[9];
										$stkFieldDefaultValue = $sgr[10];
										$stkFieldSize	= $sgr[11];
										$stkFieldVDation = $sgr[12];
										$stkDataType	 = $sgr[13];	
										$disSymbol = ($stkFieldVDation=='Y')?"*":"";
										$stkUnitGroupId	 = $sgr[14];	
										$stkUnitRecs = array();
										if ($stkUnitGroupId!=0) {
											# Get Stock Unit recs
											$stkUnitRecs = $stockItemUnitObj->fetchAllRecords($stkUnitGroupId);
										}
										$stkFieldRowChecked = "";
										if ($editStockId) {
											list($mStkGroupEntyId, $fieldValue, $selStkUnitId) = $stockObj->getStkGroupRecs($editStockId, $stkGroupEntryId);
											if ($sgr[8]=='T') $stkFieldDefaultValue = $fieldValue;
											else if ($fieldValue==$stkFieldDefaultValue) $stkFieldRowChecked = "checked";
										}
								?>
									<TR>
										<td class="fieldName" nowrap>
											<input type="hidden" name="stkGroupEntryId_<?=$sg?>" id="stkGroupEntryId_<?=$sg?>" value="<?=$stkGroupEntryId?>" >
											<?=$disSymbol.$stkLabelName?>
										</td>
										<td nowrap="true" class="listing-item">
											<input name="<?=$stkFieldName?>_<?=$sg?>" type="<?=$stkFieldType?>" id="<?=$stkFieldName?>_<?=$sg?>" value="<?=$stkFieldDefaultValue?>" <?=$stkFieldRowChecked?>>
											<?php
											if (sizeof($stkUnitRecs)>0) {
											?>
											<select name="stkUnitId_<?=$sg?>" id="stkUnitId_<?=$sg?>">
												<option value="">-- Select --</option>
												<?php 
													foreach($stkUnitRecs as $sur) {								
														$stkUnitId = $sur[0];
														$stkUnitName = stripSlash($sur[1]);
														$selected = ($selStkUnitId==$stkUnitId)?"Selected":"";
												?>						
																		<option value="<?=$stkUnitId?>" <?=$selected?>><?=$stkUnitName?></option>
												<?php
													 }
												?>
																	  </select>
											<?php
												}
											?>
										</td>
									</TR>
								<?php
									 }
								?>
								<input type="hidden" name="hidStkGroupRowCount" id="hidStkGroupRowCount" value="<?=$sg?>" />
								</table>
								<?php
								}	// Stock Type Checking ends here
								?>
					</td>				
				</tr>
		</table>
		<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$k+1;?>">

		
		<!--</fieldset>-->
		<?php
			require("template/rbBottom.php");
		?>
							</TD>
						</TR>
					</table>
				</TD>
			</tr>			
                                </table>
		  </div>
		</td>
	  </tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockEntry.php');">&nbsp;&nbsp;							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStock(document.frmStockEntry);">			
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockEntry.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStock(document.frmStockEntry);">					
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
			  	<td class="listing-item" style="padding-left:5px; padding-right:5px;">Category</td>
                      		<td nowrap="nowrap"> 		
                <!--<select name="categoryFilter" onchange="this.form.submit();" style="width:165px;">-->
				<select name="categoryFilter" onchange="getLoading(this);" style="width:165px;">
                <option value="">-- Select All --</option>
                <?php
		foreach ($categoryRecords as $cr) {
			$categoryId	=	$cr[0];
			$categoryName	=	stripSlash($cr[1]);
			$selected = ($categoryFilterId==$categoryId)?"Selected":"";		
		?>
               <option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
                <? }?>
                </select>
		</td>
		<td class="listing-item">&nbsp;</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="true">Sub-Category</td>
                <td nowrap>
			<!--<select name="subCategoryFilter" onchange="this.form.submit();" style="width:165px;">-->
			<select name="subCategoryFilter" onchange="getLoading(this);" style="width:165px;">
                        <option value="">-- Select All--</option>
                        <?
			foreach ($subCategoryFilterRecords as $scr) {
				$subCategoryId		= $scr[0];
				$subCategoryName	= stripSlash($scr[2]);
				$selected = ($subCategoryFilterId==$subCategoryId)?"Selected":"";
			?>
                        <option value="<?=$subCategoryId?>" <?=$selected;?>><?=$subCategoryName;?></option>
                        <? }?>
                       </select>
       		</td>
             </tr>
			 <tr><td colspan="5" height="10"></td></tr>
	<tr>
			  	<td class="listing-item" style="padding-left:5px; padding-right:5px;">Stock</td>
				<td> 		
               <input type="text" name="stockitemsearch" id="stockitemsearch" value="" /></td>
			   <td  colspan="3">
			   <input type=submit name="cmdSearch" Value=Search  class="button" onchange="this.form.submit();"/>
		</td>
		<td class="listing-item">&nbsp;</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap="true">&nbsp;</td>
                <td nowrap>
			&nbsp;
       		</td>
             </tr>


          </table>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Entry  </td>
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
								<TD>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockEntry.php?categoryFilter=<?=$categoryFilterId?>&subCategoryFilter=<?=$subCategoryFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300"><table><TR>	
	<TD><? if($edit==true){?><input type="submit" value=" Bulk Update " class="button"  name="cmdBulkUpdate" onClick="return validateBulkStockUpdateRec();"><? }?></td></TR></table></TD>
				</TR>
			</table>
		</TD>
	</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
		<?
			if($errDel!="") {
		?>
		<tr>
			<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
		</tr>
		<?
			}
		?>
		<tr>
			<td width="1" ></td>
			<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
			<?
				if ( sizeof($stockRecords) > 0 ) {
					$i	=	0;
			?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="13" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
	<tr  align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Code</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Company & Unit</th>
		
	<!--	<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Opening<br> Quantity </th>-->
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Current<br> Quantity</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Reorder Point </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Additional Holding Percent <br>(%) </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Price Fluct-<br>uation Indicator</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Stocking Period <br>(In Months)</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Active</th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>

	</tr>
	</thead>
	<tbody>
	<?
	foreach ($stockRecords as $sr) {
		$i++;
		$stockId = $sr[0];
		$stockCode = stripslashes($sr[1]);
		$stockName = stripslashes($sr[2]);
		$quantity = $sr[3];
		$reOrderPoint = $sr[5];
		
		$actualQuantity = $sr[9];
		/**************************/
		$displayActualQty = "";
		$displayTitle  = "";
		if ($actualQuantity<$reOrderPoint) {
			$displayActualQty = "<span style=\"color:#FF0000\">".$actualQuantity."</span>";
			$displayTitle = "This stock is below Re-order Point";
		} else {
			$displayActualQty  = $actualQuantity;
			$displayTitle = "";
		}

		$holdingPercent = $sr[10];
		# Stock Item Price Variation
		$priceVariationAmt = $stockObj->getStockItemPriceVariation($stockId);
		$stockingPeriod = $sr[11];
		$active=$sr[12];
		$existingcount=$sr[13];
		//$plantId=$sr[14];
		$getPlantList= $stockObj->getPlantList($stockId);
		//print_r($getPlantList);
		list($id,$no,$plantName)=$plantandunitObj->find($plantId);
		$pLN="";
		$qStA="";
		$qStO="";

		if ($active=='1') {
			$displayActiveStaus = "Yes";
		} else {
			$displayActiveStaus = "No";
		}
		$currentQuantity=$stockObj->getCurrentStockQty($stockId);
		$companyUnitRecs=$stockObj->getCompanyUnit($stockId);
		/*$activeStatus = $sr[6];
		
		$displayActiveStaus = "";
		if ($activeStatus=='Y') {
			$displayActiveStaus = "Yes";
		} else {
			$displayActiveStaus = "No";
		}*/

	?>
	<tr title="<?=$displayTitle?>" <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stockCode;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stockName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
			<table cellpadding="1" cellspacing="1" align="center" bgcolor="#999999" align="center">
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"  width="50%">Company</td>
					<td class="listing-head" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"  width="50%">Unit</td>
				</tr>
				<tr  bgcolor="#f2f2f2">
					<td colspan="2" bgcolor="#e8edff" align="center">
						<table cellpadding="1" cellspacing="1" align="center" width="100%" >
						<?
						$j=0;
						foreach($companyUnitRecs as $cUR)
						{
						?>
							<tr>
							<?
							$company=$cUR[0];
							$unit=$cUR[1];
							?>
								<td width="50%" class="listing-item" nowrap="nowrap">&nbsp;
								<? if($j==0)
								{
									echo $company.'<br/>';
								}
								else
								{
									if($companyOld!=$company)
									{
										echo $company.'<br/>';
									}
								}
								?>
								</td>
								<td  width="50%" class="listing-item" nowrap="nowrap"  >&nbsp;
								<? if($j==0)
								{
									echo $unit.'<br/>';
								}
								else
								{
									if($unitOld!=$unit)
									{
										echo $unit.'<br/>';
									}
								}
								?>
								</td>
							</tr>	
							<?
							$companyOld=$company;
							$unitOld=$unit;
							$j++; 
							}
							?>
						</td>
					</table>
				</tr>
			</table>
		</td>
		
	<!--	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?php
		foreach($getPlantList as $gP){

		
		list($id,$no,$plantName)=$plantandunitObj->find($gP[2]);
		$pLN=$pLN.$plantName.",";
		?>
		<?=$plantName;?><?php }?><?=trim($pLN,',');?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;">
		<?php
		foreach($getPlantList as $gP){
		//list($id,$no,$plantName)=$plantandunitObj->find($gP[2]);
		$quantity=$gP[4];
		$qStO=$qStO.$quantity.",";
		?>
		
		<?//=$quantity?><?php }?><?=trim($qStO,',');?></td> -->
	<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;">
		<?php
		/*
		foreach($getPlantList as $gP){
		//list($id,$no,$plantName)=$plantandunitObj->find($gP[2]);
		$displayActualQty=$gP[3];
		$qStA=$qStA.$displayActualQty.",";
		?>
		
		<?//=$displayActualQty?><?php }?><?=trim($qStA,',');*/?> <?=$currentQuantity?></td>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><?=$reOrderPoint;?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" size="4" name="holdingPercent_<?=$i?>" id="holdingPercent_<?=$i?>" style="text-align:right;" value="<?=$holdingPercent;?>" tabindex="<?=$i?>">
			<input type="hidden" size="4" name="hidHoldingPercent_<?=$i?>" id="hidHoldingPercent_<?=$i?>" value="<?=$holdingPercent;?>">
			<input type="hidden" size="4" name="hidStockId_<?=$i?>" id="hidStockId_<?=$i?>" value="<?=$stockId;?>">		
		</td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=($priceVariationAmt>0)?number_format($priceVariationAmt,0,'',''):"";?></td>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			<input type="text" size="2" name="stockingPeriod_<?=$i?>" id="stockingPeriod_<?=$i?>" value="<?=$stockingPeriod;?>" style="text-align:right;" tabindex="<?=$i?>">
			<input type="hidden" size="2" name="hidStockingPeriod_<?=$i?>" id="hidStockingPeriod_<?=$i?>" value="<?=$stockingPeriod;?>">			
		</td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$displayActiveStaus;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<? if ($active==0){ ?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StockEntry.php';" >
		<? }
		?>
		</td>
		<? }?>

		
		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockId;?>,'confirmId');" >
			<?php } } }?>
			
			
			
			
			</td>
												
<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
<? if($maxpage>1){?>
		<tr>
		<td colspan="13" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockEntry.php?pageNo=$page&categoryFilter=$categoryFilterId&subCategoryFilter=$subCategoryFilterId\"  class=\"link1\">>></a> ";
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
			<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
		<TD colspan="3">
			<table width="900" align="center">
				<TR>
					<TD width="300"></TD>
					<TD width="300">
						<table>
							<TR>
								<TD>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockEntry.php?categoryFilter=<?=$categoryFilterId?>&subCategoryFilter=<?=$subCategoryFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300"><table><TR>	
	<TD><? if($edit==true){?><input type="submit" value=" Bulk Update " class="button"  name="cmdBulkUpdate" onClick="return validateBulkStockUpdateRec();"><? }?></td></TR></table></TD>
				</TR>
			</table>
		</TD>
	</tr>
<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
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
<input type="hidden" name="hidCategoryFilterId" value="<?=$categoryFilterId?>">	
<input type="hidden" name="hidSubCategoryFilterId" value="<?=$subCategoryFilterId?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<? if ($unit!=0) {?>
	<script language="JavaScript" type="text/javascript">
	displayActualWtUnit();
	</script>
	<? }?>
	<? if ($addMode!="" || $editMode!="") {?>
	<script language="JavaScript" type="text/javascript">
		window.onLoad = callFn();
		function callFn()
		{		
			hidReorderPointRow();
		}
	</script>
	<? }?>
	<?php 
		if (($addMode) || ($editMode)) {
	?>
	<script language="JavaScript" type="text/javascript">

	function addNewItem2()
	{		
		//alert("entered");
		addNewPOItem2('tblPOItem2', '', '','','','','','','','<?=$mode?>');
		var row=parseInt(document.getElementById('hidTableRowCount2').value)-1;
		xajax_getUnit('<?=$defaultCompany?>',row,'');
		//alert("entered1");
		//xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
	}
		
	function addNewItem1()
	{		
				
		addNewPOItem1('tblPOItem1', '', '','','','','','','','<?=$mode?>');
		//xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
	}
			

			
			</script>
			<?php }?>



	<?php 
		if ((($addMode) || (!sizeof($packingWeightRecs) && $editMode)) && (!$copyfromstock)) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			window.load = addNewItem1();
	window.load = addNewItem2();
		</SCRIPT>
	<?php 
		}
	?>


<script language="JavaScript" type="text/javascript">	
		// Get state		
	

	<?php
		if (sizeof($packingWeightRecs)>0) {
			$j=0;
			foreach ($packingWeightRecs as $ver) {			
				$Id 	= $ver[0];
				$packingweight	= $ver[1];
				$mcpakingid	= $ver[2];
					
	?>	
		addNewPOItem1('tblPOItem1', '<?=$packingweight?>', '<?=$mcpakingid?>','<?=$Id?>','','','','','','<?=$mode?>');	
			
	<?
			$j++;
			}
		}
	?>


	<?php
		if (sizeof($plantUnitRecs)>0) {
			$j=0;
			//printr($plantUnitRecs);
			foreach ($plantUnitRecs as $ver) {	
				
				$Id 	= $ver[0];
				$companyId	= $ver[1];
				$unitId	= $ver[2];
				$quantity	= $ver[3];
					
			?>	
		xajax_getUnit('<?=$companyId?>','<?=$j?>','<?=$unitId?>');
		addNewPOItem2('tblPOItem2','<?=$companyId?>', '<?=$unitId?>', '<?=$quantity?>','<?=$Id?>','','','','','','<?=$mode?>');	
		
			
	<?
			$j++;
			}
			?>
				fieldIdStock = <?=sizeof($plantUnitRecs)?>;
		<?php }		
	else {
				?>
					fieldIdStock = 1;
				
				
				<?php }

				
			?>
	</script>	






	<script language="javascript">
<?php
				if (sizeof($poRawItemRecs)>0) {

				// Set Value to Main table
			?>
				fieldId = <?=sizeof($poRawItemRecs)?>;
			
			<?php
				}
			else {
				?>
					fieldId = 1;
				
				
				<?php }

				
			?>
			

			
			//alert("fff----"+fieldId);
			//fieldIdStock =1;
			//alert("fff2"+fieldIdStock);
	</script>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>


			