<?
	require("include/include.php");

	#List All Plants
	
	$refreshTimeLimitRecords	=	$refreshTimeLimitObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Refresh Time Limit</td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;" >
	<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
					<?
					if( sizeof($refreshTimeLimitRecords) > 0 ) {
						$i	=	0;
					?>
		<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Sub-Module</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Function</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Time (Seconds)</td>
		</tr>
		<?php
		foreach ($refreshTimeLimitRecords as $pr) {
			$i++;
			$refreshTimeLimitId	= $pr[0];				
			$selSubModuleIds	= stripSlash($pr[1]);
			$sRefreshTime		= $pr[2];
			$selFunctionName	= $pr[3];	
			$selSubModuleName	= $pr[4];
		?>
		<tr  bgcolor="WHITE"  >			
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selSubModuleName?>
			<!--		<table>
				<tr>
				<?
				/*
				$subModuleRecDisplayRow	=	2;
				if (sizeof($selSubModuleRecords)>0) {
					$sModuleNext	=	0;
					foreach ($selSubModuleRecords as $sModuleR) {
						$subModuleName	=	$sModuleR[1];
						$sModuleNext++;
				*/
				?>
				<td class="listing-item" nowrap><?// if($sModuleNext>1) echo ",";?><?//=$subModuleName?></td>
				<? 
				//if ($sModuleNext%$subModuleRecDisplayRow == 0) {
				?>
					</tr>
					<tr>
				<?
				/* 
					}	
				}
			}
				*/
			?>
			</tr>
			</table>-->
			</td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selFunctionName;?></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$sRefreshTime;?></td>			
		</tr>
			<?
			}
			?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">		
											<?
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
