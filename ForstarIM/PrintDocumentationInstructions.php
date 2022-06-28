<?
	require("include/include.php");

	# List all Category ;
	$docInstructionRecords=$docInstructionsObj->fetchAllRecords();
	$docInstructionRecordsSize		=	sizeof($docInstructionRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
Documentation Instructions</td>
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
												if($docInstructionRecordsSize > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
												<th nowrap style="padding-left:10px; padding-right:10px;">Required</th>	
											</tr>
											<?
													foreach($docInstructionRecords as $icmR)
													{
														$i++;
														$docInstructionId = $icmR[0];
														$name	=	stripSlash($icmR[1]);
														$requiredVal	=	stripSlash($icmR[2]);
														$required=($requiredVal=='Y')?"YES":"NO";
														$active= $icmR[3];
			
											?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="center"><?=$required;?></td>	
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
