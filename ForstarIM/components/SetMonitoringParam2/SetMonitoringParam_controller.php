<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/SetMonitoringParam_model.php';

require_once 'components/base/installed_capacity_model.php';
require_once 'components/base/monitoring_parameter_model.php';

class SetMonitoringParam_controller extends AFController
{
	protected $templateFolder = "SetMonitoringParam";
	var $currentUrl	= "SetMonitoringParam.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Monitoring Parameter setting added successfully.";
	var $msg_failAdd		=	"Failed to add Monitoring Parameter setting.";
	var $msg_succUpdate		=	"Successfully updated the Monitoring Parameter setting.";
	var $msg_failUpdate		=	"Failed to update the Monitoring Parameter setting.";
	var $msg_succDel		=	"Monitoring Parameter setting deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Monitoring Parameter setting.";

	var $url_afterAdd		=	"SetMonitoringParam.php";
	var $url_afterUpdate		=	"SetMonitoringParam.php";
	var $url_afterDel		=	"SetMonitoringParam.php";

  	var $label_edit			=	"Edit Monitoring Parameter setting";
  	var $label_add			=	"Add New Monitoring Parameter setting";
	var $msg_NoRecs			= 	"No records found.";
	var $SetMonitoringParam_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->SetMonitoringParam_m = new SetMonitoringParam_model();
		
		$this->ic_m = new installed_capacity_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/SetMonitoringParam/SetMonitoringParam.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->setMPQry = $this->SetMonitoringParam_m->fetchAllRecQry();

		$this->maxpage = ceil($this->SetMonitoringParam_m->getCount($this->setMPQry)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		# Fetch All Records
		$this->setMPRecs = $this->SetMonitoringParam_m->queryAll($this->setMPQry, $this->offset,$this->limit);	
		$this->setMPRecSize = sizeof($this->setMPRecs);

		// now load template to use for this function
		$this->useTemplate("SetMonitoringParam.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;

		//"Add Mode";
		$this->fillDropDowns();	
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		if (!isset($this->data["SetMonitoringParam"]["id"])) {
			
			$this->data["SetMonitoringParam"]["created"] = "NOW()";
			$this->data["SetMonitoringParam"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		$installedCapacityRecIns = $this->SetMonitoringParam_m->save($this->data);
		//echo "h".$this->userId;
		//	die();
		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;

		// Fill Drop Downs;
		$this->fillDropDowns();
		$this->editId = $this->argsArr["editId"];
		
		$rec	= $this->SetMonitoringParam_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("SetMonitoringParam", $rec);
	}
	

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->SetMonitoringParam_m->deleteMultiple($mData);
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

	function fillDropDowns()
	{	
		# Installed capacity		
		$icR = $this->ic_m->findAllForSelect("id", "name", "--Select--");;
		$this->elements['data[SetMonitoringParam][installed_capacity_id]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[SetMonitoringParam][installed_capacity_id]']->setOptions($icR);
		
		# monitoring_parameter
		$this->mpr = new monitoring_parameter_model();
		$mpR = $this->mpr->findAllForSelect("id", "name", "--Select--");;
		$this->elements['data[SetMonitoringParam][monitoring_parameter_id]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[SetMonitoringParam][monitoring_parameter_id]']->setOptions($mpR);
	}

	function setFlag($flag)
	{
		if ($flag=='Y') return "YES";
		else if ($flag=='N') return "NO";
		else return;
	}
	
}

?>