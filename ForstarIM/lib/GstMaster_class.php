<?php
class GstMaster
{
	/****************************************************************
	This class deals with all the operations relating to State Vat Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function GstMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function latestRateList()
	{
		$cDate = date("Y-m-d");	
		$qry	= "select a.id from m_gst_ratelist a where '$cDate'>=date_format(a.start_date,'%Y-%m-%d') order by a.start_date desc";

		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	# add a Record
	function addGstRateList($rateListName, $startDate, $copyRateList, $userId, $edCurRateListId)
	{
		$qry = "insert into m_gst_ratelist (name, start_date, created, created_by) values('".$rateListName."', '".$startDate."', '$startDate', '$userId')";		
		//echo $qry;
		//exit;
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
					$gstId	= $dmr[0];
					$exciseDuty 	= $dmr[1];
					$active		= $dmr[2];
					$pCatId		= $dmr[3];
					$pStateId	= $dmr[4];
					$pGroupId	= $dmr[5];
					$pChapter	= $dmr[6];
					$sGoodsType	= $dmr[7];
					$productId	= $dmr[8];
					// Insert New rec	
					$stateVatRecIns = $this->addCopyFromgst($pCatId, $pStateId, $pGroupId, $exciseDuty, $insertedRateListId, $gstId, $pChapter, $sGoodsType, $productId);				
					
				}
			}
	#-------------------- Copy Functions End -------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Insert (Entry) Records
	function addCopyFromgst($selPCategory, $selPState, $selPGroup, $gstPercent, $gstRateListId, $baseId, $pChapter, $sGoodsType, $productId)
	{
		$qry = "insert into m_gst (product_category_id, product_state_id, product_group_id, gst_per, gst_rate_list_id, base_excise_id, chapter_subheading, ex_goods_id, product_id) values('$selPCategory', '$selPState', '$selPGroup', '$gstPercent','$gstRateListId', '$baseId', '$pChapter', '$sGoodsType', '$productId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# update Dist Rate List Rec
	function updategstRateListRec($edCurrentRateListId, $startDate=null)
	{
		//$sDate		= explode("-",$startDate);
		//$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date

		if ($startDate) {
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		} else $endDate="0000-00-00";

		$qry = " update m_gst_ratelist set end_date='$endDate' where id=$edCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Fetch All Distributor Margin Records
	function fetchAllgstRecs($selRateList)
	{
		$qry = "select id, gst_per, active, product_category_id, product_state_id, product_group_id, chapter_subheading, ex_goods_id, product_id from gst_duty where gst_rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Insert (Entry) Records
	function addGst($selPCategory, $selPState, $selPGroup, $gstPercent, $gstRateListId, $chapterSubheading, $goodsType)
	{
		$qry = "insert into m_gst(product_category_id, product_state_id, product_group_id, gst_per, gst_rate_list_id, chapter_subheading, ex_goods_id) values('$selPCategory', '$selPState', '$selPGroup', '$gstPercent','$gstRateListId', '$chapterSubheading', '$goodsType')";
		//echo $qry;
		//exit;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function checkGstExist($selPCategory, $selPState, $selPGroup, $gstRateListId)
	{
		$qry = "select id from m_gst where product_category_id='$selPCategory' and product_state_id='$selPState' and product_group_id='$selPGroup' and gst_rate_list_id='$gstRateListId'";
		//echo "<br>$qry<br>";
		//exit;
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}


	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit, $gstRateListFilterId)
	{
			
		$cDate = date("Y-m-d");
		$whr = " a.product_id=0 ";		
		if ($gstRateListFilterId)	$whr .= " and a.gst_rate_list_id= ".$gstRateListFilterId; 		

		if ($gstRateListFilterId=="") {
			$whr .= " and a.gst_rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_gst_ratelist f ";
		} else {
			$whr .= " and a.gst_rate_list_id=f.id";
			$tableUpdate = " , m_gst_ratelist f";
		}

		$orderBy = " pc.name asc, ps.name asc, pg.name asc ";
		$limit	 = " $offset,$limit ";

		$qry = " select a.id, a.gst_rate_list_id, a.gst_per, pc.name, ps.name, pg.name, a.chapter_subheading, a.ex_goods_id, eg.name,
			(select GROUP_CONCAT(distinct ed.chapter_subheading SEPARATOR ', ') from 
				m_gst ed join m_product_manage pm on ed.product_id=pm.id where 
				ed.gst_rate_list_id=a.gst_rate_list_id and 
				ed.product_category_id=a.product_category_id and
				ed.product_state_id=a.product_state_id and
				ed.product_group_id=a.product_group_id and ed.product_id!=0
				) as exemption,a.activeconfirm
			 from m_gst a join m_product_category pc on pc.id=a.product_category_id
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
	function fetchAllRecords($gstRateListFilterId)
	{
		$cDate = date("Y-m-d");
		$whr = " a.product_id=0 ";		
		if ($gstRateListFilterId)	$whr .= " and a.gst_rate_list_id= ".$gstRateListFilterId; 		

		if ($gstRateListFilterId=="") {
			$whr .= " and a.gst_rate_list_id=f.id and (('$cDate'>=f.start_date && (f.end_date is null || f.end_date=0)) or ('$cDate'>=f.start_date and '$cDate'<=f.end_date)) "; 
			$tableUpdate = " , m_gst_ratelist f ";
		} else {
			$whr .= " and a.gst_rate_list_id=f.id";
			$tableUpdate = " , m_gst_ratelist f";
		}

		$orderBy = " f.start_date desc ";

		$qry = " select a.id, a.gst_rate_list_id, a.gst_per, pc.name, ps.name, pg.name, a.chapter_subheading, a.ex_goods_id, eg.name,
			(select GROUP_CONCAT(distinct ed.chapter_subheading SEPARATOR ', ') from 
				m_gst ed join m_product_manage pm on ed.product_id=pm.id where 
				ed.gst_rate_list_id=a.gst_rate_list_id and 
				ed.product_category_id=a.product_category_id and
				ed.product_state_id=a.product_state_id and
				ed.product_group_id=a.product_group_id and ed.product_id!=0 
				) as exemption,a.activeconfirm
			 from m_gst a join m_product_category pc on pc.id=a.product_category_id
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
	function find($gstId)
	{
		$qry = "select id, product_category_id, product_state_id, product_group_id, gst_rate_list_id, gst_per from m_gst where id=$gstId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Vat Entry Records
	function getGstEntryRecords($gstId)
	{
		$qry = " select id, product_category_id, product_state_id, product_group_id, gst_per, chapter_subheading, ex_goods_id from m_gst where id='$gstId' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Update Vat Entry
	function updateGst($gstId, $gstPercent, $chapterSubheading, $goodsType)
	{		
		$qry = " update m_gst set gst_per='$gstPercent', chapter_subheading='$chapterSubheading', ex_goods_id='$goodsType' where id='$gstId'";
		//echo $qry;
		//exit;
		
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Update Vat Entry
	function updateGstByBaseId($gstId, $gstPercent, $chapterSubheading, $goodsType)
	{		
		$qry = " update m_gst set gst_per='$gstPercent', chapter_subheading='$chapterSubheading', ex_goods_id='$goodsType' where base_excise_id='$gstId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	# Delete Selected State Rec
	function deleteGstRec($gstId)
	{
		$qry 	= " delete from m_gst where id=$gstId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	
	# Returns all State based Recs
	function filterGstRateListRecs()
	{
		$qry = "select id, name, start_date from m_gst_ratelist order by start_date desc";
		
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
	function findGst($rateListId)
	{
		$qry = "select gst_active from m_gst_ratelist";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function updateGSTFlag($edActive)
	{
		$curgstRateListId = $this->latestRateList();

		$qry = " update m_gst_ratelist set ex_duty_active='$edActive' where id='$curgstRateListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getGSTRateListRec($rateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_gst_ratelist where id='$rateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}



	function checkGstRecInUse($gstId)
	{
		$qry = "select id from t_salesorder_entry where gst_entry_id='$gstId'";
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}



	function updateGstconfirm($gstId){
		$qry	= "update m_gst set activeconfirm='1' where id=$gstId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateGstReleaseconfirm($gstId){
		$qry	= "update m_gst set activeconfirm='0' where id=$gstId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	
	function chkValidEDRLDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_gst_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	function updateEDRLRec($rateListId, $startDate, $rateListName)
	{		
		$qry = " update m_gst_ratelist set start_date='$startDate', name='$rateListName' where id='$rateListId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function deleteEDRLRec($selRateList)
	{
		$qry = " delete from m_gst_ratelist where id='$selRateList'";
	
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
	
	
	function updateEDRateListRec($edCurrentRateListId, $startDate=null)
	{
		//$sDate		= explode("-",$startDate);
		//$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date

		if ($startDate) {
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		} else $endDate="0000-00-00";

		$qry = " update m_gst_ratelist set end_date='$endDate' where id=$edCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}	

	#Fetch All Distributor Margin Records
	function fetchAllEDRecs($selRateList)
	{
		$qry = "select id, gst_per, active, product_category_id, product_state_id, product_group_id, chapter_subheading, ex_goods_id, product_id from m_gst where gst_rate_list_id='$selRateList'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
	

	function updateEDFlag($edActive)
	{
		$curExciseDutyRateListId = $this->latestRateList();

		$qry = " update m_gst_ratelist set gst_active='$edActive' where id='$curExciseDutyRateListId' ";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
		function chkEDRLInUse($rateListId)
	{
		$qry = "select id from m_gst where gst_rate_list_id='$rateListId'";
		//echo "<br>$qry<br>";
		$recs = $this->databaseConnect->getRecords($qry);
		return (sizeof($recs)>0)?true:false;
	}
	
	
}
?>