<?php
class WaterMark
{
	/****************************************************************
	This class deals with all the operations relating to Batch Creation
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WaterMark(&$databaseConnect)
 {
        	$this->databaseConnect =&$databaseConnect;
	}
	
	// Insert New Item
	function addWatermarkCodeRecs($WatermarkCode,$WtChallanNoWM,$companyName,$plantName,$WtDateWM,$TodayDateTime,$WTUser)
	{
		$selqry = "select * from tbl_watermark where watermarkcode='$WatermarkCode' and wtchallanno='$WtChallanNoWM' and billcompany='$companyName' and supplied='$plantName' and wtdate='$WtDateWM' and printdate='$TodayDateTime' and user='$WTUser'"; 
		$selStatus	=	$this->databaseConnect->getRecord($selqry);	
		if (sizeof($selStatus)==0) 
		{
		$qry = "insert into tbl_watermark (watermarkcode,wtchallanno,billcompany,supplied,wtdate,printdate,user) values('".$WatermarkCode."','".$WtChallanNoWM."','".$companyName."','".$plantName."','".$WtDateWM."','".$TodayDateTime."','".$WTUser."')";
				
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus){
			$this->databaseConnect->commit();
		}
		else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;	
		}		
	}

	# Return Paging Records
	function fetchAllPagingRecordsWM($offset, $limit,$WaterMarkFilterId,$WaterMarkFilterIdBillComp,$WaterMarkFilterIdUser)
	{
		$whr = "1 = 1";
		//if ($WaterMarkFilterId!="") $whr .= " and watermarkcode='$WaterMarkFilterId'";
		if ($WaterMarkFilterId!="") $whr .= " and watermarkcode like '%$WaterMarkFilterId%'";
		if ($WaterMarkFilterIdBillComp!="") $whr .= " and billcompany='$WaterMarkFilterIdBillComp'";
		if ($WaterMarkFilterIdUser!="") $whr .= " and user='$WaterMarkFilterIdUser'";

		$orderBy 	= " id desc ";
		$limit 		= " $offset,$limit";

	/*	$qry	=   "select id,watermarkcode,wtchallanno,billcompany,supplied,wtdate,printdate,user from tbl_watermark order by id desc limit $offset,$limit";	*/
		$qry	=   "select id,watermarkcode,wtchallanno,billcompany,supplied,wtdate,printdate,user from tbl_watermark ";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);

		//echo "<pre>"; print_r($result); exit;
		return $result;
	}
	
	// Getting All Records 
    function fetchAllRecords($WaterMarkFilterId,$WaterMarkFilterIdBillComp,$WaterMarkFilterIdUser)
	{
			$whr = "1 = 1";
		//if ($WaterMarkFilterId!="") $whr .= " and watermarkcode='$WaterMarkFilterId'";
		if ($WaterMarkFilterId!="") $whr .= " and watermarkcode like '%$WaterMarkFilterId%'";
		if ($WaterMarkFilterIdBillComp!="") $whr .= " and billcompany='$WaterMarkFilterIdBillComp'";
		if ($WaterMarkFilterIdUser!="") $whr .= "and user='$WaterMarkFilterIdUser'";

		$orderBy 	= " id desc ";

		/*$qry	=	"select id,watermarkcode,wtchallanno,billcompany,supplied,wtdate,printdate,user from tbl_watermark order by id desc";*/
		$qry	=	"select id,watermarkcode,wtchallanno,billcompany,supplied,wtdate,printdate,user from tbl_watermark";

		if ($whr!="") 		$qry .= " where ".$whr;		
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

function fetchWaterMarkCode()
	{
		$qry	= "select id, watermarkcode from tbl_watermark group by watermarkcode";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
 
 function fetchWaterMarkBillComp()
 {
 		$qry	= "select id, name from m_billing_company order by id";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 }

 function fetchWaterMarkUser()
 {
 		$qry	= "select id, StaffName from user where StaffName != '' order by id";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
 }

 function fetchStaffNameRec($WTUser)
{
 		$qry	= "select  StaffName from user where username='$WTUser'";	
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
}
}
