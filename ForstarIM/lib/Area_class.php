<?php
class Area
{
	/****************************************************************
	This class deals with all the operations relating to Registration Type
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Area(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Registration Type
	function addArea($areaUnitName, $baseUnitReference, $values, $userId)
	{
		$qry	=	"insert into m_area_unit (area_Unit_Name, base_Unit_Reference, val, created_on, created_by) values('".$areaUnitName."', '$baseUnitReference', '".$values."', Now(), '$userId')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select id, area_Unit_Name, base_Unit_Reference, val,active FROM m_area_unit order by area_Unit_Name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all Registration Type 
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, area_Unit_Name, base_Unit_Reference, val,active FROM m_area_unit order by area_Unit_Name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecordsActivedept()
	{
		$qry	= "select id, name, description, incharge,active from m_department where active=1 order by name";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Registration Type based on id 
	function find($areaId)
	{
		$qry	= "select id, area_Unit_Name, base_Unit_Reference, val  from m_area_unit where id=$areaId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Registration Type 
	function deleteArea($areaId)
	{
		$qry	= " delete from m_area_unit where id=$areaId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Registration Type
	function updateArea($areaId, $areaUnitName, $baseUnitReference, $values)
	{
		 $qry	= " update m_area_unit set area_Unit_Name='$areaUnitName', base_Unit_Reference='$baseUnitReference', val='$values' where id=$areaId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateAreaconfirm($areaId){
		$qry	= "update m_area_unit set active='1' where id=$areaId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateAreaReleaseconfirm($areaId){
	$qry	= "update m_area_unit set active='0' where id=$areaId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function fetchAllRecordsunitActive()
	{
		$qry	= "select id,area_Unit_Name,active from m_area_unit where active=1 order by area_Unit_Name asc";		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchPondSizeUnit($pondSizeUnitcode)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select area_Unit_Name FROM m_area_unit WHERE id=$pondSizeUnitcode";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
}

?>