<?php
	$tab = $_GET["tab"];	

	
	$spexArr = array("ProductionManPower.php", "ProductionFishCutting.php", "ProductionMarketing.php", "ProductionTravel.php", "PackingLabourCost.php", "PackingSealingCost.php", "PackingMaterialCost.php");	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>