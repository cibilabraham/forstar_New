<?php
class ExcisableGoodsMaster
{
	/****************************************************************
	This class deals with all the operations relating to State Vat Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ExcisableGoodsMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert (Entry) Records
	function addExcisableGoods($egName, $userId)
	{
		$qry = "insert into m_excisable_goods (name, created_by, created_on) values('$egName', '$userId', NOW())";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Paging Records
	function fetchAllPagingRecords($offset, $limit)
	{		
		$whr = "";		
		
		$orderBy = " name asc ";
		$limit	 = " $offset,$limit ";

		$qry = " select id, name,active,(select count(a1.id) from m_excise_duty a1 where ex_goods_id=a.id) as tot from m_excisable_goods a ";		

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		if ($limit!="")		$qry .= " limit ".$limit;
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$whr = "";		
		
		$orderBy = " name asc ";
		
		$qry = " select id, name,active from m_excisable_goods ";		

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}

	function fetchAllRecordsActiveGoods()
	{
		$whr = "active=1";		
		
		$orderBy = " name asc ";
		
		$qry = " select id, name,active from m_excisable_goods ";		

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="")	$qry .= " order by ".$orderBy;
		
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}



	# Get a Record based on id
	function find($exGoodsId)
	{
		$qry = "select id, name from m_excisable_goods where id=$exGoodsId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update Vat Entry
	function updateExcisableGoods($exGoodsId, $egName)
	{		
		$qry = " update m_excisable_goods set name='$egName' where id='$exGoodsId'";
		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete Selected State Rec
	function deleteExcisableGoodsRec($exGoodsId)
	{
		$qry 	= " delete from m_excisable_goods where id=$exGoodsId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}


	function checkExGoodsIdInUse($exGoodsId)
	{
		$qry = "select id from m_excise_duty where ex_goods_id='$exGoodsId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function updateexGoodsconfirm($exGoodsId){
		$qry	= "update m_excisable_goods set active='1' where id=$exGoodsId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}

	function updateexGoodsReleaseconfirm($exGoodsId){

		$qry	= "update m_excisable_goods set active='0' where id=$exGoodsId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;


	}




}
?>