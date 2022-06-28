<?
class IdManager
{  
	# ***************************************************************
	# This class deals with all the operations relating to ID Manager 
	# ***************************************************************
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function IdManager(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}


	function getList()
	{
		$qry	= " select id, type, start_no, end_no, current_no, generate, active from number_gen ";
		//echo $qry;
		return $this->databaseConnect->getRecords( $qry );
	}

	function update($startNumber,$endNumber,$currentNumber, $id, $type,$autoGen)
	{
		//echo "S=$startNumber,E=$endNumber,C=$currentNumber, $id, $type,$autoGen";
		$result = false;
		$sqry = " select id, type, start_no, end_no, current_no, generate from number_gen where type='$type' for update";
		$srec = $this->databaseConnect->getRecords( $sqry );
		if( sizeof($srec) > 0 )
		{
			$uqry = " update number_gen set start_no=$startNumber, end_no=$endNumber, current_no=$currentNumber, generate='$autoGen' where id=$id and type='$type'";
			$urec = $this->databaseConnect->updateRecord( $uqry );
			if( $urec )
			{
				$this->databaseConnect->commit();
				$result = true;
			}
			else $this->databaseConnect->rollback();
		}
		else $this->databaseConnect->rollback();

		return $result;
	}
	
	function check($type)
	{
		$sqry = " select id from number_gen where type='$type' and generate='Y'";
		$srec = $this->databaseConnect->getRecord( $sqry );
		return ( sizeof($srec) > 0 ) ? 1 : 0;
	}

	function generateNumberByType($type)
	{
		$result = array("N","");
		$sqry = " select id, type, start_no, end_no, current_no, generate from number_gen where type='$type' for update";
		$srec = $this->databaseConnect->getRecords( $sqry );
		if( sizeof($srec) > 0 )
		{
			$id = $srec[0][0];
			$currentNo = $srec[0][4] + 1;
			$endNumber = $srec[0][3];
			
			//echo "CurrentNumber= $currentNo, $endNumber, $id";
			
			$uqry = " update number_gen set current_no=$currentNo where id=$id";
			$urec = $this->databaseConnect->updateRecord( $uqry );
			if( $urec )	$this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
			
			if( $currentNo <= $endNumber ) $result = array("N",$currentNo);
			else $result = array("Y",$currentNo);
		}
		return $result;
	}


	function checkMaxId($type, $poId)
	{
		$sqry = " select end_no from number_gen where type='$type'";
		//echo $sqry;
		$srec = $this->databaseConnect->getRecord( $sqry );
		if( sizeof($srec) > 0 ) 
		{
			if(  $poId > $srec[0] ) return "Y";

		}
		return "N";
	}

}
?>