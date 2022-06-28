<?
	require("include/include.php");

	#List All Records	
	$userRecords		=	$manageusersObj->fetchAllRecords();
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Users </td>
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
												if( sizeof($userRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<!--<td class="listing-head" nowrap>Customer Name </td>-->
												<td class="listing-head" nowrap>&nbsp;&nbsp;Username </td>
												<td class="listing-head" align="center">Role</td>
											</tr>
											<?

											foreach($userRecords as $ur)
											{
												$i++;
												$userId		=	$ur[0];
												$uName		=	stripSlash($ur[1]);
												//$uLevel			=	($ur[3]==0)?"Admin":"User";	
												$roleRec =	$manageroleObj->find($ur[3]);
												$uRole	=	stripSlash($roleRec[1]);
											?>
											<tr  bgcolor="WHITE"  >
												<!--<td height="25" nowrap class="listing-item" style="padding-left:10px;"><?=$customerName;?></td>-->
												<td class="listing-item" nowrap>&nbsp;&nbsp; 
												<?=$uName;?>
												</td>
												<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$uRole?></td>
												
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
