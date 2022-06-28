<?php
	require("include/include.php");

	#List All Records	
	$transporterRecords	= $transporterMasterObj->fetchAllRecords();
	$transporterRecordSize  = sizeof($transporterRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Data</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?php
								if($errDel!="") {
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
	<tr>
		<td width="1" ></td>
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
		if ($transporterRecordSize) {
			$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Tel No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Bill No<br>Required</td>
	</tr>
	<?php
	foreach ($transporterRecords as $tr) {
		$i++;
		$transporterId	= $tr[0];		
		$name 	= stripSlash($tr[2]);	
		$telNo  = stripSlash($tr[5]);	
		$billRequired = ($tr[9]=='Y')?"YES":"NO";
	?>
	<tr bgcolor="WHITE">	
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$telNo;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$billRequired;?></td>	
	</tr>
		<?php
			}
		?>

		<?php
			} else {
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
