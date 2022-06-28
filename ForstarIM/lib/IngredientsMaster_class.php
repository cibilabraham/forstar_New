<?php
class IngredientMaster
{
	/****************************************************************
	This class deals with all the operations relating to Ingredient Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function IngredientMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	#Check for an Ingredient Existence
	function checkIngredientExist($ingName)
	{
		$qry = "select * from m_ingredient where name='$ingName'";
		//echo $qry;
		$chkStatus = $this->databaseConnect->getRecords($qry);
		return $chkStatus;
	}

	# Add a Ingredient
	function addIngredient($selCategory, $code, $name, $surName, $descr, $userId, $materialType, $mainCategoryId, $numberGenId,$selraw_ing,$yeild,$clearing_cost,$Packing_size)
	{
		//$qry	=	"insert into m_ingredient (code, name, surname, category_id, description, created, createdby, actual_quantity, main_category_id,number_gen_Id, material_type) values('$code', '$name', '$surName', '$selCategory', '$descr', Now(), '$userId', '$openingQty', '$mainCategoryId','$numberGenId', '$materialType')";
		$qry	=	"insert into m_ingredient (code, name, surname, category_id, description, created, createdby, actual_quantity, main_category_id,number_gen_Id, material_type,Raw_ing,Yeild,clearing_cost,Packing_size) values('$code', '$name', '$surName', '$selCategory', '$descr', Now(), '$userId', '$openingQty', '$mainCategoryId','$numberGenId', '$materialType', '$selraw_ing', '$yeild', '$clearing_cost','$Packing_size')";


		//echo $qry;
		//exit;
		
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add a Ingredient
	function addIngredientCritical($lastId, $parameterId,$status)
	{
		$qry	=	"insert into ing_critical_master (ing_id, parameter_id, critical_status) values('$lastId', '$parameterId', '$status')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $categoryFilterId, $mainCategoryFilterId,$srchName)
	{
		$whr = " b.id=a.category_id";
		if ($categoryFilterId=="") $whr .= "";
		else $whr .= " and a.category_id=".$categoryFilterId;

		if ($mainCategoryFilterId=="") $whr .= "";
		else $whr .= " and b.main_category_id=".$mainCategoryFilterId;
		/* rekha added code */
		if ($srchName!=''){
			if($whr=="")
			$whr .= " a.surname like '%".$srchName."%' or a.surname like '".$srchName."%'" ." or a.surname like '%".$srchName."'" ;
			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			$whr .= " and (a.surname like '%".$srchName."%' or a.surname like '".$srchName."%'" ." or a.surname like '%".$srchName."')" ;
		}
		/* end code */
		
		$orderBy 	= " b.name asc, a.name asc";
		$limit 		= " $offset,$limit";

