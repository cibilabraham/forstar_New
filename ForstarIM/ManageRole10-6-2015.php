<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$companySpecific = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	if ($accesscontrolObj->canCompanySpecific()) $companySpecific=true;
	//----------------------------------------------------------
		
	# Add Role Start 
	if ($p["cmdAddNew"]!="") {
		$addMode = true;
	}
	
	# Insert a Rec
	if ($p["cmdAdd"]!="") {		
		$roleName		= addSlash(trim($p["roleName"]));
		$roleDescription	= addSlash(trim($p["roleDescription"]));	
		$copyRoleId		= $p["copyRoleId"];	
		if ($roleName!="") {
			$roleRecIns	= $manageroleObj->addRole($roleName, $roleDescription, $copyRoleId);
		 if (!$copyRoleId) {	// Chk Copy From Selected
			if ($roleRecIns) {
				$lastInsertedId		= $databaseConnect->getLastInsertedId();
			}			
			$rowCount2	=	$p["hidRowCount2"];	
		
			for ($i=1; $i<=$rowCount2; $i++) {
					
				$rowCount1	=	$p["hidRowCount1_".$i];				
				for ($j=0; $j<=$rowCount1; $j++) {
					$selModule	=	$p["moduleId_".$i];
					$selSubModule   = 	$p["subModuleId_".$i];
					$selFunction	=	$p["functionId_".$i."_".$j];
				
					$selAccess = ($p["selAccess_".$i."_".$j]=="")?N:$p["selAccess_".$i."_".$j];
					$selAdd	   = ($p["selAdd_".$i."_".$j]=="")?N:$p["selAdd_".$i."_".$j];
					$selEdit   = ($p["selEdit_".$i."_".$j]=="")?N:$p["selEdit_".$i."_".$j];
					$selDelete = ($p["selDelete_".$i."_".$j]=="")?N:$p["selDelete_".$i."_".$j];
					$selPrint  = ($p["selPrint_".$i."_".$j]=="")?N:$p["selPrint_".$i."_".$j];
					$selConfirm= ($p["selConfirm_".$i."_".$j]=="")?N:$p["selConfirm_".$i."_".$j];
					$selActive = ($p["selActive_".$i."_".$j]=="")?Y:$p["selActive_".$i."_".$j];
					$selReEdit = ($p["selReEdit_".$i."_".$j]=="")?N:$p["selReEdit_".$i."_".$j];	
					$selCompanySpecific = ($p["selCompanySpecific_".$i."_".$j]=="")?N:$p["selCompanySpecific_".$i."_".$j];
					if ($selFunction!="" && $lastInsertedId!="") {
						$roleFunctionRecIns=$manageroleObj->addRoleFunction($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $lastInsertedId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg);
					}
				}
			}		$flgsd=$p["rowextraflag_".$i."_".$j];
					if ($flgsd=="sd")
					{						
					$selAccess1=$p["selAccess1"];					
					$selAdd1	= $p["selAdd1"];
					$selEdit1   = $p["selEdit1"];
					$selDelete1 = $p["selDelete1"];
					$selPrint1  = $p["selPrint1"];
					$selConfirm1= $p["selConfirm1"];
					$selActive1 = $p["selActive1"];
					$selReEdit1 = $p["selReEdit1"];	
					$selCompanySpecific1 = $p["selCompanySpecific1"];
					$selAccess2=$p["selAccess2"];
					$selAdd2	= $p["selAdd2"];
					$selEdit2   = $p["selEdit2"];
					$selDelete2 = $p["selDelete2"];
					$selPrint2  = $p["selPrint2"];
					$selConfirm2= $p["selConfirm2"];
					$selActive2 = $p["selActive2"];
					$selReEdit2 = $p["selReEdit2"];	
					$selCompanySpecific2 = $p["selCompanySpecific2"];
					$selAccess3=$p["selAccess3"];
					$selAdd3	= $p["selAdd3"];
					$selEdit3   = $p["selEdit3"];
					$selDelete3 = $p["selDelete3"];
					$selPrint3  = $p["selPrint3"];
					$selConfirm3= $p["selConfirm3"];
					$selActive3 = $p["selActive3"];
					$selReEdit3 = $p["selReEdit3"];	
					$selCompanySpecific3 = $p["selCompanySpecific3"];
					if (($selAccess1=="") && ($selAdd1=="") && ($selEdit1=="") && ($selDelete1=="") && ($selPrint1=="") && ($selConfirm1=="") && ($selActive1=="") && ($selReEdit1=="") && ($selCompanySpecific1=="") && ($selAccess2=="") && ($selAdd2=="") && ($selEdit2=="") && ($selDelete2=="") && ($selPrint2=="") && ($selConfirm2=="") && ($selActive2=="") && ($selReEdit2=="") && ($selCompanySpecific2=="") && ($selAccess3=="") && ($selAdd3=="") && ($selEdit3=="") && ($selDelete3=="") && ($selPrint3=="") && ($selConfirm3=="") && ($selActive3=="") && ($selReEdit3=="") && ($selCompanySpecific3=="")){						
					$stflg=1;
					$supplierdtflg="INV";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					$supplierdtflg="FRN";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					$supplierdtflg="RTE";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					}
					else{						
					$selAccess1 = ($p["selAccess1"]=="")?N:$p["selAccess1"];
					$selAdd1	= ($p["selAdd1"]=="")?N:$p["selAdd1"];
					$selEdit1   = ($p["selEdit1"]=="")?N:$p["selEdit1"];
					$selDelete1 = ($p["selDelete1"]=="")?N:$p["selDelete1"];
					$selPrint1  = ($p["selPrint1"]=="")?N:$p["selPrint1"];
					$selConfirm1= ($p["selConfirm1"]=="")?N:$p["selConfirm1"];
					$selActive1 = ($p["selActive1"]=="")?Y:$p["selActive1"];
					$selReEdit1 = ($p["selReEdit1"]=="")?N:$p["selReEdit1"];	
					$selCompanySpecific1 = ($p["selCompanySpecific1"]=="")?N:$p["selCompanySpecific1"];
					$supplierdtflg="INV";
					$stflg=1;					
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess1, $selAdd1, $selEdit1, $selDelete1, $selPrint1, $selConfirm1, $selActive1, $roleEditId, $selReEdit1, $selSubModule, $selCompanySpecific1,$supplierdtflg,$stflg);				
					$selAccess2 = ($p["selAccess2"]=="")?N:$p["selAccess2"];
					$selAdd2	= ($p["selAdd2"]=="")?N:$p["selAdd2"];
					$selEdit2   = ($p["selEdit2"]=="")?N:$p["selEdit2"];
					$selDelete2 = ($p["selDelete2"]=="")?N:$p["selDelete2"];
					$selPrint2  = ($p["selPrint2"]=="")?N:$p["selPrint2"];
					$selConfirm2= ($p["selConfirm2"]=="")?N:$p["selConfirm2"];
					$selActive2 = ($p["selActive2"]=="")?Y:$p["selActive2"];
					$selReEdit2 = ($p["selReEdit2"]=="")?N:$p["selReEdit2"];	
					$selCompanySpecific2 = ($p["selCompanySpecific2"]=="")?N:$p["selCompanySpecific2"];
					$supplierdtflg="FRN";
					$stflg=1;					
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess2, $selAdd2, $selEdit2, $selDelete2, $selPrint2, $selConfirm2, $selActive2, $roleEditId, $selReEdit2, $selSubModule, $selCompanySpecific2,$supplierdtflg,$stflg);					
					$selAccess3 = ($p["selAccess3"]=="")?N:$p["selAccess3"];
					$selAdd3	= ($p["selAdd3"]=="")?N:$p["selAdd3"];
					$selEdit3   = ($p["selEdit3"]=="")?N:$p["selEdit3"];
					$selDelete3 = ($p["selDelete3"]=="")?N:$p["selDelete3"];
					$selPrint3  = ($p["selPrint3"]=="")?N:$p["selPrint3"];
					$selConfirm3= ($p["selConfirm3"]=="")?N:$p["selConfirm3"];
					$selActive3 = ($p["selActive3"]=="")?Y:$p["selActive3"];
					$selReEdit3 = ($p["selReEdit3"]=="")?N:$p["selReEdit3"];	
					$selCompanySpecific3 = ($p["selCompanySpecific3"]=="")?N:$p["selCompanySpecific3"];
					$supplierdtflg="RTE";
					$stflg=1;				
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess3, $selAdd3, $selEdit3, $selDelete3, $selPrint3, $selConfirm3, $selActive3, $roleEditId, $selReEdit3, $selSubModule, $selCompanySpecific3,$supplierdtflg,$stflg);
					}}























		} 
		
		
		
		
		//Copy Rate List End
				
			if ($roleRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddRole);
				//$sessObj->createSession("nextPage",$url_afterAddRole);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddRole;
			}
			$roleRecIns	=	false;
		}
	}

	# Edit Role
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$roleRec	=	$manageroleObj->find($editId);
		$editRoleId	=	$roleRec[0];
		$editRoleName	=	stripSlash($roleRec[1]);
		$editRoleDescr	=	stripSlash($roleRec[2]);
	}
	
	#Update a Role	
	if ($p["cmdSaveChange"]!="") {		
		$roleEditId		= $p["hidRoleId"];		
		$roleName		= addSlash(trim($p["roleName"]));
		$roleDescription	= addSlash(trim($p["roleDescription"]));
		
		if ($roleName!="" && $roleEditId!="") {	
			$roleFunctionRecDel	= $manageroleObj->deleteRoleFunction($roleEditId);
				
			$roleRecUptd = $manageroleObj->updateRole($roleName,$roleDescription,$roleEditId);	
			$rowCount2	=	$p["hidRowCount2"];

			if ($p["supdChkbx1"]!="")
			 {
				$supplierdtflg="INV-FRN-RTE";
			 }
			 else if (($p["supdChkbx2"]!="") && ($p["supdChkbx3"]!="") && ($p["supdChkbx4"]!=""))
			{
				$supplierdtflg="INV-FRN-RTE";
			}
			 else if (($p["supdChkbx2"]!="") && ($p["supdChkbx3"]!=""))
			 {
				$supplierdtflg="INV-FRN";

			 }else if (($p["supdChkbx2"]!="") && ($p["supdChkbx4"]!=""))
			 {

				$supplierdtflg="INV-RTE";
			 }
			else if (($p["supdChkbx3"]!="") && ($p["supdChkbx4"]!=""))
			 {

				$supplierdtflg="FRN-RTE";
			 }
			
			else if ($p["supdChkbx3"]!="")
			 {

				$supplierdtflg="FRN";
			 }
			 else if ($p["supdChkbx4"]!="")
			 {

				$supplierdtflg="RTE";
			 }
			 else if ($p["supdChkbx2"]!="")
			 {

				$supplierdtflg="INV";
			 }
			 //echo "SF".$supplierdtflg;
			for ($i=1; $i<=$rowCount2; $i++) {
				$rowCount1	=	$p["hidRowCount1_".$i];
				for ($j=0; $j<=$rowCount1; $j++) {				
					$selModule	=	$p["moduleId_".$i];
					$selSubModule   = 	$p["subModuleId_".$i];
					$selFunction	=	$p["functionId_".$i."_".$j];				
					$selAccess  = ($p["selAccess_".$i."_".$j]=="")?N:$p["selAccess_".$i."_".$j];
					$selAdd	    = ($p["selAdd_".$i."_".$j]=="")?N:$p["selAdd_".$i."_".$j];
					$selEdit    = ($p["selEdit_".$i."_".$j]=="")?N:$p["selEdit_".$i."_".$j];
					$selDelete  = ($p["selDelete_".$i."_".$j]=="")?N:$p["selDelete_".$i."_".$j];
					$selPrint   = ($p["selPrint_".$i."_".$j]=="")?N:$p["selPrint_".$i."_".$j];
					$selConfirm = ($p["selConfirm_".$i."_".$j]=="")?N:$p["selConfirm_".$i."_".$j];
					$selActive  = ($p["selActive_".$i."_".$j]=="")?N:$p["selActive_".$i."_".$j];
					$selReEdit  = ($p["selReEdit_".$i."_".$j]=="")?N:$p["selReEdit_".$i."_".$j];
					$selCompanySpecific = ($p["selCompanySpecific_".$i."_".$j]=="")?N:$p["selCompanySpecific_".$i."_".$j];
					if ($roleEditId!="" && $selFunction!="") {
						$roleFunctionRecIns=$manageroleObj->addRoleFunction($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg);
					}

					$flgsd=$p["rowextraflag_".$i."_".$j];
					if ($flgsd=="sd")
				//if ($selFunction==162)
					 {
					//$selModule	=	$p["moduleId_".$i];
					//$selSubModule   = 	$p["subModuleId_".$i];
					//$selFunction	=	$p["functionId_".$i."_".$j];
					$selAccess1=$p["selAccess1"];
					
					$selAdd1	= $p["selAdd1"];
					$selEdit1   = $p["selEdit1"];
					$selDelete1 = $p["selDelete1"];
					$selPrint1  = $p["selPrint1"];
					$selConfirm1= $p["selConfirm1"];
					$selActive1 = $p["selActive1"];
					$selReEdit1 = $p["selReEdit1"];	
					$selCompanySpecific1 = $p["selCompanySpecific1"];
					$selAccess2=$p["selAccess2"];
					$selAdd2	= $p["selAdd2"];
					$selEdit2   = $p["selEdit2"];
					$selDelete2 = $p["selDelete2"];
					$selPrint2  = $p["selPrint2"];
					$selConfirm2= $p["selConfirm2"];
					$selActive2 = $p["selActive2"];
					$selReEdit2 = $p["selReEdit2"];	
					$selCompanySpecific2 = $p["selCompanySpecific2"];
					$selAccess3=$p["selAccess3"];
					$selAdd3	= $p["selAdd3"];
					$selEdit3   = $p["selEdit3"];
					$selDelete3 = $p["selDelete3"];
					$selPrint3  = $p["selPrint3"];
					$selConfirm3= $p["selConfirm3"];
					$selActive3 = $p["selActive3"];
					$selReEdit3 = $p["selReEdit3"];	
					$selCompanySpecific3 = $p["selCompanySpecific3"];

					if (($selAccess1=="") && ($selAdd1=="") && ($selEdit1=="") && ($selDelete1=="") && ($selPrint1=="") && ($selConfirm1=="") && ($selActive1=="") && ($selReEdit1=="") && ($selCompanySpecific1=="") && ($selAccess2=="") && ($selAdd2=="") && ($selEdit2=="") && ($selDelete2=="") && ($selPrint2=="") && ($selConfirm2=="") && ($selActive2=="") && ($selReEdit2=="") && ($selCompanySpecific2=="") && ($selAccess3=="") && ($selAdd3=="") && ($selEdit3=="") && ($selDelete3=="") && ($selPrint3=="") && ($selConfirm3=="") && ($selActive3=="") && ($selReEdit3=="") && ($selCompanySpecific3=="")){
						//echo "entered";
						?>
						<script language="javascript">
							//alert("hai1");
							</script>
					<?php $stflg=1;
					$supplierdtflg="INV";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					$supplierdtflg="FRN";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					$supplierdtflg="RTE";
					$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess, $selAdd, $selEdit, $selDelete, $selPrint, $selConfirm, $selActive, $roleEditId, $selReEdit, $selSubModule, $selCompanySpecific,$supplierdtflg,$stflg);
					}
					else{
				?>
						<script language="javascript">
							//alert("hai");
							</script>
					<?php
					$selAccess1 = ($p["selAccess1"]=="")?N:$p["selAccess1"];
					$selAdd1	= ($p["selAdd1"]=="")?N:$p["selAdd1"];
					$selEdit1   = ($p["selEdit1"]=="")?N:$p["selEdit1"];
					$selDelete1 = ($p["selDelete1"]=="")?N:$p["selDelete1"];
					$selPrint1  = ($p["selPrint1"]=="")?N:$p["selPrint1"];
					$selConfirm1= ($p["selConfirm1"]=="")?N:$p["selConfirm1"];
					$selActive1 = ($p["selActive1"]=="")?Y:$p["selActive1"];
					$selReEdit1 = ($p["selReEdit1"]=="")?N:$p["selReEdit1"];	
					$selCompanySpecific1 = ($p["selCompanySpecific1"]=="")?N:$p["selCompanySpecific1"];
					$supplierdtflg="INV";
					$stflg=1;

					//if ($selFunction!="" && $lastInsertedId!="") {
						$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess1, $selAdd1, $selEdit1, $selDelete1, $selPrint1, $selConfirm1, $selActive1, $roleEditId, $selReEdit1, $selSubModule, $selCompanySpecific1,$supplierdtflg,$stflg);
					//}
					$selAccess2 = ($p["selAccess2"]=="")?N:$p["selAccess2"];
					$selAdd2	= ($p["selAdd2"]=="")?N:$p["selAdd2"];
					$selEdit2   = ($p["selEdit2"]=="")?N:$p["selEdit2"];
					$selDelete2 = ($p["selDelete2"]=="")?N:$p["selDelete2"];
					$selPrint2  = ($p["selPrint2"]=="")?N:$p["selPrint2"];
					$selConfirm2= ($p["selConfirm2"]=="")?N:$p["selConfirm2"];
					$selActive2 = ($p["selActive2"]=="")?Y:$p["selActive2"];
					$selReEdit2 = ($p["selReEdit2"]=="")?N:$p["selReEdit2"];	
					$selCompanySpecific2 = ($p["selCompanySpecific2"]=="")?N:$p["selCompanySpecific2"];
					$supplierdtflg="FRN";
					$stflg=1;

					//if ($selFunction!="" && $lastInsertedId!="") {
						$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess2, $selAdd2, $selEdit2, $selDelete2, $selPrint2, $selConfirm2, $selActive2, $roleEditId, $selReEdit2, $selSubModule, $selCompanySpecific2,$supplierdtflg,$stflg);
					//}
					$selAccess3 = ($p["selAccess3"]=="")?N:$p["selAccess3"];
					$selAdd3	= ($p["selAdd3"]=="")?N:$p["selAdd3"];
					$selEdit3   = ($p["selEdit3"]=="")?N:$p["selEdit3"];
					$selDelete3 = ($p["selDelete3"]=="")?N:$p["selDelete3"];
					$selPrint3  = ($p["selPrint3"]=="")?N:$p["selPrint3"];
					$selConfirm3= ($p["selConfirm3"]=="")?N:$p["selConfirm3"];
					$selActive3 = ($p["selActive3"]=="")?Y:$p["selActive3"];
					$selReEdit3 = ($p["selReEdit3"]=="")?N:$p["selReEdit3"];	
					$selCompanySpecific3 = ($p["selCompanySpecific3"]=="")?N:$p["selCompanySpecific3"];
					$supplierdtflg="RTE";
					$stflg=1;

					//if ($selFunction!="" && $lastInsertedId!="") {
						$roleFunctionRecIns=$manageroleObj->addRoleFunction1($selModule, $selFunction, $selAccess3, $selAdd3, $selEdit3, $selDelete3, $selPrint3, $selConfirm3, $selActive3, $roleEditId, $selReEdit3, $selSubModule, $selCompanySpecific3,$supplierdtflg,$stflg);

					}



					//} 





					}












				}
			}








		}
	
		if ($roleRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateRole);
			//$sessObj->createSession("nextPage",$url_afterUpdateRole);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateRole;
		}
		$roleRecUptd	=	false;
	}
	
	# Delete Role
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$roleId	=	$p["delId_".$i];
			if ($roleId!="") {				
				$isRecordExisitInUser =	$manageroleObj->checkRoleLinkedWithUser($roleId);	
				if (sizeof($isRecordExisitInUser)==0) {
					$roleRecDel	=	$manageroleObj->deleteRole($roleId);
					#Delete role from role_function table
					$roleRecDel	=	$manageroleObj->deleteRoleFromFunction($roleId);
				}
			}
		}
		if ($roleRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRole);
			$sessObj->createSession("nextPage",$url_afterDelRole);
		} else {
			$errDel	=	$msg_failDelRole;
		}
		$roleRecDel	=	false;
	}

	#List All Role
	$roleRecords		=	$manageroleObj->fetchAllRecords();
	$roleRecordsSize	=	sizeof($roleRecords);

	#Get All Function Records
	$getFunctionRecords = $manageroleObj->fetchAllFunctionRecords();

	if ($editMode)	$heading	= $label_editRole;
	else 		$heading	= $label_addRole;	

	//$help_lnk="help/hlp_GradeMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/managerole.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmManageRole" action="ManageRole.php" method="post">	
  <table cellspacing="0"  align="center" cellpadding="0" width="100%">
    <tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
    <tr> 
      <td height="10" align="center" class="err1" > 
        <? if($err!="" ){?>
        <?=$err;?>
        <? }?>
      </td>
    </tr>
    <?
			if( $editMode || $addMode)
			{
		?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; 
                    <?=$heading;?>
                  </td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > 
				  <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td colspan="2" align="center"> <input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');"> 
                          &nbsp;&nbsp;
                          <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateAddRole(document.frmManageRole);" /></td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRole(document.frmManageRole);">                        </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidRoleId" value="<?=$editRoleId;?>">
                      <tr>
                        <td colspan="2" nowrap height="10" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="2" nowrap class="fieldName" align="center">
			<table width="200">
                          <tr>
                            <td class="fieldName" nowrap >*  Name : </td>
			    <td>
				<INPUT NAME="roleName" TYPE="text" id="roleName" value="<?=$editRoleName;?>" size="23">
				</td>
                          </tr>
                          <tr>
                            <td  height="10" class="fieldName" nowrap="nowrap"> Description : </td>
			     <td  height="10" ><textarea name="roleDescription" id="roleDescription"><?=$editRoleDescr?></textarea></td>
                          </tr>
<?
	if ($addMode!="") {
?>
			 <tr>
                            <td class="fieldName" nowrap >Copy From: </td>
			    <td>
				 <select name="copyRoleId" id="copyRoleId" title="Click here if you want to copy all data from the existing Role" onchange="displayRoleFunctionList();">
				<option value="">-- Select --</option>
				<?php
					foreach($roleRecords as $rrec) {
						$roleRecId = $rrec[0];
						$roleRecName = stripSlash($rrec[1]);				
				?>
				<option value="<?=$roleRecId?>"><?=$roleRecName?></option>
				<?
					}
				?>
				 </select>
			    </td>
                          </tr>	
<?
	}
?>
                    </table></td>
                 </tr>
                      <tr>
                        <td nowrap>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
          <tr id="roleFnHead">
                <td colspan="2" nowrap class="fieldName" >Please define the access level for this role by selecting from the following list:</td>
          </tr>
          <tr id="roleFnList"> 
                <td colspan="2" nowrap align="center" style="padding-left:10px;padding-right:10px;">
		<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
                     <!--tr bgcolor="#f2f2f2" align="center">
                         <td class="listing-head" height="30">Module</td>
                         <td class="listing-head">Function</td>
                         <td class="listing-head">Access</td>
                         <td class="listing-head">Add</td>
                         <td class="listing-head">Edit</td>
                         <td class="listing-head">Del</td>
                         <td class="listing-head">Print</td>
                         <td class="listing-head">Confirm</td>
                         <td class="listing-head">Active</td>
			<td class="listing-head">&nbsp;</td>
                      </tr>-->
		<?
		$k=0;
		$prevModuleId=0;
		$prevPmenu_id = 0;

		//$j=0;
		$count = 0;
		foreach ($getFunctionRecords as $gfr) {
			//$j++;
			$functionId = $gfr[0];
			$moduleId = $gfr[2];
			$pmenu_id = $gfr[10];
			$functionName = $gfr[1];
			$extraflag1=$gfr[13];
			$extraflag=$gfr[13];
			//echo "**********8$extraflag1";
			if ($extraflag1=="sd")
						{
							//echo "hai";
							$supdt=$functionId;
							$roleRecsup = $manageroleObj->findRoleRecssup($moduleId, $supdt, $editId, $pmenu_id);
							//echo $supdt;
						}


foreach ($roleRecsup as $rrS) {
							//echo "entered";
						if ($rrS[13]=="INV")
						{
						if($rrS[4]=='Y'){	$selAccess1	= 	"Checked";
                                                  $flagInvchecked="Checked";
//echo "hai";
//echo $flagInvchecked;
}
						else {$selAccess1	=	"";}

						if ($rrS[5]=='Y')	$selAdd1		=	"Checked";
						else $selAdd1	=	"";

						if ($rrS[6]=='Y')	$selEdit1	=	"Checked";
						else $selEdit1	= 	"";

						if ($rrS[7]=='Y') $selPrint1	=	"Checked";
						else $selPrint1	=	"";

						if ($rrS[8]=='Y')	$selDelete1	=	"Checked";
						else $selDelete1	=	"";

						if ($rrS[9]=='Y')	$selConfirm1	=	"Checked";
						else $selConfirm1	= "";

						if ($rrS[10]=='Y')	$selActive1	=	"Checked";
						else $selActive1	= "";

						if ($rrS[11]=='Y')	$selReEdit1	=	"Checked";
						else $selReEdit1	= "";
						
						if ($rrS[12]=='Y')	$selCompanySpecific1	=	"Checked";
						else $selCompanySpecific1	= "";
$selAll1 = "";
						if ($rrS[4]=='Y' && $rrS[5]=='Y' && $rrS[6]=='Y' && $rrS[7]=='Y' && $rrS[8]=='Y' && $rrS[9]=='Y' && $rrS[10]=='Y' && $rrS[11]=='Y' && $rrS[12]=='Y') $selAll1 = "Checked";
						else $selAll1 = "";








							}

							if ($rrS[13]=="FRN")
						{
						if($rrS[4]=='Y')	{$selAccess2	= 	"Checked";

$flagFRNchecked="Checked";
}
						else {$selAccess2	=	"";}

						if ($rrS[5]=='Y')	$selAdd2		=	"Checked";
						else $selAdd2	=	"";

						if ($rrS[6]=='Y')	$selEdit2	=	"Checked";
						else $selEdit2	= 	"";

						if ($rrS[7]=='Y') $selPrint2	=	"Checked";
						else $selPrint2	=	"";

						if ($rrS[8]=='Y')	$selDelete2	=	"Checked";
						else $selDelete2	=	"";

						if ($rrS[9]=='Y')	$selConfirm2	=	"Checked";
						else $selConfirm2	= "";

						if ($rrS[10]=='Y')	$selActive2	=	"Checked";
						else $selActive2	= "";

						if ($rrS[11]=='Y')	$selReEdit2	=	"Checked";
						else $selReEdit2	= "";
						
						if ($rrS[12]=='Y')	$selCompanySpecific2	=	"Checked";
						else $selCompanySpecific2	= "";

$selAll2 = "";
						if ($rrS[4]=='Y' && $rrS[5]=='Y' && $rrS[6]=='Y' && $rrS[7]=='Y' && $rrS[8]=='Y' && $rrS[9]=='Y' && $rrS[10]=='Y' && $rrS[11]=='Y' && $rrS[12]=='Y') $selAll2 = "Checked";
						else $selAll2 = "";


							}

							if ($rrS[13]=="RTE")
						{
						if($rrS[4]=='Y'){	$selAccess3	= 	"Checked";
                                                 $flagRTEchecked="Checked";


}
						else {$selAccess3	=	"";}

						if ($rrS[5]=='Y')	$selAdd3		=	"Checked";
						else $selAdd3	=	"";

						if ($rrS[6]=='Y')	$selEdit3	=	"Checked";
						else $selEdit3	= 	"";

						if ($rrS[7]=='Y') $selPrint3	=	"Checked";
						else $selPrint3	=	"";

						if ($rrS[8]=='Y')	$selDelete3	=	"Checked";
						else $selDelete3	=	"";

						if ($rrS[9]=='Y')	$selConfirm3	=	"Checked";
						else $selConfirm3	= "";

						if ($rrS[10]=='Y')	$selActive3	=	"Checked";
						else $selActive3	= "";

						if ($rrS[11]=='Y')	$selReEdit3	=	"Checked";
						else $selReEdit3	= "";
						
						if ($rrS[12]=='Y')	$selCompanySpecific3	=	"Checked";
						else $selCompanySpecific3	= "";

$selAll3 = "";
						if ($rrS[4]=='Y' && $rrS[5]=='Y' && $rrS[6]=='Y' && $rrS[7]=='Y' && $rrS[8]=='Y' && $rrS[9]=='Y' && $rrS[10]=='Y' && $rrS[11]=='Y' && $rrS[12]=='Y') $selAll3 = "Checked";
						else $selAll3 = "";


							
						}

						

						}











			$selActive	=	"";
			$selFunction	=	"";
			$moduleName = "";

			if ($prevModuleId!=$moduleId || $prevPmenu_id!=$pmenu_id) {

				if ( $k > 0) {

			?>
				<input type="hidden" name="hidRowCount1_<?=$k?>" id="hidRowCount1_<?=$k?>" value="<?=$j-1;?>">
					<?
					}
					$j=1;
				 	$k++;
					$moduleName = $gfr[11];
					$subMenu = $manageroleObj->findSubMenu($pmenu_id);

					$selActive	=	"";
					$selFunction	=	"";

					if ($editMode==true) {
						$roleRec = $manageroleObj->findRoleRecs($moduleId, 0, $editId, $pmenu_id);
						$roleFunctionId	=	$roleRec[0];

						if ($roleRec[3]==0 && $roleRec[3]!="") $selFunction = "Checked";
						else  $selFunction	=	"";
						if($roleRec[4]=='Y')	$selAccess	= 	"Checked";
						else $selAccess	=	"";

						if ($roleRec[5]=='Y')	$selAdd		=	"Checked";
						else $selAdd	=	"";

						if ($roleRec[6]=='Y')	$selEdit	=	"Checked";
						else $selEdit	= 	"";

						if ($roleRec[7]=='Y') $selPrint	=	"Checked";
						else $selPrint	=	"";

						if ($roleRec[8]=='Y')	$selDelete	=	"Checked";
						else $selDelete	=	"";

						if ($roleRec[9]=='Y')	$selConfirm	=	"Checked";
						else $selConfirm	= "";

						if ($roleRec[10]=='Y')	$selActive	=	"Checked";
						else $selActive	= "";

						if ($roleRec[11]=='Y')	$selReEdit	=	"Checked";
						else $selReEdit	= "";
						
						if ($roleRec[12]=='Y')	$selCompanySpecific	=	"Checked";
						else $selCompanySpecific	= "";
						
						$selAll = "";
						if ($roleRec[3]==0 && $roleRec[3]!="" && $roleRec[5]=='Y' && $roleRec[6]=='Y' && $roleRec[7]=='Y' && $roleRec[8]=='Y' && $roleRec[9]=='Y' && $roleRec[11]=='Y' && $roleRec[12]=='Y') $selAll = "Checked";
						else $selAll = "";
						//echo $roleRec[3];
						//if ($roleRec[3]==162){
						
						
						















					}
			?>
			<tr bgcolor="#f2f2f2" align="center">
                         <td class="listing-head" height="30">Module&nbsp;&nbsp;</td>
			<td class="listing-head" height="30">Sub Menu</td>
                         <td class="listing-head">Function</td>
                         <td class="listing-head" style="padding-left:5px; padding-right:5px;">Access</td>
                         <td class="listing-head" style="padding-left:10px; padding-right:10px;">Add</td>
                         <td class="listing-head" style="padding-left:10px; padding-right:10px;">Edit</td>
                         <td class="listing-head" style="padding-left:10px; padding-right:10px;">Del</td>
                         <td class="listing-head" style="padding-left:10px; padding-right:10px;">Print</td>
                         <td class="listing-head" style="padding-left:3px; padding-right:3px;">Confirm</td>
                         <td class="listing-head" style="padding-left:3px; padding-right:3px;">Re-Edit</td>
			<td class="listing-head" style="padding-left:3px; padding-right:3px;">Company <br/>Specific</td>
                         <!--td class="listing-head">Active</td-->
			 <td class="listing-head" style="padding-left:3px; padding-right:3px;">Select All</td>
                      </tr>
			<tr bgcolor="#FFFFFF">
                        <td class="listing-item" style="padding-left:20px;" height="25" nowrap>
			<div id ="t_<?=$k?>"><a href="###"  onClick="showTableRow(<?=$k?>, '<?=$moduleName?>');" class="expandLink">+</a>&nbsp;<?=$moduleName?></div><?//=$moduleName?>
			<input type="hidden" name="moduleId_<?=$k?>" value="<?=$moduleId?>"><input type="hidden" name="subModuleId_<?=$k?>" value="<?=$pmenu_id?>"></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" height="25"><?=$subMenu?></td>
                        <td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input name="functionId_<?=$k?>_0" type="checkbox" id="functionId_<?=$k?>_0" value="0" <?=$selFunction?> class="chkBox">&nbsp;All<input type="hidden" name="hidRoleFunctionId_<?=$k?>_0" value="<?=$roleFunctionId?>"></td>
                        <td class="listing-item" align="center"><input name="selAccess_<?=$k?>_0" type="checkbox" id="selAccess_<?=$k?>_0" value="Y" <?=$selAccess?> class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selAdd_<?=$k?>_0" type="checkbox" id="selAdd_<?=$k?>_0" value="Y"  onclick="checkSel(<?=$k?>,0);" <?=$selAdd?> class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selEdit_<?=$k?>_0" type="checkbox" id="selEdit_<?=$k?>_0" value="Y" <?=$selEdit?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selDelete_<?=$k?>_0" type="checkbox" id="selDelete_<?=$k?>_0" value="Y" <?=$selDelete?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selPrint_<?=$k?>_0" type="checkbox" id="selPrint_<?=$k?>_0" value="Y" <?=$selPrint?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selConfirm_<?=$k?>_0" type="checkbox" id="selConfirm_<?=$k?>_0" value="Y" <?=$selConfirm?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
                        <td class="listing-item" align="center"><input name="selReEdit_<?=$k?>_0" type="checkbox" id="selReEdit_<?=$k?>_0" value="Y" <?=$selReEdit?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
			 <td class="listing-item" align="center"><input name="selCompanySpecific_<?=$k?>_0" type="checkbox" id="selCompanySpecific_<?=$k?>_0" value="Y" <?=$selCompanySpecific?> onclick="checkSel(<?=$k?>,0);" class="chkBox"></td>
                        <!--td class="listing-item" align="center"><input name="selActive_<?=$k?>_0" type="checkbox" id="selActive_<?=$k?>_0" onclick="checkSel(<?=$k?>,0);" value="Y" <?=$selActive?>></td-->
			<td class="listing-item" align="center">
				<input type='checkbox' name='CheckAll_<?=$k?>_0' id='CheckAll_<?=$k?>_0' onclick="selAll(<?=$k?>,0);" <?=$selAll?> class="chkBox">
			</td>
                        </tr>
<!--tr bgcolor="#FFFFFF" align="left"><TD colspan="11" style="padding-left:10px; padding-right:10px;">
	<div id ="t_<?=$k?>"><a href="##"  onClick="showTableRow(<?=$k?>);" class="link1">Expand(+)</a></div>
	<?// echo "$k,$j"?>
</TD></tr-->
			<?
				}

				$selActive = "";
				//if($addMode==true) $selActive	=	"Checked";
				if ($editMode==true) {
					$roleRec = $manageroleObj->findRoleRecs($moduleId, $functionId, $editId, $pmenu_id);
					$roleFunctionId	=	$roleRec[0];

					if ($roleRec[3]==$functionId) $selFunction = "Checked";
					else $selFunction	=	"";

					if ($roleRec[4]=='Y')	$selAccess	= 	"Checked";
					else $selAccess	=	"";

					if ($roleRec[5]=='Y')	$selAdd		=	"Checked";
					else $selAdd	=	"";

					if ($roleRec[6]=='Y')	$selEdit	=	"Checked";
					else $selEdit	= 	"";

					if ($roleRec[7]=='Y') $selPrint	=	"Checked";
					else $selPrint	=	"";

					if ($roleRec[8]=='Y')	$selDelete	=	"Checked";
					else $selDelete	=	"";

					if ($roleRec[9]=='Y')	$selConfirm	=	"Checked";
					else $selConfirm	= "";

					if ($roleRec[10]=='Y')	$selActive	=	"Checked";
					else $selActive	= "";

					if ($roleRec[11]=='Y')	$selReEdit	=	"Checked";
					else $selReEdit	= "";
					
					if ($roleRec[12]=='Y')	$selCompanySpecific	=	"Checked";
					else $selCompanySpecific	= "";

					// if ($functionId==162){
						  if ($extraflag=="sd"){
						$supfilterflag=$roleRec[13];
						//echo $supfilterflag;
						//echo "hai";
					 }
					if ($functionId==162){

					}

				}

$arrsuppdtflag=explode("-",$supfilterflag);
$flag=0;
$flag2=0;
$flag3=0;
$flag4=0;
if ((in_array("FRN",$arrsuppdtflag))&& (in_array("INV",$arrsuppdtflag)) && (in_array("RTE",$arrsuppdtflag)))
					{
					$flag=1;
					$flag2=2;
					$flag3=3;
					$flag4=4;
	
					}
else if ((in_array("FRN",$arrsuppdtflag))&& (in_array("INV",$arrsuppdtflag)))
	{
	$flag2=2;
	$flag3=3;
	}
	else if ((in_array("FRN",$arrsuppdtflag))&& (in_array("RTE",$arrsuppdtflag)))
	{
	$flag3=3;
	$flag4=4;
	}
	else if ((in_array("INV",$arrsuppdtflag))&& (in_array("RTE",$arrsuppdtflag)))
	{
	$flag4=4;
	$flag2=2;
	}
	else if ((in_array("FRN",$arrsuppdtflag)))
	{
		$flag3=3;
	}
	else if ((in_array("INV",$arrsuppdtflag)))
	{
		$flag2=2;
	}
		else if ((in_array("RTE",$arrsuppdtflag)))
	{
		$flag4=4;
	}


				$selAll = "";
				if ($roleRec[3]==$functionId && $roleRec[5]=='Y' && $roleRec[6]=='Y' && $roleRec[7]=='Y' && $roleRec[8]=='Y' && $roleRec[9]=='Y' && $roleRec[11]=='Y' && $roleRec[12]=='Y') $selAll = "Checked";
				else $selAll = "";

				$formAdd 	= $fr[4];
				$displayAdd = "";
				if ($formAdd=='N') {
					$displayAdd = "hidden";
				} else {
					$displayAdd = "Checkbox";
				}

				$formEdit 	= $fr[5];
				$displayEdit = "";
				if ($formEdit=='N') {
					$displayEdit = "hidden";
				} else {
					$displayEdit = "Checkbox";
				}

				$formDelete	= $fr[6];
				$displayDelete = "";
				if ($formDelete=='N') {
					$displayDelete = "hidden";
				} else {
					$displayDelete = "Checkbox";
				}

				$formPrint 	= $fr[7];
				$displayPrint = "";
				if ($formPrint=='N') {
					$displayPrint = "hidden";
				} else {
					$displayPrint = "Checkbox";
				}

				$formConfirm = $fr[8];
				$displayConfirm = "";
				if ($formConfirm=='N') {
					$displayConfirm = "hidden";
				} else {
					$displayConfirm = "Checkbox";
				}

				$formReedit  = $fr[9];
				$displayReEdit = "";
				if ($formReedit=='N') {
					$displayReEdit = "hidden";
				} else {
					$displayReEdit = "Checkbox";
				}
				?>
				
          <tr bgcolor="#FFFFFF" id="<?=$k."_".$j?>" style="display:none"> 
		           <td class="listing-item" style="" height="25">
				     <?php //if ($functionId==162){
			  if ($extraflag=="sd"){
					$k1="invfrz";
					$moduleName1="SupplierDataSubModule";
					?>
				   <div id ="t1"> <a href="##" onClick="showTableRow1('invfrz','invfrz1','<?=$moduleName1?>');" class="expandLink1">+</a><?=$moduleName1?></div>
				   <input type="hidden" name="kvalue" id="kvalue" value=<?=$k?> /><input type="hidden" name="jvalue" id="jvalue" value=<?=$j?> />
				     <?php }?>
				   </td>
			   <td class="listing-item" style="padding-left:20px;" height="25"><? //=$displaySubMenu ?>
			   
			  
					
			 
			 
			   
			   </td>
                           <td class="listing-item" style="padding-left:20px;" nowrap="nowrap">
			   <input name="functionId_<?=$k?>_<?=$j?>" type="checkbox" id="functionId_<?=$k?>_<?=$j?>" value="<?=$functionId?>" <?=$selFunction?> class="chkBox" <?php  if ($extraflag=="sd"){?> onclick="assignval(<?=$k?>,<?=$j?>)" <?php }?>>&nbsp;<?=$functionName?>
			   
			  
			   <input type="hidden" name="hidRoleFunctionId_<?=$k?>_<?=$j?>" value="<?=$roleFunctionId?>"></td>
                            <td class="listing-item" align="center"><input name="selAccess_<?=$k?>_<?=$j?>" type="checkbox" id="selAccess_<?=$k?>_<?=$j?>" value="Y" <?=$selAccess?> class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selAdd_<?=$k?>_<?=$j?>" type="<?=$displayAdd?>" id="selAdd_<?=$k?>_<?=$j?>" value="Y" <?=$selAdd?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selEdit_<?=$k?>_<?=$j?>" type="<?=$displayEdit?>" id="selEdit_<?=$k?>_<?=$j?>" value="Y" <?=$selEdit?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selDelete_<?=$k?>_<?=$j?>" type="<?=$displayDelete?>" id="selDelete_<?=$k?>_<?=$j?>" value="Y" <?=$selDelete?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selPrint_<?=$k?>_<?=$j?>" type="<?=$displayPrint?>" id="selPrint_<?=$k?>_<?=$j?>" value="Y" <?=$selPrint?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selConfirm_<?=$k?>_<?=$j?>" type="<?=$displayConfirm?>" id="selConfirm_<?=$k?>_<?=$j?>" value="Y" <?=$selConfirm?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selReEdit_<?=$k?>_<?=$j?>" type="<?=$displayReEdit?>" id="selReEdit_<?=$k?>_<?=$j?>" value="Y" <?=$selReEdit?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
			     <td class="listing-item" align="center"><input name="selCompanySpecific_<?=$k?>_<?=$j?>" type="checkbox" id="selCompanySpecific_<?=$k?>_<?=$j?>" value="Y" <?=$selCompanySpecific?> onclick="checkSel(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <!--td class="listing-item" align="center"><input name="selActive_<?=$k?>_<?=$j?>" type="checkbox" id="selActive_<?=$k?>_<?=$j?>" value="Y" <?=$selActive?> onclick="checkSel(<?=$k?>,<?=$j?>);"></td-->
			    <td class="listing-item" align="center"><INPUT type='checkbox' name='CheckAll_<?=$k?>_<?=$j?>' id='CheckAll_<?=$k?>_<?=$j?>' onClick="selAll(<?=$k?>,<?=$j?>); " <?=$selAll?> class="chkBox"></td>
                          </tr>	
						  
						  <?php if ($extraflag=="sd"){?>
						 
						<tr bgcolor="#FFFFFF" id="invfrz" style="display:none" ><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp; <input type="hidden" name="rowextraflag_<?=$k?>_<?=$j?>" id="rowextraflag_<?=$k?>_<?=$j?>" value="sd" /></td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="checkbox" name="supdChkbx1" id="supdChkbx1" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);" <?php //if ($flag==1){?>  <?php /*}*/?>
 <?=$flagInvchecked;?>  >Supplier Data-Inventory &nbsp;</td>

<!--<td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;&nbsp;All</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Inventory</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Frozen</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;RTE</td><td class="listing-head" align="center"bgcolor="#f2f2f2"><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td>-->


<td class="listing-item" align="center"><input name="selAccess1" type="checkbox" id="selAccess1" value="Y" <?=$selAccess1?> class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selAdd1" type="<?=$displayAdd?>" id="selAdd1" value="Y" <?=$selAdd1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selEdit1" type="<?=$displayEdit?>" id="selEdit1" value="Y" <?=$selEdit1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selDelete1" type="<?=$displayDelete?>" id="selDelete1" value="Y" <?=$selDelete1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selPrint1" type="<?=$displayPrint?>" id="selPrint1" value="Y" <?=$selPrint1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selConfirm1" type="<?=$displayConfirm?>" id="selConfirm1" value="Y" <?=$selConfirm1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selReEdit1" type="<?=$displayReEdit?>" id="selReEdit1" value="Y" <?=$selReEdit1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
			     <td class="listing-item" align="center"><input name="selCompanySpecific1" type="checkbox" id="selCompanySpecific1" value="Y" <?=$selCompanySpecific1?> onclick="checkSelInv(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <!--td class="listing-item" align="center"><input name="selActive_<?=$k?>_<?=$j?>" type="checkbox" id="selActive_<?=$k?>_<?=$j?>" value="Y" <?=$selActive?> onclick="checkSel(<?=$k?>,<?=$j?>);"></td-->
			    <td class="listing-item" align="center"><INPUT type='checkbox' name='CheckAll1' id='CheckAll1' onClick="selAllInv(1,1); " <?=$selAll1?> class="chkBox"></td></tr>

				<tr bgcolor="#FFFFFF" id="invfrz1" style="display:none" ><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="checkbox" name="supdChkbx2" id="supdChkbx2" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);"  <?=$flagFRNchecked;?>  >Supplier Data-Frozen &nbsp;</td><!--<td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;&nbsp;All</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Inventory</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Frozen</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;RTE</td><td class="listing-head" align="center"bgcolor="#f2f2f2"><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td>--><td class="listing-item" align="center"><input name="selAccess2" type="checkbox" id="selAccess2" value="Y" <?=$selAccess2?> class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selAdd2" type="<?=$displayAdd?>" id="selAdd2" value="Y" <?=$selAdd2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selEdit2" type="<?=$displayEdit?>" id="selEdit2" value="Y" <?=$selEdit2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selDelete2" type="<?=$displayDelete?>" id="selDelete2" value="Y" <?=$selDelete2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selPrint2" type="<?=$displayPrint?>" id="selPrint2" value="Y" <?=$selPrint2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selConfirm2" type="<?=$displayConfirm?>" id="selConfirm2" value="Y" <?=$selConfirm2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selReEdit2" type="<?=$displayReEdit?>" id="selReEdit2" value="Y" <?=$selReEdit2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
			     <td class="listing-item" align="center"><input name="selCompanySpecific2" type="checkbox" id="selCompanySpecific2" value="Y" <?=$selCompanySpecific2?> onclick="checkSelFrn(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <!--td class="listing-item" align="center"><input name="selActive_<?=$k?>_<?=$j?>" type="checkbox" id="selActive_<?=$k?>_<?=$j?>" value="Y" <?=$selActive?> onclick="checkSel(<?=$k?>,<?=$j?>);"></td-->
			    <td class="listing-item" align="center"><INPUT type='checkbox' name='CheckAll2' id='CheckAll2' onClick="selAllInv1(1,1); " <?=$selAll2?> class="chkBox"></td></tr>

				<tr bgcolor="#FFFFFF" id="invfrz2" style="display:none" >&nbsp;<td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="checkbox" name="supdChkbx3" id="supdChkbx3" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);" <?=$flagRTEchecked;?> >Supplier Data-RTE &nbsp;</td><!--<td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;&nbsp;All</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Inventory</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;Frozen</td><td class="listing-head" align="center" bgcolor="#f2f2f2">&nbsp;RTE</td><td class="listing-head" align="center"bgcolor="#f2f2f2"><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td><td class="listing-item" align="center" bgcolor="#f2f2f2">&nbsp;</td>--><td class="listing-item" align="center"><input name="selAccess3" type="checkbox" id="selAccess3" value="Y" <?=$selAccess3?> class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selAdd3" type="<?=$displayAdd?>" id="selAdd3" value="Y" <?=$selAdd3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selEdit3" type="<?=$displayEdit?>" id="selEdit3" value="Y" <?=$selEdit3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selDelete3" type="<?=$displayDelete?>" id="selDelete3" value="Y" <?=$selDelete3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selPrint3" type="<?=$displayPrint?>" id="selPrint3" value="Y" <?=$selPrint3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selConfirm3" type="<?=$displayConfirm?>" id="selConfirm3" value="Y" <?=$selConfirm3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <td class="listing-item" align="center"><input name="selReEdit3" type="<?=$displayReEdit?>" id="selReEdit3" value="Y" <?=$selReEdit3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
			     <td class="listing-item" align="center"><input name="selCompanySpecific3" type="checkbox" id="selCompanySpecific3" value="Y" <?=$selCompanySpecific3?> onclick="checkSelRTE(<?=$k?>,<?=$j?>);" class="chkBox"></td>
                            <!--td class="listing-item" align="center"><input name="selActive_<?=$k?>_<?=$j?>" type="checkbox" id="selActive_<?=$k?>_<?=$j?>" value="Y" <?=$selActive?> onclick="checkSel(<?=$k?>,<?=$j?>);"></td-->
			    <td class="listing-item" align="center"><INPUT type='checkbox' name='CheckAll3' id='CheckAll3' onClick="selAllInv3(1,1); " <?=$selAll3?> class="chkBox"></td></tr>
						<!--<tr bgcolor="#FFFFFF" id="invfrz1" style="display:none" >&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;<?//=$k;?><input type="checkbox" name="supdChkbx1" id="supdChkbx1" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);" <?php if ($flag==1){?> checked <?php }?> >&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;<input type="checkbox" name="supdChkbx2" id="supdChkbx2" class="fsaChkbx" value="INV" <?php if ($flag2==2){?> checked <?php }?>onclick="selindv(0,0)"  ></td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;<input type="checkbox" name="supdChkbx3" id="supdChkbx3" class="fsaChkbx" value="FRN" <?php if ($flag3==3){?> checked <?php }?> onclick="selindv(0,0)"></td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;<input type="checkbox" name="supdChkbx4" id="supdChkbx4" class="fsaChkbx" value="RTE"  <?php if ($flag4==4){?> checked <?php }?> onclick="selindv(0,0)"></td><td class="listing-item" align="center"bgcolor="#FFFFFF"><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td></tr>-->
						

					<?php }?>
		
		<?
		
		  $prevPmenu_id = $pmenu_id;
		  $prevModuleId=$moduleId;
			$j++;
		 }
		?>
	
		</table>
		 <input type="hidden" name="flagvalue" id="flagvalue" value=0 />
		<input type="hidden" name="hidRowCount1_<?=$k?>" id="hidRowCount1_<?=$k?>" value="<?=$j-1;?>">
		<input type="hidden" name="hidRowCount2" id="hidRowCount2" value="<?=$k?>" ></td>
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddRole(document.frmManageRole);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddRole(document.frmManageRole);">                        </td>
                        <?}?>
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <?
			}
			
			# Listing  Starts
		?>
    <tr> 
      <td height="10" align="center" ></td>
    </tr>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage 
                    Role </td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$roleRecordsSize;?>);" ><? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageRole.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <?
					if($errDel!="")
						{
				?>
                <tr> 
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?>
                  </td>
                </tr>
                <?
			}
		?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" style="padding-left:5px;padding-right:5px;">
