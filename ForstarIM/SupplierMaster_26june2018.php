<?php
	require("include/include.php");
	require_once("lib/ChangesUpdateMaster_ajax.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	$currentUserId		=	$sessObj->getValue("userId");


	$selection 	="?pageNo=".$p["pageNo"]."&sectionFilter=".$p["sectionFilter"];
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		echo "ACCESS DENIED";
		//header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()){$print=true;}
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;
	$accesscontrolObj->getAccessControlInv($moduleId, $functionId);
	$accesscontrolObj->getAccessControlFRN($moduleId, $functionId);
	$accesscontrolObj->getAccessControlRTE($moduleId, $functionId);
	$suppdtflag=$accesscontrolObj->canViewSupplierData($moduleId, $functionId);
	if($accesscontrolObj->canAccessinv())$acessInv=true;
	//echo "----$acessInv--$edit";
	if($accesscontrolObj->canAddInv()) $addInv=true;
	if($accesscontrolObj->canEditInv()) $editInv=true;
	if($accesscontrolObj->canDelInv()) $delInv=true;
	if($accesscontrolObj->canPrintInv()) $printInv=true;
	if($accesscontrolObj->canConfirmInv()) $confirmInv=true;
	if($accesscontrolObj->canReEditInv()) $reEditInv=true;
	if($accesscontrolObj->canAccessfrn())$acessFrn=true;
	if($accesscontrolObj->canAddFRN()) $addFRN=true;
	if($accesscontrolObj->canEditFRN()) $editFRN=true;
	if($accesscontrolObj->canDelFRN()) $delFRN=true;
	if($accesscontrolObj->canPrintFRN()) $printFRN=true;
	if($accesscontrolObj->canConfirmFRN()) $confirmFRN=true;
	if($accesscontrolObj->canReEditFRN()) $reEditFRN=true;
	if($accesscontrolObj->canAccessrte())$acessRTE=true;
	if($accesscontrolObj->canAddRTE()) $addRTE=true;
	if($accesscontrolObj->canEditRTE()) $editRTE=true;
	if($accesscontrolObj->canDelRTE()) $delRTE=true;
	if($accesscontrolObj->canPrintRTE()) $printRTE=true;
	if($accesscontrolObj->canConfirmRTE()) $confirmRTE=true;
	if($accesscontrolObj->canReEditRTE()) $reEditRTE=true;

if ((!$addInv) && (!$addFRN) && (!$addRTE)){
	$add=false;
}




	//echo "ttt".$suppdtflag;
	$arrsuppdtflag=explode("-",$suppdtflag);
	//print_r($arrsuppdtflag);
	//$suppdtflag=4;
	/*-----------------------------------------------------------*/

	$stockCount=$supplierMasterObj->getCountSupplierCode();
		if ($stockCount >0){		
		$code 		=	$supplierMasterObj->getSupplierCode();
		$code=$code+1;
		} else {

		$code=1;
		}

	# Add Supplier Start 
	if ($p["cmdAddNew"]!="")	$addMode	=	true;	
	if ($p["cmdCancel"]!="") 	$addMode	=	false;
	
	if ($p["cmdAdd"]!="") {

		$code		=	addSlash(trim($p["code"]));
		$name		=	addSlash(trim($p["supplierName"]));
		$address	=	addSlash(trim($p["address"]));
		$phone		=	addSlash(trim($p["phoneNo"]));
		$vatNo		=	addSlash(trim($p["vatNo"]));
		$cstNo		=	addSlash(trim($p["cstNo"]));

		$frozen		= ($p["frozen"]!="")?$p["frozen"]:"N";	
		$inventory	= ($p["inventory"]!="")?$p["inventory"]:"N";
		$rte		= ($p["rte"]!="")?$p["rte"]:"N";

		$pinCode	= $p["pinCode"];
		$faxNo		= $p["faxNo"];
		$email		= $p["email"];
		$panNo		= $p["panNo"];	
		
		# If Frozen
		$place		= $p["place"];
		$landingCenter  = $p["landingCenter"];
		$paymentBy	= $p["paymentBy"];

		$bankAcNo	= addSlash(trim($p["bankAcNo"]));
		$bankName	= addSlash(trim($p["bankName"]));	
		$supplierStatus = $p["supplierStatus"];
		$fssaiRegNo=$p["fssaiRegNo"];
		$serviceTaxNo=$p["serviceTaxNo"];
		$bankIFSCCode=$p["bankCode"];
		
		if ($name!="" && $code!="") {
			$supplierRecIns	= $supplierMasterObj->addSupplier($code, $name, $address, $phone, $vatNo, $cstNo, $frozen, $inventory, $rte, $pinCode, $faxNo, $email, $panNo, $place, $landingCenter, $paymentBy, $currentUserId, $bankAcNo, $bankName, $supplierStatus,$fssaiRegNo,$serviceTaxNo,$bankIFSCCode);

			if ($supplierRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddSupplierInventory);
				$sessObj->createSession("nextPage",$url_afterAddSupplierInventory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSupplierInventory;
			}
			$supplierRecIns		=	false;
		}

	}
	
	# Edit Supplier
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
        $combodisplay            =        $p["combodisplay"];
		$supplierRec		=	$supplierMasterObj->find($editId);
		$editSupplierId		=	$supplierRec[0];
		$code			=	$supplierRec[1];
		$name			=	stripSlash($supplierRec[2]);
		$address		=	stripSlash($supplierRec[3]);
		$phone			=	stripSlash($supplierRec[4]);
		$vatNo			=	stripSlash($supplierRec[5]);
		$cstNo			=	stripSlash($supplierRec[6]);

		$frozen		= ($supplierRec[7]=='Y')?"checked":"";	
		$inventory	= ($supplierRec[8]=='Y')?"checked":"";	
		$rte		= ($supplierRec[9]=='Y')?"checked":"";	

		$pinCode	= $supplierRec[10];
		$faxNo		= $supplierRec[11];
		$email		= $supplierRec[12];
		$panNo		= $supplierRec[13];
		
		# If Frozen
		$placeId	= $supplierRec[14];		
		$paymentBy	= $supplierRec[15];	
		
		$bankAcNo	= stripSlash($supplierRec[16]);
		$bankName	= stripSlash($supplierRec[17]);	
		$supplierStatus = $supplierRec[18];
		$fssaiRegNo=$supplierRec[19];
		$serviceTaxNo=$supplierRec[20];
		$bankCode=$supplierRec[21];
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		
		$supplierId	=	$p["hidSupplierId"];
		$code		=	addSlash(trim($p["code"]));
		$name		=	addSlash(trim($p["supplierName"]));
		$address	=	addSlash(trim($p["address"]));
		$phone		=	addSlash(trim($p["phoneNo"]));
		$vatNo		=	addSlash(trim($p["vatNo"]));
		$cstNo		=	addSlash(trim($p["cstNo"]));

		$frozen		= ($p["frozen"]!="")?$p["frozen"]:"N";	
		$inventory	= ($p["inventory"]!="")?$p["inventory"]:"N";
		$rte		= ($p["rte"]!="")?$p["rte"]:"N";
		
		$pinCode	= $p["pinCode"];
		$faxNo		= $p["faxNo"];
		$email		= $p["email"];
		$panNo		= $p["panNo"];	
		
		# If Frozen
		$place		= $p["place"];
		$landingCenter  = $p["landingCenter"];
		$paymentBy	= $p["paymentBy"];
		
		$bankAcNo	= addSlash(trim($p["bankAcNo"]));
		$bankName	= addSlash(trim($p["bankName"]));
		$supplierStatus = $p["supplierStatus"];
		$fssaiRegNo=$p["fssaiRegNo"];
		$serviceTaxNo=$p["serviceTaxNo"];
		$bankIFSCCode=$p["bankCode"];

		if ($supplierId!="" && $name!="" && $code!="") {
			$supplierRecUptd = $supplierMasterObj->updateSupplier($supplierId, $code, $name, $address, $phone, $vatNo, $cstNo, $frozen, $inventory, $rte, $pinCode, $faxNo, $email, $panNo, $place, $landingCenter, $paymentBy, $bankAcNo, $bankName, $supplierStatus,$fssaiRegNo,$serviceTaxNo,$bankIFSCCode);
		}
	
		if ($supplierRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSupplierInventoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierInventory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSupplierInventoryUpdate;
		}
		$supplierRecUptd	=	false;
	}

	# Delete Supplier
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierId	=	$p["delId_".$i];
			# check record linked with any other table
			//$isStockUsed = $supplierMasterObj->checkRecordUsed($supplierId);
			if ($supplierId!="") {
				// Need to check the selected Category is link with any other process
				# Checking the selected Supplier is link with any other process 
				$supplierRecInUse = $supplierMasterObj->supplierRecInUse($supplierId);
				if (!$supplierRecInUse) {
					# Supplier Landing Center Delete
					$supplier2centerDel = $supplierMasterObj->deleteSupplier2Center($supplierId);
					# Sub Supplier Rec Del
					$subSupplierRecDel  = $supplierMasterObj->deleteSubSupplierRec($supplierId);	
					# Main Rec Delete
					$supplierRecDel	= $supplierMasterObj->deleteSupplier($supplierId);	
				}
			}
		}
		if ($supplierRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierInventory);
			$sessObj->createSession("nextPage",$url_afterDelSupplierInventory.$selection);
		} else {
			$errDel	=	$msg_failDelSupplierInventory;
		}
		$supplierRecDel	=	false;
	}



	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierId	=	$p["confirmId"];
			if ($supplierId!="") {
				// Checking the selected fish is link with any other process
				$supplierRecConfirm = $supplierMasterObj->updateSupplierconfirm($supplierId);
			}

		}
		if ($supplierRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsupplier);
			$sessObj->createSession("nextPage",$url_afterDelSupplierInventory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$supplierId = $p["confirmId"];
			if ($supplierId!="") {
				#Check any entries exist				
					$supplierRecConfirm = $supplierMasterObj->updateSupplierReleaseconfirm($supplierId);				
			}
		}
		if ($supplierRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsupplier);
			$sessObj->createSession("nextPage",$url_afterDelSupplierInventory.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="")	$pageNo=$g["pageNo"];
	else				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	// FRN - Frozen, INV - Inventory, RTE - RTE
	if ($g["sectionFilter"]!="") $sectionFilter = $g["sectionFilter"];
	else $sectionFilter = $p["sectionFilter"];

