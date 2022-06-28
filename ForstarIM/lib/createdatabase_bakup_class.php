<?php
class CreateDBBackup
{  
	/****************************************************************
	This class deals with all the operations relating to Billing Company Master ($billingCompanyObj)
	*****************************************************************/
	var $databaseConnect;	
	//Constructor, which will create a db instance for this class
	function CreateDBBackup(&$databaseConnect)
    {
       $this->databaseConnect =&$databaseConnect;
	}

	# Returns all Paging  Records
	function CreateDbBackupScript($userId,$DatabaseListObj,$created_by,$var_Backup_dir)
	{
		$trans_start = 0;
		//here transaction come
		$rsUsr = $this->getUsers_toblock($userId);
		if(sizeof($rsUsr)>0){
			for ($i=0; $i<=(sizeof($rsUsr)-1); $i++){	
				$uId = $rsUsr[$i][0];
				$msg = "Wait Database Backup Script is Running ...";
				$block_date = date('Y-m-d h:m:s');
				$ins_status = $this->addUser_toblock($uId,$msg,$block_date);
			}
			if($ins_status){
				$isexport=0;
				$isexport = $this->EXPORT_TABLES('localhost','root','ais2012','forstar_latest_im',$DatabaseListObj,$created_by,$var_Backup_dir);
			    $is_deleted = $this->deleteUser_toblock();
				if($is_deleted && $isexport) $trans_start = 1; else $trans_start = 0; 		
			}
			else{
				$trans_start = 0;
			}
		}
		// end code
		if ($trans_start=="1") {
			//echo("commit");
			$this->databaseConnect->commit();}
		else {
			//echo("rollback");
			$this->databaseConnect->rollback();}		
		//exit;
		return $trans_start;
	}
		function getUserBlock($userId)
	{
		$qry = "select notification_msg,block_date,is_block from m_tmpblockusers where user_id='$userId'";	
		//echo $qry;	
		//$rs		=	array();
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getUsers_toblock($userId)
	{
		$qry	= "select * from user where id <>'$userId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Add 
	function addUser_toblock($uId,$msg,$block_date)
	{
		$qry	= "insert into m_tmpblockusers (user_id,notification_msg,block_date,is_block) values('".$uId."','".$msg."','".$block_date."',  '1')";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		//if ($insertStatus) $this->databaseConnect->commit();
		//else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function deleteUser_toblock()
	{
		$qry = "delete from m_tmpblockusers";
		$result	=	$this->databaseConnect->delRecord($qry);
		//if ($result) $this->databaseConnect->commit();
		//else $this->databaseConnect->rollback();		
		return $result;
	}

function EXPORT_TABLES($host,$user,$pass,$name,$DatabaseListObj,$created_by,$var_Backup_dir,$tables=false, $backup_name=false){ 
    $isexport = 0;
	set_time_limit(3000); 
	$mysqli = new mysqli($host,$user,$pass,$name);
	$mysqli->select_db($name); 
	$mysqli->query("SET NAMES 'utf8'");
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
	//rekha updated dated on 31 aug 2018//
	//old code 
	//$basefolder = "F:/DailyBackup/Databases";
	$basefolder =$var_Backup_dir;
	file_put_contents($basefolder."/"."$backup_name",$content);
	$size = filesize($basefolder."/"."$backup_name");
	$size = formatSizeUnits($size);

	$DbRecIns = $this->add_dbbackup($basefolder,$backup_name, $size, $date_on, $created_by);
	if(file_exists($basefolder."/"."$backup_name")){
		$downloadFileurl = base64_encode($basefolder."/".$backup_name);
		$Download_link = "DownloadFile.php?file=".$downloadFileurl ; 
		$isexport =1;
	}	
	else{
		$isexport = 0;
		//$msg = "<span class=\"listing-item\" ><font color='red'>Failed to create the database backup. </font></span>";
		//echo $msg ;		
		}
	//echo $msg ;

	if (!$DbRecIns){
			$isexport = 0;
		//$msg_ins = "Database Record not added on datbase";
		//echo("<br><br>$msg_ins");
	}
	return $isexport ;
 }
	
	function add_dbbackup($add_dbbackup,$Db_filename, $size, $date_on, $created_by)
	{		
		$qry = "insert into m_dbbackup_history (Db_filename,Db_path,size, date_on, created_by) values('$Db_filename','$add_dbbackup', '$size', '$date_on', '$created_by') ";
		//echo $qry."<br>";			
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		//if ($insertStatus) $this->databaseConnect->commit();
		//else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

}
?>