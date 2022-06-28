<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/ExporterMaster_model.php';
require_once 'components/base/operation_type_model.php';
require_once 'components/base/SetMonitoringParam_model.php';
require_once 'components/base/monitoring_parameter_model.php';
require_once 'components/base/unit_model.php';
require_once 'components/base/SetExporterUnit_model.php';
class ExporterMaster_controller extends AFController
{
	protected $templateFolder = "ExporterMaster";
	var $currentUrl	= "ExporterMaster.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"Exporter added successfully.";
	var $msg_failAdd		=	"Failed to add ExporterMaster.";
	var $msg_succUpdate		=	"Successfully updated the Exporter.";
	var $msg_failUpdate		=	"Failed to update the Exporter.";
	var $msg_succDel		=	"Exporter deleted successfully.";
	var $msg_failDel		=	"Failed to delete the Exporter.";

	var $url_afterAdd		=	"ExporterMaster.php";
	var $url_afterUpdate	=	"ExporterMaster.php";
	var $url_afterDel		=	"ExporterMaster.php";

  	var $label_edit			=	"Edit a Exporter";
  	var $label_add			=	"Add New Exporter";
	var $msg_NoRecs			= 	"No records found.";
	var $Exporter_m;
	var $editId;	
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->Exporter_m = new ExporterMaster_model();
		$this->setMonitoringParam_m = new SetMonitoringParam_model();
		$this->setExporterUnit_m = new SetExporterUnit_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/ExporterMaster/ExporterMaster.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->ExporterQuery = "select * from m_exporter order by name asc";

