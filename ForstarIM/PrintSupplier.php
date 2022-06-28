<?
	require("include/include.php");
#List All Supplier

$supplierRecords	=	$supplierMasterObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >SUPPLIERS MASTER </td>
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
								<td colspan="2" >
								<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($supplierRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px">Code</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px" >Name</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px">Landing Centers </td>
<td class="listing-head" style="padding-left:5px; padding-right:5px">No. of Sub-Suppliers </td>
											</tr>
											<?

													foreach($supplierRecords as $fr)
													{
														$i++;
														$supplierId	=	$fr[0];
														$supplierName	=	stripSlash($fr[1]);
														$supplierCode	=	stripSlash($fr[2]);
														
														#Find the Grade from The procescode2grade TABLE
										$centerRecords	= $supplierMasterObj->fetchCenterRecords($supplierId);
														$displayCenter = "";
														//$grade			=	"";
														$j=0;
														foreach($centerRecords as $centerR)
															{
															$j++;
															$landingCenter	=	$centerR[5];
															if( $j>1 && $landingCenter!="") $displayCenter.=",";
															$displayCenter.="$landingCenter";
															}
#Find No.of Sub Suppliers
		$noOfSubSuppliers = $supplierMasterObj->getNumberOfSubSuppliers($supplierId);
														
														
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$supplierCode;?></td>
												<td class="listing-item" nowrap="nowrap" >&nbsp;&nbsp;<?=$supplierName;?></td>
											    <td class="listing-item" width="50" nowrap="nowrap">&nbsp;&nbsp;<?=$displayCenter?></td>
<td class="listing-item" width="50" nowrap style="padding-left:5px; padding-right:5px" align="center"><?=$noOfSubSuppliers?></td>

									        </tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
							  </td>
						  </tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>











			