<?php
class ProductBatchReport
{  
	/****************************************************************
	This class deals with all the operations relating to Product Batch Report
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductBatchReport(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Filter records of Product
	function getProductBatchRecords($selProductId)
	{
		$qry = "select id, batch_no, product_id from t_productbatch where product_id='$selProductId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Fetch All Records based on Product batch ID
	function fetchAllIngredients($productBatchId)
	{
		$qry = "select a.id, a.productbatch_id, a.ingredient_id, a.quantity, b.last_price, c.name, c.category_id, (a.quantity*b.last_price) as ratePerBatch, a.fixed_qty, d.name from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c, ing_category d where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and c.category_id=d.id and a.productbatch_id='$productBatchId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find the Product batch ain Rec Summary from t_productbatch
	function getProductBatchSummaryRec($productBatchId)
	{
		$qry = "select product_qty, fixed_qty, num_pouch, start_time, end_time, ph_factor, fo_factor, created from t_productbatch where id=$productBatchId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2], $rec[3], $rec[4], $rec[5], $rec[6], $rec[7]):"";
	}

}