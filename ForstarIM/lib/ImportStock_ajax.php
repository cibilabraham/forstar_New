<?php
	require_once("lib/databaseConnect.php");
	require_once("lib/subcategory_class.php");
	require_once("lib/stockentry_class.php");
	require_once("libjs/xajax_core/xajax.inc.php");
	
	$xajax = new xajax(); // create xajax ref 
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

	function getSubCategories($catId, $rowId)
	{
		$objResponse = new NxajaxResponse();
		$dbc = new DatabaseConnect();	
		$scato = new SubCategory($dbc);
		$subCatList = $scato->getAssocSubCategories($catId);
		$objResponse->script("document.getElementById('selUnit_$rowId').value='';");
		$objResponse->addCreateOptions("subCategory_".$rowId, $subCatList,"");
		return $objResponse;		
	}

	function getUnits( $subCatId, $rowId )
	{
		$objResponse = new NxajaxResponse();
		$dbc = new DatabaseConnect();	
		$so = new Stock($dbc);
		$unitRecords = $so->getAssocUnitRecs($subCatId);	
		$objResponse->addCreateOptions("selUnit_".$rowId, $unitRecords,"");
		return $objResponse;		
	}

	function goBackPage()
	{
		header("Location:ImportStock.php"); 
	}
			
	$xajax->registerFunction("getSubCategories");
	$xajax->registerFunction("getUnits");
	$xajax->registerFunction("goBackPage");
	$xajax->processRequest(); // xajax end
?>