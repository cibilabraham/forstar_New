<?php
//require_once("MCPkgWtMaster_class.php");
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

	# Rec Exist
	function chkSelRecExist($mcPkgId, $mode, $mcPkgWtEntryId, $netWt)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$mcPkgWtMasterObj	= new MCPkgWtMaster($databaseConnect);

		list($chkSONumExist, $mcPkgWtId)	= $mcPkgWtMasterObj->checkMCPackingRecExist($mcPkgId, $mcPkgWtEntryId, $netWt);

		if ($chkSONumExist) {
			$mcPkgRecs = $mcPkgWtMasterObj->getMCPkgWtRec($mcPkgId, $netWt);
			
			$objResponse->script("disableStateVatButton($mode);");

			if ($mode==1) {
				$tbleHTML = '<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999">';
				$tbleHTML .= '<tr align="center">';
				$tbleHTML .= '<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;">Name</td>';
				$tbleHTML .= '<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;" nowrap="true">Net Wt<br/>(Gm)</td>';
				$tbleHTML .= '<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;" nowrap="true">MC<br> Pack</td>';
				$tbleHTML .= '<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;" nowrap="true">Pkg Wt<br/>(Kg)</td>';
				$tbleHTML .= '<td class="listing-head" style="padding-left:3px; padding-right:3px; line-height:normal;" nowrap="true">Pkg Wt <br/>Tol. (Gms)</td>';
				$tbleHTML .= '</tr>';
				$pkgWtArr = Array();
				foreach ($mcPkgRecs as $mcPkgRec) {
					$mcPackingCode	= $mcPkgRec[3];	
					$packageWt	= $mcPkgRec[2];
					$pkgWtArr[] = $packageWt;

					$netWtUnit	= $mcPkgRec[4];
					$name		= $mcPkgRec[5];
					$mcPkgWtTolerance = $mcPkgRec[6];
					$mcPkgWtTolerance = ($mcPkgWtTolerance!=0)?$mcPkgWtTolerance:"";
						$tbleHTML .= '<tr>';
						$tbleHTML .= '<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left">'.$name.'</td>';
						$tbleHTML .= '<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right">'.$netWtUnit.'</td>';
						$tbleHTML .= '<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right">'.$mcPackingCode.'</td>';
						$tbleHTML .= '<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right">'.$packageWt.'</td>';
						$tbleHTML .= '<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center">'.$mcPkgWtTolerance.'</td>';
						$tbleHTML .= '</tr>';
				}
				$tbleHTML .= "</table>";
				$objResponse->assign("displayExistingRec", "innerHTML", $tbleHTML);
				$objResponse->assign("displayExistingRec", "style.display", "block");

				//$objResponse->assign("divStateIdExistTxt", "innerHTML", "Please make sure the selected MC Pack Wt is not existing.");
				$objResponse->assign("divStateIdExistTxt", "innerHTML", "<br/>The above record is already exist for the selected Net Wt and MC Pack. <br>If you wish to add different Packing Wt for the same combination, click the enable button<br><input type='button' class='button' id='enablebtn' value=' Enable ' onclick='enableAddBtn($mode);' /><br/>");
				
				$objResponse->assign("existPkgWt", "value", implode(",",$pkgWtArr));
				$objResponse->assign("existPkgName", "value", $name);
			} else  {
				$objResponse->assign("divStateIdExistTxt", "innerHTML", "Please make sure the selected MC Pack Wt is not existing.");
			}
		} else  {
			$objResponse->assign("divStateIdExistTxt", "innerHTML", "");
			$objResponse->script("enableStateVatButton($mode);");

			$objResponse->assign("displayExistingRec", "innerHTML", "");
			$objResponse->assign("displayExistingRec", "style.display", "none");
			$objResponse->assign("existPkgWt", "value", "");
			$objResponse->assign("existPkgName", "value", "");
		}
		

		return $objResponse;
	}


$xajax->register(XAJAX_FUNCTION, 'chkSelRecExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>