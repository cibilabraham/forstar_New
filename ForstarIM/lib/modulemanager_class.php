<?php
class ModuleManager
{
	/****************************************************************
	This class deals with all the operations relating to Module Manager
	*****************************************************************/
	var $databaseConnect;


	//Constructor, which will create a db instance for this class
	function ModuleManager(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Get Functions For Modules
	function getFunctionsForModule()
	{
		$qry = "select a.id, a.name, b.id, b.module_id, b.name, b.url, b.target, b.pmenu_id from module a, function b where a.id=b.module_id order by b.module_id asc, b.pmenu_id asc";
		//echo $qry;	
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#For finding the Url (Return Module Id and Function Id)
	function resolveIds($url)
	{
		$qry = "select a.id, a.name, b.id, b.module_id, b.name, b.url from module a, function b where a.id=b.module_id and b.url='$url'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecord($qry);
		return array($result[0],$result[2]); 		
	}

	#Submenu
	function getSubmenus($menuId)
	{
		$qry = "select a.id, a.name, b.id, b.module_id, b.name, b.url, b.target, b.pmenu_id from module a, function b where a.id=b.module_id and b.pmenu_id='$menuId' and b.group_main_id=0 order by b.menu_order asc";
		//echo $qry."\n";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Get Sub Module
	function getDistinctSubModule($moduleId, $roleId)
	{
		 //$qry = "select distinct b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId order by b.pmenu_id asc";
		//echo $qry = "select distinct b.pmenu_id from role_function a, function b,submodule c where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId order by c.order asc";
		 $qry = "SELECT DISTINCT b.pmenu_id FROM role_function a LEFT JOIN function b ON a.submodule_id = b.pmenu_id LEFT JOIN submodule c ON c.id = b.pmenu_id WHERE a.role_id =$roleId AND a.module_id =$moduleId ORDER BY c.order_by ASC ";
		// a.submodule_id=b.pmenu_id ->this will display only selected sub menu/
		// a.module_id=b.module_id -> will display all submenu
		//echo $qry."\n";
		$result	=array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	function getEmptyOfSubModule($moduleId)
	{
		$qry = "select id, module_id, name, url, target from function where module_id=$moduleId order by menu_order asc";
		$result	=array();
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	#Get Sub Module Id
// 	function getSubModule($functionId)
// 	{
// 		$qry = "select pmenu_id from function where id=$functionId";
// 		$rec = $this->databaseConnect->getRecord($qry);
// 		return (sizeof($rec)>0)?$rec[0]:"";
// 	}

	///////////////////////////////////////////////////////////////////////
	function updateRecord()
	{
		$qry = "select a.id, a.name, a.url, b.name, b.module_id from function_old a join function_old b on a.pmenu_id=b.id where a.pmenu_id is not null order by a.module_id asc, b.name asc";

		$result	=array();
		$result	= $this->databaseConnect->getRecords($qry);

		$prevSubModuleName = "";
		$newSubModuleId = "";
		foreach ($result as $r) {
			$functionId = $r[0];
			$functionName  = $r[1];
			$functionUrl = $r[2];
			$subModuleName  = trim($r[3]);
			$moduleId	= $r[4];
			if ($prevSubModuleName!=$subModuleName) {
				//echo "$subModuleName=$moduleId<br>";
				$moduleInserted = $this->insertSubModule($moduleId, $subModuleName);
				$newSubModuleId = $this->databaseConnect->getLastInsertedId();
			}
			$functionRecordInserted = $this->insertFunction($moduleId, $functionName, $functionUrl, $newSubModuleId);
			if ($functionRecordInserted) {
				$newFunctionId = $this->databaseConnect->getLastInsertedId();
				$updateRoleFunction = $this->UpdateRoleFunction($newFunctionId, $functionId, $moduleId, $newSubModuleId);
			}
			 $prevSubModuleName = $subModuleName;
			//echo "$prevSubModuleName = $subModuleName<br>";
		}		
	}

	function insertSubModule($moduleId, $subModuleName)
	{
		$qry	= "insert into submodule (module_id, name) values('".$moduleId."','".$subModuleName."')";
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function insertFunction($moduleId, $functionName, $functionUrl, $newSubModuleId)
	{
		$qry	= "insert into function (module_id, name, url, pmenu_id) values('".$moduleId."', '".$functionName."', '$functionUrl', '$newSubModuleId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function UpdateRoleFunction($newFunctionId, $functionId, $moduleId, $newSubModuleId)
	{
		$qry	= " update role_function set function_id='$newFunctionId', submodule_id=$newSubModuleId where function_id=$functionId and module_id=$moduleId";

		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	
	#For finding the Url 
	# Return Module Name, Sub Module Name, Function Name
	function getMenuPath($url)
	{
		$qry = " select a.id, a.name, b.id, b.name, c.id, c.name from (module a, function b) left join submodule c on c.id=b.pmenu_id where a.id=b.module_id and b.url='$url' ";
		//echo $qry;		
		$rec	= $this->databaseConnect->getRecord($qry);
		//return array($rec[1], $rec[5], $rec[3]);				
		$moduleName 	= $rec[1];
		$subModuleName	= $rec[5];
		$functionName	= ($rec[3]!="")?$rec[3]:"Home";
		$displayPath = "";
		if ($moduleName!="") 	$displayPath = " $moduleName <span class='tlHBar-arrow'>>></span>";
		if ($subModuleName!="")	$displayPath .= " $subModuleName <span class='tlHBar-arrow'>>></span>";
		if ($functionName!="")	$displayPath .= " <a href='$url' class='link4'>$functionName</a> ";
		return 	$displayPath;
	}


	# Returns Function Id, Module Id, SubModuleId from URL (Function table)
	function getFunctionIds($url)
	{
		$qry = "select id, module_id, pmenu_id  from function where url='$url'";
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return array($result[0], $result[1], $result[2]); 		
	}
	

}