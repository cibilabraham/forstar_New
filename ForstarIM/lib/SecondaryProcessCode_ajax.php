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

	function getProcessCode($fishId,$inputId,$fish)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$secondaryProcessCodeObj			= new SecondaryProcessCode($databaseConnect);
		$processCodeRec = $secondaryProcessCodeObj->getProcessCode($fishId);
		if (sizeof($processCodeRec)>0) $objResponse->addDropDownOptions("processCode_$inputId", $processCodeRec, $fish );
		return $objResponse;
	}

	function getGrade($fishId,$processCodeId,$inputId,$process)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$secondaryProcessCodeObj			= new SecondaryProcessCode($databaseConnect);
		$gradeRec = $secondaryProcessCodeObj->getGrade($fishId,$processCodeId);
		if (sizeof($gradeRec)>0) $objResponse->addDropDownOptions("grade_$inputId", $gradeRec, $grade);
		return $objResponse;
	}



/*	# Check Grade Rec Exist
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
*/


$xajax->register(XAJAX_FUNCTION, 'getGrade', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCode', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>