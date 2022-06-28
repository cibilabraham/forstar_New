<?php
	require("include/include.php");
	$stockType  = $g["stockType"];

	$folderName	= "stock_xls";
	$fileExtension  = "csv";
	if ($stockType=='P') {
		$fileName = "StockEntry_Packing.csv";
		$path = $folderName."/".$fileName;
	} else if ($stockType=='O') {
		$fileName = "StockEntry_Ordinary.csv";
		$path = $folderName."/".$fileName;
	} else $fileName = "";
	//echo "$stockType=>$fileName, $fileExtension, $folderName";
	# Download the selected file
	if ($stockType!="")
		$downloadFile = $fileManageObj->downloadFile($fileName, $fileExtension, $path);	
?>