/*if ($suppdtflag==1)
{

}
else if ($suppdtflag==2){
$sectionFilter="INV";

}else if ($suppdtflag==3){

$sectionFilter="FRN";
}else if ($suppdtflag==4){
$sectionFilter="RTE";
}*/
/*if (in_array("FRN",$arrsuppdtflag))
{
$sectionFilter="FRN";
}
else if (in_array("INV",$arrsuppdtflag))
{
$sectionFilter="INV";
}
else if (in_array("RTE",$arrsuppdtflag))
{
$sectionFilter="RTE";
}*/

//if ($suppdtflag==1)

$int=0;
if ($p[sectionFilter]!="")
{
$int=1;
}

if ($int==0){

	if($p[sectionFilter]=="-1")
					//if ((in_array("FRN",$arrsuppdtflag))&& (in_array("INV",$arrsuppdtflag)) && (in_array("RTE",$arrsuppdtflag)))
					{
					$flag=1;						
					}
	# Resettting offset values	
	if ($p["hidSectionFilter"]!=$p["sectionFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Supplier
$combodisplay=0;
	//if ($flag==1)
	 if (($acessInv && $acessFrn && $acessRTE )) {
	//{
		//echo "You entered Inv and RTE and FRN Only";
$combodisplay=3;

	$supplierRecords	= $supplierMasterObj->fetchAllPagingRecords($offset, $limit, $sectionFilter);
	$supplierSize		= sizeof($supplierRecords);
	}else 
	if (($acessInv) && ($acessFrn)){
$combodisplay=2;
$defaultFRN='FRN';
		//echo "You entered Frozen and Inv Only";
//$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsfrninv($offset, $limit, $sectionFilter);//new comment
$sectionFilter="FRN";
$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);
$supplierSize		= sizeof($supplierRecords);

}

else if (($acessFrn) && ($acessRTE))
	{
$combodisplay=2;
$defaultFRN='FRN';
	//echo "You entered Frozen and RTE Only";
//$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsfrnrte($offset, $limit, $sectionFilter); new comment
$sectionFilter="FRN";
$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);
$supplierSize		= sizeof($supplierRecords);

	}
	else if (($acessInv) && ($acessRTE))
	{
$combodisplay=2;
$defaultINV='INV';

		//echo "You entered Inv and RTE Only";
/*$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsinvrte($offset, $limit, $sectionFilter);*/
$sectionFilter="INV";
$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);

$supplierSize		= sizeof($supplierRecords);

	}
	else if ($acessInv){
$combodisplay=1;
	//echo "You entered Inventory Only";
$sectionFilter="INV";
$supplierRecords	= $supplierMasterObj->fetchAllPagingRecords($offset, $limit, $sectionFilter);
$supplierSize		= sizeof($supplierRecords);
}
else if ($acessFrn){
$combodisplay=1;
	//echo "You entered Frozen Only";
$sectionFilter="FRN";
$supplierRecords	= $supplierMasterObj->fetchAllPagingRecords($offset, $limit, $sectionFilter);
$supplierSize		= sizeof($supplierRecords);
}
else if ($acessRTE){
$combodisplay=1;
		//echo "You entered RTE Only";
$sectionFilter="RTE";
$supplierRecords	= $supplierMasterObj->fetchAllPagingRecords($offset, $limit, $sectionFilter);
$supplierSize		= sizeof($supplierRecords);
}

	


	/*else if ((in_array("FRN",$arrsuppdtflag))&& (in_array("INV",$arrsuppdtflag)))
	{
	$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsfrninv($offset, $limit, $sectionFilter);
	$supplierSize		= sizeof($supplierRecords);

	}
	else if ((in_array("FRN",$arrsuppdtflag))&& (in_array("RTE",$arrsuppdtflag)))
	{
	$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsfrnrte($offset, $limit, $sectionFilter);
	$supplierSize		= sizeof($supplierRecords);

	}
	else if ((in_array("INV",$arrsuppdtflag))&& (in_array("RTE",$arrsuppdtflag)))
	{
	$supplierRecords	= $supplierMasterObj->fetchAllPagingRecordsinvrte($offset, $limit, $sectionFilter);
	$supplierSize		= sizeof($supplierRecords);

	}
	else if ((in_array("FRN",$arrsuppdtflag)))
	{
		$sectionFilter="FRN";
		$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);
	}
	else if ((in_array("INV",$arrsuppdtflag)))
	{
		$sectionFilter="INV";
		$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);
	}
		else if ((in_array("RTE",$arrsuppdtflag)))
	{
		$sectionFilter="RTE";
		echo "hai";
		$supplierRecords	= $supplierMasterObj->fetchAllRecordsfil($sectionFilter);
	}*/
}
else
{
$supplierRecords	= $supplierMasterObj->fetchAllRecords($sectionFilter);

}

