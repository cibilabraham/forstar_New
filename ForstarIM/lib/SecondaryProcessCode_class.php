<?php
class SecondaryProcessCode
{  
	/****************************************************************
	This class deals with all the operations relating to Process Code Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function SecondaryProcessCode(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function getProcessCode($fishId)
	{
		$qry	= "SELECT id,code from m_processcode where fish_id='$fishId' and active='1'";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function getGrade($fishId,$processCodeId)
	{
		$qry	= "SELECT a.id,a.code from m_grade a left join m_processcode2grade b on b.grade_id=a.id  where b.processcode_id='$processCodeId' and a.active='1'";
		 //$qry	= "select * from t_rmprocurmentsupplier where rmProcurmentOrderId='$procurmentId' ";
		//echo $qry;
		//die;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}

	function addSecondaryProcessCode($name,$userId,$secondaryGrade)
	{
		$qry	= "insert into m_secondary_processcode(name,created_by,created_on,secondary_grade) values('$name','$userId',Now(),'$secondaryGrade')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function addSecondaryProcessCodeEntry($lastId,$fish,$processCode,$grade,$percentage)
		{
		$qry	= "insert into m_secondary_processcode_entry(secondary_id,fish_id,processcode_id,grade_id,percentage) values('$lastId','$fish', '$processCode','$grade','$percentage')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Process Code (paging)
	function fetchAllPagingRecords($offset, $limit)
	{		
		
		$limit = " $offset, $limit ";

		$qry = "select id,name,active,secondary_grade from m_secondary_processcode order by name";

		if ($limit)	$qry .= " limit ".$limit;
		//echo "Filter:<br>$qry<br>";	
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Process Code
	function fetchAllRecords()
	{
		
		$qry = "select id,name,active,secondary_grade from m_secondary_processcode order by name";
		//echo "All:<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSecondaryProcessEntry($secondaryId)
	{
		$qry = "select a.id,a.fish_id,a.processcode_id,a.grade_id,a.percentage,b.code,c.code from m_secondary_processcode_entry a left join m_processcode b on a.processcode_id=b.id left join m_grade c on  a.grade_id=c.id where secondary_id='$secondaryId' order by a.id ";
		//echo "All:<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function updateSecondaryconfirm($secondaryId)
	{
		$qry	= "update m_secondary_processcode set active='1' where id=$secondaryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}

	function updateSecondaryReleaseconfirm($secondaryId)
	{
		$qry	= "update m_secondary_processcode set active='0' where id=$secondaryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}

	# Get Record based on id 
	function find($secondaryId)
	{
		$qry	= "select id,name,secondary_grade from m_secondary_processcode where id='$secondaryId'";
		//echo $qry;		
		return $this->databaseConnect->getRecord($qry);
	}

	### update secondary process code
	function updateSecondaryProcessCode($secondaryId,$name,$secondaryGrade)
	{
		$qry	= "update m_secondary_processcode set name='$name',	secondary_grade	='$secondaryGrade' where id='$secondaryId'";
 		//echo $qry;
		//die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
	function updateSecondaryProcessCodeEntry($entryId,$secondaryId,$fish,$processCode,$grade,$percentage)
	{
		$qry	= "update m_secondary_processcode_entry set fish_id='$fish',processcode_id='$processCode',grade_id='$grade',percentage='$percentage' where id=$entryId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function delSecondaryProcessCodeEntryId($entryId)
	{
		$qry	= " delete from m_secondary_processcode_entry where id='$entryId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}


	function delSecondaryProcessCode($secondaryId)
	{
		$qry	= " delete from m_secondary_processcode where id='$secondaryId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function delSecondaryProcessCodeEntry($secondaryId)
	{
		$qry	= " delete from m_secondary_processcode_entry where secondary_id='$secondaryId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getSecondaryProcessCodeActive()
	{
		$qry = "select id,name from m_secondary_processcode where active='1' order by name";
		//echo "All:<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getSecondaryGrade()
	{
		$qry = "select id,code from m_grade where active='1' and include_secondary='Y' order by code";
		//echo "All:<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function findSecondaryGrade($secId)
	{
		$qry = "select code from m_grade where id='$secId'";
		//echo "All:<br>$qry<br>";		
		$result	=	$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
	
	#Using in PreProcessmaster.php
	function findSecondaryProcessCode($processCodeId)
	{
		$rec = $this->find($processCodeId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}



















































	
	/*#Getting Unique Records
	function fetchAllUniqueRecords($fishId,$Code)
	{
		$qry	=	"select * from m_processcode where fish_id='$fishId' and code='$Code'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Insert
	function addProcessCode($fishId, $Code, $Descr, $Weight, $gradeId, $arrivalOption, $copyFrom, $copyCode, $copyFishId, $copyCodeId, $gradeFrozenId, $frozenAvailable, $rawGradeUnit, $rawCountUnit, $frozenGradeUnit, $frozenCountUnit)
	{
 		if ($copyFrom) // Insert records into  table using copyFromId 
		{
			# Fetch all records from table using copyFromId ( FishId)
			$selRecord	=	$this->processRecordsFilter($copyFishId,$copyCodeId);
			
			if (sizeof($selRecord) > 0) {	
				$copyId				=	$selRecord[0];
				$basketWt			=	$selRecord[4];
				$rawGradeUnit		=	$selRecord[5];
				$frozenGradeUnit	=	$selRecord[6];
				$arrivalOption		=	$selRecord[7];
							
				$frozenAvailable	=	$selRecord[10];
				$rawCountUnit		=	$selRecord[11];
				$frozenCountUnit	=	$selRecord[12];
																	
				$qry	= "insert into m_processcode (fish_id, code, descr, b_weight, grade_unit_raw, grade_unit_frozen, available_option, frozen_available, count_unit_raw, count_unit_frozen) values('".$fishId."', '".$Code."', '".$Descr."', '".$basketWt."', '".$rawGradeUnit."', '".$frozenGradeUnit."', '".$arrivalOption."', '".$frozenAvailable."', '$rawCountUnit', '$frozenCountUnit')";
				//echo $qry;
				$insertStatus	=	$this->databaseConnect->insertRecord($qry);

				if ($insertStatus) {
					$this->databaseConnect->commit();
					$lastId = $this->databaseConnect->getLastInsertedId();
					if ($arrivalOption=='G' || $arrivalOption=='B' || $frozenAvailable=='G' || $frozenAvailable=='B') {
						$allGradeRecords = $this->fetchAllGradeRecords($copyId);
						while (list(,$v) = each($allGradeRecords)) {
							$gradeId	=	$v[4];
							$unitSelect	=	$v[5];
							if ($gradeId!="") { 
								$this->insertProcess2Grade($gradeId, $lastId, $unitSelect);
							}
						}
					} else {
						$this->databaseConnect->rollback();
					}
					return $insertStatus;
				} 
			}
		}
		else //No Copy from Option
		{
			$qry	= "insert into m_processcode (fish_id, code, descr, b_weight, grade_unit_raw, grade_unit_frozen, available_option, frozen_available, count_unit_raw, count_unit_frozen) values('".$fishId."', '".$Code."', '".$Descr."', '".$Weight."', '".$rawGradeUnit."', '".$frozenGradeUnit."', '".$arrivalOption."', '".$frozenAvailable."', '$rawCountUnit', '$frozenCountUnit')";
					//echo $qry;
											
					$insertStatus	=	$this->databaseConnect->insertRecord($qry);
				
					if ($insertStatus)
					{
						$this->databaseConnect->commit();
						
							$lastId = $this->databaseConnect->getLastInsertedId();
							if($arrivalOption=='G' || $arrivalOption=='B' ){
							
									$unitRawSelect		=	'R';
									$this->addProcess2Grade($gradeId,$lastId,$unitRawSelect);
							}
							if($frozenAvailable=='G' || $frozenAvailable=='B'){
								$unitFrozenSelect	=	'F';
								$this->addProcess2Grade($gradeFrozenId,$lastId,$unitFrozenSelect);									
							}
					}
					else
					{
						 $this->databaseConnect->rollback();
					}
					return $insertStatus;
			
			}
	}
  
 	#Add Process 2 Grade
	function addProcess2Grade($gradeId, $lastId, $unitSelect)
	{
 	 	if($gradeId){
			foreach ($gradeId as $gId){
				$grade	=	"$gId";
				if (!$this->chkPCGradeExist($lastId, $grade, $unitSelect)) {
					$qry	=	"insert into m_processcode2grade (processcode_id, grade_id, unit_select) values('".$lastId."', '".$grade."', '".$unitSelect."')";
					//echo $qry;
					$insertGrade	=	$this->databaseConnect->insertRecord($qry);
					if ($insertGrade) $this->databaseConnect->commit();
					else $this->databaseConnect->rollback();
				}
			}
		} 
 	}

	function chkPCGradeExist($processCodeId, $gradeId, $unitSelect)
	{
		$qry = " select id from m_processcode2grade where processcode_id='$processCodeId' and unit_select='$unitSelect' and grade_id='$gradeId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}
	
	#Grade selection for Raw Grades
 	function fetchGradeRecords($codeId)
	{
 		$qry	=	"select a.id,a.processcode_id,a.grade_id,b.id,b.code,b.min,b.max from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$codeId' and a.unit_select='r' order by b.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
 	}
 
 
 	#Grade selection for Frozen Grades
 	function fetchFrozenGradeRecords($codeId)
	{
 		$qry	=	"select a.id,a.processcode_id,a.grade_id,b.id,b.code,b.min,b.max from m_processcode2grade a, m_grade b where a.grade_id = b.id and a.processcode_id='$codeId' and a.unit_select='f' order by b.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
 
		
	# Returns all Process Code (paging)
	function fetchPagingRecords($offset, $limit)
	{
		$qry	=	"select a.id, a.fish_id, a.code, a.descr, a.b_weight, a.grade_unit_raw, a.count_unit_frozen, a.available_option, b.id, b.name,a.frozen_available, a.grade_unit_frozen, a.count_unit_raw from m_processcode a left join m_fish b on a.fish_id = b.id order by b.name asc, a.code asc limit $offset, $limit";
				
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Filter m_processcode table using fish id
	function processCodeRecFilter($filterId)
	{
		$qry	= "select a.id, a.fish_id, a.code, a.descr, a.b_weight,a.grade_unit_raw,a.count_unit_frozen,a.available_option,b.id, b.name,a.frozen_available, a.grade_unit_frozen, a.count_unit_raw from m_processcode a, m_fish b where a.fish_id = b.id and b.id='$filterId' and a.active=1 order by b.name asc, a.code asc";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Filter m_processcode table using fish id(PAGING)	
	function processCodeRecPagingFilter($filterId, $offset, $limit)
	{
		$qry	=	"select a.id, a.fish_id, a.code, a.descr, a.b_weight, a.grade_unit_raw, a.count_unit_frozen, a.available_option, b.id, b.name, a.frozen_available, a.grade_unit_frozen, a.count_unit_raw from m_processcode a, m_fish b where a.fish_id = b.id and b.id=$filterId order by b.name asc, a.code asc limit $offset, $limit";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	// For getting Grade List based on Fish ID -- It is used in Daily Rates  Form
	function processCodeGradeRecFilter($filterId)
	{
		$qry	=	"select a.id, a.fish_id, a.code, a.descr, a.b_weight, a.unit_raw, a.unit_frozen, b.id, b.name, c.id, c.processcode_id, c.grade_id, d.id, d.code from m_processcode a, m_fish b,m_processcode2grade c, m_grade d where a.fish_id = b.id and a.id=c.processcode_id and d.id=c.grade_id and b.id='$filterId' ";			
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
		
	# Filter m_processcode table using Process Code id
	
	function processCodeRecIdFilter($recId)
	{
		$qry	=	"select id,fish_id, code, descr, b_weight from m_processcode where id=$recId";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#fOR SELECTING THE SELECTED RAW MATERIAL CODE
	function fetchGradeSelectedRecords($editId)
	{
		$qry 	=	"select a.id,a.code,b.id,b.processcode_id,b.grade_id,b.unit_select from m_grade a left join m_processcode2grade b on a.id=b.grade_id and b.processcode_id='$editId' and b.unit_select='r' order by b.id desc, a.code asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#fOR SELECTING THE SELECTED frozen MATERIAL CODE
	function fetchFrozenGradeSelectedRecords($editId)
	{
		$qry 	=	"select a.id,a.code,b.id,b.processcode_id,b.grade_id,b.unit_select from m_grade a left join m_processcode2grade b on a.id=b.grade_id and b.processcode_id='$editId' and b.unit_select='f' order by b.id desc, a.code asc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#For Selecting ALL GRADE From Process2grade table
	function fetchAllGradeRecords($editId)
	{
		$qry 	=	"select a.id,a.code,b.id,b.processcode_id,b.grade_id,b.unit_select from m_grade a left join m_processcode2grade b on a.id=b.grade_id and b.processcode_id='$editId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	#Using in PreProcessmaster.php
	function findProcessCode($processCodeId)
	{
		$rec = $this->find($processCodeId);
		return sizeof($rec) > 0 ? $rec[2] : "";
	}

	# Delete a Process Code Record
	function deleteProcessCode($processCodeId)
	{
		$qry	=	" delete from m_processcode where id=$processCodeId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update a Process Code

	function updateProcessCode($processCodeId, $fishId, $Code, $Descr, $Weight, $gradeId, $arrivalOption, $gradeFrozenId, $frozenAvailable, $rawGradeUnit, $rawCountUnit, $frozenGradeUnit, $frozenCountUnit)
	{
		$qry	=	" update m_processcode set fish_id='$fishId', code='$Code', descr='$Descr', b_weight='$Weight', available_option='$arrivalOption', frozen_available='$frozenAvailable', grade_unit_raw='$rawGradeUnit', grade_unit_frozen='$frozenGradeUnit', count_unit_raw='$rawCountUnit', count_unit_frozen='$frozenCountUnit' where id=$processCodeId";
		
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {			
			$this->deleteProcessCode2Grade($processCodeId);
			if ($arrivalOption=='G' || $arrivalOption=='B' ) {
					$unitRawSelect		=	'R';
					$this->addProcess2Grade($gradeId, $processCodeId, $unitRawSelect);
			}
			if ($frozenAvailable=='G' || $frozenAvailable=='B' ) {
					$unitFrozenSelect	=	'F';
					$this->addProcess2Grade($gradeFrozenId, $processCodeId, $unitFrozenSelect);						
			}
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}

	#Delete the exisiting Process Code at the time of Update
	function deleteProcessCode2Grade($processCodeId)
	{	
		# Get PC Grade Recs
		$getPC2GradeRecs = $this->getPCWiseGradeRecs($processCodeId);

		foreach ($getPC2GradeRecs as $pcg) {
			$gradeEntryId = $pcg[0];
			$pcGradeId    = $pcg[1];
			
			if (!$this->pcGradeRecInUse($processCodeId, $pcGradeId)) {
				$delPCGradeEntry = $this->delPCWiseGradeEntry($gradeEntryId);
			}
		}
		
	}

	function delPCWiseGradeEntry($gradeEntryId)
	{
		$qry	= " delete from m_processcode2grade where id='$gradeEntryId'";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function getPCWiseGradeRecs($processCodeId)
	{
		$qry	=	"select a.id, a.grade_id from m_processcode2grade a where a.processcode_id='$processCodeId'";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Process code COPY FROM 
	function processRecordsFilter($copyFishId, $copyCodeId)
	{
		$qry = "select a.id, a.fish_id, a.code, a.descr, a.b_weight, a.grade_unit_raw,a.grade_unit_frozen,a.available_option,b.id, b.name,a.frozen_available , a.count_unit_raw,a.count_unit_frozen from m_processcode a, m_fish b where a.fish_id = b.id and  a.fish_id=$copyFishId and a.code='$copyCodeId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

 	#Insert Process 2 Grade For COPY FROM
 	function insertProcess2Grade($gradeId, $lastId, $unitSelect)
	{	 
		$qry	=	"insert into m_processcode2grade (processcode_id, grade_id, unit_select) values('".$lastId."','".$gradeId."','".$unitSelect."')";
		//echo $qry;
		$insertGrade	=	$this->databaseConnect->insertRecord($qry);
		if ($insertGrade) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
	 }

	# Checking Fish Id is in use
	function processCodeRecInUse($processCodeId)
	{		
		//select a.id as id from m_process a where processes like '%$processCodeId%'		
		$qry = " select id from (
				select a.id as id from m_process a where (processes like '$processCodeId' or processes like '$processCodeId,%' or processes like '%,$processCodeId,%' or processes like '%,$processCodeId')
			union
				select a1.id as id from t_dailycatchentry a1 where a1.fish_code='$processCodeId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Filter m_processcode table using fish id (Used for xajax function call)
	function getProcessCodeRecs($fishId)
	{
		$qry	= "select a.id, a.code from m_processcode a, m_fish b where a.fish_id = b.id and b.id='$fishId' and a.active=1 order by b.name asc, a.code asc";
	
		$result	=	$this->databaseConnect->getRecords($qry);
		
		$resultArr = array(''=>'-- Select --');
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}	

	

	function getSelGradeRecs($editId)
	{
		//$qry 	= "select a.id,a.code, a.max, a.min from m_grade a left join m_processcode2grade b on a.id=b.grade_id where b.processcode_id='$editId' and b.unit_select='r' order by b.id desc, a.code asc";
		$qry 	= "select a.id, a.code, a.max, a.min from m_grade a left join m_processcode2grade b on a.id=b.grade_id where b.processcode_id='$editId' and b.unit_select='r' order by a.code asc";
		//echo $qry;		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Checking Grade is using in (Daily catch entry/Daily rates/ Daily frozen packing/Quick entry list)
	function pcGradeRecInUse($processCodeId, $gradeId)
	{			
		$qry = " select id from (
				select a.id as id from t_dailycatchentry a where a.fish_code='$processCodeId' and a.grade_id='$gradeId'
			union
				select td.id as id from t_dailyrates td, t_dailyrates_entry tde where td.id=tde.main_id and  td.processcode_id='$processCodeId' and tde.grade_id='$gradeId'	
			union
				select tdfp.id as id from t_dailyfrozenpacking_entry tdfp, t_dailyfrozenpacking_grade tdfpg where tdfp.id=tdfpg.entry_id and  tdfp.processcode_id='$processCodeId' and tdfpg.grade_id='$gradeId'
				
			) as X group by id ";
		//union	select qel.id as id from t_fznpakng_qel_grade qel where qel.grade_id='$gradeId'
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
	
	function chkMoreGradeEntryExist($processCodeId)
	{
		$qry = " select id from m_processcode2grade where processcode_id='$processCodeId'";
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getSelFrozenGradeRecs($editId)
	{
		//b.id desc,
		$qry 	= "select a.id,a.code, a.max, a.min from m_grade a left join m_processcode2grade b on a.id=b.grade_id where b.processcode_id='$editId' and b.unit_select='f' order by a.code asc";		
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Grade Recs
	function selGradeRecs($selGradeId)
	{
		$qry 	= "select a.id, a.code, a.max, a.min from m_grade a where a.id in ($selGradeId)";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function updateProcessCodeconfirm($processCodeId)
	{
	$qry	= "update m_processcode set active='1' where id=$processCodeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateProcessCodeReleaseconfirm($processCodeId)
	{
		$qry	= "update m_processcode set active='0' where id=$processCodeId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
*/
}