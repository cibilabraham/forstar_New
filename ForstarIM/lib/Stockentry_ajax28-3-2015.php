<?php
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

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

		// For Edit Mode
		function addCityOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $ov) {
					$this->script("addOption('".$ov[2]."','".$sSelectId."','".$ov[0]."','".$ov[1]."');");
	       			}
	     		}
  		}		
	}

	# label Usage Status
	function fieldUsageStatus($categoryId, $subCategoryId, $fieldLabelId, $rowId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$stockGroupObj	= new StockGroup($databaseConnect);
		$subcategoryObj	= new SubCategory($databaseConnect);
		
		if ($subCategoryId==0) {
			# Used SubCategory Recs
			$usedRecs = $stockGroupObj->getLabelUsageRecs($categoryId, $subCategoryId, $fieldLabelId);
			# Get all Sub-category records
			$subCatRecords	= $subcategoryObj->filterSubCategoryRecords($categoryId);
			# Get Not Used SubCategory Recs
			$notUsedRecs     = ary_diff($subCatRecords, $usedRecs); // From config
			
				
			#--------------------------------
			$count =  0;
			$stkGEntryArr = array();
			if (sizeof($usedRecs)>0) {		
				$uc = 0;
				foreach ($usedRecs as $uscr) {
					$subCatId = $uscr[0];					
					# Group Entry Id
					$stkGroupEntryId = $stockGroupObj->getStkGroupId($categoryId, $subCatId, $fieldLabelId);
					# Check Field Group used in Stock Entry
					$fieldGroupExist = $stockGroupObj->chkFieldGroupExist($categoryId, $subCatId, $stkGroupEntryId);
					
					if ($fieldGroupExist) $count++;
					$stkGEntryArr[$uc] = $stkGroupEntryId;
					$uc++;
				}
				$stkgEntryIds = implode(",",$stkGEntryArr);
			}			
			$objResponse->assign("stkGroupEntryIds_$rowId","value", $stkgEntryIds);			
			$objResponse->assign("stkGroupInUse_$rowId","value", $count);
			#---------------------------------
	
			$displayStatus = "";
				if (sizeof($usedRecs)>0) {
					$displayStatus = "<table class='newspaperType' cellpadding='0' cellspacing='0'><tr align='center'><th class='listing-head' style='font-size:9px;text-align:center;'>Used</th><th class='listing-head' style='font-size:9px;text-align:center;line-height:normal;'>Not Used</th></tr>";
					$displayStatus .= "<tr>";
					$displayStatus .= "<td>";
							$displayStatus	.= "<table id='newspaper-b1-no-style'><tr>";
									$numLine = 4;
									if (sizeof($usedRecs)>0) {
										$nextRec	=	0;
										$k=0;
										$selName = "";
										foreach ($usedRecs as $zr) {
											
											$j++;
											$selName = $zr[1];
											$nextRec++;
							$displayStatus	.= "<td class='listing-item' style='font-size:9px; line-height:normal;'>";
										if ($nextRec>1) {
							$displayStatus	.=  ",";	
										}
							$displayStatus	.= "$selName</td>";
										if($nextRec%$numLine == 0) { 
							$displayStatus	.= "</tr><tr>";
										}	
										}
									} 
							$displayStatus	.= "</tr></table>";
							
					$displayStatus .= "</td>";
					$displayStatus .= "<td>";
							$displayStatus	.= "<table id='newspaper-b1-no-style'><tr>";
									$numLine = 4;
									if (sizeof($notUsedRecs)>0) {
										$nextRec	=	0;
										$k=0;
										$selName = "";
										foreach ($notUsedRecs as $zr) {
											
											$j++;
											$selName = $zr[1];
											$nextRec++;
							$displayStatus	.= "<td class='listing-item' style='font-size:9px; line-height:normal;'>";
										if ($nextRec>1) {
							$displayStatus	.=  ",";	
										}
							$displayStatus	.= "$selName</td>";
										if($nextRec%$numLine == 0) { 
							$displayStatus	.= "</tr><tr>";
										}	
										}
									} 
							$displayStatus	.= "</tr></table>";
								
					$displayStatus .= "</td>";
					$displayStatus .= "</tr>";
					$displayStatus .= "</table>";
				}
		}
		$objResponse->assign("usageStatus_".$rowId, "innerHTML", $displayStatus);		
        	return $objResponse;	
	}


function getquickEntrylist($wtArrayCw,$wtArrayCp)
	{
	$objResponse 	= new NxajaxResponse();	
	$databaseConnect= new DatabaseConnect();
		$stockObj	= new Stock($databaseConnect);
	//$objResponse->alert($wtArrayCw);
	//$objResponse->alert($wtArrayCp);

	
	$frozenPackingRecords=$stockObj->getFrozenCodeWtquickEntry($wtArrayCw,$wtArrayCp);	

	$objResponse->addDropDownOptions("selFullFrozenCode", $frozenPackingRecords, $selMCPkgId);
	return $objResponse;	
	}

$xajax->register(XAJAX_FUNCTION, 'fieldUsageStatus', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getquickEntrylist', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
//$xajax->register(XAJAX_FUNCTION, 'getFrozenCodeWtquickEntry', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));

$xajax->ProcessRequest();
?>