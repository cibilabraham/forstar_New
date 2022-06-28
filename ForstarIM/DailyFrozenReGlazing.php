<?	require_once("include/include.php");
	require_once("lib/dailyfrozenrepacking_ajax.php");
	//require_once("libjs/dailyfrozenreglazing.js");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$isSearched		=	false;
	
	$selection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"];
	
//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------	

# MC Packing Conversion type, AC - Auto convert/ MC - Manually Convert
	$LSToMCConversionType = $manageconfirmObj->getLS2MCConversionType();
	//----------------------------------------------------------	
	
		
	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;		
		if ($p["mainId"]=="" && $dailyfrozenreglazingObj->checkBlankRecord()) {
			
			$mainId		=	$dailyfrozenreglazingObj->checkBlankRecord();
					
		} else {
			if ($p["mainId"]=="") {
				$tempMainTableRecIns=$dailyfrozenreglazingObj->addTempDataMainTable();
				if ($tempMainTableRecIns!="") {				
					$mainId	=	$databaseConnect->getLastInsertedId();			
				}
				
			} else {
				$mainId 	=	$p["mainId"];
			}
		}
		#delete Old Grade records of Main id
		if ($dailyfrozenreglazingObj->checkBlankRecord() && $p["mainId"]=="") {
			$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj-> deleteFrozenRePackingGradeRec($mainId);
		}		
	}
	


	
#New Entry
	if ($p["cmdAdd"]!="") {
	
		$DateS			=	explode("/", $p["selectDate"]);
		$selectDate		=	$DateS[2]."-".$DateS[1]."-".$DateS[0];
		
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		$brand			=	$p["brand"];
		$frozenCode		=	$p["frozenCode"];
		$mCPacking		=	$p["mCPacking"];

		$repackReasonId		=	$p["reasonRePack"];

		$numNewInnerPack	=	$p["numNewInnerPack"];
		$numLabelCard		=	$p["numLabelCard"];
		$numNewMC		=	$p["numNewMC"];

		$rePackEUCode		=	$p["rePackEUCode"];
		$rePackBrand		=	$p["rePackBrand"];
		$rePackFrozenCode	=	$p["rePackFrozenCode"];
		$rePackMCPacking	=	$p["rePackMCPacking"];
	
		if( $fishId!="" && $processCode!="") {
			$updateFrozenRePackingRec =	$dailyfrozenreglazingObj->updateFrozenRePackingRec($mainId, $fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $repackReasonId, $numNewInnerPack, $numLabelCard, $numNewMC, $rePackEUCode, $rePackBrand, $rePackFrozenCode, $rePackMCPacking, $selectDate);
			
			if ($updateFrozenRePackingRec) {
				 $sessObj->createSession("displayMsg",$msg_succAddDailyFrozenRePacking);
				$sessObj->createSession("nextPage",$url_afterAddDailyFrozenRePacking.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyFrozenRePacking;
			}
			$dailyFrozenPackingRecIns	=	false;
		}
	}

# Edit Re Packing
	
	if( $p["editId"]!="" ){
	
		$editId			=	$p["editId"];
		$editMode		=	true;
		$dailyFrozenRePackingRec		=	$dailyfrozenreglazingObj->find($editId);
		
		$mainId		=	$dailyFrozenRePackingRec[0];
		
		$eDate				=	explode("-",$dailyFrozenRePackingRec[16]);
		
		
		if($p["editSelectionChange"]=='1'|| $p["fish"]==""){
			$fishId			=	$dailyFrozenRePackingRec[1];
		}
		else {
			$fishId		=	$p["fish"];
		}
		
		
		if($p["editSelectionChange"]=='1'|| $p["processCode"]==""){
			 $processId	=	$dailyFrozenRePackingRec[2];
		}
		else {
			$processId	=	$p["processCode"];
		}
		
		$freezingStage		=	$dailyFrozenRePackingRec[3];
		$eUCode			=	$dailyFrozenRePackingRec[4];
		$brand			=	$dailyFrozenRePackingRec[5];
		$frozenCode		=	$dailyFrozenRePackingRec[6];
		$mCPacking		=	$dailyFrozenRePackingRec[7];

		$repackReasonId		=	$dailyFrozenRePackingRec[8];

		$numNewInnerPack	=	$dailyFrozenRePackingRec[9];
		$numLabelCard		=	$dailyFrozenRePackingRec[10];
		$numNewMC		=	$dailyFrozenRePackingRec[11];

		$rePackEUCode		=	$dailyFrozenRePackingRec[12];
		$rePackBrand		=	$dailyFrozenRePackingRec[13];
		$rePackFrozenCode	=	$dailyFrozenRePackingRec[14];
		$rePackMCPacking	=	$dailyFrozenRePackingRec[15];
		
		$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);	
							
}

if( $p["cmdSaveChange"]!="" ){?>
	

		<?php $dailyFrozenRePackingMainId		=	$p["editId"];
			if ($dailyFrozenRePackingMainId!="") {
			    $dfId=$p["editId"];
				$i=1;
				$k=1;
				$mCPacking=$p["mcPackingId_".$i];
				$hidselFrozenCode=$p["hidselFrozenCode_1"];
				$glfrozenCodeId=$p["selFrozenCode_".$k];
						//echo "----$glfrozenCodeId";
						//echo "$hidselFrozenCode";
				$gradeRowCount	= $p["hidAllocateGradeRowCount"];	
				$dailyFrozenreGlazeId=$p["delIdReg"];
				$processId			= $p["hidProcessId"];
				$freezingStageId	= $p["hidFreezingStage"];			
				$unit				= $p["hidunit"];				
				$selectDate=mysqlDateFormat($p["selDate"]);
				$processorId=$p["hidProcessorId"];
				$repackedfrom=$p["hidNumMcPackPrev_".$i];
				//$repackedValue="PID-$processId,FSID-$freezingStageId,FCID-$glfrozenCodeId,MC-$repackedfrom";
				$repackedValue="PID-$processId,FSID-$freezingStageId,FCID-$hidselFrozenCode,MC-$repackedfrom";
				//$dfId=
				//$repackedValue=
				//$fish_id=
				if ($hidselFrozenCode==$glfrozenCodeId){
					//$insertDailyRepacking=$dailyfrozenrepackingObj->insertDailyRepacking($selectDate,$processId,$freezingStageId,$refrozenCodeId,$repmcPkId);
					$insertDailyRepacking=$dailyfrozenrepackingObj->insertDailyRepacking($selectDate,$processId,$freezingStageId,$glfrozenCodeId,$mCPacking);
						if ($insertDailyRepacking)
						$dfpPOEntryId = $databaseConnect->getLastInsertedId();
						$dailyfrozenrepackingObj->addPhysicalStkdailyFrozenmain($selectDate,$userId,$dfpPOEntryId,$unit,$processorId,$dfId,$repackedValue);
						$dailymainId = $databaseConnect->getLastInsertedId();
						$dailyfrozenrepackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processId,$freezingStageId,$glfrozenCodeId,$mCPacking);
						$dailyentrymainId = $databaseConnect->getLastInsertedId();
					
						for ($j=1; $j<=$gradeRowCount; $j++) {
									$gradeId = $p["sGradeId_".$j."_".$i];
									$numMC = $p["numMC_".$j."_".$i];
									if ($numMC>0)
										{
									$insertPOGradeRec = $dailyfrozenrepackingObj->insertDFPPOGradeForRepacking($dfpPOEntryId, $gradeId, $numMC, $numLS);			
									$dailyfrozenrepackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$gradeId,$numMC,$numLS);
										}
								}


				list($entryId)=$dailyfrozenreglazingObj->getEntryId($dailyFrozenRePackingMainId);			
				list($gradeEntryId)=$dailyfrozenreglazingObj->getGradeEntryId($entryId);
				$dailyFrozenPackingGradeRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRecMain($gradeEntryId);
				$dailyFrozenPackingEntryRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingEntryRecMain($entryId);
				$dailyFrozenRePackingRecDelMain	=$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecMain($dailyFrozenRePackingMainId);

				$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRec($dailyFrozenreGlazeId);
				$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRec($dailyFrozenreGlazeId);


				}
				else{
				list($entryId)=$dailyfrozenreglazingObj->getEntryId($dailyFrozenRePackingMainId);						
				list($gradeEntryId)=$dailyfrozenreglazingObj->getGradeEntryId($entryId);				
				$dailyfrozenreglazingObj->updateDailyFrozenPackingEntry($mCPacking,$glfrozenCodeId,$entryId);				
				for ($j=1; $j<=$gradeRowCount; $j++) {
									$gradeId = $p["sGradeId_".$j."_".$i];
									$numMC = $p["numMC_".$j."_".$i];
									$numLS = $p["numLS_".$j."_".$i];
									if (($gradeId>0) && ($numMC>0 )) {
									$dailyfrozenreglazingObj->updateDailyFrozenReglazingGradeEdit($numMC,$numLS,$entryId,$gradeId);
									$dailyfrozenreglazingObj->updateDailyFrozenReglazingGrade($numMC,$numLS,$dailyFrozenreGlazeId,$gradeId);
									}
				}
				
				list($glzentryId)=$dailyfrozenreglazingObj->getGlzEntryId($dailyFrozenRePackingMainId);
				$dailyfrozenreglazingObj->updateDailyFrozenGlazingEntry($mCPacking,$glfrozenCodeId,$dailyFrozenreGlazeId);
				}
				
				 $editMode=false;

 }
 
 
 }

