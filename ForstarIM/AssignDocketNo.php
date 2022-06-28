<?php
	require("include/include.php");
	ob_start();

	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	$recUpdated 		= 	false;
	
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

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();	
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	if ($accesscontrolObj->canCompanySpecific()) $companySpecific=true;	

	# Delivery date Confirm Enable check
	$dDateCnfmEnabled = $manageconfirmObj->deliveryDateConfirmEnabled();
	//----------------------------------------------------------
	$pageRedirection = "?supplyFrom=".$p["supplyFrom"]."&supplyTill=".$p["supplyTill"]."&transporter=".$p["transporter"];

	#For Refreshing the main Window when click PopUp window
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];

	# Update
	if ($p["cmdSave"]!="") {	
		$selTransporter		=	$p["transporter"];
		$rowCount		=	$p["hidRowCount"];		
		for ($i=1; $i<=$rowCount; $i++) {
	
			$soId		= $p["soId_".$i];
			$docketNo	= $p["docketNo_".$i];
			$deliveryDate	= ($p["deliveryDate_".$i]!="")?mysqlDateFormat($p["deliveryDate_".$i]):"";
			$deliveryRemark	= addSlash($p["deliveryRemark_".$i]);
			$odaApplicable = ($p["odaApplicable_".$i]!="")?$p["odaApplicable_".$i]:'N';

			$trptrBillSetld = $p["trptrBillSetld_".$i];
			
			if ($soId!="" && $docketNo!="" && $trptrBillSetld!='Y') {
				$updateSalesOrderRec = $assignDocketNoObj->updateSalesOrderRec($soId, $docketNo, $deliveryDate, $deliveryRemark, $odaApplicable);
			}
		}
		if ($updateSalesOrderRec!="") {
			$sessObj->createSession("displayMsg",$msg_succUpdateTransporterDocketNo);
			if (!$popupWindow) {
				$sessObj->createSession("nextPage",$url_afterUpdateTransporterDocketNo.$pageRedirection);
			} else  $recUpdated = true;
		}		
	}
	
	if ($p["transporter"]=="") $selTransporter = $g["transporter"];
	else 			$selTransporter = $p["transporter"];

	
	
	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}
		
	# Select the records based on date

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate	= mysqlDateFormat($dateFrom);
		$tillDate	= mysqlDateFormat($dateTill);

		# Get all Transporter	
		$transporterRecords	= $assignDocketNoObj->fetchTransporterRecords($fromDate, $tillDate);

		# Get Sales Order Records
		$salesOrderRecords = $assignDocketNoObj->filterSalesOrderRecords($fromDate, $tillDate, $selTransporter);
	}

	# Display heading	
	$heading = " Assign Docket No";	

	$ON_LOAD_PRINT_JS	= "libjs/AssignDocketNo.js";

	# Include Template [topLeftNav.php]
	if (!$popupWindow) require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmAssignDocketNo" action="AssignDocketNo.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="10"></td>
                      </tr>
			<?
				if (sizeof($salesOrderRecords)>0) {
			?>			
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSave" type="submit" class="button" id="cmdSave" onClick="return updateTransporterSORec(document.frmAssignDocketNo, 'SAVEBTN');" value=" Save ">
				<? }?>
			</td>
                        <?} ?>
                      </tr>
			<? } else {?>
				 <tr> 
                        <td colspan="2" height="20"></td>
                      </tr>
			<?}?>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
			<tr>
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td colspan="2" align="center">
			<table width="250">
                                  <tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="9" value="<?=$dateFrom?>" autocomplete="off" onchange="return getTransporter();">
				</td>
				<td class="fieldName">To:</td>
				<td>
                                        <input type="text" id="supplyTill" name="supplyTill" size="9"  value="<?=$dateTill?>" autocomplete="off" onChange="return getTransporter();">
					</td>
					 <td class="fieldName">Transporter:</td>
                                    <td>
                                      <select name="transporter" id="transporter">
                                        <option value="">-- Select All --</option>
                                        <?
					foreach ($transporterRecords as $tr) {
						$transporterId	=	$tr[0];
						$transporterName	=	stripSlash($tr[1]);
						$selected	=	"";
						if ($transporterId == $selTransporter) {
							$selected	=	"selected";
						}
					?>
                                        <option value="<?=$transporterId?>" <?=$selected?>> 
                                        <?=$transporterName?>
                                        </option>
                                        <? } ?>
                                      </select>
				</td>
	<td>
		<input name="cmdSearch" type="submit" id="cmdSearch" value=" Search" class="button" onclick="return validateAssignDocketNo(document.frmAssignDocketNo, '');">
	</td>
                                  </tr>
                                </table>
			</td>
                        </tr>
                  <?php 
		     if (sizeof($salesOrderRecords)>0) {
			  $i = 0;
		  ?>
         <tr>
              <td colspan="4" align="center" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              	<tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">Inv<br> No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">Inv Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">Inv Value</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">Gross Wt</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">No. of Boxes</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</th>
		<?php 
			if ($selTransporter=="") {
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Transporter</th>
		<?php
			}
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Docket No</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">Delivery Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal;">ODA<br>Applicable</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Remarks</th>
              </tr>
        <?php
		$totalInvValue  = 0;
		$totalGrossWt	= 0;
		$totNumBox	= 0;
		foreach($salesOrderRecords as $sor){
			$i++;			
			$soId	= $sor[0];
			$invNo	= $sor[1];
			$invoiceDate 	= dateFormat($sor[2]);
			$invoiceValue 	= $sor[3];
			$totalInvValue += $invoiceValue;
			$grossWt	= $sor[4];
			$totalGrossWt += $grossWt;

			$invType	= $sor[5];
			$sampleInvNo	= $sor[6];
			$docketNo	= $sor[7];
			$invoiceNo	= ($invType=='S')?"S$sampleInvNo":$invNo;

			$numOfBox	= $sor[10];
			$totNumBox	+= $numOfBox;
			
			$selTransporterName	= $sor[11];

			$dispatchDate	= ($sor[12]!='0000-00-00')?dateFormat($sor[12]):"";
			$deliveryDate	= ($sor[13]!='0000-00-00')?dateFormat($sor[13]):"";
			$deliveryRemark = stripSlash($sor[14]);
			$odaApplicable	= ($sor[15]=='Y')?"checked":"";

			$odBillSettled	= $sor[8];
			$ocBillSettled  = $sor[16];
			//&& $dDateCnfmEnabled
			if ($dDateCnfmEnabled) {
				$trptrBillSettled = (($odBillSettled=='Y' || $ocBillSettled=='Y') )?'Y':'N';
			} else {
				$trptrBillSettled = (($odBillSettled=='Y' && $ocBillSettled=='Y') )?'Y':'N';
			}
			$transporterPaid = $sor[9];
			$readOnly = "";
			if ($transporterPaid=='Y' && $trptrBillSettled=='Y') {
				$readOnly = "readonly";
			}
			if ($deliveryDate=="" && !$dDateCnfmEnabled) $readOnly = "";
	
			$distributorName	= $sor[17];
	?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;" align="center">
			<?=$invoiceNo?>
			<input type="hidden" name="soId_<?=$i?>" id="soId_<?=$i?>" value="<?=$soId?>">
			<input type="hidden" name="trptrBillSetld_<?=$i?>" id="trptrBillSetld_<?=$i?>" value="<?=$trptrBillSettled?>">
		</td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
			<?=$invoiceDate?>
		</td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
			<?=$invoiceValue?>
		</td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
			<?=$grossWt?>
		</td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
			<?=$numOfBox?>
		</td>
		<td class="listing-item" align="left" style="padding-left:5px; padding-right:5px; line-height:normal;">
			<?=$distributorName?>
		</td>
		<?php 
			if ($selTransporter=="") {
		?>
		<td class="listing-item" align="left" style="padding-left:5px; padding-right:5px; line-height:normal;">
			<?=$selTransporterName?>
		</td>
		<?php
			}
		?>
		<td align="center" nowrap="true" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="docketNo_<?=$i?>" id="docketNo_<?=$i?>" value="<?=$docketNo?>" size="12" <?=$readOnly?> onkeydown="return nextBox(event,'document.frmAssignDocketNo','docketNo_<?=$i+1;?>');" autocomplete="off">
		</td>
		<td align="center" nowrap="true" style="padding-left:5px; padding-right:5px;">
			<input type="text" name="deliveryDate_<?=$i?>" id="deliveryDate_<?=$i?>" value="<?=$deliveryDate?>" size="9" autocomplete="off" <?=$readOnly?> />
			<input type="hidden" name="dispatchDate_<?=$i?>" id="dispatchDate_<?=$i?>" value="<?=$dispatchDate?>" size="9" autocomplete="off"/>
			
		</td>
		<td align="center" nowrap="true" style="padding-left:5px; padding-right:5px;">
			<input type="checkbox" name="odaApplicable_<?=$i;?>" id="odaApplicable_<?=$i;?>" value="Y" class="chkBox" <?=$odaApplicable?> />
		</td>
		<td align="center" nowrap="true" style="padding-left:5px; padding-right:5px;">
			<textarea name="deliveryRemark_<?=$i?>" id="deliveryRemark_<?=$i?>" rows="4"><?=$deliveryRemark?></textarea>
		</td>
              </tr>
		<?php
			}
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalInvValue,2);?></strong>
		</td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalGrossWt,2);?></strong>
		</td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totNumBox,0);?></strong>
		</td>
		<?php 
			if ($selTransporter=="") {
		?>		
		<td>&nbsp;</td>
		<?php
			}
		?>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
              </tr>			  
      </table></td>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
                        </tr>
	<?php }?>
                      <tr> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($salesOrderRecords)<=0 && $selTransporter!=""){ echo $msgNoSettlement;}?></td>
                        </tr>			
			<tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
			<?
				if (sizeof($salesOrderRecords)>0) {
			?>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center">
				<? if($isAdmin==true || $reEdit==true || $companySpecific==true || $edit==true){?>
					<input name="cmdSave" type="submit" class="button" id="cmdSave" onClick="return updateTransporterSORec(document.frmAssignDocketNo, 'SAVEBTN');" value=" Save ">
				<? }?>
			</td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
			<? } else {?>
			<tr>
                        <td colspan="3" nowrap height="25"></td>
                        </tr>
			<? }?>
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
		<tr>
			<td height="10" ><input type="hidden" name="popupWindow" id="popupWindow" value="<?=$popupWindow?>"></td>
		</tr>
	
			
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
	<script>
		displayCalender('<?=sizeof($salesOrderRecords)?>');
	</script>
	<? if ($recUpdated && $popupWindow!="") {?>
		<script>
		closeWindow();
		function closeWindow()
		{
			var myParentWindow = opener.document.forms.frmTransporterAccount;
			myParentWindow.submit();
			//alert (myParentWindow);
		}
		</script>
	<? }?>
</form>
<?php
	# Include Template [bottomRightNav.php]
	if (!$popupWindow) require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;	
?>
