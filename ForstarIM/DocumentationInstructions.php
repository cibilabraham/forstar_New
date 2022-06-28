<?php
	require("include/include.php");
	require("lib/DocumentationInstructions_ajax.php");
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	

	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	$printMode=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------
	
	//if ($g["print"]=="y")
	//{
	//	$printMode=true;
	//}
	
	# Add Documentation instructions 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAdd"]!="") {

		$name	=	addSlash(trim($p["name"]));
		$required=(isset($p["required"]))?$p["required"]:"N";
		$createdBY=$userId;
		
		if ($name!="" && $required!="" && $createdBY!="") {
			$documentationInstructionRecIns	= $docInstructionsObj->addDocumentationInstructions($name,$required,$userId);

			if ($documentationInstructionRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddDocumentationInstructions);
				$sessObj->createSession("nextPage",$url_afterAddDocumentationInstructions.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_succAddDocumentationInstructions;
			}
			$documentationInstructionRecIns		=	false;
		}

	}
	
	
	# Edit documentation instructions 
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$docInstructionRec = $docInstructionsObj->find($editId);
		//print_r($docInstructionRec);
		$docInstructionId =	$docInstructionRec[0];
		$name =	stripSlash($docInstructionRec[1]);
		$required =	stripSlash($docInstructionRec[2]);
		$requiredStatus=($required=='Y')?"checked":"";
	}

	
	if ($p["cmdSaveChange"]!="") {
		
		$docInstructionId		=	$p["hidDocumentationInstructionsId"];
		$name	=	addSlash(trim($p["name"]));
		$required=(isset($p["required"]))?$p["required"]:"N";
		$chkEntryExist=false;
		if ($docInstructionId!="" && $name!="" && $required!="") {
			$chkEntryExist = $docInstructionsObj->chkEntryExist($name, $docInstructionId);
			if(!$chkEntryExist)
			{
				$docInstructionRecUptd		=	$docInstructionsObj->updateDocInstructions($docInstructionId,$name, $required);
			}
		}
	
		if ($docInstructionRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDocumentationInstructionsUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDocumentationInstructions.$selection);
		} else {
			$editMode	=	true;
			$editId=$docInstructionId;
			if($chkEntryExist)
			{
				$err=$msg_DocumentationInstructionsUpdate;
			}
			else
			{
				$err		=	$msg_failDocumentationInstructionUpdate;
			}
		}
		$docInstructionRecUptd	=	false;
	}
	
	
	if ($p["cmdConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//print_r($rowCount);
		for ($i=1; $i<=$rowCount; $i++) {
			$docInstructionId	=	$p["confirmId"];


			if ($docInstructionId!="") {
				// Checking the selected documentation instructions is link with any other process
				$docInstructionRecConfirm = $docInstructionsObj->updateconfirmDocumentationInstructions($docInstructionId);
			}

		}
		if ($docInstructionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmDocumentationInstructions);
			$sessObj->createSession("nextPage",$url_afterDelDocumentationInstructions.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmDocumentationInstructions;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$docInstructionId	=	$p["rlconfirmId"];

			if ($docInstructionId!="") {
				#Check any entries exist
				
					$docInstructionRecConfirm = $docInstructionsObj->updaterlconfirmDocumentationInstructions($docInstructionId);
					}
		}
		if ($docInstructionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmDocumentationInstructions);
			$sessObj->createSession("nextPage", $url_afterDelDocumentationInstructions.$selection);
		} else {
			$errReleaseConfirm	= $msg_failRlConfirmDocumentationInstructions;
		}
	}

