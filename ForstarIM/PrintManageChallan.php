<?php
	require("include/include.php");
	
	$filterFunctionType=$g["filterFunctionType"];
	$idGenRecords	= $manageChallanObj->fetchAllRecords($filterFunctionType);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Manage Challan </td>
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
	<td colspan="2" style="padding: 10 10 10 10px; ">
<!--  style="padding-left:10px; padding-right:10px; "-->
		<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?php
				if (sizeof($idGenRecords) > 0) {
					$i	=	0;
			?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" rowspan="2">Function Name</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Date</td>
                        <td class="listing-head" style="padding-left:10px; padding-right:10px;" colspan="2">Number</td>		
			<td class="listing-head" style="padding-left:10px; padding-right:10px;" rowspan="2">Delayed Entry Limit(Days)</td>	
                      </tr>
			<tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">From</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">To</td>	
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">From</td>
				<td class="listing-head" style="padding-left:10px; padding-right:10px;">To</td>
			</tr>
                      <?php
			foreach($idGenRecords as $igr) {
				$i++;
				$numberGenId	= $igr[0];
				$fType		= $igr[1];
				$startDate	= ($igr[2]!="")?dateFormat($igr[2]):"";
				$endDate	= ($igr[3]!="")?dateFormat($igr[3]):"";
				$startNo	= $igr[4];
				$endNo		= $igr[5];
				$selBillingCompany =  $igr[9];
				if ($selBillingCompany!=0) {
					$billingCompanyRec = $billingCompanyObj->find($selBillingCompany);
					$billingCompanyName = $billingCompanyRec[1];
				}
				$delayedEntryLimitDays = $igr[10];

				$soInvType	= $igr[11];
			?>
                      <tr  bgcolor="WHITE" >
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$idGenFunctions[$fType];?>
				<? if ($soInvType!="") {?>
					<br/><span class="fieldName" style="line-height:normal;font-size:9px;">(<?=$invoiceTypeRecs[$soInvType]?>)</span>
				<? }?>
				<?php
				if ($filterFunctionType!='RM' && $selBillingCompany!=0) {
				?>
					<br/><span class="fieldName" style="line-height:normal;font-size:9px;">(<?=$billingCompanyName?>)</span>
				<?php
				}
				?>
			</td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$startDate?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$endDate?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$startNo?></td>
			 <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$endNo?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=($delayedEntryLimitDays!=0)?$delayedEntryLimitDays:"";?></td>
                      </tr>
                      	<?php
				}
			?>
                      	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      	<input type="hidden" name="editId" value="">
		  	<input type="hidden" name="editSelectionChange" value="0">
                      	<?php
				} else {
			?>
                      <tr bgcolor="white"> 
                        <td colspan="6"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      	<?php
				}
			?>
                    </table>
							  </td>
						  </tr>	
	<tr><TD>&nbsp;</TD></tr>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>











			