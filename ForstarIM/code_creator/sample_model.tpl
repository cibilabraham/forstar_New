require_once("flib/AFModel.php");

class {modelName}_model extends AFModel
{
	protected $name = "{modelName}";
	protected $tableName = "{tableName}";
	protected $pk = 'id';	// Primary key field
	// N - numeric, S - string
	protected $fieldType = array();
}
