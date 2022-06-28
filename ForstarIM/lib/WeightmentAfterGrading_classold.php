<?php
class WeightmentAfterGrading
{  
	
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WeightmentAfterGrading(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function getSupplierDetail($rmLotId)
	{
		 $qry	=	"select id, farmer_at_harvest,product_species,total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	# Filter grade List
	function getGrade($rmLotId)
	{
		$qry	=	"select id, grade_count from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//echo $qry;
		// $result	= $this->databaseConnect->getRecords($qry);
		// return $result;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# Filter get weight
	function getWeight($rmLotId)
	{
		$qry	=	"select id, total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
		// $result = array();
		// $result = $this->databaseConnect->getRecords($qry);
		// if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		// else if (sizeof($result)==1) $resultArr = array();
		// else $resultArr = array(''=>'-- Select --');

		// while (list(,$v) = each($result)) {
			// $resultArr[$v[0]] = $v[1];
		// }
		// return $resultArr;
	}
	
}
?>