<?php
	require("include/include.php");

	#List All Records
	$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();
	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Billing Company Master</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
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
	<td colspan="2" style="padding-left:10px; padding-right:10px;">
<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($billingCompanyRecords) > 0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Address</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Tel. No.</td>		
	</tr>
	<?
		foreach ($billingCompanyRecords as $bcr) {			
			$billingCompanyId	= $bcr[0];
			$cName			= $bcr[1];
			$cAddress		= $bcr[2];
			$CPlace			= $bcr[3];
			$cPinCode		= $bcr[4];
			$cCountry		= $bcr[5];
			$telNo			= $bcr[6];
			$faxNo			= $bcr[7];	

			$displayAddress		= "";
			if ($cAddress)	$displayAddress .= $cAddress;
			if ($CPlace)	$displayAddress .= "<br>".$CPlace;
			if ($cPinCode)	$displayAddress .= "<br>".$cPinCode;
			if ($cCountry)	$displayAddress .= "<br>".$cCountry;
			$contactDetails = $billingCompanyObj->displayContactDtls($billingCompanyId);
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$cName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayAddress;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?php
			foreach($contactDetails as $cdt)
			 {
				echo $cdt[1].'<br/>';
			 }
			?><?/*=$telNo;*/?>
		</td>	
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="3" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"BillingCompanyMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"BillingCompanyMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr bgcolor="white">
		<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