//echo $sectionFilter;
//echo "$defaultFRN-$combodisplay";
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($supplierMasterObj->fetchAllRecords($sectionFilter));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode==true) {
		//$landingcenterList = $landingcenterObj->fetchAllRecords();

		$landingcenterList = $landingcenterObj->fetchAllRecordsActiveLanding();
	} else if ($editMode!="" && $frozen!='') {
		$landingcenterList = $supplierMasterObj->fetchCenterSelectedRecords($editId);
	}else if ($editMode!="" && $frozen=='') {
		//$landingcenterList = $landingcenterObj->fetchAllRecords();
		$landingcenterList = $landingcenterObj->fetchAllRecordsActiveLanding();
	}



	if ($editMode)		$heading = $label_editSupplierInventory;
	else			$heading = $label_addSupplierInventory;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS = "libjs/SupplierMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSupplier" action="SupplierMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>			
		</tr>
		<?}?>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Supplier";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
	<tr>
		<td colspan="3" align="center">
		<table width="85%" align="center">
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplier(document.frmSupplier);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierMaster.php');">&nbsp;&nbsp;
<?php
													if (($addInv) && ($sectionFilter=='INV')){
													?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >

												<?php } else if (($addFRN) && ($sectionFilter=='FRN')) {?>

												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($addRTE) && ($sectionFilter=='RTE')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }  else if (($addInv)  && ($sectionFilter=='-1')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }  else if (($addFRN) && ($sectionFilter=='-1')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($addRTE)  && ($sectionFilter=='-1')){ ?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($add) && ($sectionFilter=='')){?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }?>



												<!--<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);">-->												</td>

												<?}?>
											</tr>
	<input type="hidden" name="hidSupplierId" value="<?=$editSupplierId;?>">
	<tr><TD colspan="2" height="10"></TD></tr>
