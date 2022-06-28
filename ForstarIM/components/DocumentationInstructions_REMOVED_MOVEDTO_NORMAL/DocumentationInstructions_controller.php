<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/DocumentationInstructions_model.php';

class DocumentationInstructions_controller extends AFController
{
	protected $templateFolder = "DocumentationInstructions";
	var $currentUrl	= "DocumentationInstructions.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Documentation Instructions added successfully.";
	var $msg_failAdd		=	"Failed to add Documentation Instructions.";
	var $msg_succUpdate		=	"Successfully updated the Documentation Instructions.";
	var $msg_failUpdate		=	"Failed to update the Documentation Instructions.";
	var $msg_succDel		=	"Documentation Instructions deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Documentation Instructions.";

	var $url_afterAdd		=	"DocumentationInstructions.php";
	var $url_afterUpdate		=	"DocumentationInstructions.php";
	var $url_afterDel		=	"DocumentationInstructions.php";

  	var $label_edit			=	"Edit Documentation Instructions";
  	var $label_add			=	"Add New Documentation Instructions";
	var $msg_NoRecs			= 	"No records found.";
	var $docInstructions_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->docInstructions_m = new DocumentationInstructions_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/DocumentationInstructions/DocumentationInstructions.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->DocumentationInstructionsQuery = "select * from m_doc_instructions_chk order by name asc";

		$this->maxpage = ceil($this->docInstructions_m->getCount($this->DocumentationInstructionsQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->docInstructionsRecs = $this->docInstructions_m->queryAll($this->DocumentationInstructionsQuery, $this->offset, $this->limit);	
		$this->docInstructionsRecSize = sizeof($this->docInstructionsRecs);

		// now load template to use for this function
		$this->useTemplate("DocumentationInstructions.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;
		//"Add Mode";
		//$this->fillDropDowns();	
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		//printr($this->data);
		
		$this->data["DocumentationInstructions"]["name"] = trim($this->data["DocumentationInstructions"]["name"]);
		if (!isset($this->data["DocumentationInstructions"]["required"])) $this->data["DocumentationInstructions"]["required"] = "N";

		if (!isset($this->data["DocumentationInstructions"]["id"])) {
			$this->data["DocumentationInstructions"]["created"] = "NOW()";
			$this->data["DocumentationInstructions"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		$installedCapacityRecIns = $this->docInstructions_m->save($this->data);
		
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
		
		$rec	= $this->docInstructions_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("DocumentationInstructions", $rec);
	}

	function fillFields()
	{		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->docInstructions_m->deleteMultiple($mData);
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

	function chkList($chk)
	{
		return ($chk=='Y')?"YES":"NO";
	}

	function confirm()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->docInstructions_m->updateconfirmDocumentationInstructions($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->docInstructions_m->updaterlconfirmDocumentationInstructions($this->editId);
		
		
	
	}
}

?>