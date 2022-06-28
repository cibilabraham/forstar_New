<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/frzn_pkg_rate_list_model.php';
require_once 'components/base/FrznPkgRate_model.php';
require_once 'components/base/FrznPkgRateGrade_model.php';

class frzn_pkg_rate_list_controller extends AFController
{	
	protected $templateFolder = "frzn_pkg_rate_list";	  	
	var $currentUrl	= "FrznPkgRateList.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAddFrznPkgRateList		=	"Frozen Packing Rate List added successfully.";
	var $msg_failAddFrznPkgRateList		=	"Failed to add Frozen Packing Rate List.";
	var $msg_succUpdateFrznPkgRateList	=	"Successfully updated the Frozen Packing Rate List.";
	var $msg_failFrznPkgRateListUpdate	=	"Failed to update the Frozen Packing Rate List.";
	var $msg_succDelFrznPkgRateList		=	"Frozen Packing Rate List deleted successfully.";
	var $msg_failDelFrznPkgRateList		=	"Failed to delete the Frozen Packing Rate List.";

	var $url_afterAddFrznPkgRateList	=	"FrznPkgRateList.php";
	var $url_afterUpdateFrznPkgRateList	=	"FrznPkgRateList.php";
	var $url_afterDelFrznPkgRateList	=	"FrznPkgRateList.php";

