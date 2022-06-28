<?php

// Frozen Stock Allocation is linked with Daily Frozen Packing
	require("include/include.php");
	require_once("lib/FrozenStockAllocation_ajax.php");
	//require_once("lib/dailyfrozenrepacking_ajax.php");
	ob_start();

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= false;
	$allocateMode	= false;
	$isSearched		= false;
	$entrySection 	= true;
	

	$selection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"]."&pageNo=".$p["pageNo"]."&filterProcessCode=".$p["filterProcessCode"];
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;

	//$allocatePOId=0;
	
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
	
	# Checking day's valid pc enabled/disabled
	$validPCEnabled = $manageconfirmObj->pkgValidPCEnabled();		
	//----------------------------------------------------------	
	//echo "hii";
	
	//$mcpackingRecords		=	$mcpackingObj->fetchAllRecords();
	$mcpackingRecords		=	$mcpackingObj->fetchAllRecordsActivemcpacking();
	
	
		

	if ($g[all]!="")
	{
		$thawingStatus=$g[all];
		$rmLotID	=	$p["rmLotID"];
	}
	else
	{
		$rmLotID	=	$p["hidoptrmLotID"];
		if ($p["optionrpkrgz"]==""){
		$thawingStatus=$p["thawFlag"];
		} else 	if ($p["optionrpkrgz"]==2){
		$thawingStatus=2;
		} else 	if ($p["optionrpkrgz"]==3){
		$thawingStatus=3;
	
	}

	}
	if ($g[all]==-1)
	{
		$flag=1;
	}
		$block=$p["hidblock"];
			//echo "----------------------$block";
			
	$convert=false;
	$allocateMode=false;
	$repkgMode=false;
	$thawMode=false;
	$reglzMode=false;
	$convertRegRep=false;
	$statusRpk="-1";
	if ($g['rgrp']!='')
	{
		$flag3=1;
		$flag=1;
		$statusRpk=-1;
	}
	else
	{
		$statusRpk=$p["hidpckst"];
	}
		$statusRgz=$p["hidglzst"];
			
	if ($block=="true")
	{
		$flag1=1;
		$convert=true;
	}
	if ($statusRgz!="")
	{
		//$convert=true;
	}
	if ($statusRpk!="")
	{
		//$convert=true;
	}

	 if ($thawingStatus==0 && $thawingStatus!='')
	{
		$allocateMode=true;
		$repkgMode=false;
		$thawMode=false;
		$reglzMode=false;
		$convert=false;
	}
	else if ($thawingStatus==1)
	{
		//$allocateMode=false;
		$thawMode=true;
		$repkgMode=false;
		$reglzMode=false;
		$convert=false;
	}
	else if ($thawingStatus==2)
	{
		$repkgMode=true;
		$allocateMode=false;
		$thawMode=false;
		$reglzMode=false;
		if ($statusRpk=="")
		{
			$repkgMode=true;
			$convert=false;
		}
		else if ($statusRpk!="-1")
		{
			$repkgMode=false;
			$convert=true;
		}
	}
	else if ($thawingStatus==3)
	{
		$repkgMode=false;
		$allocateMode=false;
		$thawMode=false;
		$reglzMode=true;
		if ($statusRgz=="")
		{
			$statusRpk=-1;
			$reglzMode=true;
			$convert=false;
		}
		else if ($statusRgz!="-1")
		{
			$reglzMode=false;
			$convert=true;
		}
		if ($statusRpk!="-1")
		{
			$reglzMode=false;
			$convert=true;
			$norepack=true;
		}
	}
	else if ($thawingStatus==4)
	{
		$repkgMode=false;
		$allocateMode=false;
		$thawMode=false;
		$reglzMode=false;
		$convert=true;			
		$rethawFlag=$p["rethawFlag"];
		if ($rethawFlag!="")
		{
			$statusRpk=$rethawFlag;
		}
		if ($statusRpk=="")
		{
			$statusRpk="-1";
		}
		if ($statusRgz=="")
		{
			$statusRgz="-1";
		}
		$hidrmLotID=$p["rmLotID"];
	}
	$hidival=$p["hidrow"];
	
	//echo "Flag==$flag";
	# Reset Data
	if ($p["selQuickEntryList"]!="") $selQuickEntryList = $p["selQuickEntryList"];
	if ($p["unit"]!="") 		 $unit 		= $p["unit"];
	if ($p["selectDate"]!="") $selDate	=	$p["selectDate"];
	
	$entrySel = ($p["entrySel"]=="")?$p["hidEntrySel"]:$p["entrySel"];
	if ($entrySel) $addMode = true;

	$displayQE	= $p["displayQE"]; // DMCLS-Both MC&LS, DMC = MC, DLS = LS;
	
	$maxdate=$frozenStockAllocationObj->getMaxDate();
	$maximumdt= dateFormat($maxdate[0]);	
	#Cancel 	
	if ($p["cmdCancel"]!="") 
	{
		$addMode	=	false;
		$editMode	=	false;
		$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
		if ($p['editMode']=="") 
		{
			$dailyFrozenPackingGradeRecDel = $frozenStockAllocationObj->deleteFrozenPackingGradeRec($entryId);
			$frozenPackingEntryRecDel = $frozenStockAllocationObj->deletePackingEntryRec($entryId);
			#Check Record Exists
			$exisitingRecords = $frozenStockAllocationObj->checkRecordsExist($mainId);
			if (sizeof($exisitingRecords)==0)
			{
				$dailyFrozenPackingRecDel = $frozenStockAllocationObj->deleteDailyFrozenPackingMainRec($mainId);
			}
		}
		$mainId 	= "";
		$p["mainId"]	= "";
		$entryId	= "";
		$p["entryId"] 	= "";
		$entrySel 	= "";
		$p["hidEntrySel"] = "";
		$p["entrySel"]	= "";
		$p["editId"] = "";
		$editId = "";
		$editCriteria	= "";
		$p["editCriteria"] = "";
	}
	
	#Repack
	if ($p["cmdSaveChange"]!="")
	{
		$i=1;
		$i1=2;
		$k=1;
		$k1=2;
		$dfId=$p["editId"];
		$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
		$gradeRowCount	= $p["hidAllocateGradeRowCount"];		
		$processId			= $p["hidProcessId"];
		$freezingStageId	= $p["hidFreezingStage"];
		$frozenCodeId		= $p["hidFrozenCode"];
		//$refrozenCodeId		= $p["repselFrozenCode_".$k];
		$refrozenCodeId		= $p["repselFrozenCode_".$k1];
		$MCPkgId			= $p["hidMCPkgId"];
		$hidrmLotID			= $p["hidrmLotID"];
		$dateTill			= $p["frozenPackingTill"];
		$dateFrom			= $p["frozenPackingFrom"];
		$dateFrom=$p["hidstDate"];
		$unit				= $p["hidunit"];
		$fish_id=$p["hidFishId"];
		$processorId		= $p["hidProcessorId"];
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
		//$repmcPkId=$p["mcPackingId_".$i];
		$repmcPkId=$p["mcPackingId_".$i1];
		$repackedfrom=$p["hidNumMcPackPrev_".$i];
		$mcpackingRecRp	= $mcpackingObj->find($repmcPkId);
		$numPacksRp	= $mcpackingRecRp[2];
		$repackedValue="PID-$processId,FSID-$freezingStageId,FCID-$frozenCodeId,MC-$repackedfrom";
		$ffilledWt=$frozenpackingObj->frznPkgFilledWt($frozenCodeId);
		$filledWt=$frozenpackingObj->frznPkgFilledWt($refrozenCodeId);

		if($hidrmLotID=='0')
		{
			$insertDailyRepacking=$dailyfrozenrepackingObj->insertDailyRepacking($selectDate,$processId,$freezingStageId,$refrozenCodeId,$repmcPkId);
			if ($insertDailyRepacking)
			{
				$dfpPOEntryId = $databaseConnect->getLastInsertedId();
				$dailyfrozenrepackingObj->addPhysicalStkdailyFrozenmain($selectDate,$userId,$dfpPOEntryId,$unit,$processorId,$dfId,$repackedValue);
				$dailymainId = $databaseConnect->getLastInsertedId();
				$dailyfrozenrepackingObj->adddailyfrozenEntries($dailymainId,$fish_id,$processId,$freezingStageId,$refrozenCodeId,$repmcPkId);
				$dailyentrymainId = $databaseConnect->getLastInsertedId();
							
				for ($j=1; $j<=$gradeRowCount; $j++) 
				{
					$gradeId = $p["sGradeId_".$j."_".$i];
					/*$numMC = $p["numMC_".$j."_".$i];
					$numMCG = $p["numMCG_".$j."_".$i];
					$numLS = $p["numLS_".$j."_".$i];
					$numLSG = $p["numLSG_".$j."_".$i];*/
					$numMC1 = $p["numMC_".$j."_".$i];
					$numLS1 = $p["numLS_".$j."_".$i];
					$numMCG1 = $p["numMCG_".$j."_".$i];
					$numLSG1 = $p["numLSG_".$j."_".$i];
					$ni=2;
					$numMC = $p["numMC_".$j."_".$ni];
					$numLS = $p["numLS_".$j."_".$ni];
					$numMCG = $p["numMCG_".$j."_".$ni];
					$numLSG = $p["numLSG_".$j."_".$ni];

					/*$totalSlabsi=($numMCG-$numMC)*$numPacksRp+($numLSG-$numLS);
					$iwet=$totalSlabsi*$filledWt;
					$fwet=$totalSlabsi*$ffilledWt;	*/

					if ($numMC1<$numMCG1){
						$bal=$numMCG1-$numMC1;
						//$bal=($bal*$numPacksrg)/$numPacks;
						//echo $bal.'<br/>';
					}
					$bal1=$numMC1;
														
					if ($numLS1<$numLSG1){
						$balLs=$numLSG1-$numLS1;
						//echo $balLs.','.$numLSG1.','.$numLS1.'<br/>';
						//$balLs=($balLs*$numPacksrg)/$numPacks;
					}
					$repkdQty=0;
					if (($gradeId>0) && ($numMC>0 )) 
					{
						//$repkdQty=$repkdQty+$numMC;
						$repkdQty=$repkdQty+$bal1;										
						//echo "<br>";
						//echo $repkdQty;
						list($thawGrdTotal,$thawGrdLsTotal)=$dailyfrozenrepackingObj->getThaGradeQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						list($allocGrdTotal,$allocGrdLsTotal)=$dailyfrozenrepackingObj->getGradeAllocQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						$thaallocTotal=$thawGrdTotal+$allocGrdTotal;
						$thaallocLsTotal=$thawGrdLsTotal+$allocGrdLsTotal;
					?>											
					<?php											
					if ($thaallocTotal!=0)
					{
						$thaallocTotal=$thaallocTotal+$bal;
						$thaallocLsTotal=$thaallocLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						if (sizeof($exisitingRecords)>0) 
						{
							$n=0;
							//echo "hii".$repkdQty;
							foreach ($exisitingRecords as $er)
							{
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1)
								{
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal,$thaallocLsTotal,$repkdQty);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal,$repkdQty);
									$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMain($mainId);
								}
								else{
									$thaallocTotal=0;
									$thaallocLsTotal=0;
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal);
								}
							}
						}
					}
					else if ($thawGrdTotal!=0) 
					{
						$thawGrdTotal=$thawGrdTotal+$bal;
						$thawGrdLsTotal=$thawGrdLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						if (sizeof($exisitingRecords)>0) 
						{
							$n=0;
							foreach ($exisitingRecords as $er){
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1)
								{
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$repkdQty);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$repkdQty);
									$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMain($mainId);
								}
								else
								{
									$thawGrdTotal=0;
									$thawGrdLsTotal=0;
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$thawGrdLsTotal);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal);
								}
							}
						}
					}
					else if ($allocGrdTotal!=0) 
					{
						$allocGrdTotal=$allocGrdTotal+$bal;
						$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						if (sizeof($exisitingRecords)>0) 
						{
							$n=0;
							foreach ($exisitingRecords as $er)
							{
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1)
								{
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$repkdQty);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$repkdQty);
									$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMain($mainId);
								}
								else
								{
									$allocGrdTotal=0;
									$allocGrdLsTotal=0;
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
								}
							}
						}
					}
					else
					{
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
						if (sizeof($exisitingRecords)>0) 
						{
							foreach ($exisitingRecords as $er)
							{
								###commented on 13-11-2014
								$allocGrdTotal=0;
								$allocGrdLsTotal=0;							
								//echo $allocGrdLsTotal.','.$balLs.'<br/>';					
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$allocGrdTotal=$allocGrdTotal+$bal;
								$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
								//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$repkdQty);
								$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$repkdQty);
								$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMain($mainId);
							}
						}				
					}
					//Repacking end allocation and thawing
					$insertPOGradeRec = $dailyfrozenrepackingObj->insertDFPPOGradeForRepacking($dfpPOEntryId, $gradeId, $numMC, $numLS);
				}
				//end of condition if (($gradeId>0) && ($numMC>0 )) 
				if ($dailyentrymainId){
					if ($numMC>0)
					{
						$dailyfrozenrepackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$gradeId,$numMC,$numLS);
					}
				}
				else
				{
						$errDel="$msg_failPhysicalStockEntry";
				}
			}	//die();//end of for loop
		}
		else
		{
			$insertDailyRepacking=$dailyfrozenrepackingObj->insertDailyRepackingRMLotID($selectDate,$processId,$freezingStageId,$refrozenCodeId,$repmcPkId,$hidrmLotID);
			if ($insertDailyRepacking)
			$dfpPOEntryId = $databaseConnect->getLastInsertedId();
			$dailyfrozenrepackingObj->addPhysicalStkdailyFrozenmainRMLotID($selectDate,$userId,$dfpPOEntryId,$unit,$processorId,$dfId,$repackedValue,$hidrmLotID);
			$dailymainId = $databaseConnect->getLastInsertedId();
			$dailyfrozenrepackingObj->adddailyfrozenEntriesRMLotID($dailymainId,$fish_id,$processId,$freezingStageId,$refrozenCodeId,$repmcPkId);
			$dailyentrymainId = $databaseConnect->getLastInsertedId();
							
			for ($j=1; $j<=$gradeRowCount; $j++) 
			{
				$gradeId = $p["sGradeId_".$j."_".$i];
				/*$numMC = $p["numMC_".$j."_".$i];
				$numMCG = $p["numMCG_".$j."_".$i];
				$numLS = $p["numLS_".$j."_".$i];
				$numLSG = $p["numLSG_".$j."_".$i];*/
				$numMC1 = $p["numMC_".$j."_".$i];
				$numLS1 = $p["numLS_".$j."_".$i];
				$numMCG1 = $p["numMCG_".$j."_".$i];
				$numLSG1 = $p["numLSG_".$j."_".$i];
				$ni=2;
				$numMC = $p["numMC_".$j."_".$ni];
				$numLS = $p["numLS_".$j."_".$ni];
				$numMCG = $p["numMCG_".$j."_".$ni];
				$numLSG = $p["numLSG_".$j."_".$ni];

				if ($numMC1<$numMCG1)
				{
					$bal=$numMCG1-$numMC1;
				}
					$bal1=$numMC1;
				if ($numLS1<$numLSG1)
				{
					$balLs=$numLSG1-$numLS1;
				}
				$repkdQty=0;
				if (($gradeId>0) && ($numMC>0 )) 
				{
					$repkdQty=$repkdQty+$bal1;										
					list($thawGrdTotal,$thawGrdLsTotal)=$dailyfrozenrepackingObj->getThaGradeQtyLot($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
					list($allocGrdTotal,$allocGrdLsTotal)=$dailyfrozenrepackingObj->getGradeAllocQtyRmLot($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
					$thaallocTotal=$thawGrdTotal+$allocGrdTotal;
					$thaallocLsTotal=$thawGrdLsTotal+$allocGrdLsTotal;
				?>											
					<?php											
					if ($thaallocTotal!=0)
					{
						$thaallocTotal=$thaallocTotal+$bal;
						$thaallocLsTotal=$thaallocLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
						if (sizeof($exisitingRecords)>0) 
						{
							$n=0;
							foreach ($exisitingRecords as $er)
							{
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1)
								{
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thaallocTotal,$repkdQty);
										$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMainRMLot($mainId);
								}
								else
								{
									$thaallocTotal=0;
									$thaallocLsTotal=0;
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thaallocTotal);
								}
							}
						}
					}
					else if ($thawGrdTotal!=0) 
					{
						$thawGrdTotal=$thawGrdTotal+$bal;
						$thawGrdLsTotal=$thawGrdLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
						if (sizeof($exisitingRecords)>0) 
						{
							$n=0;
							foreach ($exisitingRecords as $er)
							{
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1)
								{
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$repkdQty);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$repkdQty);	
									$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMainRMLot($mainId);
								}
								else
								{
									$thawGrdTotal=0;
									$thawGrdLsTotal=0;
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$thawGrdLsTotal);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal);
								}
							}
						}
					}
					else	if ($allocGrdTotal!=0) 
					{
						$allocGrdTotal=$allocGrdTotal+$bal;
						$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
						if (sizeof($exisitingRecords)>0)
						{
							$n=0;
							foreach ($exisitingRecords as $er){
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$n++;
								if ($n==1){
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$repkdQty);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$repkdQty);
									$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMainRMLot($mainId);
								}
								else
								{
									$allocGrdTotal=0;
									$allocGrdLsTotal=0;
									//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
									$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal);
								}
							}
						}
					}
					else
					{
						//echo "1";
						$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
						if (sizeof($exisitingRecords)>0) 
						{
							foreach ($exisitingRecords as $er)
							{
								$allocGrdTotal=0;
								$allocGrdLsTotal=0;												
								$gradeUpid=$er[2];
								$mainId=$er[0];
								$allocGrdTotal=$allocGrdTotal+$bal;
								$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
								//echo "hii";
								// echo $allocGrdTotal.','.$allocGrdLsTotal.'<br/>';
								//echo $allocGrdTotal;
								//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$repkdQty);
								$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$repkdQty);
								$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMainRMLot($mainId);
							}
						}				
					}
					//Repacking end allocation and thawing
					$insertPOGradeRec = $dailyfrozenrepackingObj->insertDFPPOGradeForRepackingRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS);
				}
				if ($dailyentrymainId)
				{
					if ($numMC>0)
					{
						$dailyfrozenrepackingObj->adddailyFrozenGradeEntriesRMLot($dailyentrymainId,$gradeId,$numMC,$numLS);
					}
				}
				else
				{
					$errDel="$msg_failPhysicalStockEntry";
				}
			}
		}
	}//new
	//die();
	$repkgMode=false;
	$allocateMode=false;
	$thawMode=false;
	$reglzMode=false;
	$convert=false;

	//if ($dailyFrozenPackingRecDel || $frozenPackingEntryRecDel) {
		$sessObj->createSession("displayMsg", $msg_succRepacking);
		$sessObj->createSession("nextPage", $url_afterRepackingFrozenStkAllocation.$selection);
	//} else {
		//$errDel	=	$msg_failDelFrozenStkAllocation;
	//}
}	
	#RepackEnd

	#Reglaze
	if ($p["cmdReglzSaveChange"]!="")
	{		$i=1;
			$i1=2;
			$k=1;
			$k1=2;
			$dfId=$p["editId"];
			$RpYes=$p["RpYes"];
			$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
			$gradeRowCount	= $p["hidAllocateGradeRowCount"];		
			$processId			= $p["hidProcessId"];
			$selP=$processcodeObj->findProcessCode($processId);
			$selProcessCode=$selP;
			$freezingStageId	= $p["hidFreezingStage"];
			$selfreeSt=$freezingstageObj->find($freezingStageId);
			$selFreezingStage=$selfreeSt[1];
			$frozenCodeId		= $p["hidFrozenCode"];
			$hidrmLotID			= $p["hidrmLotID"];
			$MCPkgId			= $p["hidMCPkgId"];
			$dateTill			= $p["frozenPackingTill"];
			
			$dateFrom=$p["hidstDate"];
			
			$unit				= $p["hidunit"];
			$fish_id=$p["hidFishId"];			
			$processorId		= $p["hidProcessorId"];
			$mcpackingRec	= $mcpackingObj->find($MCPkgId);
			$numPacks	= $mcpackingRec[2];
			$dateT			=	explode("/", $dateTill);
			$tillDate		=	$dateT[2]."-".$dateT[1]."-".$dateT[0];
			$dateF			=	explode("/", $dateFrom);
			$fromDate		=	$dateF[2]."-".$dateF[1]."-".$dateF[0];			
			$selectDate		=	mysqlDateFormat($p["selDate"]);

			$dFrznPkgEntryId = 0;
			$POId			 = $p["POId_".$i];
			$totalSlabs		 = $p["totalSlabs_".$i];
			$totalQty		 = $p["totalQty_".$i];
			$POEntryId		 = $p["POEntryId_".$i];
			$dfpPOEntryId = 0;
			//$regmcPkId=$p["mcPackingId_".$i];
			$regmcPkId=$p["mcPackingId_".$i1];
			$reglazedfrom=$p["hidNumMcPackPrev_".$i];
			//$glfrozenCodeId=$p["selFrozenCode_".$k];
			$glfrozenCodeId=$p["selFrozenCode_".$k1];
			$reglazedValue="PID-$processId,FSID-$freezingStageId,FCID-$frozenCodeId,MC-$reglazedfrom";
			$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
			$selFrozenCode=$selFrozenC[1];
			$mcpackingRec	= $mcpackingObj->find($regmcPkId);
			$numPacksrg	= $mcpackingRec[2];
			$selEditCriteria = "$processId,$freezingStageId,$glfrozenCodeId,$selProcessCode - $selFreezingStage - $selFrozenCode -$mcpackingRec[1],$regmcPkId, $mcpackingRec[1], $numPacksrg,$fish_id,$filledWt,$repackedfrom";	
						
			if($hidrmLotID=='0')
			{

				$insertDailyRepacking=$frozenStockAllocationObj->insertDailyReglazing($selectDate,$processId,$freezingStageId,$glfrozenCodeId,$regmcPkId);
				if ($insertDailyRepacking)
					$dfpPOEntryId = $databaseConnect->getLastInsertedId();
					$frozenStockAllocationObj->addPhysicalStkdailyFrozenmainRglz($selectDate,$userId,$dfpPOEntryId,$unit,$processorId,$dfId,$reglazedValue);
					$dailymainId = $databaseConnect->getLastInsertedId();
					$frozenStockAllocationObj->adddailyfrozenEntries($dailymainId,$fish_id,$processId,$freezingStageId,$glfrozenCodeId,$regmcPkId);
					$dailyentrymainId = $databaseConnect->getLastInsertedId();
					$reglzQty=0;
					for ($j=1; $j<=$gradeRowCount; $j++)
					{
						$reglzQty=0;
						$bal=0;
						$balLs=0;
						$gradeId = $p["sGradeId_".$j."_".$i];
						/*$numMC = $p["numMC_".$j."_".$i];
						$numLS = $p["numLS_".$j."_".$i];
						$numMCG = $p["numMCG_".$j."_".$i];
						$numLSG = $p["numLSG_".$j."_".$i];*/
						$numMC1 = $p["numMC_".$j."_".$i];
						$numLS1 = $p["numLS_".$j."_".$i];
						$numMCG1 = $p["numMCG_".$j."_".$i];
						$numLSG1 = $p["numLSG_".$j."_".$i];
						$ni=2;
						$numMC = $p["numMC_".$j."_".$ni];
						$numLS = $p["numLS_".$j."_".$ni];
						$numMCG = $p["numMCG_".$j."_".$ni];
						$numLSG = $p["numLSG_".$j."_".$ni];

						if ($numMC1<$numMCG1)
						{
							$bal=$numMCG1-$numMC1;
							//$bal=($bal*$numPacksrg)/$numPacks;
							//echo $bal;
						}
						$bal1=$numMC1;
						if ($numLS1<$numLSG1){
							$balLs=$numLSG1-$numLS1;
							//$balLs=($balLs*$numPacksrg)/$numPacks;
						}
										
						if (($gradeId>0) && ($numMC>0 )) 
						{
							//$reglzQty=$reglzQty+$numMC;
							$reglzQty=$reglzQty+$bal1;
							//echo "<br>";
							//echo $reglzQty;
							list($thawGrdTotal,$thawGrdLsTotal)=$dailyfrozenrepackingObj->getThaGradeQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
							list($allocGrdTotal,$allocGrdLsTotal)=$dailyfrozenrepackingObj->getGradeAllocQty($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
							$thaallocTotal=$thawGrdTotal+$allocGrdTotal;
							$thaallocLsTotal=$thawGrdLsTotal+$allocGrdLsTotal;
							?>											
							<?php											
							if ($thaallocTotal!=0)
							{
								$thaallocTotal=$thaallocTotal+$bal;
								$thaallocLsTotal=$thaallocLsTotal+$balLs;
								$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
								if (sizeof($exisitingRecords)>0) 
								{						
									$n=0;													
									foreach ($exisitingRecords as $er)
									{							
										$gradeUpid=$er[2];
										$mainId=$er[0];
										$n++;												
										if ($n==1)
										{
											//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal,$thaallocLsTotal,$reglzQty);
											$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal,$reglzQty);
											$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMain($mainId);
										}
										else
										{
											$thaallocTotal=0;
											$thaallocLsTotal=0;
											$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thaallocTotal);
										}
									}
								}
							}
							else if ($thawGrdTotal!=0) 
							{
								$thawGrdTotal=$thawGrdTotal+$bal;
								$thawGrdLsTotal=$thawGrdLsTotal+$balLs;
								$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
								if (sizeof($exisitingRecords)>0) 
								{					
									$n=0;													
									foreach ($exisitingRecords as $er)
									{						
										$gradeUpid=$er[2];
										$mainId=$er[0];
										$n++;												
										if ($n==1)
										{
											//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$reglzQty);
											$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$reglzQty);
											$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMain($mainId);
										}
										else
										{
											$thawGrdTotal=0;
											$thawGrdLsTotal=0;
											$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal);
										}
									}
								}
							}
							else	if ($allocGrdTotal!=0) 
							{
								$allocGrdTotal=$allocGrdTotal+$bal;
								$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
								$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
								if (sizeof($exisitingRecords)>0) 
								{
									$n=0;
									foreach ($exisitingRecords as $er)
									{
										$gradeUpid=$er[2];
										$mainId=$er[0];
										$n++;
										if ($n==1)
										{
											//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$reglzQty);
											$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$thawGrdTotal,$reglzQty);
											$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMain($mainId);
										}
										else
										{
											$allocGrdTotal=0;
											$allocGrdLsTotal=0;
											$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal);
											//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
										}
									}
								}
							}
							else
							{
												
								$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExist($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId);
								if (sizeof($exisitingRecords)>0)
								{							
									foreach ($exisitingRecords as $er)
									{							
										$allocGrdTotal=0;
										$allocGrdLsTotal=0;
										$gradeUpid=$er[2];
										$mainId=$er[0];
										$allocGrdTotal=$allocGrdTotal+$bal;
										$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
										$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$reglzQty);
										//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGrade($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$reglzQty);
										$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMain($mainId);
									}
								}				
							}
							//Reglazing end allocation and thawing
							$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForReglazing($dfpPOEntryId, $gradeId, $numMC, $numLS);
						}
						if ($dailyentrymainId)
						{
							if ($numMC>0)
							{
								$dailyfrozenrepackingObj->adddailyFrozenGradeEntries($dailyentrymainId,$gradeId,$numMC,$numLS);
							}
						}
						else
						{
								$errDel="$msg_failPhysicalStockEntry";
						}
					}
				}
				else
				{
					$insertDailyRepacking=$frozenStockAllocationObj->insertDailyReglazingRMLot($selectDate,$processId,$freezingStageId,$glfrozenCodeId,$regmcPkId,$hidrmLotID);
					if ($insertDailyRepacking)
						$dfpPOEntryId = $databaseConnect->getLastInsertedId();
						$frozenStockAllocationObj->addPhysicalStkdailyFrozenmainRglzRMLotId($selectDate,$userId,$dfpPOEntryId,$unit,$processorId,$dfId,$reglazedValue,$hidrmLotID);
						$dailymainId = $databaseConnect->getLastInsertedId();
						$frozenStockAllocationObj->adddailyfrozenEntriesRMLotId($dailymainId,$fish_id,$processId,$freezingStageId,$glfrozenCodeId,$regmcPkId);
						$dailyentrymainId = $databaseConnect->getLastInsertedId();
						$reglzQty=0;
						for ($j=1; $j<=$gradeRowCount; $j++) 
						{
							$reglzQty=0;
							$bal=0;
							$balLs=0;
							$gradeId = $p["sGradeId_".$j."_".$i];
							/*$numMC = $p["numMC_".$j."_".$i];
							$numLS = $p["numLS_".$j."_".$i];
							$numMCG = $p["numMCG_".$j."_".$i];
							$numLSG = $p["numLSG_".$j."_".$i];*/
							$numMC1 = $p["numMC_".$j."_".$i];
							$numLS1 = $p["numLS_".$j."_".$i];
							$numMCG1 = $p["numMCG_".$j."_".$i];
							$numLSG1 = $p["numLSG_".$j."_".$i];
							$ni=2;
							$numMC = $p["numMC_".$j."_".$ni];
							$numLS = $p["numLS_".$j."_".$ni];
							$numMCG = $p["numMCG_".$j."_".$ni];
							$numLSG = $p["numLSG_".$j."_".$ni];

							if ($numMC1<$numMCG1)
							{
								$bal=$numMCG1-$numMC1;
								//$bal=($bal*$numPacksrg)/$numPacks;
								//echo $bal;
							} 
							$bal1=$numMC1;
							if ($numLS1<$numLSG1)
							{
								$balLs=$numLSG1-$numLS1;
								//$balLs=($balLs*$numPacksrg)/$numPacks;
							}
										
							if (($gradeId>0) && ($numMC>0 )) 
							{
								//$reglzQty=$reglzQty+$numMC;
								$reglzQty=$reglzQty+$bal1;
								//echo "<br>";
								//echo $reglzQty;
								list($thawGrdTotal,$thawGrdLsTotal)=$dailyfrozenrepackingObj->getThaGradeQtyLot($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
								list($allocGrdTotal,$allocGrdLsTotal)=$dailyfrozenrepackingObj->getGradeAllocQtyRmLot($processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
								$thaallocTotal=$thawGrdTotal+$allocGrdTotal;
								$thaallocLsTotal=$thawGrdLsTotal+$allocGrdLsTotal;
								?>											
								<?php											
								if ($thaallocTotal!=0)
								{
									$thaallocTotal=$thaallocTotal+$bal;
									$thaallocLsTotal=$thaallocLsTotal+$balLs;
									$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
									if (sizeof($exisitingRecords)>0) 
									{						
										$n=0;													
										foreach ($exisitingRecords as $er)
										{							
											$gradeUpid=$er[2];
											$mainId=$er[0];
											$n++;												
											if ($n==1)
											{
												//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thaallocTotal,$thaallocLsTotal,$reglzQty);
												$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thaallocTotal,$reglzQty);
												$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMainRMLot($mainId);
											}
											else
											{
												$thaallocTotal=0;
												$thaallocLsTotal=0;
												$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thaallocTotal);
											}
										}
									}
								}
								else if ($thawGrdTotal!=0) 
								{
									$thawGrdTotal=$thawGrdTotal+$bal;
									$thawGrdLsTotal=$thawGrdLsTotal+$balLs;
									$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
									if (sizeof($exisitingRecords)>0)
									{					
										$n=0;													
										foreach ($exisitingRecords as $er)
										{						
										$gradeUpid=$er[2];
											$mainId=$er[0];
											$n++;												
											if ($n==1)
											{
												//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$thawGrdLsTotal,$reglzQty);
												$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$reglzQty);
												$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMainRMLot($mainId);
											}
											else
											{
												$thawGrdTotal=0;
												$thawGrdLsTotal=0;
												//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal,$thawGrdLsTotal);
												$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$thawGrdTotal);
											}
										}
									}
								}
								else	if($allocGrdTotal!=0) 
								{
									$allocGrdTotal=$allocGrdTotal+$bal;
									$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
									$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
									if (sizeof($exisitingRecords)>0) 
									{
										$n=0;
										foreach ($exisitingRecords as $er)
										{
											$gradeUpid=$er[2];
											$mainId=$er[0];
											$n++;
											if ($n==1)
											{
												//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$reglzQty);
												$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$reglzQty);
												$dailyFrozenPackingMainRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingMainRMLot($mainId);
											}
											else
											{
												$allocGrdTotal=0;
												$allocGrdLsTotal=0;
												//$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal);
												$dailyFrozenPackingRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal);
											}
										}
									}
								}
								else
								{
									$exisitingRecords = $dailyfrozenrepackingObj->checkRecordsExistRMLotID($fish_id,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fromDate,$tillDate,$gradeId,$hidrmLotID);
									if (sizeof($exisitingRecords)>0) 
									{							
										foreach ($exisitingRecords as $er)
										{							
											$allocGrdTotal=0;
											$allocGrdLsTotal=0;
											$gradeUpid=$er[2];
											$mainId=$er[0];
											$allocGrdTotal=$allocGrdTotal+$bal;
											$allocGrdLsTotal=$allocGrdLsTotal+$balLs;
											//$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$allocGrdLsTotal,$reglzQty);
											$dailyFrozenPackingRecUp=$frozenStockAllocationObj->updateDailyFrozenPackingGradeRMLot($gradeUpid,$allocGrdTotal,$reglzQty);
											$dailyFrozenPackingMainRecUp=$dailyfrozenrepackingObj->updateDailyFrozenPackingMainRMLot($mainId);
										}
									}				
								}
								//Reglazing end allocation and thawing
								$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForReglazingRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS);
							}
							if ($dailyentrymainId)
							{
								if ($numMC>0)
								{
									$dailyfrozenrepackingObj->adddailyFrozenGradeEntriesRMLot($dailyentrymainId,$gradeId,$numMC,$numLS);
								}
							}
							else
							{
								$errDel="$msg_failPhysicalStockEntry";
							}
						}
					}
					$repkgMode=false;
					$allocateMode=false;
					$thawMode=false;
					$reglzMode=false;
					$convert=false;	
					$convertRegRep=true;
				}	

		# Edit 
		//if ($p["editId"]!="") {
		if (($allocateMode) || ($thawMode) || ($repkgMode) || ($reglzMode) || ($convert) || ($convertRegRep))
		{
		
			$allocateId		=	$p["allocateId"];
			if ($allocateId && $p["cmdEdit"]=="")
			//$allocateMode	=	true;
			if($rmLotID>0)
			{
				$rmLotName=$objManageRMLOTID->getLotName($rmLotID);
			}
			$editId			=	$p["editId"];
			$frozenPackingEntryId	=	$p["editFrozenPackingEntryId"];
			$editMode		= true;
			$companyIds			=	$p["companyId"];
			$unitIds			=	$p["unitId"];
			$processTypes=$p["processType"];
			//echo $companyId."--".$unitId;
			if ($allocateMode) 
			{
				$readOnly = "readOnly";
				$disabled  = "disabled";
			}
		
			if ($convertRegRep)
			{
				$editCriteria=$selEditCriteria;
			}
			else
			{
				$editCriteria=$p["editCriteria"];
			}
			$eCriteria		= explode(",",$editCriteria);
			$processId		= $eCriteria[0];
			
			$freezingStage		= $eCriteria[1];
			$frozenCode		= $eCriteria[2];
			if($rmLotName)
			{
				$displayEditMsg		= strtoupper($eCriteria[3]).'-'.$rmLotName;
			}
			else
			{
				$displayEditMsg		= strtoupper($eCriteria[3]);
			}
			$dis=explode("-",$displayEditMsg);
			$stkAllocateMCPkgId   = $eCriteria[4];
			$stkAllocateMCPkgCode = $eCriteria[5];
			$numPacks = $eCriteria[6];
			$fishIdth=$eCriteria[7];
			
			if ($frozenCode) $filledWt1 = $frozenpackingObj->frznPkgFilledWt($frozenCode);
			$entrySel = "DE";
			$entrySection = false;	
			
			if ($allocateMode) 
			{
				// Get Pending POs
				if (($processId!="") && ( $freezingStage!="") && ($frozenCode!="") &&($stkAllocateMCPkgId!=""))
				{
				//$updateAllocatedStatus = $purchaseorderObj->updateAllocatedStatus($processId, $freezingStage, $frozenCode, $stkAllocateMCPkgId);
				}
				$purchaseOrders = $purchaseorderObj->getPendingOrders($processId, $freezingStage, $frozenCode, $stkAllocateMCPkgId,$companyIds,$unitIds);
			}		
		}

	# update a rec
	if ($p["cmdSaveChange_1"]!="") {
		
		$dailyFrozenPackingId	=	$p["hidDailyFrozenPackingId"];
		
		$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
				
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		
		$unit			=	$p["unit"];
		$processorId		=	$p["processor"];		
		$fishId			=	$p["fish"];
		$processCode		=	$p["processCode"];
		$freezingStage		=	$p["freezingStage"];
		$eUCode			=	$p["eUCode"];
		$sBrand		= explode("_",$p["brand"]);
		$brand		= $sBrand[0];
		$brandFrom	= $sBrand[1];

		$frozenCode		=	$p["frozenCode"];
		$mCPacking		=	$p["mCPacking"];
		$exportLotId		=	$p["exportLotId"];
		$lotId			=	$p["lotId"];
		$selQuality 		= 	$p["selQuality"];
		$customer		= $p["customer"];

		$allocateMode		=	$p["allocateMode"];

		$prodnRowCount 	= $p["hidProdnRowCount"];
		$gradeRowCount	= $p["hidGradeRowCount"];
		
		if ($prodnRowCount>0) 
		{
			for ($i=1; $i<=$prodnRowCount; $i++) 
			{
				$dFrznPkgEntryId = $p["dFrznPkgEntryId_".$i];  
				$frozenLotId	 = $p["frozenLotId_".$i];
				$mcPackingId	 = $p["mcPackingId_".$i];
				if ($dFrznPkgEntryId) 
				{
					$updateDFPEntryRec = $frozenStockAllocationObj->updateDFPEntry($dFrznPkgEntryId, $frozenLotId, $mcPackingId);
				}
				for ($j=1; $j<=$gradeRowCount; $j++) 
				{
					$gradeEntryId = $p["gradeEntryId_".$j."_".$i];
					$numMC = $p["numMC_".$j."_".$i];
					$numLS = $p["numLS_".$j."_".$i];
					if ($gradeEntryId) 
					{
						$uptdDFPGradeEntry = $frozenStockAllocationObj->updateDFPGradeEntry($gradeEntryId, $numMC, $numLS);
					}
				} // Grade Ends here
			} // Product Row Ends here
		}
	
		if ($updateFrozenPackingEntryRec || $updateDFPEntryRec) 
		{
			$sessObj->createSession("displayMsg", $msg_succUpdateDailyFrozenPacking);
			$sessObj->createSession("nextPage", $url_afterUpdateDailyFrozenPacking.$selection);
		} 
		else 
		{
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyFrozenPacking;
		}
		$dailyFrozenPackingRecUptd	=	false;
	}


	if ($p["cmdThawing"]!="") 
	{
		$i=1;
		$allocateMode=false;
		$editCriteria		= $p["editCriteria"];
		$eCriteria		= explode(",",$editCriteria);
		$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
		$gradeRowCount	= $p["hidAllocateGradeRowCount"];		
		$processId			= $p["hidProcessId"];
		$fishIdth=$p["hidFishId"];
		$freezingStageId	= $p["hidFreezingStage"];
		$frozenCodeId		= $p["hidFrozenCode"];
		$hidrmLotID			= $p["hidrmLotID"];
		$filledWt=$eCriteria[8];
		$glazeId=$frozenpackingObj->frznPkgglaze($frozenCodeId);
		$glaze=$glazeObj->findGlazePercentage($glazeId);
		$Wt=$filledWt-($filledWt*$glaze/100);
		$MCPkgId			= $p["hidMCPkgId"];
		$selectDate		=	mysqlDateFormat($p["selDate"]);
		$dFrznPkgEntryId = 0;//$p["dFrznPkgEntryId_".$i];
		$POId			 = $p["POId_".$i];
		$totalSlabs		 = $p["totalSlabs_".$i];
		$totalQty		 = $p["totalQty_".$i];
		$POEntryId		 = $p["POEntryId_".$i];
		$dfpPOEntryId = 0;
		$newRmLot		 =$p["newRmLot"];
		$hidComId=$p["hidComId"];
		$hidUntId=$p["hidUntId"];
		$hidProcessType=$p["hidProcessType"];
		//echo $hidProcessType;
		//die();
		if($newRmLot!="")
		{
			$rmLotIdNew=$p["rmLotIdNew"];
			$number_gen=$p["number_gen"];
			$rmLotIdChar=$p["rmLotIdChar"];
			$rmLotIdNum=$p["rmLotIdNum"];
		}
	//echo "--a--".$rmLotIdNew."--b--".$number_gen."--c--".$rmLotIdChar."--d--".$rmLotIdNum."--e--".$newRmLot."--f--".$hidComId."--g--".$hidU//ntId;
	//die();
		
		if($newRmLot=="")
		{
			if($hidrmLotID=='0')
			{
				//echo "hiii";
				//die();
				$insertDailyThawing = $frozenStockAllocationObj->insertDailyThawing($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth,$hidComId,$hidUntId,$hidProcessType);
				if ($insertDailyThawing)
					$dfpPOEntryId = $databaseConnect->getLastInsertedId();
			}
			else
			{
				$insertDailyThawing = $frozenStockAllocationObj->insertDailyThawingRMLot($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth,$hidrmLotID,$hidComId,$hidUntId,$hidProcessType);
				if ($insertDailyThawing)
					$dfpPOEntryId = $databaseConnect->getLastInsertedId();
			}

			for ($j=1; $j<=$gradeRowCount; $j++) 
			{
				$gradeId = $p["sGradeId_".$j."_".$i];
				$numMC = $p["numMC_".$j."_".$i];
				if($hidrmLotID=='0')
				{
					if (($gradeId>0) && ($numMC>0 )) 
					{
						$netWt=$numMC*$Wt;
						$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForThawing($dfpPOEntryId, $gradeId, $numMC, $numLS,$netWt);
					}
				}
				else
				{
					if (($gradeId>0) && ($numMC>0 )) 
					{
						$netWt=$numMC*$Wt;
						$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForThawingRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS,$netWt);
					}
				}
			}
		}
		else if($newRmLot!="")
		{
			$insertDailyThawing = $frozenStockAllocationObj->insertDailyThawing($selectDate,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$fishIdth,$hidComId,$hidUntId,$hidProcessType);
			if ($insertDailyThawing)
			$dfpPOEntryId = $databaseConnect->getLastInsertedId();
			for ($j=1; $j<=$gradeRowCount; $j++) 
			{
				$gradeId = $p["sGradeId_".$j."_".$i];
				$numMC = $p["numMC_".$j."_".$i];
				if (($gradeId>0) && ($numMC>0 )) 
				{
					$netWt=$numMC*$Wt;
					$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForThawing($dfpPOEntryId, $gradeId, $numMC, $numLS,$netWt);
				}
			}
			if($insertDailyThawing!="")
			{
				$lot=$objManageRMLOTID->addLotIdGeneratedInFrozen($hidComId,$hidUntId,$rmLotIdNum,$rmLotIdChar,$number_gen,$userId);
				$rmlotid	= $databaseConnect->getLastInsertedId();
				$updateFrozen=$frozenStockAllocationObj->updatelotidInThawingMain($hidComId,$hidUntId,$rmlotid, $dfpPOEntryId);
			}
			$delRmLOtId = $objManageRMLOTID->deleteTemporary($rmLotIdNum,$number_gen);	
		}
			//$rmLotIdNew
		
		//die();
		$thawMode="false";
		$repkgMode=false;
		$allocateMode=false;
		$thawMode=false;
		$sessObj->createSession("displayMsg", $msg_succThawing);
		$sessObj->createSession("nextPage", $url_afterThawingFrozenStkAllocation.$selection);
	}

	// Allocation
	if ($p["cmdAllocation"]!="") 
	{
		$allocateMode		=	$p["allocateMode"];
		$prodnRowCount 	= $p["hidAllocateProdnRowCount"];
		$gradeRowCount	= $p["hidAllocateGradeRowCount"];
		$processId			= $p["hidProcessId"];
		$freezingStageId	= $p["hidFreezingStage"];
		$frozenCodeId		= $p["hidFrozenCode"];
		$MCPkgId			= $p["hidMCPkgId"];
		$dateTill				= $p["frozenPackingTill"];
		$tillDate					= mysqlDateFormat($dateTill);
		$hidrmLotID			= $p["hidrmLotID"];
		$hidCompanyId		= $p["hidCompanyId"];
		$hidUnitId 	            = $p["hidUnitId"];

		//echo $hidrmLotID	;
		//die();
			
		if ($prodnRowCount>0) 
		{
			for ($i=1; $i<=$prodnRowCount; $i++) 
			{
				$status = $p["status_".$i];	
				if ($status!='N')
				{
					$dFrznPkgEntryId = 0;//$p["dFrznPkgEntryId_".$i];
					$POId			 = $p["POId_".$i];
					$totalSlabs		 = $p["totalSlabs_".$i];
					$totalQty		 = $p["totalQty_".$i];
					$POEntryId		 = $p["POEntryId_".$i];
					$dfpPOEntryId = 0;	
					if ($POId>0 ) 
					{
						if($hidrmLotID=='0')
						{
							if ($POEntryId>0  )
							{
								// Update record
								$updatePORec = $frozenStockAllocationObj->updateDFPPORecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty, $POEntryId,$hidCompanyId,$hidUnitId);
								$dfpPOEntryId = $POEntryId;
							} 
							else 
							{
								$insertDFPPORecs =  $frozenStockAllocationObj->insertDFPPORecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidCompanyId,$hidUnitId);
								// Last Entry Id
								if ($insertDFPPORecs) $dfpPOEntryId = $databaseConnect->getLastInsertedId();
							}
						}
						elseif($hidrmLotID!='0' || $hidrmLotID!='')
						{
								if ($POEntryId>0  ) 
								{
									// Update record
									$updatePORec = $frozenStockAllocationObj->updateDFPPORMLotRecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty, $POEntryId,$hidrmLotID,$hidCompanyId,$hidUnitId);
									$dfpPOEntryId = $POEntryId;
								} 
								else 
								{
									$insertDFPPORecs =  $frozenStockAllocationObj->insertDFPPORMLotRecs($dFrznPkgEntryId, $POId, $totalSlabs, $totalQty, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidrmLotID,$hidCompanyId,$hidUnitId);
									// Last Entry Id
									if ($insertDFPPORecs) $dfpPOEntryId = $databaseConnect->getLastInsertedId();
								}
							}
							if ($dfpPOEntryId>0) 
							{
								for ($j=1; $j<=$gradeRowCount; $j++) 
								{
									$gradeId = $p["sGradeId_".$j."_".$i];
									//$gradeEntryId = $p["gradeEntryId_".$j."_".$i];
									$allocateGradeEntryId = $p["allocateGradeEntryId_".$j."_".$i];
									$numMC = $p["numMC_".$j."_".$i];
									$numLS = $p["numLS_".$j."_".$i];
									if($hidrmLotID=='0')
									{
										if ($allocateGradeEntryId>0) 
										{
											// Update
											if ($gradeId>0) 
											{
												$updatePOGradeRec = $frozenStockAllocationObj->updateDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS, $allocateGradeEntryId);
											}
										} 
										else
										{
											if ($gradeId>0 && ($numMC>0 || $numLS>0) ) 
											{
												list($rmId)=$frozenStockAllocationObj->getRmentryid($POId,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$gradeId);
												$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForAllocation($dfpPOEntryId, $gradeId, $numMC, $numLS,$rmId);
												//$upAllocationRmEntryStatus=$purchaseorderObj->getAllocatedMcno($POId,$rmId,$selGrade);
												list($allocatedCount,$balCount) = $purchaseorderObj->getAllocatedMcno($POId,$rmId,$selGrade);
												$updatedeliveredStatus=$purchaseorderObj->updateDeliveredStatus($balCount,$rmId);
											}
										}
									}
									elseif($hidrmLotID!='0' || $hidrmLotID!='')
									{
										if ($allocateGradeEntryId>0) 
										{
											if ($gradeId>0) 
											{
												$updatePOGradeRec = $frozenStockAllocationObj->updateDFPPOGradeForAllocationRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS, $allocateGradeEntryId);
											}
										} 
										else 
										{
											// Insert
											if ($gradeId>0 && ($numMC>0 || $numLS>0) ) 
											{
												list($rmId)=$frozenStockAllocationObj->getRmentryid($POId,$processId,$freezingStageId,$frozenCodeId,$MCPkgId,$gradeId);
												$insertPOGradeRec = $frozenStockAllocationObj->insertDFPPOGradeForAllocationRMLot($dfpPOEntryId, $gradeId, $numMC, $numLS,$rmId);
												//$upAllocationRmEntryStatus=$purchaseorderObj->getAllocatedMcno($POId,$rmId,$selGrade);
												list($allocatedCount,$balCount) = $purchaseorderObj->getAllocatedMcno($POId,$rmId,$selGrade);
													$updatedeliveredStatus=$purchaseorderObj->updateDeliveredStatus($balCount,$rmId);
											}
										}
											
									}
								} // Grade Ends here
							}
						}	
					}
				} // Product Row Ends here

				

			# Delete 
			if ( $p["hidDelAllocationArr"] != "" ) 
			{						
				$delArr = $p["hidDelAllocationArr"];		
				$delAllocationArr = explode(",",$delArr); 				
				if (sizeof($delAllocationArr)>0) 
				{
					for ($i=0;$i<sizeof($delAllocationArr);$i++) 
					{
						$allocationPOEntryId	= $delAllocationArr[$i];
						if ($allocationPOEntryId>0) 
						{	
							if($hidrmLotID=='0')
							{
								$delAllocation = $frozenStockAllocationObj->deleteAllocationEntry($allocationPOEntryId);
							}
							else
							{
								$delAllocation = $frozenStockAllocationObj->deleteAllocationEntryRMlot($allocationPOEntryId);
							}
						}
					}
				}
			}	

			// Insert Grouped entry ids
			if($hidrmLotID=='0')
			{
			//	$dfpAllocatedEntry = $frozenStockAllocationObj->insertAllocatedEntry($tillDate, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId);
				$dfpAllocatedEntry = $frozenStockAllocationObj->insertAllocatedEntry($tillDate, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$dfpPOEntryId);
			}
			else
			{
				//$dfpAllocatedEntry = $frozenStockAllocationObj->insertAllocatedEntryRMLot($tillDate, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidrmLotID);
				$dfpAllocatedEntry = $frozenStockAllocationObj->insertAllocatedEntryRMLot($tillDate, $processId, $freezingStageId, $frozenCodeId, $MCPkgId, $userId,$hidrmLotID,$dfpPOEntryId);
			}
			//$updateAllocatedStatus = $purchaseorderObj->updateAllocatedStatus($processId, $freezingStageId, $frozenCodeId, $MCPkgId);
		}
		//die();
		if ($insertPOGradeRec || $updatePOGradeRec) 
		{
			$sessObj->createSession("displayMsg", $msg_succAllocateFrozenStkAllocation);
			$sessObj->createSession("nextPage", $url_afterUpdateFrozenStkAllocation.$selection);
		} 
		else 
		{
			$editMode	=	true;
			$err		=	$msg_failAllocateFrozenStkAllocation;
		}
		$dailyFrozenPackingAllocationRecUptd	=	false;
	}
	
	# Delete 
	if ($p["cmdDelete"]!="") 
	{
		
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)
		{
			$dfpGroupRec  = $p["delGId_".$i];
			if ($dfpGroupRec!="") 
			{
				$eCriteria	= explode(",",$dfpGroupRec);		
				$processCodeId	= $eCriteria[0];
				$freezingStageId = $eCriteria[1];
				$frozenCodeId	= $eCriteria[2];
				# get Grouped recs
				$groupedFPRecs = $frozenStockAllocationObj->getProductionRecs($fromDate, $tillDate, $processCodeId, $freezingStageId, $frozenCodeId);

				$dfPkgEntryId = "";
				$dfPkgMainId = "";
				foreach ($groupedFPRecs as $gfpr) 
				{
					$dfPkgEntryId 	= $gfpr[0];
					$dfPkgMainId = $gfpr[4];	

					if ($dfPkgEntryId!="") 
					{
						# Delete grade Rec
						$dailyFrozenPackingGradeRecDel = $frozenStockAllocationObj-> deleteFrozenPackingGradeRec($dfPkgEntryId);
						# Delete Entry Rec
						$frozenPackingEntryRecDel = $frozenStockAllocationObj->deletePackingEntryRec($dfPkgEntryId);
						#Check Record Exists
						$exisitingRecords = $frozenStockAllocationObj->checkRecordsExist($dfPkgMainId);
						
						if (sizeof($exisitingRecords)==0) 
						{
							# delete Main Rec
							$dailyFrozenPackingRecDel = $frozenStockAllocationObj->deleteDailyFrozenPackingMainRec($dfPkgMainId);	
						}
					} 
				} // Loop Ends here
			}
		}
		if ($dailyFrozenPackingRecDel || $frozenPackingEntryRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelFrozenStkAllocation);
			$sessObj->createSession("nextPage", $url_afterDelFrozenStkAllocation.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelFrozenStkAllocation;
		}
		$dailyFrozenPackingRecDel	=	false;
	}

	
