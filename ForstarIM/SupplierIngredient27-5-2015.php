<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$addAnother	= false;
	$layer		= "";	

	$selection = "?pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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
	if ($p["cmdAddNew"]!="") $addMode	= true;	
	if ($p["cmdCancel"]!="") {
		$addMode	= false;
		$editMode	= false;
	}

	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];	
	if ($p["selIngredient"]!="") $selIngredient = $p["selIngredient"];
	
	# Insert a Rec	
	if ($p["cmdAdd"]!="" || $p["cmdAddAnother"]!="") {	
		$selSupplierId		= $p["selSupplier"];
		$selIngredient		= $p["selIngredient"];
		# Check for unique records
		$uniqueRecords = $supplierIngredientObj->chkUniqueRecords($selSupplierId, $selIngredient, $cId);	
		if ($selSupplierId) {							
			if ($selSupplierId!="" && $selIngredient!="") {				
				$supplierIngredientRecIns	=	$supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient, $userId);		
			}
			if ($supplierIngredientRecIns) {
				$addMode	=	false;				
				$sessObj->createSession("displayMsg",$msg_succAddSupplierIngredient);
				//$sessObj->createSession("nextPage",$url_afterAddSupplierIngredient.$selection);
				if ($p["cmdAddAnother"]!="") {
					$addMode	=	true;
					$addAnother	=	true;
					$selSupplierId	=	"";
					$selIngredient	=	"";					
				} else if ($p["cmdAdd"]!="") {
					$sessObj->createSession("nextPage",$url_afterAddSupplierIngredient.$selection);
					$selSupplierId	=	"";
				}
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSupplierIngredient;
			}
			$supplierIngredientRecIns		=	false;
		} else {
			$addMode	=	true;
			if ($uniqueRecords) $err = $msg_failAddSupplierIngredient."<br>".$msgFailSupplierIngExist;
			//$err		=	$msg_failAddSupplierIngredient;
		}
		$uniqueRecords = false;
	}	
	
	# Edit 	
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$supplierIngredientRec	=	$supplierIngredientObj->find($editId);		
		$editSupplierIngredientId	= $supplierIngredientRec[0];				
		$selSupplierId			= $supplierIngredientRec[1];
		$selIngredient			= $supplierIngredientRec[2];		
	}


	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$supplierIngredientId	=	$p["hidSupplierIngredientId"];

		$selSupplierId		= $p["selSupplier"];
		$selIngredient		= $p["selIngredient"];
		# Check for unique records
		//$uniqueRecords = $supplierIngredientObj->chkUniqueRecords($selSupplierId, $selIngredient, $supplierIngredientId);	
		if ($selSupplierId!="" && $selIngredient!="") {
			$supplierIngredientRecUptd = $supplierIngredientObj->addSupplierIngredient($selSupplierId, $selIngredient, $userId);
		}		
		/*
		if (!$uniqueRecords) {
			if ($supplierIngredientId!="" && $selSupplierId!="" && $selIngredient!="") {
				$supplierIngredientRecUptd	=	$supplierIngredientObj->updateSupplierIngredient($supplierIngredientId, $selSupplierId, $selIngredient);			
			}	
		}
		*/
		if ($supplierIngredientRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSupplierIngredientUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierIngredient.$selection);
			$editMode = false;
		} else {
			$editMode	=	true;
			if ($uniqueRecords) $err = $msg_failSupplierIngredientUpdate."<br>".$msgFailSupplierIngExist;
			else $err		=	$msg_failSupplierIngredientUpdate;			
		}
		$supplierIngredientRecUptd	=	false;
	}
	

	# Delete Supplier Stock
	if ($p["cmdDelete"]!="") {
		$supplierIngUsed = false;
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierIngredientId	= $p["delId_".$i];			
			$supplierId		= $p["hidSupplierId_".$i];	
			$ingId			= $p["hidIngId_".$i];
	
			/*
			#checking supplier Ing using in PO			
			$supplierIngExist  = $supplierIngredientObj->chkSupplierIngExist($supplierId, $ingId);
			if ($supplierIngredientId!=""  && $supplierIngExist!="") $supplierIngUsed = true;
				//&& !$supplierIngExist
			*/

			if ($supplierIngredientId!=""  ) {				
				$supplierIngredientRecDel = $supplierIngredientObj->deleteSupplierIngreient($supplierId);
				//$supplierIngredientRecDel =	$supplierIngredientObj->deleteSupplierIngredient($supplierIngredientId);	
			}
		}
		if ($supplierIngredientRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelSupplierIngredient.$selection);
		} else {
			if ($supplierIngUsed) $errDel = $msg_failDelSupplierIngredient."<br>".$msgForUsingSupplierIng;
			else $errDel	=	$msg_failDelSupplierIngredient;
		}
		$supplierIngredientRecDel	=	false;
	}
	



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingMainCategoryId	=	$p["confirmId"];


			if ($ingMainCategoryId!="") {
				// Checking the selected fish is link with any other process
				$ingMainCategoryRecConfirm = $supplierIngredientObj->updateSupplierIngredientconfirm($ingMainCategoryId);
			}

		}
		if ($ingMainCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelIngMainCategory.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$ingMainCategoryId= $p["confirmId"];

			if ($ingMainCategoryId!="") {
				#Check any entries exist
				
					$ingMainCategoryRecConfirm = $supplierIngredientObj->updateSupplierIngredientReleaseconfirm($ingMainCategoryId);
				
			}
		}
		if ($ingMainCategoryRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingSupplierIngredient);
			$sessObj->createSession("nextPage",$url_afterDelIngMainCategory.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!= "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------

	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];	

	# Resettting offset values
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;			
	} 	

	#List all Supplier Ingredient
	$supplierIngredientRecords	= $supplierIngredientObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId);
	$supplierIngredientSize	= sizeof($supplierIngredientRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($supplierIngredientObj->getAllRecords($supplierFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	# List all Supplier
	$supplierRecords	= $supplierMasterObj->fetchAllRecordsActivesupplier("RTE");
		
	#List all Ingredients
	if ($selSupplierId) {
		$ingredientResultSetObj = $supplierIngredientObj->fetchAllSelectedIngRecords($selSupplierId);
	} else {
		$ingredientResultSetObj = $ingredientMasterObj->fetchAllRecords();
	}
	
	//$displaySize = ceil($ingredientResultSetObj->getNumRows()/5);
	$displaySize = ceil(sizeof($ingredientResultSetObj)/5);

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/SupplierIngredient.js"; 

	if ($editMode)	$heading	= $label_editSupplierIngredient;
	else		$heading	= $label_addSupplierIngredient;
		
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmSupplierIngredient" action="SupplierIngredient.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>
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
				<?	
					$bxHeader="Ingredient Suppliers";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">

		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%">
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
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center" nowrap="true">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);" title="Save and Close"> &nbsp;&nbsp;<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidSupplierIngredientId" value="<?=$editSupplierIngredientId;?>">
											
										<tr>
											<td nowrap height="2">&nbsp;</td>
											
										</tr>
										<tr>
											  <td colspan="2" nowrap>
				<table width="200" align="center">
                                                <tr>
                                                  <td colspan="2" valign="top">
	  <table>
		  <tr>
			  <td class="fieldName">*Supplier</td>
			  <td>
			  <select name="selSupplier" onchange="this.form.submit();">
                          <option value="">--select--</option>
                           <?						  
			  foreach($supplierRecords as $sr) {
				$supplierId			=	$sr[0];
				$supplierCode			=	stripSlash($sr[1]);
				$supplierName			=	stripSlash($sr[2]);
				$selected ="";
				if($selSupplierId==$supplierId) $selected="selected";
			?>
                       <option value="<?=$supplierId?>" <?=$selected;?>>
                                                    <?=$supplierName?>
                                                    </option>
                                                    <? }?>
                                                  </select></td>
		  </tr>
		  <tr>
		    <td class="fieldName">*Ingredient</td>
		    <td>
			 <select name="selIngredient[]" multiple="true" id="selIngredient" size="<?=$displaySize?>">
				<option value="">-- Select --</option>
				<?					
				foreach($ingredientResultSetObj as $kVal=>$ir) {
					$ingredientId = $ir[0];
					$ingredientCode	= stripSlash($ir[1]);
					$ingredientName	= stripSlash($ir[2]);
					# While Sel Supplier
					$sIngredientId = "";
					$sIngredientId = $ir[3];
					$selected = ($sIngredientId==$ingredientId && $ingredientId!="")?"selected":"";
					
				?>
					<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
				<? }?>
			</select>
			</td>
	    </tr>
		  </table>										
		 </tr>
            </tr>
                                              </table></td>
										  </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center" nowrap="true">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierIngredient.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Save & Exit " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);" title="Save and Close">&nbsp;&nbsp;<input type="submit" name="cmdAddAnother" class="button" value=" Save & Add Another " onClick="return validateSupplierIngredient(document.frmSupplierIngredient);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
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
			<table cellpadding="4" cellspacing="0">
							<tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
        <tr>
		<td nowrap="nowrap">
		<table cellpadding="0" cellspacing="0">
                	<tr>
		<td class="listing-item">Supplier:&nbsp;</td>
                <td>
		<select name="supplierFilter" id="supplierFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>
		<?						  
		foreach($supplierRecords as $sr) {
			$supplierId		=	$sr[0];
			$supplierCode		=	stripSlash($sr[1]);
			$supplierName		=	stripSlash($sr[2]);
			$selected = ($supplierFilterId==$supplierId)?"selected":"";
		?>
               <option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
                <? }?>
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
          <td>&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td></tr></table>
		<?php
			require("template/rbBottom.php");
		?>
						</td>
						</tr>
						</table>
								</td>
							</tr>
	<tr><TD height="10"></TD></tr>			
<? if (!$newRateListCreated) { ?>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%" >
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Ingredient Suppliers </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<TR>
								<TD nowrap="true" colspan="3" align="center">
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierIngredient.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
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
									<td colspan="2" style="padding-left:10px;padding-right:10px;" >
	<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if (sizeof($supplierIngredientRecords)>0) {
		$i	=	0;
	?>
		<thead>
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
      				$nav.= " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredients</th>		
<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
<? }?>
<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
<? }?>
	</tr>
	</thead>
	<tbody>
	<?
		$prevSupplierId		=	0;
		foreach($supplierIngredientRecords as $ssr) {
			$i++;
			$supplierIngredientId	= $ssr[0];
			$supplierId		= $ssr[1];
			$ingId			= $ssr[2];
			$supplierName		= "";
			if ($prevSupplierId!=$supplierId) {
				$supplierName = stripSlash($ssr[3]);				
   			}
			//$stockName		= $ssr[4];
			$getSupplierWiseIngredients = $supplierIngredientObj->getIngreients($supplierId);
			$active=$ssr[5];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierIngredientId;?>" class="chkBox">
			<input type="hidden" name="hidSupplierId_<?=$i;?>" id="hidSupplierId_<?=$i;?>" value="<?=$supplierId;?>">
			<input type="hidden" name="hidIngId_<?=$i;?>" id="hidIngId_<?=$i;?>" value="<?=$ingId;?>">			
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<table id="newspaper-b1-no-style">
				<tr>
				<?
					$numColumn	=	5;
					if (sizeof($getSupplierWiseIngredients)>0) {
						$nextRec	=	0;
						$k=0;
						foreach($getSupplierWiseIngredients as $sR) {
							$j++;
							$ingredientName	=	$sR[2];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$ingredientName?>
				</td>
					<? 
						if($nextRec%$numColumn == 0) {
					?>
					</tr>
					<tr>
					<? 
						}	
						}
					}

					?>
					</tr>
			</table>
		</td>					
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SupplierIngredient.php';"><? } ?></td>
<? }?>

<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierIngredientId;?>,'confirmId');"  >
			<?php }?>
			<? }?>
	</tr>
	<?
		$prevSupplierId=$supplierId;
	}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierIngredient.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
							<TR>
								<TD nowrap="true" colspan="3" align="center">
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierIngredient.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
<!--<tr>
	<td colspan="3" height="5" >
		<table width="900" align="center">
				<TR>
					<TD width="300"></TD>
					<TD width="300">
						<table>
							<TR>
								<TD nowrap>
									<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierIngredientSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierIngredient.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
								</TD>
							</TR>
						</table>
					</TD>
					<TD width="300"><table><TR>	
	<TD><? if($edit==true){?><input type="submit" value=" Bulk Update " class="button"  name="cmdUpdate" onClick="return validateSupplierIngredientBulkUpdate();"><? }?></td></TR></table></TD>
				</TR>
			</table>
	</td>
</tr>-->
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>	
							<?
								include "template/boxBR.php"
							?>		
						</td>
					</tr>
				</table>
		<!-- Form fields end   -->	
		</td>
		</tr>	
	<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">				
		<tr>
			<td height="10"></td>
		</tr>	
<? }?>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<?php 
	if ($iFrameVal=="") { 
?>
	<script language="javascript">
	<!--
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
	//ensureInFrameset(document.frmSupplierIngredient);
	//-->
	</script>
<?php 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	//if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>