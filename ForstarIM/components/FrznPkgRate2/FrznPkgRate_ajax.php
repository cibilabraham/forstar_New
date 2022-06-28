<?php
require_once("libjs/xajax_core/xajax.inc.php");
require_once 'components/base/FrznPkgRate_model.php';
require_once 'components/base/FrznPkgRateGrade_model.php';
require_once 'components/base/FishMaster_model.php';

$xajax = new xajax();	
$xajax->configure( 'debug', false ); 

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
	}

	# installed capacity Exist
	function getFishList($categoryId, $selFishId)
	{
		$objResponse 	= new NxajaxResponse();
		$fm_m 		= new FishMaster_model();
		
		$fishMasterRecs = $fm_m->findAllForSelect("id", "name", "--Select All--", " category_id='".$categoryId."'");

		$objResponse->addCreateOptions('fishId', $fishMasterRecs, $selFishId);
		return $objResponse;
	}

	/**
	# Get QEL Recs
	@param $windowType: PW: Parent Window/ CW : Child Window
	**/
	function getQEL($fishCategoryId, $fishId, $processCodeId, $selRowId, $rateListId, $windowType)
	{
		$objResponse 	= new NxajaxResponse();
		$fpr_m 		= new FrznPkgRate_model();
		$qelQry		= $fpr_m->fetchQELRecs($fishCategoryId, $fishId, $processCodeId);
		$qelRecs 	= $fpr_m->queryAll($qelQry);
		
		//$objResponse->alert("$fishCategoryId, $fishId, $processCodeId, $selRowId, $rateListId");
		$txtId = $fishCategoryId."_".$fishId."_".$processCodeId;

		//<a href="###" onclick="getGrade({fprRec.processcode_id}, {fprRec.freezing_stage_id}, {fprRec.quality_id}, {fprRec.frozencode_id}, {getRow():h})">Exception Rate</a>

		$displayQELR = '<table cellpadding="0" cellspacing="0" width="100%" id="tbl-nb">';
		$i = 0;
		foreach ($qelRecs as $qel) {
			$i++;
			$freezingStageId = $qel->freezing_stage_id;
			$qualityId	 = $qel->quality_id;
			$frozenCodeId 	 = $qel->frozencode_id;
			
			$rateExist = $fpr_m->chkRateExist($fishId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId);
			$frznPkgRateId = $rateExist->frznpackrateid;

			$gradeWiseRateExist = array();	
			$defaultRate = "";
			$numExptRate = "";
			$rateExptList = "";
			$disRateExpt = "";	
			if ($frznPkgRateId) {
				$gradeWiseRateExist = $fpr_m->chkGradeWiseRateExist($frznPkgRateId);
				# Finding Default rate
				$defaultRate = $fpr_m->getDefaultRate($frznPkgRateId);
				# get Exception rates
				list($numExptRate, $rateExptList) = $fpr_m->displayFPRExpt($frznPkgRateId);
				if ($numExptRate!="") $disRateExpt = "<a href='###' onMouseover=\"ShowTip('$rateExptList');\" onMouseout=\"UnTip();\" class='link5'>$numExptRate</a> ";
			}

			$displayQELR .= '<tr>';
			$displayQELR .= '<td width="19%">'.$qel->freezingstage.'</td>';
			$displayQELR .= '<td width="19%">'.$qel->qualityname.'</td>';
			$displayQELR .= '<td width="32%">'.$qel->frozencode.'</td>';
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

	
	/**
	* Desc: This function will insert and update Frozen packing rate 
	* @param $fishId: Fish Id, @param $processcodeId: Process code Id , @param $freezingStageId: Freezing stage Id, @param $qualityId: Quality Id, @param $frozencodeId: Frozen Code Id, @param $rateListId: Rate List Id, @param $frznPkgRateId: Frozen Packing Rate main Entry Id, @param $exptRate: Rate, $eType: A(All Grade)/E(Exception type), @param $gArrStr: Exception sel comma sep grade id , @param $selGroupEntry: Edited coma seperated Frozen Packing rate grade entry id, @param $preProcessorId: Processor Id
	* return value: updating table 
	**/
	function addGrade($fishId, $processcodeId, $freezingStageId, $qualityId, $frozencodeId, $rateListId, $frznPkgRateId, $exptRate, $eType, $gArrStr, $selGroupEntry, $preProcessorId)
	{
		$objResponse 	= new NxajaxResponse();
		$fpr_m 		= new FrznPkgRate_model();
		$fprg_m 	= new FrznPkgRateGrade_model(); // grade

		//$objResponse->alert("$fishId, $processcodeId, $freezingStageId, $qualityId, $frozencodeId, $rateListId, $frznPkgRateId, $exptRate, $eType, $gArrStr");

		$mvals = array();
		$mvals["FrznPkgRate"]["fish_id"] = $fishId;
		$mvals["FrznPkgRate"]["process_code_id"] = $processcodeId;
		$mvals["FrznPkgRate"]["freezing_stage_id"] = $freezingStageId;
		$mvals["FrznPkgRate"]["quality_id"] = $qualityId;
		$mvals["FrznPkgRate"]["frozen_code_id"] = $frozencodeId;
		$mvals["FrznPkgRate"]["rate_list_id"] = $rateListId;
		if ($frznPkgRateId) $mvals["FrznPkgRate"]["id"] = $frznPkgRateId;

		$fprGrade = array();
		if ($processcodeId && $freezingStageId && $frozencodeId) {
			if (!$selGroupEntry) {
				# Save Recs
				$frznPkgRateRecIns = $fpr_m->save($mvals);
				if ($frznPkgRateRecIns || $frznPkgRateId) {
					# get Last Inserted Id
					if (!$frznPkgRateId) $lastInsertedId = $fpr_m->getLastInsertedId();
					else $lastInsertedId = $frznPkgRateId;	

					if ($lastInsertedId!=0) {
						if ($eType=='A') {				
							$fprGrade["FrznPkgRateGrade"]["pkg_rate_entry_id"] = $lastInsertedId;
							$fprGrade["FrznPkgRateGrade"]["grade_id"] = 0;
							$fprGrade["FrznPkgRateGrade"]["rate"] = $exptRate;
							$fprGrade["FrznPkgRateGrade"]["pre_processor_id"] = $preProcessorId;
							$recExist = $fprg_m->processorGradeCombExist($lastInsertedId, 0, $preProcessorId);
							if (!$recExist) $frznPkgRateGradeRecIns = $fprg_m->save($fprGrade);
						} else if ($eType=='E') {
							$gArr = explode(",", $gArrStr);
							$gradeId = "";
							for ($i=0;$i<sizeof($gArr);$i++) {
								$gradeId	= $gArr[$i];
								$fprGrade["FrznPkgRateGrade"]["pkg_rate_entry_id"] = $lastInsertedId;
								$fprGrade["FrznPkgRateGrade"]["grade_id"] = $gradeId;
								$fprGrade["FrznPkgRateGrade"]["rate"] = $exptRate;
								$fprGrade["FrznPkgRateGrade"]["pre_processor_id"] = $preProcessorId;

								$recExist = $fprg_m->processorGradeCombExist($lastInsertedId, $gradeId, $preProcessorId);
								if (!$recExist) $frznPkgRateGradeRecIns = $fprg_m->save($fprGrade);
							}
	
						}			
					} // Last Id chk ends here	
				}
			}  # Update section
			else if ($selGroupEntry) {
				$fprEntryArr = explode(",", $selGroupEntry);
					for ($i=0;$i<sizeof($fprEntryArr);$i++) {
						$fprGrade["FrznPkgRateGrade"]["id"] = $fprEntryArr[$i];
						//$fprGrade["FrznPkgRateGrade"]["grade_id"] = $gArr[$i];;
						$fprGrade["FrznPkgRateGrade"]["rate"] = $exptRate;
						$frznPkgRateGradeRecIns = $fprg_m->save($fprGrade);
					}	
			}
		} // Main cond ends here

		return $objResponse;
	}
	

$xajax->register(XAJAX_FUNCTION, 'getFishList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getQEL', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addGrade', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>