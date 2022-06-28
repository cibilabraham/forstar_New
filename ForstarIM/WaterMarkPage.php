<?php
	require("include/include.php");
		//require_once('lib/WaterMarkPage_ajax.php');
	$selection 	=	"?pageNo=".$p["pageNo"]."&WaterMarkFilter=".$p["WaterMarkFilter"]."&WaterMarkFilterBillComp=".$p["WaterMarkFilterBillComp"]."&WaterMarkFilterUser=".$p["WaterMarkFilterUser"];
	/*$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selection 	= "?pageNo=".$p["pageNo"];*/

	/*list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
	$accesscontrolObj->getAccessControl($moduleId, $functionId);*/

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	//echo $role;exit;
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;	
	}
	#-----------------------------------------------------------------

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	
 if ($g["WaterMarkFilter"]!="") $WaterMarkFilterId = $g["WaterMarkFilter"];
	else $WaterMarkFilterId = $p["WaterMarkFilter"];

 if ($g["WaterMarkFilterBillComp"]!="") $WaterMarkFilterIdBillComp = $g["WaterMarkFilterBillComp"];
	else $WaterMarkFilterIdBillComp = $p["WaterMarkFilterBillComp"];

 if ($g["WaterMarkFilterUser"]!="") $WaterMarkFilterIdUser = $g["WaterMarkFilterUser"];
	else $WaterMarkFilterIdUser = $p["WaterMarkFilterUser"];

	if ($p["cmdSearch"]) $offset = 0;

	if ($p["hidWaterMarkFilterId"]!=$p["WaterMarkFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}
	#List all Records

	$WMrecords = $waterMarkObj->fetchAllPagingRecordsWM($offset, $limit, $WaterMarkFilterId,$WaterMarkFilterIdBillComp,$WaterMarkFilterIdUser);
	$WMrecordsize    = sizeof($WMrecords);

	## -------------- Pagination Settings II -------------------
	$fetchWMrecords = $waterMarkObj->fetchAllRecords($WaterMarkFilterId,$WaterMarkFilterIdBillComp,$WaterMarkFilterIdUser);	// fetch All Records
	$numrows	=  sizeof($fetchWMrecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	$WMCodeRecords = $waterMarkObj->fetchWaterMarkCode();
	$WMCodeRecordsBillComp = $waterMarkObj->fetchWaterMarkBillComp();
	$WMCodeRecordsUser = $waterMarkObj->fetchWaterMarkUser();
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmWaterMark" action="WaterMarkPage.php" method="post">
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
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									//if($errDel!="") {
								?>
								<!-- <tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr> -->
								<?
									//}
								?>
								<tr>
			<td colspan="10" align="center">
				<table  align="center" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px;padding-left:10px;padding-right:10px;" >
						<tr>
							<td  style="padding-left:2px;padding-right:2px;color:#669;" nowrap="true">Water Mark Code : </td>
							<td class="listing-item">
								<input type="text" name="WaterMarkFilter" id="WaterMarkFilter">
							</td>
							<!-- <td class="listing-item" >					
							<select name="WaterMarkFilter" id="WaterMarkFilter">
					<option value="">-- Select All --</option>
					<?
					foreach ($WMCodeRecords as $cr) {
						$WMCodeID	= $cr[0];						
						$WMCodeName	= stripSlash($cr[1]);
						$selected = "";
						if ($WaterMarkFilterId==$WMCodeName) $selected = "Selected";
					?>
					<option value="<?=$WMCodeName?>" <?=$selected?>><?=$WMCodeName?></option>
					<? }?>
					</select> 
	</td>	 -->
	<td>&emsp;</td>
	<td  style="padding-left:2px;padding-right:2px;color:#669;" nowrap="true">Billing Company : </td>
							<td class="listing-item" >					
							<select name="WaterMarkFilterBillComp" id="WaterMarkFilterBillComp"> <!-- onchange="this.form.submit();" -->
					<option value="">-- Select All --</option>
					<?
					foreach ($WMCodeRecordsBillComp as $cr) {
						$WMBillCompID	= $cr[0];
						$WMBillCompName	= stripSlash($cr[1]);
						$selected = "";
						if ($WaterMarkFilterIdBillComp==$WMBillCompName) $selected = "Selected";
					?>
					<option value="<?=$WMBillCompName?>" <?=$selected?>><?=$WMBillCompName?></option>
					<? }?>
					</select>
	</td>
	<td>&emsp;</td>
	<td  style="padding-left:2px;padding-right:2px;color:#669;" nowrap="true">User : </td>
							<td class="listing-item" >					
							<select name="WaterMarkFilterUser" id="WaterMarkFilterUser"> <!-- onchange="this.form.submit();" -->
					<option value="">-- Select All --</option>
					<?
					foreach ($WMCodeRecordsUser as $cr) {
						$WMUserID	= $cr[0];
						$WMUserName	= stripSlash($cr[1]);
						$selected = "";
						if ($WaterMarkFilterIdUser==$WMUserName) $selected = "Selected";
					?>
					<option value="<?=$WMUserName?>" <?=$selected?>><?=$WMUserName?></option>
					<? }?>
					</select>
	</td>
	<td style="padding-right:10px;padding-left:15px">
		<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search ">
	</td>	
	</tr>				
	</table>
			</td>
		</tr><tr><td>&nbsp;</td></tr>
								<tr>
									<td width="1" ></td>
		<td colspan="2" style="padding-left:10px; padding-right:10px;">
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if (sizeof($WMrecords) > 0) {
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
      				//$nav.= " <a href=\"WaterMarkPage.php?pageNo=$page\" class=\"link1\">$page</a> ";	
      				$nav.= " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\" class=\"link1\">$page</a> ";			
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			//$prev  = " <a href=\"WaterMarkPage.php?pageNo=$page\"  class=\"link1\"><<</a> ";
   			$prev  = " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			//$next = " <a href=\"WaterMarkPage.php?pageNo=$page\"  class=\"link1\">>></a> ";
   			$next = " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\"  class=\"link1\">>></a> ";
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
		<!--<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >ID</th>-->
		<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Water Mark Code</th>
		<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Wt Challan No</th>
		<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Billing Company</th>
		<!-- <th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Supplied At</th> -->
		<!-- <th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Wt Date/Time</th> -->
		<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >Print Date</th>
		<th align="center" nowrap style="padding-left:10px; padding-right:10px;" >User</th>
		</tr>
	</thead>
	<tbody>
	<?
		foreach ($WMrecords as $bcr) {
			$i++;
			//$idd = $bcr[0];
			$WMCode	= $bcr[1];
			$WMChallan = $bcr[2];
			$WMComp = $bcr[3];
			$WMSupplied = $bcr[4];
			$WMWtdate		= $bcr[5];
			$WMPrintdate =$bcr[6];
			$WMUser =$bcr[7];
			?>
	<tr>
		<!--<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$idd ;?>	</td>-->
		<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMCode ;?>	</td>
		<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMChallan;?></td>
		<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMComp ;?>	</td>
	<!-- 	<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMSupplied; ?>	</td> -->
		<!-- <td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMWtdate ?>	</td> -->
		<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMPrintdate ?>	</td>
		<td align="left" nowrap style="padding-left:10px; padding-right:10px;"><?=$WMUser ?>	</td>
		</tr>
	<?
		}
	?>
	<!-- <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" > -->
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
      				//$nav.= " <a href=\"WaterMarkPage.php?pageNo=$page\" class=\"link1\">$page</a> ";			
      				$nav.= " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\" class=\"link1\">$page</a> ";	
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			//$prev  = " <a href=\"WaterMarkPage.php?pageNo=$page\"  class=\"link1\"><<</a> ";
   			$prev  = " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			//$next = " <a href=\"WaterMarkPage.php?pageNo=$page\"  class=\"link1\">>></a> ";
   				$next = " <a href=\"WaterMarkPage.php?pageNo=$page&WaterMarkFilter=$WaterMarkFilterId&WaterMarkFilterBillComp=$WaterMarkFilterIdBillComp&WaterMarkFilterUser=$WaterMarkFilterIdUser\"  class=\"link1\">>></a> ";
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
		<td colspan="10"  class="err1" height="10" align="center" style="color: red"><?=$msgNoRecords;?></td>
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
