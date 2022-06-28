<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/CommonReason_model.php';
require_once 'components/base/CommonReasonChkList_model.php';


class CommonReason_controller extends AFController
{
	protected $templateFolder = "CommonReason";
	var $currentUrl	= "CommonReason.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Common Reason added successfully.";
	var $msg_failAdd		=	"Failed to add Common Reason.";
	var $msg_succUpdate		=	"Successfully updated the Common Reason.";
	var $msg_failUpdate		=	"Failed to update the Common Reason.";
	var $msg_succDel		=	"Common Reason deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Common Reason.";

	var $url_afterAdd		=	"CommonReason.php";
	var $url_afterUpdate		=	"CommonReason.php";
	var $url_afterDel		=	"CommonReason.php";

  	var $label_edit			=	"Edit Common Reason";
  	var $label_add			=	"Add New Common Reason";
	var $msg_NoRecs			= 	"No records found.";
	var $comReason_m;
	var $editId;	
	var $codArr	= array();
	var $crChkList_m;
	var $chkListRecs = array();	
	
	function __construct($argsArr=null,$xajax=null, $codArr=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->comReason_m = new CommonReason_model();
		// Account type array
		$this->codArr = $codArr; 
		$this->crChkList_m = new CommonReasonChkList_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/CommonReason/CommonReason.js";
		//$this->limit = 1;	
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->comReasonQuery = "select * from m_common_reason order by default_entry asc, reason asc";

		$this->maxpage = ceil($this->comReason_m->getCount($this->comReasonQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->comReasonRecs = $this->comReason_m->queryAll($this->comReasonQuery, $this->offset,$this->limit);		
		$this->comReasonRecSize = sizeof($this->comReasonRecs);
			
		// now load template to use for this function
		$this->useTemplate("CommonReason.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;
		$this->fillDropDowns();
	}	
	//payment received: credit
	function save()
	{		
		$succMsg = "";
		$errMsg  = "";
		
		$this->data["CommonReason"]["reason"] = trim($this->data["CommonReason"]["reason"]);
		if (!isset($this->data["CommonReason"]["check_point"])) $this->data["CommonReason"]["check_point"] = "N";
		if (!isset($this->data["CommonReason"]["id"])) {			
			$this->data["CommonReason"]["created"] = "NOW()";
			$this->data["CommonReason"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}
		# Save main table 
		$commonReasonRecIns = $this->comReason_m->save($this->data);
		
		# Insert Check List
		$dataArr = array();
		if ($commonReasonRecIns && $this->data["CommonReason"]["check_point"]=="Y") {

			if (!isset($this->data["CommonReason"]["id"])) {
				# last Inserted Id
				$lastInsertedId = $this->comReason_m->getLastInsertedId();
				$dataArr["CommonReasonChkList"]["common_reason_id"] = $lastInsertedId;
			} else $dataArr["CommonReasonChkList"]["common_reason_id"] = $this->data["CommonReason"]["id"];
			
			$rowCount = $this->argsArr["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
					$status = $this->argsArr["status_".$i];
					$chkListEntryId = $this->argsArr["chkListEntryId_".$i];
					
					if ($chkListEntryId) $dataArr["CommonReasonChkList"]["id"] = $chkListEntryId;
					else $dataArr["CommonReasonChkList"]["id"] = null;

					if ($status!='N') {
						$chkListName	= trim($this->argsArr["chkListName_".$i]);
						$required	= $this->argsArr["required_".$i];
						$dataArr["CommonReasonChkList"]["name"] = $chkListName;
						$dataArr["CommonReasonChkList"]["required"] = ($required)?$required:"N";
						if (isset($dataArr["CommonReasonChkList"]["common_reason_id"]) && $chkListName!="") {
							$crChkListRecIns = $this->crChkList_m->save($dataArr);
						}
					} // Status check ends here
					else if ($status=='N' && $chkListEntryId!="") {
						$delChkListRec =  $this->crChkList_m->deleteSingle("id=$chkListEntryId");
					}
			} // Row count ends here			
		} // Chk List ends here 
		else if (isset($this->data["CommonReason"]["id"])) {
			# delete all check list rec
			$delChkListRec =  $this->crChkList_m->deleteSingle("common_reason_id='".$this->data["CommonReason"]["id"]."'");
		}

		if ($commonReasonRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;
		
		$this->editId = $this->argsArr["editId"];		
		$rec	= $this->comReason_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		
		$chkListRecs = array();
		if ($rec["check_point"]=="Y") {
			$chkListRecs = $this->crChkList_m->findAll(array("where"=>"common_reason_id='".$this->editId."'", "order"=>"id asc"));
		}
		$this->chkListRecs = $chkListRecs;
		$this->chkListRecSize = sizeof($chkListRecs);

			$i = 0;
			foreach ($chkListRecs as $clr) {
				$this->elements["required_$i"] = new HTML_Template_Flexy_Element();			
				$this->elements["required_$i"]->setValue($clr->required);
				$i++;
			}	

		# set readonly
		if ($rec["default_entry"]=='Y') {
			$this->elements["data[CommonReason][reason]"] = new HTML_Template_Flexy_Element();
			$this->elements["data[CommonReason][reason]"]->attributes['readonly'] = 'true';
		}

		// Fill Drop Downs;
		$this->fillDropDowns($rec["cod"]);	
		$this->createVars("CommonReason", $rec);

		
	}

	function fillDropDowns($cd=null)
	{			
		$this->elements['data[CommonReason][cod]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[CommonReason][cod]']->setOptions($this->codArr);		
		if ($cd!="") $this->elements['data[CommonReason][cod]']->setValue($cd);		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["CommonReason"]["__del"] != "" ) {
				$commonReasonId = $mvals["CommonReason"]["__del"];
				
				# Check Common reason id using anywhere
				$moreEntryExist = $this->comReason_m->commonReasonExist($commonReasonId);				
				if (!$moreEntryExist) {
					# delete all check list rec
					$delChkListRec =  $this->crChkList_m->deleteSingle("common_reason_id='".$commonReasonId."'");
					if ($this->comReason_m->deleteSingle("id='".$commonReasonId."'")) $recDel = true;
				} else if ($moreEntryExist) {
					$entryExist = true;
				}				
			}
		}
		//$recDel = $this->comReason_m->deleteMultiple($mData);
		if ($recDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDel);
			//$sessObj->createSession("nextPage",$url_afterDel.$selection);
		} else {
			if ($entryExist) $this->errDel = $this->msg_failDel."The common reason you have selected is already in use.";	
			else $this->errDel = $this->msg_failDel;
		}
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

	function getACType($cod)
	{			
		return $this->codArr[$cod];
	}

	function mandatoryChk($required)
	{		
		$this->elements['required_0'] = new HTML_Template_Flexy_Element;		
		$this->elements['required_0']->setValue($required);
		//return ($required=="Y")?"true":"false";
	}

	function showAddRow()
	{
		if (($this->addMode || $this->editMode) && !sizeof($this->chkListRecs)) return true;
		return false;
	}

	function displayChkList($commonReasonId)
	{
		$chkListRecs = $this->crChkList_m->findAll(array("where"=>"common_reason_id='".$commonReasonId."'", "order"=>"id asc"));
		
		$displayHtml = "";
		if (sizeof($chkListRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
			$displayHtml .= "<td class=listing-head>Check List</td>";
			$displayHtml .= "<td class=listing-head>Required</td>";
			$displayHtml .= "</tr>";
			foreach ($chkListRecs as $clr) {
				$displayHtml .= "<tr bgcolor=#fffbcc>";
				$displayHtml .= "<td class=listing-item nowrap>";
				$displayHtml .= $clr->name;
				$displayHtml .= "</td>";
				$displayHtml .= "<td class=listing-item align=center>";
				$displayHtml .= ($clr->required=='Y')?'YES':'NO';
				$displayHtml .=	"</td>";
				$displayHtml .= "</tr>";	
			}
			$displayHtml  .= "</table>";
		}
		return $displayHtml;
		
	}

	function chkDefaultEntry($chk)
	{
		return ($chk=='Y')?true:false;
	}


	function confirm()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->comReason_m->updateconfirmcommonReason($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->comReason_m->updaterlconfirmcommonReason($this->editId);
		
		
	
}
	
}

?>