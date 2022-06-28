<?php
require_once 'flib/AFController.php';

// load all required models
require_once 'components/base/FrznPkgAccounts_model.php';
require_once 'components/base/DailyFrozenPacking_model.php';
require_once 'components/base/frzn_pkg_rate_list_model.php';
require_once 'components/base/FrznPkgRate_model.php';	


class FrznPkgAccounts_controller extends AFController
{
	protected $templateFolder = "FrznPkgAccounts";
	var $currentUrl	= "FrznPkgAccounts.php";
	var $mode='A';	// Possible values are A- add & E-edit
	var $err=false;	
	var $errDel =	false;	
	var $heading = "";
	var $displayNavRow = false;

	# Constants
	var $msg_succAdd		=	"FrznPkgAccounts added successfully.";
	var $msg_failAdd		=	"Failed to add FrznPkgAccounts.";
	var $msg_succUpdate		=	"Successfully updated the FrznPkgAccounts.";
	var $msg_failUpdate		=	"Failed to update the FrznPkgAccounts.";
	var $msg_succDel		=	"FrznPkgAccounts deleted successfully.";
	var $msg_failDel		=	"Failed to delete the FrznPkgAccounts.";

	var $url_afterAdd		=	"FrznPkgAccounts.php";
	var $url_afterUpdate		=	"FrznPkgAccounts.php";
	var $url_afterDel		=	"FrznPkgAccounts.php";

  	var $label_edit			=	"Edit FrznPkgAccounts";
  	var $label_add			=	"Add New FrznPkgAccounts";
	var $msg_NoRecs			= 	"No records found.";
	var $FrznPkgAccounts_m;
	var $editId;	
	var $totalFrznQty	= 0;
	var $totalSlab		= 0;
	var $totalPkdQty 	= 0;
	
	function __construct($argsArr=null,$xajax=null)
	{
		parent::__construct($argsArr,$xajax);
		$this->FrznPkgAccounts_m = new FrznPkgAccounts_model();

		$this->dFrznPkg_m	= new DailyFrozenPacking_model();

		# Frzn Pkg Rate List
		$this->fprl_m 	= new frzn_pkg_rate_list_model();

		# Frzn Pkg rate
		$this->fpr_m = new FrznPkgRate_model();
	}

	function index()
	{
		$this->loadJS=true;
		$this->onLoadJS = "components/FrznPkgAccounts/FrznPkgAccounts.js";		
		//$this->limit = 1;		
		/*
		if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
		else $this->pageNo=1;
		$this->offset = ($this->pageNo-1)*$this->limit;

		$this->FrznPkgAccountsQuery = "##FILL IN HERE##";

		$this->maxpage = ceil($this->FrznPkgAccounts_m->getCount($this->FrznPkgAccountsQuery)/$this->limit);
		if ($this->maxpage>1) $this->displayNavRow = true;
		
		$this->FrznPkgAccountsRecs = $this->FrznPkgAccounts_m->queryAll($this->FrznPkgAccountsQuery, $this->offset,$this->limit);		
		$this->FrznPkgAccountsRecSize = sizeof($this->FrznPkgAccountsRecs);
		*/

		# /*---------------------------------------------------------------------------
		$this->dateFrom 	= $this->argsArr["dateFrom"];
		$this->dateTo 		= $this->argsArr["dateTo"];
		$this->selProcessorId 	= $this->argsArr["selProcessor"];
		
		# Fill Drop Downs		
		$this->fillDropDowns();
		
		if ($this->dateFrom && $this->dateTo && $this->selProcessorId) {
			/*
			$this->frznPkgRecs = $this->dFrznPkg_m->getDFPRecs(mysqlDateFormat($this->dateFrom), mysqlDateFormat($this->dateTo), $this->selProcessorId);
			//printr($this->frznPkgRecs);
			$this->frznPkgRecSize = sizeof($this->frznPkgRecs);
			*/

			if ($this->argsArr["pageNo"]!="") $this->pageNo=$this->argsArr["pageNo"];		
			else $this->pageNo=1;
			$this->offset = ($this->pageNo-1)*$this->limit;
	
			$this->FrznPkgAccountsQuery = $this->dFrznPkg_m->getDFPRecQry(mysqlDateFormat($this->dateFrom), mysqlDateFormat($this->dateTo), $this->selProcessorId);
	
			$this->maxpage = ceil($this->FrznPkgAccounts_m->getCount($this->FrznPkgAccountsQuery)/$this->limit);
			if ($this->maxpage>1) $this->displayNavRow = true;
			
			$this->frznPkgRecs = $this->FrznPkgAccounts_m->queryAll($this->FrznPkgAccountsQuery, $this->offset, $this->limit);		
			$this->frznPkgRecSize = sizeof($this->frznPkgRecs);

			if ($this->frznPkgRecSize) {
				$fetchAllRecs	= $this->FrznPkgAccounts_m->queryAll($this->FrznPkgAccountsQuery);
				$grandTotalFrznQty 	= 0;
				$grandTotalSlab		= 0;
				$grandTotalPkdQty 	= 0;
				$grandTotalPkgAmt	= 0;
				foreach ($fetchAllRecs as $fpr) {
					$frznQty = $fpr->frozenqty;
					$grandTotalFrznQty += $frznQty;

					$slab	 = $fpr->slab;
					$grandTotalSlab += $slab;

					$pkdQty  = $fpr->pkdqty;
					$grandTotalPkdQty += $pkdQty;

					$pkgAmt	 = $fpr->pkgamt;
					$grandTotalPkgAmt += $pkgAmt;
				}
				$this->grandTotalFrznQty = number_format($grandTotalFrznQty,2,'.',',');
				$this->grandTotalSlab = number_format($grandTotalSlab,0,'',',');
				$this->grandTotalPkdQty = number_format($grandTotalPkdQty,2,'.',',');
				$this->grandTotalPkgAmt = number_format($grandTotalPkgAmt,2,'.',',');
			}
			
		}
		# ---------------------------------------------------------------------------*/

		// now load template to use for this function
		$this->useTemplate("FrznPkgAccounts.html");

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
		$fprRecUpdated = false;
		$this->chkAccess();
			
		
		//printr($this->argsArr);
		$rowCount = $this->argsArr["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$setld		= $this->argsArr["settled_".$i];
			$rate 		= $this->argsArr["pkgRate_".$i];
			$totPkgAmt 	= $this->argsArr["totPkgAmt_".$i];
			$numpack	= $this->argsArr["numpack_".$i];
			$filledwt	= $this->argsArr["filledwt_".$i];
			
			$gEntryIds 	= $this->argsArr["gradeEntryId_".$i];
			$gnummc 	= $this->argsArr["gnummc_".$i];
			$gnumls		= $this->argsArr["gnumls_".$i];
			$groupGradeArr 	= explode(",",$gEntryIds);
			$groupNumMC	= explode(",",$gnummc);
			$groupNumLS	= explode(",",$gnumls);
			$totSlab = 0;
			$qty = 0;
			$gEntryId = "";
			for ($j=0; $j<sizeof($groupGradeArr); $j++) {
				$gEntryId = $groupGradeArr[$j];
				$numMC	= $groupNumMC[$j];
				$numLS	= $groupNumLS[$j];
				$totSlab = ($numMC*$numpack)+$numLS;
				$qty     = $totSlab*$filledwt;
				$gradeTotalRate = $qty*$rate;

				$settled = "";
				if ( ($this->isAdmin==true || $this->reEdit==true) ) {
					$settled = ($setld=="")?N:$setld;
				} 

				//echo "<br>$gEntryId, $settled, $rate, $gradeTotalRate";
				if ($gEntryId!="" && $rate!="") {					
					$this->FrznPkgAccounts_m->updateDFPGradeRec($gEntryId, $settled, $rate, $gradeTotalRate);
					$fprRecUpdated = true;
				}
				//echo "<br>TotSlab=>$gEntryId=$totSlab=R=$gradeTotalRate, $settled";
			}			
			//echo "<br>$gEntryIds";
		}

		
		$succMsg = $this->msg_succUpdate;
		$errMsg  = $this->msg_failUpdate; 
		


		if ($fprRecUpdated) HTTP_Session2::set("displayMsg", $succMsg);
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
		
		$rec	= $this->FrznPkgAccounts_m->find("id=".$this->editId, MDB2_FETCHMODE_ASSOC);	
		$this->createVars("FrznPkgAccounts", $rec);
	}


