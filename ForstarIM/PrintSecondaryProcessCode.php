<?
	require("include/include.php");
#List All Process Codes

$secondaryProcessCodeRecords	=	$secondaryProcessCodeObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >SECONDARY PROCESS CODE  MASTER </td>
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
												if( sizeof($secondaryProcessCodeRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
											  <th class="listing-head" align="center">Name</td>
											  <th class="listing-head" align="center" >Secondary Grade</th>
											<th class="listing-head" align="center" nowrap>Process Code </th>
											<th class="listing-head" align="center" >Grade</th>
											<th class="listing-head" align="center" >Percentage</th>
											</tr>
											<?

													foreach($secondaryProcessCodeRecords as $spr)
													{
														$i++;
														$secondaryProcessId		=	$spr[0];
												$name	=	stripSlash($spr[1]);
												$active=$spr[2];
												$secondaryGrade=$spr[3];
												$secGrade=$secondaryProcessCodeObj->findSecondaryGrade($secondaryGrade);
												$secondaryEntryRecs=$secondaryProcessCodeObj->getSecondaryProcessEntry($secondaryProcessId);
											?>
											<tr  bgcolor="#FFFFFF">
											  <td class="listing-item" nowrap align="center">&nbsp;&nbsp;<?=$name;?></td>
											  <td class="listing-item" nowrap >&nbsp;&nbsp;<?=$secGrade;?></td>
												<td class="listing-item" nowrap align="center">
												<? 
													foreach($secondaryEntryRecs as $secondaryEntry)
													{
														$processCode=$secondaryEntry[5];
														echo $processCode.'<br/>';
													}
													?></td>
													<td class="listing-item" nowrap align="center">
													<? 
													foreach($secondaryEntryRecs as $secondaryEntry)
													{
														$processCode=$secondaryEntry[6];
														echo $processCode.'<br/>';
													}
													?></td>
													<td class="listing-item" align="center">
													<? 
													foreach($secondaryEntryRecs as $secondaryEntry)
													{
														$percentage=$secondaryEntry[4];
														echo $percentage.'<br/>';
													}
													?>
												</td>
												
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="editSelectionChange" value="0">
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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











			