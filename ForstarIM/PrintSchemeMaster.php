<?
	require("include/include.php");


	#List All Records
	$schemeMasterResultSetObj = $schemeMasterObj->fetchAllRecords();
	$schemeMasterRecordSize	= $schemeMasterResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Scheme Master</td>
							</tr>
							<tr>
								<td colspan="3" height="15" ></td>
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
<table cellpadding="2"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($schemeMasterRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Buy</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Based On</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Get</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Based On</td>	
	</tr>
	<?	
/*
BUY <SELECT/ENTER HOW MANY> BASED ON <PRODUCT / MRP> 
IF PRODUCT BASED <ALL PRODUCTS / SELECT ONE OR MORE PRODUCTS>
IF MRP BASED <SELECT MRP 89 / MRP 75 / ETC. > based on the product mrp list
GET <SELECT/ENTER HOW MANY> BASED ON <PRODUCT / MRP> 
IF PRODUCT GET WHAT <SELECT MRP PRODUCT / SAMPLE PRODUCT>
IF MRP PRODUCT BASED THEN <GROUP / IND PRODUCTS>
IF MRP GROUP BASED THEN <SAME MRP / LESS MRP>
AND <SELECT MRP 89 / MRP 75 / ETC. > based on the product mrp list
IF MRP IND PRODUCT BASED <ALL PRODUCTS / SELECT ONE OR MORE PRODUCTS>
IF SAMPLE PRODUCT <SELECT SAMPLE PRODUCT FROM LIST>	
*/
		while ($smr=$schemeMasterResultSetObj->getRow()) {
			$i++;
			$schemeMasterId = $smr[0];
			$schemeName	= $smr[1];
			$buyNum		= $smr[2];
			$buyBasedOn	= $smr[3];
			$selMrp		= $smr[4];
			$chkAllProduct  = "";
			if ($buyBasedOn=='P') {
				$chkAllProduct = $schemeMasterObj->chkAllProductSelectedRec($schemeMasterId, $buyBasedOn);
				$selAllProduct = "";
				if ($chkAllProduct) $selAllProduct = "- All Products";				
			}
			$displayBuyBasedOn = "";
			if ($buyBasedOn=='P')  $displayBuyBasedOn = "PRODUCT&nbsp;$selAllProduct";
			else if ($buyBasedOn=='M') $displayBuyBasedOn = "MRP&nbsp;$selMrp";
			
			$getNum		= $smr[5];
			$getProductType = $smr[6];
			$sampleProductId = $smr[10];
			$displayGetProductType = "";
			if ($getProductType=='MP') {
				$getMrpProductType = $smr[7];
				$disMrpProductType = "";
				if ($getMrpProductType=='G') {
					$getMrpGroupType = $smr[8];
					$selGroupMrp = $smr[9];
					$disMrpGroupType = "";
					if ($getMrpGroupType=='SM') $disMrpGroupType = "SAME MRP"; 
					else if ($getMrpGroupType=='LM') $disMrpGroupType = "LESS MRP"; 
					$disMrpProductType = "GROUP&nbsp;-&nbsp;$disMrpGroupType&nbsp;-&nbsp;$selGroupMrp";
				}
				else if ($getMrpProductType=='I') {
					$chkIndProduct = $schemeMasterObj->chkAllProductSelectedRec($schemeMasterId, $getMrpProductType);
					$selIndProduct = "";
					if ($chkIndProduct) $selIndProduct = "&nbsp;- All Products";			
				
					$disMrpProductType = "INDIVIDUAL $selIndProduct";
				}
				$displayGetProductType = "MRP PRODUCT - $disMrpProductType";
			}else if ($getProductType=='SP') {
				$sampleProductRec	= $sampleProductMasterObj->find($sampleProductId);
				$sampleProductName	= stripSlash($sampleProductRec[2]);
				$displayGetProductType = "SAMPLE PRODUCT - $sampleProductName";
			}
						
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$schemeName;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$buyNum;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">		
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD class="listing-item"><?=$displayBuyBasedOn;?></TD>
					<TD class="listing-item"><? if (!$chkAllProduct) $buyAllProduct = $schemeMasterObj->listSelProduct($schemeMasterId, 'P');?></TD>
				</TR>
			</table>
		</td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="cnter"><?=$getNum;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left">
			<table cellpadding="0" cellspacing="0" align="left">
				<TR>
					<TD class="listing-item" nowrap><?=$displayGetProductType;?></TD>
					<TD class="listing-item"><? if (!$chkIndProduct) $getIndProduct = $schemeMasterObj->listSelProduct($schemeMasterId, 'I');?></TD>
				</TR>
			</table>				
		</td>		
	</tr>
	<?
		
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
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
