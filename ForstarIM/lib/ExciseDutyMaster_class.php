<?php
class ExciseDutyMaster
{
	/****************************************************************
	This class deals with all the operations relating to State Vat Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ExciseDutyMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function latestRateList()
	{
		$cDate = date("Y-m-d");	
		$qry	= "select a.id from m_excise_duty_ratelist a where '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	# add a Record
	function addExciseDutyRateList($rateListName, $startDate, $copyRateList, $userId, $edCurRateListId)
	{
		$qry = "insert into m_excise_duty_ratelist (name, start_date, created, created_by) values('".$rateListName."', '".$startDate."', '$startDate', '$userId')";		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			# Update Prev Rate List Rec END DATE
			if ($edCurRateListId!="") {
				$updateRateListEndDate = $this->updateEDRateListRec($edCurRateListId, $startDate);
			}

	#----------------------- Copy Functions ---------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$edRecs = $this->fetchAllEDRecs($copyRateList);
				foreach ($edRecs as $dmr) {
					$exciseDutyId	= $dmr[0];
					$exciseDuty 	= $dmr[1];
					$active		= $dmr[2];
					$pCatId		= $dmr[3];
					$pStateId	= $dmr[4];
					$pGroupId	= $dmr[5];
					$pChapter	= $dmr[6];
					$sGoodsType	= $dmr[7];
					$productId	= $dmr[8];
					// Insert New rec	
					$stateVatRecIns = $this->addCopyFromExciseDuty($pCatId, $pStateId, $pGroupId, $exciseDuty, $insertedRateListId, $exciseDutyId, $pChapter, $sGoodsType, $productId);				
					
				}
			}
	#-------------------- Copy Functions End -------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Insert (Entry) Records
	function addCopyFromExciseDuty($selPCategory, $selPState, $selPGroup, $excisePercent, $exciseDutyRateListId, $baseId, $pChapter, $sGoodsType, $productId)
	{
		$qry = "insert into m_excise_duty (product_category_id, product_state_id, product_group_id, excise_duty, excise_rate_list_id, base_excise_id, chapter_subheading, ex_goods_id, product_id) values('$selPCategory', '$selPState', '$selPGroup', '$excisePercent','$exciseDutyRateListId', '$baseId', '$pChapter', '$sGoodsType', '$productId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# update Dist Rate List Rec
	function updateEDRateListRec($edCurrentRateListId, $startDate=null)
	{
		//$sDate		= explode("-",$startDate);
		//$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date

		if ($startDate) {
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		} else $endDate="0000-00-00";

		$qry = " update m_excise_duty_ratelist set end_date='$endDate' where id=$edCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Fetch All Distributor Margin Records
	function fetchAllEDRecs($selRateList)
	{
		$qry = "select id, excise_duty, active, product_category_id, product_state_id, product_group_id, chapter_subheading, ex_goods_id, product_id from m_excise_duty where excise_rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Insert (Entry) Records
	function addExciseDuty($selPCategory, $selPState, $selPGroup, $excisePercent, $exciseDutyRateListId, $chapterSubheading, $goodsType)
	{
		$qry = "insert into m_excise_duty (product_category_id, product_state_id, product_group_id, excise_duty, excise_rate_list_id, chapter_subheading, ex_goods_id) values('$selPCategory', '$selPState', '$selPGroup', '$excisePercent','$exciseDutyRateListId', '$chapterSubheading', '$goodsType')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function checkExciseDutyExist($selPCategory, $selPState, $selPGroup, $exciseDutyRateListId)
	{
		$qry = "select id from m_excise_duty where product_category_id='$selPCategory' and product_state_id='$selPState' and product_group_id='$selPGroup' and excise_rate_list_id='$exciseDutyRateListId'";
		//echo "<br>$qry<br>";
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $exciseDutyRateListFilterId)
	{
			
		$cDate = date("Y-m-d");
		$whr = " a.product_id=0 ";		
		if ($exciseDutyRateListFilterId)	$whr .= " and a.excise_rate_list_id= ".$exciseDutyRateListFilterId; 		

		if ($exciseDutyRateListFilterId=="") {
			$whr .= " and a.excise_rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_excise_duty_ratelist f ";
		} else {
			$whr .= " and a.excise_rate_list_id=f.id";
			$tableUpdate = " , m_excise_duty_ratelist f";
		}

		$orderBy = " pc.name asc, ps.name asc, pg.name asc ";
		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.excise_rate_list_id, a.excise_duty, pc.name, ps.name, pg.name, a.chapter_subheading, a.ex_goods_id, eg.name,
			(select GROUP_CONCAT(distinct ed.chapter_subheading SEPARATOR ', ') from 
				m_excise_duty ed join m_product_manage pm on ed.product_id=pm.id where 
				ed.excise_rate_list_id=a.excise_rate_list_id and 
				ed.product_category_id=a.product_category_id and
				ed.product_state_id=a.product_state_id and
				ed.product_group_id=a.product_group_id and ed.product_id!=0
				) as exemption,a.activeconfirm
			 from m_excise_duty a join m_product_category pc on pc.id=a.product_category_id
			 join m_product_state ps on  a.product_state_id=ps.id 
			 left join m_product_group pg on a.product_group_id = pg.id
			 left join m_excisable_goods eg on eg.id=a.ex_goods_id	
			";
		$qry .=  " $tableUpdate "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo "<br>$qry<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}

	# Returns all Records
	function fetchAllRecords($exciseDutyRateListFilterId)
	{
		$cDate = date("Y-m-d");
		$whr = " a.product_id=0 ";		
		if ($exciseDutyRateListFilterId)	$whr .= " and a.excise_rate_list_id= ".$exciseDutyRateListFilterId; 		

		if ($exciseDutyRateListFilterId=="") {
			$whr .= " and a.excise_rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_excise_duty_ratelist f ";
		} else {
			$whr .= " and a.excise_rate_list_id=f.id";
			$tableUpdate = " , m_excise_duty_ratelist f";
		}

		$orderBy = " f.start_date desc ";

		$qry = " select a.id, a.excise_rate_list_id, a.excise_duty, pc.name, ps.name, pg.name, a.chapter_subheading, a.ex_goods_id, eg.name,
			(select GROUP_CONCAT(distinct ed.chapter_subheading SEPARATOR ', ') from 
				m_excise_duty ed join m_product_manage pm on ed.product_id=pm.id where 
				ed.excise_rate_list_id=a.excise_rate_list_id and 
				ed.product_category_id=a.product_category_id and
				ed.product_state_id=a.product_state_id and
				ed.product_group_id=a.product_group_id and ed.product_id!=0 
				) as exemption,a.activeconfirm
			 from m_excise_duty a join m_product_category pc on pc.id=a.product_category_id
			 join m_product_state ps on  a.product_state_id=ps.id 
			 left join m_product_group pg on a.product_group_id = pg.id	
			 left join m_excisable_goods eg on eg.id=a.ex_goods_id
			";
		$qry .=  " $tableUpdate "; 

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";
		return new ResultSetIterator($this->databaseConnect->getResultSet($qry));
	}


	# Get a Record based on id
	function find($exciseDutyId)
	{
		$qry = "select id, product_category_id, product_state_id, product_group_id, excise_rate_list_id, excise_duty from m_excise_duty where id=$exciseDutyId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Vat Entry Records
	function getExciseDutyEntryRecords($exciseDutyId)
	{
		$qry = " select id, product_category_id, product_state_id, product_group_id, excise_duty, chapter_subheading, ex_goods_id from m_excise_duty where id='$exciseDutyId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update Vat Entry
	function updateExciseDuty($exciseDutyId, $excisePercent, $chapterSubheading, $goodsType)
	{		
		$qry = " update m_excise_duty set excise_duty='$excisePercent', chapter_subheading='$chapterSubheading', ex_goods_id='$goodsType' where id='$exciseDutyId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update Vat Entry
	function updateExciseDutyByBaseId($exciseDutyId, $excisePercent, $chapterSubheading, $goodsType)
	{		
		$qry = " update m_excise_duty set excise_duty='$excisePercent', chapter_subheading='$chapterSubheading', ex_goods_id='$goodsType' where base_excise_id='$exciseDutyId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Delete Selected State Rec
	function deleteExciseDutyRec($exciseDutyId)
	{
		$qry 	= " delete from m_excise_duty where id=$exciseDutyId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	# Returns all State based Recs
	function filterExciseDutyRateListRecs()
	{
		$qry = "select id, name, start_date from m_excise_duty_ratelist order by start_date desc";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

# Check Whether Product Group Exist
	function checkProductGroupExist($productStateId)
	{
		$qry = "select product_group from m_product_state where id=$productStateId";
		//echo "<br>$qry";
		$rec = $this->databaseConnect->getRecord($qry);
		return ($rec[0]=='Y')?true:false;
	}

	# Filter State List
	function filterProductGroupList($productGroupExist)
	{		
		$qry	=	"select  id, name from m_product_group where active=1 order by name asc";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (!$productGroupExist) $resultArr = array('N'=>'-- No Group --');		
		else if ($productGroupExist) {			
			$resultArr = array('0'=>'-- Select All --');
			while (list(,$v) = each($result)) {
				$resultArr[$v[0]] = $v[1];
			}
		}
		return $resultArr;
	}

	# Get a Record based on id
	function findExciseDuty($rateListId)
	{
		$qry = "select ex_duty_active from m_excise_duty_ratelist";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function updateEDFlag($edActive)
	{
		$curExciseDutyRateListId = $this->latestRateList();

		$qry = " update m_excise_duty_ratelist set ex_duty_active='$edActive' where id='$curExciseDutyRateListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getEDRateListRec($rateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_excise_duty_ratelist where id='$rateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function chkValidEDRLDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_excise_duty_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateEDRLRec($rateListId, $startDate, $rateListName)
	{		
		$qry = " update m_excise_duty_ratelist set start_date='$startDate', name='$rateListName' where id='$rateListId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function chkEDRLInUse($rateListId)
	{
		$qry = "select id from m_excise_duty where excise_rate_list_id='$rateListId'";
		//echo "<br>$qry<br>";
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}

	function deleteEDRLRec($selRateList)
	{
		$qry = " delete from m_excise_duty_ratelist where id='$selRateList'";
	
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			$latestRateListId = $this->latestRateList();
			if ($latestRateListId!="") {
				# Update Prev Rate List Date
				$this->updateEDRateListRec($latestRateListId, $sDate=null);
			}
		}
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	function checkExDutyRecInUse($exciseDutyId)
	{
		$qry = "select id from t_salesorder_entry where ex_duty_id='$exciseDutyId'";
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}

	function getExHeadExemption($pCategoryId, $pStateId, $pGroupId, $rateListId)
	{
		
	}



	function updateExciseDutyconfirm($exciseDutyId){
		$qry	= "update m_excise_duty set activeconfirm='1' where id=$exciseDutyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateExciseDutyReleaseconfirm($exciseDutyId){
		$qry	= "update m_excise_duty set activeconfirm='0' where id=$exciseDutyId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}


	
}
?>