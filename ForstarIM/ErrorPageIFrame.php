<?
	require("include/include.php");
	
	# Include Template [topLeftNav.php]
	require("template/btopLeftNav.php");

?>
	<form name="frmErrorIFrameMaster" action="ErrorPageIFrame" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="50%">
			<tr>
			<td height="40" align="center" class="err1" >&nbsp;</td>
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
					  <!-- Form fields start --></td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<?
			}
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" align="center" >&nbsp;Sorry!!! Access Denied </td>
								</tr>
								
								<?
									if($errDel!="")
									{
								?>
								
								<?
									}
								?>
								
								
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
		
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	//require("template/bottomRightNav.php");
?>