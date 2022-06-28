<?php

require_once 'MDB2.php';
require_once 'PEAR.php';
define("WHERE","where");
define("ORDER","order");
define("GROUP","group");

abstract class AFModel
{
	// Primary key mappings for each table
	protected $pk;
	protected $tableName;
	//private $dsn = 'mysql://root:ais2012@localhost/foresee_server'; //forstar_im/ forstar_server
	//private $dsn = 'mysql://root:ais2012@localhost/forstar_im'; //forstar_im/ forstar_server
	//private $dsn = 'mysql://root:ais2012@localhost/forstar_staging'; //forstar_im/ forstar_server
	//private $dsn='mysql://root:@localhost/foresee';//mysql://ais2010_Foresee:fsee2013@localhost/ais2010_Foresee
	private $dsn='mysql://root:password@localhost/forsee_new';//mysql://ais2010_Foresee:fsee2013@localhost/ais2010_Foresee
	//private $dsn='mysql://root:ais2010_Foresee@localhost/foresee';
	
	private $options = array(
    		'debug' => 2,
    		'result_buffering' => false,
	);
	private $updateFlag = "__updated";
	protected $fieldType = array();
	

	function __construct()
	{	
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);			
		PEAR::setErrorHandling(PEAR_ERROR_DIE); 	
		$mdb2 =& MDB2::singleton($this->dsn);
		if (PEAR::isError($mdb2)) {
		    die($mdb2->getMessage());
		}
	}

	public function getCount($query)
	{
		$mdb2 =& MDB2::singleton();
		//echo "<br>$query<br>";
		$res =& $mdb2->query($query);
		return $res->numRows();
	}

	public function exec($sql)
	{
		$mdb2 =& MDB2::singleton();		
		$mdb2->exec($sql);		
	}

	public function queryAll($query,$offset=0,$limit=0)
	{
		$mdb2 =& MDB2::singleton();		
		if ( $limit > 0 )	$mdb2->setLimit($limit,$offset);
		//echo "<br>$query<br>";
		$res =& $mdb2->query($query);
		// Always check that result is not an error
		if (PEAR::isError($res)) {
		    die($res->getMessage());
		}
		return $res->fetchAll(MDB2_FETCHMODE_OBJECT);
	}

	public function query($query,$offset=0,$limit=0)
	{
		$mdb2 =& MDB2::singleton();		
		if ( $limit > 0 )	$mdb2->setLimit($limit,$offset);
		//echo $query;
		$res =& $mdb2->query($query);
		// Always check that result is not an error
		if (PEAR::isError($res)) {
		    die($res->getMessage());
		}
		return $res->fetchRow(MDB2_FETCHMODE_OBJECT);
	}

	public function findAllForSelect($keyCol, $valCol, $defaultStr=null, $condition="", $offset=0,$limit=0)
	{
		$result = $this->findAll($condition,$offset,$limit,MDB2_FETCHMODE_ASSOC);
		$assoc = array();
		if ($defaultStr!=null) $assoc[""] = $defaultStr;
		foreach ($result as $rec) {
			$assoc[ $rec[$keyCol] ] = $rec[$valCol]; 
		}
		return $assoc;
	}	
	
	public function findAll($condition="",$offset=0,$limit=0, $fetchType=MDB2_FETCHMODE_OBJECT)
	{
		$mdb2 =& MDB2::singleton();		
		if ( $limit > 0 )	$mdb2->setLimit($limit,$offset);
		$qry = 'SELECT * FROM ' . $this->tableName;
		if ( is_array($condition) )	{
			if ( isset($condition[WHERE]) ) $qry .=  ' where ' . $condition[WHERE];		
			if ( isset($condition[GROUP]) ) $qry .=  ' group by ' . $condition[GROUP];
			if ( isset($condition[ORDER]) ) $qry .=  ' order by ' . $condition[ORDER];
		}
		else {
			if ( $condition != "" ) $qry .=  ' where ' . $condition;
		}
		//echo "<br>Find All<br>".$qry;
		$res =& $mdb2->query($qry);
		// Always check that result is not an error
		if (PEAR::isError($res)) {
		    die($res->getMessage());
		}
		return $res->fetchAll($fetchType);
	}

	public function find($condition, $fetchType=MDB2_FETCHMODE_OBJECT)
	{
		$mdb2 =& MDB2::singleton();		
		if ( $limit > 0 )	$mdb2->setLimit($limit,$offset);
		$qry = 'SELECT * FROM ' . $this->tableName . ' where ' . $condition;
		//echo $qry;
		$res =& $mdb2->query($qry);
		// Always check that result is not an error
		if (PEAR::isError($res)) {
		    die($res->getMessage());
		}
		return $res->fetchRow($fetchType);
	}

	public function save($arr)
	{
		foreach ($arr as $model => $value)
		{
			$mVals = $value;
			$mode = "I";			
			$pkey = $this->pk; //_map[$model];
			if ( isset($mVals[ $pkey ]) )	$mode = "U";
			$flds = "";
			$vals = "";
			$skipRecord=false;
			foreach ($value as $fkey => $fvalue)
			{
				if ( $fkey == $pkey ) continue;
				if ( $fkey == $this->updateFlag )	{
					//echo "$fkey => $fvalue";
					if ( $fvalue == "0" ) {
						$skipRecord=true;
						break;	// skip records not modified
					}
					continue;
				}
				if ( $mode == "I" )	{
					if ( $flds != "" ) $flds .= ",";
					$flds .= $fkey;
					if ( $vals != "" ) $vals .= ",";
					if ( $this->fieldType[$fkey] == "N" )	$vals  .= $fvalue;	
					else $vals  .= "'" . $fvalue . "'";
				}
				else	{
					if ( $vals != "" ) $vals .= ",";
					if ( $this->fieldType[$fkey] == "N" )	$vals  .= $fkey . "=" . $fvalue;
					else $vals  .= $fkey . "='" . $fvalue . "'";
					
				}
			}			
			
			if ( $skipRecord ) continue;

			if ( $mode == "I" )	{
				$qry = "INSERT INTO ".$this->tableName." ($flds) values($vals)";
			}
			else	{
				$pkval = $mVals[ $pkey ];
				$qry = "UPDATE ".$this->tableName." set $vals WHERE $pkey = '$pkval'";
			}
			
			//echo "<br>$qry<br>";

			// Fire query to database 
			$mdb2 =& MDB2::singleton();
			//$mdb2 =& MDB2::factory($this->dsn, $this->options);			
			$result = $mdb2->exec($qry);
			// Always check that result is not an error
			if (PEAR::isError($result)) return false; 
			else return true; 
		}
		return false;
	}

	public function saveMultiple($arr)
	{
		foreach ($arr as $key => $mvals)
		{
			$this->save($mvals);
		}
		return false;
	}

	public function deleteMultiple($arr)
	{
		foreach ($arr as $key => $mvals)
		{
			if ( $mvals[$this->name]["__del"] != "" )	{
				$condition = $this->pk."=".$mvals[$this->name]["__del"];
				if ( !$this->deleteSingle($condition) ) return false;
			}
		}
		return true;
	}	

	public function deleteSingle($condition)
	{
		$mdb2 =& MDB2::singleton();
		$qry = 'DELETE FROM ' . $this->tableName . ' where ' . $condition;		
		//$mdb2 =& MDB2::factory($this->dsn, $this->options);
		$result = $mdb2->exec($qry);
		// Always check that result is not an error
		if (PEAR::isError($result)) return false; 
		else return true;
	}

	public function getLastInsertedId()
	{
		$mdb2 =& MDB2::singleton();
		return $mdb2->lastInsertId($this->tableName, $this->pk);
	}

	public function updateRecord($sql)
	{
		$mdb2 =& MDB2::singleton();		
		$result = $mdb2->exec($sql);
		if (PEAR::isError($result)) return false; 
		else return true;
	}

}

?>