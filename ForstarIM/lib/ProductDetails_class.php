<?php
class ProductDetails
{  
	/****************************************************************
	This class deals with all the operations relating to Product Batch
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ProductDetails(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Insert Record
	function addProductBatch($batchNo, $selProduct, $productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch, $userId)
	{
		$qry	=	"insert into t_productbatch (batch_no, product_id, product_qty, fish_qty, num_pouch, created, createdby) values ('$batchNo', '$selProduct', '$productGmsPerPouch', '$fishGmsPerPouch', '$pouchPerBatch',  Now(), '$userId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#For adding Ingredient  Items
	function addIngredientRec($lastId, $ingredientId, $quantity)
	{
		$qry	=	"insert into t_productbatch_entry (productbatch_id, ingredient_id, quantity) values('$lastId', '$ingredientId', '$quantity')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select a.id, a.batch_no, a.product_id, a.created, a.createdby, b.name from t_productbatch a, m_productmaster b where a.product_id=b.id order by a.created desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Supplier stock based on Supplier id
	function find($productBatchId)
	{
		$qry = "select a.id, a.batch_no, a.product_id, a.created, a.createdby, product_qty, fish_qty, num_pouch from t_productbatch a where a.id=$productBatchId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	#Delete  Ingredient  Recs
	function deleteIngredientRecs($productBatchId)
	{
		$qry	=	" delete from t_productbatch_entry where productbatch_id=$productBatchId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


	# Delete a Rec
	function deleteProductBatch($productBatchId)
	{
		$qry	=	" delete from t_productbatch where id=$productBatchId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update  a  Record
	function updateProductBatch($productBatchId, $batchNo, $selProduct, $productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch)
	{
		$qry = "update t_productbatch set batch_no='$batchNo', product_id='$selProduct', product_qty='$productGmsPerPouch', fish_qty='$fishGmsPerPouch', num_pouch='$pouchPerBatch' where id='$productBatchId'";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	/////////////////////////////////////////////////////////////////////////////////

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
		$qry = "select product_qty, fixed_qty, num_pouch from t_productbatch where id=$productBatchId";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):"";
	}

	#Get Fish Rate Per batch
	function getFishRatePerBatch($productBatchId)
	{
		$qry = "select sum(a.quantity*b.last_price) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  a.fixed_qty='Y' group by a.productbatch_id";
		//$qry = "select sum(a.quantity*b.last_price) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  c.category='S' group by a.productbatch_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	#Get Gravy Rate Per batch
	function getGravyRatePerBatch($productBatchId)
	{
		$qry = "select sum(a.quantity*b.last_price) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  a.fixed_qty='N' group by a.productbatch_id";
		//$qry = "select sum(a.quantity*b.last_price) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  c.category='V' group by a.productbatch_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Get Fish Kg Per batch
	function getfishKgPerbatch($productBatchId)
	{
		$qry = "select sum(a.quantity) from t_productbatch_entry a where a.productbatch_id='$productBatchId' and  a.fixed_qty='Y' group by a.productbatch_id";
		//$qry = "select sum(a.quantity) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  c.category='S' group by a.productbatch_id"; edited13-03
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	#Get Gravy Kg Per batch
	function getGravyKgPerbatch($productBatchId)
	{
		$qry = "select sum(a.quantity) from t_productbatch_entry a where a.productbatch_id='$productBatchId' and  a.fixed_qty='N' group by a.productbatch_id";
		//$qry = "select sum(a.quantity) from t_productbatch_entry a, m_ingredient_rate b, m_ingredient c where  a.ingredient_id=b.ingredient_id and a.ingredient_id=c.id and a.productbatch_id='$productBatchId' and  c.category='V' group by a.productbatch_id";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}	

}