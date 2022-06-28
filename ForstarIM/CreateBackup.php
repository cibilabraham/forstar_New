<?php
	require("include/include.php");
	require("lib/MySQLDump_class.php");
	$msg = "";
	$status = "";
	$date= Date("mdyhis");	
	/*
	$filename = dirname(__FILE__)."/db_backup/IM_".$date.".sql";
	$filename2 = base64_encode(dirname(__FILE__)."/db_backup/IM_".$date.".sql");
	*/

	$filename = "db_backup/IM_".$date.".sql";
	$filename2 = base64_encode("db_backup/IM_".$date.".sql");

	$mysqlDump = new MySQLDump($filename, $databaseConnect);
	$createDatabaseBackup = $mysqlDump->createFullBackup();

	if($createDatabaseBackup!=""){		
		$status = "";
		$msg = "<span class=\"listing-item\" >Successfully created the database backup.<br><br>Click <a href=\"DownloadFile.php?file=$filename2\" title=\"Click here to download backup file.\">here</a> to download. </br></span>";
	}
	else $msg = "<span class=\"listing-item\" ><font color='red'>Failed to create the database backup. </font></span>";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<br><br>
<center>
<?
		if( $status!="" ) echo $status;
		else echo $msg;
?>
</center>
<?

	
	
	

	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>