<?php
	require("include/include.php");

	# List all Category ;
	$ingredientRateListRecords		=	$ingredientRateListObj->fetchAllRecords();
	$ingredientRateListRecordSize		=	sizeof($ingredientRateListRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Rate List Master</td>
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
								<td colspan="2" style="padding:10px;" >
			<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
				if ( sizeof($ingredientRateListRecords) > 0 ) {
					$i	=	0;
			?>
			<thead> 		 
                      <tr bgcolor="#f2f2f2">
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
                        <th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>			
                      </tr>
	</thead>
	<tbody>
                      <?
			foreach ($ingredientRateListRecords as $prl) {
				$i++;
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		= 	dateFormat($prl[2]);
			?>
                      <tr bgcolor="White">
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
                        <td class="listing-item" style="padding-left:10px; padding-right:10px;" align="center"><?=$startDate?></td>
                      </tr>
                      <?
			}
			?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value=""> 		 
                       <?
				} else {
			?>
                      <tr> 
                        <td colspan="2"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
                      </tr>
                      <?
			}
			?>
	</tbody>
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
