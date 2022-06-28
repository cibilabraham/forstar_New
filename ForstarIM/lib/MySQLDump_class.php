<?php
class MySQLDump {
	/**
	* @access private
	*/
	var $database = null;

	/**
	* @access private
	*/
	var $username = null;
	
	/**
	* @access private
	*/
	var $password = null;


	/**
	* @access private
	*/
	var $compress = false;

	/**
	* The output filename
	* @access private
	*/
	var $filename = "";

	var $databaseConnect;
	
	/**
	* Class constructor
	* @param string $db The database name
	* @param string $filepath The file where the dump will be written
	* @param boolean $compress It defines if the output file is compress (gzip) or not
	*/
	function MYSQLDump($fn, &$databaseConnect){
		$this->databaseConnect =&$databaseConnect;
		$this->database = $this->databaseConnect->db;
		$this->username = $this->databaseConnect->user;
		$this->password = $this->databaseConnect->pwd;
		$this->fileName = $fn;		
	}

	function createFullBackup()
	{
			$db_connection = $this->db_connect();
			mysql_select_db ($this->db_name());
			$tab_status = mysql_query("SHOW TABLE STATUS");

			while($all = mysql_fetch_assoc($tab_status)):
				$tbl_stat[$all[Name]] = $all[Auto_increment];
			endwhile;
			unset($backup);
			$tables = mysql_list_tables($this->db_name());
			while($tabs = mysql_fetch_row($tables)):
				$backup .= "--\n--Table structure for `$tabs[0]`\n--\n\nDROP IF EXISTS TABLE `$tabs[0]`\nCREATE TABLE IF NOT EXISTS `$tabs[0]` (";

				$res = mysql_query("SHOW CREATE TABLE $tabs[0]");
				while($all = mysql_fetch_assoc($res)):
					$str = str_replace("CREATE TABLE `$tabs[0]` (", "", $all['Create Table']);
					$str = str_replace(",", ",", $str);
					$str2 = str_replace("`) ) TYPE=MyISAM ", "`)\n ) TYPE=MyISAM ", $str);
					$backup .= $str2." AUTO_INCREMENT=".$tbl_stat[$tabs[0]].";\n\n";
				endwhile;
				$backup .= "--\n--Data to be executed for table `$tabs[0]`\n--\n\n";
				$data = mysql_query("SELECT * FROM $tabs[0]");
				while($dt = mysql_fetch_row($data)):
					$backup .= "INSERT INTO `$tabs[0]` VALUES('$dt[0]'";
					for($i=1; $i<sizeof($dt); $i++):
						$backup .= ", '$dt[$i]'";
					endfor;
					$backup .= ");\n";
				endwhile;
				$backup .= "\n-- --------------------------------------------------------\n\n";
				$this->write_backup_sql($this->fileName,$backup);
			endwhile;
			return "Y";
  }

  function write_backup_sql($file, $string_in) { 
	$handle = fopen($file,"w");
    	fwrite($handle, $string_in);
   }
  
  function db_name() {
      return ($this->database);
  }
  
  function db_connect() {	
    $db_connection = mysql_connect($this->databaseConnect->server, $this->username, $this->password);
    return $db_connection;
  }  
}
?>