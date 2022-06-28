<?php 
date_default_timezone_set ("Asia/Calcutta");
class LogManager
{
	//var $logFile = "user_log/userLog.txt";
	var $logFileName = "userLog.log";
	var $logFile 	 = "user_log/userLog.log";
	var $logFolder   = "user_log";
	
	var $databaseConnect;
	var $sessObj;

	//Constructor, which will create a db instance for this class
	function LogManager(&$databaseConnect, &$sessObj)
    	{	
        	$this->databaseConnect  = &$databaseConnect;
		$this->sessObj		= &$sessObj; 
	}	

	function userLog($msg, $userId)
	{
		$this->clearLogFile($this->logFile);
		$date = date('d.m.Y h:i:s');
    		$log = $msg."   |  Date:  ".$date."  |  User:  ".$userId."\n";
    		error_log($log, 3, $this->logFile);	
	}

	function clearLogFile($fileName)
	{
		$max_size = 1024;
		if (filesize($fileName)>=$max_size) {
			$fp = fopen($fileName, 'w');
			fwrite($fp, '');
			fclose($fp);
		}
	}

	# 10485760 - 10 MB
	# 1048576  - 5 MB
	function createLogFile($data)
	{
		$logFileSize  = 51200;
		$userName = $this->sessObj->getValue("userName");
		$userId	  = $this->sessObj->getValue("userId");

		$data = "\n".date("j M Y H:i:s")."|".$userId."|".$userName."|".$data;

		try {
			$cwd = getcwd();
			//if (file_exists("$cwd/".$this->logFile) && filesize("$cwd/".$this->logFile)>$logFileSize) {
			if (filesize("$cwd/".$this->logFile)>$logFileSize) {				
				rename("$cwd/".$this->logFile, "$cwd/".$this->logFolder."/".date("Ymdhis")."_".$this->logFileName);
			}

			$fh = fopen($this->logFile, 'a+') or die("can't open file");
			fwrite($fh, $data);
			fclose($fh);
			return true;
		}

		catch (Exception $e) {
			echo 'Log Exception Exist: '. $e->getMessage(). "<br>";

		}
		return false;

	}

	# Read Log
	function readLogFile()
	{
		$logArr = array();
		$cwd = getcwd();
		if (file_exists("$cwd/".$this->logFile)) {
			$fh = fopen($this->logFile, 'r') or die("can't open file");	
			$contents = fread($fh, filesize($this->logFile));
			fclose($fh);
	
			$extractData = explode("\n",$contents);
			foreach ($extractData as $id=>$content) {
				if ($content=="") continue;
				$logData = explode("|", $content);
				$date = $logData[0];
				$userId = $logData[1];
				$userName = $logData[2];
				$url = $logData[3];
				$logArr[$userName][] = array($date,$url);
			}
		}	
		return $logArr;
	}

	# Read files within the directory
	function readDirectory()
	{
		$dirArr = array();
		if ($handle = opendir($this->logFolder)) {			
			//echo "Files:\n";
			
			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				//echo "$file\n";
				if ($file != "." && $file != "..") {
					$dirArr[] = $file;
				}
			}			
			
			closedir($handle);
		}
		return $dirArr;
	}

	# Delete a log file
	function deleteLogFile($filename)
	{		
		$cwd = getcwd();
		unlink("$cwd/".$this->logFolder."/".$filename);
	}
	

}
?>