if( $p["cmdSaveChange1"]!="" ){
		
		
		?>
		
		<?php
		/*$mainId 	=	$p["mainId"];

		$DateS			=	explode("/", $p["selectDate"]);
		$selectDate		=	$DateS[2]."-".$DateS[1]."-".$DateS[0];
		
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		$brand			=	$p["brand"];
		$frozenCode		=	$p["frozenCode"];
		$mCPacking		=	$p["mCPacking"];

		$repackReasonId		=	$p["reasonRePack"];

		$numNewInnerPack	=	$p["numNewInnerPack"];
		$numLabelCard		=	$p["numLabelCard"];
		$numNewMC		=	$p["numNewMC"];

		$rePackEUCode		=	$p["rePackEUCode"];
		$rePackBrand		=	$p["rePackBrand"];
		$rePackFrozenCode	=	$p["rePackFrozenCode"];
		$rePackMCPacking	=	$p["rePackMCPacking"];
		
		
		if( $fishId!="" && $processCode!="" && $mainId!="") {
			$dailyFrozenRePackingRecUptd =	$dailyfrozenreglazingObj->updateFrozenRePackingRec($mainId, $fishId, $processCode, $freezingStage, $eUCode, $brand, $frozenCode, $mCPacking, $repackReasonId, $numNewInnerPack, $numLabelCard, $numNewMC, $rePackEUCode, $rePackBrand, $rePackFrozenCode, $rePackMCPacking, $selectDate);
		}
	
		if ($dailyFrozenRePackingRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyFrozenRePacking);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyFrozenRePacking.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyFrozenRePacking;
		}
		$dailyFrozenRePackingRecUptd	=	false;*/
			$i=1;
			$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
			$gradeRowCount	= $p["hidAllocateGradeRowCount"];		
			$processId			= $p["hidProcessId"];
			$freezingStageId	= $p["hidFreezingStage"];
			$frozenCodeId		= $p["hidFrozenCode"];
			$MCPkgId			= $p["hidMCPkgId"];
			$dateTill			= $p["frozenPackingTill"];
			$dateFrom			= $p["frozenPackingFrom"];
			$unit				= $p["hidunit"];
			//echo "$unit";
			$processorId		= $p["hidProcessorId"];
			//echo "ProcessId is $processorId";
			$mcpackingRec	= $mcpackingObj->find($MCPkgId);
			$numPacks	= $mcpackingRec[2];
			$dateT			=	explode("/", $dateTill);
			$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
			$dateF			=	explode("/", $dateFrom);
			$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];
			
			$selectDate		=	mysqlDateFormat($p["selDate"]);
						$dFrznPkgEntryId = 0;//$p["dFrznPkgEntryId_".$i];
						$POId			 = $p["POId_".$i];
						$totalSlabs		 = $p["totalSlabs_".$i];
						$totalQty		 = $p["totalQty_".$i];
						$POEntryId		 = $p["POEntryId_".$i];
						$dfpPOEntryId = 0;
						$repmcPkId=$p["mcPackingId_".$i];
						
						$insertDailyRepacking=$dailyfrozenreglazingObj->insertDailyRepacking($selectDate,$processId,$freezingStageId,$frozenCodeId,$repmcPkId);
						if ($insertDailyRepacking)
						$dfpPOEntryId = $databaseConnect->getLastInsertedId();
						$dailyfrozenreglazingObj->addPhysicalStkdailyFrozenmain($selectDate, $userId,$dfpPOEntryId,$unit,$processorId);
						$dailymainId = $databaseConnect->getLastInsertedId();
						$dailyfrozenreglazingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processId,$freezingStageId,$frozenCodeId,$repmcPkId);
						$dailyentrymainId = $databaseConnect->getLastInsertedId();
						
						for ($j=1; $j<=$gradeRowCount; $j++) {
									$gradeId = $p["sGradeId_".$j."_".$i];
									$numMC = $p["numMC_".$j."_".$i];
									$numLS = $p["numLS_".$j."_".$i];
									if (($gradeId>0) && ($numMC>0 )) {
											list($thawGrdTotal,$thawGrdLsTotal)=$dailyfrozenreglazingObj->getThaGradeQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
											list($allocGrdTotal,$allocGrdLsTotal)=$dailyfrozenreglazingObj->getAllocQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
											$thaallocTotal=$thawGrdTotal+$allocGrdTotal;
											$thaallocLsTotal=$thawGrdLsTotal+$allocGrdLsTotal;

											?>
											
											<?php
											
											if ($thaallocTotal!=0)
										{
												

												$exisitingRecords = $dailyfrozenreglazingObj->checkRecordsExist($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
													if (sizeof($exisitingRecords)>0) {
													
													$n=0;
													
													foreach ($exisitingRecords as $er){
															
															$gradeUpid=$er[2];
															$mainId=$er[0];
															$n++;
															
															if ($n==1){
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal,$thaallocLsTotal);
																$dailyFrozenPackingMainRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingMain($mainId);
															}
															else{
																$thaallocTotal=0;
																$thaallocLsTotal=0;
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal);
															}
														}
													}
										}
										else if ($thawGrdTotal!=0) {

													$exisitingRecords = $dailyfrozenreglazingObj->checkRecordsExist($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
													if (sizeof($exisitingRecords)>0) {
													
													$n=0;
													
													foreach ($exisitingRecords as $er){
															
															$gradeUpid=$er[2];
															$mainId=$er[0];
															$n++;
															
															if ($n==1){
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$thawGrdLsTotal);
																$dailyFrozenPackingMainRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingMain($mainId);
															}
															else{
																$thawGrdTotal=0;
																$thawGrdLsTotal=0;
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$thawGrdLsTotal);
															}
														}
													}


										}
									/*	else
										{
												echo "***";

												$exisitingRecords = $dailyfrozenreglazingObj->checkRecordsExist($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$selectDate,$gradeId);
													if (sizeof($exisitingRecords)>0) {
													
													
													foreach ($exisitingRecords as $er){
															
																$thawGrdTotal=0;
																$gradeUpid=$er[2];
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal);
															
														}
													}


												echo "&&&";


										}*/


											else	if ($allocGrdTotal!=0) {

													$exisitingRecords = $dailyfrozenreglazingObj->checkRecordsExist($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
													if (sizeof($exisitingRecords)>0) {
													
													$n=0;
													foreach ($exisitingRecords as $er){
															
															$gradeUpid=$er[2];
															$mainId=$er[0];
															$n++;
															if ($n==1){
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
																$dailyFrozenPackingMainRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingMain($mainId);
															}
															else{
																$allocGrdTotal=0;
																$allocGrdLsTotal=0;
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
															}
														}
													}


										}
									else
										{
											
												$exisitingRecords = $dailyfrozenreglazingObj->checkRecordsExist($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
													if (sizeof($exisitingRecords)>0) {
													
													
													foreach ($exisitingRecords as $er){
															
																$allocGrdTotal=0;
																$allocGrdLsTotal=0;
																$gradeUpid=$er[2];
																$mainId=$er[0];
																$dailyFrozenPackingRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
																$dailyFrozenPackingMainRecUp=$dailyfrozenreglazingObj->updateDailyFrozenPackingMain($mainId);
															
														}
													}				


										}
										//Repacking end allocation and thawing
											$insertPOGradeRec = $dailyfrozenreglazingObj->insertDFPPOGradeForRepacking($dfpPOEntryId, $gradeId, $numMC, $numLS);
										}
									if ($dailyentrymainId){
										if ($numMC>0)
										{
									$dailyfrozenreglazingObj->adddailyFrozenGradeEntries($dailyentrymainId,$gradeId,$numMC,$numLS);
										}
								}
								else{
									$errDel="$msg_failPhysicalStockEntry";
								}


						}					
								

	}

