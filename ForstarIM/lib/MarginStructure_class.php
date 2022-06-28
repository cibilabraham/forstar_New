<?php
class MarginStructure
{
	/****************************************************************
	This class deals with all the operations relating to Margin Structure
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function MarginStructure(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add
	function addMarginStructure($marginStructureCode, $marginStructureName, $description, $calcAvgDistMagn, $priceCalcType, $billingFormF, $schemeChk, $selSchemeHeadId)
	{
		#find the display order Id
		$displayOrderId = $this->getDisplayOrderId();

		$qry = "insert into m_margin_structure(code, name, descr, use_avg_dist, price_calc, display_order, billing_form_f, scheme_chk, scheme_struct_id) values('$marginStructureCode', '".$marginStructureName."', '".$description."', '$calcAvgDistMagn', '$priceCalcType', '$displayOrderId', '$billingFormF', '$schemeChk', '$selSchemeHeadId')";
		//echo $qry;
		$insertStatus = $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Records (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		$qry = "select id, name, descr, price_calc, use_avg_dist, code, display_order, billing_form_f from m_margin_structure order by use_avg_dist asc, display_order asc limit $offset, $limit";
		//$qry = "select id, name, descr, price_calc, use_avg_dist, code, display_order from m_margin_structure order by id asc, use_avg_dist asc, name asc limit $offset, $limit";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Returns all Records
	function fetchAllRecords()
	{
		$qry = "select id, name, descr, price_calc, use_avg_dist, code, display_order, billing_form_f from m_margin_structure order by use_avg_dist asc, display_order asc";
		//echo $qry;
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Record  based on id 
	function find($marginStructureId)
	{
		$qry = "select id, name, descr, use_avg_dist, price_calc, code, billing_form_f, scheme_chk, scheme_struct_id from m_margin_structure where id=$marginStructureId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	# Update
	function updateMarginStructure($marginStructureId, $marginStructureCode, $marginStructureName, $description, $calcAvgDistMagn, $priceCalcType, $billingFormF, $schemeChk, $selSchemeHeadId)
	{
		$qry = "update m_margin_structure set code='$marginStructureCode', name='$marginStructureName', descr='$description', use_avg_dist='$calcAvgDistMagn', price_calc='$priceCalcType', billing_form_f='$billingFormF', scheme_chk='$schemeChk', scheme_struct_id='$selSchemeHeadId' where id=$marginStructureId";
		//echo $qry;
		$result = $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	# Delete 
	function deleteMarginStructure($marginStructureId)
	{
		$qry = " delete from m_margin_structure where id=$marginStructureId";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Checking Margin Structure used in another table
	function checkMgnStructUse($marginStructureId)
	{
		$qry = "select id from m_distributor_margin_entry where margin_structure_id='$marginStructureId'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	#Find the Margin Structure Name
	function findMarginStructureName($marginStructureId)
	{
		$rec = $this->find($marginStructureId);
		return sizeof($rec)>0?$rec[1]:"";
	}

	# Find the Display Order Id
	function getDisplayOrderId()
	{
		$qry = "select (count(*)+1) from m_margin_structure";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:1;
	}

	/************************ Display Order Starts Here ******************************/
	# Move Up rec
	/*
	function moveUpRec($moveUpId)
	{
		//echo "MoveUpRecId=".echo "MoveDownRecId=".
		$moveUpRecId = $this->findRecId($moveUpId);
		$moveDownRecId = $this->findRecId($moveUpId-1);

		$qryMoveUp = " update m_margin_structure set display_order=display_order-1 where id='$moveUpRecId' ";
		$qryMoveDown = " update m_margin_structure set display_order=display_order+1 where id='$moveDownRecId' ";
		
		//echo $qry;
		$resultMoveUp = $this->databaseConnect->updateRecord($qryMoveUp);
		$resultMoveDown = $this->databaseConnect->updateRecord($qryMoveDown);

		if ($resultMoveUp || $resultMoveDown) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $resultMoveUp;	
	}
	
	#find the record Id based on Display Order Id
	function findRecId($displayId)
	{
		$qry = "select id from m_margin_structure where display_order='$displayId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# Move Down Rec
	function moveDownRec($moveDownId)
	{
		$moveUpRecId = $this->findRecId($moveDownId+1);
		$moveDownRecId = $this->findRecId($moveDownId);

		$qryMoveUp = " update m_margin_structure set display_order=display_order-1 where id='$moveUpRecId' ";
		$qryMoveDown = " update m_margin_structure set display_order=display_order+1 where id='$moveDownRecId' ";
		
		//echo $qry;
		$resultMoveUp = $this->databaseConnect->updateRecord($qryMoveUp);
		$resultMoveDown = $this->databaseConnect->updateRecord($qryMoveDown);

		if ($resultMoveUp || $resultMoveDown) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $resultMoveUp;		
	}
	*/
	/********************* Display Order End Here****************************/

	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
	function changeDisplayOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeDisOrderF = $splitRec[0];
		$changeDisOrderS = $splitRec[1];
		list($maginStructIdF, $disOrderIdF) = $this->getMarginStructRec($changeDisOrderF);
		list($marginStructIdS, $disOrderIdS) = $this->getMarginStructRec($changeDisOrderS);
		if ($maginStructIdF!="") {
			$updateDisOrderRecF = $this->updateMaginStructDisOrder($maginStructIdF, $disOrderIdF);
		}

		if ($marginStructIdS!="") {
			$updateDisOrderRecS = $this->updateMaginStructDisOrder($marginStructIdS, $disOrderIdS);
		}
		return ($updateDisOrderRecF || $updateDisOrderRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getMarginStructRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):"";
	}

	# update Menu Order
	function updateMaginStructDisOrder($marginStructId, $displayOrder)
	{
		$qry = "update m_margin_structure set display_order='$displayOrder' where id='$marginStructId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/
}
?>