/*
	if ($addMode || $editMode) {
		#List All Plants
		$plantRecords		= $plantandunitObj->fetchAllRecords();
	
		#List All Fishes
		$fishMasterRecords	= $fishmasterObj->fetchAllRecords();
	
		#List All Freezing Stage Record
		$freezingStageRecords	= $freezingstageObj->fetchAllRecords();
		
		#List All EU Code Records
		$euCodeRecords		= $eucodeObj->fetchAllRecords();
	
		#List All Brand Records
		if ($customer) $brandRecords		= $brandObj->getBrandRecords($customer);
			
		#List All Frozen Code Records
		$frozenPackingRecords	= $frozenpackingObj->fetchAllRecords();
		
		#List All MC Packing Records
		$mcpackingRecords	= $mcpackingObj->fetchAllRecords();
	
		#List All Purchase Order Records
		$purchaseOrderRecords	= $purchaseorderObj->fetchNotCompleteRecords();
	
		#List All Quality Records
		$qualityMasterRecords	= $qualitymasterObj->fetchAllRecords();

		#List All Customer Records
		$customerRecords		=	$customerObj->fetchAllRecords();
	}
	*/

	#List all Lot Id for a selected date
	if ($p["selectDate"])	$packingDate	= mysqlDateFormat($p["selectDate"]);
	else 			$packingDate	= mysqlDateFormat(date("d/m/Y"));
	if ($addMode) $frozenLotIdRecords = $frozenStockAllocationObj->fetchLotIdRecords($packingDate);

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

    if ($p["filterProcessCode"]!="") $filterProcessCode = $p["filterProcessCode"];
    else if ($g["filterProcessCode"]!="") $filterProcessCode = $g["filterProcessCode"];
	else $filterProcessCode = "";
	$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
	# select records between selected date
	if ($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!="") {
		$dateFrom = $g["frozenPackingFrom"];
		$dateTill = $g["frozenPackingTill"];		
	} else if ($p["frozenPackingFrom"]!="" && $p["frozenPackingTill"]!="") {
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];		
	} else {
		//$dateFrom = date("d/m/Y");
		//$currYear=Date("Y");
		//$dateFrom="01/04/$currYear";
		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
		$dateFrom=$defaultDFPDate;
		$dateTill = date("d/m/Y");
		$supplierFilterId = "";
	}