<tr>
	<TD colspan="2" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD valign="top">

					<!--<fieldset>-->
					<?php			
						$entryHead = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
		  <td class="fieldName" nowrap >*Code</td>
		  <td><input type="text" name="code" size="20" value="<?=$code;?>"/></td>
	  </tr>
	<tr>
		  <td class="fieldName" nowrap >*Name </td>
		  <td><input type="text" name="supplierName" size="20" value="<?=$name;?>" /></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Address</td>
		<td><textarea name="address"><?=$address;?></textarea></td>
	</tr>
	<tr>
		  <td class="fieldName" nowrap >Pin Code</td>
		  <td ><input type="text" name="pinCode" size="10" value="<?=$pinCode;?>" /></td>
	</tr>
	<tr>
		  <td class="fieldName" nowrap >Phone No</td>
		  <td><input type="text" name="phoneNo" value="<?=$phone;?>"></td>
  	</tr>
	<tr>
		<td class="fieldName" nowrap >Fax No</td>
		<td><INPUT TYPE="text" NAME="faxNo" size="30" value="<?=$faxNo;?>"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Email</td>
		<td><INPUT TYPE="text" NAME="email" size="30" maxlenght="10" value="<?=$email;?>"></td>
	</tr>
	
	</table>
	<?php
			require("template/rbBottom.php");
		?>
	<!--</fieldset>-->
				</TD>
	<td>&nbsp;</td>
				<td valign="top">
	<!--<fieldset>-->
	<?php			
		$entryHead = "";
		require("template/rbTop.php");
	?>
	<table>
	<tr>
		<td class="fieldName" nowrap >FSSAI Regn No</td>
		<td><INPUT TYPE="text" NAME="fssaiRegNo" size="30" value="<?=$fssaiRegNo;?>"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >PAN No</td>
		<td><INPUT TYPE="text" NAME="panNo" size="30" value="<?=$panNo;?>"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >VAT No</td>
		<td><input type="text" name="vatNo" value="<?=$vatNo;?>"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >CST No</td>
		<td><input type="text" name="cstNo" value="<?=$cstNo;?>"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Service Tax No</td>
		<td>
		<input type="text" name="serviceTaxNo" value="<?=$serviceTaxNo;?>" size="30">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Bank Account No</td>
		<td><input type="text" name="bankAcNo" value="<?=$bankAcNo;?>" size="30"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Bank name</td>
		<td><input type="text" name="bankName" value="<?=$bankName;?>" size="30"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Bank IFSC Code</td>
		<td><input type="text" name="bankCode" value="<?=$bankCode;?>" size="30"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >Active</td>
		<td>
		<select name="supplierStatus">
			<option value="Y" <?=($supplierStatus=='Y')?"selected":""?>>Yes</option>
			<option value="N" <?=($supplierStatus=='N')?"selected":""?>>No</option>
		</select>
		</td>
	</tr>
	
	</table>
	<?php
		require("template/rbBottom.php");
	?>
	<!--</fieldset>-->
				</td>
			</TR>
		</table>
	</TD>
