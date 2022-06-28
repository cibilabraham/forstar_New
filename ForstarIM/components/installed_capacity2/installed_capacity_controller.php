<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/installed_capacity_model.php';

require_once 'components/base/operation_type_model.php';
require_once 'components/base/stock_unit_model.php';
require_once 'components/base/monitoring_parameter_model.php';
require_once 'components/base/SetMonitoringParam_model.php';

# m_installed_capacity - Main table
# m_set_monitoring_param - sub table

class installed_capacity_controller extends AFController
{
	protected $templateFolder = "installed_capacity";	  	
	var $currentUrl	= "InstalledCapacity.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAddInstalledCapacity		=	"Installed Capacity added successfully.";
	var $msg_failAddInstalledCapacity		=	"Failed to add Installed Capacity.";
	var $msg_succUpdateInstalledCapacity		=	"Successfully updated the Installed Capacity.";
	var $msg_failInstalledCapacityUpdate		=	"Failed to update the Installed Capacity.";
	var $msg_succDelInstalledCapacity		=	"Installed Capacity deleted successfully.";
	var $msg_failDelInstalledCapacity		=	"Failed to delete the Installed Capacity.";

	var $url_afterAddInstalledCapacity		=	"InstalledCapacity.php";
	var $url_afterUpdateInstalledCapacity		=	"InstalledCapacity.php";
	var $url_afterDelInstalledCapacity		=	"InstalledCapacity.php";

