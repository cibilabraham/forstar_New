<?php
	require("include/include.php");
	//require_once ("components/base/installed_capacity_model.php");
	//require_once ("components/base/SetMonitoringParam_model.php");
	//require("lib/dailyfreezingchart_ajax.php");
	//$ic_m = new installed_capacity_model();	
	//$setMonitoringParam_m = new SetMonitoringParam_model();	

	ob_start();

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$disableField = "";
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selDate=".$p["selDate"];

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
		header("Location: ErrorPage.php");
		die();
	}

	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	//----------------------------------------------------------
	
	# Reset values
	if ($p["installedCapacity"]) 	$installedCapacityId = $p["installedCapacity"];
	if ($p["selectDate"]) 		$selectDate = $p["selectDate"];	

	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode 	= false;
		$p["cmdAddNew"] = "";
		$editId 	= "";
		$p["editId"] 	= "";
	}

	# Add New
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
		
		/*
		if ($p["mainId"]=="" && $dailyFreezingChartObj->checkBlankRecord()) {
			$mainId 	=	$dailyFreezingChartObj->checkBlankRecord();
		} else 	{
			if ($p["mainId"]=="") {
				$tempMainTableRecIns=$dailyFreezingChartObj->addTempDataMainTable();
				if ($tempMainTableRecIns!="") {
					$mainId	=	$databaseConnect->getLastInsertedId();				
				}
			} else $mainId 	=	$p["mainId"];
		}	
		*/	
	}

	/*
	if ($addMode) {
		$curDate = date("d/m/Y");
		$dateC	  = explode("/",$curDate);
		$closingDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-1,$dateC[2])); //Find the opening data
		list($dieselOB, $iceOB, $firstGeneratorPrevious, $secondGeneratorPrevious,  $thirdGeneratorPrevious, $firstElectricityMeterOpening, $secondElectricityMeterOpening, $thirdElectricityMeterOpening, $waterMeterOpening) = $dailyFreezingChartObj->getClosingActivityDetails($closingDate);
	}
	*/

	#Insert 
	if ($p["cmdAdd"]!="") {
		$selectDate	= mysqlDateFormat($p["selectDate"]);
		$paramRowCount = $p["hidParamRowCount"];
		$installedCapacityId = $p["installedCapacity"];
		
		if ($installedCapacityId && $paramRowCount>0) {
			# Insert main Rec
			$dailyProcessingchartRecIns = $dailyFreezingChartObj->addDailyProcessingChart($selectDate, $installedCapacityId, $userId);
			if ($dailyProcessingchartRecIns) {
				$dpcLastId = $databaseConnect->getLastInsertedId();
				if ($dpcLastId!=0) {
					for ($i=0;$i<$paramRowCount;$i++) {
						$monitoringParamId 	= $p["monitoringParamId_".$i];
						//$monitoringInterval 	= $p["monitoringInterval_".$i];
						//$numSplit	 	= $p["numSplit_".$i];

						$startTime = "";						
						$startTimeHour		= $p["startTimeHour_".$i];
						$startTimeMints		= $p["startTimeMints_".$i];
						$startTimeOption	= $p["startTimeOption_".$i];
						if ($startTimeHour!=0 && $startTimeMints!=0) $startTime = $startTimeHour."-".$startTimeMints."-".$startTimeOption;

						$stopTime = "";
						$stopTimeHour		= $p["stopTimeHour_".$i];
						$stopTimeMints		= $p["stopTimeMints_".$i];
						$stopTimeOption		= $p["stopTimeOption_".$i];
						if ($stopTimeHour!=0 && $stopTimeMints!=0) $stopTime = $stopTimeHour."-".$stopTimeMints."-".$stopTimeOption;

						# if Monitoring Interval set
						$startedAtTime = "";
						$startedAtHr	 	= $p["startedAtHr_".$i];
						$startedAtMints	 	= $p["startedAtMints_".$i];
						$startedAtOption 	= $p["startedAtOption_".$i];
						if ($startedAtHr!=0 && $startedAtMints!=0) $startedAtTime = $startedAtHr."-".$startedAtMints."-".$startedAtOption;

						$stoppedAtTime = "";
						/*
						$stoppedAtHr	 	= $p["stoppedAtHr_".$i];
						$stoppedAtMints	 	= $p["stoppedAtMints_".$i];
						$stoppedAtOption 	= $p["stoppedAtOption_".$i];
						if ($stoppedAtHr!=0 && $stoppedAtMints!=0) $stoppedAtTime = $stoppedAtHr."-".$stoppedAtMints."-".$stoppedAtOption;
						*/

						$hidTableRowCount 	= $p["hidTableRowCount_".$i];
						$stopMonitoring		= ($p["stopMonitoring_".$i])?$p["stopMonitoring_".$i]:"N";
						if ($stopMonitoring=='Y') $stoppedAtTime = $p["stoppedAtTime_".$i];
						$monitoringLastInterval = $p["stoppedAtTime_".$i];

						if ($monitoringParamId) {
							$dpcParamterRecIns = $dailyFreezingChartObj->addDailyPCMonitoringParam($dpcLastId, $monitoringParamId, $startTime, $stopTime, $startedAtTime, $stoppedAtTime, $stopMonitoring, $monitoringLastInterval);
							
							if ($dpcParamterRecIns) {
								# Get Monitor param Entry Last Id
								$dpcMonitorParamEntryLastId = $databaseConnect->getLastInsertedId();
								$startTime = "";
								$stopTime = "";
								if ($dpcMonitorParamEntryLastId!=0) {
									for ($j=0;$j<$hidTableRowCount;$j++) {
										$status = $p["status_".$i."_".$j];
										if ($status!='N') {
											$startTime = "";
											$startTimeHour		= $p["startTimeHour_".$i."_".$j];
											$startTimeMints		= $p["startTimeMints_".$i."_".$j];
											$startTimeOption	= $p["startTimeOption_".$i."_".$j];
											if ($startTimeHour!=0 && $startTimeMints!=0) $startTime = $startTimeHour."-".$startTimeMints."-".$startTimeOption;
											$startTemp		= $p["startTemp_".$i."_".$j];
					
											$stopTime = "";
											$stopTimeHour		= $p["stopTimeHour_".$i."_".$j];
											$stopTimeMints		= $p["stopTimeMints_".$i."_".$j];
											$stopTimeOption		= $p["stopTimeOption_".$i."_".$j];
											if ($stopTimeHour!=0 && $stopTimeMints!=0) $stopTime = $stopTimeHour."-".$stopTimeMints."-".$stopTimeOption;
											$stopTemp		= $p["stopTemp_".$i."_".$j];
											
											if ($startTime) {
												$dpcIntervalParamRecIns = $dailyFreezingChartObj->addIntervalParam($dpcMonitorParamEntryLastId, $startTime, $startTemp, $stopTime, $stopTemp);	
											}
										} // Status Chk Ends 
									} // Interval param loop ends here
								}
							}
						} // Monitoring Param Entry check ends here				
					} //Main Loop ends here	
				}

				$sessObj->createSession("displayMsg", $msg_succAddDailyFreezingChart);
				$sessObj->createSession("nextPage",$url_afterAddDailyFreezingChart.$selection);				
			} // Rec Ins ends here
		} else {
			$addMode		=	true;
			$err			=	$msg_failAddDailyFreezingChart;
		}

		/*
		$mainId 	=	$p["mainId"];		
		$selectTimeHour	=	$p["selectTimeHour"];
		$selectTimeMints=	$p["selectTimeMints"];
		$timeOption 	= 	$p["timeOption"];
		$selectTime	=	$p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];

		if ($mainId!="") {

			#Daily Activity Chart Main Rec Uptd
			$updateDailyFreezingChartMainRec = $dailyFreezingChartObj->updateDailyFreezingMainRec($mainId, $selectDate, $selectTime);
			
			if ($updateDailyFreezingChartMainRec) {

				if ( $p['editMode'] == "1" ) $sessObj->createSession("displayMsg", $msg_succUpdateDailyFreezingChart);
				else $sessObj->createSession("displayMsg", $msg_succAddDailyFreezingChart);
				//$sessObj->createSession("nextPage",$url_afterAddDailyFreezingChart.$selection);

				//Save and Add New in same date
				if ($p["cmdAddSameEntry"]!="") {
					$mainId 	=	$p["mainId"];
					$tempEntryTableRecIns=$dailyFreezingChartObj->addTempDataEntryTable($mainId);
					if ($tempEntryTableRecIns!="") {
						$entryId	=	$databaseConnect->getLastInsertedId();
					}
					$addMode=true;

					$freezerId	=	"";
					$p["freezerName"]	=	"";
					$startTimeHour		=	"";
					$p["startTimeHour"]	=	"";
					$startTimeMints		=	"";
					$p["startTimeMints"]	=	"";
					$startTimeOption 	= 	"";
					$p["startTimeOption"]	=	"";
					$startTemp		=	"";
					$p["startTemp"]		=	"";
					$stopTimeHour		=	"";
					$p["stopTimeHour"]	=	"";
					$stopTimeMints		=	"";
					$p["stopTimeMints"]	=	"";
					$stopTimeOption 	= 	"";
					$p["stopTimeOption"]	=	"";
					$stopTemp		=	"";
					$p["stopTemp"]		=	"";
					$coreTemp		=	"";
					$p["coreTemp"]		=	"";
					$unloadTimeHour		=	"";
					$p["unloadTimeHour"]	=	"";
					$unloadTimeMints	=	"";
					$p["unloadTimeMints"]	=	"";
					$unloadTimeOption 	= 	"";
					$p["unloadTimeOption"]	=	"";
					

				} else if($p["cmdAdd"]!="") {
					$addMode = false;
					$p["mainId"] = "";
					$mainId = "";
				}

			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyFreezingChart;
			}
			$updateDailyFreezingChartMainRec	=	false;
			$updateDailyFreezingChartEntryRec	=	false;
		} else {
			$addMode		=	true;
			$err			=	$msg_failAddDailyFreezingChart;
		}
		*/
	}




	# Edit
	
	if ($p["editId"]!="" && $p["cmdSaveChange"]=="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$freezingChartEntryId	=	$p["editDailyFreezingChartEntryId"];

		$dailyFreezingChartRec = $dailyFreezingChartObj->find($editId);
		
		$mainId 		=	$dailyFreezingChartRec[0];		
		$selectDate		=	dateFormat($dailyFreezingChartRec[1]);
		$installedCapacityId  =  $dailyFreezingChartRec[2];
		$disableField = "disabled='true'";
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$mainId 	= $p["mainId"];
		$selectDate	= mysqlDateFormat($p["selectDate"]);
		$paramRowCount 	= $p["hidParamRowCount"];
		$installedCapacityId = $p["installedCapacity"];
		
		if ($mainId && $paramRowCount>0) {

			# Insert main Rec
			$updateDailyProcessingchartRec = $dailyFreezingChartObj->updateDailyProcessingChart($mainId, $selectDate);

			if ($updateDailyProcessingchartRec) {
				$dpcLastId = $mainId;
				if ($dpcLastId!=0) {
					for ($i=0;$i<$paramRowCount;$i++) {
						# Entry Id
						$mpEntryId		= $p["mpEntryId_".$i];

						$monitoringParamId 	= $p["monitoringParamId_".$i];
						//$monitoringInterval 	= $p["monitoringInterval_".$i];
						//$numSplit	 	= $p["numSplit_".$i];

						$startTime = "";						
						$startTimeHour		= $p["startTimeHour_".$i];
						$startTimeMints		= $p["startTimeMints_".$i];
						$startTimeOption	= $p["startTimeOption_".$i];
						if ($startTimeHour!=0 && $startTimeMints!=0) $startTime = $startTimeHour."-".$startTimeMints."-".$startTimeOption;

						$stopTime = "";
						$stopTimeHour		= $p["stopTimeHour_".$i];
						$stopTimeMints		= $p["stopTimeMints_".$i];
						$stopTimeOption		= $p["stopTimeOption_".$i];
						if ($stopTimeHour!=0 && $stopTimeMints!=0) $stopTime = $stopTimeHour."-".$stopTimeMints."-".$stopTimeOption;

						# if Monitoring Interval set
						$startedAtTime = "";
						$startedAtHr	 	= $p["startedAtHr_".$i];
						$startedAtMints	 	= $p["startedAtMints_".$i];
						$startedAtOption 	= $p["startedAtOption_".$i];
						if ($startedAtHr!=0 && $startedAtMints!=0) $startedAtTime	= $startedAtHr."-".$startedAtMints."-".$startedAtOption;

						$stoppedAtTime = "";
						$stoppedAtHr	 	= $p["stoppedAtHr_".$i];
						$stoppedAtMints	 	= $p["stoppedAtMints_".$i];
						$stoppedAtOption 	= $p["stoppedAtOption_".$i];
						if ($stoppedAtHr!=0 && $stoppedAtMints!=0) $stoppedAtTime	= $stoppedAtHr."-".$stoppedAtMints."-".$stoppedAtOption;

						$hidTableRowCount 	= $p["hidTableRowCount_".$i];

						$stopMonitoring		= ($p["stopMonitoring_".$i])?$p["stopMonitoring_".$i]:"N";
						if ($stopMonitoring=='Y') $stoppedAtTime = $p["stoppedAtTime_".$i];
						$monitoringLastInterval = $p["stoppedAtTime_".$i];

						if ($monitoringParamId) {
							if ($mpEntryId) {
								# update
								$updateDPCParamterRec = $dailyFreezingChartObj->updateDailyPCMonitoringParam($mpEntryId, $startTime, $stopTime, $startedAtTime, $stoppedAtTime, $stopMonitoring, $monitoringLastInterval);
								$dpcMonitorParamEntryLastId = $mpEntryId;
							} else {
								# Insert
								$dpcParamterRecIns = $dailyFreezingChartObj->addDailyPCMonitoringParam($dpcLastId, $monitoringParamId, $startTime, $stopTime, $startedAtTime, $stoppedAtTime, $stopMonitoring, $monitoringLastInterval);
								# Get Monitor param Entry Last Id
								$dpcMonitorParamEntryLastId = $databaseConnect->getLastInsertedId();
							}	
							
							if ($dpcMonitorParamEntryLastId) {
								
								$startTime = "";
								$stopTime = "";
								if ($dpcMonitorParamEntryLastId!=0) {
									for ($j=0;$j<$hidTableRowCount;$j++) {
										$status = $p["status_".$i."_".$j];
										$mpIntervalEntryId = $p["mpIntervalEntryId_".$i."_".$j];	

										if ($status!='N') {
											$startTime = "";
											$startTimeHour		= $p["startTimeHour_".$i."_".$j];
											$startTimeMints		= $p["startTimeMints_".$i."_".$j];
											$startTimeOption	= $p["startTimeOption_".$i."_".$j];
											if ($startTimeHour!=0 && $startTimeMints!=0) $startTime = $startTimeHour."-".$startTimeMints."-".$startTimeOption;
											$startTemp		= $p["startTemp_".$i."_".$j];
						
											$stopTime = "";
											$stopTimeHour		= $p["stopTimeHour_".$i."_".$j];
											$stopTimeMints		= $p["stopTimeMints_".$i."_".$j];
											$stopTimeOption		= $p["stopTimeOption_".$i."_".$j];
											if ($stopTimeHour!=0 && $stopTimeMints!=0) $stopTime = $stopTimeHour."-".$stopTimeMints."-".$stopTimeOption;
											$stopTemp		= $p["stopTemp_".$i."_".$j];
											
											if ($mpIntervalEntryId && $startTime ) {
												# update			
												$UptdDPCIntervalParamRec = $dailyFreezingChartObj->updateIntervalParam($mpIntervalEntryId, $startTime, $startTemp, $stopTime, $stopTemp);		
											} else if ($startTime) {
												# insert
												$dpcIntervalParamRecIns = $dailyFreezingChartObj->addIntervalParam($dpcMonitorParamEntryLastId, $startTime, $startTemp, $stopTime, $stopTemp);
											}
										} // Status Chk Ends 

										if ($status=='N' && $mpIntervalEntryId!="") {
											$delIntervalParamRec = $dailyFreezingChartObj->delIntervalParamRec($mpIntervalEntryId);
										}
									} // Interval param loop ends here
								}
							}
						} // Monitoring Param Entry check ends here				
					} //Main Loop ends here	
				}

				$editMode = false;
				$editId = "";
				$p["editId"] = "";
				$sessObj->createSession("displayMsg",$msg_succUpdateDailyFreezingChart);
				$sessObj->createSession("nextPage",$url_afterUpdateDailyFreezingChart.$selection);				
			} // Rec Ins ends here
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyFreezingChart;
		}

		/*
		$selectTimeHour		=	$p["selectTimeHour"];
		$selectTimeMints	=	$p["selectTimeMints"];
		$timeOption 		= 	$p["timeOption"];

		$selectTime		=	$p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		

		if ($mainId) {

			#Daily Freezing Chart Main Rec Uptd
			$updateDailyFreezingChartMainRec = $dailyFreezingChartObj->updateDailyFreezingMainRec($mainId, $selectDate, $selectTime);
		}
	
		if ($updateDailyFreezingChartMainRec) {
			$editMode = false;
			$editId = "";
			$p["editId"] = "";
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyFreezingChart);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyFreezingChart.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyFreezingChart;
		}
		$updateDailyFreezingChartMainRec = false;
		*/
	}
	

	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$dpChartMainId	=	$p["delId_".$i];
			$dpChartEntryId	=	$p["dailyActivityChartEntryId_".$i];

			if ($dpChartMainId!="") {

				# Delete Interval entry table 
				$deleteDPCIntervalEntryRecDel = $dailyFreezingChartObj->deleteDFCIntervalEntryRecs($dpChartEntryId);

				//Delete Entry Table Record
				$dailyFreezingChartEntryRecDel = $dailyFreezingChartObj->deleteDailyFreezingChartEntryRec($dpChartEntryId);

				#Check Record Exists
				$recExist = $dailyFreezingChartObj->checkRecordsExist($dpChartMainId);
				//Delete Main Table
				if (!$recExist) {
					//echo "Here";
					$dailyFreezingChartRecDel = $dailyFreezingChartObj->deleteDailyFreezingChartRec($dpChartMainId);
				}
			}

		}
		if ($dailyFreezingChartRecDel || $dailyFreezingChartEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyFreezingChart);
			$sessObj->createSession("nextPage",$url_afterDelDailyFreezingChart.$selection);
		} else {
			$errDel	=	$msg_failDelDailyFreezingChart;
		}
		$dailyFreezingChartRecDel	=	false;
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit;
	## ----------------- Pagination Settings I End ------------

	#List All Record
	if ($g["selDate"]!="") $selDate = $g["selDate"];
	else if ($p["selDate"]=="") $selDate = date("d/m/Y");
	else $selDate = $p["selDate"];
		
	if ($selDate!="" || $p["cmdSearch"]!="") {

		$searchDate	=	mysqlDateFormat($selDate);

		//$dailyFreezingChartRecords	= $dailyFreezingChartObj->fetchPagingActivityChartRecords($searchDate, $offset, $limit);
		$dailyFreezingChartRecords	= $dailyFreezingChartObj->fetchAllPagingRecords($searchDate, $offset, $limit);
		$dailyFreezingChartRecordSize	= sizeof($dailyFreezingChartRecords);
	}

	## -------------- Pagination Settings II -------------------
		$numrows	=  	sizeof($dailyFreezingChartObj->fetchAllRecords($searchDate));
		$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
		
	if ($addMode || $editMode) {
		#List all Freezer Records
		//$freezerRecords = $freezercapacityObj->fetchAllRecords();
		
		# List all installed capacity recs
		//$installedCapacityRecs = $ic_m->findAll(array("order"=>"name asc"));

		# Get Installed  capacity monitoring recs
		if ($installedCapacityId) {
			//$monitorParamRecs = $setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$installedCapacityId."'", "order"=>"id asc"));
			# Monitoring param recs	
			$monitorParamRecs = $setMonitoringParam_m->filterMontoringParams($installedCapacityId);
		}

		//printr($monitorParamRecs);
	}

	# get Records if same machine has value and stop == N
	$stopMParamExist = false;
	$prevSelDate = "";	
	if ($addMode) {
		list($stopMParamExist, $prevSelDate) = $dailyFreezingChartObj->chkStopMParamExist(mysqlDateFormat($selectDate), $installedCapacityId);
	}

	if ($editMode)	$heading = $label_editDailyFreezingChart;
	else		$heading = $label_addDailyFreezingChart;

	
	$ON_LOAD_PRINT_JS = "libjs/dailyfreezingchart.js";

	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDailyFreezingChart" action="DailyFreezingChart.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr><TD height="5"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
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
												<td height="10" ></td>
											</tr>
	<tr>
		<? if($editMode){?>
		<td align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFreezingChart.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyFreezingChart(document.frmDailyFreezingChart);">							
		</td>
		<?} else{?>
		<td align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFreezingChart.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value="Save & Exit" onClick="return validateAddDailyFreezingChart(document.frmDailyFreezingChart);">							
			<input type="hidden" name="cmdAddNew" value="1">
		</td>
		<?}?>
	</tr>
	<input type="hidden" name="hidDailyFreezingChartId" value="<?=$editDailyFreezingChartId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
		<tr>
		<td colspan="2">
		<table width="100%">
			<tr>
				<TD colspan="2">
				<table>
					<TR>
						<TD class="fieldName" nowrap>Entry Date:</TD>
						<td>
							<?php
							if ($addMode && $p["selectDate"]!="") $selectDate = $p["selectDate"];
							if ($selectDate=="") $selectDate = date("d/m/Y");
							?>
							<input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selectDate?>" autocomplete="off" />
						</td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap>Machinery:</TD>
						<td nowrap="true">
							<select name="installedCapacity" id="installedCapacity" onchange="this.form.submit();" <?=$disableField?>>
								<option value="">--Select--</option>
								<?php
								foreach ($installedCapacityRecs as $icr) {
									$selected = ($icr->id==$installedCapacityId)?"selected":"";
								?>
								<option value="<?=$icr->id;?>" <?=$selected?>><?=$icr->name;?></option>
								<?php
								}
								?>
							</select>							
						</td>
					</TR>
				</table>
				</TD>
			</tr>
	<?php
	if ($stopMParamExist) {
	?>
	<tr>
		<TD colspan="2" class="listing-item" style="color:Maroon">Continuation from <?=dateFormat($prevSelDate)?></TD>
	</tr>
	<?
	}
	?>
