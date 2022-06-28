<?php
	require("include/include.php");
	$err			= "";
	$errDel			= "";	
	
	$transporterNavArr = array("0"=>"TransporterMaster.php", "1"=>"TransporterRateList.php");
	
	# Include Template [topLeftNav.php]
	require("template/btopLeftNav.php");
?>
<form name="frmTransporterNavPage" id="frmTransporterNavPage" action="TransporterNavPage.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="100%">	
	<tr><TD>
		<table width="100%">
			<TR>
				<TD style="padding-left:10px; padding-right:5px;" align="left">Prev</TD>
				<TD style="padding-left:5px; padding-right:10px;" align="right">Next</TD>
			</TR>
		</table>
	</TD></tr>	
	<!--<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td bgcolor="white">						
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">							
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">			
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
	</table>
  	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>	
		</td>
	</tr>-->
	<input type="text" name="pageNav" id="pageNav">
  </table>
</form>
	
