<?php
	$tab = $_GET["tab"];	
	
	$spexArr = array('TransporterMaster.php', 'ZoneMaster.php', 'WeightSlabMaster.php', 'TransporterRateList.php', 'TransporterOtherCharges.php', 'TransporterWeightSlab.php', 'TransporterRateMaster.php', 'TransporterCost.php', 'TransporterStatus.php');	
?>
	<iframe src="<?=$spexArr[$tab-1]?>" width="100%" height="600" frameborder="0"></iframe>