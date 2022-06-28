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
		//header("Location: ErrorPage.php");
		//die();
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
	/*
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
*/

	/*# Edit Role
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$roleRec	=	$manageroleObj->find($editId);
		$editRoleId	=	$roleRec[0];
		$editRoleName	=	stripSlash($roleRec[1]);
		$editRoleDescr	=	stripSlash($roleRec[2]);
	}
*/	
	#Update a folder access	
	if ($p["cmdSaveChange"]!="") {		
			
			$roleFunctionRecDel	= $folderaccessObj->deleteFolderAccess();
			$rowCount2	=	$p["hidRowCount2"];

			
			if (($p["selAccess1"]!="") && ($p["selAccess2"]!="") && ($p["selAccess3"]!=""))
			{
				$supplierdtflg="INV-FRN-RTE";
			}
			else if (($p["selAccess1"]!="") && ($p["selAccess2"]!=""))
			{
				$supplierdtflg="INV-FRN";

			}else if (($p["selAccess1"]!="") && ($p["selAccess3"]!=""))
			{
				$supplierdtflg="INV-RTE";
			}
			else if (($p["selAccess2"]!="") && ($p["selAccess3"]!=""))
			{
				$supplierdtflg="FRN-RTE";
			}
			else if ($p["selAccess2"]!="")
			{
				 $supplierdtflg="FRN";
			}
			else if ($p["selAccess3"]!="")
			{
				$supplierdtflg="RTE";
			}
			 
			
			for ($i=1; $i<=$rowCount2; $i++) 
			{
				$rowCount1	=	$p["hidRowCount1_".$i];
				for ($j=0; $j<=$rowCount1; $j++) 
				{	
					$selModule		=	$p["moduleId_".$i];
					$selSubModule   = 	$p["subModuleId_".$i];
					$selFunction	=	$p["functionId_".$i."_".$j];				
					$selAccess		=	($p["selAccess_".$i."_".$j]=="")?N:$p["selAccess_".$i."_".$j];
					//echo $selAccess.$p["selAccess_".$i."_".$j];
					//echo $selModule.'--'.$selSubModule.'--'.$selFunction.'--'.$selAccess.'<br/>';
					//die();

					$flgsd=$p["rowextraflag_".$i."_".$j];
					

					if($selAccess=="Y") 
					{
						$acessFunctionRecIns=$folderaccessObj->addAccessFunction($selModule, $selFunction, $selAccess,$selSubModule,$supplierdtflg);
						$oldj=$j;
					}


					
					//echo "hii".$flgsd.'--'.$selAccess.'---'.$oldj;
					//die();
					if ($flgsd=="sd")
					{
						if($selAccess=='N' && $oldj=='0')
						{
							$selAccess1 ="Y";
							$supplierdtflg="INV";
							$stflg=1;
							$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess1,$selSubModule,$supplierdtflg,$stflg);

							$selAccess2 ="Y";
							$supplierdtflg="FRN";
							$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess2,$selSubModule,$supplierdtflg,$stflg);

							$selAccess3 ="Y";
							$supplierdtflg="RTE";
							$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess3,$selSubModule,$supplierdtflg,$stflg);
						}
						//if ($selFunction==162)
						//$selModule	=	$p["moduleId_".$i];
						//$selSubModule   = 	$p["subModuleId_".$i];
						//$selFunction	=	$p["functionId_".$i."_".$j];
						else
						{
							$selAccess1 = ($p["selAccess1"]=="")?N:$p["selAccess1"];
							if($selAccess1!='N')
							{
								$supplierdtflg="INV";
								$stflg=1;

								//if ($selFunction!="" && $lastInsertedId!="") {
									$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess1,$selSubModule,$supplierdtflg,$stflg);
								//}
							}
							$selAccess2 = ($p["selAccess2"]=="")?N:$p["selAccess2"];
							if($selAccess2!='N')
							{
								$supplierdtflg="FRN";
								$stflg=1;

								//if ($selFunction!="" && $lastInsertedId!="") {
									$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess2,$selSubModule,$supplierdtflg,$stflg);
								//}
							}
							$selAccess3 = ($p["selAccess3"]=="")?N:$p["selAccess3"];
							if($selAccess3!='N')
							{
								$supplierdtflg="RTE";
								$stflg=1;

								//if ($selFunction!="" && $lastInsertedId!="") {
								$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $selAccess3,$selSubModule,$supplierdtflg,$stflg);
							}
						}
					}

					//echo "selFunction".'--'.$selFunction.'--'."selSubModule".'--'.$selSubModule.'--'."selModule".'--'.$selModule.'--'."selAccess".'--'.$selAccess.'<br/>';

					if($selSubModule && $selModule && $selAccess)
					{	
						if($selAccess=="N" && $flgsd!="sd")
						{
							$roleFunctionRecIns=$folderaccessObj->deleteRoleFunction($selFunction,$selSubModule,$selModule);
						}
						else if($selAccess=="Y" && $flgsd!="sd")
						{
							$roleFunctionRecIns=$folderaccessObj->getAdministratorRole($selFunction,$selSubModule,$selModule);
						}
						if($selAccess=="N" && $flgsd=="sd" && $oldj=='0')
						{
							$roleFunctionRecIns=$folderaccessObj->getAdministratorRoleSd($selFunction,$selSubModule,$selModule);
						}
						
					}
					else if($selModule && $selAccess=="N" && $selSubModule=='0')
					{	//echo "huiii".'<br/>';
						$roleFunctionRecIns=$folderaccessObj->deleteRoleFunction($selFunction,$selSubModule,$selModule);
					}
					else if( $selModule && $selAccess=='Y')
					{ //echo "hii".'<br/>';
						$roleFunctionRecIns=$folderaccessObj->getAdministratorRole($selFunction,$selSubModule,$selModule);
					}
					
					
					


					if ($flgsd=="se")
					{
						if($selAccess=='N' && $oldj=='0')
						{
							$stockAccess1 ="Y";
							$supplierdtflg="FRN";
							$stflg=1;
							$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $stockAccess1,$selSubModule,$supplierdtflg,$stflg);

							$stockAccess2 ="Y";
							$supplierdtflg="RTE";
							$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $stockAccess2,$selSubModule,$supplierdtflg,$stflg);

						}
						//if ($selFunction==162)
						//$selModule	=	$p["moduleId_".$i];
						//$selSubModule   = 	$p["subModuleId_".$i];
						//$selFunction	=	$p["functionId_".$i."_".$j];
						else
						{
							$stockAccess1 = ($p["stockAccess1"]=="")?N:$p["stockAccess1"];
							if($stockAccess1!='N')
							{
								$supplierdtflg="FRN";
								$stflg=1;

								//if ($selFunction!="" && $lastInsertedId!="") {
									$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $stockAccess1,$selSubModule,$supplierdtflg,$stflg);
								//}
							}
							$stockAccess2 = ($p["stockAccess2"]=="")?N:$p["stockAccess2"];
							if($stockAccess2!='N')
							{
								$supplierdtflg="RTE";
								$stflg=1;

								//if ($selFunction!="" && $lastInsertedId!="") {
									$roleFunctionRecIns=$folderaccessObj->addAccessFunction1($selModule, $selFunction, $stockAccess2,$selSubModule,$supplierdtflg,$stflg);
								//}
							}
							
						}
					}

					//echo "selFunction".'--'.$selFunction.'--'."selSubModule".'--'.$selSubModule.'--'."selModule".'--'.$selModule.'--'."selAccess".'--'.$selAccess.'<br/>';

					if($selSubModule && $selModule && $selAccess)
					{	
						if($selAccess=="N" && $flgsd!="sd" && $flgsd!="se")
						{
							$roleFunctionRecIns=$folderaccessObj->deleteRoleFunction($selFunction,$selSubModule,$selModule);
						}
						else if($selAccess=="Y" && $flgsd!="sd"  && $flgsd!="se")
						{
							$roleFunctionRecIns=$folderaccessObj->getAdministratorRole($selFunction,$selSubModule,$selModule);
						}
						else if($selAccess=="N" && $flgsd=="sd"  && $flgsd!="se" && $oldj=='0')
						{
							$roleFunctionRecIns=$folderaccessObj->getAdministratorRoleSd($selFunction,$selSubModule,$selModule);
						}
						else if($selAccess=="N" && $flgsd!="sd"  && $flgsd=="se" && $oldj=='0')
						{
							$roleFunctionRecIns=$folderaccessObj->getAdministratorRoleSe($selFunction,$selSubModule,$selModule);
						}
						
					}
					else if($selModule && $selAccess=="N" && $selSubModule=='0')
					{	//echo "huiii".'<br/>';
						$roleFunctionRecIns=$folderaccessObj->deleteRoleFunction($selFunction,$selSubModule,$selModule);
					}
					else if( $selModule && $selAccess=='Y')
					{ //echo "hii".'<br/>';
						$roleFunctionRecIns=$folderaccessObj->getAdministratorRole($selFunction,$selSubModule,$selModule);
					}




















				}
			}



		//die();
	
		if ($acessFunctionRecIns) {
			$sessObj->createSession("displayMsg",$msg_succFolder);
			//$sessObj->createSession("nextPage",$url_afterUpdateRole);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateFolder;
		}
		$acessFunctionRecIns	=	false;
	}
	
	

	#List All Role
	$roleRecords		=	$folderaccessObj->fetchAllRecords();
	$roleRecordsSize	=	sizeof($roleRecords);

	#Get All Function Records
	$getFunctionRecords = $folderaccessObj->fetchAllFunctionRecords();

	$heading	= $label_mngfolder;
	$ON_LOAD_PRINT_JS	= "libjs/folderaccess.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmFolderAccess" id="frmFolderAccess" action="FolderAccess.php" method="post">	
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
			//if( $editMode || $addMode)
			//{
		?>
		<tr> 
			<td> 
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr> 
												<td colspan="2" height="10" ></td>
											</tr>
											<tr> 
												<?// if($editMode){?>
												<td colspan="2" align="center">
													<!--<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');">--> 
												  &nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateAddFolder();" />
												 </td>
												<? //}?>
											</tr>
												<input type="hidden" name="hidRoleId" value="<?=$editRoleId;?>">
											<tr>
												<td colspan="2" nowrap height="10" align="center">&nbsp;</td>
											</tr>
											<tr>
												<td nowrap>&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr id="roleFnHead">
												<td colspan="2" nowrap class="fieldName" >Please define the access level for this version by selecting from the following list:</td>
											</tr>
											<tr id="roleFnList"> 
												<td colspan="2" nowrap align="center" style="padding-left:10px;padding-right:10px;">
													<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999">
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
															//echo "**********$extraflag";
															if ($extraflag1=="sd")
															{
																//echo "hai";
																$supdt=$functionId;
																$roleRecsup = $folderaccessObj->findFolderRecsup($moduleId, $functionId, $pmenu_id);
																//echo $supdt;
																//echo "hii";
																foreach ($roleRecsup as $rrS) 
																{
																	//echo "entered";
																	//echo "hii".$rrS[3];
																	if ($rrS[4]=="INV")
																	{ 
																		if($rrS[3]=='Y')
																		{
																			$selAccess1	= 	"Checked";
																			$flagInvchecked="Checked";
																			//echo "hai";
																			//echo $flagInvchecked;
																		}
																		else 
																		{
																			$selAccess1	=	""; $flagInvchecked="";
																		}
																		$selAll1 = "";
																		if ($rrS[3]=='Y' ) $selAll1 = "Checked";
																		else $selAll1 = "";
																	}

																	if ($rrS[4]=="FRN")
																	{
																		if($rrS[3]=='Y')	
																		{
																			$selAccess2	= 	"Checked";
																			$flagFRNchecked="Checked";
																		}
																		else 
																		{
																			$selAccess2	=	""; 
																			$flagFRNchecked="";
																		}

																		$selAll2 = "";
																		if ($rrS[3]=='Y' ) 
																		$selAll2 = "Checked";
																		else $selAll2 = "";
																	}
																	if ($rrS[4]=="RTE")
																	{
																		if($rrS[3]=='Y')
																		{	
																			$selAccess3	= 	"Checked";
																			$flagRTEchecked="Checked";
																		}
																		else 
																		{
																			$selAccess3	=	""; $flagFRNchecked="";
																		}
																		$selAll3 = "";
																		if ($rrS[3]=='Y' ) $selAll3 = "Checked";
																		else $selAll3 = "";
																	}
																}
															}
															else if ($extraflag1=="se")
															{
																//echo "hai";
																$supdt=$functionId;
																$roleRecsup = $folderaccessObj->findFolderRecsup($moduleId, $functionId, $pmenu_id);
																//echo $supdt;
																//echo "hii";
																foreach ($roleRecsup as $rrS) 
																{
																	//echo "entered";
																	//echo "hii".$rrS[3];
																	if ($rrS[4]=="FRN")
																	{
																		if($rrS[3]=='Y')	
																		{
																			$stockAccess1	= 	"Checked";
																			$flagFRNchecked="Checked";
																		}
																		else 
																		{
																			$stockAccess1	=	""; 
																			$flagFRNchecked="";
																		}

																	//	$selAll2 = "";
																	//	if ($rrS[3]=='Y' ) 
																	//	$selAll2 = "Checked";
																	//	else $selAll2 = "";
																	}
																	if ($rrS[4]=="RTE")
																	{
																		if($rrS[3]=='Y')
																		{	
																			$stockAccess2	= 	"Checked";
																			$flagRTEchecked="Checked";
																		}
																		else 
																		{
																			$stockAccess2	=	""; $flagFRNchecked="";
																		}
																		//$selAll3 = "";
																		//if ($rrS[3]=='Y' ) $selAll3 = "Checked";
																		//else $selAll3 = "";
																	}
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

															//if ($editMode==true) {
																$roleRec = $folderaccessObj->findFolderRecs($moduleId, 0,$pmenu_id);
																$roleFunctionId	=	$roleRec[0];
													
																if ($roleRec[2]==0 && $roleRec[2]!="") 
																{
																	$selFunction = "Checked"; 
																	$disAccess="disabled";
																}
																else  
																{
																	$selFunction	=	""; 
																	$disAccess="";
																}
																if($roleRec[3]=='Y' && $roleRec[3]!="")	
																{ 
																	$selAccess	= 	"Checked"; 
																	$disAccess="disabled";
																}
																else 
																{	
																	$selAccess	=	""; 
																	$disAccess="";
																}

																
																$selAll = "";
																if ($roleRec[2]==0 && $roleRec[2]!="" && $roleRec[3]=='Y' ) $selAll = "Checked";
																else $selAll = "";
																//echo $roleRec[3];
																//if ($roleRec[3]==162){
															//	}
															?>
														<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head" height="30">Module&nbsp;&nbsp;</td>
															<td class="listing-head" height="30">Sub Menu</td>
															<td class="listing-head">Function</td>
															<td class="listing-head" style="padding-left:5px; padding-right:5px;">Access</td>
														</tr>
														<tr bgcolor="#FFFFFF">
															<td class="listing-item" style="padding-left:20px;" height="25" nowrap>
																<div id ="t_<?=$k?>"><a href="javascript:void(0);"  onClick="showTableRow(<?=$k?>, '<?=$moduleName?>');" class="expandLink">+</a>&nbsp;<?=$moduleName?></div><?//=$moduleName?>
																<input type="hidden" name="moduleId_<?=$k?>" value="<?=$moduleId?>"><input type="hidden" name="subModuleId_<?=$k?>" value="<?=$pmenu_id?>">
															</td>
															<td class="listing-item" style="padding-left:10px; padding-right:10px;" height="25"><?=$subMenu?></td>
															<td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input name="functionId_<?=$k?>_0" type="hidden" id="functionId_<?=$k?>_0" value="0" <?=$selFunction?> class="chkBox">&nbsp;All<input type="hidden" name="hidRoleFunctionId_<?=$k?>_0" value="<?=$roleFunctionId?>"></td>
															<td class="listing-item" align="center"><input name="selAccess_<?=$k?>_0" type="checkbox" id="selAccess_<?=$k?>_0" value="Y" <?=$selAccess?> class="chkBox" onclick="fieldState(<?=$k?>);"></td>
															<?
															}

															$selActive = "";
															$roleRec = $folderaccessObj->findFolderRecs($moduleId, $functionId, $pmenu_id);
															$roleFunctionId	=	$roleRec[0];
															if ($roleRec[2]==$functionId) $selFunction = "Checked";
															else $selFunction	=	"";
															//printr($roleRec);
															if ($roleRec[3]=='Y' && $roleRec[2]==$functionId)	$selAccess	= 	"Checked";
															else $selAccess	=	"";
															//echo $selAccess;
															$selAll = "";
															if ($roleRec[2]=='0') $selAll = "Checked";
															else $selAll = "";
															
															if ($extraflag=="sd"){
																$supfilterflag=$roleRec[4];
																						//echo $supfilterflag;
																						//echo "hai";
																}
																

																//}

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
															<?php }
															else if($extraflag=="se")
															{
																$k1="invrte";
																$moduleName1="Stock Entry Sub Module";
															?>
																<div id ="t2"> <a href="##" onClick="showTableRow2('invrte','invrte1','<?=$moduleName1?>');" class="expandLink1">+</a><?=$moduleName1?></div>
																<input type="hidden" name="kvalue" id="kvalue" value=<?=$k?> /><input type="hidden" name="jvalue" id="jvalue" value=<?=$j?> />
															<?php }?>
															</td>
														   <td class="listing-item" style="padding-left:20px;" height="25"><? //=$displaySubMenu ?>
															</td>
														   <td class="listing-item" style="padding-left:20px;" nowrap="nowrap">
															   <input name="functionId_<?=$k?>_<?=$j?>" type="hidden" id="functionId_<?=$k?>_<?=$j?>" value="<?=$functionId?>" <?=$selFunction?>  class="chkBox" <?php  if ($extraflag=="sd"){?> onclick="assignval(<?=$k?>,<?=$j?>)" <?php }?>>&nbsp;<?=$functionName?>
															   
																<input type="hidden" name="hidRoleFunctionId_<?=$k?>_<?=$j?>" value="<?=$roleFunctionId?>"></td>
															<td class="listing-item" align="center"><?php  if ($extraflag!="sd" && $extraflag!="se"){?><input name="selAccess_<?=$k?>_<?=$j?>" type="checkbox" id="selAccess_<?=$k?>_<?=$j?>" <?=$disAccess?> value="Y" <?=$selAccess?> class="chkBox selAcc_<?=$k?>" >
															<? } else if($extraflag=="sd") { ?>
															<input name="selAccess_<?=$k?>_<?=$j?>" type="hidden" id="selAccess_<?=$k?>_<?=$j?>" <?=$disAccess?> value="Y" <?=$selAccess?> class="chkBox selAcc_<?=$k?>">
															<input type="hidden" name="rowextraflag_<?=$k?>_<?=$j?>" id="rowextraflag_<?=$k?>_<?=$j?>" value="sd" />
															<? }
															else if($extraflag=="se") { ?>
															<input name="selAccess_<?=$k?>_<?=$j?>" type="hidden" id="selAccess_<?=$k?>_<?=$j?>" <?=$disAccess?> value="Y" <?=$selAccess?> class="chkBox selAcc_<?=$k?>">
															<input type="hidden" name="rowextraflag_<?=$k?>_<?=$j?>" id="rowextraflag_<?=$k?>_<?=$j?>" value="se" />
															<? }
