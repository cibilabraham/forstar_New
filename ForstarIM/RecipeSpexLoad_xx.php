<?php
	$tab = $_GET["tab"];	
	$spexArr = array('RecipeMainCategory.php', 'RecipeMaster.php', RecipeRateList.php', 'RecipeRateMaster.php', 'SupplierRecipe.php');	
	//$spexArr = array('RecipeMainCategory.php', 'RecipeSubCategory.php', 'RecipeMaster.php', 'RecipeRateList.php', 'RecipeRateMaster.php', 'SupplierRecipe.php');	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0">

</iframe>
?>

