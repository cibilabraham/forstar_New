<?php
	require("include/include.php");
	ob_start();
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$saveChanges		=	false;
	$editId			=	"";
	$fishId			=	"";
	
	#Selection Criteria
	$selCriteria = "?selFilter=".$p["selFilter"]."&selRateList=".$p["selRateList"]."&pageNo=".$p["pageNo"]."&recUpdated=1";
	
	#------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
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
	#----------------------------------------------------------	
	
	# Reset val
	if ($p["processFish"]!="") $fishId  = $p["processFish"];

	# Add New 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;		
		if ($processObj->checkBlankRecord() && $p["processMainId"]=="")	{
			$lastId = $processObj->checkBlankRecord();			
		} else {
			if ($p["processMainId"]=="") {
				$tempdataRecIns=$processObj->addBlankRec();
				if ($tempdataRecIns!="") {
					$lastId	= $databaseConnect->getLastInsertedId();
				}
			} else {
				$lastId = $p["processMainId"];				
			}
		}

		if ($processObj->checkBlankRecord() && $p["processMainId"]=="")	{
			$processYieldRecDel = $processObj->deleteYieldProcessWiseRec($lastId);
			$processorExptRecDel = $processObj->deleteAllProcessorExptRec($lastId);
		}		
	}
	
	if ($p["cmdCancel"]!="") {
		$p["processFish"] 	= 	"";
		$editMode	  	=	false;
		$addMode		=	false;
		$editId			=	"";
	}
	
	if ($p["cmdAddCancel"]!="") {
		$processRecDel		=	$processObj->deleteProcess($lastId);	
		$processYieldRecDel	=	$processObj->deleteYieldProcessWiseRec($lastId);
		$processorExptRecDel 	= 	$processObj->deleteAllProcessorExptRec($lastId);
		$lastId			=	"";
		$p["processFish"]	= 	"";
		$addMode		=	false;
	}

	# Insert
	if ($p["cmdAddProcess"]!="" ) {
		$fishId			=	$p["processFish"];
		$preProcessCode		=	trim($p["preProcessCode"]);
		$noProcess		=	($p["noProcess"]=="")?0:$p["noProcess"]; // No Further Process
		$hidColumnCount		=	$p["hidColumnCount"];
		
		$Processes	=	"";
		$sProcess  = array();
		for ($j=0; $j<$hidColumnCount; $j++) {
		 	 $selProcess = $p["process_".$j];			
			if ($selProcess!="" && $j>0)	$Processes .=",";
			$Processes	.="$selProcess";
			$sProcess[$j] = $selProcess;
		 }
	
		#For Checking - the selected Process has no Further Processing
		$cProcess 	= $sProcess[0].",".$sProcess[0];		
		$Day		= ($p["processTime"]=="")?0:$p["processTime"];
		
		$Rate		=	$p["processRate"];
		$Commission	=	$p["processCommission"];
		$Criteria	=	$p["processCriteria"];		
		
		if ($p["rateList"]=="") {
			$rateListId 	= 	$processratelistObj->latestRateList();	
		} else {
			$rateListId	=	$p["rateList"];
		}
				
		#Copy Fish
		$copyFishId		= $p["selCopyFrom"];
		$copyPreProcessCode	= $p["selPreProcessCode"];
		if ($copyFishId!="") $copyFrom	=	true;
		else $copyFrom	=	false;

		//$yieldTolerance		= trim($p["yieldTolerance"]);
			
		#Check for Further Process
		$checkFurtherProcess = $processObj->checkFurtherProcess($fishId, $cProcess);
		
		#For Checking Unique Record based on Fish & Code & quality
		$checkUniqueRecords = $processObj->checkProcessesUniqueRecords($fishId,$Processes,$rateListId);	
		$uniqueRecordId		= $checkUniqueRecords[0];		
		$processYieldRec 	=  $processObj ->findYieldRec($uniqueRecordId);		
		$totalYield		=	$processYieldRec[3]+$processYieldRec[4]+$processYieldRec[5]+$processYieldRec[6]+$processYieldRec[7]+$processYieldRec[8]+$processYieldRec[9]+$processYieldRec[10]+$processYieldRec[11]+$processYieldRec[12]+$processYieldRec[13]+$processYieldRec[14];
	
		$averageYield	=	$totalYield/12;

		# Check Process Code Exist
		$pcExist  = $processObj->chkProcessCodeExist($preProcessCode, $rateListId, '');

		if (sizeof($checkUniqueRecords)==0 && sizeof($checkFurtherProcess)==0 && !$pcExist) {
			$ProcessRecIns	= $processObj->addProcess($fishId, $Processes, $Day, $Rate, $Commission, $Criteria, $lastId, $copyFrom, $copyFishId, $copyPreProcessCode, $preProcessCode, $rateListId, $noProcess);					

			if ($ProcessRecIns) {
				$addMode=false;
				$sessObj->createSession("displayMsg",$msg_succAddProcess);
				$sessObj->createSession("nextPage",$url_afterAddProcess.$selCriteria);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddProcess;
			}
			$ProcessRecIns	=	false;
		} else  {
			$addMode	=	true;
			$averageMsg	=	$averageYield;
			$err		=	$msg_failAddDuplicateProcess;
		}				
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		$processId		=	$p["hidProcessId"];
		$fishId			=	$p["processFish"];
		$preProcessCode		=	$p["preProcessCode"];		
		$hidColumnCount		=	$p["hidColumnCount"];
		
		$Processes	=	"";
		for ($j=0; $j<$hidColumnCount; $j++) {
		 	 $selProcess = $p["process_".$j];			
			if($selProcess!="" && $j>0)	$Processes .=",";
			$Processes	.="$selProcess";
		 }
		
		$Day			=	($p["processTime"]=="")?0:$p["processTime"];		
		$Rate			=	$p["processRate"];
		$Commission		=	$p["processCommission"];
		$Criteria		=	($p["processCriteria"]=="")?0:$p["processCriteria"];		
		$rateListId		=	$p["rateList"];				
		$hidEditProcesses	=	$p["hidEditProcesses"];
		//$yieldTolerance		= trim($p["yieldTolerance"]);		

		if($hidEditProcesses!=$Processes){
			#For Checking Unique Record based on Fish & Code & quality
			$checkUniqueRecords = $processObj->checkProcessesUniqueRecords($fishId, $Processes, $rateListId);
		}

		if (sizeof($checkUniqueRecords)==0) {
			$processRecUptd =	$processObj->updateProcess($processId, $fishId, $Processes, $Day, $Rate, $Commission, $Criteria, $preProcessCode, $rateListId);
			if ($processRecUptd) {
				$saveChanges	=	true;
				$sessObj->createSession("displayMsg",$msg_succProcessUpdate);		
				$sessObj->createSession("nextPage", $url_afterUpdateProcess.$selCriteria);
			} else {
				$editMode	=	true;
				$err		=	$msg_failProcessUpdate;
			}
			$processRecUptd	=	false;
		} else  {
			$editMode	=	true;
			$err		=	$msg_failProcessUpdateDuplicate;
		}
	}

	 # Edit a Process 
	if ($p["editId"]!="" && $p["cmdCancel"]=="" && $saveChanges==false) {
		$editId				=	$p["editId"];
		$editMode			=	true;				
		$processRec			=	$processObj->find($editId);
		$processId			=	$processRec[0];
		if ($p["editSelectionChange"]=='1'||$p["processFish"]=="") {
			$fishId			=	$processRec[1];
		} else {
			$fishId			=	$p["processFish"];
		}
		$Processes			=	stripSlash($processRec[2]);
		$Process			=	explode(",",$Processes);				
		$Day				=	$processRec[3];
		$BasketWeight			=	$processRec[4];
		
		$Rate				=	$processRec[4];
		$Commission			=	$processRec[5];
		$Criteria			=	$processRec[6];
		$code				=	$processRec[7];
		$rateListId			=	$processRec[8];
		
		if ($processRec[9]=='Y') {
			$noProcess 		=	"Checked";
		}
		//$yieldTolerance = $processRec[10];
	}
	
	if ($editMode==true) $lastId=$processId;

	# Delete a Process
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processId	=	$p["delId_".$i];

			if ($processId!="") {
				#Check the selected Pre-Processor is link with any other table 
				$isPreProcessUsed 	=	$processObj->checkPreProcessLinked($processId);
				if(!$isPreProcessUsed){
					$processRecDel		=	$processObj->deleteProcess($processId);	
					$processYieldRecDel	=	$processObj->deleteYieldProcessWiseRec($processId);
					$processorExptRecDel = $processObj->deleteAllProcessorExptRec($processId);
				}
			}
		}
		if ($processRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProcess);
			$sessObj->createSession("nextPage",$url_afterDelProcess.$selCriteria);
		} else {
			$errDel	=	$msg_failDelProcess;
		}
		$processRecDel	=	false;
	}




