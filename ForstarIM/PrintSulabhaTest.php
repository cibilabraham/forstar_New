<?
	require("include/include.php");

	# List all Departments 
	$employeeRecords	=	$sulabhaTestObj->fetchAllRecords();
	$employeeRecSize		=	sizeof($employeeRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Employee master</td>
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
			if (sizeof($employeeRecords) > 0) {
				$i	=	0;
			?>
											<tr  bgcolor="#f2f2f2" align="center">
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Name</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;">Designation</th>
												<th class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>Department </th>
												
                                            </tr>
<?
	foreach($employeeRecords as $cr) {
		$i++;
		 $employeeId = $cr[0];
		 $name = stripSlash($cr[1]);
		 $designation =	stripSlash($cr[2]);
		 $department = stripSlash($cr[3]);
		 $active = $cr[4];
		
	?>
	<tr  bgcolor="WHITE">
	
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;text-align:center;"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;text-align:center;"><?=$designation;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;text-align:center;"><?=$department;?></td>
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
