<?
	require("include/include.php");
#List All IP Address

	$ipAddressRecords			=	$manageipaddressObj->fetchAllRecords();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Manage IP Address </td>
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
								<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
												if( sizeof($ipAddressRecords) > 0 )
												{
													$i	=	0;
											?>
                      <tr  bgcolor="#f2f2f2"  > 
                        <td class="listing-head" nowrap>&nbsp;&nbsp;IP Address </td>
                        <td class="listing-head" align="center">Description</td>
						<? if($edit==true){?>
                        <? }?>
                      </tr>
                      <?

													foreach($ipAddressRecords as $ipar)
													{
														
														$i++;
														$ipAddressId		=	$ipar[0];
														$IPAddress		=	stripSlash($ipar[1]);
														$description	= $ipar[2];
														
											?>
                      <tr  bgcolor="WHITE" > 
                        <td height="25" nowrap class="listing-item">&nbsp;&nbsp; 
                          <?=$IPAddress;?>                        </td>
                        <td class="listing-item" nowrap>&nbsp;&nbsp;<?=$description?></td>
						<? if($edit==true){?>
                        <? }?>
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
                        <td colspan="5"  class="err1" height="10" align="center"> 
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











			