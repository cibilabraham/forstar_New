<?php
class IngredientRateList
{
	/****************************************************************
	This class deals with all the operations relating to Process Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function IngredientRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#add a Record
	function addIngredientRateList($rateListName, $startDate, $copyRateList, $ingCurrentRateListId, $userId)
	{
		$qry	=	"insert into m_ingredient_ratelist (name,start_date) values('".$rateListName."','".$startDate."')";
		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($ingCurrentRateListId!="") {
				$updateRateListEndDate = $this->updateRateListRec($ingCurrentRateListId, $startDate);
			}
	#-----------------------------Copy Functions-------------------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$ingredientRateRecords = $this->fetchAllIngredientRateRecords($copyRateList);
				foreach ($ingredientRateRecords as $irr) {
					//$ingredientRateId =	$irr[0];
					$selIngredient	=	$irr[1];
					$ingRatePerKg	=	$irr[2];
					$ingYield	=	$irr[3];
					$ingHighPrice	=	$irr[4];
					$ingLowPrice	=	$irr[5];
					$ingLastPrice	=	$irr[6];
					//$ingRateList	=	$irr[7];
					//$created	=	$irr[8];
					//$userId		=	$irr[9];
							
					$ingRateInsertStatus = $this->addIngredientRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $insertedRateListId, $userId);
				}
			}
	#----------------------------Copy Functions End   --------------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	/**
	* Returns all Paging Recs
	*/
	function fetchAllPagingRecords($offset, $limit)
	{
		$qry	= "select id, name, start_date,active from m_ingredient_ratelist order by start_date desc limit $offset, $limit";
		//echo "<br>$qry";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Recs
	function fetchAllRecords()
	{
		$qry	= "select id, name, start_date,active from m_ingredient_ratelist order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Rec based on id 	
	function find($rateListId)
	{
		$qry	=	"select id, name, start_date from m_ingredient_ratelist where id=$rateListId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateIngredientRateList($rateListName, $startDate, $ingredientRateListId)
	{
		$qry = " update m_ingredient_ratelist set name='$rateListName', start_date='$startDate' where id=$ingredientRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteIngredientRateList($ingredientRateListId)
	{
		$qry	=	" delete from m_ingredient_ratelist where id=$ingredientRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList();
			# Update Prev Rate List Date
			$sDate = "0000-00-00";			
			$this->updatePrevRateListRec($latestRateListId, $sDate);
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	#Checking Rate List Id used
	function checkRateListUse($ingredientRateListId)
	{
		$qry	=	"select id from m_ingredient_rate where rate_list_id='$ingredientRateListId'";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Current Rate List
	function latestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	=	"select a.id from m_ingredient_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Using in other Screen
	function findRateList()
	{
		$cDate = date("Y-m-d");
		$qry	=	"select a.id,name,start_date from m_ingredient_ratelist a where '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}

#---------------------------------Copy Functions---------------------------------------------
	#Fetch All Ingredient Rate Records
	function fetchAllIngredientRateRecords($selRateList)
	{
		$qry	= "select id, ingredient_id, rate_per_kg, yield, highest_price, lowest_price, last_price, rate_list_id, created, createdby from m_ingredient_rate where rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Insert Record PreProcess Record
	function addIngredientRate($selIngredient, $ingRatePerKg, $ingYield, $ingHighPrice, $ingLowPrice, $ingLastPrice, $insertedRateListId, $userId)
	{
		$qry	=	"insert into m_ingredient_rate (ingredient_id, rate_per_kg, yield, highest_price, lowest_price, last_price, rate_list_id, created, createdby) values('$selIngredient', '$ingRatePerKg', '$ingYield', '$ingHighPrice', '$ingLowPrice', '$ingLastPrice', '$insertedRateListId', Now(), '$userId')";
		//echo $qry."<br>";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
#------------------------------Copy Functions End------------------------------------------------

	# update Dist Rate List Rec
	function updateRateListRec($ingCurrentRateListId, $startDate)
	{
		$sDate		= explode("-",$startDate);
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_ingredient_ratelist set end_date='$endDate' where id=$ingCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	# update Prev Rate List of current dist Rec
	function updatePrevRateListRec($ingCurrentRateListId, $sDate)
	{		
		$qry = " update m_ingredient_ratelist set end_date='$endDate' where id=$ingCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateRateListconfirm($ingCurrentRateListId)
	{
	$qry	= "update m_ingredient_ratelist set active='1' where id=$ingCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateRateListReleaseconfirm($ingCurrentRateListId)
	{
		$qry	= "update m_ingredient_ratelist set active='0' where id=$ingCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

}
?>