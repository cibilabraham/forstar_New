<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";	
	$fishName		=	"";
	$fishCode		=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	
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

	# Add Fish Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAddFish"]!="") {

		$fishCode	=	addSlash(trim($p["fishCode"]));
		$fishName	=	addSlash(trim($p["fishName"]));
		$categoryId	=	$p["fishCategory"];
		$sourceId	=   $p["fishSource"];
		if ($fishCode!="" && $fishName!="" && $categoryId!="") {
			$fishRecIns	=	$fishmasterObj->addFish($fishCode,$fishName,$categoryId,$sourceId);

			if ($fishRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFish);
				$sessObj->createSession("nextPage",$url_afterAddFish.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddFish;
			}
			$fishRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fishId	=	$p["confirmId"];


			if ($fishId!="") {
				// Checking the selected fish is link with any other process
				$fishRecConfirm = $fishmasterObj->updateFishconfirm($fishId);
			}

		}
		if ($fishRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmFish);
			$sessObj->createSession("nextPage",$url_afterDelFish.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$fishId	=	$p["confirmId"];

			if ($fishId!="") {
				#Check any entries exist
				
					$fishRecConfirm = $fishmasterObj->updateFishReleaseconfirm($fishId);
				
			}
		}
		if ($fishRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmFish);
			$sessObj->createSession("nextPage",$url_afterDelFish.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	
	# Edit Fish 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$fishRec		=	$fishmasterObj->find($editIt);
		$fishId			=	$fishRec[0];
		$fishName		=	stripSlash($fishRec[1]);
		$fishCode		=	stripSlash($fishRec[2]);
		$editCategoryId	=	$fishRec[3];
		$editSourceId	=	$fishRec[4];
	}

	if ($p["cmdSaveChange"]!="") {
		
		$fishId		=	$p["hidFishId"];
		$fishCode	=	addSlash(trim($p["fishCode"]));
		$fishName	=	addSlash(trim($p["fishName"]));
		$categoryId	=	$p["fishCategory"];
		$sourceId	=   $p["fishSource"];
		
		if ($fishId!="" && $fishName!="" && $fishCode!="" && $categoryId!="") {
			$fishRecUptd		=	$fishmasterObj->updateFish($fishId,$fishName, $fishCode,$categoryId,$sourceId);
		}
	
		if ($fishRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succFishUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateFish.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failFishUpdate;
		}
		$fishRecUptd	=	false;
	}


	# Delete Fish
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fishId	=	$p["delId_".$i];

			if ($fishId!="") {
				// Checking the selected fish is link with any other process
				$fishRecInUse = $fishmasterObj->fishRecInUse($fishId);
				if (!$fishRecInUse) {
					$fishRecDel = $fishmasterObj->deleteFish($fishId);	
				}
			}

		}
		if ($fishRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelFish);
			$sessObj->createSession("nextPage",$url_afterDelFish.$selection);
		} else {
			$errDel	=	$msg_failDelFish;
		}
		$fishRecDel	=	false;

	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$fishMasterRecords	=	$fishmasterObj->fetchAllPagingRecords($offset, $limit);
	$fishMasterSize		=	sizeof($fishMasterRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fishmasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	# List all Fish Category;
	$sourceRecords = array();
	//if ($addMode || $editMode) $categoryRecords	= $fishcategoryObj->fetchAllRecords();
	if ($addMode || $editMode) { 
		$categoryRecords	= $fishcategoryObj->fetchAllRecordscategoryActive(); 
		$sourceRecords	    = $fishmasterObj->fetchAllSourceRecords();
	}
	if ($editMode) $heading = $label_editFish;
	else $heading = $label_addFish;

	$help_lnk="help/hlp_addFishMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/fishmaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


	<form name="frmFishMaster" action="FishMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFish(document.frmFishMaster);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFish" class="button" value=" Add " onClick="return validateAddFish(document.frmFishMaster);">												</td>

												<?}?>
											</tr>
											<input type="text" name="hidFishId" value="<?=$fishId;?>">
											<tr>
												<td class="fieldName" nowrap >*Fish Code</td>
												<td><INPUT TYPE="text" NAME="fishCode" size="15" value="<?=$fishCode;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Fish Name</td>
												<td >
												<INPUT TYPE="text" NAME="fishName" size="25"  maxlength="25"value="<?=$fishName;?>">												</td>
											</tr>								
											<tr>
											  <td  height="10" class="fieldName">*Fish Type </td>
										      <td  height="10" ><select name="fishCategory">
											  <option value="">--select--</option>
											  <?
												foreach($categoryRecords as $cr)
													{
														$categoryId		=	$cr[0];
														$categoryName	=	stripSlash($cr[1]);
														
														$selected	=	"";
														if( $categoryId == $editCategoryId){
																$selected	=	"selected";
														}
											?>
											  <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
											  <? }?>
										        </select>										      </td>
										  </tr>
										  <tr>
											  <td  height="10" class="fieldName">*Fish Source </td>
										      <td  height="10" >
											  <select name="fishSource" id="fishSource">
											  <option value="">--select--</option>
											  <?
												foreach($sourceRecords as $cr)
													{
														$sourceId		=	$cr[0];
														$sourceName	    =	stripSlash($cr[1]);
														
														$selected	=	"";
														if( $sourceId == $editSourceId){
																$selected	=	"selected";
														}
											?>
											  <option value="<?=$sourceId?>" <?=$selected?>><?=$sourceName?></option>
											  <? }?>
										        </select>										      </td>
										  </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFish(document.frmFishMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFish" class="button" value=" Add " onClick="return validateAddFish(document.frmFishMaster);">												</td>

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
			
			# Listing Fish Starts
		?>
		
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
								$bxHeader="Fish Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Master</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="50%">
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFish(document.frmFishMaster);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFish" class="button" value=" Add " onClick="return validateAddFish(document.frmFishMaster);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidFishId" value="<?=$fishId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Fish Code</td>
												<td><INPUT TYPE="text" NAME="fishCode" size="15" value="<?=$fishCode;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Fish Name</td>
												<td >
												<INPUT TYPE="text" NAME="fishName" size="25"  maxlength="25" value="<?=$fishName;?>">												</td>
											</tr>
											
											<tr>
											  <td  height="10" class="fieldName">*Fish Type </td>
										      <td  height="10" ><select name="fishCategory">
											  <option value="">--select--</option>
											  <?
												foreach($categoryRecords as $cr)
													{
														$categoryId		=	$cr[0];
														$categoryName	=	stripSlash($cr[1]);
														
														$selected	=	"";
														if( $categoryId == $editCategoryId){
																$selected	=	"selected";
														}
											?>
											  <option value="<?=$categoryId?>" <?=$selected?>><?=$categoryName?></option>
											  <? }?>
										        </select>										      </td>
										  </tr>
										  <tr>
											  <td  height="10" class="fieldName">*Fish Source </td>
										      <td  height="10" >
											  <select name="fishSource" id="fishSource">
											  <option value="">--select--</option>
											  <?
												foreach($sourceRecords as $cr)
													{
														$sourceId		=	$cr[0];
														$sourceName	    =	stripSlash($cr[1]);
														
														$selected	=	"";
														if( $sourceId == $editSourceId){
																$selected	=	"selected";
														}
											?>
											  <option value="<?=$sourceId?>" <?=$selected?>><?=$sourceName?></option>
											  <? }?>
										        </select>										      </td>
										  </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddFish(document.frmFishMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FishMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFish" class="button" value=" Add " onClick="return validateAddFish(document.frmFishMaster);">												</td>

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
			# Listing Fish Starts
		?>
	</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintFishMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="4" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
							<table  cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">							
											<?
												if( sizeof($fishMasterRecords) > 0 )
												{
													$i	=	0;
											?>
										<thead>
											<? if($maxpage>1){?>
											<tr>
											  <td colspan="6" align="right" style="padding-right:10px;"><div align="right" class="navRow">
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
      	$nav.= " <a href=\"FishMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"FishMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"FishMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div> </td>
		  </tr>
										  <? }?>
											<tr >
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th nowrap>Code</th>
												<th>Name</td>
												<th nowrap>Category </th>

											<? if($edit==true){?>	<th class="listing-head"></th><? }?>
											<? if($confirmF==true){?>	<th class="listing-head"></th><? }?>
											</tr>
		</thead>
		<tbody>
											<?
														$displayStatus = "";
													foreach($fishMasterRecords as $fr)
													{
														$i++;
														$fishId		=	$fr[0];
														$fishName	=	stripSlash($fr[1]);
														$fishCode	=	stripSlash($fr[2]);
														$received_type	=	stripSlash($fr[3]);			
														$fishTypeId	=	$fr[3];	
														$categoryRec		=	$fishcategoryObj->find($fishTypeId);
														$fishType			=	stripSlash($categoryRec[1]);
														$active=$fr[4];
														$existingcount=$fr[5];
														//echo "existing count is $existingcount";
														//echo $confirmF;
														
											?>
											<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
												<td width="20" align="center">
												<?php 
												
												if ($existingcount==0) {?>
												<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fishId;?>" class="chkBox"></td>
												<?php 
												}
												?>
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$fishCode;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$fishName;?>&nbsp;</td>
												<td class="listing-item" nowrp>&nbsp;&nbsp;<?=$fishType?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="45" align="center"><?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fishId;?>,'editId'); this.form.action='FishMaster.php';" ><?php }
												?></td> 
																	<? }?>

												<? if ($confirmF==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$fishId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			
			//if ($existingcount==0) {
				?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$fishId;?>,'confirmId');" >
			<?php 
			//}
			?>
			<?php }?>
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
				<td align="right" style="padding-right:10px" colspan="6" class="navRow">
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
      	$nav.= " <a href=\"FishMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"FishMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"FishMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	  </div></td></tr>
											<? }?>
	</tbody>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="4">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$fishMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintFishMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
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
			<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>
	</table>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

