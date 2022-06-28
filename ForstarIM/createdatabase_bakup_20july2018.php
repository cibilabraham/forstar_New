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
		/*
		echo "<script type='text/javascript'>
				window.close();
			 </script" ;
	*/
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
require("template/topLeftNav.php");
//print_r($p);
# script to execute backup 
	# db backup  
	if ($p["cmdcreatebackup"]!="") {
		// block users 
		$rsUsr = $userObj->getUsers_toblock($userId);
		if(sizeof($rsUsr)>0){
			for ($i=0; $i<=(sizeof($rsUsr)-1); $i++){	
				$uId = $rsUsr[$i][0];
				$msg = "Wait Database Backup Script is Running ...";
				$block_date = date('Y-m-d h:m:s');
				$rsaddUsr = $userObj->addUser_toblock($uId,$msg,$block_date);
			}
		}
		//export data in .sql file  
		EXPORT_TABLES('localhost','root','ais2012','forstar_latest_im',$DatabaseListObj,$created_by);
		//unblock users
		$is_deleted = $userObj->deleteUser_toblock();
		if ($is_deleted){ 
			header("location:DatabaseListMaster.php");
		}	
	}	
	#end code 
?>
<!-- tag here -->
	<form name="frmcreatedbbackup" action="createdatabase_bakup.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Database Backup";
								include "template/boxTL.php";
							?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="4" height="5" >
									</td>
								</tr>
		<tr>
			<td colspan="4" height="10" align='center'> <a href='DatabaseListMaster.php' class="link1"><strong>Database Backup List</strong></a></td>
		</tr>
		<tr>
			<td colspan="4" height="5" align='center'></td>
		</tr>
												
		<tr>
			<td colspan="4" height="5" ></td>
		</tr>
		<tr >	
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td>
						<input type='submit' name='cmdcreatebackup' id='cmdcreatebackup' value='Generate Database Backup File' class="button" onclick="javascript: return confirm('You want to Generate Database Backup File? \n If yes click on OK and If no click on Cancel button.');" style='height:30;font-size:11px;'>&nbsp;&nbsp;<input type='button' name='cmdcancel' id='cmdcancel' value='Cancel' class="button" onclick="javascript: window.location.href='DatabaseListMaster.php';" style='height:30;font-size:11px;'></td>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<tr>
			<td colspan="4" height="5" ></td>
		</tr>								
		</table>
		<?php
			include "template/boxBR.php"
		?>
						</td>
					</tr>
		</table>
				<!-- Form fields end   -->
			</td>
			
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	</form>
	
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
