<?php
class PreProcessor
{
	/****************************************************************
	This class deals with all the operations relating to Pre-Processor 
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function PreProcessor(&$databaseConnect)
	{
		 $this->databaseConnect =&$databaseConnect;
	}

	function addPreProcessor($Code, $Name, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo , $selPlant, $selActivity, $processorStatus)
	{
		$qry	= "insert into m_preprocessor (code, name, address, place, pin, telno, faxno, email, lstno, cstno, panno, active) values('".$Code."','".$Name."','".$Address."','".$Place."','".$Pincode."','".$TelNo."','".$FaxNo."','".$Email."','".$LstNo."','".$CstNo."','".$PanNo."', '$processorStatus')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			#Getting Last Id
			$lastId = $this->databaseConnect->getLastInsertedId();
			#Insert Selected Plants
			$this->addProcessor2Plant($lastId, $selPlant);
			#Insert Selected Activities
			$this->addProcessor2Activity($lastId, $selActivity);
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Pre-Processors
	function fetchAllRecords($confirm)
	{
		//if ($confirm){
		//$qry	=	"select id, name, code, active,activeconfirm from m_preprocessor order by name asc";
		//} else {
		$qry	=	"select id, name, code, active,activeconfirm from m_preprocessor  order by name asc";
		//}
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Pre-Processors(Paging)
	function fetchPagingRecords($offset, $limit,$confirm)
	{
		//if ($confirm){
		//$qry	= "select id, name, code, active,activeconfirm from m_preprocessor order by name asc limit $offset, $limit";
		//}
		//else {
		$qry	= "select id, name, code, active,activeconfirm,((select COUNT(a.id) from t_dailypreprocess_processor_qty a where a.preprocessor_id=mp.id)+(select COUNT(a1.id) from t_dailyfrozenpacking_main a1 where a1.processor_id=mp.id)) as tot from m_preprocessor mp order by name asc limit $offset, $limit";
		//}
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Pre-Processor based on id 
	function find($processorId)
	{
		$qry	= "select id, code, name, address, place, pin, telno, faxno, email, lstno, cstno, panno, active from m_preprocessor where id=$processorId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Pre-Processor
	function deleteProcessor($processorId)
	{
		$qry	= " delete from m_preprocessor where id=$processorId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Pre-Processor
	function updateProcessor($processorId, $Code, $Name, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo, $selPlant, $selActivity, $processorStatus)
	{
		$qry	=	" update m_preprocessor set code='$Code' , name='$Name', address='$Address', place='$Place', pin='$Pincode', telno='$TelNo', faxno='$FaxNo', email='$Email', lstno='$LstNo', cstno='$CstNo', panno ='$PanNo', active='$processorStatus' where id=$processorId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			#Delete all Entries regarding the Processor Id
			$this->deleteProcessor2Plant($processorId);
			$this->deleteProcessor2Activity($processorId);

			#Insert Selected Plants
			$this->addProcessor2Plant($processorId, $selPlant);
			#Insert Selected Activities
			$this->addProcessor2Activity($processorId, $selActivity);

			$this->databaseConnect->commit();
			
		} 
		else $this->databaseConnect->rollback();
		return $result;	
	}

	#Find Pre Processor name
	function findPreProcessor($preProcessorId)
	{
		$rec = $this->find($preProcessorId);
		return sizeof($rec)>0?$rec[2]:"";
	}




function updatepreProcessorconfirm($processorId)
	{
	$qry	= "update m_preprocessor set activeconfirm='1' where id=$processorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updatepreProcessorReleaseconfirm($processorId)
	{

	$qry	= "update m_preprocessor set activeconfirm='0' where id=$processorId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#Filter all Pre-Processing Records where preprocessing Activity=Yes
	#(Using in other PreProcess Screen) 
	function fetchAllPreProcessingRecords($currentUrl, $unitId=null)
	{
		if ($unitId!="") {
			$whrConn =  " and a.id=e.processor_id and e.plant_id='$unitId' ";
			$tableConn = " , m_preprocessor2plant e";
		}

		$whr = "a.id=b.processor_id and b.activity_id=c.activity_id and c.submodule_id=d.pmenu_id and d.url='$currentUrl' $whrConn";

		$orderBy = " a.name asc ";

		$qry = "select distinct a.id, a.name, a.code from m_preprocessor a, m_preprocessor2activity b, m_activity2submodule c, function d $tableConn ";

		if ($whr!="") $qry .= " where ". $whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Active Processor Recs
	function getActiveProcessorRecs($currentUrl, $unitId=null)
	{
		if ($unitId!="") {
			$whrConn =  " and a.id=e.processor_id and e.plant_id='$unitId' ";
			$tableConn = " , m_preprocessor2plant e";
		}

		$whr = "a.id=b.processor_id and b.activity_id=c.activity_id and c.submodule_id=d.pmenu_id and a.active='Y' and a.activeconfirm=1 and d.url='$currentUrl' $whrConn";

		$orderBy = " a.name asc ";

		$qry = "select distinct a.id, a.name,a.active from m_preprocessor a, m_preprocessor2activity b, m_activity2submodule c, function d $tableConn ";

		if ($whr!="") $qry .= " where ". $whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add Processor 2 Plant
  	function addProcessor2Plant($lastId, $selPlant)
	{
 	 	if ($selPlant) {
			foreach ($selPlant as $pId) {
				$plantId =	"$pId";
				$qry	=	"insert into m_preprocessor2plant (processor_id, plant_id) values('".$lastId."','".$plantId."')";
				//echo $qry;
				$insertPlant	=	$this->databaseConnect->insertRecord($qry);
				if ($insertPlant) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		} 
 	}

	#Add Processor 2 Activity
  	function addProcessor2Activity($lastId, $selActivity)
	{
 	 	if ($selActivity) {
			foreach ($selActivity as $aId) {
				$activityId =	"$aId";
				$qry	=	"insert into m_preprocessor2activity (processor_id, activity_id) values('".$lastId."','".$activityId."')";
				//echo $qry;
				$insertActivity	=	$this->databaseConnect->insertRecord($qry);
				if ($insertActivity) $this->databaseConnect->commit();
				else $this->databaseConnect->rollback();
			}
		} 
 	}

	#In Edit mode fetch plant records
	function  fetchSelectedPlantRecords($editId)
	{
		$qry 	=	"select a.id, a.no, a.name, b.id, b.processor_id, b.plant_id from m_plant a left join m_preprocessor2plant b on a.id=b.plant_id and b.processor_id='$editId' where a.active=1 order by b.id desc, a.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#In Edit mode fetch Activity records
	function fetchSelectedActivityRecords($editId)
	{
		$qry 	=	"select a.id, a.name, b.id, b.processor_id, b.activity_id from m_processingactivities a left join m_preprocessor2activity b on a.id=b.activity_id and b.processor_id='$editId' where a.active=1 order by b.id desc, a.name asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete a Processor's Plant
	function deleteProcessor2Plant($processorId)
	{
		$qry	=	" delete from m_preprocessor2plant where processor_id=$processorId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete a Processor's Activity
	function deleteProcessor2Activity($processorId)
	{
		$qry	=	" delete from m_preprocessor2activity where processor_id=$processorId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}


	#Fetch plant Records From m_preprocesor2plant table based on ProcessorId
 	function fetchPlantRecords($processorId)
	{
 		$qry 	=	"select a.id, a.processor_id, a.plant_id, b.id, b.no, b.name  from m_preprocessor2plant a, m_plant b where b.id=a.plant_id and a.processor_id='$processorId' order by b.name asc";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
 	}

	#Fetch Activity Records From m_preprocesor2activity table based on ProcessorId
 	function fetchActivityRecords($processorId)
	{
 		$qry 	=	"select a.id, a.processor_id, a.activity_id, b.id, b.name from  m_preprocessor2activity a, m_processingactivities b where b.id=a.activity_id and a.processor_id='$processorId' order by b.name asc";
		//echo $qry."<br>";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
 	}

	# -----------------------------------------------------
	# Checking Pre-Processor Id is in use ( Daily Pre Process);
	# -----------------------------------------------------
	function preProcessorRecInUse($processorId)
	{				
		//$qry = "select a.id as id from t_dailypreprocess_processor_qty a where a.preprocessor_id='$processorId'";		
		$qry = "select id from (
				select a.id as id from t_dailypreprocess_processor_qty a where a.preprocessor_id='$processorId'
			union
				select a1.id as id from t_dailyfrozenpacking_main a1 where a1.processor_id='$processorId'			
			) as X group by id 
			";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	# Update Processor Status
	function updateProcessorStatus($processorId, $processorStatus)
	{
		$qry	=	" update m_preprocessor set active='$processorStatus' where id=$processorId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	function getProcessorCurrentStatus($processorId)
	{
		$rec = $this->find($processorId);
		return $rec[12];
	}
	# Processor Status Ends here

	# Get Active Processor Recs using in daily preprocess
	function getActiveProcessorRecsForDailyPreProcess($currentUrl, $unitId=null)
	{
		if ($unitId!="") {
			$whrConn =  " and a.id=e.processor_id and e.plant_id='$unitId' ";
			$tableConn = " , m_preprocessor2plant e";
		}

		$whr = "a.id=b.processor_id and b.activity_id=c.activity_id and c.submodule_id=d.pmenu_id and a.active='Y' and a.activeconfirm=1 and d.url='$currentUrl' $whrConn";

		$orderBy = " a.name asc ";

		$qry = "select distinct a.id, a.name from m_preprocessor a, m_preprocessor2activity b, m_activity2submodule c, function d $tableConn ";

		if ($whr!="") $qry .= " where ". $whr;
		if ($orderBy!="") $qry .= " order by".$orderBy;
		//echo $qry;
		
$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	
}
?>