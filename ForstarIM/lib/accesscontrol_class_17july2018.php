<?php
class AccessControl
{
	/****************************************************************
	This class deals with all the operations relating to Access Control
	*****************************************************************/
	var $databaseConnect;
	var $roleId;
	var $rec;
	
	//Constructor, which will create a db instance for this class
	
	function AccessControl(&$databaseConnect,$roleId)
        {
        	$this->databaseConnect =&$databaseConnect;
		$this->roleId	=	$roleId;
	}

	function getAccessControl($moduleId, $functionId)
	{
		
		$roleId=$this->roleId;
		//echo($roleId);
		//exit;
		
		#Finding Sub Module
		$roleFunctionQry = "select a.id, b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";
        //echo $roleFunctionQry;
		$roleRec = $this->databaseConnect->getRecord($roleFunctionQry);
		$subModuleId = $roleRec[1];

		#Check for access
		$qry = "select a.id, a.role_id, a.module_id, a.function_id, a.access, a.form_add, a.form_edit, a.form_print, a.form_del, a.confirm, a.active, a.form_reedit, a.frm_cpny_specific,a.supplierdtflg from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId and (a.function_id='$functionId' or a.function_id=0) and stflg is null order by a.function_id desc";
		//echo $qry;

		//$qry	=	"select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit from role_function where role_id='$roleId' and module_id='$moduleId' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo $qry."\n";
		$this->rec = $this->databaseConnect->getRecord($qry);
	}

	function canViewSupplierData($moduleId, $functionId)
	{
		$roleId=$this->roleId;
		$roleFunctionQry = "select a.id, b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";
		$roleRec = $this->databaseConnect->getRecord($roleFunctionQry);
		$subModuleId = $roleRec[1];

		$qryv="select a.id, a.role_id, a.module_id, a.function_id,a.supplierdtflg from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId and (a.function_id='$functionId' or a.function_id=0) and a.supplierdtflg is not null";
		//echo $qryv;
		$this->recv = $this->databaseConnect->getRecord($qryv);
		return $this->recv[4];
	}

	function canAccess()
	{
		
		
		return ($this->rec[4]=='Y')?true:false;
	}
	function canAdd()
	{

		return ($this->rec[5]=='Y')?true:false;
	}

	function canEdit()
	{
		return ($this->rec[6]=='Y')?true:false;
	}
	function canPrint()
	{
		return ($this->rec[7]=='Y')?true:false;
	}
	
	function canDel()
	{
		return ($this->rec[8]=='Y')?true:false;
	}
	
	function canConfirm()
	{
		return ($this->rec[9]=='Y')?true:false;
	}
	
	function isActive()
	{
		return ($this->rec[10]=='Y')?true:false;
	}

	function canReEdit()
	{
		return ($this->rec[11]=='Y')?true:false;
	}

	function canCompanySpecific()
	{
		return ($this->rec[12]=='Y')?true:false;
	}



function getAccessControlInv($moduleId, $functionId)
	{
		
		$roleId=$this->roleId;
		#Finding Sub Module
		$roleFunctionQry = "select a.id, b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";

		$roleRec = $this->databaseConnect->getRecord($roleFunctionQry);
		$subModuleId = $roleRec[1];

		#Check for access
		$qry = "select a.id, a.role_id, a.module_id, a.function_id, a.access, a.form_add, a.form_edit, a.form_print, a.form_del, a.confirm, a.active, a.form_reedit, a.frm_cpny_specific,a.supplierdtflg from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId and supplierdtflg='INV' and (a.function_id='$functionId' or a.function_id=0) order by a.function_id desc ";
			//echo $qry;
		//$qry	=	"select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit from role_function where role_id='$roleId' and module_id='$moduleId' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo $qry."\n";
		$this->recinv = $this->databaseConnect->getRecord($qry);
	}
	function getAccessControlFRN($moduleId, $functionId)
	{
		
		$roleId=$this->roleId;
		#Finding Sub Module
		$roleFunctionQry = "select a.id, b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";

		$roleRec = $this->databaseConnect->getRecord($roleFunctionQry);
		$subModuleId = $roleRec[1];

		#Check for access
		$qry = "select a.id, a.role_id, a.module_id, a.function_id, a.access, a.form_add, a.form_edit, a.form_print, a.form_del, a.confirm, a.active, a.form_reedit, a.frm_cpny_specific,a.supplierdtflg from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId  and supplierdtflg='FRN' and (a.function_id='$functionId' or a.function_id=0) order by a.function_id desc";

		//$qry	=	"select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit from role_function where role_id='$roleId' and module_id='$moduleId' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo $qry."\n";
		$this->recfrn = $this->databaseConnect->getRecord($qry);
	}
function getAccessControlRTE($moduleId, $functionId)
	{
		
		$roleId=$this->roleId;
		#Finding Sub Module
		$roleFunctionQry = "select a.id, b.pmenu_id from role_function a, function b where a.submodule_id=b.pmenu_id and a.role_id=$roleId and a.module_id=$moduleId and  b.id='$functionId' order by a.function_id desc";

		$roleRec = $this->databaseConnect->getRecord($roleFunctionQry);
		$subModuleId = $roleRec[1];

		#Check for access
		$qry = "select a.id, a.role_id, a.module_id, a.function_id, a.access, a.form_add, a.form_edit, a.form_print, a.form_del, a.confirm, a.active, a.form_reedit, a.frm_cpny_specific,a.supplierdtflg from role_function a where a.role_id=$roleId and a.module_id=$moduleId and a.submodule_id=$subModuleId and supplierdtflg='RTE' and (a.function_id='$functionId' or a.function_id=0) order by a.function_id desc ";

		//$qry	=	"select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit from role_function where role_id='$roleId' and module_id='$moduleId' and (function_id='$functionId' or function_id=0) order by function_id desc";
		//echo $qry."\n";
		$this->recrte = $this->databaseConnect->getRecord($qry);
	}





