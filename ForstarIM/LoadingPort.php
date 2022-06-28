<?php
	require("include/include.php");
	
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
	
	# Add loading port
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAdd"]!="") {
		$name	=	addSlash(trim($p["name"]));
		$createdBY=$userId;
		$chkEntryExist=false;
		if ($name!="" && $createdBY!="") {
		$chkEntryExist = $loadingPortObj->chkEntryExist($name, $userId);
		//print_r($chkEntryExist);
			if(!$chkEntryExist)
				{
				$loadingPortRecIns	= $loadingPortObj->addPortOfLoading($name,$userId);
				}
			}
			
			if ($loadingPortRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddLoadingPort);
				$sessObj->createSession("nextPage",$url_afterAddLoadingPort.$selection);
			} else {
				$addMode	=	true;
				if($chkEntryExist)
						{
							$err=$msg_AddLoadingPortExists;
						}
				else
						{
							$err		=	$msg_failLoadingPortUpdate;
						}
				
				//$err		=	$  $msg_succAddLoadingPort;
			}
			$loadingPortRecIns		=	false;
		}

	
	
	# Edit loading port
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$loadingPortRec = $loadingPortObj->find($editId);
		//print_r($loadingPortRec);
		$loadingPortId =	$loadingPortRec[0];
		$name =	stripSlash($loadingPortRec[1]);
	}

	
	if ($p["cmdSaveChange"]!="") {
	
		$loadingPortId		=	$p["hidLoadingPortId"];
		$name	=	addSlash(trim($p["name"]));
		$chkEntryExist=false;
		if ($loadingPortId!="" && $name!="") {
			$chkEntryExist = $loadingPortObj->chkEntryExist($name, $loadingPortId);
			if(!$chkEntryExist)
			{
				$loadingPortRecUptd		=	$loadingPortObj->updateLoadingPort($loadingPortId,$name);
			}
		}
	
		if ($loadingPortRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succLoadingPortUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateLoadingPort.$selection);
		} else {
			$editMode	=	true;
			$editId=$loadingPortId;
			if($chkEntryExist)
			{
				$err=$msg_LoadingPortUpdate;
			}
			else
			{
				$err		=	$msg_failLoadingPortUpdate;
			}
		}
		$loadingPortRecUptd	=	false;
	}
	
	
	if ($p["cmdConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//print_r($rowCount);
		for ($i=1; $i<=$rowCount; $i++) {
			$loadingPortId	=	$p["confirmId"];


			if ($loadingPortId!="") {
				// Checking the selected port of loading is link with any other process
				$loadingPortRecConfirm = $loadingPortObj->updateconfirmLoadingPort($loadingPortId);
			}

		}
		if ($loadingPortRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmLoadingPort);
			$sessObj->createSession("nextPage",$url_afterConfirmLoadingPort.$selection);
		} else {
			$errConfirm	=	$  $msg_failConfirmLoadingPort;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$loadingPortId	=	$p["rlconfirmId"];

			if ($loadingPortId!="") {
				#Check any entries exist
				
					$loadingPortRecConfirm = $loadingPortObj->updaterlconfirmLoadingPort($loadingPortId);
					}
		}
		if ($loadingPortRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmLoadingPort);
			$sessObj->createSession("nextPage", $url_afterReleaseConfirmLoadingPort.$selection);
		} else {
			$errReleaseConfirm	= $msg_failRlConfirmLoadingPort;
		}
	}

