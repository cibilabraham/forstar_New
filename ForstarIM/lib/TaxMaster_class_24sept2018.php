<?php
class TaxMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Tax Master
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function TaxMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}




#--------------------------------------------------------------------------------------------------------
		# Add
	function addTaxMasterRec($baseCst, $cstActive, $cstRateListId)
	{	
		$qry	= "insert into m_tax (base_cst, active, rate_list_id) values('$baseCst', '$cstActive', '$cstRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Get Record
	function find($rateListId=null)
	{
		if ($rateListId) $whr = "rate_list_id='$rateListId'";

		$qry	= " select id, base_cst, active from m_tax";
		if ($whr) $qry .= " where ".$whr;

		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateTaxMasterRec($taxRecId, $baseCst, $cstActive, $cstRateListId)
	{
		$qry	= " update m_tax set base_cst='$baseCst', active='$cstActive', rate_list_id='$cstRateListId' where id=$taxRecId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Get Base CST rate list
	function getBaseCst($startDate=null , $taxType)
	{
		//$qry = " select base_cst from m_tax where active='Y'  "; 

		if ($startDate) {			
			$qry = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and tax_type='$taxType' and date_format(mtrl.start_date,'%Y-%m-%d')<='$startDate' and (date_format(mtrl.end_date,'%Y-%m-%d')>='$startDate' or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		} else {
			$qry = "select mt.base_cst from m_tax mt join m_tax_ratelist mtrl on mtrl.id=mt.rate_list_id where mt.active='Y' and tax_type='$taxType' and date_format(mtrl.start_date,'%Y-%m-%d')<=NOW() and (date_format(mtrl.end_date,'%Y-%m-%d')>=NOW() or (mtrl.end_date is null || mtrl.end_date=0)) order by mtrl.start_date desc";
		}
		//echo "<br>$qry<br>";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	# -------------------------------------RATE LIST SECTION STARTS HERE --------------------------
	# Latest rate list	
	function latestRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id from m_tax_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function fetchAllCSTRateListRecs()
	{
		$qry	= "select mcrl.id, mcrl.name, mcrl.start_date from m_tax_ratelist mcrl order by mcrl.start_date desc";

		return $this->databaseConnect->getRecords($qry);
	}

	function addTaxRateList($rateListName, $startDate, $userId)
	{
		$qry	= "insert into m_tax_ratelist (name, start_date, created, created_by) values('$rateListName', '$startDate', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# update Dist Rate List Rec
	function updateTaxRateListRec($latestRateListId, $startDate=null)
	{
		if ($startDate) {
			$sDate		= explode("-",$startDate);
			$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		} else $endDate="0000-00-00";

		$qry = " update m_tax_ratelist set end_date='$endDate' where id='$latestRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkValidRateListDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_tax_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# tax Rate List rec
	function taxRLRec($rateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_tax_ratelist where id='$rateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function chkRateListInUse($srlStartDate)
	{
		$distMgnRLRecs = $this->getDistMgnRateListRecs($srlStartDate);
		if (sizeof($distMgnRLRecs)>0) return true;
		else return false;		
	}

	function getDistMgnRateListRecs($srlStartDate)
	{
		$qry = "select start_date from m_distmargin_ratelist where date_format(start_date,'%Y-%m-%d')>='$srlStartDate'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function delRateListRec($selRateList)
	{
		# Delete CST Master
		$delTaxMasterRec = $this->deleteCSTTaxMasterRec($selRateList);

		if ($delTaxMasterRec) {

			$qry = " delete from m_tax_ratelist where id='$selRateList'";
	
			$result	= $this->databaseConnect->delRecord($qry);
			if ($result) {
				$this->databaseConnect->commit();
				$latestRateListId = $this->latestRateList();
				if ($latestRateListId!="") {
					# Update Prev Rate List Date
					//$sDate = "0000-00-00";
					$this->updateTaxRateListRec($latestRateListId, $sDate=null);
				}
			}
			else $this->databaseConnect->rollback();
			
			return $result;
		} else return false;
	}

	function deleteCSTTaxMasterRec($rateListId)
	{
		$qry = " delete from m_tax  where rate_list_id='$rateListId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateRateListRec($cstRateListId, $startDate)
	{
		$qry = " update m_tax_ratelist set start_date='$startDate' where id='$cstRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
#---------------------------- RATE LIST SECTION ENDS HERE-------------------------

	#-----------------------------EXCISE DUTY SECTION STARTS HERE--------------------------------
	#Add
	function addExcTaxMasterRec($excBaseCst, $excCstActive, $excCstRateListId)
	{	
		$qry	= "insert into m_excise_duty (excise_base_cst,active, excise_rate_list_id) values('$excBaseCst', '$excCstActive', '$excCstRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# Get Record
	function findExc($excRateListId=null)
	{
		if ($excRateListId) $whr = "excise_rate_list_id='$excRateListId'";

		$qry	= " select id,excise_base_cst, active from m_excise_duty";
		if ($whr) $qry .= " where ".$whr;

		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateExcTaxMasterRec($excTaxRecId, $excBaseCst, $excCstActive, $excCstRateListId)
	{
		$qry	= " update m_excise_duty set excise_base_cst='$excBaseCst', active='$excCstActive', excise_rate_list_id='$excCstRateListId' where id=$excTaxRecId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	#-----------------------------EXCISE DUTY SECTION ENDS HERE---------------------


	# -------------------------------------BASIC EXCISE RATE LIST SECTION STARTS HERE --------------------------
	# Latest rate list	
	function latestExcRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id from m_excise_duty_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function fetchAllExciseCSTRateListRecs()
	{
		$qry	= "select mcrl.id, mcrl.name, mcrl.start_date from m_excise_duty_ratelist mcrl order by mcrl.start_date desc";

		return $this->databaseConnect->getRecords($qry);
	}

	function addExcTaxRateList($excRateListName, $excStartDate, $userId)
	{
		$qry	= "insert into m_excise_duty_ratelist (name, start_date, created, created_by) values('$excRateListName', '$excStartDate', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# update Dist Rate List Rec
	function updateExcTaxRateListRec($latestExcRateListId, $excStartDate=null)
	{
		//echo ("here");
		if ($excStartDate) {
			$excSDate		= explode("-",$excStartDate);
			$excEndDate  	= date("Y-m-d",mktime(0, 0, 0,$excSDate[1],$excSDate[2]-1,$excSDate[0])); //End Date
		} else $excEndDate="0000-00-00";

		$qry = " update m_excise_duty_ratelist set end_date='$excEndDate' where id='$latestExcRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkValidExcRateListDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_excise_duty_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# tax Rate List rec
	function excTaxRLRec($excRateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_excise_duty_ratelist where id='$excRateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function delExcRateListRec($selRateList)
	{
		# Delete CST Master
		$delTaxMasterRec = $this->deleteCSTTaxMasterRec($selRateList);

		if ($delTaxMasterRec) {

			$qry = " delete from m_excise_duty_ratelist where id='$selRateList'";
	
			$result	= $this->databaseConnect->delRecord($qry);
			if ($result) {
				$this->databaseConnect->commit();
				$latestRateListId = $this->latestRateList();
				if ($latestRateListId!="") {
					# Update Prev Rate List Date
					//$sDate = "0000-00-00";
					$this->updateTaxRateListRec($latestRateListId, $sDate=null);
				}
			}
			else $this->databaseConnect->rollback();
			
			return $result;
		} else return false;
	}

	function deleteExcCSTTaxMasterRec($excRateListId)
	{
		$qry = " delete from m_excise_duty  where excise_rate_list_id='$excRateListId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateExcRateListRec($excCstRateListId, $excStartDate)
	{
		$qry = " update m_excise_duty_ratelist set start_date='$excStartDate' where id='$excCstRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# ------------------------------------- BASIC EXCISE RATE LIST SECTION ENDS HERE --------------------------

	#-----------------------------EDU CESS SECTION STARTS HERE--------------------------------
	#Add
	function addECessRec($eCess, $eCessActive, $eCessRateListId)
	{	
		$qry	= "insert into m_edu_cess (base_cst,active,rate_list_id) values('$eCess', '$eCessActive', '$eCessRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# Get Record
	function findECess($eRateListId=null)
	{
		if ($eRateListId) $whr = "rate_list_id='$eRateListId'";

		$qry	= " select id,base_cst, active from m_edu_cess";
		if ($whr) $qry .= " where ".$whr;

		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateECessRec($eCessRecId, $eCess, $eCessActive, $eCessRateListId)
	{
		$qry	= " update m_edu_cess set base_cst='$eCess', active='$eCessActive', rate_list_id='$eCessRateListId' where id=$eCessRecId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	#-----------------------------EDU CESS SECTION ENDS HERE---------------------


	# -------------------------------------EDU CESS RATE LIST SECTION STARTS HERE --------------------------
	# Latest rate list	
	function latestECessRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id from m_edu_cess_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function fetchAllECessRateListRecs()
	{
		$qry	= "select mcrl.id, mcrl.name, mcrl.start_date from m_edu_cess_ratelist mcrl order by mcrl.start_date desc";

		return $this->databaseConnect->getRecords($qry);
	}

	function addECessRateList($eCessRateListName, $eCessStartDate, $userId)
	{
		$qry	= "insert into m_edu_cess_ratelist (name, start_date, created, created_by) values('$eCessRateListName', '$eCessStartDate', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# update Dist Rate List Rec
	function updateECessEdRateListRec($latestECessRateListId, $eCessStartDate=null)
	{
		//echo ("here");
		if ($eCessStartDate) {
			$eCessSDate		= explode("-",$eCessStartDate);
			$eCessEndDate  	= date("Y-m-d",mktime(0, 0, 0,$eCessSDate[1],$eCessSDate[2]-1,$eCessSDate[0])); //End Date
		} else $eCessEndDate="0000-00-00";

		$qry = " update m_edu_cess_ratelist set end_date='$eCessEndDate' where id='$latestECessRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkValidECessRateListDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_edu_cess_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# tax Rate List rec
	function eCessRLRec($eRateListId)
	{
		$qry	= " select id, name, start_date, end_date from m_edu_cess_ratelist where id='$eRateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function delECessRateListRec($selRateList)
	{
		# Delete Edu cess
		$delTaxMasterRec = $this->deleteECessRec($selRateList);

		if ($delTaxMasterRec) {

			$qry = " delete from m_edu_cess_ratelist where id='$selRateList'";
	
			$result	= $this->databaseConnect->delRecord($qry);
			if ($result) {
				$this->databaseConnect->commit();
				$latestRateListId = $this->latestECessRateList();
				if ($latestRateListId!="") {
					# Update Prev Rate List Date
					//$sDate = "0000-00-00";
					$this->updateECessEdRateListRec($latestRateListId, "");
				}
			}
			else $this->databaseConnect->rollback();
			
			return $result;
		} else return false;
	}

	function deleteECessRec($eRateListId)
	{
		$qry = " delete from m_edu_cess where rate_list_id='$eRateListId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateECessSdRateListRec($eCessRateListId, $eCessStartDate)
	{
		$qry = " update m_edu_cess_ratelist set start_date='$eCessStartDate' where id='$eCessRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# ------------------------------------- EDU CESS RATE LIST SECTION ENDS HERE --------------------------

	#-----------------------------SECONDARY EDU CESS SECTION STARTS HERE--------------------------------
	#Add
	function addSecECessRec($secECess, $secECessActive, $secECessRateListId)
	{	
		$qry	= "insert into m_sec_edu_cess (base_cst,active,rate_list_id) values('$secECess', '$secECessActive', '$secECessRateListId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	# Get Record
	function findSecECess($eSecRateListId=null)
	{
		if ($eSecRateListId) $whr = "rate_list_id='$eSecRateListId'";

		$qry	= " select id,base_cst, active from m_sec_edu_cess";
		if ($whr) $qry .= " where ".$whr;

		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateSecECessRec($secECessRecId, $secECess, $secECessActive, $secECessRateListId)
	{
		$qry	= " update m_sec_edu_cess set base_cst='$secECess', active='$secECessActive', rate_list_id='$secECessRateListId' where id=$secECessRecId";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	#-----------------------------SECONDARY EDU CESS SECTION ENDS HERE---------------------


	# -------------------------------------SECONDARY EDU CESS RATE LIST SECTION STARTS HERE --------------------------
	# Latest rate list	
	function latestSecECessRateList()
	{
		$cDate = date("Y-m-d");
	
		$qry	= "select mcrl.id from m_sec_edu_cess_ratelist mcrl where '$cDate'>=date_format(mcrl.start_date,'%Y--%m-%d') order by mcrl.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	function fetchAllSecECessRateListRecs()
	{
		$qry	= "select mcrl.id, mcrl.name, mcrl.start_date from m_sec_edu_cess_ratelist mcrl order by mcrl.start_date desc";

		return $this->databaseConnect->getRecords($qry);
	}

	function addSecECessRateList($secECessRateListName, $secECessStartDate, $userId)
	{
		$qry	= "insert into m_sec_edu_cess_ratelist (name, start_date, created, created_by) values('$secECessRateListName', '$secECessStartDate', NOW(), '$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	# update Dist Rate List Rec
	function updateSecEdRateListRec($latestSecECessRateListId, $secECessStartDate=null)
	{
		//echo ("here");
		if ($secECessStartDate) {
			$secECessSDate		= explode("-",$secECessStartDate);
			$secECessEndDate  	= date("Y-m-d",mktime(0, 0, 0,$secECessSDate[1],$secECessSDate[2]-1,$secECessSDate[0])); //End Date
		} else $secECessEndDate="0000-00-00";

		$qry = " update m_sec_edu_cess_ratelist set end_date='$secECessEndDate' where id='$latestSecECessRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function chkValidSecECessRateListDate($seldate, $cId=null)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		
		$qry	= "select a.id, a.name, a.start_date from m_sec_edu_cess_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry order by a.start_date desc";

		//echo "<br>$qry<br>";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	# tax Rate List rec
	function secECessRLRec($secECessRateListId)
	{
		$qry	= "select id, name, start_date, end_date from m_sec_edu_cess_ratelist where id='$secECessRateListId'";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function delSecECessRateListRec($selRateList)
	{
		# Delete CST Master
		$delTaxMasterRec = $this->deleteSecECessRec($selRateList);

		if ($delTaxMasterRec) {

			$qry = " delete from m_sec_edu_cess_ratelist where id='$selRateList'";
	
			$result	= $this->databaseConnect->delRecord($qry);
			if ($result) {
				$this->databaseConnect->commit();
				$latestRateListId = $this->latestSecECessRateList();
				if ($latestRateListId!="") {
					# Update Prev Rate List Date
					//$sDate = "0000-00-00";
					$this->updateSecEdRateListRec($latestRateListId, "");
				}
			}
			else $this->databaseConnect->rollback();
			
			return $result;
		} else return false;
	}

	function deleteSecECessRec($secECessRateListId)
	{
		$qry = " delete from m_sec_edu_cess  where rate_list_id='$secECessRateListId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updateSecSdRateListRec($secECessRateListId, $secECessStartDate)
	{
		$qry = " update m_sec_edu_cess_ratelist set start_date='$secECessStartDate' where id='$secECessRateListId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	# ------------------------------------- SECONDARY EDU CESS RATE LIST SECTION ENDS HERE --------------------------

}
?>