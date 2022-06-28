<?php
	require("include/include.php");

	#List All Records	
	$mcPkgWtRecords		= $mcPkgWtMasterObj->fetchAllRecords();
	$mcPkgWtRecordSize	= sizeof($mcPkgWtRecords);
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;MC Packing Wt Master</td>
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
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?php
			if (sizeof($mcPkgWtRecords)) {
				$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Net Wt<br/>(Gm)</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">MC<br> Pack</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Package Wt<br/>(Kg)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Pkg Wt <br/>Tolerance<br> [+/-] (Gms)</td>	
	</tr>
			<?php			
			foreach ($mcPkgWtRecords as $mpwr) {	
				$i++;
				$mcPkgWtEntryId 	= $mpwr[0];
				$mcPackingCode	= $mpwr[3];	
				$packageWt	= $mpwr[2];
				$netWtUnit	= $mpwr[4];
				$name		= $mpwr[5];
				$mcPkgWtTolerance = $mpwr[6];
			?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$name;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$netWtUnit;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$mcPackingCode;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$packageWt;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=($mcPkgWtTolerance!=0)?$mcPkgWtTolerance:"";?></td>	
	</tr>
		<?			
			}
		?>
		<?
			} else {
		?>
		<tr bgcolor="white">
			<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