# Delete 

	if( $p["cmdDelete"]!=""){
		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)		{
			$dailyFrozenRePackingMainId		=	$p["delId_".$i];
			$dailyFrozenreGlazeId=$p["reglazeId_".$i];
		
			$rmlotId=$p["rmlotId_".$i];
			//$dailyFrozenreGlazeId=$p["delIdReg"];
			if ($dailyFrozenRePackingMainId!="") {
			
				if($rmlotId=='0')
				{
					list($oldMainId)=$dailyfrozenreglazingObj->getOldMainId($dailyFrozenRePackingMainId);
					list($oldEntryId)=$dailyfrozenreglazingObj->getEntryId($oldMainId);
					$olddailygrd=$dailyfrozenreglazingObj->getGradeEntryId($oldEntryId[0]);
					list($oldNumPack)=$dailyfrozenreglazingObj->getMCNumPack($oldEntryId[1]);
					list($oldFilledWt)=$dailyfrozenreglazingObj->getFrozenFilledWt($oldEntryId[2]);

					list($entryId)=$dailyfrozenreglazingObj->getEntryId($dailyFrozenRePackingMainId);
					$dailygrd=$dailyfrozenreglazingObj->getGradeEntryId($entryId[0]);
					list($numPack)=$dailyfrozenreglazingObj->getMCNumPack($entryId[1]);
					list($filledWt)=$dailyfrozenreglazingObj->getFrozenFilledWt($entryId[2]);
					$numtotal='0';
					foreach($olddailygrd as $oldval)
					{	
						$id= $oldval[0];
						$grade_id= $oldval[1];
						$number_mc= $oldval[2];
						$number_loose_slab= $oldval[3];
						$repckQty= $oldval[4];
						$oldtotalrepack=$repckQty*$oldNumPack;
						if($oldtotalrepack=='' || $oldtotalrepack=='0')
						{	
							if($number_mc>0 || $number_loose_slab>0)
							{
								//$numtotal=(($number_mc*$oldNumPack)+$number_loose_slab);
								$numtotal=(($number_mc*$oldNumPack)+$number_loose_slab)*$oldFilledWt;
							}
							//echo "old".$numtotal.','.$oldNumPack.','..'<br/>';
							//echo $id.','.$grade_id.','.$number_mc.','.$number_loose_slab.'<br/>';	
							foreach($dailygrd as $delVal)
							{
								 $delid= $delVal[0];
								 $delgrade_id= $delVal[1];
								 $delnumber_mc= $delVal[2];
								 $delnumber_loose_slab= $delVal[3];
								// $delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab);
								 $delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab)*$filledWt;
								//echo"new".$delnumtotal.','.$numPack.','..'<br/>';
								 if( $delgrade_id==$grade_id)
								 {
									//echo $numtotal.','.$oldNumPack.'<br/>';
									//echo $delnumtotal.','.$numPack.'<br/>';

									$totalNumMCLS= $numtotal+$delnumtotal;
									$totNummCLS=(($totalNumMCLS)/($oldNumPack*$oldFilledWt));
									$deltotalrepack=$delnumber_mc*$numPack;

									$mc= round($totNummCLS);
									//echo $totNummCLS.','.$mc.'<br/>';

									//$ls=$totalNumMCLS % $oldNumPack;
									//$number=explode('.',($totalNumMCLS / $oldNumPack));
									//$mc=$number[0];

									$reglzNew='0';
								$updatedailygrd=$dailyfrozenreglazingObj->updateFrozenPackingGradeReEnter($id, $grade_id, $mc, $reglzNew);
								//echo $mc.','.$ls.'<br/>';
							 }
								//echo $delid.','.$delgrade_id.','.$delnumber_mc.','.$delnumber_loose_slab.'<br/>';	
							}
						}
						else
						{
							//echo $number_mc.','.$repckQty.'<br/>';
							$mc=$number_mc+$repckQty;
							//echo $mc;
							//$repckNew=$number_mc-$repckQty;
							foreach($dailygrd as $delVal)
							{
								$delid= $delVal[0];
								$delgrade_id= $delVal[1];
								$delnumber_mc= $delVal[2];
								$delnumber_loose_slab= $delVal[3];
								$delrePack= $delVal[4];
								if($delrePack=='' || $delrePack=='0')
								{
									//$delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab);
									$delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab)*$filledWt;
								//echo	$delnumtotal.','.$numPack.'<br/>';
									 if( $delgrade_id==$grade_id)
									 {
										$number=($delnumtotal / ($oldNumPack*$oldFilledWt));
										$reglazed=round($number);
										//$reglazed=$number[0];
										$reglzNew=$reglazed-$repckQty;
										
										//echo $mc.','.$reglazed.','.$repckQty.','.$reglzNew.'<br/>';
						$updatedailygrd=$dailyfrozenreglazingObj->updateFrozenPackingGradeReEnter($id, $grade_id, $mc, $reglzNew);
									 }
								}
								else
								{
									
									$err="Cannot delete data with history records";
									
								}
							}
							
						}

					}
					//die();
					//printr($dailygrd);
					//printr($olddailygrd);
			
			###commented on 15-11-2014
				
					if($updatedailygrd)
					{
						$dailyFrozenPackingEntryRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeWithEntryId($entryId[0]);
						$dailyfrozenreglazingObj->deleteFrozenReGlazingEntryRecMain($entryId[0]);
						$dailyFrozenPackingGradeRecDelMain = 
						$dailyFrozenRePackingRecDelMain	=$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecMain($dailyFrozenRePackingMainId);
						$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRec($dailyFrozenreGlazeId);
						$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRec($dailyFrozenreGlazeId);
					}



				/*	list($entryId)=$dailyfrozenreglazingObj->getEntryId($dailyFrozenRePackingMainId);
					//echo $entryid;
					$dailyFrozenPackingGradeRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeWithEntryId($entryId);
					
					$dailyFrozenPackingEntryRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingEntryRecMain($entryId);
					$dailyFrozenRePackingRecDelMain	=$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecMain($dailyFrozenRePackingMainId);
					$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRec($dailyFrozenreGlazeId);
					$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRec($dailyFrozenreGlazeId);*/

					
				}
				else
				{
					
					list($oldMainId)=$dailyfrozenreglazingObj->getOldMainIdRMLotID($dailyFrozenRePackingMainId);
					list($oldEntryId)=$dailyfrozenreglazingObj->getEntryIdRMLotID($oldMainId);
					$olddailygrd=$dailyfrozenreglazingObj->getGradeEntryIdRMLotID($oldEntryId[0]);
					list($oldNumPack)=$dailyfrozenreglazingObj->getMCNumPack($oldEntryId[1]);
					list($oldFilledWt)=$dailyfrozenreglazingObj->getFrozenFilledWt($oldEntryId[2]);

					list($entryId)=$dailyfrozenreglazingObj->getEntryIdRMLotID($dailyFrozenRePackingMainId);
					$dailygrd=$dailyfrozenreglazingObj->getGradeEntryIdRMLotID($entryId[0]);
					list($numPack)=$dailyfrozenreglazingObj->getMCNumPack($entryId[1]);
					list($filledWt)=$dailyfrozenreglazingObj->getFrozenFilledWt($entryId[2]);
					$numtotal='0';
					foreach($olddailygrd as $oldval)
					{	
						$id= $oldval[0];
						$grade_id= $oldval[1];
						$number_mc= $oldval[2];
						$number_loose_slab= $oldval[3];
						$repckQty= $oldval[4];
						$oldtotalrepack=$repckQty*$oldNumPack;
						if($oldtotalrepack=='' || $oldtotalrepack=='0')
						{	
							if($number_mc>0 || $number_loose_slab>0)
							{
								$numtotal=(($number_mc*$oldNumPack)+$number_loose_slab)*$oldFilledWt;
							}
					
							foreach($dailygrd as $delVal)
							{
								 $delid= $delVal[0];
								 $delgrade_id= $delVal[1];
								 $delnumber_mc= $delVal[2];
								 $delnumber_loose_slab= $delVal[3];
								 $delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab)*$filledWt;

								 if( $delgrade_id==$grade_id)
								 {
									$totalNumMCLS= $numtotal+$delnumtotal;
									$totNummCLS=(($totalNumMCLS)/($oldNumPack*$oldFilledWt));
									$deltotalrepack=$delnumber_mc*$numPack;
									$mc= round($totNummCLS);
									$totNummCLS.','.$mc.'<br/>';
									$reglzNew='0';
								$updatedailygrd=$dailyfrozenreglazingObj->updateFrozenPackingGradeReEnterRMLotID($id, $grade_id, $mc, $reglzNew);
								//echo $mc.','.$ls.'<br/>';
							 }
								
							}
						}
						else
						{
							$mc=$number_mc+$repckQty;
							foreach($dailygrd as $delVal)
							{
								$delid= $delVal[0];
								$delgrade_id= $delVal[1];
								$delnumber_mc= $delVal[2];
								$delnumber_loose_slab= $delVal[3];
								$delrePack= $delVal[4];
								if($delrePack=='' || $delrePack=='0')
								{
									$delnumtotal=(($delnumber_mc*$numPack)+$delnumber_loose_slab)*$filledWt;
									 if( $delgrade_id==$grade_id)
									 {
										$number=($delnumtotal / ($oldNumPack*$oldFilledWt));
										$reglazed=round($number);
										$reglzNew=$reglazed-$repckQty;
										
										//echo $mc.','.$reglazed.','.$repckQty.','.$reglzNew.'<br/>';
										$updatedailygrd=$dailyfrozenreglazingObj->updateFrozenPackingGradeReEnterRMLotID($id, $grade_id, $mc, $reglzNew);
									 }
								}
								else
								{
									
									$err="Cannot delete data with history records";
									
								}
							}
							
						}

					}
					
				
					if($updatedailygrd)
					{
						$dailyFrozenPackingEntryRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRMLotIDWithEntryId($entryId[0]);
						$dailyfrozenreglazingObj->deleteFrozenReGlazingEntryRecMainRMLotID($entryId[0]);
						$dailyFrozenPackingGradeRecDelMain = 
						$dailyFrozenRePackingRecDelMain	=$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecMainRMLotID($dailyFrozenRePackingMainId);
						$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRecRMLotID($dailyFrozenreGlazeId);
						$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecRMLotID($dailyFrozenreGlazeId);
					}









					
				/*	list($entryId)=$dailyfrozenreglazingObj->getEntryIdRMLotId($dailyFrozenRePackingMainId);
					//echo $entryid;
					$dailyFrozenPackingGradeRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRMLotIDWithEntryId($entryId);
					
					$dailyFrozenPackingEntryRecDelMain = $dailyfrozenreglazingObj->deleteFrozenReGlazingEntryRecMainRMLotID($entryId);
					$dailyFrozenRePackingRecDelMain	=$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecMainRMLotID($dailyFrozenRePackingMainId);
					$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj->deleteFrozenReGlazingGradeRecRMLotID($dailyFrozenreGlazeId);
					$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenReGlazingMainRecRMLotID($dailyFrozenreGlazeId);
					
				*/			
				}
			}
		}
	if($dailyFrozenRePackingRecDelMain)
		{
			$sessObj->createSession("displayMsg",$msg_succDelDailyFrozenReGlazing);
			$sessObj->createSession("nextPage",$url_afterDelDailyFrozenReGlazing.$selection);
	}
		else
		{
			$errDel	=	$msg_failDelDailyFrozenReGlazing;
		}

		$dailyFrozenRePackingRecDelMain	=	false;
		 $editMode=false;

	}



