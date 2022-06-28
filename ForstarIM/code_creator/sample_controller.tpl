require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/{modelName}_model.php';

class {modelName}_controller extends AFController
{
	protected $templateFolder = "{modelName}";
	var $currentUrl	= "{routingPagePrefix}.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"{functionalityName} added successfully.";
	var $msg_failAdd		=	"Failed to add {functionalityName}.";
	var $msg_succUpdate		=	"Successfully updated the {functionalityName}.";
	var $msg_failUpdate		=	"Failed to update the {functionalityName}.";
	var $msg_succDel		=	"{functionalityName} deleted successfully.";
	var $msg_failDel		=	"Failed to delete the {functionalityName}.";

	var $url_afterAdd		=	"{routingPagePrefix}.php";
	var $url_afterUpdate		=	"{routingPagePrefix}.php";
	var $url_afterDel		=	"{routingPagePrefix}.php";

  	var $label_edit		=	"Edit {functionalityName}";
  	var $label_add			=	"Add New {functionalityName}";
	var $msg_NoRecs			= 	"No records found.";
	var ${modelShortName}_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->{modelShortName}_m = new {modelName}_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/{modelName}/{routingPagePrefix}.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->{modelShortName}Query = "##FILL IN HERE##";

		$this->maxpage = ceil($this->{modelShortName}_m->getCount($this->{modelShortName}Query)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->{modelShortName}Recs = $this->{modelShortName}_m->queryAll($this->{modelShortName}Query, $this->offset,$this->limit);		
		$this->{modelShortName}RecSize = sizeof($this->{modelShortName}Recs);

		// now load template to use for this function
		$this->useTemplate("{modelName}.html");

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
		if (!isset($this->data["{modelName}"]["id"])) {
			$this->data["{modelName}"]["created"] = "NOW()";
			$this->data["{modelName}"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		$installedCapacityRecIns = $this->{modelShortName}_m->save($this->data);

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
		
		$rec	= $this->{modelShortName}_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("{modelName}", $rec);
	}

	function fillFields()
	{		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->{modelShortName}_m->deleteMultiple($mData);
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
