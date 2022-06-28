<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/FrznPkgRate_model.php';
require_once 'components/base/FishCategory_model.php';
require_once 'components/base/FishMaster_model.php';
require_once 'components/base/frzn_pkg_rate_list_model.php';
require_once 'components/base/FrznPkgRateGrade_model.php';
require_once 'components/base/GradeMaster_model.php';


class FrznPkgRate_controller extends AFController
{
	protected $templateFolder = "FrznPkgRate";
	var $currentUrl	= "FrozenPackingRate.php";  // Mandatory
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Frozen Packing Rate added successfully.";
	var $msg_failAdd		=	"Failed to add Frozen Packing Rate.";
	var $msg_succUpdate		=	"Successfully updated the Frozen Packing Rate.";
	var $msg_failUpdate		=	"Failed to update the Frozen Packing Rate.";
	var $msg_succDel		=	"Frozen Packing Rate deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Frozen Packing Rate.";

	var $url_afterAdd		=	"FrozenPackingRate.php";
	var $url_afterUpdate		=	"FrozenPackingRate.php";
	var $url_afterDel		=	"FrozenPackingRate.php";

  	var $label_edit			=	"Edit Frozen Packing Rate";
  	var $label_add			=	"Add New Frozen Packing Rate";
	var $msg_NoRecs			= 	"No records found.";
	var $fpr_m;
	var $editId;	
	var $rateListId;
	var $divContainerId ;
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->fpr_m = new FrznPkgRate_model();

		# Frzn Pkg Rate List
		$this->fprl_m 	= new frzn_pkg_rate_list_model();
		$this->rateListId = $this->fprl_m->latestRateList();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/FrznPkgRate/FrozenPackingRate.js";

		//echo "h==>".$this->rateListId;
		// Rate List creation starts here
		if (!$this->rateListId) {
			$fpRLArr = array();
			$fpRLArr["FrznPkgRateList"]["created"] = "NOW()";
			$fpRLArr["FrznPkgRateList"]["created_by"] = $this->userId;
			$fpRLArr["FrznPkgRateList"]["name"] = "Frzn-PKg-Rate-List"."-".date("dMy");
			$fpRLArr["FrznPkgRateList"]["start_date"] =  date("Y-m-d");
			if ($this->fprl_m->save($fpRLArr)) {
				$this->rateListId = $this->fprl_m->latestRateList();
			}			
		}
		// Rate List creation ends here
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;
		//print_r($this->data);
		$fishCategoryId = $this->argsArr["fishCategory"];
		$fishId = $this->argsArr["fishId"];

		if ($this->argsArr["cmdSearch"]!="") {				
			//$this->fprQuery = "select * from m_frzn_pkg_rate";		
			$this->fprQuery = $this->fpr_m->getPCRecs($fishCategoryId, $fishId);
			//print_r($this->fprQuery);
			$this->maxpage = ceil($this->fpr_m->getCount($this->fprQuery)/$this->limit);
			if ($this->maxpage>1) $this->displayNavRow = true;
			
			$this->fprRecs = $this->fpr_m->queryAll($this->fprQuery, $this->offset,$this->limit);			
			$this->fprRecSize = sizeof($this->fprRecs);
		}

		

		
		# Fill Drop Downs		
		$this->fillDropDowns($fishCategoryId, $fishId);	
		
		// now load template to use for this function
		$this->useTemplate("FrznPkgRate.html");

