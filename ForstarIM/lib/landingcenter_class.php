<?php
class LandingCenter
{  
	/****************************************************************
	This class deals with all the operations relating to Landing Center 
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function LandingCenter(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addLandingCenter($landingCenterCode, $landingCenterName, $landingCenterDesc, $distance)
	{
		$qry	= "insert into m_landingcenter (code, name, descr, distance) values('".$landingCenterCode."', '".$landingCenterName."', '".$landingCenterDesc."', '$distance')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Landing centers	
	function fetchAllRecords()
	{
		
		$qry	=	"select id, name, code, descr, distance,active,((select COUNT(a.id) from m_supplier2center a where a.center_id = ml.id)+(select COUNT(a1.id) from m_process_yield_months a1 where a1.center_id=ml.id)+(select count(a2.id) from t_dailypreprocess_entries a2 where a2.center_id=ml.id)+
				(select count(a3.id) from m_subsupplier a3 where a3.place=ml.id)+(select count(a4.id) from t_dailycatch_main a4 where a4.landing_center=ml.id)) as tot from m_landingcenter ml order by name asc";
		
	//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsActiveLanding()
	{
		
		$qry	=	"select id, name, code, descr, distance,active from m_landingcenter where active=1 order by name asc";
		
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Paging Landing centers	
	function fetchPagingRecords($offset, $limit)
	{
		
		$qry	=	"select id, name, code, descr, distance,active,((select COUNT(a.id) from m_supplier2center a where a.center_id = ml.id)+(select COUNT(a1.id) from m_process_yield_months a1 where a1.center_id=ml.id)+(select count(a2.id) from t_dailypreprocess_entries a2 where a2.center_id=ml.id)+
				(select count(a3.id) from m_subsupplier a3 where a3.place=ml.id)+(select count(a4.id) from t_dailycatch_main a4 where a4.landing_center=ml.id)) as tot from m_landingcenter ml order by name asc limit $offset,$limit";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Landing centers based on Id	
	function filterRecord($landingCenterId)
	{
		$qry	=	"select id, name, code, descr, distance from m_landingcenter where id=$landingCenterId";		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Landing Center based on id 
	function find($centerId)
	{
		$qry	=	"select id, name, code, descr, distance from m_landingcenter where id=$centerId";
		return $this->databaseConnect->getRecord($qry);
	}
	function fetchLocationType($centerId)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select name FROM m_landingcenter WHERE id=$centerId";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	# Delete a Landing Center	
	function deleteCenter($centerId)
	{
		$qry	=	" delete from m_landingcenter where id='$centerId'";		
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update Center
	function updateCenter($centerId, $centerName, $centerCode, $centerDesc, $distance)
	{
		$qry	= " update m_landingcenter set code='$centerCode' , name='$centerName', descr='$centerDesc', distance='$distance'  where id=$centerId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function updateCentreconfirm($centerId)
	{
		$qry	= "update m_landingcenter set active='1' where id=$centerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateCenterReleaseconfirm($centerId)
	{

		$qry	= "update m_landingcenter set active='0' where id=$centerId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#Find Landing Center using Id
	function findLandingCenter($centerId)
	{
		$rec = $this->find($centerId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	# -----------------------------------------	
	# Checking Fish Id is in use (linked with Supplier, Process Yield, Daily Pre Process, Sub Supplier, Daily Catch Entry
	# -----------------------------------------
	function landingCenterRecInUse($landingCenterId)
	{		
		$qry = " select id from (
				select a.id as id from m_supplier2center a where a.center_id='$landingCenterId'
			union
				select a1.id as id from m_process_yield_months a1 where a1.center_id='$landingCenterId'
			union 
				select a2.id as id from t_dailypreprocess_entries a2 where a2.center_id='$landingCenterId'
			union 
				select a3.id as id from m_subsupplier a3 where a3.place='$landingCenterId'
			union 
				select a4.id as id from t_dailycatch_main a4 where a4.landing_center='$landingCenterId'
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}
	
}