<?php
	require("include/include.php");


	$unitGroupFilterId = $g["unitGroupFilter"];
	# List all Category ;
	$stockItemUnitRecords	=	$stockItemUnitObj->fetchAllRecords($unitGroupFilterId);
	$stockItemUnitSize	= sizeof($stockItemUnitRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Unit</td>
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
									<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
								<?
								if ( sizeof($stockItemUnitRecords) > 0) {
									$i	=	0;
								?>
		
		<tr  bgcolor="#f2f2f2" align="center">			
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Group </td>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Description </td>
		</tr>
		<?
		foreach ($stockItemUnitRecords as $siur) {
			$i++;
			$stockItemUnitId = $siur[0];
			$unitName	 = stripSlash($siur[1]);
			$description     = stripSlash($siur[2]);
			$unitGroupName   = $siur[4];			
		?>
		<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unitName;?></td>		
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$unitGroupName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$description?></td>			
		</tr>
		<?
			}
		?>
	
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
