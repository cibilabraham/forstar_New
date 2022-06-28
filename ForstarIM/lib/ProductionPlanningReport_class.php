<?php
class ProductionPlanningReport
{
	/****************************************************************
	This class deals with all the operations relating to Production Planning Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductionPlanningReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	# get Stock Wise Issuance Records
	function getProductionPlannedRecords($fromDate, $tillDate, $reportType) 
	{
	
		$ingredientRecords	= $this->getProdPlannedIngRecords($fromDate, $tillDate, $reportType);
		$semiFinishedRecords	= $this->getProdPlannedSemiFinishedRecs($fromDate, $tillDate, $reportType);
		$resultArr = array();	
		while (list(,$v) = each($ingredientRecords)) {
			//echo "<br>ing=$v[1]=$v[2]=$v[3]";
			$resultArr[$v[1]] = array($v[2], $v[3]) ;
		}

		while (list(,$v) = each($semiFinishedRecords)) {
			//echo "<br>ing=$v[1]=$v[2]=$v[4]";
			$resultArr[$v[1]] = array($v[2], $v[4]);
		}
		//arsort($resultArr);
		//uasort($resultArr, 'compareData');
		return $resultArr;	
		
	}
	/*
	function compareData($a, $b) 
	{ 		
		return (strnatcmp($a[0],$b[0]));
	}
	*/

	function getProdPlannedIngRecords($fromDate, $tillDate, $reportType)
	{
		$whr = "d.id=c.category_id and b.ingredient_id=c.id and a.id=b.production_plan_id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate' and b.sel_ing_type='ING'";		
		if ($reportType=='S') 		$groupBy = " b.ingredient_id";	
		else if ($reportType=='D') 	$groupBy = " a.product_id";
		$orderBy	= " d.name asc, c.name asc";
		$qry = " select a.id, b.ingredient_id, sum(b.quantity), c.name from t_production_plan a, t_production_plan_entry b, m_ingredient c, ing_category d";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo "Ing=<br>".$qry."<br>";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# semi Finished Records
	function getProdPlannedSemiFinishedRecs($fromDate, $tillDate, $reportType)
	{
		$whr = "a.id=b.production_plan_id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate' and b.sel_ing_type='SFP'";		
		if ($reportType=='S') 		$groupBy = " b.ingredient_id";	
		else if ($reportType=='D') 	$groupBy = " a.product_id";
		//$orderBy	= " d.name asc, c.name asc";
		$qry = " select a.id, b.ingredient_id, sum(b.quantity) from t_production_plan a, t_production_plan_entry b";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		//$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);

		$resultArr = array ();
		if (sizeof($result)>0) {
			foreach ($result as $rec) {
				$productId = $rec[1];
				$resultArr = $this->productMasterRecs($productId);
			}			
		}			
		return $resultArr;
	}

	function productMasterRecs($productId)
	{
		//$qry = " select a.id, a.ingredient_id, a.quantity, a.sel_ing_type, b.name from m_productmaster_entry a, m_ingredient b where a.ingredient_id=b.id and a.product_id='$productId' order by b.name asc ";
		$qry = " select a.id, a.ingredient_id, sum(a.raw_qty), a.percent_per_btch, b.name from m_sf_product_entry a, m_ingredient b where a.ingredient_id=b.id and a.sf_product_id='$productId' group by a.ingredient_id order by b.name asc ";
		//echo "Semi=><br>".$qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		/*
		if (sizeof($result)>0) {
			$totalQty 	= 0;
			$currentStock 	= 0;
			foreach ($result as $r) {
				$ingredientId 	= $r[2];	
				$quantity 	= $r[3];				
			}
		}
		*/
		return $result;
	}

	/* Original 
	# get Stock Wise Issuance Records
	function getProductionPlannedRecords($fromDate, $tillDate, $reportType) 
	{
		$whr = "d.id=c.category_id and b.ingredient_id=c.id and a.id=b.production_plan_id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate'";
		
		if ($reportType=='S') 		$groupBy = " b.ingredient_id";	
		else if ($reportType=='D') 	$groupBy = " a.product_id";	

		$orderBy	= " d.name asc, c.name asc";

		$qry = " select a.id, b.ingredient_id, c.name, sum(b.quantity) from t_production_plan a, t_production_plan_entry b, m_ingredient c, ing_category d";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/

	# Get Latestest unit Price
	function getIngPrice($ingredientId)
	{
		$unitPrice = 0;

		$qry1 = "select d.rate from ing_receipt_entry a, ing_receipt b, ing_purchaseorder c, ing_purchaseorder_entry d  where a.ing_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.ingredient_id=d.ingredient_id and a.ingredient_id='$ingredientId' order by b.created desc limit 1 ";
			
		$qry2 = "SELECT min(rate_per_kg) FROM m_ingredient_rate where ingredient_id='$ingredientId'";

		$lsunitPriceRec = $this->databaseConnect->getRecords($qry1);
		if (sizeof($lsunitPriceRec)>0) {
			$unitPrice = $lsunitPriceRec[0][0]; // find the lastest price 
			//echo $qry1."<br>";
		} else  {
			$minUnitPriceRec = $this->databaseConnect->getRecords($qry2);
			if (sizeof($minUnitPriceRec)>0) $unitPrice = $minUnitPriceRec[0][0]; // get the lowst price of this stock
			//echo $qry2."<br>";
		}
		return $unitPrice;
	}
	

	/* Original Records for getting Planned Records
		$whr = "d.id=c.category_id and b.ingredient_id=c.id and a.id=b.production_plan_id and a.planned_date>='$fromDate' and a.planned_date<='$tillDate'";		
		if ($reportType=='S') 		$groupBy = " b.ingredient_id";	
		else if ($reportType=='D') 	$groupBy = " a.product_id";
		$orderBy	= " d.name asc, c.name asc";
		$qry = " select a.id, b.ingredient_id, c.name, sum(b.quantity) from t_production_plan a, t_production_plan_entry b, m_ingredient c, ing_category d";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;				
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	*/

}
?>