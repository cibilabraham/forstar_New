<?php
	require("include/include.php");
	require("lib/config_query.php");

	$objCon                 = new Config_query($databaseConnect);
	
	
	if(isset($p['process_type']))
	{
		$where = array('process_type' => $p['process_type']);
		// if($p['process_type_already'] == '' || $p['process_type_already'] != $p['process_type'])
		// {
			$processTypes = $objCon->getItems('m_lotid_process_type','*',$where);
			if(sizeof($processTypes) > 0)
			{
				echo 'Process type already exists';
			}

		// }
	}
?>