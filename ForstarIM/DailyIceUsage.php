<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 		=	"?pageNo=".$p["pageNo"]."&selDate=".$p["selDate"];

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
		header("Location: ErrorPage.php");
		die();
	}

	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	//----------------------------------------------------------
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode	=	true;

	#Insert
	if ($p["cmdAdd"]!="") {
		$selectDate	= mysqlDateFormat($p["selectDate"]);
		$customerType	= $p["customerType"];

		$supplier = "";
		$plantId  = "";
		$partyName = "";
		$partyLocation = "";
		$issuedTo = false;
		if ($customerType=='SU') {
			$supplier = $p["supplier"];				
			$issuedTo = true;
		} else if ($customerType=='SE') {
			$plantId = $p["plantId"];
			$issuedTo = true;
		} else if ($customerType=='NW') {
			$partyName	= trim($p["partyName"]);
			$partyLocation	= trim($p["partyLocation"]);
			$issuedTo = true;
		}	
				
		$qty		= $p["qty"];
		$unitId		= $p["unitId"];
		$sold		= ($p["sold"])?$p["sold"]:"N";

		$soldQty	= "";
		$adjQty		= "";
		$rate		= "";
		$amount		= "";
		$pymntRecd	= "";		
		if ($customerType!='SE' && $sold=='Y') {
			$soldQty	= $p["soldQty"];
			$adjQty		= $p["adjQty"];
			$rate		= $p["rate"];
			$amount		= $p["amount"];
			$pymntRecd	= $p["pymntRecd"];	
		}

		if ($selectDate!="" && $issuedTo) {

			#Daily Activity Chart Main Rec Uptd
			$dailyIceUsageRecIns = $dailyIceUsageObj->addDailyIceUsage($selectDate, $supplier, $isuedTo, $qty, $unitId, $sold, $soldQty, $adjQty, $rate, $amount, $pymntRecd, $userId, $customerType, $plantId, $partyName, $partyLocation);
			
			if ($dailyIceUsageRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg", $msg_succAddDailyIceUsage);
				$sessObj->createSession("nextPage",$url_afterAddDailyIceUsage.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddDailyIceUsage;
			}			
		} else {
			$addMode		=	true;
			$err			=	$msg_failAddDailyIceUsage;
		}
	}


	# Edit	
	if ($p["editId"]!="" && $p["cmdSaveChange"]=="" ) {
		$editId		= $p["editId"];
		$editMode	= true;
		
		$dailyIceUsageRec = $dailyIceUsageObj->find($editId);
		
		$editDailyIceUsageId 	= $dailyIceUsageRec[0];		
		$selectDate	= dateFormat($dailyIceUsageRec[1]);
		$supplier	= $dailyIceUsageRec[2];
		$issuedTo	= $dailyIceUsageRec[3];
		$qty		= $dailyIceUsageRec[4];
		$unitId		= $dailyIceUsageRec[5];
		$sold		= $dailyIceUsageRec[6];
		$soldQty	= $dailyIceUsageRec[7];
		$adjQty		= $dailyIceUsageRec[10];
		$rate		= $dailyIceUsageRec[8];
		$amount		= $dailyIceUsageRec[9];
		$pymntRecd	= $dailyIceUsageRec[11];

		$customerType	= $dailyIceUsageRec[12];
		$plantId  	= $dailyIceUsageRec[13];
		$partyName 	= $dailyIceUsageRec[14];
		$partyLocation 	= $dailyIceUsageRec[15];
		
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$dailyIceUsageId = $p["hidDailyIceUsageId"];

		$selectDate	= mysqlDateFormat($p["selectDate"]);
		//$supplier	= $p["supplier"];
		//$issuedTo	= $p["issuedTo"];
		$customerType	= $p["customerType"];

		$supplier = "";
		$plantId  = "";
		$partyName = "";
		$partyLocation = "";
		$issuedTo = false;
		if ($customerType=='SU') {
			$supplier = $p["supplier"];				
			$issuedTo = true;
		} else if ($customerType=='SE') {
			$plantId = $p["plantId"];
			$issuedTo = true;
		} else if ($customerType=='NW') {
			$partyName	= trim($p["partyName"]);
			$partyLocation	= trim($p["partyLocation"]);
			$issuedTo = true;
		}
		$qty		= $p["qty"];
		$unitId		= $p["unitId"];
		$sold		= ($p["sold"])?$p["sold"]:"N";

		$soldQty	= "";
		$adjQty		= "";
		$rate		= "";
		$amount		= "";
		$pymntRecd	= "";		
		if ($customerType!='SE' && $sold=='Y') {
			$soldQty	= $p["soldQty"];
			$adjQty		= $p["adjQty"];
			$rate		= $p["rate"];
			$amount		= $p["amount"];
			$pymntRecd	= $p["pymntRecd"];	
		}
		

		if ($dailyIceUsageId) {
			#Daily Freezing Chart Main Rec Uptd
			$updateDailyIceUsageRec = $dailyIceUsageObj->updateDailyIceUsageRec($dailyIceUsageId, $selectDate, $supplier, $issuedTo, $qty, $unitId, $sold, $soldQty, $adjQty, $rate, $amount, $pymntRecd, $customerType, $plantId, $partyName, $partyLocation);
		}
	
		if ($updateDailyIceUsageRec) {
			$editMode = false;
			$editId = "";
			$p["editId"] = "";
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyIceUsage);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyIceUsage.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyIceUsage;
		}
		$updateDailyIceUsageRec = false;
	}
	

	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$dailyIceUsageId = $p["delId_".$i];
			
			if ($dailyIceUsageId!="") {
				$dailyIceUsageRecDel	= $dailyIceUsageObj->deleteDailyIceUsageRec($dailyIceUsageId);
			}
		}
		if ($dailyIceUsageRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyIceUsage);
			$sessObj->createSession("nextPage",$url_afterDelDailyIceUsage.$selection);
		} else {
			$errDel	=	$msg_failDelDailyIceUsage;
		}
		$dailyIceUsageRecDel	=	false;
	}

	# Update Days total
	if ($p["cmdDaysCalcUpdate"]!="") {
		
		$selDate = $p["selDate"];
		$selectDate = mysqlDateFormat($selDate);

		$daysQtyTrackId = $p["daysQtyTrackId"]; // Entry Id
		$daysTotalQty	= $p["daysTotalQty"];
		$daysIssuedQty	= $p["daysIssuedQty"];
		$daysSoldQty	= $p["daysSoldQty"];

		if ( ($daysTotalQty!="" || $daysIssuedQty!="" || $daysSoldQty!="") && $daysQtyTrackId=="") {
			$insertDaysTotalQty = $dailyIceUsageObj->addIceUsageDaysTotal($selectDate, $daysTotalQty, $daysIssuedQty, $daysSoldQty, $userId);
		} else if ( ($daysTotalQty!="" || $daysIssuedQty!="" || $daysSoldQty!="") && $daysQtyTrackId!="") {
			$updateDaysTotalQty = $dailyIceUsageObj->updateIceUsageDaysTotal($daysQtyTrackId, $selectDate, $daysTotalQty, $daysIssuedQty, $daysSoldQty);
		}
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;

	$offset = ($pageNo - 1) * $limit;
	## ----------------- Pagination Settings I End ------------

	#List All Record
	if ($g["selDate"]!="") $selDate = $g["selDate"];
	else if ($p["selDate"]=="") $selDate = date("d/m/Y");
	else $selDate = $p["selDate"];
		
	if ($selDate!="" || $p["cmdSearch"]!="") {

		$searchDate	=	mysqlDateFormat($selDate);

		$dailyIceUsageRecs	= $dailyIceUsageObj->fetchAllPagingRecords($searchDate, $offset, $limit);
		$dailyIceUsageRecSize	= sizeof($dailyIceUsageRecs);
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=  	sizeof($dailyIceUsageObj->fetchAllRecords($searchDate));
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	
	if ($addMode || $editMode) {
		# Unit Records
		$stockUnitRecs = $stockItemUnitObj->fetchAllRecords();

		#List all Main Suppliers
		$supplierRecords	= $supplierMasterObj->fetchAllRecords("FRN");

		# landing Center
		//$landingCenterRecords = $landingcenterObj->fetchAllRecords();

		#List All Plants
		$plantRecords	= $plantandunitObj->fetchAllRecords();
	}

	# Get Recs
	list($daysQtyTrackId, $daysTotalQty, $daysIssuedQty, $daysSoldQty) = $dailyIceUsageObj->getIceUsageDaysTotal($searchDate);
	//echo "$daysQtyTrackId, $daysTotalQty, $daysIssuedQty, $daysSoldQty";

	$customerTypeArr = array("SU"=>"SUPPLIER","SE"=>"SELF", "NW"=>"NEW");
	//ksort($customerTypeArr);

	if ($editMode)	$heading	= $label_editDailyIceUsage;
	else		$heading	= $label_addDailyIceUsage;

	$ON_LOAD_PRINT_JS	= "libjs/DailyIceUsage.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php"); 
?>
<form name="frmDailyIceUsage" action="DailyIceUsage.php" method="post">
<script type="text/javascript" language="JavaScript">
		$(document).ready(function(){
			$("#issuedToRow").hide();
			// Customer type
			$("#customerType").change(function() {
				var custType = $("select#customerType").val();
				if (custType=='SU') {
					$("#issuedToRow").show();
					$("#supplierRow").show();
					$("#plantUnitRow").hide();
					$("#newRow").hide();	
					$("#soldQtyRow").show();
					$("#soldSectionRow").show();
					$("#soldRow").show();
				} else if (custType=='SE') {
					$("#issuedToRow").show();
					$("#supplierRow").hide();
					$("#plantUnitRow").show();
					$("#newRow").hide();
					$("#soldSectionRow").hide();
					$("#soldRow").hide();
					//$("#soldQtyRow").hide();
					//soldSectionRow
					$("select#sold").val('');
				} else if (custType=='NW') {
					$("#issuedToRow").show();
					$("#supplierRow").hide();
					$("#plantUnitRow").hide();
					$("#newRow").show();
					$("#soldQtyRow").show();
					$("#soldRow").show();
					$("#soldSectionRow").show();
				} else {
					$("#issuedToRow").hide();
				}		
				$("#sold").change();		
			});
			$("#customerType").change();

			// Sold
			$("#sold").change(function() {
				var sold = $("select#sold").val();
				if (sold=='Y') {
					$("#soldSectionRow").show();
					/*
					$("#soldQtyRow").show();
					$("#adjQtyRow").show();
					$("#rateRow").show();
					$("#amtRow").show();
					$("#pymntRecdRow").show();
					*/
				} else if (sold=='N') {
					$("#soldSectionRow").hide();
					/*
					$("#soldQtyRow").hide();
					$("#adjQtyRow").hide();
					$("#rateRow").hide();
					$("#amtRow").hide();
					$("#pymntRecdRow").hide();
					*/
				}	
			});
			$("#sold").change();			
		});
</script>
  <table cellspacing="0"  align="center" cellpadding="0" width="65%">
	<? 
	if($err!="" ){
        ?>
	<tr>
	<td height="10" align="center" class="err1" >
			<?=$err;?>
	</td>
	</tr>
	<?php
		}
        ?>
    <?php
	 if ( $editMode || $addMode) {
    ?>
    <tr>
      <td>
        <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
          <tr>
            <td   bgcolor="white">
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr>
                  <td width="1" background="images/heading_bg.gif" class="page_hint">
                  </td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >
                     &nbsp;<?=$heading;?>
                  </td>
                </tr>
                <tr>
                  <td width="1" >
                  </td>
                  <td colspan="2" >
                    <table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
                      <tr>
                        <td height="10" >
                        </td>
                      </tr>
                      <tr>
                        <? if($editMode){
                        ?>
                        <td align="center">
                          <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyIceUsage.php');">&nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyIceUsage(document.frmDailyIceUsage);">
                        </td>
                        <?} else{
                        ?>
                        <td align="center">
                          <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyIceUsage.php');">&nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value="Save & Exit" onClick="return validateDailyIceUsage(document.frmDailyIceUsage);">
                        </td>
                        <?}
                        ?>
                      </tr>
                      <input type="hidden" name="hidDailyIceUsageId" value="<?=$editDailyIceUsageId;?>" readonly="true">
                      <tr>
                        <td nowrap class="fieldName">
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" height="5">
                        </td>
                      </tr>
	<tr>
		<TD colspan="2" align="center">
		<table>
		<TR>
		<TD valign="top">
			<fieldset>
			<table>
                                  <TR>
                                    <TD class="fieldName" nowrap>*Entry Date</TD>
                                    <td nowrap>
                                      <?php
					if ($selectDate=="") $selectDate = date("d/m/Y");
                                      ?>
                                      <input type="text" id="selectDate" name="selectDate" size="8" value="<?=$selectDate?>">
                                    </td>
                                  </TR>
				<TR>
                                    	<TD class="fieldName" nowrap>*Customer Type</TD>
                                    	<td nowrap>
						<select name="customerType" id="customerType">
						<option value="">--Select--</option>
						<?php
						foreach ($customerTypeArr as $custTypeKey=>$custTypeVal) {
							$selected = ($customerType==$custTypeKey)?"selected":"";
						?>
						<option value="<?=$custTypeKey?>" <?=$selected?>><?=$custTypeVal?></option>
						<?php
						}
						?>
						</select>
					</td>
				</TR>
<tr id="issuedToRow">
	<TD colspan="2">
	<table cellpadding="0" cellspacing="0">
		<TR>
			<TD>
			<fieldset style="border-right:0px; border-left:0px;">
				<legend class="listing-item">Issued To</legend>
				<table cellpadding="0" cellspacing="0">
					<TR id="supplierRow">
						<TD>
							<table cellpadding="0" cellspacing="0">
								<TR>
								<TD class="fieldName" nowrap>*Supplier</TD>
								<td nowrap>
										<select name="supplier" id="supplier" style="width:162px;">
										<option value="">--Select--</option>
										<?php
											foreach ($supplierRecords as $fr) {
												$supplierId	= $fr[0];
												$supplierName	= stripSlash($fr[2]);
												$selected	= ($supplierId==$supplier)?"selected":"";
										?>
										<option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
										<? } ?>
										<!--<option value="0" <?=($supplier=='0')?"selected":"";?>>SELF</option>-->
										</select>
								</td>
								</TR>
							</table>
						</TD>
					</TR>
					<TR id="plantUnitRow">
						<TD>
							<table cellpadding="0" cellspacing="0">
								<TR>
									<TD class="fieldName" nowrap>*Plant/Unit</TD>
									<td nowrap>
										<select id="plantId" name="plantId" style="width:162px;">
										<option value="">--Select--</option>
										<?php 
										foreach($plantRecords as $pr) {
											$plantRecId 	= $pr[0];
											$plantName 	= stripSlash($pr[2]);
											$selected= ($plantId == $plantRecId)?"selected":"";
										?>
										<option value="<?=$plantRecId?>" <?=$selected?>><?=$plantName?></option>
										<?php
												}
										?>
										</select>
									</td>
								</TR>
							</table>
						</TD>
					</TR>
					<TR id="newRow">
						<TD>
							<table cellpadding="0" cellspacing="0">
								<TR>
									<TD class="fieldName" nowrap>*Name of the Party</TD>
									<td nowrap>
									<input type="text" name="partyName" id="partyName" size="20" value="<?=$partyName?>" />
									</td>
								</TR>
								<TR>
									<TD class="fieldName" nowrap>*Location</TD>
									<td nowrap>
									<input type="text" name="partyLocation" id="partyLocation" size="20" value="<?=$partyLocation?>" />
									</td>
								</TR>
							</table>
						</TD>
					</TR>
				</table>
				</fieldset>
			</TD>
		</TR>
	</table>
	</TD>
</tr>
					
				
				<TR>
                                    <TD class="fieldName" nowrap>*Qty</TD>
                                    <td nowrap>
					<input type="text" id="qty" name="qty" size="8" value="<?=$qty?>" style="text-align:right;">
                                    </td>
                                  </TR>
				<TR>
                                    <TD class="fieldName" nowrap>*Unit</TD>
                                    <td nowrap>
					<select name="unitId" id="unitId">
						<option value="">--Select--</option>
						<?php
							foreach ($stockUnitRecs as $sur) {
								$stkUnitId 	= $sur[0];
								$unitName	= $sur[1];
								$selected = ($stkUnitId==$unitId)?"selected":"";
						?>
						<option value="<?=$stkUnitId?>" <?=$selected?>><?=$unitName?></option>
						<?php
							}
						?>
					</select>
                                    </td>
                                  </TR>	
				<TR id="soldRow">
                                    <TD class="fieldName" nowrap>*Sold</TD>
                                    <td nowrap>
					<select id="sold" name="sold">
						<option value="">--Select--</option>
						<option value="Y" <?=($sold=='Y')?"selected":"";?>>YES</option>
						<option value="N" <?=($sold=='N')?"selected":"";?>>NO</option>
					</select>
                                    </td>
                                  </TR>			
                         </table>
			</fieldset>
			</TD>
			<td valign="top">&nbsp;</td>
<!-- IInd Col -->
			<TD valign="top" id="soldSectionRow">
			<fieldset>
			<table>				
				<TR id="soldQtyRow">
                                    <TD class="fieldName" nowrap>Sold Qty</TD>
                                    <td nowrap>
					<input type="text" id="soldQty" name="soldQty" size="8" value="<?=$qty?>" style="text-align:right;" onkeyup="calcAmt();">
                                    </td>
                                  </TR>
				<TR id="adjQtyRow">
                                    <TD class="fieldName" nowrap>Adjusted Qty</TD>
                                    <td nowrap>
					<input type="text" id="adjQty" name="adjQty" size="8" value="<?=$adjQty?>" style="text-align:right;">
                                    </td>
                                </TR>
				<TR id="rateRow">
                                    <TD class="fieldName" nowrap>Rate</TD>
                                    <td nowrap>
					<input type="text" id="rate" name="rate" size="8" value="<?=$rate?>" style="text-align:right;" onkeyup="calcAmt();">
                                    </td>
                                  </TR>
				<TR id="amtRow">
                                    <TD class="fieldName" nowrap>Amount</TD>
                                    <td nowrap>
					<input type="text" id="amount" name="amount" size="8" value="<?=$amount?>" style="text-align:right; border:none;" readonly="true">
                                    </td>
                                </TR>
				<TR id="pymntRecdRow">
                                    <TD class="fieldName" nowrap>Pymnt Recd</TD>
                                    <td nowrap>
					<input type="text" id="pymntRecd" name="pymntRecd" size="8" value="<?=$pymntRecd?>" style="text-align:right; ">
                                    </td>
                                </TR>
			</table>
			</fieldset>
			</TD>
			</TR>
		</table>
		</TD>
	</tr>
        <tr>
         <? if($editMode){ ?>
                        <td align="center">
                          <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyIceUsage.php');">&nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyIceUsage(document.frmDailyIceUsage);">
                        </td>
                        <? } else{
                        ?>
                        <td align="center">
                          <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyIceUsage.php');">&nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateDailyIceUsage(document.frmDailyIceUsage);">
                        </td>
                        <? }
                        ?>
                      </tr>
                      <tr>
                        <td  height="10" >
                        </td>
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
			
			# Listing Grade Starts
    ?>
    <tr>
      <td height="10" align="center" >
      </td>
    </tr>
    <tr>
      <td>
        <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
          <tr>
            <td   bgcolor="white">
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr>
                  <td width="1" background="images/heading_bg.gif" class="page_hint">
                  </td>
                  <td  colspan="1" background="images/heading_bg.gif" class="pageName" >
                     &nbsp;Daily Ice Usage 
                  </td>
                  <td background="images/heading_bg.gif" >
                    <table cellpadding="0" cellspacing="0" align="right">
                      <tr>
                        <td class="listing-item" nowrap>
                           &nbsp;&nbsp;Date:&nbsp; 
                        </td>
                        <td nowrap>
                          <? 
							if($selDate=="") $selDate=date("d/m/Y"); 
                          ?>
                          <input type="text" id="selDate" name="selDate" size="8" value="<?=$selDate?>">
                          &nbsp; 
                        </td>
                        <td>
                           &nbsp;<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search ">&nbsp; 
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
		<tr>
                  <td colspan="3" height="10" >
                  </td>
                </tr>
		<?php
			if ($enabled) {
		?>
		<tr>
                  <td colspan="3" height="10" style="padding-left:10px; padding-right:10px;">
			<table>
			<TR>
			<TD>
			<fieldset>
			<table>
			<tr>
				<TD>
				<table>
				<TR>
					 <TD class="fieldName" nowrap>Day's Total</TD>
					<td>
						<input type="text" id="daysTotalQty" name="daysTotalQty" size="6" value="<?=$daysTotalQty?>" style="text-align:right;">
					</td>
				</TR>
				</table>
				</TD>
				<td>
				<table>
				<TR>
				    <TD class="fieldName" nowrap>Issued Today</TD>
                                    <td>
					<input type="text" id="daysIssuedQty" name="daysIssuedQty" size="6" value="<?=$daysIssuedQty?>" style="text-align:right;">
                                   </td>
				</TR>
				</table>
				</td>
				<td>
				<table>
				<TR>
				    <TD class="fieldName" nowrap>Sold Today</TD>
                                    <td>
					<input type="text" id="daysSoldQty" name="daysSoldQty" size="6" value="<?=$daysSoldQty?>" style="text-align:right;">
                                    </td>
				</TR>
				</table>
				</td>
				<td>					
					<input type="hidden" id="daysQtyTrackId" name="daysQtyTrackId" size="6" value="<?=$daysQtyTrackId?>" readonly>
					<input name="cmdDaysCalcUpdate" type="submit" class="button" id="cmdDaysCalcUpdate" value=" Update ">
				</td>
			</tr>				
			</table>
			</fieldset>
			</TD>
			</TR>
			</table>
                  </td>
                </tr>
		<? 
			}
		?>
                <tr>
                  <td colspan="3" height="10" >
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <table cellpadding="0" cellspacing="0" align="center">
                      <tr>
                        <td>
                          <? if($del==true){
                          ?>
                          <input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyIceUsageRecSize;?>);"><? }
                          ?>
                           &nbsp;<? if($add==true){
                          ?>
                          <input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }
                          ?>
                           &nbsp;<? if($print==true){
                          ?>
                          <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyIceUsage.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }
                          ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" height="5" >
                  </td>
                </tr>
                <?
									if($errDel!="")
									{
                ?>
                <tr>
                  <td colspan="3" height="15" align="center" class="err1">
                    <?=$errDel;
                    ?>
                  </td>
                </tr>
                <?
									} 
                ?>
                <tr>
                  <td width="1" ></td>
                  <td colspan="2" style="padding-left:10px; padding-right:10px;">
                    <table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
				if (sizeof($dailyIceUsageRecs) > 0) {
					$i	=	0; 
                      ?>
                      <? if($maxpage>1){
                      ?>
                      <tr bgcolor="#FFFFFF">
                        <td colspan="15" style="padding-right:10px">
                          <div align="right">
                            <?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
   				}
   				else
   				{
					$nav.= " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
                            ?>
                            <input type="hidden" name="pageNo" value="<?=$pageNo?>">
                          </div>
                        </td>
                      </tr>
                      <? }
                      ?>
		<tr  bgcolor="#f2f2f2" align="center">
		<td width="20" >
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">
			Date
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">
			Customer Type
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">
			Issued to
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Qty 
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Unit 
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Sold
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Sold<br> Qty
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Adjust<br> Qty
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Rate
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Amt
		</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">
			Pymnt<br> Recd
		</td>
		<?php
		if($edit==true){
		?>
		<td class="listing-head" width="45" >&nbsp;</td>
		<?php
		}
		?>
		</tr>
                 <?php
			foreach($dailyIceUsageRecs as $diur) {
				$i++;
				$dailyIceUsageId 	= $diur[0];
				$selEntryDate		= dateFormat($diur[1]);
				$selSupplierName	= ($diur[12])?$diur[12]:"SELF";
				//$selLndingCenterName	= ($diur[13])?$diur[13]:"SELF";
				$qty			= $diur[4];
				$selUnitName		= $diur[14];
				$sold			= $diur[6];
				$soldQty		= $diur[7];
				$rate			= $diur[8];
				$amount			= $diur[9];
				$adjustQty		= $diur[10];
				$receivedAmt		= $diur[11];
				
				$selCustomerType = $diur[15];
				$custType	 = $customerTypeArr[$selCustomerType];
				$selPlantName	 = $diur[13];
				$selPartyName	 = $diur[17];
				$selPartyLoc	 = $diur[18];
				$displayIssuedTo	= "";
				if ($selCustomerType=='SU') $displayIssuedTo = $selSupplierName;
				else if ($selCustomerType=='SE') $displayIssuedTo = $selPlantName;
				else if ($selCustomerType=='NW') $displayIssuedTo = $selPartyName."<br>(".$selPartyLoc.")";
				
                 ?>
		<tr  bgcolor="WHITE"  >
		<td width="20" height="25">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyIceUsageId;?>" class="chkBox">
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
			<?=$selEntryDate;?>
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;">
			<?=$custType;?>
		</td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap>
			<?=$displayIssuedTo;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=$qty;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
			<?=$selUnitName;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
			<?=($sold=='Y')?"YES":"NO";?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=($soldQty!=0)?$soldQty:"";?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=($adjustQty!=0)?$adjustQty:"";?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=($rate!=0)?$rate:"";?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=($amount!=0)?$amount:"";?>
		</td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
			<?=($receivedAmt!=0)?$receivedAmt:"";?>
		</td>
		<? if($edit==true){
		?>
		<td class="listing-item" width="45" align="center">
			<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyIceUsageId;?>,'editId');">
		</td>
		<? }
		?>
		</tr>
                      <?php
			} 
                      ?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value=""><input type="hidden" name="editDailyFreezingChartEntryId" value="<?=$freezingChartEntryId;?>">
                      <input type="hidden" name="editMode" value="<?=$editMode?>">
	<? if($maxpage>1){
                      ?>
                      <tr bgcolor="#FFFFFF">
                        <td colspan="15" style="padding-right:10px">
                          <div align="right">
                            <?php
	$nav  = '';
	for($page=1; $page<=$maxpage; $page++)
		{
			if ($page==$pageNo)
   				{
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page
   				}
   				else
   				{
					$nav.= " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"DailyIceUsage.php?pageNo=$page&selDate=$selDate\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
                            ?>
                            <input type="hidden" name="pageNo" value="<?=$pageNo?>">
                          </div>
                        </td>
                      </tr>
                      <? }
                      ?>
                      <?
												}
												else
												{
                      ?>
                      <tr bgcolor="white">
                        <td colspan="15"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;
                          ?>
                        </td>
                      </tr>
                      <?
												} 
                      ?>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" height="5" >
                  </td>
                </tr>
                <tr >
                  <td colspan="3">
                    <table cellpadding="0" cellspacing="0" align="center">
                      <tr>
                        <td>
                          <? if($del==true){
                          ?>
                          <input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$dailyIceUsageRecSize;?>);"><? }
                          ?>
                           &nbsp;<? if($add==true){
                          ?>
                          <input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }
                          ?>
                           &nbsp;<? if($print==true){
                          ?>
                          <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyIceUsage.php?selDate=<?=$selDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);"><? }
                          ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" height="5" >
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
      <td height="10">
      </td>
    </tr>
  </table>
  <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
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
