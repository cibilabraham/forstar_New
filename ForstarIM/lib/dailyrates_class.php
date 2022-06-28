<?php
Class DailyRates
{
	/****************************************************************
	This class deals with all the operations relating to Daily Rate 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function DailyRates(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
		
	# Add Daily Rate
	function addDailyRate($currentDate, $landingCenterId, $supplier, $fishId, $processCodeId)
	{
		$qry	= " insert into t_dailyrates (center_id, fish_id, supplier_id, date, processcode_id) values($landingCenterId, $fishId, $supplier, '$currentDate', '$processCodeId')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else 		 	$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Add Entry Rec
	function addDailyRateEntryRec($lastId, $selGradeId, $countAverage, $higherCount, $lowerCount, $marketRate, $decRate)
	{
		$qry	= " insert into t_dailyrates_entry (main_id, grade_id, count_avg, high_count, low_count, market_rate, decl_rate) values('$lastId', '$selGradeId', '$countAverage', '$higherCount', '$lowerCount', '$marketRate', '$decRate')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus)	$this->databaseConnect->commit();
		else 		 	$this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all fishs 
	/*
	function fetchAllRecords()
	{
		$qry	= "select  a.id, a.fish_id, a.grade_id, a.center_id, a.date, a.supplier_id, a.marketrate, a.decrate, a.count,a.processcode_id,b.name  from t_dailyrates a left join m_fish b on a.fish_Id = b.id order by a.date desc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	*/
	# Filter table using fish id
	function dailyRateRecFilter($filterId, $recordsDate, $supplierFilterId)
	{		
		$whr		=  "a.date='".$recordsDate."'" ;
		if ($filterId!=0) $whr	.= " and c.id = '".$filterId."'" ;		
		if ($supplierFilterId!="") $whr .= " and (a.supplier_id='$supplierFilterId' or a.supplier_id=0 )";

		$orderBy	=	"a.date asc, c.name asc, d.code asc, b.count_avg asc, e.code asc";

		$qry		= " select a.id, a.fish_id, b.grade_id, a.center_id, a.date, a.supplier_id, b.market_rate, b.decl_rate, b.count_avg, a.processcode_id, c.name, b.id, d.code, e.code from t_dailyrates a left join t_dailyrates_entry b on a.id=b.main_id join m_fish c on a.fish_Id = c.id join m_processcode d on a.processcode_id=d.id left join m_grade e on b.grade_id=e.id ";

		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="")	$qry   .= " order by ".$orderBy;
				
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
		
	# Filter table using fish id (PAGING)
	function dailyRateRecPagingFilter($filterId, $recordsDate, $offset, $limit, $supplierFilterId)
	{
		$whr		=	"a.date='".$recordsDate."'" ;
		if ($filterId!=0) $whr	.=	" and c.id = '".$filterId."'" ;
		if ($supplierFilterId!="")	$whr .= " and (a.supplier_id='$supplierFilterId' or a.supplier_id=0 )";		

		$orderBy	=	"a.date asc, c.name asc, d.code asc, b.count_avg asc, e.code asc";

		$limit		=	" ".$offset.", ".$limit."";

		$qry		= " select a.id, a.fish_id, b.grade_id, a.center_id, a.date, a.supplier_id, b.market_rate, b.decl_rate, b.count_avg, a.processcode_id, c.name, b.id, d.code, e.code from t_dailyrates a left join t_dailyrates_entry b on a.id=b.main_id join m_fish c on a.fish_Id = c.id join m_processcode d on a.processcode_id=d.id left join m_grade e on b.grade_id=e.id ";

		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="")	$qry   .= " order by ".$orderBy;
		if ($limit!="")		$qry   .= " limit ".$limit;
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter table using fish id
	function find($dailyrateId)
	{
		$qry = "select  a.id, a.center_id, a.fish_id, a.supplier_id, a.processcode_id, a.date from t_dailyrates a where a.id=$dailyrateId ";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result;
	}

	function getDailyRateEntryRecs($dailyRateId)
	{
		$qry = " select id, grade_id, count_avg, high_count, low_count, market_rate, decl_rate from t_dailyrates_entry where main_id='$dailyRateId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	# update Daily Rate  record 
	function updateDailyRate($dailyRateId, $landingCenterId, $supplier, $fishId, $processCodeId) {
		$qry	= " update t_dailyrates set center_id='$landingCenterId', fish_id='$fishId', supplier_id='$supplier', processcode_id='$processCodeId' where id=$dailyRateId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else 		$this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Daily Rate Entry Rec
	function delDailyRateEntryRecs($dailyRateId)
	{
		$qry	=	" delete from t_dailyrates_entry where main_id=$dailyRateId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;
	}
	
	# Delete Daily Rate
	function deleteDailyRate($dailyRateId)
	{
		$qry	=	" delete from t_dailyrates where id=$dailyRateId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function deleteDailyRateEntryRec($entryId)
	{
		$qry	=	" delete from t_dailyrates_entry where id=$entryId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) 	$this->databaseConnect->commit();
		else		$this->databaseConnect->rollback();		
		return $result;
	}

	# Checking More Entry Exist
	function chkMoreEntryExist($dailyRateId)
	{
		$qry = " select id from t_dailyrates_entry where main_id=$dailyRateId";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false; 
	}


	#Date wise records
 	function fetchAllDateRecords()
	{
		$qry	=	"select distinct date from t_dailyrates";
		//echo $qry;	
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Grade selection for Raw Grades
 	function fetchSelectedGrade($processCodeId)
	{
 		$qry	=	"select b.id, b.code from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$processCodeId' and a.unit_select='r' order by b.code asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
 	}

	# Copied From Supplier Master
	function fetchSupplierRecords($centerId)
	{
 		$qry	= "select a.supplier_id, b.name from m_supplier2center a, supplier b where a.supplier_id=b.id and a.center_id='$centerId' order by b.name asc ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array('0'=>'-- Select All --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
 	}

	# Filter m_processcode table using fish id (Copied from Processcode_class)
	function processCodeRecFilter($filterId)
	{
		$qry = "select a.id, a.code from m_processcode a, m_fish b where a.fish_id = b.id and b.id='$filterId' order by b.name asc, a.code asc";		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	# get Exisitng Fish recs from Daily rates based on date
	function getExistingFishRecs($selDate, $landingCenterId, $supplierId)
	{
		$whr = " a.date='$selDate' ";
		if ($landingCenterId!=0) $whr .= " and a.center_id='$landingCenterId' ";
		if ($supplierId!=0) $whr .= " and a.supplier_id='$supplierId' ";
		$orderBy	= 	" b.name asc";

		$qry = " select  distinct a.fish_id, b.name from t_dailyrates a left join m_fish b on a.fish_id=b.id ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by".$orderBy;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;		
	}

	# Get Existing Process Code Based on Date and Fish Id
	function getExistingPcsCodeRecs($selDate, $landingCenterId, $supplierId, $fishId)
	{	
		$whr = " a.fish_id = b.id and b.id='$fishId' and tdr.date='$selDate' ";

		if ($landingCenterId!=0) $whr .= " and tdr.center_id='$landingCenterId' ";
		if ($supplierId!=0) $whr .= " and tdr.supplier_id='$supplierId' ";

		$orderBy	= 	" b.name asc, a.code asc";

		$qry = " select  distinct a.id, a.code from t_dailyrates tdr left join m_processcode a on tdr.processcode_id=a.id, m_fish b ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by".$orderBy;
	
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;		
	}

	# Get Existing Landing center
	function getExistingLandingCenters($selDate)
	{
		$qry = "select distinct a.id, a.name from t_dailyrates tdr join m_landingcenter a on tdr.center_id=a.id where tdr.date='$selDate' order by a.name asc";	
		//echo $qry;	
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultArr = array('0'=>'-- Select All--');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;	
	}

	# Copied From Supplier Master
	function getExistingSupplierRecords($selDate, $centerId)
	{
 		$qry	= "select distinct a.supplier_id, b.name from  m_supplier2center a , supplier b join t_dailyrates tdr on tdr.supplier_id=b.id  where a.supplier_id=b.id and a.center_id='$centerId' and tdr.date='$selDate' order by b.name asc ";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array('0'=>'-- Select All --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
 	}

	# Get Edited Rec
	function getEditRecId($cpyFrmDate, $cpyFrmLandingCenter, $cpyFrmSupplier, $cpyFrmFish, $cpyFrmProcessCode)
	{			
		$whr = " a.date='$cpyFrmDate' and a.fish_id='$cpyFrmFish' and a.processcode_id='$cpyFrmProcessCode' ";

		if ($cpyFrmLandingCenter!=0) $whr .= " and (a.center_id='$cpyFrmLandingCenter' or a.center_id=0) ";
		if ($cpyFrmSupplier!=0) $whr .= " and (a.supplier_id='$cpyFrmSupplier' or a.supplier_id=0) ";

		$qry = " select  a.id from t_dailyrates a ";
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by".$orderBy;

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result[0][0];
	}
	
}	
?>