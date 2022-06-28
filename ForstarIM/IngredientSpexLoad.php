<?php
	$tab = $_GET["tab"];
	$spexArr = array('IngredientMainCategory.php', 'IngredientCategory.php', 'IngredientCriticalParameters.php', 'IngredientRateList.php', 'IngredientsMaster.php', 'IngredientRateMaster.php', 'SupplierIngredient.php','IngredientPhysicalStock.php');	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>