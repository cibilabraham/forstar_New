<?php
require_once("lib/databaseConnect.php");
require_once("FrozenPackingQuickEntryList_class.php");
require_once("libjs/xajax_core/xajax.inc.php");
require_once("config.php");

	$xajax = new xajax();	
	//$xajax->configure('defaultMode', 'synchronous' ); // For return value
//$xajax->configure('statusMessages', true); // For display status
	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}	
		
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}

	# Get Process Code Records
	function getProcessCodeRecords($fishId, $rowId, $selPCId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$processcodeObj		=	new ProcessCode($databaseConnect);
		//$frznPkngQuickEntryListObj	=	new FrozenPackingQuickEntryList($databaseConnect);
		
		# Process Code Records
		$pcRecords = $processcodeObj->getProcessCodeRecs($fishId);
		$objResponse->addCreateOptions("selProcessCode_".$rowId, $pcRecords,$selPCId);		
		return $objResponse;			
	}

	function insertGradeRecs($userId, $selProcesscodes, $gradeQELId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		$noGradeRec = false;
		$objResponse->assign("hidArrangeGrade","value","0");
		//Just Hide 	
		# Get Grade Records
		$getGradeRecords	= $frznPkngQuickEntryListObj->getPCGradeRecords($selProcesscodes);

		# If QEL ID Exist
		if (sizeof($getGradeRecords)>0 && $gradeQELId!="") {
			$selGradeArr = array();
			$getSelGradeRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords($userId, $gradeQELId);	
			if (sizeof($getSelGradeRecords)==0) $noGradeRec = true;
			$nSelGradeArr = array();
			foreach ($getGradeRecords as $gr) {
				$nsGradeId = $gr[0];
				$selGradeArr[$nsGradeId] = 0;
				$nSelGradeArr[$nsGradeId] = 0;
			}
			$sGradeArr = array();
			$gradeStatusArr = array();
			foreach ($getSelGradeRecords as $cRec) {
				$sGradeId = $cRec[0];
				$displayOrderId = $cRec[3];
				$gradeStatus = $cRec[5];
				$selGradeArr[$sGradeId] = $displayOrderId;
				$sGradeArr[$sGradeId] = $displayOrderId;
				$gradeStatusArr[$sGradeId] = $gradeStatus;
			}
			arsort($selGradeArr);
			// Function from config (Array Diff
			$searchArr = arr_diff($sGradeArr,$nSelGradeArr);

			if (sizeof($selGradeArr)>0) {
				# Delete Blank Recs
				$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $gradeQELId);
				foreach ($selGradeArr as $sGradeId=>$selDisOrderId) {	
					$disOrderId = "";
					$gradeStatus = "";		
					if (array_key_exists($sGradeId, $searchArr)) {
						if ($selDisOrderId==0) {
							$disOrderId = $frznPkngQuickEntryListObj->getMaxDisplayOrderId($gradeQELId);
							$cDisOrderId = $disOrderId+1;
							$insRec = $frznPkngQuickEntryListObj->addGradeRec($gradeQELId, $sGradeId, $cDisOrderId, $userId);
						} else {
							$gradeStatus = $gradeStatusArr[$sGradeId];
							$insRec = $frznPkngQuickEntryListObj->addGradeRec($gradeQELId, $sGradeId, $selDisOrderId, $userId, $gradeStatus);
						}
					}
				}
			}
			$objResponse->script("sortGraeR('$userId', '$gradeQELId', '$selProcesscodes');");
			$objResponse->assign("hidArrangeGrade","value","1");
		} # Chk $gradeQELId Ends Here

		
		# If QEL Id Empty
		if (sizeof($getGradeRecords)>0 && ($gradeQELId=="" || $noGradeRec)) {			
			# Delete Blank Recs
			$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $gradeQELId);
			$g = 0;
			foreach ($getGradeRecords as $gr) {
				$g++;
				$gradeId = $gr[0];
				$gradeCode = $gr[1];
				$insRec = $frznPkngQuickEntryListObj->addGradeRec($gradeQELId, $gradeId, $g, $userId);
			}
			$objResponse->script("sortGraeR('$userId','', '$selProcesscodes');");
			$objResponse->assign("hidArrangeGrade","value","1");
		}
		return $objResponse;
	}
	

	# Get Grade Recs For Arrange
	function getGradeRecsForArrange($userId, $qelEntryId, $selProcesscodes)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();		
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		
		# Get Grade Records
		$getGradeRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords($userId, $qelEntryId);
		$gradeRecSize 		= sizeof($getGradeRecords);
		
		# Insert Temp Process code recs
		$insertPCRecs		= $frznPkngQuickEntryListObj->insertTempPCRecs($selProcesscodes, $userId);
		//$pcSize = $frznPkngQuickEntryListObj->getTempPCRecs($userId);		
	
		if ($gradeRecSize>0) {			
			$dispalyGrade = "<table  cellspacing='1' bgcolor='#999999' cellpadding='3' id='arrangeTable'>";
			$dispalyGrade .= "<tr bgcolor='white' align='right'><td colspan='6' nowrap style='padding-left:5px; padding-right:5px;'><input type='button' value=' Save Sort Order ' name='btn' class='button' onClick=\"updateGdOrder('$userId', '$qelEntryId', '$selProcesscodes');\" style='width:120px;'></td></tr>";
			$dispalyGrade .= "<tr bgcolor='#f2f2f2' align='center'>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true'>Current<br/> Position</td>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true'>Grade</td>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true'>Manual Sort</td>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true'>Quick Sort<br/><!--a title='Save Order' href='###' onclick=\"updateGdOrder('$userId', '$qelEntryId', '$selProcesscodes');\"><img src='images/filesave.png' border='0' title='Save Order' ></a--></td>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true'>Use In <br>Quickentry<!--Active/<br>Inactive--><br>
				<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick=\"checkAll(this.form,'gradeStatus_');\" class=\"chkBox\">
			</td>";
			$dispalyGrade .= "<td class='listing-head' style='padding-left:5px;padding-right:5px;' nowrap='true' onMouseover=\"ShowTip('Process Codes that do not have grade');\" onMouseout=\"UnTip();\">
				Process<br> Codes
			</td>";
			//$dispalyGrade .= "<td class='listing-head' style='padding-left:5px; padding-right:5px;' colspan='2'>Display Order</td>";
			$dispalyGrade .= "</tr>";	
			//$dispalyGrade .= "<tr bgcolor='#f2f2f2' align='center'>";
			//$dispalyGrade .= "<td align='center' class='listing-head' style='padding-left:5px; padding-right:5px;'>Move Up</td>";
			//$dispalyGrade .= "<td class='listing-head' style='padding-left:5px; padding-right:5px;'>Move Down</td>";
			//$dispalyGrade .= "</tr>";		
					
			for ($g=1;$g<=sizeof($getGradeRecords);$g++) {
				$cRec = $getGradeRecords[$g-1]; // Current Record
				$nRec   = $getGradeRecords[$g]; // Next Record
				$pRec   = $getGradeRecords[$g-2]; // Prev Rec
				# Display Settings	
				/* Original
					$disOrderUp = "$pRec[2]-$cRec[3];$cRec[2]-$pRec[3]";// Pass URL value
					$disOrderDown = "$nRec[2]-$cRec[3];$cRec[2]-$nRec[3]";
					$disOrderUp = "$pRec[2]-$cRec[3];$cRec[2]-$pRec[3]";// Pass URL value
					$disOrderDown = "$nRec[2]-$cRec[3];$cRec[2]-$nRec[3]";
				*/
				$curentOrderId = $g;
				$nextOrderId = $g+1;
				$prevOrderId = $g-1;
				$disOrderUp = "$pRec[2]-$curentOrderId;$cRec[2]-$prevOrderId";// Pass URL value
				$disOrderDown = "$nRec[2]-$curentOrderId;$cRec[2]-$nextOrderId";
				
				$gradeId = $cRec[0];
				
				# Get Grade not selected PC recs 
				$gnsPCRecs = $frznPkngQuickEntryListObj->getGradeNotSelPC($gradeId, $userId);
				//$objResponse->alert("$gradeId, $userId::Size=".sizeof($gnsPCRecs));				

				$gradeCode = $cRec[1];
				$gradeEntryId = $cRec[2];
				$displayOrderId = $cRec[3];

				$gradeQELId	= $cRec[4];
				
				$gradeStatus = ($cRec[5]=='Y')?"checked":"";
				
				$dispalyGrade .= "<tr bgcolor='WHITE' id='trg_$g'>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px;padding-right:5px;' align='center'>$g</td>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:10px;padding-right:10px;'><input type='hidden' name='gradeId_$g' value='$gradeId'>$gradeCode</td>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:10px;padding-right:10px;' align='Center'>
				<table cellpadding='0' cellspacing='0'>
					<tr>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px;' align='center'>";
					if ($g>1 && $g!=$gradeRecSize) {
				$dispalyGrade .= "<a href=\"javascript:changeDisplay('$disOrderUp', '$userId', '$gradeQELId', '$selProcesscodes');\" class='displayArrow'><img src='images/arrow_up.gif' border='0' title='Move Up'></a>";
					}
					if ($g==$gradeRecSize) {
				$dispalyGrade .= "<a href=\"javascript:changeDisplay('$disOrderUp', '$userId', '$gradeQELId', '$selProcesscodes');\" class='displayArrow'><img src='images/arrow_up.gif' border='0' title='Move Up'></a>";
					 }
				$dispalyGrade .= "</td>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px;' align='center'>";
					if ($g==1) {
				$dispalyGrade .= "<a href=\"javascript:changeDisplay('$disOrderDown', '$userId', '$gradeQELId', '$selProcesscodes');\" class='displayArrow'><img src='images/arrow_down.gif' border='0' title='Move Down'></a>";
					}
					if ($g>1 && $g!=$gradeRecSize) {
				$dispalyGrade .= "<a href=\"javascript:changeDisplay('$disOrderDown', '$userId', '$gradeQELId', '$selProcesscodes');\" class='displayArrow'><img src='images/arrow_down.gif' border='0' title='Move Down'></a>";
					}
				$dispalyGrade .= "</td>";
		$dispalyGrade  .= "	</tr>
					</table>
				</td>";
				$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px;padding-right:5px;' align='center'>
				<table>	
					<tr><td align='center'>
						<input type='text' name='displayOrderId_$g' id='displayOrderId_$g' value='$g' size='3' style='text-align:center;' autocomplete='off' onkeydown=\"return nextBox(event,'document.frmFrozenPackingQuickEntryList','displayOrderId_".($g+1)."');\" onkeyup=\"chkDupSortOrder();\">
						<input type='hidden' name='hidDisplayOrderId_$g' id='hidDisplayOrderId_$g' value='$g' size='3'>
						<input type='hidden' name='hidGradeEntryId_$g' id='hidGradeEntryId_$g' value='$gradeEntryId' size='3' style='text-align:center;'>
					</td></tr>
				</table>
				</td>";
			$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px;padding-right:5px;' align='center'>
				<input type='checkbox' name='gradeStatus_$g' id='gradeStatus_$g' value='Y' class='chkBox' $gradeStatus />
			</td>";
			$dispalyGrade .= "<td class='listing-item' nowrap style='padding-left:5px;padding-right:5px;' align='center'>";
		# List Process Codes
			$dispalyGrade	.= "<table><tr>";
				$numLine = 4;
				if (sizeof($gnsPCRecs)>0) {
					$nextRec	=	0;					
					$selName = "";
					foreach ($gnsPCRecs as $zr) {					
						$selName = $zr[2];
						$sPCId   = $zr[3];
						$nextRec++;
		$dispalyGrade	.= "<td class='listing-item' style='line-height:normal'>";
					 if ($nextRec>1) {
		$dispalyGrade	.=  ",";	
					}
		$dispalyGrade	.= "<a href='###'  onMouseover=\"ShowTip('Click here to add grade to Process Codes');\" onMouseout=\"UnTip();\" onclick=\" return addGradeToPC('$sPCId', '$gradeId', '$userId', '$qelEntryId', '$selProcesscodes');\" class='link4'>$selName</a></td>";
				   	 if($nextRec%$numLine == 0) { 
		$dispalyGrade	.= "</tr><tr>";
					  }	
					}
				} 
		$dispalyGrade	.= "</tr></table>";	
		$dispalyGrade .= " </td>";
				
				$dispalyGrade .= "</tr>";
			}
			$dispalyGrade .= "<input type='hidden' name='hidGradeRowCount' id='hidGradeRowCount' value='$g'/></table>";
		}	
		//sleep(1);
		$displayErrTble ="<table  cellspacing='1' bgcolor='#999999' cellpadding='3' id='arrangeTable'><tr bgcolor='white'><td class='err1'>No grade record exist.<input type='hidden' name='hidGradeRowCount' id='hidGradeRowCount' value='0'/></td></tr></table>";

		if ($qelEntryId!="") $objResponse->script("displaySortArrBtn();");
		if ($gradeRecSize>0) $objResponse->assign("gradeRecs","innerHTML",$dispalyGrade);
		else $objResponse->assign("gradeRecs","innerHTML",$displayErrTble);
		$objResponse->assign("gradeRecSize","value", $gradeRecSize);	
		//$objResponse->setReturnValue('Hello World');	
		//sleep(1);		
		return $objResponse;
	}

	# Change Display order
	function changeDisplayOrder($displayChangeId, $userId, $gradeQELId, $selProcesscodes)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		$updateDisplayOrder = $frznPkngQuickEntryListObj->changeDisplayOrder($displayChangeId);
		
		$objResponse->script("sortGraeR('$userId', '$gradeQELId', '$selProcesscodes');");
		//sleep(1);
		return $objResponse;
	}
	
	# Update Display Order
	function updateDisplayOrder($entryId, $displayOrder)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);		
		$updateDisplayOrder = $frznPkngQuickEntryListObj->updateQELDisplayOrder($entryId, $displayOrder);
		return $objResponse;
	}

	function delGradeRec($userId, $gradeQELId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);		
		$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $gradeQELId);
		return $objResponse;
	}

	function getSelPCGradeCount($selProcesscodes, $userId, $gradeQELId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		$getGradeRecords = $frznPkngQuickEntryListObj->getPCGradeRecords($selProcesscodes);
		# If QEL ID Exist
		if (sizeof($getGradeRecords)>0 && $gradeQELId!="") {
			$selGradeArr = array();
			# Get Sel Grade Recs
			$getSelGradeRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords($userId, $gradeQELId);
			$nSelGradeArr = array();
			$k = 0;
			foreach ($getGradeRecords as $gr) {
				$nsGradeId = $gr[0];				
				$nSelGradeArr[$k] = $nsGradeId;
				$k++;
			}
			$sGradeArr = array();
			$m = 0;
			foreach ($getSelGradeRecords as $cRec) {
				$sGradeId = $cRec[0];
				$sGradeArr[$m] = $sGradeId;
				$m++;
			}
			$searchArr = array_diff($nSelGradeArr,$sGradeArr);
			
		} # Chk $gradeQELId Ends Here		
		$objResponse->assign("selGradeRecSize","value",sizeof($getGradeRecords));
		$objResponse->assign("selGradeRecSizeDiff","value",sizeof($searchArr));
		
		return $objResponse;
	}

	# Update Full set grade recs
	function updateFullSetGradeRecs($userId, $selProcesscodes, $gradeQELId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		
		# Get All PC Wise Grade Records
		$getGradeRecords = $frznPkngQuickEntryListObj->getPCGradeRecords($selProcesscodes);
		# If QEL ID Exist
		if (sizeof($getGradeRecords)>0 && $gradeQELId!="") {
			$selGradeArr = array();
			# Get Sel Grade Recs
			$getSelGradeRecords	= $frznPkngQuickEntryListObj->getSelGradeRecords($userId, $gradeQELId);
			$nSelGradeArr = array();
			foreach ($getGradeRecords as $gr) {
				$nsGradeId = $gr[0];
				$selGradeArr[$nsGradeId] = 0;
				$nSelGradeArr[$nsGradeId] = 0;
			}
			$sGradeArr = array();
			$gradeStatusArr = array();
			foreach ($getSelGradeRecords as $cRec) {
				$sGradeId = $cRec[0];
				$displayOrderId = $cRec[3];
				$gradeStatus = $cRec[5];
				$selGradeArr[$sGradeId] = $displayOrderId;
				$sGradeArr[$sGradeId] = $displayOrderId;
				$gradeStatusArr[$sGradeId] = $gradeStatus;
			}
			arsort($selGradeArr);
			// Function from config (Array Diff
			$searchArr = arr_diff($sGradeArr,$nSelGradeArr);

			if (sizeof($selGradeArr)>0) {
				# Delete Blank Recs
				$delTempGradeRec = $frznPkngQuickEntryListObj->delTempGradeRec($userId, $gradeQELId);
				foreach ($selGradeArr as $sGradeId=>$selDisOrderId) {	
					$disOrderId = "";
					$gradeStatus = "";		
					if (array_key_exists($sGradeId, $searchArr)) {
						if ($selDisOrderId==0) {
							$disOrderId = $frznPkngQuickEntryListObj->getMaxDisplayOrderId($gradeQELId);
							$cDisOrderId = $disOrderId+1;
							$insRec = $frznPkngQuickEntryListObj->addGradeRec($gradeQELId, $sGradeId, $cDisOrderId, $userId);
						} else {
							$gradeStatus = $gradeStatusArr[$sGradeId];
							$insRec = $frznPkngQuickEntryListObj->addGradeRec($gradeQELId, $sGradeId, $selDisOrderId, $userId, $gradeStatus);
						}
					}
				}
			}
		} # Chk $gradeQELId Ends Here

		return $objResponse;
	} // Update Function Ends here



	# Update Full Grade Recs
	function updateFullGradeSet($userId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);

		# Get All Records
		$getQuickEntryRecs = $frznPkngQuickEntryListObj->fetchAllRecords();
		foreach ($getQuickEntryRecs as $qelr) {
			$fznPkgQEListId = $qelr[0];
			# Get Sel Process Codes
			$getRawDataRecs = $frznPkngQuickEntryListObj->getQELRawRecords($fznPkgQEListId);
			
			$j=0;
			$selProcesscodes = "";
			$spCodeArr= array();
			foreach ($getRawDataRecs as $rdr) {
				$qelEntryId = $rdr[0];
				$sFishId    = $rdr[1];	
				$sProcessCodeId = $rdr[2];
				$spCodeArr[$j] = $sProcessCodeId;
				$j++;
			} // Get Raw Data Recs
			$selProcesscodes = implode(",", $spCodeArr);
	
			# Update All Grade Recs			
			updateFullSetGradeRecs($userId, $selProcesscodes, $fznPkgQEListId);

		} // Full Set Grade Loop Ends here
		sleep(2);
		$objResponse->alert("Successfully updated all Quick Entry Grade set.");
		$objResponse->script("document.getElementById('frmFrozenPackingQuickEntryList').submit();");	
		return $objResponse;		
	}

	function addGradeToProcessCode($processCodeId, $gradeId, $userId, $gradeQELId, $selProcesscodes)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		$addGradeToPC	= $frznPkngQuickEntryListObj->addGradeToPC($processCodeId, $gradeId);
		if ($addGradeToPC) $objResponse->script("sortGraeR('$userId', '$gradeQELId', '$selProcesscodes');");
		return $objResponse;
	}

	# Get Brand Recs
	function getBrandRecs($customerId, $selBrandId)
	{
		$objResponse 	 = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		//$frznPkngQuickEntryListObj 	= new FrozenPackingQuickEntryList($databaseConnect);
		$brandObj			= new Brand($databaseConnect);
		# get Recs
		$brandRecs     = $brandObj->getBrandRecords($customerId);
		$objResponse->addCreateOptions("brand", $brandRecs, $selBrandId);
		return $objResponse;
	}

	function getSecondaryGrade($secondaryId,$inputId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frznPkngQuickEntryListObj = new FrozenPackingQuickEntryList($databaseConnect);
		$secondaryGrade	= $frznPkngQuickEntryListObj->getSecondaryGrade($secondaryId);
		if (sizeof($secondaryGrade)>0) addDropDownOptions("selGrade_$inputId", $secondaryGrade, $cel, $objResponse);
		
		return $objResponse;
	}

//$xajax->registerFunction("getSelPCGradeCount");
$xajax->registerFunction("addGradeToProcessCode");
$xajax->register(XAJAX_FUNCTION, 'getSecondaryGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSelPCGradeCount', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCodeRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION, 'getProcessCodeRecords');
$xajax->register(XAJAX_FUNCTION,'getProcessCodeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getGradeRecsForArrange', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'insertGradeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'changeDisplayOrder', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateDisplayOrder', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'delGradeRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateFullGradeSet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateFullSetGradeRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecs', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getBrandRecords', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->processRequest(); // xajax end
//$xajax->ProcessRequest();
?>