<!-- 	Ajax	 -->
<tr>
	<TD colspan="2" id="monitoringParamRow">
	<table>
	<TR>
		<?php
		$j=0;
		foreach ($monitorParamRecs as $mpr) {
			$icMonitoringParamId = $mpr->id;
		        $headName = $mpr->headname;
			$start    = $mpr->start;
		        $stop	  = $mpr->stop;
			$monitoringInterval = $mpr->monitorinterval;
		        $paramterName = $mpr->parametername;
			$stkUnitName = $mpr->stkunit;
			$seqMParamId = $mpr->seq_mparam_id;

			# Edit Section
			$mpEntryId = "";
			$stopMonitFlagChk = "";
			if ($mainId && $icMonitoringParamId) {
				$mpRec = $dailyFreezingChartObj->getMonitoringValue($mainId, $icMonitoringParamId);
				$mpEntryId  = $mpRec[0];
				$mpStartTime = $mpRec[1];
				$mpStopTime  = $mpRec[2];
				$mpStartedTime = $mpRec[3];
				//$mpStoppedTime = $mpRec[4];
				$stopMonitFlag = $mpRec[5];
				if ($stopMonitFlag=='Y') $stopMonitFlagChk = "checked"; 

				list($startTimeHour, $startTimeMints, $startTimeOption) = explode("-", $mpStartTime);
				list($stopTimeHour, $stopTimeMints, $stopTimeOption) = explode("-", $mpStopTime);
				list($startedAtHr, $startedAtMints, $startedAtOption) = explode("-", $mpStartedTime);
				//list($stoppedAtHr, $stoppedAtMints, $stoppedAtOption) = explode("-", $mpStoppedTime);
				$stoppedAtTime = $mpRec[4];
			}

			# Add Mode
			$readOnly = "";
			if ($addMode && $stopMParamExist) {
				list($prevSelDate, $mpStartTime, $mpStartedTime) = $dailyFreezingChartObj->getPrevDPCRecs(mysqlDateFormat($selectDate), $installedCapacityId, $icMonitoringParamId);
				list($startTimeHour, $startTimeMints, $startTimeOption) = explode("-", $mpStartTime);
				list($startedAtHr, $startedAtMints, $startedAtOption) = explode("-", $mpStartedTime);
				if (($j+1)!=sizeof($monitorParamRecs)) $readOnly = "readonly='true'"; 
			}

			$paramEntry = false;
			$mpIntervalRecs = array();
			$mpIntervalRecSize = "";	
			if ($start=='Y' && $stop=="Y" && $monitoringInterval!=0) {
				$paramEntry = true;
				if ($mainId && $icMonitoringParamId && $mpEntryId) {
					$mpIntervalRecs = $dailyFreezingChartObj->getMonitoringIntervalValue($mpEntryId);
					$mpIntervalRecSize = sizeof($mpIntervalRecs);
				}
			}
			//echo "<br>$start,$stop,$monitoringInterval";
		?>	
	<TD valign="top">
		<input type="hidden" name="monitoringParamId_<?=$j?>" id="monitoringParamId_<?=$j?>" value="<?=$icMonitoringParamId?>" readonly="true" />
		<input type="hidden" name="paramEntryExist_<?=$j?>" id="paramEntryExist_<?=$j?>" value="<?=$paramEntry?>" readonly="true" />
		<input type="hidden" name="stop_<?=$j?>" id="stop_<?=$j?>" value="<?=$stop?>" readonly="true" />
		<input type="hidden" name="mpEntryId_<?=$j?>" id="mpEntryId_<?=$j?>" value="<?=$mpEntryId?>" readonly="true" />
		<input type="hidden" name="seqMParamId_<?=$j?>" id="seqMParamId_<?=$j?>" value="<?=$seqMParamId?>" readonly="true" />
		<table>
			<TR>
				<TD>
				<fieldset>
				<legend class="listing-item"><?=$headName?></legend>
				<table>
				<?php
					if ($paramEntry) {
					// If Monitoring Interval
				?>
				<tr>
				<TD>
	<input type="hidden" name="monitoringInterval_<?=$j?>" id="monitoringInterval_<?=$j?>" value="<?=number_format($monitoringInterval,2,'.','');?>" readonly="true" />
	<input type="hidden" name="numSplit_<?=$j?>" id="numSplit_<?=$j?>" value="0" readonly="true" />
	<input type="hidden" name="stoppedAtTime_<?=$j?>" id="stoppedAtTime_<?=$j?>" value="<?=$stoppedAtTime?>" readonly="true" />
					<table cellpadding="1" cellspacing="1" border="0" align="center" bgcolor="#999999" width="100%">
						<TR bgcolor="#f2f2f2" align="center">
							<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Started At</TD>		
							<!--<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Stopped At</TD>-->
							<!--<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Stop</TD>-->
							<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Monitoring<br> Interval (HR)</TD>
						</tr>
						<TR bgcolor="WHITE">
						<TD style="padding-left:5px; padding-right:5px;" nowrap>
							<?php
							if ($addMode && $p["startedAtHr_".$j]!="") $startedAtHr = $p["startedAtHr_".$j];
							if ($startedAtHr=="") $startedAtHr = date("g");
							?>
							<input type="text" id="startedAtHr_<?=$j?>" name="startedAtHr_<?=$j?>" size="1" value="<?=$startedAtHr;?>"  style="text-align:center;" maxlength="2" onkeyup="addTime('<?=$j?>');chkStartTime();" autocomplete="off" <?=$readOnly?>>:
							<?php
							if ($addMode && $p["startedAtMints_".$j]!="") $startedAtMints = $p["startedAtMints_".$j];
							if ($startedAtMints=="") $startedAtMints = date("i");
							?>
							<input type="text" id="startedAtMints_<?=$j?>" name="startedAtMints_<?=$j?>" size="1" value="<?=$startedAtMints;?>" onkeyup="addTime('<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off" <?=$readOnly?>>
							<?php
							if ($addMode && $p["startedAtOption_".$j]!="") $startedAtOption = $p["startedAtOption_".$j];
							if ($startedAtOption=="") $startedAtOption = date("A");							
							?>
							<select name="startedAtOption_<?=$j?>" id="startedAtOption_<?=$j?>" onchange="addTime('<?=$j?>');">
								<option value="AM" <?=($startedAtOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($startedAtOption=='PM')?"selected":"";?>>PM</option>
							</select>	
						</TD>						
						<!--<TD style="padding-left:5px; padding-right:5px;" nowrap>-->
							<!--<input type="checkbox" name="stopMonitoring_<?=$j?>" id="stopMonitoring_<?=$j?>" value="Y" class="chkBox" />-->
							<?php
							if ($addMode && $p["stoppedAtHr_".$j]!="") $stoppedAtHr = $p["stoppedAtHr_".$j];
							if ($stoppedAtHr=="") $stoppedAtHr = date("g");
							?>
							<!--<input type="text" id="stoppedAtHr_<?=$j?>" name="stoppedAtHr_<?=$j?>" size="1" value="<?=$stoppedAtHr;?>" onkeyup="allocMoniIntrval('<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off">:-->
							<?
							if ($addMode && $p["stoppedAtMints_".$j]!="") $stoppedAtMints = $p["stoppedAtMints_".$j];
							if ($stoppedAtMints=="") $stoppedAtMints = date("i");
							?>
							<!--<input type="text" id="stoppedAtMints_<?=$j?>" name="stoppedAtMints_<?=$j?>" size="1" value="<?=$stoppedAtMints;?>" onkeyup="allocMoniIntrval('<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off">-->
							<?php
								if ($addMode && $p["stoppedAtOption_".$j]!="") $stoppedAtOption = $p["stoppedAtOption_".$j];
								if ($stoppedAtOption=="") $stoppedAtOption = date("A");
							?>
							<!--<select name="stoppedAtOption_<?=$j?>" id="stoppedAtOption_<?=$j?>" onchange="allocMoniIntrval('<?=$j?>');">
								<option value="AM" <?=($stoppedAtOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($stoppedAtOption=='PM')?"selected":"";?>>PM</option>
							</select>-->
						<!--</TD>-->
						<td nowrap class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$monitoringInterval?></td>						
					</TR>
					</table>
				</TD>
				</tr>
				<?php
					}
					// Starting and Stop Main value setting ends here
				?>
				<TR><TD>
				<table>
				<TR>
				<TD align="center">
				<table cellpadding="1" cellspacing="1" border="0" align="center" bgcolor="#999999" width="100%" id="tblParamMonitor_<?=$j?>">
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;"><!--Start-->Time</TD>
						<?php
						if ($paramEntry) {
						?>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;"><?=$paramterName?></TD>
						<?php
						}
						?>
						<?php
						if ($stop=='Y' && !$paramEntry) {
						?>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Time</TD>
						<?php
						}
						?>
						<?php
						if ($paramEntry && $monitoringInterval==0) {
							
						?>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;"><?=$paramterName?></TD>
						<?php
						}
						?>
						<?php
						if ($paramEntry) {
						?>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Stop</TD>
						<?php
						}
						?>
						<?php
						if ($paramEntry) {
						?>
						<TD nowrap>&nbsp;</TD>
						<?php
						}
						?>
					</TR>
					<?php
						if (!$paramEntry) {
					?>
					<TR bgcolor="WHITE">
						<TD style="padding-left:5px; padding-right:5px;" nowrap>
							<?php
							if ($addMode && $p["startTimeHour_".$j]!="") $startTimeHour = $p["startTimeHour_".$j];
							if ($startTimeHour=="") $startTimeHour = date("g");
							?>
							<input type="text" id="startTimeHour_<?=$j?>" name="startTimeHour_<?=$j?>" size="1" value="<?=$startTimeHour;?>" onkeyup="dfcTChk('startTimeHour_<?=$j?>', 'startTimeMints_<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off" <?=$readOnly?>>:
							<?php
							if ($addMode && $p["startTimeMints_".$j]!="") $startTimeMints = $p["startTimeMints_".$j];
							if ($startTimeMints=="") $startTimeMints = date("i");
							?>
							<input type="text" id="startTimeMints_<?=$j?>" name="startTimeMints_<?=$j?>" size="1" value="<?=$startTimeMints;?>" onkeyup="dfcTChk('startTimeHour_<?=$j?>', 'startTimeMints_<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off" <?=$readOnly?>>
							<?php
							if ($addMode && $p["startTimeOption_".$j]!="") $startTimeOption = $p["startTimeOption_".$j];
							if ($startTimeOption=="") $startTimeOption = date("A");
							?>
							<select name="startTimeOption_<?=$j?>" id="startTimeOption_<?=$j?>">
								<option value="AM" <?=($startTimeOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($startTimeOption=='PM')?"selected":"";?>>PM</option>
							</select>	
						</TD>
						<?php
						if ($paramEntry) {
						?>
						<TD style="padding-left:5px; padding-right:5px;" nowrap>
							<INPUT type="text" size="3" name="startTemp_<?=$j?>"  value="<?=$startTemp?>" autocomplete="off">
						</TD>
						<?php
						}
						?>
						<?php
						if ($stop=='Y') {
						?>
						<TD style="padding-left:5px; padding-right:5px;" nowrap>
							<?php
							if ($addMode && $p["stopTimeHour_".$j]!="") $stopTimeHour = $p["stopTimeHour_".$j];
							if ($stopTimeHour=="") $stopTimeHour = date("g");
							?>
							<input type="text" id="stopTimeHour_<?=$j?>" name="stopTimeHour_<?=$j?>" size="1" value="<?=$stopTimeHour;?>" onkeyup="dfcTChk('stopTimeHour_<?=$j?>', 'stopTimeMints_<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off">:
							<?
							if ($addMode && $p["stopTimeMints_".$j]!="") $stopTimeMints = $p["stopTimeMints_".$j];
							if ($stopTimeMints=="") $stopTimeMints = date("i");
							?>
							<input type="text" id="stopTimeMints_<?=$j?>" name="stopTimeMints_<?=$j?>" size="1" value="<?=$stopTimeMints;?>" onkeyup="dfcTChk('stopTimeHour_<?=$j?>', 'stopTimeMints_<?=$j?>');" style="text-align:center;" maxlength="2" autocomplete="off">
							<?php
								if ($addMode && $p["stopTimeOption_".$j]!="") $stopTimeOption = $p["stopTimeOption_".$j];
								if ($stopTimeOption=="") $stopTimeOption = date("A");
							?>
							<select name="stopTimeOption_<?=$j?>" id="stopTimeOption_<?=$j?>">
								<option value="AM" <?=($stopTimeOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($stopTimeOption=='PM')?"selected":"";?>>PM</option>
							</select>
						</TD>
						<?php
						}
						?>
						<?php
						if ($paramEntry) {
						?>
						<TD style="padding-left:5px; padding-right:5px;" nowrap>
							<INPUT type="text" size="3" name="stopTemp_<?=$j?>" id="stopTemp_<?=$j?>" value="<?=$stopTemp?>" autocomplete="off">
						</TD>
						<?php
						}
						?>
						
					</TR>
					<?php
						}
					?>
					<?php
					# Edit Section starts here
					if ($paramEntry && $mpIntervalRecSize>0) {
						$k = 0;
						$mpiStartHr 	= "";
						$mpiStartMints	= "";
						$mpiStartOption = "";
						foreach ($mpIntervalRecs as $mpir) {
							$mpIntervalEntryId = $mpir[0];
							$mpiStartTime = $mpir[1];
							$mpiStartVal  = $mpir[2];
							//$mpiStopTime = $mpir[3];
							//$mpiStopVal  = $mpir[4];
							list($mpiStartHr, $mpiStartMints, $mpiStartOption) = explode("-", $mpiStartTime);
							//list($mpiStopHr, $mpiStopMints, $mpiStopOption) = explode("-", $mpiStopTime);
					?>
					<tr align="center" class="whiteRow" id="row_<?=$j?>_<?=$k?>">
						<td nowrap align="center" class="listing-item">
						<input type="text" maxlength="2" style="text-align: center;" onkeyup="dfcTChk('startTimeHour_<?=$j?>_<?=$k?>', 'startTimeMints_<?=$j?>_<?=$k?>');" value="<?=$mpiStartHr?>" size="1" name="startTimeHour_<?=$j?>_<?=$k?>" id="startTimeHour_<?=$j?>_<?=$k?>" autocomplete="off"/>: <input type="text" maxlength="2" style="text-align: center;" onkeyup="dfcTChk('startTimeHour_<?=$j?>_<?=$k?>', 'startTimeMints_<?=$j?>_<?=$k?>');" value="<?=$mpiStartMints?>" size="1" name="startTimeMints_<?=$j?>_<?=$k?>" id="startTimeMints_<?=$j?>_<?=$k?>" autocomplete="off"/>
							<select id="startTimeOption_<?=$j?>_<?=$k?>" name="startTimeOption_<?=$j?>_<?=$k?>">
								<option value="AM" <?=($mpiStartOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($mpiStartOption=='PM')?"selected":"";?>>PM</option>
							</select>
						</td>
						<td nowrap align="center" class="listing-item">
							<input type="text" value="<?=$mpiStartVal?>" id="startTemp_<?=$j?>_<?=$k?>" name="startTemp_<?=$j?>_<?=$k?>" size="3" style='text-align:right;' autocomplete="off"/>
						</td>
						<td nowrap align="center" class="listing-item" style="display:none;">
						<input type="text" maxlength="2" style="text-align: center;" onkeyup="dfcTChk('stopTimeHour_<?=$j?>_<?=$k?>', 'stopTimeMints_<?=$j?>_<?=$k?>');" value="<?=$mpiStopHr?>" size="1" name="stopTimeHour_<?=$j?>_<?=$k?>" id="stopTimeHour_<?=$j?>_<?=$k?>" autocomplete="off"/>: <input type="text" maxlength="2" style="text-align: center;" onkeyup="dfcTChk('stopTimeHour_<?=$j?>_<?=$k?>', 'stopTimeMints_<?=$j?>_<?=$k?>');" value="<?=$mpiStopMints?>" size="1" name="stopTimeMints_<?=$j?>_<?=$k?>" id="stopTimeMints_<?=$j?>_<?=$k?>" autocomplete="off"/>
							<select id="stopTimeOption_<?=$j?>_<?=$k?>" name="stopTimeOption_<?=$j?>_<?=$k?>">
								<option value="AM" <?=($mpiStopOption=='AM')?"selected":"";?>>AM</option>
								<option value="PM" <?=($mpiStopOption=='PM')?"selected":"";?>>PM</option>
							</select>
						</td>
						<td nowrap align="center" class="listing-item" style="display:none;">
							<input type="text" value="<?=$mpiStopVal?>" id="stopTemp_<?=$j?>_<?=$k?>" name="stopTemp_<?=$j?>_<?=$k?>" size="3" style='text-align:right;' autocomplete="off" />
						</td>
						<td nowrap align="center" class="listing-item" id="stopFCol_<?=$j?>_<?=$k?>">
							<?php
								if (($k+1)==$mpIntervalRecSize) {
							?>
							<input type='checkbox' name='stopMonitoring_<?=$j?>' id='stopMonitoring_<?=$j?>' value='Y' class='chkBox' <?=$stopMonitFlagChk?> />
							<?php
								 }
							?>
						</td>
						<td nowrap align="center" class="listing-item" >
							<a onclick="setMParamItemStatus('<?=$k?>', '<?=$j?>');" href="###">
								<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
							</a>
							<input type="hidden" value="" id="status_<?=$j?>_<?=$k?>" name="status_<?=$j?>_<?=$k?>"/>
							<input type="hidden" value="N" id="IsFromDB_<?=$j?>_<?=$k?>" name="IsFromDB_<?=$j?>_<?=$k?>"/>
							<input type="hidden" value="<?=$mpIntervalEntryId?>" id="mpIntervalEntryId_<?=$j?>_<?=$k?>" name="mpIntervalEntryId_<?=$j?>_<?=$k?>"/>
						</td>
					</tr>
					<?php
						$k++;
						} // Loop Ends here
					}
					# Edit section ends here
					?>

				</table>
				<input type='hidden' name="hidTableRowCount_<?=$j?>" id="hidTableRowCount_<?=$j?>" value="<?=$mpIntervalRecSize?>" readonly="true">
				</td>
				</tr>
				<?php
					if ($paramEntry) {
				?>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<table cellpadding="0" cellspacing="0">
							<TR>
								<TD>
									<a href="###" id='addRow' onclick="javascript:addNewParamItem('<?=$j?>');"  class="link1" title="Click here to add new item.">
										<img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New </a>
								</TD>
								<td valign="top" nowrap="true">
								<?php
									if ($paramEntry) {
									// If Monitoring Interval
								?>
								<!--<table>
									<TR>
									<TD class="listing-head" style="padding-left:15px;">Stop</TD>
									<td align="left" nowrap>
										<input type="checkbox" name="stopMonitoring_<?=$j?>" id="stopMonitoring_<?=$j?>" value="Y" class="chkBox" <?//=$stopMonitFlagChk?> />
									</td>
									</TR>
								</table>-->
								<?php
									}
								?>
								</td>
							</TR>
						</table>
						
					</TD>
				</tr>
				<?php
				}
				?>
				</table>
				</TD></TR>
	<?php
	if ($paramEntry && !$mpIntervalRecSize) {
	?>
	<script language="JavaScript" type="text/javascript">
		addNewParam('tblParamMonitor_'+<?=$j?>, '<?=$j?>', document.getElementById('hidTableRowCount_'+<?=$j?>).value);
	</script>
	<?php }?>
	<?php
	if ($paramEntry && $mpIntervalRecSize>0) {
	?>
	<script language="JavaScript" type="text/javascript">
		//allocMoniIntrval('<?=$j?>');
	</script>
	<?php
	}
	?>
				</table>
			</fieldset>
			</TD>
		</TR>
		</table>
	</TD>
					<?php
						$j++;
						} // parameter loop ends here
					?>
				</tr>
				</table>
	<input type='hidden' name="hidParamRowCount" id="hidParamRowCount" value="<?=sizeof($monitorParamRecs)?>" readonly="true">	
			</TD>
		</tr>	
			<!--<tr>
				<TD colspan="2">
				<table align="center">
				<TR><TD valign="top">
				<fieldset>
				<iframe src="DailyFreezingChartDetails.php?mainId=<?=$mainId?>" width="700" frameborder="0" height="400" marginwidth="1" name="iFrame1"></iframe>
				</fieldset>
				</TD>			
				</TR></table>
				</TD>
			</tr>-->
	<!-- 	Summary of stock Start Here -->
		<tr>
		<TD>
			
		</TD>
		</tr>
<!-- 	Summary of stock End Here -->
                </table></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFreezingChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyFreezingChart(document.frmDailyFreezingChart);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyFreezingChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateAddDailyFreezingChart(document.frmDailyFreezingChart);">												</td>

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
								<td  colspan="1" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Processing Chart </td>
								<td background="images/heading_bg.gif" >
						<table cellpadding="0" cellspacing="0" align="right">
						<tr>
                        <td class="listing-item" nowrap>&nbsp;&nbsp;Date:&nbsp;</td>
											
                        <td nowrap>
						   <? 
							if($selDate=="") $selDate=date("d/m/Y");
							?>
                            <input type="text" id="selDate" name="selDate" size="8" value="<?=$selDate?>">&nbsp;</td>
											
                        <td>&nbsp;<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search ">&nbsp;</td>
										</tr>
									</table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFreezingChartRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFreezingChart.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
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
				<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?
				if (sizeof($dailyFreezingChartRecords) > 0) {
					$i	=	0;
				?>
				<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="15" style="padding-right:10px">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
   				}
   				else
   				{
					$nav.= " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 	}
		else
		{
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
	<td width="20" rowspan="2">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
	</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Sl.<br>No </td>
	<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">P.F.No/ B.F.No </td>-->
	<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Machinery</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Monitoring<br>Parameter</td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Start </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Stop </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Time<br> Diff</td>
	<!--<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Core<br> Temp </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Unloading</td>-->
	<? if($edit==true){?>
	<td class="listing-head" width="45" rowspan="2"></td>
	<? }?>
</tr>
<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
	<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
</tr>
		<?php
			foreach ($dailyFreezingChartRecords as $dpcr) {
				$i++;
				$dpChartMainId	= $dpcr[0];
				$dpChartEntryId	= $dpcr[1];
				
				//$selFreezerId			=	$dpcr[4];
				//$freezerNo			=	$freezercapacityObj->findFreezer($selFreezerId);
				$machineryName	= $dpcr[9];
				$parameterHead  = $dpcr[10];
				
				$startTime	= $dpcr[5];				
				$stopTime	= $dpcr[6];
				$startTemp	= $dpcr[12];
				$stopTemp	= $dpcr[14];
				//$coreTemp	= $dpcr[9];
				//$unloadTime	= $dpcr[10];
				
				#----------------------------------------------------
				//Calculating difference between Start and Stoptime
				//$freezerTime  = $freezercapacityObj->getFreezerTime($selFreezerId);
				$freezerTime  = $dpcr[15];

				list($startTimeHour, $startTimeMints, $startTimeOption) = explode("-", $startTime);
				$parseStartTime = "$startTimeHour"."-"."$startTimeMints";
				$startTimeStamp = getTimeStamp($parseStartTime); //From Config File

				list($stopTimeHour, $stopTimeMints, $stopTimeOption) = explode("-", $stopTime);
				$parseStopTime 	= "$stopTimeHour"."-"."$stopTimeMints";
				$stopTimeStamp = getTimeStamp($parseStopTime);
				$mode='H';				
				$workedTime = abs(dateDiff($startTimeStamp, $stopTimeStamp, $mode));
				$timeDiff = abs($freezerTime - $workedTime);				
				//echo "$freezerTime-$workedTime<br>";
				$displayDiffTime = "";
				if ($freezerTime<$workedTime) {
					$displayDiffTime = "<span style=\"color:#FF0000\">"."+".$workedTime."</span>";
				} else if ($freezerTime>$workedTime &&  $workedTime!=0) {
					$displayDiffTime = "-".$workedTime;
				} else {
					$displayDiffTime = "";
				}
				//---------------------------------------------------
			?>
<tr  bgcolor="WHITE"  >
	<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dpChartMainId;?>" class="chkBox"><input type="hidden" name="dailyActivityChartEntryId_<?=$i;?>" value="<?=$dpChartEntryId?>"></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$i;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$machineryName;?></td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$parameterHead;?></td>	
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTime;?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTemp;?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$stopTime;?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=($stopTemp!=0)?$stopTemp:"";?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$displayDiffTime;?></td>
	<!--<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$coreTemp;?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$unloadTime;?></td>-->	
	<? if($edit==true){?>
	<td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dpChartMainId;?>,'editId'); assignValue(this.form,<?=$dpChartEntryId?>,'editDailyFreezingChartEntryId');"></td>
	<? }?>
</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editDailyFreezingChartEntryId" value="<?=$freezingChartEntryId;?>">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="15" style="padding-right:10px">
<div align="right">
<?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
   				}
   				else
   				{
					$nav.= " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyFreezingChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 	}
		else
		{
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
												<td colspan="15"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>
											<?
												}
											?>
										</table>

	<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
	<? //echo "$mainId-$entryId"; ?>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFreezingChartRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyFreezingChart.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
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

	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			function addNewParamItem(rowId)
			{
				if (!chkMParamAlloc(rowId)) return false;
				addNewParam('tblParamMonitor_'+rowId, rowId, document.getElementById('hidTableRowCount_'+rowId).value);		
			}
			//validateStartTime();
		</SCRIPT>

	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>