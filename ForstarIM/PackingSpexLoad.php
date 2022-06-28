<?php
	$tab = $_GET["tab"];	

	
	$spexArr = array("PackingCostMaster.php","PackingMaterial.php", "PackingMatrix.php");	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>