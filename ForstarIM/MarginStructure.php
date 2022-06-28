<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$selection 	= "?pageNo=".$p["pageNo"];

	
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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------	
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	

	/*
	$moveUpId = $g["up"];	
	$moveDownId = $g["down"];
	if ($moveUpId!="") {
		$moveUpRec = $marginStructureObj->moveUpRec($moveUpId);
	}

	if ($moveDownId!="") {
		$moveDownRec = $marginStructureObj->moveDownRec($moveDownId);
	}
	*/
	
	# Changing Display Order
	if ($g["up"]!="")		$displayChangeId   = $g["up"];			
	else if ($g["down"]!="")	$displayChangeId	= $g["down"];
	else $displayChangeId	= "";
	if ($displayChangeId!="" && ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $displayChangeId)) {
		$updateDisplayOrder = $marginStructureObj->changeDisplayOrder($displayChangeId);		
	} 

	# Insert a Rec
	if ($p["cmdAdd"]!="") {
		$marginStructureCode	= addSlash(trim($p["marginStructureCode"]));
		$marginStructureName	= addSlash(trim($p["marginStructureName"]));
		$description		= addSlash(trim($p["description"]));
		$calcAvgDistMagn	= ($p["calcAvgDistMagn"]=="")?N:$p["calcAvgDistMagn"]; // is it using to calculate Avg dist Margin
		$priceCalcType		= $p["priceCalcType"];
		$billingFormF		= $p["billingFormF"];

		$schemeChk		= ($p["schemeChk"]=="")?N:$p["schemeChk"];
		$selSchemeHeadId	= $p["selSchemeHeadId"];
	
		if ($marginStructureName!="") {
			$marginStructureRecIns = $marginStructureObj->addMarginStructure($marginStructureCode, $marginStructureName, $description, $calcAvgDistMagn, $priceCalcType, $billingFormF, $schemeChk, $selSchemeHeadId);
			
			if ($marginStructureRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddMarginStructure );
				$sessObj->createSession("nextPage",$url_afterAddMarginStructure .$selection);
			} else {
				$addMode	= true;
				$err		= $msg_failAddMarginStructure ;
			}
			$marginStructureRecIns	=	false;
		}
	}

	
	# Edit 
	if ($p["editId"]!="") {
		$editId		= $p["editId"];
		$editMode	= true;
		
		$marginStructureRec	= $marginStructureObj->find($editId);
		
		$editMarginStructureId	= $marginStructureRec[0];
		$marginStructureName	= stripSlash($marginStructureRec[1]);
		$description		= stripSlash($marginStructureRec[2]);
		$calcAvgDistMagn	= $marginStructureRec[3];
		if ($calcAvgDistMagn=='Y') $chkCalcAvgDistMagn = "Checked";
		$priceCalcType		= $marginStructureRec[4];
		if ($priceCalcType=='MU') $chkMarkUp = "Checked";
		else if ($priceCalcType=='MD') $chkMarkDown = "checked";
		
		$marginStructureCode	= $marginStructureRec[5];
		$billingFormF		= $marginStructureRec[6];
		
		$selSchemeChk		= $marginStructureRec[7];
		if ($selSchemeChk=='Y') $schemeChk	= "checked";
		$selSchemeHeadId	= $marginStructureRec[8];
	}

	# update Record
	if ($p["cmdSaveChange"]!="") {
		
		$marginStructureId	= $p["hidMarginStructureId"];

		$marginStructureCode	= addSlash(trim($p["marginStructureCode"]));
		$marginStructureName	= addSlash(trim($p["marginStructureName"]));
		$description		= addSlash(trim($p["description"]));
		$calcAvgDistMagn	= ($p["calcAvgDistMagn"]=="")?N:$p["calcAvgDistMagn"]; // is it using to calculate Avg dist Margin
		$priceCalcType		= $p["priceCalcType"];
		$billingFormF		= $p["billingFormF"];
		
		$schemeChk		= ($p["schemeChk"]=="")?N:$p["schemeChk"];
		$selSchemeHeadId	= $p["selSchemeHeadId"];
		
		if ($marginStructureId!="" && $marginStructureName!="") {
			$marginStructureRecUptd = $marginStructureObj->updateMarginStructure($marginStructureId, $marginStructureCode, $marginStructureName, $description, $calcAvgDistMagn, $priceCalcType, $billingFormF, $schemeChk, $selSchemeHeadId);
		}
	
		if ($marginStructureRecUptd){
			$sessObj->createSession("displayMsg",$msg_succUpdateMarginStructure);
			$sessObj->createSession("nextPage",$url_afterUpdateMarginStructure .$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateMarginStructure ;
		}
		$marginStructureRecUptd	=	false;
	}
	
	
	# Delete 
	if ($p["cmdDelete"]!="") {

		$rowCount	= $p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$marginStructureId	= $p["delId_".$i];
		
			#Checking the selected magn Structure is using in another table
			$isMgnStructUsed = $marginStructureObj->checkMgnStructUse($marginStructureId);

			if ($marginStructureId!="" && !$isMgnStructUsed) {			
				$marginStructureRecDel = $marginStructureObj->deleteMarginStructure($marginStructureId);
			}
		}
		if ($marginStructureRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelMarginStructure );
			$sessObj->createSession("nextPage",$url_afterDelMarginStructure .$selection);
		} else {
			$errDel	=	$msg_failDelMarginStructure ;
		}
		$marginStructureRecDel	=	false;
	}


	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;

	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
	
	#List All Margin Structure (Head) Record
	$marginStructureRecords = $marginStructureObj->fetchPagingRecords($offset, $limit);
	$marginStructureRecordsize = sizeof($marginStructureRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($marginStructureObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) $schemeHeadRecs = $marginStructureObj->fetchAllRecords();

	if ($editMode) $heading = $label_editMarginStructure ;
	else $heading = $label_addMarginStructure ;

	
	if ($addMode!="" || $selSchemeChk=='N') $ON_LOAD_FN = "return hidSchemeHead();";

	//$help_lnk="help/hlp_Packing.html";

	$ON_LOAD_PRINT_JS	= "libjs/MarginStructure.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmMarginStructure" action="MarginStructure.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">		
	<tr>
		<td height="10" align="center"><a href="DistMarginStructure.php" class="link1" title="Click to Manage Distributor Margin Structure">Distributor Margin Structure</a>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
	<?}?>

