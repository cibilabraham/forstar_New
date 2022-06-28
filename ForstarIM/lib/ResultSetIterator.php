<?
class ResultSetIterator
{
	#--------------------------------------------------------------------------------|-
	# ResultSetIterator, which will read & return the rows inside a mysql result set.|-
	#--------------------------------------------------------------------------------|-
	var $resultSet = null;	// variable used to store result set returned by mysql_query().

	/**
	* Desc: Constructor
	**/
	function ResultSetIterator(&$resultSet)
	{
		$this->resultSet	=	$resultSet;	// set resultSet variable to mysql result set.
    	}
	
	/**
	* Desc: getRow, which will return my_sql_fetch_row of resultSet.
	* return value: return mysql row 
	**/
	function getRow()
	{
		if($this->resultSet !=FALSE) $row	=	mysql_fetch_row($this->resultSet); // get a result row as an enumerated array.
		if( $row !=FALSE ) return $row; // if result row not equals to FALSE then return result row.
		@mysql_free_result ( $this->resultSet ); // free the resources associated with the result set.
		return null; // return null if there are no more result rows. 
	}

	/**
	* Desc
	**/
	function getNumRows()
	{
		if($this->resultSet !=FALSE) return $numRows	=	mysql_num_rows($this->resultSet); // get result.
		return -1; // return null if there are no more result rows. 
	}
	
}
?>