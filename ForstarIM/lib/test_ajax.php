<?php
require_once("libjs/xajax_core/xajax.inc.php");
$xajax = new xajax();	

require_once("libjs/xajax_core/jquery.php");
$xajax->setFlag('debug',true);

	class NxajaxResponse extends xajaxResponse
	{ 
		
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   				if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}
	}

	function show() {
		$objResponse = new NxajaxResponse();
		//$objResponse->assign("div1","innerHTML",1);
		$objResponse->jquery->show("#div1");
		return $objResponse;
	}

	function hide() {
		$objResponse = new NxajaxResponse();
		$objResponse->jquery->hide("#div1");
		
		return $objResponse;
	}
	


$xajax->registerFunction("show");
$xajax->registerFunction("hide");


$xajax->ProcessRequest();
?>