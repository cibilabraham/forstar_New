<?php

require("include/include.php");
$created_by = $userObj->getUserName($userId);
function EXPORT_TABLES($host,$user,$pass,$name,$DatabaseListObj,$created_by,$tables=false, $backup_name=false){ 
    set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
    $queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }   if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); } 
    $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
    foreach($target_tables as $table){
        if (empty($table)){ continue; } 
        $result = $mysqli->query('SELECT * FROM `'.$table.'`');     $fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows;     $res = $mysqli->query('SHOW CREATE TABLE '.$table); $TableMLine=$res->fetch_row(); 
        $content .= "\n\n".$TableMLine[1].";\n\n";
        for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
            while($row = $result->fetch_row())  { //when started (and every after 100 command cycle):
                if ($st_counter%100 == 0 || $st_counter == 0 )  {$content .= "\nINSERT INTO ".$table." VALUES";}
                    $content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}     if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
                //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";} $st_counter=$st_counter+1;
            }
        } $content .="\n\n\n";
    }
 	
	$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	$backup_name = $backup_name ? $backup_name : $name."_(".date('H-i-s')."_".date('d-m-Y').").sql";
	$date_on = date('Y-m-d');
	//$basefolder = "DailyDBBackup";
	$basefolder = "F:/DailyBackup/Databases";
	file_put_contents($basefolder."/"."$backup_name",$content);
	$size = filesize($basefolder."/"."$backup_name");
	$size = formatSizeUnits($size);

	$DbRecIns = $DatabaseListObj->adddbbackup($backup_name, $size, $date_on, $created_by);
	if(file_exists($basefolder."/"."$backup_name")){

		$downloadFileurl = base64_encode($basefolder."/".$backup_name);
		$Download_link = "DownloadFile.php?file=".$downloadFileurl ; 
		//$msg = "<span class=\"listing-item\" >Successfully created the database backup.<br><br>Click <a href='$Download_link' title=\'Click here to download backup file.\'>here</a> to download. </br></span>";
		//header("location:DatabaseListMaster.php");
		// window.opener.location.reload();
		echo "<script type='text/javascript'>
				window.close();
			 </script>" ;
	
	}	
	else{
		$msg = "<span class=\"listing-item\" ><font color='red'>Failed to create the database backup. </font></span>";
		echo $msg ;		
		}
	//echo $msg ;

	if (!$DbRecIns){
		$msg_ins = "Database Record not added on datbase";
		echo("<br><br>$msg_ins");
	}

 }      //see import.php too
	EXPORT_TABLES('localhost','root','ais2012','forstar_latest_im',$DatabaseListObj,$created_by);
	//$DbRecIns = $DatabaseListObj->adddbbackup($backup_name, $size, $date_on, $created_by);

function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
	?>
