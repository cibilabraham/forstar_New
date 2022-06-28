<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/DAMSetting_model.php';
require_once 'components/base/stock_unit_model.php';
require_once 'components/base/DAMSetting_subhead_model.php';


# Daily activity Monitoring Setting
class DAMSetting_controller extends AFController
{
	protected $templateFolder = "DAMSetting";
	var $currentUrl	= "DAMSetting.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Daily Activity Monitoring Setting added successfully.";
	var $msg_failAdd		=	"Failed to add Daily Activity Monitoring.";
	var $msg_succUpdate		=	"Successfully updated the Daily Activity Monitoring.";
	var $msg_failUpdate		=	"Failed to update the Daily Activity Monitoring.";
	var $msg_succDel		=	"Daily Activity Monitoring deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Daily Activity Monitoring.";

	var $url_afterAdd		=	"DAMSetting.php";
	var $url_afterUpdate		=	"DAMSetting.php";
	var $url_afterDel		=	"DAMSetting.php";

  	var $label_edit			=	"Edit Daily Activity Monitoring";
  	var $label_add			=	"Add New Daily Activity Monitoring";
	var $msg_NoRecs			= 	"No records found.";
	var $DAMSetting_m;
	var $editId;	
	var $DAMSetting_subhead_m;
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->DAMSetting_m = new DAMSetting_model();

		$this->DAMSetting_subhead_m = new DAMSetting_subhead_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/DAMSetting/DAMSetting.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->DAMSettingQuery = "select mds.id, mds.head_name as mainhead, mds.sub_head as numsubhead, mdse.sub_head_name as subhead, if (mdse.produced='Y','YES','NO') as produced, if (mdse.stocked='Y','YES','NO') as stocked, if (mdse.os_supply='Y','YES',if (mdse.os_supply='N','NO','')) as ossupply, if (mdse.os_sale='Y','YES',if (mdse.os_sale='N','NO','')) as ossale, mdse.opening_balance as ob, mdse.unit_id as unit, date_format(mdse.start_date,'%d/%m/%Y') as startdate, msu.name as stkname,mds.active as active from m_dam_setting mds left join m_dam_setting_entry mdse on mds.id=mdse.entry_id left join m_stock_unit msu on mdse.unit_id=msu.id order by mds.head_name asc, mdse.sub_head_name asc";

		//echo "--->".$this->DAMSettingQuery;

