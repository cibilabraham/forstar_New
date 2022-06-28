<?
require_once("flib/AFModel.php");
class role_model extends AFModel
{
	protected $tableName = "";
	protected $pk = 'id';	// Primary key field

	#Get distinct Module queryAllAll
	function disitnctModuleIdRecs($roleMainId)
	{
		$qry	=	"select distinct a.module_id, b.name from role_function a, module b where a.module_id=b.id and role_id='$roleMainId' order by module_id asc";
		return $this->queryAll($qry);
	}

	# Find Sub Menu Rec
	function findSubMenu($subMenuId)
	{
		$qry = "select name from submodule where id='$subMenuId'";
		$rec = $this->queryAll($qry);
		return (sizeof($rec)>0) ? $rec[0]->name	:	"";
	}
}

?>
