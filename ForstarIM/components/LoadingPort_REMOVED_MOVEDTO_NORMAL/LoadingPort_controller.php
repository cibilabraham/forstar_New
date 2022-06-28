<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/LoadingPort_model.php';

class LoadingPort_controller extends AFController
{
	protected $templateFolder = "LoadingPort";
	var $currentUrl	= "LoadingPort.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Port of loading added successfully.";
	var $msg_failAdd		=	"Failed to add Port of loading.";
	var $msg_succUpdate		=	"Successfully updated the Port of loading.";
	var $msg_failUpdate		=	"Failed to update the Port of loading.";
	var $msg_succDel		=	"Port of loading deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Port of loading.";

	var $url_afterAdd		=	"LoadingPort.php";
	var $url_afterUpdate		=	"LoadingPort.php";
	var $url_afterDel		=	"LoadingPort.php";

  	var $label_edit		=	"Edit Port of loading";
  	var $label_add			=	"Add New Port of loading";
	var $msg_NoRecs			= 	"No records found.";
	var $LoadingPort_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->LoadingPort_m = new LoadingPort_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/LoadingPort/LoadingPort.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->LoadingPortQuery = "select * from m_loading_port order by name asc";

		$this->maxpage = ceil($this->LoadingPort_m->getCount($this->LoadingPortQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->LoadingPortRecs = $this->LoadingPort_m->queryAll($this->LoadingPortQuery, $this->offset,$this->limit);		
		$this->LoadingPortRecSize = sizeof($this->LoadingPortRecs);

		// now load template to use for this function
		$this->useTemplate("LoadingPort.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		if (!isset($this->data["LoadingPort"]["id"])) {
			$this->data["LoadingPort"]["created"] = "NOW()";
			$this->data["LoadingPort"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		$installedCapacityRecIns = $this->LoadingPort_m->save($this->data);

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;
		
		$this->editId = $this->argsArr["editId"];
		
		$rec	= $this->LoadingPort_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("LoadingPort", $rec);
	}

	function fillFields()
	{		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["LoadingPort"]["__del"] != "" ) {
				$loadingPortId = $mvals["LoadingPort"]["__del"];
				
				# Check loadingPortId using anywhere
				$moreEntryExist = $this->LoadingPort_m->loadingPortExist($loadingPortId);				
				if (!$moreEntryExist) {
					if ($this->LoadingPort_m->deleteSingle("id='".$loadingPortId."'")) $recDel = true;
				} else if ($moreEntryExist) {
					$entryExist = true;
				}				
			}
		}
		
		if ($recDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDel);
			//$sessObj->createSession("nextPage",$url_afterDel.$selection);
		} else {
			echo $entryExist;
			if ($entryExist) $this->errDel = $this->msg_failDel."The port of loading you have selected is already in use.";	
			else $this->errDel = $this->msg_failDel;
		}
		
		/*
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->LoadingPort_m->deleteMultiple($mData);
		if ($recDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDel);
			//$sessObj->createSession("nextPage",$url_afterDel.$selection);
		} else 	$this->errDel	=	$this->msg_failDel;
		*/
	}

	function printList()
	{
		$this->printMode = true;
		$this->addOnLoadFn('window.print();');
	}


	function confirm()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->LoadingPort_m->updateconfirmloadingPort($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->LoadingPort_m->updaterlconfirmloadingPort($this->editId);
		
		
	
}
	
}

?>