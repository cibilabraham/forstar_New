<?php
class ManageRole
{
	/****************************************************************
	This class deals with all the operations relating to Manage Role
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageRole(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#add Role
	function addRole($roleName, $roleDescription, $copyRoleId)
	{		
		$qry = "insert into role(name,description) values('".$roleName."','".$roleDescription."')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) {
			$this->databaseConnect->commit();
			#----------------------- Copy Functions ---------------------------------------
			$insertedRoleId = $this->databaseConnect->getLastInsertedId();			
			if ($copyRoleId!="" ) {
				$fetchRoleFunctionRecords = $this->fetchAllRoleFunctionRecords($copyRoleId);
				if ($fetchRoleFunctionRecords>0) {
					foreach ($fetchRoleFunctionRecords as $rf) {
						$selModule	= $rf[2];
						$selSubModule   = $rf[11];
						$selFunction	= $rf[3];				
						$selAccess 	= $rf[4];
						$selAdd	   	= $rf[5];
						$selEdit   	= $rf[6];
						$selDelete 	= $rf[8];
						$selPrint  	= $rf[7];
						$selConfirm	= $rf[9];
						$selActive 	= $rf[10];
						$selReEdit 	= $rf[12];
						$selCompanySpecific = $rf[13];
						$supplierdtflag=$rf[14];
						$stflg=$rf[15];

						if ($insertedRoleId!="") {
							# Insert Role Function
							if ($selFunction!=162){
							$roleFunctionRecIns=$this->addRoleFunctionCopy($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $insertedRoleId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflag,null);
							} else {
								$roleFunctionRecIns=$this->addRoleFunctionCopy($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $insertedRoleId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflag,$stflg);

							}
						}
					}
				}			
			}
			#----------------------- Copy Functions End -----------------------------------
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


	# Returns all Roles
	function fetchAllRecords()
	{
		$qry	=	"select id, name, description from role order by name asc";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get Roles based on id 
	function find($roleId)
	{
		$qry	=	"select id, name, description from role where id=$roleId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Role
	function updateRole($roleName, $roleDescription, $roleEditId)
	{
		$qry	= " update role set name='$roleName',description='$roleDescription' where id=$roleEditId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}
	
	
	# Delete a Role
	function deleteRole($roleId)
	{
		$qry	=	" delete from role where id=$roleId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}


	#Delete Role Function Based on Main ID
	function deleteRoleFromFunction($roleId)
	{
		$qry	=	" delete from role_function where role_id=$roleId";
	
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	#Fetch All Module Records
	function fetchAllModuleRecords()
	{
		$qry	=	"select id, name from module";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Filter Function Records from Function table Based on Module Id
	function functionRecFilter($selModule)
	{
		$qry	=	"select id, name, module_id, url, form_add, form_edit, form_delete, form_print, form_confirm, form_reedit from function where module_id='$selModule'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Update Role Function
	function updateRoleFunction($rolFunctionId, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive)
	{
		$qry	=	" update role_function set access='$selAccess', form_add='$selAdd', form_edit='$selEdit', form_print='$selPrint', form_del='$selDelete', confirm='$selConfirm', active='$selActive' where id=$rolFunctionId";
 		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;	
	}


	#Add Role Function
	function addRoleFunction($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $lastInsertedId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg)
	{
		//echo $supplierdtflg;
		if ($selFunction!=162){
		$supplierdtflg="";
		}
		else{		
		$supplierdtflg="INV-FRN-RTE";
		}
		$qry	=	"insert into role_function (role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, submodule_id, frm_cpny_specific,supplierdtflg) values('$lastInsertedId', '$selModule', '$selFunction', '$selAccess', '$selAdd', '$selEdit', '$selPrint', '$selDelete', '$selConfirm','$selActive','$selReEdit', '$selSubModule', '$selCompanySpecific','$supplierdtflg')";

		//echo "--------------$qry";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}



function addRoleFunctionCopy($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $lastInsertedId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg)
	{
		
		$qry	=	"insert into role_function (role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, submodule_id, frm_cpny_specific,supplierdtflg,stflg) values('$lastInsertedId', '$selModule', '$selFunction', '$selAccess', '$selAdd', '$selEdit', '$selPrint', '$selDelete', '$selConfirm','$selActive','$selReEdit', '$selSubModule', '$selCompanySpecific','$supplierdtflg','$stflg')";

		//echo "--------------$qry";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}


function addRoleFunctionCopy_1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $lastInsertedId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg)
	{
		//echo $supplierdtflg;
		/*if ($selFunction!=162){
		$supplierdtflg="";
		}
		else{		
		$supplierdtflg="INV-FRN-RTE";
		}*/
		$qry	=	"insert into role_function (role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, submodule_id, frm_cpny_specific,supplierdtflg) values('$lastInsertedId', '$selModule', '$selFunction', '$selAccess', '$selAdd', '$selEdit', '$selPrint', '$selDelete', '$selConfirm','$selActive','$selReEdit', '$selSubModule', '$selCompanySpecific','$supplierdtflg')";

