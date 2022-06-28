<?php
backup_tables('localhost','root','ais2012','Forstar_latest_im');

/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	$tab_status = mysql_query("SHOW TABLE STATUS");	
	
	echo($tab_status);
	exit;
	
	
	
	//save file
	//$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}
	
?>