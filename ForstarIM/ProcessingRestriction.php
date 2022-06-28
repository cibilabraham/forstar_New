<?
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
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if(!$accesscontrolObj->canAccess())
			{ 
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

	

	# Add a ProcessingRestriction
	
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}

	if( $p["cmdAddProcessingRestriction"]!="" ){

		$selpage	=	$p["selpage"];
		$selActivity	=	$p["selActivity"];
		
		if( $selpage!="" &&  $selActivity)
		{
			$processingRestrictionRecIns = $processingrestrictionObj->addProcessingRestriction($selpage, $selActivity);

			if($processingRestrictionRecIns)
			{
				$sessObj->createSession("displayMsg",$msg_succAddProcessingRestriction);
				$sessObj->createSession("nextPage",$url_afterAddProcessingRestriction.$selection);
			}
			else
			{
				$addMode	=	true;
				$err		=	$msg_failAddProcessingRestriction;
			}
			$processingRestrictionRecIns	=	false;
		}

	}


	# Edit a Processing Activity

	if( $p["editId"]!="" ){
		$editIt				=	$p["editId"];
		$editMode			=	true;
		$processingRestrictionRec	=	$processingrestrictionObj->find($editIt);
		$processingRestrictionId	=	$processingRestrictionRec[0];
		$selPageId			=	$processingRestrictionRec[1];
		$selActivityId			=	$processingRestrictionRec[2];
	}

	if( $p["cmdSaveChange"]!="" ){
		
		$processingRestrictionId	=	$p["hidProcessingActivityId"];
		$selpage	=	$p["selpage"];
		$selActivity	=	$p["selActivity"];
		
		if( $processingRestrictionId!="" && $selpage!="" &&  $selActivity!="")
		{
			$processingRestrictionRecUptd	=	$processingrestrictionObj->updateProcessingRestriction($processingRestrictionId, $selpage, $selActivity);
		}
	
		if($processingRestrictionRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succProcessingRestrictionUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProcessingRestriction.$selection);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failProcessingRestrictionUpdate;
		}
		$processingRestrictionRecUptd	=	false;
	}


	# Delete ProcessingRestriction

	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$processingRestrictionId	=	$p["delId_".$i];

			if( $processingRestrictionId!="" )
			{
				// Need to check the selected Restriction active
				$processingRestrictionRecDel		=	$processingrestrictionObj->deleteProcessingRestriction($processingRestrictionId);	
			}

		}
		if($processingRestrictionRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelProcessingRestriction);
			$sessObj->createSession("nextPage",$url_afterDelProcessingRestriction.$selection);
		}
		else
		{
			$errDel	=	$msg_failDelProcessingRestriction;
		}
		$processingRestrictionRecDel	=	false;

	}

## -------------- Pagination Settings I ------------------
	if ( $p["pageNo"] != "" )	{
		$pageNo=$p["pageNo"];
	}
	else if ( $g["pageNo"] != "" )	{
		$pageNo=$g["pageNo"];
	}
	else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
## ----------------- Pagination Settings I End ------------	

	#List All ProcessingRestrictions
	
	$processingRestrictionRecords	=	$processingrestrictionObj->fetchPagingRecords($offset, $limit);
	$processingRestrictionSize	=	sizeof($processingRestrictionRecords);
	
## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($processingrestrictionObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
## ----------------- Pagination Settings II End ------------

	$processingActivityRecords     = $processingactivityObj->fetchAllRecords();
	$processingActivityPageRecords	= $processingrestrictionObj->getUrlForProcessingRestriction();

	if($editMode) { 
		$heading	=	$label_editProcessingRestriction;
	}else{
		$heading	=	$label_addProcessingRestriction;
	}

	//$help_lnk="help/hlp_ProcessingRestriction.html";

	$ON_LOAD_PRINT_JS	= "libjs/processingrestriction.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProcessingRestriction" action="ProcessingRestriction.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="50%">
	<tr>
		<td height="40" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
	</tr>
	<?
		if( $editMode || $addMode)
			{
	?>
	<tr>
		<td>
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
		<tr>
			<td   bgcolor="white">
			<!-- Form fields start -->
			<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
			<tr>
				<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
				<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
			</tr>
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
					<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingRestriction.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProcessingRestriction(document.frmProcessingRestriction);">
					</td>
				<?} else{?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingRestriction.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAddProcessingRestriction" class="button" value=" Add " onClick="return validateProcessingRestriction(document.frmProcessingRestriction);">
					</td>
				<?}?>
				</tr>
				<input type="hidden" name="hidProcessingActivityId" value="<?=$processingRestrictionId;?>">
				<tr>
					<td class="fieldName" nowrap >Page</td>
					<td>
			<select name="selpage" id="selPage">
                        <option value="" > Select a Page </option>
                        <?
			if( sizeof($processingActivityPageRecords)> 0 )
			{
				foreach( $processingActivityPageRecords as $pap )
					{
						$processingActivityPageId	=	$pap[0];
						$processingActivityPage		=	$pap[1];
	
						$selected		=	"";
						if( $selPageId== $processingActivityPageId)
						{
							$selected	=	" selected ";
						}
																	?>
                        <option value="<?=$processingActivityPageId;?>" <?=$selected;?>><?=$processingActivityPage;?></option>
                        <?
				  	}
			}
			?>
                        </select></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Activity</td>
					<td >
					<select name="selActivity" id="selActivity">
                        <option value="" > Select Activity </option>
                        <?
			if( sizeof($processingActivityRecords)> 0 )
			{
				foreach( $processingActivityRecords as $pa )
					{
						$processingActivityId	=	$pa[0];
						$processingActivity	=	$pa[1];
						$recordActivityId	=	$pa[4];				
						$selected		=	"";
						if( $selActivityId== $processingActivityId)
						{
							$selected	=	" selected ";
						}
																	?>
                        <option value="<?=$processingActivityId;?>" <?=$selected;?>><?=$processingActivity;?></option>
                        <?
				  	}
			}
			?>
                        </select>
					</td>
				</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
					<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingRestriction.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProcessingRestriction(document.frmProcessingRestriction);">
					</td>
				<?} else{?>
					<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProcessingRestriction.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAddProcessingRestriction" class="button" value=" Add " onClick="return validateProcessingRestriction(document.frmProcessingRestriction);">
					</td>
				<?}?>
				</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
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
	# Listing Processing Restriction Starts
	?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
			<tr>
				<td   bgcolor="white">
				<!-- Form fields start -->
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
					<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Processing Restriction </td>
				</tr>
			<tr>
				<td colspan="3" height="10" ></td>
			</tr>
			<tr>	
				<td colspan="3">
					<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processingRestrictionSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessingRestriction.php',700,600);"><? }?></td>
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
										<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($processingRestrictionRecords) > 0 )
												{
													$i	=	0;
											?>
											<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="4" style="padding-right:10px">
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
					$nav.= " <a href=\"ProcessingRestriction.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessingRestriction.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessingRestriction.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  <? }?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="30"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
												<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Screen Name</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Activity</td>
			<? if($edit==true){?>
			<td class="listing-head" width="80"></td>
			<? }?>
		</tr>
		<?
		foreach($processingRestrictionRecords as $pr)
			{
				$i++;
				$processingRestrictionId	=	$pr[0];
				$pageName		=	stripSlash($pr[1]);
				$activityName		=	stripSlash($pr[2]);
		?>
		<tr  bgcolor="WHITE"  >
			<td width="30" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processingRestrictionId;?>" class="chkBox"></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$pageName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$activityName;?></td>
			<? if($edit==true){?>
			<td class="listing-item" width="70" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processingRestrictionId;?>,'editId'); this.form.action='ProcessingRestriction.php';"></td>
			<? }?>
		</tr>
			<?
			}
			?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<? if($maxpage>1){?>
<tr bgcolor="#FFFFFF">
<td colspan="4" style="padding-right:10px">
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
					$nav.= " <a href=\"ProcessingRestriction.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
	if ($pageNo > 1)
		{
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProcessingRestriction.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProcessingRestriction.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  <? }?>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$processingRestrictionSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProcessingRestriction.php',700,600);"><? }?></td>
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
		
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
