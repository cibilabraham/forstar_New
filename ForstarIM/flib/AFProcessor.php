<?php
require_once 'MDB2.php';
require_once 'PEAR.php';

class AFProcessor
{
	// Primary key mappings for each table
	static $pk_map = array('user' => 'id');
	static $dsn = 'mysql://root:ais2008@192.168.0.100/frameworktest';
	static $options = array(
    		'debug' => 2,
    		'result_buffering' => false,
	);

	public static function preprocess($arr)
	{
		$res = array();
		$ind=0;

		if ( isset($arr['data']) )	{
			$data = $arr['data'];
			foreach ($data as $model => $keys)
			{
				$res[$model]=$keys;
			}
		}
		return $res;
	}

	public static function preprocessMultiple($arr)
	{
		$res = array();
		$ind=0;

		if ( isset($arr['data']) )	{
			$data = $arr['data'];
			foreach($data as $row => $mdata)
			{
				foreach ($mdata as $model => $keys)
				{
					$res[$ind++][$model]=$keys;
				}
			}
		}
		return $res;
	}

	private static function preprocess1($arr)
	{
		ksort($arr);
		$res = array();
		$ind=0;
		// sort the array based on key
		foreach ($arr as $key => $value)
		{
			$search = "data[";
			if ( strncmp($key,$search , strlen($search)) == 0 ) {
				$key = str_replace($search,"",$key);
				$key = str_replace("]","",$key);
				$tkns = explode("[",$key);
				$model = $tkns[0];
				$attribute = $tkns[1];
				$atArr = array();
				if ( !isset($res[$model]) )	$res[$model] = $atArr;
				else $atArr = $res[$model];
				$atArr[$attribute]=$value;
				$res[$model]=$atArr;
			}
		}		
		print_r($res);
		echo "<br>";
		return $res;
	}
}

?>