		$this->maxpage = ceil($this->Exporter_m->getCount($this->ExporterQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->ExporterRecs = $this->Exporter_m->queryAll($this->ExporterQuery, $this->offset,$this->limit);		
		$this->ExporterRecSize = sizeof($this->ExporterRecs);

		// now load template to use for this function
		$this->useTemplate("ExporterMaster.html");

		// finally render the template
		$this->render($this->elements);
	}

function displayunitCodes()
	{
		
		$this->ExporterQuery = "select * from m_exporter_unit";		
		$this->ExporterRecs1 = $this->Exporter_m->queryAll($this->ExporterQuery, $this->offset,$this->limit);		
		$this->ExporterRecSize1 = sizeof($this->ExporterRecs1);

		// now load template to use for this function
		$this->useTemplate("ExporterMaster.html");

		// finally render the template
		$this->render($this->elements);
	}
	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_add;
		$this->fillDropDowns();	
		//"Add Mode";
			
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		//printr($this->data);
		if (!isset($this->data["Exporter"]["id"])) {
			$this->data["Exporter"]["created"] = "NOW()";
			$this->data["Exporter"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAdd;
			$errMsg  = $this->msg_failAdd; 
		} else {
			$succMsg = $this->msg_succUpdate;
			$errMsg  = $this->msg_failUpdate; 
		}
		$installedCapacityRecIns = $this->Exporter_m->save($this->data);
		# Insert Set Monitoring paramters
		$dataArr = array();
		if ($installedCapacityRecIns) {
			//if (!isset($this->data["InstalledCapacity"]["id"])) {
				if (!isset($this->data["Exporter"][id])) {
				# last Inserted Id
				$lastInsertedId = $this->Exporter_m->getLastInsertedId();
				$dataArr["SetExporterUnit"]["exporterid"] = $lastInsertedId;
			//} else $dataArr["SetExporterUnit"]["exporterid"] = $this->data["InstalledCapacity"]["id"];
			} else $dataArr["SetExporterUnit"]["exporterid"] = $this->data["Exporter"]["id"];
			$rowCount = $this->argsArr["hidTableRowCount"];
			//echo $rowCount;
			for ($i=0; $i<$rowCount; $i++) {
					$status = $this->argsArr["status_".$i];
					//echo $status;
					# Edit Id
					$monitorParamEntryId = $this->argsArr["monitoringParamEntryId_".$i];
					//echo $monitorParamEntryId;
					//echo "<br>";
					//if ($monitorParamEntryId) $dataArr["SetMonitoringParam"]["id"] = $monitorParamEntryId;
					//else $dataArr["SetMonitoringParam"]["id"] = null;
					if ($monitorParamEntryId) $dataArr["SetExporterUnit"]["id"] = $monitorParamEntryId;
					else $dataArr["SetExporterUnit"]["id"] = null;
					if ($status!='N') {
						$headName		= trim($this->argsArr["headName_".$i]);
						$monitoringParamId	= $this->argsArr["monitoringParamId_".$i];
						/*$smpStart		= $this->argsArr["smpStart_".$i];
						$smpStop		= $this->argsArr["smpStop_".$i];
						$monitoringInterval	= trim($this->argsArr["monitoringInterval_".$i]);
						$seqFlag		= $this->argsArr["seqFlag_".$i];
						$seqMParamId		= $this->argsArr["seqMParamId_".$i];*/
						$dataArr["SetExporterUnit"]["unitcode"] = $headName;
						$dataArr["SetExporterUnit"]["unitno"] = $monitoringParamId;
						//$dataArr["SetMonitoringParam"]["start"] = $smpStart; /*($smpStart)?$smpStart:"N";*/
						//$dataArr["SetMonitoringParam"]["stop"] = ($smpStop)?$smpStop:"N"; /*$smpStop;*/ /*($smpStop)?$smpStop:"N";*/
						//$dataArr["SetMonitoringParam"]["monitoring_interval"] = $monitoringInterval;
						//$dataArr["SetMonitoringParam"]["seq_flag"] = ($seqFlag)?$seqFlag:"N";
						//$dataArr["SetMonitoringParam"]["seq_mparam_id"] = $seqMParamId;												
						if (isset($dataArr["SetExporterUnit"]["exporterid"]) && $headName!="") {
							$setMonitorParamRecs = $this->setExporterUnit_m->save($dataArr);
						}
					} // Status check ends here
					else if ($status=='N' && $monitorParamEntryId!="") {
						//echo "hai";
						$delMonitorParamRec =  $this->setExporterUnit_m->deleteSingle("id=$monitorParamEntryId");
					}
			} // Row count ends here			
		} // Chk List ends here 
		else if (isset($this->data["Exporter"]["id"])) {
			# delete all check list rec
			$delMonitorParamRec =  $this->setExporterUnit_m->deleteSingle("exporterid='".$this->data["Exporter"]["id"]."'");
		}
		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_edit;	
		$this->fillDropDowns();
		$this->editId = $this->argsArr["editId"];		
		$rec	= $this->Exporter_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("Exporter", $rec);		
		//$icRec	= $this->ic_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		//$this->createVars("InstalledCapacity", $icRec);
		$exporterUnitParamRecs = $this->setExporterUnit_m->findAll(array("where"=>"exporterid='".$this->editId."'", "order"=>"id asc"));		
		$this->exporterUnitParamRecs = $exporterUnitParamRecs;
		$this->monitorParamRecSize = sizeof($exporterUnitParamRecs);
		
		//$this->setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$this->editId."'", "order"=>"id asc"));
		//findAllForSelect
		
		$i = 0;
		foreach ($exporterUnitParamRecs as $mpr) {	
			
			$this->elements["headName_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["headName_$i"]->setValue($mpr->unitcode);		

			$this->elements["monitoringParamId_$i"] = new HTML_Template_Flexy_Element();	
			$this->elements["monitoringParamId_$i"]->setOptions($this->mParamRecs);		
			$this->elements["monitoringParamId_$i"]->setValue($mpr->unitno);

			
			
			/*if ($i!=0) {
				# Get monitoring Param recs
				$smpRecs = $this->setMonitoringParam_m->findAllForSelect("id", "head_name", "--Select--", array("where"=>"installed_capacity_id='".$this->editId."' and  id not in (".$mpr->id.")", "order"=>"id asc"));
				$this->elements["seqMParamId_$i"] = new HTML_Template_Flexy_Element();			
				$this->elements["seqMParamId_$i"]->setOptions($smpRecs);	
				$this->elements["seqMParamId_$i"]->setValue($mpr->seq_mparam_id);
			} else $this->elements["seqMParamId_$i"]->attributes['style'] = 'display:none';*/
			$i++;
		} // Sub head loop ends here

	}
	
	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["Exporter"]["__del"] != "" ) {
				$exporterId = $mvals["Exporter"]["__del"];
				
				# Check ExporterId using anywhere
				$moreEntryExist = $this->Exporter_m->ExporterExist($exporterId);				
				if (!$moreEntryExist) {
					if ($this->Exporter_m->deleteSingle("id='".$exporterId."'")) $recDel = true;
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
			if ($entryExist) $this->errDel = $this->msg_failDel." The Exporter you have selected is already in use.";	
			else $this->errDel = $this->msg_failDel;
		}
		
		/*
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->Exporter_m->deleteMultiple($mData);
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

	function getAddress($cAddress, $cPlace, $cPinCode, $cCountry)
	{
		$displayAddress		= "";
		if ($cAddress)	$displayAddress .= $cAddress;
		if ($cPlace)	$displayAddress .= "<br/>".$cPlace;
		if ($cPinCode)	$displayAddress .= "<br/>".$cPinCode;
		if ($cCountry)	$displayAddress .= "<br/>".$cCountry;

		return nl2br($displayAddress);
	}

	function showDefaultBtn()
	{
		if ($this->add || $this->edit) return true;
		return false;
	}

	function setDefaultExporter()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["Exporter"]["__del"] != "" ) {
				$exporterId = $mvals["Exporter"]["__del"];
				
				$updateDefault = $this->Exporter_m->updateDefaultRec($exporterId);				
								
			}
		}
	}

	function showDefaultChk($defaultRowChk)
	{
			return ($defaultRowChk=='Y')?true:false;
	}

	function fillDropDowns()
	{		
			# Stock Unit Model		
		//$this->mpr_m = new monitoring_parameter_model();
		//$this->mParamRecs = $this->mpr_m->findAllForSelect("id", "name", "--Select--");

		$this->urm = new unit_model();
		$this->mParamRecs = $this->urm->findAllForSelect("id", "name", "--Select--","active=1");
		
		
	}

	function showAddRow()
	{
		if (($this->addMode || $this->editMode) && !sizeof($this->exporterUnitParamRecs)) return true;
		return false;
	}

	function displayExporterUnitParams($exporterId)
	{
		//$monitoringParamRecs = $this->setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$installedCapacityId."'", "order"=>"id asc"));
		$UnitCodesRecs = $this->Exporter_m->fetchAllUnitCodesdis($exporterId);
		
		$displayHtml = "";
		if (sizeof($UnitCodesRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
				$displayHtml .= "<td class=listing-head>Unit No</td>";
				$displayHtml .= "<td class=listing-head>Unit Code</td>";
				$displayHtml .= "<td class=listing-head>Alpha Code</td>";
				$displayHtml .= "</tr>";
			foreach ($UnitCodesRecs as $ucr) {
					$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-item nowrap>";
					$displayHtml .= $ucr->no;
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item nowrap>";
					$displayHtml .= $ucr->name;
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item align=left>";
					$displayHtml .= $ucr->unitcode;
					$displayHtml .=	"</td>";
					
				$displayHtml .= "</tr>";	
			}
			$displayHtml  .= "</table>";
		}
		return $displayHtml;
		
	}



function setFlag($flag)
	{
		if ($flag=='Y') return "YES";
		else if ($flag=='N') return "NO";
		else return;
	}

function confirm1()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->Exporter_m->updateconfirmExporterDetails($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->Exporter_m->updaterlconfirmExporterDetails($this->editId);
		
		
	}


		
		
		

	
}

?>