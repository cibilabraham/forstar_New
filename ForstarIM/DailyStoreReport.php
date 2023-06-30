<?php
	require("include/include.php");
	require_once("lib/purchaseorder_ajax.php");

	ob_start();

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
		
	$dateSelection = "?frozenPackingFrom=".$p["frozenPackingFrom"]."&frozenPackingTill=".$p["frozenPackingTill"]."&pageNo=".$p["pageNo"];	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	

	//------------  Checking Access Control Level  ----------------
	$add=$edit=$del=$print=$confirm=$reEdit=false;	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}		
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	

	list($urlFnId, $urlModuleId, $urlSubModuleId) = $modulemanagerObj->getFunctionIds($currentUrl);	
	$rfrshTimeLimit = $refreshTimeLimitObj->getRefreshTimeLimit($urlSubModuleId,$urlFnId);
	$refreshTimeLimit = ($rfrshTimeLimit!=0)?$rfrshTimeLimit:60;	
	//----------------------------------------------------------	
	$cyRateListId=$cyCode=$cyValue = "";


	
		
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------
	

	# select records between selected date
	if($g["frozenPackingFrom"]!="" && $g["frozenPackingTill"]!="") {
		$dateFrom = $g["frozenPackingFrom"];
		$dateTill = $g["frozenPackingTill"];
	} else if ($p["frozenPackingFrom"]!="" && $p["frozenPackingTill"]!="") {
		$dateFrom = $p["frozenPackingFrom"];
		$dateTill = $p["frozenPackingTill"];
	} else {
		//$dateFrom = date("d/m/Y");
		//$dateTill = date("d/m/Y");
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}


	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) 
	{	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		
		$purchaseOrderRecords = $purchaseorderObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		print_r($purchaseOrderRecords);
		# Fetch All Recs
		$fetchAllPORecords		= $purchaseorderObj->fetchAllRecords($fromDate, $tillDate);
		$purchaseOrderRecordSize	= sizeof($fetchAllPORecords);
	}
	

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllPORecords);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) { 
		#List All Fishes
		//$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();
		$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();
		#List All Freezing Stage Record
		//$freezingStageRecords	= $freezingstageObj->fetchAllRecords();
		$freezingStageRecords	= $freezingstageObj->fetchAllRecordsActivefreezingstage();
		#List All EU Code Records
			//$euCodeRecords		= $eucodeObj->fetchAllRecords();
			$euCodeRecords		= $eucodeObj->fetchAllRecordsActiveEucode();
		#List All Brand Records
		//	$brandRecords		= $brandObj->fetchAllRecords();
			
		#List All Frozen Code Records
			//$frozenPackingRecords	= $frozenpackingObj->fetchAllRecords();
			
		#List All MC Packing Records
			//$mcpackingRecords	= $mcpackingObj->fetchAllRecords();
		
		#List All Customer Records
			//$customerRecords	= $customerObj->fetchAllRecords();
			$customerRecords	= $customerObj->fetchAllRecordsActiveCustomer();
		#List All payment Terms Record
			$paymentTermRecords	= $paymenttermsObj->fetchAllRecords();
			//$paymentTermRecs	= $paymenttermsObj->fetchAllRecordsActivePayment();

		# List All Country
			//$countryMasterRecs	= $countryMasterObj->fetchAllRecords(); 
		$countryMasterRecs	= $countryMasterObj->fetchAllRecordsActivecountry();
		# Get Invoice Type Recs
		//$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecords();
		$invoiceTypeMasterRecs	= $invoiceTypeMasterObj->fetchAllRecordsActiveinvoice();
		# List Carriage Mode Recs
		//$carriageModeRecs = $carriageModeObj->fetchAllRecords();
		$carriageModeRecs = $carriageModeObj->fetchAllRecordsActivecarriagemode();
		# Document Instruction check list
		$docInstructionsChkList =$docInstructionsObj->findAll();

		# Get All Currency Recs
		$currencyRecs	= $usdvalueObj->getCYRecs();
		
		/*
		# Get Purchase Order Unit Recs 
		$spoUnitRecs = array("1"=>"Kgs","2"=>"Lbs");
		*/
	}

	if ($editMode) {
		# get Brand Recs
		$brandRecs     = $purchaseorderObj->getBrandRecords($selCustomerId);
		# Get Agent
		$agentRecs = $purchaseorderObj->getAgentRecs($selCustomerId);
		# Port List
		$portRecs = $purchaseorderObj->getPortRecs($selCountry);
		# Payment terms
		$paymentTermRecs = $purchaseorderObj->getPaymentTermRecs($selCustomerId);
	}

	/* Wt Type array */
	$wtTypeArr = array("FW"=>"Frozen Wt", "NW"=>"Net Wt");

	if ($editMode)	$heading = $label_editPurchaseOrder;
	else 		$heading = $label_addPurchaseOrder;
	
	list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
					if($company=="")
					{
						$units=$unitRecords[$defaultCompany];
					}
					else
					{
						$units=$unitRecords[$company];
					}


	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/purchaseorder.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<div id="spo-filter"></div>