//echo $dateFrom;
//echo $dateTill;
	
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) 
	{
		$dateFrom=$maximumdt;
		$fromDate = mysqlDateFormat($dateFrom);
		if ($maximumdt=="")
		{
			$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
			$fromDate=mysqlDateFormat(date("$defaultDFPDate"));
			$dateFrom=dateformat($displayrecordObj->getDefaultDFPDate());
		}
		//echo "******".$fromDate;
		$dateTill = date("d/m/Y");
		$tillDate = mysqlDateFormat($dateTill);
		$dailyFrozenPackingRecs = $frozenStockAllocationObj->getPagingDFPRecs($fromDate, $tillDate, $offset, $limit, $filterProcessCode);	
		$numrows	=  sizeof($frozenStockAllocationObj->getDFPForDateRange($fromDate, $tillDate, $filterProcessCode));
		// Get All Process Codes
		$frznStkProcessCodes = $frozenStockAllocationObj->getFrozenStockProcessCodes($fromDate,$tillDate);
	}
	
	$dailyFrozenPackingRecordSize = sizeof($dailyFrozenPackingRecs);
	## -------------- Pagination Settings II -------------------
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Get Quick Entry List Records
	$fznPkngQuickEntryListRecords = array();
	

	# Coomon Setting
	if ($selQuickEntryList)
	{
		list($qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId, $qeBrandFrom) = $frozenStockAllocationObj->getQERec($selQuickEntryList);
			$numPacks = "";
			if ($qeMCPackingId!=0) 
			{
				$mcpackingRec	= $mcpackingObj->find($qeMCPackingId);
				$numPacks	= $mcpackingRec[2];
				$qelMCPackingCode = $mcpackingRec[1];
			}
	}
	

	if ($addMode || $editMode) 
	{
		# Get All Active Processors
		if ($unit) {
			$activeProcessorRecords = $preprocessorObj->getActiveProcessorRecs($currentUrl, $unit);
			$selFPProcessors        = $frozenStockAllocationObj->getSelDFPProcessor(mysqlDateFormat($selDate));	
			$processorRecords	= multi_unique(array_merge($activeProcessorRecords, $selFPProcessors));
			# sort by name asc
			usort($processorRecords, 'cmp_name');	
			# Processor section ends here
		}
	}
	
	# Edit Mode
	if ($editMode) 
	{
		if (($allocateMode) || ($thawMode))
		{
			# Get products
			if($rmLotID=="0")
			{
				$productRecs = $frozenStockAllocationObj->getAllocateProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $stkAllocateMCPkgId);
			}
			else
			{
				$productRecs = $frozenStockAllocationObj->getAllocateProductionRMlotIdRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $stkAllocateMCPkgId,$rmLotID);
			}
			$productRecSize = sizeof($productRecs);
		}
		else 
		{
			# Get products
			if($rmLotID=="0")
			{
				$productRecs = $frozenStockAllocationObj->getProductionRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode);
			}
			else
			{
				$productRecs = $frozenStockAllocationObj->getProductionRecsRMLot($fromDate, $tillDate, $processId, $freezingStage, $frozenCode,$rmLotID);
			}
			$productRecSize = sizeof($productRecs);
		}
		# grade
		//$gradeRecs = $frozenStockAllocationObj->getProductionGradeRecs($fromDate, $tillDate, $processId, $freezingStage, $frozenCode, $stkAllocateMCPkgId);
		$gradeRecs =$dailyfrozenpackingObj->fetchFrozenGradeRecords($processId, $entryId);
	}

	if ($editMode && !$allocateMode) $heading = $label_editFrozenStkAllocation;
	else if ($allocateMode) $heading = $label_allocateFrozenStkAllocation; 
	else $heading = $label_addFrozenStkAllocation;
	
	# $help_lnk="help/hlp_Packing.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	

	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/FrozenStockAllocation.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

