<?php
	require("include/include.php");	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selFilterStkType=".$p["selFilterStkType"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	#Cancel
	if ($p["cmdCancel"]!="") $addMode = false;

	if ($p["selStkType"]!="")	$selStkType = $p["selStkType"];
	if ($p["selDate"]!="")		$selDate	= $p["selDate"];	

	#Add a Product
	if ($p["cmdAdd"]!="") {

		$selDate	= mysqlDateFormat($p["selDate"]);
		$selStkType	= $p["selStkType"];

		$rowCount	= $p["hidTableRowCount"];
		
		# Check for existing rec
		$recExist	= $physicalStockEntryObj->chkRecExist($selDate, $selStkType, $cId);

		if ($selDate!="" && $selStkType!="" && !$recExist) {
			$physicalStockRecIns = $physicalStockEntryObj->addPhysicalStock($selDate, $selStkType, $userId);

			if ($physicalStockRecIns) {
				#Find the Last inserted Id From m_physical_stock
				$physicalStkEntryId = $databaseConnect->getLastInsertedId();
			}
			if ($physicalStkEntryId) {
				for ($i=1; $i<=$rowCount; $i++) {
					$stockId 	= $p["stkId_".$i];
					$stkQty	 	= $p["stkQty_".$i];
					$physicalStkQty = $p["physicalStkQty_".$i];
					$diffStkQty	= $p["diffStkQty_".$i];
					if ($stockId!="" && $physicalStkEntryId!="") {
						$physicalStockEntryRecIns = $physicalStockEntryObj->addPhysicalStockEntries($physicalStkEntryId, $stockId, $physicalStkQty, $stkQty, $diffStkQty);
					}
				} // For Loop Ends 
			} // entry Id Check				
		} // Condition Ends

		if ($physicalStockRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddPhysicalStockEntry);
			$sessObj->createSession("nextPage",$url_afterAddPhysicalStockEntry.$selection);
		} else {
			$addMode	=	true;
			if ($recExist) $err = $msg_failAddPhysicalStockEntry."<br>".$msgProductMRPExistRec ;
			else $err	= $msg_failAddPhysicalStockEntry;
		}
		$physicalStockRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$physicalStockEntryRec	=	$physicalStockEntryObj->find($editId);
		
		$editPhysicalStockRecId	= $physicalStockEntryRec[0];
		$selDate		= dateFormat($physicalStockEntryRec[1]);
		$selStkType		= $physicalStockEntryRec[2];

		$disableField		= "disabled";		
	}

	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$physicalStockRecId = $p["hidPhysicalStockRecId"];

		$selDate	= mysqlDateFormat($p["selDate"]);
		//$selStkType	= $p["selStkType"];
		$selStkType	= $p["hidSelStkType"];
		$rowCount	= $p["hidTableRowCount"];
		
		# Check for existing rec
		$recExist	= $physicalStockEntryObj->chkRecExist($selDate, $selStkType, $physicalStockRecId);	

		if ($selDate!="" && $selStkType!="" && !$recExist) {
			$physicalStockEntryRecUptd = $physicalStockEntryObj->updatePhysicalStock($physicalStockRecId, $selDate, $selStkType);	
			if ($physicalStockEntryRecUptd) {
				# Delete Physical Stk Entries
				$deletePhysicalStkEntries = $physicalStockEntryObj->delPhysicalStockEntries($physicalStockRecId);
				for ($i=1; $i<=$rowCount; $i++) {
					$stockId 	= $p["stkId_".$i];
					$stkQty	 	= $p["stkQty_".$i];
					$physicalStkQty = $p["physicalStkQty_".$i];
					$diffStkQty	= $p["diffStkQty_".$i];
					if ($stockId!="" && $physicalStockRecId!="") {
						$physicalStockEntryRecIns = $physicalStockEntryObj->addPhysicalStockEntries($physicalStockRecId, $stockId, $physicalStkQty, $stkQty, $diffStkQty);
					}
				} // For Loop Ends 
			}	
		}
	
		if ($physicalStockEntryRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPhysicalStockEntryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePhysicalStockEntry.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failPhysicalStockEntryUpdate."<br>".$msgProductMRPExistRec ;
			else $err = $msg_failPhysicalStockEntryUpdate;
		}
		$physicalStockEntryRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$physicalStockRecId	=	$p["delId_".$i];
			if ($physicalStockRecId!="" ) {		
				// Need to check
				# Delete Physical Stk Entries (entry Table)
				$deletePhysicalStkEntries = $physicalStockEntryObj->delPhysicalStockEntries($physicalStockRecId);

				# Main Table Rec Del		
				$physicalStockEntryRecDel = $physicalStockEntryObj->deletePhysicalStock($physicalStockRecId);
			}
		}
		if ($physicalStockEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPhysicalStockEntry);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockEntry.$selection);
		} else {
			$errDel	=	$msg_failDelPhysicalStockEntry;
		}
		$physicalStockEntryRecDel	=	false;
	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$physicalStockRecId		=	$p["confirmId"];


			if ($physicalStockRecId!="") {
				// Checking the selected fish is link with any other process
				$physicalStockRecConfirm = $physicalStockEntryObj->updatephysicalStockconfirm($physicalStockRecId);
			}

		}
		if ($physicalStockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmphysicalStock);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockEntry.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmphysicalStock;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$physicalStockRecId	 = $p["confirmId"];

			if ($physicalStockRecId!="") {
				#Check any entries exist
				
					$physicalStockRecConfirm = $physicalStockEntryObj->updatephysicalStockReleaseconfirm($physicalStockRecId);
				
			}
		}
		if ($physicalStockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmphysicalStock);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockEntry.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmphysicalStock;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	
	if ($g["selFilterStkType"]!="") $selFilterStkType = $g["selFilterStkType"];
	else 				$selFilterStkType = $p["selFilterStkType"];
	
	# List all Records
	$physicalStockRecords 		= $physicalStockEntryObj->fetchAllPagingRecords($offset, $limit, $selFilterStkType);
	$physicalStockRecordSize   	= sizeof($physicalStockRecords);

	## -------------- Pagination Settings II -------------------
	// Fetch All Records
	$fetchPhysicalStockRecords = $physicalStockEntryObj->fetchAllRecords($selFilterStkType);
	$numrows	=  sizeof($fetchPhysicalStockRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ingredient
	if ($addMode || $editMode) {
		# Get All Product Records	
		
		if ($selStkType=='I') {
			$stockRecords = $physicalStockEntryObj->getIngredientRecords();
		} else if ($selStkType=='P') {
			$stockRecords = $physicalStockEntryObj->getProductRecords();
		} else if ($selStkType=='S') {
			$stockRecords = $physicalStockEntryObj->getSemiFinishedProductRecs();
		}
	}
	
	if ($editMode) $heading	= $label_editPhysicalStockEntry;
	else	       $heading	= $label_addPhysicalStockEntry;
	

	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/PhysicalStockEntry.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	//$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	 require("template/topLeftNav.php");

	/*
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
	*/
?>
	<form name="frmPhysicalStockEntry" action="PhysicalStockEntry.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Physical Stock Entry";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
	<tr>
		<td width="1" ></td>
		<td colspan="2" >
		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhysicalStockEntry.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntry);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhysicalStockEntry.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntry);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidPhysicalStockRecId" value="<?=$editPhysicalStockRecId;?>">
		<tr>
			<td nowrap height="10"></td>			
		  </tr>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top" align="center">
			<table>
				<tr>
					<td class="fieldName">Date</td>
					<td class="listing-item">
					<input name="selDate" type="text" id="selDate" value="<?=$selDate?>" size="9" autoComplete="off" />
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap="true">Stock Type</td>
					<td nowrap="true">
					<select name="selStkType" id="selStkType" onchange="this.form.submit();" <?=$disableField?>>
					<option value="">-- Select --</option>
					<?php			
					foreach ($stkTypes as $key=>$value) {
						$selected = ($key==$selStkType)?"Selected":"";							
						?>
						<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
						<? }?>
					</select>
					<input type="hidden" name="hidSelStkType" id="hidSelStkType" value="<?=$selStkType?>">
					</td>
				</tr>
			</table>
		</td>
		</tr>
		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top" align="center">
					<table>
			<?php			
			if (sizeof($stockRecords)>0) {
				$j = 0;
			?>
			<tr>
				<td class="fieldName" nowrap colspan="2">
					<table  cellspacing="1" cellpadding="3" id="newspaper-b1">
					<tr align="center">
						<th class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Stock Name</th>
						<th class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Stock Qty</th>
						<th class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Physical <br/>Stock Qty</th>
						<th class="listing-head" style="padding-left:5px;padding-right:5px;" nowrap="true">Diff</th>
					</tr>	
					<?php
						foreach ($stockRecords as $sr) {
							$j++;
							$stockId	= $sr[0];
							$stockName	= $sr[2];
							$stkQty		= $sr[3];

							if ($editMode) {
								list($stkQty, $physicalStockQty, $diffStockQty) = $physicalStockEntryObj->getPhysicalStkRec($editPhysicalStockRecId, $stockId);
							}
					?>
					<tr>
						<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true">
							<input type="hidden" name="stkId_<?=$j?>" id="stkId_<?=$j?>" size="4" value="<?=$stockId?>" />	
							<?=$stockName?>
						</td>
						<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center">
							<input type="text" name="stkQty_<?=$j?>" id="stkQty_<?=$j?>" size="9" style="border:none;text-align:right;" value="<?=$stkQty?>" readonly="true" />
						</td>
						<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center">
							<input type="text" name="physicalStkQty_<?=$j?>" id="physicalStkQty_<?=$j?>" size="8" style="text-align:right;" value="<?=$physicalStockQty?>" onkeydown="return nextStockBox(event,'document.frmPhysicalStockEntry','physicalStkQty_<?=$j+1?>');" autocomplete="off" onkeyup="calcStkDiff('<?=$j?>');" tabindex="<?=$j?>" />	
						</td>
						<td class="listing-item" style="padding-left:5px;padding-right:5px;" nowrap="true" align="center">
							<input type="text" name="diffStkQty_<?=$j?>" id="diffStkQty_<?=$j?>" size="9" style="border:none;text-align:right;font-weight:bold;" value="<?=$diffStockQty?>" />
						</td>
					</tr>			
					<?php
						}
					?>
				</table>
				</td>
			</tr>
			<?
			  } // Stock Size check
			?>				
                                              </table>
			<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$j?>">
					</td>					
					</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhysicalStockEntry.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntry);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PhysicalStockEntry.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntry);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
			<?php
				require("template/rbBottom.php");
			?>
		</td>
		</tr>
		</table>
	<!-- Form fields end   --></td>
		</tr>
		<?
			}			
			# Listing Category Starts
		?>
			</table>
		</td>
	</tr>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
