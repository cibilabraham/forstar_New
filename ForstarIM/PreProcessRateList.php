<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$mode			=	$g["mode"];
	$dateS			=	explode("/",$p["selectDate"]);
	$selectDate		=	$dateS[2]."-".$dateS[1]."-".$dateS[0];
	$selection 	=	"?pageNo=".$p["pageNo"];

	#------------  Checking Access Control Level  ----------------
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
	
	if ($p["startDate"]) $startDate = $p["startDate"];
	if ($p["copyRateList"]) $selRateList = $p["copyRateList"];
	else $selRateList = $processratelistObj->latestRateList();

	# Get daily Pre Process Records for updation
	//$seldate	= "2008-09-02";
	//$prevDate	= "2008-09-01";
	//$updateRecords	=	$processratelistObj->getPreProcessRecords($seldate, $prevDate);	
// 	echo $checkVaildDateEntry	= $processratelistObj->chkValidDateEntry($seldate,$cId);

	#Insert a Record
	if ($p["cmdAdd"]!="") {
	
		$rateListName	=	addSlash(trim($p["rateListName"]));
		$seldate	= mysqlDateFormat($p["startDate"]);	
		$copyRateList	=	$p["copyRateList"];
		# Check Valid Date Selected
		$vaildDateEntry	= $processratelistObj->chkValidDateEntry($seldate, $cId);	

		if ($rateListName!="" && $p["startDate"]!="" && $vaildDateEntry) {	
			$preProcessRateListRecIns = $processratelistObj->addProcessRateList($rateListName, $seldate, $copyRateList);
				
			if ($preProcessRateListRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddPreProcessRateList);
				$sessObj->createSession("nextPage",$url_afterAddPreProcessRateList.$selection);
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddPreProcessRateList;
			}
			$preProcessRateListRecIns	=	false;
		}
		if (!$vaildDateEntry) $err =	" Please check the start date";
		else $err	=	$msg_failAddPreProcessRateList;
		if ($err) $addMode	=	true;
	}
	
	# Edit 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$rateListRec		=	$processratelistObj->find($editId);
		
		$editRateListId		=	$rateListRec[0];
		$rateListName		=	stripSlash($rateListRec[1]);
		$array			=	explode("-",$rateListRec[2]);
		$startDate		=	$array[2]."/".$array[1]."/".$array[0];		
	}
	
	
	#Update a Record
	if ($p["cmdSaveChange"]!="") {		
		$processRateListId	=	$p["hidRateListId"];		
		$rateListName		=	addSlash(trim($p["rateListName"]));
		$seldate	= mysqlDateFormat($p["startDate"]);
		$hidStartDate		=	mysqlDateFormat($p["hidStartDate"]);

		# Check Valid Date Selected
		$vaildDateEntry	= $processratelistObj->chkValidDateEntry($seldate, $processRateListId);		
		
		if ($processRateListId!="" && $rateListName!="" && $vaildDateEntry) {
			$processRateListRecUptd	=	$processratelistObj->updateProcessRateList($rateListName, $seldate, $processRateListId, $hidStartDate);
		}
	
		if ($processRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdatePreProcessRateList);
			$sessObj->createSession("nextPage",$url_afterUpdatePreProcessRateList.$selection);
		} else {
			$editMode	=	true;
			if (!$vaildDateEntry) $err = " Please check the start date";
			else $err		=	$msg_failUpdatePreProcessRateList;
		}
		$processRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processRateListId	=	$p["delId_".$i];
			
			$isRateListUsed = $processratelistObj->checkRateListUse($processRateListId);
			
			if ($processRateListId!="" && !$isRateListUsed) {
				$processRateListRecDel = $processratelistObj->deleteProcessRateList($processRateListId);
			}
		}
		if ($processRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPreProcessRateList);
			$sessObj->createSession("nextPage",$url_afterDelPreProcessRateList.$selection);
		} else {
			$errDel	=	$msg_failDelPreProcessRateList;
		}
		$processRateListRecDel	=	false;
	}
	