</tr>	
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD colspan="2">
							<!--<fieldset><legend class="fieldName">Section</legend>-->
							<?php			
								$entryHead = "Section";
								require("template/rbTop.php");
							?>
							<table align="center">

							<?php 	 if ($sectionFilter=='-1'){?>

							<tr>
							<?php if ($addFRN){?>
							<TD>
										<INPUT type="checkbox" name="frozen" id="frozen" class="chkBox" value="Y" onclick="showFrnSection();" <?=$frozen?> >
									</TD>
									<td class="listing-item">Frozen</td>
									<?php }?>
									
									<?php if ($addInv){?>
									<TD>
										<INPUT type="checkbox" name="inventory" class="chkBox" value="Y" <?=$inventory?>>
									</TD>
									
									<td class="listing-item">Inventory</td>
									<?php }?>
									<?php if ($addRTE){?>
									<TD>
										<INPUT type="checkbox" name="rte" value="Y" class="chkBox" <?=$rte?>>
									</TD>
									<td class="listing-item">RTE</td>
									<?php }?>
							
							
							</tr>
							<?php } else {?>
								<TR>
								<? if ($sectionFilter=='FRN'){?>
								<?php if ($addFRN){?>
									<TD>
										<INPUT type="checkbox" name="frozen" id="frozen" class="chkBox" value="Y" onclick="showFrnSection();" <?=$frozen?> >
									</TD>
									
									<td class="listing-item">Frozen</td>
									 <?php }}?>
									 <? if ($sectionFilter=='INV'){?>
									 <?php if ($addInv){?>
									<TD>
										<INPUT type="checkbox" name="inventory" class="chkBox" value="Y" <?=$inventory?>>
									</TD>
									<td class="listing-item">Inventory</td>
									<?php }}?>
									<? if ($sectionFilter=='RTE'){?>
									<?php if ($addRTE){?>
									<TD>
										<INPUT type="checkbox" name="rte" value="Y" class="chkBox" <?=$rte?>>
									</TD>
									<td class="listing-item">RTE</td>
									<?php }?>
									<?php } }?>
								</TR>
							</table>
							<?php
								require("template/rbBottom.php");
							?>
							<!--</fieldset>-->
						</TD>
					</tr>
	<tr><TD height="5"></TD></tr>
	<tr id="frnSectionId" style="display:none;">
		<TD colspan="2">
			<!--<fieldset><legend class="fieldName">Frozen</legend>-->
			<?php			
				$entryHead = "Frozen";
				require("template/rbTop.php");
			?>
					<table cellpadding="2"  width="70%" cellspacing="0" border="0" align="center">
						<tr>
							  <td class="fieldName" nowrap >*Place</td>
							  <td >
								<select name="place" id="place" >
								<option value="">--Select--</option>
								<?
								 foreach($landingcenterList as $landingcenterRecord) {
									$centerid = $landingcenterRecord[0];
									$recordCenterId	=	$landingcenterRecord[4];
									$select="";
									if($centerid==$placeId){
										$select="selected";
									}
							  	?>
							<option value="<?=$centerid;?>" <?=$select;?>><?=$landingcenterRecord[1];?></option>
							<?
								}
							?>
						  </select>
						</td>
		  				</tr>
						<tr>
							<td class="fieldName" nowrap >*Landing Center </td>
							<td >						
								<select name="landingCenter[]" size="5" multiple="multiple" id="landingCenter" >
								  <option value="0">--Select--</option>
								<?
								  foreach($landingcenterList as $landingcenterRecord) {
									$centerid = $landingcenterRecord[0];
									$recordCenterId	= $landingcenterRecord[4];
									$select="";
									if($centerid==$recordCenterId){
										$select="selected";
									}
							  	?>
								<option value="<?=$centerid;?>" <?=$select;?>><?=$landingcenterRecord[1];?></option>
								<?
									}
								?>
								</select>				
							</td>
								</tr>
							<tr>
										  <td  height="10" class="fieldName">Payment By </td>
								          <td  height="10" ><select name="paymentBy" id="paymentBy">
										  <option value="E" <? if($paymentBy=='E') echo "Selected";?>>Effective</option>
										  <option value="D" <? if($paymentBy=='D') echo "Selected";?>>Declared</option>
								            </select>
								          </td>
								          <td  height="10" ></td>
								          <td  height="10" ></td>
								          <td  height="10" ></td>
								  </tr>
									<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
								</table>
						<?php
							require("template/rbBottom.php");
						?>
						<!--</fieldset>-->
						</TD>
					</tr>
											

											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplier(document.frmSupplier);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierMaster.php');">&nbsp;&nbsp;

												<?php
													if (($addInv) && ($sectionFilter=='INV')){
													?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >

												<?php } else if (($addFRN) && ($sectionFilter=='FRN')) {?>

												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($addRTE) && ($sectionFilter=='RTE')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }  else if (($addInv)  && ($sectionFilter=='-1')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }  else if (($addFRN) && ($sectionFilter=='-1')) {?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($addRTE)  && ($sectionFilter=='-1')){ ?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php } else if (($add) && ($sectionFilter=='')){?>
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplier(document.frmSupplier);" >
												<?php }?>


												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>
						<?php
							require("template/rbBottom.php");
						?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
		</table>
		</td>
	</tr>
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="20" ></td>
	</tr>
	<?php
		}
	?>
	<tr>
	<td colspan="3" align="center">
		<table width="20%" align="center" border="0">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>

				<input type="hidden" name="combodisplay" value=<?=$combodisplay;?> />
	
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">	
				<tr>	