<tr>
				<td colspan="3" align="center">
						<table width="25%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table  cellspacing="0" cellpadding="0">
			<tr>
				<td class="listing-item" style="padding-left:2px;padding-right:2px;" nowrap="true">Stock Type</td>
				<td style="padding-left:2px;padding-right:10px;">
					<select name="selFilterStkType" id="selFilterStkType" onchange="this.form.submit();">
						 <option value="">-- Select --</option>
						 <?php			
							foreach ($stkTypes as $key=>$value) {
								$selected = ($key==$selFilterStkType)?"Selected":"";
						 ?>
						 <option value="<?=$key?>" <?=$selected?>><?=$value?></option>
						 <? }?>
						 </select>
				</td>
			</tr>
		</table>
		</td></tr>
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
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
	<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Physical Stock Entry</td>
	<td background="images/heading_bg.gif" align="right" nowrap="nowrap" style="padding-left:20px;"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhysicalStockEntry.php?selFilterStkType=<?=$selFilterStkType?>',700,600);"><?}?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
	<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if ( sizeof($physicalStockRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Stock Type</th>		
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($physicalStockRecords as $psr) {
		$i++;
		$physicalStockRecId	= $psr[0];		
		$selEntryDate	= dateFormat($psr[1]);
		$selStockType   = $stkTypes[$psr[2]];
		$active=$psr[3];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$physicalStockRecId;?>" class="chkBox"></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selEntryDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selStockType;?></td>	
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$physicalStockRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PhysicalStockEntry.php';">
		</td>
		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$physicalStockRecId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$physicalStockRecId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PhysicalStockEntry.php?pageNo=$page&selFilterStkType=$selFilterStkType\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr>
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhysicalStockEntry.php?selFilterStkType=<?=$selFilterStkType?>',700,600);"><?}?></td>
											</tr>
										</table>			</td>
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
				<!-- Form fields end   -->	
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table> 
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
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
	<script language="JavaScript" type="text/javascript">
		//xajax_getProductGroupExist('<?=$selProductStateId?>', '<?=$selProductGroupId?>');
	</script>	
<?php 
	if ($iFrameVal=="") { 
?>
	<!--script language="javascript">	
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	ensureInFrameset(document.frmPhysicalStockEntry);	
	</script-->
<?php 
	}
?>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
	require("template/bottomRightNav.php");
?>