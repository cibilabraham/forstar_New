<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/MyCapacity_model.php';

class MyCapacity_controller extends AFController
{
	protected $templateFolder = "MyCapacity";
	var $currentUrl	= "";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"My Capacity added successfully.";
	var $msg_failAdd		=	"Failed to add My Capacity.";
	var $msg_succUpdate		=	"Successfully updated the My Capacity.";
	var $msg_failUpdate		=	"Failed to update the My Capacity.";
	var $msg_succDel		=	"My Capacity deleted successfully.";
	var $msg_failDel		=	"Failed to delete the My Capacity.";

	var $url_afterAdd		=	"CapacityMaster.php";
	var $url_afterUpdate		=	"CapacityMaster.php";
	var $url_afterDel		=	"CapacityMaster.php";

  	var $label_edit		=	"Edit My Capacity";
  	var $label_add			=	"Add New My Capacity";
	var $mc_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->mc_m = new MyCapacity_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "libjs/CapacityMaster.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->mcQuery = "##FILL IN HERE##";

		$this->maxpage = ceil($this->mc_m->getCount($this->mcQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->mcRecs = $this->mc_m->queryAll($this->mcQuery, $this->offset,$this->limit);		
		$this->mcRecSize = sizeof($this->mcRecs);

		// now load template to use for this function
		$this->useTemplate("MyCapacity.html");

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
		if (!isset($this->data["MyCapacity"]["id"])) {
			$this->data["MyCapacity"]["created"] = "NOW()";
			$this->data["MyCapacity"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		$installedCapacityRecIns = $this->mc_m->save($this->data);

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;
		// Fill Drop Downs;
		$this->fillFields();
		$this->editId = $this->argsArr["editId"];
		
		$rec	= $this->mc_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("MyCapacity", $rec);
	}

	function fillFields()
	{		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->mc_m->deleteMultiple($mData);
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
	
}

?>