<?php
class FrozenPacking 
{  
	/****************************************************************
	This class deals with all the operations relating to Frozen Packing
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function FrozenPacking(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addFrozenPacking($frozenCode,$selUnit,$freezingId,$declWt,$glazeId, $filledWt,$description, $actualFilledWt)
	{
		$qry	=	"insert into m_frozenpacking (code, unit, freezing_id, decl_wt, glaze_id, filled_wt, descr, actual_filled_wt) values('".$frozenCode."','".$selUnit."','".$freezingId."','".$declWt."', '".$glazeId."', '".$filledWt."', '".$description."', '$actualFilledWt')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry	=	"select id, code, unit, freezing_id, decl_wt, glaze_id, filled_wt, descr, actual_filled_wt,active,((select count(a1.id) from t_dailyfrozenpacking_entry a1 where a1.frozencode_id=a.id)+(select count(a3.id) from m_stock2frozencode a3 where a3.frozencode_id=a.id)+(select count(a3.id) from t_purchaseorder_rm_entry a3 where a3.frozencode_id=a.id)) as tot from m_frozenpacking a order by code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	function fetchAllRecordsActiveFrozen()
	{
		$qry	=	"select id, code, unit, freezing_id, decl_wt, glaze_id, filled_wt, descr, actual_filled_wt,active from m_frozenpacking where active=1 order by code asc";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all Records[PAGING]
	function fetchPagingRecords($offset, $limit)
	{
		$qry	= "select id, code, unit, freezing_id, decl_wt, glaze_id, filled_wt, descr, actual_filled_wt,active,((select count(a1.id) from t_dailyfrozenpacking_entry a1 where a1.frozencode_id=a.id)+(select count(a3.id) from m_stock2frozencode a3 where a3.frozencode_id=a.id)+(select count(a3.id) from t_purchaseorder_rm_entry a3 where a3.frozencode_id=a.id)) as tot from m_frozenpacking a order by code asc limit $offset, $limit";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}


	# Get Packing  based on id 

	function find($frozenPackingId)
	{
		$qry	= "select id, code, unit, freezing_id, decl_wt, glaze_id, filled_wt, descr, actual_filled_wt  from m_frozenpacking where id=$frozenPackingId";
		return $this->databaseConnect->getRecord($qry);
		//echo $qry;
	}

	# Delete a Frozen Packing Code
	function deleteFrozenPacking($frozenPackingId)
	{
		$qry	=	" delete from m_frozenpacking where id=$frozenPackingId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Update Frozen packing
	function updateFrozenPacking($frozenPackingId,$frozenCode, $selUnit, $freezingId,$declWt,$glazeId, $filledWt,$description, $actualFilledWt)
	{
		$qry	=	" update m_frozenpacking set code='$frozenCode', unit='$selUnit', freezing_id='$freezingId', decl_wt='$declWt', glaze_id='$glazeId', filled_wt='$filledWt', descr='$description', actual_filled_wt='$actualFilledWt' where id=$frozenPackingId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}
	
	function findFrozenPackingCode($frozenPackingId)
	{
		$rec = $this->find($frozenPackingId);
		return sizeof($rec) > 0 ? $rec[1] : "";
	}

	function chkDuplicateEntry($frznCode, $frznCodeId)
	{
		$qry	= "select id from m_frozenpacking where code='$frznCode'";
		if ($frznCodeId) $qry .= " and id!='$frznCodeId'";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}	

	# Get Filled Wt of Frozen Packing
	function frznPkgFilledWt($frozenPackingId)
	{
		$qry	= "select filled_wt from m_frozenpacking where id='$frozenPackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}
	function frznPkgglaze($frozenPackingId)
	{
		$qry	= "select glaze_id from m_frozenpacking where id='$frozenPackingId'";
		$rec 	= $this->databaseConnect->getRecords($qry);
		return $rec[0][0];
	}


	function updateFrozenPackingconfirm($frozenPackingId)
	{
	$qry	= "update m_frozenpacking set active='1' where id='$frozenPackingId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}


function updateFrozenPackingReleaseconfirm($frozenPackingId)
	{
		$qry	= "update m_frozenpacking set active='0' where id='$frozenPackingId'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	

	}
}
	# Frozen Pack Class Ends here


	
	/**
	# Xajax Starts Here ----------------------------------------------------
	**/
	function chkFrznCodeExist($frznCode, $frznCodeId, $mode)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$frozenpackingObj	= new FrozenPacking($databaseConnect);
		$chkFrznCodeExist = $frozenpackingObj->chkDuplicateEntry(trim($frznCode), $frznCodeId);		
		if ($chkFrznCodeExist) {
			$objResponse->assign("divFrznCodeExistMsg", "innerHTML", "Frozen packing code is already in database.<br>Please choose another one.");
			$objResponse->script("disableFrznCodeBtn($mode);");
		} else  {
			$objResponse->assign("divFrznCodeExistMsg", "innerHTML", "");
			$objResponse->script("enableFrznCodeBtn($mode);");
		}		
		return $objResponse;
	}
	/*# Xajax Ends here --------------------------------------------------*/