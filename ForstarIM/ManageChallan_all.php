<?php

	require("include/include.php");
	require_once("lib/invoice_ajax.php");
	ob_start();
	$err			= "";
	$errDel			= "";	
	$noRec			= "";	
	$editMode		= false;
	$addMode		= false;

	// ------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	//$accesscontrolObj->getAccessControl($moduleId,$functionId);
	$is_admin = false;
	if($roleId=="1"){
		$is_admin = true;
	}
	//if (!$accesscontrolObj->canAccess()) {
	if(!$is_admin){	
	//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	$add	=true;
	$edit	=true;
	$del	=true;
	$print	=true;
	$confirm=true;
	
	//if ($accesscontrolObj->canAdd()) $add=true;
	//if ($accesscontrolObj->canEdit()) $edit=true;
	//if ($accesscontrolObj->canDel()) $del=true;
	//if ($accesscontrolObj->canPrint()) $print=true;
	//if ($accesscontrolObj->canConfirm()) $confirm=true;
	//----------------------------------------------------------
	# Selection
	if($filterFunctionType)
	{
	$selection = "?pageNo=".$p["pageNo"]."&filterFunctionType=".$p["filterFunctionType"];
	}
	if($filterUnitName)
	{
	$selection = "?pageNo=".$p["pageNo"]."&filterUnitName=".$p["filterUnitName"];
	}
	if($filterCompanyName)
	{
	$selection = "?pageNo=".$p["pageNo"]."&filterCompanyName=".$p["filterCompanyName"];
	}
	if($filterYear)
	{
	$selection = "?pageNo=".$p["pageNo"]."&filterYear=".$p["filterYear"];
	}

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode  =   true;	
	if ($p["cmdCancel"]!="") $addMode   =  false;

	/*
	#Add a Record
	if ($p["cmdAdd"]!="") {	
		//$idRcCheck="";
		$functionType	= $p["functionType"];
		$billingCompany	= $p["billingCompany"];
		$idDateFrom	= mysqlDateFormat($p["idDateFrom"]);
		$idDateTo	= mysqlDateFormat($p["idDateTo"]);
		$startNo	= trim($p["startNo"]);
		$endNo		= trim($p["endNo"]);
		$dEntryLimitDays = trim($p["dEntryLimitDays"]);
		$soInvoiceType	 = $p["soInvoiceType"];
		$exporter		 = $p["exporter"];
		$alpha_code_prefix	= $p["alpha_code_prefix"];
		$unitid=$p["unitidInv"];
		$auto_Generate	= $p["auto_Generate"];
		$soInvoiceType  =$p["soInvoiceType"];
		 
		//die;
		// if ($functionType=="PO"){
		// $unitid=$p["unitidInv"];
		// }
		// else{
		// $unitid=$p["unitid"];
		// }
		if($functionType!='SL')
		{
			$idRcCheck = $manageChallanObj->CheckExistance($functionType,$billingCompany,$idDateFrom,$idDateTo,$unitid,$soInvoiceType);
			
		}//die();
		if(sizeof($idRcCheck)>0)
		{
		//echo "hii";
			echo "<script type='text/javascript'>alert('Unit already exist.Cannot save data');</script>'";
		}
		else
		{
			//die();
			if ($alpha_code_prefix!="") $chkUnique = $manageChallanAllObj->checkAlphaCodeExist($alpha_code_prefix, "");
			
			if ($functionType!="" && $idDateFrom!="" && $idDateTo!="" && $startNo && $endNo && !$chkUnique) {
				$idRcIns = $manageChallanAllObj->addIdGenRec($functionType, $billingCompany, $idDateFrom, $idDateTo, $startNo, $endNo, $dEntryLimitDays, $soInvoiceType, $exporter,$unitid,$alpha_code_prefix,$auto_Generate);
			}
			if ($idRcIns) 
			{
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddIdRestriction);
				$sessObj->createSession("nextPage",$url_afterAddIdRestriction.$selection);
			}
			else 
			{
				$addMode = true;
				$err	 = $msg_failAddIdRestriction;
			}
			$idRcIns = false;
		}
	}
*/
	# Edit a Record
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		$idGenRec	= $manageChallanObj->find($editId);
		$editIdGenRecId	= $idGenRec[0];
		$functionType	= $idGenRec[1];
		$billingCompany	= $idGenRec[2];
		$idDateFrom	= ($idGenRec[3]!="")?dateFormat($idGenRec[3]):"";
		$idDateTo	= ($idGenRec[4]!="")?dateFormat($idGenRec[4]):"";
		$startNo	= $idGenRec[5];
		$endNo		= $idGenRec[6];
		$dEntryLimitDays = $idGenRec[7];
		$soInvoiceType  = $idGenRec[8];
		$exporter		= $idGenRec[9];
		$unitid=$idGenRec[11];
		$alpha_code_prefix	= $idGenRec[12];
		$challan_statusval	= $idGenRec[13];
		$auto_generateval	= $idGenRec[14];
		//$unitRecordsInv=$plantandunitObj->fetchAllRecordsPlantsActive();
		list($billingCompanyRecords,$unitRecords,$departmentRecords,$defaultUserCompanyId)= $manageusersObj->getUserReferenceSet($userId);
		$unitRecordsInv=$unitRecords[$billingCompany];
		//PRINTR($unitRecordsInv);
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$recordId		= $p["hidRecId"];
		$functionType	= $p["functionType"];
		$billingCompany	= $p["billingCompany"];
		$idDateFrom	= mysqlDateFormat($p["idDateFrom"]);
		$idDateTo	= mysqlDateFormat($p["idDateTo"]);
		$startNo	= trim($p["startNo"]);
		$endNo		= trim($p["endNo"]);
		$dEntryLimitDays = trim($p["dEntryLimitDays"]);
		$soInvoiceType	 = $p["soInvoiceType"];
		$exporter		 = $p["exporter"];
		$alpha_code_prefix	= $p["alpha_code_prefix"];
		$unitid=$p["unitidInv"];
		$disable=$p["disable"];
		$auto_Generate	= $p["auto_Generate"];
		 
		if($disable=="")
		{
		$disable=0;
		}
		if($auto_Generate=="")
		{
		$auto_Generate	=0;
		}
		// if ($functionType=="PO"){
		// $unitid=$p["unitidInv"];
		// }
		// else{
		// $unitid=$p["unitid"];
		// }
		
		if ($recordId!="" && $functionType!="" && $idDateFrom!="" && $idDateTo!="" && $startNo && $endNo) {
			$idGenRecUptd = $manageChallanObj->updateIdGenRec($recordId, $functionType, $billingCompany, $idDateFrom, $idDateTo, $startNo, $endNo, $dEntryLimitDays, $soInvoiceType, $exporter,$unitid,$alpha_code_prefix,$disable,$auto_Generate);
		}	
		if ($idGenRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIdRestrictionUpdate);
			$url_afterUpdateIdRestriction = "ManageChallan_all.php";
			$sessObj->createSession("nextPage",$url_afterUpdateIdRestriction.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIdRestrictionUpdate;
		}
		$idGenRecUptd	=	false;
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$recId	=	$p["delId_".$i];

			if ($recId!="") {
				// Need to check the selected Category is link with any other process
				$idGenRecDel = $manageChallanAllObj->deleteIdGenRec($recId);
			}
		}
		if ($idGenRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIdRestriction);
			$sessObj->createSession("nextPage",$url_afterDelIdRestriction.$selection);
		} else {
			$errDel	=	$msg_failDelIdRestriction;
		}
		$idGenRecDel	=	false;
	}	
	

	# Edit a MANAGE CHALLAN Record
	/*
	$manageChallanRec	=	$manageChallanObj->getManangeChallanRec();		
	$soDEntryLimitDays	= 	$manageChallanRec[4];
	$challanDEntryLimitDays	= 	$manageChallanRec[5];
	*/

	#Update  a record
	if ($p["cmdUpdateRec"]!="") {		
		$soDEntryLimitDays	= trim($p["soDEntryLimitDays"]);
		$challanDEntryLimitDays = trim($p["challanDEntryLimitDays"]);
		
		$manageChallanRecUptd = $manageChallanAllObj->updateChallanRecord($soDEntryLimitDays, $challanDEntryLimitDays);
	
		if ($manageChallanRecUptd) {
			$sessObj->createSession("displayMsg", $msg_succChallanRecordUpdate);	
		} else {
			$editMode	=	true;
			$err		=	$msg_failChallanRecordUpdate;
		}
		$manageChallanRecUptd		=	false;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	

	if ($g["filterFunctionType"]!="") $filterFunctionType = $g["filterFunctionType"];
	else  $filterFunctionType = $p["filterFunctionType"];
	
	if ($g["filterUnitName"]!="") $filterUnitName = $g["filterUnitName"];
	else $filterUnitName = $p["filterUnitName"];
	
	if ($g["filterCompanyName"]!="") $filterCompanyName = $g["filterCompanyName"];
	else $filterCompanyName = $p["filterCompanyName"];

	if ($g["filterYear"]!="") $filterYear = $g["filterYear"];
	else $filterYear = $p["filterYear"];

	
	if ($p["filterFunctionType"]!=$p["hidFilterFunctionType"]) {
		$offset	= 0;
	}
	if ($p["filterUnitName"]!=$p["hidFilterUnit"]) {
		$offset	= 0;
	}
	if ($p["filterCompanyName"]!=$p["hidFilterCompany"]) {
		$offset	= 0;
	}
	if ($p["filterYear"]!=$p["filterYear"]) {
		$offset	= 0;
	}


	# Get All Records
	$idGenRecords	= $manageChallanAllObj->fetchAllPagingRecords($offset, $limit, $filterFunctionType,$filterUnitName,$filterCompanyName,$filterYear);
	$idGenRecordsSize = sizeof($idGenRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($manageChallanAllObj->fetchAllRecords($filterFunctionType,$filterUnitName,$filterCompanyName,$filterYear));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	$companyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();
	$unitRcds=$plantandunitObj->fetchAllRecordsPlantsActive();

	if ($addMode || $editMode) {
		# Get Billing Comapany  Records
		//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();
		//	$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();
		# Exporter 
		$exporterRecs = $exporterMasterObj->findAll();
	}

	if($addMode)
	{
		list($billingCompanyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unitRecordsInv=$unitRecords[$defaultCompany];
	}

	#heading Section
	if ($editMode) $heading	= $label_editIdRestriction;
	else	       $heading	= $label_addIdRestriction;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/ManageChallan.js"; // For Printing JS in Head section
	//$ON_LOAD_PRINT_JS	= "libjs/invoice.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	//printr($idGenFunctions);
?>

<script language="javascript">
function funcSubmit(obj,ind){
	//alert(obj);
	if(ind=='1'){
	obj.form.filterUnitName.value="";
	obj.form.filterCompanyName.value="";
	if(typeof(obj.form.filterYear)=='object'){
		obj.form.filterYear.value="";
	}
	}
	if(ind=='2'){
	obj.form.filterCompanyName.value=""
	obj.form.filterFunctionType.value="";
	if(typeof(obj.form.filterYear)=='object'){
		obj.form.filterYear.value="";
	}
	}
	if(ind=='3'){
	obj.form.filterFunctionType.value="";
	obj.form.filterUnitName.value="";
	if(typeof(obj.form.filterYear)=='object'){
		obj.form.filterYear.value="";
	}
	}
	if(ind=='4'){
	obj.form.filterFunctionType.value="";
	obj.form.filterUnitName.value="";
	obj.form.filterCompanyName.value="";
	}
	
	
	obj.form.submit();
	//obj.frm.submit();
}

</script>
<form name="frmManageChallan" action="ManageChallan_all.php" method="post">
    <table cellspacing="0"  align="center" cellpadding="0" width="80%">	
    <tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
	<?php
		//if (!$filterFunctionType || !$filterUnitName || !$filterCompanyName ){
	?>
	<!--
	<tr> 
		<td height="10" align="center" class="listing-item" style="color:Maroon;">
			<strong>Challan restrictions for current financial year.</strong>
		</td>
	</tr>-->
	<?php
		//}
	?>
    <tr> 
      <td height="10" align="center" class="err1" > 
        <? if($err!="" ){?>
        <?=$err;?>
        <?}?>
      </td>
    </tr>	
    <?
	if( $editMode || $addMode)
	{
	?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
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
                  <td colspan="2" > <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ManageChallan_all.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageRMChallan(document.frmManageChallan);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ManageChallan_all.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageRMChallan(document.frmManageChallan);">                        </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidIPAddressId" value="<?=$editIPAddressId;?>">
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td  height="10" colspan="2" >
			<table width="200">
				<tr>
					<TD class="fieldName" nowrap="true">*Function Name:</TD>
					<td>
					<!--<select name="functionType" id="functionType" onchange="showBillingComapny();">-->
					<select name="functionType" id="functionType" onchange="xajax_chkUnitExist(document.getElementById('functionType').value,document.getElementById('billingCompany').value,document.getElementById('unitidInv').value);">
							<option value="">-- Select --</option>
							<?php
								foreach ($idGenFunctions as $fType=>$txtValue) {
									$selected = "";
									if ($functionType==$fType) $selected = "selected";
							?>
							<option value="<?=$fType?>" <?=$selected?>> <?=$txtValue?> </option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr id="billingComapanyRow">
					<TD class="fieldName" nowrap="true">*Billing Company:</TD>
					<td>
						<select name="billingCompany" id="billingCompany" onchange="xajax_getUnit(document.getElementById('billingCompany').value,'','');">
						<option value="">-- Select --</option>
						<?php
						foreach ($billingCompanyRecords as $bcr=>$bcrValue) {
							$billingCompanyId	= $bcr;
							$displayCName		= $bcrValue;
						//	$cName			= $bcr[1];
							
							//$defaultChk		= $bcrValue;
							$selected = "";
							if (($billingCompanyId==$billingCompany) || ($billingCompany=="" && ($billingCompanyId==$defaultCompany))  ) $selected = "selected";
						?>
						<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
						<?php	
							}		
						?>
						</select>
					</td>
				</tr>
				<!--seal number input type end-->
				<tr id="unitRowInv" >
					<TD class="fieldName" nowrap="true">*Unit:</TD>
					<td nowrap>
						 <select id="unitidInv" name="unitidInv" onchange="xajax_chkUnitExist(document.getElementById('functionType').value,document.getElementById('billingCompany').value,document.getElementById('unitidInv').value);">
						<!--<option selected="true" value="T">Taxable</option>
						<option value="S">Sample</option>-->
						<option value=''>--Select--</option>
					<?php
						foreach ($unitRecordsInv as $unitd=>$unitNm) {
							$unitId 		= $unitd;
							$unitName	= $unitNm;
							$selectedunitType = ($unitid==$unitId)?"selected":"";
							
					?>
						<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
					<?php
						}
						
					?>
					</select>
				</td>
			</tr>				
				
				<tr id="alphaCode" >
					<TD class="fieldName" nowrap="true">*Alpha Code Prefix:</TD>
					<td>
						<input type="text" id="alpha_code_prefix" name="alpha_code_prefix" value="<?php echo $alpha_code_prefix;?>" onKeyUp="xajax_chkAlphaCodeExist(document.getElementById('alpha_code_prefix').value,'<?=$editIdGenRecId?>');" autocomplete="off"/>
						<br/><span id="divPOIdExistTxt" class="listing-item" style="line-height:normal; font-size:10px; color:red;"></span>
						
					</td>
				</tr>
				
				
				
				
				<tr id="salesOrderRow">
					<TD class="fieldName" nowrap="true">*Invoice Type:</TD>
					<td>
						<select name="soInvoiceType" id="soInvoiceType">
						<option value="">-- Select --</option>
							<?php
								foreach ($invoiceTypeRecs as $invType=>$invValue) {
									$selected = "";
									if ($soInvoiceType==$invType) $selected = "selected";
							?>
							<option value="<?=$invType?>" <?=$selected?>> <?=$invValue?> </option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr id="exporterRow" style="display:none;">
					<TD class="fieldName" nowrap="true">*Exporter:</TD>
					<td nowrap>
						<select name="exporter" id="exporter" onchange="displayExporter();">
							  <option value="0">-- Select --</option>
							<?php
								foreach($exporterRecs as $er) {
									$exporterId		= $er[0];
									$exporterName	= $er[1];
									$exporterDisplayName =$er[10];
									$defaultChk		= $er[9];
									$selected 	= (($exporter==$exporterId) || ($defaultChk=='Y' && ($exporter=="" || $exporter==0) ))?"Selected":"";
							?>
							<option value="<?=$exporterId?>" <?=$selected?>><?=$exporterDisplayName?></option>
							<?php
								 }
							?>
							</select>
					</td>
					<?php
							//$exporter=17;	
							
							$unitRecords=$manageChallanObj->fetchAllRecordsUnitsActiveExpId($exporter);?>
				</tr>
<tr id="unitRow" style="display:none;">
					<TD class="fieldName" nowrap="true">*Unit:</TD>
					<td nowrap>
						 <select  id="unitid" name="unitid" onchange="displayExporter();>
					<!--<option selected="true" value="T">Taxable</option>
					<option value="S">Sample</option>-->
				<option value=''>--Select--</option>
			<?php
				foreach ($unitRecords as $unitd) {
					$unitId 		= $unitd[0];
					$unitName	= $unitd[1];
					$selectedunitType = ($unitid==$unitId)?"selected":"";
					
			?>
				<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
			<?php
				}
			?>
			</select>
					</td>
				</tr>
				<!--seal number input type start-->
				

			<tr colspan="2" align="center">
				<td class="fieldName" nowrap="true">
					<input type="checkbox" name="auto_Generate" id="auto_Generate" value="1">Autogenerate
				</td>
			</tr>
				
				 <? if($editMode){?>
				<tr colspan="2" align="center">
					<td class="fieldName" nowrap="true">
					<input type="checkbox" name="disable" id="disable" value="1">Deactive</td>
				</tr>
				<? } ?>

	<tr>
	<td class="listing-item" colspan="2">
		<table>
		<TR>
		<TD>
			<fieldset><legend class="listing-item">ID Restriction</legend>
			<table>
			<TR>
				<TD class="fieldName" nowrap>*Date range: From</TD>
				<TD>
					<input type="text" name="idDateFrom" id="idDateFrom" size="9" value="<?=$idDateFrom;?>" autocomplete="off" />
				</TD>
				<td class="fieldName" nowrap>To</td>
				<td>
					<input type="text" name="idDateTo" id="idDateTo" size="9" value="<?=$idDateTo?>" autocomplete="off" />
				</td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap>*Number accepted: From</TD>
				<TD><input type="text" name="startNo" size="10" value="<?=$startNo;?>" maxlength="10"></TD>
				<td class="fieldName" nowrap>To</td>
				<td><input type="text" name="endNo" size="10" value="<?=$endNo?>" maxlength="10"></td>
			</TR>
			</table>
			</fieldset>
		</TD>
		</TR>
		</table>
	</td>
         </tr>	
	<tr>
		<td class="listing-item" colspan="2">
			<table>
					<TR>
					<TD>
						<fieldset>
						<legend class="listing-item">Delayed Entry Limit</legend>
						<table>
							<TR>
								<TD class="fieldName" nowrap>No.of Days:</TD>
								<TD>
									<input type="text" name="dEntryLimitDays" id="dEntryLimitDays" size="3" autocomplete="off" value="<?=$dEntryLimitDays?>" style="text-align:right;" title="Enter number of days">
								</TD>
							</TR>
						</table>
						</fieldset>
					</TD>
					</TR>
				</table>
		</td>
	</tr>
         </table>
			</tr>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageChallan_all.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateManageRMChallan(document.frmManageChallan);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageChallan_all.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateManageRMChallan(document.frmManageChallan);"></td>
                        <?}?>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
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
			
			# Listing Grade Starts
		?>
    <tr> 
      <td height="10" align="center" ></td>
    </tr>
    <tr> 
      <td> 
	  
	  <div style="float:right;padding:10px;"><a href='ManageChallan.php' class="link1"><strong>View Current Financial Year (Active) Manage Challan</strong></a></div>
	  <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
			
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Challan All</td>
                  <td background="images/heading_bg.gif" class="pageName" >
			<table cellpadding="0" cellspacing="0" align="right">	
				<tr>
					<td nowrap="true" class="listing-item">Function Name:</td>
					<td nowrap="true">
						<!--<select name="filterFunctionType" id="filterFunctionType" onchange="this.form.submit();">-->
						<select name="filterFunctionType" id="filterFunctionType" onchange="funcSubmit(this,'1');">
							<option value="">-- Select All--</option>
							<?php
								foreach ($idGenFunctions as $fType=>$txtValue) {
									$selected = "";
									if ($filterFunctionType==$fType) $selected = "selected";
							?>
							<option value="<?=$fType?>" <?=$selected?>> <?=$txtValue?> </option>
							<?php
								}
							?>
						</select>
					</td>
					<td>&nbsp;</td>
				
					<td nowrap="true" class="listing-item">Unit Name:</td>
					<td nowrap="true">
						<!--<select name="filterFunctionType" id="filterFunctionType" onchange="this.form.submit();">-->
						<select name="filterUnitName" id="filterUnitName" onchange="funcSubmit(this,'2');">
							<option value="">-- Select All--</option>
							<?php
				foreach ($unitRcds as $unitd) {
					$unitId 		= $unitd[0];
					$unitName	= $unitd[2];
					$selectedunitType = ($filterUnitName==$unitId)?"selected":"";
					
			?>
				<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
			<?php
				}
			?>
								<?php
								/*
								//ECHO filterUnitName;
									foreach ($unitRcds as $unitd) {
										$unitId 		= $unitd[0];
										$unitName	= $unitd[2];
										//$selectedunitType = ($unitid==$unitId)?"selected":"";
										if ($filterUnitName==$unitId) $selected = "selected";
										
								?>
									<option value='<?=$unitId?>'<?=$selectedunitType?>><?=$unitName?></option>
								<?php
									}
									*/
								?>
						</select>
					</td>
					<td>&nbsp;</td>
					
					<td nowrap="true" class="listing-item">Company Name:</td>
					<td nowrap="true">
						<!--<select name="filterFunctionType" id="filterFunctionType" onchange="this.form.submit();">-->
						<select name="filterCompanyName" id="filterCompanyName" onchange="funcSubmit(this,'3');">
							<option value="">-- Select All--</option>
							<?php
						foreach ($companyRecords as $bcr) {
							$billingCompanyId	= $bcr[0];
							$cName			= $bcr[1];
							$displayCName		= $bcr[9];
							$defaultChk		= $bcr[10];
							$selected = "";
							//if ($billingCompanyId==$billingCompany) $selected = "selected";
							if ($filterCompanyName==$billingCompanyId) $selected = "selected";
						?>
						<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
						<?php	
							}		
						?>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
                </tr>
		<tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
		<!--<tr> 
                 <td colspan="3" style="padding-left:5px;padding-right:5px;">
			<table>
			<tr>
                              <TD>
					<table>
					<TR>
						<TD>
						<fieldset>
						<legend class="listing-item">Challan Delayed Entry Limit</legend>
								<table>
									<TR>
										<TD class="fieldName" nowrap>No.of Days:</TD>
										<TD>
							<input type="text" name="challanDEntryLimitDays" id="challanDEntryLimitDays" size="3" autocomplete="off" value="<?=$challanDEntryLimitDays?>" style="text-align:right;" title="Enter number of days">
							</TD>
									</TR>
								</table>
								</fieldset>
								</TD>
							<td>&nbsp;</td>
							<TD>
				<table>
					<TR>
					<TD>
						<fieldset>
						<legend class="listing-item">Sales Order Delayed Entry Limit</legend>
						<table>
							<TR>
								<TD class="fieldName" nowrap>No.of Days:</TD>
								<TD>
									<input type="text" name="soDEntryLimitDays" id="soDEntryLimitDays" size="3" autocomplete="off" value="<?=$soDEntryLimitDays?>" style="text-align:right;" title="Enter number of days">
								</TD>
							</TR>
						</table>
						</fieldset>
					</TD>
					</TR>
				</table>
			      </TD>
				<td nowrap="true" style="padding-left:5px;padding-right:5px;" align="center">
					<input type="submit" name="cmdUpdateRec" class="button" value=" Update " onClick="return validateUpdateRec(document.frmManageChallan);">
				</td>
							</TR>
						</table>
			      </TD>
                            </tr>
			</table>
		</td>
                </tr>-->
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <!--
				<tr> 
                  <td colspan="3"> 
				  
				  <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? //if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?//=$idGenRecordsSize;?>);" > <? //}?>
                          &nbsp;<? //if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? //}?> 
                          &nbsp;<? //if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageChallan_all.php?filterFunctionType=<?//=$filterFunctionType?>',700,600);"><? //}?></td>
                      </tr>
                    </table></td>
                </tr>-->
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <?php
			if($errDel!="")	{
		?>
                <tr> 
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?> 
                 </td>
                </tr>
                <?php
			}
		?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" style="padding-left:10px;padding-right:10px;"> 
			<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999" id="newspaper-b1">
                

				<?php
				if (sizeof($idGenRecords) > 0) {
					$i	=	0;
			?>
               

<!-- rekha added code -->

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
      				$nav.= " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYear\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYear\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYear\"  class=\"link1\">>></a> ";
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
			<!-- end code -->


			   <tr  bgcolor="#f2f2f2" align="center"> 
                       
					   <td width="20" rowspan="2">
				<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">-->
			</td>
                        <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Function Name</td>
							<!--<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Company</td>-->
						  <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Unit</td>
						  <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Alpha Prefix</td>
			<?php
				if ($filterFunctionType=='RM') {
			?>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Billing Company</td>
			<?php
				}
			?>
			<?php
				if ($filterFunctionType=='SPO') {
			?>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Exporter</td>
			<?php
				}
			?>
			<!--<?php
				//if (($filterFunctionType=='MG') || ($filterFunctionType=='LF') || ($filterFunctionType=='LU') || ($filterFunctionType=='LC')) {
			?>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Alpha Prefix</td>
			<?php
				//} 
			?>-->
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Select Year: 
			<select name="filterYear" id="filterYear" onchange="funcSubmit(this,'4');">
			<option value="">-- All--</option>
			<?php
				
				//$yearRcds = 
				$yearRcds=$manageChallanAllObj->fetchdistinctYear();				
				foreach ($yearRcds as $yearId) {
					$yeardt = $yearId[0];
					//$unitName	= $unitd[2];
					$selectedyearType = ($yeardt==$filterYear)?"selected":"";
					
			?>
				<option value='<?=$yeardt?>'<?=$selectedyearType?>><?=$yeardt?></option>
			<?php
				}
			?>
			</select>
			</td>
                        <td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Number</td>
			 <td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Current Number</td>			
			 <td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Delayed Entry Limit(Days)</td>
			 <td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Auto generate</td>
		
			<? if($edit==true){?>
	                        <td class="listing-head" width="50" rowspan="2"></td>
			<? }?>
                      </tr>
	
			
			<tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">From Date</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">To Date</td>	
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">From</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">To</td>
			</tr>
                      <?php
					  if($pageNo==1)
					  $cnt = 0;
					  else $cnt = ($pageNo-1) * $limit; 
			foreach($idGenRecords as $igr) {
				$i++;
				$cnt++;
				$numberGenId	= $igr[0];
				$fType		= $igr[1];
				//echo "The ftype is $fType";
				$startDate	= ($igr[2]!="")?dateFormat($igr[2]):"";
				$endDate	= ($igr[3]!="")?dateFormat($igr[3]):"";
				$startNo	= $igr[4];
				$endNo		= $igr[5];
				$currentNo=$igr[6];
				$selBillingCompany =  $igr[9];
				$displayname= $igr[15];
				if ($selBillingCompany!=0) {
					$billingCompanyRec = $billingCompanyObj->find($selBillingCompany);
					//$billingCompanyName = $billingCompanyRec[1];
					$billingCompanyName = $billingCompanyRec[9];
				}
				$delayedEntryLimitDays = $igr[10];
				$soInvType		= $igr[11];
				//$soInvType		= $igr[11];
				$selExporterId	= $igr[12];
				$selunitName=$igr[13];
				$selcompany=$igr[9];
				$alpha_code_prefix=$igr[14];
				$auto_generate=$igr[15];
				$exporterDisplayName = "";				
				if ($selExporterId!=0) {
					$rec	= $exporterMasterObj->find($selExporterId);
					$exporterDisplayName = $rec["display_name"];
				}
				//Rekha added code here 
				//startdate $igr[2];
				$cDate = date("Y-m-d");
				//>=start_date
				$bgcolor="#ffffff";
				if($igr[2]<$cDate && $igr[3]<$cDate){
					
					$bgcolor="#d3d3d3";
					
				} 
			?>
                      
<tr  bgcolor="<?=$bgcolor?>" > 
                        <td width="20" height="25">
				<!--<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$numberGenId;?>" class="chkBox">-->
				<?=$cnt.")";?>
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
				<?=$idGenFunctions[$fType];?>
				<? if ($soInvType!="") {?>
					<br/><span class="fieldName" style="line-height:normal;font-size:9px;">(<?=$invoiceTypeRecs[$soInvType]?>)</span>
				<? }?>
				<?php
				if ($filterFunctionType!='RM' && $selBillingCompany!=0) {
				?>
					<br/><span class="fieldName" style="line-height:normal;font-size:9px;">(<?=$billingCompanyName?>)</span>
				<?php
				}
				?>
				<?php
				if ($filterFunctionType!='SPO' && $selExporterId!=0) {
				?>
					<br/><span class="fieldName" style="line-height:normal;font-size:9px;">(<?=$exporterDisplayName?>)</span>
				<?php
				}
				?>
			</td>
			<!--
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">&nbsp;
			<?php 
				//$billrec = $billingCompanyObj->find($selcompany);
			//echo $billrec[1];?></td>-->
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">&nbsp;<?php echo $selunitName;?></td>
			<?php
				if ($filterFunctionType=='RM') {
			?>
			 	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$billingCompanyName?></td>
			<?php
				}
			?>
			<?php
				if ($filterFunctionType=='SPO') {
			?>
			 	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$exporterDisplayName?></td>
			<?php
				}
			?>
			<!--<?php
				//if (($filterFunctionType=='MG') || ($filterFunctionType=='LF') || ($filterFunctionType=='LU') || ($filterFunctionType=='LC')){
			?>
			 	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alpha_code_prefix?></td>
			<?php
				//}
			?>-->
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alpha_code_prefix?></td>
             <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$endDate?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$startNo?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$endNo?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentNo?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=($delayedEntryLimitDays!=0)?$delayedEntryLimitDays:"";?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=($auto_generate==1)? Yes:"";?></td>
			
			
			<? if($edit==true){?>
                        	<td class="listing-item" align="center" width="40"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$numberGenId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');this.form.action='ManageChallan_all.php';"></td>
			<? }?>
                      </tr>
                      	<?php
				
				//end

				}
			?>
                      	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      	<input type="hidden" name="editId" value="">
		  	<input type="hidden" name="editSelectionChange" value="0">
                

<!-- rekha added code -->

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
      				$nav.= " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYear\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYear\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ManageChallan_all.php?pageNo=$page&filterFunctionType=$filterFunctionType&filterUnitName=$filterUnitName&filterCompanyName=$filterCompanyName&filterYear=$filterYears\" class=\"link1\">>></a> ";
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
			<!-- end code -->




				<?php
				} else {
			?>
                      <tr bgcolor="white"> 
                        <td colspan="9"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      	<?php
				}
			?>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <!--
				<tr > 
                  <td colspan="3"> 
				  
				  <table cellpadding="0" cellspacing="0" align="center">
     				  <tr> 
                        <td><? //if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?//=$idGenRecordsSize;?>);" > <?// }?>
                          &nbsp;<? //if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? //}?> 
                          &nbsp;<? //if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageChallan_all.php?filterFunctionType=<?//=$filterFunctionType?>',700,600);"><? //}?></td>
                      </tr>
                    </table>
					
					</td>
                </tr>-->
                <tr> 
                  <td colspan="3" height="5" >
			<input type="hidden" name="hidRecId" value="<?=$editIdGenRecId;?>">
			<input type="hidden" name="hidFilterFunctionType" value="<?=$filterFunctionType?>">
			<input type="hidden" name="hidFilterUnit" value="<?=$filterUnitName?>">
			<input type="hidden" name="hidFilterCompany" value="<?=$filterCompanyName?>">
			<input type="hidden" name="hidFilterYear" value="<?=$filterYear?>">
		  </td>
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
  </table>
	<?php
		if ($addMode || $editMode) {
	?>
		<script language="JavaScript" type="text/javascript">
			showBillingComapny();
		</script>
	<?php
		}
	?>

 	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "idDateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "idDateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<SCRIPT>
jQuery(document).ready(function(){
		//alert("hii");
		var disable = '<?php echo $challan_statusval;?>';
		 //alert(disable);
			if(disable != 0)
			{
				document.getElementById('disable').checked = true;
				
			}
			var auto_generate = '<?php echo $auto_generateval;?>';
			// alert(auto_generate);
			if(auto_generate != 0)
			{
				document.getElementById('auto_Generate').checked = true;
				
			}
		
		});
</SCRIPT>
	</form>

	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>