if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$processRateListId	=	$p["confirmId"];


			if ($processRateListId!="") {
				// Checking the selected fish is link with any other process
				$processRateListRecConfirm = $processratelistObj->updateProcessRateListconfirm($processRateListId);
			}

		}
		if ($processRateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmprocessRateList);
			$sessObj->createSession("nextPage",$url_afterDelPreProcessRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$processRateListId = $p["confirmId"];

			if ($processRateListId!="") {
				#Check any entries exist
				
					$processRateListRecConfirm = $processratelistObj->updateProcessRateListReleaseconfirm($processRateListId);
				
			}
		}
		if ($processRateListRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmprocessRateList);
			$sessObj->createSession("nextPage",$url_afterDelPreProcessRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}



	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="")		$pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "")	$pageNo=$g["pageNo"];
	else				$pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	#List All Category
	$processRateListRecords			=	$processratelistObj->fetchAllPagingRecords($offset, $limit);
	$processRateListRecordsactive=	$processratelistObj->fetchAllPagingRecordsRatelistActive($offset, $limit);
	$processRateListRecordsSize		=	sizeof($processRateListRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($processratelistObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------	

	if ($editMode)	$heading	=	$label_editPreProcessRateList;
	else 		$heading	=	$label_addPreProcessRateList;	
	
	//$help_lnk="help/hlp_Category.html";
	
	$ON_LOAD_PRINT_JS	= "libjs/processratelist.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPreProcessRateList" action="PreProcessRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<!--<tr>
	  <td height="10" align="center">&nbsp;</td>
	  </tr>-->
	<tr>
	  <td height="10" align="center"><a href="Processes.php" class="link1"> Pre-Process Rate Master </a></td>
	</tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
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
								$bxHeader="Pre-Process Rate List Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Pre-Process Rate List Master  </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddProcessRateList(document.frmPreProcessRateList);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddProcessRateList(document.frmPreProcessRateList);">												</td>
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
			<td>
				<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8">
	<input type="hidden" name="hidStartDate" id="hidStartDate" value="<?=$startDate?>"></td>
											</tr>
											<? if($addMode==true){?>
											<tr>
												<td class="fieldName" nowrap >*Copy From  </td>
												<td>
						<select name="copyRateList" id="copyRateList">
                      <option value="">-- Select --</option>
                      <?
						foreach($processRateListRecordsactive as $prl)
							{
								$processRateListId	=	$prl[0];
								$rateListName		=	stripSlash($prl[1]);					
								$startDate		=	dateFormat($prl[2]);
								$displayRateList = $rateListName."&nbsp;(".$startDate.")";
								$selected = "";
								if($selRateList==$processRateListId) $selected = "Selected";
					?>
                      <option value="<?=$processRateListId?>" <?=$selected?>><?=$displayRateList?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddProcessRateList(document.frmPreProcessRateList);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PreProcessRateList.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddProcessRateList(document.frmPreProcessRateList);">												</td>

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
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php
		}
	?>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processRateListRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPreProcessRateList.php',700,600);"><? }?></td>
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
									<td colspan="2" >
			<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
                      		<?
					if( sizeof($processRateListRecords) > 0 ) {
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
      				$nav.= " <a href=\"PreProcessRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PreProcessRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PreProcessRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                        <th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<? if($edit==true){?>
                        	<th width="45">&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
                      </tr>
			</thead>
			<tbody>
                      <?
			foreach ($processRateListRecords as $prl) {
				$i++;
				$processRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		=	dateFormat($prl[2]);
				$active=$prl[3];
				$existingrecords=$prl[4];
			?>
                      <tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> > 
                        <td width="20" align="center">
						<?php
						if ($existingrecords==0) {?>
						<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processRateListId;?>" class="chkBox"></td>
                        <?php 
						}
						?>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			<? if($edit==true){?>
                        <td class="listing-item" align="center">
						 <?php if ($active!=1) {?>
						<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processRateListId;?>,'editId'); this.form.action='PreProcessRateList.php';">
						<? } ?>
						</td>
			<? }?>

			 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?>  " name="btnConfirm" onClick="assignValue(this.form,<?=$processRateListId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$processRateListId;?>,'confirmId');" >
			<?php
			//} 
			}?>
			<? }?>
			
			
			
			</td>
                      </tr>
                      <?
				}
			?>			
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
					  <input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"PreProcessRateList.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PreProcessRateList.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PreProcessRateList.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
                        <td colspan="4"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
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
												<td nowrap><? if($del==true){?>
												<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processRateListRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPreProcessRateList.php',700,600);"><? }?></td></tr></table></td></tr>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		    <tr>
		      <td height="10"></td>
      </tr>
	    <tr><td height="10" align="center"><a href="Processes.php" class="link1"> Pre-Process Rate Master</a></td></tr>
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
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>