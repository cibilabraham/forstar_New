<?php
class USDValue
{  
	/****************************************************************
	This class deals with all the operations relating to USD Value 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class

	function USDValue(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addUSDValue($dollarValue,$description)
	{
		$qry	= "insert into m_usd (usd,descr) values('".$dollarValue."','".$description."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Get Record
	function find()
	{
		$qry	= "select id,usd,descr from m_usd where id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateUSDValue($USDId,$dollarValue,$description)
	{
		$qry	= " update m_usd set usd='$dollarValue', descr='$description' where id=$USDId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function findUSDValue()
	{
		$rec = $this->find();
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# =============================================================================================================

	#Add
	function addCurrency($currencyCode, $currencyValue, $description, $cyRateListId)
	{
		$qry	= "insert into m_currency (code, currency_value, descr, rate_list_id) values('$currencyCode', '$currencyValue', '$description', '$cyRateListId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function addCurrencyRateList($rateListName, $startDate, $userId, $currencyValue, $description)
	{
		$qry	= "insert into m_currency_ratelist (name, start_date, created, created_by, currency_value, descr) values('$rateListName', '$startDate', NOW(), '$userId', '$currencyValue', '$description')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}	

	function insertedCYLatestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select id from m_currency_ratelist where '$cDate'>=date_format(start_date,'%Y--%m-%d') and (currency_id=0 or currency_id is null) order by start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function chkCurrencyExist($currencyCode)
	{
		$qry	= "select id from m_currency where code='$currencyCode'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}

	function updateCurrencyRateList($cyRateListId, $cyLatestId, $currencyValue, $description)
	{
		$qry	= " update m_currency_ratelist set currency_id='$cyLatestId', currency_value='$currencyValue', descr='$description' where id=$cyRateListId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Returns all Records
	function fetchAllRecords($currencyId=null)
	{
		/*
		$qry	= "select id, code, currency_value, descr, rate_list_id from m_currency order by code asc";		
		*/

		$whr 	= " mc.id=mcrl.currency_id";

		if ($currencyId!="") $whr .= " and mcrl.currency_id=".$currencyId;
		else $whr .= " and ((CURDATE()>=mcrl.start_date && (mcrl.end_date is null || mcrl.end_date=0)) or (CURDATE()>=mcrl.start_date and CURDATE()<=mcrl.end_date))";

		$orderBy  = " mc.code asc, mcrl.start_date desc";
		$limit = " $offset, $limit ";
				