  	var $label_editFrznPkgRateList		=	"Edit Frozen Packing Rate List";
  	var $label_addFrznPkgRateList		=	"Add New Frozen Packing Rate List";
	var $msg_NoRecs				= 	"No records found.";
	var $msg_chkStartDate			= 	"Please check start date.";
	var $icm;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->fprl_m = new frzn_pkg_rate_list_model();
	}

	function index()
	{
		//$this->copyRateListRec(1);

		$this->loadJS=true;
		$this->onLoadJS = "components/frzn_pkg_rate_list/FrznPkgRateList.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->rmmQuery = "select fprl.id, fprl.name, fprl.start_date from m_frzn_pkg_rate_list fprl order by fprl.start_date desc";
		$this->maxpage = ceil($this->fprl_m->getCount($this->rmmQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;		
		$this->icmRecs = $this->fprl_m->queryAll($this->rmmQuery, $this->offset,$this->limit);		
		$this->icmRecSize = sizeof($this->icmRecs);

		// now load template to use for this function
		$this->useTemplate("frzn_pkg_rate_list.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_addFrznPkgRateList;
		$this->fillDropDowns();
		//echo mysqlDateFormat("15/01/2010");
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		$editId = "";		
		if (!isset($this->data["FrznPkgRateList"]["id"])) {
			# Add
			$this->data["FrznPkgRateList"]["created"] = "NOW()";
			$this->data["FrznPkgRateList"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAddFrznPkgRateList;
			$errMsg  = $this->msg_failAddFrznPkgRateList; 
		} else {
			# Update
			$succMsg = $this->msg_succUpdateFrznPkgRateList;
			$errMsg  = $this->msg_failFrznPkgRateListUpdate;
			$editId =  $this->data["FrznPkgRateList"]["id"];
		}
	
		# Check Valid Date Selected
		$vaildDateEntry	= $this->fprl_m->chkValidDateEntry(mysqlDateFormat($this->data["FrznPkgRateList"]["start_date"]), $editId);		

		if ($this->data && $vaildDateEntry) {
			$this->data["FrznPkgRateList"]["start_date"] = mysqlDateFormat($this->data["FrznPkgRateList"]["start_date"]);
			$installedCapacityRecIns = $this->fprl_m->save($this->data);
			if ($installedCapacityRecIns) {
				//$lastInsertedId = $this->fprl_m->getLastInsertedId();
				# Update Prev Rate List Rec END DATE
				$sDate		= explode("-",$this->data["FrznPkgRateList"]["start_date"]);
				$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
				$lastRateListId = $this->fprl_m->getFrznPkgRateList($endDate);
				if ($lastRateListId!=0) {
					$updateRateListEndDate = $this->fprl_m->updateRateListRec($lastRateListId, $endDate);
				}				
			}
			
			if ($this->argsArr["copyRateList"]!="") {
				$this->copyRateListRec($this->argsArr["copyRateList"]);
			}
		}

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else {			
			if (!$vaildDateEntry) $this->err = $errMsg.$this->msg_chkStartDate;
			else $this->err = $errMsg;

			# Failed to update 
			if ($editId) $this->edit();
			else $this->showAddView();

			$this->createVars("FrznPkgRateList", $this->data["FrznPkgRateList"]);
			//$this->elements['copyRateList'] = new HTML_Template_Flexy_Element;			
			$this->fillDropDowns();
			$this->elements['copyRateList']->setValue($this->argsArr["copyRateList"]);
		}
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_editFrznPkgRateList;				
		$this->editId = $this->argsArr["editId"];		
		$icRec	= $this->fprl_m->find("id='".$this->editId."'", MDB2_FETCHMODE_ASSOC);	
		//print_r($icRec);		
		if ($this->editId) $this->createVars("FrznPkgRateList", $icRec);

		$this->elements['data[FrznPkgRateList][start_date]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[FrznPkgRateList][start_date]']->setValue(dateFormat($icRec["start_date"]));
		
		$this->elements['hidStartDate'] = new HTML_Template_Flexy_Element;
		$this->elements['hidStartDate']->setValue(dateFormat($icRec["start_date"]));
	}


	function deleteRecs()
	{		
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recInUse = false;
		foreach ($mData as $key => $mvals)
		{
			if ( $mvals[$this->fprl_m->name]["__del"] != "" )	{
				$frznPkgRateListId = $mvals[$this->fprl_m->name]["__del"];

				# Check Rec using in other section
				$recExist = $this->fprl_m->chkRateListUse($frznPkgRateListId);
				
				if (!$recExist) {
					$delRec = $this->fprl_m->deleteSingle("id=".$frznPkgRateListId);
					if ($delRec) {
						$lastRateListId = $this->fprl_m->latestRateList();
						if ($lastRateListId!="") {
							# Update Prev Rate List End Date
							$endDate = "0000-00-00";
							$updateRateListEndDate = $this->fprl_m->updateRateListRec($lastRateListId, $endDate);
						}	
					}
				} else if ($recExist) {
					$recInUse = true;
				}
			}
		}
		if ($delRec) {
			HTTP_Session2::set("displayMsg", $this->msg_succDelFrznPkgRateList);
			//$sessObj->createSession("nextPage",$url_afterDelFrznPkgRateList.$selection);
		} else {
			if ($recInUse) $this->errDel = $this->msg_failDelFrznPkgRateList."The selected record is already in use.";
			else $this->errDel = $this->msg_failDelFrznPkgRateList;
		}
	}

	function printList()
	{
		$this->printMode = true;
		$this->addOnLoadFn('window.print();');
	}

	function fillDropDowns()
	{		
		# Get Frozen List recs
		$frznPkgLR = $this->fprl_m->findAllForSelect("id", "name", "--Select--");
		$this->elements['copyRateList'] = new HTML_Template_Flexy_Element;
		$this->elements['copyRateList']->setOptions($frznPkgLR);
	}

	# Copy rate list recs
	function copyRateListRec($copyRateListId)
	{
		$fpr_m	= new FrznPkgRate_model();
		$fprg_m = new FrznPkgRateGrade_model();

		# Current rate list
		$rateListId = $this->fprl_m->latestRateList();

		# Frzn Pkg Rate List
		$frznPkgMainRecs = $fpr_m->findAll(array("where"=>"rate_list_id='$copyRateListId'"));
		
		foreach ($frznPkgMainRecs as $fprObj) {
			$fprArr = (array) $fprObj; // convert array obj->array
			$nfprArr = array("FrznPkgRate"=>$fprArr);
			unset($nfprArr["FrznPkgRate"]["id"]);
			$nfprArr["FrznPkgRate"]["rate_list_id"] = $rateListId;

			if($fpr_m->save($nfprArr)) {
				# get Last Inserted Id
				$lastInsertedId = $fpr_m->getLastInsertedId();

				# Grade recs
				$frznGradeRateRecs = $fprg_m->findAll(array("where"=>"pkg_rate_entry_id='".$fprObj->id."'"));
				foreach ($frznGradeRateRecs as $fpgObj) {
					$fpgArr = (array) $fpgObj;
					$nfpgArr = array("FrznPkgRateGrade"=>$fpgArr);
					unset($nfpgArr["FrznPkgRateGrade"]["id"]);
					$nfpgArr["FrznPkgRateGrade"]["pkg_rate_entry_id"] = $lastInsertedId;
					# Save
					$frznPkgRateGradeRecIns = $fprg_m->save($nfpgArr);
				}
			}
		} // Main Loop ends here		
	}
}
?>