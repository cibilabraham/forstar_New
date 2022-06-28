<?php
	require("include/include.php");

	#List All 	
	$checkPointRecords	=	$checkPointObj->fetchAllRecords()
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Check Point</td>
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
	<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($checkPointRecords) > 0 )
												{
													$i	=	0;
											?>


			<tr  bgcolor="#f2f2f2" align="center">
				<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Description</td>
		</tr>
		<?
		foreach($checkPointRecords as $pr)
			{
				$i++;
				$processingActivityId		=	$pr[0];
				$name		=	stripSlash($pr[1]);
				$description	=	stripSlash($pr[2]);
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$description;?></td>
			
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