if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processCodeId	=	$p["confirmId"];
			if ($processCodeId!="") {
				// Checking the selected fish is link with any other process
				$processCodeRecConfirm = $processObj->updateProcessconfirm($processCodeId);
			}

		}
		if ($processCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmprocess);
			$sessObj->createSession("nextPage",$url_afterDelProcess.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$processCodeId = $p["confirmId"];

			if ($processCodeId!="") {
				#Check any entries exist
				
					$processCodeRecConfirm = $processObj->updateProcessReleaseconfirm($processCodeId);
				
			}
		}
		if ($processCodeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmprocess);
			$sessObj->createSession("nextPage",$url_afterDelProcess.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	#------------------------------------	
	if ($g["selRateList"]!="")	$selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="")	$selRateList	= $p["selRateList"];
	else				$selRateList 	= $processratelistObj->latestRateList();	
	#------------------------------------

	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	#List All Processes
	if ($g["selFilter"]!="") $recordsFilterId = $g["selFilter"];
	else $recordsFilterId	= $p["selFilter"];
		
	#Condition for Select a Fish 	
	if ($p["existRecordsFilterId"]==0 && $p["selFilter"]!=0) {
		$offset = 0;
		$pageNo = 1;
	}
	
	/*
	if ($recordsFilterId!=0) {			
		$processRecords 	= $processObj->processRecPagingFilter($recordsFilterId, $selRateList, $offset, $limit);
		$numrows	=  sizeof($processObj->processRecFilter($recordsFilterId,$selRateList));
	} else {		
		$processRecords		=	$processObj->fetchPagingRecords($selRateList, $offset, $limit);
		$numrows	=  sizeof($processObj->fetchAllRecords($selRateList));
	}
	*/

	# List All Recs
	$processRecords 	= $processObj->fetchAllPagingRecords($recordsFilterId, $selRateList, $offset, $limit);	
	$processSize		= sizeof($processRecords);

	## -------------- Pagination Settings II -------------------
	$numrows 	= sizeof($processObj->fetchAllRecords($selRateList, $recordsFilterId));
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {

		#List All Landing Centers
		//$landingCenterRecords	=	$landingcenterObj->fetchAllRecords();
		$landingCenterRecords	=	$landingcenterObj->fetchAllRecordsActiveLanding();

		if ($fishId!="") $processCodeRecords = $processcodeObj->processCodeRecFilter($fishId);
	}

	# Returns all Fish
	$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();
	
	#Process Rate List
	$processRateListRecords	=	$processratelistObj->fetchAllRecordsRateactive();

	# Default Yield
	$defaultYieldTolerance  = 	$displayrecordObj->getDefaultYieldTolerance();

	if ($editMode)	$heading	= $label_editProcess;
	else		$heading	= $label_addProcess;

	# Update Pre-Process Sequence Table
	if ($g["recUpdated"]!="") $productionAnalysisReportObj->getPreProcessMap();
	
	$help_lnk="help/hlp_Process.html";

	$ON_LOAD_PRINT_JS	= "libjs/process.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmProcess" id="frmProcess" action="Processes.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
