<?php
set_time_limit(0);    //Set no execution time
date_default_timezone_set('Asia/Kolkata');
class DatabaseConnect_taskmgmt
{
	var $server   = "localhost" ;		//MySql Server Name/IP
    var $user     = "root";			//Mysql DB User Name
    var $pwd      = "ais2012" ;			//Mysql DB User Password   ais2008
	var $db       = "Forstar_taskmgmt";
	var $conn_task     = "";
	
	function DatabaseConnect_taskmgmt()
	{
		$this->conn_task = mysql_connect($this->server,$this->user, $this->pwd) or die("Could not connect ".mysql_errno().": ".mysql_error());
		
		// mysql_db_query($this->db,"SET AUTOCOMMIT=0",$this->conn);
		mysql_select_db($this->db);
		//die($this->conn);
	}

	function getRecord($query)
 	{
		$recList = $this->getRecords($query,1);
		if ( sizeof($recList) > 0 ) return $recList[0];
		return array();
		/**
		$values = array();
   		$stmt = mysql_db_query ($this->db,$query, $this->conn);

   		if ($stmt == false)
   		{
   			return mysql_errno().": ".mysql_error()."<br>";
 			// return $query;
   		}

   		while ($fields = mysql_fetch_row ($stmt))
   		{
    			$values[] = $fields;
				break;
   		}

		@mysql_free_result ($stmt);
			return $values;
		//return $query;
		**/
  	}

	// limit is used to specify a coun of the number of records to fetch.
	function getRecords($query,$limit=0)
 	{
		$values = array();
   		//$stmt = mysql_db_query ($this->db,$query, $this->conn); // hide on 16-07-09
		$stmt = mysql_unbuffered_query($query, $this->conn_task);
   		if ($stmt == false) {
   			return mysql_errno().": ".mysql_error()."<br>"; 			
   		}

   		while ($fields = mysql_fetch_row ($stmt))
   		{
    			$values[] = $fields;
				if ( $limit == 1 ) break;
   		}

		@mysql_free_result ($stmt);
       		return $values;
  	}
	

  	function delRecord($query)
	{
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
    		if ( $stmt != false ) @mysql_free_result ($stmt);
		return $stmt;
		//return $query;
	}

	function updateRecord($query)
	{
		
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
		if ( $stmt == "" )	{
			echo "error=". mysql_errno().": ".mysql_error()."<br>";
		}
		if ( $stmt != false ) @mysql_free_result ($stmt);
		return $stmt;
		
	}

	function insertRecord($query)
	{
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
    		if ( $stmt != false) @mysql_free_result ($stmt);
		return $stmt;

	}

	function lastInserted()
    	{
    		$values = array();
    		$query="Select LAST_INSERT_ID() lid";

		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);

    		if ($stmt == false)
    		{
    			return mysql_errno().": ".mysql_error()."<br>";
    		}

    		while ($fields = mysql_fetch_row ($stmt))
    		{
    			$values[] = $fields;
    		}
		@mysql_free_result ($stmt);
		return $values;
    	}

	function createTable($query)
	{
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
    		if ( $stmt != false ) @mysql_free_result ($stmt);
		return $stmt;
	}

	 function commit()
	 {
		$query="COMMIT";
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
	 }

	 function rollback()
	 {
		$query="ROLLBACK";
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
	 }
	 
	 function getLastInsertedId()
    {
		$lid=0;
		$values = array();
		$query="Select LAST_INSERT_ID() lid";
		$stmt = mysql_query ($query, $this->conn_task);
		if ($stmt == false)	{
			$this->err_no		=	mysql_errno();
			return mysql_errno().": ".mysql_error()."<br>";
		}
		while ($fields = mysql_fetch_row ($stmt))
		{
			$lid = $fields[0];
			break;
		}
		@mysql_free_result ($stmt);
		return $lid;
    }

	/**
	* Desc: This function will return restultset (my_sql statement)
	* @param $query: select  query 
	* return value: return mysql restultset 
	**/

	function getResultSet($query)
 	{
		$stmt	= &mysql_query ($query, $this->conn_task);
		if ($stmt == false) {
			$this->err_no		=	mysql_errno();
   			return mysql_errno().": ".mysql_error()."<br>";
   		}
		return $stmt;
	}

	// limit is used to specify a coun of the number of records to fetch.
	#  using m
	function fetchRecords($query,$limit=0)
 	{
		$values = array();
   		//$stmt = mysql_db_query ($this->db,$query, $this->conn_task);
		$stmt = mysql_unbuffered_query($query, $this->conn_task);
   		if ($stmt == false) {
   			return mysql_errno().": ".mysql_error()."<br>"; 		
   		}
   		while ($fields = mysql_fetch_row ($stmt))
   		{
    			$values[] = $fields;
				if ( $limit == 1 ) break;
   		}

		@mysql_free_result ($stmt);
        	return $values;
  	}
	
	public function fetch_array($sql){
		$result = array();
		$query = mysql_query ($sql, $this->conn_task);
		if(mysql_num_rows($query)>0)
		{
			while($row = mysql_fetch_assoc($query))
			{
				$result[] = $row;
			}
		}
		return $result;
	}
	
	/* Add for return the delete query */
	function deleteRow($tableName,$where = array())
	{	
		$query =  "";
		if(sizeof($where) != 0)
		{
			$query =  "DELEET FROM ".$tableName;
			$i = 0;
			foreach($where as $field=>$value)
			{
				if($i>0)
				{
					$query.= " AND ".$field."='".$value."' ";
				}
				else
				{	
					$query.= " WHERE ".$field."='".$value."' ";
				}
			}
		}
		mysql_query ($query, $this->conn_task);
		return true;		
	}
	function deleteRows($tableName,$where = '')
	{			
		$query =  "DELETE FROM ".$tableName." ".$where;

		mysql_query ($query, $this->conn_task);
		return true;		
	}
	function insertData($tableName,$dataArray)
	{
		$query = "INSERT INTO ".$tableName."  ";
		$i = 0;
		foreach($dataArray as $field => $value)
		{
			if($i == 0)
			{
				$query.= " SET ".$field ."='".$value."' ";
			}
			else
			{
				$query.= ",".$field ."='".$value."' ";
			}				
			$i++;
		}
		mysql_query ($query, $this->conn_task);
		return true;	
	}
	function updateData($tableName,$dataArray,$where)
	{
		$query = "UPDATE ".$tableName."  ";
		$i = 0;
		foreach($dataArray as $field => $value)
		{
			if($i == 0)
			{
				$query.= " SET ".$field ."='".$value."' ";
			}
			else
			{
				$query.= ",".$field ."='".$value."' ";
			}				
			$i++;
		}
		$i = 0;
		foreach($where as $field => $value)
		{
			if($i == 0)
			{
				$query.= " WHERE ".$field ."='".$value."' ";
			}
			else
			{
				$query.= " AND ".$field ."='".$value."' ";
			}				
			$i++;
		}
		mysql_query ($query, $this->conn_task);
		return true;	
	}
	function dbClose()
	{
		mysql_close($this->conn_task);
	}

	/*
	# Lock Table
	function lock($tbleName, $mode="WRITE")
	{
		$query="LOCK TABLES ".$tbleName." ".$mode ;
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
	}

	# Unlock Table
	function unlock()
	{
		$query="UNLOCK TABLES" ;
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
	}

	function begin()
	{
		$query="START TRANSACTION";
		$stmt = mysql_db_query ($this->db, $query, $this->conn_task);
	}
	*/

}

?>