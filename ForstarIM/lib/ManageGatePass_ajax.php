<?php
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();
//$xajax->configure('debug',true);
//$xajax->configure('defaultMode', 'synchronous'); // For return value	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}

		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}		
	}
	
	
	function getUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unit",$unit,$cel);
		return $objResponse;	
	}

	#Generate GatePass
	function getPONumber($compId,$invUnit)
	{
		$selDate=date("Y-m-d");
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$manageGatePassObj		= new ManageGatePass($databaseConnect);
		//$objResponse->alert($compId);
		//$qry	="select id,start_no, end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='MGP' and billing_company_id='$compId' and unitid='$invUnit'  or date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and type='MGP' and billing_company_id='$compId' and unitid='0'";
		
		$checkGateNumberSettingsExist=$manageGatePassObj->chkValidGatePassId($selDate,$compId,$invUnit);
		
		//$objResponse->alert($checkGateNumberSettingsExist);
		if (sizeof($checkGateNumberSettingsExist)>0)
		{
			$alpId=$checkGateNumberSettingsExist[0][0];
			$alphaCode=$manageGatePassObj->getAlphaCode($alpId);
			$alphaCodePrefix= $alphaCode[0];
			//$objResponse->alert($alphaCodePrefix);
			$numbergen=$checkGateNumberSettingsExist[0][0];
			//$objResponse->alert($numbergen);
			$checkExist=$manageGatePassObj->checkGatePassDisplayExist($numbergen);
			//$objResponse->alert($checkExist);
			if ($checkExist>0)
			{
				$getFirstRecord=$manageGatePassObj->getmaxGatePassId($numbergen);
				$getFirstRec= $getFirstRecord[0];
				//$objResponse->alert($getFirstRec);
				$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
				//$objResponse->alert($alphaCodePrefix);
				$nextGatePassId=$getFirstRecEx[1]+1;
				$validendno=$manageGatePassObj->getValidendnoGatePassId($selDate,$compId,$invUnit);
				//$objResponse->alert($nextGatePassId);
				if ($nextGatePassId>$validendno)
				{
					$PurchaseOrderMsg="Please set the Purchase Order Id in Settings,since it reached the end no";
					$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
					$objResponse->assign("gatePassNo","value","");	
				}
				else
				{
					$disGateNo="$alphaCodePrefix$nextGatePassId";
					//$disGateNo="$nextGatePassId";
					//$objResponse->alert($disGateNo);
					$objResponse->assign("gatePassNo","value","$disGateNo");	
					$objResponse->assign("number_gen_id","value","$numbergen");	
					$objResponse->assign("divPOIdExistTxt","innerHTML","");
				}
			
			}
			else
			{
				
				//$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and billing_company_id='$compId' and uitid='$invUnit' OR date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='MGP' and billing_company_id='$compId' and uitid='$invUnit'";
				$validPassNo=$manageGatePassObj->getValidGatePassId($selDate,$compId,$invUnit);	
				$checkPassId=$manageGatePassObj->chkValidGatePassId($selDate,$compId,$invUnit);
				$disGatePassId="$alphaCodePrefix$validPassNo";
				//$disGatePassId="$validPassNo";
				//$objResponse->alert($disGatePassId);	
				$objResponse->assign("gatePassNo","value","$disGatePassId");	
				$objResponse->assign("number_gen_id","value","$numbergen");	
				$objResponse->assign("divPOIdExistTxt","innerHTML","");
			}
			
		}
		else
		{
		//$objResponse->alert("hi");
			$PurchaseOrderMsg="Please set the Purchase Order Id in Settings" ;
			//$PurchaseOrderMsg=$qry ;
			$objResponse->assign("gatePassNo","value","");	
			$objResponse->assign("divPOIdExistTxt","innerHTML",$PurchaseOrderMsg);
		}
	
		return $objResponse;
	}

	

// showLoading, hideLoading
$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPONumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->ProcessRequest();
?>