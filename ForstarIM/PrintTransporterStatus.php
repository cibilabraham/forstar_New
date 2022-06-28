<?php
	require("include/include.php");

	#List All Records
	$transporterStatusRecords = $transporterStatusObj->fetchAllRecords();
	$transporterStatusRecordSize	= sizeof($transporterStatusRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Management</td>
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
	<td colspan="2" style="padding-left:5px; padding-right:5px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($transporterStatusRecordSize) {
			$i = 0;
	?>

	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transporter</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">From</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">To</td>	
		
	</tr>
	<?php	
	foreach ($transporterStatusRecords as $tsr) {
		$i++;
		$transporterStatusId		= $tsr[0];
		$transporterName		= $tsr[4];
		$fromDate	= dateFormat($tsr[2]);
		$tillDate	= dateFormat($tsr[3]);	
		
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$transporterName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$fromDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$tillDate;?></td>		
	</tr>
	<?
	
		}
	?>
		<?
			} else {
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
