<?php
	require("include/include.php");

	#List All Records	
	$packingGroupRecords	= $packingGroupMasterObj->fetchAllRecords();
	$packingGroupRecordSize	= sizeof($packingGroupRecords);
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Group Master</td>
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
		<?
		if (sizeof($packingGroupRecords)) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Group</td>
<? if($edit==true){?>
		<td class="listing-head"></td>
<? }?>
			</tr>
			<?php			
			foreach ($packingGroupRecords as $pgr) {	
				$i++;
				$packingGroupId = $pgr[0];
				$pLSel		= explode(",",$pgr[1]);
				$pStateRecL	= $productStateObj->find($pLSel[0]);		
				$pStateNameL	= stripSlash($pStateRecL[1]);
				$pGroupRecL	= $productGroupObj->find($pLSel[1]);
				$pGroupNameL	= ($pGroupRecL[1]!="")?stripSlash($pGroupRecL[1]):"No Group";
				$pNetWtL	= $pLSel[2];

				$pRSel		= explode(",",$pgr[2]);
				$pStateRecR	= $productStateObj->find($pRSel[0]);		
				$pStateNameR	= stripSlash($pStateRecR[1]);
				$pGroupRecR	= $productGroupObj->find($pRSel[1]);
				$pGroupNameR	= ($pGroupRecR[1]!="")?stripSlash($pGroupRecR[1]):"No Group";
				$pNetWtR	= $pRSel[2];
				$displayPkgGroup ="";
				$displayPkgGroup = "$pStateNameL,&nbsp;$pGroupNameL,&nbsp;$pNetWtL&nbsp;<b>=</b> &nbsp;$pStateNameR,&nbsp;$pGroupNameR,&nbsp;$pNetWtR";
				
				
				
			?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="left"><?=$displayPkgGroup;?></td>				
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingGroupId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='PackingGroupMaster.php';" ></td>
<? }?>
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
												<td colspan="1"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