# Delete Documentation instructions
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$docInstructionId	=	$p["delId_".$i];
			if ($docInstructionId!="") {
				// Checking the selected documentation instructions is link with any other process
				$docInstructionRecInUse = $docInstructionsObj->docInstructionRecInUse($docInstructionId);
				if (!$docInstructionRecInUse) {
					$docInstructionRecDel = $docInstructionsObj->deleteDocInstructions($docInstructionId);	
				}
			}

		}
		if ($docInstructionRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDocumentationInstructions);
			$sessObj->createSession("nextPage",  $url_afterDelDocumentationInstructions.$selection);
		} else {
			$errDel	=	$msg_failDelDocumentationInstructions	;
		}
		$docInstructionRecDel	=	false;

	}
	
	
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All documentation instructions	
	$docInstructionsRecs	=	$docInstructionsObj->fetchAllPagingRecords($offset, $limit);
	$docInstructionsRecSize		=	sizeof($docInstructionsRecs);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($docInstructionsObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	
	if ($editMode) $heading =   $label_editDocumentationInstructions;
	else $heading =   $label_addDocumentationInstructions;

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/DocumentationInstructions.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


<form name="frmDocumentationInstructions" action="DocumentationInstructions.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<?
			}
			
			# Listing Documentation instructions Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
		
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
								
								$bxHeader=$label_pageHeadingDocumentationInstructions;
								include "template/boxTL.php";
							?>
											
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="70%" align="center">	
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
					<tr>
						<td>			
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
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
												<input  type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DocumentationInstructions.php');">&nbsp;&nbsp;
												<input  type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDocumentationInstructions(document.frmDocumentationInstructions);">	
												</td>	
												<?} else{?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DocumentationInstructions.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDocumentationInstructions(document.frmDocumentationInstructions);">
												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidDocumentationInstructionsId" value="<?= $editId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
										  <tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
			<table width="70%" border="0">
				<tr>
				<TD align="center">
					<table>							
							<tr>
							<td class="fieldName" nowrap="nowrap">*Name</td>
							<td class="listing-item">
								<input name="name" type="text" id="name" size="28" value="<?=$name?>" autocomplete="off" onblur="xajax_chkRecExist(document.getElementById('name').value, '<?=$editId;?>');" />
								<? if($editMode){?>
								<input type="hidden" name="id" value="" readonly>
								<?}?>
							</td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap >Required</td>
								<td nowrap="true" align="left">
									<INPUT type="checkbox" name="required" id="required" class="chkBox" <?=$requiredStatus?> value="Y">&nbsp;&nbsp;<span class="fieldName" style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
								</td>
							</tr>						
						</table>
				</TD>
				</tr>
	
                                          </table>
					</td>
					</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>	
												<? if($editMode){?>
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DocumentationInstructions.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDocumentationInstructions(document.frmDocumentationInstructions);">	
												</td>	
												<?} else if($addMode){?>												
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DocumentationInstructions.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDocumentationInstructions(document.frmDocumentationInstructions);">
												</td>
												<?}?>
											</tr>
											<tr>
												<td  height="10" ></td>
											</tr>
										</table>
										<?php
								require("template/rbBottom.php");
								?>
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
			# Listing Documentation instructions Starts
		?>
	</table>
		</td>
			</tr>	
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<? if(!$addMode||!$editMode){?>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<? if(!$printMode){?>
			<tr>
				<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$docInstructionsRecSize?>);"> <? } ?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"> <? } ?>&nbsp; <? if($print==true){?> <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDocumentationInstructions.php',700,600);"><? }?></td>
				
			</tr>
			<?}?>
			</table>
		</td>
		</tr>
		<? } ?>
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
	<? if(!$addMode||!$editMode) { ?>
	<tr>
				<td width="1" ></td>
				<td colspan="2" >
				<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
				<?
					if( sizeof($docInstructionsRecs) > 0 )
						{
							$i	=	0;
				?>
	<thead>
				<? if($maxpage>1) { ?>
				<tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right" class="navRow">
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
      	$nav.= " <a href=\"DocumentationInstructions.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"DocumentationInstructions.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"DocumentationInstructions.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
				<?
					}
				?>
	<tr align="center">
		<? if(!$printMode) { ?>
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th> <? } ?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Required</th>		
		<? if($edit==true && !$printMode){?>
		<th width="45" >&nbsp;</th>
		<? } ?>
		<? if($edit==true && !$printMode){?>
		<th width="45" >&nbsp;</th>
		<? } ?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($docInstructionsRecs as $icmR)
		{
			$i++;
			$docInstructionId = $icmR[0];
			$name	=	stripSlash($icmR[1]);
			$requiredVal	=	stripSlash($icmR[2]);
			$required=($requiredVal=='Y')?"YES":"NO";
			$active= $icmR[3];
			
			
	?>
	<tr>
		<? if(!$printMode) { ?>
		<td width="20" align="center">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$docInstructionId;?>" class="chkBox">
		</td>
		<? } ?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="center"><?=$required;?></td>		
		<? if($edit==true && !$printMode){?>
		  <td class="listing-item" width="45" align="center">
		  <?php if ($active!=1) {?>
		  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?= $docInstructionId;?>,'editId');">
		  <? } ?>
			</td>
		<? } ?>
		<? if($active==false && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"> <input type="submit" value=" <?=$pending;?> " name="cmdConfirm" onClick="assignValue(this.form,<?=$docInstructionId;?>,'confirmId');">
			</td>
		<?}?>
		<? if($active==true && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"> <input type="submit" value=" <?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$docInstructionId;?>,'rlconfirmId');">
			</td>
		<? } ?>
	</tr>
		
	<? } ?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$docInstructionsRecSize?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="rlconfirmId" value="">
	</tbody>
	<? if($maxpage>1){?>
	<tr>
		<td colspan="10" style="padding-right:10px" class="navRow">
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
      	$nav.= " <a href=\"DocumentationInstructions.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"DocumentationInstructions.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"DocumentationInstructions.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div>
		</td>
	</tr>
		<? }?>
		<?
			}
			else
			{
		?>
	<tr><TD align="center"><?=$msgNoRecords;?></TD></tr>
		<?
			}
		?>
		</table>
		</td>
				</tr>
					<?
						}
					?>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<? if(!$addMode||!$editMode){?>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<? if(!$printMode){?>
											<tr>
	
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=docInstructionsRecSize?>);"> <? } ?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"> <? } ?>&nbsp; <? if($print==true){?> <input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDocumentationInstructions.php',700,600);"><? }?></td>
											</tr>
											<? } ?>
										</table>
									</td>
								</tr>
								<? } ?>
								
								
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
			</td>
		</tr>			
		<tr>
			<td height="10">
			<input type="hidden" name="entryExist" id="entryExist" value="" readonly />
			<input type="hidden" name="pageNo" value="<?=$pageNo?>" readonly /> 
			</td>
		</tr>	
</table>
</form>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

