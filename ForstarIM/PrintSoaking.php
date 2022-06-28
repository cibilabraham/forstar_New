<?
	require("include/include.php");
	$recordsFilterId=$g['selFilter'];
	$recordsDate=$g['selDate'];
	# List all Soaking 
	$soakingRecords	=	$soakingObj->soakingRecFilter($recordsFilterId, $recordsDate);
	$soakingDataSize		=	sizeof($soakingRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Soaking </td>
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
			if (sizeof($soakingRecords) > 0) {
				$i	=	0;
			?>
				<tr  bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">RM Lot ID</td>
					<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Fish</td>
                    <td class="listing-head" style="padding-left:10px; padding-right:10px;">Process Code </td>
                    <td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-IN<br>
					Type</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-IN<br>Count
					</td>
                    <td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-IN<br>Grade
					</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-IN<br>
					Qty</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-IN<br>
					Time</td> 
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-OUT<br>
					Count</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-OUT<br>Grade
					</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-OUT<br>
					Qty</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">SOAK-OUT<br>
					Time</td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Temperature<br></td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Gain(%)<br></td>
					<td class="listing-head" style="padding-left:10px; padding-right:10px;">Chemical
					<br>Used</td>
                    <td class="listing-head" style="padding-left:10px; padding-right:10px;">Chemical<br>
					QtY</td>
</tr>
<?php 
			
			foreach ($soakingRecords as $pr) {
				$i++;
				$soakingId	=	$pr[0];
				$rmlotid	=$pr[21];
				$fishName	=$pr[22];
				$ProcessCode	=$pr[23];
				$soakingEntryId=$pr[5];
			?>
                <tr  bgcolor="WHITE"> 
                        
				<td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$rmlotid?></td>
                        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$fishName;?></td>
                        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><?=$ProcessCode?>
				
                        <!--<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?//=$openingBalQty?></td>-->
			<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;">
				<?php if($pr[7]=="1")
						{
							echo "Count";
							$grade_in=''; $grade_out='';
						}
						elseif($pr[7]=="2")
						{
							echo "Grade";
							$grade1=$soakingObj->getGradeInOrOut($pr[9]);
							$grade_in=$grade1[0];
							$grade2=$soakingObj->getGradeInOrOut($pr[13]);
							$grade_out=$grade2[0];
						}
						?>
			</td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;" ><?=$pr[8]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$grade_in?></td>
						 <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[10]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[11]?> </td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[12]?></td>
						 <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$grade_out?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[14]?> </td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[15]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;"><?=$pr[16]?></td>
                        <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[17]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[24]?></td>
						<td class="listing-item" align="right" style="padding-left:2px; padding-right:2px; " ><?=$pr[19]?></td>
	</tr>
	<?
		}
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
