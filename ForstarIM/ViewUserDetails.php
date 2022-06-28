<?php
require("include/include.php");
$datasList = array();
$userId =$_REQUEST['userId'];
if($userId != '' && $userId != 0)
{
	$roles =	$manageusersObj->findDetails($userId);
}
?>
<html>
<head>
<title>WEIGHMENT DATA SHEET DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<?php 
if(sizeof($roles) > 0)
{
?>
<form name="frmPrintDailyCatchReportMemo">
<!--table width="65%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table-->

	<table width='40%'  border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
			<tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head"> Role </td>
			</tr>
			<?php if (sizeof($roles)>0)
			{
				foreach ($roles as $rr) 
				{
					$roleRec =	$manageroleObj->find($rr[5]);
					$uRole	=	stripSlash($roleRec[1]);
				?>
			<tr align="center"  bgcolor="#fff">
				<td class="listing-item"><?=$uRole?></td>
			</tr>
			<?php
				}
			}
			?>
								
		</table>
		<br/>
											
	</table>
	



</form>
<?php
}
?>
</body>
</html>