<form name="frmDailyFrozenPacking" action="FrozenStockAllocation.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<? }?>
		<?php if (($defaultDFPDate=="" ) && ($maximumdt=="")){?>
		<tr>
			<td height="10" align="center" class="listing-item" style="color:Maroon;">Please set the Frozen stock start date</td>
		</tr>
		<? }?>
		<?
		if ($editMode || $addMode) 
		{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white"> 
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<?php
							if ($addMode && $entrySel=="") {
							?>
								<tr> 
									<td width="1" ></td>
									<td colspan="2" > 
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr> 
												<td height="10"></td>
											</tr>
											<tr> 
												<td align="center">
													<table>
															<TR>
																<TD class="listing-item">
																	<INPUT type="radio" name="entrySel" id="entrySel1"  class="chkBox" value="QE" onclick="this.form.submit();">&nbsp;Quick Entry
																</TD>
																<TD class="listing-item">
																	<INPUT type="radio" name="entrySel" id="entrySel2" class="chkBox" value="DE" onclick="this.form.submit();">&nbsp;Detailed Entry
																</TD>
															</TR>
													</table>
												</td>
											</tr>
											<tr> 
												<td height="10"></td>
											</tr>
										</table>
									</td>
								</tr>
								<?php
								}
								?>
								<?php
								//echo $allocateMode;
								//if ($entrySel || $editMode) {
								//echo "Reglaze Mode-$reglzMode";
								/*echo "**********";
								echo "crp-$convertRegRep";
								echo "st---$statusRpk";
								echo "con--$convert";*/

								if ($convertRegRep)
								{
									$statusRpk="-1";
									$convert=false;
								?>
								<tr>
									<td>
										<table align="center">
											 <tr width=100%><td colspan=5>&nbsp;</td><td>Do you want to Repack&nbsp;<input type="submit" value="Yes" name="cmdYes" onclick="this.form.action='FrozenStockAllocation.php?all=2&rgrp=1'"; />&nbsp;&nbsp;<input type="submit" value="No" name="cmdNo" onClick="this.form.action='FrozenStockAllocation.php?all=-1'" /></td></tr>
										</table>
									</td>
								</tr>
								<?php } 
								else 
								//if (($convert) && ($flag3!=1))
								if ($convert)
								{
									//echo "The block value is $block $statusRgz";
									if (($block==="true") && ($statusRgz=="-1"))
									{
									//if ($block==="true"){
									$flagb=1;
									$reglzMode=false;
									?>		
								<tr>
									<td>
										<table align="center">
											<tr width=100%><td colspan=5 >&nbsp;<span style="color:red">
												Block Products you cannot Reglaze.But You can Repack</span></td>
										  </tr>
										</table>
									</td>
								</tr>
								<?php } else ?>
								<?php 
								if ($flag!=1)
								{	
								?>
								<tr>
									<td>
										<table align="center">
											 <tr width=100%>
												<td colspan=5>&nbsp;</td>
												<td>Reglazing <input type="radio" id="rpkrgz" name="optionrpkrgz"  value=3 onclick="return checkboxSel();" <?php if (($flag1==1) || ($block=="true")){?> disabled=true; <?php }?> />Repacking<input type="radio" id="rpkrgz" name="optionrpkrgz" onclick="return checkboxSel();" value=2 /></td>
											</tr>
											<tr width=100%>
												<td colspan=5>&nbsp;&nbsp</td><td>&nbsp;&nbsp<input type=Submit value=Go name="cmdGo" class="button" onclick="return go(document.frmDailyFrozenPacking);"/>
												<input type='hidden' name="hidoptrmLotID" id='hidoptrmLotID' value="<?=$hidrmLotID?>"/>
												<input type=Submit name="cmdCancel" class="button" value=" Cancel " onClick=" this.form.action='FrozenStockAllocation.php?all=-1';">&nbsp;&nbsp;
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<?php 
								}
								?>
								<?
								}
								if ($thawMode || $allocateMode || $repkgMode || $reglzMode )
								{
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<?php
												//echo "$allocateMode,$thawMode,$repkgMode";
												if (($allocateMode) && ($thawMode==0) && ($repkgMode==0) && ($reglzMode==0)) {?>
												<td height="10" background="images/heading_bg.gif" class="pageName" >Allocation</td>
												<?php }?>
												<?php if ($thawMode) {?>
												<td height="10" background="images/heading_bg.gif" class="pageName" >Thawing</td>
												<?php }?>
												<?php if ($repkgMode) {?>
												<td height="10" background="images/heading_bg.gif" class="pageName" >Repacking</td>
												<?php }?>
												<?php if ($reglzMode) {?>
												<td height="10" background="images/heading_bg.gif" class="pageName" >Reglazing</td>
												<?php }?>
											</tr>
											<tr>
												<? if($editMode){?>
												<td align="center">
													<? if($editMode && !$allocateMode){?>
													<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenStockAllocation.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);">-->		
													<?php } else if ($allocateMode) {?>
													<?php }?>
												</td>
												<?} else{?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenStockAllocation.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" style="width:100px;">&nbsp;&nbsp;<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:130px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New">	
												<? if ($selQuickEntryList!="") {?>
												&nbsp;&nbsp;<input name="cmdSaveAndQE" type="submit" class="button" id="cmdSaveAndQE" style="width:200px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New Quick Entry">	
												<? }?>
												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
												<td nowrap></td>
											 </tr>
											 <tr>
												<td colspan="2" height="10"></td>
											 </tr>
											<?php
											 //if ($selQuickEntryList=="" && $entrySel=="DE" ) {
											if ($entrySel=="DE" && $entrySection) {
											?>
											<tr>
												<td colspan="2" style="padding-left:60px; padding-right:60px;" align="center" nowrap>
													<?	
													if(!$allocateMode)
													{
													?>
													&nbsp; &nbsp;
													<input name="cmdAddFishInSameICAndMC" type="submit" class="button" id="cmdAddFishInSameICAndMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Fish in Same IC & MC Pkg" style="width:290px;">&nbsp; &nbsp;
													<input name="cmdAddFishInSameMC" type="submit" class="button" id="cmdAddFishInSameMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Fish in Same MC Pkg" style="width:260px;" />&nbsp; &nbsp;
													<?}?>
												</td>
											</tr>
											<tr><td height="10">&nbsp;</td></tr>
											<tr>
												<td align="center">
												<?if(!$allocateMode){?>
													<input name="cmdAddProcessCodeInSameICAndMC" type="submit" class="button" id="cmdAddProcessCodeInSameICAndMC" tabindex="32" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="Save &amp; Add New Process Code in same IC&MC Pkg" style="width:340px;" />
												<?}?>
												</td>
											</tr>
											<tr>
												<td colspan="2" style="padding-left:60px;">&nbsp;</td>
											</tr>
											<?php
												}
											?>
											<tr>
												<td colspan="2" align="center" style="padding:10px;">
													<table width="75%" align="center" cellpadding="0" cellspacing="0">
														<tr>
															<td valign="top">
															<?php
															if ($entrySection) 
															{
															?>
																<table width="200">
																	 <tr>
																		  <td class="fieldName" nowrap="nowrap">*Date</td>
																			  <td nowrap>
																				<?							
																				if($selDate=="")
																				{
																					$selDate	=	date("d/m/Y");
																				}						
																				?>
																				<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selDate?>" <?=$readOnly?> onChange="this.form.submit();">
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName" nowrap="nowrap">*Unit</td>
																			<td nowrap>
																			<?php  
																			if ($addMode==true) {
																			?>
																				<select name="unit" onchange="this.form.submit();">
																			<?
																			 } else {
																			?>
																				<select name="unit" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" <?=$disabled?>>
																			<? }?>		
																					<option value="">-- Select --</option>
																					<? foreach($plantRecords as $pr)
																						{
																							$plantId	=	$pr[0];
																							$plantNo	=	stripSlash($pr[1]);
																							$plantName	=	stripSlash($pr[2]);
																							$selected="";
																							if($plantId== $unit) $selected	=	"selected";
																					?>
																					<option value="<?=$plantId?>" <?=$selected?>><?=$plantName?></option>
																					<? }?>
																				</select>		
																			</td>
																		</tr>
																		<tr>
																			<td class="fieldName">*Processor</td>
																			<td nowrap>
																				<?  
																				if($p["processor"]!="") $processor	=	$p["processor"]; 
																				?>
																				<select name="processor" id="processor" <?=$disabled?>>
																					<option value="">-- Select --</option>
																					<? 
																					foreach($processorRecords as $ppr) 
																					{
																						$processorId		=	$ppr[0];
																						$processorName		=	stripSlash($ppr[1]);
																						$selected="";
																						if($processorId== $processor || $editProcessorId==$processorId) $selected	=	"selected";
																					?>
																					<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
																					<? }?>
																				</select>			
																			</td>
																		</tr>
																		<?php if ($addMode && $entrySel=='QE') 
																		{
																		?>	
																		<tr>
																			<TD class="fieldName" nowrap="true">Quick Enty List</TD>
																			<td>
																				<select name="selQuickEntryList" id="selQuickEntryList" onchange="this.form.submit();">
																					<option value="">-- Select --</option>
																					<?php
																					foreach ($fznPkngQuickEntryListRecords as $fznPkngQuickEntryListId=>$qEntryName) {
																						$selected = "";
																						if ($selQuickEntryList==$fznPkngQuickEntryListId) $selected = "selected";	
																					?>
																					<option value="<?=$fznPkngQuickEntryListId?>" <?=$selected?>><?=$qEntryName?></option>
																					<?php
																						}
																					?>
																				</select>
																			</td>
																		</tr>
																		<tr>
																			<TD class="fieldName" nowrap="true">Display</TD>
																			<td>
																				<select name="displayQE" id="displayQE" onchange="this.form.submit();">
																					<option value="DMCLS" <? if ($displayQE=='DMCLS') echo "selected";?>>Both MC & LS</option>
																					<option value="DMC" <? if ($displayQE=='DMC') echo "selected";?>>Only MC</option>
																					<option value="DLS" <? if ($displayQE=='DLS') echo "selected";?>>Only LS</option>
																				</select>
																			</td>
																		</tr>
																		<? }?>
																		<tr id="fishRow">
																			<td class="fieldName" nowrap="nowrap">*Fish</td>
																			<td>
																			 <? 
																			if($addMode==true){
																			$fishId		=	$p["fish"];
																			if ( $fishId != "" ){	
																			$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);	
																			}	
																			?>
																				<select name="fish" onchange="this.form.submit();">
																				<? } else {?>
																				<select name="fish" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" <?=$disabled?>>
																				<? }?>
																					<option value="">--Select--</option>
																					<?
																					foreach($fishMasterRecords as $fr)
																					{
																						$Id		=	$fr[0];
																						$fishName	=	stripSlash($fr[1]);
																						$fishCode	=	stripSlash($fr[2]);
																						$selected	=	"";
																						if( $fishId==$Id){
																							$selected	=	"selected";
																						}
																					?>
																					<option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="pcRow">
																			<td class="fieldName" nowrap="nowrap">*Process Code</td>
																			<td>
																			<?
																			if($addMode==true){ 
																			$processId	=	$p["processCode"];
																			?>
																				<select name="processCode" id="processCode" onchange="this.form.submit();">
																				<? 
																				 } else {
																				?>
																				 <select name="processCode" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" <?=$disabled?>>
																				  <? }?>
																					<option value="">-- Select --</option>
																					<?
																					foreach ($processCodeRecords as $fl)
																					{
																						$processCodeId		=	$fl[0];
																						$processCode		=	$fl[2];
																						$selected	=	"";
																						if( $processId==$processCodeId){
																						$selected	=	"selected";
																						}
																					?>
																					<option value="<?=$processCodeId;?>" <?=$selected;?> >
																					<?=$processCode;?>
																					</option>
																				<?
																				}
																				?>
																				</select>
																			</td>
																		</tr>
																		<tr id="fsRow">
																			<td class="fieldName" nowrap="nowrap">*Freezing Stage</td>
																			<td>
																				<? if($p["freezingStage"]!="") $freezingStage = $p["freezingStage"];?>
																				<select name="freezingStage" id="freezingStage">
																					<option value="0">--Select--</option>
																					<?
																					foreach($freezingStageRecords as $fsr)
																					{
																						$freezingStageId		=	$fsr[0];
																						$freezingStageCode		=	stripSlash($fsr[1]);
																						$selected		=	"";
																						if($freezingStage==$freezingStageId)  $selected	=	" selected ";
																					?>
																					<option value="<?=$freezingStageId?>" <?=$selected?>>
																					  <?=$freezingStageCode?>
																					</option>
																					<? 
																					}
																					?>
																				</select>
																			</td>
																		</tr>
																		<tr id="qltyRow">
																			<TD class="fieldName">Quality</TD>
																			<td>
																			<?
																			if($p["selQuality"]!="") $selQuality = $p["selQuality"];
																			?>
																				<select name="selQuality" id="selQuality">
																					<option value="0">-- Select --</option>
																					<?
																					foreach ($qualityMasterRecords as $fr) {
																						$qualityId	=	$fr[0];
																						$qualityName	=	stripSlash($fr[1]);
																						$qualityCode	=	stripSlash($fr[2]);
																						$selected = "";
																						if($selQuality==$qualityId)  $selected = " selected ";
																					?>
																					<option value="<?=$qualityId?>" <?=$selected?>><?=$qualityName?></option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="eucRow">
																		   <td class="fieldName" nowrap="nowrap">EU Code</td>
																		   <td>
																				<? if($p["eUCode"]!="") $eUCode = $p["eUCode"];?>
																				<select name="eUCode" id="eUCode">
																					<option value="0">-- Select--</option>
																					<?php
																					foreach ($euCodeRecords as $eucr) {
																						$euCodeId		=	$eucr[0];
																						$euCode			=	stripSlash($eucr[1]);
																						$selected		=	"";
																						if ($eUCode==$euCodeId)  $selected = " selected ";
																					?>
																					<option value="<?=$euCodeId?>" <?=$selected?>>
																					<?=$euCode?>
																					</option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="buyerRow">
																			<td class="fieldName" nowrap="nowrap">Customer</td>
																			<td nowrap>	
																				<? if($p["customer"]!="") $customer =$p["customer"];?>  
																				<select name="customer" id="customer" onchange="xajax_getBrandRecs(document.getElementById('customer').value, '');">
																					<option value="">-- select --</option>
																					<?php
																						foreach ($customerRecords as $cr) {
																							$customerId	=	$cr[0];
																							$customerName	=	stripSlash($cr[2]);
																							$selected 	=	($customerId==$customer)?"Selected":"";
																					?>
																					<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="brndRow">
																			<td class="fieldName" nowrap="nowrap">Brand</td>
																			<td>
																				<? if($p["brand"]!="") $brand =$p["brand"];?>
																				<select name="brand" id="brand">
																				<? if (!sizeof($brandRecords)) {?> <option value="0">-- Select --</option><? }?>
																				<?php
																				//foreach($brandRecords as $br) {
																				foreach($brandRecords as $brandId=>$brandName) 
																				{
																					//$brandId	=	$br[0];
																					//$brandName	=	stripSlash($br[1]);
																					$selected = ($brand==$brandId)?"selected":"";
																				?>
																					<option value="<?=$brandId?>" <?=$selected?>><?=$brandName?></option>
																				<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="fcRow">
																			<td class="fieldName" nowrap="nowrap">*Frozen Code</td>
																			<td nowrap="nowrap">
																			<? if($p["frozenCode"]!="") $frozenCode=$p["frozenCode"];?>
																				<select name="frozenCode" id="frozenCode" <?=$disabled?>>
																					<option value="">-- Select --</option>
																					<?
																					foreach($frozenPackingRecords as $fpr) 
																					{
																						$frozenPackingId	= $fpr[0];
																						$frozenPackingCode	= stripSlash($fpr[1]);
																						$selected		=	"";
																						//if ($frozenCode==$frozenPackingId)  $selected = " selected ";
																					?>
																					<option value="<?=$frozenPackingId?>" <?=$selected?>>
																					<?=$frozenPackingCode?>
																					</option>
																					<? 
																					}
																					?>
																				</select>
																			</td>
																		</tr>
																		<tr id="mcpRow">
																			<td class="fieldName" nowrap="nowrap">MC Pkg1</td>
																			<td nowrap="nowrap">
																				<? if($p["mCPacking"]!="") $mCPacking = $p["mCPacking"];?>
																				<select name="mCPacking" id="mCPacking" onchange="passMCPkgValue();">
																					<option value="0">-- Select --</option>
																					<?
																					foreach($mcpackingRecords as $mcp) 
																					{
																						$mcpackingId		=	$mcp[0];
																						$mcpackingCode		=	stripSlash($mcp[1]);
																						$selected		=	"";
																						if($mCPacking==$mcpackingId)  $selected	= " selected ";
																					  ?>
																					<option value="<?=$mcpackingId?>" <?=$selected?>>
																					  <?=$mcpackingCode?>
																					</option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="fliRow">
																			<td class="fieldName" nowrap="nowrap">Frozen Lot Id </td>
																			<td nowrap>
																				<? if($p["lotId"]!="") $lotId	=	$p["lotId"]; ?>
																				<select name="lotId">
																					<option value=""> -- Select --</option>
																					<?php
																					$k=0;
																					foreach($frozenLotIdRecords as $flr)
																					{
																						$k++;
																						$dailyActivityChartEntryId = $flr[1];
																						$freezer	=	$flr[2];
																						$displayLotId	=	$k."-".$freezer;
																						$selected	= "";
																						if ($dailyActivityChartEntryId==$lotId) $selected = "Selected";
																					?>
																					<option value="<?=$dailyActivityChartEntryId?>" <?=$selected?>><?=$displayLotId?></option>
																					<? }?>
																				</select>
																			</td>
																		</tr>
																		<tr id="eliRow">
																			<td class="fieldName" nowrap="nowrap">Export &nbsp;Lot ID</td>
																			<td nowrap="nowrap" class="listing-item">
																				<? if($p["exportLotId"]!="") $exportLotId = $p["exportLotId"];?>
																				<select name="exportLotId" id="exportLotId">
																					<option value="0">-- Select --</option>
																					<? 
																					foreach($purchaseOrderRecords as $por)
																					{
																						$purchaseOrderId	=	$por[0];
																						$pOId			=	$por[1];
																						$selected		=	"";
																						if($exportLotId==$purchaseOrderId) $selected = "Selected";
																					?>
																					 <option value="<?=$purchaseOrderId?>" <?=$selected;?>><?=$pOId?></option>
																					<? }?>
																				</select>                                                      
																			</td>
																		</tr>
																	</table>
																	<?php
																		} // Enable section ends here
																	?>
																</td>
																<td valign="top" align="center" id="gradeRow">
																	<table width="200">
																	<? 
																	if($fishId!="" && $processId!="")
																	{
																	?>
																		<tr>
																			<TD align="center">
																				<table width="100%" border="0" cellpadding="0" cellspacing="0">
																					<tr>
																						<td>
																							<table align="center" cellpadding="0" cellspacing="0">
																								<tr>
																									<td>
																										<fieldset>
																											<legend class="listing-item">Grade</legend>
																											<iframe name="iFrame1" id="iFrame1" src="DailyFrozenPackingGrade.php?entryId=<?=$entryId?>&fishId=<?=$fishId?>&process=<?=$processId?>&mCPacking=<?=$mCPacking?>&allocateMode=<?=$allocateMode?>" width="280" frameborder="0" height="370"></iframe>
																										</fieldset>
																									</td>
																								</tr>
																							</table>				
																						</td>
																					</tr>
																				</table>
																			</TD>
																		</tr>
																		<?
																		}
																		?>
																	</table>
																</td>
															</tr>
															<?php
															if ($selQuickEntryList) {
															?>
															<tr>
																<TD colspan="2">
																	<table>
																		<tr bgcolor="White">
																			<TD class="listing-head" colspan="2">MC Packing = &nbsp;
																			<!--<span class="listing-item"><b><?=($qelMCPackingCode!="")?$qelMCPackingCode:"Not Selected"?></b></span>-->
																			<? if ($qelMCPackingCode!="") { ?>
																				<span class="listing-item"><b><?=$qelMCPackingCode?></b></span>
																				<? } else {?>
																				<select name="qeMCPacking" id="qeMCPacking" onchange="xajax_getMCNumPack(document.getElementById('qeMCPacking').value); calcMCPack('<?=$displayQE?>');">
																					<option value="">-- Select --</option>
																				 </select>
																			<?php
																			}		
																			?>
																		</TD>
																	</tr>
																	<TR>
																		<TD>
																			<table width="200" border="0" cellpadding="1" cellspacing="1" align="center" bgcolor="#999999">
																			<?php
																			if (sizeof($qeGradeRecords) && sizeof($selProcessCodeRecs)>0) 
																			{
																				$i = 0;
																			?>
																				<tr bgcolor="#f2f2f2"  align="center">		
																					<td nowrap style="padding-left:2px;padding-right:2px;" class="listing-head">Grade</td>
																						<?php
																						$spc = 0;
																						foreach ($selProcessCodeRecs as $pcr) 
																						{
																							$spc++;
																							//$pCode = $pcr[3];
																							$pCode = $pcr[2];
																							$qelSFishId	= $pcr[1];
																							$qelSPCId 	= $pcr[0];
																						?>
																					<td nowrap style="padding-left:2px;padding-right:2px;">				
																						<table cellpadding="1" cellspacing="0" width="100%">
																							<TR bgcolor="#f2f2f2">
																								<TD class="listing-head" colspan="3" align="center" id="processCodeCol_<?=$spc?>">
																									<?=$pCode;?>
																									<input type="hidden" name="packEntered_<?=$spc?>" id="packEntered_<?=$spc?>" value=""/>
																									<input type="hidden" name="hProcesscodeId_<?=$spc?>" id="hProcesscodeId_<?=$spc?>" value="<?=$qelSPCId?>"/>
																									<input type="hidden" name="hFishId_<?=$spc?>" id="hFishId_<?=$spc?>" value="<?=$qelSFishId?>"/>	
																									<input type="hidden" name="recExist_<?=$spc?>" id="recExist_<?=$spc?>" value=""/>
																									<span id="qelPCErr_<?=$spc?>" class="listing-item" style="font-size:9px; color:black;"></span>
																								</TD>
																							</TR>
																							<tr>
																								<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																							</tr>
																							<tr>
																								<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																								?>
																								<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																								<?php }?>
																								<?php
																									if ($displayQE=='DMCLS') {
																								?>
																								<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																								<? }?>	
																								<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																								?>
																								<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																								<? }?>
																							</tr>
																						</table>
																					</td>
																				<?php
																					}
																				?>
																				</tr>
																				<?php	
																				$g = 0;
																				foreach ($qeGradeRecords as $gr) {
																					$g++;
																					$gradeId 	= $gr[0];
																					$displayGrade	= $gr[1];
																				?>
																				<tr bgcolor="WHITE">
																					<td class="listing-item" nowrap style="padding-left:2px;padding-right:2px;" id="gradeRow_<?=$g?>">
																						<input type="hidden" name="gradeId_<?=$g;?>" value="<?=$gradeId?>">
																							<?=$displayGrade?>
																					</td>
																					<?php
																					$p=0;
																					foreach ($selProcessCodeRecs as $pcr) 
																					{
																						$p++;
																						$sFishId	= $pcr[1];
																						$sProcessCodeId = $pcr[0];

																						# Check The selected PC has Grade Exist
																						$pcHasGrade = $frozenStockAllocationObj->processCodeHasGrade($sProcessCodeId, $gradeId);

																						$cellReadonly = "";
																						$styleDisplay = "";
																						if (!$pcHasGrade) { 
																							$cellReadonly = "readonly";
																							$styleDisplay = "border:none;";
																						}
																					?>
																					<td nowrap align="right" height="25" style="padding-left:2px;padding-right:2px;">
																						<input type="hidden" name="hidProcesscodeId_<?=$p?>" id="hidProcesscodeId_<?=$p?>" value="<?=$sProcessCodeId?>"/>
																						<input type="hidden" name="hidFishId_<?=$p?>" id="hidFishId_<?=$p?>" value="<?=$sFishId?>"/>	
																						<!--<input type="text" name="recExist_<?=$p?>" id="recExist_<?=$p?>" value=""/>-->
																						<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																							<TR>
																								<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																								?>
																								<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																									<input name="numMC_<?=$p?>_<?=$g?>" type="text" id="numMC_<?=$p?>_<?=$g?>" size="4" value="<?=$numMC?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyFrozenPacking','numMC_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" autocomplete="off" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" <?=$cellReadonly?> onkeyup="chkQePcExist();" />
																								</TD>
																								<? } ?>
																								<?php
																									if ($displayQE=='DMCLS') {
																								?>
																								<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																								<? }?>
																								<?php
																									if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																								?>
																								<TD align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																									<input name="numLooseSlab_<?=$p?>_<?=$g?>" type="text" id="numLooseSlab_<?=$p?>_<?=$g?>" size="4" value="<?=$numLooseSlab?>" style="text-align:right; <?=$styleDisplay?>" onblur="calcQETotal('<?=$displayQE?>');" onkeydown="return fNGradeTxtBox(event,'document.frmDailyFrozenPacking','numLooseSlab_<?=$p?>_<?=$g?>', '<?=$displayQE?>');" onfocus="hLightRNC('<?=$g?>', '<?=$p?>');" autocomplete="off" <?=$cellReadonly?> onkeyup="chkQePcExist();" />
																								</TD>
																								<? }?>
																							</TR>
																						</table>
																					</td>
																					<?php
																						} // Process code Loop ends here
																					?>
																				</tr>
																				<? 
																					} // Grade Loop Ends Here
																				?>				
																				<tr bgcolor="White">
																					<TD class="listing-head" align="right" style="padding-left:2px;padding-right:2px;">Total:</TD>
																						<?php
																							$p=0;
																							foreach ($selProcessCodeRecs as $pcr) {
																								$p++;
																						?>
																					<TD align="right" style="padding-left:2px;padding-right:2px;">
																						<table cellpadding="1" cellspacing="0" width="100%" height="100%" border="0">
																							<TR>
																							<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DMC') {
																							?>
																								<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																									<input name="totalMCPack_<?=$p?>" type="text" id="totalMCPack_<?=$p?>" size="4" value="<?=$totMcPack?>" style="text-align:right;font-weight:bold;border:none;" readonly />				
																								</td>
																							<? } ?>
																							<?php
																								if ($displayQE=='DMCLS') {
																							?>
																								<TD background="images/VL.png" style="background-repeat:repeat-y;line-height:normal;" width="1" ></TD>
																							<? }?>
																							<?php
																								if ($displayQE=='DMCLS' || $displayQE=='DLS') {
																							?>
																								<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;" width="45%">
																									<input name="totLooseSlab_<?=$p?>" type="text" id="totLooseSlab_<?=$p?>" size="4" value="<?=$totLooseSlab?>" style="text-align:right;font-weight:bold;border:none;" readonly />
																									<?php //if ($numPacks) {?>
																									<span id="totSlabs_<?=$p?>" onMouseover="ShowTip('Total Slabs');" onMouseout="UnTip();"></span>
																									<? //}?>
																								</td>
																							<? }?>
																							</TR>
																						</table>
																					</TD>
																					<?php
																					}
																					?>
																				</tr>	
																				<?php
																				// Size of Grade Check
																				} else if (sizeof($qeGradeRecords)>0) 
																				{
																				?>
																				<tr bgcolor="White">
																					<TD class="err1" nowrap="true" style="padding:10 10 10 10px;">No process codes are valid for the selected day.</TD></tr>
																					<? }?>
																					<input type="hidden" name="hidPCRowCount" id="hidPCRowCount" value="<?=$p?>"/>
																					<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=$g;?>" >
																					</TD>
																				</tr>
																			</table>
																		</TD>
																	</TR>
																</table>
															</TD>
														</tr>
														<?php
														 // QE List Ends Here
														}  
														?>
														<!-- 	Quick List Ends Here	 -->
														<!-- Allocation Starts here -->
														<?php
														/*
														* Allocation starts here
														*/
														if (($allocateMode) && ($thawingStatus==0)) 
														{
															$selDate	=	date("d/m/Y");
														?>
														<?php if ($thawingStatus==1){?>	
														<tr><TD height="5"></TD></tr>
														<tr>
															<td class="fieldName">Date:</td>
															<td class="listing-item">
																<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" />
															</td>
														</tr>
														<?php }?>
														<tr>
															<TD colspan="2">
																<table>	
																	<tr>
																		<TD class="listing-head" nowrap="true">
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
																			<table width="200" border=
																			"0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">
																				<tr bgcolor="#f2f2f2"  align="center">		
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;</td>	
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
																					SLABS OF EACH GRADE/COUNT
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																				</tr>
																				<tr bgcolor="#f2f2f2"  align="center">
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head" title="Set Purchase order">
																					<?php 
																					if ($thawingStatus==0)
																					{
																					?>SET PO <?php }?>	&nbsp;</td>			
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																					<?php
																					$g = 1;
																					foreach ($gradeRecs as $gR) {
																						$gId = $gR[0];
																						$gradeCode = $gR[1];
																					?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																						<?=$gradeCode?>
																						<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																					</td>
																					<?php
																						$g++;
																					}
																					?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																				</tr>
																				<tr bgcolor="#f2f2f2"  align="center">
																				<?php 
																				if ($thawingStatus==0)
																				{
																				?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" >
																					Available Qty&nbsp;</td><?php } else{?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" >
																					Available Qty&nbsp;</td>
																					<?php }?>
																					<input type="hidden" name="hidrmLotID" id="hidrmLotID" value="<?=$rmLotID?>"  />	
																					<input type="hidden" name="hidCompanyId" id="hidCompanyId" value="<?=$companyIds?>"  />	
																					<input type="hidden" name="hidUnitId" id="hidUnitId" value="<?=$unitIds?>" />
																					
																					
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																						<table cellpadding="1" cellspacing="0" width="100%">
																							<tr>
																								<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																							</tr>
																							<tr>
																								<td nowrap class="listing-item" title="Available Purchase Order" align="center" width="50%" style="padding-left:2px;padding-right:2px;">PO </td>
																							</tr>
																							<!--<tr>
																								<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																							</tr>-->
																							<!--<tr>
																								<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																							</tr>-->
																						</table>
																					</td>
																					<?php
																					$totAvailableMC = 0;
																					$totAvailableLS = 0;					
																					$dateFrom=mysqlDateFormat($maximumdt);
																					
																					if ($maximumdt==""){
																						//$currYear=Date("Y");
																					//$currFinanYear="01/04/$currYear";
																					$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																					$dateFrom=mysqlDateFormat(date($defaultDFPDate));
																					//$fromDate=mysqlDateFormat(date("d/m/Y"));
																					$fromDate=mysqlDateFormat(date("$defaultDFPDate"));
																					//$dateFrom=mysqlDateFormat(date("d/m/Y"));
																					//$fromDate=mysqlDateFormat(date("d/m/Y"));
																					}
																					//echo "&&&&&&&&$dateFrom&&&$fromDate";
																					foreach ($gradeRecs as $gR) 
																					{
																						$j++;
																						$sGradeId   = $gR[0];
																						if($rmLotID=="0")
																						{
																							list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																							list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																						}
																						else
																						{
																							list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																							list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																						}
																						//echo "uuuuuuuuuuuuu".$dateFrom;
																						//echo $availableMC;
																						$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
																						//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																						
																						$availableNetMC=$availableMC-$thawingGrdTotal;
																						$totAvailableMC += $availableMC;
																						$totAvailableLS += $availableLS;
																					?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																						<table cellpadding="1" cellspacing="0" width="100%">
																							<tr>
																								<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																									<input type="hidden" name="tothidAvailableallocSlabs_<?=$j?>" id="tothidAvailableallocSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																									<b><?=($availableNetMC!=0)?$availableNetMC:"&nbsp;"?></b>
																								</td>
																							</tr>
																							<tr>
																								<td nowrap class="listing-item" title="Available Purchase order" align="center" width="50%" style="padding-left:2px;padding-right:2px; ">
																								<!--<input type="hidden" name="hidpurchaseQnty_<?=$j?>" id="hidpurchaseQnty_<?=$j?>" value="" readonly="true" />-->
																								<b><span name="purchaseQnty_<?=$j?>" id="purchaseQnty_<?=$j?>" value=""></span></b>
																								</td>
																							</tr>
																						</table>
																					</td>
																					<?php
																					} // Grade Loop Ends here
																					# Total Available Slabs
																					$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																					?>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																						<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																						&nbsp;
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																				</tr>
																				<?php
																				//echo "hiiii".$companyIds."---".$unitIds;
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
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-item" align="center">
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
																						</table>										
																						<!--input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" /-->
																						<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																						<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																						<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																						<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">	
																					<?php 
																					if ($thawingStatus==0)
																					{
																					?>
																						<select name="POId_<?=$i?>" id="POId_<?=$i?>" style="width:120px;" onchange="changePO(this,'<?=$processId?>');">
																							<option value="">--Select--</option>
																							<?php
																							$k=0;
																							foreach($purchaseOrders as $por) 
																							{
																								
																								$poId	= $por[0];
																								$poDate = $por[3];
																								$selected = ($allocatePOId==$poId)?"selected":"";
																							?>
																							<option value="<?=$poId?>" <?=$selected?>><?=$poDate?></option>
																							<?php
																							}
																							?>
																						</select>&nbsp;
																						<?php
																						if ($allocatePOId>0) 
																						{
																						?>
																						<script>
																						getPOItems('<?=$allocatePOId?>', '<?=$i?>');
																						</script>
																						<?php }?>&nbsp;
																						<div id="ViewPOItems" style="padding:2px;"><a href="javascript:void(0);" id="viewPOForAllocation_<?=$i?>" onclick="viewPO('<?=$allocatePOId?>')" class="link1">View</a></div>&nbsp;
																						<?php }?>&nbsp;
																					</td>
																					<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																						<table cellpadding="1" cellspacing="0" width="100%">
																							<tr>
																								<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																							</tr>
																						</table>
																					</td>
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
																						$totNumMC += $numMC;
																						$totNumLS += $numLS;
																						$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
																						$lsPkgGroupArr[$sGradeId] += $numLS;
																							list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																							$totNumMC += $availableMC;
																							$totNumLS += $availableLS;
																							//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																							list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																							$availableNetMC=$availableMC-$thawingGrdTotal;
																						?>
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<table cellpadding="1" cellspacing="0" width="100%">
																								<tr>
																									<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																										<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																										<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																										<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />			
																									</td>
																								</tr>
																								<tr>
																									<!--<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>-->
																								</tr>
																							</table>
																						</td>
																						<?php
																						} // Grade Loop Ends here

																						# Total Slabs
																						$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;
																						#total Qty	
																						$totalQty	= $totalSlabs*$filledWt;
																						?>	
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																						</td>
																						<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																							<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																						</td>
																					</tr>
																					<?php
																						} // Loop Ends here
																					?>
																					<tr><td height="10"></td></tr>
																					<tr>
																						<td style="padding-left:10px;padding-right:10px;" nowrap colspan="2">
																						<?php if ($thawingStatus==0)
																						{
																						?>
																							<a href="javascript:void(0);" id='addRow' onclick="javascript:addAllocateRow();" class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New</a>	
																						<?php }?>
																						</td>
																					</tr>
																					<tr bgcolor="White" >
																						<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																						<td>&nbsp;</td>
																						<td style="<?//=$bottomBdr?>">&nbsp;</td>
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
																					foreach ($pkgGroupArr as $pga=>$gradeArr) 
																					{					
																						$selMcPkgCode = $pga;
																					?>
																					<tr bgcolor="White" id="tRow_<?=$fieldRowSize+$p?>">
																						<TD style="border-left: 1px solid #ffffff; border-top: 1px solid #ffffff;">&nbsp;</TD>
																						<TD class="listing-head" style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>">MC PKG</TD>
																						<TD style="<?=$rightBdr.$bottomBdr?>">&nbsp;</TD>
																						<?php					
																						foreach($gradeRecs as $gR) 
																						{
																								$gradeId = $gR[0];
																								$mcQty  = $gradeArr[$gradeId];
																								$totAllocatedMC += $mcQty;
																						?>
																						<TD class="listing-item" align="right" style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>"><strong><?=($mcQty!=0)?$mcQty:"&nbsp;";?></strong></TD>
																						<?php
																						}
																						?>
																						<TD colspan="2">&nbsp;</TD>			
																					</tr>
																					<?php
																							$p++;
																						}
																					?>
																					<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																					<tr bgcolor="White">
																						<?php
																						# Total Allocated Slabs
																						$totAllocatedSlabs 	= ($totAllocatedMC*$numPacks)+$totAllocatedLS;
																						?>
																						<input type="hidden" name="totAllocatedSlabs" id="totAllocatedSlabs" value="<?=$totAllocatedSlabs?>" readonly="true" />&nbsp;<!--</TD>-->				
																						</tr>
																					</table>
																				</TD>
																			</TR>		
																		</table>
																		<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																		<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																	</TD>
																</tr>
																<?php
																# Allocation section Ends here
																}
																?>
																<!-- Allocation ends here-->
																<!---thawing code-->
																<?php
																/*
																* Thawing starts here
																*/
																if ($thawMode) 
																{
																	$selDate	=	date("d/m/Y");
																?>
																<?php 
																if ($thawingStatus==1)
																{
																?>	
																<tr><TD height="5"></TD></tr>
																<?php }?>
																<tr>
																	<TD colspan="2">
																		<table>	
																			<tr>
																				<TD class="listing-head" nowrap="true">
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
																					<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble">
																						<tr bgcolor="#f2f2f2"  align="center">		
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+4?>">&nbsp;SLABS OF EACH GRADE/COUNT</td>	
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																						</tr>
																						<tr bgcolor="#f2f2f2"  align="center">
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
																							<?php
																								$g = 1;
																								foreach ($gradeRecs as $gR) 
																								{
																									$gId = $gR[0];
																									$gradeCode = $gR[1];
																							?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																								<?=$gradeCode?>
																								<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																							</td>
																							<?php
																									$g++;
																							}
																							?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																							</tr>
																							<tr bgcolor="#f2f2f2"  align="center">
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="2" >
																									Available MC&nbsp;
																									<input type="hidden" name="hidrmLotID" id="hidrmLotID" value="<?=$rmLotID?>" readonly="true" />	
																								</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC </td>
																										</tr>
																									</table>
																								</td>
																								<?php
																								$totAvailableMC = 0;
																								$totAvailableLS = 0;
																								if ($maximumdt=="")
																								{
																									//$currYear=Date("Y");
																									//$currFinanYear="01/04/$currYear";
																									$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																									//$dateFrom=mysqlDateFormat(date("d/m/$currFinanYear"));
																									//$fromDate=mysqlDateFormat(date("d/m/Y"));
																									$dateFrom=mysqlDateFormat(date("$defaultDFPDate"));
																								}
																								else
																								{
																									$dateFrom=mysqlDateFormat($maximumdt);
																								}
																								//echo "____________$dateFrom******------------";
																								foreach ($gradeRecs as $gR) 
																								{
																									$j++;
																									$sGradeId   = $gR[0];
																									if($rmLotID=="0")
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																									}
																									else
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																									}
																									//list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																									$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
																									//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																									if($rmLotID=="0")
																									{ 	list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																									}
																									else
																									{
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																									}
																									$availableNetMC=$availableMC-$thawingGrdTotal;
																									$totAvailableMC += $availableMC;
																									$totAvailableLS += $availableLS;
																									?>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																												<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																													<b><?=($availableMC!=0)?$availableNetMC:"&nbsp;"?></b>
																												</td>
																											</tr>
																										</table>
																									</td>
																									<?php
																									} // Grade Loop Ends here
																									# Total Available Slabs
																									$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																									?>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																										<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																										&nbsp;
																									</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;</td>
																								</tr>
																								<?php
																								$i = 0;
																								$pkgGroupArr = array();
																								$lsPkgGroupArr = array();
																								//printr($productRecs);
																								foreach ($productRecs as $pr) 
																								{
																									$i++;
																									$dFrznPkgEntryId = $pr[0];
																									$frozenLotId 	= $pr[1];
																									$mcPackingId	= $pr[2];
																									$mcPkgCode	= $pr[3];
																									$selProcessCodeId=$pr[7];
																									$selFreezingStageId=$pr[8];
																									$selFrozenCodeId=$pr[9];
																									$frznStkMCPkgId=$pr[10];
																									$comId=$pr[12];
																									$untId=$pr[13];
																									
																								?>	
																								<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
																									<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																									<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																									<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																									<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="3">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;" >MC </td>
																											</tr>
																										</table>
																									</td>
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
																										$totNumMC += $numMC;
																										$totNumLS += $numLS;
																										$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
																										$lsPkgGroupArr[$sGradeId] += $numLS;
																											list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																											//new Code
																											$totNumMC += $availableMC;
																											$totNumLS += $availableLS;
																											list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																											$availableNetMC=$availableMC-$thawingGrdTotal;
																										?>
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																											<table cellpadding="1" cellspacing="0" width="100%">
																												<tr>
																													<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																														<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																														<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																														<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																														<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																														<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />			
																													</td>
																												</tr>
																												<tr>
																													<!--<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>-->
																												</tr>
																											</table>
																										</td>
																										<?php
																										} // Grade Loop Ends here

																										# Total Slabs
																										$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																										#total Qty	
																										$totalQty	= $totalSlabs*$filledWt;
																										?>	
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																											<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																										</td>
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																											<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																										</td>
																									</tr>
																									<?php
																										} // Loop Ends here
																									?>
																									<tr><td height="10"></td></tr>
																									<tr>
																										<td style="padding-left:10px;padding-right:10px;" nowrap colspan="2">&nbsp;
																										
																										</td>
																									</tr>
																									<tr bgcolor="White" >
																										<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																										<input type="hidden" name="hidComId" id="hidComId" value="<?=$comId?>" readonly="true" />
																										<input type="hidden" name="hidUntId" id="hidUntId" value="<?=$untId?>" readonly="true" />
																										<input type="hidden" name="hidProcessType" id="hidProcessType" value="<?=$processTypes?>" />
																										<td>&nbsp;</td>
																										<td style="<?//=$bottomBdr?>">&nbsp;</td>
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
																									foreach ($pkgGroupArr as $pga=>$gradeArr) 
																									{					
																										$selMcPkgCode = $pga;
																									?>
																									<?php
																											$p++;
																									}
																									?>
																									<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																								</table>
																							</TD>
																						</TR>		
																					</table>
																					<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																					<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																				</TD>
																			</tr>
																			<? 
																			//echo "hii".$rmLotID;
																			if($rmLotID=="0") 
																			{ 
																			?>
																			<tr>
																				<td  class="listing-item">Generate new rm lotid<input type="checkbox" name="newRmLot" id="newRmLot" value="1" onclick="generateNewLot();"></td>
																			</tr>
																			<tr  id="rmlotDetails" style="display:none">
																				<td  class="fieldName">RM Lot Id:-</td>
																				<td  class="listing-item"><INPUT name="rmLotIdNew" id="rmLotIdNew" size="7"  readonly style="border:none;"/><INPUT type="hidden" name="number_gen" id="number_gen" size="7"  readonly style="border:none;"/>
																				<INPUT type="hidden" name="rmLotIdChar" id="rmLotIdChar" size="7"  /></td>
																				<INPUT type="hidden" name="rmLotIdNum" id="rmLotIdNum" size="7"  />
																			</tr>
																			
																			<tr>
																				<td>
																					<span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span>
																				</td>
																			</tr>
																			<? 
																			}
																			?>
																			<?php
																			}
																			?>
																			<tr>
																				<td class="fieldName" style="float:left"> Thawed material to be used on<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" /></td>
																				<td class="listing-item">
																					<input name="todDate" type="hidden"  value="<?=$selDate?>" size="9" autoComplete="off" />
																					<input type="hidden" id="thawFlag" name="thawFlag" size="8" value="<?=$g[all]?>" <?=$readOnly?> >
																					<input type="hidden" id="rethawFlag" name="rethawFlag" size="8" value="<?=$statusRpk?>" <?=$readOnly?> >
																				</td>
																			</tr>
																		<!-- thawing ends here-->
																		<?php
																		/*
																		* Repacking starts here
																		*/
																		//echo $repkgMode;
																		if ($repkgMode) 
																		{
																			$allocateMode=false;
																			$selDate	=	date("d/m/Y");
																		?>
																		<?php if ($thawingStatus==2){?>	
																		<tr>
																			<TD height="5"></TD>
																		</tr>
																		<?php }?>
																		<tr>
																			<TD colspan="2">
																				<table>	
																					<tr>
																						<td class="fieldName" style="float:left">Date<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" /></td>
																						<td class="listing-item">
																							<input name="todDate" type="hidden"  value="<?=$selDate?>" size="9" autoComplete="off" />
																							<input type="hidden" id="thawFlag" name="thawFlag" size="8" value="<?=$g[all]?>" <?=$readOnly?> >
																						</td>
																					</tr>
																					<tr>
																						<TD class="listing-head" nowrap="true">
																						PRODUCTION DETAILS OF <?=$displayEditMsg?><?=$selFrozenCode;?>
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
																						<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
																							<tr bgcolor="#f2f2f2"  align="center">		
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+4?>">&nbsp;SLABS OF EACH GRADE/COUNT</td>	
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																							</tr>
																							<tr bgcolor="#f2f2f2"  align="center">
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
																								<?php
																								$g = 1;
																								foreach ($gradeRecs as $gR) {
																									$gId = $gR[0];
																									$gradeCode = $gR[1];
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																									<?=$gradeCode?>
																									<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																								</td>
																								<?php
																										$g++;
																									}
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																							</tr>
																							<tr bgcolor="#f2f2f2"  align="center">				
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																								CURRENT SETTING&nbsp;</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																								CURRENT SETTING&nbsp;</td>				
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">Available MC </td>
																											<input type="hidden" name="hidrmLotID" id="hidrmLotID" value="<?=$rmLotID?>" readonly="true" />	
																										</tr>
																										<!--<tr>
																											<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																										</tr>
																										<tr>
																											<td nowrap class="listing-item" title="Num of Loose Pack" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS</td>
																										</tr>-->
																									</table>
																								</td>
																								<?php
																								$totAvailableMC = 0;
																								$totAvailableLS = 0;
																								$lastVal=explode(" ",$dis[2]);
																										//print_r($lastVal);
																										$n=count($lastVal);
																										//echo $n;
																										$lastVal=$lastVal[$n-2];
																								$glazeId=$frozenpackingObj->frznPkgglaze($frozenCode);
																								$frozenCodeValues=$frozenStockAllocationObj->getFrozQty($processId,$glazeId,$lastVal,$frozenCode);
																								$getF=$frozenpackingObj->find($frozenCode);
																								 $selFrozenCode=$getF[0];
																								 //echo "---".$selFrozenCode;
																								//$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
																								$selFrozenCode1=$getF[1];
																									//echo "---".$processId;
																									//print_r($frozenCodeValues);
																								if ($maximumdt==""){
																								//$currYear=Date("Y");
																								//$currFinanYear="01/04/$currYear";
																								$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																								//$dateFrom=mysqlDateFormat(date("d/m/$currFinanYear"));
																								//$fromDate=mysqlDateFormat(date("d/m/Y"));
																								$dateFrom=mysqlDateFormat(date("$defaultDFPDate"));
																								}
																								else{
																										$dateFrom=mysqlDateFormat($maximumdt);
																								}
																								//echo "____________$dateFrom******------------";
																								foreach ($gradeRecs as $gR) 
																								{
																									$j++;
																									
																									$sGradeId   = $gR[0];
																									if($rmLotID=="0")
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);	
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																									}
																									else
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																									}
																									
																									
																									$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
																									//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																									
																									$availableNetMC=$availableMC-$thawingGrdTotal;
																									//$totAvailableMC += $availableMC;
																									//$totAvailableLS += $availableLS;
																									$totAvailableMC += $availableNetMC;
																									$totAvailableLS += $availableNetLS;
																										if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																								?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																										<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																											<b><?=($availableMC!=0)?$availableNetMC:"&nbsp;"?></b>
																										</td>
																									</tr>
																									<tr>
																										<TD colspan="3"  style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																									</tr>
																									<tr>
																										<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																											<b><?//=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
																										</td>
																									</tr>
																								</table>
																							</td>
																							<?php
																							} // Grade Loop Ends here
																			
																							# Total Available Slabs
																							$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																							?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<b><?=$totAvailableSlabs;?><b>
																								<?php $totalQty	= $totAvailableSlabs*$filledWt;
																								?>
																								<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																								&nbsp;
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">&nbsp;<b><?=$totalQty;?><b></td>
																						</tr>
																						<?php
																						$i = 0;
																						$pkgGroupArr = array();
																						$lsPkgGroupArr = array();
																						//foreach ($productRecs as $pr) {
																							//$i++;
																							$i=1;
																							$k=1;
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
																							<input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																							<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																							<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																							<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																							<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																							<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																							<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																										<!--<input type="text" name="tmcPackingId_<?=$i?>" value="<?=$mcpackingCode?>"-->
																										<input type="hidden" id="hidselFrozenCode_1" name="hidselFrozenCode_1" value=<?=$frozenCode;?> size=4 />
																										<input type="text" name="trpselFrozenCode_<?=$i?>" value="<?=$selFrozenCode1?>" readonly style="text-align:right; border:none;"/>
																										</td>
																									</tr>
																								</table>
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1"><input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
																								<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																										<?//=$frozenCode;?>
																											<!--<select id="repselFrozenCode_<?=$k?>" name="repselFrozenCode_<?//=$k?>" onchange="xajax_getFilledWt(document.getElementById('repselFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('repselFrozenCode_<?=$k?>').value, '');" >-->
																											<input type="text" name="trepmcPackingId_<?=$i?>" value="<?=$stkAllocateMCPkgCode?>" style="text-align:right; border:none;" readonly size=4/>
																										</td>
																									</tr>
																								</table>
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item"><table cellpadding="1" cellspacing="0" width="100%">
																								<tr>
																									<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC
																									</td>
																								</tr>
																								<tr>
																									<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																								</tr>
																								<tr>
																									<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS
																									</td>
																								</tr>
																							</table>
																						</td>
																						<input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
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
																							if($rmLotID=="0")
																							{
																								list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);	
																								list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																							}
																							else
																							{
																								list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																								list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																							}
																							
																							//list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																							//new Code
																							//$totNumMC += $availableMC;
																							$totNumLS += $availableLS;
																							//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																							$availableNetMC=$availableMC-$thawingGrdTotal;
																							$totNumMC += $availableNetMC;
																							//echo $selMCPkgId;
																							$mcpackingRec	= $mcpackingObj->find($stkAllocateMCPkgId);
																							$numPacks	= $mcpackingRec[2];
																							$hidtotSlabs=$availableNetMC*$numPacks;
																							if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																							//echo $availableNetMC;
																							?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																											<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																											<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																											<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																											<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																											<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyrep();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);"  <?php if ($availableNetMC==0){ ?> readonly <?php }?> />
																											<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																											<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																											<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																											<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->			
																										</td>
																									</tr>
																									<tr>
																										<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																									</tr>
																									<tr>
																										<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																											<!--<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->
																											<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="" readonly/>	
																											<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																											<input name="numLSG_<?=$j?>_<?=$i?>" type="hidden" id="numLSG_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
																											<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																										</td>
																									</tr>
																								</table>
																							</td>
																							<?php
																							} // Grade Loop Ends here

																							# Total Slabs
																							$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																							#total Qty	
																							//echo $filledWt;
																							$totalQty	= $totalSlabs*$filledWt;
																							?>	
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																							</td>
																						</tr>
																						<tr bgcolor="#f2f2f2"  align="center">				
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																							SET FROZEN CODE&nbsp;</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																							SET MC PKG&nbsp;</td>				
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item" title="Num of MC" align="center" width="50%" style="padding-left:2px;padding-right:2px;">Repacked MC </td>
																									</tr>
																								</table>
																							</td>
																							<?php 
																							foreach ($gradeRecs as $gR) 
																							{
																								$gId = $gR[0];
																								$gradeCode = $gR[1];
																							?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																								<?=$gradeCode?>
																								<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																								<b><?=($availableMC!=0)?$availableNetMC:"&nbsp;"?></b>
																							</td>
																							<?php
																							} // Grade Loop Ends here
																	
																							# Total Available Slabs
																							$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;
																							?>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																							SLABS 	
																								<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																								&nbsp;
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">&nbsp;QTY (KG)</td>
																						</tr>
																						<!--New Row Start--->
																						<?php
																						$k=2;
																						$i=2;
																						?>
																						<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
																							<input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																							<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																							<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																							<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																							<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																							<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																							<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																										<!--<input type="text" name="tmcPackingId_<?=$i?>" value="<?=$mcpackingCode?>"-->
																											<select id="repselFrozenCode_<?=$k?>" name="repselFrozenCode_<?=$k?>" onchange="xajax_assignFrzPackChgrep(this.value);xajax_getFilledWt(document.getElementById('repselFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('repselFrozenCode_<?=$k?>').value, '')" >
																												<option value="0">-- Select --</option>
																												<?php
																												if (sizeof($frozenCodeValues)>0) 
																												{	
																													foreach($frozenCodeValues as $frozenPackingId=>$frozenPackingCode) 
																													{
																														//if ($frozenPackingId==$frozenCode) continue;
																												?>	
																												<option value="<?=$frozenPackingId?>" <?php //if ($selFrozenCode==$frozenPackingId){?>  <?php //}?>  ><?=stripslashes($frozenPackingCode)?></option>
																												<?php
																													}
																												} else {
																												?>
																												<option value="<?=$frozenCode?>" ><?=stripslashes($selFrozenCode1)?></option>
																												<option value="">-- Select --</option>
																												<?php
																												}
																												?>	
																											</select>
																										</td>
																									</tr>
																								</table>
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1"><input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
																								<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																										<?//=$frozenCode;?>
																										<!--<select id="repselFrozenCode_<?=$k?>" name="repselFrozenCode_<?//=$k?>" onchange="xajax_getFilledWt(document.getElementById('repselFrozenCode_<?=$k?>').value, '<?=$k?>'); xajax_getMCPkgs('<?=$k?>', document.getElementById('repselFrozenCode_<?=$k?>').value, '');" >-->
																											<select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPackChgrep(document.getElementById('mcPackingId_<?=$i?>').value,'<?=$i?>','<?=$i?>');"  >
																												<option value="0">--Select--</option>
																												<?php
																													  foreach($mcpackingRecords as $mcp) {
																														$mcPkgId		= $mcp[0];
																														$mcpackingCode		= stripSlash($mcp[1]);
																														//$selected		= ($stkAllocateMCPkgId==$mcPkgId)?"selected":"";
																													?>
																												<option value="<?=$mcPkgId?>" <?=$selected?>>
																												  <?=$mcpackingCode?>
																												  </option>
																												<? }?>
																											</select>
																										</td>
																									</tr>
																								</table>
																							</td>
																							<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item">
																								<table cellpadding="1" cellspacing="0" width="100%">
																									<tr>
																										<td nowrap class="listing-item"  align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC
																										</td>
																									</tr>
																									<tr>
																										<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																									</tr>
																									<tr>
																										<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS
																										</td>
																									</tr>
																								</table>
																							</td>
																							<input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																							<?php
																							# MC Section
																							$j=0;
																							$totNumMC = 0;
																							$totNumLS = 0;
																							foreach ($gradeRecs as $gR) 
																							{
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

																								if($rmLotID=="0")
																								{
																									list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);	
																									list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																								}
																								else
																								{
																									list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																									list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																								}


																									//list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																									//new Code
																									//$totNumMC += $availableMC;
																									$totNumLS += $availableLS;
																									//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																									$availableNetMC=$availableMC-$thawingGrdTotal;
																									$totNumMC += $availableNetMC;
																									//echo $selMCPkgId;
																									$mcpackingRec	= $mcpackingObj->find($stkAllocateMCPkgId);
																									$numPacks	= $mcpackingRec[2];
																									$hidtotSlabs=$availableNetMC*$numPacks;
																									if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																									?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																												<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																												<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																												<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																												<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																												<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="" style="text-align:right;" autocomplete="off"  readonly onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->			
																											</td>
																										</tr>
																										<tr>
																											<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																										</tr>
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																												<!--<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" value="<?=($numLS!=0)?$numLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->
																												<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" readonly onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value=""/>	
																												<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												<input name="numLSG_<?=$j?>_<?=$i?>" type="hidden" id="numLSG_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
																												<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																											</td>
																										</tr>
																									</table>
																								</td>
																								<?php
																								} // Grade Loop Ends here

																								# Total Slabs
																								$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																								#total Qty	
																								//echo $filledWt;
																								$totalQty	= $totalSlabs*$filledWt;
																								?>	
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																								</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																								</td>
																							</tr>
																							<?php
																								//} // Loop Ends here
																							?>
																							<tr><td height="10"></td></tr>
																							<tr>
																								<td style="padding-left:10px;padding-right:10px;" nowrap colspan="2">&nbsp;
																								</td>
																							</tr>
																							<tr bgcolor="White" >
																								<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																								<td>&nbsp;</td>
																								<td style="<?//=$bottomBdr?>">&nbsp;</td>
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
																							<?php
																									$p++;
																								}
																							?>
																							<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																						</table>
																					</TD>
																				</TR>		
																			</table>
																			<input type="hidden" name="hidSummaryTblRowCount" id="hidSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																			<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productRecSize?>" readonly="true" />
																			<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																			<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																			<input type="hidden" name="hidProdnRowCount" id="hidProdnRowCount" value="<?=$i?>" readonly="true" />
																			<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																		</TD>
																	</tr>
																	<?php
																		//echo "---$allocateMode";
																	}
																	?>
																	<!-- repacking ends here-->
																	<!-- reglazing starts here-->
																	<?php
																	//echo "Reglaze$reglzMode";
																	if ($reglzMode) 
																	{
																		$allocateMode=false;
																		$selDate	=	date("d/m/Y");
																	?>
																	<?php if ($thawingStatus==3){?>	
																	<tr><TD height="5"></TD></tr>
																	<?php }?>
																	<tr>
																		<TD colspan="2">
																			<table>	
																				<tr>
																					<td class="fieldName" style="float:left">Date<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" /></td>
																					<td class="listing-item">
																						<input name="todDate" type="hidden"  value="<?=$selDate?>" size="9" autoComplete="off" />
																						<input type="hidden" id="thawFlag" name="thawFlag" size="8" value="<?=$g[all]?>" <?=$readOnly?> >
																					</td>
																				</tr>
																				<tr>
																					<TD class="listing-head" nowrap="true">
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
																						<table width="200" border="0" cellpadding="1" cellspacing="0" align="center" id="prodnDtlsTble">
																							<tr bgcolor="#f2f2f2"  align="center">		
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$topBdr.$bottomBdr?>" class="listing-head" colspan="<?=sizeof($gradeRecs)+3?>">&nbsp;SLABS OF EACH GRADE/COUNT</td>	
																								<!--<td nowrap style="padding-left:2px;padding-right:2px; <?=$fullBdr?> " class="listing-head" colspan="<?=sizeof($gradeRecs)?>">
																									SLABS OF EACH GRADE/COUNT
																								</td>-->
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$topBdr.$bottomBdr.$rightBdr?>" class="listing-head" colspan="2">TOTAL</td>			
																							</tr>
																							<tr bgcolor="#f2f2f2"  align="center">
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head" colspan="3">&nbsp;<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">&nbsp;ENTRY NO--></td>
																								<?php
																								$g = 1;
																								foreach ($gradeRecs as $gR) {
																									$gId = $gR[0];
																									$gradeCode = $gR[1];
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">
																									<?=$gradeCode?>
																									<input type="hidden" name="gId_<?=$g?>" id="gId_<?=$g?>" value="<?=$gId?>" readonly="true" />
																								</td>
																								<?php
																									$g++;
																								}
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)</td>
																							</tr>
																							<tr bgcolor="#f2f2f2"  align="center">
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																								INTIAL SETTING FROZEN CODE&nbsp;</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$bottomBdr.$rightBdr ?>" class="listing-head"  colspan="1" >
																									INTIAL SETTING MC PKG&nbsp;</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">Available MC </td>
																											<input type="hidden" name="hidrmLotID" id="hidrmLotID" value="<?=$rmLotID?>" readonly="true" />	
																										</tr>
																									</table>
																								</td>
																								<?php
																								$frznCodeRecs = $frozenStockAllocationObj->glazeFrzncode($fishIdth, $processId,$code);
																								//$glazeRecords		= $glazeObj->fetchAllRecords();
																								$glazeRecords		= $glazeObj->fetchAllRecordsGlazeActive();
																								$totAvailableMC = 0;
																								$totAvailableLS = 0;
																								if ($maximumdt=="")
																								{
																									//$currYear=Date("Y");
																									//$currFinanYear="01/04/$currYear";
																									$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																									//$dateFrom=mysqlDateFormat(date("d/m/$currFinanYear"));
																									//$fromDate=mysqlDateFormat(date("d/m/Y"));
																									$dateFrom=mysqlDateFormat(date("$defaultDFPDate"));
																								}
																								else
																								{
																									$dateFrom=mysqlDateFormat($maximumdt);
																								}
																								//echo "____________$dateFrom******------------";
																								//echo	sizeof($gradeRecs);
																								foreach ($gradeRecs as $gR) 
																								{
																									$j++;
																									
																									$sGradeId   = $gR[0];
																									//echo $rmLotID;
																									if($rmLotID=='0')
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																									}
																									else
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																										
																									}
																									$selectDate		=($p["selDate"]!="")?mysqlDateFormat($p["selDate"]):mysqlDateFormat(date("d/m/Y"));
																									//list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$selectDate,$sGradeId);
																									
																									$availableNetMC=$availableMC-$thawingGrdTotal;
																									//$totAvailableMC += $availableMC;
																									//$totAvailableLS += $availableLS;
																									$totAvailableMC += $availableNetMC;
																									$totAvailableLS += $availableNetLS;
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																											<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																												<b><?=($availableMC!=0)?$availableNetMC:"&nbsp;"?></b>
																											</td>
																										</tr>
																										<tr>
																											<TD colspan="3"  style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																										</tr>
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																												<b><?//=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
																											</td>
																										</tr>
																									</table>
																								</td>
																								<?php
																									} // Grade Loop Ends here

																									# Total Available Slabs
																									$totAvailableSlabs 	= ($totAvailableMC*$numPacks)+$totAvailableLS;

																									$totAvailableSlabs=number_format($totAvailableSlabs,2,'.','');
																								?>
																								<?php 
																								if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																								$totalQty	= $totAvailableSlabs*$filledWt;
																								$totalQty=number_format($totalQty,2,'.','');
																								?>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item"><b><?=$totAvailableSlabs;?><b>
																									<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																									&nbsp;
																								</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">&nbsp;
																								
																								<b><?=$totalQty;?></b>
																								</td>
																							</tr>
																							<?php
																							$i = 0;
																							$pkgGroupArr = array();
																							$lsPkgGroupArr = array();
																							//foreach ($productRecs as $pr) {
																								//$i++;
																								$i=1;
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
																								<input type="hidden" name="numMcPack_<?=$i?>" id="numMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																								<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																								<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																								<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																								<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																								<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																								<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																											<?php
																												$lastVal=explode(" ",$dis[2]);
																												//print_r($lastVal);
																												$n=count($lastVal);
																												//echo $n;
																												$lastVal=$lastVal[$n-2];
																												//echo $lastVal;
																												//list($unit,$freeId,$glazeFreezId,$declWt,$frozID)=$frozenStockAllocationObj->getfrozenCodeValue($dis[2]);
																												list($unit,$freeId,$glazeFreezId,$declWt,$frozID)=$frozenStockAllocationObj->getfrozenCodeValue($frozenCode);
																												$selFrozenCodeId=$frozenCode;
																												$getF=$frozenpackingObj->find($frozenCode);
																												 $selFrozenCode=$getF[0];
																												$selFrozenCode1=$getF[1];
																												$glazeId=$frozenpackingObj->frznPkgglaze($frozenCode);
																												$frozenCodeValues=$frozenStockAllocationObj->getFrozQtygl($processId,$glazeId);
																												$k=1;
																												?>
																													<input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
																													<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
																													<input type=hidden name="hidcomb" id="hidcomb" value="" />
																													<input type="hidden" id="hidselFrozenCode_1" name="hidselFrozenCode_1" value=<?=$frozenCode;?> size=4 />
																													<input type="text" name="tselFrozenCode_<?=$i?>" value="<?=$selFrozenCode1?>" readonly style="text-align:right; border:none;"/>
																											</td>
																										</tr>
																									</table>
																								</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																												<input type="text" name="tmcPackingId_<?=$i?>" value="<?=$stkAllocateMCPkgCode?>" style="text-align:right; border:none;" readonly size=4/>
																											</td>
																										</tr>
																									</table>
																								</td>
																								<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item">
																									<table cellpadding="1" cellspacing="0" width="100%">
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC
																											</td>
																										</tr>
																										<tr>
																											<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																										</tr>
																										<tr>
																											<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS
																											</td>
																										</tr>
																									</table>
																								</td>
																								<input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
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

																									if($rmLotID=='0')
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																									}
																									else
																									{
																										list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																										list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																										
																									}

																										//new Code
																										//$totNumMC += $availableMC;
																										$totNumLS += $availableLS;
																										
																										$availableNetMC=$availableMC-$thawingGrdTotal;
																										$totNumMC += $availableNetMC;
																										//echo $selMCPkgId;
																										$mcpackingRec	= $mcpackingObj->find($stkAllocateMCPkgId);
																										$numPacks	= $mcpackingRec[2];
																										$hidtotSlabs=$availableNetMC*$numPacks;
																										if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																									?>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																													<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																													<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																													<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																													<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																													<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" <?php if ($availableNetMC==0){ ?> readonly <?php }?> />
																													<input name="newnumMC_<?=$j?>_<?=$i?>" type="hidden" id="newnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" <?php if ($availableNetMC==0){ ?> readonly <?php }?> />
																													<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																													<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																													<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																													<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->			
																												</td>
																											</tr>
																											<tr>
																												<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																											</tr>
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																													<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value=""/>
																													<input name="newnumLS_<?=$j?>_<?=$i?>" type="hidden" id="newnumLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value=""/>
																													<input name="numLSG_<?=$j?>_<?=$i?>" type="hidden" id="numLSG_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
																													<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																													<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																												</td>
																											</tr>
																										</table>
																									</td>
																									<?php
																									} // Grade Loop Ends here

																									# Total Slabs
																									$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																									#total Qty	
																									$totalQty	= $totalSlabs*$filledWt;
																									?>	
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																									</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																									</td>
																								</tr>
																								<tr bgcolor="#f2f2f2"  align="center">
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?>" class="listing-head"  colspan="1" >
																										SET FROZEN CODE&nbsp;</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$bottomBdr.$rightBdr ?>" class="listing-head"  colspan="1" >
																										SET MC PKG&nbsp;</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">Reglazed MC </td>
																											</tr>
																										</table>
																									</td>
																									<?php
																									$g = 1;
																									foreach ($gradeRecs as $gR) 
																									{
																										$gId = $gR[0];
																										$gradeCode = $gR[1];
																									?>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap  title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;"  class="listing-head">
																												<?=$gradeCode?>

																												<input type="hidden" name="tothidAvailableSlabs_<?=$j?>" id="tothidAvailableSlabs_<?=$j?>" value="<?=$availableNetMC?>" readonly="true" />
																													<!--<b><?=($availableMC!=0)?$availableNetMC:"&nbsp;"?></b>-->
																													
																												</td>
																											</tr>
																											<tr>
																												<TD colspan="3"  style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																											</tr>
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																													<b><?//=($availableLS!=0)?$availableLS:"&nbsp;"?></b>
																												</td>
																											</tr>
																										</table>
																									</td>
																									<?php
																									} // Grade Loop Ends here
																									?>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">SLABS 	
																										<input type="hidden" name="totAvailableSlabs" id="totAvailableSlabs" value="<?=$totAvailableSlabs?>" readonly="true" />
																										&nbsp;
																									</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-head">QTY (KG)&nbsp;</td>
																								</tr>
																								<!---New Row Start--->
																								<tr bgcolor="White" id="allocateRow_<?=$i;?>" class="tr_clone">				
																									<input type="hidden" name="numMcPack_<?=$k?>" id="numMcPack_<?=$k?>" value="<?=$numPacks;?>" readonly="true" />
																									<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																									<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																									<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																									<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																									<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																									<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />					
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																												<?php
																													$lastVal=explode(" ",$dis[2]);
																													//print_r($lastVal);
																													$n=count($lastVal);
																													//echo $n;
																													$lastVal=$lastVal[$n-2];
																													//echo $lastVal;
																													//list($unit,$freeId,$glazeFreezId,$declWt,$frozID)=$frozenStockAllocationObj->getfrozenCodeValue($dis[2]);
																													list($unit,$freeId,$glazeFreezId,$declWt,$frozID)=$frozenStockAllocationObj->getfrozenCodeValue($frozenCode);
																													$selFrozenCodeId=$frozenCode;
																													$getF=$frozenpackingObj->find($frozenCode);
																													 $selFrozenCode=$getF[0];
																													//echo "---".$selFrozenCode;
																													//$selFrozenC=$frozenpackingObj->find($glfrozenCodeId);
																													$selFrozenCode1=$getF[1];
																													//echo $selFrozenCodeId;
																													//echo $unit;
																													//echo $freeId;
																													//echo $glazeFreezId;
																													//$frozenCodeValues=$frozenStockAllocationObj->getAllfrozenCodeValues($unit,$freeId,$glazeFreezId,$declWt,$lastVal);
																													//echo "hai";
																													//echo sizeof($frozenCodeValues);
																													$glazeId=$frozenpackingObj->frznPkgglaze($frozenCode);
																													$frozenCodeValues=$frozenStockAllocationObj->getFrozQtygl($processId,$glazeId);
																													//$k=1;
																													$k=2;
																													$i=2;
																													?>
																													<input type=hidden name="finfrozenCode" id="finfrozenCode" value="<?=$frozenCode;?>" />
																													<input type=hidden name="hidffilledWt" id="hidffilledWt" value="" />
																													<input type=hidden name="hidcomb" id="hidcomb" value="" />
																													<input type="hidden" id="hidselFrozenCode_1" name="hidselFrozenCode_1" value=<?=$frozenCode;?> />
																													<select id="selFrozenCode_<?=$k?>" name="selFrozenCode_<?=$k?>" onchange="xajax_assignFrzPackChg(this.value,'<?=$k?>');xajax_getFilledWt(document.getElementById('selFrozenCode_<?=$k?>').value, '<?=$k?>');"  >
																														<?php		
																														if (sizeof($frozenCodeValues)>0) 
																														{	
																														?>
																														<option value="0">-- Select --</option>
																														<!--<option value="<?=$frozenCode?>" ><?=stripslashes($selFrozenCode1)?></option>-->
																														<?php
																														foreach($frozenCodeValues as $frozenPackingId=>$frozenPackingCode) 
																														{
																															//$selFrznPkg = ($selFrozenCodeId==$frozenPackingId)?"selected":"";
																															//if ($frozenPackingId==$frozenCode) continue;
																														?>	
																														<option value="<?=$frozenPackingId?>" <?php //if ($selFrozenCodeId==$frozenPackingId){?>  <?php //}?>  ><?=stripslashes($frozenPackingCode)?></option>	
																														<?php
																																}
																														} else {
																														?>
																														<option value="">-- Select --</option>
																														<!--<option value="<?=$frozenCode?>" ><?=stripslashes($selFrozenCode1)?></option>-->
																														<?php
																														}
																														?>	
																													</select>
																												</td>
																											</tr>
																										</table>
																									</td>
																									<td nowrap style="padding-left:2px;padding-right:2px; <?=$leftBdr.$rightBdr.$bottomBdr?><?=$rightBdr.$bottomBdr?>" class="listing-item" colspan="1">
																										<table cellpadding="1" cellspacing="0" width="100%">
																											<tr>
																												<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;" > <?//=$i?>
																													<select name="mcPackingId_<?=$i?>" id="mcPackingId_<?=$i?>" onchange="xajax_assignMCPackChgrprg(document.getElementById('mcPackingId_<?=$i?>').value,'<?=$i?>');"  >
																														<option value="0">--Select--</option>
																														<?php
																															  foreach($mcpackingRecords as $mcp) {
																																$mcPkgId		= $mcp[0];
																																$mcpackingCode		= stripSlash($mcp[1]);
																																//$selected		= ($stkAllocateMCPkgId==$mcPkgId)?"selected":"";
																															?>
																														<option value="<?=$mcPkgId?>" <?=$selected?>>
																														  <?=$mcpackingCode?>
																														  </option>
																														<? }?>
																													  </select>
																													</td>
																												</tr>
																											</table>
																										</td>
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?><?//=$rightBdr.$bottomBdr?>" class="listing-item">
																											<table cellpadding="1" cellspacing="0" width="100%">
																												<tr>
																													<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">MC
																													</td>
																												</tr>
																												<tr>
																													<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																												</tr>
																												<tr>
																													<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">LS
																													</td>
																												</tr>
																											</table>
																										</td>
																										<input type="hidden" name="hidNumMcPackPrev_<?=$i?>" id="hidNumMcPackPrev_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
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
																											if($rmLotID=='0')
																											{
																												list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate);
																												list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQty($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId);
																											}
																											else
																											{
																												list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processId, $freezingStage, $frozenCode, $sGradeId, $stkAllocateMCPkgId,$dateFrom,$tillDate,$rmLotID);
																												list($thawingGrdTotal)=$frozenStockAllocationObj->getThaGradeQtyLot($processId, $freezingStage, $frozenCode,$stkAllocateMCPkgId,$dateFrom,$sGradeId,$rmLotID);
																												
																											}
																											$availableNetMC=$availableMC-$thawingGrdTotal;
																											$totNumMC += $availableNetMC;
																											//echo $selMCPkgId;
																											$mcpackingRec	= $mcpackingObj->find($stkAllocateMCPkgId);
																											$numPacks	= $mcpackingRec[2];
																											$hidtotSlabs=$availableNetMC*$numPacks;
																											if ($frozenCode) $filledWt = $frozenpackingObj->frznPkgFilledWt($frozenCode);
																											?>
																											<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																												<table cellpadding="1" cellspacing="0" width="100%">
																													<tr>
																														<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																															<input type="hidden" name="sGradeId_<?=$j?>_<?=$i?>" id="sGradeId_<?=$j?>_<?=$i?>" size="4" value="<?=$sGradeId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																															<input type="hidden" name="gradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$gradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																															<input type="hidden" name="allocateGradeEntryId_<?=$j?>_<?=$i?>" id="gradeEntryId_<?=$j?>_<?=$i?>" size="4" value="<?=$allocateGradeEntryId?>" style="text-align:right;" autocomplete="off" readonly="true" />
																															<!--<input name="numMCAv_<?=$j?>_<?=$i?>" type="text" id="numMCAv_<?=$j?>_<?=$i?>" size="4" value="<?=$availableNetMC?>" />-->
																															<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" readonly value="" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQtyreg();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" readonly/>
																															<input name="inumMC_<?=$j?>_<?=$i?>" type="hidden" id="inumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />

																															<input name="numMCG_<?=$j?>_<?=$i?>" type="hidden" id="numMCG_<?=$j?>_<?=$i?>" size="4" value="<?=($availableNetMC!=0)?$availableNetMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																															<input name="hidnumMC_<?=$j?>_<?=$i?>" type="hidden" id="hidnumMC_<?=$j?>_<?=$i?>" size="4" value="<?=($hidtotSlabs!=0)?$hidtotSlabs:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																															<!--<input name="numMC_<?=$j?>_<?=$i?>" type="text" id="numMC_<?=$j?>_<?=$i?>" size="4" value="<?=($numMC!=0)?$numMC:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />-->			
																														</td>
																													</tr>
																													<tr>
																														<TD colspan="3" background="images/HL.png" style="background-repeat:repeat-x;color:#f2f2f2;line-height:normal;" width="100" height="1"></TD>
																													</tr>
																													<tr>
																														<td nowrap class="listing-item" title="" align="center" width="50%" style="padding-left:2px;padding-right:2px;">
																															<input name="numLS_<?=$j?>_<?=$i?>" type="text" id="numLS_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="" readonly/>
																															<input name="numLSG_<?=$j?>_<?=$i?>" type="hidden" id="numLSG_<?=$j?>_<?=$i?>" size="4" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" value="<?=($availableLS!=0)?$availableLS:""?>"/>
																															<input name="hidnumLS_<?=$j?>_<?=$i?>" type="hidden" id="hidnumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																															<input name="inumLS_<?=$j?>_<?=$i?>" type="hidden" id="inumLS_<?=$j?>_<?=$i?>" size="4" value="<?=($availableLS!=0)?$availableLS:""?>" style="text-align:right;" autocomplete="off" onkeyup="calcAllocateProdnQty();" onkeydown="return nTxtBoxAL(event,'document.frmDailyFrozenPacking',this);" />
																														</td>
																													</tr>
																												</table>
																											</td>
																											<?php
																											} // Grade Loop Ends here

																											# Total Slabs
																											$totalSlabs 	= ($totNumMC*$numPacks)+$totNumLS;

																											#total Qty	
																											$totalQty	= $totalSlabs*$filledWt;
																										?>
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																											<?//=$i?><input name="totalSlabs_<?=$i?>" type="text" id="totalSlabs_<?=$i?>" size="4" value="<?//=$totalSlabs?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />
																										</td>
																										<td nowrap style="padding-left:2px;padding-right:2px; <?=$rightBdr.$bottomBdr?>" class="listing-item">
																											<input type="text" name="totalQty_<?=$i?>" id="totalQty_<?=$i?>" size="6" value="<?//=($totalQty!=0)?number_format($totalQty,2,'.',''):"";?>" style="text-align:right; border:none;" autocomplete="off" readonly="true" />					
																										</td>
																									</tr>
																									<input type="hidden" name="numMcPack_<?=$k?>" id="numMcPack_<?=$k?>" value="<?=$numPacks;?>" readonly="true" />
																									<input type="hidden" name="hidNumMcPack_<?=$i?>" id="hidNumMcPack_<?=$i?>" value="<?=$numPacks;?>" readonly="true" />
																									<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																									<input type="hidden" name="dFrznPkgEntryId_<?=$i?>" id="dFrznPkgEntryId_<?=$i?>" value="<?=$dFrznPkgEntryId;?>" readonly="true" />
																									<input type="hidden" name="dFrznPkgMainId_<?=$i?>" id="dFrznPkgMainId_<?=$i?>" value="<?=$dFrznPkgMainId;?>" readonly="true" />
																									<input type="hidden" name="POEntryId_<?=$i?>" id="POEntryId_<?=$i?>" value="<?=$POEntryId;?>" readonly="true" />
																									<input type="hidden" name="status_<?=$i?>" id="status_<?=$i?>" value="" readonly="true" />	
																									<!---New Row End--->
																									<?php
																									//} // Loop Ends here
																									?>
																									<tr><td height="10"></td></tr>
																									<tr>
																										<td style="padding-left:10px;padding-right:10px;" nowrap colspan="2">&nbsp;
																										</td>
																									</tr>
																									<tr bgcolor="White" >
																										<input type="hidden" name="hidAllocateTblRowCount" id="hidAllocateTblRowCount" value="<?=$productRecSize?>" readonly="true" />
																										<td>&nbsp;</td>
																										<td style="<?//=$bottomBdr?>">&nbsp;</td>
																										<td style="<?//=$bottomBdr?>">&nbsp;</td>
																										<?php					
																										foreach ($gradeRecs as $gR) 
																										{
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
																									<?php
																										$p++;
																									}
																									?>
																									<input type="hidden" name="hidAllocateSummaryTblRowCount" id="hidAllocateSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																								</table>
																							</TD>
																						</TR>		
																					</table>
																					<input type="hidden" name="hidSummaryTblRowCount" id="hidSummaryTblRowCount" value="<?=sizeof($pkgGroupArr)?>" readonly="true" title="summary table row count" />
																					<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$productRecSize?>" readonly="true" />
																					<input type="hidden" name="hidAllocateProdnRowCount" id="hidAllocateProdnRowCount" value="<?=$i?>" readonly="true" />
																					<input type="hidden" name="hidAllocateGradeRowCount" id="hidAllocateGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																					<input type="hidden" name="hidProdnRowCount" id="hidProdnRowCount" value="<?=$i?>" readonly="true" />
																					<input type="hidden" name="hidGradeRowCount" id="hidGradeRowCount" value="<?=sizeof($gradeRecs);?>" readonly="true" />
																				</TD>
																			</tr>
																			<?php
																			//echo "---$allocateMode";
																			}
																			?>
																			<!-- reglazing ends here-->
																		</table>
																	</td>
																</tr>
																<tr>
																	<? if($editMode){
																	?>
																	<td align="center">
																	<? 
																	if ($thawingStatus==2)
																	{
																	$allocateMode=false;?>
																		<input type="submit" name="cmdCancel" class="button" value=" Cancel "  onClick=" this.form.action='FrozenStockAllocation.php?all=-1';" >&nbsp;&nbsp;
																		<input type="submit" name="cmdSaveChange" class="button" value=" Repack " onClick="return validateDFPPacking(form)">
																	<?php 
																	}
																	if ($thawingStatus==3) {
																	 $allocateMode=false;?>
																		<input type="submit" name="cmdCancel" class="button" value=" Cancel "  onClick=" this.form.action='FrozenStockAllocation.php?all=-1';" >&nbsp;&nbsp;
																		<input type="submit" name="cmdReglzSaveChange" class="button" value=" Reglaze " onClick="return validateDFPReglazing(form)">
																		<input type="hidden" name="RpYes" id="RpYes" />
																	<?php 
																	}
																	if (($thawingStatus==1) || ($thawMode)){
																	?>
																		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="this.form.action='FrozenStockAllocation.php?all=-1';">
																		<input type="submit" name="cmdThawing" class="button" value=" Thawing " onClick="return validateDFPThawing(form);">
																	<?php }	
																	if($editMode && !$allocateMode){?>
																		<?php } else if ($allocateMode) {?>
																		<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenStockAllocation.php');">-->&nbsp;&nbsp;
																			<?php if ($thawingStatus==0){?>
																			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="this.form.action='FrozenStockAllocation.php?all=-1';">
																			<input type="submit" name="cmdAllocation" class="button" value=" Allocate " onClick="return validateDFPAllocation(document.frmDailyFrozenPacking);">
																	<?php }
																		} 
																	?>
																	</td>
																	<? } else{?>
																	<td align="center">
																		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenStockAllocation.php');">&nbsp;&nbsp;
																		<input type="submit" name="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" style="width:100px;">&nbsp;&nbsp;
																		<input name="cmdSaveAndAddNew" type="submit" class="button" id="cmdSaveAndAddNew" style="width:130px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New">
																		<? if ($selQuickEntryList!="") {?>
																			&nbsp;&nbsp;<input name="cmdSaveAndQE" type="submit" class="button" id="cmdSaveAndQE" style="width:200px;" onclick="return validateAddDailyFrozenPacking(document.frmDailyFrozenPacking);" value="save &amp; Add New Quick Entry">	
																		<? }?>
																	</td>
																	<? }?>
																</tr>
																<tr>
																	<td  height="10" ></td>
																</tr>
															</table>
														</td>
													</tr>
													<?php
														} // Entry Selection
													?>
												</table>
											</td>
										</tr>
									</table>
									<!-- Form fields end   -->
								</td>
							</tr>	
							<?
							}
							# Listing Grade Starts
							?>
							<tr>
								<td height="10" align="center" ></td>
							</tr>
							<tr>
								<td>
									<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
										<tr>
											<td   bgcolor="white">
												<!-- Form fields start -->
												<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
													<tr>
														<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
														<td background="images/heading_bg.gif" class="pageName" >&nbsp;Frozen Stock Allocation</td>
														<td background="images/heading_bg.gif" class="pageName" align="right" >
														<table cellpadding="0" cellspacing="0">
															<tr> 
																<td class="listing-item">&nbsp;</td>
																<td class="listing-item" nowrap>As on</td>
																<td class="listing-item">&nbsp;</td>
																<td nowrap> 
																  <?php 
																	
																	$dateTill=date("d/m/Y");
																  ?>
																  <input type="text" id="frozenPackingTill" name="frozenPackingTill" size="10"  value="<?=$dateTill?>" style="border:none; font-size:9pt; font-weight:bold; text-align:center;" readonly />
																  </td>
																  <td class="listing-item">&nbsp;</td>
																<td class="listing-item" nowrap>Process Code</td>
																<td class="listing-item">&nbsp;</td>
																<td>
																	<select name="filterProcessCode" id="filterProcessCode" style="width:100px;">
																		<option value="">-- All --</option>
																		<?php
																		foreach ($frznStkProcessCodes as $fspc) {
																			$fsProcesscodeId = $fspc[0];
																			$fsProcesscode	 = $fspc[1];
																			$selected = ($filterProcessCode==$fsProcesscodeId)?"selected":"";
																		?>
																		<option value="<?=$fsProcesscodeId?>" <?=$selected?>><?=$fsProcesscode?></option>
																		<?php
																		}
																		?>
																	</select>
																</td>
																<td class="listing-item">&nbsp;</td>
																<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="this.form.action='FrozenStockAllocation.php?all=-1';return validateFrozenStkAllocationSearch();"></td>
																<td class="listing-item" nowrap >&nbsp;</td>
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
																	<input type="submit" value=" Allocate " name="cmdAllocate" onClick=" this.form.action='FrozenStockAllocation.php?all=0';return checkboxSel();" class="button">&nbsp;
																	<input type="submit" value=" Thawing " name="cmdThawe" onClick=" this.form.action='FrozenStockAllocation.php?all=1';return checkboxSel();" class="button">&nbsp;
																	<!--<input type="submit" value=" Thawing " name="cmdThawe" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria'); this.form.action='FrozenStockAllocation.php?all=1';"  <?=$allocateDisabled?> class="button">-->
																	<input type="submit" value=" Convert " name="cmdConvert" class="button" onClick=" this.form.action='FrozenStockAllocation.php?all=4';return checkboxSel();" >
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
													<td colspan="2" style="padding-left:5px; padding-right:5px;" >
														<table cellpadding="1"  width="85%" cellspacing="1" border="0" align="center" bgcolor="#999999">
														<?
														$dateFrom=dateformat($fromDate);
														if (sizeof($dailyFrozenPackingRecs)>0) {
															$i	=	0;
														?>
															<? if($maxpage>1){?>
															<tr bgcolor="#FFFFFF">
																<td colspan="15" style="padding-right:10px">
																	<div align="right">
																	<?php 				 			  
																	$nav  = '';
																	for($page=1; $page<=$maxpage; $page++) {
																		if ($page==$pageNo) {
																			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																		} else {
																				$nav.= " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\" class=\"link1\">$page</a> ";
																		}
																	}
																	if ($pageNo > 1) {
																		$page  = $pageNo - 1;
																		$prev  = " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\"  class=\"link1\"><<</a> ";
																	} else {
																		$prev  = '&nbsp;'; // we're on page one, don't print previous link
																	$first = '&nbsp;'; // nor the first page link
																	}
																	if ($pageNo < $maxpage)	{
																		$page = $pageNo + 1;
																		$next = " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\"  class=\"link1\">>></a> ";
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
																<td width="20">
																<input type=hidden name="hidstDate" id="hidstDate" value=<?=$dateFrom; ?> />
																</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">RM LOT ID</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Process Code</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Freezing Stage</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Code</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">MC Pkg</td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">No.of<br> MCs</td>	
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Frozen Qty</td>	
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Allocated Qty</td>	
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Thawed Qty</td>	
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Repacked Qty </td>	
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Reglazed Qty </td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">Balance/Free qty </td>
																<td class="listing-head" style="padding-left:5px;padding-right:5px;">View</td>
															</tr>
															<?php
															$totalPkdQty = 0;
															$totalFrozenQty = 0;
															$totalActualQty = 0;
															$totNumMCs = 0;
															$c=0;
															$sumflag=0;
															$disMsg="";
															$reglazedPrdc="";
															//$choice3=0;
															//$t="";
															$repackedfrom="";
															$block="false";
															//echo sizeof($dailyFrozenPackingRecs);
															//printr($dailyFrozenPackingRecs);
															foreach ($dailyFrozenPackingRecs as $dfpr) {
																$c++;
																$balMC = $dfpr[20];
																$balLS = $dfpr[23];
																$invConfirm=$dfpr[24];
																
																//if ($balMC<=0 && $balLS<=0) continue; // skip the row
																if ($balMC<0) continue;

																//$i++;
																$dailyFrozenPackingMainId	= $dfpr[0];
																
																$fishId				= $dfpr[2];
																$selProcessCodeId	= $dfpr[3];
																$selProcessCode		= $dfpr[6];	
																$selFreezingStageId	= $dfpr[4];
																$selFreezingStage	= $dfpr[7];
																$selFrozenCodeId	= $dfpr[5];
																$selFrozenCode		= $dfpr[8];
																//echo $selFrozenCode;
																$block="false";
																if (strpos($selFrozenCode,'Block') !== false) {
																// echo 'true';
																 $block="true";
																}
																	
																//echo $dfpr[21];

																$filledWt=$dfpr[25];
																//echo $filledWt;
																$reId=$dfpr[26];
																$sumflag=$dfpr[27];
																 $repackedfrom=$dfpr[28];
																// echo $c.'--'.$repackedfrom.'<br/>';
																if($dfpr[32]!='0')
																{
																	$rmLotIDNm			= $dfpr[32];
																}
																else
																{
																	$rmLotIDNm			='';
																}
																$rmLotID			= $dfpr[33];
																$processType=$dfpr[37];
																$companyId=$dfpr[36];
																$unitId=$dfpr[35];
																if ($repackedfrom=="")$repackedfrom="-1";
																
																//echo $repackedfrom;
																//echo $repackedfrom;
																$repackedfromEx=explode(",",$repackedfrom);
																//print_r($repackedfromEx);
																$repackedfromExMC=explode("-",$repackedfromEx[3]);
																//echo $repackedfromExMC[1];
																$repackedfromExMCVal=$repackedfromExMC[1];
																$repackedfromExMCf=explode("-",$repackedfromEx[2]);
																//echo $repackedfromExMC[1];
																$repackedfromExMCValf=$repackedfromExMCf[1];
																list($repackedPrdf,$repackedPrdfc)=$frozenpackingObj->find($repackedfromExMCValf);
																//echo "^^^^^^^^^^^^^$repackedfromExMCVal";
																$repQtyno=$dfpr[29];
																$reglzQtyno=$dfpr[30];
																$reglazedfrom=$dfpr[31];
																// echo $c.'--'.$reglazedfrom.'<br/>';
																if ($reglazedfrom=="")$reglazedfrom="-1";
																$reglazedfromEx=explode(",",$reglazedfrom);
																//print_r($repackedfromEx);

																$reglazedfromExMC=explode("-",$reglazedfromEx[3]);
																//echo $repackedfromExMC[1];
																$reglazedfromExMCVal=$reglazedfromExMC[1];
																$reglazedfromExFCID=explode("-",$reglazedfromEx[2]);
																//echo $repackedfromExMC[1];
																$reglazedfromExFCIDVal=$reglazedfromExFCID[1];
																
																list($reglazedPrd,$reglazedPrdc)=$frozenpackingObj->find($reglazedfromExFCIDVal);
																//echo "---$repackedfrom";
																	list($repackedPrd)=$mcpackingObj->findMCPackingValue($repackedfromExMCVal);
																	list($reMCPrd)=$mcpackingObj->findMCPackingValue($reglazedfromExMCVal);
																	//echo "<hr>";
																	//echo $repackedPrd;
																	//echo "<hr>";
																//echo $reId;
																//echo $filledWt;
																$glazeId=$frozenpackingObj->frznPkgglaze($dfpr[5]);
																//echo $glazeId;
																$glaze=$glazeObj->findGlazePercentage($glazeId);
																$Wt=$filledWt-($filledWt*$glaze/100);
																//echo $Wt;
																$netWt=$dfpr[15]*$Wt;
																//echo "<hr>";
																//echo $netWt;
																
																# MC Pkg Recs hide on 02 APRIL 13
																//$mcPkgRecs	= $frozenStockAllocationObj->getMCPkgRecs($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId);
																$mcPkgRecs	= array();

																# Frozen Lot Ids hide shobu on 01 APR 13
																//$frznLotIds	= $frozenStockAllocationObj->getFrznLotIds($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId);
																$frznLotIds	= array();
																//echo "hii";			
																$reportConfirmed = 	$dfpr[9];
																$poAllocated = $dfpr[10];
																$frznStkMCPkgId	 = $dfpr[11];
																$frznStkMCPkgCode	 = $dfpr[12];
																$frznStkMCNumPack	 = $dfpr[13];
																$selectDate		=	Date('Y-m-d');
																//list($thawingTotal)=$frozenStockAllocationObj->getThaQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$selectDate);
																//list($thawingTotal)=$frozenStockAllocationObj->getThaQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$dateFrom);
																if ($maximumdt==""){
																		//$currYear=Date("Y");
																		//$currFinanYear="01/04/$currYear";
																		$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
																		//$dateFrom=mysqlDateFormat(date("d/m/$currFinanYear"));
																		//$fromDate=mysqlDateFormat(date("d/m/Y"));
																		$dateFrom=mysqlDateFormat(date("$defaultDFPDate"));
																}
																else{
																		$dateFrom=mysqlDateFormat($maximumdt);
																}
																if($rmLotID=='0')
																{
																	list($thawingTotal)=$frozenStockAllocationObj->getThaQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$dateFrom);
																	//echo $thawingTotal;
																}
																else
																{
																	list($thawingTotal)=$frozenStockAllocationObj->getThaQtyRMLotId($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$frznStkMCPkgId,$dateFrom,$rmLotID);
																}
																$disabled = "";
																$allocateDisabled = "";
																if ($reportConfirmed=='Y' && $reEdit==false) {
																	$disabled = "disabled";
																}
																

																/*
																if ($poAllocated!="") {
																	$disabled = "disabled";
																	$allocateDisabled = "disabled";
																}
																*/

																#Find Number of packing Details
																/*
																list($pkdQty, $numMCs, $frozenQty, $actualQty) = $frozenStockAllocationObj->getPkdQty($fromDate, $tillDate, $selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $frznStkMCPkgId);
																*/
																

																$pkdQty = number_format($dfpr[14],2,'.','');
																$numMCs = $dfpr[15];
																$frozenQty = number_format($dfpr[16],2,'.','');
																$actualQty = number_format($dfpr[17],2,'.','');

																
																$allocatedMC = $dfpr[19];

																if ($allocatedMC!=0)
																//if ($allocatedMC==0)
																{
																	$avgFlag=0;
																	//echo "Avalable qty is 0";
																}
																else
																{
																	$avgFlag=1;
																	//echo "Avalable qty is 1";
																}

																# Edit criteria (ProcessCode id, freezing stage id, frozen code id, Display message, mc pkg id, MC num pack)
																$selEditCriteria = "$selProcessCodeId, $selFreezingStageId, $selFrozenCodeId, $selProcessCode - $selFreezingStage - $selFrozenCode - $frznStkMCPkgCode, $frznStkMCPkgId, $frznStkMCPkgCode, $frznStkMCNumPack,$fishId,$filledWt,$repackedfrom";			
																$netMc=$numMCs-$allocatedMC;
																$totBalthaw=$netMc-$thawingTotal;

																###if $totBalthaw=0 that means, the  numcs in frozen packing is equal to the number of mc in thawing ie, the  mc available in frozen packing is completed converted to thawing so the data does not exist in daily frozen packing for display.

																$choice1=0;
																$choice2=0;
																$choice3=0;
																$t=0;
																
															//	if ($totBalthaw!=0)
																if ($totBalthaw!=0)
																{
																
																$choice1=1;
																}
																if	($invConfirm==0)
																{
																	
																$choice2=1;
																}		
																
																if ($totBalthaw==0 && $avgFlag==1)			
																{
																$choice3=1;				
															}
																
																
															if (($choice1==1 || $choice2==1) && $choice3==0) 
															{
																	

																	$i++;
																	
																$totalPkdQty += $pkdQty;
																$totalFrozenQty += $frozenQty;
																$totalActualQty += $actualQty;
																$totNumMCs += $numMCs;
																$totAlloc+=$allocatedMC;
																$totBal+=$netMc;
																$netthawingTotal+=$thawingTotal;
																
																
																$nettotBalthaw+=$totBalthaw;
																
																
																$disMsg="";
		
																if (trim($repackedfrom)!='-1')$disMsg="Repacked From $repackedPrd,$repackedPrdfc";
																 if ($totBalthaw==0)$disMsg.= "Waiting for PO Confirmation";
																 if (trim($reglazedPrdc)!='')$disMsg.="Reglazed From $reMCPrd,$reglazedPrdc";
																$disVal="";
																if (trim($repackedfrom)!='-1' || $totBalthaw==0 || trim($reglazedPrdc)!='')$disVal="<span onMouseover=\"ShowTip('".$disMsg."');\" onMouseout=\"UnTip();\" style=\"color:red;\" >*</span>";
																?>
																<tr <?=$listRowMouseOverStyle?>>
																	<td width="20">
																		<?//=$dailyFrozenPackingMainId;?><input type="checkbox" name="delGId_<?=$i;?>" id="delGId_<?=$i;?>" value="<?=$selEditCriteria;?>" class="chkBox fsaChkbx" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId'); 
																		assignValue(this.form,'<?=$rmLotID;?>','rmLotID'); 
																		assignValue(this.form,<?=$i;?>,'hidrow');assignValue(this.form,'<?=$block;?>','hidblock');assignValue(this.form,'<?=$filledWt;?>','filledWt'); assignValue(this.form,'<?=$selEditCriteria?>','editCriteria');
																		assignValue(this.form,'<?=$companyId?>','companyId');
																		assignValue(this.form,'<?=$unitId?>','unitId'); assignValue(this.form,'<?=$processType?>','processType');"
																		<?php if ($hidival==$i){?> checked="true" <?php }?> 
																		<?php if ($totBalthaw==0){?> disabled="true" <?php }?>>
																	</td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$rmLotIDNm?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selProcessCode?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFreezingStage?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"><?=$selFrozenCode?></td>
																	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?//=$forPkg?></td>-->
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" nowrap><?=$frznStkMCPkgCode?>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($numMCs!=0)?$numMCs:"";?></td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($frozenQty!=0)?$frozenQty:"";?></td>
																	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($pkdQty!=0)?$pkdQty:"";?></td>-->
																	<!--<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?//=($actualQty!=0)?$actualQty:"";?></td>-->	
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;" align="right"><?=($allocatedMC!=0)?$allocatedMC:"";?>
																	
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right"><?=($thawingTotal!=0)?$thawingTotal:"";?>&nbsp;</td>	
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;">&nbsp;<?//=$t;//=$sumflag;?><?//=$reId;?>
																	<?=($repQtyno!=0)?$repQtyno:"";?></td>	
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;">&nbsp;<?//=$t;//=$sumflag;?><?//=$reId;?>
																	<?=($reglzQtyno!=0)?$reglzQtyno:"";?></td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;"  align="right">
																	<?=$totBalthaw;?>
																	</td>
																	<td class="listing-item" nowrap style="padding-left:5px;padding-right:5px;"  ><?php //echo $choice1;echo $choice2; echo $choice3; ?><?=$disVal;?></td>	
																	
																</tr>	
																		<?php
																		//}	
																		}
																	} // Main loopEnds here 
																?>
																<tr bgcolor="White">
																	<TD  class="listing-head" align="right" style="padding-left:5px;padding-right:5px;"></TD>
																	<TD colspan="5" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;">Total:</TD>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
																		<strong><?=$totNumMCs?></strong>
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
																		<strong><?=number_format($totalFrozenQty,2,'.','');?></strong>
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">&nbsp;
																		<strong><?=$totAlloc;//=number_format($totalPkdQty,2,'.','');?></strong>
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
																		<strong><?=$netthawingTotal;//=number_format($totalPkdQty,2,'.','');?></strong>
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">	
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
																	</td>
																	<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="right">
																		<strong><?//=$totBal;//=number_format($totalActualQty,2,'.','');
																		?><?=$nettotBalthaw;?></strong>
																	</td>
																	<TD colspan="5" class="listing-head" align="right" style="padding-left:5px;padding-right:5px;"></TD>
																</tr>
																<input type="hidden" name="hidrow" value="" id="hidrow"/>
																<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" readonly="true">
																<input type="hidden" name="editId" value="<?=$editId?>" readonly="true">
																<input type="hidden" name="editFrozenPackingEntryId" value="<?=$frozenPackingEntryId;?>" readonly="true">
																<input type="hidden" name="editSelectionChange" value="0" readonly="true">
																<input type="hidden" name="editMode" value="<?=$editMode?>" readonly="true">
																<input type="hidden" name="allocateId" value="<?=$allocateId?>" readonly="true">
																<input type="hidden" name="rmLotID" id="rmLotID" value="<?=$rmLotID?>" readonly>
																<input type="hidden" name="editCriteria" id="editCriteria" value="<?=$editCriteria?>" readonly="true">
																<input type="hidden" name="filledWt" id="filledWt" value="<?=$filledWt1?>" readonly/>
																<input type="hidden" name="hidpckst" id="hidpckst" value="" />
																<input type="hidden" name="hidglzst" id="hidglz" value="" />
																<input type="hidden" name="hidblock" id="hidblock" value="<?=$block?>" />
																<input type="hidden" name="companyId" id="companyId" value="<?=$companyId?>" />
																<input type="hidden" name="unitId" id="unitId" value="<?=$unitId?>" />
																<input type="hidden" name="processType" id="processType" value="<?=$processType?>" />
																
																<?
																$dateFrom=dateformat($dateFrom);
																		if($maxpage>1){?>
																<tr bgcolor="#FFFFFF">
																	<td colspan="15" style="padding-right:10px">
																		<div align="right">
																		<?php 				 			  
																		$nav  = '';
																		for($page=1; $page<=$maxpage; $page++) {
																			if ($page==$pageNo) {
																				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
																			} else {
																				$nav.= " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\" class=\"link1\">$page</a> ";
																			}
																		}
																		if ($pageNo > 1) {
																			$page  = $pageNo - 1;
																			$prev  = " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\"  class=\"link1\"><<</a> ";
																		} else {
																			$prev  = '&nbsp;'; // we're on page one, don't print previous link
																			$first = '&nbsp;'; // nor the first page link
																		}

																		if ($pageNo < $maxpage) {
																			$page = $pageNo + 1;
																			$next = " <a href=\"FrozenStockAllocation.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill&filterProcessCode=$filterProcessCode\"  class=\"link1\">>></a> ";
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
																} 
																else 	
																{
																?>
																<tr bgcolor="white">
																	<td colspan="14"  class="err1" height="11" align="center"><?=$msgNoRecords;?></td>
																</tr>	
																<?
																}
																?>
																<input type="hidden" name="allocateMode" value="<?//=$allocateMode?>">
															</table>
															<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
															<input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>">
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
																			<input type="submit" value=" Allocate " name="cmdAllocate" onClick=" this.form.action='FrozenStockAllocation.php?all=0';return checkboxSel()" <?=$allocateDisabled?>" class="button">&nbsp;
																			<input type="submit" value=" Thawing " name="cmdThawe" onClick="this.form.action='FrozenStockAllocation.php?all=1';return checkboxSel()"  <?=$allocateDisabled?> class="button">
																			<!--<input type="submit" value=" Thawing " name="cmdThawe" onClick="assignValue(this.form,<?=$dailyFrozenPackingMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); assignValue(this.form,'<?=$dailyFrozenPackingEntryId?>','editFrozenPackingEntryId'); assignValue(this.form,'<?=$dailyFrozenPackingMainId;?>','allocateId');  assignValue(this.form,'<?=$selEditCriteria?>','editCriteria'); this.form.action='FrozenStockAllocation.php?all=1';"  <?=$allocateDisabled?> class="button">-->&nbsp;
																			<input type="submit" value=" Convert " name="cmdConvert" class="button" onClick=" this.form.action='FrozenStockAllocation.php?all=4';return checkboxSel();" >
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
										<input type="hidden" name="hidNumPack" id="hidNumPack" value="<?=$numPacks?>"/>
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
										<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" />
										<input type="hidden" name="hidMCPkgCode" id="hidMCPkgCode" value="<?=$stkAllocateMCPkgCode?>" />
										<input type="hidden" name="hidDelAllocationArr" id="hidDelAllocationArr" value="" />
										<input type="hidden" name="hidProcessId" id="hidProcessId" value="<?=$processId?>" readonly />
										<input type="hidden" name="hidFishId" id="hidFishId" value="<?=$fishIdth?>" readonly />
										<input type="hidden" name="hidFreezingStage" id="hidFreezingStage" value="<?=$freezingStage?>" readonly />
										<input type="hidden" name="hidFrozenCode" id="hidFrozenCode" value="<?=$frozenCode?>" readonly />
										<input type="hidden" name="hidMCPkgId" id="hidMCPkgId" value="<?=$stkAllocateMCPkgId?>" readonly />
										<input type="hidden" name="hidWt" id="hidWt" value="<?=$netWt;?>" readonly />
										<!-- QEL Selection Hidden Ends here  -->
									</td>
								</tr>	
							</table>	
							<SCRIPT LANGUAGE="JavaScript">
								fieldId = '<?=$productRecSize+2?>';
								fldId = '<?=sizeof($pkgGroupArr)?>';
								
							</script>
							<?php 
								
								if ($entrySel=="QE") {
							?>
							<SCRIPT LANGUAGE="JavaScript">
								hidRows();
							</SCRIPT>
							<?php
								}
							?>
							<?php 
								
								if ($allocateMode) {
							?>
							<SCRIPT LANGUAGE="JavaScript">
								displayAllocateSummary();
							</SCRIPT>
							<?php
								}
							?>

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
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
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

	$(document).ready(function(){
 var $unique = $('input.fsaChkbx');
$unique.click(function() {
    $unique.filter(':checked').not(this).removeAttr('checked');
});
});
	</SCRIPT>	
	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>