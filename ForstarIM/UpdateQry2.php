<?php
require("include/include.php");
require("lib/UpdateQry_class.php");
$updateQryObj	= new UpdateQry($databaseConnect);

	/*
	$gppRateList = $updateQryObj->getPreProcessRateList();
	//foreach ($gppRateList as $prl) {
	for ($i=0;$i<sizeof($gppRateList);$i++) {
		$prl = 	$gppRateList[$i];
		$nPRL = $gppRateList[$i+1];	
		$rateListId	= $prl[0];
		$startDate	= $prl[2];
		# Prev Rate List
		$nPRLId		= $nPRL[0];
		$nPRLStartDate	= $nPRL[2];
		echo "<br>$rateListId, $startDate::::$nPRLId,$nPRLStartDate";
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		//echo "<br>------------<br>";		
		if ($nPRLId!="" && $endDate!="") {
			$updateQryObj->updateRateListRec($nPRLId, $endDate);
			echo "Update=>$nPRLId=$endDate";
		}
	}
	*/
?>
