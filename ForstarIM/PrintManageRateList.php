<?php
	require("include/include.php");

	#List All Records
	$functionFilterId = $g["functionFilter"];
	$rateListRecords	=	$manageRateListObj->fetchAllRecords($functionFilterId);
	$rateListRecordSize	=	sizeof($rateListRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="95%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3" >
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Rate List Master</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<thead>
                <?php
			if( sizeof($rateListRecords) > 0 ) {
				$i	=	0;
		?>
                      <tr  bgcolor="#f2f2f2">
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
			<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Function </th>
                      </tr>
	</thead>
	<tbody>
                      <?php
			$functionName = "";
			foreach ($rateListRecords as $rl) {
				$i++;
				$rateListId	= $rl[0];
				$rateListName	= stripSlash($rl[1]);				
				$startDate	= dateFormat($rl[2]);
				$pageType	= $rl[3];
				$functionName	= $masterRateListPages[$pageType];				
			?>
                      <tr  bgcolor="WHITE">
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px; text-align:center;"><?=$startDate?></td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$functionName?></td>			
                      </tr>
                      <?
			}
			?>
        	        <?
				} else {
			?>
                      <tr> 
                        <td colspan="5"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?php
				}
			?>
		</tbody>
                    </table>									</td>
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
