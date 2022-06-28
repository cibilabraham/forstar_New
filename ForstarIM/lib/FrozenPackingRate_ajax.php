<?php
//require_once("lib/databaseConnect.php");
//require_once("RMProcurmentOrder_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
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
	
	function getQEL($fishCategoryId, $fishId, $processCodeId, $selRowId, $rateListId, $windowType)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenPackingRateObj=	new FrozenPackingRate($databaseConnect);
		$qelRecs=$frozenPackingRateObj->fetchQELRecs($fishCategoryId, $fishId, $processCodeId);
		//$objResponse->alert("$fishCategoryId, $fishId, $processCodeId, $selRowId, $rateListId");
		$txtId = $fishCategoryId."_".$fishId."_".$processCodeId;

		//<a href="###" onclick="getGrade({fprRec.processcode_id}, {fprRec.freezing_stage_id}, {fprRec.quality_id}, {fprRec.frozencode_id}, {getRow():h})">Exception Rate</a>

		$displayQELR = '<table cellpadding="0" cellspacing="0" width="100%" id="tbl-nb">';
		$i = 0;
		foreach ($qelRecs as $qel) {
			$i++;
			$freezingStageId = $qel[8];
			$qualityId	 = $qel[9];
			$frozenCodeId 	 = $qel[10];
			
			$rateExist =$frozenPackingRateObj->chkRateExist($fishId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId);
			$frznPkgRateId = $rateExist[0];

			$gradeWiseRateExist = array();	
			$defaultRate = "";
			$numExptRate = "";
			$rateExptList = "";
			$disRateExpt = "";	
			if ($frznPkgRateId) {
				$gradeWiseRateExist = $frozenPackingRateObj->chkGradeWiseRateExist($frznPkgRateId);
				# Finding Default rate
				$defaultRate =$frozenPackingRateObj->getDefaultRate($frznPkgRateId);
				# get Exception rates
				list($numExptRate, $rateExptList) =$frozenPackingRateObj->displayFPRExpt($frznPkgRateId);
				//$objResponse->alert($numExptRate);
				if ($numExptRate!="") $disRateExpt = "<a href='###' onMouseover=\"ShowTip('$rateExptList');\" onMouseout=\"UnTip();\" class='link5'>$numExptRate</a> ";
			}

			$displayQELR .= '<tr>';
			$displayQELR .= '<td width="19%">'.$qel[4].'</td>';
			$displayQELR .= '<td width="19%">'.$qel[5].'</td>';
			$displayQELR .= '<td width="32%">'.$qel[6].'</td>';
			$displayQELR .= '<td width="8%" align="center">'.$defaultRate.'</td>';
			$displayQELR .= '<td width="10%" align="center">'.$disRateExpt.'</td>';
			/*
			$displayQELR .= '<td width="18%" align="right" style="padding-left:5px; padding-right:5px;">';
			$displayQELR .= '<input type="text" name="defaultRate_'.$txtId.'_'.$i.'" id="defaultRate_'.$i.'" value="" size="3" style="text-align:right;" autocomplete="off" onkeyup="chkModified(\''.$txtId.'\');">';
			$displayQELR .= '</td>';
			*/
			$displayQELR .= '<td width="20%">';
			if (sizeof($gradeWiseRateExist)>0) {
				$displayQELR .= '<a href="###" onclick="getGrade('.$processCodeId.','.$freezingStageId.','.$qualityId.','. $frozenCodeId.', '.$i.','.$rateListId.','.$fishId.',\''.$selRowId.'\','.$fishCategoryId.')">Edit</a>';
			} else {
				$displayQELR .= '<a href="###" onclick="getGrade('.$processCodeId.','.$freezingStageId.','.$qualityId.','. $frozenCodeId.', '.$i.','.$rateListId.','.$fishId.', \''.$selRowId.'\','.$fishCategoryId.')">Set</a>';
			}
			$displayQELR .= '</td>';
			$displayQELR .= '</tr>';
		}
		$displayQELR .= '<input type="hidden" name="itemRowCount" id="itemRowCount" value="'.$i.'" readonly></table>';
		
		if ($windowType=='CW') {
			$displayHTML = str_replace('\'','\\\'',$displayQELR);
			//$objResponse->alert($displayHTML);
			$objResponse->script("parent.refreshJS('$selRowId', '$displayHTML');");
		}
		if ($windowType=='PW') $objResponse->assign($selRowId, "innerHTML", $displayQELR);
		
		return $objResponse;
	}

	function addGrade($fishId, $processcodeId, $freezingStageId, $qualityId, $frozencodeId, $rateListId, $frznPkgRateId, $exptRate, $eType, $gArrStr, $selGroupEntry, $preProcessorId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenPackingRateObj=	new FrozenPackingRate($databaseConnect);
		$frozenPackingRateGradeObj=	new FrozenPackingRateGrade($databaseConnect);
		

		$fprGrade = array();
		if ($processcodeId && $freezingStageId && $frozencodeId) {
			
				# Save Recs
				//$frznPkgRateRecIns = $fpr_m->save($mvals);
				if($frznPkgRateId!='')
				{
					$frznPkgRateRecIns=$frozenPackingRateObj->updateFrozenPackRateId($frznPkgRateId,$fishId,$processcodeId,$freezingStageId,$qualityId,$frozencodeId,$rateListId);
				}
				else
				{
					$frznPkgRateRecIns=$frozenPackingRateObj->addFrozenPackRate($fishId,$processcodeId,$freezingStageId,$qualityId,$frozencodeId,$rateListId);
				}
				
				if ($frznPkgRateRecIns || $frznPkgRateId) {
					# get Last Inserted Id
					if (!$frznPkgRateId) $lastInsertedId =$databaseConnect->getLastInsertedId();
					else $lastInsertedId = $frznPkgRateId;	

					if ($lastInsertedId!=0) {
						if ($eType=='A') {				
							$recExist =$frozenPackingRateGradeObj->processorGradeCombExist($lastInsertedId, 0, $preProcessorId);
							if (!$recExist) $frznPkgRateGradeRecIns = $frozenPackingRateGradeObj->addFrozenPackRateGrade($lastInsertedId,'0',$exptRate,$preProcessorId);
							
						} else if ($eType=='E') {
							$gArr = explode(",", $gArrStr);
							$gradeId = "";
							for ($i=0;$i<sizeof($gArr);$i++) {
								$gradeId	= $gArr[$i];
								$recExist = $frozenPackingRateGradeObj->processorGradeCombExist($lastInsertedId, $gradeId, $preProcessorId);
								if (!$recExist) $frznPkgRateGradeRecIns = $frozenPackingRateGradeObj->addFrozenPackRateGrade($lastInsertedId,$gradeId,$exptRate,$preProcessorId);
							}
	
						}			
					} // Last Id chk ends here	
				}
			
		} // Main cond ends here

		return $objResponse;
	}
	
	function updateGrade($fishId, $processcodeId, $freezingStageId, $qualityId, $frozencodeId, $rateListId, $frznPkgRateId, $exptRate, $eType, $gArrStr, $selGroupEntry, $preProcessorId,$gpIdArrStr)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenPackingRateObj=	new FrozenPackingRate($databaseConnect);
		$frozenPackingRateGradeObj=	new FrozenPackingRateGrade($databaseConnect);
		

		$fprGrade = array();
		if ($processcodeId && $freezingStageId && $frozencodeId) {
			
				# Save Recs
				//$frznPkgRateRecIns = $fpr_m->save($mvals);
				if($frznPkgRateId!='')
				{
					$frznPkgRateRecIns=$frozenPackingRateObj->updateFrozenPackRateId($frznPkgRateId,$fishId,$processcodeId,$freezingStageId,$qualityId,$frozencodeId,$rateListId);
				}
				
				if ($frznPkgRateId) {
					# get Last Inserted Id
					 $lastInsertedId = $frznPkgRateId;	
					if ($lastInsertedId!=0) {
						if ($eType=='A') {	
							$recExistFrozen =$frozenPackingRateGradeObj->chckRateListExistInFrozenGrade($rateListId);
							if (!$recExistFrozen)
							{
								$delIns = $frozenPackingRateGradeObj->deleteFrznPkgRateGrade($gpIdArrStr);
								$recExist =$frozenPackingRateGradeObj->processorGradeCombExist($lastInsertedId, 0, $preProcessorId);
								if (!$recExist) $frznPkgRateGradeRecIns = $frozenPackingRateGradeObj->addFrozenPackRateGrade($lastInsertedId,'0',$exptRate,$preProcessorId);
							}
							/*$recExist =$frozenPackingRateGradeObj->processorGradeCombExist($lastInsertedId, 0, $preProcessorId);
							if (!$recExist) $frznPkgRateGradeRecIns = $frozenPackingRateGradeObj->addFrozenPackRateGrade($lastInsertedId,'0',$exptRate,$preProcessorId);*/
							
						} else if ($eType=='E') {

							$recExistFrozen =$frozenPackingRateGradeObj->chckRateListExistInFrozenGrade($rateListId);
							if(!$recExistFrozen)
							{
								$delIns = $frozenPackingRateGradeObj->deleteFrznPkgRateGrade($gpIdArrStr);
								$gArr = explode(",", $gArrStr);
								$gradeId = "";
								for ($i=0;$i<sizeof($gArr);$i++) {
									$gradeId	= $gArr[$i];
									$recExist = $frozenPackingRateGradeObj->processorGradeCombExist($lastInsertedId, $gradeId, $preProcessorId);
									if (!$recExist) $frznPkgRateGradeRecIns = $frozenPackingRateGradeObj->addFrozenPackRateGrade($lastInsertedId,$gradeId,$exptRate,$preProcessorId);
								}
							}
						}			
					} // Last Id chk ends here	
				}
			
		} // Main cond ends here

		return $objResponse;
	}





	$xajax->register(XAJAX_FUNCTION, 'getQEL', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'addGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'updateGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();
?>