  	var $label_editInstalledCapacity		=	"Edit Installed Capacity";
  	var $label_addInstalledCapacity			=	"Add New Installed Capacity";
	var $msg_NoRecs					= 	"No records found.";
	var $ic_m;
	var $editId;	
	var $setMonitoringParam_m;

	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);

		$this->ic_m = new installed_capacity_model();
		$this->setMonitoringParam_m = new SetMonitoringParam_model();	
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/installed_capacity/InstalledCapacity.js";
		//$this->limit = 1;
		
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->icmQuery = "select  mic.id, mic.name, mic.description, mic.operation_type_id, mic.capacity, mic.unit_id, mic.per_val, mic.monitor, mic.monitoring_parameter_id, mot.name as operationType, msu.name as unitName, mmp.name as parameter from m_installed_capacity mic join m_operation_type mot on mic.operation_type_id=mot.id join m_stock_unit msu on msu.id=mic.unit_id left join m_monitoring_parameters mmp on mic.monitoring_parameter_id=mmp.id  order by mic.name asc";
		$this->maxpage = ceil($this->ic_m->getCount($this->icmQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;		
		$this->icmRecs = $this->ic_m->queryAll($this->icmQuery, $this->offset,$this->limit);		
		$this->icmRecSize = sizeof($this->icmRecs);

		// now load template to use for this function
		$this->useTemplate("installed_capacity.html");

		// finally render the template
		$this->render($this->elements);
	}

	function showAddView()
	{
		$this->listMode = false;
		$this->addMode = true;
		$this->heading = $this->label_addInstalledCapacity;
		//"Add Mode";
		$this->fillDropDowns();	
	}	
	
	function save()
	{
		$succMsg = "";
		$errMsg  = "";
		if (!isset($this->data["InstalledCapacity"]["id"])) {
			$this->data["InstalledCapacity"]["created"] = "NOW()";
			$this->data["InstalledCapacity"]["created_by"] = $this->userId;
			$succMsg = $this->msg_succAddInstalledCapacity;
			$errMsg  = $this->msg_failAddInstalledCapacity; 
		} else {
			$succMsg = $this->msg_succUpdateInstalledCapacity;
			$errMsg  = $this->msg_failInstalledCapacityUpdate; 
		}

		$installedCapacityRecIns = $this->ic_m->save($this->data);

		# Insert Set Monitoring paramters
		$dataArr = array();
		if ($installedCapacityRecIns) {

			if (!isset($this->data["InstalledCapacity"]["id"])) {
				# last Inserted Id
				$lastInsertedId = $this->ic_m->getLastInsertedId();
				$dataArr["SetMonitoringParam"]["installed_capacity_id"] = $lastInsertedId;
			} else $dataArr["SetMonitoringParam"]["installed_capacity_id"] = $this->data["InstalledCapacity"]["id"];
			
			$rowCount = $this->argsArr["hidTableRowCount"];
			for ($i=0; $i<$rowCount; $i++) {
					$status = $this->argsArr["status_".$i];

					# Edit Id
					$monitorParamEntryId = $this->argsArr["monitoringParamEntryId_".$i];
					
					if ($monitorParamEntryId) $dataArr["SetMonitoringParam"]["id"] = $monitorParamEntryId;
					else $dataArr["SetMonitoringParam"]["id"] = null;

					if ($status!='N') {
						$headName		= trim($this->argsArr["headName_".$i]);
						$monitoringParamId	= $this->argsArr["monitoringParamId_".$i];
						$smpStart		= $this->argsArr["smpStart_".$i];
						$smpStop		= $this->argsArr["smpStop_".$i];
						$monitoringInterval	= trim($this->argsArr["monitoringInterval_".$i]);
						$seqFlag		= $this->argsArr["seqFlag_".$i];
						$seqMParamId		= $this->argsArr["seqMParamId_".$i];

						$dataArr["SetMonitoringParam"]["head_name"] = $headName;
						$dataArr["SetMonitoringParam"]["monitoring_parameter_id"] = $monitoringParamId;
						$dataArr["SetMonitoringParam"]["start"] = $smpStart; /*($smpStart)?$smpStart:"N";*/
						$dataArr["SetMonitoringParam"]["stop"] = ($smpStop)?$smpStop:"N"; /*$smpStop;*/ /*($smpStop)?$smpStop:"N";*/
						$dataArr["SetMonitoringParam"]["monitoring_interval"] = $monitoringInterval;
						$dataArr["SetMonitoringParam"]["seq_flag"] = ($seqFlag)?$seqFlag:"N";
						$dataArr["SetMonitoringParam"]["seq_mparam_id"] = $seqMParamId;
												
						if (isset($dataArr["SetMonitoringParam"]["installed_capacity_id"]) && $headName!="") {
							$setMonitorParamRecs = $this->setMonitoringParam_m->save($dataArr);
						}
					} // Status check ends here
					else if ($status=='N' && $monitorParamEntryId!="") {
						$delMonitorParamRec =  $this->setMonitoringParam_m->deleteSingle("id=$monitorParamEntryId");
					}
			} // Row count ends here			
		} // Chk List ends here 
		else if (isset($this->data["InstalledCapacity"]["id"])) {
			# delete all check list rec
			$delMonitorParamRec =  $this->setMonitoringParam_m->deleteSingle("installed_capacity_id='".$this->data["InstalledCapacity"]["id"]."'");
		}

		if ($installedCapacityRecIns) HTTP_Session2::set("displayMsg", $succMsg);
		else $this->err = $errMsg;
	}

	function edit()
	{
		$this->listMode = false;
		$this->editMode = true;
		$this->heading = $this->label_editInstalledCapacity;
		// Fill Drop Downs;
		$this->fillDropDowns();
		$this->editId = $this->argsArr["editId"];
		
		$icRec	= $this->ic_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("InstalledCapacity", $icRec);

		$monitorParamRecs = $this->setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$this->editId."'", "order"=>"id asc"));		
		$this->monitorParamRecs = $monitorParamRecs;
		$this->monitorParamRecSize = sizeof($monitorParamRecs);
		
		//$this->setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$this->editId."'", "order"=>"id asc"));
		//findAllForSelect
		
		$i = 0;
		foreach ($monitorParamRecs as $mpr) {	
			
			$this->elements["headName_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["headName_$i"]->setValue($mpr->head_name);		

			$this->elements["monitoringParamId_$i"] = new HTML_Template_Flexy_Element();	
			$this->elements["monitoringParamId_$i"]->setOptions($this->mParamRecs);		
			$this->elements["monitoringParamId_$i"]->setValue($mpr->monitoring_parameter_id);

			$this->elements["smpStart_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["smpStart_$i"]->setValue($mpr->start);
			
			$this->elements["smpStop_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["smpStop_$i"]->setValue($mpr->stop);

			$monitoringInterval = ($mpr->monitoring_interval!=0)?$mpr->monitoring_interval:"";
			$this->elements["monitoringInterval_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["monitoringInterval_$i"]->setValue($monitoringInterval);		
			
			$this->elements["seqFlag_$i"] = new HTML_Template_Flexy_Element();			
			$this->elements["seqFlag_$i"]->setValue($mpr->seq_flag);
			
			if ($i!=0) {
				# Get monitoring Param recs
				$smpRecs = $this->setMonitoringParam_m->findAllForSelect("id", "head_name", "--Select--", array("where"=>"installed_capacity_id='".$this->editId."' and  id not in (".$mpr->id.")", "order"=>"id asc"));
				$this->elements["seqMParamId_$i"] = new HTML_Template_Flexy_Element();			
				$this->elements["seqMParamId_$i"]->setOptions($smpRecs);	
				$this->elements["seqMParamId_$i"]->setValue($mpr->seq_mparam_id);
			} else $this->elements["seqMParamId_$i"]->attributes['style'] = 'display:none';
			$i++;
		} // Sub head loop ends here
		
	}

	function fillDropDowns()
	{		
		# Operation Type Recs
		$this->otr = new operation_type_model();
		$oTR = $this->otr->findAllForSelect("id", "name", "--Select--","active=1");
		$this->elements['data[InstalledCapacity][operation_type_id]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[InstalledCapacity][operation_type_id]']->setOptions($oTR);

		# Stock Unit Model		
		$this->sur = new stock_unit_model();
		$suR = $this->sur->findAllForSelect("id", "name", "--Select--","active=1");
		$this->elements['data[InstalledCapacity][unit_id]'] = new HTML_Template_Flexy_Element;
		$this->elements['data[InstalledCapacity][unit_id]']->setOptions($suR);
		
		# Monitoring parameter
		
		$this->mpr_m = new monitoring_parameter_model();
		$this->mParamRecs = $this->mpr_m->findAllForSelect("id", "name", "--Select--","active=1");
		//$this->elements['data[InstalledCapacity][monitoring_parameter_id]'] = new HTML_Template_Flexy_Element;
		//$this->elements['data[InstalledCapacity][monitoring_parameter_id]']->setOptions($this->mParamRecs);
		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$entryExist = false;
		foreach ($mData as $key => $mvals) {
			if ($mvals["InstalledCapacity"]["__del"] != "" ) {
				$installedCapacityId = $mvals["InstalledCapacity"]["__del"];
				
				# Check Common reason id using anywhere
				//$moreEntryExist = $this->comReason_m->commonReasonExist($installedCapacityId);				
				$moreEntryExist = false;
				if (!$moreEntryExist) {
					# delete all Sub head rec
					$delSetMonitorParamRec =  $this->setMonitoringParam_m->deleteSingle("installed_capacity_id='".$installedCapacityId."'");
					if ($this->ic_m->deleteSingle("id='".$installedCapacityId."'")) $installedCapacityRecDel = true;
				} else if ($moreEntryExist) {
					$entryExist = true;
				}				
			}
		}
		

		//$mData = AFProcessor::preprocessMultiple($this->argsArr);
		//$installedCapacityRecDel = $this->ic_m->deleteMultiple($mData);	

		if ($installedCapacityRecDel) {
			HTTP_Session2::set("displayMsg", $this->msg_succDelInstalledCapacity);
			//$sessObj->createSession("nextPage",$url_afterDelInstalledCapacity.$selection);
		} else 	$this->errDel	=	$this->msg_failDelInstalledCapacity;
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

	function showAddRow()
	{
		if (($this->addMode || $this->editMode) && !sizeof($this->monitorParamRecs)) return true;
		return false;
	}

	function displayMonitorParams($installedCapacityId)
	{
		//$monitoringParamRecs = $this->setMonitoringParam_m->findAll(array("where"=>"installed_capacity_id='".$installedCapacityId."'", "order"=>"id asc"));
		$monitoringParamRecs = $this->ic_m->fetchAllMonitoringParams($installedCapacityId);
		
		$displayHtml = "";
		if (sizeof($monitoringParamRecs)>0) {
			$displayHtml  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
			$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
				$displayHtml .= "<td class=listing-head>Head</td>";
				$displayHtml .= "<td class=listing-head>Monitoring<br/> Factor</td>";
				$displayHtml .= "<td class=listing-head>Start</td>";
				$displayHtml .= "<td class=listing-head>Stop</td>";
				$displayHtml .= "<td class=listing-head>Monitoring <br/> Interval<br/>(HR)</td>";
			$displayHtml .= "</tr>";
			foreach ($monitoringParamRecs as $mpr) {
				$displayHtml .= "<tr bgcolor=#fffbcc>";
					$displayHtml .= "<td class=listing-item nowrap>";
					$displayHtml .= $mpr->head_name;
					$displayHtml .= "</td>";
					$displayHtml .= "<td class=listing-item align=left>";
					$displayHtml .= $mpr->name;
					$displayHtml .=	"</td>";
					$displayHtml .= "<td class=listing-item align=center>";
					$displayHtml .= $this->setFlag($mpr->start);
					$displayHtml .=	"</td>";
					$displayHtml .= "<td class=listing-item align=center>";
					$displayHtml .= $this->setFlag($mpr->stop);
					$displayHtml .=	"</td>";
					$displayHtml .= "<td class=listing-item align=right>";
					$displayHtml .= ($mpr->monitoring_interval!=0)?$mpr->monitoring_interval:"";
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

	function confirm()
	{
		$this->editId = $this->argsArr["confirmId"];		
		$updateRateListEndDate = $this->ic_m->updateconfirminstalledcapacity($this->editId);
		
		
	}

	function Releaseconfirm()
	{
		$this->editId = $this->argsArr["rlconfirmId"];		
		$updateRateListEndDate = $this->ic_m->updaterlconfirminstalledcapacity($this->editId);
		
		
	
}

	
}
?>