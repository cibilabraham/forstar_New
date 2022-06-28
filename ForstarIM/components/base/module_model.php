<?
require_once("flib/AFModel.php");
class module_model extends AFModel
{
	protected $tableName = "module";
	protected $pk = 'id';	// Primary key field

	function getMenuPath($url)
	{
		$qry = " select a.id, a.name, b.id, b.name as fname, c.id, c.name as sname from module a, function b left join submodule c on c.id=b.pmenu_id where a.id=b.module_id and b.url='$url' ";
		$recs = $this->queryAll($qry);
		if ( sizeof($recs) <= 0 ) return "";
		$rec = $recs[0];
//		print_r($rec);
		//return array($rec[1], $rec[5], $rec[3]);				
		$moduleName 	= $rec->name;
		$subModuleName	= $rec->sname;
		$functionName	= ($rec->fname!="")? $rec->fname: "Home";
		$displayPath = "";
		if ($moduleName!="") 	$displayPath = " $moduleName <span class='tlHBar-arrow'>>></span>";
		if ($subModuleName!="")	$displayPath .= " $subModuleName <span class='tlHBar-arrow'>>></span>";
		if ($functionName!="")	$displayPath .= " <a href='$url' class='link4'>$functionName</a> ";
		return 	$displayPath;
	}

	#Submenu
	function getSubmenus($menuId)
	{
		$qry = "select a.id as id, a.name as name, b.id as fnId, b.module_id as moduleId, b.name as fnName, b.url as fnUrl , b.target as fnTarget, b.pmenu_id as pMenuId from module a, function b where a.id=b.module_id and b.pmenu_id='$menuId' and b.group_main_id=0 order by b.menu_order asc";
		return $this->queryAll($qry);
	}

	#Get Sub Module
	function getDistinctSubModule($moduleId, $roleId)
	{
		$qry = "select distinct b.pmenu_id as pMenuId from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId order by b.pmenu_id asc";
		// a.submodule_id=b.pmenu_id ->this will display only selected sub menu/
		// a.module_id=b.module_id -> will display all submenu
		//echo $qry."\n";
		return $this->queryAll($qry);
	}

	function getEmptyOfSubModule($moduleId)
	{
		$qry = "select id, module_id, name, url, target from function where module_id=$moduleId order by menu_order asc";
		return $this->queryAll($qry);
	}

	#For finding the Url (Return Module Id and Function Id)
	function resolveIds($url)
	{
		$qry = "select a.id as moduleid, b.id as functionid from module a, function b where a.id=b.module_id and b.url='$url'";
		$result = $this->query($qry);
		//print_r($result);
		return array($result->moduleid,$result->functionid); 		
	}
}

?>