		//echo "--------------$qry";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}



function addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $lastInsertedId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg)
	{
		//echo $supplierdtflg;
		if ($selFunction!=162){
		$supplierdtflg="";
		}
		else{		

		}
		$qry	=	"insert into role_function (role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, submodule_id, frm_cpny_specific,supplierdtflg,stflg) values('$lastInsertedId', '$selModule', '$selFunction', '$selAccess', '$selAdd', '$selEdit', '$selPrint', '$selDelete', '$selConfirm','$selActive','$selReEdit', '$selSubModule', '$selCompanySpecific','$supplierdtflg','$stflg')";

		//echo "--------------$qry";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
function addSupDetails($selModule,$selFunction,$supplierdtflg,$selSubModule,$roleEditId,$supStatus)
{
$qry	=	"insert into role_function (role_id, module_id, function_id,submodule_id,supplierdtflg) values('$lastInsertedId','$selModule','$selFunction','$selSubModule','$supStatus')";

		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;


}
	#Delete Role Function Based on ID
	function deleteRoleFunction($rolMainId)
	{
		$qry	=	" delete from role_function where role_id=$rolMainId";
		//echo $qry;
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $result;
	}

	#Get distinct Module records
	function disitnctModuleIdRecs($roleMainId)
	{
		$qry	=	"select distinct a.module_id, b.name from role_function a, module b where a.module_id=b.id and role_id='$roleMainId' order by module_id asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#List all Role Function Records based on Role Id
	function fetchAllRoleFunctionRecords($roleMainId)
	{
		$qry	= "select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, submodule_id, form_reedit, frm_cpny_specific,supplierdtflg,stflg from role_function where role_id='$roleMainId'";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Find Module  Name
	function findModule($moduleId)
	{
		$qry	=	"select id,name from module where id=$moduleId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Find Function
	function findFunction($functionId)
	{
		$qry	=	"select id,name,url from function where id=$functionId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	#Find Roles
	function findRoleRecs($moduleId, $functionId, $roleId, $subModuleId)
	{
		$qry	= "select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, frm_cpny_specific,supplierdtflg from role_function where role_id='$roleId' and module_id='$moduleId' and function_id='$functionId' and submodule_id='$subModuleId' and (stflg is null || stflg=0)";
		//echo $qry."<br>";
		return $this->databaseConnect->getRecord($qry);
	}

	function findRoleRecssup($moduleId, $functionId, $roleId, $subModuleId)
	{
		$qry	= "select id, role_id, module_id, function_id, access, form_add, form_edit, form_print, form_del, confirm, active, form_reedit, frm_cpny_specific,supplierdtflg from role_function where role_id='$roleId' and module_id='$moduleId' and function_id='$functionId' and submodule_id='$subModuleId' and stflg=1";
		//echo $qry."<br>";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Check Role Linked with any user
	function checkRoleLinkedWithUser($roleId)
	{
		$qry	=	"select role_id from user where role_id='$roleId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	#Finding Name of the Role
	function findRoleName($roleId)
	{
		$rec = $this->find($roleId);
		return (sizeof($rec)>0)?$rec[1]:"";
	}	

	#Testing Listing Function records
	function fetchAllFunctionRecords()
	{
		$qry	= "select a.id, a.name, a.module_id, a.url, a.form_add, a.form_edit, a.form_delete, a.form_print, a.form_confirm, a.form_reedit, a.pmenu_id, b.name, a.frm_cpny_specific,a.extraflag from function a, module b where a.module_id=b.id order by a.module_id asc, a.pmenu_id asc, a.name asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Find Sub Menu Rec
	function findSubMenu($subMenuId)
	{
		$qry = "select name from submodule where id='$subMenuId'";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
	}
}