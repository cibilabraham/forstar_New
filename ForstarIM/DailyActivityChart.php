<?php
	require("include/include.php");
	//require_once ("components/base/DAMSetting_model.php");
	//require_once ("components/base/DAMSetting_subhead_model.php");
	require_once ("lib/dailyactivitychart_ajax.php");

	//$DAMSetting_m = new DAMSetting_model();
	//$DAMSetting_subhead_m = new DAMSetting_subhead_model();

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

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
		
	# ----------------------------------------------------------------------------------
	$cfArr = array();	
	$cfArr["NNNN"] = array("CB", "-", "OB", "=", "DIFF");	
	$cfArr["NYNN"] = array("OB", "+", "PURCHASED", "-", "USED", "=", "CB");
	$cfArr["YYNN"] = array("OB", "+", "PRODUCED", "+", "PURCHASED", "-", "USED", "=", "CB");
	$cfArr["YYYN"] = array("OB", "+", "PRODUCED", "+", "PURCHASED", "-", "USED", "-", "OSSUPPLY", "=", "CB"); 
	$cfArr["YYNY"] = array("OB", "+", "PRODUCED", "+", "PURCHASED", "-", "USED", "-", "OSSALE", "=", "CB");
	$cfArr["YYYY"] = array("OB", "+", "PRODUCED", "+", "PURCHASED", "-", "USED", "-", "OSSUPPLY", "-", "OSSALE", "=", "CB");
	//printr($cfArr);

	$fieldArr = array();
	$fieldArr["CB"] = array("C/B","<input type='text' name='closingBal_[R]' id='closingBal_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["OB"] = array("O/B","<input type='text' name='openingBal_[R]' id='openingBal_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["DIFF"] = array("DIFF","<input type='text' name='diffBal_[R]' id='diffBal_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["PRODUCED"] = array("PRODUCED","<input type='text' name='produced_[R]' id='produced_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["PURCHASED"] = array("PURCHASED","<input type='text' name='purchased_[R]' id='purchased_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["USED"] = array("USED","<input type='text' name='used_[R]' id='used_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["OSSUPPLY"] = array("O/S SUPPLY","<input type='text' name='osSupply_[R]' id='osSupply_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	$fieldArr["OSSALE"] = array("O/S SALE","<input type='text' name='osSale_[R]' id='osSale_[R]' size='4' style='text-align:right;' onkeyup='calcActChart();' value='[V]' autocomplete='off' />");
	
	//printr($fieldArr);
	$operArr = array("+","-");
	# ----------------------------------------------------------------------------------

		
	if ($p["cmdCancel"]!="") {
		$addMode 	= false;
		$editMode 	= false;
		$editId 	= "";
	}
	

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true; 
	if ($addMode) {
		$curDate = date("d/m/Y");
		$dateC	  = explode("/",$curDate);
		$closingDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-1,$dateC[2])); //Find the opening data
		list($dieselOB, $iceOB, $firstGeneratorPrevious, $secondGeneratorPrevious,  $thirdGeneratorPrevious, $firstElectricityMeterOpening, $secondElectricityMeterOpening, $thirdElectricityMeterOpening, $waterMeterOpening) = $dailyactivitychartObj->getClosingActivityDetails($closingDate);
		//die();
	}

	/**
	* Insert  
	* Date wise duplicate check applied
	*/
	if ($p["cmdAdd"]!="") {

		$selectDate		= mysqlDateFormat($p["selectDate"]);
		$selectTimeHour		= $p["selectTimeHour"];
		$selectTimeMints	= $p["selectTimeMints"];
		$timeOption 		= $p["timeOption"];
		$selectTime = $p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];
		if ($selectDate) {
			# Insert Main Rec
			$insertMainRec = $dailyactivitychartObj->addMainRec($selectDate, $selectTime, $userId);
			$dacMainId = "";
			if ($insertMainRec) $dacMainId = $databaseConnect->getLastInsertedId();
		}

		$dActHeadRowCount	= $p["dActHeadRowCount"];
		for ($i=1; $i<=$dActHeadRowCount; $i++) {
			$subHeadRowCount = $p["subHeadRowCount_".$i];
			for ($j=1; $j<=$subHeadRowCount; $j++) {
				$rIndx = $i."_".$j;
				$damEntryId 	= $p["damEntryId_".$rIndx];
				$closingBal 	= $p["closingBal_".$rIndx];
				$openingBal 	= $p["openingBal_".$rIndx];
				$diffBal 	= $p["diffBal_".$rIndx];
				$produced	= $p["produced_".$rIndx];
				$purchased	= $p["purchased_".$rIndx];
				$used		= $p["used_".$rIndx];
				$osSupply	= $p["osSupply_".$rIndx];
				$osSale		= $p["osSale_".$rIndx];

				if ($dacMainId!=0) {
					$insertDACRecs = $dailyactivitychartObj->addDACEntryRec($dacMainId, $damEntryId, $closingBal, $openingBal, $diffBal, $produced, $purchased, $used, $osSupply, $osSale);
				}
			} // Sub head loop ends here
		} // Main head Loop Ends here


		if ($insertMainRec) {
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddDailyActivityChart);
			$sessObj->createSession("nextPage",$url_afterAddDailyActivityChart.$selection);
		} else {
			$addMode 	= true;
			$err		= $msg_failAddDailyActivityChart;
		}		
	}


	/**
	* Edit	
	*/
	if ($p["editId"]!="" && $p["cmdSaveChange"]=="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
	
		$dailyActivityChartRec	=	$dailyactivitychartObj->find($editId);
		
		$mainId 	=	$dailyActivityChartRec[0];
	
		$selectDate		=	dateFormat($dailyActivityChartRec[1]);

		$selectTime		=	explode("-", $dailyActivityChartRec[2]);
		$selectTimeHour		=	$selectTime[0];
		$selectTimeMints	=	$selectTime[1];
		$timeOption 		= 	$selectTime[2];
		
	}

	/**
	* Update 
	*/
	if ($p["cmdSaveChange"]!="") {
		
		$mainId 		=	$p["mainId"];
		$selectDate		=	mysqlDateFormat($p["selectDate"]);
		$selectTimeHour		=	$p["selectTimeHour"];
		$selectTimeMints	=	$p["selectTimeMints"];
		$timeOption 		= 	$p["timeOption"];
		$selectTime		=	$p["selectTimeHour"]."-".$p["selectTimeMints"]."-".$p["timeOption"];

		if ($selectDate) {
			# Update Main Rec
			$updateMainRec = $dailyactivitychartObj->updateMainRec($mainId, $selectDate, $selectTime);
		}

		$dActHeadRowCount	= $p["dActHeadRowCount"];
		for ($i=1; $i<=$dActHeadRowCount; $i++) {
			$subHeadRowCount = $p["subHeadRowCount_".$i];
			for ($j=1; $j<=$subHeadRowCount; $j++) {
				$rIndx = $i."_".$j;

				$dacEntryId 	= $p["dacEntryId_".$rIndx];

				$damEntryId 	= $p["damEntryId_".$rIndx];
				$closingBal 	= $p["closingBal_".$rIndx];
				$openingBal 	= $p["openingBal_".$rIndx];
				$diffBal 	= $p["diffBal_".$rIndx];
				$produced	= $p["produced_".$rIndx];
				$purchased	= $p["purchased_".$rIndx];
				$used		= $p["used_".$rIndx];
				$osSupply	= $p["osSupply_".$rIndx];
				$osSale		= $p["osSale_".$rIndx];

				if ($mainId!=0 && $dacEntryId!="") { 
					$updateDACRecs = $dailyactivitychartObj->updateDACEntryRec($dacEntryId, $damEntryId, $closingBal, $openingBal, $diffBal, $produced, $purchased, $used, $osSupply, $osSale);
				} else if ($mainId!=0 && $dacEntryId=="") {
					$insertDACRecs = $dailyactivitychartObj->addDACEntryRec($mainId, $damEntryId, $closingBal, $openingBal, $diffBal, $produced, $purchased, $used, $osSupply, $osSale);
				}
				
			} // Sub head loop ends here
		} // Main head Loop Ends here


		if ($updateMainRec) {
			$editMode = false;
			$editId = "";
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyActivityChart);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyActivityChart.$selection);
		} else {
			$editMode	= true;
			$err		= $msg_failUpdateDailyActivityChart;
		}		
	}
	
	/**
	* Delete Recs
	*/
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailyActivityChartMainId = $p["delId_".$i];			
			if ($dailyActivityChartMainId!="") $dailyActivityChartRecDel = $dailyactivitychartObj->deleteDAChartRec($dailyActivityChartMainId);
		}
		if ($dailyActivityChartRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyActivityChart);
			$sessObj->createSession("nextPage",$url_afterDelDailyActivityChart.$selection);
		} else {
			$errDel	= $msg_failDelDailyActivityChart;
		}
		$dailyActivityChartRecDel	= false;
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit;
	## ----------------- Pagination Settings I End ------------

	#List All Record
	if ($g["selDate"]!="") 		$selDate = $g["selDate"];
	else if($p["selDate"]=="") 	$selDate = date("d/m/Y");
	else				$selDate = $p["selDate"];

	
	if ($selDate!="" || $p["cmdSearch"]!="") {

		$searchDate	=	mysqlDateFormat($selDate);

		$dailyActivityChartRecords = $dailyactivitychartObj->fetchPagingActivityChartRecords($searchDate, $offset, $limit);
		$dailyActivityChartRecordSize	=	sizeof($dailyActivityChartRecords);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=  	sizeof($dailyactivitychartObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	#Find the main Id for the selected date 
	$dailyActivityMainId = $dailyactivitychartObj->getMainTableId($searchDatee);
	if ($dailyActivityMainId!="") {	 
		
		 
	}
	

	if ($addMode || $editMode) {
		#List all Freezer Records
		//$freezerRecords = $freezercapacityObj->fetchAllRecords();
		
		# Master > Daily Activity Monitoring setting  head
		//$damSettingHeadRecs = $dailyactivitychartObj->findAll(array("order"=>"head_name asc"));
		//$damSettingHeadRecSize = sizeof($damSettingHeadRecs);
	}

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";

	if ($editMode)	$heading = $label_editDailyActivityChart;
	else 		$heading = $label_addDailyActivityChart;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/dailyactivitychart.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDailyActivityChart" action="DailyActivityChart.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<? } ?>
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyActivityChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChart(document.frmDailyActivityChart);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyActivityChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value="Save & Exit" onClick="return validateDailyActivityChart(document.frmDailyActivityChart);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidDailyActivityChartId" value="<?=$editDailyActivityChartId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
<!-- 	Display Error message	 -->
		<tr>
			<TD class="listing-item" style='line-height:normal; font-size:10px; color:red;' id="divDupExistMsg" nowrap="true" align="center" colspan="2"></TD>
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
                      <input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selectDate?>" autocomplete="off" onchange="xajax_chkDupEntry(document.getElementById('selectDate').value, '<?=$mode?>', '<?=$mainId?>');">
			</td>			
                  <td class="fieldName" nowrap>Entry Time </td>
                  <td nowrap="nowrap">
				  <?
					if ($addMode && $p["selectTimeHour"]!="") $selectTimeHour = $p["selectTimeHour"];
					if ($selectTimeHour=="") $selectTimeHour = date("g");
				  ?>
				  <input type="text" id="selectTimeHour" name="selectTimeHour" size="1" value="<?=$selectTimeHour;?>" onchange="return activityTimeCheck();" style="text-align:center;" maxlength="2">:
                   		 <?
					if ($addMode && $p["selectTimeMints"]!="") $selectTimeMints = $p["selectTimeMints"];
					if ($selectTimeMints=="") $selectTimeMints = date("i");
				  ?>
				    <input type="text" id="selectTimeMints" name="selectTimeMints" size="1" value="<?=$selectTimeMints;?>" onchange="return activityTimeCheck();" style="text-align:center;" maxlength="2">
				  <? 
					if ($addMode && $p["timeOption"]!="") $timeOption = $p["timeOption"];
					if ($timeOption=="") $timeOption = date("A");
				  ?>
                    <select name="timeOption" id="timeOption">
			<option value="AM" <? if($timeOption=='AM') echo "selected"?>>AM</option>
			<option value="PM" <? if($timeOption=='PM') echo "selected"?>>PM</option>
                    </select>
	            </td>			
			</TR>
				</table>
				</TD>
			</tr>
<!-- Dynamic Activity recording starts here	 -->
<tr>
	<TD colspan="2" valign="top" style="padding-left:10px; padding-right:10px;" align="center">
		<table>
		<TR>
		<td>
		<table>
		<TR>
			<?php
			$numCol = 5;
			$nextRec= 0;
			$j = 0;
			foreach ($damSettingHeadRecs as $dshr) {							
				$j++;
				$nextRec++;
				$damsMainId = $dshr->id;

				# Get Installed capacity Sub head
				$subHeadRecs = $DAMSetting_subhead_m->findAll(array("where"=>"entry_id='".$damsMainId."'", "order"=>"id asc"));
				$subHeadRecSize = sizeof($subHeadRecs);
				//printr($subHeadRecs);
			?>
			<TD valign="top">
				<table>
				<TR>
				<TD>
				<fieldset>
				<legend class="listing-item"><?=$dshr->head_name?></legend>
				<table>
				<tr>
				<?php
				$subHeadNumCol = 3;
				$subHeadNextRec = 0;
				$k = 0;
				$damEntryId = "";
				foreach ($subHeadRecs as $shr) {
					$k++;
					$subHeadNextRec++;
					$damEntryId = $shr->id;
					$produced = $shr->produced;
					$stocked = $shr->stocked;
					$osSupply = ($shr->os_supply)?$shr->os_supply:"N";
					$osSale = ($shr->os_sale)?$shr->os_sale:"N";
					
					$formArr = $cfArr[$produced.$stocked.$osSupply.$osSale];
					$rIndx = $j."_".$k;

					$dacArr = array();
					$dacEntryId = "";
					if ($mainId) {
						$dacRec = $dailyactivitychartObj->getDAChartEntryRec($mainId, $damEntryId);
						$dacEntryId 		= $dacRec[0];
						$dacArr["CB"] 		= $dacRec[1];
						$dacArr["OB"] 		= ($dacRec[2]!=0)?$dacRec[2]:"";
						$dacArr["DIFF"] 	= $dacRec[3];
						$dacArr["PRODUCED"] 	= ($dacRec[4]!=0)?$dacRec[4]:"";
						$dacArr["PURCHASED"] 	= ($dacRec[5]!=0)?$dacRec[5]:"";
						$dacArr["USED"] 	= ($dacRec[6]!=0)?$dacRec[6]:"";
						$dacArr["OSSUPPLY"] 	= ($dacRec[7]!=0)?$dacRec[7]:"";
						$dacArr["OSSALE"] 	= ($dacRec[8]!=0)?$dacRec[8]:"";
					}
				?>
				<TD valign="top">
			<input type="hidden" name="damEntryId_<?=$rIndx?>" id="damEntryId_<?=$rIndx?>" value="<?=$damEntryId?>" readonly="true" />
			<input type="hidden" name="dacEntryId_<?=$rIndx?>" id="dacEntryId_<?=$rIndx?>" value="<?=$dacEntryId?>" readonly="true" />
					<table cellpadding="0" cellspacing="0">
					<TR>
						<TD>
							<?php
								if ($subHeadRecSize>1) {
							?>
							<fieldset>
							<legend class="listing-item"><?=$shr->sub_head_name?></legend>
							<?php
								}
							?>
							<table>
								<?php
									$m = 0;
									foreach ($formArr as $frK=>$frV) {
										$m++;
										$fa = $fieldArr[$frV];
										
										$txtBox = str_replace("[R]",$rIndx,$fa[1]);
										if ($mainId) $txtBox = str_replace("[V]",$dacArr[$frV],$txtBox);
										else $txtBox = str_replace("[V]","",$txtBox);
								?>
								<tr>
									<TD class="listing-item" nowrap="true">
										<?=$fa[0]?>
										<input type="hidden" name="hidRowVal_<?=$rIndx?>_<?=$m?>" id="hidRowVal_<?=$rIndx?>_<?=$m?>" value="<?=$frV?>" readonly="true" size="4" />	
									</Td>
									<TD class="listing-item"><?=$txtBox?></Td>
								</tr>
								<?php
									} // Field Loop ends here
								?>
							</table>
							<?php
								if ($subHeadRecSize>1) {
							?>
							</fieldset>
							<?php
								}
							?>
							<input type="hidden" name="fArrRowCount_<?=$rIndx?>" id="fArrRowCount_<?=$rIndx?>" value="<?=sizeof($formArr)?>" readonly="true" />
						</TD>
					</TR>
					</table>
				</TD>
				<?php
					if ($subHeadNextRec%$subHeadNumCol == 0) {
				?>
				</tr><tr>
				<?php
					}
				} // Sub head loop ends here
				?>
				</tr>
				</table>
			</fieldset>
	<input type="hidden" name="subHeadRowCount_<?=$j?>" id="subHeadRowCount_<?=$j?>" value="<?=$subHeadRecSize?>" readonly="true" />
			</TD>
			</TR>
			</table>
				</TD>
				<?php
					if ($nextRec%$numCol == 0) {
				?>
				</tr>
				</table>
				</td>
				</tr>
				<tr>
				<td>
					<table>
					<tr>
				<?php
					}
				} // Head Loop Ends here
				?>
			</tr>
		</table>
		</td>
		</tr>
		</table>
		<input type="hidden" name="dActHeadRowCount" id="dActHeadRowCount" value="<?=$damSettingHeadRecSize?>" readonly="true" />
	</TD>
</tr>
<!-- Dynamic Activity recording Ends here	 -->	
                </table></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyActivityChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateDailyActivityChart(document.frmDailyActivityChart);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyActivityChart.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save & Exit " onClick="return validateDailyActivityChart(document.frmDailyActivityChart);">												</td>

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
								<td  colspan="1" background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Activity Chart </td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyActivityChart.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
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
				<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" bgcolor="#999999">
				<?
				if (sizeof($dailyActivityChartRecords) > 0) {
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
					$nav.= " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
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
<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
<td class="listing-head" style="padding-left:5px; padding-right:5px;">Entry Date </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Entry Time</td>
<? if($edit==true){?>
<td class="listing-head" width="45" ></td>
<? }?>
			<?
			foreach($dailyActivityChartRecords as $dacr)
			{
				$i++;
				$dailyActivityChartMainId	=	$dacr[0];
				//$dailyActivityChartEntryId	=	$dacr[1];
				$entryDate = dateFormat($dacr[1]);
				$entryTime = $dacr[2];
			?>
<tr  bgcolor="WHITE"  >
<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyActivityChartMainId;?>" class="chkBox"><input type="hidden" name="dailyActivityChartEntryId_<?=$i;?>" value="<?=$dailyActivityChartEntryId?>"></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$entryDate;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$entryTime;?></td>
<? if($edit==true){?>
  <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyActivityChartMainId;?>,'editId');"></td>
  <? }?>
</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editDailyActivityChartEntryId" value="<?=$activityChartEntryId;?>">
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
					$nav.= " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyActivityChart.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
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
			} else {
		?>
	<tr bgcolor="white">
		<td colspan="15"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
		<?
			}
		?>
	</table>
	<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>" readonly="true">
	</td>
	</tr>
	<tr>
	<TD colspan="3">
	<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">	
<!--  List all main Rec-->
		<? if ($dailyActivityMainId!="") {?>
		<tr>
		<td colspan="2">Here display daily activity</td>
		</tr>		
		<? }?>		
		</table></TD></tr>
		<tr>
			<td colspan="3" height="5" ></td>
		</tr>
		<tr>
			<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyActivityChart.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }?></td>
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
	<?php
	if ($addMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		xajax_chkDupEntry(document.getElementById('selectDate').value, '<?=$mode?>', '<?=$mainId?>');	
	</script>	
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>