<?php //echo $sectionFilter;


if ($combodisplay!=1){
?>
				
                                    <td class="listing-item">Section</td>
					<td style="padding-left:5px; padding-right:5px;">
					<select name="sectionFilter" onchange="this.form.submit();">
					<?php //if ($suppdtflag==1)
					//if ((in_array("FRN",$arrsuppdtflag))&& (in_array("INV",$arrsuppdtflag)) && (in_array("RTE",$arrsuppdtflag)))
					?>
					<?php if (($acessInv) && ($acessFrn) && ($acessRTE))
					{?>
                                        <option value="-1" <? if (($sectionFilter=='-1') || ($sectionFilter=="")){  echo "selected";?> <?php }?> >--Select All--</option>
										<?php }?>
										<?php //if ($suppdtflag==3)
										
										//if (in_array("FRN",$arrsuppdtflag))
										if (($acessFrn)){?>
						<option value="FRN" <? if ($sectionFilter=='FRN') echo "selected";?>>Frozen</option>
						<?php }?>
						
												<?php //if ($suppdtflag==2)
						//if (in_array("INV",$arrsuppdtflag))
						if (($acessInv))
						{?>
						<option value="INV" <? if ($sectionFilter=='INV') echo "selected";?>>Inventory</option>
						<?php }?>
					<?php //if ($suppdtflag==4)
						//if (in_array("RTE",$arrsuppdtflag))
						if (($acessRTE))
						{?>
						<option value="RTE" <? if ($sectionFilter=='RTE') echo "selected";?>>RTE</option>
						<?php }?>
	


                                        </select>

</td>

<?php } else {?>  <td class="listing-item">Section:<?=$sectionFilter;?>
<input type=hidden name=sectionFilter value=<?=$sectionFilter;?>>


</td><td style="padding-left:0px; padding-right:5px;">&nbsp;<?//=$sectionFilter;?></td>

<?php } ?>
                         	</tr>
			  </table>
			<?php
				require("template/rbBottom.php");
			?>
		</td>
		</tr>
		</table>	

	</td>