<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
			if (sizeof($roleRecords)>0) {
				$i	=	0;
			?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
                        <td class="listing-head" nowrap style="padding-left:10px;padding-right:10px;">Role</td>
                        <td class="listing-head" style="padding-left:10px;padding-right:10px;">Description</td>
						<? if($edit==true){?>
                        <td class="listing-head" width="45"></td>
						<? }?>
                      </tr>
		 <?
			foreach($roleRecords as $rrec) {
				$i++;
				$roleId				=	$rrec[0];
				$roleName			=	stripSlash($rrec[1]);
				$roleDescription	=	stripSlash($rrec[2]);
				$roleFlag			=	$rrec[3];
		?>
                      <tr  bgcolor="WHITE"  > 
                        <td width="20" align="center" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$roleId;?>" class="chkBox"></td>
                        <td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;">
                          <?=$roleName;?>
                        </td>
                        <td class="listing-item" nowrap style="padding-left:10px;padding-right:10px;"><?=$roleDescription?></td>
						<? if($edit==true){?>
                        <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$roleId;?>,'editId'); this.form.action='ManageRole.php';"></td>
						<? }?>
                      </tr>
                      <?
					  	}
					  ?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
					  
					  <? if($roleFlag==0){ ?>
					  <input type="hidden" name="roleMainId" id="roleMainId" value="<?=$roleId?>">
					  <? } else {?>
					  <input type="hidden" name="roleMainId" id="roleMainId" value="<?=$lastId?>">
					  <? } ?>
			    	  <?
						}
						else
						{
					  ?>
                      <tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>
                        </td>
                      </tr>
                      <?
						}
					  ?>
                    </table><input type="hidden" name="checkRoleFunction" id="checkFunction"></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <tr > 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$roleRecordsSize;?>);" ><? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageRole.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <tr> 
      <td height="10"></td>
    </tr>
<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">
  </table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<style type="text/css">
.expandLink1 {
    text-decoration:none;
	color:red;
}

</style>





