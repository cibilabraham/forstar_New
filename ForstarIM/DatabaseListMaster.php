<?php
	require("include/include.php");
	//require_once("lib/ChangesUpdateMaster_ajax.php");	
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selection 	= "?pageNo=".$p["pageNo"];
	// rekha hided code 
	//$basefolder = "F:/DailyBackup/Databases";
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId, $functionId);

	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$Id = $p["delId_".$i];
			if ($Id!=""){
			$Find_arr = $DatabaseListObj->findDbFile($Id);
			$FileN = $Find_arr[0];
			//rekha updated code dated on 31 aug 2018
			$basefolder = $Find_arr[1];	
			//end code 
			$DbRecDel = $DatabaseListObj->deleteDBDetails($Id);
			$file_arr = glob($basefolder."/".$FileN); 
			$file = $file_arr[0];
				if(is_file($file)){
					unlink($file); // delete file
				}			
			// end code
			}
		} // Loop ends here

		if ($DbRecDel) {
			$sessObj->createSession("displayMsg",'Database deleted Successfully.');
			//$sessObj->createSession("nextPage",$url_afterDelBillingCompany.$selection);
		} else {			
			$errDel = "Database Not Deleted";
		}
		$DbRecDel	=	false;
	}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List all Records
	//$dbrecords = $DatabaseListObj->adddbbackup('testing', '100', '19-06-2018', 'Rekha');
	$dbrecords = $DatabaseListObj->fetchAllPagingRecords($offset, $limit);
	$dbrecordsize    = sizeof($dbrecords);

	## -------------- Pagination Settings II -------------------
	$fetchdbrecords = $DatabaseListObj->fetchAllRecords();	// fetch All Records
	$numrows	=  sizeof($fetchdbrecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	//if ($editMode) $heading	= $label_editBillingCompany;
	//else	       $heading	= $label_addBillingCompany;

	//$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	//$ON_LOAD_PRINT_JS = "libjs/BillingCompanyMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDatabaseListMaster" action="DatabaseListMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>								
								<tr>
									<td colspan="3">
										
<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<input type="submit" name="cmdDelete" value="Delete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dbrecordsize;?>);">&nbsp;&nbsp;&nbsp;<a href="createdatabase_bakup.php"><input type="button" name="cmdAdd" value="Create Database Backup" class="button"></a> 
</td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="") {
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
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if (sizeof($dbrecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DatabaseListMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DatabaseListMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DatabaseListMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20" width='8%'>
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" width='8%'>Sr. No.</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" width='30%'>Database File Name</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" width='20%'>Database Size</th>
		<th align="center" style="padding-left:10px; padding-right:10px;" width='14%'>Date</th>
		<th style="padding-left:10px; padding-right:10px;" width='10%'>Created By</th>
		<th width='10%'>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?
		foreach ($dbrecords as $bcr) {
			$i++;
			$Id	= $bcr[0];
			$Db_filename = $bcr[1];
			$Dbsize = $bcr[2];
			$backuo_date_on = $bcr[3];
			$created_by		= $bcr[4];
			//$basefolder = "DailyDBBackup";
			//$basefolder = "F:/DailyBackup/Databases";
			//$Download_link = $basefolder."/".$Db_filename ; 
	//$filename2 = base64_encode("DailyDBBackup/".$Db_filename);
		$downloadFileurl = base64_encode($basefolder."/".$Db_filename);
		$Download_link = "DownloadFile.php?file=".$downloadFileurl ; 

	?>
	<tr>
		<td width="20" align="center">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$Id;?>" class="chkBox">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$i.").";?></td>

		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$Db_filename;?></td>
		<td align="center" style="padding-left:5px; padding-right:5px;"><?=$Dbsize ;?>
		
		</td>
		<td align="center" style="padding-left:5px; padding-right:5px;">
		<?=dateFormat($backuo_date_on); ?>
		</td>
		<td align="center" id="statusRow_<?=$i?>">
		<?=$created_by ?>
		</td>
		<td align="center">
			<a href='<?=$Download_link?>' target='_self' title='Click here to download backup file.' style='cursor:hand;'><strong>Download</strong></a>
		</td>

		</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<? if($maxpage>1){?>
		<tr>
		<td colspan="10" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DatabaseListMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DatabaseListMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DatabaseListMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</tbody>
	</table>
</td>
							</tr>

							</table>	
					
			</td>
			
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	</form>
	
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
    ?>