</tr>							
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier</td>
<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<!--<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierInventory.php?sectionFilter=<?=$sectionFilter?>',700,600);"><? }?></td>-->


												<td nowrap><? //if($del==true){
													
													if (($delFRN || $delInv || $delRTE ) || (!$delFRN && !$delInv && !$delRTE && $del==true )) {
													
													?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierSize;?>);"><? }?>&nbsp;<? //if($add==true)
												
												if (($addFRN || $addInv || $addRTE ) || (!$addFRN && !$addInv && !$addRTE && $add==true )) {
   
?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? 

//if($print==true){
//if($accesscontrolObj->canPrint()){$print=true;}

//echo "The current print value is $print";
if ($sectionFilter=='RTE'){
if(!$printRTE){
$sectionFilter1=0;
}
else{
$sectionFilter1="RTE";
}
}
else if ($sectionFilter=='INV'){
if(!$printInv){
$sectionFilter1=0;
}
else{
$sectionFilter1="INV";
}
}
else if ($sectionFilter=='FRN'){
if(!$printFRN){
$sectionFilter1=0;
}
else{
$sectionFilter1="FRN";
}
}
else if ($sectionFilter=='-1'){
if(!$print){
$sectionFilter1=0;
}
else{
$sectionFilter1="-1";
}
}
else if ($sectionFilter=="")
{
if((!$printRTE) || (!$printInv) || (!$printFRN)){
$sectionFilter1="0";
}

}





	if (($printFRN || $printInv || $printRTE ) || (!$printFRN && !$printInv && !$printRTE && $print==true )) {
	
	?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierInventory.php?sectionFilter=<?=$sectionFilter1?>',700,600);"><? }?></td>




											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if (sizeof($supplierRecords)>0) {
				$i	=	0;
		?>
	<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="13" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"  >-->
		
		<?php if ($sectionFilter=='INV'){?>

<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox" <?php if (!$delInv){?> disabled <?php }?> ><?php } else if ($sectionFilter=='FRN'){?>

<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox" <?php if (!$delFRN){?> disabled <?php }?>><?php }else if ($sectionFilter=='RTE'){?>

<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox" <?php if (!$delRTE){?> disabled <?php }?> ><?php } else {
	
	//echo "The value of $del";
	?>

<input type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox" <?php
	
if ($del!=1){
	
	
	
	?> disabled <?php }?> >

	<?php }?>
		
		</th>
		<th style="padding-left:5px; padding-right:5px;">Code</th>
		<th style="padding-left:5px; padding-right:5px;">Name</th>					
		<th style="padding-left:5px; padding-right:5px;">Phone</th>
		<th nowrap style="padding-left:5px; padding-right:5px">Landing<br>Centers </th>
		<th style="padding-left:5px; padding-right:5px">No.of<br>Sub-Supp</th>
<?php if (($combodisplay==3) || ($sectionFilter=='-1')){?>
<th style="padding-left:5px; padding-right:5px">Frozen</th><th style="padding-left:5px; padding-right:5px">Invtry </th><th style="padding-left:5px; padding-right:5px">RTE</th>



<?php } else {?>
<?php if (($sectionFilter=='FRN') || ($defaultFRN=='FRN')){?> 

		<th style="padding-left:5px; padding-right:5px">Frozen</th>
<?php }?>
<?php if (($sectionFilter=='INV') || ($defaultINV=='INV' )){?> 
		<th style="padding-left:5px; padding-right:5px">Invtry </th>
<?php }?>
<?php if (($sectionFilter=='RTE'))
{?>
		<th style="padding-left:5px; padding-right:5px">RTE</th>
<?php }

}
?>



		<th nowrap style="padding-left:5px; padding-right:5px;">Active/<br>Inactive</th>
<? if($edit==true){?>
<th>&nbsp;</th>

	 <? } else if (($editFRN) && (($sectionFilter=='FRN')|| ($defaultFRN=='FRN'))){  
		  
		  
		  ?>
		<th>&nbsp;</th>
<? } else if (($editInv )&& ($sectionFilter=='INV')){?> 

	<th>&nbsp;</th>
<? } else if (($editRTE ) && ($sectionFilter=='RTE')){?>
<th>&nbsp;</th>
<? } else if (($sectionFilter=='-1') && ($edit==true)){?>
<th>&nbsp;</th>
<? } else {?>
<th>&nbsp;</th><?php }?>
	<? //if($add==true){
			if (($addFRN || $addInv || $addRTE ) || (!$addFRN && !$addInv && !$addRTE && $add==true )) {
		?>
		<th width="50" nowrap="nowrap">&nbsp;</th>
	<? }?>
	<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	$supplierId = "";
	foreach($supplierRecords as $sr) {
		$i++;
		$supplierId		= $sr[0];
		$supplierCode		= stripSlash($sr[1]);
		$supplierName		= stripSlash($sr[2]);
		$address		= $sr[3];
		$phoneNo		= $sr[4];
		$vatNo			= $sr[5];
		$cstNo			= $sr[6];

		$frozenChk		= $sr[7];
		$inventoryChk		= $sr[8];
		$rteChk			= $sr[9];
		$suppStatus		= $sr[10];
		$active=$sr[11];
	
		$centerRecords 		= "";
		$noOfSubSuppliers 	= "";
		if ($frozenChk=='Y') {
			#Find the Grade from The procescode2grade TABLE
			$centerRecords	= $supplierMasterObj->fetchCenterRecords($supplierId);
			#Find No.of Sub Suppliers
			$noOfSubSuppliers = $supplierMasterObj->getNumberOfSubSuppliers($supplierId);
		}
		
	?>
<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
<td width="20" align="center">
<?php
	//if($accesscontrolObj->canDel()) $del=true;	

	if ($sectionFilter=='-1'){?>

<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierId;?>" class="chkBox" <?php if (!$del){?> disabled <?php }?> ><?php } else
	
	if (($sectionFilter=='INV')){?>

<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierId;?>" class="chkBox" <?php if (!$delInv){?> disabled <?php }?> ><?php } else if (($sectionFilter=='FRN') ){?>

<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierId;?>" class="chkBox" <?php if (!$delFRN){?> disabled <?php }?>><?php }else if ($sectionFilter=='RTE'){?>

<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierId;?>" class="chkBox" <?php if (!$delRTE){?> disabled <?php }?> ><?php } else {
	
	//echo "The value of $del";
	?>

<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierId;?>" class="chkBox" <?php
	
if (!$del){
	
	
	
	?> disabled <?php }?> >

	<?php }?>


</td>
<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplierCode;?></td>
<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplierName;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$phoneNo?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px" align="left">
				<?php
					$numLine = 3;
					if (sizeof($centerRecords)>0) {
						$nextRec	=	0;									
						foreach ($centerRecords as $centerR) {
							$landingCenter = $centerR[5];
							$nextRec++;
							if ($nextRec>1) echo "&nbsp;,&nbsp;"; echo $landingCenter;
							if ($nextRec%$numLine == 0) echo "<br/>";
					 	}
					}
				?>		
</td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?=$noOfSubSuppliers?></td>


<?php if (($combodisplay==3) || ($sectionFilter=='-1')){?>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($frozenChk=='Y'){?><img src="images/y.png" /><?}?></td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($inventoryChk=='Y'){?><img src="images/y.png" /><?}?></td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($rteChk=='Y'){?><img src="images/y.png" /><?}?></td>

<?php } else {?>

<?php if (($sectionFilter=='FRN') || ($defaultFRN=='FRN')){?> 
	<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($frozenChk=='Y'){?><img src="images/y.png" /><?}?></td>
<?php }?>

<?php if (($sectionFilter=='INV') || ($defaultINV=='INV')){?>
	<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($inventoryChk=='Y'){?><img src="images/y.png" /><?}?></td>
<?php }?>

<?php if (($sectionFilter=='RTE'))
{?>

	<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?if($rteChk=='Y'){?><img src="images/y.png" /><?}?></td><?php }
}

?>	
	<td align="center" id="statusRow_<?=$i?>">

	<?php 
		
	//echo $confirm;
	if ($active==0){?>
	<img src="images/x.png" border="0" />
	<?php } else {?>
	<a href="###" class="link5">
		<? if($suppStatus=='Y'){?>
			<img src="images/y.png" border="0" onMouseover="ShowTip('Click here to Inactive');" onMouseout="UnTip();" onclick="return validateSuppStatus('<?=$supplierId?>','<?=$i?>');"/>
		<? } else { ?>
			<img src="images/x.png" border="0" onMouseover="ShowTip('Click here to activate');" onMouseout="UnTip();" onclick="return validateSuppStatus('<?=$supplierId?>','<?=$i?>');"/>
		<? }?>
	</a>

	<?php }?>
	</td>
<?php //if($edit==true){
//if($accesscontrolObj->canEdit()) $edit=true;
//echo "The value of $edit";
	//if (($editFRN || $editInv || $editRTE ) || (!$editFRN && !$editInv && !$editRTE && $edit==true )) {
		if (($editFRN) && ($sectionFilter=='FRN')){
		
		?>
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierMaster.php';">
			 <? } ?>
		</td>
<?php } else if (($editInv )&& ($sectionFilter=='INV')){?> 
<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierMaster.php';"> <? } ?>
		</td>

<?php } else if (($editRTE ) && ($sectionFilter=='RTE')){?>
<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
			 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierMaster.php';">
			 <? } ?>
		</td>
		
<? } else if (($sectionFilter=='-1') && ($edit==true)){?>

<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierMaster.php';"><? } ?>
		</td>
		<?php } 
 else if (($sectionFilter=='') && ($edit==true)){?>

<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;">
	 <?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierMaster.php';"><? } ?>
		</td>
		<?php } 

else {?><td>&nbsp;</td><?php }?>
		<? //if($add==true){
			if (($addFRN || $addInv || $addRTE ) || (!$addFRN && !$addInv && !$addRTE && $add==true )) {
		
		?>		
		<td class="listing-item" align="center" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
			<?php 
			
				if ($frozenChk=='Y') { 
					//echo $sectionFilter;
			if (($addFRN) && (($sectionFilter=='FRN') || ($combodisplay=="3") || ($defaultFRN=='FRN'))){
			?>
			<input type="button" value=" Add Sub-Supplier " name="cmdSubSupplier" onClick="return printWindow('SubSuppliers.php?sid=<?=$supplierId?>&name=<?=$supplierName?>&popupWindow=1',700,600);" style="width:100px;">
			<?php }?>
			<?
				}
			?>
		</td>
		<? }?>


		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierId;?>,'confirmId');" >
			<?php } }?>
			<? }?>
			
			
			
			</td>