		$qry	= "select mc.id, mc.code, mcrl.currency_value, mcrl.descr, mc.rate_list_id, mcrl.start_date,active,(select count(a.id) from t_purchaseorder_main a where a.currency_ratelist_id=mcrl.id) as tot from m_currency mc, m_currency_ratelist mcrl";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit, $currencyId=null)
	{
		$whr 	= " mc.id=mcrl.currency_id";

		if ($currencyId!="") $whr .= " and mcrl.currency_id=".$currencyId;
		else $whr .= " and ((CURDATE()>=mcrl.start_date && (mcrl.end_date is null || mcrl.end_date=0)) or (CURDATE()>=mcrl.start_date and CURDATE()<=mcrl.end_date))";

		$orderBy  = " mc.code asc, mcrl.start_date desc";
		$limit = " $offset, $limit ";
				
		$qry	= "select mc.id, mc.code, mcrl.currency_value, mcrl.descr, mc.rate_list_id, mcrl.start_date, mcrl.id as rateListId,mc.active,(select count(a.id) from t_purchaseorder_main a where a.currency_ratelist_id=mcrl.id) as tot from m_currency mc, m_currency_ratelist mcrl";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;

		//$qry	= "select id, code, currency_value, descr, rate_list_id from m_currency order by code asc limit $offset, $limit";		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	function findCY($editId)
	{
		$qry	= "select id, code, currency_value, descr, rate_list_id from m_currency where id='$editId' ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# CY Rate List rec
	function cyRLRec($rateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_currency_ratelist where id='$rateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function getCYRLRecs($currencyId)
	{
		$qry	= " select id, name, start_date, end_date from m_currency_ratelist where currency_id='$currencyId' order by start_date desc";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function chkValidRateListDate($seldate, $cId=null, $currencyId=null)
	{
		$uptdQry ="";
		if ($cId!="")		$uptdQry .= " and a.id!=$cId";
		if ($currencyId!="") $uptdQry .= " and a.currency_id=$currencyId";
		
		$qry	= "select a.id, a.name, a.start_date from m_currency_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# update Rate List Rec
	function updateCYRateListRec($latestRateListId, $startDate=null)
	{
		if ($startDate) {
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		} else $endDate="0000-00-00";

		$qry = " update m_currency_ratelist set end_date='$endDate' where id='$latestRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateCurrency($currencyId, $currencyCode, $currencyValue, $description, $cyRateListId)
	{
		$qry	= " update m_currency set code='$currencyCode', currency_value='$currencyValue', descr='$description', rate_list_id='$cyRateListId' where id=$currencyId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateRateListRec($cyRateListId, $startDate)
	{
		$qry = " update m_currency_ratelist set start_date='$startDate' where id='$cyRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}


	function getCYRecs()
	{
		$qry	= " select id, code from m_currency where active=1 order by code asc";
		//$qry	= " select id, code from m_currency order by code asc";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	# Delete a Rec
	function deleteCurrencyRL($currencyId, $selCYRateListId)
	{
		$qry = " delete from m_currency_ratelist where id=$selCYRateListId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
			list($latestRateListId,$currencyValue) = $this->latestRateList($currencyId);
			if ($latestRateListId!="") {
				# Update Prev Rate List Date
				$sDate = "0000-00-00";
				$this->updatePrevRateListRec($latestRateListId, $sDate);
				$this->updateCurrencyMaster2Latest($currencyId,$latestRateListId,$currencyValue);
			}
		}
		else $this->databaseConnect->rollback();
		
		return $result;
	}

	# Latest rate list	
	function latestRateList($currencyId)
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id, mcrl.currency_value from m_currency_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') and currency_id='$currencyId' order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1]):array();
	}

	function updatePrevRateListRec($pageCurrentRateListId, $sDate)
	{		
		$qry = " update m_currency_ratelist set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateCurrencyMaster2Latest($currencyId, $CYRateListId, $currencyValue)
	{		
		$qry = " update m_currency set currency_value='$currencyValue', rate_list_id='$CYRateListId' where id='$currencyId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	#Checking Rate List Id used
	function checkRateListUse($currencyRLId)
	{
		$qry = "select id from t_purchaseorder_main where currency_ratelist_id='$currencyRLId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	
	function getLatestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id from m_currency_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Get Rate List based on Date
	function getCYRateList($currencyId, $selDate)
	{	
		$qry	= "select mcrl.id, mc.code, mcrl.currency_value from m_currency_ratelist mcrl join m_currency mc on mcrl.currency_id=mc.id  where mcrl.currency_id='$currencyId' and date_format(mcrl.start_date,'%Y-%m-%d')<='$selDate' and  (date_format(mcrl.end_date,'%Y-%m-%d')>='$selDate' or (mcrl.end_date is null || mcrl.end_date=0)) order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?array($rec[0],$rec[1],$rec[2]):array();
	}

	function checkCurrencyInUse($currencyId)
	{
		$qry = "select id from m_currency_ratelist where currency_id='$currencyId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function deleteCurrency($currencyId)
	{
		$qry = " delete from m_currency where id='$currencyId'";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $result;
	}


	function updateUsdconfirm($carriageModeId)
	{
	$qry	= "update m_currency set active='1' where id=$carriageModeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateUsdReleaseconfirm($carriageModeId)
	{
		$qry	= "update m_currency set active='0' where id=$carriageModeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}


}
?>