	function canAccessinv()
	{
		
		return ($this->recinv[4]=='Y')?true:false;
	}
	function canAddinv()
	{

		return ($this->recinv[5]=='Y')?true:false;
	}

	function canEditinv()
	{
		return ($this->recinv[6]=='Y')?true:false;
	}
	function canPrintinv()
	{
		return ($this->recinv[7]=='Y')?true:false;
	}
	
	function canDelinv()
	{
		return ($this->recinv[8]=='Y')?true:false;
	}
	
	function canConfirminv()
	{
		return ($this->recinv[9]=='Y')?true:false;
	}
	
	function isActiveinv()
	{
		return ($this->recinv[10]=='Y')?true:false;
	}

	function canReEditinv()
	{
		return ($this->recinv[11]=='Y')?true:false;
	}

	function canCompanySpecificinv()
	{
		return ($this->recinv[12]=='Y')?true:false;
	}



	
	function canAccessfrn()
	{
		
		return ($this->recfrn[4]=='Y')?true:false;
	}
	function canAddfrn()
	{

		return ($this->recfrn[5]=='Y')?true:false;
	}

	function canEditfrn()
	{
		return ($this->recfrn[6]=='Y')?true:false;
	}
	function canPrintfrn()
	{
		return ($this->recfrn[7]=='Y')?true:false;
	}
	
	function canDelfrn()
	{
		return ($this->recfrn[8]=='Y')?true:false;
	}
	
	function canConfirmfrn()
	{
		return ($this->recfrn[9]=='Y')?true:false;
	}
	
	function isActivefrn()
	{
		return ($this->recfrn[10]=='Y')?true:false;
	}

	function canReEditfrn()
	{
		return ($this->recfrn[11]=='Y')?true:false;
	}

	function canCompanySpecificfrn()
	{
		return ($this->recfrn[12]=='Y')?true:false;
	}

	
	function canAccessrte()
	{
		
		return ($this->recrte[4]=='Y')?true:false;
	}
	function canAddrte()
	{

		return ($this->recrte[5]=='Y')?true:false;
	}

	function canEditrte()
	{
		return ($this->recrte[6]=='Y')?true:false;
	}
	function canPrintrte()
	{
		return ($this->recrte[7]=='Y')?true:false;
	}
	
	function canDelrte()
	{
		return ($this->recrte[8]=='Y')?true:false;
	}
	
	function canConfirmrte()
	{
		return ($this->recrte[9]=='Y')?true:false;
	}
	
	function isActiverte()
	{
		return ($this->recrte[10]=='Y')?true:false;
	}

	function canReEditrte()
	{
		return ($this->recrte[11]=='Y')?true:false;
	}

	function canCompanySpecificrte()
	{
		return ($this->recrte[12]=='Y')?true:false;
	}

function homeAccessInv($roleId)
	{
$qry="select access from role_function where role_id='$roleId' and supplierdtflg='INV'";
//echo $qry;
$this->recv = $this->databaseConnect->getRecord($qry);
return ($this->recv[0]=='Y')?true:false;

	}

}