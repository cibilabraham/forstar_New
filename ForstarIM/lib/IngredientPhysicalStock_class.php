<?php
class IngredientPhysicalStock
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Ingredient
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function IngredientPhysicalStock(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchAllIngredientRecords()
	{
		$qry="select id,name from  m_ingredient where active='1'  order by name asc " ;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSupplierIng($ingId)
	{
			$qry="select * from (select a.supplier_id as supplier_id,s.name,a.id,a.ingredient_id as ingredient_id from  m_supplier_ing a left join supplier s on a.supplier_id=s.id  where a.ingredient_id='$ingId' order by id desc) dum group by supplier_id,ingredient_id";
			//$qry="select a.supplier_id,s.name,a.id from  m_supplier_ing a left join supplier s on a.supplier_id=s.id where a.ingredient_id='$ingId'  order by id desc" ;
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;

	}

	function addPhysicalIngredient($effectiveDate,$userId)
	{
		$qry	= "insert into m_physical_ingredient (date, created_on,created_by) values('".$effectiveDate."', Now(),'".$userId."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//return $insertStatus;
		return true;
	}

	function addPhysicalIngredientQuantity($lastId,$selSupplier,$selIngredient,$quantity,$effectiveDate,$expectedQuantity,$differenceInQuantity)
	{
		$qry	= "insert into m_physical_ingredient_quantity (physical_ing_id, supplier,ingredient,quantity,entry_date,expected_quantity,difference) values('".$lastId."','".$selSupplier."','".$selIngredient."','".$quantity."','".$effectiveDate."','".$expectedQuantity."','".$differenceInQuantity."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//return $insertStatus;
		return true;
	}


	function getSupplierQty($supplierIngId,$supplierId,$ingId)
	{
		$qry="select sum(a.quantity) as Qty from  m_supplier_ing_qty a  left join m_supplier_ing b on a.supplier_ing_id=b.id   where  b.supplier_id='$supplierId' and  b.ingredient_id='$ingId' " ;
		//$qry="select sum(a.quantity) as Qty from  m_supplier_ing_qty a  left join m_supplier_ing b on a.supplier_ing_id=b.id   where a.supplier_ing_id='$supplierIngId'  and b.supplier_id='$supplierId' and  b.ingredient_id='$ingId' " ;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"0";
	}

	function addSupplierIng($supplierIngId,$diff,$effectiveDate,$physicalId)
	{
		$qry	= "insert into m_supplier_ing_qty (supplier_ing_id, quantity,ingredient_date,physical_ing_id,active) values('".$supplierIngId."','".$diff."','".$effectiveDate."','".$physicalId."','0')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//return $insertStatus;
		return true;
	}

	function getSupplierIngId($ingId,$supplierId)
	{
			$qry="select * from (select a.id,a.supplier_id as supplier_id,a.ingredient_id as ingredient_id  from  m_supplier_ing a left join supplier s on a.supplier_id=s.id  where  a.supplier_id='$supplierId' and a.ingredient_id='$ingId' order by id desc) dum group by supplier_id,ingredient_id";
			//$qry="select a.id from  m_supplier_ing a left join supplier s on a.supplier_id=s.id where a.supplier_id='$supplierId' and a.ingredient_id='$ingId'" ;
			//echo $qry;
			$result	=	$this->databaseConnect->getRecord($qry);
			return (sizeof($result)>0)?$result[0]:"0";
	}

	# Returns all Ingredients Records
	function fetchSelectedSupplierIngRecords($supplierId)
	{		
		$qry = "select a.id, a.code, a.name, c.ingredient_id, d.id, d.name, b.id, b.name from m_ingredient a join ing_category b on b.id=a.category_id left join ing_main_category d on a.main_category_id=d.id left join m_supplier_ing c on a.id=c.ingredient_id where c.supplier_id='$supplierId'  and c.ingredient_id is not null   group by ingredient_id order by d.name asc, b.name asc, a.name asc";

		//$qry = "select a.id, a.code, a.name, c.ingredient_id, d.id, d.name, b.id, b.name from m_ingredient a join ing_category b on b.id=a.category_id left join ing_main_category d on a.main_category_id=d.id left join m_supplier_ing c on a.id=c.ingredient_id and c.supplier_id='$supplierId'    order by d.name asc, b.name asc, a.name asc";
		//left join ing_main_category d on a.main_category_id=d.id
		//echo $qry;
		//return new ResultSetIterator($this->databaseConnect->getResultSet($qry));

		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr	= array();
		$i=0;
		$prevCategoryId 	= "";
		$preSubCategoryId 	= "";		
		foreach ($result as $r) {
			$ingredientId 	= $r[0];
			$ingredientCode = $r[1];
			$ingName	= $r[2];
			$selIngId	= $r[3];			
			$categoryId 	= $r[4]; 
			$categoryName	= $r[5];
			$subCategoryId 	= $r[6];
			$subCategoryName = $r[7];			
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('',"----- $categoryName -------");	
				$i++;
			}
			/*
			if ($preSubCategoryId!=$subCategoryId) {
				$resultArr [$i]      = "-- $subCategoryName --";	
				$i++;
			}
			*/			
			$resultArr[$i] = array($ingredientId,$ingName);
			$prevCategoryId 	= $categoryId;
			$preSubCategoryId 	= $subCategoryId;
			$i++;
		}
		//printr($resultArr);
		return $resultArr;
	}
	
	function checkPhysicalStockExist($date,$userId)
	{
		$qry="select id from m_physical_ingredient where date='$date' and created_by='$userId'" ;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function updatePhysicalIngredientQuantity($phyStckEntryId,$supplierId,$ingId,$quantity,$bulkDate,$expectedQuantity,$differenceInQuantity)
	{
		$qry	= "update m_physical_ingredient_quantity set supplier='$supplierId', ingredient ='$ingId', expected_quantity='$expectedQuantity', quantity='$quantity',difference='$differenceInQuantity',entry_date='$bulkDate' where id='$phyStckEntryId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatePhysicalIngredientReleaseconfirm($physicalId)
	{
		$qry	= "update m_physical_ingredient set active='0' where id=$physicalId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updatePhysicalIngredientConfirm($physicalId)
	{
		$qry	= "update m_physical_ingredient set active='1' where id=$physicalId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateSupplierIngredientsConfirm($physicalId)
	{
		$qry	= "update m_supplier_ing_qty set active='1' where physical_ing_id=$physicalId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function updateSupplierIngredientsReleaseConfirm($physicalId)
	{
		$qry	= "update m_supplier_ing_qty set active='0' where physical_ing_id=$physicalId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}















	# Check Unique Records
	function chkUniqueRecords($effectiveDate,$selSupplierId, $selIngredient,$cId)
	{
		$updateQry = "";
		if ($cId!="") $updateQry = " and id!=$cId";
		$qry	= "select id from m_supplier_ing where supplier_id='$selSupplierId' and ingredient_id='$selIngredient' and ( '$effectiveDate' between start_date and end_date or start_date='$effectiveDate' and end_date='0000-00-00')  $updateQry";
		//echo $qry;
		//die();
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function chkGreaterStartDate($effectiveDate,$selSupplierId, $selIngredient,$cId)
	{
		$updateQry = ""; 
		if ($cId!="") $updateQry = " and id!=$cId";
		$qry	= "select id,start_date from m_supplier_ing where supplier_id='$selSupplierId' and ingredient_id='$selIngredient' and start_date>'$effectiveDate' and end_date='0000-00-00' $updateQry order by id desc limit 1";
		//echo $qry;
		//die();
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
		//return (sizeof($result)>0)?true:false;
	}

	function updateSupplierIng($supplierIngId,$prevDate)
	{
		$qry	= "update m_supplier_ing set end_date='$prevDate' where id='$supplierIngId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function chkLessStartDate($effectiveDate,$selSupplierId, $selIngredient,$cId)
	{
		$updateQry = ""; 
		if ($cId!="") $updateQry = " and id!=$cId";
		$qry	= "select id,start_date from m_supplier_ing where supplier_id='$selSupplierId' and ingredient_id='$selIngredient' and start_date<'$effectiveDate' and end_date='0000-00-00' $updateQry order by id desc limit 1";
		//echo $qry;
		//die();
		$result = $this->databaseConnect->getRecord($qry);
		return $result;
		//return (sizeof($result)>0)?true:false;
	}

	#Add 
	function addSupplierIngredient($selSupplierId, $selIngredient,$rate,$effectiveDate,$userId,$endDate)
	{
	//	$this->deleteSupplierIngreient($selSupplierId);
		if($endDate!="")
		{
			$qry	= "insert into m_supplier_ing (supplier_id, ingredient_id,rate,start_date,created, createdby,end_date) values('".$selSupplierId."', '".$selIngredient."','".$rate."', '".$effectiveDate."',Now(),'$userId','$endDate')";
		}
		else
		{
			$qry	= "insert into m_supplier_ing (supplier_id, ingredient_id,rate,start_date,created, createdby) values('".$selSupplierId."', '".$selIngredient."','".$rate."', '".$effectiveDate."',Now(),'$userId')";
		}
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//return $insertStatus;
		return true;
	}	

	function addSupplierIngredientQty($lastId, $quantity)
	{
		$qry	= "insert into m_supplier_ing_qty (supplier_ing_id, quantity) values('".$lastId."', '".$quantity."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		//return $insertStatus;
		return true;
	}

	function deleteSupplierIngreient($selSupplierId)
	{
		$getSupplierWiseIngredients = $this->getIngreients($selSupplierId);
		foreach ($getSupplierWiseIngredients as $gr) {
			$supplierIngredientId = $gr[0];
			$ingId  = $gr[1];
			$supplierIngExist  = $this->chkSupplierIngExist($selSupplierId, $ingId);
			if (!$supplierIngExist) {				
				$supplierIngredientRecDel =	$this->deleteSupplierIngredient($supplierIngredientId);	
			}			
		}
		return true;		
	}

	# Returns all Supplier Paging Stock
	function fetchAllPagingRecords($offset, $limit, $supplierFilterId)
	{
		$limit 		= " $offset,$limit";
		$qry="select * from m_physical_ingredient group by date,created_by order by id desc  ";
		//echo $qry;
		if ($limit!="") 	$qry .= " limit ".$limit;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Supplier Stock
	function getAllRecords()
	{
		$qry="select * from m_physical_ingredient group by date,created_by order by id desc  ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all (using in other section)
	function fetchAllRecords($supplierFilterId)
	{
		$whr = " b.id=a.supplier_id and c.id=a.ingredient_id ";
			
		if ($supplierFilterId!="") $whr .= " and a.supplier_id=".$supplierFilterId;	
	
		$groupBy	= " supplier_id,ingredient_id ";
		$orderBy 	= " b.name asc, c.name asc,a.start_date desc";

		$qry = "select   a.id as id , a.supplier_id as supplier_id, a.ingredient_id as ingredient_id, b.name as name, c.name as cname,a.active as  active,a.start_date as start_date  from m_supplier_ing a, supplier b, m_ingredient c ";

		if ($whr!="") 		$qry .= " where ".$whr;
		//if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		if($supplierFilterId!="")
		{
			$query= $qry;
		}
		else
		{
			$query="select * from (". $qry.") dum ";
			if ($groupBy!="") 	$query .= " group by ".$groupBy;
		}
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($query);
		return $result;
	}
	# Get Supplier Ingreients
	function getIngreients($supplierId)
	{
		$qry = " select  a.id, a.ingredient_id, b.name from m_supplier_ing a, m_ingredient b where b.id=a.ingredient_id and a.supplier_id='$supplierId' order by b.name asc ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Get Supplier stock based on Supplier id 
	function find($physicalIngId)
	{
		$qry	= "select id,date,created_by,active from m_physical_ingredient where id=$physicalIngId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);		
	}

	# Get Supplier stock based on Supplier id 
	function findPhysicalId($startDate,$userId)
	{
		$resultArr="";
		$qry	= "select * from (select id from m_physical_ingredient where date='$startDate' and created_by='$userId' order by id desc) dum group by id desc";
		//echo $qry;
		$res=$this->databaseConnect->getRecords($qry);	
		foreach($res as $rt)
		{
			$resultArr[]=$rt[0];
		}
		//printr($resultArr);
		$result= join(",",$resultArr);
		return $result;
	}

	function findPhysicalStock($physicalStockId)
	{
		$qry	= "select * from (select id,supplier,ingredient,expected_quantity,quantity,difference from m_physical_ingredient_quantity where physical_ing_id in ($physicalStockId) group by  physical_ing_id,supplier,ingredient) dum order by id,ingredient,supplier desc";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);		
	}

	function findQty($supplierIngId)
	{
		$qry	= "select quantity from m_supplier_ing_qty where supplier_ing_id=$supplierIngId and ing_recipe_id='0'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);		
	}

	# Update
	function updateSupplierIngredient($selSupplierId, $selIngredient,$rate,$supplierIngredientId	)
	{
		$qry	= "update m_supplier_ing set supplier_id='$selSupplierId', ingredient_id='$selIngredient',rate='$rate' where id='$supplierIngredientId'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateSupplierIngredientQty($supplierIngredientId, $quantity)
	{
		$qry	= "update m_supplier_ing_qty set quantity='$quantity' where supplier_ing_id='$supplierIngredientId'  and ing_recipe_id='0' and ing_recipe_entryid='0'";
		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Checking supplier stock used in po
	function chkSupplierIngExist($supplierId, $ingredientId)
	{
		$qry = " select a.id from ing_purchaseorder a, ing_purchaseorder_entry b where a.id=b.po_id and a.supplier_id='$supplierId' and ingredient_id='$ingredientId' ";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	


	# Delete a Supplier Stock
	function deletePhysicalIngredient($physicalIngredientId)
	{
		$qry	= " delete from m_physical_ingredient where id='$physicalIngredientId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Physical Ingredient Entry
	function deletePhysicalIngredientEntry($physicalIngredientId)
	{
		$qry	= " delete from m_physical_ingredient_quantity where physical_ing_id='$physicalIngredientId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Supplier Stock
	function deleteSupplierIngredientQty($physicalIngredientId)
	{
		$qry	= " delete from m_supplier_ing_qty where physical_ing_id='$physicalIngredientId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	


function updateSupplierIngredientconfirm($supplierIngredientId)
	{
	$qry	= "update m_supplier_ing set active='1' where id=$supplierIngredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateSupplierIngredientReleaseconfirm($supplierIngredientId)
	{
		$qry	= "update m_supplier_ing set active='0' where id=$supplierIngredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	function chckSupplierIdInPo($supplierIngId)
	{
		$qry = " select id from  ing_purchaseorder_entry  where supplier_ing_id='$supplierIngId' ";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;

	}

	

		# Returns all Ingredients Records
	function fetchAllSelectedIngRecords($supplierId)
	{		
		$qry = "select a.id, a.code, a.name, c.ingredient_id, d.id, d.name, b.id, b.name from m_ingredient a join ing_category b on b.id=a.category_id left join ing_main_category d on a.main_category_id=d.id left join m_supplier_ing c on a.id=c.ingredient_id where c.supplier_id='$supplierId'  and c.ingredient_id is not null   group by ingredient_id order by d.name asc, b.name asc, a.name asc";

		//$qry = "select a.id, a.code, a.name, c.ingredient_id, d.id, d.name, b.id, b.name from m_ingredient a join ing_category b on b.id=a.category_id left join ing_main_category d on a.main_category_id=d.id left join m_supplier_ing c on a.id=c.ingredient_id and c.supplier_id='$supplierId'    order by d.name asc, b.name asc, a.name asc";
		//left join ing_main_category d on a.main_category_id=d.id
		//echo $qry;
		//return new ResultSetIterator($this->databaseConnect->getResultSet($qry));

		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr	= array();
		$i=0;
		$prevCategoryId 	= "";
		$preSubCategoryId 	= "";		
		foreach ($result as $r) {
			$ingredientId 	= $r[0];
			$ingredientCode = $r[1];
			$ingName	= $r[2];
			$selIngId	= $r[3];			
			$categoryId 	= $r[4]; 
			$categoryName	= $r[5];
			$subCategoryId 	= $r[6];
			$subCategoryName = $r[7];			
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('','',"----- $categoryName -------",'');	
				$i++;
			}
			/*
			if ($preSubCategoryId!=$subCategoryId) {
				$resultArr [$i]      = "-- $subCategoryName --";	
				$i++;
			}
			*/			
			$resultArr[$i] = array($ingredientId,$ingredientCode,$ingName, $selIngId);
			$prevCategoryId 	= $categoryId;
			$preSubCategoryId 	= $subCategoryId;
			$i++;
		}
		
		return $resultArr;
	}


	function getIngredientPhysicalStock($bulkDate,$supplierId,$ingId)
	{
		$bulkDt=mysqlDateFormat($bulkDate);
		$qry = " select quantity from  m_physical_ingredient_quantity  where supplier='$supplierId' and ingredient='$ingId' and entry_date='$bulkDt'";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}



	
}
?>