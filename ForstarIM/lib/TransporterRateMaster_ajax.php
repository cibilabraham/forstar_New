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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}			
  		}
	}
	
	# State Exist
	/*
	function chkEntryExist($transporterId, $zoneId, $mode, $currentId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$transporterRateMasterObj	= 	new TransporterRateMaster($databaseConnect);
		$chkRecExist 		= $transporterRateMasterObj->checkEntryExist($transporterId, $zoneId, $transporterRateList, $currentId);
		if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableTransporterRateButton($mode);");
		} else  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableTransporterRateButton($mode);");
		}
		return $objResponse;
	}
	*/
	
	# Get Transporter Rate List  
	function getTransporterRateRec($transporterId, $zoneId, $mode, $currentId, $transporterFunctionType)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$transporterRateMasterObj	= new TransporterRateMaster($databaseConnect);
		$transporterRateListObj		= new TransporterRateList($databaseConnect);

		# Rate List Id
		$rateListId = $transporterRateListObj->latestRateList($transporterId, $transporterFunctionType);

		# Check Rec Exist
		$chkRecExist 	= $transporterRateMasterObj->checkEntryExist($transporterId, $zoneId, $rateListId, $currentId);
		if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableTransporterRateButton($mode);");
		} else  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableTransporterRateButton($mode);");
		}

		$objResponse->assign("transporterRateList", "value", $rateListId);
		return $objResponse;
	}

	# Get Area Demarcation
	function getAreaDemarcation($zoneId)
	{
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$transporterRateMasterObj	= new TransporterRateMaster($databaseConnect);
		if ($zoneId) $zoneWiseAreaRecs = $transporterRateMasterObj->getZWAreaDemarcationRecs($zoneId);
		
		$areaRec	= "<fieldset><legend class='fieldName' style='line-height:normal;'>Area Demarcation</legend><table><tr>";
				$numLine = 4;
				if (sizeof($zoneWiseAreaRecs)>0) {
					$nextRec	=	0;
					$k=0;
					$selName = "";
					foreach ($zoneWiseAreaRecs as $zr) {
						
						$j++;
						$selName = $zr[0];
						$nextRec++;
		$areaRec	.= "<td class='listing-item'>";
					 if ($nextRec>1) {
		$areaRec	.=  ",";	
					}
		$areaRec	.= "$selName</td>";
				   	 if($nextRec%$numLine == 0) { 
		$areaRec	.= "</tr><tr>";
					  }	
					}
				} else if ($zoneId) {
		$areaRec	.= "<tr><td class='err1'>Please define area demarcation for the selected zone.</td><tr>";
				}
		$areaRec	.= "</tr></table></fieldset>";
		
		$objResponse->assign("areaDemarcation", "innerHTML", $areaRec);
		return $objResponse;
	}


	/**
	* Wt Slab List
	*/
	function getWtSlabList($transporterId, $mode, $transporterRateId)
	{
		$objResponse 			= new NxajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$transporterRateMasterObj	= new TransporterRateMaster($databaseConnect);
		
		# Transporter Wt Slab List
		$wtSlabListRecs = $transporterRateMasterObj->getWtSlabListRecs($transporterId);
		
		$trptrRateTypeArr = array("RPW"=>"Rate Per Kg", "FRC"=>"Fixed Rate");
		/*
		bgcolor='#999999'
		bgcolor='#f2f2f2' 
		 bgcolor='white'
		*/
		if (sizeof($wtSlabListRecs)>0) {
		$wtSlabList	= "<table cellspacing='1' cellpadding='2' class='newspaperType'>
					<thead>
					<TR align='center'>
						<th class='listing-head' style='padding-left:5px; padding-right:5px; text-align:center;'>Weight Slab</th>
						<th class='listing-head' nowrap style='padding-left:5px; padding-right:5px; text-align:center;'>Rate</th>
						<th class='listing-head' nowrap style='padding-left:5px; padding-right:5px; text-align:center;'>Type</th>
					</TR>
					</thead>
					<tbody>
				";
			$m= 0;
			foreach ($wtSlabListRecs as $wsr) {
				$m++;
				$weightSlabId	= $wsr[2];		
				$name 		= stripSlash($wsr[3]);
				$trptrWtSlabEntryId = $wsr[1];
				
				$slabRate = "";
				$transporteRateEntryId = "";
				$trptrRateType = "";	
				if ($mode==2) {
					list ($transporteRateEntryId, $slabRate, $trptrRateType) = $transporterRateMasterObj->getWtSlabRate($transporterRateId, $weightSlabId);
				}
				
				$nM = $m+1;
		$wtSlabList .= "<TR>
					<td class='listing-item' style='padding-left:5px; padding-right:5px;'>
						$name
						<input type='hidden' name='weightSlabId_$m' id='weightSlabId_$m' value='$weightSlabId'>
						<input type='hidden' name='trptrWtSlabEntryId_$m' id='trptrWtSlabEntryId_$m' value='$trptrWtSlabEntryId'>
						<input type='hidden' name='transporterRateEntryId_$m' id='transporterRateEntryId_$m' value='$transporteRateEntryId'>
					</td>
					<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px;'>
						<input type='text' name='rate_$m' id='rate_$m' size='4' style='text-align:right;' value='$slabRate' onkeypress=\"return nextTBox(event,'document.frmTransporterRateMaster','rate_$nM');\" autocomplete='off'>
					</td>
					<td class='listing-item' nowrap style='padding-left:5px; padding-right:5px;'>
						<select name='trptrRateType_$m' id='trptrRateType_$m'>";
							foreach ($trptrRateTypeArr as $rtKey=>$rtVal) {
								$selected = ($rtKey==$trptrRateType)?'selected':"";
					$wtSlabList	.= "<option value='$rtKey' $selected>$rtVal</option>";
							}
					$wtSlabList	.="</select></td>	
					</TR>
					<input type='hidden' name='hidTableRowCount' id='hidTableRowCount' value='$m'>
					";					
				}
		$wtSlabList	.=	"</tbody></table>";
		} else if ($transporterId) {
			$wtSlabList = "<span class='err1'>Please define transporter wise Weight slab </span>";
		}
		$objResponse->assign("WtSlabExist", "value", sizeof($wtSlabListRecs));
		$objResponse->assign("wtSlabList", "innerHTML", $wtSlabList);
		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'getTransporterRateRec', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getAreaDemarcation', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getWtSlabList', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));			


$xajax->ProcessRequest();
?>