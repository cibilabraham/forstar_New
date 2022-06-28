<?php
	$tab = $_GET["tab"];	

	$spexArr = array("ProductionMatrix.php","ProductionWorkingHours.php","ProductionFuelPrice.php","ProductionOtherCost.php","ProductionAdvertisement.php","ProductionPower.php","ProductionMarketingCost.php","ProductionOperation.php");	
	//$spexArr = array("ProductionMatrixMaster.php", "ProductionMatrix.php","ProductionWorkingHours.php","ProductionFuelPrice.php","ProductionOtherCost.php","ProductionAdvertisement.php","ProductionPower.php","ProductionMarketingCost.php");	
	//$spexArr = array("ProductionMatrixMaster.php", "ProductionMatrix.php","ProductionWorkingHours.php","FuelRate.php");	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>