		$this->maxpage = ceil($this->DAMSetting_m->getCount($this->DAMSettingQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->DAMSettingRecs = $this->DAMSetting_m->queryAll($this->DAMSettingQuery, $this->offset,$this->limit);		
		$this->DAMSettingRecSize = sizeof($this->DAMSettingRecs);

		// now load template to use for this function
		$this->useTemplate("DAMSetting.html");

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
		if (!isset($this->data["DAMSetting"]["id"])) {
			$this->data["DAMSetting"]["created"] = "NOW()";
			$this->data["DAMSetting"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}

		# insert Main rec
		$dailyAMSettingRecIns = $this->DAMSetting_m->save($this->data);
		
		# insert Sub-head
		$dataArr = array();
		if ($dailyAMSettingRecIns) {

			if (!isset($this->data["DAMSetting"]["id"])) {
				# last Inserted Id
				$lastInsertedId = $this->DAMSetting_m->getLastInsertedId();
				$dataArr["DAMSettingSubhead"]["entry_id"] = $lastInsertedId;
			} else {
				$dataArr["DAMSettingSubhead"]["entry_id"] = $this->data["DAMSetting"]["id"];	
				# delete all Sub head rec
				$delSubHeadRec =  $this->DAMSetting_subhead_m->deleteSingle("entry_id='".$this->data["DAMSetting"]["id"]."'");
			}


			$rowCount = $this->argsArr["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
				$status = $this->argsArr["status_".$i];

				$damEntryId = $this->argsArr["damEntryId_".$i];
				/*
				if ($damEntryId) $dataArr["DAMSettingSubhead"]["id"] = $damEntryId;
				else $dataArr["DAMSettingSubhead"]["id"] = null;
				*/				
				$dataArr["DAMSettingSubhead"]["id"] = null;

					if ($status!='N') {
						$subheadName	= trim($this->argsArr["subheadName_".$i]);
						$produced	= $this->argsArr["produced_".$i];
						$stocked	= $this->argsArr["stocked_".$i];
						$osSupply	= $this->argsArr["osSupply_".$i];
						$osSale		= $this->argsArr["osSale_".$i];
						$openingBalance = trim($this->argsArr["openingBalance_".$i]);
						$selUnit	= $this->argsArr["selUnit_".$i];
						$startDate	= mysqlDateFormat($this->argsArr["startDate_".$i]);

						$dataArr["DAMSettingSubhead"]["sub_head_name"] 	= $subheadName;
						$dataArr["DAMSettingSubhead"]["produced"] 	= $produced;
						$dataArr["DAMSettingSubhead"]["stocked"] 	= $stocked;
						$dataArr["DAMSettingSubhead"]["os_supply"] 	= $osSupply;
						$dataArr["DAMSettingSubhead"]["os_sale"] 	= $osSale;
						$dataArr["DAMSettingSubhead"]["opening_balance"] = $openingBalance;
						$dataArr["DAMSettingSubhead"]["unit_id"] 	= $selUnit;
						$dataArr["DAMSettingSubhead"]["start_date"] 	= $startDate;

						if (isset($dataArr["DAMSettingSubhead"]["entry_id"]) && $subheadName!="") {
							$dailyAMSettingSubheadRecIns = $this->DAMSetting_subhead_m->save($dataArr);
						}
					} // Status check ends here
					else if ($status=='N' && $damEntryId!="") {
						$delDAMSettingEntryRec =  $this->DAMSetting_subhead_m->deleteSingle("id=$damEntryId");
					}
			} // Row count ends here	
		} // Rec Ins chk Ends 

		if ($dailyAMSettingRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;
		
		$this->editId = $this->argsArr["editId"];
		
		$rec	= $this->DAMSetting_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("DAMSetting", $rec);

		$this->elements["hidTotalHead"] = new HTML_Template_Flexy_Element();			
		$this->elements["hidTotalHead"]->setValue($rec["sub_head"]);

		// Fill Drop Downs;
		$this->fillDropDowns();		
		
		$subHeadRecs = array();
		$subHeadRecs = $this->DAMSetting_subhead_m->findAll(array("where"=>"entry_id='".$this->editId."'", "order"=>"id asc"));
		
		$this->subHeadRecs = $subHeadRecs;
		$this->subHeadRecSize = sizeof($subHeadRecs);

		$i = 0;
		foreach ($subHeadRecs as $shr) {	
			
			$this->elements["subheadName_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["subheadName_$i"]->setValue($shr->sub_head_name);		

			$this->elements["produced_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["produced_$i"]->setValue($shr->produced);

			$this->elements["stocked_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["stocked_$i"]->setValue($shr->stocked);
			
			$this->elements["osSupply_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["osSupply_$i"]->setValue($shr->os_supply);

			$this->elements["osSale_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["osSale_$i"]->setValue($shr->os_sale);

			$this->elements["openingBalance_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["openingBalance_$i"]->setValue($shr->opening_balance);

			$this->elements["selUnit_$i"] = new HTML_Template_Flexy_Element;
			$this->elements["selUnit_$i"]->setOptions($this->suR);		
			if ($shr->unit_id) $this->elements["selUnit_$i"]->setValue($shr->unit_id);
			
			$this->elements["startDate_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["startDate_$i"]->setValue(dateFormat($shr->start_date));

			$i++;
		} // Sub head loop ends here
	
		
	}

	function fillDropDowns()
	{	
		# Stock Unit Model		
		$this->sur = new stock_unit_model();
		$this->suR = $this->sur->findAllForSelect("id", "name", "--Select--", array("order"=>"name asc","where"=>"active=1"));
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["DAMSetting"]["__del"] != "" ) {
				$damSettingId = $mvals["DAMSetting"]["__del"];
				
				# Check Common reason id using anywhere
				$damsEntryExist = $this->DAMSetting_m->damSettingRecExist($damSettingId);				
				
				if (!$damsEntryExist) {
					# delete all Sub head rec
					$delSubHeadRec =  $this->DAMSetting_subhead_m->deleteSingle("entry_id='".$damSettingId."'");
					if ($this->DAMSetting_m->deleteSingle("id='".$damSettingId."'")) $recDel = true;
				} else if ($damsEntryExist) {
					$entryExist = true;
				}				
			}
		}
		//$recDel = $this->comReason_m->deleteMultiple($mData);
		if ($recDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDel);
			//$sessObj->createSession("nextPage",$url_afterDel.$selection);
		} else {
			if ($entryExist) $this->errDel = $this->msg_failDel."The settings you have selected is already in use.";	
			else $this->errDel = $this->msg_failDel;
		}

		/*
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->DAMSetting_m->deleteMultiple($mData);
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

	function showAddRow()
	{
		if (($this->addMode || $this->editMode) && !sizeof($this->subHeadRecs)) return true;
		return false;
	}

	function setPrevRec($damSettingId)
	{
		$this->prevDamSettingId = $damSettingId; 
	}
	
	function chkPrevDAMSettingId($damSettingId)
	{
		if ($this->prevDamSettingId!=$damSettingId) return true;
		else return false;
	}
	

	function confirm()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->DAMSetting_m->updateconfirmDAMSetting($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->DAMSetting_m->updaterlconfirmDAMSetting($this->editId);
		
		
	
}
}

?>