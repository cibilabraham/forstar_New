<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;

	$selection 	=	"?pageNo=".$p["pageNo"]."&unitGroupFilter=".$p["unitGroupFilter"];;

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
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode 	= false;
		$editMode 	= false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="" ) {

		$unitGroup	=	$p["unitGroup"];
		$unitName	=	addSlash(trim($p["unitName"]));
		$descr		=	addSlash(trim($p["unitDescription"]));		
		
		if ($unitName!="" && $unitGroup) {

			$unitRecIns = $stockItemUnitObj->addStockItemUnit($unitGroup, $unitName, $descr);

			if ($unitRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddStockItemUnit);
				$sessObj->createSession("nextPage",$url_afterAddStockItemUnit.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockItemUnit;
			}
			$unitRecIns		=	false;
		}
	}


	# Edit 
	if ($p["editId"]!="" ) {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$stockItemUnitRec	=	$stockItemUnitObj->find($editId);
		$stockItemUnitId	=	$stockItemUnitRec[0];
		$unitName		=	stripSlash($stockItemUnitRec[1]);
		$description		=	stripSlash($stockItemUnitRec[2]);
		$selUnitGroupId 	=  	$stockItemUnitRec[3];
	}


	#Update 
	if ($p["cmdSaveChange"]!="" ) {
		
		$stockItemUnitId	=	$p["hidStockItemUnitId"];
		$unitGroup	=	$p["unitGroup"];
		$unitName	=	addSlash(trim($p["unitName"]));
		$descr		=	addSlash(trim($p["unitDescription"]));	
		
		if ($stockItemUnitId!="" && $unitName!="") {
			$stockItemUnitRecUptd = $stockItemUnitObj->updateStockItemUnit($stockItemUnitId, $unitGroup, $unitName, $descr);
		}
	
		if ($stockItemUnitRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockItemUnitUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockItemUnit.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockItemUnitUpdate;
		}
		$stockItemUnitRecUptd	=	false;
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockItemUnitId	=	$p["delId_".$i];

			if ($stockItemUnitId!="") {
				// Check the selected Stock Item unit is linked with any other process
				$stkItemUnitExist = $stockItemUnitObj->checkMoreEntriesExist($stockItemUnitId);
				if (!$stkItemUnitExist) {
					$stockItemUnitRecDel = $stockItemUnitObj->deleteStockItemUnit($stockItemUnitId);
				}
			}
		}
		if ($stockItemUnitRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelStockItemUnit);
			$sessObj->createSession("nextPage", $url_afterDelStockItemUnit.$selection);
		} else {
			if ($stkItemUnitExist) $errDel	= $msg_failDelStockItemUnit."<br><b>The selected unit is already in use.</b>";
			else $errDel	=	$msg_failDelStockItemUnit;
		}
		$stockItemUnitRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockItemUnitId	=	$p["confirmId"];
			if ($stockItemUnitId!="") {
				// Checking the selected fish is link with any other process
				$stockRecConfirm = $stockItemUnitObj->updateStockItemUnitconfirm($stockItemUnitId);
			}

		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmstockunit);
			$sessObj->createSession("nextPage", $url_afterDelStockItemUnit.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$stockItemUnitId = $p["confirmId"];
			if ($stockItemUnitId!="") {
				#Check any entries exist
				
					$stockRecConfirm = $stockItemUnitObj->updateStockItemUnitReleaseconfirm($stockItemUnitId);
				
			}
		}
		if ($stockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmstockunit);
			$sessObj->createSession("nextPage", $url_afterDelStockItemUnit.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	if ($g["unitGroupFilter"]!="") $unitGroupFilterId = $g["unitGroupFilter"];
	else $unitGroupFilterId = $p["unitGroupFilter"];	

	# Resettting offset values
	if ($p["hidUnitGroupFilterId"]!=$p["unitGroupFilter"]) {		
		$offset = 0;
		$pageNo = 1;
	}

	# List all Unit 
	$stockItemUnitRecords	= $stockItemUnitObj->fetchAllPagingRecords($offset, $limit, $unitGroupFilterId);
	$stockItemUnitSize	= sizeof($stockItemUnitRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($stockItemUnitObj->fetchAllRecords($unitGroupFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	# List all Unit Group 
	//$unitGroupRecords = $unitGroupObj->fetchAllRecords();
	$unitGroupRecords = $unitGroupObj->fetchAllActiveRecords();

	if ($editMode)	$heading = $label_editStockItemUnit;
	else 		$heading = $label_addStockItemUnit;
	
	$ON_LOAD_PRINT_JS	= "libjs/StockItemUnit.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmStockItemUnit" action="StockItemUnit.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="5"></TD></tr>
		<tr><td align="center"><a href="UnitGroup.php" class="link1">Unit Group</a></td></tr>
		<tr><TD height="5"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>	
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Unit";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">	
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockItemUnit.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStockItemUnit(document.frmStockItemUnit);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockItemUnit.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockItemUnit(document.frmStockItemUnit);">												</td>

												<?}?>
											</tr>
	<input type="hidden" name="hidStockItemUnitId" value="<?=$stockItemUnitId;?>">
<tr>
	<td colspan="2"  height="10" ></td>
</tr>
		<tr>
			<td colspan="2" align="center">
			<table>
				<tr>
					<td class="fieldName" nowrap >*Group</td>
					<td nowrap>
					<select name="unitGroup" id="unitGroup">
						<option value="">--Select--</option>
				<?
				foreach ($unitGroupRecords as $cr) {			
					$unitGroupId	= $cr[0];
					$groupName	= stripSlash($cr[1]);	
					$selected = ($selUnitGroupId==$unitGroupId)?"Selected":"";		
				?>
				<option value="<?=$unitGroupId?>" <?=$selected?>><?=$groupName?></option>
				<? }?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >*Name</td>
					<td><INPUT TYPE="text" NAME="unitName" size="15" value="<?=$unitName;?>"></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap >Description</td>
					<td ><textarea name="unitDescription"><?=$description;?></textarea></td>
				</tr>
			</table>
			</td>
		</tr>
		
		<tr>
			<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockItemUnit.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateStockItemUnit(document.frmStockItemUnit);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockItemUnit.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateStockItemUnit(document.frmStockItemUnit);">												</td>

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
						<table width="20%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="4">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table cellpadding="0" cellspacing="0">
                	<tr>
			  	<td class="listing-item"> Unit Group&nbsp;</td>
                      		<td nowrap="nowrap">
                <select name="unitGroupFilter" onchange="this.form.submit();">
                <option value="">-- Select All --</option>
                <?php
		foreach ($unitGroupRecords as $ugr) {			
			$fUnitGroupId	= $ugr[0];
			$fUnitGroupName	= stripSlash($ugr[1]);	
			$selected = ($unitGroupFilterId==$fUnitGroupId)?"Selected":"";		
		?>
               <option value="<?=$fUnitGroupId?>" <?=$selected;?>><?=$fUnitGroupName;?></option>
                <? }?>
                </select>
		</td>		
                        </tr>
                    </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Unit  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
								<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
								<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockItemUnitSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockItemUnit.php?unitGroupFilter=<?=$unitGroupFilterId?>',700,600);"><? }?></td>
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
								<td colspan="3" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ( sizeof($stockItemUnitRecords) > 0) {
			$i	=	0;
		?>
		<thead>
		<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\"  class=\"link1\">>></a> ";
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
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Group </th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </th>
			<? if($edit==true){?><th class="listing-head">&nbsp;</th><? }?>
			<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
		</tr>
	</thead>
	<tbody>
		<?
		foreach ($stockItemUnitRecords as $siur) {
			$i++;
			$stockItemUnitId = $siur[0];
			$unitName	 = stripSlash($siur[1]);
			$description     = stripSlash($siur[2]);
			$unitGroupName   = $siur[4];	
			$active=$siur[5];
			$existingcount=$siur[6];

		?>
		<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20">
		<?php 
		if($existingcount){
		?>
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockItemUnitId;?>" class="chkBox"></td>
		<?php 
		}
		?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unitName;?></td>		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$unitGroupName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$description?></td>	
		<? if($edit==true){?><td class="listing-item" width="60" align="center"> 
		<?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockItemUnitId;?>,'editId'); this.form.action='StockItemUnit.php';">
		<? }?></td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockItemUnitId;?>,'confirmId');" >
			<?php } else if ($active==1){ 
			//if ($existingcount==0){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockItemUnitId;?>,'confirmId');" >
			<?php
			//}
			} }?>
			
			
			
			
			</td>
												
<? }?>
		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="confirmId" value="">
		<? if($maxpage>1){?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"StockItemUnit.php?pageNo=$page&unitGroupFilter=$unitGroupFilterId\"  class=\"link1\">>></a> ";
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
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td><? if($del){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$stockItemUnitSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintStockItemUnit.php?unitGroupFilter=<?=$unitGroupFilterId?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"><input type="hidden" name="hidUnitGroupFilterId" value="<?=$unitGroupFilterId?>">	</td>
		</tr>
		<tr><td height="10" align="center"><a href="UnitGroup.php" class="link1"> Unit Group</a></td></tr>
	</table>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>