#Cancel 	
	if ($p["cmdCancel"]!="")
	{
		//echo "hai";
		$addMode	=	false;
		$editMode	=	false;
		/*$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
		
		if($p['editMode']==""){
			$dailyFrozenPackingGradeRecDel = $dailyfrozenreglazingObj-> deleteFrozenPackingGradeRec($entryId);
					
			$frozenPackingEntryRecDel	=	$dailyfrozenreglazingObj->deletePackingEntryRec($entryId);
			#Check Record Exists
			$exisitingRecords			=	$dailyfrozenreglazingObj->checkRecordsExist($mainId);
			if(sizeof($exisitingRecords)==0)
				{
					$dailyFrozenRePackingRecDel	=	$dailyfrozenreglazingObj->deleteDailyFrozenPackingMainRec($mainId);
				}
		}
		$mainId 		= "";
		$p["mainId"]	= "";
		$entryId		= "";
		$p["entryId"] 	= "";*/
	}

#List All Dailyfrozen Packing Records
	


#List All Fishes
$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();

#List All Freezing Stage Record
	$freezingStageRecords		=	$freezingstageObj->fetchAllRecords();
	
#List All EU Code Records
	$euCodeRecords		=	$eucodeObj->fetchAllRecords();

#List All Brand Records
	$brandRecords		=	$brandObj->fetchAllRecords();

#List All Frozen Code Records
	$frozenPackingRecords		=	$frozenpackingObj->fetchAllRecords();
	
#List All MC Packing Records
	$mcpackingRecords		=	$mcpackingObj->fetchAllRecords();

#List All Re Packing Reason Records
	$rePackingRecords		=	$repackingObj->fetchAllRecords();

# select records between selected date
if($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!=""){
	
	//$dateFrom = $g["frozenPackingFrom"];
	$fromdateSt=$g["frozenPackingFrom"];
	$fromdateStp=$g["frozenPackingFrom"];
	$fromdateSt=mysqldateformat($fromdateSt);
	$dateStFrom=$g["frozenPackingFrom"];
	//echo $fromdateSt;
	//$dateStFrom=dateFormat($fromdateSt);
	//echo "kkk";
	//echo $dateStFrom;
	//echo "kkk";
	$tillDate = $g["frozenPackingTill"];
	$tillDatep = $g["frozenPackingTill"];
	//echo "&&&&&&&&&&&&&&$tillDate";
	$tillDate=mysqldateformat($tillDate);
/*} else {
	$dateFrom = $p["frozenPackingFrom"];
	$dateTill = $p["frozenPackingTill"];
}*/
} else {
		/* As on Physical stock Entry last date*/		
		$maxdate= $dailyfrozenpackingObj->getMaxDate();
		//$currYear=Date("Y");
		//$currFinanYear="01/04/$currYear";
		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());

		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):$defaultDFPDate;
		//$maximumdt= ($maxdate[0]!="")?dateFormat($maxdate[0]):date("d/m/Y");
		$dateStFrom=$maximumdt;
		$dateTill = date("d/m/Y");
		$supplierFilterId = "";
	}




	if ($p["cmdSearch"]!="" || ($dateStFrom!="" && $dateTill!="")) {
		
	$dateF			=	explode("/", $dateFrom);
	$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$fromdateSt=$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$dateT			=	explode("/", $dateTill);
	$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
	$isSearched	=	true;
} else {
	$CurrentDay = date("d/m/Y");
	$dateTill = date("d/m/Y");
	
	$dateF			=	explode("/", $dateFrom);
	$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];
	$dateT			=	explode("/", $dateTill);
	$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
	//$Date			=	explode("/",$CurrentDay);
	//$fromDate		=	$Date[2]."-".$Date[1]."-".$Date[0];
	//$tillDate		=	$Date[2]."-".$Date[1]."-".$Date[0];
}

//$numPacks = $eCriteria[6];


## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------
	//echo $tillDate."---------";
	$fromdateSt=mysqldateformat($dateStFrom);
	$dailyFrozenRePackingRecords		=	$dailyfrozenreglazingObj->getDFPForDateRange($fromdateSt, $tillDate,$offset, $limit);
	//$dailyFrozenRePackingRecordSize	=	sizeof($dailyFrozenRePackingRecords);
	$dailyFrozenRePackingRecordSize	=sizeof($dailyfrozenreglazingObj->getDFPReForDateRange($fromdateSt, $tillDate));
	$numrows=sizeof($dailyfrozenreglazingObj->getDFPReForDateRange($fromdateSt, $tillDate));
			
			## -------------- Pagination Settings II -------------------
			$maxpage	=	ceil($numrows/$limit);

	
	//$dailyFrozenRePackingRecordSize	=	sizeof($dailyFrozenRePackingRecords);
	$dailyFrozenPackingRecordSize=sizeof($dailyFrozenRePackingRecords);
	

	if ($editMode) {
		$heading	=	$label_editDailyFrozenReGlazing;
	} else {
		$heading	=	$label_addDailyFrozenReGlazing;
	}
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/dailyfrozenrepacking.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmDailyFrozenReGlazing" action="DailyFrozenReGlazing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?php
		//echo $editMode;
			if( $editMode || $addMode)
			{
				$editId=$p["editId"];
				$dailyFrozenreGlazeId=$p["delIdReg"];
		$editCriteria		= $p["editCriteria"];
		//echo $editCriteria;
		$eCriteria		= explode(",",$editCriteria);		
		$processId		= trim($eCriteria[0]);
		$freezingStage		= trim($eCriteria[1]);
		$frozenCode			= trim($eCriteria[2]);
		$displayEditMsg		= strtoupper(trim($eCriteria[3]));
		$dis=explode("-",$displayEditMsg);
		$selMCPkgId			= trim($eCriteria[4]);
		$unit=$eCriteria[6];
		$processorId=$eCriteria[5];
		//echo "The unit is $unit $processorId";
		
		$mcpackingRec	= $mcpackingObj->find($selMCPkgId);
		$numPacks	= $mcpackingRec[2];

				
				$productRecs = $frozenStockAllocationObj->getAllocateProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);			
				$productRecSize = sizeof($productRecs);
				
				//$gradeRecs = $frozenStockAllocationObj->getProductionGradeRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $selMCPkgId);
				$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
				
				$leftBdr 	= "border-left: 1px solid #999999;";
				$rightBdr 	= "border-right: 1px solid #999999;";
				$topBdr 	= "border-top: 1px solid #999999;";
				$bottomBdr 	= "border-bottom: 1px solid #999999;";
				$fullBdr	= "border: 1px solid #999999;";
			
		
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  >
					<tr>
						<td   bgcolor="white" nowrap  style="<?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" >
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<?php if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenReGlazing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDFPReglazing(document.frmDailyFrozenReGlazing);"></td>
												
												<?php } else{?>

												
											  <td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenReGlazing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenRePacking(document.frmDailyFrozenRePacking);">&nbsp;&nbsp;												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
											  <td nowrap class="fieldName"></td>
										  </tr>
											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>
											
											<tr>
											  <td colspan="2" style="padding-left:60px;">&nbsp;</td>
										  </tr>
											<tr>
											  <td colspan="2" align="center">
											  <table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
        <td valign="top">
		<table width="585">	
		<tr>
                                                 <td class="fieldName" colspan=3><div align="center">Date:<span class="listing-item">
                                                   <?php		
												 
							if($selDate=="") $selDate=date("d/m/Y");
											
							?>
                                                   <input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autocomplete="off" />
                                                 </span></div></td>
                                </tr>
		<tr><TD class="listing-head" nowrap="true">
			PRODUCTION DETAILS OF <?=$displayEditMsg?>
			<?php
				$leftBdr 	= "border-left: 1px solid #999999;";
				$rightBdr 	= "border-right: 1px solid #999999;";
				$topBdr 	= "border-top: 1px solid #999999;";
				$bottomBdr 	= "border-bottom: 1px solid #999999;";
				$fullBdr	= "border: 1px solid #999999;";
			?>
		</TD>
		</tr>
		<TR>
		<TD align="center">
			<!--<table width="533" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">-->

			<table width="533" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
			<!--<tr bgcolor="#f2f2f2"  align="center">	
				
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+4?>">&nbsp;</td>	-->
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
					SLABS OF EACH GRADE/COUNT
				</td>-->
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">&nbsp;</td>			
			</tr>-->
			<tr bgcolor="#f2f2f2"  align="center">
			  <td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="4">&nbsp;</td>
			  <td width="240" colspan="<?=sizeof($gradeRecs)+1?>" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$topBdr.$bottomBdr?>"><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SLABS OF EACH GRADE/COUNT</span></td>
			  
			  <td width="52" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$topBdr.$bottomBdr?>">TOTAL</td>
			  </tr>
			<tr bgcolor="#f2f2f2"  align="center">
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="4">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
				
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head" title="Set Purchase order">
				
					&nbsp;</td>			
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>-->
			
				<?php
					$g = 1;
					foreach ($gradeRecs as $gR) {
						$gId = $gR[0];
						$gradeCode = $gR[1];
				?>
					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
						<?=$gradeCode?>
						<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />					</td>
				<?php
						$g++;
					}
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
				<td width="72" nowrap class="listing-head" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">QTY (KG)</td>
			</tr>
			<tr bgcolor="#f2f2f2"  align="center">
			
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="2" ><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SET FROZEN CODE</span></td>
	<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"   ><span class="listing-head" style="padding-left:2px;padding-right:2px; ">SET MC PKG</span></td>			

				
				<td width="54" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">&nbsp;</td>
				
				<?php
					$totAvailableMC = 0;
					$totAvailableLS = 0;
					$dateFrom=mysqlDateFormat($maximumdt);
					foreach ($gradeRecs as $gR) {
						$j++;
						
						$sGradeId   = $gR[0];
						list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $selMCPkgId,$dateFrom,$tillDate);
						$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
						
						list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$selMCPkgId,$dateFrom,$sGradeId);
						$availableNetMC=$availableMC-$thawingGrdTotal;
						$totAvailableMC += $availableNetMC;
						$totAvailableLS += $availableLS;
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
					<table cellpadding="1" cellspacing="0" width="100%">
					<tr>
						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
						<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
							<b><?=($availableNetMC!=0)?$availableNetMC:"&nbsp;"?></b>						</td>
					</tr>
					<!--<tr>
						<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
					</tr>-->
					<!--<tr>
						<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
							<b><?//=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
						</td>
					</tr>-->
					</table>				</td>
				<?php
					} // Grade Loop Ends here

					# Total Available Slabs
					$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
				?>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
					<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