# Delete port of loading
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$recExists= false;
		for ($i=1; $i<=$rowCount; $i++) {
			$loadingPortId	=	$p["delId_".$i];
			if ($loadingPortId!="") {
				// Checking the selected port of loading is link with any other process
				$loadingPortRecInUse = $loadingPortObj->loadingPortRecInUse($loadingPortId);
				if (!$loadingPortRecInUse) {
					$loadingPortRecDel = $loadingPortObj->deleteLoadingPort($loadingPortId);	
				}
				else
				{
					$recExists=true;
				}
			}
		}
		if ($loadingPortRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelLoadingPort);
			$sessObj->createSession("nextPage",  $url_afterDelLoadingPort.$selection);
			}
		else {
					if($recExists==true)
						{
							$errDel	=	$msg_delLoadingPortExists;
						}
						else
						{
							$errDel	=	$msg_failDelLoadingPort	;
						}
			}
		$loadingPortRecDel	=	false;

	}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All port of loading	
	$LoadingPortRecs	=	$loadingPortObj->fetchAllPagingRecords($offset, $limit);
	$LoadingPortRecSize		=	sizeof($LoadingPortRecs);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($loadingPortObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	
	if ($editMode) $heading =   $label_editLoadingPort;
	else $heading =   $label_addLoadingPort;

	
	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/LoadingPort.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>

<form name="frmLoadingPort" action="LoadingPort.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<?if($err){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err ?></td>
		</tr>
		<? }?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>							
							
								<?php	
								
									$bxHeader=$label_pageHeadingLoadingPort;
									include "template/boxTL.php";
								?>				
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="50%" align="center">

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
												<input  type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LoadingPort.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateLoadingPort(document.frmLoadingPort);">	
												</td>	
												
												<?} else if($addMode){?>	
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LoadingPort.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateLoadingPort(document.frmLoadingPort);">
												</td>
												<? }?>
											</tr>
											<input type="hidden" name="hidLoadingPortId" value="<?=$editId?>">
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
						<TR>
						<TD valign="top">
						<table>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Name</td>
							<td class="listing-item">
								<input name="name" type="text" id="name" size="28" value="<?=$name?>">
								<?if($editMode){?>
								<input type="hidden" name="<?=$loadingPortId?>" value="" readonly>
							    <? }?>
							</td>
							</tr>							
						</table>
						</TD>						
						</TR>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LoadingPort.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateLoadingPort(document.frmLoadingPort);">	
												</td>	
												<? } else if($addMode){?>
												
												<td align="center">
												<input  type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LoadingPort.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateLoadingPort(document.frmLoadingPort);">
												</td>
												<? } ?>
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
	<?if(!$addMode || !$editMode){?>
	<tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<? if(!$printMode){?>
			<tr>
				<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$LoadingPortRecSize?>);"><? } ?>&nbsp; <? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp; <? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintLoadingPort.php?print=y',700,600);"><? } ?></td>
			</tr>
			<? } ?>
			
			</table>
		</td>
		</tr>
		<? } ?>
		
		
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	<?if($errDel){?>
	<tr>
		<td colspan="3" height="15" align="center" class="err1"><?=$errDel?></td>
	</tr>
	<? } ?>
	<?if(!$addMode || !$editMode){?>
	<tr>
				<td width="1" ></td>
				<td colspan="2" >
				<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
				<?
					if( sizeof($LoadingPortRecs) > 0 )
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
      	$nav.= " <a href=\"LoadingPort.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"LoadingPort.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"LoadingPort.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<? if(!$printMode){?>
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="chkAll(this.form,'delId_'); " class="chkBox"></th>
		<? } ?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
		<? if($edit==true  && !$printMode){?>
		<th width="45" >&nbsp;</th>
		<? }?>
		<? if($edit==true  && !$printMode){?>
		<th width="45">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach($LoadingPortRecs as $icmR)
	{
			$i++;
			$loadingPortId = $icmR[0];
			$name	=	stripSlash($icmR[1]);
			$active= $icmR[2];
	?>
	<tr>
		<? if(!$printMode){?>
		<td width="20" align="center">
			<input type="checkbox"  name="delId_<?=$i;?>" id="delId_<?=$i;?>"  value="<?=$loadingPortId;?>" class="chkBox">
		</td>
		<? }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name?></td>		
		<?if($edit==true && !$printMode){?>
		  <td class="listing-item" width="45" align="center" >
		   <?php if ($active!=1) {?>
		   <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$loadingPortId?>,'editId');">
		   <? } ?>
			</td>
		<? }?> 
		<? if($active==false && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"><input type="submit" value=" <?=$pending;?> "  name="cmdConfirm" 
		 onClick="assignValue(this.form,<?=$loadingPortId?>,'confirmId');">
		</td>
		<? } ?>
		<? if($active==true && !$printMode ) {?>
		 <td class="listing-item" width="45" align="center"><input type="submit" value=" <?=$ReleaseConfirm;?> " name="btnRlConfirm"
		 onClick="assignValue(this.form,<?=$loadingPortId?>,'rlconfirmId');">
			</td>
		<? } ?>
	</tr>
	<? } ?>

	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$LoadingPortRecSize?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="confirmId" value="">
	<input type="hidden" name="rlconfirmId" value="">
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
      	$nav.= " <a href=\"LoadingPort.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"LoadingPort.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"LoadingPort.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
							
								
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?if(!$addMode || !$editMode){?>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<?if(!$printMode)?>
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$LoadingPortRecSize?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintLoadingPort.php?print=y',700,600);"><? }?></td>
											</tr>
											<? }?>
										</table>
									</td>
								</tr>
								<? }?>
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




