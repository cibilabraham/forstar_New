<?php
	require("include/include.php");
	
	# List All Records
	$dashBoardRecords	= $dashboardManagerObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Manage Dashboard</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;padding-top:10px;padding-bottom:10px;">
								<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?php
			if (sizeof($dashBoardRecords)>0) {
			$i	=	0;
			?>
                      <tr  bgcolor="#f2f2f2" align="center">
                        <td class="listing-head" nowrap>Role</td>
                        <td class="listing-head" align="center">Access</td>			
                      </tr>
                      <?php
			foreach ($dashBoardRecords as $dbr) {
				$i++;
				$dashBoardEntryId = $dbr[0];
				$selRoleId	  = $dbr[1];
				# Get Dash Board Records
				$getDashboardAccessRecords = $dashboardManagerObj->dashboardAccessRecords($selRoleId);
				$selRoleName	= $dbr[3];
			?>
                      <tr  bgcolor="WHITE" >
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selRoleName;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
				<table>
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($getDashboardAccessRecords)>0) {
						$nextRec	=	0;
						$k=0;
						$vatPercent = "";
						foreach ($getDashboardAccessRecords as $cR) {
							$j++;
							$dashboardType = $cR[0];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$dashBoardFunctions[$dashboardType]?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}
				?>
				</tr>
			</table>
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
                        <td colspan="2"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
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











			