<tr><td height="10" align="center"><a href="ProcessCode.php" class="link1" title="Click to manage Process Code">Process Code Master</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="PreProcessRateList.php" class="link1" title="Click to manage Process Rate list">Pre-Process Rate List Master</a></td></tr>
	<? if($err!="" ){?>
	<tr>
		<td  align="center" class="err1" ><?=$err;?></td>
	</tr>
	<? }?>
	<tr>
	  <td height="10" align="center" ></td>
    </tr>
	<tr>
		<td height="10" align="center">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
			<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                        <option value="">--Select--</option>
                      	<?php
			foreach($processRateListRecords as $prl) {
				$processRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		=	dateFormat($prl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = "";
				if($selRateList==$processRateListId) $selected = "Selected";
			?>
                      <option value="<?=$processRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? }?>
                    </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='PreProcessRateList.php?mode=AddNew'"></td>
		<? }?>
			<?php
			if ($defaultYieldTolerance!=0) {
			?>
			<td>&nbsp;</td>
			<td class="fieldName" nowrap>Default Yield Tolerance</td>
			<td class="listing-item"><strong><?=$defaultYieldTolerance?>&nbsp;%</strong></td>
			<?php
				}
			?>
                  </tr>
                </table></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="PRE-PROCESSING RATES MASTER";
								include "template/boxTL.php";
							?>
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr><TD colspan="3" align="center">
	<table width="70%" align="center">
	<?php
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
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
								<td width="244" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
							    <td width="286" background="images/heading_bg.gif" class="pageName" ></td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="85%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="3" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Processes.php')">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddProcess(document.frmProcess,0);">	
										</td>
											<?} else{?>

											<td  colspan="3" align="center">
											
											<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Processes.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddProcess" class="button" value=" Add " onClick="return validateAddProcess(document.frmProcess,1);">
											</td>
											<?}?>
										</tr>
										<input type="hidden" name="hidProcessId" value="<?=$processId;?>">
										<input type="hidden" name="hidEditProcesses" value="<?=$Processes?>">
	<tr><TD height="10" colspan="3" style="padding-left:10px;padding-right:10px;" align="center">
	<? if($averageMsg!="" ){?>Selected Pre-Process Average Yield=<span class="listing-item"><?=$averageMsg;?></span><? }?> 
	</TD></tr>
	<tr>
		<td colspan="3" nowrap style="padding-left:10px;padding-right:10px;" align="center">
			  <table width="300" cellpadding="0" cellspacing="0" border="0">
				<tr><TD colspan="2">
				<table>
					<tr>
                                       <td class="fieldName" nowrap>*Fish</td>
                                       <td nowrap>
			<select name="processFish" id="processFish" onChange="<? if ($addMode==true) { ?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<? }?>">
			<option value="">--Select--</option>
			<?php
			if (sizeof($fishMasterRecords)> 0) {
				foreach ($fishMasterRecords as $fr) {
					$id	= $fr[0];
					$name	= $fr[1];
					$selected = "";
					if ($fishId==$id) $selected = " selected ";
			?>
			<option value="<?=$id;?>" <?=$selected;?> ><?=$name;?></option>
			<?
				}
			}
			?>
			</select>
			</td>
			<td width="10">&nbsp;</td>
			<td class="fieldName" nowrap="nowrap">Code</td>
                                              <td nowrap>
						  <? 
							  if($p["preProcessCode"]!="") $code=$p["preProcessCode"];
						   ?> 
							<input name="preProcessCode" type="text" id="preProcessCode" size="5" value="<?=$code?>">
						</td>
                </tr>
				</table>
				</TD></tr>
                                            <tr>
                                              <td class="fieldName" nowrap="nowrap">No Further Processing</td>
                                              <td nowrap>
						  <? 
							  if($addMode==true){
								  $noProcess = "";
								  if($p["noProcess"]=='Y') {
									$noProcess = "Checked";
								  }
							  }
						  ?>
						  <input name="noProcess" type="checkbox" id="noProcess" value="Y" <?=$noProcess?> class="chkBox">
						  &nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="listing-item"><font size="1">(If Yes, please give tick mark)</font></span>
					</td>
                                            </tr>
                                            <tr>
                                              <td class="fieldName" nowrap>Process Sequence</td>
                                              <td nowrap>
						  <table width="200">
                                                <tr>
												<? 
												$selProcess	=	"";
												for($j=0;$j<sizeof($processCodeRecords);$j++)
												 {
													 $selProcess	=	 $Process[$j];
													if($Process[$j]=="") $selProcess	=	$p["process_".$j];
												?>
                                                  <td>
												  <select name="process_<?=$j?>" id="process_<?=$j?>">
                                              	  <option value="">--Select--</option>
                                              	  <? 
													foreach ($processCodeRecords as $fl)
														{
															$processCodeId		=	$fl[0];
															$processCode		=	$fl[2];
																															
															$selected	=	"";
															if( $selProcess == $processCodeId )
															{
																$selected	=	"selected";
															}															
													?>
												<option value="<?=$processCodeId;?>" <?=$selected;?>><?=$processCode;?></option>
                                                 <?
													}
												 ?>
                                            </select></td>
											<? }?>
											<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$j?>" />
                                                </tr>
                                              </table></td>
                                            </tr>
                                            <tr>
                                              <td colspan="5" nowrap="nowrap" class="fieldName"></td>
                                            </tr>
                                            <? if($addMode==true){?>
                                            <tr>
                                              <td class="fieldName" nowrap="nowrap">Copy From</td>
                                              <td nowrap="nowrap" align="left"><table width="200">
                                                <tr>
                                                  <td>
							<? 
								$copyFromId		=	$p["selCopyFrom"];
								$copyPreProcessCodeRecords = $processObj->preProcessCodeRecFilter($copyFromId,$selRateList);
							?>
                                                    <select name="selCopyFrom" id="selCopyFrom" onchange=" this.form.submit(); disableProcessEntries(document.frmProcess);" style="width:150;">
                                                      <option value="">Select Fish </option>
                                                      <?php
							if (sizeof($fishMasterRecords)> 0) {
								foreach ($fishMasterRecords as $fl) {
									$id		=	$fl[0];
									$name		=	$fl[1];
									$selected	=	"";
									if ($copyFromId == $id) {
										$selected	=	" selected ";
									}
							?>
                                                      <option value="<?=$id;?>" <?=$selected;?>><?=$name;?></option>
                                                      <?											
								}
							}
						?>
                                                    </select></td>
                                                  <td class="fieldName" nowrap="nowrap">&nbsp;Pre-Process Code</td>
                                                  <td nowrap>
							<select name="selPreProcessCode">
                                                    	<option value="">--Select--</option>
                                                    <?php
							foreach ($copyPreProcessCodeRecords as $fl) {
								$processCodeId		=	$fl[0];
								$processCode		=	$fl[2];
								$preProcessCode		=	$fl[10];					
								$selected	=	"";
								if( $recordProcessCode == $processCodeId || $processId==$processCodeId){
									$selected	=	"selected";
								}
							?>
                                                    <option value="<?=$preProcessCode;?>" <?=$selected;?> ><?=$preProcessCode;?></option>
                                                    <?
							}
							?>
                                                  </select>
						</td>
                                                </tr>
                                              </table></td>
                                            </tr>
				<? }?>
                                          </table></td>
			  </tr>
			<? if($addMode==true){?>
				<tr><td  height="10" nowrap class="listing-item"  colspan="3" align="center">[OR]</td></TD></tr>
			<? }?>
	<? if($p["selCopyFrom"]==""){?>							
	<tr>
	  <td  height="10" colspan="3" align="left" style="padding-left:10px;padding-right:10px;">
		<table><TR><TD>
			<!--<fieldset><legend class="listing-item">Landing Center</legend>-->
		<?php			
			$entryHead = "Landing Center";
			require("template/rbTop.php");
		?>
		<table width="100%" cellpadding="0" cellspacing="0"><TR><TD>
		<iframe name="iFrame1" id="iFrame1" src="ProcessYield.php?lastId=<?=$lastId?>&editId=<? if($editMode==true) echo 1;?>" width="700" frameborder="0" height="165">		
		</iframe>
		</TD></TR>
		</table>
		<?php
			require("template/rbBottom.php");
		?>
		<!--</fieldset>-->
			</TD></TR>
		</table>			
	</td>
		  </tr>
		<tr>
			<td  height="10" colspan="3" align="left" style="padding-left:10px;padding-right:10px;">
			<table><TR><TD>
			<!--<fieldset><legend class="listing-item">Rate Details</legend>-->
				<?php			
					$entryHead = "Rate Details";
					require("template/rbTop.php");
				?>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr><TD height="5"></TD></tr>
				<TR><TD>
				<iframe name="iFrame2" id="iFrame2" src="ProcessPreProcessors.php?lastId=<?=$lastId?>&editId=<? if($editMode==true) echo 1;?>" width="500" frameborder="0" height="150"></iframe>
				</td></tr>
				</table>
				<?php
					require("template/rbBottom.php");
				?>
			<!--</fieldset>-->
			</TD></TR></table>
			</td>
	  </tr>		
	<? }?>
	<tr>
		<td colspan="3" valign="top" style="padding-left:10px; padding-right:10px;" align="left">
			<Table cellpadding="2" cellspacing="0">
			<tr>
                            	<td class="fieldName" nowrap="true">Time</td>
                           	<td class="listing-item" nowrap="nowrap" align="left">
					<input type="text" name="processTime" size="1" value="<?=$Day;?>">&nbsp;Days
				</td>
                                <td  class="fieldName" nowrap="true">Rate List</td>
				<td nowrap>
					<select name="rateList" id="rateList">
                      			<option value="">--Select--</option>
                      			<?php
						foreach ($processRateListRecords as $prl) {
							$processRateListId	= $prl[0];
							$rateListName		= stripSlash($prl[1]);
							$startDate		= dateFormat($prl[2]);
							$displayRateList = $rateListName."&nbsp;(".$startDate.")";
							$selected = "";
							if($selRateList==$processRateListId || $rateListId==$processRateListId) $selected = "Selected";
					?>
                      			<option value="<?=$processRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                      			<? }?>
                    			</select>
			</td>
			<!--<td class="fieldName">Yield Tolerance</td>
			<td class="listing-item" nowrap>
				<input type="text" name="yieldTolerance" id="yieldTolerance" value="<?//=$yieldTolerance?>" size="3" autocomplete="off" style="text-align:right;" />&nbsp;%
			</td>-->
                           </tr>
			</table>
		</td>
	</tr>
	<tr style="display:none">
		<td colspan="3" valign="top" style="padding-left:10px;padding-right:10px;">
		<table width="60%" border="0" cellpadding="0" cellspacing="0">
                              <tr style="display:none">
                                                <td class="fieldName">*Rate</td>
                                                <td class="fieldName">&nbsp;*Commission</td>
                                                <td class="fieldName">&nbsp;&nbsp;Criteria</td>
                                              </tr>
                                              <tr style="display:none">
                                                <td class="listing-item" nowrap="nowrap"><input name="processRate" type="text" size="3" value="<?=$Rate;?>" style="text-align:right;"> 
                                                Rs. </td>
                                                <td class="listing-item" nowrap="nowrap">&nbsp;<input name="processCommission" type="text" size="3" value="<?=$Commission;?>" style="text-align:right;">
                                                Rs.</td>
                                                <td nowrap="nowrap">&nbsp;&nbsp;
												<select name="processCriteria" id="processCriteria" title="Select for Process Rate calculation">
													<option value="0">To</option>
                                                  <option value="1"<? if($Criteria=='1'){ echo "selected";}?>>From</option>
                                                </select>                                                </td>
                                              </tr>
											  <tr>
                                                <td colspan="3">&nbsp;</td>
                                              </tr>
                                            </table></td>
										    <td>&nbsp;</td>
										    <td width="14%">&nbsp;</td>
										</tr>
		<tr><TD height="10"></TD></tr>
										<tr>
											<? if($editMode){?>

											<td colspan="3" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Processes.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddProcess(document.frmProcess,0);">											</td>
											<?} else{?>

											<td  colspan="3" align="center">
											<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Processes.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddProcess" class="button" value=" Add " onClick="return validateAddProcess(document.frmProcess,1);">											</td>
											<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
										</tr>
										<tr>
											<td colspan="4" >&nbsp;</td>
										</tr>
								</table>							  </td>
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
		
		# Listing Processes Starts
	?>
	</table>
		</TD></tr>
		<?php 
		if ($addMode || $editMode) {
		?>
		<tr>
			<td colspan="3" height="20" ></td>
		</tr>
		<?php
			}
		?>
	<tr>
		<td colspan="3" align="center">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">	
				<tr>
					<td nowrap class="listing-item" align="right">Fish&nbsp;</td>
					<td nowrap align="center">
						<select name="selFilter" onChange="this.form.submit();">
						<option value="0">--Select All--</option>
						<?php
						if (sizeof($fishMasterRecords)>0) {
							foreach ($fishMasterRecords as $fl) {
								$fishId		=	$fl[0];
								$fishName	=	$fl[1];
								$selected	=	"";
								if ($fishId == $recordsFilterId) {
									$selected	=	"selected";
								}
						?>
						<option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
						<?
							}
						}
						?>
						</select>
					</td>					
				</tr>
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
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="290" background="images/heading_bg.gif" class="pageName" >PRE-PROCESSING RATES MASTER</td>
								    <td background="images/heading_bg.gif" class="pageName" align="right"  nowrap="nowrap">
								</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input style="background-color:#ff0000;color: white;" type="submit" value=" Delete **" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcesses.php?selFilter=<?=$recordsFilterId?>&selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
								<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($processRecords) > 0 )
												{
													$i	=	0;
											?>
		<thead>
											<? if($maxpage>1){?>
											<tr>
		<td colspan="12" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:5px; padding-right:5px;">Fish Name </th>
		<th style="padding-left:5px; padding-right:5px;">Code </th>
		<th style="padding-left:5px; padding-right:5px;">Process Sequence </th>
		<th style="padding-left:5px; padding-right:5px;">Rate</th>
		<th style="padding-left:5px; padding-right:5px;">Commission</th>								
		<th style="padding-left:5px; padding-right:5px;">Criteria</th>
		<th style="padding-left:5px; padding-right:5px;">Yield <br>Average(%) </th>
		<th style="padding-left:5px; padding-right:5px;">Yield<br>Tolerance(%) </th>
		<th style="padding-left:5px; padding-right:5px;">No.of <br>Exception<br> L.Centrs</th>
		<th style="padding-left:5px; padding-right:5px;">No.of <br>Exception<br> Processors</th>
		<? if($edit==true){?>
		<th>&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th width="60">&nbsp;</th>
		<? }?>

	</tr>
	</thead>
	<tbody>
	<?php
	$fishName = "";
	$averageYield = "";
	foreach($processRecords as $fr)
	{
		$i++;
		$processId		=	$fr[0];
		$fishId			=	$fr[1];							
		$fishRec		=	$fishmasterObj->find($fishId);
		$fishName		=	stripSlash($fishRec[1]);
		$Processes		=	stripSlash($fr[2]);
		$Process		=	explode(",",$Processes);
		$averageYield		= $processObj->getYieldAverage($processId);			
		$processTime		=	$fr[3];
		//$processRate		=	$fr[4];
		//$processCommission	=	$fr[5];
		//$selPPCriteria	= 	$fr[7];
		$processFlag		=	$fr[6];
		$active=$fr[11];

		# get Default rate
		list($processRate, $processCommission, $selPPCriteria, $ppYieldTolerance) = $processObj->getDefaultPreProcessRate($processId);
		$criteria	= "";
		if ($selPPCriteria==0) $criteria	= "To";
		else $criteria	= "From";
		
		$preProcessCode	= $fr[10];

		$expLCenterRecords = $processObj->fetchAllExceptionCenterRecords($processId);	
		$noOfCenters	= "";
		$disLCExpt = "";
		if(sizeof($expLCenterRecords)>0) {
			$noOfCenters = sizeof($expLCenterRecords);
			$showLCExption = $processObj->displayExceptionLC($processId);
			$disLCExpt = "<a href='###' onMouseover=\"ShowTip('$showLCExption');\" onMouseout=\"UnTip();\" class='link5'>$noOfCenters</a> ";
		}
		# Exception Processors
		$exceptionPreProcessors	= $processObj->fetchExptedProcessor($processId);
		$noOfExptProcessors = "";
		$disPPExpt = "";
		if(sizeof($exceptionPreProcessors)>0) {
			$noOfExptProcessors = sizeof($exceptionPreProcessors);	
			$showPPExpt = $processObj->displayPPException($processId);
			$disPPExpt = "<a href='###' onMouseover=\"ShowTip('$showPPExpt');\" onMouseout=\"UnTip();\" class='link5'>$noOfExptProcessors</a> ";
		}

		//$ppYieldTolerance = $fr[11];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
<?php if ($existingcount==0) {?>		
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processId;?>" class="chkBox">
<?php 
}
?>		
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$preProcessCode;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">			
	<?php
	for($k=0; $k<sizeof($Process);$k++){								
		$displayProcess	=	stripSlash( $processcodeObj->findProcessCode($Process[$k]));
		if( sizeof($Process) > 2 && $k < sizeof($Process)-1 )	{
			$compareProcess = $Process[$k].",".$Process[$k+1];
			$checkUniqueRecords	=	$processObj->checkProcessesUniqueRecords($fishId,$compareProcess,$selRateList);
			if( sizeof($checkUniqueRecords)==0 )	{					
				$displayProcess .= "<font color=red>-></font>";
			} else $displayProcess .= "->";
		} else if ( $k < sizeof($Process)-1 ) $displayProcess .= "->";
		echo $displayProcess;
	}
	?>												
	</td>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$processRate;?></td>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$processCommission;?></td>
	<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$criteria?></td>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=number_format($averageYield,2,'.','');?></td>
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=($ppYieldTolerance!=0)?$ppYieldTolerance:"";?></td>	
	<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$disLCExpt?></td>
	<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$disPPExpt?></td>
	<? if($edit==true){?>
                  <td class="listing-item" width="50" align="center">
				  <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processId;?>,'editId');assignValue(this.form,'1','editSelectionChange'); this.form.action='Processes.php';"><? } ?></td>
	  <? }?>
	   <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$processId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$processId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="<?=$editId?>">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">	
	 <? if($maxpage>1){?>
		<tr>
		<td colspan="12" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"Processes.php?selFilter=$recordsFilterId&selRateList=$selRateList&pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
	</tbody>
	<?
		} else {
	?>
	<tr>
		<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table>
<input type="hidden" name="processMainId" id="processMainId" value="<?=$lastId?>">
<input type="hidden" name="existRecordsFilterId" value="<?=$recordsFilterId?>" ></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" style="background-color:#ff0000;color: white;" value=" Delete ** " name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcesses.php?selFilter=<?=$recordsFilterId?>&selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" align="center" ></td>
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
		
		<tr>
			<td height="10">
				<input type="hidden" name="noProcessorExptRate" id="noProcessorExptRate" value="">
				<input type="hidden" name="defaultRateExist" id="defaultRateExist" value="">
			</td>
		</tr>
		<tr>
		  <td height="10" align="center"><a href="ProcessCode.php" class="link1" title="Click to manage Process Code">Process Code Master</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="PreProcessRateList.php" class="link1" title="Click to manage Process Rate list">Pre-Process Rate List Master</a></td>
    </tr>	
  </table>
  
  <? if ( $addMode == true && $copyFromId!="" ){?>
<script language="javascript">
 disableProcessEntries(document.frmProcess);
</script>
<? }?>
</form>
	<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");

	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
	?>
