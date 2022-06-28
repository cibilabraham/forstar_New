<?php
	require("include/include.php");
	require_once("lib/PurchaseStatement_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$checked		=	"";

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
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
	$reEdit = false;
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
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	if ($accesscontrolObj->canCompanySpecific()) $companySpecific=true;		
	//----------------------------------------------------------
	
	#Checking Confirm enabled or Disabled
	$acConfirmed = $manageconfirmObj->isACConfirmEnabled();
		
	
	$selectSupplier		= $p["supplier"];
	if ($p["billingCompany"]>0) $billingCompany  = $p["billingCompany"];
	else $billingCompany = 0;

	# select record between selected date
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];
	
	$Date1		=	explode("/",$dateFrom);
	$fromDate	=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
	
	$Date2		=	explode("/",$dateTill);
	$tillDate	=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	
	if ($dateFrom!="" && $dateTill!="") {
		$supplierRecords = $purchasestatementObj->fetchSupplierRecords($fromDate, $tillDate, $acConfirmed);
	
		#Select the records based on date and Supplier
		if ($selectSupplier!="") {
			$purchaseStatementRecords = $purchasestatementObj->filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $acConfirmed, $billingCompany);
			# Get Billing Comapany  Records
			$billingCompanyRecords = $purchasestatementObj->getOtherBillingCompany($selectSupplier, $fromDate, $tillDate, $acConfirmed, $billingCompany);
			
			if (sizeof($billingCompanyRecords)>0 && sizeof($billingCompanyRecords)==1) {
				if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];
				else $billingCompany = $purchasestatementObj->getBillingCompanyId($selectSupplier, $fromDate, $tillDate, $acConfirmed, $billingCompany);
			} else if (sizeof($billingCompanyRecords)>1 && ($p["billingCompany"]=="" || $p["billingCompany"]==0)) {
				$billingCompany = $purchasestatementObj= $purchasestatementObj->getDefaultCompany();
			}	
		}
	}
$ON_LOAD_SAJAX = "Y"; 
	# For Printing JS in Head section	
	$ON_LOAD_PRINT_JS = "libjs/PurchaseStatement.js"; 
	
	# Display heading
	if ($editMode)	$heading	= $label_editPurchaseSettlement;
	else		$heading	= $label_addPurchaseSettlement;
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPurchaseStatement" action="PurchaseStatement.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="30%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
						<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
						<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Statement</td>
					</tr>
					<tr>
						<td width="1" ></td>
						<td colspan="2"  align="center" style="padding-left:10px;padding-right:10px;">
						<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
				                <tr>
							<td colspan="2" height="10"></td>
                      				</tr>
                      				<tr>
                        <td colspan="4" align="center">
				<? if($print==true){?>
				<input type="button" name="View" value=" View / Print" class="button" onClick="return validatePurchaseStatementSearch(document.frmPurchaseStatement, 'PrintPurchaseStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&billingCompany=<?=$billingCompany?>');" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>
					<!--<input type="button" name="View" value=" View / Print" class="button" onClick="return printWindow('PrintPurchaseStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&billingCompany=<?=$billingCompany?>',700,600);" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>-->
				<? }?>
			</td>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="10"></td>
                        </tr>
                      <tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td align="center"><table width="250">
                                  <tr> 
                                    <td class="fieldName"> From</td>
                                    <td> 
                                      <? $dateFrom = $p["supplyFrom"];?>
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmPurchaseStatement);" autocomplete="off">
					</td>
					 <td class="fieldName">To</td>
					    <td><? $dateTill = $p["supplyTill"];?>
                                        <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onchange="submitForm('supplyFrom','supplyTill',document.frmPurchaseStatement);" autocomplete="off"></td>
                                  </tr>
                                </table></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td align="left">
			<table width="250" cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td class="fieldName"></td>
                                    <td></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier		=	$p["supplier"];?>
                                     <!-- <select name="supplier" onchange="this.form.submit();">-->
									 <select name="supplier" onchange="getSupplier(this);">
                                        <option value="">--select--</option>
                                        <?
					foreach ($supplierRecords as $fr) {
							$supplierId	=	$fr[0];
							$supplierName	=	stripSlash($fr[2]);
							$selected	=	"";
							if ($supplierId == $selectSupplier) {
								$selected	=	"selected";
							}
					?>
                                        <option value="<?=$supplierId?>" <?=$selected?>> 
                                        <?=$supplierName?>
                                        </option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
			<?php
			if (sizeof($billingCompanyRecords)>0 && ($companySpecific==true || $isAdmin)) {	
			?>
			<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>					
					<!--<select name="billingCompany" onchange="this.form.submit();">-->
					<select name="billingCompany" onchange="getbillCompany(this)">
					<option value="0">-- Select --</option>			
					<?
					foreach ($billingCompanyRecords as $bcr) {
						$i++;
						$billingCompanyId	= $bcr[0];
						$cName			= $bcr[1];
						$defaultChk		= $bcr[2];
						$selected = "";
						if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y')) $selected = "selected";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$cName?></option>
					<?	
						}	
					?>
					</select>
				</td>
			</tr>
			<?php
				}
			?>
			<!--<tr>
				<TD colspan="2" align="center">
					<input type="submit" name="cmdSearch" value="Search" class="button" onClick="return validatePurchaseStatementSearch(document.frmPurchaseStatement);">
				</TD>
			</tr>-->
                        </table></td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr> 
                        <td colspan="2" align="center" class="err1">
				<? if(sizeof($purchaseStatementRecords)<=0 && $selectSupplier!=""){ echo $msgNoSettlement;}?>
			</td>
                        <td align="center" colspan="2">&nbsp;</td>
                      </tr>
                      <tr> 
                        <td colspan="4" align="center">
			<? if($print==true){?>
<!--  validatePurchaseStatementSearch(document.frmPurchaseStatement);"-->
				<input type="button" name="View" value=" View / Print" class="button" onClick="return validatePurchaseStatementSearch(document.frmPurchaseStatement, 'PrintPurchaseStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&billingCompany=<?=$billingCompany?>');" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>

<!--<input type="button" name="View" value=" View / Print" class="button" onClick="return validatePurchaseStatementSearch(document.frmPurchaseStatement);return printWindow('PrintPurchaseStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&billingCompany=<?=$billingCompany?>',700,600);" <? if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>-->
			<? }?>
			</td>
                        <input type="hidden" name="cmdAddNew" value="1">
                      </tr>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                    </table></td></tr></table></td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
		?>
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	
	Calendar.setup 
	(	
		{
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