		// finally render the template
		$this->render($this->elements);
	}

	
	function save()
	{
		# Frzn Pkg Rate Grade
		$this->fprg_m 	= new FrznPkgRateGrade_model();
		# grade master 
		$this->grade_m 	= new GradeMaster_model();
		
		$mData = AFProcessor::preprocessMultiple($this->argsArr);	
		foreach ($mData as $key => $mvals)
		{
			$rec = $mvals["FrznPkgRate"];
			$mvals["FrznPkgRate"]["rate_list_id"] = $this->rateListId;

			$fishId 		= $rec["fish_id"];
			$processCodeId		= $rec["process_code_id"];
			$freezingStageId 	= $rec["freezing_stage_id"];
			$qualityId		= $rec["quality_id"];
			$frozenCodeId		= $rec["frozen_code_id"];
    			$defaultRate		= $rec["default_rate"];
			$fprGrade		= array();
			
			# Insert Rec
			if ($processCodeId && $freezingStageId && $qualityId && $frozenCodeId) {
				# Save Recs
				$frznPkgRateRecIns = $this->fpr_m->save($mvals);
				if ($frznPkgRateRecIns) {
					# get Last Inserted Id
					$lastInsertedId = $this->fpr_m->getLastInsertedId();
					$fprGrade["FrznPkgRateGrade"]["pkg_rate_entry_id"] = $lastInsertedId;

					$rowR = $processCodeId."_".$freezingStageId."_".$qualityId."_".$frozenCodeId;
					$exptCombRate = $this->argsArr["frznPkgExceptionRate_".$rowR];
					$eCmbArr = explode("||", $exptCombRate); // Combination split using ||
					for ($i=0; $i<sizeof($eCmbArr); $i++) {
						$gCmb 		= $eCmbArr[$i];
						if ($gCmb!="") {
							$gCmbArr = explode(":", $gCmb); //Rate split
							$gCmbGradeArr 	= explode(",", $gCmbArr[0]);  //Grade split
							$gCmbGradeRate	= $gCmbArr[1];
							$fprGrade["FrznPkgRateGrade"]["rate"] = $gCmbGradeRate;

							for ($j=0; $j<sizeof($gCmbGradeArr); $j++) {
								$exptGrade = $gCmbGradeArr[$j];
								if ($lastInsertedId!=0) {
									$gRec = $this->grade_m->find("code='$exptGrade'");
									$gradeId = $gRec->id;
									$fprGrade["FrznPkgRateGrade"]["grade_id"] = $gradeId;

									if (sizeof($fprGrade)>0) {
										$frznPkgRateGradeRecIns = $this->fprg_m->save($fprGrade);
									}
								} // Last is chk ends here 
							} // Grade loop ends here
						} // cmb Empty chk ends here
					} // Cmb Split chk Ends here
				} // Ins Chk ends here				
			} // Insert Ends here			
		} // PreProcess data Ends here

		$succMsg = "";
		$errMsg  = "";
		if (!isset($this->data["FrznPkgRate"]["id"])) {
			$this->data["FrznPkgRate"]["created"] = "NOW()";
			$this->data["FrznPkgRate"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}






	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;
		//"Add Mode";
		$this->fillDropDowns();	
	}	
	

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;
		// Fill Drop Downs;
		$this->fillFields();
		$this->editId = $this->argsArr["editId"];
		
		$rec	= $this->fpr_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("FrznPkgRate", $rec);
	}


	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->fpr_m->deleteMultiple($mData);
		if ($recDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDel);
			//$sessObj->createSession("nextPage",$url_afterDel.$selection);
		} else 	$this->errDel	=	$this->msg_failDel;
	}

	function printList()
	{
		$this->printMode = true;
		$this->addOnLoadFn('window.print();');
	}

	# Drop Down Section
	function fillDropDowns($fishCategoryId=null, $fishId=null)
	{
		# Fish Category 
		$this->fc_m 	= new FishCategory_model();
		$this->fcRecs 	= $this->fc_m->findAll(array(ORDER=>"category asc",WHERE=>"active=1"));
		$this->fcRecSize = sizeof($this->fcRecs);
		//$this->ar  = array("0"=>"A", "1"=>"B", "2"=>array("0"));
		/*
		echo "<pre>";
		print_r($this->fcRecs);
		echo "</pre>";
		*/

		$fcRecs = $this->fc_m->findAllForSelect("id", "category", "--Select--");
		$this->elements['fishCategory'] = new HTML_Template_Flexy_Element;
		$this->elements['fishCategory']->setOptions($fcRecs);		
		if ($fishCategoryId) $this->elements['fishCategory']->setValue($fishCategoryId);

		# Fish Master recs
		$this->fm_m = new FishMaster_model();
		$fishMasterRecs = $this->fm_m->findAllForSelect("id", "name", "--Select All--", " category_id='".$fishCategoryId."'");
		$this->elements['fishId'] = new HTML_Template_Flexy_Element;
		$this->elements['fishId']->setOptions($fishMasterRecs);		
		if ($fishId) $this->elements['fishId']->setValue($fishId);		
	}	

	function getGradeRecs($fprRec)
	{
		//$processCodeId, $freezingStageId, $qualityId, $frozenCodeId
		$this->gradeR = $this->fpr_m->getGrades($fprRec->processcode_id, $fprRec->freezing_stage_id, $fprRec->quality_id, $fprRec->frozencode_id);
		$this->gradeRecSize = sizeof($this->gradeR);
		//print_r($this->gradeR);
	}

	# Get Quick Entry Wise PC Recs
	function getQELWisePCRecs($fishCategoryId, $fishId=null)
	{
		$qePCQry = $this->fpr_m->getPCRecs($fishCategoryId, $fishId);
		$this->qeFprRecs = $this->fpr_m->queryAll($qePCQry);
		$this->qeFprRecSize = sizeof($this->qeFprRecs);
	}

	function gCRRec($fprRec)
	{
		//$processCodeId, $freezingStageId, $qualityId, $frozenCodeId
		return $fprRec->processcode_id."_".$fprRec->freezing_stage_id."_".$fprRec->quality_id."_".$fprRec->frozencode_id;
	}

	# Get Quiuck Entry List Fish Recs
	function getQELWiseFishRecs($fishCategoryId)
	{
		$qelFishQry = $this->fpr_m->getQELFishRecs($fishCategoryId);
		$this->qelFishRecs = $this->fpr_m->queryAll($qelFishQry);
		$this->qelFishRecSize = sizeof($this->qelFishRecs);
	}

	# Get Selected PC
	function getSelPCRecs($fishCategoryId, $fishId)
	{		
		$qePCQry = $this->fpr_m->processCodeRecs($fishCategoryId, $fishId);
		$this->qeFprPCRecs = $this->fpr_m->queryAll($qePCQry);
		//print_r($this->qeFprPCRecs);
		$this->qeFprPCRecSize = sizeof($this->qeFprPCRecs);
	}

	function divContainerId()
	{		
		$displayDiv = "";
		$displayDiv .= "$(function() {";
		foreach ($this->fcRecs as $fcR) {
			$displayDiv .= '$("#container'.$fcR->id.'").tabs().addClass("ui-tabs-vertical ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all");';
			$displayDiv .= '$("#container'.$fcR->id.' li").removeClass("ui-corner-top").addClass("ui-corner-left");';
			$displayDiv .= '$("#container'.$fcR->id.'").tabs().removeClass("ui-tabs-nav").addClass("ui-vtabs-nav");';
			$displayDiv .= '$("#container'.$fcR->id.' ul").removeClass("ui-tabs-nav ui-tabs-panel").addClass("ui-vtabs-nav ui-vtabs-panel");';
		}	
		$displayDiv .= "});";
		$this->divContainerId = $displayDiv;
	}


	function getFrzPkgRateEntryId()
	{

	}
}

?>