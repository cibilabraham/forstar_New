<?php
require_once("lib/databaseConnect.php");
require_once('processcode_class.php');
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure( 'defaultMode', 'synchronous' ); // For return value

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


	# Check Grade Rec Exist
	function chkPCGradeUsage($processCodeId, $gradeId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$processcodeObj		= new ProcessCode($databaseConnect);
		
		$chkRecExist = $processcodeObj->pcGradeRecInUse($processCodeId, $gradeId);
		//if ($chkRecExist) $objResponse->alert("The selected grade is already in use.");
		$objResponse->setReturnValue($chkRecExist);
		return $objResponse;
	}
	

$xajax->registerFunction("chkPCGradeUsage");


$xajax->register(XAJAX_FUNCTION, 'chkPCGradeUsage', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));


$xajax->ProcessRequest();
?>