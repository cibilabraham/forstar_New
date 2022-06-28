<?php

require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}
			
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}
	
	
	function getCompanyDetails($companyId)
	{
		
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$billingCompanyObj		=new BillingCompanyMaster($databaseConnect);
		$companyRecs 			= $billingCompanyObj->find($companyId);
		$companyContactDetailsRecs 			= $billingCompanyObj->findContactdetail($companyId);
		if(sizeof($companyContactDetailsRecs)>0)
		{
			$telephoneNo=''; $fax='';
			foreach($companyContactDetailsRecs as $cdt)
			{
				if($cdt[1]!='')
				{
					if($telephoneNo=='')
					{
						$telephoneNo=$cdt[1];
					}
					else
					{
						$telephoneNo.=' , '.$cdt[1];
					}
				}
				if($cdt[3]!='')
				{
					if($fax=='')
					{
						$fax=$cdt[3];
					}
					else
					{
						$fax.=' , '.$cdt[3];
					}
				}
			}
		}
		if(sizeof($companyRecs)>0)
		{
			$objResponse->assign("address", "value", "$companyRecs[2]");
			$objResponse->assign("place", "value", "$companyRecs[3]");
			$objResponse->assign("pinCode", "value", "$companyRecs[4]");
			$objResponse->assign("country", "value", "$companyRecs[5]");
			$objResponse->assign("telNo", "value", "$telephoneNo");
			$objResponse->assign("faxNo", "value", "$fax");
			$objResponse->assign("alphaCode", "value", "$companyRecs[8]");
			$objResponse->assign("displayName", "value", "$companyRecs[9]");
		}
		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION, 'getCompanyDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->ProcessRequest();
?>