<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;	

	$selection 	=	"?pageNo=".$p["pageNo"]."&selStateFilter=".$p["selStateFilter"];

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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode  =   true;	
	if ($p["cmdCancel"]!="") {
		$addMode   = false;
		$editMode  = false;
	}
	
	#Add a Record
	if ($p["cmdAdd"]!="") {
		$cityCode 	= "CITY_".autoGenNum();
		$cityName	= addSlash(trim($p["cityName"]));
		$state		= $p["state"];		
		$octroi		= ($p["octroi"]=="")?N:$p["octroi"]; //y/n
		$octroiPercent	= $p["octroiPercent"];
		
		if ($cityCode!="" && $cityName!="" && $state!="") {
			$cityRecIns = $cityMasterObj->addCity($cityCode, $cityName, $state, $octroi, $octroiPercent);
			if ($cityRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddCity);
				$sessObj->createSession("nextPage",$url_afterAddCity.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddCity;
			}
			$cityRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$cityId	=	$p["hidCityId"];
		$cityName	= addSlash(trim($p["cityName"]));
		$state		= $p["state"];
		$octroi		= ($p["octroi"]=="")?N:$p["octroi"]; //y/n
		$octroiPercent	= $p["octroiPercent"];
		
		if ($cityId!="" && $cityName!="") {
			$cityRecUptd = $cityMasterObj->updateCity($cityId, $cityName, $state, $octroi, $octroiPercent);
		}
	
		if ($cityRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succCityUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateCity.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failCityUpdate;
		}
		$cityRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$cityRec	=	$cityMasterObj->find($editId);
		$editCityId 	=	$cityRec[0];
		$cityCode	=	stripSlash($cityRec[1]);
		$cityName	=	stripSlash($cityRec[2]);
		$state		=	$cityRec[3];
		$octroi		= 	($cityRec[4]=="Y")?"Checked":""; //y/n
		$octroiPercent	= 	($cityRec[5]!=0)?$cityRec[5]:"";
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$cityId	=	$p["delId_".$i];			
			if ($cityId!="") {
				# check entry exist
				$cityEntryExist = $cityMasterObj->cityEntryExist($cityId);
				if (!$cityEntryExist) {
					// Need to check the selected Category is link with any other process
					$cityRecDel = $cityMasterObj->deleteCity($cityId);
				}
			}
		}
		if ($cityRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelCity);
			$sessObj->createSession("nextPage",$url_afterDelCity.$selection);
		} else {
			$errDel	=	$msg_failDelCity;
		}
		$cityRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$cityId	=	$p["confirmId"];


			if ($cityId!="") {
				// Checking the selected fish is link with any other process
				$cityRecConfirm = $cityMasterObj->updatecityconfirm($cityId);
			}

		}
		if ($cityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmcity);
			$sessObj->createSession("nextPage",$url_afterDelCity.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$cityId = $p["confirmId"];

			if ($cityId!="") {
				#Check any entries exist
				
					$cityRecConfirm = $cityMasterObj->updatecityReleaseconfirm($cityId);
				
			}
		}
		if ($cityRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmcity);
			$sessObj->createSession("nextPage",$url_afterDelCity.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	


	if ($g["selStateFilter"]!="") $selStateFilter = $g["selStateFilter"];
	else $selStateFilter = $p["selStateFilter"];

	if ($p["selStateFilter"]!=$p["hidSelStateFilter"]) {
		$offset	= 0;
	}
	# List all City
	$cityResultSetObj = $cityMasterObj->fetchAllPagingRecords($offset, $limit, $selStateFilter);
	$cityRecordSize	= $cityResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allCityResultSetObj = $cityMasterObj->fetchAllRecords($selStateFilter);
	$numrows	=  $allCityResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	#List all State
	if ($addMode || $editMode ) $stateResultSetObj=$stateMasterObj->fetchAllRecordsActiveState();//$stateResultSetObj = $stateMasterObj->fetchAllRecords();
	
	//$stateFilterResultSetObj = $stateMasterObj->fetchAllRecords();
	$stateFilterResultSetObj = $stateMasterObj->fetchAllRecordsActiveState();
	#heading Section
	if ($editMode) $heading	= $label_editCity;
	else	       $heading	= $label_addCity;

	$ON_LOAD_PRINT_JS	= "libjs/CityMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmCityMaster" action="CityMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >	
	<tr><td height="5" align="center"><a href="StateMaster.php" class="link1">State</a></td></tr>
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
		<td nowrap="true">
			<!-- Form fields start -->
		<?php	
			$bxHeader="City Master";
			include "template/boxTL.php";
		?>
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
<tr>
		<td colspan="3" align="center">
		<table width="50%" align="center">
<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('CityMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateCityMaster(document.frmCityMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCityMaster(document.frmCityMaster);">
												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidCityId" value="<?=$editCityId;?>">
											<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
											  <td colspan="2" nowrap align="center">
											  <table width="200">
											<tr>
											  <td class="fieldName" nowrap >*Name</td>
											  <td>
											  <input type="text" name="cityName" size="20" value="<?=$cityName;?>" /></td>
										  </tr>
				<tr>
					<td nowrap class="fieldName" >*State</td>
					<td nowrap>
                                        <select name="state">
                                        <option value="">--Select--</option>
					<?
					while (($sr=$stateResultSetObj->getRow())) {
						$stateId = $sr[0];
						$stateCode	= stripSlash($sr[1]);
						$stateName	= stripSlash($sr[2]);	
						$selected = "";
						if ($state==$stateId) $selected = "Selected";			
					?>
                                        <option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
					<? }?>
                                        </select></td>
				</tr>
				<tr>
					<td class="fieldName" nowrap="nowrap">Octroi</td>
					<td nowrap="true">
						<table>
							<TR>
								<TD onMouseover="ShowTip('If Yes, please give tick mark');" onMouseout="UnTip();">
									<input name="octroi" type="checkbox" id="octroi" value="Y" <?=$octroi?> class="chkBox" onclick="disOctroiPercent();"> 
								</TD>
								<TD nowrap="true" class="listing-item" id="octroiPercentCol">
									<input type="text" name="octroiPercent" id="octroiPercent" size="4" value="<?=$octroiPercent?>" style="text-align:right;" />&nbsp;%
								</TD>
							</TR>
						</table>
					</td>					
				</tr>
                              </table></td>
			  </tr>
						<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCityMaster(document.frmCityMaster);">	
												</td>
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CityMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCityMaster(document.frmCityMaster);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
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
		<?
			}			
			# Listing Category Starts
		?>
		</table>
		</td>
	</tr>
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="15" ></td>
	</tr>
	<?php
		}
	?>		
		<tr>
		<td colspan="3" align="center">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;" border="0">	
				<tr>
					<td align="right" nowrap class="listing-item">State</td>
				<td nowrap valign="top" align="center">
				<select name="selStateFilter" onChange="this.form.submit();">
				 <option value="">--Select All--</option>
					<?php
					while (($sr=$stateFilterResultSetObj->getRow())) {
						$stateId = $sr[0];					
						$stateName = stripSlash($sr[2]);	
						$selected = "";
						if ($selStateFilter==$stateId) $selected = "Selected";
					?>
                                        <option value="<?=$stateId?>" <?=$selected?>><?=$stateName?></option>
					<? }?>
				  </select>
				</td>				
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
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" >&nbsp;City Master  </td>
	<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$cityRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCityMaster.php?selStateFilter=<?=$selStateFilter?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if ($errDel!="") {
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
							<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($cityRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\"  class=\"link1\">>></a> ";
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
			<th style="padding-left:10px; padding-right:10px;">Name</th>
			<th style="padding-left:10px; padding-right:10px;">State</th>
			<th style="padding-left:10px; padding-right:10px;">Octroi</th>
			<th style="padding-left:10px; padding-right:10px;">Octroi (%)</th>
			<? if($edit==true){?>
			<th>&nbsp;</th>
			<? }?>
			<? if($confirm==true){?>
			<th>&nbsp;</th>
			<? }?>
	</tr>
	</thead>
	<tbody>

			<?php
			$prevStateId = "";
			while ($cr=$cityResultSetObj->getRow()) {
				$i++;
				$cityId 	= $cr[0];
				$cityCode	= stripSlash($cr[1]);
				$cityName	= stripSlash($cr[2]);	
				$stateId	= $cr[3];
				$stateName	= $cr[4];
				$sOctroi	= ($cr[5]=='Y')?"YES":"NO";
				$sOctroiPercent	= $cr[6];
				$active=$cr[7];
			?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8" <?php }?>>
		<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$cityId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$cityName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stateName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$sOctroi;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=($sOctroiPercent!=0)?$sOctroiPercent:"";?></td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		 <?php if ($active!=1) {?>
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$cityId;?>,'editId');this.form.action='CityMaster.php';" >
		<? } ?>
		</td>
<? }?>
 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$cityId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$cityId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>


		</tr>
		<?
			$prevStateId = $stateId;
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
		<input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="7" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"CityMaster.php?pageNo=$page&selStateFilter=$selStateFilter\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$cityRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintCityMaster.php?selStateFilter=<?=$selStateFilter?>',700,600);"><? }?></td>
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
<input type="hidden" name="hidSelStateFilter" value="<?=$selStateFilter?>">
		<tr>
			<td height="10"></td>
		</tr>
		<tr><td height="5" align="center"><a href="StateMaster.php" class="link1">State</a></td></tr>
	</table>
	<? 
		if ($addMode || $editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		disOctroiPercent();
	</script>
	<?php
		}
	?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
