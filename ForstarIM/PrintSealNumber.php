<?
	require("include/include.php");

	# List all Departments 
	$sealNumberRecords	=	$sealNumberObj->fetchAllRecords();
	$sealNumberSize		=	sizeof($sealNumberRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Seal Number</td>
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
			if (sizeof($sealNumberRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Seal Number</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Purpose </th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Change Status </th>
</tr>
<?
	foreach($sealNumberRecords as $cr) {
		$i++;
		 $sealNumberId		=	$cr[0];
		 $sealNo		=	stripSlash($cr[1]);
		 $status	=	stripSlash($cr[2]);
		 $purpose		=	stripSlash($cr[3]);
		 $changeStatus		=	stripSlash($cr[4]);
		 $active=$cr[5];
		 $existingrecords=$cr[6];
			
		
	?>
	<tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$sealNo;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$status;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$purpose;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$changeStatus;?></td>
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
