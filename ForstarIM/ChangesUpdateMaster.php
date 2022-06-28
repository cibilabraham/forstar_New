<?php
	require("include/include.php");
	$err			= "";
	$errDel			= "";	

	$editTaxMasterRecId	= "";
	$taxRecId		= "";
	$baseCst		= "";
	
	$editMode		= true;
	$addMode		= false;	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Update
	if( $p["cmdSaveChange"]!="" ) {
		$taxRecId	= $p["hidTaxMasterRecId"];
		$baseCst	= $p["baseCst"];
		$cstActive	= ($p["cstActive"]=="")?N:$p["cstActive"];
						
		if ($baseCst!="" && $taxRecId!="") {
			$taxRecUptd	= $taxMasterObj->updateTaxMasterRec($taxRecId, $baseCst, $cstActive);
		} else {
			$taxRecUptd	= $taxMasterObj->addTaxMasterRec($baseCst, $cstActive);		
		}	
		if ($taxRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succTaxMasterUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msg_failTaxMasterUpdate;
		}
		$taxRecUptd		=	false;
	}
		
	# Edit 
	$taxMasterRec		= $taxMasterObj->find();
	$editTaxMasterRecId	= $taxMasterRec[0];
	$baseCst		= $taxMasterRec[1];
	$cstActive		= $taxMasterRec[2];
	$active 		= "";
	if ($cstActive=='Y') $active = "checked";	

	//echo "H=".$cstPercent	= $taxMasterObj->getBaseCst();

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ChangesUpdateMaster.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmChangesUpdateMaster" action="ChangesUpdateMaster.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="59%">
	<tr>
		<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
	</tr>
	<?
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Changes Update Master</td>
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
			<td colspan="2" align="center"><? if($edit==true && $isAdmin==true){?>&nbsp;&nbsp;
			 <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateChangesUpdateMaster(document.frmChangesUpdateMaster)">	<? }?>		
			</td>
			<?} else{?>
			  <td align="center">&nbsp;&nbsp;</td>
			<?}?>
		</tr>
		<input type="hidden" name="hidTaxMasterRecId" value="<?=$editTaxMasterRecId;?>">
		<tr>
			<td colspan="2" nowrap class="fieldName" height="5"></td>
		</tr>
		<tr>
		  	<td colspan="2" nowrap>
			<table width="200" align="center">
			<tr>
                	<td class="fieldName" nowrap="nowrap">RTE: </td>
                        <td nowrap="true">
				  <input type="checkbox" name="rteChk" id="rteChk" value="Y" <?=$active?> class="chkBox"> &nbsp;&nbsp;
			</td>
                </tr>
               </table></td>
	</tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){ ?>
		<td colspan="2" align="center"><? if($edit==true && $isAdmin==true){?>&nbsp;&nbsp;
		  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateChangesUpdateMaster(document.frmChangesUpdateMaster);"><? }?></td>
		<? } else{ ?>
		  <td align="center">&nbsp;&nbsp;</td>
		<? } ?>
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
		}
		
		# Listing LandingCenter Starts
	?>

	<tr>
				<td height="10" align="center" ></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
  </table>
</form>
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>