</tr>
											<?
												}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
										<input type="hidden" name="editSelectionChange" value="0">

<? if($maxpage>1){?>
		<tr>
		<td colspan="13" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
	</tbody>
	</table>									
	</td>
					</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<!--<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierInventory.php?sectionFilter=<?=$sectionFilter?>',700,600);"><? }?></td>-->


<td nowrap><? //if($del==true){
													
													if (($delFRN || $delInv || $delRTE ) || (!$delFRN && !$delInv && !$delRTE && $del==true )) {
													
													?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierSize;?>);"><? }?>&nbsp;<? //if($add==true)
												
												if (($addFRN || $addInv || $addRTE ) || (!$addFRN && !$addInv && !$addRTE && $add==true )) {
   
?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? 

//if($print==true){



	if (($printFRN || $printInv || $printRTE ) || (!$printFRN && !$printInv && !$printRTE && $print==true )) {
	
	?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierInventory.php?sectionFilter=<?=$sectionFilter1?>',700,600);"><? }?></td>

												
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	<input type="hidden" name="hidSectionFilter" value="<?=$sectionFilter?>">	
	</table>
	<?
		if ($editMode && $frozen!='') {
	?>
	<script language="JavaScript">
		showFrnSection();
	</script>
	<?
		}
	?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>


