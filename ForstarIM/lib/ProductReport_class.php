<?php
class ProductReport
{  
	/****************************************************************
	This class deals with all the operations relating to Product Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductReport(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Filter all Ingredients for the selected date
	function fetchProductRecords($selectDate)
	{
		$qry = " select a.id, a.code, a.name, a.net_wt, b.id, b.product_id, b.product_qty, b.fixed_qty, b.num_pouch from m_productmaster a left join t_productbatch b on a.id=b.product_id and b.created='$selectDate'";
			
		//echo $qry;

		/*
		$qry = "select ingId, ingName, ingQty, sum(grnSum) as gSum, sum(siSum) as sSum from ( select d.id as ingId, d.name as ingName, a.quantity as ingQty, sum(b.quantity) as grnSum,0 as siSum from m_ingredient_rate a left join ing_receipt_entry b on a.ingredient_id=b.ingredient_id left join ing_receipt c on (c.id=b.ing_receipt_id) and c.created='$selectDate', m_ingredient d where d.id=a.ingredient_id group by d.id
		union
		select d1.id as ingId, d1.name as ingName, a1.quantity as ingQty, 0 as grnSum, sum(b1.quantity) as siSum from m_ingredient_rate a1 left join t_productbatch_entry b1 on a1.ingredient_id=b1.ingredient_id left join t_productbatch c1 on c1.id=b1.productbatch_id and c1.created='$selectDate', m_ingredient d1 where d1.id=a1.ingredient_id group by d1.id
		) as X group by ingId order by ingName ";
		*/
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Find the opening Qty (using in another Screens)
	function  getOpeningQty($productId, $lastDate)
	{
		$qry = "select sum(a.num_pouch) from t_productbatch a where a.product_id='$productId' group by product_id";
		//echo $qry."<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}


	# Product Id = RM CODE ID in t_product_matrix
	function getDespatchQty($productId, $lastDate)
	{
		$qry = " select sum(b.quantity) from t_salesorder a, t_salesorder_entry b, t_product_matrix c, m_productmaster d where a.id=b.salesorder_id and b.product_id=c.id and d.id=c.rm_code_id and d.id='$productId' group by d.id";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}


	/*
	#Find the opening Qty (using in another Screens)
	function  getOpeningQty($ingredientId, $lastDate)
	{
		$qry1 = "select a.quantity from m_ingredient_rate a where a.ingredient_id='$ingredientId' ";

		$qry2 = "select a.current_stock from ing_receipt_entry a, ing_receipt b where a.ing_receipt_id=b.id and a.ingredient_id='$ingredientId' and b.created<='$lastDate' order by b.id desc ";

		$qry3 = "select a.current_stock from t_productbatch_entry a, t_productbatch b where a.productbatch_id=b.id and a.ingredient_id='$stockId' and b.created<='$lastDate' order by b.id desc ";;
		//echo $qry;

		$issuanceRec = $this->databaseConnect->getRecord($qry3);
		if (sizeof($issuanceRec)>0) {
			//echo "Here1";
			return $issuanceRec[0];
		} else {
			$grnRec = $this->databaseConnect->getRecord($qry2);
			if (sizeof($grnRec)>0) {
				//echo "Here2";
				return $grnRec[0];
			} else {
				//echo "Here3";
				$stockRec = $this->databaseConnect->getRecord($qry1);
				return $stockRec[0];
			}
		}
	}
	*/

}
?>