<?
	require("include/include.php");

	#List All Records	
	$brandRecords		=	$brandObj->fetchAllRecords();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Brands </td>
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
									<td colspan="2" >
										<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($brandRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<!--<td class="listing-head" nowrap>Customer Name </td>-->
												<td class="listing-head" >Brand</td>
												<? if($edit==true){?>
												<? }?>
											</tr>
											<?

													foreach($brandRecords as $br)
													{
														$i++;
														$brandId		=	$br[0];
														//$customerName	=	stripSlash($br[1]);
														//$customerName	=	$customerObj->findCustomer($br[1]);
														$brandName		=	stripSlash($br[1]);
														//$indainAgent	=	stripSlash($br[3]);
											?>
											<tr  bgcolor="WHITE"  >
												<!--<td height="25" nowrap class="listing-item" style="padding-left:10px;"><?=$customerName;?></td>-->
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px;"><?=$brandName;?></td>
												<? if($edit==true){?>
											  <? }?>
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
												<td colspan="2"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