<tr>
<td>
<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
<tr>
	<td nowrap="true">
	<!-- Form fields start -->
	<?php	
		$bxHeader="Margin Structure";
		include "template/boxTL.php";
	?>
	<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
	<tr>
	<td align="center" colspan="3">
	<table width="50%" align="center">
		<?php
			if ($editMode || $addMode) {
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('MarginStructure.php');">&nbsp;&nbsp;
		<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateMarginStructure(document.frmMarginStructure);">												</td>
				<?} else{?>
	<td align="center">
				<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('MarginStructure.php');">&nbsp;&nbsp;
			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateMarginStructure(document.frmMarginStructure);">
	</td>
	<?}?>
	</tr>
<input type="hidden" name="hidMarginStructureId" value="<?=$editMarginStructureId;?>">
			<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" style="padding-left:60px;"> 
		<table width="50%">
			<tr>
                        	<td class="fieldName" nowrap="nowrap">*Code</td>
                                <td class="listing-item">
					<input name="marginStructureCode" type="text" id="marginStructureCode" value="<?=$marginStructureCode?>" size="10">
				</td>
                        </tr>
			<tr>
                        	<td class="fieldName" nowrap="nowrap">*Name</td>
                                <td class="listing-item">
					<input name="marginStructureName" type="text" id="marginStructureName" value="<?=$marginStructureName?>" size="20">
				</td>
                        </tr>
                                                <tr>
                                                  <td class="fieldName" nowrap="nowrap">Description</td>
                                                  <td class="listing-item"><textarea name="description" rows="1" id="description"><?=$description?></textarea></td>
                                                </tr>
	<tr>
		<td class="fieldName" nowrap>Billing on Form F</td>
		<td>
		<select name="billingFormF" id="billingFormF">
		<option value="N" <? if($billingFormF=='N') echo "Selected"; ?>>No</option>
		<option value="Y" <? if($billingFormF=='Y') echo "Selected"; ?>>Yes</option>	
		</select>
		</td>
	</tr>	
	<tr>
             <td class="fieldName" nowrap>Using in Avg Dist Margin</td>
             <td class="listing-item" valign="middle" nowrap>
			<INPUT type="checkbox" name="calcAvgDistMagn" class="chkBox" value="Y" <?=$chkCalcAvgDistMagn?>>&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
	    </td>
       </tr>
	<tr>
             <td class="fieldName" nowrap>Scheme</td>
             <td class="listing-item" valign="middle" nowrap>
			<INPUT type="checkbox" name="schemeChk" id="schemeChk" class="chkBox" value="Y" <?=$schemeChk?> onclick="showSchemeHead();">&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
	    </td>
       </tr>
	<tr>
		<TD colspan="2" align="center">
		<div id="selScheme" style="display:block">
			<table width="250">
				<TR>
					<td class="fieldName" nowrap>Scheme Head</td>
             				<td class="listing-item" nowrap>
					<select name="selSchemeHeadId" id="selSchemeHeadId">
					<option value="">-- select ---</option>
				<?
				foreach ($schemeHeadRecs as $msr) {
					$marginStructureId	= $msr[0];
					$marginStructureName	= stripSlash($msr[1]);
					$mgnStructureCode	= $msr[5];
					$selected = "";
					if ($selSchemeHeadId==$marginStructureId) $selected = "selected";
				?>
				<option value="<?=$marginStructureId?>" <?=$selected?>><?=$mgnStructureCode?></option>	
				<? }?>
				</select>
				</td>
				</TR>
			</table>
		</div>
		</TD>
	</tr>
	<tr>
        <td colspan="2" align="center">
	<table>
		<TR>
			<TD>
			<table>
				<TR><TD><INPUT type="radio" name="priceCalcType" value="MU" class="chkBox" <?=$chkMarkUp?>></TD><TD class="fieldName">Markup</TD></TR>
			</table>
			</TD>
			<TD>
			<table>
				<TR><TD><INPUT type="radio" name="priceCalcType" value="MD" class="chkBox" <?=$chkMarkDown?>></TD><TD class="fieldName">Markdown</TD></TR>
			</table>
			</TD>
			</TR>
							</table>
						  </td>
                                                </tr>
                                              </table></td>
											</tr>
											<tr>
				<? if($editMode){?>
				<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MarginStructure.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateMarginStructure(document.frmMarginStructure);">												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('MarginStructure.php');">&nbsp;&nbsp;
	<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateMarginStructure(document.frmMarginStructure);">												</td>

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
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">							
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp; Margin Structure</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$marginStructureRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMarginStructure.php',700,600);"><? }?></td>
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
		<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<thead>
		<?
		if (sizeof($marginStructureRecords)>0) {
			$i	=	0;
		?>
		<? if($maxpage>1){?>
<tr>
<td colspan="7" style="padding-right:10px" class="navRow">
<div align="right">
<?php
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   				} else {
					$nav.= " <a href=\"MarginStructure.php?pageNo=$page\" class=\"link1\">$page</a> ";
				}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MarginStructure.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"MarginStructure.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
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
	<tr align="center">
		<th width="20" rowspan="2"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Code </th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Name </th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2" width="100px">Markup/Markdown </th>		
		<? if($edit==true){?>
			<th class="listing-head" width="50" rowspan="2">&nbsp;</th>
		<? }?>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Display Order</th>
	</tr>
	<tr align="center">
		<th align="center" style="padding-left:5px; padding-right:5px;" class="secondRowHead" width="60px;">Move Up</th>
		<th style="padding-left:5px; padding-right:5px;" class="secondRowHead" width="60px;">Move Down</th>
	</tr>
	</thead>
	<tbody>
	<?
	/*
	foreach ($marginStructureRecords as $msr) {
		$i++;
	*/
	
	for ($i=1;$i<=sizeof($marginStructureRecords);$i++) {
		$msr = $marginStructureRecords[$i-1]; // Get Current Record
		$fRec   = $marginStructureRecords[$i]; //Forward Record
		$pRec   = $marginStructureRecords[$i-2]; // Prev Rec
		
		$marginStructureId	= $msr[0];
		$marginStructureName	= stripSlash($msr[1]);
		$mgnStructureDescr	= stripSlash($msr[2]);
		$priceCalcType		= $msr[3];
		$displayPriceCalcType = "";
		if ($priceCalcType=='MU') $displayPriceCalcType = "Markup"; 
		else if ($priceCalcType=='MD') $displayPriceCalcType = "Markdown";
		

		$mgnStructureCode	= $msr[5];
		$displayOrderId		= $msr[6];	
		# Display Settings	
		$disOrderUp		= "$pRec[0]-$msr[6];$msr[0]-$pRec[6]";	// Pass URL value		
		$disOrderDown	= "$fRec[0]-$msr[6];$msr[0]-$fRec[6]";
		//echo $disOrderUp."<------------->".$disOrderDown."<br>";
	?>
	<tr>
		<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$marginStructureId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$mgnStructureCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$marginStructureName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayPriceCalcType;?></td>
		<? if($edit==true){?>
			  <td class="listing-item" width="50" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$marginStructureId;?>,'editId');"></td>
		  <? }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
		<? if ($i>1 && $i!=$marginStructureRecordsize) {?>
			<a href="MarginStructure.php?up=<?=$disOrderUp?>" class="displayArrow"><img src="images/arrow_up.gif" border="0" title="Move Up"></a>
		<? } ?>
		<?if ($i==$marginStructureRecordsize) {?>
			<a href="MarginStructure.php?up=<?=$disOrderUp?>" class="displayArrow"><img src="images/arrow_up.gif" border="0" title="Move Up"></a>
		<? }?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<? if ($i==1) {?>
				<a href="MarginStructure.php?down=<?=$disOrderDown?>" class="displayArrow"><img src="images/arrow_down.gif" border="0" title="Move Down"></a>
			<? } ?>
			<? if ($i>1 && $i!=$marginStructureRecordsize) {?>
				<a href="MarginStructure.php?down=<?=$disOrderDown?>" class="displayArrow"><img src="images/arrow_down.gif" border="0" title="Move Down"></a>
			<? } ?>
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
<td colspan="7" style="padding-right:10px" class="navRow">
<div align="right">
<?php
	$nav  = '';
	for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
   			} else {
				$nav.= " <a href=\"MarginStructure.php?pageNo=$page\" class=\"link1\">$page</a> ";
			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"MarginStructure.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"MarginStructure.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
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
		<tr>
			<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$marginStructureRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintMarginStructure.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
		<tr>
			<td height="10" align="center"><a href="DistMarginStructure.php" class="link1" title="Click to Manage Distributor Margin Structure">Distributor Margin Structure</a>
			</td>
		</tr>
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>