<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/role_master_model.php';

class role_master_controller extends AFController
{
	protected $templateFolder = "role_master";	  	
	var $currentUrl	= "RoleMaster.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAddRoleMaster		=	"Role added successfully.";
	var $msg_failAddRoleMaster		=	"Failed to add Role.";
	var $msg_succUpdateRoleMaster		=	"Successfully updated the Role.";
	var $msg_failRoleMasterUpdate		=	"Failed to update the Role.";
	var $msg_succDelRoleMaster		=	"Role deleted successfully.";
	var $msg_failDelRoleMaster		=	"Failed to delete the Role.";

	var $url_afterAddRoleMaster		=	"RoleMaster.php";
	var $url_afterUpdateRoleMaster		=	"RoleMaster.php";
	var $url_afterDelRoleMaster		=	"RoleMaster.php";

  	var $label_editRoleMaster		=	"Edit Role";
  	var $label_addRoleMaster		=	"Add New Role";
	var $msg_NoRecs				= 	"No records found.";
	var $icm;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->rmm = new role_master_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/role_master/RoleMaster.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->rmmQuery = "select mr.id, mr.name, mr.description from m_role mr order by mr.name asc";
		$this->maxpage = ceil($this->rmm->getCount($this->rmmQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;		
		$this->icmRecs = $this->rmm->queryAll($this->rmmQuery, $this->offset,$this->limit);		
		$this->icmRecSize = sizeof($this->icmRecs);

		// now load template to use for this function
		$this->useTemplate("role_master.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_addRoleMaster;
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		if (!isset($this->data["m_role"]["id"])) {
			$this->data["m_role"]["created"] = "NOW()";
			$this->data["m_role"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAddRoleMaster;
			$errMsg  = $this->msg_failAddRoleMaster; 
		} else {
			$succMsg = $this->msg_succUpdateRoleMaster;
			$errMsg  = $this->msg_failRoleMasterUpdate; 
		}

		if ($this->data) $installedCapacityRecIns = $this->rmm->save($this->data);

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_editRoleMaster;				
		$this->editId = $this->argsArr["editId"];		
		$icRec	= $this->rmm->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		//print_r($icRec);
		$this->createVars("m_role", $icRec);
	}


	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$installedCapacityRecDel = $this->rmm->deleteMultiple($mData);
		if ($installedCapacityRecDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDelRoleMaster);
			//$sessObj->createSession("nextPage",$url_afterDelRoleMaster.$selection);
		} else 	$this->errDel	=	$this->msg_failDelRoleMaster;
	}

	function printList()
	{
		$this->printMode = true;
		$this->addOnLoadFn('window.print();');
	}

	function getMonitor($monitor)
	{
		return ($monitor=='S')?"Single":"Multiple";
	}

	
}
?>