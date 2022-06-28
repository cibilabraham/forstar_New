<?php
require_once("libjs/xajax_core/xajax.inc.php");
require_once 'components/base/DailyFrozenPacking_model.php';

$xajax = new xajax();	
$xajax->configure( 'debug', false ); 

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
	}

	# Processors List
	function getProcessors($fromDate, $toDate, $selProcessorId)
	{
		$objResponse 	= new NxajaxResponse();
		$dFrznPkg_m 	= new DailyFrozenPacking_model();
		
		$processorRecs = $dFrznPkg_m->findAllForSelect("id", "name", "--Select--", " category_id='".$categoryId."'");

		$objResponse->addCreateOptions('selProcessor', $processorRecs, $selProcessorId);

		return $objResponse;
	}	
	

$xajax->register(XAJAX_FUNCTION, 'getProcessors', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->ProcessRequest();
?>