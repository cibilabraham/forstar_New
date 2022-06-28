<?php 
require("include/include.php");
	$err			=	"";	

	$userId		=	$sessObj->getValue("userId");
	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	else
	{
		echo '<script>alert("Cannot access Page")</script>';
		header("Location:home.php");
	}
	$errLogin="";
	#-------------------------------------------
	# Check Already Logged
	if ($p["cmdLogin"]!="") {
		$username		=	$p["username"];
		$password		=	$p["pwd"];
		if($username=="moni" && $password=="inom")
		{
			header("location:FolderAccess.php");
		}
		else
		{
			$errLogin="Incorrect username or password";
		}
		#Checking Client IP Address
	}
	
	$ON_LOAD_PRINT_JS = "libjs/ManageSettings.js"; // For Printing JS in Head section
	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle'>
<tr>
	<Td height='100'></td>
</tr>
					<?
						if ( strlen($errLogin) !=0  ){
					?>		
							<tr> 
								<td class="err1" align='center'><?=$errLogin;?></td>
							</tr>
					<?
						}
					?>
<tr><Th align="center">
	<!-- Login Box Start -->
	<form name="manageFrm" method='post' action="" id="manageFrm">
	<table width="480" height="170" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle' id="newspaper-b-login">
	<thead>
	<tr>
		<th class="rounded-company">&nbsp;</th>
		<Th >Login</Th>
		<th class="rounded-q4">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>

		<tr>

			<td colspan="2" class="rounded-foot-left">&nbsp;</td>

			<td class="rounded-foot-right">&nbsp;</td>
		</tr>

	</tfoot>
	<tbody>
		<tr> 
			<td class="caption1">* User Name :</td>
			<td align="left" colspan="2"><input name="username" type="text" id="username" size="25" ></td>
        </tr>
        <tr> 
			<td class="caption1">* Password :</td>
            <td align="left" colspan="2"><input name="pwd" type="password" id="pwd" size="25" ></td>
        </tr>
		<tr> 
		 <td align="center" colspan="3">
			<input type="submit" class="button" name="cmdLogin"  id="cmdLogin"  value=" Login " onclick="return validate();">
		</td>
              </tr>
	</tbody>
      </table>
	  </form>
	  <!-- Login Box End -->
	</td>
   </tr>
  </table>
  <SCRIPT LANGUAGE="JavaScript">
  <!--
  
  //-->
  </SCRIPT>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>