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
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
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
	if ($p["cmdAddFuelRate"]!="" ) {

		$date		= mysqlDateFormat($p["date"]);
		$fuelRate		= addSlash(trim($p["fuelRate"]));
		
		if ($date!="" && $fuelRate!="") {
			$fuelRateIns = $fuelRateObj->addFuelRate($date, $fuelRate, $userId);

			if ($fuelRateIns) {
				$sessObj->createSession("displayMsg",$msg_succAddFuelRate);
				$sessObj->createSession("nextPage",$url_afterAddFuelRate.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddFuelRate;
			}
			$recpMainCategoryRecIns		=	false;
		}
	}


	# Edit Category 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$fuelRateRec	=	$fuelRateObj->find($editId);
		$fuelRateId	=	$fuelRateRec[0];
		$date	=	dateFormat(stripSlash($fuelRateRec[1]));
		$rate		=	stripSlash($fuelRateRec[2]);
	}

	#Update a Category
	if ($p["cmdSaveChange"]!="" ) {
		
		$fuelRateId	=	$p["hidFuelRateId"];
		$date		= mysqlDateFormat($p["date"]);
		$fuelRate		= addSlash(trim($p["fuelRate"]));
		
		if ($fuelRateId!="" && $date!="" && $fuelRate!="") {
			$fuelRateRecUptd = $fuelRateObj->updateFuelRate($fuelRateId, $date, $fuelRate);
		}
	
		if ($fuelRateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succFuelRateUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateFuelRate.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failFuelRateUpdate;
		}
		$fuelRateRecUptd	=	false;
	}


	# Delete Category
	if ( $p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fuelRateId	=	$p["delId_".$i];

			if ($fuelRateId!="") {
				// Check the selected Category is linked with any other process
				//$moreEntriesExist = $fuelRateObj->checkMoreEntriesExist($recpMainCategoryId);
				
				//if (!$moreEntriesExist) {
					$recpRateRecDel = $fuelRateObj->deleteFuelRate($fuelRateId);
				//}
			}
		}
		if ($recpRateRecDel) {
			$sessObj->createSession("displayMsg", $msg_succDelFuelRate);
			$sessObj->createSession("nextPage", $url_afterDelFuelRate.$selection);
		} else {
			$errDel	=	$msg_failDelFuelRate;
		}
		$recpRateRecDel	=	false;
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$fuelRateId	=	$p["confirmId"];


			if ($fuelRateId!="") {
				// Checking the selected fish is link with any other process
				$recpFuelRateRecConfirm = $fuelRateObj->updateFuelRateconfirm($fuelRateId);
			}

		}
		if ($recpFuelRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelFuelRate.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
	$rowCount	=	$p["hidRowCount"];
	for ($i=1; $i<=$rowCount; $i++) {

			$fuelRateId= $p["confirmId"];

			if ($fuelRateId!="") {
				#Check any entries exist
				
					$recpRateRecConfirm = $fuelRateObj->updateFuelRateReleaseconfirm($fuelRateId);
				
			}
		}
		if ($recpRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmcategory);
			$sessObj->createSession("nextPage",$url_afterDelFuelRate.$selection);
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
	$fuelRateRecords	=	$fuelRateObj->fetchAllPagingRecords($offset, $limit);
	$offval=$offset+1;
	$nextfuelRateRecords	=	$fuelRateObj->fetchAllPagingRecords($offval, $limit);
	$fuelSize		=	sizeof($fuelRateRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fuelRateObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/FuelRate.js"; 

	if ($editMode)	$heading = $label_editFuelRate;
	else 		$heading = $label_addFuelRate ;
	
	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	/*if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");*/
	require("template/topLeftNav.php");
?>

	<form name="frmFuelRate" action="FuelRate.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" > 	 
		<!--<tr><td height="10" align="center"><a href="RecipeCategory.php" class="link1">Recipe Sub-Category</a>&nbsp;&nbsp;<a href="RecipesMaster.php" class="link1">Recipe Master</a></td></tr>-->
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
					$bxHeader="Fuel Rate";
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
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('FuelRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFuelRate(document.frmFuelRate);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('FuelRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFuelRate" class="button" value=" Add " onClick="return validateFuelRate(document.frmFuelRate);">												</td>

												<?}?>
											</tr>
												<input type="hidden" name="hidFuelRateId" value="<?=$fuelRateId;?>">
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Date</td>
												<td><INPUT TYPE="text" NAME="date" id="date" size="15" value="<?=$date;?>"></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Fuel Rate</td>
												<td ><INPUT TYPE="text" NAME="fuelRate" size="15" value="<?=$rate;?>"></td>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FuelRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFuelRate(document.frmFuelRate);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FuelRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddFuelRate" class="button" value=" Add " onClick="return validateFuelRate(document.frmFuelRate);">												</td>

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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Recipe Category </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
								<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
								<tr>
		<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintFuelRate.php',700,600);"><? }?></td>
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
		<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">		
		<?php
			if (sizeof($fuelRateRecords)>0) {
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
      				$nav.= " <a href=\"FuelRate.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FuelRate.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FuelRate.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Rate </th>
		<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Difference In Rate% </th>
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
		$j=0;
		foreach ($fuelRateRecords as $cr) {
			$i++;
			$fuelRateId	= $cr[0];
			$date	= dateFormat(stripSlash($cr[1]));
			$rate	= stripSlash($cr[2]);
			$active=$cr[3];
			//echo $j;
			$prevRate=$nextfuelRateRecords[$j][2];
			//echo $prevRate;
			($prevRate!="")?$perDifference=$rate/$prevRate:$perDifference="";
			
			
			
		?>
		<tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$fuelRateId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$date;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$rate?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=number_format($perDifference, 2, '.', ' ');?></td>
		<? if($edit==true){?><td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$fuelRateId;?>,'editId'); this.form.action='FuelRate.php';"><? } ?></td>
		<? }?>


		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$fuelRateId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm"  onClick="assignValue(this.form,<?=$fuelRateId;?>,'confirmId');" >
			<?php }?>
			<? }?>
		</tr>
		<?	
			$j++;
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
      				$nav.= " <a href=\"FuelRate.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"FuelRate.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"FuelRate.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$categorySize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintFuelRate.php',700,600);"><? }?></td>
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
			//	include "template/boxBR.php"
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
			<td height="10" align="center"><a href="RecipeCategory.php" class="link1">Recipe Sub-Category</a>&nbsp;&nbsp;<a href="RecipesMaster.php" class="link1">Recipe Master</a></td></tr>-->
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
	//ensureInFrameset(document.frmFuelRate);
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

<script>
Calendar.setup 
	(	
		{
			inputField  : "date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

</script>

<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>