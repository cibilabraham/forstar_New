<?
	require("include/include.php");

	#List All Category
	
	$categoryRecords				=	$fishcategoryObj->fetchAllRecords();
	$categoryRecordsSize			=	sizeof($categoryRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Category </td>
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
									<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
												if( sizeof($categoryRecords) > 0 )
												{
													$i	=	0;
											?>
                      <tr  bgcolor="#f2f2f2"  > 
                        <td width="20" align="center" class="listing-head">No</td>
                        <td class="listing-head" width="167" nowrap>&nbsp;&nbsp;Fish Type                        </td>                        
                        </tr>
                      <?

													foreach($categoryRecords as $cr)
													{
														
														$i++;
														$categoryId		=	$cr[0];
														$categoryName	=	stripSlash($cr[1]);
														
											?>
                      <tr  bgcolor="WHITE"  > 
                        <td width="20" align="center"><?=$i?></td>
                        <td class="listing-item" width="167" nowrap>&nbsp;&nbsp;<?=$categoryName;?></td>
                        </tr>
                      <?
													}
											?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
                      <?
												}
												else
												{
											?>
                      <tr bgcolor="white"> 
                        <td colspan="5"  class="err1" height="10" align="center">
                          <?=$msgNoRecords;?>                        </td>
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