?>
															</td>
														</tr>
														<?php
														  //echo $extraflag;
														  if ($extraflag=="sd"){?>
														 
														<tr bgcolor="#FFFFFF" id="invfrz" style="display:none" ><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp; </td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="hidden" name="supdChkbx1" id="supdChkbx1" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);" <?php //if ($flag==1){?>  <?php /*}*/?>
														 <?=$flagInvchecked;?>  >Supplier Data-Inventory &nbsp;</td>
															<td class="listing-item" align="center"><input name="selAccess1" type="checkbox" id="selAccess1" value="Y" <?=$selAccess1?> class="chkBox" ></td>
														</tr>
															<tr bgcolor="#FFFFFF" id="invfrz1" style="display:none" ><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="hidden" name="supdChkbx2" id="supdChkbx2" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);"  <?=$flagFRNchecked;?>  >Supplier Data-Frozen &nbsp;</td><td class="listing-item" align="center"><input name="selAccess2" type="checkbox" id="selAccess2" value="Y" <?=$selAccess2?> class="chkBox" ></td>
															</tr>

															<tr bgcolor="#FFFFFF" id="invfrz2" style="display:none" >&nbsp;<td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="hidden" name="supdChkbx3" id="supdChkbx3" class="fsaChkbx" value="1" onclick="selAllinv(<?=$k?>,0);" <?=$flagRTEchecked;?> >Supplier Data-RTE &nbsp;</td><td class="listing-item" align="center"><input name="selAccess3" type="checkbox" id="selAccess3" value="Y" <?=$selAccess3?> class="chkBox" ></td>
														</tr>
														<?php
														}
														else if ($extraflag=="se"){?>
														 
														<tr bgcolor="#FFFFFF" id="invrte" style="display:none" ><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp; </td><td class="listing-item" align="center" bgcolor="#FFFFFF">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="hidden" name="supdChkbx1" id="supdChkbx1" class="fsaChkbx" value="1" <?php //if ($flag==1){?>  <?php /*}*/?>
														 <?=$flagInvchecked;?>  >Stock Entry-Frozen  &nbsp;</td>
															<td class="listing-item" align="center"><input name="stockAccess1" type="checkbox" id="stockAccess1" value="Y" <?=$stockAccess1?> class="chkBox" ></td>
														</tr>
														<tr bgcolor="#FFFFFF" id="invrte1" style="display:none" >
															<td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap">&nbsp;</td><td class="listing-item" style="padding-left:20px;" nowrap="nowrap"><input type="hidden" name="supdChkbx2" id="supdChkbx2" class="fsaChkbx" value="1" <? /*onclick="selAllinv(<?=$k?>,0);" */?> >Stock Entry-RTE &nbsp;</td><td class="listing-item" align="center"><input name="stockAccess2" type="checkbox" id="stockAccess2" value="Y" <?=$stockAccess2?> class="chkBox" >
															</td>
														</tr>

															</td>
														</tr>
														<?php
														}
														?>

														
										
																				

													

													

																
														<?
														
														  $prevPmenu_id = $pmenu_id;
														  $prevModuleId=$moduleId;
															$j++;
														 }
														?>
													</table>
													<input type="hidden" name="flagvalue" id="flagvalue" value=0 />
													<input type="hidden" name="hidRowCount1_<?=$k?>" id="hidRowCount1_<?=$k?>" value="<?=$j-1;?>">
													<input type="hidden" name="hidRowCount2" id="hidRowCount2" value="<?=$k?>" >
												</td>
											</tr>
											<tr> 
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr> 
												<?// if($editMode){?>
												<td colspan="2" align="center"> 
												<!--<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageRole.php');"> -->
												  &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFolder();">                   
												</td>
											<? //}?>
										  </tr>
										  <tr> 
											<td colspan="2"  height="10" ></td>
										  </tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<!-- Form fields end   -->
			</td>
		</tr>
		<?
		//	}
			
			# Listing  Starts
		?>
		<tr> 
		  <td height="10" align="center" ></td>
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





