<?php
Class ManageMenus
{

	/****************************************************************
	This class deals with all the operations relating to Challan Verification
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ManageMenus(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	# Get Module Records
	function getModuleRecords()
	{
		$qry = "select id, name from module order by id";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Sub Menus
	function getSubMenus($moduleId)
	{
		$qry = "select id, name from submodule where module_id='$moduleId' order by id";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Function Records
	function getFunctionRecords($selModule, $selSubModule)
	{
		$whr  = " module_id='$selModule' and group_main_id=0";
		
		if ($selSubModule!="") $whr .= " and pmenu_id='$selSubModule'";
		else $whr .= " and pmenu_id=0";
		
		$orderBy = " menu_order asc";

		$qry = "select id, name, menu_order,extraflag from function ";

		if ($whr!="") 		$qry .= " where".$whr ;
		if ($orderBy!="") 	$qry .= " order by".$orderBy ;
		//echo  $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
	function changeMenuOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeMenuF = $splitRec[0];
		$changeMenuS = $splitRec[1];
		list($functionIdF, $menuOrderF) = $this->getFunctionRec($changeMenuF);
		list($functionIdS, $menuOrderS) = $this->getFunctionRec($changeMenuS);
		if ($functionIdF!="") {
			$updateMenuRecF = $this->updateMenuOrder($functionIdF, $menuOrderF);
		}

		if ($functionIdS!="") {
			$updateMenuRecS = $this->updateMenuOrder($functionIdS, $menuOrderS);
		}
		return ($updateMenuRecF || $updateMenuRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getFunctionRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):"";
	}

	# update Menu Order
	function updateMenuOrder($functionId, $menuOrder)
	{
		 $qry = "update function set menu_order='$menuOrder' where id='$functionId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateextraflagvalue($idValue,$flagValue)
	{
		$qry = "update function set extraflag='$flagValue' where id='$idValue'";
		$result = $this->databaseConnect->updateRecord($qry);
		//echo $qry;
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/

}	
?>