&nbsp;				</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
			</tr>
			<?php
				$i = 0;
				$pkgGroupArr = array();
				$lsPkgGroupArr = array();
				foreach ($productRecs as $pr) {
					$i++;
					
				$dFrznPkgEntryId = $pr[0];
					$frozenLotId 	= $pr[1];
					$mcPackingId	= $pr[2];
					$mcPkgCode	= $pr[3];
					
					$selProcessCodeId=$pr[7];
					$selFreezingStageId=$pr[8];
					$selFrozenCodeId=$pr[9];
					$frznStkMCPkgId=$pr[10];
					

								
			?>	
			<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
				<!--<td nowrap style="padding-left:2px;padding-right:2px; <?//=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
					<table cellpadding="0" cellspacing="0">
						<TR>
						
							
							<?php 
				if ($thawingStatus==0)
					{
				?>
				<TD style="padding-left:5px;padding-right:5px;">
								<a onclick="setAllocateRowStatus(this);" href="javascript:void(0);" id="allocateRemoveLink_<?=$i?>"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/></a>
								</TD>
								<?php }?>
							
							
						</TR>
					</table>	-->									
			
					<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
					<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
					<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
					<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
				<!--</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?//=$rightBdr.$bottomBdr?>" class="listing-item">	
				

			
						
						



				&nbsp;
				</td>-->
				 <?php
					  $lastVal=explode(" ",$dis[2]);
							//print_r($lastVal);
							$n=count($lastVal);
							//echo $n;
							$lastVal=$lastVal[$n-2];
					  list($unit,$freeId,$glazeFreezId,$declWt,$frozID)=$frozenStockAllocationObj->getfrozenCodeValue($frozenCode);
					  $selFrozenCodeId=$frozenCode;
					  $getF=$frozenpackingObj->find($frozenCode);
						$selFrozenCode=$getF[0];
						//echo "---".$selFrozenCode;
						//$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
						$selFrozenCode1=$getF[1];
							//echo $unit;
							//echo $freeId;
							//echo $glazeFreezId;
							//$frozenCodeValues=$frozenStockAllocationObj->getAllfrozenCodeValues($unit,$freeId,$glazeFreezId,$declWt,$lastVal);
							//echo "hai";
							//echo sizeof($frozenCodeValues);
							$glazeId=$frozenpackingObj->frznPkgglaze($frozenCode);
							$frozenCodeValues=$frozenStockAllocationObj->getFrozQtygl($processId,$glazeId);
							$k=1;
							?>
							<input type="hidden" id="hidselFrozenCode_1" name="hidselFrozenCode_1" value=<?=$frozenCode;?> />
							<input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
				<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
				<td colspan="2" rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>"><span class="listing-head" style="padding-left:2px;padding-right:2px; ">
				<select id="selFrozenCode_<?=$k?>" name="selFrozenCode_<?=$k?>" onchange="xajax_assignFrzPackChg(this.value);xajax_getFilledWt(document.getElementById('selFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('selFrozenCode_<?=$k?>').value, '');" >
												<?php		
												if (sizeof($frozenCodeValues)>0) {	
												?>
													<option value="<?=$frozenCode?>" ><?=stripslashes($selFrozenCode1)?></option>
													<?php foreach($frozenCodeValues as $frozenPackingId=>$frozenPackingCode) {
														//$selFrznPkg = ($selFrozenCodeId==$frozenPackingId)?"selected":"";
														//if ($frozenPackingId==$frozenCode) continue;
												?>	
													<option value="<?=$frozenPackingId?>"  <?php if ($selFrozenCodeId==$frozenPackingId){?> selected <?php }?> ><?=stripslashes($frozenPackingCode)?></option>	
												<?php
														}
												} else {
												?>
												<option value="">-- Select --</option>
												<?php
												}
												?>	
												</select>
				 
				</span>
				
				<input type=hidden name="hidflag_<?=$i?>" value="c" id="hidflag_<?=$i?>" readonly />
				</td>
				<td  rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>"><span class="listing-head" style="padding-left:2px;padding-right:2px; ">
				 <!-- <select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPack(document.getElementById('mcPackingId_<?=$i?>').value, '<?=$i?>');"  >
                    <option value="0">--Select--</option>
                    <?php
						  foreach($mcpackingRecords as $mcp) {
							$mcPkgId		= $mcp[0];
							$mcpackingCode		= stripSlash($mcp[1]);
							$selected		= ($mcPackingId==$mcPkgId)?"selected":"";
						?>
                    <option value="<?=$mcPkgId?>" <?=$selected?>>
                      <?=$mcpackingCode?>
                      </option>
                    <? }?>
                  </select>-->
				  <input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
						 <select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPack(document.getElementById('mcPackingId_<?=$i?>').value, '<?=$i?>');"  >
                    <option value="0">--Select--</option>
                    <?php
						  foreach($mcpackingRecords as $mcp) {
							$mcPkgId		= $mcp[0];
							$mcpackingCode		= stripSlash($mcp[1]);
							$selected		= ($mcPackingId==$mcPkgId)?"selected":"";
						?>
                    <option value="<?=$mcPkgId?>" <?=$selected?>>
                      <?=$mcpackingCode?>
                      </option>
                    <? }?>
                  </select>



				</span>
				
				<input type=hidden name="hidflag_<?=$i?>" value="c" id="hidflag_<?=$i?>" readonly />
				</td>
				<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item"><span class="listing-item" style="padding-left:2px;padding-right:2px;">MC</span></td>
				<?php
					# MC Section
					$j=0;
					$totNumMC = 0;
					$totNumLS = 0;
					foreach ($gradeRecs as $gR) {
						$j++;
						$sGradeId   = $gR[0];
						$gradeCode = $gR[1];
						# Find MC
						$allocateGradeEntryId = "";
						if ($POEntryId>0) {
							//list($allocateGradeEntryId, $numMC, $numLS) = $frozenStockAllocationObj->getAllocatedSlab($POEntryId, $sGradeId);
						} else {
							//list($gradeEntryId, $numMC, $numLS) = $frozenStockAllocationObj->getSlab($dFrznPkgEntryId, $sGradeId);
						}



						
						
						$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
						$lsPkgGroupArr[$sGradeId] += $numLS;
							list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $selMCPkgId,$dateFrom,$tillDate);
							//new Code
							//$totNumMC += $availableMC;
							$totNumLS += $availableLS;
							list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$selMCPkgId,$dateFrom,$sGradeId);
							$availableNetMC=$availableMC-$thawingGrdTotal;
							$totNumMC += $availableNetMC;
							//echo $selMCPkgId;
							$mcpackingRec	= $mcpackingObj->find($selMCPkgId);
							$numPacks	= $mcpackingRec[2];
							$hidtotSlabs=$availableNetMC*$numPacks;
							if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
							





				?>
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<table cellpadding="1" cellspacing="0" width="100%">
					<tr>
						<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
						
							<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
							<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
							<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />-->
							<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />
							<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
							<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />
							<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenRePacking',this);" />						</td>
					</tr>
					<tr>
						<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
					</tr>
					<tr>
						<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
							<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>		
							
							<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
							<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>		
							</td>
					</tr>
					</table>				</td>
					
				<?php
					} // Grade Loop Ends here

					# Total Slabs
					$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;
					

					#total Qty	
					$totalQty	= $totalSlabs*$filledWt;
				?>	
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />				</td>
				<td rowspan="2" nowrap class="listing-item" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>">
					<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />				</td>
			</tr>
			<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">
			  <td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item"><span class="listing-item" style="padding-left:2px;padding-right:2px;">LS</span></td>
			  </tr>
			<?php
				} // Loop Ends here
			?>
			<tr>
			  <td width="21"></td>
			  <td width="21" height="10"></td>
			  <td width="59"></td>
			</tr>
			<tr>
				<td style="padding-left:10px;padding-right:10px;" nowrap colspan="3">&nbsp;				</td>
			</tr>
			<tr bgcolor="White" >
			  <td>&nbsp;</td>
			  <input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
			<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
				<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="<?//=$bottomBdr?>">&nbsp;</td>
				<?php					
					foreach ($gradeRecs as $gR) {
				?>
					<td style="<?//=$bottomBdr?>">&nbsp;</td>
				<?php
					}
				?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?php				
				$fieldRowSize = $productRecSize+1;
				$p = 1;
				$totAllocatedMC = 0;
				$totAllocatedLS = 0;
				foreach ($pkgGroupArr as $pga=>$gradeArr) {					
					$selMcPkgCode = $pga;
			?>
			<!--<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
				<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
				<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG1</TD>
				<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
				<?php					
					foreach ($gradeRecs as $gR) {
						$gradeId = $gR[0];
						$mcQty  = $gradeArr[$gradeId];
						$totAllocatedMC += $mcQty;
				?>
				<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>"><strong><?=($mcQty!=0)?$mcQty:"&nbsp;";?></strong></TD>
				<?php
					}
				?>
				<TD colspan="2">&nbsp;</TD>			
			</tr>-->
			<?php
					$p++;
				}
			?>
			<input type="hidden" name="hidSummaryTblRowCount" id="hidSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
			<input type="hidden" name="hidProdnRowCount" id="hidProdnRowCount" value="<?=$i?>" readonly="true" />
		<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
			<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
			<input type="hidden" name="hidLS2MCType" id="hidLS2MCType" value="<?=$LSToMCConversionType?>" readonly />
				<input type="hidden" name="hidSelQuickEntryList" id="hidSelQuickEntryList" value="<?=$selQuickEntryList?>"/>
				<input type="hidden" name="hidEntrySel" id="hidEntrySel" value="<?=$entrySel?>"/>
		<!-- QEL Selection Hidden Starts here  -->
				<input type="hidden" name="qeFreezingStageId" id="qeFreezingStageId" value="<?=$qeFreezingStageId?>"/>
				<input type="hidden" name="qeEUCodeId" id="qeEUCodeId" value="<?=$qeEUCodeId?>"/>
				<input type="hidden" name="qeBrandId" id="qeBrandId" value="<?=$qeBrandId?>"/>
				<input type="hidden" name="qeFrozenCodeId" id="qeFrozenCodeId" value="<?=$qeFrozenCodeId?>"/>
				<input type="hidden" name="qeMCPackingId" id="qeMCPackingId" value="<?=$qeMCPackingId?>"/>
				<input type="hidden" name="qeFrozenLotId" id="qeFrozenLotId" value="<?=$qeFrozenLotId?>"/>
				<input type="hidden" name="qeExportLotId" id="qeExportLotId" value="<?=$qeExportLotId?>"/>
				<input type="hidden" name="qeQualityId" id="qeQualityId" value="<?=$qeQualityId?>"/>
				<input type="hidden" name="qeCustomerId" id="qeCustomerId" value="<?=$qeCustomerId?>"/>
				<input type="hidden" name="filledWt" id="filledWt" value="<?=$filledWt?>" readonly/>
				<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" />
				<input type="hidden" name="hidMCPkgCode" id="hidMCPkgCode" value="<?=$stkAllocateMCPkgCode?>" />
				<input type="hidden" name="hidDelAllocationArr" id="hidDelAllocationArr" value="" />
			<input type="hidden" name="hidProcessId" id="hidProcessId" value="<?=$processId?>" readonly />
			<input type="hidden" name="hidProcessorId" id="hidProcessorId" value="<?=$processorId?>" readonly />
			<input type="hidden" name="hidunit" id="hidunit" value="<?=$unit?>" readonly />
				<input type="hidden" name="hidFreezingStage" id="hidFreezingStage" value="<?=$freezingStage?>" readonly />
				<input type="hidden" name="hidFrozenCode" id="hidFrozenCode" value="<?=$frozenCode?>" readonly />
				<input type="hidden" name="hidMCPkgId" id="hidMCPkgId" value="<?=$selMCPkgId?>" readonly />
				<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
		<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />

			<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
			<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productRecSize?>" readonly="true" />
			<!--<tr bgcolor="White">-->
				<!--<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; border-bottom: 1px solid #ffffff;">&nbsp;</TD>-->
				<!--<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?//=$leftBdr.$rightBdr.$bottomBdr?>">LS SLABS</TD>-->
				<!--<TD style="<?//=$rightBdr.$bottomBdr?>">&nbsp;</TD>-->
				<?php					
			/*foreach ($gradeRecs as $gR) {
						$gradeId = $gR[0];
						$lsQty  = $lsPkgGroupArr[$gradeId];
						$totAllocatedLS += $lsQty;*/
				?>
			<!--	<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" id="LS_<?=$gradeId?>"><strong><?=($lsQty!=0)?$lsQty:"&nbsp;";?></strong></TD>-->
					<?php //} 
								
				
				
				// Grade Loop ends here

					# Total Allocated Slabs
					//$totAllocatedSlabs 	= ($totAllocatedMC*$numPacks)+$totAllocatedLS;
				?>
				<!--<TD colspan="2"><input type="hidden" name="totAllocatedSlabs" id="totAllocatedSlabs" value="<?//=$totAllocatedSlabs?>" readonly="true" />&nbsp;</TD>-->				
			<!--</tr>-->
			</table>		</TD>
		</TR>		
		</table>
		
		
		
		</td>
				 	 </tr>
					<? if($fishId!="" && $processId!=""){?>
					<tr>
					  <td colspan="2" align="center"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                         <tr>
                                             <td>
					<table align="center">
					<tr><td>
					 <fieldset>
					 <legend class="listing-item">Grade</legend>
			 		<iframe id="iFrame1" 
src ="DailyFrozenRePackingGrade.php?mainId=<?=$mainId?>&fishId=<?=$fishId?>&process=<?=$processId?>" width="500" frameborder="0" height="300" name="iFrame1"></iframe>
					  </fieldset>
												  </td></tr></table>												  </td>
                                                   </tr>
                                                
                                              </table></td>
										  </tr>
										  <? }?>
											<tr>
											  <td colspan="2" align="center"><table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td valign="top"><!--<table width="200">
                                                    
                                                    
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Reason for Repacking </td>
                                                      <td>
													  <? if($p["reasonRePack"]!="") $reasonRePack = $p["reasonRePack"];?>
													  <select name="reasonRePack" id="reasonRePack">                                                      <option value="">-- Select--</option>
                                                        <?
													 foreach($rePackingRecords as $rpr)
														{
														$rePackingId		=	$rpr[0];
														$rePackingCode		=	stripSlash($rpr[1]);
														$rePackingReason	=	stripSlash($rpr[2]);
														$selected		=	"";
														if($reasonRePack==$rePackingId || $repackReasonId==$rePackingId)  $selected	=	" selected ";
													  
													  ?>
													  <option value="<?=$rePackingId?>" <?=$selected?>><?=$rePackingCode?></option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of New Inner Packs used</td>
                                                      <td>
													  <? if($p["numNewInnerPack"]!="") $numNewInnerPack = $p["numNewInnerPack"];?>
													  <input name="numNewInnerPack" type="text" id="numNewInnerPack" size="4" value="<?=$numNewInnerPack?>"></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of Labels / Header Cards Used</td>
                                                      <td>
													  <? if($p["numLabelCard"]!="") $numLabelCard = $p["numLabelCard"];?>
													  <input name="numLabelCard" type="text" id="numLabelCard" size="4" value="<?=$numLabelCard?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">No of New MC Used</td>
                                                      <td>
													  <? if($p["numNewMC"]!="") $numNewMC = $p["numNewMC"];?>
													  <input name="numNewMC" type="text" id="numNewMC" size="4" value="<?=$numNewMC?>"></td>
                                                    </tr>
                                                    
                                                  </table>--></td>
                                                  <td valign="top"><!--<table width="200">
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked EU Code</td>
                                                      <td>
													  <? if($p["rePackEUCode"]!="") $rePackEUCode = $p["rePackEUCode"];?>
													  <select name="rePackEUCode" id="rePackEUCode">
                                                        <option value="">-- Select--</option>
                                                        <?
													  foreach($euCodeRecords as $eucr)
														{
														$euCodeId		=	$eucr[0];
														$euCode			=	stripSlash($eucr[1]);
														
														$selected		=	"";
														if($rePackEUCode==$euCodeId)  $selected	=	" selected ";
													  
													  ?>
                                                        <option value="<?=$euCodeId?>" <?=$selected?>>
                                                          <?=$euCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked Brand</td>
                                                      <td>
													  <? if($p["rePackBrand"]!="") $rePackBrand =$p["rePackBrand"];?>
													  <select name="rePackBrand" id="rePackBrand">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($brandRecords as $br)
														{
														$brandId		=	$br[0];
														$brandName		=	stripSlash($br[2]);
														$customerName	=	stripSlash($br[3]);
														$displayBrand   = 	$brandName."&nbsp;(".$customerName.")";
														$selected		=	"";
														if($rePackBrand==$brandId)  $selected	=	" selected ";
													  ?>
														<option value="<?=$brandId?>" <?=$selected?>><?=$displayBrand?></option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked Frozen Code</td>
                                                      <td nowrap="nowrap">
													  <? if($p["rePackFrozenCode"]!="") $rePackFrozenCode=$p["rePackFrozenCode"];?>
													  <select name="rePackFrozenCode" id="rePackFrozenCode">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($frozenPackingRecords as $fpr)
														{
														$frozenPackingId		=	$fpr[0];
												
														$frozenPackingCode		=	stripSlash($fpr[1]);													  
														$selected		=	"";
														if($rePackFrozenCode==$frozenPackingId)  $selected	=	" selected ";
														
													  ?>
                                                        <option value="<?=$frozenPackingId?>" <?=$selected?>>
                                                          <?=$frozenPackingCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Repacked MC Pkg</td>
                                                      <td nowrap="nowrap">
													  <? if($p["rePackMCPacking"]!="") $rePackMCPacking = $p["rePackMCPacking"];?>
													  <select name="rePackMCPacking" id="rePackMCPacking">
                                                        <option value="">-- Select --</option>
                                                        <?
													  foreach($mcpackingRecords as $mcp)
														{
														$mcpackingId		=	$mcp[0];
														$mcpackingCode	=	stripSlash($mcp[1]);
														$selected		=	"";
														if($rePackMCPacking==$mcpackingId)  $selected	=	" selected ";
													  ?>
                                                        <option value="<?=$mcpackingId?>" <?=$selected?>>
                                                          <?=$mcpackingCode?>
                                                        </option>
                                                        <? }?>
                                                      </select></td>
                                                    </tr>
                                                    </table>--></td>
                                                </tr>
                                              </table></td>
										  </tr>
											<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenReGlazing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDFPReglazing(document.frmDailyFrozenReGlazing);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFrozenRePacking.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenRePacking(document.frmDailyFrozenRePacking);">&nbsp;&nbsp;												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
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
			
				$leftBdr 	= "border-left: 1px solid #999999;";
				$rightBdr 	= "border-right: 1px solid #999999;";
				$topBdr 	= "border-top: 0.5px solid #999999;";
				$bottomBdr 	= "border-bottom: 1px solid #999999;";
				$fullBdr	= "border: 1px solid #999999;";
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td  >
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  >
					<tr>
						<td   bgcolor="white"  style="<?=$leftBdr.$rightBdr.$bottomBdr.$topBdr ?>" >
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Frozen Re-Glazing </td>
								    <td background="images/heading_bg.gif" class="pageName" align="right" ><table cellpadding="0" cellspacing="0" width="50%">
                      <tr> 
					  	<td class="listing-item">As&nbsp;On:</td>
                                    <td nowrap="nowrap"> 
                            <? 
							
							if($dateStFrom=="") $dateStFrom=date("d/m/Y");
							
							?>
                            <input type="text" id="frozenPackingFrom" name="frozenPackingFrom" size="8" value="<?=$dateStFrom?>"></td>
						            <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					     if($dateTill=="") $dateTill=date("d/m/Y");
				  ?>
                                      <input type="text" id="frozenPackingTill" name="frozenPackingTill" size="8"  value="<?=$dateTill?>"></td>
							        <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateDailyFrozenPackingSearch(document.frmDailyFrozenRePacking);"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<!--<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFrozenRePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenRePacking.php',700,600);"><? }?></td>
											</tr>-->
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
		<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
<?
	if (sizeof($dailyFrozenRePackingRecordSize)>0) {
		$i	=	0;
	?>
	 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+8;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>







		<?php
		
		if( sizeof($dailyFrozenRePackingRecords) > 0 )
			{
			$i	=	0;
		?>
		<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
		<!--<td class="listing-head" nowrap>&nbsp;&nbsp;Date</td>
		<td class="listing-head" >Fish</td>-->	<td class="listing-head" >RM LotID</td>
												<td class="listing-head" >Process Code</td>
												<td class="listing-head" >Freezing Stage</td>
												<!--<td class="listing-head" >EU Code</td>
												<td class="listing-head">Brand</td>-->
												<td class="listing-head">Frozen Code&nbsp; </td>
												<td class="listing-head">MC Pkg</td>
												<td class="listing-head" >No.of MCs</td>
												<td class="listing-head" >No.of LSs</td>
		<!--<td class="listing-head">Re-Packed Quantity</td>-->
												<? //if($edit==true){?>
												<!--<td class="listing-head" width="45"></td>-->
<? //}?>
	</tr>
	<?php
	foreach($dailyFrozenRePackingRecords as $dfrp)
	{
		$i++;
		$dailyFrozenPackingMainId	=	$dfrp[0];
		$entryDate 	=	$dfrp[16];
		$selectedDate	=	dateFormat($entryDate);
		$fish	=	$fishmasterObj->findFishName($dfrp[2]);
		$processCode = $processcodeObj->findProcessCode($dfrp[3]);
		$selProcessCode=$processCode;
		$selProcessCodeId=$dfrp[3];
		$freezingStage = $freezingstageObj->findFreezingStageCode($dfrp[4]);
		$selFreezingStageId=$dfrp[4];
		$selFreezingStage=$freezingStage;
		$eUCode = $eucodeObj->findEUCode($dfrp[4]);
		$brand = $brandObj->findBrandCode($dfrp[5]);
		$frozenCode = $frozenpackingObj->findFrozenPackingCode($dfrp[5]);
		$selFrozenCode=$frozenCode;
		$selFrozenCodeId=$dfrp[5];
		$mCPackingCode = $mcpackingObj->findMCPackingCode($dfrp[11]);
		$selMCPkgCode=$mCPackingCode;
		$selMCPackingId=$dfrp[11];
		$rePackingReason	=	$repackingObj->findRePackingReason($dfrp[8]);
		$flag=$dfrp[19];
		$unit=$dfrp[20];
		$processor_id=$dfrp[21];
		$numLS=$dfrp[22];
		$reglazemainid=$dfrp[23];
		$rmlotid=$dfrp[24];
		
		($rmlotid>0) ? $rmlotidNm=$dfrp[25]:$rmlotidNm='';
		//echo $rmlotidNm;
		# Edit criteria
		$selEditCriteria = "$selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selProcessCode - $selFreezingStage - $selFrozenCode - $selMCPkgCode, $selMCPackingId,$unit,$processor_id";

		//echo $selEditCriteria;
		
		//list($rePackedQty)=$dailyfrozenreglazingObj->getRepackGradeQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromDate);
		list($thawingTotal)=$dailyfrozenreglazingObj->getThaQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromdateSt,$tillDate);
		
		
		list($allocatedTotal)=$dailyfrozenreglazingObj->getAllocQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromdateSt,$tillDate);
		//echo "Alloc$allocatedTotal";
		if (($flag==1) || ($flag==0))
		{
		$numMCs=$dfrp[14]-($thawingTotal+$allocatedTotal);
		if ($numMCs<0)
			{
				$numMCs="";
			}
		//$numMCs=$dfrp[14];
		}
		else
		{
			$numMCs=$dfrp[14];
		}
		$disabled="";
		if ($flag==2)
		{
			//$disabled="disabled";

		}
		$repackMcSum=$numMCs+$repackMcSum;
		$repackLSSum=$numLS+$repackLSSum;
		
		?>
	<tr  bgcolor="WHITE"  >
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyFrozenPackingMainId;?>" onClick="assignValue(this.form,<?=$reglazemainid;?>,'delIdReg'); assignValue(this.form,<?=$rmlotid?>,'hidRMLotID');" ><?//=$dailyFrozenPackingMainId;?>
		<input type='hidden' name='reglazeId_<?=$i?>' id='reglazeId_<?=$i?>' value="<?=$reglazemainid;?>"/>
		<input type='hidden' name='rmlotId_<?=$i?>' id='rmlotId_<?=$i?>' value="<?=$rmlotid;?>"/>
		
		</td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?=$selectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$fish?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$rmlotidNm?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$processCode?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$freezingStage?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?//=$eUCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?//=$brand?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$frozenCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px;"><?=$mCPackingCode?></td>
		<td class="listing-item" nowrap style="padding-left:10px;" align="right"><?=($numMCs!=0)?$numMCs:"";?></td>
		<td class="listing-item" nowrap style="padding-left:10px;" align="right"><?=($numLS!=0)?$numLS:"";?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px;"><?//=$rePackedQty;?></td>-->
		<? //if($edit==true){?>
		<!--<td class="listing-item" width="45" align="center"><input type="submit" value="Edit" name="cmdEdit" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId');assignValue(this.form,'<?=$selEditCriteria?>','editCriteria'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyFrozenReGlazing.php';" <?php echo $disabled;?>></td>-->
		<? //}?>
	</tr>
	
		<?
			}
		?>
		<tr  bgcolor="WHITE"  >
	  <td>&nbsp;</td>
	  <td colspan="4" nowrap class="listing-item" style="padding-left:10px;">&nbsp;</td>
	  <td class="listing-item" nowrap style="padding-left:10px;"><span class="listing-head" style="padding-left:10px; padding-right:10px;">Total:</span></td>
	  <td class="listing-item" nowrap style="padding-left:10px;" align="right">
	    <?=$repackMcSum;?>
	  </td>
	  <td class="listing-item" align="right"><?=$repackLSSum;?></td>
	   <!--<td class="listing-item" align="right">&nbsp;</td>-->
	  </tr>
	<input type="hidden" name="editCriteria" id="editCriteria" value="<?=$editCriteria?>" readonly="true">
	<input type="hidden" name="editId"  id="editId" value="<?=$editId?>">
	<input type="hidden" name="delIdReg" id="delIdReg" value="<?=$dailyFrozenreGlazeId?>">
	<input type="hidden" name="hidRMLotID" id="hidRMLotID" value="<?=$rmlotid?>">
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	
	<!--input type="hidden" name="editFrozenPackingEntryId" value="<?=$frozenPackingEntryId;?>"-->
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">

	<tr bgcolor="WHITE" >
            <td class="listing-item" colspan=8 align="center"><?//=$dailyFrozenPackingMainId;?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onclick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);" /></td>
            </tr>
		<?
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>



	 <? if($maxpage>1){?>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+8;?>" style="padding-right:10px">
			<div align="right">
			<?php 				 			  
			$nav  = '';
			for($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
  				} else {
				      	$nav.= " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\" class=\"link1\">$page</a> ";
	   			}
			}
			if ($pageNo > 1) {
		   		$page  = $pageNo - 1;
   				$prev  = " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\"><<</a> ";
	 		} else {
   				$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
			}
			if ($pageNo < $maxpage)	{
		   		$page = $pageNo + 1;
   				$next = " <a href=\"DailyFrozenReGlazing.php?pageNo=$page&flag=1&frozenPackingFrom=$fromdateStp&frozenPackingTill=$tillDatep&stocktype=$stocktype&packType=$packType&reportType=$reportType\"  class=\"link1\">>></a> ";
	 		} else {
   				$next = '&nbsp;'; // we're on the last page, don't print next link
   				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr><? }?>

 
	<?php
			} // Main loopEnds here 				
	?>
	</table>
	
	         <input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
	      <!--input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>"-->
	    
	    <table width="200" border="0" align="center">
         <!-- <tr>
            <td><?//=$dailyFrozenPackingMainId;?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onclick="return cfmDelete(this.form,'delGId_',<?=$dailyFrozenPackingRecordSize;?>);" /></td>
            </tr>-->
        </table>	    
	    <p>&nbsp; </p></td>
	</tr>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<!--<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFrozenRePackingRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFrozenRePacking.php',700,600);"><? }?></td>
											</tr>-->
										</table>									</td>
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
			<td height="10"></td>
		</tr>	
	</table>
	
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
	
	 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
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
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
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
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>