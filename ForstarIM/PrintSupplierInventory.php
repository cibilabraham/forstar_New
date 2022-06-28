<?php
	require("include/include.php");

	$sectionFilter = $g["sectionFilter"];

	//echo "The value of $sectionFilter";
	if ($sectionFilter=="0")
	{
		$msgNoRecords="Access Denied for Print";
	}
	# List all Supplier
	else{
	$supplierRecords	=	$supplierMasterObj->fetchAllRecordsPrint($sectionFilter);
	$supplierSize		=	sizeof($supplierRecords);
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
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
								<td colspan="2" style="padding-left:5px;padding-right:5px;">
<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
			if (sizeof($supplierRecords)>0) {
				$i	=	0;
		?>
		<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Code</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
<td class="listing-head" style="padding-left:5px; padding-right:5px;">Phone</td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Landing<br>Centers </td>
<td class="listing-head" style="padding-left:5px; padding-right:5px">No.of<br>Sub-Supp</td>
				</tr>
	<?
	foreach($supplierRecords as $sr) {
		$i++;
		$supplierId		= $sr[0];
		$supplierCode		= stripSlash($sr[1]);
		$supplierName		= stripSlash($sr[2]);
		$address		= $sr[3];
		$phoneNo		= $sr[4];
		$vatNo			= $sr[5];
		$cstNo			= $sr[6];

		$frozenChk		= $sr[7];
		$inventoryChk		= $sr[8];
		$rteChk			= $sr[9];
		$centerRecords 		= "";
		$noOfSubSuppliers 	= "";
		if ($frozenChk=='Y') {
			#Find the Grade from The procescode2grade TABLE
			$centerRecords	= $supplierMasterObj->fetchCenterRecords($supplierId);
			#Find No.of Sub Suppliers
			$noOfSubSuppliers = $supplierMasterObj->getNumberOfSubSuppliers($supplierId);
		}
	?>
<tr  bgcolor="WHITE">
<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplierCode;?></td>
<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$supplierName;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$phoneNo?></td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px">
		<table>
				<tr>
				<?
					$numLine = 3;
					if (sizeof($centerRecords)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($centerRecords as $centerR) {
							$j++;
							$landingCenter = $centerR[5];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$landingCenter?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<? 
						}	
					 }
					}
				?>
				</tr>
		</table>
</td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?=$noOfSubSuppliers?></td>		
</tr>
											<?
												}
											?>
												
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="11" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierMaster.php?pageNo=$page&sectionFilter=$sectionFilter\"  class=\"link1\">>></a> ";
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
											<tr bgcolor="white">
												<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
						
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
