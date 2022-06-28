<?php
class SupplierIngredient
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Ingredient
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SupplierIngredient(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Check Unique Records
	function chkUniqueRecords($supplierId, $selIngredient, $cId)
	{
		$updateQry = "";
		if ($cId!="") $updateQry = " and id!=$cId";
		$qry	= "select id from m_supplier_ing where supplier_id='$supplierId' and ingredient_id='$selIngredient' $updateQry";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Add 
	function addSupplierIngredient($selSupplierId, $selIngredients, $userId)
	{
		$this->deleteSupplierIngreient($selSupplierId);

		if (sizeof($selIngredients)>0) {
			foreach ($selIngredients as $ingId) {				
				$ingredientId	=	"$ingId";
				# Check for unique records
				$uniqueRec = $this->chkUniqueRecords($selSupplierId, $ingredientId, $cId);
				if (!$uniqueRec) {
					$qry	= "insert into m_supplier_ing (supplier_id, ingredient_id, created, createdby) values('".$selSupplierId."', '".$ingredientId."', Now(),'$userId')";
					//echo $qry;
					$insertStatus	= $this->databaseConnect->insertRecord($qry);
					if ($insertStatus) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();
				}
				//return $insertStatus;
			}
		}
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
		$whr = " b.id=a.supplier_id and c.id=a.ingredient_id ";
			
		if ($supplierFilterId!="") $whr .= " and a.supplier_id=".$supplierFilterId;		

		$groupBy	= " a.supplier_id ";
		$orderBy 	= " b.name asc, c.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select  a.id, a.supplier_id, a.ingredient_id, b.name, c.name,a.active from m_supplier_ing a, supplier b, m_ingredient c ";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;		
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Supplier Stock
	function getAllRecords($supplierFilterId)
	{
		$whr = " b.id=a.supplier_id and c.id=a.ingredient_id ";
			
		if ($supplierFilterId!="") $whr .= " and a.supplier_id=".$supplierFilterId;	
	
		$groupBy	= " a.supplier_id ";
		$orderBy 	= " b.name asc, c.name asc";

		$qry = "select  a.id, a.supplier_id, a.ingredient_id, b.name, c.name from m_supplier_ing a, supplier b, m_ingredient c ";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($groupBy!="") 	$qry .= " group by ".$groupBy;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all (using in other section)
	function fetchAllRecords($supplierFilterId)
	{
		$whr = " b.id=a.supplier_id and c.id=a.ingredient_id ";
			
		if ($supplierFilterId!="") $whr .= " and a.supplier_id=".$supplierFilterId;	
	
		$orderBy 	= " b.name asc, c.name asc";

		$qry = "select  a.id, a.supplier_id, a.ingredient_id, b.name, c.name,a.active from m_supplier_ing a, supplier b, m_ingredient c ";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		//echo $qry;
		
		$result	=	$this->databaseConnect->getRecords($qry);
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
	function find($supplierIngId)
	{
		$qry	= "select id, supplier_id, ingredient_id from m_supplier_ing where id=$supplierIngId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);		
	}

	# Update
	function updateSupplierIngredient($supplierIngredientId, $selSupplierId, $selIngredient)
	{
		$qry	= "update m_supplier_ing set supplier_id='$selSupplierId', ingredient_id='$selIngredient' where id='$supplierIngredientId'";
		//echo $qry;
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
	function deleteSupplierIngredient($supplierIngredientId)
	{
		$qry	= " delete from m_supplier_ing where id=$supplierIngredientId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Returns all Ingredients Records
	function fetchAllSelectedIngRecords($supplierId)
	{		
		$qry = "select a.id, a.code, a.name, c.ingredient_id, d.id, d.name, b.id, b.name from m_ingredient a join ing_category b on b.id=a.category_id left join ing_main_category d on a.main_category_id=d.id left join m_supplier_ing c on a.id=c.ingredient_id and c.supplier_id='$supplierId'  order by d.name asc, b.name asc, a.name asc";
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
	
}
?>