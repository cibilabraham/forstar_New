<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$mode		= $g["mode"];	

	$selection 	=	"?pageNo=".$p["pageNo"];

	#------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
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
	#----------------------------------------------------------

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") {
		$addMode = true;
	}

	if ($p["cmdCancel"]!="") {
		$addMode   =  false;
		$editMode  =  false;
	}

	#Insert a Record
	if ($p["cmdAdd"]!="") {	
		$rateListName	=	addSlash(trim($p["rateListName"]));		
		$startDate	= 	mysqlDateFormat(trim($p["startDate"]));
		$copyRateList	=	$p["copyRateList"];
		
		$ingCurrentRateListId	= $p["hidCurrentRateListId"]; 

		if ($rateListName!="" && $p["startDate"]!="") {	
				$ingredientRateListRecIns = $ingredientRateListObj->addIngredientRateList($rateListName, $startDate, $copyRateList, $ingCurrentRateListId, $userId);
				
				if ($ingredientRateListRecIns) {
					$sessObj->createSession("displayMsg",$msg_succAddIngredientRateList);
					$sessObj->createSession("nextPage",$url_afterAddIngredientRateList.$selection);
				} else {
					$addMode		=	true;
					$err			=	$msg_failAddIngredientRateList;
				}
				$ingredientRateListRecIns	=	false;
		}
	}


	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$ingredientRateListObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);
		//$array		=	explode("-",$rateListRec[2]);
		//$startDate		=	$array[2]."/".$array[1]."/".$array[0];	
		$startDate		=	dateFormat($rateListRec[2]);
		# Check Rate List used any where
		$isRateListUsed = $ingredientRateListObj->checkRateListUse($editRateListId);

		$fReadOnly	= "";
		$fStyle		= "";
		if ($isRateListUsed!="") {
			$fReadOnly = "readOnly";
			$fStyle = "style='border:none'";
		}	
	}
	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$ingredientRateListId	=	$p["hidRateListId"];
		
		$rateListName		=	addSlash(trim($p["rateListName"]));
		$Date1			=	explode("/",$p["startDate"]);
		$startDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		
		if ($ingredientRateListId!="" && $rateListName!="") {
			$ingredientRateListRecUptd = $ingredientRateListObj->updateIngredientRateList($rateListName, $startDate, $ingredientRateListId);
		}
	
		if ($ingredientRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateIngredientRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateIngredientRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateIngredientRateList;
		}
		$ingredientRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientRateListId	=	$p["delId_".$i];
			
			$isRateListUsed = $ingredientRateListObj->checkRateListUse($ingredientRateListId);
			
			if ($ingredientRateListId!="" && !$isRateListUsed) {
				$processRateListRecDel = $ingredientRateListObj->deleteIngredientRateList($ingredientRateListId);
			}
		}
		if ($processRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngredientRateList);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRateList.$selection);
		} else {
			$errDel	=	$msg_failDelIngredientRateList;
		}
		$processRateListRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientRateListId	=	$p["confirmId"];


			if ($ingredientRateListId!="") {
				// Checking the selected fish is link with any other process
				$rateListRecConfirm = $ingredientRateListObj->updateRateListconfirm($ingredientRateListId);
			}

		}
		if ($rateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmRateList);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{

			$ingredientRateListId= $p["confirmId"];

			if ($ingredientRateListId!="") {
				#Check any entries exist
				
					$rateListRecConfirm = $ingredientRateListObj->updateRateListReleaseconfirm($ingredientRateListId);
				
			}
		}
		if ($rateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmRateList);
			$sessObj->createSession("nextPage",$url_afterDelIngredientRateList.$selection);
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

	#List All Records
	$ingredientRateListRecords	=	$ingredientRateListObj->fetchAllPagingRecords($offset, $limit);
	$ingredientRateListRecordSize	=	sizeof($ingredientRateListRecords);

	## -------------- Pagination Settings II -------------------
	$ingRateListRecs = $ingredientRateListObj->fetchAllRecords();
	$numrows	=  sizeof($ingRateListRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Find the current rate List Id
	$currentRateListId	= $ingredientRateListObj->latestRateList();

	if ($editMode)	$heading = $label_editIngredientRateList;
	else		$heading = $label_addIngredientRateList;
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientRateList.js"; 
		
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmIngredientRateList" action="IngredientRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">	
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
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
					$bxHeader="Ingredient Rate List Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientRateListMaster(document.frmIngredientRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientRateListMaster(document.frmIngredientRateList);">												</td>

												<?}?>
											</tr>
						<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Name </td>
												<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Start Date </td>
												<td><INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$fReadOnly?> <?=$fStyle?>></td>
											</tr>
											<? if($addMode==true){?>
											<tr>
												<td class="fieldName" nowrap>Copy From  </td>
			<td>
		      <select name="copyRateList" id="copyRateList" title="Click here if you want to copy all the Exisiting Rate list">
                      <option value="">-- Select --</option>
                      <?
			foreach($ingRateListRecs as $prl) {
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		= 	dateFormat($prl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected =  ($currentRateListId==$ingredientRateListId)?"Selected":"";
				?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                    </select></td></tr>
									<? }?>		
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientRateListMaster(document.frmIngredientRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientRateListMaster(document.frmIngredientRateList);">												</td>

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
				<!-- Form fields end   -->			</td>
		</tr>	
		<?php
			}			
			# Listing Grade Starts
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Rate List Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintIngredientRateList.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
			<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
                      <?
				if ( sizeof($ingredientRateListRecords) > 0 ) {
					$i	=	0;
			?>
			<thead>
		<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"IngredientRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                      <tr> 
                        <th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<? if($edit==true){?>
                        <th class="listing-head" width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
                      </tr>
	</thead>
	<tbody>
                      <?
			foreach ($ingredientRateListRecords as $prl) {
				$i++;
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		= 	dateFormat($prl[2]);
				$active=$prl[3];
			?>
                      <tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
                        <td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingredientRateListId;?>" class="chkBox"></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;" align="center"><?=$startDate?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientRateListId;?>,'editId'); this.form.action='IngredientRateList.php';"><? } ?></td>
			<? }?>


<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value="  <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$ingredientRateListId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$ingredientRateListId;?>,'confirmId');"  >
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
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"IngredientRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"IngredientRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"IngredientRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientRateListRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintIngredientRateList.php',700,600);"><? }?></td></tr></table></td></tr>
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
<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
		    <tr>
		      <td height="10"></td>
      </tr>
	    <!--<tr><td height="10" align="center"><a href="Processes.php" class="link1"> Back to Pre-Process Rate Master</a></td></tr>-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
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
	//ensureInFrameset(document.frmIngredientRateList);
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