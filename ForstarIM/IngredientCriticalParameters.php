<?php

	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;

	$selection 	=	"?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	
	
	
	
	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		//Rekha commented here 
		header("Location: ErrorPageIFrame.php");
		
		//header("Location: ErrorPage.php");
		//die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start
	if ($p["cmdAddNew"]!="") $addMode = true;

	# Add a Category
	if ($p["cmdAddCriticalParameters"]!="" ) 
	{
		$name		= addSlash(trim($p["name"]));
		$descr		= addSlash(trim($p["description"]));
		$entryType	= trim($p["entryType"]);
		
		if ($name!="") 
		{
			//echo "hii";
			$ingCriticalParametersRecIns = $ingredientCriticalParametersObj->addIngredientCriticalParameters($name, $descr, $entryType);
			if ($ingCriticalParametersRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddIngCriticalParameters);
				$sessObj->createSession("nextPage",$url_afterAddIngCriticalParameters.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddIngCriticalParameters;
			}
			$ingCriticalParametersRecIns		=	false;
		}
	}


	# Edit Category 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$ingMainCategoryRec	=	$ingredientCriticalParametersObj->find($editId);
		$ingMainCategoryId	=	$ingMainCategoryRec[0];
		$name	=	stripSlash($ingMainCategoryRec[1]);
		$descr		=	stripSlash($ingMainCategoryRec[2]);
		$entryType		=	stripSlash($ingMainCategoryRec[3]);
		($entryType=="radio")? $selRadio="selected":$selRadio="";
		($entryType=="checkbox")? $selChk="selected":$selChk="";
		($entryType=="text")? $selText="selected":$selText="";
	}

	#Update a Category
	if ($p["cmdSaveChange"]!="" ) {
		
		$ingCriticalParameterId	=	$p["hidCriticalParameterId"];
		$name			=	addSlash(trim($p["name"]));
		$descr			=	addSlash(trim($p["description"]));
		$entryType	= trim($p["entryType"]);
		
		if ($ingCriticalParameterId!="" && $name!="") {
			$ingCriticalParameterRecUptd = $ingredientCriticalParametersObj-> updateCriticalParameters($ingCriticalParameterId, $name, $descr,$entryType);
		}
	
		if ($ingCriticalParameterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIngCriticalParametersUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngCriticalParameters.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIngCriticalParametersUpdate;
		}
		$ingCriticalParameterRecUptd	=	false;
	}


	# Delete Category
	if ( $p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingCriticalParameterId	=	$p["delId_".$i];

			if ($ingCriticalParameterId!="") 
			{
				$ingCriticalParameterRecDel=$ingredientCriticalParametersObj->deleteCriticalParameter($ingCriticalParameterId);
			}
		}
		if ($ingCriticalParameterRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelIngCriticalParameters);
			$sessObj->createSession("nextPage", $url_afterDelIngCriticalParameters.$selection);
		} else {
			$errDel	=	$msg_failIngCriticalParametersUpdate;
		}
		$ingCriticalParameterRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingCriticalParameterId	=	$p["confirmId"];


			if ($ingCriticalParameterId!="") {
				// Checking the selected fish is link with any other process
				$ingCriticalParameterRecConfirm = $ingredientCriticalParametersObj->updateCriticalParameterConfirm($ingCriticalParameterId);
			}

		}
		if ($ingCriticalParameterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmingCriticalParameter);
			$sessObj->createSession("nextPage",$url_afterDelIngCriticalParameters.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$ingCriticalParameterId= $p["confirmId"];

			if ($ingCriticalParameterId!="") {
				#Check any entries exist
				
					$ingCriticalParameterRecConfirm = $ingredientCriticalParametersObj->updateCriticalParameterReleaseconfirm($ingCriticalParameterId);
				
			}
		}
		if ($ingCriticalParameterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmingCriticalParameter);
			$sessObj->createSession("nextPage",$url_afterDelIngCriticalParameters.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Category ;	
	$ingCriticalParametersRecords	=	$ingredientCriticalParametersObj->fetchAllPagingRecords($offset, $limit);
	//echo($ingCriticalParametersRecords);
	
	$categorySize		=	sizeof($ingCriticalParametersRecords);

	//echo($categorySize);
	//exit;
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($ingredientCriticalParametersObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientCriticalParameters.js"; 

	if ($editMode)	$heading =$label_editIngCriticalParameters;
	else 		$heading =  $label_addIngCriticalParameters;
	
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>

	<form name="frmIngredientCriticalParameters" action="IngredientCriticalParameters.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" > 	 
		<!--<tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1">Ingredient Sub-Category</a>&nbsp;&nbsp;<a href="IngredientsMaster.php" class="link1">Ingredient Master</a></td></tr>-->
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
		</tr>
		<?}?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>	
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?	
					$bxHeader="Ingredient Critical Parameters";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%" >
					<tr>
						<td>
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('IngredientCriticalParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngCriticalParameters(document.frmIngredientCriticalParameters);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('IngredientCriticalParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCriticalParameters" class="button" value=" Add " onClick="return validateIngCriticalParameters(document.frmIngredientCriticalParameters);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCriticalParameterId" value="<?=$ingMainCategoryId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Name</td>
												<td><INPUT TYPE="text" NAME="name" size="15" value="<?=$name;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >Description</td>
												<td ><textarea name="description"><?=$descr;?></textarea></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Entry Type</td>
												<td>
												<select name="entryType" id="entryType">
													<option value="">--Select--</option>
													<option value="radio" <?=$selRadio?>>Radio Button</option>
													<option value="checkbox" <?=$selChk?>>Check Box</option>
													<option value="text" <?=$selText?>>Input Field</option>
												</select>
												</td>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientCriticalParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngCriticalParameters(document.frmIngredientCriticalParameters);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientCriticalParameters.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCriticalParameters" class="button" value=" Add " onClick="return validateIngCriticalParameters(document.frmIngredientCriticalParameters);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
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
			
			# Listing Category Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>							
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Category </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
								<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
								<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientCriticalParameters.php',700,600);"><? }?></td>
		</tr>
		</table></td>
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
		<?php
			if (sizeof($ingCriticalParametersRecords)>0) {
				$i	= 0;
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
      				$nav.= " <a href=\"IngredientCriticalParameters.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientCriticalParameters.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientCriticalParameters.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Entry Type</th>
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
		foreach ($ingCriticalParametersRecords as $cr) {
			$i++;
			$ingMainCategoryId	= $cr[0];
			$ingCategoryName	= stripSlash($cr[1]);
			$categoryDescr	= stripSlash($cr[2]);
			$entryType	= stripSlash($cr[3]);
			$active=$cr[4];
		?>
		<tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingMainCategoryId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$ingCategoryName;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$categoryDescr?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$entryType?></td>
		<? if($edit==true){?><td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingMainCategoryId;?>,'editId'); this.form.action='IngredientCriticalParameters.php';"><? } ?></td>
		<? }?>


		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingMainCategoryId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm"  onClick="assignValue(this.form,<?=$ingMainCategoryId;?>,'confirmId');" >
			<?php }?>
			<? }?>
		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"IngredientCriticalParameters.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientCriticalParameters.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientCriticalParameters.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintIngredientCriticalParameters.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
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
		
		<tr>
			<td height="10"></td>
		</tr>
		<!--<tr>
			<td height="10" align="center"><a href="IngredientCategory.php" class="link1">Ingredient Sub-Category</a>&nbsp;&nbsp;<a href="IngredientsMaster.php" class="link1">Ingredient Master</a></td></tr>-->
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
	//ensureInFrameset(document.frmIngredientMainCategory);
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