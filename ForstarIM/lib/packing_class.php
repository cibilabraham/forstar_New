<?
class PackingGoods
{  
	/****************************************************************
	This class deals with all the operations relating to fish master 
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function PackingGoods(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect;
	}

	function addPacking($code,$descr,$weight,$unit)
	{
		$qry	=	"insert into m_packing (code,descr,packed_weight,unit) values('".$code."','".$descr."','".$weight."','".$unit."')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Finished Goods 

	function fetchAllRecords()
	{
		$qry	=	"select id,code,descr,packed_weight,unit from m_packing";

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Packing  based on id 

	function find($packingId)
	{
		$qry	=	"select id,code,descr,packed_weight,unit from m_packing where id=$packingId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Packing Code

	function deletePacking($packingId)
	{
		$qry	=	" delete from m_packing where id=$packingId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	# Update packing

	function updatePacking($packingId,$code,$descr,$weight,$unit)
	{
		$qry	=	" update m_packing set code='$code', descr='$descr', packed_weight='$weight', unit='$unit' where id=$packingId";
		//echo $qry;

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
}