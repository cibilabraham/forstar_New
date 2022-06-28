<?
	require("include/include.php");

	#List All Units

	$unitRecords				=	$unitmasterObj->fetchAllRecords();
	$unitRecordsSize			=	sizeof($unitRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;UNIT MASTER  </td>
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
									<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($unitRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
												<td class="listing-head" nowrap >&nbsp;&nbsp;Unit </td>
												<td class="listing-head" nowrap="nowrap" align="center" > Available for </td>
											</tr>
											<?

													foreach($unitRecords as $ur)
													{
														
														$i++;
														$unitId			=	$ur[0];
														$unit			=	stripSlash($ur[1]);
														$unitReceived	=	$ur[2];
														if($unitReceived=='G'){
														$displayStatus	=	"Grade";
														} else if($unitReceived=='C'){
														$displayStatus	=	"Count";
														}
														else {
														$displayStatus	=	"Grade & Count";
														}
														
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item"nowrap >&nbsp;&nbsp;<?=$unit;?></td>
												<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$displayStatus?></td>
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
