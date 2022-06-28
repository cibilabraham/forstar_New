<?php
require_once("flib/AFModel.php");
class accesscontrol_model extends AFModel
{
	protected $tableName = "";
	protected $pk = 'id';	// Primary key field
	protected $rec;

	function getAccessControl($roleId,$moduleId, $functionId)
	{
		//$roleId=$this->roleId;
		#Finding Sub Module
		$roleFunctionQry = "select a.id, b.pmenu_id as pMenuId from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";

		$roleRec = $this->queryAll($roleFunctionQry);
		if ( sizeof($roleRec) <= 0 ) return;
		//print_r($roleRec);
		$subModuleId = $roleRec[0]->pmenuid;
		

		#Check for access
		$qry = "select a.id, a.role_id, a.module_id, a.function_id, a.access, a.form_add, a.form_edit, a.form_print, a.form_del, a.confirm, a.active, a.form_reedit, a.frm_cpny_specific from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId and (a.function_id='$functionId' or a.function_id=0) order by a.function_id desc";

		//$qry	=	"select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit from role_function where role_id='$roleId' and module_id='$moduleId' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo $qry."\n";
		$recs = $this->queryAll($qry);		
		if ( sizeof($recs) > 0 ) $this->rec = $recs[0];
	}

	function canAccess()
	{
		
		return ($this->rec->access=='Y')?true:false;
	}
	function canAdd()
	{

		return ($this->rec->form_add=='Y')?true:false;
	}

	function canEdit()
	{
		return ($this->rec->form_edit=='Y')?true:false;
	}
	function canPrint()
	{
		return ($this->rec->form_print=='Y')?true:false;
	}
	
	function canDel()
	{
		return ($this->rec->form_del=='Y')?true:false;
	}
	
	function canConfirm()
	{
		return ($this->rec->form_confirm=='Y')?true:false;
	}
	
	function isActive()
	{
		return ($this->rec->active=='Y')?true:false;
	}

	function canReEdit()
	{
		return ($this->rec->form_reedit=='Y')?true:false;
	}

	function canCompanySpecific()
	{
		return ($this->rec->cpny_specific=='Y')?true:false;
	}

}
