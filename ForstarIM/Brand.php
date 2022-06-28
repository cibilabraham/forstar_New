<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selCriteria = "?selFilter=".$p["selFilter"]."&pageNo=".$p["pageNo"];	

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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------	
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["brand"]) $brandName = $p["brand"];

	# Add
	if ($p["cmdAdd"]!="") {	
		//$selCustomer	=	$p["selCustomer"];
		$brand		=	addSlash(trim($p["brand"]));
		
		$recExist 	= $brandObj->chkRecExist($brand, '');
		
		if ($brand!="" && !$recExist) {
			
			$brandRecIns	=	$brandObj->addBrand($brand);
			
			if ($brandRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddBrand);
				$sessObj->createSession("nextPage",$url_afterAddBrand.$selCriteria);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddBrand;
			}
			$brandRecIns	=	false;
		} else if ($recExist) {
			$addMode		=	true;
			$err			=	"Brand already exist in our database.".$msg_failAddBrand;
		}		
	}
	
	# Edit	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$brandRec		=	$brandObj->find($editId);
		
		$editBrandId		=	$brandRec[0];
		//$customer		=	$brandRec[1];
		$brandName		=	stripSlash($brandRec[1]);
	}


	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$brandId		=	$p["hidBrandId"];
		//$selCustomer		=	$p["selCustomer"];
		$brand			=	addSlash(trim($p["brand"]));
		
		$recExist 	= $brandObj->chkRecExist($brand, $brandId);

		if ($brandId!="" && $brand!="" && !$recExist) {
			$brandRecUptd	=	$brandObj->updateBrand($brandId, $brand);
		}
	
		if($brandRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateBrand);
			$sessObj->createSession("nextPage",$url_afterUpdateBrand.$selCriteria);
		} else {
			$editMode	=	true;
			if ($recExist) $err	=	"Brand already exist in our database.".$msg_failBrandUpdate;
			else $err		=	$msg_failBrandUpdate;
		}
		$brandRecUptd	=	false;
	}
	
	
	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount  = $p["hidRowCount"];
		$recInUse  = false;

		for($i=1; $i<=$rowCount; $i++) {
			$brandId	=	$p["delId_".$i];

			if ($brandId!="") {
				# Check Brand In use
				$brandInUse = $brandObj->brandRecInUse($brandId);

				if (!$brandInUse) $brandRecDel = $brandObj->deleteBrand($brandId);
				else $recInUse  = true;
			}
		}
		if ($brandRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelBrand);
			$sessObj->createSession("nextPage",$url_afterDelBrand.$selCriteria);
		} else {
			if ($recInUse) $errDel = $msg_failDelBrand." Brand is already in use.<br>Please check in Quick Entry List/ Daily Frozen Packing/ Purchase Order";
			else $errDel	=	$msg_failDelBrand;
		}
		$brandRecDel	=	false;
	}
	


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$brandId	=	$p["confirmId"];
			if ($brandId!="") {
				// Checking the selected fish is link with any other process
				$brandRecConfirm = $brandObj->updateBrandconfirm($brandId);
			}

		}
		if ($brandRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmbrand);
			$sessObj->createSession("nextPage",$url_afterDelBrand.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {
			$brandId = $p["confirmId"];
			if ($brandId!="") {
				#Check any entries exist				
					$brandRecConfirm = $brandObj->updateBrandReleaseconfirm($brandId);				
			}
		}
		if ($brandRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmbrand);
			$sessObj->createSession("nextPage",$url_afterDelBrand.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Brand Record
	if ($g["selFilter"]!="") $recordsFilterId		=	$g["selFilter"];
	else $recordsFilterId		=	$p["selFilter"];
	

	#Condition for Select a Customer	
	if($p["existRecordsFilterId"]==0 && $p["selFilter"]!=0){
		$offset = 0;
		$pageNo = 1;
	}
	
	$brandRecords		=	$brandObj->fetchPagingRecords($offset, $limit);		
	$brandRecordSize	=	sizeof($brandRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($brandObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List All Customer Records
	//$customerRecords		=	$customerObj->fetchAllRecords();

	if ($editMode)	$heading = $label_editBrand;
	else $heading	= $label_addBrand;
	
	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/brand.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmBrand" action="Brand.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err;?></td>
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
								$bxHeader="BRANDS";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td colspan="3" align="center">
	<table width="50%" align="center">
	<?php
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="55%">
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
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('Brand.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddBrand(document.frmBrand);">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Brand.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddBrand(document.frmBrand);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidBrandId" value="<?=$editBrandId;?>">
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>
												<td colspan="2" style="padding-left:60px;"> 
										<table width="50%">
                                                <!--<tr>
                                                  <td class="fieldName" nowrap="nowrap">* Customer: </td>
                                                  <td class="listing-item">
												  <select name="selCustomer" id="selCustomer">
												  <option value="">-- Select --</option>
												  
												  <?

													foreach($customerRecords as $cr)
													{
														$customerId		=	$cr[0];
														$customerCode	=	$cr[1];
														$customerName	=	stripSlash($cr[2]);
														$selected 	=	"";
														if($customerId==$customer) $selected = "Selected";
														
											?>
											<option value="<?=$customerId?>" <?=$selected?>><?=$customerName?></option>
											<? }?>
                                                  </select>                                                  </td>
                                                </tr>-->
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap"> * Brand</td>
                                                  <td class="listing-item">
							 
							  <input name="brand" type="text" id="brand" value="<?=$brandName?>"></td>
                                                </tr>
                                              </table></td>
					</tr>
					<tr>
						<td  height="5" ></td>
					</tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Brand.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddBrand(document.frmBrand);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Brand.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddBrand(document.frmBrand);">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
											</tr>
										</table>
									</td>
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
			# Listing Grade Starts
		?>
	</table>
								</td>
							</tr>	
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp; BRANDS </td>
								    <td background="images/heading_bg.gif" class="pageName" > -->
									<!--<table width="200" align="right" cellpadding="0" cellspacing="0">	
											<tr>
											<td align="right" nowrap="nowrap" class="listing-item"> Select a Customer:  </td>
											<td align="right" nowrap="nowrap">&nbsp;
												<select name="selFilter" onChange="this.form.submit();">
													<option value="0">--All--</option>
													<?
													foreach($customerRecords as $cr)
													{
														$customerId		=	$cr[0];
														$customerCode	=	$cr[1];
														$customerName	=	stripSlash($cr[2]);
														$selected 	=	"";
														if($customerId==$recordsFilterId) $selected = "Selected";
														
													?>
													<option value="<?=$customerId;?>" <?=$selected;?> ><?=$customerName;?> </option>
													<?
													}
													?>
											  </select>											</td>
											<td width="4">&nbsp;</td>
										</tr>
										
								  </table>-->
								<!--</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$brandRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintBrand.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
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
									<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?php
												if (sizeof($brandRecords) > 0 ) {
													$i = 0;
											?>
		<thead>
		<? if($maxpage>1){?>
		<tr>
			<td colspan="3" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<!--<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Customer Name </th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Brand</th>
		<? if($edit==true){?>
			<th class="listing-head" width="50">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
                        <th class="listing-head">&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		foreach ($brandRecords as $br) {
			$i++;
			$brandId	=	$br[0];
			//$customerName	=	stripSlash($br[1]);
			//$customerName	=	$customerObj->findCustomer($br[1]);
			$brandName	=	stripSlash($br[1]);
			//$indainAgent	=	stripSlash($br[3]);
			$active=$br[2];
			$existingcount=$br[3];
			
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20" align="center">
		<?php 
		if($existingcount){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$brandId;?>" class="chkBox"></td>
		<?php 
		}
		?>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$customerName;?></td>-->
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$brandName;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="50" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$brandId;?>,'editId');">
		<? } ?>
		</td>
		 <? }?>
		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$brandId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$brandId;?>,'confirmId');" >
			<?php 
			//}
			}?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
	<tr>
		<td colspan="3" style="padding-right:10px" class="navRow">
		<div align="right">
	  	<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"Brand.php?selFilter=$recordsFilterId&pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr><? }?>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
		</tbody>
		</table>
		<input type="hidden" name="existRecordsFilterId" value="<?=$recordsFilterId?>">
		</td>
	</tr>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$brandRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintBrand.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>