<div id="spo-box">
	<iframe width="95%" height="400" id="addNewIFrame" src="" style="border:none;" frameborder="0"></iframe>	
	<p align="center"> 
		<input type="button" name="cancel" value="Close" onClick="closeLightBox()">
	</p>
</div>
<form name="frmPurchaseOrder" id="frmPurchaseOrder" action="DailyStoreReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">	
		
	<tr>
		<td height="10" align="center" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" >&nbsp;Store Report  </td>
								
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintDailyStoreReport.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?>
											</td>
										</tr>
									</table>									
								</td>
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
								<td colspan="2" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if (sizeof($purchaseOrderRecords)>0) {
										$i	=	0;
									?>
									<? if($maxpage>1){?>
										<tr bgcolor="#FFFFFF">
											<td colspan="12" align="right" style="padding-right:10px;">
												<div align="right">
												<?php
												 $nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
												} else {
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
									<?php
										 }
									?>
									<tr  bgcolor="#f2f2f2" align="center">
										<td width="20">
											<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
										</td>
										<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;" nowrap>PO No.</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>PO Date</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Customer</td>		
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total Num MC</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in USD</td>				
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in INR</td>	
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Entry<br/>Date</td>
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Despatch<br/>Date</td>	
										<td class="listing-head" style="padding-left:5px; padding-right:5px;">Quick Entry List</td>	
										<td class="listing-head">&nbsp;</td>
										<? if($edit==true){?>
										<td class="listing-head"></td>
										<? }?>		
									</tr>
									<?php
									$totalValueInUSD = "";
									$totalValueInINR = "";
									$totalNumPC = "";	
									foreach ($purchaseOrderRecords as $por) {
										$i++;
										$poMainId = $por[0];
										$custName 	= $por[6];
										$poNumMC	= $por[1];
										$totalNumPC	+= $poNumMC;

										$poValueUSD	= $por[2];
										$totalValueInUSD += $poValueUSD;

										$poValueINR	= $por[3];
										$totalValueInINR += $poValueINR;

										$poLastDate	= $por[4];
										$soEntryDate  	= $por[5];

										# ----- QEL Gen Starts ---------
										$qelGen		= $por[7];
										$qelConfirmed 	= $por[8];
										//echo "$qelGen, $qelConfirmed";
										$qelMsg    = "";
										$qelStatus = "";
										$qelBgColor = "";
										if ($qelConfirmed=='Y') {
											$qelMsg    = "COMPLETED";
											$qelStatus = "COMPLETED";
											$qelBgColor = "#90EE90";
										} else if ($qelGen=='Y') {						
											$qelMsg    = "GENERATED";
											$qelStatus = "PENDING";
											$qelBgColor = "gray";
										} else  {					
											$qelMsg    = "Click here to generate new Quick Entry List.";
											$qelStatus = "<a href='###' onclick=\"return validateQELGen('$poMainId', '$userId', '$i', '$poLastDate');\" class='link2'>GENERATE</a>";
											$qelBgColor = "white";
										}

										/*Disable edit only after all purchase Order invoice Confirmed*/
										$notConfirmedCount = $por[12];
										$invoiceCount = $por[13];
										$poCompletedStatus = $por[9];
										$disableEdit = ($poCompletedStatus=='C' && ($invoiceCount>0 && $notConfirmedCount<=0))?"disabled":"";
										$purchaseOrderNo	= $por[10];
										$purchaseOrderDate	= dateFormat($por[11]);

									
									?>
									<tr  <?=$listRowMouseOverStyle?>>
										<td width="20" height="25">
											<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$poMainId;?>" class="chkBox" />
											<input type="hidden" name="PORMEntryId_<?=$i;?>" value="<?=$PORMEntryId?>">
											<input type="hidden" name="POGradeEntryId_<?=$i;?>" value="<?=$POGradeEntryId?>">
										</td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=($purchaseOrderNo!="")?$purchaseOrderNo:"";?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=($purchaseOrderDate!='00/00/0000')?$purchaseOrderDate:"";?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$custName?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poNumMC?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueUSD?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueINR?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($soEntryDate)?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($poLastDate)?></td>
										<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"  onMouseover="ShowTip('<?=$qelMsg?>');" onMouseout="UnTip();" id="qelCol_<?=$i?>" bgcolor="<?=$qelBgColor?>">
											<?=$qelStatus;?>
										</td>
										<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px; line-height:normal;" nowrap="true">
											<a href="javascript:printWindow('ViewPO.php?selPOId=<?=$poMainId?>',700,600)" class="link1" title="Click here to View PO">
												VIEW
											</a>
											<?php 
												if($print==true){
											?>
											<!--/-->
											<!--<a href="javascript:printWindow('PrintSOTaxInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to Print the Invoice">
												PRINT
											</a>-->
											<? }?>
										</td>		
										<? 
										//echo $edit;
											if($edit==true){?>
											  <td class="listing-item" width="45" align="center">
												<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$poMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyStoreReport.php';" <?=$disableEdit?>>
											</td>
										  <? }?>
									</tr>
									<?php
										}
									?>
									<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
									<input type="hidden" name="editId" value="">
									<tr bgcolor="white">
										<td height="10" colspan="4" align="right" class="listing-head">Total :</td>
										<td  height="10" align="right" class="listing-item" style="padding-right:5px; padding-left:5px;">
											<strong><?=number_format($totalNumPC,0);?></strong>
										</td>
										<td class="listing-item" style="padding-right:5px; padding-left:5px;" align="right">
											<strong><?=number_format($totalValueInUSD,2);?></strong>
										</td>
										<td class="listing-item" style="padding-right:5px; padding-left:5px;" align="right">
											<strong><?=number_format($totalValueInINR,2);?></strong>
										</td>
										<td class="listing-item" style="padding-right:5px; padding-left:5px;">
											&nbsp;
										</td>
										<td class="listing-item" style="padding-right:5px; padding-left:5px;">
											&nbsp;
										</td>
										<td class="listing-head"></td>
										<td class="listing-head">&nbsp;</td>
										<? if($edit==true){?>
										<td class="listing-head"></td>
										<? }?>
									</tr>
									<? if($maxpage>1){?>
									<tr bgcolor="#FFFFFF">
										<td colspan="12" align="right" style="padding-right:10px;">
											<div align="right">
											<?php
											 $nav  = '';
											for ($page=1; $page<=$maxpage; $page++) {
												if ($page==$pageNo) {
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												} else {
														$nav.= " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\" class=\"link1\">$page</a> ";
													//echo $nav;
												}
											}
											if ($pageNo > 1) {
												$page  = $pageNo - 1;
												$prev  = " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\"><<</a> ";
											} else {
												$prev  = '&nbsp;'; // we're on page one, don't print previous link
												$first = '&nbsp;'; // nor the first page link
											}

											if ($pageNo < $maxpage) {
												$page = $pageNo + 1;
												$next = " <a href=\"DailyStoreReport.php?pageNo=$page&frozenPackingFrom=$dateFrom&frozenPackingTill=$dateTill\"  class=\"link1\">>></a> ";
											} else {
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
									<? }?>
								  <?
									} else {
									?>
									<tr bgcolor="white">
										<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
										}
									?>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5" >
								<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>">
								<input type="hidden" name="rmEntryId" id="rmEntryId" value="<?=$rmEntryId?>"><input type="hidden" name="gradeEntryId" id="gradeEntryId" value="<?=$gradeEntryId?>">
							</td>
						</tr>
						<tr >	
							<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td><? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyStoreReport.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
									</tr>
								</table>									
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5" ></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
				<!-- Form fields end   -->
	</td>
</tr>	
<input type="hidden" name="editPORMEntryId" value="<?=$editPORMEntryId?>">
<input type="hidden" name="editPOGradeEntryId" value="<?=$editPOGradeEntryId?>">
<input type="hidden" name="editSelectionChange" value="0">							
<input type="hidden" name="editMode" value="<?=$editMode?>">
<input type="hidden" name="oneUSDToINR" id="oneUSDToINR" value="<?=$oneUSD?>" readonly>
<input type="hidden" name="currencyRateListId" id="currencyRateListId" value="<?=$cyRateListId?>" readonly>
<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>" />
<input type="hidden" name="hidKG2LBS" id="hidKG2LBS" value="<?=KG2LBS?>" />
<tr>
	<td height="10"></td>
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
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "lastDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "lastDate", 
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
			inputField  : "frozenPackingFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingFrom", 
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
			inputField  : "frozenPackingTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "frozenPackingTill", 
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
			inputField  : "poDate",         // ID of the input field
			eventName  : "click",	    // name of event
			button : "poDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<?php
		if ($addMode || $editMode) {
	?>		
	<script language="JavaScript" type="text/javascript">
		//showInvRow('<?=$mode?>', '<?=$proformaInvoiceNo?>', '<?=$sampleInvoiceNo?>');

		// Split Row
		function addSplitRow()
		{
			splitPO('tblSplitItem', '', '', '', '', '', '', '');			
		}
	</script>
	<?php
		}
	?>
		<script language="JavaScript" type="text/javascript">
			<?php
				if (sizeof($poRawItemRecs)>0) {

				// Set Value to Main table
			?>
				fieldId = <?=sizeof($poRawItemRecs)?>;
			<?php
				}
			?>
			<?php
				if (sizeof($splitInvoiceMainRecs)>0) {
					
				// Set value to Split Row
			?>
				fldId = <?=sizeof($splitInvoiceMainRecs)?>;
				chkMCQty();
			<?php
				} else if ($editMode && !sizeof($splitInvoiceMainRecs)) {
			?>
				// Display Split Row section
				addSplitRow(); 
			<?php
				}
			?>
			
			function addNewItem()
			{				
				addNewPOItem('tblPOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '<?=$mode?>');
				xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
			function addNewItemCpy()
			{
				addNewPOItem('tblPOItem', '', '', '', '', '', '', '', '', '', '', '', '', '', 'C', '<?=$mode?>');
				xajax_getBrandRecs(document.getElementById('hidTableRowCount').value, document.getElementById('selCustomer').value);
			}
		</script>
	<?php 
		if ($addMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			window.load = addNewItem();
		</SCRIPT>
	<?php 
		}
	?>

	<?php
		if (sizeof($poRawItemRecs)>0 && $enabled) {			
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getBrandRecs('<?=sizeof($poRawItemRecs);?>', '<?=$selCustomerId?>');
	</script>
	<!--SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<?php
		// When Edit Mode Products are loading from Top function
		$totalAmount = 0;
		$k = 0;
		foreach ($poRawItemRecs as $rec) {
			$poEntryId = $rec[0];
			$selFishId = $rec[1];
			$selProcessCodeId = $rec[2];
			$selEuCodeId	  = $rec[3];

			$selBrd		  = $rec[4];
			$selBrdFrom	  = $rec[13];
			$selBrandId	  = $selBrd."_".$selBrdFrom;

			$selGradeId	  = $rec[5];
			$selFreezingStageId = $rec[6];
			$selFrozenCodeId  = $rec[7];
			$selMCPackingId	  = $rec[8];
			$numMC		= $rec[9];
			$pricePerKg	= $rec[10];
			$valueInUSD	= $rec[11];
			$valueInINR	= $rec[12];
		?>					
			addNewPOItem('tblPOItem', '<?=$poEntryId?>', '<?=$selFishId?>', '<?=$selProcessCodeId?>', '<?=$selEuCodeId?>', '<?=$selBrandId?>', '<?=$selGradeId?>', '<?=$selFreezingStageId?>', '<?=$selFrozenCodeId?>', '<?=$selMCPackingId?>', '<?=$numMC?>', '<?=$pricePerKg?>', '<?=$valueInUSD?>', '<?=$valueInINR?>', '', '<?=$mode?>');
			xajax_getProcessCodes('<?=$selFishId?>', '<?=$k?>', '<?=$selProcessCodeId?>');
			xajax_getGradeRecs('<?=$selProcessCodeId?>', '<?=$k?>', '<?=$selGradeId?>');
			xajax_getFilledWt('<?=$selFrozenCodeId?>', '<?=$k?>');
			xajax_getNumMC('<?=$selMCPackingId?>', '<?=$k?>');
			xajax_getNumMCExist('<?=$mainId?>', '<?=$selFishId?>', '<?=$selProcessCodeId?>', '<?=$selGradeId?>', '<?=$k?>');
		<?php 
			$k++;
			}  // SO Loop Ends here
		?>
			xajax_getBrandRecs('<?=sizeof($poRawItemRecs);?>', '<?=$selCustomerId?>');
	</SCRIPT-->
	<?php
		} // SO Size Check
	?>
	<script language="JavaScript" type="text/javascript">
		function reLoadCustomer() 
		{
			xajax_reloadCustomer();
		}

		function reLoadAgent()
		{
			xajax_getAgentList(document.getElementById('selCustomer').value, '');
		}
	</script>
	<?php
	 if ($editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		//xajax_getAgentList('<?=$selCustomerId?>', '<?=$selAgent?>');
		//xajax_getPortList('<?=$selCountry?>', '<?=$selPort?>');
		//xajax_getCustPaymentTerm('<?=$selCustomerId?>', '<?=$paymentTerms?>');
	</script>
	<?php
		}
	?>

	<?php
		# Split invoice section && $k=sizeof($poRawItemRecs)	
		if (sizeof($splitInvoiceMainRecs)>0 && $enabled) {
	?>	
	<!--SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<?php
			$l = 0;
			foreach ($splitInvoiceMainRecs as $sir) {
				$invoiceId 	= $sir[0];
				$invNo		= $sir[1];
				$invDate	= dateFormat($sir[2]);
				$invType	= $sir[3];
				$invProfomaNo	= $sir[4];
				$invSampleNo	= $sir[5];
				$entryDate	= $sir[6];
		?>
			splitPO('tblSplitItem', '<?=$invoiceId?>', '<?=$invNo?>', '<?=$invDate?>', '<?=$invType?>', '<?=$invProfomaNo?>', '<?=$invSampleNo?>', '<?=$entryDate?>');
		<?php 
			$l++;
			}  // Split invoice Loop Ends here
		?>
	</SCRIPT-->	
	<?php
		} // Split invoice Size Check
	?>

	<script language="JavaScript" type="text/javascript">
		$(document).ready(function () {
			<?php
			if ($cyCode!="")
			{	
			?>
			$(".replaceCY").html('<?=$cyCode?>');
			<?php
			}	
			?>			

			$('#selUnit').change(function() {
			  changeUnitTxt();
			});

			<?php
			if ($editMode)
			{	
			?>
			changeUnitTxt();
			calcAllRowVal();
			<?php
			}	
			?>
		});
	</script>

	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>