		$qry = "select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name, c.name,a.active, a.material_type  from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id ";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;
		//echo $qry;						
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Filter Records
	function ingredientRecFilter($categoryFilterId, $mainCategoryFilterId,$srchName)
	{
		$whr = " b.id=a.category_id";
		if ($categoryFilterId=="") $whr .= "";
		else $whr .= " and a.category_id=".$categoryFilterId;

		if ($mainCategoryFilterId=="") $whr .= "";
		else $whr .= " and b.main_category_id=".$mainCategoryFilterId;

		
		/* rekha added code */
		if ($srchName!=''){
			if($whr=="")
			$whr .= " a.surname like '%".$srchName."%' or a.surname like '".$srchName."%'" ." or a.surname like '%".$srchName."'" ;
			else	
			//$whr .= " and name like '% ".$srchName. "%'";
			$whr .= " (and a.surname like '%".$srchName."%' or a.surname like '".$srchName."%'" ." or a.surname like '%".$srchName."')" ;
		}
		/* end code */
		
		
		$orderBy 	= " b.name asc, a.name asc";
		
		$qry = "select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name, c.name,a.material_type from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id";

		if ($whr!="") $qry .= " where ".$whr;
		if ($orderBy!="") $qry .= " order by ".$orderBy;		
		//echo $qry;
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	/*rekha added dated on 10 aug 2018 */

	# Returns all Filter Records
	function fetch_cleaned_raw($catId, $SubcatId)
	{

		$whr = " where material_type='1'";
		if ($catId=="") $whr .= "";
		else $whr .= " and category_id=".$catId;

		if ($SubcatId=="") $whr .= "";
		else $whr .= " and main_category_id=".$SubcatId;

		$orderBy 	= " order by code;";	
		
		$qry = "SELECT * FROM `m_ingredient`";
		
		if ($whr!="") $qry .= $whr;
		if ($orderBy!="") $qry .= $orderBy;		
		
		//echo $qry;
	
		return $this->databaseConnect->getRecords($qry);
	}
	
	/* end code */
	
	# Returns all Ingredients Records
	function fetchAllRecords()
	{
		/*
		Modified on 09-1-09 (Original)
			$qry = "select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name  from m_ingredient a, ing_category b where b.id=a.category_id order by b.name asc, a.name asc";
		*/
		$qry = " select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name, c.name, b.id, c.id from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id where b.id=a.category_id and a.active='1' order by c.name asc, b.name asc, a.name asc ";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr	= array();
		$i=0;
		$prevCategoryId 	= "";
		$preSubCategoryId 	= "";		
		foreach ($result as $r) {
			$ingredientId 	= $r[0];
			$ingredientCode = $r[1];
			$ingName	= $r[2];
			$ingSurName	= $r[3];
			$ingOpenQty	= $r[4];
			$actualQty	= $r[5];
			$categoryId 	= $r[9]; 
			$categoryName	= $r[7];
			$subCategoryId 	= $r[8];
			$subCategoryName = $r[6];			
			if ($prevCategoryId!=$categoryId) {
				$resultArr [$i]      = array('','',"----- $categoryName -------");	
				$i++;
			}
			/*
			if ($preSubCategoryId!=$subCategoryId) {
				$resultArr [$i]      = "-- $subCategoryName --";	
				$i++;
			}
			*/			
			$resultArr[$i] = array($ingredientId,$ingredientCode,$ingName);
			$prevCategoryId 	= $categoryId;
			$preSubCategoryId 	= $subCategoryId;
			$i++;
		}
		//echo $qry;
		//return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
		//return new ResultSetIterator($resultArr);
		return $resultArr;
	}
	
	#Returns Ingredients for a particular category (Using in Filter)
	function getIngredientRecords($mainCategoryFilterId, $categoryFilterId)
	{
	    if($mainCategoryFilterId!="" && $categoryFilterId=="")
		{
			$qry = "select id, code, name from m_ingredient where main_category_id='$mainCategoryFilterId' and active='1'";
			$result = $this->databaseConnect->getRecords($qry);
			return (sizeof($result>0))?$result:"";
		}
		else if($categoryFilterId!="" && $mainCategoryFilterId=="")
		{
			$qry = "select id, code, name from m_ingredient where category_id='$categoryFilterId' and active='1'";
			$result = $this->databaseConnect->getRecords($qry);
			return (sizeof($result>0))?$result:"";
		}
		else if($mainCategoryFilterId!="" && $categoryFilterId!="")
		{
			$qry = "select id, code, name from m_ingredient where category_id='$categoryFilterId' and active='1'";
			$result = $this->databaseConnect->getRecords($qry);
			return (sizeof($result>0))?$result:"";
		}
		else 
		{
			$qry = " select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name, c.name, b.id, c.id from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id where b.id=a.category_id and a.active='1' order by c.name asc, b.name asc, a.name asc ";
		    //echo $qry; 
			$result	=	$this->databaseConnect->getRecords($qry);
			$resultArr	= array();
			$i=0;
			$prevCategoryId 	= "";
			$preSubCategoryId 	= "";		
			foreach ($result as $r) {
				$ingredientId 	= $r[0];
				$ingredientCode = $r[1];
				$ingName	= $r[2];
				$ingSurName	= $r[3];
				$ingOpenQty	= $r[4];
				$actualQty	= $r[5];
				$categoryId 	= $r[9]; 
				$categoryName	= $r[7];
				$subCategoryId 	= $r[8];
				$subCategoryName = $r[6];			
				if ($prevCategoryId!=$categoryId) {
					$resultArr [$i]      = array('','',"----- $categoryName -------");	
					$i++;
				}
				/*
				if ($preSubCategoryId!=$subCategoryId) {
					$resultArr [$i]      = "-- $subCategoryName --";	
					$i++;
				}
				*/			
				$resultArr[$i] = array($ingredientId,$ingredientCode,$ingName);
				$prevCategoryId 	= $categoryId;
				$preSubCategoryId 	= $subCategoryId;
				$i++;
			}
			return $resultArr;
		}
	}
	
	#Fetch All Raw Ingredients
	function fetchAllRawIngredients()
	{
		$qry = " select a.id, a.code, a.name, a.surname, a.opening_qty, a.actual_quantity, b.name, c.name, b.id, c.id from (m_ingredient a, ing_category b) left join ing_main_category c on a.main_category_id=c.id where b.id=a.category_id && a.material_type=1 && a.active='1' order by c.name asc, b.name asc, a.name asc ";
		$result	=	$this->databaseConnect->getRecords($qry);
		$rawResultArr	= array();
		$i=0;
		$prevCategoryId 	= "";
		$preSubCategoryId 	= "";		
		foreach ($result as $r) {
			$ingredientId 	= $r[0];
			$ingredientCode = $r[1];
			$ingName	= $r[2];
			$ingSurName	= $r[3];
			$ingOpenQty	= $r[4];
			$actualQty	= $r[5];
			$categoryId 	= $r[9]; 
			$categoryName	= $r[7];
			$subCategoryId 	= $r[8];
			$subCategoryName = $r[6];			
			if ($prevCategoryId!=$categoryId) {
				$rawResultArr [$i]      = array('','',"----- $categoryName -------");	
				$i++;
			}
					
			$rawResultArr[$i] = array($ingredientId,$ingredientCode,$ingName);
			$prevCategoryId 	= $categoryId;
			$preSubCategoryId 	= $subCategoryId;
			$i++;
		}
		
		return $rawResultArr;
	}

	# Get a Record based on id
	function find($ingredientId)
	{
		$qry = "select id, code, name, surname, category_id, description, opening_qty, main_category_id, material_type, Raw_ing, Yeild,clearing_cost,Packing_size from m_ingredient where id=$ingredientId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function getCritical($ingredientId)
	{
		$qry = "select id, parameter_id, critical_status from ing_critical_master where ing_id=$ingredientId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}

	# Delete a Record
	function deleteIngredient($ingredientId)
	{
		$qry	= " delete from m_ingredient where id=$ingredientId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function deleteCriticalData($ingredientId)
	{
		$qry	= " delete from ing_critical_master where ing_id='$ingredientId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Record
	function updateIngredient($ingredientId, $selCategory, $code, $name, $surName, $descr, $openingQty, $hidExistingQty, $mainCategoryId, $materialType, $Packing_size)
	{
		//Update the actual Qty
		$updateField = "";
		if ($openingQty!=$hidExistingQty) {
			$actualQty = $openingQty-$hidExistingQty;
			if ($actualQty>0) $updateField = ", actual_quantity=actual_quantity+$actualQty";
			else $updateField = ", actual_quantity=actual_quantity-'".abs($actualQty)."'";
		}

		$qry	= " update m_ingredient set code='$code', name='$name', surname='$surName', category_id='$selCategory', description='$descr', opening_qty='$openingQty', main_category_id='$mainCategoryId', material_type='$materialType',Packing_size='$Packing_size' $updateField where id=$ingredientId ";
		
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# Check Any entry exist in Ingredient Rate Master table
	function checkIngredientRateMaster($ingredientId)
	{
		$qry = "select id from m_ingredient_rate where ingredient_id='$ingredientId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	# Check Any entry exist in Purchase Order table
	function checkPurchaseOrder($ingredientId)
	{
		$qry = "select id from ing_purchaseorder_entry where ingredient_id='$ingredientId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	# Check Any entry exist in Supplier Ingredient table
	function checkSupplierIngredient($ingredientId)
	{
		$qry = "select id from m_supplier_ing where ingredient_id='$ingredientId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	# Check Any entry exist in Rate Master table
	function checkRecipeMaster($ingredientId)
	{
		$qry = "select id from m_recipemaster_entry where ingredient_id='$ingredientId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}

	#Get Total Qty of a Ingredient (usng in Other Screen)
	function  getTotalStockQty($ingredientId)
	{
		$qry = "select actual_quantity from m_ingredient where id='$ingredientId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	# Getting Ing Name
	function getIngName($ingredientId)
	{
		$rec = $this->find($ingredientId);
		return (sizeof($rec)>0)?$rec[2]:"";
	}
	
	# Ing Rate List
	function getIngCurrentRate($ingredientId,$latestIngRateListId)
	{
		$qry = " select rate_per_kg from m_ingredient_rate where ingredient_id='$ingredientId' and rate_list_id='$latestIngRateListId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}


	function updateingredientconfirm($ingredientId)
	{
	$qry	= "update m_ingredient set active='1' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


	function updateingredientReleaseconfirm($ingredientId)
	{
		$qry	= "update m_ingredient set active='0' where id=$ingredientId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	
	function chkValidGatePassId($selDate)
	{
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and  type='IGC'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}

	function getAlphaCode()
	{
		$qry = "select alpha_code from number_gen where type='IGC'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec;
	}
	
	function checkDisplayExist()
	{
	                                                                                                                
		$qry = "select (count(*)) from m_ingredient";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getmaxId()
	{
		$qry = "select 	code from  m_ingredient order by id desc limit 1";
		return $this->databaseConnect->getRecord($qry);
	}


	function getValidId($selDate)
	{
		$qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='RG'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function getValidendnoId($selDate)
	{
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='RG'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}

	function chkValidId($selDate)
	{
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 and  type='RG'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}



	function generateIngredientCode()
	{
		$selDate=Date('Y-m-d');
		$checkGateNumberSettingsExist=$this->chkValidGatePassId($selDate);
		if (sizeof($checkGateNumberSettingsExist)>0){
		$alphaCode=$this->getAlphaCode();
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert($alphaCodePrefix);
		$numbergen=$checkGateNumberSettingsExist[0][0];
		//$objResponse->alert($alphaCodePrefix);
		$checkExist=$this->checkDisplayExist();
		if ($checkExist>0){
		$getFirstRecord=$this->getmaxId();
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
			if($getFirstRec=="0")
			{
				$validStartNo=$this->getValidId($selDate);	
				$nextGatePassId=$validStartNo[0];
			//get first number
			}
			else
			{
				$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
				//$objResponse->alert($getFirstRecEx[1]);
				$nextId=$getFirstRecEx[1]+1;
			}
			
		$validendno=$this->getValidendnoId($selDate);
		//$objResponse->alert($nextGatePassId);
		if ($nextId>$validendno){
			$IngredientMsg="Please set the Code in Settings,since it reached the end no";
			$ingredientCode=""; 
			$numbergenId="";
			//$objResponse->assign("message","innerHTML",$IngredientMsg);
		}
		else{
		
			$ingredientCode="$alphaCodePrefix$nextId";
			$numbergenId=$numbergen;
			$IngredientMsg="";
			//$objResponse->alert($disGateNo);
			//$objResponse->assign("ingredientCode","value","$disIngredient");	
			//$objResponse->assign("number_gen_id","value","$numbergen");	
		
		}
		
		}
		else{
		
			$validPassNo=$this->getValidId($selDate);	
			$checkPassId=$this->chkValidId($selDate);
			$ingredientCode="$alphaCodePrefix$validPassNo";
			$numbergenId=$numbergen;
			$IngredientMsg="";
			//$objResponse->assign("ingredientCode","value","$disIngredient");
			//$objResponse->assign("number_gen_id","value","$numbergen");		
		}
		
		}
		else{
		//$objResponse->alert("hi");
			$IngredientMsg="Please set the Code in Settings";
			$ingredientCode=""; 
			$numbergenId="";
		//$objResponse->assign("message","innerHTML",$IngredientMsg);
		}
		$resultArr=array($IngredientMsg,$ingredientCode,$numbergenId);
		return $resultArr;
	}

	function criticalParameters()
	{
		$qry	= "select id,name,entry_type from ing_critical_parameters where active='1' order by entry_type";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
	}
	
	#Return Ingredient Stock
	function getIngredientStock($ingredientId)
	{
		$qry = "select sum(a.quantity) as quantity from m_supplier_ing_qty a left join m_supplier_ing b on a.supplier_ing_id=b.id where ingredient_id='$ingredientId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result))?$result:"";
	}

	
	

}