	function fillDropDowns()
	{	
		# get processor List
		$processorRecs = $this->dFrznPkg_m->getProcessors(mysqlDateFormat($this->dateFrom),mysqlDateFormat($this->dateTo), "--Select--");

		$this->elements['selProcessor'] = new HTML_Template_Flexy_Element;
		$this->elements['selProcessor']->setOptions($processorRecs);		
		if ($this->selProcessorId) $this->elements['selProcessor']->setValue($this->selProcessorId);
	}

	function fillFields()
	{		
	}

	function deleteRecs()
	{
		$mData = AFProcessor::preprocessMultiple($this->argsArr);
		$recDel = $this->FrznPkgAccounts_m->deleteMultiple($mData);
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

	# Frozen Packing Qty details
	function fpQty($fromDate, $tillDate, $preProcessorId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId)
	{		
		list($this->pkdQty, $numMC, $this->frozenQty, $this->totSlab, $this->glaze, $this->netWt, $this->filledWt) = $this->dFrznPkg_m->getFrznPkgQty(mysqlDateFormat($fromDate),mysqlDateFormat($tillDate), $preProcessorId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId);
		//echo "<br>$pkdQty, $numMCs, $frozenQty";
	}

	# get Common rate
	function getRate($selDate, $preProcessorId, $processCodeId, $freezingStageId, $qualityId, $frozenCodeId)
	{
		$rateListId = $this->fprl_m->validFPRateList($selDate);

		$this->defaultRate = $this->fpr_m->defaultFrznPkgRate($processCodeId, $freezingStageId, $qualityId, $frozenCodeId, $rateListId);
		//return $rate;
	}

	function setRate($selRate)
	{		
		return ($selRate!=0)?$selRate:$this->defaultRate;
	}

	function setSettledChk($rowId, $settled)
	{
		//echo "$rowId, $settled";
		$rowId = $rowId+1;
		$this->elements["settled_$rowId"] = new HTML_Template_Flexy_Element();			
		$this->elements["settled_$rowId"]->setValue($settled);
		//printr($this->elements);
	}

	
	function calcTotQty($frznQty, $slab, $pkdQty)
	{
		$this->totalFrznQty 	+= $frznQty; 
		$this->totalSlab	+= $slab;
		$this->totalPkdQty	+= $pkdQty;
		$this->totFrznQty = number_format($this->totalFrznQty,2,'.',',');
		$this->totSlab = number_format($this->totalSlab,0,'',',');
		$this->totPkdQty = number_format($this->totalPkdQty,2,'.',',');
	}
	
	
}

?>