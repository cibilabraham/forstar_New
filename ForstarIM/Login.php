<?php 
require("include/includeLogin.php");
	$err			=	"";	


	#-------------------------------------------
 	#Getting Clent & Server IP Address
	$clientIP	=	$_SERVER['REMOTE_ADDR'];
	
	$serverIP	=	$_SERVER['SERVER_ADDR']; 
	#IsIPAddressAlloed	(manageipaddress_class.php)
	if(($serverIP!=$clientIP) && !($manageipaddressObj->isIPAddressAllowed($clientIP)) && ($manageipaddressObj->isIPEnabled()!=""))
	{
		die();
	}
	#-------------------------------------------
	# Check Already Logged
	$cUserId = $sessObj->getValue("userId");
	$cUserRoleId 	= $sessObj->getValue("userRole");
	if ($cUserId && $cUserRoleId) {
		header("Location:".$urlAfterLogin);
	}
	if ($p["cmdLogin"]!="") {
		$username		=	$p["txtUsername"];
		$password		=	$userObj->getEncodedString($p["txtPwd"]);

		$checkLogin		=	$userObj->chkLogin($username,$password);
		
	#Checking Client IP Address
	
		if ((sizeof($checkLogin) > 0)) {
			$userId		=	$checkLogin[0];
			$userName	=	$checkLogin[1];
			$userRoleId	=	$checkLogin[3];
			$userRoleName = 	$checkLogin[4];
			$lastLogin = date('j M Y, H:i a ',strtotime($checkLogin[5]));
					
			#Update Login Time	
			$updateLoginTime = $userObj->UpdateLoginTime($userId);
				
			$sessObj->createSession("userId",$userId);
			$sessObj->createSession("userName",$userName);
			$sessObj->createSession("userRole",$userRoleId);
			$sessObj->createSession("userRoleName",$userRoleName);
			$sessObj->createSession("lastLogin",$lastLogin);
			$sessObj->createSession("loginTime",time());
			header("Location:".$urlAfterLogin);
			exit;
		} else {
			$err		=	$errLogin;
		}
	}
	

	$ON_LOAD_PRINT_JS	= "libjs/user.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle'>
<tr>
	<Td height='100'></td>
</tr>
					<?
						if ( strlen($err) !=0  ){
					?>		
							<tr> 
								<td class="err1" align='center'><?=$errLogin;?></td>
							</tr>
					<?
						}
					?>
<tr><Th align="center">
	<!-- Login Box Start -->
	<form name="loginFrm" method='post' action="Login.php" onLoad='document.loginFrm.txtUser.focus();' >
	<table width="330" height="170" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle' id="newspaper-b-login">
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
		<td>&nbsp;</td>
                <td class="caption1">* User Name :</td>
                <td align="center"><input name="txtUsername" type="text" id="txtUsername" size="25"></td>
              </tr>
              <tr> 
		<td>&nbsp;</td>
                <td class="caption1">* Password :</td>
                <td align="center"><input name="txtPwd" type="password" id="txtPwd" size="25"></td>
              </tr>
		<tr> 
		<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center">
			<input type="submit" class="button" name="cmdLogin"  id="cmdLogin"  value=" Login "  onClick=" return validateLogin(document.loginFrm);">
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
  document.loginFrm.txtUsername.focus();
  //-->
  </SCRIPT>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>