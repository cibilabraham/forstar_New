<?php
	$tab = $_GET["tab"];	
	
	$spexArr = array('ProductCategory.php', 'ProductState.php', 'ProductGroup.php', 'ProductMaster.php', 'SemiFinishProductMaster.php', 'ProductPriceRateList.php', 'ProductPricing.php', 'ManageProduct.php', 'ProductMRPMaster.php', 'ProductMatrix.php');

	//$heightArr = array(550,550,550,600,600,450,450,450,450);
	//$heightArr[$tab-1]
	//600
?>
	
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>