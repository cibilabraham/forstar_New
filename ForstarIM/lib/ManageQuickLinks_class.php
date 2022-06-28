<?php
Class ManageQuickLinks
{

	/****************************************************************
	This class deals with all the operations relating to Challan Verification
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ManageQuickLinks(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	

	# Add To Quick List
	function addToQuickList($funcId,$userId)
	{
			$menu_order = "select max(menu_order)+1 from quicklist";
			$result	=	$this->databaseConnect->getRecords($menu_order);
			$rec=$result[0][0];
			//echo "Result is =".$rec;
		
			$qry	=	"insert into quicklist (func_id,user_id,menu_order) values('$funcId','$userId','$rec')";
		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	#Delete From Quick List
	function deletFromQuickList($funcId,$userId)
	{
		$qry	=	" delete from quicklist where func_id=$funcId and user_id=$userId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}
	
	# Get Quick List Records
	function getQuickListRecords()
	{
		$qry = "select id,func_id,user_id,menu_order from quicklist order by menu_order";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Function Name 
	function getFunctionName($funcId)
	{
		$qry = "select name from function where id='$funcId'";
		return $this->databaseConnect->getRecord($qry);
	}

	#Get Quick List 
	function getAllQuickList($userId)
	{
		$qry = "select a.func_id,b.name,b.url from quicklist a, function b where a.func_id=b.id and user_id='$userId' order by a.menu_order";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Find Record
	function  find($funcId)
	{
		$qry = "select func_id from quicklist where func_id='$funcId'";
		return $this->databaseConnect->getRecord($qry);
	}

	# Get Module Records
	function getModuleRecords($roleMainId)
	{
		//$qry = "select id, name from module order by id";
		$qry	= "select distinct a.module_id, b.name from role_function a, module b where a.module_id=b.id and role_id='$roleMainId' order by module_id asc";
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
	function getFunctionRecords($selModule, $selSubModule, $userId)
	{
		$whr  = "  b.id is NULL and a.module_id='$selModule' ";
		
		if ($selSubModule!="") $whr .= " and a.pmenu_id='$selSubModule'";
		else $whr .= " and a.pmenu_id=0";
		
		$orderBy = " a.menu_order asc";

		//$qry = "select id, name, menu_order from function ";
		$qry = "select a.id,a.name from function a left join quicklist b on a.id = b.func_id and b.user_id='$userId' ";

		if ($whr!="") 		$qry .= " where".$whr ;
		if ($orderBy!="") 	$qry .= " order by".$orderBy ;
		//echo $qry."<br>";
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
		
		$qry = "update quicklist set menu_order='$menuOrder' where id='$functionId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/

}	
?>